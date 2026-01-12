package main

import (
	"fmt"
	"log"
	"os"
	"github.com/algolia/algoliasearch-client-go/v4/algolia/search"
	"github.com/joho/godotenv"
)

func PrintErrAndExit(err error) {
	fmt.Println(err)
	os.Exit(1)
}

func main() {
	err := godotenv.Load()
	if err != nil {
		log.Fatal("Error loading .env file")
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

	//Setting settings
	//https://www.algolia.com/doc/libraries/sdk/methods/search/set-settings
	resSetSettings, err := client.SetSettings(client.NewApiSetSettingsRequest(
		indexName,
		search.NewEmptyIndexSettings().SetSearchableAttributes(
            []string{"actors", "genre"},
        )).WithForwardToReplicas(true),
	)
	if err != nil {
		PrintErrAndExit(err)
	}
	_, err = client.WaitForTask(indexName, resSetSettings.TaskID)
	if err != nil {
		PrintErrAndExit(err)
	}

	//Printing settings
	//https://www.algolia.com/doc/api-reference/api-methods/get-settings/
	fmt.Println("Index settings:")
	resGetSettings, err := client.GetSettings(client.NewApiGetSettingsRequest(
	indexName).WithGetVersion(2))
		if err != nil {
		// handle the eventual error
		panic(err)
	}

	fmt.Println(resGetSettings)
}