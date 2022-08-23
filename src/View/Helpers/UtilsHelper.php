<?php

declare(strict_types=1);

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
}