from django.shortcuts import render, redirect, get_object_or_404
from django.contrib import messages
from django.http import JsonResponse, HttpResponse
from django.utils import timezone
import xml.etree.ElementTree as ET
from .models import Member, Trainer, Subscription
from .services import (
    get_trainer_prices,
    calculate_subscription_prices,
    create_subscription_for_member,
    get_club_statistics,
    DAYS_MAP
)


def dashboard(request):
    return render(request, 'index.html', {
        'members': Member.objects.all().order_by('-id'),
        'trainers': Trainer.objects.all(),
        'subs': Subscription.objects.filter(is_active=True).order_by('-id'),
        'stats': get_club_statistics()
    })


def api_trainer_prices(request, trainer_id):
    try:
        trainer = Trainer.objects.get(id=trainer_id)
        return JsonResponse(get_trainer_prices(trainer))
    except Trainer.DoesNotExist:
        return JsonResponse({"error": "Not found"}, status=404)


def add_member_full(request):
    if request.method == 'POST':
        phone = request.POST.get('phone')
        if Member.objects.filter(phone=phone).exists():
            messages.error(request, f"Клиент с телефоном {phone} уже существует!")
            return redirect('dashboard')

        trainer_id = request.POST.get('trainer_id') or None
        if trainer_id:
            trainer_id = int(trainer_id)

        m = Member.objects.create(
            full_name=request.POST['full_name'],
            phone=phone,
            status='Активен'
        )

        create_subscription_for_member(
            member=m,
            mem_type=request.POST['mem_type'],
            duration=request.POST['duration'],
            trainer_id=trainer_id
        )
        messages.success(request, "Клиент успешно создан!")
    return redirect('dashboard')


def member_detail(request, pk):
    m = get_object_or_404(Member, pk=pk)
    return render(request, 'members/detail.html', {'member': m, 'subs': m.subscriptions.all()})


def member_update(request, pk):
    m = get_object_or_404(Member, pk=pk)
    if request.method == 'POST':
        full_name = request.POST.get('full_name', '').strip()
        phone = request.POST.get('phone', '').strip()
        status = request.POST.get('status', m.status)

        if full_name: m.full_name = full_name.title()
        if phone: m.phone = phone

        m.status = status
        m.save()
        messages.success(request, "Данные обновлены!")
        return redirect('member_detail', pk=pk)
    return render(request, 'members/form.html', {'member': m})


def archive_member(request, member_id):
    if request.method == 'POST':
        Member.objects.filter(id=member_id).update(status='Архив')
        Subscription.objects.filter(member_id=member_id, is_active=True).update(is_active=False)
        messages.success(request, "Клиент отправлен в архив.")
    return redirect('dashboard')


def restore_member(request, member_id):
    if request.method == 'POST':
        m = get_object_or_404(Member, id=member_id)
        m.status = 'Активен'
        m.save()

        trainer_id = request.POST.get('trainer_id') or None
        if trainer_id:
            trainer_id = int(trainer_id)

        sub = create_subscription_for_member(
            member=m,
            mem_type=request.POST['mem_type'],
            duration=request.POST['duration'],
            trainer_id=trainer_id
        )
        messages.success(request, f"Восстановлен! До {sub.end_date}. Сумма: {sub.total_price} ₽")
    return redirect('dashboard')


def add_trainer(request):
    if request.method == 'POST':
        name = request.POST.get('name')
        if Trainer.objects.filter(name=name).exists():
            messages.error(request, f"Тренер '{name}' уже существует!")
            return redirect('dashboard')
        Trainer.objects.create(
            name=name, specialization=request.POST['spec'],
            price_1m=request.POST['p1'], price_3m=request.POST['p3'],
            price_6m=request.POST['p6'], price_1y=request.POST['p12']
        )
        messages.success(request, "Тренер добавлен")
    return redirect('dashboard')


