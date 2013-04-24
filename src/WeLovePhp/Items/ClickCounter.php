<?php

namespace WeLovePhp\Items;

class ClickCounter
{
    protected $redis;
    protected $ranges;

    public function __construct($redis)
    {
        $this->redis = $redis;

        $this->ranges = array(
            'global' => NULL,
            'week'   => 60 * 60 * 24 * 7,
            'day'    => 60 * 60 * 24,
            'hour'   => 60 * 60,
            'minute' => 60,
            'second' => 1,
        );
    }

    public function countClick($id)
    {
        foreach ($this->ranges as $range => $expiration)
        {
            $key = $this->buildKey($range);
            $this->redis->zincrby($key, 1, $id);

            if (!is_null($expiration))
            {
                $this->redis->expire($key, $expiration);
            }
        }
    }

    public function getClicks($id, $range)
    {
        if (!in_array($range, array_keys($this->ranges)))
        {
            throw new Exception("Range '$range' not supported.");
        }

        $key = $this->buildKey($range);
        $count = $this->redis->zscore($key, $id);

        return is_null($count) ? 0 : (int) $count;
    }

    public function getPopularItems($range)
    {
        if (!in_array($range, array_keys($this->ranges)))
        {
            throw new Exception("Range '$range' not supported.");
        }

        $key = $this->buildKey($range);
        return $this->redis->zrevrange($key, 0, -1);
    }


    /*
     * I want to dedicate this method to Franco, who I know he loves
     * private methods.
     */
    private function buildKey($range)
    {
        switch ($range)
        {
            case 'global':
                return "items:clicks";
            case 'week':
                $week = date("Y-W");
                return "items:clicks:$week";
            case 'day':
                $day = date("Y-m-d");
                return "items:clicks:$day";
            case 'hour':
                $hour = date("Y-m-d-h");
                return "items:clicks:$hour";
            case 'minute':
                $minute = date("Y-m-d-h-m");
                return "items:clicks:$minute";
            case 'second':
                $second = date("Y-m-d-h-m-s");
                return "items:clicks:$second";
        }
    }
}
