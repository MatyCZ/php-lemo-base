<?php

namespace LemoBase\Validator;

use Exception;
use Laminas\Validator\AbstractValidator;

use function array_key_exists;
use function checkdate;
use function is_int;
use function is_string;
use function preg_match;
use function preg_quote;
use function sprintf;

class BirthNumber extends AbstractValidator
{
    public const INVALID = 'intInvalid';
    public const NOT_BIRTHNUMBER = 'notBirthNumber';

    protected array $messageTemplates = [
        self::INVALID => "Invalid type given. String or integer expected",
        self::NOT_BIRTHNUMBER => "The value does not appear to be a birth number",
    ];

    /**
     * Excluded regular expression patterns without delimiter
     *
     * ['0000$', '9999$']
     */
    protected array $exclude = [];

    /**
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
     * @param  string|integer $value
     * @return boolean
     */
    public function isValid($value): bool
    {
        if (!is_int($value) && !is_string($value)) {
            $this->error(self::INVALID);
            return false;
        }

        // Regularni vyrazy pro hodnoty, ktere se povazuji za validni
        foreach ($this->exclude as $pattern) {
            if (1 === preg_match('~' . $pattern . '~', preg_quote($value, '~'))) {
                return true;
            }
        }

        $this->setValue($value);
        if (!preg_match('~^\s*(\d\d)(\d\d)(\d\d)(\d\d\d)(\d?)\s*$~', preg_quote($value, '~'), $matches)) {
            $this->error(self::NOT_BIRTHNUMBER);
            return false;
        }

        [, $year, $month, $day, $ext, $c] = $matches;

        // Do roku 1954 pridelovano 9 mistne RC nelze overit
        if ($c === '') {
            return true;
        }

        // Kontrolni cislice
        $mod = ($year . $month . $day . $ext) % 11;
        if ($mod === 10) {
            $mod = 0;
        }
        if ($mod !== (int) $c) {
            $this->error(self::NOT_BIRTHNUMBER);
            return false;
        }

        // Kontrola data
        $year += $year < 54 ? 2000 : 1900;

        // K mesici muze byt pricteno 20, 50 nebo 70
        if ($month > 70 && $year > 2003) {
            $month -= 70;
        } elseif ($month > 50) {
            $month -= 50;
        } elseif ($month > 20 && $year > 2003) {
            $month -= 20;
        }

        if (!checkdate($month, $day, $year)) {
            $this->error(self::NOT_BIRTHNUMBER);
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
