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
 * File upload status
 */
class UploadStatus
{
    private ?bool $succeeded;

    private array $errors;

    private ?string $filename;

    /**
     * Get the value of succeeded
     */
    public function hasSucceeded(): ?bool
    {
        return $this->succeeded;
    }

    /**
     * Set the value of succeeded
     */
    public function setSucceeded(?bool $succeeded): self
    {
        $this->succeeded = $succeeded;

        return $this;
    }

    /**
     * Get the value of errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Set the value of errors
     */
    public function setErrors(array $errors): self
    {
        $this->errors = $errors;

        return $this;
    }

    /**
     * Get the value of filename
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * Set the value of filename
     */
    public function setFilename(?string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }
}
