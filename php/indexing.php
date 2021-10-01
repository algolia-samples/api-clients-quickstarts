<?php

require __DIR__.'/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

# Algolia client credentials
$ALGOLIA_APP_ID = $_ENV['ALGOLIA_APP_ID'];
$ALGOLIA_API_KEY = $_ENV['ALGOLIA_API_KEY'];
$ALGOLIA_INDEX_NAME = $_ENV['ALGOLIA_INDEX_NAME'];

# Initialize the client
# https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/
$client = \Algolia\AlgoliaSearch\SearchClient::create($ALGOLIA_APP_ID, $ALGOLIA_API_KEY);

# Initialize an index
# https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/#initialize-an-index
$index = $client->initIndex($ALGOLIA_INDEX_NAME);

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
$res = $index->search('');
print('Current objects: ');
print_r($res['hits']);
print("\n");

# Save Objects: Add mutliple new objects to an index.
# https://www.algolia.com/doc/api-reference/api-methods/add-objects/?client=python
print('Save Objects - Adding multiple objects: ');
print_r($contacts);
$index->saveObjects($contacts)->wait();

$res = $index->search('');
print('Current objects: ');
print_r($res['hits']);
print("\n");

# Save Objects: Replace an existing object with an updated set of attributes.
# https://www.algolia.com/doc/api-reference/api-methods/save-objects/?client=python
print('Save Objects - Replacing objects’s attributes on: ');
print_r($contacts[0]);
$new_contact = [
    'name' => 'FooBar',
    'objectID' => '1'
];
$index->saveObject($new_contact)->wait();

$res = $index->search('');
print('Current objects: ');
print_r($res['hits']);
print("\n");

# Partial Update Objects: Update one or more attributes of an existing object.
# https://www.algolia.com/doc/api-reference/api-methods/partial-update-objects/?client=python
print('Save Objects - Updating object’s attributes on: ');
print_r($contacts[0]);
$new_contact = [
    'email' => 'foo@bar.com', # New attribute
    'objectID' => '1'
];
$index->partialUpdateObject($new_contact)->wait();

$res = $index->search('');
print('Current objects: ');
print_r($res['hits']);
print("\n");

# Delete Objects: Remove objects from an index using their objectID.
# https://www.algolia.com/doc/api-reference/api-methods/delete-objects/?client=python
$objectID_to_delete = $contacts[0]["objectID"];
printf('Delete Objects - Deleting object with objectID "%s"', $objectID_to_delete);
$index->deleteObject($objectID_to_delete)->wait();

$res = $index->search('');
print('Current objects: ');
print_r($res['hits']);
print("\n");

# Replace All Objects: Clears all objects from your index and replaces them with a new set of objects.
# https://www.algolia.com/doc/api-reference/api-methods/replace-all-objects/?client=python
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
$index->replaceAllObjects($new_contacts)->wait();

$res = $index->search('');
print('Current objects: ');
print_r($res['hits']);
print("\n");

# Delete By: Remove all objects matching a filter (including geo filters).
# https://www.algolia.com/doc/api-reference/api-methods/delete-by/?client=python
print_r('Delete By - Remove all objects matching "name:NewBar"');

# Firstly, have an attribute to filter on
# https://www.algolia.com/doc/api-client/methods/settings/?client=python
$index->setSettings([
    'attributesForFaceting' => ['name']
])->wait();

$index->deleteBy([
    'facetFilters' => ['name:NewBar'] # https://www.algolia.com/doc/api-reference/api-parameters/facetFilters/
])->wait();

$res = $index->search('');
print('Current objects: ');
print_r($res['hits']);
print("\n");

# Get Objects: Get one or more objects using their objectIDs.
# https://www.algolia.com/doc/api-reference/api-methods/get-objects/?client=python
$object_id = $new_contacts[0]['objectID'];
printf('Get Objects - Getting object with objectID "%s"', $object_id);

$res = $index->getObject($object_id);
print('Results: ');
print_r($res);
print("\n");

# Custom Batch: Perform several indexing operations in one API call.
# https://www.algolia.com/doc/api-reference/api-methods/batch/?client=python
$operations = [
    [
        'action' => 'addObject',
        'indexName' => $ALGOLIA_INDEX_NAME,
        'body' => [
            'name' => 'BatchedBar',
        ]
    ],
    [
        'action' => 'updateObject',
        'indexName' => $ALGOLIA_INDEX_NAME,
        'body' => [
            'objectID' => $object_id,
            'name' => 'NewBatchedBar',
        ]
    ]
];
print('Custom Batch - Batching the operations: ');
print_r( $operations);
$res = $client->multipleBatch($operations)->wait();

$res = $index->search('');
print('Current objects: ');
print_r($res['hits']);
print("\n");


# Clear Objects: Clear the records of an index without affecting its settings.
# https://www.algolia.com/doc/api-reference/api-methods/clear-objects/?client=python
print_r("Clear Objects: Clear the records of an index without affecting its settings.\n");
$index->clearObjects()->wait();

# We don't have any objects in our index
$res = $index->search('');
print('Current objects: ');
print_r($res['hits']);