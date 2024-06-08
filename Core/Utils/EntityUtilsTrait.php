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

trait EntityUtilsTrait
{
  /**
   * Check if a given date is valid
   * @param string $date The date to check
   * @param string|null $pastOrFuture Values are 'past', 'future' and null
   * 
   * @return bool True if the date is valid, false otherwsie
   */
  function isValidDate(string $date, ?string $pastOrFuture = null): bool
  {
    try {
      $dateTime = new \DateTime($date);
      if ($dateTime instanceof \DateTime) {
        if ($pastOrFuture === 'past') {
          return $dateTime->getTimestamp() - time() < 0;
        }

        if ($pastOrFuture === 'future') {
          return $dateTime->getTimestamp() - time() > 0;
        }

        return true;
      }
      return false;
    } catch (\Exception $e) {
      return false;
    }
  }
}
