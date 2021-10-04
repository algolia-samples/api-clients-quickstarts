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

# Save Objects: Add mutliple new objects to an index.
# https://www.algolia.com/doc/api-reference/api-methods/add-objects/
new_object = {objectID: 1, name: 'Foo'}
res = index.save_objects([new_object])

# Waiting for the indexing task to complete
# https://www.algolia.com/doc/api-reference/api-methods/wait-task/
res.wait()

# Search Index: Method used for querying an index.
# https://www.algolia.com/doc/api-reference/api-methods/search/
objects = index.search('Fo')
puts objects