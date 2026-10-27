from celery import shared_task
from django.utils import timezone
from .models import Subscription


@shared_task
def check_expired_subscriptions():
    """Фоновая задача: отключает подписки, у которых истек срок действия."""
    today = timezone.now().date()
    expired_subs = Subscription.objects.filter(is_active=True, end_date__lt=today)

    count = expired_subs.count()
    if count > 0:
        expired_subs.update(is_active=False)
        return f"Deactivated {count} expired subscriptions."
    return "No expired subscriptions found."