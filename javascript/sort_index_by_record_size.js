/*
Sort Index By Record Size
Sometimes we want to easily find the largest record in an index (in file size) so we can investigate situations where some small number of records are over the fileSizeLimit. This script is designed to fetch the entire index and then sort it by the total string size, and then export it to a file for analysis.
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

let records = [];

(async () => {
  // retrieve all records from index
  console.log(`Retrieving records...`);
  try {
    await index.browseObjects({
      batch: (batch) => {

        // This method gets an approximation of the size of the record (total string length) we can use for sorting purposes 
        for (let i = 0; i < batch.length; i++) {
            batch[i].string_length = JSON.stringify(batch[i]).length;
        } 
        records = records.concat(batch);
      }
    });

    console.log(`${records.length} records retrieved`);

    console.log(`Sorting Records By Size...`);

    // Sort the result so the largest string length is at the beggining
    records.sort((a, b) => b.string_length - a.string_length);

    
  } catch (error) {
    console.log(`Error retrieving data ${error.message}`);
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
        console.log(`Error writing files: ${error.message}`);
      };
  }
  try {
    let name = "records";
    createJson(records, name);
  } catch (error) {
    console.log(`Error exporting data ${error.message}`);
  }
})();
