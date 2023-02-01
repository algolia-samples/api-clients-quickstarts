'''
Analytics Backup - Retrieve the last 7 days of analytics data

This script fetches data from the Analytics REST API - https://www.algolia.com/doc/rest-api/analytics/ and writes it with a
timestamp to a local file. Analytics data is only available via the Dashboard for a maximum 365 days, so this script allows users
to backup their data.

There is no API client for Analytics, so this script uses the Python Requests library to make the call.
'''

import requests
import json
from os import getenv
from datetime import datetime

from dotenv import find_dotenv, load_dotenv

load_dotenv(find_dotenv())

now = datetime.now()
timestamp = now.strftime("%Y-%m-%d_%H:%M:%S")

# Get your Algolia Application ID, (analytics) API key, and Index name from the dashboard: https://www.algolia.com/account/api-keys
# Get your Algolia analytics domain here: https://www.algolia.com/infra/analytics
# Add these environment variables to a `.env` file:
ALGOLIA_APP_ID = getenv('ALGOLIA_APP_ID')
ALGOLIA_API_KEY = getenv('ALGOLIA_API_KEY')
ALGOLIA_INDEX_NAME = getenv('ALGOLIA_INDEX_NAME')
ANALYTICS_DOMAIN = getenv('ALGOLIA_ANALYTICS_DOMAIN')

# Initialise requests session
client = requests.Session()

# Set required session headers
client.headers = {"X-Algolia-API-Key": ALGOLIA_API_KEY, "X-Algolia-Application-Id": ALGOLIA_APP_ID}

# Set the index and limit parameters. You can also set params for startDate, endDate, orderBy and more - https://www.algolia.com/doc/rest-api/analytics/#get-top-searches
params = {'index': {ALGOLIA_INDEX_NAME}, 'limit': 100}

# Create a function to handle the API calls.
def make_request(endpoint, metric):
    try:
        response = client.request("GET", f'{ANALYTICS_DOMAIN}/2/{endpoint}', params=params)
    except requests.RequestException:
        raise

    if response.status_code != 200:
        print(f'Problem with {endpoint} request: {response.status_code}. No data collected.')
        return

    data = response.json()
    return data[metric]

# Retrieve the analytics data
top_searches = make_request('searches', 'searches')
total_searches = make_request('searches/count', 'count')
top_no_results = make_request('searches/noResults', 'searches')
user_count = make_request('users/count', 'count')
top_results = make_request('hits', 'hits')
no_result_rate = make_request('searches/noResultRate', 'rate')
no_click_rate = make_request('searches/noClickRate', 'rate')

# Define the object structure that will be output to JSON
output = {
    timestamp: {
        "top_searches": top_searches,
        "total_searches": total_searches,
        "searches_without_results": top_no_results,
        "total_users": user_count,
        "top_results": top_results,
        "no_results_rate": no_result_rate,
        "no_clicks_rate": no_click_rate
    }
}

# Create a JSON file with the analytics data
try:
    with open(f'analytics-backup_{timestamp}.json', 'w') as file:
        json.dump(output, file)
except Exception:
    raise Exception ('Error creating json file.')



