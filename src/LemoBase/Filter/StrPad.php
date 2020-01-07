<?php

namespace LemoBase\Filter;

use Laminas\Filter\AbstractFilter;
use Traversable;

class StrPad extends AbstractFilter
{
    protected $options = [
        'pad_length' => null,
        'pad_string' => null,
        'pad_type'   => null,
    ];

    /**
     * Constructor
     *
     * Supported options are
     *     'pad_length' => If the value of pad_length is negative, less than, or equal to the length of the input string, no padding takes place, and input will be returned.
     *     'pad_string' => The pad_string may be truncated if the required number of padding characters can't be evenly divided by the pad_string's length.
     *     'pad_type'   => Optional argument pad_type can be STR_PAD_RIGHT, STR_PAD_LEFT, or STR_PAD_BOTH. If pad_type is not specified it is assumed to be STR_PAD_RIGHT.
     *
     * @param  array|Traversable|string|null $options
     */
    public function __construct($options = null)
    {
        if ($options instanceof Traversable) {
            $options = iterator_to_array($options);
        }

        if (null !== $options) {
            $this->setOptions($options);
        }
    }

    /**
     * @param  int $padLength
     * @return $this
     */
    public function setPadLength($padLength)
    {
        $this->options['pad_length'] = $padLength;
        return $this;
    }

    /**
     * @return int
     */
    public function getPadLength()
    {
        return $this->options['pad_length'];
    }

    /**
     * @param  string $padString
     * @return $this
     */
    public function setPadString($padString)
    {
        $this->options['pad_string'] = $padString;
        return $this;
    }

    /**
     * @return string
     */
    public function getPadString()
    {
        return $this->options['pad_string'];
    }

    /**
     * @param  string $padType
     * @return $this
     */
    public function setPadType($padType)
    {
        $this->options['pad_type'] = $padType;
        return $this;
    }

    /**
     * @return string
     */
    public function getPadType()
    {
        return $this->options['pad_type'];
    }

    /**
     * @param  string $value
     * @return string
     */
    public function filter($value)
    {
        if (empty($value)) {
            return $value;
        }

        return str_pad($value, $this->options['pad_length'], $this->options['pad_string'], $this->options['pad_type']);
    }
}
