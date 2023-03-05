/*
Restore Index

This script will restore an index, including any settings, rules and synonyms.

It is best used in conjuction with backup_index.js to backup and restore an index to an application.

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

// Requiring built in modules for fs (file system) and readline (console interface)
const fs = require("fs");
const readline = require("readline");

// Get your Algolia Application ID and (admin) API key from the dashboard: https://www.algolia.com/account/api-keys
// Add these 2 environment variables to a `.env` file:
const ALGOLIA_APP_ID = process.env.ALGOLIA_APP_ID;
const ALGOLIA_API_KEY = process.env.ALGOLIA_API_KEY;

// Start the API client
const client = algoliasearch(ALGOLIA_APP_ID, ALGOLIA_API_KEY);

// Initiate interface for console
const rl = readline.createInterface({
  input: process.stdin,
  output: process.stdout,
});

// Function to ask user for index name
let ALGOLIA_INDEX_NAME;

const askForIndexName = async () => {
  while (true) {
    try {
      const name = await new Promise((resolve, reject) => {
        rl.question(
          "Please enter an index name to restore the backup to: ",
          (input) => {
            if (!input) {
              reject(new Error("Index name cannot be empty"));
            } else {
              resolve(input);
            }
          }
        );
      });
      ALGOLIA_INDEX_NAME = name;
      const answer = await new Promise((resolve, reject) => {
        rl.question(
          `You entered ${ALGOLIA_INDEX_NAME}, is this correct? (yes/no) `,
          (input) => {
            if (input.toLowerCase() !== "yes" && input.toLowerCase() !== "y") {
              reject(new Error("Invalid answer"));
            } else {
              resolve(input);
            }
          }
        );
      });
      console.log(`Index name is: ${ALGOLIA_INDEX_NAME}`);
      return name;
    } catch (err) {
      console.error(err.message);
    }
  }
};

(async () => {
  // Assign correct index
  ALGOLIA_INDEX_NAME = await askForIndexName();

  // Connect to the index
  const index = client.initIndex(ALGOLIA_INDEX_NAME);

  // Check if input has been received before proceeding with the rest of the script

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
    rl.close();
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

  //  Confirm with the user that user will overwrite an existing index, then proceed to overwrite

  const askQuestion = () => {
    console.log(`
    WARNING: Continuing will overwrite ${existing_records.length} records in existing index "${ALGOLIA_INDEX_NAME}" with ${index_data.length} records from local index. \
    Do you want to continue (Y/N): `);
    rl.question("", (confirmation) => {
      confirmation = confirmation.trim().toLowerCase();
      switch (confirmation) {
        default:
          console.log(`please enter 'yes', 'y', 'no', or 'n'`);
          askQuestion();
          break;
        case "no":
        case "n":
          console.log("exiting script");
          rl.close();
          break;
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
          rl.close();
          break;
      }
    });
  };

  askQuestion();
})();
