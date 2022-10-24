from algoliasearch.search_client import SearchClient

# Import the environment variables from the .env file
from dotenv import load_dotenv
import os

load_dotenv()

# Algolia client credentials
ALGOLIA_APP_ID = os.getenv('ALGOLIA_APP_ID')
ALGOLIA_API_KEY = os.getenv('ALGOLIA_API_KEY')
ALGOLIA_INDEX_NAME = os.getenv('ALGOLIA_INDEX_NAME')

# Initialize the client
# https://www.algolia.com/doc/api-client/getting-started/initialize/python/?client=python
client = SearchClient.create(ALGOLIA_APP_ID, ALGOLIA_API_KEY)

# Initialize the index
# https://www.algolia.com/doc/api-client/getting-started/initialize/python/?client=python
index = client.init_index(ALGOLIA_INDEX_NAME)

# Set your index settings 
# attributesForFaceting
# https://www.algolia.com/doc/api-reference/api-parameters/attributesForFaceting/
# filterOnly : the attribute will be filterable only and not facetable.
# searchable : the attribute will be be searchable.
# customRanking : You can decide whether the order should be descending or ascending.
# https://www.algolia.com/doc/guides/managing-results/must-do/custom-ranking/
index.set_settings({
  "searchableAttributes": ["name", "address"],
  'attributesForFaceting': ["name", "filterOnly(address)","searchable(followers)"],
  "customRanking": ["desc(followers)"]
})

# Get index configuration
print(index.get_settings())
