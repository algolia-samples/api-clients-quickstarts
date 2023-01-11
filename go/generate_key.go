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

	"github.com/algolia/algoliasearch-client-go/v3/algolia/search"
	"github.com/joho/godotenv"
)

func main() {
	var err error

	err = godotenv.Load()
	if err != nil {
		log.Fatal("Error loading .env file")
	}
	// Get your Algolia Application ID and (admin) API key from the dashboard: https://www.algolia.com/account/api-keys
	// and choose a name for your index. Add these environment variables to a `.env` file:
	appID, apiKey, indexName := os.Getenv("ALGOLIA_APP_ID"), os.Getenv("ALGOLIA_API_KEY"), os.Getenv("ALGOLIA_INDEX_NAME")

	// Start the API client
	// https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/
	client := search.NewClient(appID, apiKey)

	// Create the API key.
	// https://www.algolia.com/doc/api-reference/api-methods/add-api-key/?client=go
	fmt.Println("Generating key...")

	keyParams := search.Key{
		ACL:                    []string{"search"},
		Description:            "Restricted search-only API key for algolia.com",
		MaxQueriesPerIPPerHour: 100,
	}

	var key string
	var keyRes search.CreateKeyRes

	keyRes, err = client.AddAPIKey(keyParams)
	err = keyRes.Wait()
	if err != nil {
		panic(errors.New("Error generating key."))
	} else {
		key = keyRes.Key
		fmt.Println("Key generated successfully:", key)
	}

	// Test the new key
	fmt.Println("Testing key...")

	// Initialise a new client with the generated key
	client = search.NewClient(appID, key)

	// Create an index (or connect to it, if an index with the name `ALGOLIA_INDEX_NAME` already exists)
	// https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/#initialize-an-index
	index := client.InitIndex(indexName)

	// Search the index with an empty string
	// https://www.algolia.com/doc/api-reference/api-methods/search/
	var indexRes search.QueryRes

	indexRes, err = index.Search("")
	if err != nil {
		panic(errors.New("Error testing key."))
	} else {
		fmt.Println("Key tested successfully.", indexRes.NbHits, "hits found.")
	}
}
