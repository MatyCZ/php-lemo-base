<?php

namespace LemoBase\Validator;

use Exception;
use Laminas\Validator\AbstractValidator;

use function array_key_exists;
use function is_int;
use function is_string;
use function preg_match;
use function preg_quote;
use function sprintf;
use function str_pad;

class IdentificationNumber extends AbstractValidator
{
    public const INVALID = 'intInvalid';
    public const NOT_IDENTIFICATIONNUMBER = 'notIdentificationNumber';

    protected array $messageTemplates = [
        self::INVALID => "Invalid type given. String or integer expected",
        self::NOT_IDENTIFICATIONNUMBER => "The value does not appear to be an identification number",
    ];

    /**
     * Excluded regular expression patterns without delimiter
     *
     * ['0000$', '9999$']
     *
     * @var array
     */
    protected array $exclude = [];

    /**
     * BirthNumber constructor.
     *
     * @param null|array $options
     * @throws Exception
     */
    public function __construct(array $options = [])
    {
        if (array_key_exists('exclude', $options)) {
            $this->setExclude($options['exclude']);
        }

        parent::__construct($options);
    }

    /**
     * Returns true if and only if $value is a valid integer
     *
     * @param  string|int $value
     * @return bool
     */
    public function isValid($value): bool
    {
        if (!is_string($value) && !is_int($value)) {
            $this->error(self::INVALID);
            return false;
        }

        $this->setValue($value);
        $value = str_pad($value, 8, '0', STR_PAD_LEFT);

        if (!preg_match('~^\d{8}$~', preg_quote($value, '~'))) {
            $this->error(self::NOT_IDENTIFICATIONNUMBER);
            return false;
        }

        // Regularni vyrazy pro hodnoty, ktere se povazuji za validni
        foreach ($this->exclude as $pattern) {
            if (1 === preg_match('~' . $pattern . '~', preg_quote($value, '~'))) {
                return true;
            }
        }

        // kontrolní součet
        $a = 0;
        for ($i = 0; $i < 7; $i++) {
            $a += $value[$i] * (8 - $i);
        }

        $a = $a % 11;

        if ($a === 0) {
            $c = 1;
        } elseif ($a === 10) {
            $c = 1;
        } elseif ($a === 1) {
            $c = 0;
        } else {
            $c = 11 - $a;
        }

        if ((int) $value[7] !== $c) {
            $this->error(self::NOT_IDENTIFICATIONNUMBER);
            return false;
        }

        return true;
    }

    /**
     * Set excluded patterns
     *
     * @param array $exclude
     * @return $this
     * @throws Exception
     */
    public function setExclude(array $exclude): self
    {
        foreach ($exclude as $pattern) {
            $this->testPregPattern($pattern);
            $this->exclude[] = $pattern;
        }

        return $this;
    }

    /**
     * Test regular expression pattern
     *
     * @param  string $pattern
     * @throws Exception
     */
    protected function testPregPattern(string $pattern): void
    {
        if (false === @preg_match('~' . $pattern . '~', "")) {
            throw new Exception(
                sprintf(
                    'Invalid regular expression pattern `%s` in options',
                    $pattern
                )
            );
        }
    }
}
