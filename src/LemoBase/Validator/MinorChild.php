<?php

namespace LemoBase\Validator;

use Laminas\Validator\AbstractValidator;

use function checkdate;
use function is_int;
use function is_string;
use function preg_match;
use function strtotime;

class MinorChild extends AbstractValidator
{
    public const INVALID         = 'intInvalid';
    public const NOT_BIRTHNUMBER = 'notBirthNumber';
    public const NOT_MINORCHILD  = 'notMinorChild';

    protected array $messageTemplates = [
        self::INVALID         => "Invalid type given. String or integer expected",
        self::NOT_BIRTHNUMBER => "The value does not appear to be a birth number",
        self::NOT_MINORCHILD  => "The value does not appear to be a minor child",
    ];

    protected int $limit = 18;

    public function __construct(array $options = [])
    {
        if (array_key_exists('limit', $options)) {
            $this->setLimit($options['limit']);
        }

        parent::__construct($options);
    }

    /**
     * @param  int $limit
     * @return self
     */
    public function setLimit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
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
        if (!preg_match('#^\s*(\d\d)(\d\d)(\d\d)(\d\d\d)(\d?)\s*$#', $value, $matches)) {
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

        if (strtotime($year . '-' . $month . '-' . $day) < strtotime('-' . $this->getLimit() . ' YEARS')) {
            $this->error(self::NOT_MINORCHILD);
            return false;
        }

        return true;
    }
}
