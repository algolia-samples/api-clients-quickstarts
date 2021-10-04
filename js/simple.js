const algoliasearch = require("algoliasearch");
const dotenv = require("dotenv");

dotenv.config();

// Algolia client credentials
const ALGOLIA_APP_ID = process.env.ALGOLIA_APP_ID;
const ALGOLIA_API_KEY = process.env.ALGOLIA_API_KEY;
const ALGOLIA_INDEX_NAME = process.env.ALGOLIA_INDEX_NAME;

// Initialize the client
// https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/
const client = algoliasearch(ALGOLIA_APP_ID, ALGOLIA_API_KEY);

// Initialize an index
// https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/#initialize-an-index
const index = client.initIndex(ALGOLIA_INDEX_NAME);

// Save objects: Add multiple new objects to an index
// https://www.algolia.com/doc/api-reference/api-methods/add-objects/
const newObject = { objectID: 1, name: "Foo" };

index
  .saveObjects([newObject])
  // Wait for the indexing task to complete
  // https://www.algolia.com/doc/api-reference/api-methods/wait-task/
  .wait()
  .then((response) => console.log(response));

// Search index: Method used for querying an index
// https://www.algolia.com/doc/api-reference/api-methods/search/

index.search("Fo").then((objects) => console.log(objects));
