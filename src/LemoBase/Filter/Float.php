<?php


namespace LemoBase\Filter;

use Zend\Filter\AbstractFilter;

class Float extends AbstractFilter
{
    /**
     * @var int
     */
    protected $precision = 4;

    /**
     * @param null|array $options
     */
    public function __construct($options = null) {

        if(is_array($options)) {
            if(array_key_exists('precision', $options) && is_int($options['precision']) && $options['precision'] > 4) {
                $this->setPrecision($options['precision']);
            }
        }
    }

    /**
     * @param mixed $value
     * @return float|mixed|string
     */
    public function filter($value) {

        if (!strlen((string) $value)) {
            return $value;
        }

        $value = (string) $value;
        $isNegative = preg_match('~^\-~', $value) ? true : false;
        $value = preg_replace('~[^[0-9\.]]~', '', $value);

        $value = (float) $value;
        $value = round($value, $this->getPrecision());

        $value = (string) $value;

        if(preg_match('~([0-9]+)(\.*)([0-9]*)~', $value, $m)) {
            $value = $m[1] . '.' . str_pad($m[3], $this->getPrecision(), '0', STR_PAD_RIGHT);
        }

        if(true === $isNegative) {
            $value = '-' . $value;
        }

        return $value;
    }

    /**
     * @param int $precision
     * @return Float
     */
    public function setPrecision($precision)
    {
        $this->precision = $precision;

        return $this;
    }

    /**
     * @return int
     */
    public function getPrecision()
    {
        return $this->precision;
    }
}