<?php

namespace LemoBase\Validator;

use Zend\Validator\AbstractValidator;

class IdentificationNumber extends AbstractValidator
{
    const INVALID = 'intInvalid';
    const NOT_IDENTIFICATIONNUMBER = 'notIdentificationNumber';

    /**
     * @var array
     */
    protected $messageTemplates = array(
        self::INVALID => "Invalid type given. String or integer expected",
        self::NOT_IDENTIFICATIONNUMBER => "The value does not appear to be an identification number",
    );

    /**
     * Returns true if and only if $value is a valid integer
     *
     * @param  string|integer $value
     * @return boolean
     */
    public function isValid($value)
    {
        if (!is_string($value) && !is_int($value)) {
            $this->error(self::INVALID);
            return false;
        }

        $this->setValue($value);
        if (!preg_match('#^\d{8}$#', $value)) {
            $this->error(self::NOT_IDENTIFICATIONNUMBER);
            return false;
        }

        // kontrolní součet
        $a = 0;
        for ($i = 0; $i < 7; $i++) {
            $a += $value[$i] * (8 - $i);
        }

        $a = $a % 11;

        if ($a === 0) $c = 1;
        elseif ($a === 10) $c = 1;
        elseif ($a === 1) $c = 0;
        else $c = 11 - $a;

        if((int) $value[7] !== $c) {
            $this->error(self::NOT_IDENTIFICATIONNUMBER);
            return false;
        }

        return true;
    }
}
