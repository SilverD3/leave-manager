<?php

declare(strict_types=1);

/**
 * Leave manager : Simple app for contract and leave management.
 *
 * @copyright Copyright (c) Silevester D. (https://github.com/SilverD3)
 * @link      https://github.com/SilverD3/leave-manager Leave Manager Project
 * @since     1.0 (2022)
 */

namespace App\View\Helpers;

/**
 * Date Helper
 */
class DateHelper
{
    /**
     * Format date to short format
     * @param  mixed $date Date to format
     * @return string      Formated date
     */
    public static function shortDate($date): ?string
    {
        if (empty($date)) {
            return null;
        }
        if (self::isTimestamp($date)) {
            return date('d/m/Y', $date);
        }

        if ($date instanceof \DateTime) {
            $date = $date->format('Y-m-d H:i:s');
        }

        $date = strtotime($date);

        return date('d/m/Y', $date);
    }

    /**
     * Format date to short format
     * @param  mixed $date Date to format
     * @return string      Formated date
     */
    public static function dateTime($date): ?string
    {
        if (empty($date)) {
            return null;
        }

        if (self::isTimestamp($date)) {
            return date('d/m/Y H:i', $date);
        }

        if ($date instanceof \DateTime) {
            $date = $date->format('Y-m-d H:i:s');
        }

        $date = strtotime($date);

        return date('d/m/Y H:i', $date);
    }

