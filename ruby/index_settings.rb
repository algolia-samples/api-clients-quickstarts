# Install the API client: https://www.algolia.com/doc/api-client/getting-started/install/ruby/?client=ruby
require 'dotenv/load'
require 'algolia'

# Get your Algolia Application ID and (admin) API key from the dashboard: https://www.algolia.com/account/api-keys
# and choose a name for your index. Add these environment variables to a `.env` file:
ALGOLIA_APP_ID = ENV['ALGOLIA_APP_ID']
ALGOLIA_API_KEY = ENV['ALGOLIA_API_KEY']
ALGOLIA_INDEX_NAME = ENV['ALGOLIA_INDEX_NAME']

# Start the API client
# https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/
client = Algolia::Search::Client.create(ALGOLIA_APP_ID, ALGOLIA_API_KEY)

# Create an index (or connect to it, if an index with the name `ALGOLIA_INDEX_NAME` already exists)
# https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/#initialize-an-index
index = client.init_index(ALGOLIA_INDEX_NAME)


# Changing index settings
# https://www.algolia.com/doc/api-reference/api-methods/set-settings/
index.set_settings({
  # https://www.algolia.com/doc/api-reference/api-parameters/typoTolerance/
  typoTolerance: true,
  # https://www.algolia.com/doc/api-reference/api-parameters/queryLanguages/
  queryLanguages: ['es'],
  # https://www.algolia.com/doc/api-reference/api-parameters/ignorePlurals/
  ignorePlurals: true
})


# Wait for the indexing task to complete
# https://www.algolia.com/doc/api-reference/api-methods/wait-task/
res.wait()

# Search the index for "Fo"
# https://www.algolia.com/doc/api-reference/api-methods/search/
objects = index.search('Fo')
puts objects
