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

// Changes an index's settings. Only specified settings are overridden; unspecified settings are left unchanged
// https://www.algolia.com/doc/libraries/sdk/methods/search/set-settings

(async () => {
  try {
    const response = await client.setSettings({
      indexName: indexName,
      indexSettings: { paginationLimitedTo: 10, typoTolerance: "false" },
      // Option to forward the same settings to the replica indices.
      forwardToReplicas: true,
    });
    
    // print the response
    console.log(response);

    // Wait for the indexing task to complete
    // https://www.algolia.com/doc/libraries/sdk/methods/search/wait-for-task
    await client.waitForTask({ indexName: indexName, taskID: response.taskID });

    // Get the index settings
    // https://www.algolia.com/doc/libraries/sdk/methods/search/get-settings
    const settings = await client.getSettings({
      indexName: indexName,
      getVersion: 2,
    });

    // Display both changed settings
    console.log(settings["paginationLimitedTo"], settings["typoTolerance"]);
  } catch (error) {
    console.log(`Error retrieving data ${error.message}`);
  }
})();