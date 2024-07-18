<?php

declare(strict_types=1);

/**
 * Leave manager : Simple app for contract and leave management.
 *
 * @copyright Copyright (c) Silevester D. (https://github.com/SilverD3)
 * @link      https://github.com/SilverD3/leave-manager Leave Manager Project
 * @since     2.0 (2024)
 */

namespace Core\Utils;

class DateUtils
{
    /**
     * Get the difference in days between two dates
     * 
     * @param string|int $date1 First date
     * @param string|int $date2 Second date
     * @param bool $strict Whether to return the exact difference 
     * or to 0 in case of negative value
     * @return int The difference between the provided date in days
     */
    public static function differenceInDays(string|int $date1, string|int $date2, bool $strict = true): int
    {
        $dateTime1 = new \DateTime($date1);
        $dateTime2 = new \DateTime($date2);

        if (!$dateTime1 instanceof \DateTime || !$dateTime2 instanceof \DateTime) {
            return 0;
        }

        $difference = $dateTime1->diff($dateTime2);

        if ($difference === false) {
            return 0;
        }

        return ($strict && $difference->days) < 0 ? 0 : $difference->days;
    }
}