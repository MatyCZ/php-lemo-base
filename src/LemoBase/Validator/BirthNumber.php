<?php

namespace LemoBase\Validator;

use Exception;
use Laminas\Validator\AbstractValidator;

class BirthNumber extends AbstractValidator
{
    const INVALID = 'intInvalid';
    const NOT_BIRTHNUMBER = 'notBirthNumber';

    /**
     * @var array
     */
    protected $messageTemplates = array(
        self::INVALID => "Invalid type given. String or integer expected",
        self::NOT_BIRTHNUMBER => "The value does not appear to be a birth number",
    );

    /**
     * Excluded regular expression patterns without delimiter
     *
     * ['0000$', '9999$']
     *
     * @var array
     */
    protected $exclude = [];

    /**
     * BirthNumber constructor.
     *
     * @param null|array $options
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
    public function isValid($value)
    {
        if (!is_string($value) && !is_int($value)) {
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
        if(!preg_match('~^\s*(\d\d)(\d\d)(\d\d)(\d\d\d)(\d?)\s*$~', preg_quote($value, '~'), $matches)) {
            $this->error(self::NOT_BIRTHNUMBER);
            return false;
        }

        list(, $year, $month, $day, $ext, $c) = $matches;

        // Do roku 1954 pridelovano 9 mistne RC nelze overit
        if ($c === '') {
            return true;
        }

        // Kontrolni cislice
        $mod = ($year . $month . $day . $ext) % 11;
        if ($mod === 10) $mod = 0;
        if ($mod !== (int) $c) {
            $this->error(self::NOT_BIRTHNUMBER);
            return false;
        }

        // Kontrola data
        $year += $year < 54 ? 2000 : 1900;

        // K mesici muze byt pricteno 20, 50 nebo 70
        if ($month > 70 && $year > 2003) $month -= 70;
        elseif ($month > 50) $month -= 50;
        elseif ($month > 20 && $year > 2003) $month -= 20;

        if (!checkdate($month, $day, $year)) {
            $this->error(self::NOT_BIRTHNUMBER);
            return false;
        }

        return true;
    }

    /**
     * Set excluded patterns
     *
     * @param  array $exclude
     * @return $this
     */
    public function setExclude(array $exclude)
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
    protected function testPregPattern($pattern)
    {
        if (false === @preg_match('~' . $pattern . '~', null)) {
            throw new Exception(sprintf('Invalid regular expression pattern `%s` in options', $pattern));
        }
    }
}
