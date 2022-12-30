/*
Backup Index
This script will export an index, including records, settings, rules and synonyms to the current directory.
It can be used in conjunction with restore.js to backup and restore an index to an application.
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

// Requiring fs module in which writeFile function is defined.
const fs = require("fs");

let records = [],
  settings = [],
  rules = [],
  synonyms = [];

(async () => {
  // retrieve all records from index
  console.log(`Retrieving records...`);
  try {
    await index.browseObjects({
      batch: (batch) => {
        records = records.concat(batch);
      }
    });

    console.log(`${records.length} records retrieved`);

    console.log(`Retrieving settings...`);

    // retrieve all index settings
    settings = await index.getSettings().then();

    console.log(`settings retrieved`);

    console.log(`Retrieving rules...`);

    // retrieve all rules for index
    await index
      .browseRules({
        batch: (batch) => {
          rules = rules.concat(batch);
        }
      });

    console.log(`${rules.length} rules retrieved`);

    console.log(`Retrieving synonyms...`);

    // retrieve all synonyms for index
    await index
      .browseSynonyms({
        batch: (batch) => {
          synonyms = synonyms.concat(batch);
        },
      });

    console.log(`${synonyms.length} synonyms retrieved`);
  } catch (error) {
    console.log(`Error retrieving data ${error}`);
  }

  //   write json files to current directory
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
        console.log(`Error writing files: ${error}`);
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
    console.log(`Error exporting data ${error}`);
  }
})();
