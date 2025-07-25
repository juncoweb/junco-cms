<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Authentication;

use Authentication;
use Database;
use Junco\Users\Enum\UserStatus;
use SystemException;

class Curuser
{
    // vars
    protected ?Authentication $authentication = null;
    //
    protected int     $id            = 0;
    protected string  $username        = '';
    protected string  $user_slug    = '';
    protected string  $fullname        = '';
    protected string  $email        = '';
    protected string  $password        = '';
    protected int     $avatar_id    = 0;
    protected string  $avatar_file    = '';
    protected string  $status        = '';
    //
    protected ?bool   $is_admin        = null;
    protected ?string $ip            = null;
    protected ?array  $roles        = null;
    protected ?array  $permissions    = null;

    /**
     * Constructor
     *
     * @param int $user_id
     */
    public function __construct(int $user_id = 0)
    {
        if (!$user_id) {
            $this->authentication ??= new Authentication();
            $user_id = $this->authentication->getCurrentUserId();
        }

        if ($user_id > 0) {
            $data = db()->safeFind("
			SELECT
			 id ,
			 username ,
			 fullname ,
			 email ,
			 password ,
			 user_slug ,
			 avatar_id ,
			 avatar_file ,
			 status
			FROM `#__users`
			WHERE id = ?
			AND status = ?", $user_id, UserStatus::active)->fetch();

            if ($data) {
                $this->id          = $data['id'];
                $this->username    = $data['username'];
                $this->fullname    = $data['fullname'];
                $this->email       = $data['email'];
                $this->password    = $data['password'];
                $this->user_slug   = $data['user_slug'];
                $this->avatar_id   = $data['avatar_id'];
                $this->avatar_file = $data['avatar_file'];
                $this->status      = $data['status'];
            }
        }
    }

    /**
     * Getters
     */
    public function __get(string $name)
    {
        switch ($name) {
            case 'id':
            case 'username':
            case 'status':
            case 'user_slug':
            case 'fullname':
            case 'email':
            case 'password':
            case 'avatar_id':
            case 'avatar_file':
                return $this->{$name};
        }

        $trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $name . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'],
            E_USER_NOTICE
        );
    }

    /**
     * Get
     * 
     * @return int
     */
    public function getPreLoginUserId(): int
    {
        if ($this->id) {
            return 0;
        }

        return $this->authentication?->getPreLoginUserId();
    }

    /**
     * PreLogin
     * 
     * @param int $user_id
     * 
     * @return mixed
     */
    public function preLogin(int $user_id, bool $not_expire = false, ?array &$data = null): mixed
    {
        return $this->authentication?->preLogin($user_id, $not_expire, $data);
    }

    /**
     * Take Login
     * 
     * @return mixed
     */
    public function takePreLogin(?array &$data = null): mixed
    {
        return $this->authentication?->takePreLogin($data);
    }

    /**
     * Login
     * 
     * @param int $user_id
     * 
     * @return mixed
     */
    public function login(int $user_id, bool $not_expire = false, ?array &$data = null): mixed
    {
        return $this->authentication?->login($user_id, $not_expire, $data);
    }

    /**
     * Logout
     * 
     * @param int $user_id
     * 
     * @return bool
     */
    public function logout(): bool
    {
        return (bool)$this->authentication?->logout();
    }

    /**
     * Is Admin
     */
    public function isAdmin()
    {
        if ($this->is_admin === null) {
            $this->is_admin = in_array(L_SYSTEM_ADMIN, $this->getPermissions());
        }

        return $this->is_admin;
    }

    /**
     * Permissions
     *
     * @return array  All user permissions
     */
    public function getPermissions()
    {
        if ($this->permissions !== null) {
            return $this->permissions;
        }

        $this->permissions = [];
        $cache_key = config('usys-system.permissions_q');

        if ($cache_key) {
            $cache = cache();
            $permissions = $cache->get($cache_key);

            if ($permissions === null) {
                // query
                $rows = db()->safeFind("
				SELECT
				 role_id ,
				 label_id
				FROM `#__users_roles_labels_map`
				WHERE status > 0")->fetchAll();

                $permissions = [];
                foreach ($rows as $row) {
                    $permissions[$row['role_id']][] = $row['label_id'];
                }

                $cache->set($cache_key, $permissions);
            }


            foreach ($this->getRoles() as $role_id) {
                if (isset($permissions[$role_id])) {
                    foreach ($permissions[$role_id] as $label_id) {
                        $this->permissions[] = $label_id;
                    }
                }
            }
        } else {
            $roles = $this->getRoles();

            if ($roles) {
                $this->permissions = db()->safeFind("
				SELECT label_id
				FROM `#__users_roles_labels_map`
				WHERE role_id IN ( ?.. )
				AND status = 1", $roles)->fetchAll(Database::FETCH_COLUMN);
            }
        }

        return $this->permissions;
    }

    /**
     * Authenticate
     *
     * @param int label_id
     *   -1 only not register
     *    0 only register
     *   >0 labels permissions
     *
     * @throws
     *   401 the user must login
     *   403 the user has insufficient permissions
     */
    public function authenticate(int ...$label_id): void
    {
        if (!$label_id || in_array(0, $label_id)) {
            if (!$this->id) {
                throw new SystemException(401);
            }
        } elseif (in_array(-1, $label_id)) {
            if ($this->id) {
                throw new SystemException(_t('This section is only for anonymous users'), 403);
            }
        } else {
            if (!$this->getRoles()) {
                throw new SystemException(401);
            }
            if (SYSTEM_DEVELOPER_MODE && $this->isAdmin()) {
                return;
            }
            if (!array_intersect($label_id, $this->getPermissions())) {
                throw new SystemException(403);
            }
        }
    }

    /**
     * Verify that one or more tags have access permission
     * 
     * @param int ...$label_id
     * 
     * @return bool
     */
    public function hasPermissions(...$label_id): bool
    {
        if (!$label_id || in_array(0, $label_id)) {
            if (!$this->id) {
                return false;
            }
        } elseif (in_array(-1, $label_id)) {
            if ($this->id) {
                return false;
            }
        } else {
            if (!$this->getRoles()) {
                return false;
            }

            if (
                !array_intersect($label_id, $this->getPermissions())
                && !($this->isAdmin() && SYSTEM_DEVELOPER_MODE)
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns the current Ip
     * 
     * @return string
     */
    public function getIp(): string
    {
        if ($this->ip === null) {
            $this->ip = getenv('HTTP_CLIENT_IP') ?:
                getenv('HTTP_X_FORWARDED_FOR') ?:
                getenv('HTTP_X_FORWARDED') ?:
                getenv('HTTP_FORWARDED_FOR') ?:
                getenv('HTTP_FORWARDED') ?:
                getenv('REMOTE_ADDR');
        }

        return $this->ip;
    }

    /**
     * Returns the current Ip as a binary
     * 
     * @return binary
     */
    public function getIpAsBinary(): string
    {
        return inet_pton($this->getIp()) ?: '';
    }

    /**
     * Returns the current Ip as a integer
     * 
     * @return int
     */
    public function getIpAsInteger(): int
    {
        return ip2long($this->getIp()) ?: 0;
    }

    /**
     * Get
     */
    protected function getRoles()
    {
        if ($this->roles === null) {
            if ($this->id) {
                $this->roles = db()->safeFind("
				SELECT role_id
				FROM `#__users_roles_map`
				WHERE user_id = ?", $this->id)->fetchAll(Database::FETCH_COLUMN);
            } else {
                $this->roles = [];
            }
        }

        return $this->roles;
    }
}
