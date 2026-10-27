from datetime import timedelta
from django.utils import timezone
from .models import Member, Trainer, Subscription

MEMBERSHIP_PRICES = {
    "gym": {"Разовое": 1500, "1 месяц": 6000, "3 месяца": 12000, "6 месяцев": 18000, "1 год": 28000},
    "pool": {"Разовое": 1500, "1 месяц": 5000, "3 месяца": 11000, "6 месяцев": 17000, "1 год": 30000},
    "combo": {"Разовое": 2000, "1 месяц": 5000, "3 месяца": 13000, "6 месяцев": 23000, "1 год": 35000}
}
DAYS_MAP = {"Разовое": 1, "1 месяц": 30, "3 месяца": 90, "6 месяцев": 180, "1 год": 365}


def get_trainer_prices(trainer: Trainer) -> dict:
    """Возвращает словарь цен тренера по периодам."""
    if not trainer:
        return {"1 месяц": 0, "3 месяца": 0, "6 месяцев": 0, "1 год": 0}
    return {
        "1 месяц": float(trainer.price_1m),
        "3 месяца": float(trainer.price_3m),
        "6 месяцев": float(trainer.price_6m),
        "1 год": float(trainer.price_1y)
    }


def calculate_trainer_cost(trainer: Trainer, duration: str) -> float:
    """Рассчитывает стоимость услуг тренера в зависимости от срока."""
    if not trainer:
        return 0.0
    prices = get_trainer_prices(trainer)
    if duration == "Разовое":
        return prices.get("1 месяц", 0) / 4
    return prices.get(duration, 0.0)


def calculate_subscription_prices(mem_type: str, duration: str, trainer_id: int = None) -> tuple:
    """Рассчитывает базовую цену, стоимость тренера и итоговую сумму."""
    base_price = float(MEMBERSHIP_PRICES.get(mem_type, {}).get(duration, 0))
    trainer_cost = 0.0

    trainer = None
    if trainer_id:
        try:
            trainer = Trainer.objects.get(id=trainer_id)
            trainer_cost = calculate_trainer_cost(trainer, duration)
        except Trainer.DoesNotExist:
            pass

    total_price = base_price + trainer_cost
    return base_price, trainer_cost, total_price


def create_subscription_for_member(member: Member, mem_type: str, duration: str, trainer_id: int = None,
                                   apply_discount: bool = False) -> Subscription:
    """Сервисная функция для создания/продления абонемента клиенту."""
    base_price, trainer_price, total_price = calculate_subscription_prices(mem_type, duration, trainer_id)

    # Логика скидки 10% при раннем продлении
    if apply_discount and duration != "Разовое":
        base_price *= 0.9
        total_price = base_price + trainer_price

    today = timezone.now().date()
    days = DAYS_MAP.get(duration, 1)
    end_date = today + timedelta(days=days)

    # Делаем предыдущие активные подписки неактивными (опционально, по логике)
    # Subscription.objects.filter(member=member, is_active=True).update(is_active=False)

    sub = Subscription.objects.create(
        member=member,
        trainer_id=trainer_id,
        membership_type=mem_type,
        duration=duration,
        base_price=base_price,
        trainer_price=trainer_price,
        total_price=total_price,
        start_date=today,
        end_date=end_date,
        is_active=True
    )
    return sub


def get_club_statistics() -> dict:
    """Собирает общую аналитику по клубу."""
    active_subs = Subscription.objects.filter(is_active=True)
    return {
        "revenue": sum(s.base_price for s in Subscription.objects.all()),
        "trainers_earnings": sum(s.trainer_price for s in active_subs),
        "total_members": Member.objects.count(),
        "total_trainers": Trainer.objects.count()
    }