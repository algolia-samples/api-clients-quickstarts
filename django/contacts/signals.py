from django.conf import settings
from django.db.models.signals import post_save
from django.dispatch import receiver

from algoliasearch.search_client import SearchClient

from .models import Contact
from .serializers import ContactSerializer


@receiver(post_save, sender=Contact)
def index(instance, **kwargs):
    client = SearchClient.create(settings.ALGOLIA_APP_ID, settings.ALGOLIA_API_KEY)
    index = client.init_index(settings.ALGOLIA_INDEX_NAME)

    contact = ContactSerializer(instance)
    index.save_object(contact.data)