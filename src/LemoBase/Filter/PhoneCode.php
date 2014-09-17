<?php


namespace LemoBase\Filter;

use Zend\Filter\AbstractFilter;
use Zend\Filter\StringTrim;

class PhoneCode extends AbstractFilter
{
    /**
     * @var string
     */
    protected $code = '420';

    /**
     * @param array|\Traversable $options
     */
    public function __construct(array $options = array())
    {
        if(array_key_exists('code', $options) && is_string($options['code'])) {
            $this->setCode($options['code']);
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
        $value = (string) $value;

        if(!$this->getCode()) {
            return $value;
        }

        if(preg_match('~^(\++)?(' . $this->getCode() . ')?([0-9]+){1}$~', $value, $m)) {

            // není číslo
            if(empty($m[3])) {
                return $value;
            }

            // pravděpodobně jiná předvolba
            if(!empty($m[1]) && empty($m[2])) {
                return $value;
            }

            return '+' . $this->getCode()  . $m[3];
        }

        return $value;
    }

    /**
     * @param string $code
     * @return PhoneCode
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }
}
