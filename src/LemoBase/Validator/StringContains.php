<?php

namespace LemoBase\Validator;

use Laminas\Validator\AbstractValidator;

class StringContains extends AbstractValidator
{
    const NO_ALPHA            = 'noAlpha';
    const NO_VALID_CHARACTERS = 'noValidCharacters';
    const NO_CAPITAL_LETTER   = 'noCapitalLetter';
    const NO_NUMERIC          = 'noNumeric';
    const NO_SMALL_LETTER     = 'noSmallLetter';

    /**
     * @var string|int|null
     * string - valid characters
     * int - 1 .. 128 ASCII characters
     */
    protected $cahracters = null;

    /**
     * @var bool
     */
    protected $requireAlpha = false;

    /**
     * @var bool
     */
    protected $requireCapitalLetter = false;

    /**
     * @var bool
     */
    protected $requireNumeric = false;

    /**
     * @var bool
     */
    protected $requireSmallLetter = false;

    /**
     * @var array
     */
    protected $messageTemplates = [
        self::NO_ALPHA            => 'Value must contain at least one alphabetic character',
        self::NO_VALID_CHARACTERS => 'The input contains an invalid characters',
        self::NO_CAPITAL_LETTER   => 'Value must contain at least one capital letter',
        self::NO_NUMERIC          => 'Value must contain at least one numeric character',
        self::NO_SMALL_LETTER     => 'Value must contain at least one small letter',
    ];

    public function __construct(array $options = null)
    {
        if (null !== $options) {

            if (!empty($options['characters'])) {
                if (
                    is_int($options['characters'])
                    && $options['characters'] > 0
                    && $options['characters'] <= 128
                    ||
                    is_string($options['characters'])
                ) {
                    $this->cahracters = $options['characters'];
                }
            }

            if (isset($options['requireAlpha'])) {
                $this->requireAlpha = (bool) $options['requireAlpha'];
            }

            if (isset($options['requireNumeric'])) {
                $this->requireNumeric = (bool) $options['requireNumeric'];
            }

            if (isset($options['requireCapitalLetter'])) {
                $this->requireCapitalLetter = (bool) $options['requireCapitalLetter'];
            }

            if (isset($options['requireSmallLetter'])) {
                $this->requireSmallLetter = (bool) $options['requireSmallLetter'];
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
        if (true === $this->requireAlpha && 0 == preg_match('/[a-z]/i', $value)) {
            $this->error(self::NO_ALPHA);
            return false;
        }

        // ASCII characters or allowed cahracters
        if (null !== $this->cahracters) {
            if (is_int($this->cahracters)) {
                for ($x = 0; $x < $this->cahracters; ++$x) {
                    $value = str_replace(chr($x), '', $value);
                }
            } else {
                $chars = str_split($this->cahracters);
                foreach ($chars as $char) {
                    $value = str_replace($char, '', $value);
                }
            }

            if (strlen($value) > 0) {
                $this->error(self::NO_VALID_CHARACTERS);
                return false;
            }
        }

        // Capital letter
        if (true === $this->requireCapitalLetter && 0 == preg_match('/[A-Z]/', $value)) {
            $this->error(self::NO_CAPITAL_LETTER);
            return false;
        }

        // Numeric
        if (true === $this->requireNumeric && 0 == preg_match('/\d/', $value)) {
            $this->error(self::NO_NUMERIC);
            return false;
        }

        // Small letter
        if (true === $this->requireSmallLetter && 0 == preg_match('/[a-z]/', $value)) {
            $this->error(self::NO_SMALL_LETTER);
            return false;
        }

        return true;
    }
}
