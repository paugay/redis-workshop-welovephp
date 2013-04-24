<?php

namespace WeLovePhp\Items;

use Doctrine\DBAL\Connection;

class ItemsManager
{
    protected $connection;
    protected $redis;

    public function __construct(Connection $conn, $redis)
    {
        $this->connection = $conn;
        $this->redis = $redis;
    }

    public function load($id)
    {
        return $this->connection->executeQuery('SELECT * FROM items WHERE id = :id', array('id' => $id))->fetch(\PDO::FETCH_OBJ);
    }

    public function loadByTitle($title)
    {
        return $this->connection->executeQuery('SELECT * FROM items WHERE title = :title', array('title' => $title))->fetch(\PDO::FETCH_OBJ);
    }

    public function create($title)
    {
        $this->connection->executeQuery('INSERT INTO items (title, ts) VALUES (:title, :ts)', array(
            'ts' => time(),
            ':title' => $title,
        ));

        $item = $this->load($this->connection->lastInsertId());

        $this->redis->lpush("items", json_encode($item));
        $this->redis->ltrim("items", 0, 10);

        return $id;
    }

    public function delete($title)
    {
        $item = $this->loadByTitle($title);

        $this->redis->lrem("items", 0, json_encode($item));

        $this->connection->delete('items', array('id' => $item->id));
    }

    public function getLatestItems($n)
    {
        $items =  $this->redis->lrange("items", 0, $n - 1);

        return array_map(
            function ($item) {
                return json_decode($item, TRUE);
            },
            $items
        );
    }
}
