<?php

namespace LemoBase\Cache;

use Traversable;
use Zend\Cache\Storage\StorageInterface;
use Zend\Stdlib\RequestInterface;
use Zend\Cache\Exception;
use Zend\Cache\StorageFactory;

class CacheManager
{
    /**
     * @var StorageInterface[]
     */
    protected $storage = [];

    /**
     * @var StorageInterface[]
     */
    protected $storageInstance = [];

    /**
     * @var string
     */
    protected $hash;

    /**
     * Konstruktor
     *
     * @param RequestInterface $request
     */
    public function __construct(RequestInterface $request)
    {
        // Zjistime si HASH stranky, pokud se nejedná o console request
        if ($request instanceof \Zend\Console\Request) {
            $hashPost = null;
            $hashQuery = null;
        } else {
            $hashPost = $request->getPost('_ch');
            $hashQuery = $request->getQuery('_ch');
        }

        if (!empty($hashPost)) {
            $hash = $hashPost;
        } elseif (!empty($hashQuery)) {
            $hash = $hashQuery;
        } else {
            $hash = uniqid(null, true);
        }

        // Pouzijeme hash
        $this->hash = $hash;
    }

    /**
     * Memcache failure callback
     *
     * @param $host
     * @param $port
     */
    public static function memcacheFailureCallback($host, $port)
    {
    }

    /**
     * Add a $storage
     *
     * @param  array|Traversable|StorageInterface $storage
     * @throws Exception\InvalidArgumentException
     * @return CacheManager
     */
    public function add($storage, $name)
    {
        if (is_array($storage)
            || ($storage instanceof Traversable && !$storage instanceof StorageInterface)
        ) {
            $storage = StorageFactory::factory($storage);
        }

        if (!$storage instanceof StorageInterface) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s requires that $storage be an object implementing %s; received "%s"',
                __METHOD__,
                StorageInterface::class,
                (is_object($storage) ? get_class($storage) : gettype($storage))
            ));
        }

        if (empty($name)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s: adapter provided is not named,',
                __METHOD__
            ));
        }

        $this->storage[$name] = $storage;

        return $this;
    }

    /**
     * Does the cache manager have a storage by the given name?
     *
     * @param  string $name
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($name, $this->storage);
    }

    /**
     * @param  string $name
     * @return Cache
     */
    public function get($name)
    {
        $hash = $this->hash;
        if (null === $this->hash) {
            $hash = 'default';
        }

        if (!isset($this->storage[$name])) {
            throw new Exception\InvalidArgumentException(sprintf(
                "Storage with name '%s' was not found in '%s'",
                $name,
                self::class
            ));
        }

        if (!isset($this->storageInstance[$name][$hash])) {
            $this->storageInstance[$name][$hash] = new Cache($this->storage[$name], $hash);
        }

        return $this->storageInstance[$name][$hash];
    }

    /**
     * Remove a named storage
     *
     * @param  string $name
     * @return CacheManager
     */
    public function remove($name)
    {
        if (is_array($this->storage) && array_key_exists($name, $this->storage)) {
            unset($this->storage[$name]);
        }
        if (is_array($this->storageInstance) && array_key_exists($name, $this->storageInstance)) {
            unset($this->storageInstance[$name]);
        }

        return $this;
    }

    /**
     * Set storages
     *
     * @return CacheManager
     */
    public function setStorages(array $storages)
    {
        $this->clear();

        foreach ($storages as $name => $storage) {
            $this->add($storage, $name);
        }

        return $this;
    }

    /**
     * Retrieve all attached storages
     *
     * @return array
     */
    public function getStorages()
    {
        return $this->storage;
    }

    /**
     * Clear all attached columns
     *
     * @return CacheManager
     */
    public function clear()
    {
        $this->storage = array();
        $this->storageInstance = array();

        return $this;
    }
    /**
     * @param  string $hash
     * @return CacheManager
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Vrati hash, ktery se pouziva pro unikatnost zaznamu v memcache
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }
}
