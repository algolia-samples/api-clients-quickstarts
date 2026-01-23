// generate_key creates an API key for an Algolia application.
// The generated key will be valid for Search operations only, and is limited to 100
// queries per hour.

package main

// Install the API client: https://www.algolia.com/doc/api-client/getting-started/install/go/?client=go
import (
	"errors"
	"fmt"
	"log"
	"os"
	"github.com/algolia/algoliasearch-client-go/v4/algolia/search"
	"github.com/joho/godotenv"
)

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

	// Create the API key.
	// https://www.algolia.com/doc/api-reference/api-methods/add-api-key/?client=go
	fmt.Println("Generating key...")
	var key string

	keyRes, err := client.AddApiKey(client.NewApiAddApiKeyRequest(
		search.NewEmptyApiKey().SetAcl(
		[]search.Acl{search.Acl("search")}).SetDescription("Restricted search-only API key for algolia.com")))
	if err != nil {
		panic(errors.New("Error generating key."))
	} else {
		key = keyRes.Key
		fmt.Println("Key generated successfully:", key)
	}

	// Wait for API key
	// https://www.algolia.com/doc/libraries/sdk/methods/search/wait-for-api-key
	resWaitForKey, err := client.WaitForApiKey(
		key, search.ApiKeyOperation("add"))
	if err != nil {
		// handle the eventual error
		panic(err)
	} else {
		fmt.Println("Task is complete.")
		fmt.Println(resWaitForKey)
	}

	// Test the new key
	fmt.Println("Testing key...")

	// Initialise a new client with the generated key
	newClient, err := search.NewClient(appID, key)
	if err != nil {
		// The client can fail to initialize if you pass an invalid parameter.
		fmt.Printf("Client error")
		panic(err)
	}

	// Search the index with an empty string
	// https://www.algolia.com/doc/api-reference/api-methods/search/
	indexRes, err := newClient.SearchSingleIndex(newClient.NewApiSearchSingleIndexRequest(indexName))
	if err != nil {
		panic(errors.New("Error testing key."))
		fmt.Println(err)
	} else {
		fmt.Println("Key tested successfully.", len(indexRes.Hits), "hits found.")
	}
}
