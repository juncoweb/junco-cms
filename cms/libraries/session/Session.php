<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Session\UserAgent;

class Session
{
    // vars
    protected array     $config;
    protected UserAgent $userAgent;
    protected string    $userIp;
    protected ?string   $hash;

    // const
    const SAFE_PATH = SYSTEM_STORAGE . 'session';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->config = config('session');

        if ($this->config['session.allow_safe_path']) {
            ini_set('session.save_path', self::SAFE_PATH);
        }

        switch ($this->config['session.handler']) {
            case 'file':
                session_set_save_handler(new Junco\Session\Handler\FileHandler);
                break;
            case 'db':
                session_set_save_handler(new Junco\Session\Handler\DbHandler);
                break;
        }

        $options = ['use_only_cookies' => true];

        if ($this->config['session.set_params']) {
            $this->getSystemCookieOptions($options);
        }

        session_start($options);

        $server = request()->getServerParams();
        $this->userAgent = new UserAgent($server['HTTP_USER_AGENT'] ?? '');
        $this->userIp = ($server['REMOTE_ADDR'] ?? '');

        // security - I verify the user agent
        if ($this->get('__user_agent') != $this->userAgent) {
            $this->destroy();
        }

        // security - session expires
        if ($this->config['session.expires']) {
            $time = time();
            $expires = $this->get('__expires');

            if ($expires && $time > $expires) {
                $this->destroy();
            }
            $this->set('__expires', $time + $this->config['session.expires']);
        }

        // security - verify ip
        if ($this->config['session.verify_ip']) {
            $userIp = $this->get('__user_ip');

            if ($userIp === null) {
                $this->set('__user_ip', $this->userIp);
            } elseif ($userIp != $this->userIp) {
                $this->destroy();
                $this->set('__user_ip', $this->userIp);
            }
        }
    }

    /**
     * Gets a session value
     * 
     * @param string $key
     *
     * @return mixed
     */
    public function get(string $key): mixed
    {
        return $_SESSION[$key] ?? null;
    }

    /**
     * Set a session value
     * 
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    public function set(string $key, mixed $value = null): void
    {
        if ($value === null) {
            $this->unset($key);
        } else {
            $_SESSION[$key] = $value;
        }
    }

    /**
     * Unset a session value
     * 
     * @param string $key
     *
     * @return mixed
     */
    public function unset(string $key): mixed
    {
        $value = null;

        if (isset($_SESSION[$key])) {
            $value = $_SESSION[$key];
            unset($_SESSION[$key]);
        }

        return $value;
    }

    /**
     * Secure destruction of the php session
     * 
     * @return void
     */
    public function destroy(): void
    {
        session_unset();
        session_destroy();
        session_start();
        session_regenerate_id(true);

        $this->set('__user_agent', $this->userAgent->__toString());
    }

    /**
     * Returns a md5 hash that represents the session data
     *
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash ??= $this->buildHash();
    }

    /**
     * Get
     * 
     * @return UserAgent
     */
    public function getUserAgent(): UserAgent
    {
        return $this->userAgent;
    }

    /**
     * Get
     * 
     * @return string
     */
    public function getUserIp(): string
    {
        return $this->userIp;
    }

    /**
     * @return string
     */
    protected function buildHash(): string
    {
        $hash = $this->userAgent->__toString();

        if ($this->config['session.verify_ip']) {
            $hash .= $this->userIp;
        }

        return md5($hash);
    }

    /**
     * Is Safe To Continue
     * 
     * @param string $hash
     * @param string $user_agent
     *
     * @return bool
     */
    public function isSafeToContinue(string $hash, string $user_agent): bool
    {
        if (
            $this->config['session.verify_ip']
            && $this->userIp != $this->get('__user_ip')
        ) {
            return false;
        }

        if ($hash == $this->getHash()) {
            return true;
        }

        // if the hash fails, verify that the user_agent has been updated.
        return $this->userAgent->compareTo(new UserAgent($user_agent), '>=');
    }

    /**
     * Get
     */
    protected function getSystemCookieOptions(array &$options)
    {
        $config = config('system');
        $options += [
            'cookie_lifetime' => $config['system.cookie_lifetime'],
            'cookie_path'     => $config['system.cookie_path'],
            'cookie_domain'   => $config['system.cookie_domain'],
            'cookie_secure'   => $config['system.cookie_secure'],
            'cookie_httponly' => $config['system.cookie_httponly'],
        ];
    }
}
