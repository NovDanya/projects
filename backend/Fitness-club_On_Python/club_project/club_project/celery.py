import os
from celery import Celery

# Устанавливаем настройки по умолчанию для Django
os.environ.setdefault('DJANGO_SETTINGS_MODULE', 'club_project.settings')

app = Celery('club_project')

# Читаем конфигурацию из settings.py с префиксом CELERY
app.config_from_object('django.conf:settings', namespace='CELERY')

# Автоматически находим задачи в файлах tasks.py внутри приложений
app.autodiscover_tasks()