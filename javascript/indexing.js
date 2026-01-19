// Install the API client: https://www.algolia.com/doc/libraries/sdk/install#javascript
import { algoliasearch } from "algoliasearch";
import "dotenv/config";

// Algolia client credentials
const ALGOLIA_APP_ID = process.env.ALGOLIA_APP_ID;
const ALGOLIA_API_KEY = process.env.ALGOLIA_API_KEY;
const ALGOLIA_INDEX_NAME = process.env.ALGOLIA_INDEX_NAME;

// Initialize the client
// https://www.algolia.com/doc/libraries/sdk/install#test-your-installation
const client = algoliasearch(ALGOLIA_APP_ID, ALGOLIA_API_KEY);

// Create an index name (or connect to it, if an index with the name `ALGOLIA_INDEX_NAME` already exists)
// https://www.algolia.com/doc/libraries/sdk/install#test-your-installation
const indexName = ALGOLIA_INDEX_NAME || "new_index_name";

(async () => {
  try {
    const contacts = [
      {
        name: "Foo",
        objectID: "1",
      },
      {
        name: "Bar",
        objectID: "2",
      },
    ];

    // We don't have any objects (yet) in our index
    let res = await client.search({
      requests: [
        {
          indexName,
          query: "",
        },
      ],
    });
    console.log("Current objects: ", res.results[0].hits);

    // Save objects: Add multiple objects to an index
    // https://www.algolia.com/doc/libraries/sdk/methods/search/save-objects
    console.log("Save objects - Adding multiple objects: ", contacts);
    res = await client.saveObjects({
      indexName: indexName,
      objects: contacts,
    });

    // Wait for the task to complete
    const task = res[0].taskID;
    await client.waitForTask({ indexName: indexName, taskID: task });

    res = await client.search({
      requests: [
        {
          indexName,
          query: "",
        },
      ],
    });
    console.log("Current objects: ", res.results[0].hits);

    // Add or replace an object: replace an existing object with an updated set of attributes
    // https://www.algolia.com/doc/libraries/sdk/methods/search/add-or-update-object
    console.log(
      "Add or replace object - Replacing objects' attributes on ",
      contacts[0]
    );

    let newContact = { name: "FooBar" };

    res = await client.addOrUpdateObject({
      indexName: indexName,
      objectID: 1,
      body: newContact,
    });

    // Wait for the task to complete
    await client.waitForTask({ indexName: indexName, taskID: res.taskID });

    res = await client.search({
      requests: [
        {
          indexName,
          query: "",
        },
      ],
    });
    console.log("Current objects: ", res.results[0].hits);

    // Partially update objects: update one or more attribute of an existing object
    // https://www.algolia.com/doc/libraries/sdk/methods/search/partial-update-object
    const updatedObject = await client.getObject({
      indexName: indexName,
      objectID: 1,
    });
    console.log(
      "Partial update object - Updating object attributes on ",
      updatedObject
    );
    newContact = { email: "foo@bar.com" };

    res = await client.partialUpdateObject({
      indexName: indexName,
      objectID: 1,
      attributesToUpdate: newContact,
    });

    // Wait for the task to complete
    await client.waitForTask({ indexName: indexName, taskID: res.taskID });

    res = await client.search({
      requests: [
        {
          indexName,
          query: "",
        },
      ],
    });
    console.log("Current objects: ", res.results[0].hits);

    // Delete object: remove an object from an index using their objectID
    // https://www.algolia.com/doc/libraries/sdk/methods/search/delete-object
    const objectIDToDelete = updatedObject.objectID;
    console.log(
      `Delete objects - Deleting object with objectID ${objectIDToDelete}`
    );
    res = await client.deleteObject({
      indexName: indexName,
      objectID: objectIDToDelete,
    });

    // Wait for the task to complete
    await client.waitForTask({ indexName: indexName, taskID: res.taskID });

    res = await client.search({
      requests: [
        {
          indexName,
          query: "",
        },
      ],
    });
    console.log("Current objects: ", res.results[0].hits);

    // Replace all objects: clears all objects from your index and replaces them with a
    // new set of objects.
    // https://www.algolia.com/doc/libraries/sdk/methods/search/replace-all-objects
    const newContacts = [
      {
        name: "NewFoo",
        objectID: "3",
      },
      {
        name: "NewBar",
        objectID: "4",
      },
    ];

    console.log(
      "Replace all objects - clears all objects and replaces them with ",
      newContacts
    );
    res = await client.replaceAllObjects({
      indexName: indexName,
      objects: newContacts,
      scopes: ["settings", "synonyms"],
    });

    // Wait for the copy operation
    await client.waitForTask({
      indexName: indexName,
      taskID: res.copyOperationResponse.taskID,
    });
    console.log("Indexed has been copied");

    // Wait for the move operation
    await client.waitForTask({
      indexName: indexName,
      taskID: res.moveOperationResponse.taskID,
    });
    console.log("Index has been moved");

    res = await client.search({
      requests: [
        {
          indexName,
          query: "",
        },
      ],
    });
    console.log("Current objects: ", res.results[0].hits);

    // Delete by: remove all objects matching a filter (including geo filters)
    // https://www.algolia.com/doc/libraries/sdk/methods/search/delete-by
    console.log("Delete by - Remove all objects matching 'name:NewBar'");

    // First, define an attribute to filter
    // https://www.algolia.com/doc/libraries/sdk/methods/search/set-settings

    res = await client.setSettings({
      indexName: indexName,
      indexSettings: { attributesForFaceting: ["name"] },
    });

    // Wait for task to complete
    await client.waitForTask({ indexName: indexName, taskID: res.taskID });

    // https://www.algolia.com/doc/api-reference/api-parameters/facetFilters
    res = await client.deleteBy({
      indexName: indexName,
      deleteByParams: { facetFilters: ["name:newBar"] },
    });

    // Wait for task to complete
    await client.waitForTask({ indexName: indexName, taskID: res.taskID });

    res = await client.search({
      requests: [
        {
          indexName,
          query: "",
        },
      ],
    });
    console.log("Current objects: ", res.results[0].hits);

    // Get objects: get one or more objects by their objectIDs
    // https://www.algolia.com/doc/libraries/sdk/methods/search/get-object
    const objectID = newContacts[0].objectID;
    console.log(`Get Objects - Getting object with objectID "${objectID}"`);

    const results = await client.getObject({
      indexName: indexName,
      objectID: objectID,
    });
    console.log("Results: ", results);

    // Custom batch: perform several indexing operations in one API call
    // https://www.algolia.com/doc/libraries/sdk/methods/search/multiple-batch
    const operations = [
      {
        action: "addObject",
        indexName: indexName,
        body: {
          name: "BatchedBar",
        },
      },
      {
        action: "updateObject",
        indexName: indexName,
        body: {
          objectID: objectID,
          name: "NewBatchedBar",
        },
      },
    ];

    console.log(`Custom batch - batching ${operations.length} operations`);
    res = await client.multipleBatch({ requests: operations });

    await client.waitForTask({
      indexName: indexName,
      taskID: res.taskID[indexName],
    });

    res = await client.search({
      requests: [
        {
          indexName,
          query: "",
        },
      ],
    });
    console.log("Current objects: ", res.results[0].hits);

    // Clear objects: clear the records of an index without affecting its settings
    // https://www.algolia.com/doc/libraries/sdk/methods/search/clear-objects
    console.log(
      "Clear objects - clear the records of an index without affecting its settings"
    );
    res = await client.clearObjects({ indexName: indexName });
    await client.waitForTask({
      indexName: indexName,
      taskID: res.taskID,
    });

    res = await client.search({
      requests: [
        {
          indexName,
          query: "",
        },
      ],
    });
    console.log("Current objects: ", res.results[0].hits);
  } catch (error) {
    console.error(error);
  }
})();
