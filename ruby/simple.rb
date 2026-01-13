# Install the API client: https://www.algolia.com/doc/libraries/sdk/install#ruby
require 'dotenv/load'
require 'algolia'

# Get your Algolia Application ID and (admin) API key from the dashboard: https://www.algolia.com/account/api-keys
# and choose a name for your index. Add these environment variables to a `.env` file:
ALGOLIA_APP_ID = ENV['ALGOLIA_APP_ID']
ALGOLIA_API_KEY = ENV['ALGOLIA_API_KEY']
ALGOLIA_INDEX_NAME = ENV['ALGOLIA_INDEX_NAME']

# Start the API client & connect to it, when an index with the name `ALGOLIA_INDEX_NAME` already exists
# https://www.algolia.com/doc/libraries/sdk/methods/search#ruby
client = Algolia::SearchClient.create(ALGOLIA_APP_ID, ALGOLIA_API_KEY)

# Add new objects to the index
# https://www.algolia.com/doc/libraries/sdk/methods/search/save-objects#ruby
new_object = {objectID: 1, name: 'Foo'}
res = client.save_objects(ALGOLIA_INDEX_NAME, [new_object], true) # waitForTasks: true - Wait for the indexing task to complete

# Search the index for "Fo"
# https://www.algolia.com/doc/libraries/sdk/methods/search/search-single-index
objects = client.search_single_index(ALGOLIA_INDEX_NAME, {query: 'Fo'})
puts objects
