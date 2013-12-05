<?php

namespace LemoBase\Filter;

use Traversable;
use Zend\Filter\AbstractFilter;
use Zend\Filter\Exception;

class StringReplace extends AbstractFilter
{
    protected $options = array(
        'search'     => null,
        'replace' => '',
    );

    /**
     * Constructor
     *
     * Supported options are
     *     'search'  => The value being searched for.
     *     'replace' => The replacement value that replaces found search values.
     *
     * @param  array|Traversable|string|null $options
     */
    public function __construct($options = null)
    {
        if ($options instanceof Traversable) {
            $options = iterator_to_array($options);
        }

        if (!is_array($options)
            || (!isset($options['search']) && !isset($options['replace'])))
        {
            $args = func_get_args();
            if (isset($args[0])) {
                $this->setPattern($args[0]);
            }
            if (isset($args[1])) {
                $this->setReplacement($args[1]);
            }
        } else {
            $this->setOptions($options);
        }
    }

    /**
     * Set the value being searched for
     *
     * @see str_replace()
     *
     * @param  string|array $search - same as the first argument of str_replace
     * @return self
     * @throws Exception\InvalidArgumentException
     */
    public function setPattern($search)
    {
        if (!is_array($search) && !is_string($search)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects pattern to be string or array; received "%s"',
                __METHOD__,
                (is_object($search) ? get_class($search) : gettype($search))
            ));
        }

        $this->options['search'] = $search;
        return $this;
    }

    /**
     * Get currently set value being searched for
     *
     * @return string|array
     */
    public function getPattern()
    {
        return $this->options['search'];
    }

    /**
     * Set the replacement array/string
     *
    * @see str_replace()
     *
     * @param  array|string $replace - same as the second argument of str_replace
     * @return self
     * @throws Exception\InvalidArgumentException
     */
    public function setReplacement($replace)
    {
        if (!is_array($replace) && !is_string($replace)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects replace to be string or array; received "%s"',
                __METHOD__,
                (is_object($replace) ? get_class($replace) : gettype($replace))
            ));
        }
        $this->options['replace'] = $replace;
        return $this;
    }

    /**
     * Get currently set replace value
     *
     * @return string|array
     */
    public function getReplace()
    {
        return $this->options['replace'];
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

        return str_replace($this->options['search'], $this->options['replace'],$value);
    }
}
