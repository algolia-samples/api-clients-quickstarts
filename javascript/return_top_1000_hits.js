// Install the API client: https://www.algolia.com/doc/libraries/sdk/install#javascript
import { algoliasearch } from "algoliasearch";
import "dotenv/config";
import * as fs from "fs";

// Get your Algolia Application ID and (admin) API key from the dashboard: https://www.algolia.com/account/api-keys
// use the name of the index you want to target. Add these environment variables to the `.env` file:
const ALGOLIA_APP_ID = process.env.ALGOLIA_APP_ID;
const ALGOLIA_API_KEY = process.env.ALGOLIA_API_KEY;
const ALGOLIA_INDEX_NAME = process.env.ALGOLIA_INDEX_NAME;
const ALGOLIA_REGION = process.env.ALGOLIA_REGION;

(async () => {
  const indexName = ALGOLIA_INDEX_NAME;
  const region = ALGOLIA_REGION;
  
  const client = algoliasearch(ALGOLIA_APP_ID, ALGOLIA_API_KEY).initAnalytics({
    region: region,
  });
  
  // Method from Analytics API client to retrieve top searches. In this example limited to 1000
  // https://www.algolia.com/doc/libraries/sdk/methods/analytics/get-top-searches
  const response = await client.getTopSearches({
    index: indexName,
    limit: 1000,
  });

  console.log(response);

  console.log("creating JSON file...");
  // Create JSON file and export to current directory
  fs.writeFile(
    `${ALGOLIA_INDEX_NAME}_top_1000_searches.json`,
    JSON.stringify(response),
    (err) => {
      if (err) throw err;
    }
  );
  console.log(`JSON file "${ALGOLIA_INDEX_NAME}_top_1000_searches" created!`);
})();
