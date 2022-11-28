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


// const newObject = { objectID: 1, name: "Foo" };

// index
//   .saveObjects([newObject])
//   // Wait for the indexing task to complete
//   // https://www.algolia.com/doc/api-reference/api-methods/wait-task/
//   .wait()
//   .then((response) => {
//     console.log(response);
//     // Search the index for "Fo"
//     // https://www.algolia.com/doc/api-reference/api-methods/search/
    // index.search("Fo").then((objects) => console.log(objects)).catch();




fetch(
	`https://analytics.algolia.com/2/searches?index=${ALGOLIA_INDEX_NAME}&limit=1000`,
	{
		method: 'GET',
		headers: {
			"X-Algolia-API-Key": `${ALGOLIA_API_KEY}`,
            "X-Algolia-Application-Id": `${ALGOLIA_APP_ID}`

		}
	}
).then(res => res.json())
.catch(error => console.error('Error:', error))
.then(response => console.log('Your top searches are:', response));


