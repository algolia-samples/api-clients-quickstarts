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

# Restoring all records with replace all objects method
# https://www.algolia.com/doc/api-reference/api-methods/replace-all-objects/

# Read json file
$jsonRecords = file_get_contents("{$ALGOLIA_INDEX_NAME}_records.json");
print("All the records:");
var_dump($jsonRecords);
print("\n");

# Decode json file
$arrayRecords = json_decode($jsonRecords, true);

# Restore Records
$index->replaceAllObjects($arrayRecords);
print("Records restored\n");

# Restoring settings with set settings method
# https://www.algolia.com/doc/api-reference/api-methods/set-settings/

# Read json file
$jsonSettings = file_get_contents("{$ALGOLIA_INDEX_NAME}_settings.json");
print("Index settings:\n");
var_dump($jsonSettings);
print("\n");

# Decode json file
$settings = json_decode($jsonSettings, true);

# Restore settings
$index->setSettings($settings);
print("Settings restored\n");

# Restoring Rules with replace all rules method
# https://www.algolia.com/doc/api-reference/api-methods/replace-all-rules/

# Read json file
$jsonRules = file_get_contents("{$ALGOLIA_INDEX_NAME}_rules.json");
print("Rules:\n");
var_dump($jsonRules);
print("\n");

# Decode json file
$arrayRules = json_decode($jsonRules, true);

# Restore Rules
$index->replaceAllRules($arrayRules);
print("Rules restored\n");


# Restoring Synonyms with replace all synonyms method
# https://www.algolia.com/doc/api-reference/api-methods/replace-all-synonyms/?client=php

# Read json file
$jsonSynonyms = file_get_contents("{$ALGOLIA_INDEX_NAME}_synonyms.json");
print("Synonyms:\n");
var_dump($jsonSynonyms);
print("\n");

# Decode json file
$arraySynonyms = json_decode($jsonSynonyms, true);

# Restore Synonyms
$index->replaceAllSynonyms($arraySynonyms);
print("Synonyms restored\n");