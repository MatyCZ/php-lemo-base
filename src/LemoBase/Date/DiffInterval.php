<?php

namespace LemoBase\Date;

class DiffInterval
{
    /**
     * @var int
     */
    public $days = 0;

    /**
     * @var int
     */
    public $months = 0;

    /**
     * @var int
     */
    public $years = 0;

    /**
     * @return int
     */
    public function getDays()
    {
        return $this->days;
    }

    /**
     * @return int
     */
    public function getMonths()
    {
        return $this->months;
    }

    /**
     * @return int
     */
    public function getYears()
    {
        return $this->years;
    }
}