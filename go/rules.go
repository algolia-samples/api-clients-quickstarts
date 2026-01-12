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

	// Exporting the rules 
	// https://www.algolia.com/doc/api-reference/api-methods/export-rules/#examples
	fmt.Printf("Original rules:\n")

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

	for _, rule := range rules {
		fmt.Printf("- Rule: %s\n", rule.ObjectID)
	}

	// Adding a new rule 
	// https://www.algolia.com/doc/api-reference/api-methods/save-rule/#save-a-rule
	objectID := "id1"
	fmt.Println("Adding a new rule called:", objectID)
	resSaveRule, err := client.SaveRule(client.NewApiSaveRuleRequest(
	indexName, objectID,
	search.NewEmptyRule().SetObjectID(objectID).SetConditions(
		[]search.Condition{*search.NewEmptyCondition().SetPattern("flower").SetAnchoring(search.Anchoring("contains"))}).SetConsequence(
		search.NewEmptyConsequence().SetPromote(
		[]search.Promote{*search.PromoteObjectIDAsPromote(
			search.NewEmptyPromoteObjectID().SetObjectID("439957720").SetPosition(0))}))))

	if err != nil {
		PrintErrAndExit(err)
	}
	_, err = client.WaitForTask(indexName, resSaveRule.TaskID)
	if err != nil {
		PrintErrAndExit(err)
	}


	// print the response
	fmt.Println(resSaveRule)

	// Exporting the modified rules 
	// https://www.algolia.com/doc/api-reference/api-methods/export-rules/#examples
	fmt.Printf("Updated rules:\n")

	var updatedRules []search.Rule

	err = client.BrowseRules(
		indexName,
		*search.NewEmptySearchRulesParams(),
		search.WithAggregator(func(r any, err error) {
			if err != nil {
				log.Fatalf("There was an error: %v", err)
			}

			updatedRules = append(updatedRules, r.(*search.SearchRulesResponse).Hits...)
		}),
	)
	if err != nil {
		log.Fatal(err)
	}

	for _, rule := range updatedRules {
		fmt.Printf("- Rule: %s\n", rule.ObjectID)
	}
}