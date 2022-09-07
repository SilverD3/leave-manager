<?php

declare(strict_types=1);

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
        $date_diff = $start_date - $end_date + 60*60*24;

        $years = floor($date_diff / (365*60*60*24));
        $months = floor(($date_diff - $years * 365*60*60*24) / (30*60*60*24));
        $days = floor(($date_diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

        $string_diff = '';
        
        if ($years > 0) {
            $string_diff .= $years . ( $years == 1 ? ' an' : ' ans');
        }

        if ($months > 0) {
            $string_diff .= $months . ' mois';
        }

        if (empty($string_diff)) {
            $string_diff = $days . ( $days == 1 ? ' jour' : ' jours');
        } else {
            $string_diff .= $days . ( $days == 1 ? ' jour' : ' jours');
        }

        return $string_diff;
    }

    /**
     * Check if date is a timestamp
     *
     * @param mixed $ts Date to check
     * @return boolean
     */
    private static function isTimestamp($ts)
    {
        return is_numeric($ts) && ((int) $ts == $ts);
    }

    /**
     * Convert date into timestamp
     *
     * @param [type] $date
     * @return integer|null
     */
    private static function toTimestamp($date): ?int
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