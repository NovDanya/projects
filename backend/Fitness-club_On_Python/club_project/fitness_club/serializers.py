from rest_framework import serializers
from .models import Member, Trainer, Subscription

class TrainerSerializer(serializers.ModelSerializer):
    class Meta:
        model = Trainer
        fields = '__all__'

class MemberSerializer(serializers.ModelSerializer):
    class Meta:
        model = Member
        fields = '__all__'

class SubscriptionSerializer(serializers.ModelSerializer):
    member_name = serializers.CharField(source='member.full_name', read_only=True)
    trainer_name = serializers.CharField(source='trainer.name', read_only=True, allow_null=True)

    class Meta:
        model = Subscription
        fields = '__all__'