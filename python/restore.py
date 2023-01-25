"""
Restore Index

This script will restore an index, including any settings, rules and synonyms.

It can be used in conjuction with backup.py to backup and restore an index to an application.

When run, the user will be prompted to enter an index name. The script will look for files prefixed
with this index name to use for the restore. E.g. if the index name is 'my-index', the script will
look for 'my-index_index.json', 'my-index_rules.json', 'my-index_settings.json' and 
'my-index_synonyms.json' files in the same directory.

This script will overwrite any existing indexes with the same name.

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
ALGOLIA_INDEX_NAME = input('Enter index name: ')

# Start the API client
# https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/
client = SearchClient.create(ALGOLIA_APP_ID, ALGOLIA_API_KEY)

# Create an index (or connect to it, if an index with the name `ALGOLIA_INDEX_NAME` already exists)
# https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/#initialize-an-index
index = client.init_index(ALGOLIA_INDEX_NAME)

# Check to see which files are available for restoration. There must be at least an index file.
current_path = os.path.dirname(os.path.abspath(__file__))

is_index_file = os.path.isfile(f'{current_path}/{ALGOLIA_INDEX_NAME}_index.json')
is_settings_file = os.path.isfile(f'{current_path}/{ALGOLIA_INDEX_NAME}_settings.json')
is_rules_file = os.path.isfile(f'{current_path}/{ALGOLIA_INDEX_NAME}_rules.json')
is_synonyms_file = os.path.isfile(f'{current_path}/{ALGOLIA_INDEX_NAME}_synonyms.json')

if not is_index_file:
    print('No index file available. Terminating script.')
    exit()

# Load the json files so that the data can be restored
with open(f'{current_path}/{ALGOLIA_INDEX_NAME}_index.json', 'r', encoding='utf-16') as f:
    index_data = json.load(f)

if is_settings_file:
    with open(f'{current_path}/{ALGOLIA_INDEX_NAME}_settings.json', 'r', encoding='utf-16') as f:
        settings_data = json.load(f)

if is_rules_file:
    with open(f'{current_path}/{ALGOLIA_INDEX_NAME}_rules.json', 'r', encoding='utf-16') as f:
        rules_data = json.load(f)

if is_synonyms_file:
    with open(f'{current_path}/{ALGOLIA_INDEX_NAME}_synonyms.json', 'r', encoding='utf-16') as f:
        synonyms_data = json.load(f)

# Check the status of the current index.
try:
    existing_records = list(index.browse_objects(
        {"attributesToRetrieve": ["objectId"]}
    ))
except Exception as e:
    print(f'Error querying existing index {e}. Exiting')
    exit()

# Confirm with the user that they're going to overwrite an existing index
confirmation = ''

while confirmation.lower() not in ['y', 'n', 'yes', 'no']:
    confirmation = input(f'\
WARNING: Continuing will overwrite {str(len(existing_records))} records \
in existing index "{ALGOLIA_INDEX_NAME}" with {str(len(index_data))} records from local index. \
Do you want to continue (Y/N): '
    )

if confirmation.lower() in ['n', 'no']:
    print('Exiting process.')
    exit()
  
# Restore the index and associated data to the application.
try:
    print('Restoring index...')
    index.replace_all_objects(index_data, {
        'safe': True
    })
    print('Index restored.')
    
    if is_settings_file:
        print('Restoring settings...')
        index.set_settings(settings_data)
        print('Settings restored...')

    if is_rules_file:
        print('Restoring rules...')
        index.replace_all_rules(rules_data)
        print('Rules restored...')

    if is_synonyms_file:
        print('Restoring synonyms...')
        index.replace_all_synonyms(synonyms_data)
        print('Synonyms restored...')
except Exception as e:
    print(f'Error restoring index data: {e}')
    exit()