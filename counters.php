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

    $itemsManager = new ItemsManager($conn, $redis);
    $clickCounters = new ClickCounter($redis);

    // create the items
    $itemsManager->create('Item num. 1');
    $itemsManager->create('Item num. 2');

    // fetch them
    $items = $itemsManager->getLatestItems(5);
    $itemId = $items[0]['id'];

    // add some clicks
    echo "We do some clicks into item $itemId\n";
    $clickCounters->countClick($items[0]['id']);
    $clickCounters->countClick($items[0]['id']);

    // fetch
    echo "Get the number of clicks for item $itemId in the last second: ";
    echo $clickCounters->getClicks($items[0]['id'], 'second') . "\n";

    // sleep
    echo "Sleep for 1 second ...\n";
    sleep(1);

    // fetch again
    echo "Get the number of clicks for item $itemId in the last second: ";
    echo $clickCounters->getClicks($items[0]['id'], 'second') . "\n";

} catch (\Exception $e) {
    die($e->getMessage());
}
