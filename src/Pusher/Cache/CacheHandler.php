<?php

namespace PhpPusher\Cache;

trait CacheHandler
{
    /**
     * Cache data
     */
    protected $cache = ['list' => [], 'dict' => []];

    /**
     * Running Tiumer
     */
    protected $timer = [];
}
