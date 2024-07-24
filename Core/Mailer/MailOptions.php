<?php

declare(strict_types=1);

/**
 * Leave manager : Simple app for contract and leave management.
 *
 * @copyright Copyright (c) Silevester D. (https://github.com/SilverD3)
 * @link      https://github.com/SilverD3/leave-manager Leave Manager Project
 * @since     2.0 (2024)
 */

namespace Core\Mailer;

class MailOptions
{
    /**
     * Sender email address
     * @var string
     */
    public string $senderEmail;

    /**
     * Sender name
     * @var string|null
     */
    public ?string $senderName;

    /**
     * Mail subject
     * @var string
     */
    public string $object;

    /**
     * Mail message body
     * @var string
     */
    public string $body;

    /**
     * Mail plain-text message body. 
     * @var string|null
     */
    public ?string $altBody = null;

    /**
     * Files to attach to the mail
     * @var array
     */
    public array $attachments = [];

    /**
     * List of recipients
     * @var array
     */
    public array $recipients;

    /**
     * Whether the mail body is in HTML format or not
     * @var bool
     */
    public bool $isHtml = true;

    /**
     * Initialize mailer options
     * 
     * @param array $options Key-value's array in which keys represent this class props
     */
    public function __construct(array $options = []){
        if(isset($options["senderEmail"]) && !empty($options["senderEmail"])){
            $this->senderEmail = $options["senderEmail"];
        }

        if(isset($options["senderName"]) && !empty($options["senderName"])){
            $this->senderName = $options["senderName"];
        }

        if(isset($options["object"]) && !empty($options["object"])){
            $this->object = $options["object"];
        }

        if(isset($options["body"]) && !empty($options["body"])){
            $this->body = $options["body"];
        }

        if (isset($options["altBody"]) && !empty($options["altBody"])) {
            $this->altBody = $options["altBody"];
        }

        if(isset($options["attachments"]) && !empty($options["attachments"])){
            $this->attachments = $options["attachments"];
        }

        if(isset($options["recipients"]) && !empty($options["recipients"])){
            $this->recipients = $options["recipients"];
        }

        if(isset($options["isHtml"]) && !is_null($options["isHtml"])){
            $this->isHtml = boolval($options["isHtml"]);
        }
    }
}
