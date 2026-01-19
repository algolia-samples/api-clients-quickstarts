/*
Backup Index
This script will export an index, including records, settings, rules and synonyms to the current directory.
It can be used in conjunction with restore.js to backup and restore an index to an application.
*/

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

let records = [],
  settings = [],
  rules = [],
  synonyms = [];

(async () => {
  try {
    console.log(`Retrieving records...`);
    
    // retrieve all records from index
    // https://www.algolia.com/doc/libraries/sdk/methods/search/browse-objects#javascript
    await client.browseObjects({
      indexName,
      aggregator: (res) => {
        records.push(...res.hits);
      },
    });

    console.log(`${records.length} record(s) retrieved`);

    console.log(`Retrieving settings...`);

    // retrieve all index settings
    // https://www.algolia.com/doc/libraries/sdk/methods/search/get-settings
    settings = await client.getSettings({ indexName: indexName }).then();

    console.log(`settings retrieved`);

    console.log(`Retrieving rules...`);

    // retrieve all rules for index
    // https://www.algolia.com/doc/libraries/sdk/methods/search/browse-rules
    await client.browseRules({
      indexName,
      aggregator: (res) => {
        rules.push(...res.hits);
      },
    });

    console.log(`${rules.length} rules retrieved`);

    console.log(`Retrieving synonyms...`);

    // retrieve all synonyms for index
    // https://www.algolia.com/doc/libraries/sdk/methods/search/browse-synonyms
    await client.browseSynonyms({
      indexName,
      aggregator: (res) => {
        synonyms.push(...res.hits);
      },
    });

    console.log(`${synonyms.length} synonyms retrieved`);
  } catch (error) {
    console.log(`Error retrieving data ${error.message}`);
  }

  // write json files to current directory
  function createJson(data, name) {
    if (data) {
      fs.writeFile(
        `${ALGOLIA_INDEX_NAME}_${name}.json`,
        JSON.stringify(data),
        (err) => {
          if (err) throw err;
        }
      );
    } else
      (error) => {
        console.log(`Error writing files: ${error.message}`);
      };
  }

  try {
    let name = "records";
    createJson(records, name);
    name = "settings";
    createJson(settings, name);
    name = "rules";
    createJson(rules, name);
    name = "synonyms";
    createJson(synonyms, name);
  } catch (error) {
    console.log(`Error exporting data ${error.message}`);
  }
})();
