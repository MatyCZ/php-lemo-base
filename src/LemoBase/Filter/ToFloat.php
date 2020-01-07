<?php


namespace LemoBase\Filter;

use Laminas\Filter\AbstractFilter;

class ToFloat extends AbstractFilter
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
        $value = preg_replace('~[^0-9\.]~', '', $value);

        $value = floatval($value);
        $value = round($value, $this->getPrecision());
        $value = number_format($value, $this->getPrecision(), '.', '');

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