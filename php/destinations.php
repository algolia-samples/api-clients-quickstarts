<?php

require __DIR__.'/vendor/autoload.php';

use Algolia\AlgoliaSearch\Api\IngestionClient;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

# Algolia client credentials
$ALGOLIA_APP_ID = $_ENV['ALGOLIA_APP_ID'];
$ALGOLIA_API_KEY = $_ENV['ALGOLIA_API_KEY'];
$ALGOLIA_ADMIN_API_KEY = $_ENV['ALGOLIA_ADMIN_API_KEY'];
$ALGOLIA_INDEX_NAME = $_ENV['ALGOLIA_INDEX_NAME'];
$ALGOLIA_APPLICATION_REGION = "us";

// Initialize the client
$client = IngestionClient::create($ALGOLIA_APP_ID, $ALGOLIA_API_KEY, $ALGOLIA_APPLICATION_REGION);

/* If you have an Authentication ID, you may remove the next portion of the code */

// -----------------------------------------------------------------------------

# Get list of Authentication IDs. If none exist, create an Authentication ID. 
// Call the API
$response = $client->listAuthentications();

// print the response
var_dump($response);

if (empty($response["authentications"])) {
    $authenticationID = createAuthenticationID();
}
else {
    // loop through response to find Authentication ID with type: algolia
    $type = "search";
    $i = 0;

    while ($type != "algolia") {
        $currentType = $response["authentications"][$i]["type"];

        // Exit condition: Stop the loop if the fruit is 'orange'
        if ($currentType === 'algolia') {
            $authenticationID = $response["authentications"][$i]["authenticationID"];
            $type = "algolia";
            break; // Prematurely exits the while loop
        }
        
        $i++; // Move to the next index
    }

    if ($type = "search") {
       $authenticationID = createAuthenticationID();
    }

}

// -----------------------------------------------------------------------------

# Create destination
// Call the API
$response = $client->createDestination(
    ['type' => 'search',
        'name' => 'destinationName',
        'input' => ['indexName' => $ALGOLIA_INDEX_NAME,
        ],
        'authenticationID' => $authenticationID,
    ],
);

// print the response
var_dump($response);

$destinationID = $response["destinationID"];

var_dump("Destination ID");
var_dump($destinationID);

# List destinations
// Call the API
$response = $client->listDestinations();

// print the response
var_dump($response);

# Update destination
// Call the API
$response = $client->updateDestination(
    $destinationID,
    ['name' => 'newName',],
);


// print the response
var_dump($response);

# Search for destination
$response = $client->searchDestinations(
    ['destinationIDs' => [
        $destinationID,
      ],
    ],
);

// print the response
var_dump($response);

# Delete destination
// Call the API
$response = $client->deleteDestination(
    $destinationID,
);

// print the response
var_dump($response);

// Functions

function createAuthenticationID()
{
    global $client;
    global $ALGOLIA_ADMIN_API_KEY;
    global $ALGOLIA_APP_ID;
    $response = $client->createAuthentication(
        ['type' => 'algolia',
            'name' => 'my-algolia-destination-auth',
            'input' => [
                'apiKey' => $ALGOLIA_ADMIN_API_KEY,
                'appID' => $ALGOLIA_APP_ID,
            ],
        ],
    );


    // print the response
    var_dump($response);
    return $response[ "authenticationID"];
}