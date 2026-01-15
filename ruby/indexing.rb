# Install the API client: https://www.algolia.com/doc/libraries/sdk/install#ruby
require 'dotenv/load'
require 'algolia'

# Get your Algolia Application ID and (admin) API key from the dashboard: https://www.algolia.com/account/api-keys
# and choose a name for your index. Add these environment variables to a `.env` file:
ALGOLIA_APP_ID = ENV['ALGOLIA_APP_ID']
ALGOLIA_API_KEY = ENV['ALGOLIA_API_KEY']
ALGOLIA_INDEX_NAME = ENV['ALGOLIA_INDEX_NAME']

# Initialise the client and connect to it
# https://www.algolia.com/doc/libraries/sdk/methods/search#ruby
client = Algolia::SearchClient.create(ALGOLIA_APP_ID, ALGOLIA_API_KEY)

# Define some objects to add to our index
# https://www.algolia.com/old-docs/api-client/methods/indexing/#object-and-record
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
res = client.search_single_index(ALGOLIA_INDEX_NAME, {query: ''})
puts 'Current objects: ', res.hits, "\n"


# Save Objects: Add mutliple new objects to an index.
# https://www.algolia.com/doc/libraries/sdk/methods/search/save-objects#ruby
puts 'Save Objects - Adding multiple objects: ', contacts
client.save_objects(ALGOLIA_INDEX_NAME, contacts, true) # waitForTasks: true - Wait for the indexing task to complete

res = client.search_single_index(ALGOLIA_INDEX_NAME, {query: ''})
puts 'Current objects: ', res.hits, "\n"


# Save Objects: Replace an existing object with an updated set of attributes.
# https://www.algolia.com/doc/libraries/sdk/methods/search/add-or-update-object
puts 'Save Objects - Replacing objects’s attributes on:', contacts[0]
new_contact = {
    name: 'FooBar',
    objectID: '1'
}
response = client.add_or_update_object(ALGOLIA_INDEX_NAME, '1', new_contact)

# Wait for task to complete
# https://www.algolia.com/doc/libraries/sdk/methods/search/wait-for-task#ruby
client.wait_for_task(ALGOLIA_INDEX_NAME, response.task_id)

res = client.search_single_index(ALGOLIA_INDEX_NAME, {query: ''})
puts 'Current objects: ', res.hits, "\n"


# Partial Update Objects: Update one or more attributes of an existing object.
# https://www.algolia.com/doc/libraries/sdk/methods/search/partial-update-object
puts 'Save Objects - Updating object’s attributes on:',  contacts[0]
new_contact = {
    email: 'foo@bar.com', # New attribute
    objectID: '1'
}
response = client.partial_update_object(ALGOLIA_INDEX_NAME, '1', new_contact)

# Wait for task to complete
# https://www.algolia.com/doc/libraries/sdk/methods/search/wait-for-task#ruby
client.wait_for_task(ALGOLIA_INDEX_NAME, response.task_id)

res = client.search_single_index(ALGOLIA_INDEX_NAME, {query: ''})
puts 'Current objects: ', res.hits, "\n"


# Delete Object: Remove an object from an index using their objectID.
# https://www.algolia.com/doc/libraries/sdk/methods/search/delete-object
objectID_to_delete = contacts[0][:objectID]
puts 'Delete Objects - Deleting object with objectID: "%s"' % objectID_to_delete
response = client.delete_object(ALGOLIA_INDEX_NAME, objectID_to_delete)

# Wait for task to complete
# https://www.algolia.com/doc/libraries/sdk/methods/search/wait-for-task#ruby
client.wait_for_task(ALGOLIA_INDEX_NAME, response.task_id)

res = client.search_single_index(ALGOLIA_INDEX_NAME, {query: ''})
puts 'Current objects: ', res.hits, "\n"


# Replace All Objects: Clears all objects from your index and replaces them with a new set of objects.
# https://www.algolia.com/doc/libraries/sdk/methods/search/replace-all-objects
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
response = client.replace_all_objects(ALGOLIA_INDEX_NAME, new_contacts)

# puts response # <- Check the response - the move operation happens after the copy operation
# Wait for task to complete
# https://www.algolia.com/doc/libraries/sdk/methods/search/wait-for-task#ruby
client.wait_for_task(ALGOLIA_INDEX_NAME, response.move_operation_response.task_id)

res = client.search_single_index(ALGOLIA_INDEX_NAME, {query: ''})
puts 'Current objects: ', res.hits, "\n"


# Delete By: Remove all objects matching a filter (including geo filters).
# https://www.algolia.com/doc/libraries/sdk/methods/search/delete-by
puts 'Delete By - Remove all objects matching "name:NewBar"'

# Firstly, have an attribute to filter on
# https://www.algolia.com/doc/libraries/sdk/methods/search/set-settings
response = client.set_settings(ALGOLIA_INDEX_NAME, {
    attributesForFaceting: ['name']
})

# Wait for task to complete
# https://www.algolia.com/doc/libraries/sdk/methods/search/wait-for-task#ruby
client.wait_for_task(ALGOLIA_INDEX_NAME, response.task_id)

response = client.delete_by(ALGOLIA_INDEX_NAME, {
    facetFilters: ['name:NewBar'] # https://www.algolia.com/doc/api-reference/api-parameters/facetFilters/
})

# Wait for task to complete
# https://www.algolia.com/doc/libraries/sdk/methods/search/wait-for-task#ruby
client.wait_for_task(ALGOLIA_INDEX_NAME, response.task_id)

res = client.search_single_index(ALGOLIA_INDEX_NAME, {query: ''})
puts 'Current objects: ', res.hits, "\n"


# Get Objects: Get one or more objects using their objectIDs.
# https://www.algolia.com/doc/libraries/sdk/methods/search/get-objects
object_id = new_contacts[0][:objectID]
puts 'Get Objects - Getting object with objectID "%s"' % object_id

res = client.get_object(ALGOLIA_INDEX_NAME, object_id)
puts 'Results: ', res, "\n"


# Custom Batch: Perform several indexing operations in one API call.
# https://www.algolia.com/doc/libraries/sdk/methods/search/batch
operations = [
    {
        action: 'addObject',
        body: {
            name: 'BatchedBar',
        }
    },
    {
        action: 'updateObject',
        body: {
            objectID: object_id,
            name: 'NewBatchedBar',
        }
    }
]
puts 'Custom Batch - Batching %d operations' % operations.length
response = client.batch(ALGOLIA_INDEX_NAME, {requests: operations})

# Wait for task to complete
# https://www.algolia.com/doc/libraries/sdk/methods/search/wait-for-task#ruby
client.wait_for_task(ALGOLIA_INDEX_NAME, response.task_id)

res = client.search_single_index(ALGOLIA_INDEX_NAME, {query: ''})
puts 'Current objects: ', res.hits, "\n"


# Clear Objects: Clear the records of an index without affecting its settings.
# https://www.algolia.com/doc/libraries/sdk/methods/search/clear-objects
puts 'Clear Objects: Clear the records of an index without affecting its settings.'
response = client.clear_objects(ALGOLIA_INDEX_NAME)

# Wait for task to complete
# https://www.algolia.com/doc/libraries/sdk/methods/search/wait-for-task#ruby
client.wait_for_task(ALGOLIA_INDEX_NAME, response.task_id)

# We don't have any objects in our index
res = client.search_single_index(ALGOLIA_INDEX_NAME, {query: ''})
puts 'Current objects: ', res.hits
