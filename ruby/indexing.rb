require 'dotenv/load'
require 'algolia'

# Algolia client credentials
ALGOLIA_APP_ID = ENV['ALGOLIA_APP_ID']
ALGOLIA_API_KEY = ENV['ALGOLIA_API_KEY']
ALGOLIA_INDEX_NAME = ENV['ALGOLIA_INDEX_NAME']

# Initialize the client
# https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/
client = Algolia::Search::Client.create(ALGOLIA_APP_ID, ALGOLIA_API_KEY)

# Initialize an index
# https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/#initialize-an-index
index = client.init_index(ALGOLIA_INDEX_NAME)

# Define some objects to add to our index
# https://www.algolia.com/doc/api-client/methods/indexing/#object-and-record
contacts = [
    {
        name: 'Foo',
        objectID: '1'
    },
    {
        name: 'Bar',
        objectID: '2'
    }
]

# We don't have any objects (yet) in our index
res = index.search('')
puts 'Current objects: ', res[:hits], "\n"

# Save Objects: Add mutliple new objects to an index.
# https://www.algolia.com/doc/api-reference/api-methods/add-objects/?client=ruby
puts 'Save Objects - Adding multiple objects: ', contacts
index.save_objects!(contacts)

res = index.search('')
puts 'Current objects: ', res[:hits], "\n"

# Save Objects: Replace an existing object with an updated set of attributes.
# https://www.algolia.com/doc/api-reference/api-methods/save-objects/?client=ruby
puts 'Save Objects - Replacing objects’s attributes on:', contacts[0]
new_contact = {
    name: 'FooBar',
    objectID: '1'
}
index.save_object!(new_contact)

res = index.search('')
puts 'Current objects: ', res[:hits], "\n"

# Partial Update Objects: Update one or more attributes of an existing object.
# https://www.algolia.com/doc/api-reference/api-methods/partial-update-objects/?client=ruby
puts 'Save Objects - Updating object’s attributes on:',  contacts[0]
new_contact = {
    email: 'foo@bar.com', # New attribute
    objectID: '1'
}
index.partial_update_object!(new_contact)

res = index.search('')
puts 'Current objects: ', res[:hits], "\n"

# Delete Objects: Remove objects from an index using their objectID.
# https://www.algolia.com/doc/api-reference/api-methods/delete-objects/?client=ruby
objectID_to_delete = contacts[0][:objectID]
puts 'Delete Objects - Deleting object with objectID: "%s"' % objectID_to_delete
index.delete_object!(objectID_to_delete)

res = index.search('')
puts 'Current objects: ', res[:hits], "\n"

# Replace All Objects: Clears all objects from your index and replaces them with a new set of objects.
# https://www.algolia.com/doc/api-reference/api-methods/replace-all-objects/?client=ruby
new_contacts = [
    {
        name: 'NewFoo',
        objectID: '3'
    },
    {
        name: 'NewBar',
        objectID: '4'
    }
]
puts 'Replace All Objects - Clears all objects and replaces them with:', new_contacts
res = index.replace_all_objects!(new_contacts)

res = index.search('')
puts 'Current objects: ', res[:hits], "\n"

# Delete By: Remove all objects matching a filter (including geo filters).
# https://www.algolia.com/doc/api-reference/api-methods/delete-by/?client=ruby
puts 'Delete By - Remove all objects matching "name:NewBar"'

# Firstly, have an attribute to filter on
# https://www.algolia.com/doc/api-client/methods/settings/?client=ruby
index.set_settings({
    attributesForFaceting: ['name']
}).wait()

index.delete_by({
    facetFilters: ['name:NewBar'] # https://www.algolia.com/doc/api-reference/api-parameters/facetFilters/
}).wait()

res = index.search('')
puts 'Current objects: ', res[:hits], "\n"

# Get Objects: Get one or more objects using their objectIDs.
# https://www.algolia.com/doc/api-reference/api-methods/get-objects/?client=ruby
object_id = new_contacts[0][:objectID]
puts 'Get Objects - Getting object with objectID "%s"' % object_id

res = index.get_object(object_id)
puts 'Results: ', res, "\n"

# Custom Batch: Perform several indexing operations in one API call.
# https://www.algolia.com/doc/api-reference/api-methods/batch/?client=ruby
operations = [
    {
        action: 'addObject',
        indexName: ALGOLIA_INDEX_NAME,
        body: {
            name: 'BatchedBar',
        }
    },
    {
        action: 'updateObject',
        indexName: ALGOLIA_INDEX_NAME,
        body: {
            objectID: object_id,
            name: 'NewBatchedBar',
        }
    }
]
puts 'Custom Batch - Batching %d operations' % operations.length
res = client.multiple_batch!(operations)

res = index.search('')
puts 'Current objects: ', res[:hits], "\n"


# Clear Objects: Clear the records of an index without affecting its settings.
# https://www.algolia.com/doc/api-reference/api-methods/clear-objects/?client=ruby
puts 'Clear Objects: Clear the records of an index without affecting its settings.'
index.clear_objects!()

# We don't have any objects in our index
res = index.search('')
puts 'Current objects: ', res[:hits]
