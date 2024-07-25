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

use App\Entity\PermissionRequest;
use Core\Configure;
use Core\Mailer\Mailer;
use Core\Mailer\MailOptions;

require_once dirname(dirname(__DIR__)) . DS . DS . 'autoload.php';

class MailerServices
{
    private array $mailConfig;

    public function __construct()
    {
        $this->mailConfig = (new Configure())->read('Mail', []);
    }

    /**
     * Get admin users' emails
     * 
     * @return array<string> List of admins' emails
     */
    public function getAdminEmails()
    {
        $adminEmails = [];

        $admins = (new EmployeesServices())->getByRole(1);

        $adminEmails = array_map(fn ($admin) => $admin->getEmail(), $admins);

        return $adminEmails;
    }

    public function sendAccountCreationMail($email, $pwd): bool
    {
        $template = file_get_contents(VIEW_PATH . 'email' . DS . 'accountcreation.php');

        $placeholders = ['$_user_email', '$_user_pwd', '$_app_link'];
        $replacementsArray = [
            $email,
            $pwd,
            BASE_URL
        ];

        $body = str_replace($placeholders, $replacementsArray, $template);

        $mailOptions = new MailOptions([
            'senderEmail' => (isset($this->mailConfig['from']) && !empty($this->mailConfig['from']))
                ? $this->mailConfig['from']
                : 'no-reply@leavemanager.com',
            'object' => 'Creation de votre compte',
            'body' => $body,
            'recipients' => [$email],
        ]);

        return Mailer::send($mailOptions);
    }

    public function sentNewPermissionRequestEmail(int $employeId, int $permissionRequestId): bool
    {
        $employeesService = new EmployeesServices();

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
            'senderEmail' => (isset($this->mailConfig['from']) && !empty($this->mailConfig['from']))
                ? $this->mailConfig['from']
                : 'no-reply@leavemanager.com',
            'object' => 'Nouvelle demande de permission',
            'body' => $body,
            'recipients' => $adminEmails,
        ]);

        return Mailer::send($mailOptions);
    }

    private function sendPermissionRequestProcessedMail(PermissionRequest $permissionRequest, bool $approved): bool
    {
        $employeesService = new EmployeesServices();

        $employee = $employeesService->getById($permissionRequest->getEmployeeId());

        if (!$employee) {
            return false;
        }

        $template = $approved 
            ? file_get_contents(VIEW_PATH . 'email' . DS . 'permissionrequestapproved.php') 
            : file_get_contents(VIEW_PATH . 'email' . DS . 'permissionrequestrejected.php');

        $placeholders = ['$_start_date', '$_end_date', '$_details_link'];
        $replacementsArray = [
            $permissionRequest->getStartDate(),
            $permissionRequest->getEndDate(),
            VIEWS . 'PermissionRequests/view.php?id=' . $permissionRequest->getId()
        ];

        $body = str_replace($placeholders, $replacementsArray, $template);

        $mailOptions = new MailOptions([
            'senderEmail' => (isset($this->mailConfig['from']) && !empty($this->mailConfig['from']))
                ? $this->mailConfig['from']
                : 'no-reply@leavemanager.com',
            'object' => $approved ? 'Confirmation de votre demande de permission' : 'Rejet de votre demande de permission',
            'body' => $body,
            'recipients' => [$employee->getEmail()],
        ]);

        return Mailer::send($mailOptions);
    }

    public function sentPermissionRequestApprovedEmail(PermissionRequest $permissionRequest): bool
    {
        return $this->sendPermissionRequestProcessedMail($permissionRequest, true);
    }

    public function sentPermissionRequestRejectedEmail(PermissionRequest $permissionRequest): bool
    {
        return $this->sendPermissionRequestProcessedMail($permissionRequest, false);
    }
}