from django.contrib import admin
from django import forms
from django.utils import timezone
from datetime import timedelta
from .models import Member, Trainer, Subscription
from .services import calculate_subscription_prices, DAYS_MAP, calculate_trainer_cost


class SubscriptionAdminForm(forms.ModelForm):
    class Meta:
        model = Subscription
        fields = '__all__'

    def clean(self):
        cleaned_data = super().clean()
        mem_type = cleaned_data.get('membership_type')
        duration = cleaned_data.get('duration')
        trainer = cleaned_data.get('trainer')

        trainer_id = trainer.id if trainer else None
        base_price, trainer_price, total_price = calculate_subscription_prices(mem_type, duration, trainer_id)

        cleaned_data['base_price'] = base_price
        cleaned_data['trainer_price'] = trainer_price
        cleaned_data['total_price'] = total_price

        if not cleaned_data.get('start_date'):
            cleaned_data['start_date'] = timezone.now().date()

        if cleaned_data.get('start_date') and duration:
            days = DAYS_MAP.get(duration, 1)
            cleaned_data['end_date'] = cleaned_data['start_date'] + timedelta(days=days)

        return cleaned_data


@admin.register(Member)
class MemberAdmin(admin.ModelAdmin):
    list_display = ('id', 'full_name', 'phone', 'status', 'created_at')
    list_filter = ('status', 'created_at')
    search_fields = ('full_name', 'phone')
    ordering = ('-created_at',)
    list_editable = ('status',)


@admin.register(Trainer)
class TrainerAdmin(admin.ModelAdmin):
    list_display = ('id', 'name', 'specialization', 'price_1m', 'price_3m', 'price_6m', 'price_1y')
    list_filter = ('specialization',)
    search_fields = ('name',)
    list_editable = ('price_1m', 'price_3m', 'price_6m', 'price_1y')


@admin.register(Subscription)
class SubscriptionAdmin(admin.ModelAdmin):
    form = SubscriptionAdminForm

    list_display = (
        'id', 'member', 'trainer', 'membership_type', 'duration',
        'start_date', 'end_date', 'total_price', 'is_active'
    )

    list_filter = ('membership_type', 'duration', 'is_active', 'trainer', 'start_date')
    search_fields = ('member__full_name', 'member__phone')
    autocomplete_fields = ('member', 'trainer')
    list_editable = ('is_active',)

    readonly_fields = ('base_price', 'trainer_price', 'total_price', 'end_date')

    fieldsets = (
        ('Основная информация', {
            'fields': ('member', 'trainer', 'membership_type', 'duration', 'is_active')
        }),
        ('Сроки действия', {
            'fields': ('start_date', 'end_date'),
            'description': 'Дата окончания рассчитывается автоматически на основе срока.'
        }),
        ('Финансы (авторасчет)', {
            'fields': ('base_price', 'trainer_price', 'total_price'),
            'classes': ('collapse',),
            'description': 'Цены рассчитываются автоматически на основе типа абонемента и тарифов тренера.'
        }),
    )

    ordering = ('-start_date',)

    def save_model(self, request, obj, form, change):
        if not obj.start_date:
            obj.start_date = timezone.now().date()
        if obj.duration:
            days = DAYS_MAP.get(obj.duration, 1)
            obj.end_date = obj.start_date + timedelta(days=days)

        trainer_id = obj.trainer.id if obj.trainer else None
        base, t_cost, total = calculate_subscription_prices(obj.membership_type, obj.duration, trainer_id)

        obj.base_price = base
        obj.trainer_price = t_cost
        obj.total_price = total

        super().save_model(request, obj, form, change)