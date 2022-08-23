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
        if (self::isTimestamp($date)) {
            return date('d/m/Y H:i', $date);
        }

        if ($date instanceof \DateTime) {
            $date = $date->format('Y-m-d H:i:s');
        }

        $date = strtotime($date);

        return date('d/m/Y H:i', $date);
    }

    private static function isTimestamp($ts)
    {
        return is_numeric($ts) && ((int) $ts == $ts);
    }
}