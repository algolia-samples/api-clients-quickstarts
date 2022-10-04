from algoliasearch.search_client import SearchClient

# Import the environment variables from the .env file
from dotenv import load_dotenv
import os

load_dotenv()

# Algolia client credentials
ALGOLIA_APP_ID = os.getenv('ALGOLIA_APP_ID')
ALGOLIA_API_KEY = os.getenv('ALGOLIA_API_KEY')
ALGOLIA_INDEX_NAME = os.getenv('ALGOLIA_INDEX_NAME')

# Initialize the Algolia Client
client = SearchClient.create(ALGOLIA_APP_ID, ALGOLIA_API_KEY)

# Initialize an index
index = client.init_index(ALGOLIA_INDEX_NAME)

# Example of a record structure 
# record = {
  # "objectID": 1,
  # "name": "John Doe",
  # "address": "London",
  # "followers": 5
  # }

# Set your index settings 
index.set_settings({
  "searchableAttributes": ["name", "address"],
  'attributesForFaceting': ["name", "filterOnly(address)","searchable(followers)"],
  "customRanking": ["desc(followers)"]
})

# Get index configuration
print(index.get_settings())
