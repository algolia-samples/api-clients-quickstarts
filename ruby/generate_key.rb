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

opts = {
    # Add a description
    # https://www.algolia.com/doc/rest-api/search/add-api-key#body-description
    description: "Restricted search-only API key limited to 100 API calls per hour",
    # Set the rate limited parameters for API key - Rate-limit to 100 requests per hour per IP address
    # https://www.algolia.com/doc/rest-api/search/add-api-key#body-max-queries-per-ip-per-hour
    maxQueriesPerIPPerHour: 100
}

# Create a new restricted search-only API key
puts "Creating new restricted search-only API key ..."

# Set permissions for API key and generate the key
res = client.add_api_key(acl: ['search'], **opts)

# Print the new generated search api key
new_search_api_key = res.key
puts "Your new Search only Rate Limited API Key is: #{new_search_api_key}"