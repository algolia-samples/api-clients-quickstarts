// Install the API client: https://www.algolia.com/doc/libraries/sdk/install#javascript
import { algoliasearch } from "algoliasearch";
import 'dotenv/config'

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

// Add new object to the index
// https://www.algolia.com/doc/libraries/sdk/methods/search/save-object
const newObject = { objectID: 1, name: "Foo" };

const { taskID } = await client.saveObject({
  indexName,
  body: newObject,
});

// Wait until indexing is done
// https://www.algolia.com/doc/libraries/sdk/methods/search/wait-for-task
await client.waitForTask({
  indexName,
  taskID,
});

// Search for "test"
// https://www.algolia.com/doc/libraries/sdk/methods/search/search
const { results } = await client.search({
  requests: [
    {
      indexName,
      query: "Fo",
    },
  ],
});


// Check the reponse
console.log(JSON.stringify(results));

