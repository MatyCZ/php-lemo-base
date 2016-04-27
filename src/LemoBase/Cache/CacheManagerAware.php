<?php

namespace LemoBase\Cache;

trait CacheManagerAware
{
    /**
     * @var CacheManager
     */
    protected $cacheManager;

    /**
     * @param  CacheManager $cacheManager
     * @return mixed
     */
    public function setCacheManager($cacheManager)
    {
        $this->cacheManager = $cacheManager;
        return $this;
    }

    /**
     * @return CacheManager
     */
    public function getCacheManager()
    {
        return $this->cacheManager;
    }
}