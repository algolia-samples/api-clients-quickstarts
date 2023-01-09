'''
Analytics API Query

This script fetches data from the Analytics REST API - https://www.algolia.com/doc/rest-api/analytics/ and writes it with a
timestamp to a local file. Analytics data is only available via the Dashboard for a maximum 365 days, so this script allows users
to backup their data.

There is no API client for Analytics, so this script uses the Python Requests library to make the call.
'''

import requests
import csv
from os import getenv

from dotenv import find_dotenv, load_dotenv

load_dotenv(find_dotenv())

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