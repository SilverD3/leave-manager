<?php

declare(strict_types=1);

/**
 * Leave manager : Simple app for contract and leave management.
 *
 * @copyright Copyright (c) Silevester D. (https://github.com/SilverD3)
 * @link      https://github.com/SilverD3/leave-manager Leave Manager Project
 * @since     1.0 (2022)
 */

namespace Core\Auth;

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'autoload.php';

use Core\Configure;

/**
 * Auth Class
 */
class AuthSession
{
    protected $connected = false;

    protected $auth_user;

    protected $expiration_date;

    protected $session_life_time;

    protected $connected_at;

    protected $default_session_config = [
        'timeout' => 60 * 60 * 6, // 6 hours
    ];

    function __construct()
    {
        if (!isset($_SESSION['__configure__'])) {
            $config = new Configure();
        } else {
            $config = unserialize($_SESSION['__configure__']);
        }

        $session_config = $config->read('Session', $this->default_session_config);

        $this->session_life_time = $session_config['timeout'];
    }

    /**
     * @return self
     */
    public function setConnected(bool $connected)
    {
        $this->connected = $connected;
        return $this;
    }

    /**
     * @return bool
     */
    public function getConnected(): bool
    {
        return $this->connected;
    }

    /**
     * @return self
     */
    public function setAuthUser($user)
    {
        $this->auth_user = $user;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAuthUser()
    {
        return $this->auth_user;
    }

    /**
     * @return mixed
     */
    public function getExpirationDate()
    {
        return $this->expiration_date;
    }

    /**
     * @param mixed $expiration_date
     *
     * @return self
     */
    public function setExpirationDate($expiration_date)
    {
        $this->expiration_date = $expiration_date;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSessionLifeTime()
    {
        return $this->session_life_time;
    }

    /**
     * @param mixed $session_life_time
     *
     * @return self
     */
    public function setSessionLifeTime($session_life_time)
    {
        $this->session_life_time = $session_life_time;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getConnectedAt()
    {
        return $this->connected_at;
    }

    /**
     * @param mixed $connected_at
     *
     * @return self
     */
    public function setConnectedAt($connected_at)
    {
        $this->connected_at = $connected_at;

        return $this;
    }
}
