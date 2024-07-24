<?php

declare(strict_types=1);

/**
 * Leave manager : Simple app for contract and leave management.
 *
 * @copyright Copyright (c) Silevester D. (https://github.com/SilverD3)
 * @link      https://github.com/SilverD3/leave-manager Leave Manager Project
 * @since     1.0 (2022)
 */

namespace App\Service;
use Core\Configure;
use Core\Mailer\Mailer;
use Core\Mailer\MailOptions;

require_once dirname(dirname(__DIR__)) . DS . DS . 'autoload.php';

class MailerServices
{
    /**
     * Get admin users' emails
     * 
     * @return array<string> List of admins' emails
     */
    public function getAdminEmails()
    {
        $adminEmails = [];

        $admins = (new EmployeesServices())->getByRole(1);

        $adminEmails = array_map(fn($admin) => $admin->getEmail(), $admins);

        return $adminEmails;
    }

    public function sentNewPermissionRequestEmail(int $employeId, int $permissionRequestId): bool
    {
        $employeesService = new EmployeesServices();
        $mailConfig = (new Configure())->read('Mail');

        $employee = $employeesService->getById($employeId);
        $adminEmails = $this->getAdminEmails();

        if (!$employee || empty($adminEmails)) {
            return false;
        }

        $template = file_get_contents(VIEW_PATH . 'email' . DS . 'newpermissionrequest.php');

        $placeholders = ['$_employee_name', '$_details_link'];
        $replacementsArray = [
            $employee->getFullName(), 
            VIEWS . 'PermissionRequests/view.php?id=' . $permissionRequestId
        ];

        $body = str_replace($placeholders, $replacementsArray, $template);

        $mailOptions = new MailOptions([
            'senderEmail' => (isset($mailConfig['from']) && !empty($mailConfig['from'])) ? $mailConfig['from'] : 'no-reply@leavemanager.com',
            'object' => 'Nouvelle demande de permission',
            'body' => $body,
            'recipients' => $adminEmails,
        ]);

        return Mailer::send($mailOptions);
    }
}