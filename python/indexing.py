from os import getenv


from algoliasearch.search_client import SearchClient
from dotenv import load_dotenv, find_dotenv

load_dotenv(find_dotenv())

# Algolia client credentials
ALGOLIA_APP_ID = getenv('ALGOLIA_APP_ID')
ALGOLIA_API_KEY = getenv('ALGOLIA_API_KEY')
ALGOLIA_INDEX_NAME = getenv('ALGOLIA_INDEX_NAME')

# Initialize the client
# https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/?client=python
client = SearchClient.create(ALGOLIA_APP_ID, ALGOLIA_API_KEY)

# Initialize an index
# https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/#initialize-an-index
index = client.init_index(ALGOLIA_INDEX_NAME)

# Define some objects to add to our index
# https://www.algolia.com/doc/api-client/methods/indexing/#object-and-record
contacts = [
    {
        'name': 'Foo',
        'objectID': '1'
    },
    {
        'name': 'Bar',
        'objectID': '2'
    }
]

# We don't have any objects (yet) in our index
res = index.search('')
print('Current objects: ', res['hits'])

# Save Objects: Add mutliple new objects to an index.
# https://www.algolia.com/doc/api-reference/api-methods/add-objects/?client=python
print('Save Objects - Adding multiple objects: ', contacts)
index.save_objects(contacts).wait()

res = index.search('')
print('Current objects: ', res['hits'], '\n')

# Save Objects: Replace an existing object with an updated set of attributes.
# https://www.algolia.com/doc/api-reference/api-methods/save-objects/?client=python
print(f'Save Objects - Replacing objects’s attributes on {contacts[0]}')
new_contact = {
    'name': 'FooBar',
    'objectID': '1'
}
index.save_object(new_contact).wait()

res = index.search('')
print('Current objects: ', res['hits'], '\n')

# Partial Update Objects: Update one or more attributes of an existing object.
# https://www.algolia.com/doc/api-reference/api-methods/partial-update-objects/?client=python
print(f'Save Objects - Updating object’s attributes on {contacts[0]}')
new_contact = {
    'email': 'foo@bar.com', # New attribute
    'objectID': '1'
}
index.partial_update_object(new_contact).wait()

res = index.search('')
print('Current objects: ', res['hits'], '\n')

# Delete Objects: Remove objects from an index using their objectID.
# https://www.algolia.com/doc/api-reference/api-methods/delete-objects/?client=python
objectID_to_delete = contacts[0]["objectID"]
print(f'Delete Objects - Deleting object with objectID "{objectID_to_delete}"')
index.delete_object(objectID_to_delete).wait()

res = index.search('')
print('Current objects: ', res['hits'], '\n')

# Replace All Objects: Clears all objects from your index and replaces them with a new set of objects.
# https://www.algolia.com/doc/api-reference/api-methods/replace-all-objects/?client=python
new_contacts = [
    {
        'name': 'NewFoo',
        'objectID': '3'
    },
    {
        'name': 'NewBar',
        'objectID': '4'
    }
]
print(f'Replace All Objects - Clears all objects and replaces them with "{new_contacts}"')
index.replace_all_objects(new_contacts).wait()

res = index.search('')
print('Current objects: ', res['hits'], '\n')

# Delete By: Remove all objects matching a filter (including geo filters).
# https://www.algolia.com/doc/api-reference/api-methods/delete-by/?client=python
print(f'Delete By - Remove all objects matching "name:NewBar"')

# Firstly, have an attribute to filter on
# https://www.algolia.com/doc/api-client/methods/settings/?client=python
index.set_settings({
    'attributesForFaceting': ['name']
}).wait()

index.delete_by({
    'facetFilters': [f'name:NewBar'] # https://www.algolia.com/doc/api-reference/api-parameters/facetFilters/
}).wait()

res = index.search('')
print('Current objects: ', res['hits'], '\n')

# Get Objects: Get one or more objects using their objectIDs.
# https://www.algolia.com/doc/api-reference/api-methods/get-objects/?client=python
object_id = new_contacts[0]['objectID']
print(f'Get Objects - Getting object with objectID "{object_id}"')

res = index.get_object(object_id)
print('Results: ', res, '\n')

# Custom Batch: Perform several indexing operations in one API call.
# https://www.algolia.com/doc/api-reference/api-methods/batch/?client=python
operations = [
    {
        'action': 'addObject',
        'indexName': ALGOLIA_INDEX_NAME,
        'body': {
            'name': 'BatchedBar',
        }
    },
    {
        'action': 'updateObject',
        'indexName': ALGOLIA_INDEX_NAME,
        'body': {
            'objectID': object_id,
            'name': 'NewBatchedBar',
        }
    }
]
print(f'Custom Batch - Batching {len(operations)} operations')
res = client.multiple_batch(operations).wait()

res = index.search('')
print('Current objects: ', res['hits'], '\n')


# Clear Objects: Clear the records of an index without affecting its settings.
# https://www.algolia.com/doc/api-reference/api-methods/clear-objects/?client=python
print('Clear Objects: Clear the records of an index without affecting its settings.')
index.clear_objects().wait()

# We don't have any objects in our index
res = index.search('')
print('Current objects: ', res['hits'])
