require 'dotenv/load'
require 'algolia'

# Algolia client credentials
ALGOLIA_APP_ID = ENV['ALGOLIA_APP_ID']
ALGOLIA_API_KEY = ENV['ALGOLIA_API_KEY']
ALGOLIA_INDEX_NAME = ENV['ALGOLIA_INDEX_NAME']
ALGOLIA_SEARCH_API_KEY = ENV['ALGOLIA_SEARCH_API_KEY']


# Create API rate limit key TODO: generate key
# generate a public API key that is restricted to 'index1' and 'index2':
created_Algolia_API_key = Algolia::Search::Client.generate_secured_api_key(ALGOLIA_SEARCH_API_KEY, { restrictIndices: ALGOLIA_INDEX_NAME })
# generated API key
p 'generated API key'
p created_Algolia_API_key
client2= Algolia::Search::Client.create(ALGOLIA_APP_ID, created_Algolia_API_key)
# Initialize an index
# https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/#initialize-an-index
index = client2.init_index(ALGOLIA_INDEX_NAME)
# use that key to run a search TODO: run a search
