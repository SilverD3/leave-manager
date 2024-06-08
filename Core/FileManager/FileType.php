<?php

declare(strict_types=1);

/**
 * Leave manager : Simple app for contract and leave management.
 *
 * @copyright Copyright (c) Silevester D. (https://github.com/SilverD3)
 * @link      https://github.com/SilverD3/leave-manager Leave Manager Project
 * @since     2.0 (2024)
 */

namespace Core\FileManager;

/**
 * File validation constraints
 */
enum FileType
{
  case IMAGE;
  case VIDEO;
  case PDF;
  case EXCEL;
  case WORD;
  case ARCHIVE;
  case AUDIO;
  case UNKNOWN;
}
