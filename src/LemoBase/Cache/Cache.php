<?php

namespace LemoBase\Cache;

use Laminas\Cache\Storage\StorageInterface;
use Laminas\Cache\Storage\TaggableInterface;

class Cache
{
    /**
     * @var string
     */
    protected $hash;

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * Konstruktor
     *
     * @param StorageInterface $storage
     * @param string           $hash
     */
    public function __construct(StorageInterface $storage, $hash)
    {
        $this->storage = $storage;
        $this->hash = $hash;
    }

    /**
     * @return StorageInterface
     */
    public function getInstance()
    {
        return $this->storage;
    }

    /**
     * Nastavi tagy k polozce v cache dle zadaneho klice
     *
     * Prazdne pole s tagy odstrani vsechny tagy pro polozku.
     *
     * @param string   $key
     * @param string[] $tags
     * @param bool     $tryAgain
     * @return bool
     */
    public function setTags($key, array $tags, $tryAgain = true)
    {
        try {

            if ($this->storage instanceof TaggableInterface) {
                return $this->storage->setTags($key, $tags);
            }

            // Polozka neni v cache, nema smysl hledat tagy
            if (!$this->hasItem($key)) {
                return false;
            }

            // Nacteme si tagy z cache
            $listOfTags = $this->getItem('_list_of_tags_');
            if (null === $listOfTags) {
                $listOfTags = array();
            }

            // Pridame tagy do cache
            foreach ($tags as $tag) {
                $listOfTags[$tag][] = $key;
            }

            // Ulozime tagy zpet do cache
            $this->setItem('_list_of_tags_', $listOfTags);

        } catch (\Exception $ex) {
            if (true === $tryAgain) {
                return $this->setTags($key, $tags, false);
            }

            return false;
        }

        return true;
    }

    /**
     * Nastavi tagy k polozce s hash v cache dle zadaneho klice
     *
     * Prazdne pole s tagy odstrani vsechny tagy pro polozku.
     *
     * @param string   $key
     * @param string[] $tags
     * @return bool
     */
    public function setTagsWithHash($key, array $tags)
    {
        return $this->setTags($this->hash . '_' . $key, $tags);
    }

    /**
     * Vrati tagy pro polozku dle zadaneho klice
     *
     * @param  string $key
     * @return string[]|false
     */
    public function getTags($key)
    {
        if ($this->storage instanceof TaggableInterface) {
            return $this->storage->getTags($key);
        }

        // Polozka neni v cache, nema smysl hledat tagy
        if (!$this->hasItem($key)) {
            return false;
        }

        // Nacteme si tagy z cache
        $listOfTags = $this->getItem('_list_of_tags_');
        if (null === $listOfTags) {
            $listOfTags = array();
        }

        // Projdeme vsechny tagy a vratime ty, ve kterych je klic obsazen
        $tags = array();
        foreach ($listOfTags as $tag => $keys) {
            if (in_array($key, $keys)) {
                $tags[] = $tag;
            }
        }

        return $tags;
    }

    /**
     * Vrati tagy pro polozku s hash dle zadaneho klice
     *
     * @param  string $key
     * @return string[]|false
     */
    public function getTagsWithHash($key)
    {
        return $this->getTags($this->hash . '_' . $key);
    }

    /**
     * Odstrani polozky z cache, ktere odpovidaji zadanym tagum
     *
     * If $disjunction only one of the given tags must match
     * else all given tags must match.
     *
     * @param string[] $tags
     * @param  bool  $disjunction
     * @param  bool  $tryAgain
     * @return bool
     */
    public function clearByTags(array $tags, $disjunction = false, $tryAgain = true)
    {
        try {

            if ($this->storage instanceof TaggableInterface) {
                return $this->storage->clearByTags($tags, $disjunction);
            }

            // Nacteme si tagy z cache
            $listOfTags = $this->getItem('_list_of_tags_');
            if (null === $listOfTags) {
                $listOfTags = array();
            }

            // Sestavime si seznam klicu, ktere se maji odstranit
            $keysToRemove = array();
            foreach ($tags as $tag) {
                if (isset($listOfTags[$tag])) {
                    if (true == $disjunction) {
                        $keysToRemove = array_merge($keysToRemove, $listOfTags[$tag]);
                    } else {
                        if (empty($keysToRemove)) {
                            $keysToRemove = $listOfTags[$tag];
                        }

                        $keysToRemove = array_intersect($keysToRemove, $listOfTags[$tag]);
                    }
                }
            }

            // Odstranime z mapy tagu klice, ktere budou odstraneny
            foreach (array_keys($listOfTags) as $tag) {
                $listOfTags[$tag] = array_diff($listOfTags[$tag], $keysToRemove);

                // Tag je uplne prazdny, odstranime ho
                if (empty($listOfTags[$tag])) {
                    unset($listOfTags[$tag]);
                }
            }

            // Odstranime klice z cache
            $this->removeItems($keysToRemove);

            // Ulozime tagy zpet do cache
            $this->setItem('_list_of_tags_', $listOfTags);

        } catch (\Exception $ex) {
            // přidáno kvůli problému s file system cache mandatory flock() na Windows
            if (true === $tryAgain) {
                return $this->clearByTags($tags, $disjunction, false);
            }

            return false;
        }

        return true;
    }

