<?php

require __DIR__.'/vendor/autoload.php';

use Algolia\AlgoliaSearch\Api\MonitoringClient;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

# Algolia client credentials
$ALGOLIA_APP_ID = $_ENV['ALGOLIA_APP_ID'];
$ALGOLIA_MONITORING_API_KEY = $_ENV['ALGOLIA_MONITORING_API_KEY'];
$ALGOLIA_INDEX_NAME = $_ENV['ALGOLIA_INDEX_NAME'];

$client = MonitoringClient::create($ALGOLIA_APP_ID, $ALGOLIA_MONITORING_API_KEY);

//Get Status Updates
print_r("Retrieving Status Updates");
$response = $client->getStatus();

var_dump($response);

//Get All Servers, Followed By Latency Times
print_r("Retrieving Latency Times Of All Servers");
$respServers = $client->getServers();

foreach ($respServers["inventory"] as $key => $value) {
    $respLatencyTime = $client->getLatency($value["name"]);
    print_r("Current Server:");
    print_r($value["name"]);
    var_dump($respLatencyTime);
}



?>