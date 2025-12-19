<?php

require __DIR__.'/vendor/autoload.php';

use Algolia\AlgoliaSearch\Api\SearchClient;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

# Algolia client credentials
$ALGOLIA_APP_ID = $_ENV['ALGOLIA_APP_ID'];
$ALGOLIA_API_KEY = $_ENV['ALGOLIA_API_KEY'];
$ALGOLIA_INDEX_NAME = $_ENV['ALGOLIA_INDEX_NAME'];

# Initialize the client
# https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/
$client = SearchClient::create($ALGOLIA_APP_ID, $ALGOLIA_API_KEY);

# Define some objects to add to our index
# https://www.algolia.com/doc/api-client/methods/indexing/#object-and-record
$contacts = [
    [
        'name' => 'Foo',
        'objectID' => '1'
    ],
    [
        'name' => 'Bar',
        'objectID' => '2'
    ]
    ];

# We don't have any objects (yet) in our index
$res = $client->search(
    ['requests' => [
        ['indexName' => $ALGOLIA_INDEX_NAME,
            'query' => '',
            'hitsPerPage' => 50,
        ],
    ],
    ],
);

print('Current objects: ');
print_r($res['results'][0]['hits']);
print("\n");

# Save Objects: Add mutliple new objects to an index.
# https://www.algolia.com/doc/api-reference/api-methods/add-objects/?client=php
print('Save Objects - Adding multiple objects: ');
print_r($contacts);
$res = $client->saveObjects($ALGOLIA_INDEX_NAME, $contacts);

$client->waitForTask($ALGOLIA_INDEX_NAME, $res['taskID']);

$res = $client->search(
    ['requests' => [
        ['indexName' => $ALGOLIA_INDEX_NAME,
            'query' => '',
            'hitsPerPage' => 50,
        ],
    ],
    ],
);

print('Current objects: ');
print_r($res['results'][0]['hits']);
print("\n");

# Save Objects: Replace an existing object with an updated set of attributes.
# https://www.algolia.com/doc/api-reference/api-methods/save-objects/?client=php
print('Save Objects - Replacing objects’s attributes on: ');
print_r($contacts[0]);
$new_contact = [
    'name' => 'FooBar',
    'objectID' => '1'
];
$res = $client->saveObject($ALGOLIA_INDEX_NAME, $new_contact);

$client->waitForTask($ALGOLIA_INDEX_NAME, $res['taskID']);

$res = $client->search(
    ['requests' => [
        ['indexName' => $ALGOLIA_INDEX_NAME,
            'query' => '',
            'hitsPerPage' => 50,
        ],
    ],
    ],
);

print('Current objects: ');
print_r($res['results'][0]['hits']);
print("\n");

# Partial Update Objects: Update one or more attributes of an existing object.
# https://www.algolia.com/doc/api-reference/api-methods/partial-update-objects/?client=php
print('Save Objects - Updating object’s attributes on: ');
print_r($contacts[0]);
$new_contact = [
    'email' => 'foo@bar.com' # New attribute
];
$res = $client->partialUpdateObject($ALGOLIA_INDEX_NAME, '1', $new_contact);

$client->waitForTask($ALGOLIA_INDEX_NAME, $res['taskID']);

$res = $client->search(
    ['requests' => [
        ['indexName' => $ALGOLIA_INDEX_NAME,
            'query' => '',
            'hitsPerPage' => 50,
        ],
    ],
    ],
);

print('Current objects: ');
print_r($res['results'][0]['hits']);
print("\n");

# Delete Objects: Remove objects from an index using their objectID.
# https://www.algolia.com/doc/api-reference/api-methods/delete-objects/?client=php
$objectID_to_delete = $contacts[0]["objectID"];
printf('Delete Objects - Deleting object with objectID "%s"', $objectID_to_delete);
$res = $client->deleteObject($ALGOLIA_INDEX_NAME, $objectID_to_delete);

$client->waitForTask($ALGOLIA_INDEX_NAME, $res['taskID']);

$res = $client->search(
    ['requests' => [
        ['indexName' => $ALGOLIA_INDEX_NAME,
            'query' => '',
            'hitsPerPage' => 50,
        ],
    ],
    ],
);