def trainer_update(request, pk):
    trainer = get_object_or_404(Trainer, pk=pk)
    if request.method == 'POST':
        name = request.POST.get('name', '').strip()
        spec = request.POST.get('specialization', '').strip()

        if name: trainer.name = name.title()
        if spec: trainer.specialization = spec.title()

        trainer.price_1m = request.POST.get('price_1m')
        trainer.price_3m = request.POST.get('price_3m')
        trainer.price_6m = request.POST.get('price_6m')
        trainer.price_1y = request.POST.get('price_1y')
        trainer.save()
        messages.success(request, f"Тренер {trainer.name} обновлен!")
        return redirect('dashboard')
    return render(request, 'trainers/form.html', {'trainer': trainer})


def trainer_detail(request, pk):
    trainer = get_object_or_404(Trainer, pk=pk)
    subscriptions = Subscription.objects.filter(trainer=trainer).select_related('member')
    return render(request, 'trainers/detail.html', {
        'trainer': trainer,
        'subscriptions': subscriptions
    })


def delete_trainer(request, trainer_id):
    if request.method == 'POST':
        Subscription.objects.filter(trainer_id=trainer_id, is_active=True).delete()
        Trainer.objects.filter(id=trainer_id).delete()
        messages.success(request, "Тренер удален")
    return redirect('dashboard')


def renew_subscription(request, member_id):
    if request.method == 'POST':
        m = get_object_or_404(Member, id=member_id)
        mem_type, duration = request.POST['mem_type'], request.POST['duration']
        trainer_id = request.POST.get('trainer_id') or None
        if trainer_id:
            trainer_id = int(trainer_id)

        discount = False
        active_sub = Subscription.objects.filter(member=m, is_active=True).order_by('-start_date').first()
        if active_sub and duration != "Разовое":
            days_left = (active_sub.end_date - timezone.now().date()).days
            if 0 <= days_left < 30:
                discount = True

        sub = create_subscription_for_member(
            member=m,
            mem_type=mem_type,
            duration=duration,
            trainer_id=trainer_id,
            apply_discount=discount
        )
        msg = f"Продлено до {sub.end_date}. Итого: {sub.total_price} ₽" + (" (Скидка 10%)" if discount else "")
        messages.success(request, msg)
    return redirect('dashboard')


def export_xml(request):
    root = ET.Element("FitnessClubData")

    members_elem = ET.SubElement(root, "Members")
    for m in Member.objects.all():
        el = ET.SubElement(members_elem, "Member")
        ET.SubElement(el, "ID").text = str(m.id)
        ET.SubElement(el, "Name").text = m.full_name
        ET.SubElement(el, "Phone").text = m.phone
        ET.SubElement(el, "Status").text = m.status

    trainers_elem = ET.SubElement(root, "Trainers")
    for t in Trainer.objects.all():
        el = ET.SubElement(trainers_elem, "Trainer")
        ET.SubElement(el, "ID").text = str(t.id)
        ET.SubElement(el, "Name").text = t.name
        ET.SubElement(el, "Spec").text = t.specialization

    subs_elem = ET.SubElement(root, "Subscriptions")
    for s in Subscription.objects.all():
        el = ET.SubElement(subs_elem, "Subscription")
        ET.SubElement(el, "ID").text = str(s.id)
        ET.SubElement(el, "MemberID").text = str(s.member_id)
        ET.SubElement(el, "TrainerID").text = str(s.trainer_id) if s.trainer_id else "Нет"
        ET.SubElement(el, "Type").text = f"{s.membership_type} ({s.duration})"
        ET.SubElement(el, "Status").text = "Active" if s.is_active else "Inactive"
        ET.SubElement(el, "ClubRevenue").text = str(s.base_price)
        ET.SubElement(el, "TrainerFee").text = str(s.trainer_price)
        ET.SubElement(el, "TotalPaid").text = str(s.total_price)

    xml_data = ET.tostring(root, encoding='utf-8', xml_declaration=True)

    response = HttpResponse(xml_data, content_type='application/xml')
    response['Content-Disposition'] = 'attachment; filename="fitness_club_export.xml"'
    return response