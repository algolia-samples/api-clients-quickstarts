<?php

# Install the API client: https://www.algolia.com/doc/api-client/getting-started/install/php/?client=php
require __DIR__.'/vendor/autoload.php';

use Algolia\AlgoliaSearch\Api\SearchClient;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

# Get your Algolia Application ID and (admin) API key from the dashboard: https://www.algolia.com/account/api-keys
# and choose a name for your index. Add these environment variables to a `.env` file:
$ALGOLIA_APP_ID = $_ENV['ALGOLIA_APP_ID'];
$ALGOLIA_API_KEY = $_ENV['ALGOLIA_API_KEY'];
$ALGOLIA_INDEX_NAME = $_ENV['ALGOLIA_INDEX_NAME'];

# Start the API client
# https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/
$client = SearchClient::create($ALGOLIA_APP_ID, $ALGOLIA_API_KEY);

# Get all records from an index
# https://www.algolia.com/doc/api-reference/api-methods/browse/#get-all-records-from-an-index
# Use an API key with `browse` ACL
print("All the records:");
$records = $client->browse($ALGOLIA_INDEX_NAME,);
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
$settings = $client->getSettings($ALGOLIA_INDEX_NAME, 1);
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
$rules = $client->browseRules($ALGOLIA_INDEX_NAME);
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
$synonyms = $client->browseSynonyms($ALGOLIA_INDEX_NAME);
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



