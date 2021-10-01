package main

import (
	"fmt"
	"log"
	"os"

	"github.com/algolia/algoliasearch-client-go/v3/algolia/opt"
	"github.com/algolia/algoliasearch-client-go/v3/algolia/search"
	"github.com/joho/godotenv"
)

type Contact struct {
	ObjectID string `json:"objectID,omitempty"`
	Name     string `json:"name,omitempty"`
	Email    string `json:"email,omitempty"`
}

func PrintErrAndExit(err error) {
	fmt.Println(err)
	os.Exit(1)
}

func PrintCurrentObjects(i search.Index) {
	resSearch, err := i.Search("")
	if err != nil {
		PrintErrAndExit(err)
	}
	fmt.Println("Current objects: ", resSearch.Hits, "\n")
}

func main() {
	if err := godotenv.Load(); err != nil {
		log.Fatalf("godotenv.Load: %v", err)
	}

	// Algolia client credentials
	appID, apiKey, indexName := os.Getenv("ALGOLIA_APP_ID"), os.Getenv("ALGOLIA_API_KEY"), os.Getenv("ALGOLIA_INDEX_NAME")

	// Initialize the client
	// https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/
	client := search.NewClient(appID, apiKey)

	// Initialize an index
	// https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/#initialize-an-index
	index := client.InitIndex(indexName)

	// Define some objects to add to our index
	// https://www.algolia.com/doc/api-client/methods/indexing/#object-and-record
	contacts := []Contact{
		{
			Name:     "Foo",
			ObjectID: "1",
		},
		{
			Name:     "Bar",
			ObjectID: "2",
		},
	}

	// We don't have any objects (yet) in our index
	PrintCurrentObjects(*index)

	// Save Objects: Add mutliple new objects to an index.
	// https://www.algolia.com/doc/api-reference/api-methods/add-objects/?client=go
	fmt.Println("Save Objects - Adding multiple objects: ", contacts)
	resSaveMultiple, err := index.SaveObjects(contacts)
	if err != nil {
		PrintErrAndExit(err)
	}
	err = resSaveMultiple.Wait()
	if err != nil {
		PrintErrAndExit(err)
	}
	PrintCurrentObjects(*index)

	// Save Objects: Replace an existing object with an updated set of attributes.
	// https://www.algolia.com/doc/api-reference/api-methods/save-objects/?client=go
	fmt.Println("Save Objects - Replacing objects’s attributes on:", contacts[0])
	newContact := Contact{
		Name:     "FooBar",
		ObjectID: "1",
	}
	resSaveSingle, err := index.SaveObject(newContact)
	if err != nil {
		PrintErrAndExit(err)
	}
	err = resSaveSingle.Wait()
	if err != nil {
		PrintErrAndExit(err)
	}
	PrintCurrentObjects(*index)

	// Partial Update Objects: Update one or more attributes of an existing object.
	// https://www.algolia.com/doc/api-reference/api-methods/partial-update-objects/?client=go
	fmt.Println("Partial Update Objects - Updating object’s attributes on:", contacts[0])
	newContact = Contact{
		Email:    "foo@bar.com", // New attribute
		ObjectID: "1",
	}
	resPartialUpdate, err := index.PartialUpdateObject(newContact)
	if err != nil {
		PrintErrAndExit(err)
	}
	err = resPartialUpdate.Wait()
	if err != nil {
		PrintErrAndExit(err)
	}
	PrintCurrentObjects(*index)

	// Delete Objects: Remove objects from an index using their objectID.
	// https://www.algolia.com/doc/api-reference/api-methods/delete-objects/?client=go
	objectIDToDelete := contacts[0].ObjectID
	fmt.Println("Delete Objects - Deleting object with objectID:", objectIDToDelete)
	resDelete, err := index.DeleteObject(objectIDToDelete)
	if err != nil {
		PrintErrAndExit(err)
	}
	err = resDelete.Wait()
	if err != nil {
		PrintErrAndExit(err)
	}
	PrintCurrentObjects(*index)

	// Replace All Objects: Clears all objects from your index and replaces them with a new set of objects.
	// https://www.algolia.com/doc/api-reference/api-methods/replace-all-objects/?client=go
	newContacts := []Contact{
		{
			Name:     "NewFoo",
			ObjectID: "3",
		},
		{
			Name:     "NewBar",
			ObjectID: "4",
		},
	}
	fmt.Println("Replace All Objects - Clears all objects and replaces them with:", newContacts)
	resReplaceAll, err := index.ReplaceAllObjects(newContacts)
	if err != nil {
		PrintErrAndExit(err)
	}
	err = resReplaceAll.Wait()
	if err != nil {
		PrintErrAndExit(err)
	}
	PrintCurrentObjects(*index)

	// Delete By: Remove all objects matching a filter (including geo filters).
	// https://www.algolia.com/doc/api-reference/api-methods/delete-by/?client=go
	fmt.Println("Delete By - Remove all objects matching 'name:NewBar'")

	// Firstly, have an attribute to filter on
	// https://www.algolia.com/doc/api-client/methods/settings/?client=go
	resSetSettings, err := index.SetSettings(search.Settings{
		AttributesForFaceting: opt.AttributesForFaceting("name"),
	})
	if err != nil {
		PrintErrAndExit(err)
	}
	err = resSetSettings.Wait()
	if err != nil {
		PrintErrAndExit(err)
	}

	resDeleteBy, err := index.DeleteBy(opt.Filters("name:NewBar")) // https://www.algolia.com/doc/api-reference/api-parameters/filters/
	if err != nil {
		PrintErrAndExit(err)
	}
	err = resDeleteBy.Wait()
	if err != nil {
		PrintErrAndExit(err)
	}
	PrintCurrentObjects(*index)

	// Get Objects: Get one or more objects using their objectIDs.
	// https://www.algolia.com/doc/api-reference/api-methods/get-objects/?client=go
	objectID := newContacts[0].ObjectID
	fmt.Println("Get Objects - Getting object with objectID:", objectID)

	var retrievedContact Contact
	err = index.GetObject(objectID, &retrievedContact)
	if err != nil {
		PrintErrAndExit(err)
	}
	fmt.Println("Results: ", retrievedContact)

	// Custom Batch: Perform several indexing operations in one API call.
	// https://www.algolia.com/doc/api-reference/api-methods/batch/?client=go
	operations := []search.BatchOperationIndexed{
		{
			IndexName: indexName,
			BatchOperation: search.BatchOperation{
				Action: search.AddObject,
				Body: Contact{
					Name: "BatchedBar",
				},
			},
		},
		{
			IndexName: indexName,
			BatchOperation: search.BatchOperation{
				Action: search.UpdateObject,
				Body: Contact{
					ObjectID: objectID,
					Name:     "NewBatchedBar",
				},
			},
		},
	}
	fmt.Println("Custom Batch - Batching operations:", operations)
	resBatch, err := client.MultipleBatch(operations)
	if err != nil {
		PrintErrAndExit(err)
	}
	err = resBatch.Wait()
	if err != nil {
		PrintErrAndExit(err)
	}
	PrintCurrentObjects(*index)

	// Clear Objects: Clear the records of an index without affecting its settings.
	// https://www.algolia.com/doc/api-reference/api-methods/clear-objects/?client=go
	fmt.Println("Clear Objects: Clear the records of an index without affecting its settings.")
	resClear, err := index.ClearObjects()
	if err != nil {
		PrintErrAndExit(err)
	}
	err = resClear.Wait()
	if err != nil {
		PrintErrAndExit(err)
	}
	PrintCurrentObjects(*index)
}
