<?php

require __DIR__.'/vendor/autoload.php';

use Algolia\AlgoliaSearch\Api\IngestionClient;
use Algolia\AlgoliaSearch\Exceptions\AlgoliaException;

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

#Find an authentication ID

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

    while ($type != "algolia" && $i < count($response["authentications"])) {
        $currentType = $response["authentications"][$i]["type"];

        // Exit condition: Stop the loop if the fruit is 'orange'
        if ($currentType == 'algolia') {
            $authenticationID = $response["authentications"][$i]["authenticationID"];
            $type = "algolia";
            break; // Prematurely exits the while loop
        }
        
        $i++; // Move to the next index
    }

    if ($type == "search") {
        var_dump("Type never found");
       $authenticationID = createAuthenticationID(); //Replace value with the Authentication ID of choice
    }

} 

// -----------------------------------------------------------------------------

/* Find the Destination ID */

/* If you have an Destination ID, you may remove the next portion of the code */

// -----------------------------------------------------------------------------

// Call the API
$response = $client->listDestinations();

// print the response
var_dump($response);

if (empty($response["destinations"])) {
    $destinationID = createDestinationID($authenticationID);
}
else {
    // loop through response to find Authentication ID with type: algolia
    $i = 0;
    $conditionMet = false;

    while ($conditionMet = false && $i < count($response["destinations"])) {

        // Exit condition: Stop the loop if the fruit is 'orange'
        if ($response["destinations"][$i]["authenticationID"] == $authenticationID) {
            $conditionMet = true;
            break; // Prematurely exits the while loop
        }
        
        $i++; // Move to the next index
    }

    if ($conditionMet == false) {
       var_dump("Destination ID never found");
       $destinationID = createDestinationID($authenticationID); //Replace value with the Authentication ID of choice
    }

} 


// -----------------------------------------------------------------------------

/* Find the Source ID */

/* If you have a Source ID, you may remove the next portion of the code */

// -----------------------------------------------------------------------------

// Call the API
$response = $client->listSources();

// print the response
var_dump($response);

if (empty($response["sources"])) {
    $sourceID = createSourceID($authenticationID);
}
else {
    // loop through response to find Authentication ID with type: algolia
    $i = 0;
    $conditionMet = false;

    while ($conditionMet = false && $i < count($response["sources"])) {

        // Exit condition: Stop the loop if the fruit is 'orange'
        if ($response["sources"][$i]["authenticationID"] == $authenticationID) {
            $conditionMet = true;
            break; // Prematurely exits the while loop
        }
        
        $i++; // Move to the next index
    }

    if ($conditionMet == false) {
       var_dump("Source ID never found");
       $sourceID = createSourceID($authenticationID); //Replace value with the Source ID of choice
    }

} 


// -----------------------------------------------------------------------------

# Create a task

$response = $client->createTask(
    ['sourceID' => $sourceID,
        'destinationID' => $destinationID,
        'action' => 'replace',
    ],
);

// print the response
var_dump($response);
$taskID = $response["taskID"];

# List tasks
// Call the API
$response = $client->listTasks();

// print the response
var_dump($response);

# Update task
// Call the API
$response = $client->updateTask(
    $taskID,
    ['enabled' => false,
        'cron' => '* * * * *',
    ],
);

// print the response
var_dump($response);

# Search for task
// Call the API
$response = $client->searchTasks(
    ['taskIDs' => [
        $taskID,
    ],
    ],
);

// print the response
var_dump($response);

# Enable task
// Call the API
$response = $client->enableTask(
    $taskID,
);

// print the response
var_dump($response);

# Run task
// Call the API
$response = $client->runTask(
    $taskID,
);

// print the response
var_dump($response);

# Disable task
// Call the API
$response = $client->disableTask(
    $taskID,
);

// print the response
var_dump($response);

# Delete task
// Call the API
$response = $client->deleteTask(
    $taskID,
);

// print the response
var_dump($response);

//Functions

function createAuthenticationID()
{
    global $client;
    global $ALGOLIA_ADMIN_API_KEY;
    global $ALGOLIA_APP_ID;
    $response = $client->createAuthentication(
        ['type' => 'algolia',
            'name' => 'my-algolia-destination-auth-6',
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

function createDestinationID($authenticationID) {
    global $client;
    global $ALGOLIA_ADMIN_API_KEY;
    global $ALGOLIA_APP_ID;
    global $ALGOLIA_INDEX_NAME;

    $response = $client->createDestination(
        ['type' => 'search',
            'name' => 'brandNewDestinationName8',
            'input' => ['indexName' => $ALGOLIA_INDEX_NAME,
            ],
        'authenticationID' => $authenticationID,
        ],
    );

    var_dump($response);

    return $response["destinationID"];
}

function createSourceID($authenticationID) {
    global $client;

    global $ALGOLIA_INDEX_NAME;

    $response = $client->createSource([
    'type' => 'commercetools',
    'name' => 'brandNewSourceName9',
    'input' => [
        'storeKeys' => [
            'myStore',
        ],
        'locales' => [
            'de',
        ],
        'url' => 'http://commercetools.com', //Replace with your API URL
        'projectKey' => 'keyID',
        'productQueryPredicate' =>
            'masterVariant(attributes(name="Brand" and value="Algolia"))',
    ],
    'authenticationID' => $authenticationID,
]);

$sourceID = $response["sourceID"];
}