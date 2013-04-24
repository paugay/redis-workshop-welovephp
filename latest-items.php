<?php

require_once 'vendor/autoload.php';
require_once 'config.php';

use WeLovePhp\Items\ItemsManager;
use Doctrine\DBAL\DriverManager;

$n = $argv[1] ? (int) $argv[1] : 4;

try {
    $config = new \Doctrine\DBAL\Configuration();
    $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
    $redis = new Predis\Client('tcp://127.0.0.1:6379');

    $manager = new ItemsManager($conn, $redis);

    $manager->create('Item num. 1');
    $manager->create('Item num. 2');
    $manager->create('Item num. 3');
    $manager->create('Item num. 4');
    $manager->create('Item num. 5');
    $manager->create('Item num. 6');

    $manager->delete('Item num. 5');

    $items = $manager->getLatestItems($n);

    print_r($items);

} catch (\Exception $e) {
    die($e->getMessage());
}
