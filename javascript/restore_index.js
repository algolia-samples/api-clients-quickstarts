/*
Restore Index

This script will restore an index, including any settings, rules and synonyms.

It can be used in conjuction with backup.py to backup and restore an index to an application.

When run, the user will be prompted to enter an index name. The script will look for files prefixed
with this index name to use for the restore. E.g. if the index name is 'my-index', the script will
look for 'my-index_index.json', 'my-index_rules.json', 'my-index_settings.json' and 
'my-index_synonyms.json' files in the same directory.

This script will overwrite any existing indexes with the same name.
*/

// Install the API client: https://www.algolia.com/doc/api-client/getting-started/install/javascript/?client=javascript
const algoliasearch = require("algoliasearch");
const dotenv = require("dotenv");
dotenv.config();

// Requiring fs module in which writeFile function is defined.
const fs = require("fs");
// Requiring readline-sync module for console dialogue
const readlineSync = require("readline-sync");

// Get your Algolia Application ID and (admin) API key from the dashboard: https://www.algolia.com/account/api-keys
// and choose a name for your index. Add these environment variables to a `.env` file:
const ALGOLIA_APP_ID = process.env.ALGOLIA_APP_ID;
const ALGOLIA_API_KEY = process.env.ALGOLIA_API_KEY;
const ALGOLIA_INDEX_NAME = readlineSync.question(`Enter index name: `).trim();

// Start the API client
// https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/
const client = algoliasearch(ALGOLIA_APP_ID, ALGOLIA_API_KEY);

// Create an index (or connect to it, if an index with the name `ALGOLIA_INDEX_NAME` already exists)
// https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/#initialize-an-index
const index = client.initIndex(ALGOLIA_INDEX_NAME);

(async () => {
  const is_index_file = `./${ALGOLIA_INDEX_NAME}_records.json`;
  const is_rules_file = `./${ALGOLIA_INDEX_NAME}_rules.json`;
  const is_settings_file = `./${ALGOLIA_INDEX_NAME}_settings.json`;
  const is_synonyms_file = `./${ALGOLIA_INDEX_NAME}_synonyms.json`;

  let index_data = [];
  let rules_data;
  let settings_data;
  let synonyms_data;
  if (!fs.existsSync(is_index_file)) {
    console.log("No index file available. Terminating script.");
    return;
  } else {
    index_data = JSON.parse(fs.readFileSync(is_index_file, "utf8"));
  }

  if (is_rules_file) {
    rules_data = JSON.parse(fs.readFileSync(is_rules_file, "utf8"));
  }

  if (is_settings_file) {
    settings_data = JSON.parse(fs.readFileSync(is_settings_file, "utf8"));
  }

  if (is_synonyms_file) {
    synonyms_data = JSON.parse(fs.readFileSync(is_synonyms_file, "utf8"));
  }

  //    Check the status of the current index.
  let existing_records = [];
  try {
    await index.browseObjects({
      attributesToRetrieve: ["objectId"],
      batch: (batch) => {
        existing_records = existing_records.concat(batch);
      },
    });
  } catch (error) {
    console.log(`Error querying existing index. ${error}. Exiting script`);
    return;
  }

  //  Confirm with the user that they're going to overwrite an existing index

  const askQuestion = () => {
    let confirmation = readlineSync.question(`
// WARNING: Continuing will overwrite ${existing_records.length} records \
// in existing index "${ALGOLIA_INDEX_NAME}" with ${index_data.length} records from local index. \
// Do you want to continue (Y/N): `);

    switch (confirmation.toLowerCase()) {
      default:
        console.log(`please enter 'yes', 'y', 'no', or 'n'`);
        askQuestion();
      case "no":
      case "n":
        console.log("exiting script");
        return;
      case "yes":
      case "y":
        try {
          //  Restore the index and associated data to the application.

          console.log("Restoring index...");
          index.replaceAllObjects(index_data, {
            safe: true,
          });
          console.log("Index restored.");
          if (is_rules_file) {
            console.log("Restoring rules...");
            index.replaceAllRules(rules_data);
            console.log("Rules restored...");
          }

          if (is_settings_file) {
            console.log("Restoring settings...");
            index.setSettings(settings_data);
            console.log("Settings restored...");
          }

          if (is_synonyms_file) {
            console.log("Restoring synonyms...");
            index.replaceAllSynonyms(synonyms_data);
            console.log("Synonyms restored...");
          }
        } catch (error) {
          console.log(`error restoring data. ${error}`);
        }
        break;
    }
  };
  askQuestion();
})();
