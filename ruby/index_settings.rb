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

# Changing index settings
# https://www.algolia.com/doc/libraries/sdk/methods/search/set-settings
response = client.set_settings(ALGOLIA_INDEX_NAME, {
  # https://www.algolia.com/doc/api-reference/api-parameters/typoTolerance
  typoTolerance: true,
  # https://www.algolia.com/doc/api-reference/api-parameters/queryLanguages
  queryLanguages: ['es'],
  # https://www.algolia.com/doc/api-reference/api-parameters/ignorePlurals
  ignorePlurals: true
})

# Wait for asynchronous task to complete
# https://www.algolia.com/doc/libraries/sdk/methods/search/wait-for-task
client.wait_for_task(ALGOLIA_INDEX_NAME, response.task_id)

# Retrieve new settings
# https://www.algolia.com/doc/libraries/sdk/methods/search/get-settings
response = client.get_settings(ALGOLIA_INDEX_NAME)
puts response