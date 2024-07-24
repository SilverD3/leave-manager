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
        if (!isset(self::$phpMailer)) {
            self::$phpMailer = new PHPMailer();

            $mailConfig = (new Configure())->read("Mail");

            self::$phpMailer->IsSMTP();
            self::$phpMailer->SMTPDebug = SMTP::DEBUG_OFF;
            self::$phpMailer->SMTPAuth = true;
            self::$phpMailer->SMTPSecure = $mailConfig['tls'] === true ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
            self::$phpMailer->Host = $mailConfig['host'];
            self::$phpMailer->Port = intval($mailConfig['port']);
            self::$phpMailer->Username = $mailConfig['username'];
            self::$phpMailer->Password = $mailConfig['password'];

            if (isset($mailConfig['from']) && !empty($mailConfig['from'])) {
                self::$phpMailer->setFrom($mailConfig['from']);
            }

            return self::$phpMailer;
        }

        return self::$phpMailer;
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