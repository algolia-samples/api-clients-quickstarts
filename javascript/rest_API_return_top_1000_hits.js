/*
Analytics API Query
This script makes a GET request to the /searches endpoint on the Analytics REST API - https://www.algolia.com/doc/rest-api/analytics/. 
To get the top 1000 searches over the last 7 days.
There is no API client for Analytics, so this script uses the JavaScript Requests library to make the call.
*/
try {
const dotenv = require("dotenv");
dotenv.config();

// Get your Algolia Application ID and (admin) API key from the dashboard: https://www.algolia.com/account/api-keys
// use the name of the index you want to target. Add these environment variables to the `.env` file:
const ALGOLIA_APP_ID = process.env.ALGOLIA_APP_ID;
const ALGOLIA_API_KEY = process.env.ALGOLIA_API_KEY;
const ALGOLIA_INDEX_NAME = process.env.ALGOLIA_INDEX_NAME;
const URL_DOMAIN = process.env.URL_DOMAIN;

const fs = require("fs");

/*
# The Analytics API can be reached from multiple domains, each specific to a region. 
# You should use the domain that matches the region where your analytics data is stored and processed. 
# View your analytics region: https://www.algolia.com/infra/analytics
# The following domains are available:
# United States: https://analytics.us.algolia.com
# Europe (Germany): https://analytics.de.algolia.com
Add this environment variable to the '.env' file 
*/

let url = `https://analytics.${URL_DOMAIN}.algolia.com`;

// // Create fetch request to Rest API for top searches limited to 1000
// https://www.algolia.com/doc/rest-api/analytics/#get-top-searches
(async () => {
  const response = await fetch(
    `${url}/2/searches?index=${ALGOLIA_INDEX_NAME}&limit=1000`,
    {
      method: "GET",
      headers: {
        "X-Algolia-API-Key": `${ALGOLIA_API_KEY}`,
        "X-Algolia-Application-Id": `${ALGOLIA_APP_ID}`,
      },
    }
  );
  if (!response.ok) {
    throw new Error(`HTTP error! status: ${response.status}`);
  }
  const data = await response.json();
  console.log("creating JSON file...");
  // Create JSON file and export to current directory
  fs.writeFile(
    `${ALGOLIA_INDEX_NAME}_top_1000_searches.json`,
    JSON.stringify(data),
    (err) => {
      if (err) throw err;
    }
  );
  console.log(`JSON file "${ALGOLIA_INDEX_NAME}_top_1000_searches" created!`);
})();
} catch (er) {
  console.log(er)
}
