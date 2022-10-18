/*
API Key Generator
This script will generate an API key for an Algolia application.
The generated key will be valid for Search operations, and will be limited to 100 queries per hour.
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
let client = algoliasearch(ALGOLIA_APP_ID, ALGOLIA_API_KEY);

// Create an index (or connect to it, if an index with the name `ALGOLIA_INDEX_NAME` already exists)
// https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/#initialize-an-index
let index = client.initIndex(ALGOLIA_INDEX_NAME);

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

console.log("Creating key...");
client
  .addApiKey(acl, params)
  .wait()
  .then((response) => {
    const NEW_API_KEY = response["key"];
    console.log(`Key generated successfully: ${NEW_API_KEY}`);

    console.log("Testing the key...");

    // Start the API client
    client = algoliasearch(ALGOLIA_APP_ID, NEW_API_KEY);

    // # Create an index (or connect to it, if an index with the name `ALGOLIA_INDEX_NAME` already exists)
    index = client.initIndex(ALGOLIA_INDEX_NAME);

    // # Implement an empty search query
    const res = index.search("");

    // # Check that the search works
    !res ? console.log(`Error generating key`) : console.log(`New key connected to App successfully`);
  });