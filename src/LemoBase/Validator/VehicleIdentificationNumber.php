<?php

namespace LemoBase\Validator;

use Zend\Validator\AbstractValidator;

class VehicleIdentificationNumber extends AbstractValidator
{
    const INVALID        = 'vinInvalid';
    const INVALID_CHARS  = 'vinInvalidChars';
    const INVALID_LENGTH = 'vinInvalidLength';
    const NOT_VIN        = 'notVin';

    /**
     * @var array
     */
    protected $messageTemplates = array(
        self::INVALID        => "Invalid type given. String expected",
        self::INVALID_CHARS  => "The value contains invalid charactes",
        self::INVALID_LENGTH => "Invalid value length",
        self::NOT_VIN        => "The value does not appear to be a vehicle identification number",
    );

    /**
     * true = strict validation to 17 chars
     *
     * @var array
     */
    protected $strict = false;

    /**
     * BirthNumber constructor.
     *
     * @param null|array $options
     */
    public function __construct(array $options = [])
    {
        if (array_key_exists('strict', $options)) {
            $this->setStrict(boolval($options['strict']));
        }

        parent::__construct($options);
    }

    /**
     * Returns true if and only if $value is a valid integer
     *
     * @param  string|integer $value
     * @return boolean
     */
    public function isValid($value)
    {
        if (!is_string($value)) {
            $this->error(self::INVALID . ' ' . $value);
            return false;
        }

        if (!preg_match('~^([0-9A-Z]{8}|[0-9A-Z]{17})$~', $value)) {
            $this->error(self::INVALID_LENGTH);
            return false;
        }

        if (true === $this->strict && 17 !== mb_strlen($value, 'utf8')) {
            $this->error(self::INVALID_LENGTH);
            return false;
        }

        if (preg_match('~(I|Q|O)~', $value)) {
            $this->error(self::INVALID_CHARS);
            return false;
        }

        $this->setValue($value);

        return true;
    }

    /**
     * true .. accepts string lengt 17 characters only.
     *
     * @param  bool|int $strict
     * @return $this
     */
    public function setStrict($strict)
    {
        $this->strict = boolval($strict);

        return $this;
    }
}