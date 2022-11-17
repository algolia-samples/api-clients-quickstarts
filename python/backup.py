"""
Backup Index

This script will export an index, including any settings, rules and synonyms.

It can be used in conjunction with restore.py to backup and restore an index to an application.

"""

import json
import os.path
from os import getenv

# Install the API client: https://www.algolia.com/doc/api-client/getting-started/install/python/?client=python
from algoliasearch.search_client import SearchClient
from dotenv import find_dotenv, load_dotenv

load_dotenv(find_dotenv())

# Get your Algolia Application ID and (admin) API key from the dashboard: https://www.algolia.com/account/api-keys
# and choose a name for your index. Add these environment variables to a `.env` file:
ALGOLIA_APP_ID = getenv('ALGOLIA_APP_ID')
ALGOLIA_API_KEY = getenv('ALGOLIA_API_KEY')
ALGOLIA_INDEX_NAME = getenv('ALGOLIA_INDEX_NAME')

# Set the path for the current file, we'll need this when generating the exports
current_path = os.path.dirname(os.path.abspath(__file__))

# Start the API client
# https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/
client = SearchClient.create(ALGOLIA_APP_ID, ALGOLIA_API_KEY)

# Create an index (or connect to it, if an index with the name `ALGOLIA_INDEX_NAME` already exists)
# https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/#initialize-an-index
index = client.init_index(ALGOLIA_INDEX_NAME)

# Initialise lists to store the exported data
records, settings, rules, synonyms = [], [], [], []

# Get the index records and any associated rules, settings, and synonyms.
try:
    # Get all the records from the index and store them in a list
    print('Retrieving records...')
    records = list(index.browse_objects())
    print(f'{len(records)} records retrieved.')

    # Get the settings for the index
    print('Retrieving settings...')
    settings = index.get_settings()
    print('Settings retrieved.')

    # Get the rules for the index
    print('Retrieving rules...')
    rules = list(index.browse_rules())
    print(f'{str(len(rules))} rules retrieved.')

    # Get the synonyms for the index
    print('Retrieving synonyms...')
    synonyms = list(index.browse_synonyms())
    print(f'{str(len(synonyms))} synonyms retrieved.')
except Exception as e:
    print(f'Error retrieving data: {e}')
    exit()

# Create a function to help with writing the output data
def create_json(data, name, path):
    '''
    Converts a list to a json file
    '''
    if data:
        json_data = json.dumps(data)
        with open(f'{path}/{ALGOLIA_INDEX_NAME}_{name}.json', 'w', encoding='utf-16') as file:
            file.write(json_data)

# Write the exported data to output files
print('Exporting data...')
try:
    create_json(records, 'index', current_path)
    print('Exported index.')
    create_json(settings, 'settings', current_path)
    print('Exported settings.')
    create_json(rules, 'rules', current_path)
    print('Exported rules.')
    create_json(synonyms, 'synonyms', current_path)
    print('Exported synonyms.')
    print('Data exported successfully.')
except Exception as e:
    print(f'Error exporting data: {e}')
    exit()
