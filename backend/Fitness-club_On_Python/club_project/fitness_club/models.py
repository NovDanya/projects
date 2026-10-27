from django.db import models

class Trainer(models.Model):
    name = models.CharField('ФИО тренера', max_length=100, unique=True)
    specialization = models.CharField('Специализация', max_length=100)
    price_1m = models.DecimalField('Цена 1 мес', max_digits=10, decimal_places=2, default=0)
    price_3m = models.DecimalField('Цена 3 мес', max_digits=10, decimal_places=2, default=0)
    price_6m = models.DecimalField('Цена 6 мес', max_digits=10, decimal_places=2, default=0)
    price_1y = models.DecimalField('Цена 1 год', max_digits=10, decimal_places=2, default=0)

    def save(self, *args, **kwargs):
        if self.name:
            self.name = self.name.title()
        if self.specialization:
            self.specialization = self.specialization.title()
        super().save(*args, **kwargs)

    def __str__(self):
        return f"{self.name} ({self.specialization})"

class Member(models.Model):
    full_name = models.CharField('ФИО клиента', max_length=100)
    phone = models.CharField('Телефон', max_length=20, unique=True)
    status = models.CharField('Статус', max_length=20, default='Активен',
                              choices=[('Активен', 'Активен'), ('Архив', 'Архив')])
    created_at = models.DateTimeField('Дата регистрации', auto_now_add=True)

    def save(self, *args, **kwargs):
        if self.full_name:
            self.full_name = self.full_name.title()
        super().save(*args, **kwargs)

    def __str__(self):
        return self.full_name

class Subscription(models.Model):
    MEMBER_TYPES = [('gym', 'Спортзал'), ('pool', 'Бассейн'), ('combo', 'Комбо')]
    DURATIONS = [('Разовое', 'Разовое'), ('1 месяц', '1 месяц'), ('3 месяца', '3 месяца'),
                 ('6 месяцев', '6 месяцев'), ('1 год', '1 год')]

    member = models.ForeignKey(Member, on_delete=models.CASCADE, related_name='subscriptions', verbose_name='Клиент')
    trainer = models.ForeignKey(Trainer, on_delete=models.SET_NULL, null=True, blank=True, verbose_name='Тренер')
    membership_type = models.CharField('Тип абонемента', max_length=20, choices=MEMBER_TYPES)
    duration = models.CharField('Срок', max_length=20, choices=DURATIONS)
    base_price = models.DecimalField('Базовая цена', max_digits=10, decimal_places=2)
    trainer_price = models.DecimalField('Цена тренера', max_digits=10, decimal_places=2, default=0)
    total_price = models.DecimalField('Итого', max_digits=10, decimal_places=2)
    start_date = models.DateField('Дата начала')
    end_date = models.DateField('Дата окончания')
    is_active = models.BooleanField('Активен', default=True)

    def __str__(self):
        return f"{self.member.full_name} - {self.get_membership_type_display()}"