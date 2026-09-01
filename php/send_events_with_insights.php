<?php

require __DIR__.'/vendor/autoload.php';

use Algolia\AlgoliaSearch\Api\SearchClient;
use Algolia\AlgoliaSearch\Api\InsightsClient;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

# Algolia client credentials
$ALGOLIA_APP_ID = $_ENV['ALGOLIA_APP_ID'];
$ALGOLIA_API_KEY = $_ENV['ALGOLIA_API_KEY'];
$ALGOLIA_INDEX_NAME = $_ENV['ALGOLIA_INDEX_NAME'];
$ALGOLIA_APPLICATION_REGION = "us";

# Initialize the client
# https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/
$client = SearchClient::create($ALGOLIA_APP_ID, $ALGOLIA_API_KEY);

//Add objectIDs for testing
$products = [
    [
        'name' => 'Computer',
        'objectID' => '9780545139700'
    ],
    [
        'name' => 'Phone',
        'objectID' => '9780439784542'
    ]
];

$response = $client->saveObjects($ALGOLIA_INDEX_NAME, $products);

$client->waitForTask($ALGOLIA_INDEX_NAME, $response[0]['taskID']);

$insightsClient = InsightsClient::create($ALGOLIA_APP_ID, $ALGOLIA_API_KEY, $ALGOLIA_APPLICATION_REGION);

// Product Clicked
$response = $insightsClient->pushEvents(
    ['events' => [
        ['eventType' => 'click',
            'eventName' => 'Product Clicked',
            'index' => $ALGOLIA_INDEX_NAME,
            'userToken' => 'user-123456',
            'authenticatedUserToken' => 'user-123456',
            'timestamp' => time(),
            'objectIDs' => [
                '9780545139700',

                '9780439784542',
            ],
            'queryID' => '43b15df305339e827f0ac0bdc5ebcaa7',
            'positions' => [
                7,

                6,
            ],
        ],
    ],
    ],
);

var_dump($response);

// Add to Cart
$response = $insightsClient->pushEvents(
    ['events' => [
        ['eventType' => 'conversion',
            'eventSubtype' => 'addToCart',
            'eventName' => 'Product Added to Cart',
            'index' => $ALGOLIA_INDEX_NAME,
            'userToken' => 'user-123456',
            'authenticatedUserToken' => 'user-123456',
            'timestamp' => time() + 60,
            'objectIDs' => ['9780545139700'],
            'queryID' => '43b15df305339e827f0ac0bdc5ebcaa7',
            'objectData' => [
                    [
                        'price'    => 29.99,
                        'quantity' => 1,
                        'discount' => 0.00
                    ]
                ],
            'currency' => 'USD',
            'value' => 29.99
        ],
    ],
    ],
);

var_dump($response);

// Purchase Event
$response = $insightsClient->pushEvents(
    ['events' => [
        ['eventType' => 'conversion',
            'eventName' => 'Product Purchased',
            'index' => $ALGOLIA_INDEX_NAME,
            'userToken' => 'user-123456',
            'authenticatedUserToken' => 'user-123456',
            'timestamp' => time() + 120,
            'objectIDs' => [
                '9780545139700',

                '9780439784542',
            ],
            'queryID' => '43b15df305339e827f0ac0bdc5ebcaa7',
        ],
    ],
    ],
);


// print the response
var_dump($response);
