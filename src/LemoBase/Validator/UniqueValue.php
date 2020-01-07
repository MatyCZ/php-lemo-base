<?php

namespace LemoBase\Validator;

use Laminas\Validator\AbstractValidator;

class UniqueValue extends AbstractValidator
{
    const INVALID = 'valueInvalid';
    const NOT_UNIQUE = 'valueNotUnique';

    /**
     * @var bool
     */
    protected $caseSensitive = false;

    /**
     * @var array
     */
    protected $haystack = array();

    /**
     * @var array
     */
    protected $messageTemplates = array(
        self::INVALID => "Invalid type given. String or integer expected",
        self::NOT_UNIQUE => "Value must be unique",
    );

    /**
     * Set validator options
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        if(array_key_exists('haystack', $options) && is_array($options['haystack'])) {
            $this->setHaystack($options['haystack']);
        }

        if(array_key_exists('case_sensitive', $options) && true === $options['case_sensitive']) {
            $this->setCaseSensitive(true);
        }

        parent::__construct($options);
    }

    /**
     * If value is unique oposit the haystack, returns true
     *
     * @param  string $value
     * @return bool
     */
    public function isValid($value)
    {
        if (!is_string($value) && !is_int($value)) {
            $this->error(self::INVALID);
            return false;
        }

        if(empty($this->haystack)) {
            return true;
        }

        if($this->inArray($value, $this->haystack)) {
            $this->error(self::NOT_UNIQUE);
            return false;
        }

        return true;
    }

    /**
     * Lower string multibyte by default
     *
     * @param  string $string
     * @param  string $encoding
     * @return string
     */
    protected function toLower($string, $encoding = 'UTF-8')
    {
        return mb_strtolower($string, $encoding);
    }

    /**
     * Lower string on all levels of array
     *
     * @param  array $array
     * @return array
     */
    protected function arrayToLower(array $array)
    {
        foreach ($array as &$value) {
            switch (true) {
                case is_string($value):
                    $value = $this->toLower($value);
                    break;
                case is_array($value):
                    $value = $this->arrayToLower($value);
                    break;
            }
        }

        return $array;
    }

    /**
     * In array comparsion, case insensitive by default
     *
     * @param  string|array $needle
     * @param  string|array $haystack
     * @param  bool         $caseSensitive
     * @param  bool         $strict
     * @return bool
     */
    protected function inArray($needle, $haystack, $caseSensitive = false, $strict = false)
    {
        switch ($caseSensitive) {
            case true:
                return in_array($needle, $haystack, $strict);
                break;

            default:
            case false:
                if (is_array($needle)) {
                    return in_array($this->arrayToLower($needle), $this->arrayToLower($haystack), $strict);
                } else {
                    return in_array($this->toLower($needle), $this->arrayToLower($haystack), $strict);
                }
                break;
        }
    }

    /**
     * Set case sensitive comparsion
     *
     * @param  boolean $caseSensitive
     * @return UniqueValue
     */
    public function setCaseSensitive($caseSensitive)
    {
        $this->caseSensitive = $caseSensitive;

        return $this;
    }

    /**
     * Set haystack for comparsion
     *
     * @param  array $values
     * @return UniqueValue
     */
    public function setHaystack(array $values)
    {
        $this->haystack = $values;

        return $this;
    }
}