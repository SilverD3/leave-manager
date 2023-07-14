<?php

declare(strict_types=1);

/**
 * Leave manager : Simple app for contract and leave management.
 *
 * @copyright Copyright (c) Silevester D. (https://github.com/SilverD3)
 * @link      https://github.com/SilverD3/leave-manager Leave Manager Project
 * @since     1.0 (2022)
 */

namespace Core\FlashMessages;

/**
 * Message
 */
class Message
{
    private string $message;
    private string $type;

    const ALERT = 'default';
    const ERROR = 'error';
    const SUCCESS = 'success';

    function __construct(string $message, string $type)
    {
        $this->message = $message;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     *
     * @return self
     */
    public function setMessage(string $message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return self
     */
    public function setType(string $type)
    {
        $this->type = $type;

        return $this;
    }
}
