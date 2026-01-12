package main

import (
	"fmt"
	"log"
	"os"
	"io/ioutil"
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

	// Restoring all records with replace all objects method
	// https://www.algolia.com/doc/api-reference/api-methods/replace-all-objects/

	// Read json file
    fmt.Println("Records:")
	recordsFile := indexName + "_records.json"
	jsonFile, err := os.Open(recordsFile)
    if err != nil {
        fmt.Printf("failed to open json file: %s, error: %v", recordsFile, err)
        return
    }
    defer jsonFile.Close()

    byteValue, _ := ioutil.ReadAll(jsonFile)

    var result []map[string]any
    json.Unmarshal([]byte(byteValue), &result)

    fmt.Println(result)

	// Restore Records
	resReplaceObj, err := client.ReplaceAllObjects(indexName, result)
	if err != nil {
	// handle the eventual error
		panic(err)
	}

	_, err = client.WaitForTask(indexName, resReplaceObj.MoveOperationResponse.TaskID);
	if err != nil {
		PrintErrAndExit(err)
	}

	fmt.Println("Records restored")

	// Restoring settings with set settings method
	// https://www.algolia.com/doc/api-reference/api-methods/set-settings/

	// Read json file
	fmt.Println("Index settings")
	settingsFileName := indexName + "_settings.json"
	settingsFile, err := os.Open(settingsFileName)
    if err != nil {
        fmt.Printf("failed to open json file: %s, error: %v", settingsFileName, err)
        return
    }
    defer jsonFile.Close()

    settingsJson, _ := ioutil.ReadAll(settingsFile)

	settings := search.NewEmptyIndexSettings()
	json.Unmarshal(settingsJson, settings)

    fmt.Println(settings)

	// Restore settings
	resSetSettings, err := client.SetSettings(
		client.NewApiSetSettingsRequest(indexName, settings).
			WithForwardToReplicas(true),
	)
	if err != nil {
		// handle the eventual error
		panic(err)
	}

	_, err = client.WaitForTask(indexName, resSetSettings.TaskID)
	if err != nil {
		PrintErrAndExit(err)
	}

	fmt.Println("Settings restored")

	// Restoring Rules with replace all rules method
	// https://www.algolia.com/doc/api-reference/api-methods/replace-all-rules/

	// Read json file
	fmt.Println("Rules:")
	rulesFileName := indexName + "_rules.json"
	rulesFile, err := os.Open(rulesFileName)
    if err != nil {
        fmt.Printf("failed to open json file: %s, error: %v", rulesFileName, err)
        return
    }
    defer rulesFile.Close()

    rulesJson, _ := ioutil.ReadAll(rulesFile)

    var rules []search.Rule
	json.Unmarshal(rulesJson, &rules)

	fmt.Println(rules)

	// Restore Rules
	resSaveRules, err := client.SaveRules(	
		client.NewApiSaveRulesRequest(
			indexName,
			rules,
		).
			WithForwardToReplicas(false).
			WithClearExistingRules(true),
	)
	if err != nil {
		PrintErrAndExit(err)
	}

	_, err = client.WaitForTask(indexName, resSaveRules.TaskID)
	if err != nil {
		PrintErrAndExit(err)
	}

	fmt.Println("Rules restored")

	// Restoring Synonyms with replace all synonyms method
	// https://www.algolia.com/doc/api-reference/api-methods/replace-all-synonyms/?client=php

	// Read json file
	fmt.Println("Synonyms:")
	synonymsFileName := indexName + "_synonyms.json"
	synonymsFile, err := os.Open(synonymsFileName)
    if err != nil {
        fmt.Printf("failed to open json file: %s, error: %v", synonymsFileName, err)
        return
    }
    defer synonymsFile.Close()

    synonymsJson, _ := ioutil.ReadAll(synonymsFile)

    var synonyms []search.SynonymHit
	json.Unmarshal(synonymsJson, &synonyms)

	fmt.Println(synonyms)

	// Restore Synonyms
	resSaveSynonyms, err := client.SaveSynonyms(	
		client.NewApiSaveSynonymsRequest(
			indexName,
			synonyms,
		).
			WithForwardToReplicas(true).
			WithReplaceExistingSynonyms(true),
	)
	if err != nil {
		PrintErrAndExit(err)
	}

	_, err = client.WaitForTask(indexName, resSaveSynonyms.TaskID)
	if err != nil {
		PrintErrAndExit(err)
	}

	fmt.Println("Synonyms restored")

}