// Install the API client: https://www.algolia.com/doc/libraries/sdk/install#javascript
import { algoliasearch } from "algoliasearch";
import "dotenv/config";
import * as fs from "fs";

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

(async () => {
  // Export Rules for this index
  // https://www.algolia.com/doc/libraries/sdk/methods/search/browse-rules
  await client.browseRules({
    indexName,
    aggregator: (res) =>
      fs.writeFile(
        `${ALGOLIA_INDEX_NAME}_rules.json`,
        JSON.stringify(res.hits),
        (err) => {
          // In case of a error throw err.
          if (err) throw err;
        }
      ),
  });

  console.log(
    `Rules saved as ${ALGOLIA_INDEX_NAME}_rules.json in the current directory`
  );

  // Create a rule
  const rule = {
    objectID: "a-rule-id",
    conditions: [
      {
        pattern: "Jimmie",
        anchoring: "is",
      },
    ],
    consequence: {
      params: {
        filters: "zip_code = 12345",
      },
    },

    // Optionally, to disable the rule change to 'false'
    enabled: true,

    // Optionally, to add valitidy time ranges
    validity: [
      {
        from: Math.floor(Date.now() / 1000),
        until: Math.floor(Date.now() / 1000) + 10 * 24 * 60 * 60,
      },
    ],
  };

  // Save the Rule, and forward it to all replicas of the index.
  const response = await client.saveRule({
    indexName: indexName,
    objectID: "a-rule-id",
    forwardToReplicas: true,
    rule: rule,
  });

  // Log the response
  console.log(response);
})();
