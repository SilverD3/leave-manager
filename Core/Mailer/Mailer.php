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

use Core\Configure;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class Mailer
{
    private static PHPMailer $phpMailer;
    private static array $config;

    public static function send(MailOptions $options): bool
    {
        self::validateOptions($options);

        try {
            $phpMailer = self::getPHPMailer();

            if (!empty($options->senderEmail)) {
                $phpMailer->setFrom($options->senderEmail, $options->senderEmail);
            }

            foreach ($options->recipients as $recipient) {
                $phpMailer->addAddress($recipient);
            }

            if (!empty($options->attachments)) {
                foreach ($options->attachments as $attach) {
                    $phpMailer->addAttachment($attach);
                }
            }

            $phpMailer->isHTML($options->isHtml);
            $phpMailer->Subject = $options->object;
            $phpMailer->Body = $options->body;
            $phpMailer->AltBody = $options->altBody;

            $phpMailer->CharSet = 'UTF-8';

            $mailerDisabled = isset(self::$config['enable'])
                && (self::$config['enable'] === false || self::$config['enable'] === 'false');
            // Just return true if mail sending is disabled
            if ($mailerDisabled) {
                return true;
            }

            if ($phpMailer->send() !== false) {
                return true;
            }

            return false;
        } catch (\Throwable $th) {
            throw new \Exception("Echec d'envoi du mail. Erreur: " . $th->getMessage());
        }
    }

    public static function getPHPMailer()
    {
        self::initConfig();
        return self::$phpMailer;
    }

    public static function initConfig(): void
    {
        if (!isset(self::$config) || empty(self::$config)) {
            self::$config = (new Configure())->read("Mail");
        }

        if (!isset(self::$phpMailer)) {
            self::$phpMailer = new PHPMailer();

            self::$phpMailer->IsSMTP();
            self::$phpMailer->SMTPDebug = SMTP::DEBUG_OFF;
            self::$phpMailer->SMTPAuth = true;
            self::$phpMailer->SMTPSecure = self::$config['tls'] === true ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
            self::$phpMailer->Host = self::$config['host'];
            self::$phpMailer->Port = intval(self::$config['port']);
            self::$phpMailer->Username = self::$config['username'];
            self::$phpMailer->Password = self::$config['password'];

            if (isset(self::$config['from']) && !empty(self::$config['from'])) {
                self::$phpMailer->setFrom(self::$config['from']);
            }
        }
    }

    public static function validateOptions(MailOptions $options, PHPMailer $phpMailer = null)
    {
        if (empty($options->senderEmail) && (is_null($phpMailer) || empty($phpMailer->From))) {
            throw new \Exception("L'adresse de l'expéditeur n'a pas été fourni");
        }

        if (empty($options->recipients)) {
            throw new \Exception("Aucun destinataire n'a été renseigné");
        }
    }
}