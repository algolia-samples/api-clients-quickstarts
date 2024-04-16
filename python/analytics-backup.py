'''
Analytics Backup - Retrieve the last 7 days of analytics data

This script fetches data from the Analytics REST API - https://www.algolia.com/doc/rest-api/analytics/ and writes it with a
timestamp to a local file. Analytics data is only available via the Dashboard for a maximum 365 days, so this script allows users
to backup their data.

There is no API client for Analytics, so this script uses the Python Requests library to make the call.
'''

import json
from datetime import datetime
from os import getenv

import requests
from dotenv import find_dotenv, load_dotenv

load_dotenv(find_dotenv())

# Get and set the current timestamp
now = datetime.now()
timestamp = now.strftime("%Y-%m-%d_%H:%M:%S")

# Get your Algolia Application ID, (analytics) API key, and Index name from the dashboard: https://www.algolia.com/account/api-keys
# Get your Algolia analytics region here: https://www.algolia.com/infra/analytics
# Read more about analytics domains here: https://www.algolia.com/doc/rest-api/analytics/
# Add these environment variables to a `.env` file (you can use the .env.example file as a template).
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


# Create a function to validate date inputs
def validate(date_string):
    try:
        if date_string != datetime.strptime(date_string, "%Y-%m-%d").strftime('%Y-%m-%d'):
            raise ValueError
        return True
    except ValueError:
        print("Incorrect date format.")
        return False


# Create a function to handle the API calls.
# If there is an error querying an endpoint, the script will continue but the value for that metric will be null.
def make_request(endpoint, metric, start_date='', end_date=''):
    try:
        if not start_date:
            response = client.request("GET", f'{ANALYTICS_DOMAIN}/2/{endpoint}', params=params)
        else:
            params["startDate"] = start_date
            params["endDate"] = end_date
            response = client.request("GET", f'{ANALYTICS_DOMAIN}/2/{endpoint}', params=params)

        if response.status_code != 200:
            print(f'Problem with {endpoint} request: {response.status_code}. No data collected.')
            return

        data = response.json()

        return data[metric]
    except:
        print(f'Could not complete analytics retrieval for {endpoint, metric}')


# Ask the user whether they want to enter a custom date range for analytics retrieval.
# NOTE: There is a limit to the period that analytics data is held for. Either 90 or 360 days, depending on your plan.
# The script will except start dates before the beginning of your analytics retention period.
custom_date_range = input('Default date range is last 7 days. Do you want to enter a custom date range for analytics retrieval? (Y/N): ').lower()

while custom_date_range not in ['y', 'n', 'yes', 'no']:
    custom_date_range = input('Default date range is last 7 days. Do you want to enter a custom date range for analytics retrieval? (Y/N): ').lower()

# Ask the user for their required start and end dates.
if custom_date_range in ['y', 'yes']:
    start_date = input('Enter start date for analytics retrieval (YYYY-MM-DD): ')
    while not validate(start_date):
        start_date = input('Enter start date for analytics retrieval (YYYY-MM-DD): ')

    end_date = input('Enter end date for analytics retrieval (YYYY-MM-DD): ')
    while not validate(end_date):
        end_date = input('Enter end date for analytics retrieval (YYYY-MM-DD): ')
    
    date_params = {'start_date': start_date, 'end_date': end_date}
else:
    date_params = {}

# Create an array for API endpoints and the metric we want to target from each.
queries = [
    {'endpoint': 'searches', 'metric': 'searches'}, 
    {'endpoint': 'searches/count', 'metric': 'count'},
    {'endpoint': 'searches/noResults', 'metric': 'searches'},
    {'endpoint': 'users/count', 'metric': 'count'},
    {'endpoint': 'hits', 'metric': 'hits'},
    {'endpoint': 'searches/noResultRate', 'metric': 'rate'},
    {'endpoint': 'searches/noClickRate', 'metric': 'rate'}
]

# Make the requests and create an object with the results.
print('Retrieving analytics data...')
results = {query['endpoint']: make_request(query['endpoint'], query['metric'], **date_params) for query in queries}
print('Analytics data retrieved')
# Define the object structure that will be output to JSON
output = {
    timestamp: {
        "top_searches": results['searches'],
        "total_searches": results['searches/count'],
        "searches_without_results": results['searches/noResults'],
        "total_users": results['users/count'],
        "top_results": results['hits'],
        "no_results_rate": results['searches/noResultRate'],
        "no_clicks_rate": results['searches/noClickRate'],
        "date_range": date_params if date_params else 'Last 7 days'
    }
}

# Create a JSON file with the analytics data
print("Creating output...")
filename = f'{ALGOLIA_INDEX_NAME}_analytics_{timestamp}.json'
try:
    with open(filename, 'w') as file:
        json.dump(output, file)
    print(f'Output file - {filename} - created.')
except Exception:
    raise Exception ('Error creating json file.')



