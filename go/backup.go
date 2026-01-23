package main

import (
	"fmt"
	"log"
	"os"
	"github.com/algolia/algoliasearch-client-go/v4/algolia/search"
	"github.com/joho/godotenv"
	"encoding/json"
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

	// Get all records from an index
	// https://www.algolia.com/doc/api-reference/api-methods/browse/#get-all-records-from-an-index
	// Use an API key with `browse` ACL
	resBrowseObj, err := client.Browse(client.NewApiBrowseRequest(
		indexName))

	if err != nil {
	// handle the eventual error
		panic(err)
	}

	fmt.Println("Current objects")
	PrintCurrentObjects()

	// Encode array to json
	resEncodeObjJson, err := json.Marshal(resBrowseObj.Hits)
	if err != nil {
		log.Fatal(err)
	}

	// Write json to file
	err = os.WriteFile(indexName + "_records.json", resEncodeObjJson, 0644) // 0644 gives read/write permissions for the owner, and read for others
	if err != nil {
		log.Fatalf("Error writing to file: %v", err)
	}

	// Retrieve settings for an index
	// https://www.algolia.com/doc/api-reference/api-methods/get-settings/#retrieve-settings-for-an-index
	resGetSettings, err := client.GetSettings(client.NewApiGetSettingsRequest(
	indexName).WithGetVersion(2))
		if err != nil {
		// handle the eventual error
		panic(err)
	}

	fmt.Println("Current settings")
	fmt.Println(resGetSettings)

	// Encode array to json
	resEncodeSettingsJson, err := json.Marshal(resGetSettings)
	if err != nil {
		log.Fatal(err)
	}
	
	// Write json to file
	err = os.WriteFile(indexName + "_settings.json", resEncodeSettingsJson, 0644) // 0644 grants read/write permissions for the owner
	if err != nil {
		log.Fatalf("Error writing to file: %v", err)
	}

	// Export rules 
	// https://www.algolia.com/doc/api-reference/api-methods/export-rules/
	var rules []search.Rule

	err = client.BrowseRules(
		indexName,
		*search.NewEmptySearchRulesParams(),
		search.WithAggregator(func(r any, err error) {
			if err != nil {
				log.Fatalf("There was an error: %v", err)
			}

			rules = append(rules, r.(*search.SearchRulesResponse).Hits...)
		}),
	)
	if err != nil {
		log.Fatal(err)
	}

	fmt.Println("Current rules:")
	for _, rule := range rules {
		fmt.Printf("- Rule: %s\n", rule.ObjectID)
	}

	// Encode array to json
	resEncodeRulesJson, err := json.Marshal(rules)
	if err != nil {
		log.Fatal(err)
	}

	// Write json to file
	err = os.WriteFile(indexName + "_rules.json", resEncodeRulesJson, 0644) // 0644 grants read/write permissions for the owner
	if err != nil {
		log.Fatalf("Error writing to file: %v", err)
	}

	// Export synonyms
	// https://www.algolia.com/doc/api-reference/api-methods/export-synonyms/
		var synonyms []search.SynonymHit

	err = client.BrowseSynonyms(
		indexName,
		*search.NewEmptySearchSynonymsParams(),
		search.WithAggregator(func(r any, err error) {
			if err != nil {
				log.Fatalf("There was an error: %v", err)
			}

			synonyms = append(synonyms, r.(*search.SearchSynonymsResponse).Hits...)
		}),
	)
	if err != nil {
		log.Fatal(err)
	}

	for _, synonym := range synonyms {
		fmt.Printf("- Synonym: %s\n", synonym.ObjectID)
	}

	// Encode array to json
	resEncodeSynonymsJson, err := json.Marshal(synonyms)
	if err != nil {
		log.Fatal(err)
	}

	// Write json to file
	err = os.WriteFile(indexName + "_synonyms.json", resEncodeSynonymsJson, 0644) // 0644 grants read/write permissions for the owner
	if err != nil {
		log.Fatalf("Error writing to file: %v", err)
	}
}