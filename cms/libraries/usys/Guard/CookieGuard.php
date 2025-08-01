<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Usys\Guard;

use Junco\Authentication\GuardInterface;
use Junco\Utils\Aleatory;

class CookieGuard implements GuardInterface
{
    // const
    protected const SELECTOR_LENGTH = 16;
    protected const VALIDATOR_LENGTH = 16;

    // vars
    protected $db;
    protected $session;
    protected string $user_key;
    protected string $rm_key;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->db = db();
        $this->session = session();

        $config         = config('usys-system');
        $this->rm_key   = $config['usys-system.rm_key'];
        $this->user_key = $config['usys-system.user_key'];
    }

    /**
     * Returns the user ID from the session or cookie
     *
     * @param string $token
     */
    public function getUserId(): int
    {
        $user_id = (int)$this->session->get($this->user_key);

        if ($user_id > 0) {
            return $user_id;
        }

        $user_id = $this->getUserIdFromCookie();

        if ($user_id) {
            $this->session->set($this->user_key, $user_id);
        }

        return $user_id;
    }

    /**
     * Returns the user ID retrieved from storage.
     *
     * @return int
     */
    public function getPreLoginUserId(): int
    {
        $login = $this->getPreLoginUser();

        if (!$login) {
            return 0;
        }

        return $login['user_id'];
    }

    /**
     * Login
     * 
     * @param int  $user_id       If it is zero, log out.
     * @param bool $not_expire
     * 
     * @return bool
     */
    public function preLogin(int $user_id = 0, bool $not_expire = false, ?array &$data = []): bool
    {
        if (!($user_id > 0)) {
            return false;
        }

        $this->session->set('__mfa_' . $this->user_key, [
            'user_id' => $user_id,
            'not_expire' => $not_expire,
            'expires_at' => time() + config('usys.mfa_lifetime') ?: 300
        ]);

        return true;
    }

    /**
     * PreLogin
     * 
     * @return bool
     */
    public function takePreLogin(array &$data = []): bool
    {
        $login = $this->getPreLoginUser();

        if (!$login) {
            return false;
        }

        $this->session->unset('__mfa_' . $this->user_key);

        return $this->login($login['user_id'], $login['not_expire'], $data);
    }

    /**
     * Login
     * 
     * @param int  $user_id       If it is zero, log out.
     * @param bool $not_expire
     * 
     * @return bool
     */
    public function login(int $user_id = 0, bool $not_expire = false, array &$data = []): bool
    {
        if (!($user_id > 0)) {
            return false;
        }

        if ($not_expire) {
            $this->setCookie($this->start($user_id), 0x7fffffff);
        } else {
            $this->destroy();
        }

        $this->session->set($this->user_key, $user_id);

        return true;
    }

    /**
     * Logout
     */
    public function logout(): bool
    {
        $this->session->set($this->user_key, 0);

        return $this->destroy();
    }

    /**
     * Get
     *
     * @return array
     */
    protected function getPreLoginUser(): ?array
    {
        $data = $this->session->get('__mfa_' . $this->user_key);


        if (!$data || $data['expires_at'] > time()) {
            return null;
        }

        return $data;
    }

    /**
     * Returns the user ID from the "remember me" cookie.
     *
     * @return int
     */
    protected function getUserIdFromCookie(bool $security = true): int
    {
        $token = $_COOKIE[$this->rm_key] ?? null;

        if (!$token || !$this->validateToken($token)) {
            return 0;
        }

        // query
        $data = $this->db->query("
		SELECT
		 id ,
		 user_id,
		 session_validator ,
		 session_hash
		FROM `#__usys_sessions`
		WHERE session_selector = ?", $this->getSelector($token))->fetch();

        if (!$data) {
            return 0;
        }

        if (!hash_equals($data['session_validator'], $this->getValidator($token))) {
            // this is impossible without the manipulation of the data
            return 0;
        }

        if ($security) {
            if (!$this->session->isSafeToContinue($data['session_hash'])) {
                return 0;
            }

            $this->db->exec("
            UPDATE `#__usys_sessions` 
            SET
             accessed_at = NOW(),
             session_hash = ?  
            WHERE id = ?", $this->session->getHash(), $data['id']);
        }

        return $data['user_id'];
    }

    /**
     * Generates a token and saves it in the database.
     *
     * @param int $user_id
     */
    protected function start($user_id)
    {
        do {
            $token    = $this->generateToken();
            $selector = $this->getSelector($token);
            $total    = $this->db->query("
			SELECT COUNT(*)
			FROM `#__usys_sessions`
			WHERE session_selector = ?", $selector)->fetchColumn();
        } while ($total);

        // query
        $this->db->exec("INSERT INTO `#__usys_sessions` (??, accessed_at) VALUES (??, NOW())", [
            'user_id'           => $user_id,
            'session_selector'  => $selector,
            'session_validator' => $this->getValidator($token),
            'session_hash'      => $this->session->getHash(),
            'session_ip'        => $_SERVER['REMOTE_ADDR'] ?? '',
            'session_ua'        => $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);

        return $token;
    }

    /**
     * Destroy a token from the database and remove it from the cookie.
     */
    protected function destroy(): bool
    {
        $token = $_COOKIE[$this->rm_key] ?? null;

        if (!$token || !$this->validateToken($token)) {
            return false;
        }

        $this->db->exec("DELETE FROM `#__usys_sessions` WHERE session_selector = ?", $this->getSelector($token));
        $this->setCookie();

        return true;
    }

    /**
     * Set cookie value
     * 
     * @param string  $token
     * @param int     $expire
     */
    protected function setCookie(string $token = '', int $expire = 0): void
    {
        $config = config('system');
        setcookie(
            $this->rm_key,
            $token,
            $expire,
            $config['system.cookie_path'],
            $config['system.cookie_domain'],
            $config['system.cookie_secure'],
            $config['system.cookie_httponly']
        );
    }

    /**
     * Get
     *
     * @return string
     */
    protected function generateToken(): string
    {
        return Aleatory::token(self::SELECTOR_LENGTH + self::VALIDATOR_LENGTH);
    }

    /**
     * Validator
     *
     * @param string $token
     * 
     * @return bool
     */
    protected function validateToken(string $token): bool
    {
        return (bool)preg_match('/^[\w-]{' . (self::SELECTOR_LENGTH + self::VALIDATOR_LENGTH) . '}$/i', $token);
    }

    /**
     * Get
     *
     * @param string $token
     * 
     * @return string
     */
    protected function getSelector($token): string
    {
        return substr($token, 0, self::SELECTOR_LENGTH);
    }

    /**
     * Get
     *
     * @param string $token
     * 
     * @return string
     */
    protected function getValidator($token): string
    {
        return hash('sha256', substr($token, -self::VALIDATOR_LENGTH));
    }
}
