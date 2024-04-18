<?php

#API Key Generator
#This script will generate an API key for an Algolia application.
#The generated key will be valid for Search operations, and will be limited to 100 queries per hour.

# Install the API client: https://www.algolia.com/doc/api-client/getting-started/install/php/?client=php
require __DIR__ . '/vendor/autoload.php';

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

# Set permissions for API key
# https://www.algolia.com/doc/api-reference/api-methods/add-api-key/#method-param-acl
$acl = ["search"];


try {
    // Set the parameters for the API key
    // https://www.algolia.com/doc/api-reference/api-methods/add-api-key/#method-param-maxqueriesperipperhour
    $params = [
        'description'=> 'Restricted search-only API key for algolia.com',
        // Rate-limit to 100 requests per hour per IP address
        'maxQueriesPerIPPerHour' => 100
    ];

    # Create a new restricted search-only API key
    print("Creating new key...\n");
    $res = $client->addApiKey($acl, $params)->wait();
    $new_key = $res['key'];
    if (strlen($new_key)==32) {
        echo "Key generated successfully: $new_key \n";
    } else {
        echo "Failed search with the new key\n";
    }
    
}
catch (Exception $e) {
    echo 'Message: ' .$e->getMessage();
}

# Test the created key
print("Testing key...\n");

# Initialise a new client with the generated key
$client = \Algolia\AlgoliaSearch\SearchClient::create($ALGOLIA_APP_ID, $new_key);

# Create an index (or connect to it, if an index with the name `ALGOLIA_INDEX_NAME` already exists)
# https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/#initialize-an-index
$index = $client->initIndex($ALGOLIA_INDEX_NAME);

try {
    # Test the new generated key by performing a search
    if ($search_res = $index->search('')) {
        echo "Successful key test\n";
    } else {
        echo "Failed search with the new key\n";
    }
}
catch (Exception $e) {
    echo 'Message: ' .$e->getMessage();
}

?>

