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

// Start the API client
// https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/
const client = algoliasearch(ALGOLIA_APP_ID, ALGOLIA_API_KEY);

// List all indices with listIndices method
// https://www.algolia.com/doc/api-reference/api-methods/list-indices/?client=javascript
client.listIndices().then(({ items }) => {
    // Loop into each element and extract only index name and record size
    let filteredItems = [];
    items.forEach(element => filteredItems.push({
        name: element.name,
        entries: element.entries,
    }));
    // Export JSON file containing only name of indices and record size into same directory
    fs.writeFile(
        `listIndices.json`,
        JSON.stringify(filteredItems),
            (err) => {
                // In case of a error throw err.
                if (err) throw err;
            }
        );
    })
    .then((response) => {
        // Success message
        console.log(
            `Indices name and record size saved as listIndices.json in the current directory`
        );
    })
    .catch((error) => console.log(error));