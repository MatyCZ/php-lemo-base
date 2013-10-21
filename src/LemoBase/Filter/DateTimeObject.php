<?php

namespace LemoBase\Filter;

use DateTime;
use Traversable;
use Zend\Filter\AbstractFilter;

class DateTimeObject extends AbstractFilter
{
    /**
     * Sets filter options
     *
     * @param array|Traversable $options
     */
    public function __construct($options = null)
    {
        if ($options) {
            $this->setOptions($options);
        }
    }

    /**
     * Filter a datetime string by normalizing it to the filters specified format
     *
     * @param  string $value
     * @return string
     */
    public function filter($value)
    {
        if (empty($value)) {
            return null;
        }

        return new DateTime($value);
    }
}