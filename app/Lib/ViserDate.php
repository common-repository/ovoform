<?php

namespace Ovoform\Lib;

class ViserDate {

    private $currentTime;
    private $now;
    private $datetime;


    public function __construct()
    {
        $this->currentTime = current_time('timestamp');
        $this->datetime = $this->currentTime;
    }

    public function parse(string $time)
    {
        $datetime = strtotime($time); 
        $this->datetime = $datetime;
        return $this;
    }

    public function addSeconds($int)
    {
        $this->datetime += $int;
        return $this;
    }

    public function addMinutes($int)
    {
        $minutes = $int * MINUTE_IN_SECONDS;
        $this->datetime += $minutes;
        return $this;
    }

    public function addDays($int)
    {
        $days = $int * DAY_IN_SECONDS;
        $this->datetime += $days;
        return $this;
    }

    public function addWeeks($int)
    {
        $weeks = $int * WEEK_IN_SECONDS;
        $this->datetime += $weeks;
        return $this;
    }

    public function addMonths($int)
    {
        $months = $int * MONTH_IN_SECONDS;
        $this->datetime += $months;
        return $this;
    }

    public function addYears($int)
    {
        $years = $int * YEAR_IN_SECONDS;
        $this->datetime += $years;
        return $this;
    }

    public function subSeconds($int)
    {
        $this->datetime -= $int;
        return $this;
    }

    public function subMinutes($int)
    {
        $minutes = $int * MINUTE_IN_SECONDS;
        $this->datetime -= $minutes;
        return $this;
    }

    public function subDays($int)
    {
        $days = $int * DAY_IN_SECONDS;
        $this->datetime -= $days;
        return $this;
    }

    public function subWeeks($int)
    {
        $weeks = $int * WEEK_IN_SECONDS;
        $this->datetime -= $weeks;
        return $this;
    }

    public function subMonths($int)
    {
        $months = $int * MONTH_IN_SECONDS;
        $this->datetime -= $months;
        return $this;
    }

    public function subYears($int)
    {
        $years = $int * YEAR_IN_SECONDS;
        $this->datetime -= $years;
        return $this;
    }

    public function now($format = 'Y-m-d H:i:s')
    {
        $this->now = date($format,$this->currentTime); 
        return $this->now;
    }

    public function toDateTime($format = 'Y-m-d H:i:s')
    {
        return date($format,$this->datetime); 
    }

    public function toDate($format = 'Y-m-d')
    {
        return date($format,$this->datetime); 
    }

    public function toTime($format = 'H:i:s')
    {
        return date($format,$this->datetime); 
    }

    public function toTimeStamp()
    {
        return $this->datetime; 
    }
}