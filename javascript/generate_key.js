/*
  API Key Generator
  This script will generate an API key for an Algolia application.
  The generated key will be valid for Search operations, and will be limited to 100 queries per hour.
*/

// Install the API client: https://www.algolia.com/doc/libraries/sdk/install#javascript
import { algoliasearch } from "algoliasearch";
import "dotenv/config";

// Get your Algolia Application ID and (admin) API key from the dashboard: https://www.algolia.com/account/api-keys
// and choose a name for your index. Add these environment variables to a `.env` file:
const ALGOLIA_APP_ID = process.env.ALGOLIA_APP_ID;
const ALGOLIA_API_KEY = process.env.ALGOLIA_API_KEY;
const ALGOLIA_INDEX_NAME = process.env.ALGOLIA_INDEX_NAME;

// Start the API client
// https://www.algolia.com/doc/libraries/sdk/install#test-your-installation
const client = algoliasearch(ALGOLIA_APP_ID, ALGOLIA_API_KEY);

// Create an index name (or connect to it, if an index with the name `ALGOLIA_INDEX_NAME` already exists)
// https://www.algolia.com/doc/libraries/sdk/install#test-your-installation
const indexName = ALGOLIA_INDEX_NAME || "new_index_name";

// Set permissions for API key
// https://www.algolia.com/doc/api-reference/api-methods/add-api-key/#method-param-acl
const acl = ["search"];

// Set the rate limited parameters for API key
// https://www.algolia.com/doc/api-reference/api-methods/add-api-key/#method-param-maxqueriesperipperhour

const params = {
  description: "Restricted search-only API key for algolia.com",
  // Rate-limit to 100 requests per hour per IP address
  maxQueriesPerIPPerHour: 100,
};

// Create a new restricted search-only API key
(async () => {
  console.log("Creating key...");
  const response = await client.addApiKey({ acl: acl, ...params });

  const NEW_API_KEY = response["key"];
  console.log(`Key generated successfully: ${NEW_API_KEY}`);
  console.log(`---------`);
  console.log("Testing the key...");

  // Loop to wait until the key is created
  while (true) {
    try {
      await client.getApiKey({ key: NEW_API_KEY });
      console.log("API key is active!");
      break;
    } catch (err) {
      if (err.status !== 404) throw err;
      await new Promise((r) => setTimeout(r, 500));
    }
  }

  // Start the API client
  const newClient = algoliasearch(ALGOLIA_APP_ID, NEW_API_KEY);

  // Implement an empty search query
  const newResponse = await newClient.search({
    requests: [{ indexName: indexName, query: "", hitsPerPage: 50 }],
  });

  // const newResponse = await index.search("", { hitsPerPage: 50 });

  console.log(newResponse);
  // Checking connection of new API key to index
  try {
    !Object.keys(newResponse).length
      ? console.log(`Error connecting new key to index`)
      : console.log(`New key connected to index successfully`);
  } catch (error) {
    console.log(error);
  }
})();

// client
//   .addApiKey(acl, params)
//   .wait()
//   .then((response) => {
//   });
