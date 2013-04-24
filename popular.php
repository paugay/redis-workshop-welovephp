<?php

require_once 'vendor/autoload.php';
require_once 'config.php';

date_default_timezone_set('Europe/London');

use WeLovePhp\Items\ItemsManager;
use WeLovePhp\Items\ClickCounter;
use Doctrine\DBAL\DriverManager;

try {
    $config = new \Doctrine\DBAL\Configuration();
    $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
    $redis = new Predis\Client('tcp://127.0.0.1:6379');

    $clickCounters = new ClickCounter($redis);
    $itemsManager = new ItemsManager($conn, $redis, $clickCounters);

    // flush all data to not get confing results
    $itemsManager->deleteAll();
    $redis->flushdb();

    // create the items
    $id1 = $itemsManager->create('Item num. 1');
    $id2 = $itemsManager->create('Item num. 2');
    $id3 = $itemsManager->create('Item num. 3');

    // add some clicks
    $clickCounters->countClick($id1);
    $clickCounters->countClick($id2);
    $clickCounters->countClick($id2);
    $clickCounters->countClick($id3);
    $clickCounters->countClick($id3);
    $clickCounters->countClick($id3);

    sleep(1);

    // add some more clicks
    $clickCounters->countClick($id1);
    $clickCounters->countClick($id1);
    $clickCounters->countClick($id1);

    // get populars
    $sec = $itemsManager->getPopularItems('second');
    $min = $itemsManager->getPopularItems('minute');

    echo "\nPopular items in the last second:\n";
    var_dump($sec);

    echo "\nPopular items in the last minute:\n";
    var_dump($min);

} catch (\Exception $e) {
    die($e->getMessage());
}
