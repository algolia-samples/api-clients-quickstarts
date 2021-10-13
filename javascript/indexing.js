const algoliasearch = require("algoliasearch");
const dotenv = require("dotenv");

dotenv.config();

(async () => {
  try {
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
    let res = await index.search("");
    console.log("Current objects: ", res.hits);

    // Save objects: Add multiple objects to an index
    // https://www.algolia.com/doc/api-reference/api-methods/add-objects/?client=javascript
    console.log("Save objects - Adding multiple objects: ", contacts);
    await index.saveObjects(contacts).wait();

    res = await index.search("");
    console.log("Current objects: ", res.hits);

    // Save objects: replace an existing object with an updated set of attributes
    // https://www.algolia.com/doc/api-reference/api-methods/save-objects/?client=javascript
    console.log(
      "Save objects - Replacing objects' attributes on ",
      contacts[0]
    );
    let newContact = { name: "FooBar", objectID: "1" };
    await index.saveObject(newContact).wait();

    res = await index.search("");
    console.log("Current objects: ", res.hits);

    // Partially update objects: update one or more attribute of an existing object
    console.log("Save objects - Updating objects attributes on ", contacts[0]);
    newContact = { email: "foo@bar.com", objectID: "1" };
    await index.partialUpdateObject(newContact).wait();

    res = await index.search("");
    console.log("Current objects: ", res.hits);

    // Delete objects: remove objects from an index using their objectID
    // https://www.algolia.com/doc/api-reference/api-methods/delete-objects/?client=javascript
    objectIDToDelete = contacts[0].objectID;
    console.log(
      `Delete objects - Deleting object with objectID ${objectIDToDelete}`
    );
    await index.deleteObject(objectIDToDelete).wait();

    res = await index.search("");
    console.log("Current objects: ", res.hits);

    // Replace all objects: clears all objects from your index and replaces them with a
    // new set of objects.
    // https://www.algolia.com/doc/api-reference/api-methods/replace-all-objects/?client=javascript
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
    await index.replaceAllObjects(newContacts).wait();

    res = await index.search("");
    console.log("Current objects: ", res.hits);

    // Delete by: remove all objects matching a filter (including geo filters)
    // https://www.algolia.com/doc/api-reference/api-methods/delete-by/?client=javascript
    console.log("Delete by - Remove all objects matching 'name:NewBar'");

    // First, define an attribute to filter
    // https://www.algolia.com/doc/api-client/methods/settings/?client=javascript
    await index
      .setSettings({
        attributesForFaceting: ["name"],
      })
      .wait();

    // https://www.algolia.com/doc/api-reference/api-parameters/facetFilters/
    await index
      .deleteBy({
        facetFilters: ["name:newBar"],
      })
      .wait();

    res = await index.search("");
    console.log("Current objects: ", res.hits);

    // Get objects: get one or more objects by their objectIDs
    // https://www.algolia.com/doc/api-reference/api-methods/get-objects/?client=javascript
    const objectID = newContacts[0].objectID;
    console.log(`Get Objects - Getting object with objectID "${objectID}"`);

    res = await index.getObject(objectID);
    console.log("Results: ", res);

    // Custom batch: perform several indexing operations in one API call
    // https://www.algolia.com/doc/api-reference/api-methods/batch/?client=javascript
    const operations = [
      {
        action: "addObject",
        indexName: ALGOLIA_INDEX_NAME,
        body: {
          name: "BatchedBar",
        },
      },
      {
        action: "updateObject",
        indexName: ALGOLIA_INDEX_NAME,
        body: {
          objectID: objectID,
          name: "NewBatchedBar",
        },
      },
    ];

    console.log(`Custom batch - batching ${operations.length} operations`);
    await client.multipleBatch(operations).wait();

    res = await index.search("");
    console.log("Current objects: ", res.hits);

    // Clear objects: clear the records of an index without affecting its settings
    // https://www.algolia.com/doc/api-reference/api-methods/clear-objects/?client=javascript
    console.log(
      "Clear objects - clear the records of an index without affecting its settings"
    );
    await index.clearObjects().wait();

    res = await index.search("");
    console.log("Current objects: ", res.hits);
  } catch (error) {
    console.error(error);
  }
})();
