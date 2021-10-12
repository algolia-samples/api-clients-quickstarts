from os import getenv

# Install the API client: https://www.algolia.com/doc/api-client/getting-started/install/python/?client=python
from algoliasearch.search_client import SearchClient
from dotenv import load_dotenv, find_dotenv

load_dotenv(find_dotenv())

# Get your Algolia Application ID and (admin) API key from the dashboard: https://www.algolia.com/account/api-keys
# and choose a name for your index. Add these environment variables to a `.env` file:
ALGOLIA_APP_ID = getenv('ALGOLIA_APP_ID')
ALGOLIA_API_KEY = getenv('ALGOLIA_API_KEY')
ALGOLIA_INDEX_NAME = getenv('ALGOLIA_INDEX_NAME')

# Start the API client
# https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/
client = SearchClient.create(ALGOLIA_APP_ID, ALGOLIA_API_KEY)

# Create an index (or connect to it, if an index with the name `ALGOLIA_INDEX_NAME` already exists)
# https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/#initialize-an-index
index = client.init_index(ALGOLIA_INDEX_NAME)

# Add new objects to the index
# https://www.algolia.com/doc/api-reference/api-methods/add-objects/
new_object = {'objectID': 1, 'name': 'Foo'}
res = index.save_objects([new_object])

# Wait for the indexing task to complete
# https://www.algolia.com/doc/api-reference/api-methods/wait-task/
res.wait()

# Search the index for "Fo"
# https://www.algolia.com/doc/api-reference/api-methods/search/
objects = index.search('Fo')
print(objects)
