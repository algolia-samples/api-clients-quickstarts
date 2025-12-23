/*
Sort Index By Record Size
Sometimes we want to easily find the largest record in an index (in file size) so we can investigate situations where some small number of records are over the fileSizeLimit. This script is designed to fetch the entire index and then sort it by the total string size, and then export it to a file for analysis.
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

let records = [];

(async () => {
  // retrieve all records from index
  console.log(`Retrieving records...`);
  try {
    await client.browseObjects({
      indexName,
      aggregator: (res) => {
        // This method gets an approximation of the size of the record (total string length in bytes) we can use for sorting purposes
        res.hits.forEach((record) => {
          const sizeInBytes = Buffer.byteLength(JSON.stringify(record), "utf8");

          records.push({
            objectID: record.objectID,
            sizeInBytes,
            record,
          });
        });
      },
    });

    console.log(`${records.length} records retrieved`);
    
    console.log(`Sorting Records By Size...`);

    // Sort the records from largest to smallest
    records.sort((a, b) => b.sizeInBytes - a.sizeInBytes);
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