    /**
     * Return the difference between two dates
     *
     * @param mixed $start_date Start date
     * @param mixed $end_date End date
     * @return string|null Return the difference or null.
     */
    public static function dateDiff($start_date, $end_date): ?string
    {
        $start_date = self::toTimestamp($start_date);
        $end_date = self::toTimestamp($end_date);

        if ($start_date > $end_date) {
            return '0 jour';
        }

        // Add one day to the diff
        $date_diff = $start_date - $end_date + 60 * 60 * 24;

        $years = floor($date_diff / (365 * 60 * 60 * 24));
        $months = floor(($date_diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
        $days = floor(($date_diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));

        $string_diff = '';

        if ($years > 0) {
            $string_diff .= $years . ($years == 1 ? ' an' : ' ans');
        }

        if ($months > 0) {
            $string_diff .= $months . ' mois';
        }

        if (empty($string_diff)) {
            $string_diff = $days . ($days == 1 ? ' jour' : ' jours');
        } else {
            $string_diff .= $days . ($days == 1 ? ' jour' : ' jours');
        }

        return $string_diff;
    }

    /**
     * Get working minutes between two dates
     *
     * @param mixed $startDate Start date
     * @param mixed $endDate End date
     * @param string $workBeginAt Work begin time
     * @param string $workEndAt Work end time
     * @param array  $holidays Holidays
     * @param array  $openedDays Working days
     * @param array  $dailyBreaks Daily breaks
     * @return int Number of minutes between the dates
     */
    public static function getWorkingMinutes($startDate, $endDate, $workBeginAt, $workEndAt, $holidays = [], $openedDays = [1, 2, 3, 4, 5], $dailyBreaks = [])
    {
        // Convert dates to timestamp
        $endDate = self::toTimestamp($endDate);
        $startDate = self::toTimestamp($startDate);

        // Invert dates if end date is lower than start date
        if ($startDate > $endDate) {
            $tmp = $endDate;
            $endDate = $startDate;
            $startDate = $tmp;
            unset($tmp);
        }

        // Get not opening days
        $nonWorkingDays = array_diff([1, 2, 3, 4, 5, 6, 7], $openedDays);

        // Anonymous function to compute number of minutes between two dates, taking into account non-working days
        $getNbMinutes = function ($start_date, $end_date) use ($workBeginAt, $workEndAt, $holidays, $nonWorkingDays) {
            $start_date = self::toTimestamp($start_date);
            $end_date = self::toTimestamp($end_date);

            // Check if the date is a holiday, return 0
            if (in_array(date('Y-m-d', $start_date), $holidays) || in_array(date('N', $start_date), $nonWorkingDays)) {
                return 0;
            }

            $start_hour = (int)date('H', $start_date);
            $start_minute = (int)date('i', $start_date);
            $end_hour = (int)date('H', $end_date);
            $end_minute = (int)date('i', $end_date);

            $work_start_hour = (int)(explode(':', $workBeginAt))[0];
            $work_start_minute = (int)(explode(':', $workBeginAt))[1];
            $work_end_hour = (int)(explode(':', $workEndAt))[0];
            $work_end_minute = (int)(explode(':', $workEndAt))[1];

            if ($start_hour > $work_end_hour || ($end_hour == $work_start_hour && $end_minute > $work_start_minute)) {
                return 0;
            }

            if ($end_hour == $work_end_hour) {
                if ($end_minute < $work_end_minute) {
                    $work_end_minute = $end_minute;
                }
            }

            if ($end_hour < $work_end_hour) {
                $work_end_hour = $end_hour;
                $work_end_minute = $end_minute;
            }

            if ($start_hour == $work_start_hour) {
                if ($start_minute < $work_start_minute) {
                    $work_start_minute = $start_minute;
                }
            }

            if ($start_hour > $work_start_hour) {
                $work_start_hour = $start_hour;
                $work_start_minute = $start_minute;
            }

            // Add leading 0
            $work_start_hour = $work_start_hour > 9 ? $work_start_hour : '0' . $work_start_hour;
            $work_start_minute = $work_start_minute > 9 ? $work_start_minute : '0' . $work_start_minute;
            $work_end_hour = $work_end_hour > 9 ? $work_end_hour : '0' . $work_end_hour;
            $work_end_minute = $work_end_minute > 9 ? $work_end_minute : '0' . $work_end_minute;

            $nb_minutes = (strtotime(date('Y-m-d') . ' ' . $work_end_hour . ':' . $work_end_minute) -
                strtotime(date('Y-m-d') . ' ' . $work_start_hour . ':' . $work_start_minute)
            ) / 60;

            return floor($nb_minutes);
        };

        $getBreakMinutes = function ($start_date, $end_date) use ($dailyBreaks, $getNbMinutes) {
            if (empty($dailyBreaks)) {
                return 0;
            }

            $start_date = self::toTimestamp($start_date);
            $end_date = self::toTimestamp($end_date);

            $nb_breaks_minutes = 0;

            // Get the number of minutes between the dates
            $start_hour = (int)date('H', $start_date);
            $start_minute = (int)date('i', $start_date);
            $end_hour = (int)date('H', $end_date);
            $end_minute = (int)date('i', $end_date);

            foreach ($dailyBreaks as $dailyBreak) {
                $breakParts = explode('-', $dailyBreak);
                $break_start_hour = (int)(explode(':', $breakParts[0]))[0];
                $break_start_minute = (int)(explode(':', $breakParts[0]))[1];
                $break_end_hour = (int)(explode(':', $breakParts[1]))[0];
                $break_end_minute = (int)(explode(':', $breakParts[1]))[1];

                if ($end_hour < $break_start_hour || ($end_hour == $break_start_hour && $end_minute < $break_start_minute)) {
                    continue;
                }

                if ($end_hour == $break_end_hour) {
                    if ($end_minute < $break_end_minute) {
                        $break_end_minute = $end_minute;
                    }
                }

                if ($end_hour < $break_end_hour) {
                    $break_end_hour = $end_hour;
                    $break_end_minute = $end_minute;
                }

                if ($start_hour == $break_start_hour) {
                    if ($start_minute < $break_start_minute) {
                        $break_start_minute = $start_minute;
                    }
                }

                if ($start_hour > $break_start_hour) {
                    $break_start_hour = $start_hour;
                    $break_start_minute = $start_minute;
                }

                // Add leading 0
                $break_start_hour = $break_start_hour > 9 ? $break_start_hour : '0' . $break_start_hour;
                $break_start_minute = $break_start_minute > 9 ? $break_start_minute : '0' . $break_start_minute;
                $break_end_hour = $break_end_hour > 9 ? $break_end_hour : '0' . $break_end_hour;
                $break_end_minute = $break_end_minute > 9 ? $break_end_minute : '0' . $break_end_minute;

                $break_nb_minutes = $getNbMinutes(
                    strtotime(date('Y-m-d', $start_date) . ' ' . $break_start_hour . ':' . $break_start_minute),
                    strtotime(date('Y-m-d', $end_date) . ' ' . $break_end_hour . ':' . $break_end_minute)
                );

                $nb_breaks_minutes += $break_nb_minutes;
            }

            return $nb_breaks_minutes;
        };

        // First check if the two dates are same day
        if (
            date('Y', $startDate) == date('Y', $endDate)
            && date('m', $startDate) == date('m', $endDate)
            && date('d', $startDate) == date('d', $endDate)
        ) {
            $nb_minutes = $getNbMinutes($startDate, $endDate) - $getBreakMinutes($startDate, $endDate);
        } else {
            $firstDayStartDate = date('Y-m-d', $startDate) . ' ' . $workBeginAt;
            $firstDayEndDate = date('Y-m-d', $startDate) . ' ' . $workEndAt;

            $nb_minutes = $getNbMinutes($firstDayStartDate, $firstDayEndDate) - $getBreakMinutes($firstDayStartDate, $firstDayEndDate);

            $secondDayStartDate = strtotime('+1 day', $startDate);
            if (
                date('Y', $secondDayStartDate) == date('Y', $endDate)
                && date('m', $secondDayStartDate) == date('m', $endDate)
                && date('d', $secondDayStartDate) == date('d', $endDate)
            ) {
                $secondDayStartDate = strtotime(date('Y-m-d', $secondDayStartDate) . ' ' . $workBeginAt);
                $nb_minutes += $getNbMinutes($secondDayStartDate, $endDate) - $getBreakMinutes($secondDayStartDate, $endDate);
            } else {
                $work_start_hour = date('H', strtotime(date('Y-m-d', $endDate) . ' ' . $workBeginAt));

                if ((int)date('H', $endDate) < (int)$work_start_hour) {
                    $lastDayStartDate = date('Y-m-d', $endDate) . ' ' . $workBeginAt;
                    $lastDayEndDate = date('Y-m-d', $endDate) . ' ' . $workEndAt;
                    $nb_minutes += $getNbMinutes($lastDayStartDate, $lastDayEndDate) - $getBreakMinutes($lastDayStartDate, $lastDayEndDate);
                    $prevDay = strtotime(date('Y-m-d', strtotime('-1 day', $endDate)));
                } else {
                    $prevDay = strtotime(date('Y-m-d', $endDate));
                }

                $nextDay = strtotime(date('Y-m-d', strtotime('+1 day', $startDate)));

                $nb_days = self::nbDaysBetween($nextDay, $prevDay);
                $nbMinutes = 0;
                for ($i = 0; $i < $nb_days; $i++) {
                    $dayStartDate = date('Y-m-d', strtotime("+$i day", $nextDay)) . ' ' . $workBeginAt;
                    $dayEndDate = date('Y-m-d', strtotime("+$i day", $nextDay)) . ' ' . $workEndAt;

                    $nbMinutes += $getNbMinutes($dayStartDate, $dayEndDate) - $getBreakMinutes($dayStartDate, $dayEndDate);
                }

                $nb_minutes += $nbMinutes;
            }
        }


        return $nb_minutes;
    }

    /**
     * Get working days between two dates, taking in account holidays
     *
     * @param mixed $startDate The start date
     * @param mixed $endDate The end date
     * @param string[] $holidays Array of holidays
     * @param int[] $openedDays Array of working days
     * @return int|null number of working days between the dates, or null if error occurs
     */
    public static function getWorkingDays($startDate, $endDate, $holidays = [], $openedDays = [1, 2, 3, 4, 5])
    {
        // do strtotime calculations just once
        $endDate = self::toTimestamp($endDate);
        $startDate = self::toTimestamp($startDate);

        $nonWorkingDays = array_diff([1, 2, 3, 4, 5, 6, 7], $openedDays);

        //The total number of days between the two dates. We compute the no. of seconds and divide it to 60*60*24
        //We add one to inlude both dates in the interval.
        $days = ($endDate - $startDate) / 86400 + 1;

        $no_full_weeks = floor($days / 7);
        $no_remaining_days = fmod($days, 7);

        //It will return 1 if it's Monday,.. ,7 for Sunday
        $the_first_day_of_week = date("N", $startDate);
        $the_last_day_of_week = date("N", $endDate);

        //---->The two can be equal in leap years when february has 29 days, the equal sign is added here
        //In the first case the whole interval is within a week, in the second case the interval falls in two weeks.
        if ($the_first_day_of_week <= $the_last_day_of_week) {
            foreach ($nonWorkingDays as $nonWorkingDay) {
                if ($the_first_day_of_week <= $nonWorkingDay && $nonWorkingDay <= $the_last_day_of_week) $no_remaining_days--;
            }
        } else {
            $remaining_days = [];

            // Memorize the first day of the week in a variable $j;
            $j = $the_first_day_of_week;

            // Loop through the number of remaining days to determine the days of the week  
            for ($i = 0; $i < $no_remaining_days; $i++) {

                if ($j >= 7) {
                    // $j is Sunday, we add just add it and reinitialize $j to 0, .i.e. Monday
                    $remaining_days[] = $j;
                    $j = 1;
                } else {
                    // If $j is lower than 7, i.e. the day is before Sunday, we add one day before storing
                    $remaining_days[] = $j;
                    $j++;
                }
            }

            // Substract non working days form remaining days and count them to get the number of remaining business days
            $no_remaining_days = count(array_diff($remaining_days, $nonWorkingDays));
        }

        //The no. of business days is: (number of weeks between the two dates) * (5 working days) + the remainder
        //---->february in none leap years gave a remainder of 0 but still calculated weekends between first and last day, this is one way to fix it
        $workingDays = $no_full_weeks * count($openedDays);
        if ($no_remaining_days > 0) {
            $workingDays += $no_remaining_days;
        }

        //We subtract the holidays
        foreach ($holidays as $holiday) {
            $time_stamp = strtotime($holiday);
            //If the holiday doesn't fall in weekend
            if ($startDate <= $time_stamp && $time_stamp <= $endDate && !in_array(date("N", $time_stamp), $nonWorkingDays))
                $workingDays--;
        }

        return $workingDays;
    }

    /**
     * Returns days number in the week for passed days names
     *
     * @param array $daysNames Days names
     * @return array
     */
    public static function daysNumbers(array $daysNames)
    {
        $map = ['lundi' => 1, 'mardi' => 2, 'mercredi' => 3, 'jeudi' => 4, 'vendredi' => 5, 'samedi' => 6, 'dimanche' => 7];

        $func = function (string $name) use ($map) {
            $val = isset($map[$name]) ? $map[$name] : $name;
            return $val;
        };

        return array_map($func, $daysNames);
    }

    /**
     * Get the month name
     *
     * @param string $monthNum Month number
     * @return string|null Return the month name or null if error occurs
     */
    public static function monthName(string $monthNum): ?string
    {
        $dateObj   = \DateTime::createFromFormat('!m', $monthNum);
        if ($dateObj instanceof \DateTime) {
            return $dateObj->format('F');
        }

        return null;
    }

    /**
     * Get first day of the month
     *
     * @param string $monthName Month name
     * @return string|bool
     */
    public static function firstDayOfMonth(string $monthName): string|bool
    {
        return date('Y-m-d', strtotime('first day of ' . $monthName));
    }

    /**
     * Get last day of the month
     *
     * @param string $monthName Month name
     * @return string|bool
     */
    public static function lastDayOfMonth(string $monthName): string|bool
    {
        return date('Y-m-d', strtotime('last day of ' . $monthName));
    }

    /**
     * Check if a time period is past, present or future
     *
     * @param mixed   $dateFrom Start date. Can be timestamp, date string or date object
     * @param mixed   $dateTo End date. Can be timestamp, date string or date object
     * @return string Returns the period status
     */
    public static function periodStatus($dateFrom, $dateTo): string
    {
        $dateFrom = self::toTimestamp($dateFrom);
        $dateTo = self::toTimestamp($dateTo);

        // If start date is greater than current date, it means that the period is future
        if ($dateFrom > time()) {
            return 'future';
        }

        // If end date is lower than current date, it means that the period is past
        if ($dateTo < time()) {
            return 'past';
        }

        if ($dateFrom <= time() && time() <= $dateTo) {
            return 'present';
        }

        return 'unknown';
    }

    /**
     * Get number of days between two dates
     *
     * @param mixed $date1 First date
     * @param mixed $date2 Second date
     * @return int Number of days between dates.
     */
    public static function nbDaysBetween($date1, $date2): int
    {
        // do strtotime calculations just once
        $date1 = self::toTimestamp($date1);
        $date2 = self::toTimestamp($date2);

        if ($date1 > $date2) {
            $days = ($date1 - $date2) / 86400;
        } else {
            $days = ($date2 - $date1) / 86400;
        }

        return (int)round($days, 0, PHP_ROUND_HALF_DOWN);
    }

    /**
     * Check if date is a timestamp
     *
     * @param mixed $ts Date to check
     * @return boolean
     */
    public static function isTimestamp($ts)
    {
        return is_numeric($ts) && ((int) $ts == $ts);
    }

    /**
     * Convert date into timestamp
     *
     * @param [type] $date
     * @return integer|null
     */
    public static function toTimestamp($date): ?int
    {
        if (self::isTimestamp($date)) {
            return $date;
        }

        if ($date instanceof \DateTime) {
            $date = $date->format('Y-m-d H:i:s');
        }

        if (is_string($date)) {
            $timestamp = strtotime($date);
            if ($timestamp) {
                return $timestamp;
            }

            return null;
        }

        return null;
    }
}
