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
 * Usefull method to process data in views
 */
class UtilsHelper
{
    /**
     * Troncates a string 
     * @param  string $string String to troncate
     * @param  int    $length Max length
     * @return string         Troncated string
     */
    public static function troncate(string $string, int $length): string
    {
        if (strlen($string) >= $length) {
            $string = substr($string, 0, $length);
            $espace = strrpos($string, " ");

            if ($espace) {
                $string = substr($string, 0, $espace);
                $string .= '...';
            }
        }

        return $string;
    }

    /**
     * Get money value of number
     *
     * @param int|float $amount Amount to be returned
     * @param int $decimals Number of decimails
     * @return string Money value
     */
    public static function currency($amount, $decimals = 2)
    {
        return number_format($amount, $decimals, '.', ' ');
    }
}
