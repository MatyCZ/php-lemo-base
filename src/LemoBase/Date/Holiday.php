<?php

namespace LemoBase\Date;

use DateInterval;
use DateTime;
use Laminas\Stdlib\ArrayUtils;
use Locale;
use Traversable;

class Holiday
{
    const EASTERFRIDAY = 'easterFriday';
    const EASTERMONDAY = 'easterMonday';
    const EASTERSUNDAY = 'easterSunday';

    /**
     * ISO 3611 Country Code
     *
     * @var string
     */
    protected $country;

    /**
     * Day patterns
     *
     * @var array
     */
    protected static $days = [];

    /**
     * Constructor
     *
     * Options
     * - country | string | field or value
     *
     * @param array|Traversable $options
     */
    public function __construct($options = [])
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        if (array_key_exists('country', $options)) {
            $this->setCountry($options['country']);
        } else {
            $country = Locale::getRegion(Locale::getDefault());
            $this->setCountry($country);
        }
    }

    /**
     * @param  int $year
     * @return array
     */
    private function createList($year)
    {
        $country = strtoupper($this->getCountry());

        // Load pattern
        if (!$daysPattern = $this->loadPattern($country)) {
            throw new Exception\InvalidArgumentException(sprintf(
                "Pattern file for country '%s' was not found",
                $country
            ));
        }

        // Pattern
        if (empty($daysPattern['dynamic']) || empty($daysPattern['static'])) {
            throw new Exception\ParseException(sprintf(
                "Pattern file for country '%s' has bad format",
                $country
            ));
        }

        $year = $year ?: date('Y');

        $holidays = [];

        // Static holidays
        foreach ($daysPattern['static'] as $date => $name) {
            $date = $year . '-' . $date;

            $holidays[$date] = $name;
        }

        // Dynamic holidays
        $dynamicDates = $this->createDynamicDates($year);
        foreach ($daysPattern['dynamic'] as $pattern => $name) {
            $date = $dynamicDates[$pattern];

            $holidays[$date] = $name;
        }

        ksort($holidays);

        return $holidays;
    }

    /**
     * @param  int $year
     * @return array
     */
    protected function createDynamicDates($year)
    {
        // Easter - Monday
        $easterSunday = new DateTime(date('Y-m-d', easter_date($year)));

        // Easter - Sunday
        $easterFriday = clone $easterSunday;
        $easterFriday = $easterFriday->sub(new DateInterval('P2D'));

        // Easter - Monday
        $easterMonday = clone $easterSunday;
        $easterMonday = $easterMonday->add(new DateInterval('P1D'));

        // List of dynamic dates
        $days = [];
        $days[self::EASTERFRIDAY] = $easterFriday->format('Y-m-d');
        $days[self::EASTERSUNDAY] = $easterSunday->format('Y-m-d');
        $days[self::EASTERMONDAY] = $easterMonday->format('Y-m-d');

        return $days;
    }

    /**
     * @return array
     */
    public function getList()
    {
        return $this->createList(date('Y'));
    }

    /**
     * @param  int $year
     * @return array
     */
    public function getListForYear($year)
    {
        return $this->createList($year);
    }

    /**
     * Get Country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set Country
     *
     * @param  string $country
     * @return $this
     */
    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }

    /**
     * Load Pattern
     *
     * @param  string        $code
     * @return array[]|false
     */
    protected function loadPattern($code)
    {
        if (!isset(static::$days[$code])) {
            if (!preg_match('/^[A-Z]{2}$/D', $code)) {
                return false;
            }

            $file = __DIR__ . '/Holiday/Pattern/' . $code . '.php';
            if (!file_exists($file)) {
                return false;
            }

            static::$days[$code] = include $file;
        }

        return static::$days[$code];
    }
}