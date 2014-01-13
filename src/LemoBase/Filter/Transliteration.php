<?php

namespace LemoBase\Filter;

use Traversable;
use Zend\Filter\AbstractFilter;
use Zend\Stdlib\ArrayUtils;

class Transliteration extends AbstractFilter
{
    /**
     * Unique ID prefix used for allowing comments
     */
    const UNIQUE_ID_PREFIX = '__LemoBase_Filter_Transliteration__';

    /**
     * Encoding for the input string
     *
     * @var string
     */
    protected $encoding;

    /**
     *  Constructor
     *
     * Allowed options are
     *     'encoding'   => Input character encoding
     *
     * @param  string|array|Traversable $options
     * @return void
     */
    public function __construct($options = null)
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }
        if (!is_array($options)) {
            $options = func_get_args();
            $temp    = array();
            if (!empty($options)) {
                $temp['encoding'] = array_shift($options);
            }
            $options = $temp;
        }

        if (!array_key_exists('encoding', $options) && function_exists('mb_internal_encoding')) {
            $options['encoding'] = 'UTF-8';
        }

        if (array_key_exists('encoding', $options)) {
            $this->setEncoding($options['encoding']);
        }
    }

    /**
     * Defined by Zend\Filter\FilterInterface
     *
     * @param  string $value
     * @return string
     */
    public function filter($value)
    {
        if(setlocale(LC_ALL, 0) == 'C') {
            $locale = \Locale::getDefault();
            $localeKeys = array_keys($locale);

            setlocale(LC_ALL, $localeKeys[0]);
        }

        $value = iconv($this->getEncoding(), 'ASCII//TRANSLIT', $value);
        $value = str_replace("'", '', $value);

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
}
