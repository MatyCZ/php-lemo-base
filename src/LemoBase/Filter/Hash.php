<?php

namespace LemoBase\Filter;

use Laminas\Filter\AbstractFilter;
use Traversable;

class Hash extends AbstractFilter
{
    /**
     * @var int
     */
    protected $algorithm = null;

    /**
     * @param null|array $options
     */
    public function __construct($options = null)
    {
        if ($options instanceof Traversable) {
            $options = iterator_to_array($options);
        }

        if (!is_array($options) || (!isset($options['algorythm']))) {
            $args = func_get_args();
            if (isset($args[0])) {
                $this->setAlgorithm($args[0]);
            }
        } else {
            $this->setOptions($options);
        }
    }

    /**
     * @param  mixed $value
     * @return string
     */
    public function filter($value)
    {
        return hash($this->getAlgorithm(), $value);
    }

    /**
     * @param  int $algorithm
     * @return $this
     */
    public function setAlgorithm($algorithm)
    {
        $this->algorithm = $algorithm;

        return $this;
    }

    /**
     * @return int
     */
    public function getAlgorithm()
    {
        return $this->algorithm;
    }
}