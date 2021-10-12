package main

// Install the API client: https://www.algolia.com/doc/api-client/getting-started/install/go/?client=go
import (
	"fmt"
	"os"

	"github.com/algolia/algoliasearch-client-go/v3/algolia/search"
	"github.com/joho/godotenv"
)

type Contact struct {
	ObjectID string `json:"objectID"`
	Name     string `json:"name"`
}

func main() {
  // Get your Algolia Application ID and (admin) API key from the dashboard: https://www.algolia.com/account/api-keys
  // and choose a name for your index. Add these environment variables to a `.env` file:
	appID, apiKey, indexName := os.Getenv("ALGOLIA_APP_ID"), os.Getenv("ALGOLIA_API_KEY"), os.Getenv("ALGOLIA_INDEX_NAME")

	// Start the API client
	// https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/
	client := search.NewClient(appID, apiKey)

  // Create an index (or connect to it, if an index with the name `ALGOLIA_INDEX_NAME` already exists)
	// https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/#initialize-an-index
	index := client.InitIndex(indexName)

  // Add new objects to the index
	// https://www.algolia.com/doc/api-reference/api-methods/add-objects/
	resSave, err := index.SaveObjects([]Contact{
		{ObjectID: "1", Name: "Foo"},
	})
	if err != nil {
		fmt.Println(err)
		os.Exit(1)
	}

	// Wait for the indexing task to complete
	// https://www.algolia.com/doc/api-reference/api-methods/wait-task/
	err = resSave.Wait()
	if err != nil {
		fmt.Println(err)
		os.Exit(1)
	}

  // Search the index for "Fo"
	// https://www.algolia.com/doc/api-reference/api-methods/search/
	res, err := index.Search("Foo")
	if err != nil {
		fmt.Println(err)
		os.Exit(1)
	}

	var contacts []Contact

	err = res.UnmarshalHits(&contacts)
	if err != nil {
		fmt.Println(err)
		os.Exit(1)
	}

	fmt.Println("search results: ", contacts)
}
