/*
Change Settings
This Script is an exmaple of how to change index settings using the API client.
*/

// Install the API client: https://www.algolia.com/doc/api-client/getting-started/install/javascript/?client=javascript
const algoliasearch = require("algoliasearch");
const dotenv = require("dotenv");

dotenv.config();

// Get your Algolia Application ID and (admin) API key from the dashboard: https://www.algolia.com/account/api-keys
// and choose a name for your index. Add these environment variables to a `.env` file:
const ALGOLIA_APP_ID = process.env.ALGOLIA_APP_ID;
const ALGOLIA_API_KEY = process.env.ALGOLIA_API_KEY;
const ALGOLIA_INDEX_NAME = process.env.ALGOLIA_INDEX_NAME;

// Start the API client
// https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/
const client = algoliasearch(ALGOLIA_APP_ID, ALGOLIA_API_KEY);

// Create an index (or connect to it, if an index with the name `ALGOLIA_INDEX_NAME` already exists)
// https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/#initialize-an-index
const index = client.initIndex(ALGOLIA_INDEX_NAME);

// Change the settings in the index
// https://www.algolia.com/doc/api-reference/api-methods/set-settings/

// try catch for errors
try {
  index.setSettings({
      'searchableAttributes': ['name', 'address'],
      'customRanking': ['desc(followers)']
    }).wait().then(() => {
        // done
  
  });
} catch (error) {
  console.error("An error occurred: ", error);
}
