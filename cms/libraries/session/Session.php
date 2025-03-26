<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

class Session
{
    // vars
    protected $config        = null;
    protected $user_agent    = null;
    protected $user_ip        = null;

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
        $this->user_agent = ($_SERVER['HTTP_USER_AGENT'] ?? '');

        // security - I verify the user agent
        if (
            !isset($_SESSION['__user_agent'])
            || $_SESSION['__user_agent'] != $this->user_agent
        ) {
            $this->destroy();
        }

        // security - session expires
        if ($this->config['session.expires']) {
            $time = time();
            if (isset($_SESSION['__expires']) && $time > $_SESSION['__expires']) {
                $this->destroy();
            }
            $_SESSION['__expires'] = $time + $this->config['session.expires'];
        }

        // security - verify ip
        if ($this->config['session.verify_ip']) {
            $this->user_ip = ($_SERVER['REMOTE_ADDR'] ?? '');

            if (!isset($_SESSION['__user_ip'])) {
                $_SESSION['__user_ip'] = $this->user_ip;
            } elseif ($_SESSION['__user_ip'] !== $this->user_ip) {
                $this->destroy();
                $_SESSION['__user_ip'] = $this->user_ip;
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

        $_SESSION['__user_agent'] = $this->user_agent;
    }

    /**
     * Returns a md5 hash that represents the session data
     *
     * @return string
     */
    public function getHash(): string
    {
        $hash = $this->user_agent;

        if ($this->config['session.verify_ip']) {
            $hash .= $this->user_ip;
        }

        return md5($hash);
    }

    /**
     * Get browser
     * 
     * @param ?string $user_agent
     * 
     * @return array
     */
    public function getBrowser(?string $user_agent = null): array
    {
        if ($user_agent === null) {
            $user_agent = $this->user_agent;
        }

        $user_agent = strtolower($user_agent);
        $result = [
            'platform'    => 'Unknown',
            'browser'    => 'Unknown',
            'version'    => ''
        ];

        // platform
        $_platforms    = [
            'android'    => 'android',
            'linux'        => 'linux',
            'macintosh'    => 'mac',
            'mac os x'    => 'mac',
            'windows'    => 'windows',
            'win32'        => 'windows',
        ];

        foreach ($_platforms as $needle => $value) {
            if (false !== strpos($user_agent, $needle)) {
                $result['platform'] = $value;
                break;
            }
        }

        // browser
        $_browsers = [
            'opera'            => ['Opera', 'opera/'],
            'opr/'            => ['Opera', 'opr/'],
            'edge'          => ['Edge', 'edge/'],
            'chrome'        => ['Chrome', 'chrome/'],
            'safari'        => ['Safari', 'version/'],
            'firefox'        => ['Firefox', 'firefox/'],
            'msie'            => ['Internet Explorer', 'msie/'],
            'trident/7'        => ['Internet Explorer', 'rv:'],
        ];

        foreach ($_browsers as $needle => $value) {
            if (false !== strpos($user_agent, $needle)) {
                $result['browser'] = $value[0];
                break;
            }
        }

        // version
        if (
            $result['browser'] !== 'Unknown'
            && (false !== ($i = strpos($user_agent, $value[1])))
        ) {
            $version = preg_split('#[^0-9.]#', substr($user_agent, $i + strlen($value[1])), 2);
            $result['version'] = $version[0];
        }

        return $result;
    }

    /**
     * Is Safe To Continue
     *
     * @return bool
     */
    public function isSafeToContinue(): bool
    {
        if ($this->config['session.verify_ip']) {
            if (($_SESSION['__user_ip'] ?? '') !== $this->user_ip) {
                return false;
            }
        }

        $current = $this->getBrowser();
        $saved   = $this->getBrowser($_SESSION['__user_agent']);

        if (
            $saved['platform'] == $current['platform']
            && $saved['browser'] == $current['browser']
            && $saved['version']
            && $current['version']
            && version_compare($saved['version'], $current['version'], '<=')
        ) {
            $_SESSION['__user_agent'] = $this->user_agent;
            return true;
        }

        return false;
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
