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
            $key = $this->buildKey($id, $range);
            $this->redis->incr($key);

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

        $key = $this->buildKey($id, $range);
        $count = $this->redis->get($key);

        return is_null($count) ? 0 : (int) $count;
    }

    /*
     * I want to dedicate this method to Franco, who I know he loves
     * private methods.
     */
    private function buildKey($id, $range)
    {
        switch ($range)
        {
            case 'global':
                return "items:clicks:$id";
            case 'week':
                $week = date("Y-W");
                return "items:clicks:$week:$id";
            case 'day':
                $day = date("Y-m-d");
                return "items:clicks:$day:$id";
            case 'hour':
                $hour = date("Y-m-d-h");
                return "items:clicks:$hour:$id";
            case 'minute':
                $minute = date("Y-m-d-h-m");
                return "items:clicks:$minute:$id";
            case 'second':
                $second = date("Y-m-d-h-m-s");
                return "items:clicks:$second:$id";
        }
    }
}
