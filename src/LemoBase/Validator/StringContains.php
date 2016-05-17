<?php

namespace LemoBase\Validator;

use Zend\Validator\AbstractValidator;

class StringContains extends AbstractValidator
{
    const NO_ALPHA          = 'noAlpha';
    const NO_CAPITAL_LETTER = 'noCapitalLetter';
    const NO_NUMERIC        = 'noNumeric';
    const NO_SMALL_LETTER   = 'noSmallLetter';

    /**
     * @var bool
     */
    protected $_requireAlpha         = true;

    /**
     * @var bool
     */
    protected $_requireCapitalLetter = false;

    /**
     * @var bool
     */
    protected $_requireNumeric       = true;

    /**
     * @var bool
     */
    protected $_requireSmallLetter   = false;

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::NO_ALPHA          => 'Value must contain at least one alphabetic character',
        self::NO_CAPITAL_LETTER => 'Value must contain at least one capital letter',
        self::NO_NUMERIC        => 'Value must contain at least one numeric character',
        self::NO_SMALL_LETTER   => 'Value must contain at least one small letter',
    );

    public function __construct(array $options = null)
    {
        if (null !== $options) {
            if (isset($options['requireAlpha'])) {
                $this->_requireAlpha = (bool) $options['requireAlpha'];
            }

            if (isset($options['requireNumeric'])) {
                $this->_requireNumeric = (bool) $options['requireNumeric'];
            }

            if (isset($options['requireCapitalLetter'])) {
                $this->_requireCapitalLetter = (bool) $options['requireCapitalLetter'];
            }

            if (isset($options['requireSmallLetter'])) {
                $this->_requireNumeric = (bool) $options['requireSmallLetter'];
            }
        }

        parent::__construct($options);
    }

    /**
     * Validate a password with the set requirements
     *
     * @param mixed $value
     * @param null  $context
     * @return bool
     */
    public function isValid($value, $context = null)
    {
        $value = (string) $value;

        // Alphanumeric
        if (true === $this->_requireAlpha && 0 == preg_match('/[a-z]/i', $value)) {
            $this->error(self::NO_ALPHA);
            return false;
        }

        // Capital letter
        if (true === $this->_requireCapitalLetter && 0 == preg_match('/[A-Z]/', $value)) {
            $this->error(self::NO_CAPITAL_LETTER);
            return false;
        }

        // Numeric
        if (true === $this->_requireNumeric && 0 == preg_match('/\d/', $value)) {
            $this->error(self::NO_NUMERIC);
            return false;
        }

        // Small letter
        if (true === $this->_requireSmallLetter && 0 == preg_match('/[a-z]/', $value)) {
            $this->error(self::NO_SMALL_LETTER);
            return false;
        }

        return true;
    }
}
