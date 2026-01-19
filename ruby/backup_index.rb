# Install the API client: https://www.algolia.com/doc/libraries/sdk/install#ruby
require 'algolia'
require 'dotenv/load'

# Get your Algolia Application ID and (admin) API key from the dashboard: https://www.algolia.com/account/api-keys
# and choose a name for your index. Add these environment variables to a `.env` file:
ALGOLIA_APP_ID = ENV['ALGOLIA_APP_ID']
ALGOLIA_API_KEY = ENV['ALGOLIA_API_KEY']
ALGOLIA_INDEX_NAME = ENV['ALGOLIA_INDEX_NAME']

# Initialise the client and connect to it
# https://www.algolia.com/doc/libraries/sdk/methods/search#ruby
client = Algolia::SearchClient.create(ALGOLIA_APP_ID, ALGOLIA_API_KEY)

# Retrieve records
# https://www.algolia.com/doc/libraries/sdk/methods/search/browse-objects#ruby
puts 'Retrieving records...'
records = client.browse_objects(ALGOLIA_INDEX_NAME, { query: "" })
puts "#{records.length} records retrieved"