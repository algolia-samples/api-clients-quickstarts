// Install the API client: https://www.algolia.com/doc/api-client/getting-started/install/javascript/?client=javascript
const algoliasearch = require("algoliasearch");
const dotenv = require("dotenv");

// dotenv.config();

// // Get your Algolia Application ID and (admin) API key from the dashboard: https://www.algolia.com/account/api-keys
// // and choose a name for your index. Add these environment variables to a `.env` file:
// const ALGOLIA_APP_ID = process.env.ALGOLIA_APP_ID;
// const ALGOLIA_API_KEY = process.env.ALGOLIA_API_KEY;
// const ALGOLIA_INDEX_NAME = process.env.ALGOLIA_INDEX_NAME;

// Start the API client
// https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/
const client = algoliasearch("OA5TOH2VFB", "66b73691a7304fe7947039f75192d522");

// Create an index (or connect to it, if an index with the name `ALGOLIA_INDEX_NAME` already exists)
// https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/#initialize-an-index
const index = client.initIndex("export_rules");


index.browseRules({
    // A `batch` callback function that's called
    // on every batch of rules with the
    // signature: (rules: Rules[]) => any
    batch: batch => console.log(batch)
  }).then((response)=> {
    // console.log(response)
  })

  // Create a rule
  const rule = {
    objectID: 'a-rule-id',
    conditions: [{
      pattern: 'Jimmie',
      anchoring: 'is'
    }],
    consequence: {
      params: {
        filters: 'zip_code = 12345'
      }
    },
  
    // Optionally, to disable the rule
    enabled: true,
  
    // Optionally, to add valitidy time ranges
    validity: [
      {
        from: Math.floor(Date.now()/1000),
        until: Math.floor(Date.now()/1000) + 10*24*60*60,
      }
    ]
  };
  
  // Save the Rule.
  index.saveRule(rule).then(() => {
    // done
  });
  
  // Save the Rule, and forward it to all replicas of the index.
  index.saveRule(rule, {forwardToReplicas: true}).then(() => {
    // done
  });
  