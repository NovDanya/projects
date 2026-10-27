import pytest
from django.urls import reverse
from rest_framework.test import APIClient
from fitness_club.models import Member, Trainer, Subscription
from fitness_club.services import calculate_subscription_prices, create_subscription_for_member


@pytest.mark.django_db
class TestFitnessClubBusinessLogic:

    def setup_method(self):
        # Создаем тестового тренера с ценами
        self.trainer = Trainer.objects.create(
            name="Иван Иванов",
            specialization="Фитнес",
            price_1m=4000,
            price_3m=10000,
            price_6m=18000,
            price_1y=30000
        )
        # Создаем тестового клиента
        self.member = Member.objects.create(
            full_name="Алексей Смирнов",
            phone="+79991112233",
            status="Активен"
        )

    def test_calculate_subscription_prices_gym(self):
        """Тест расчета цен для спортзала с тренером"""
        base, trainer_cost, total = calculate_subscription_prices(
            mem_type="gym",
            duration="1 месяц",
            trainer_id=self.trainer.id
        )
        # База спортзала на 1 мес = 6000, тренер на 1 мес = 4000. Итого = 10000
        assert base == 6000.0
        assert trainer_cost == 4000.0
        assert total == 10000.0

    def test_create_subscription_service(self):
        """Тест сервисной функции создания подписки"""
        sub = create_subscription_for_member(
            member=self.member,
            mem_type="pool",
            duration="3 месяца",
            trainer_id=self.trainer.id
        )
        assert sub.is_active is True
        assert sub.member == self.member
        assert sub.trainer == self.trainer
        # База бассейна на 3 мес = 11000, тренер на 3 мес = 10000. Итого = 21000
        assert sub.total_price == 21000.0


@pytest.mark.django_db
class TestFitnessClubAPI:

    def setup_method(self):
        self.client = APIClient()
        self.member = Member.objects.create(
            full_name="Тест Тестов",
            phone="+79000000000"
        )

    def test_get_members_api(self):
        """Тест получения списка клиентов через REST API"""
        url = reverse('member-list')  # Роутер DRF генерирует имя как 'modelname-list'
        response = self.client.get(url)

        assert response.status_code == 200
        assert len(response.data) >= 1
        assert response.data[0]['full_name'] == "Тест Тестов"

    def test_create_member_api(self):
        """Тест создания клиента через REST API"""
        url = reverse('member-list')
        data = {
            "full_name": "Новый Клиент",
            "phone": "+79112223344",
            "status": "Активен"
        }
        response = self.client.post(url, data, format='json')

        assert response.status_code == 201
        assert Member.objects.filter(phone="+79112223344").exists()