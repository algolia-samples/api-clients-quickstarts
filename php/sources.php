<?php

require __DIR__.'/vendor/autoload.php';

use Algolia\AlgoliaSearch\Api\IngestionClient;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

# Algolia client credentials
$ALGOLIA_APP_ID = $_ENV['ALGOLIA_APP_ID'];
$ALGOLIA_API_KEY = $_ENV['ALGOLIA_API_KEY'];
$ALGOLIA_INDEX_NAME = $_ENV['ALGOLIA_INDEX_NAME'];
$ALGOLIA_APPLICATION_REGION = "us";

// Initialize the client
$client = IngestionClient::create($ALGOLIA_APP_ID, $ALGOLIA_API_KEY, $ALGOLIA_APPLICATION_REGION);

// Get authentication ID

/* If you have an Authentication ID, you may remove the next portion of the code */

// -------------------------------------------------
// Call the API
$response = $client->createAuthentication(
    ['type' => 'oauth',
        'name' => 'myAuthName10',
        'input' => ['url' => 'http://test.oauth',
            'client_id' => 'myID',
            'client_secret' => 'mySecret',
        ],
    ],
); 


// print the response
var_dump("Authentication created");
var_dump($response);
var_dump ("Authentication ID");
var_dump($response[ "authenticationID"]);

// -------------------------------------------------

$sourceAuthenticationID = $response[ "authenticationID"]; // Replace with a string if Authentication ID is known

// Call the API
$response = $client->createSource(
    ['type' => 'algolia',
        'name' => 'sourceName11',
        'input' => ['storeKeys' => [
            'myStore',
        ],
            'locales' => [
                'de',
            ],
            'url' => 'http://commercetools.com',
            'projectKey' => 'keyID',
            'productQueryPredicate' => 'masterVariant(attributes(name="Brand" and value="Algolia"))',
        ],
        'authenticationID' => $sourceAuthenticationID,
    ],
);


// print the response
var_dump($response);

$sourceID = $response["sourceID"];

// List sources
$response = $client->listSources();

// print the response
var_dump($response);

# Validate source payload
$response = $client->validateSource(
    ['type' => 'commercetools',
        'name' => 'sourceName11',
        'input' => ['storeKeys' => [
            'myStore',
        ],
            'locales' => [
                'de',
            ],
            'url' => 'http://commercetools.com',
            'projectKey' => 'keyID',
        ],
        'authenticationID' => $sourceAuthenticationID,
    ],
);


// print the response
var_dump($response);

#Create a destination
// Call the API
$response = $client->createDestination(
    ['type' => 'search',
        'name' => 'destinationName',
        'input' => ['indexName' => $ALGOLIA_INDEX_NAME,
        ],
        'authenticationID' => $sourceAuthenticationID,
    ],
);


// print the response
var_dump($response);

$destinationID = $response["destinationID"];

# Create a task

// Call the API
$response = $client->createTask(
    ['sourceID' => $sourceID,
        'destinationID' => $destinationID,
        'action' => 'replace',
    ],
);


// print the response
var_dump($response);

$taskID = $response["taskID"];

// Run tasks linked to a source
$response = $client->runSource(
    $sourceID,
    ['indexToInclude' => [
        $ALGOLIA_INDEX_NAME,
    ],
        'entityIDs' => [
            '1234',

            '5678',
        ],
        'entityType' => 'product',
    ],
);


// print the response
var_dump($response);

// Delete a source
$response = $client->deleteSource(
    $sourceAuthenticationID,
);


// print the response
var_dump($response);