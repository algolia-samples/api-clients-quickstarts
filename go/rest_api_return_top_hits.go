package main

import (
	"fmt"
	"log"
	"os"
	"github.com/algolia/algoliasearch-client-go/v4/algolia/analytics"
	"github.com/joho/godotenv"
	"encoding/json"
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
	// Initialize the client with your application region, eg. analytics.ALGOLIA_APPLICATION_REGION
	client, err := analytics.NewClient(appID, apiKey, analytics.US)
	if err != nil {
	// The client can fail to initialize if you pass an invalid parameter.
		fmt.Printf("Client error")
		panic(err)
	}

	resTopSearches, err := client.GetTopSearches(client.NewApiGetTopSearchesRequest(indexName).WithLimit(1000),)
	if err != nil {
	// handle the eventual error
		panic(err)
	}

	// Format the resulting json string
	resEncodeTopSearchesJson, err := json.Marshal(resTopSearches)
	if err != nil {
		log.Fatal(err)
	}

	// Write json to file
	err = os.WriteFile(indexName + "_top_1000_searches.json", resEncodeTopSearchesJson, 0644) // 0644 grants read/write permissions for the owner, and read for others
	if err != nil {
		log.Fatalf("Error writing to file: %v", err)
	}
}