package main

// Install the API client: https://www.algolia.com/doc/api-client/getting-started/install/go/?client=go
import (
	"fmt"
	"os"
	"github.com/algolia/algoliasearch-client-go/v4/algolia/search"
	"github.com/joho/godotenv"
	"log"
)

func main() {
	err := godotenv.Load()
	if err != nil {
		log.Fatal("Error loading .env file")
	}

  	// Get your Algolia Application ID and (admin) API key from the dashboard: https://www.algolia.com/account/api-keys
   // and choose a name for your index. Add these environment variables to a `.env` file:
	appID, apiKey, indexName := os.Getenv("ALGOLIA_APP_ID"), os.Getenv("ALGOLIA_API_KEY"), os.Getenv("ALGOLIA_INDEX_NAME")

	// Start the API client
	// https://www.algolia.com/doc/libraries/sdk/methods/search#go
	client, err := search.NewClient(appID, apiKey)
	if err != nil {
		// The client can fail to initialize if you pass an invalid parameter.
		fmt.Printf("Client error")
		panic(err)
	}

    // Add new objects to the index
	// https://www.algolia.com/doc/libraries/sdk/methods/search/save-objects
	resSave, saveObjsErr := client.SaveObjects(
		indexName,
		[]map[string]any{
			{
				"objectID":   "1",
				"name": "Foo",
			},
		},
		)
	if saveObjsErr != nil {
		// handle the eventual error
		panic(saveObjsErr)
	}

	_, err = client.WaitForTask(indexName, resSave[len(resSave)-1].TaskID)
	if err != nil {
		fmt.Print("Error")
		log.Fatal(err)
	}

	// Search the index for "Foo"
	respSearch, err := client.Search(client.NewApiSearchRequest(
		search.NewEmptySearchMethodParams().SetRequests(
			[]search.SearchQuery{*search.SearchForHitsAsSearchQuery(
			search.NewEmptySearchForHits().SetIndexName(indexName).SetQuery("Foo").SetHitsPerPage(50))}),
	))
	if err != nil {
	// handle the eventual error
	panic(err)
	}

	fmt.Println(respSearch.Results);

}
