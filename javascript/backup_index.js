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

// Get all records as an iterator

(async () => {
  console.log(`Retrieving records...`);

  await index.browseObjects({
    batch: (batch) => {
      records = records.concat(batch);

      fs.writeFile(
        `${ALGOLIA_INDEX_NAME}_records.json`,
        JSON.stringify(batch),
        (err) => {
          if (err) throw err;
        }
      );
    },
  });

  console.log(`${records.length} records retrieved`);

  console.log(`Retrieving settings...`);

  settings = await index.getSettings().then((settings) => {
    fs.writeFile(
      `${ALGOLIA_INDEX_NAME}_settings.json`,
      JSON.stringify(settings),
      (err) => {
        if (err) throw err;
      }
    );
  });

  console.log(`settings retrieved`);

  console.log(`Retrieving rules...`);

  await index
    .browseRules({
      batch: (batch) => {
        rules = rules.concat(batch);
        fs.writeFile(
          `${ALGOLIA_INDEX_NAME}_rules.json`,
          JSON.stringify(batch),
          (err) => {
            if (err) throw err;
          }
        );
      },
    })
    .then(() => {
      // done
    });

  console.log(`${rules.length} rules retrieved`);

  console.log(`Retrieving synonyms...`);

  await index
    .browseSynonyms({
      batch: (batch) => {
        synonyms = synonyms.concat(batch);

        fs.writeFile(
          `${ALGOLIA_INDEX_NAME}_synonyms.json`,
          JSON.stringify(batch),
          (err) => {
            if (err) throw err;
          }
        );
      },
    })
    .then(() => {
    });

  console.log(`${synonyms.length} synonyms retrieved`);
})();
