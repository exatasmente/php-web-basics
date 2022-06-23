<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use DateInterval;
use DateTime;

class PeriodService
{
    protected DateTime $start_date;
    protected DateTime $end_date;
    private $numberOfCycles = null;
    private $daysLeftToEndOfMonth = null;


    public function __construct($start_date, $end_date)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }


    public static function make($start_date, $end_date)
    {
        return new self($start_date, $end_date);
    }


    public function getDateDiff()
    {
        return $this->start_date->diff($this->end_date, true);
    }

    public function calculateFirstPaymentAmount(int $fullAmount)
    {
        $daysLeft = $this->getDaysLeftToEndOfMonth($this->start_date);
        $numberOfDays = $this->getNumberOfDaysInMonth($this->start_date);

        if ($daysLeft === 0) {
            return $fullAmount;
        }


        return intval(round(($daysLeft / $numberOfDays) * $fullAmount));
    }

    public function calculateLastPaymentAmount(int $fullAmount)
    {
        $daysLeft = $this->getDaysLeftToEndOfMonth($this->end_date);
        $numberOfDays = $this->getNumberOfDaysInMonth($this->end_date);

        if ($daysLeft === 0) {
            return $fullAmount;
        }


        return intval(round(($daysLeft / $numberOfDays) * $fullAmount));
    }


    public function getNumberOfCycles()
    {
        if ($this->numberOfCycles) {
            return $this->numberOfCycles;
        }

        $startYear = (int) $this->start_date->format('Y');
        $startMonth = (int) $this->start_date->format('m');
        $endYear = (int) $this->end_date->format('Y');
        $endMonth = (int) $this->end_date->format('m');


        $this->numberOfCycles =  (($endYear - $startYear) * 12) + ($endMonth - $startMonth);

        return $this->numberOfCycles;
    }

    public function getStartDateForCycle(int $cycle)
    {
        if ($cycle <= 0 || $cycle > $this->getNumberOfCycles()) {
            throw new BusinessException('invalid cycle for period', 500);
        }

        $date = clone $this->start_date;

        if ($cycle === 1) {
            return $date;
        }

        $date = $date->add(new \DateInterval("P{$cycle}M"));
        $numberOfDaysLeft = $this->getDaysLeftToEndOfMonth($date);
        $numberOfDays = $this->getNumberOfDaysInMonth($date);

        if ($numberOfDaysLeft < $numberOfDays) {
            $currentDay = (int) $date->format('d');
            $date = $date->sub(new \DateInterval("P{$currentDay}D"));
        }

        return $date->add(new \DateInterval("P{$numberOfDaysLeft}D"));
    }

    public function getEndDateForCycle(int $cycle)
    {
        if ($cycle <= 0 || $cycle > $this->getNumberOfCycles()) {
            throw new BusinessException('invalid cycle for period', 500);
        }

        $date = $this->getStartDateForCycle($cycle);
        $numberOfDaysLeft = $this->getDaysLeftToEndOfMonth($date);
        $numberOfDays = $this->getNumberOfDaysInMonth($date);

        if ($numberOfDaysLeft < $numberOfDays) {
            $currentDay = (int) $date->format('d');
            $date = $date->sub(new \DateInterval("P{$currentDay}D"));
        }


        return $date->add(new \DateInterval("P{$numberOfDays}D"));
    }

    public function getDaysLeftToEndOfMonth($date)
    {
        $startDay = (int) $date->format('d');
        $numberOfDays = $this->getNumberOfDaysInMonth($date);

        return $numberOfDays - $startDay;

    }

    public function getNumberOfDaysInMonth($date)
    {
        return (int) $date->format('t');
    }

}