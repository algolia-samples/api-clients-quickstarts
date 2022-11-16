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
# https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/
$client = \Algolia\AlgoliaSearch\SearchClient::create($ALGOLIA_APP_ID, $ALGOLIA_API_KEY);

# Create an index (or connect to it, if an index with the name `ALGOLIA_INDEX_NAME` already exists)
# https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/#initialize-an-index
$index = $client->initIndex($ALGOLIA_INDEX_NAME);

# Exporting the rules 
# https://www.algolia.com/doc/api-reference/api-methods/export-rules/#examples
print("Original rules:\n");
$iterator = $index->browseRules();
foreach ($iterator as $rule) {
  var_dump($rule);
}
print("\n");

# Adding a new rule 
# https://www.algolia.com/doc/api-reference/api-methods/save-rule/#save-a-rule
$object_id = 'a-rule-id';
print("Adding a new rule called: $object_id\n");
$rule = array(
    'objectID' => $object_id,
    'conditions' => array(array(
        'pattern'   => 'flower',
        'anchoring' => 'contains',
    )),
    'consequence' => array(
        'promote' => array(array(
            'objectID' => '439957720',
            'position' => 0,
        ))
    )
);
$index->saveRule($rule)->wait();

# Exporting the modified rules 
# https://www.algolia.com/doc/api-reference/api-methods/export-rules/#examples
print("Modified rules:\n");
$iterator = $index->browseRules();
foreach ($iterator as $rule) {
  var_dump($rule);
}
print("\n");

?>
