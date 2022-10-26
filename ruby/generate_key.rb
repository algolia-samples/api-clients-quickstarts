# API Key Generator
# This script will generate an API key for an Algolia application.
# The generated key will be valid for Search operations, and will be limited to 100 queries per hour.

require 'dotenv/load'
require 'algolia'

# Algolia client credentials
ALGOLIA_APP_ID = ENV['ALGOLIA_APP_ID']
ALGOLIA_API_KEY = ENV['ALGOLIA_API_KEY']
ALGOLIA_INDEX_NAME = ENV['ALGOLIA_INDEX_NAME']

# Initialize the client
# https://www.algolia.com/doc/api-client/getting-started/initialize/ruby/?client=ruby#initialize-the-search-client
client = Algolia::Search::Client.create(ALGOLIA_APP_ID, ALGOLIA_API_KEY)

# Initialize an index
# https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/#initialize-an-index
index = client.init_index(ALGOLIA_INDEX_NAME)

# Set permissions for API key
# https://www.algolia.com/doc/api-reference/api-methods/add-api-key/?client=ruby#method-param-acl
acl = ['search']

# Set the rate limited parameters for API key
# https://www.algolia.com/doc/api-reference/api-methods/add-api-key/#method-param-maxqueriesperipperhour

opts = {
    description: "TEST !! Restricted search-only API key",
    # Rate-limit to 100 requests per hour per IP address
    maxQueriesPerIPPerHour: 100
    }

# Create a new restricted search-only API key
puts "Creating key new restricted search-only API key ..."

res = client.add_api_key(acl, opts)

puts "You can check your Dashboard now ! "