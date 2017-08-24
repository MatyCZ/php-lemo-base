<?php

namespace LemoBase\Date;

use DateTime;
use LemoBase\Date\Exception;

class Diff
{
    /**
     * @var DateTime
     */
    protected $dateStart;

    /**
     * @var DateTime
     */
    protected $dateEnd;

    /**
     * @var bool
     */
    protected $includeEndDay = false;

    /**
     * @var bool
     */
    protected $includeEveryStarted = false;

    /**
     * @var DiffInterval
     */
    protected $interval;

    /**
     * Diff constructor.
     *
     * @param      $dateStart
     * @param null $dateEnd
     * @param bool $includeEndDay
     * @param bool $includeEveryStarted
     */
    public function __construct($dateStart, $dateEnd = null, $includeEndDay = false, $includeEveryStarted = false)
    {
        if (!$dateStart instanceof DateTime) {
            $dateStart = new DateTime($dateStart);
        }
        if (!$dateEnd instanceof DateTime) {
            $dateEnd = new DateTime($dateEnd);
        }

        $this->dateStart           = clone $dateStart;
        $this->dateEnd             = clone $dateEnd;
        $this->includeEndDay       = $includeEndDay;
        $this->includeEveryStarted = $includeEveryStarted;

        $this->calculate();
    }

    /**
     * Calculate day difference
     *
     * @return $this
     */
    private function calculate()
    {
        if ($this->dateStart->format('YmdHis') > $this->dateEnd->format('YmdHis')) {
            throw new Exception\RuntimeException('Start date is greater than end date');
        }

        // Pokud jsou oba datumy shodne, vratime jeden den
        if ($this->dateStart == $this->dateEnd) {
            $interval = new DiffInterval();
            $interval->days++;

            $this->interval = $interval;

            return $this;
        }

        // Pridame jeden den navic
        if (true === $this->includeEndDay) {
            $this->dateEnd->modify('+1 day');
        }

        // Calculate date diff
        $diff = $this->dateEnd->diff($this->dateStart);

        $interval = new DiffInterval();
        $interval->days = $diff->days;
        $interval->months = ($diff->y * 12) + $diff->m;
        $interval->years = $diff->y;

        if (true === $this->includeEveryStarted) {
            if ($this->dateStart->format('m') >= $this->dateEnd->format('m') && $this->dateStart->format('d') > $this->dateEnd->format('d')) {
                $interval->months++;
            }
            if ($this->dateStart->format('m') > $this->dateEnd->format('m')) {
                $interval->years++;
            }
        }

        $this->interval = $interval;

        return $this;
    }

    /**
     * @return int
     */
    public function getDays()
    {
        return $this->interval->getDays();
    }

    /**
     * @return int
     */
    public function getMonths()
    {
        return $this->interval->getMonths();
    }

    /**
     * @return int
     */
    public function getYears()
    {
        return $this->interval->getYears();
    }
}