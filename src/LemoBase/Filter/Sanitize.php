<?php

namespace LemoBase\Filter;

use Traversable;
use Zend\Filter\AbstractFilter;
use Zend\Filter\StringToLower;
use Zend\Filter\StringTrim;
use Zend\Stdlib\ArrayUtils;

class Sanitize extends AbstractFilter
{
    /**
     * Unique ID prefix used for allowing comments
     */
    const UNIQUE_ID_PREFIX = '__LemoBase_Filter_Sanitize__';

    /**
     * Encoding for the input string
     *
     * @var string
     */
    protected $encoding;

    /**
     * Word separator
     *
     * @var string
     */
    protected $separator = '-';

    /**
     * Convert to lower case
     *
     * @var bool
     */
    protected $lowercase = true;

    /**
     * Sets the filter options
     * Allowed options are
     *     'encoding'   => Input character encoding
     *     'separator'  => Word separator
     *
     * @param  string|array|Traversable $options
     * @return void
     */
    public function __construct($options = null)
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }
        if ((!is_array($options)) || (is_array($options) && !array_key_exists('encoding', $options) &&
            !array_key_exists('separator', $options))) {
            $options = func_get_args();
            $temp = array();
            if (!empty($options)) {
                $temp['encoding'] = array_shift($options);
            }
            if (!empty($options)) {
                $temp['separator'] = array_shift($options);
            }

            $options = $temp;
        }

        if (!array_key_exists('encoding', $options) && function_exists('mb_internal_encoding')) {
            $options['encoding'] = mb_internal_encoding();
        }

        if (array_key_exists('encoding', $options)) {
            $this->setEncoding($options['encoding']);
        }

        if (array_key_exists('separator', $options)) {
            $this->setSeparator($options['separator']);
        }

        if (array_key_exists('lowercase', $options)) {
            $this->setLowercase($options['lowercase']);
        }
    }

    /**
     * Defined by Zend\Filter\FilterInterface
     *
     * @todo improve docblock descriptions
     *
     * @param  string $value
     * @return string
     */
    public function filter($value)
    {
        $value = (string) $value;

        $value = $this->trimString($value);
        $value = $this->convertToAscii($value);
        $value = $this->convertToLowerCase($value);
        $value = $this->convertToLetterAndDigits($value);

        return $value;
    }

    /**
     * Trim spaces, tabs and newlines
     *
     * @param string $value
     * @return string
     */
    protected function trimString($value)
    {
        $filter = new StringTrim();

        return $filter->filter($value);
    }

    /**
     * Convert value to ASCII
     *
     * @param string $value
     * @return string
     */
    protected function convertToAscii($value)
    {
        $filter = new Transliteration();

        return $filter->filter($value);
    }

    /**
     * Convert value to lowercase
     *
     * @param string $value
     * @return string
     */
    protected function convertToLowerCase($value)
    {
        if(true !== $this->getLowercase()) {
            return $value;
        }

        $filter = new StringToLower();
        $filter->setEncoding($this->getEncoding());

        return $filter->filter($value);
    }

    /**
     * Convert value to string with letters and digits
     *
     * @param string $value
     * @return string
     */
    protected function convertToLetterAndDigits($value)
    {
        $value = preg_replace('~[^\\pL\d]+~u', $this->getSeparator(), $value);
        $value = trim($value, $this->getSeparator());
        return $value;
    }

    /**
     * Set input character encoding
     *
     * @param  string $encoding
     * @return Sanitize
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;

        return $this;
    }

    /**
     * Return input character encoding
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * Set char(s) which are set for word separator
     *
     * @return Sanitize
     */
    public function setSeparator($separator)
    {
        $this->separator = $separator;

        return $this;
    }

    /**
     * Return char(s) which are set for word separator
     *
     * @return string
     */
    public function getSeparator()
    {
        return $this->separator;
    }

    /**
     * @param bool $lowercase
     * @return Sanitize
     */
    public function setLowercase($lowercase)
    {
        $this->lowercase = $lowercase;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getLowercase()
    {
        return $this->lowercase;
    }
}
