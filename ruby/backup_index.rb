# Install the API client: https://www.algolia.com/doc/api-client/getting-started/install/ruby/?client=ruby
require 'algolia'
require 'dotenv/load'
require 'json'

# Algolia client credentials
ALGOLIA_APP_ID = ENV['ALGOLIA_APP_ID']
ALGOLIA_API_KEY = ENV['ALGOLIA_API_KEY']
ALGOLIA_INDEX_NAME = ENV['ALGOLIA_INDEX_NAME']

# Initialize the client and the index
# https://www.algolia.com/doc/api-client/getting-started/initialize/ruby/?client=ruby#initialize-the-search-client
client = Algolia::Search::Client.create(ALGOLIA_APP_ID, ALGOLIA_API_KEY)
index = client.init_index(ALGOLIA_INDEX_NAME)

# Retrieve records
puts 'Retrieving records...'
records = index.browse({})['hits']
puts "#{records.length} records retrieved"