print('Current objects: ');
print_r($res['results'][0]['hits']);
print("\n");

# Replace All Objects: Clears all objects from your index and replaces them with a new set of objects.
# https://www.algolia.com/doc/api-reference/api-methods/replace-all-objects/?client=php
$new_contacts = [
    [
        'name' => 'NewFoo',
        'objectID' => '3'
    ],
    [
        'name' => 'NewBar',
        'objectID' => '4'
    ]
];
print('Replace All Objects - Clears all objects and replaces them with: ');
print_r($new_contacts);
$res = $client->replaceAllObjects($ALGOLIA_INDEX_NAME, $new_contacts);

$client->waitForTask($ALGOLIA_INDEX_NAME, $res['taskID']);

$res = $client->search(
    ['requests' => [
        ['indexName' => $ALGOLIA_INDEX_NAME,
            'query' => '',
            'hitsPerPage' => 50,
        ],
    ],
    ],
);

print('Current objects: ');
print_r($res['results'][0]['hits']);
print("\n");

# Delete By: Remove all objects matching a filter (including geo filters).
# https://www.algolia.com/doc/api-reference/api-methods/delete-by/?client=php
print_r('Delete By - Remove all objects matching "name:NewBar"');

# Firstly, have an attribute to filter on
# https://www.algolia.com/doc/api-client/methods/settings/?client=php
$res = $client->setSettings($ALGOLIA_INDEX_NAME, 
[
    'attributesForFaceting' => ['name']
]);

$client->waitForTask($ALGOLIA_INDEX_NAME, $res['taskID']);

$res = $client->deleteBy($ALGOLIA_INDEX_NAME,
[
    'facetFilters' => ['name:NewBar'] # https://www.algolia.com/doc/api-reference/api-parameters/facetFilters/
]);

$client->waitForTask($ALGOLIA_INDEX_NAME, $res['taskID']);

$res = $client->search(
    ['requests' => [
        ['indexName' => $ALGOLIA_INDEX_NAME,
            'query' => '',
            'hitsPerPage' => 50,
        ],
    ],
    ],
);

print('Current objects: ');
print_r($res['results'][0]['hits']);
print("\n");

# Get Objects: Get one or more objects using their objectIDs.
# https://www.algolia.com/doc/api-reference/api-methods/get-objects/?client=php
$object_id = $new_contacts[0]['objectID'];
printf('Get Objects - Getting object with objectID "%s"', $object_id);

$res = $client->getObject($ALGOLIA_INDEX_NAME, $object_id);
print('Results: ');
print_r($res);
print("\n");

# Custom Batch: Perform several indexing operations in one API call.
# https://www.algolia.com/doc/api-reference/api-methods/batch/?client=php
print('Custom Batch - Batching the operations: ');

$res = $client->multipleBatch(
    ['requests' => [
        ['action' => 'addObject',
            'body' => [
                'name' => 'BatchedBar',
            ],
            'indexName' => $ALGOLIA_INDEX_NAME,
        ],
    ],
    ],
);

$client->waitForTask($ALGOLIA_INDEX_NAME, $res['taskID'][$ALGOLIA_INDEX_NAME]);

$res = $client->search(
    ['requests' => [
        ['indexName' => $ALGOLIA_INDEX_NAME,
            'query' => '',
            'hitsPerPage' => 50,
        ],
    ],
    ],
);

print('Current objects: ');
print_r($res['results'][0]['hits']);
print("\n");


# Clear Objects: Clear the records of an index without affecting its settings.
# https://www.algolia.com/doc/api-reference/api-methods/clear-objects/?client=php
print_r("Clear Objects: Clear the records of an index without affecting its settings.\n");
$res = $client->clearObjects($ALGOLIA_INDEX_NAME);

$client->waitForTask($ALGOLIA_INDEX_NAME, $res['taskID']);

# We don't have any objects in our index
$res = $client->search(
    ['requests' => [
        ['indexName' => $ALGOLIA_INDEX_NAME,
            'query' => '',
            'hitsPerPage' => 50,
        ],
    ],
    ],
);

print('Current objects: ');
print_r($res['results'][0]['hits']);