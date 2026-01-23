package main

import (
	"fmt"
	"log"
	"os"
	"github.com/algolia/algoliasearch-client-go/v4/algolia/search"
	"github.com/joho/godotenv"
	"strconv"
)

func PrintErrAndExit(err error) {
	fmt.Println(err)
	os.Exit(1)
}

func PrintCurrentObjects() {
	appID, apiKey, indexName := os.Getenv("ALGOLIA_APP_ID"), os.Getenv("ALGOLIA_API_KEY"), os.Getenv("ALGOLIA_INDEX_NAME")
	client, err := search.NewClient(appID, apiKey)
	if err != nil {
		// The client can fail to initialize if you pass an invalid parameter.
		fmt.Printf("Client error")
		panic(err)
	}
	resSearch, err := client.SearchSingleIndex(client.NewApiSearchSingleIndexRequest(indexName))
	if err != nil {
		// handle the eventual error
		panic(err)
	}
	fmt.Println("Current objects: ", resSearch.Hits, "\n")
}

func main() {
	if err := godotenv.Load(); err != nil {
		log.Fatalf("godotenv.Load: %v", err)
	}

	// Algolia client credentials
	appID, apiKey, indexName := os.Getenv("ALGOLIA_APP_ID"), os.Getenv("ALGOLIA_API_KEY"), os.Getenv("ALGOLIA_INDEX_NAME")

	// Start the API client
	// https://www.algolia.com/doc/libraries/sdk/methods/search#go
	client, err := search.NewClient(appID, apiKey)
	if err != nil {
		// The client can fail to initialize if you pass an invalid parameter.
		fmt.Printf("Client error")
		panic(err)
	}

	// Define some objects to add to our index
	contacts := []map[string]any{
		map[string]any{"objectID": "1", "name": "Foo"},
		map[string]any{"objectID": "2", "name": "Bar"},
	}

	// We don't have any objects (yet) in our index
	PrintCurrentObjects()

	// Save Objects: Add mutliple new objects to an index.
	// https://www.algolia.com/doc/libraries/sdk/methods/search/save-objects
	fmt.Println("Save Objects - Adding multiple objects: ", contacts)

	resSaveMultiple, err := client.SaveObjects(indexName,contacts)

	if err != nil {
		PrintErrAndExit(err)
	}

	_, err = client.WaitForTask(indexName, resSaveMultiple[len(resSaveMultiple)-1].TaskID)

	if err != nil {
		PrintErrAndExit(err)
	}
	PrintCurrentObjects()

	// Save Objects: Replace an existing object with an updated set of attributes.
	// https://www.algolia.com/doc/libraries/sdk/v1/methods/save-objects
	fmt.Println("Save Objects - Replacing objects’s attributes on:", contacts[0])
	newContact := map[string]any{
			"name":        "FooBar",
			"objectID":    "1",
	}

	resSaveSingle, err := client.SaveObject(client.NewApiSaveObjectRequest(
		indexName,
		newContact,
	))

	if err != nil {
		PrintErrAndExit(err)
	}

	_, err = client.WaitForTask(indexName, resSaveSingle.TaskID)

	if err != nil {
		PrintErrAndExit(err)
	}
	PrintCurrentObjects()

	// Partial Update Objects: Update one or more attributes of an existing object.
	// https://www.algolia.com/doc/api-reference/api-methods/partial-update-objects/?client=go
	fmt.Println("Partial Update Objects - Updating object’s attributes on:", contacts[0])

	newUpdate := map[string]any{
		"email": "foo@bar.com",
	}

	resPartialUpdate, err := client.PartialUpdateObject(client.NewApiPartialUpdateObjectRequest(
  		indexName, 
		"1", 
		newUpdate))

	if err != nil {
		PrintErrAndExit(err)
	}
	fmt.Println(resPartialUpdate)
	if err != nil {
		PrintErrAndExit(err)
	}
	PrintCurrentObjects()

	// Delete Objects: Remove objects from an index using their objectID.
	// https://www.algolia.com/doc/api-reference/api-methods/delete-objects/?client=go
	objectIDToDelete := 1
	fmt.Println("Delete Objects - Deleting object with objectID:", objectIDToDelete)
	resDelete, err := client.DeleteObject(client.NewApiDeleteObjectRequest(
		indexName, strconv.Itoa(objectIDToDelete)))
	if err != nil {
		PrintErrAndExit(err)
	}
	_, err = client.WaitForTask(indexName, resDelete.TaskID)
	if err != nil {
		PrintErrAndExit(err)
	}
	PrintCurrentObjects()

	// Replace All Objects: Clears all objects from your index and replaces them with a new set of objects.
	// https://www.algolia.com/doc/api-reference/api-methods/replace-all-objects/?client=go
	newContacts := []map[string]any{
		{"objectID": "1", "name": "Adam"}, 
		{"objectID": "2", "name": "Benoit"},
	}
	fmt.Println("Replace All Objects - Clears all objects and replaces them with:", newContacts)
	resReplaceAll, err := client.ReplaceAllObjects(
  		indexName,
  		newContacts, 
		search.WithBatchSize(77), 
		search.WithScopes([]search.ScopeType{search.ScopeType("settings"), search.ScopeType("synonyms")}),
	)
	if err != nil {
		PrintErrAndExit(err)
	}
	_, err = client.WaitForTask(indexName, resReplaceAll.MoveOperationResponse.TaskID);
	if err != nil {
		PrintErrAndExit(err)
	}
	PrintCurrentObjects()

	// Delete By: Remove all objects matching a filter (including geo filters).
	// https://www.algolia.com/doc/api-reference/api-methods/delete-by/?client=go
	fmt.Println("Delete By - Remove all objects matching 'name:NewBar'")

	// Firstly, have an attribute to filter on
	// https://www.algolia.com/doc/api-client/methods/settings/?client=go
	resSetSettings, err := client.SetSettings(client.NewApiSetSettingsRequest(
		indexName,
		search.NewEmptyIndexSettings().SetAttributesForFaceting(
            []string{"name"},
        )).WithForwardToReplicas(true),
	)
	if err != nil {
		PrintErrAndExit(err)
	}
	_, err = client.WaitForTask(indexName, resSetSettings.TaskID)
	if err != nil {
		PrintErrAndExit(err)
	}

	// https://www.algolia.com/doc/api-reference/api-parameters/filters/
	resDeleteBy, err := client.DeleteBy(client.NewApiDeleteByRequest(
 		indexName,
  		search.NewEmptyDeleteByParams().SetFilters("name:NewBar")),
	)
	if err != nil {
		PrintErrAndExit(err)
	}
	_, err = client.WaitForTask(indexName, resDeleteBy.TaskID)
	if err != nil {
		PrintErrAndExit(err)
	}
	PrintCurrentObjects()

	// Get Objects: Get one or more objects using their objectIDs.
	// https://www.algolia.com/doc/api-reference/api-methods/get-objects/?client=go
	objectID := newContacts[0]["objectID"]
	objectIDStr := fmt.Sprint(objectID)
	fmt.Println("Get Objects - Getting object with objectID:", objectID)

	retrievedContact, err := client.GetObject(client.NewApiGetObjectRequest(
		indexName, objectIDStr).WithAttributesToRetrieve(
		[]string{"objectID", objectIDStr}),
	)
	if err != nil {
		PrintErrAndExit(err)
	}
	fmt.Println("Results: ", retrievedContact)

	// Custom Batch: Perform several indexing operations in one API call.
	// https://www.algolia.com/doc/api-reference/api-methods/batch/?client=go
	operations := search.NewEmptyBatchWriteParams().SetRequests(
    []search.BatchRequest{
      *search.NewEmptyBatchRequest().SetAction(search.Action("addObject")).SetBody(map[string]any{"Name": "BatchedBar"}),
      *search.NewEmptyBatchRequest().SetAction(search.Action("addObject")).SetBody(map[string]any{"Name": "NewBatchedBar"}),
    })

	fmt.Println("Custom Batch - Batching operations:", operations)
	resBatch, err := client.Batch(client.NewApiBatchRequest(
		indexName,
		operations),
	)
	if err != nil {
		PrintErrAndExit(err)
	}
	_, err = client.WaitForTask(indexName, resBatch.TaskID)
	if err != nil {
		PrintErrAndExit(err)
	}
	PrintCurrentObjects()

	// Clear Objects: Clear the records of an index without affecting its settings.
	// https://www.algolia.com/doc/api-reference/api-methods/clear-objects/?client=go
	fmt.Println("Clear Objects: Clear the records of an index without affecting its settings.")
	resClear, err := client.ClearObjects(client.NewApiClearObjectsRequest(indexName))
	if err != nil {
		PrintErrAndExit(err)
	}

	_, err = client.WaitForTask(indexName, resClear.TaskID)

	if err != nil {
		PrintErrAndExit(err)
	}
	PrintCurrentObjects()
}
