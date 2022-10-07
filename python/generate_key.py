"""
API Key Generator

This script will generate an API key for an Algolia application.

The generated key will be valid for Search operations, and will be limited to 100 queries per hour.

"""

from os import getenv

# Install the API client: https://www.algolia.com/doc/api-client/getting-started/install/python/?client=python
from algoliasearch.search_client import SearchClient
from dotenv import find_dotenv, load_dotenv
import json

load_dotenv(find_dotenv())

# Get your Algolia Application ID and (admin) API key from the dashboard: https://www.algolia.com/account/api-keys
# and choose a name for your index. Add these environment variables to a `.env` file:
ALGOLIA_APP_ID = getenv('ALGOLIA_APP_ID')
ALGOLIA_API_KEY = getenv('ALGOLIA_API_KEY')
ALGOLIA_INDEX_NAME = getenv('ALGOLIA_INDEX_NAME')

# Start the API client
# https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/
client = SearchClient.create(ALGOLIA_APP_ID, ALGOLIA_API_KEY)

# Create the key
#https://www.algolia.com/doc/api-reference/api-methods/add-api-key/?client=python

# Set the permissions for the key
acl = ['search']

# Set the parameters for the key
params = {
    'description': 'Restricted search-only API key for algolia.com',
    # Rate-limit to 100 requests per hour per IP address
    'maxQueriesPerIPPerHour': 100
}

# Make the API request to add the key
try:
    res = client.add_api_key(acl, params)
    key = res['key']
except Exception as e:
    print(f'Error: {e}')
    exit()

# Initialise a new client with the generated key
client = SearchClient.create(ALGOLIA_APP_ID, key)

# Create an index (or connect to it, if an index with the name `ALGOLIA_INDEX_NAME` already exists)
# https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/#initialize-an-index
index = client.init_index(ALGOLIA_INDEX_NAME)

# Test the created key
try:
    res = index.search('')
    if res:
        print(f'Key generated successfully: {key}')
except Exception as e:
    print(f'Error: {e}')
    exit()

