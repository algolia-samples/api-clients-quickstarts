package main

import (
	"fmt"
	"os"

	"github.com/algolia/algoliasearch-client-go/v3/algolia/search"
)

type Contact struct {
	ObjectID string `json:"objectID"`
	Name     string `json:"name"`
}

func main() {
	// Algolia client credentials
	appID, apiKey, indexName := os.Getenv("ALGOLIA_APP_ID"), os.Getenv("ALGOLIA_API_KEY"), os.Getenv("ALGOLIA_INDEX_NAME")

	// Initialize the client
	// https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/
	client := search.NewClient(appID, apiKey)

	// Initialize an index
	// https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/#initialize-an-index
	index := client.InitIndex(indexName)

	// Save Objects: Add mutliple new objects to an index.
	// https://www.algolia.com/doc/api-reference/api-methods/add-objects/
	resSave, err := index.SaveObjects([]Contact{
		{ObjectID: "1", Name: "Foo"},
	})
	if err != nil {
		fmt.Println(err)
		os.Exit(1)
	}

	// Waiting for the indexing task to complete
	// https://www.algolia.com/doc/api-reference/api-methods/wait-task/
	err = resSave.Wait()
	if err != nil {
		fmt.Println(err)
		os.Exit(1)
	}

	// Search Index: Method used for querying an index.
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