    /**
     * Overi, zda v cache existuje hodnota dle zadaneho klice
     *
     * @param  string $key
     * @return bool
     */
    public function hasItem($key)
    {
        return $this->storage->hasItem($key);
    }

    /**
     * Overi, zda v cache existuje hodnota dle zadaneho klice
     *
     * @param  string $key
     * @return bool
     */
    public function hasItemWithHash($key)
    {
        return $this->hasItem($this->hash . '_' . $key);
    }

    /**
     * Ulozi do cache novou hodnotu nebo prepise stavajici
     *
     * @param  string $key
     * @param  mixed  $value
     * @param  bool   $tryAgain
     * @return CacheManager
     */
    public function setItem($key, $value, $tryAgain = true)
    {
        try {

            if (null === $value) {
                $this->storage->removeItem($key);
                return $this;
            }

            $this->storage->setItem($key, $value);

        } catch (\Exception $ex) {
            if(true === $tryAgain) {
                $this->setItem($key, $value, false);
            }
        }

        return $this;
    }

    /**
     * Ulozi do cache novou hodnotu nebo prepise stavajici a pouzije hash pro cache
     *
     * @param  string $key
     * @param  mixed  $value
     * @return CacheManager
     */
    public function setItemWithHash($key, $value)
    {
        return $this->setItem($this->hash . '_' . $key, $value);
    }

    /**
     * Vrati z cache existujici hodnotu nebo pole hodnot, jinak vrati false
     *
     * @param  string $key
     * @return array|string
     */
    public function getItem($key)
    {
        return $this->storage->getItem($key);
    }

    /**
     * Vrati z cache existujici hodnotu nebo pole hodnot, jinak vrati false a pouzije hash pro cac
     *
     * @param  string $key
     * @return array|string
     */
    public function getItemWithHash($key)
    {
        return $this->getItem($this->hash . '_' . $key);
    }

    /**
     * Odstrani hodnotu z cache
     *
     * @param  string $key
     * @param  bool   $tryAgain
     * @return Cache
     */
    public function removeItem($key, $tryAgain = true)
    {
        try {
            if(!empty($key)) {
                $this->storage->removeItem($key);
            }

        } catch (\Exception $ex) {
            if(true === $tryAgain) {
                $this->removeItem($key, false);
            }
        }

        return $this;
    }

    /**
     * Odstrani hodnotu z cache
     *
     * @param  string $key
     * @return Cache
     */
    public function removeItemWithHash($key)
    {
        return $this->removeItem($this->hash . '_' . $key);
    }

    /**
     * Odstrani nekolik hodnot z cache najednou
     *
     * @param  array $keys
     * @return array Pole odstranenych klicu
     */
    public function removeItems(array $keys)
    {
        if(empty($keys)) {
            return $keys;
        }

        return $this->storage->removeItems($keys);
    }

    /**
     * Odstrani nekolik hodnot z cache najednou
     *
     * @param  array $keys
     * @return array Pole odstranenych klicu
     */
    public function removeItemsWithHash(array $keys)
    {
        foreach ($keys as $index => $key) {
            $keys[$index] = $this->hash . '_' . $key;
        }

        return $this->removeItems($keys);
    }

    /**
     * @return Cache
     */
    public function flush()
    {
        $this->storage->flush();

        return $this;
    }
}