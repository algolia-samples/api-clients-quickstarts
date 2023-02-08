<?php

# Install the API client: https://www.algolia.com/doc/api-client/getting-started/install/php/?client=php
require __DIR__.'/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

# Get your Algolia Application ID and (admin) API key from the dashboard: https://www.algolia.com/account/api-keys
# and choose a name for your index. Add these environment variables to a `.env` file:
$ALGOLIA_APP_ID = $_ENV['ALGOLIA_APP_ID'];
$ALGOLIA_API_KEY = $_ENV['ALGOLIA_API_KEY'];
$ALGOLIA_INDEX_NAME = $_ENV['ALGOLIA_INDEX_NAME'];

# Start the API client
# https://www.algolia.com/doc/api-client/getting-started/initialize/php/?client=php#initialize-the-search-client
$client = \Algolia\AlgoliaSearch\SearchClient::create($ALGOLIA_APP_ID, $ALGOLIA_API_KEY);

# Create an index (or connect to it, if an index with the name `ALGOLIA_INDEX_NAME` already exists)
# https://www.algolia.com/doc/api-client/getting-started/initialize/php/?client=php#initialize-the-search-client
$index = $client->initIndex($ALGOLIA_INDEX_NAME);

# Get all records from an index
# https://www.algolia.com/doc/api-reference/api-methods/browse/#get-all-records-from-an-index
# Use an API key with `browse` ACL
print("All the records:");
$records = $index->browseObjects();
var_dump($records);
print("\n");

# Transform records iterator to array
$all_records = iterator_to_array($records);

# Encode array to json
$jsonRecords = json_encode($all_records);

# Write json to file
if (file_put_contents("{$ALGOLIA_INDEX_NAME}_records.json", $jsonRecords))
    echo "JSON file created successfully...\n";
else 
    echo "Oops! Error creating json file...\n";

# Retrieve settings for an index
# https://www.algolia.com/doc/api-reference/api-methods/get-settings/#retrieve-settings-for-an-index
print("Index settings:\n");
$settings = $index->getSettings();
var_dump($settings);
print("\n");

# Encode array to json
$jsonSettings = json_encode($settings);

# Write json to file
if (file_put_contents("{$ALGOLIA_INDEX_NAME}_settings.json", $jsonSettings))
    echo "JSON file created successfully...\n";
else 
    echo "Oops! Error creating json file...\n";

# Export rules 
# https://www.algolia.com/doc/api-reference/api-methods/export-rules/
print("Rules:\n");
$rules = $index->browseRules();
var_dump($rules);
print("\n");

# Transform rules iterator to array
$all_rules = iterator_to_array($rules);

# Encode array to json
$jsonRules = json_encode($all_rules);

# Write json to file
if (file_put_contents("{$ALGOLIA_INDEX_NAME}_rules.json", $jsonRules))
    echo "JSON file created successfully...\n";
else 
    echo "Oops! Error creating json file...\n";

# Export synonyms
# https://www.algolia.com/doc/api-reference/api-methods/export-synonyms/
print("Synonyms:\n");
$synonyms = $index->browseSynonyms();
var_dump($synonyms);
print("\n");

# Transform synonyms iterator to array
$all_synonyms = iterator_to_array($synonyms);

# Encode array to json
$jsonSynonyms = json_encode($all_synonyms);

# Write json to file
if (file_put_contents("{$ALGOLIA_INDEX_NAME}_synonyms.json", $jsonSynonyms))
    echo "JSON file created successfully...\n";
else 
    echo "Oops! Error creating json file...\n";



