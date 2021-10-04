from rest_framework import serializers

from .models import Contact

class AlgoliaModelSerializer(serializers.ModelSerializer):
    objectID = serializers.CharField(source='id', read_only=True)

class ContactSerializer(AlgoliaModelSerializer):
    class Meta:
        model = Contact
        fields = ['objectID', 'name']