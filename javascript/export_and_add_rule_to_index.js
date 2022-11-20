// Install the API client: https://www.algolia.com/doc/api-client/getting-started/install/javascript/?client=javascript
const algoliasearch = require("algoliasearch");
const dotenv = require("dotenv");
// Requiring fs module in which writeFile function is defined.
const fs = require("fs");

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

// Export Rules for this index
// https://www.algolia.com/doc/api-reference/api-methods/export-rules/
index
  .browseRules({
    // A `batch` callback function that's called on every batch of rules
    batch: (batch) =>
      // Export JSON file containing Rules into same directory with prefix of index_name
      fs.writeFile(
        `${ALGOLIA_INDEX_NAME}_rules.json`,
        JSON.stringify(batch),
        (err) => {
          // In case of a error throw err.
          if (err) throw err;
        }
      ),
  })
  .then((response) => {
    // Success message
    console.log(
      `Rules saved as ${ALGOLIA_INDEX_NAME}_rules.json in the current directory`
    );
  })
  .catch((error) => console.log(error));

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

// Save the Rule.
index.saveRule(rule).then(() => {
  // done
});

// Save the Rule, and forward it to all replicas of the index.
index.saveRule(rule, { forwardToReplicas: true }).then(() => {
  // done
});
