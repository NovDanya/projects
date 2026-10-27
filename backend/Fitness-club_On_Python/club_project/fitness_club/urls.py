from django.urls import path, include
from rest_framework.routers import DefaultRouter
from . import api_views
from . import views

# Роутер для DRF (автоматически создает пути для list, create, retrieve, update, destroy)
router = DefaultRouter()
router.register(r'members', api_views.MemberViewSet)
router.register(r'trainers', api_views.TrainerViewSet)
router.register(r'subscriptions', api_views.SubscriptionViewSet)

urlpatterns = [
    # Обычные веб-страницы
    path('', views.dashboard, name='dashboard'),
    path('api/trainer/prices/<int:trainer_id>/', views.api_trainer_prices, name='api_trainer_prices'),
    path('add_member/', views.add_member_full, name='add_member'),
    path('add_trainer/', views.add_trainer, name='add_trainer'),
    path('member/<int:member_id>/archive/', views.archive_member, name='archive_member'),
    path('member/<int:member_id>/restore/', views.restore_member, name='restore_member'),
    path('trainer/<int:pk>/detail/', views.trainer_detail, name='trainer_detail'),
    path('trainer/<int:pk>/edit/', views.trainer_update, name='trainer_update'),
    path('trainer/<int:trainer_id>/delete/', views.delete_trainer, name='delete_trainer'),
    path('member/<int:member_id>/renew/', views.renew_subscription, name='renew_sub'),
    path('export/xml/', views.export_xml, name='export_xml'),
    path('member/<int:pk>/detail/', views.member_detail, name='member_detail'),
    path('member/<int:pk>/edit/', views.member_update, name='member_update'),

    # REST API пути
    path('api/v1/', include(router.urls)),
]