<?php

namespace LemoBase\Filter;

use Laminas\Filter\AbstractFilter;
use Laminas\Filter\StringTrim;

class BankAccount extends AbstractFilter
{
    /**
     * @param  string|array|\Traversable $options
     */
    public function __construct($options = null)
    {

    }

    /**
     * Defined by Laminas\Filter\FilterInterface
     *
     * @param  string $value
     * @return string
     */
    public function filter($value)
    {
        $value = (string) $value;
        $value = $this->stringTrim($value);
        $value = $this->stripSpaces($value);

        $accountPrefix = '';
        $accountNo = '';
        $bankCode = '';

        if(preg_match('~^\-?([0-9]{2,10})\/([0-9]{4})$~', $value, $m)) {
            $accountNo = $this->stripLeadingZeros($m[1]);
            $bankCode = $m[2];
        } elseif(
            preg_match('~^([0-9]{2,6})\-([0-9]{2,10})\/([0-9]{4})$~', $value, $m) ||
            preg_match('~^([0-9]{1,6})([0-9]{10})\/([0-9]{4})$~', $value, $m)
        ) {
            $accountPrefix = $this->stripLeadingZeros($m[1]);
            $accountNo = $this->stripLeadingZeros($m[2]);
            $bankCode = $m[3];
        }

        if(empty($bankCode) || empty($accountNo)) {
            $value = '';
        } else {
            $value = $accountNo . '/' . $bankCode;
            if(!empty($accountPrefix)) {
                $value = $accountPrefix . '-' . $value;
            }
        }

        return $value;
    }

    /**
     * Trim spaces, tabs and newlines
     *
     * @param string $value
     * @return string
     */
    protected function stringTrim($value)
    {
        $filter = new StringTrim();
        return $filter->filter($value);
    }

    /**
     * Strip spaces
     *
     * @param $value
     * @return mixed
     */
    protected function stripSpaces($value)
    {
        return str_replace(' ', '', $value);
    }

    /**
     * Strip leading zeros
     *
     * @param $value
     * @return string
     */
    protected function stripLeadingZeros($value)
    {
        return ltrim($value, '0');
    }

}
