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
class FileValidation
{
    private ?int $minSize = null;

    private ?int $maxSize = null;

    private array $fileTypes = [];


    /**
     * Get the value of minSize
     */
    public function getMinSize(): ?int
    {
        return $this->minSize;
    }

    /**
     * Set the value of minSize
     */
    public function setMinSize(?int $minSize): self
    {
        $this->minSize = $minSize;

        return $this;
    }

    /**
     * Get the value of maxSize
     */
    public function getMaxSize(): ?int
    {
        return $this->maxSize;
    }

    /**
     * Set the value of maxSize
     */
    public function setMaxSize(?int $maxSize): self
    {
        $this->maxSize = $maxSize;

        return $this;
    }

    /**
     * Get the value of fileTypes
     * @return array<FileType>
     */
    public function getFileTypes(): array
    {
        return $this->fileTypes;
    }

    /**
     * Set the value of fileTypes
     * @var array<FileType> $fileTypes
     */
    public function setFileTypes(array $fileTypes): self
    {
        $this->fileTypes = $fileTypes;

        return $this;
    }
}
