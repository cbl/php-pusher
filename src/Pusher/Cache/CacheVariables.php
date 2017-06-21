<?php

namespace PhpPusher\Cache;

trait CacheVariables
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
