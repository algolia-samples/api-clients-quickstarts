'''
Analytics API Query

This script makes a GET request to the /searches endpoint on the Analytics REST API - https://www.algolia.com/doc/rest-api/analytics/. 
To get the top 1000 searches over the last 7 days.

There is no API client for Analytics, so this script uses the Python Requests library to make the call.

The script uses the default 'analytics region' - https://analytics.us.algolia.com. Check where your analytics data is stored
and update the 'ANALYTICS_DOMAIN' variable accordingly. Your analytics region can be found here - https://www.algolia.com/infra/analytics
'''

import requests
import csv
from os import getenv

from dotenv import find_dotenv, load_dotenv

load_dotenv(find_dotenv())

# Get your Algolia Application ID and (admin) API key from the dashboard: https://www.algolia.com/account/api-keys
# and choose a name for your index. Add these environment variables to a `.env` file:
ALGOLIA_APP_ID = getenv('ALGOLIA_APP_ID')
ALGOLIA_API_KEY = getenv('ALGOLIA_API_KEY')
ALGOLIA_INDEX_NAME = getenv('ALGOLIA_INDEX_NAME')

ANALYTICS_DOMAIN = "https://analytics.algolia.com"

# Initialise requests session
client = requests.Session()

# Set required session headers
client.headers = {"X-Algolia-API-Key": ALGOLIA_API_KEY, "X-Algolia-Application-Id": ALGOLIA_APP_ID}

# Set the index and limit parameters. You can also set params for startDate, endDate, orderBy and more - https://www.algolia.com/doc/rest-api/analytics/#get-top-searches
params = {'index': {ALGOLIA_INDEX_NAME}, 'limit': 1000}

# Make the call to the analytics endpoint
print('Fetching analytics data...')
try:
    response = client.request("GET", f'{ANALYTICS_DOMAIN}/2/searches', params=params)
except requests.RequestException:
    raise

if response.status_code != 200:
    print(f'Problem with request: {response.status_code}. Exiting.')
    exit()

print('Analytics data retrieved.')

content = response.json()
searches = content['searches']

print('Creating CSV file...')

# Create a csv file with the data
try:
    keys = searches[0].keys()
    with open(f'{ALGOLIA_INDEX_NAME}_top_1000_searches.csv', 'w') as f:
        dict_writer = csv.DictWriter(f, keys, lineterminator='\n')
        dict_writer.writeheader()
        dict_writer.writerows(searches)
except Exception:
    print('Error creating csv file.')
    exit()

print(f'CSV file "{ALGOLIA_INDEX_NAME}_top_1000_searches.csv" created.')