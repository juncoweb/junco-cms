<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Users;

use Junco\Users\Entity\User;
use Junco\Users\Enum\UserStatus;
use Database;

class Curuser extends User
{
    // vars
    protected ?bool   $is_admin    = null;
    protected ?string $ip          = null;
    protected ?array  $roles       = null;
    protected ?array  $permissions = null;

    /**
     * Constructor
     *
     * @param int $user_id
     */
    public function __construct(int $user_id = 0)
    {
        if (!$user_id) {
            $user_id = auth()->getUserId();
        }

        if ($user_id > 0) {
            $data = db()->query("
			SELECT
			 id ,
			 username ,
			 fullname ,
			 email ,
			 password ,
             status
			FROM `#__users`
			WHERE id = ?
			AND status = ?", $user_id, UserStatus::active)->fetch();

            if ($data) {
                $this->id       = $data['id'];
                $this->username = $data['username'];
                $this->fullname = $data['fullname'];
                $this->email    = $data['email'];
                $this->password = $data['password'];
                $this->status   = UserStatus::active;
            }
        }
    }

    /**
     * Getters
     */
    public function __get(string $name)
    {
        $trace = debug_backtrace();
        trigger_error(
            'Deprecated property via __get(): ' . $name . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'],
            E_USER_DEPRECATED
        );

        switch ($name) {
            case 'id':
            case 'username':
            case 'fullname':
            case 'email':
            case 'password':
                return $this->{$name};
        }

        $trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $name . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'],
            E_USER_NOTICE
        );
    }

    /**
     * Is Admin
     */
    public function isAdmin(): bool
    {
        if ($this->is_admin === null) {
            $this->is_admin = in_array(L_SYSTEM_ADMIN, $this->getPermissions());
        }

        return $this->is_admin;
    }

    /**
     * Permissions
     *
     * @return array  All current user permissions.
     */
    public function getPermissions(): array
    {
        if ($this->permissions !== null) {
            return $this->permissions;
        }

        $this->permissions = [];
        $roles = $this->getRoles();

        if (!$roles) {
            return $this->permissions;
        }

        $cache_key = config('usys-system.permissions_q');

        if ($cache_key) {
            $permissions = $this->getAllPermissionsFromCache($cache_key);

            foreach ($roles as $role_id) {
                if (isset($permissions[$role_id])) {
                    foreach ($permissions[$role_id] as $label_id) {
                        $this->permissions[] = $label_id;
                    }
                }
            }
        } else {
            $this->permissions = db()->query("
            SELECT label_id
            FROM `#__users_roles_labels_map`
            WHERE role_id IN ( ?.. )
            AND status = 1", $roles)->fetchAll(Database::FETCH_COLUMN);
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
                alert(401);
            }
        } elseif (in_array(-1, $label_id)) {
            if ($this->id) {
                alert(403, _t('This section is only for anonymous users'));
            }
        } else {
            if (!$this->id) {
                alert(401);
            }
            if (SYSTEM_DEVELOPER_MODE && $this->isAdmin()) {
                return;
            }
            if (!array_intersect($label_id, $this->getPermissions())) {
                alert(403);
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
            if (!$this->id) {
                return false;
            }

            if (
                !array_intersect($label_id, $this->getPermissions())
                && !(SYSTEM_DEVELOPER_MODE && $this->isAdmin())
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
     * @return string
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
    protected function getAllPermissionsFromCache(string $cache_key): array
    {
        $cache = cache();
        $permissions = $cache->get($cache_key);

        if ($permissions === null) {
            $permissions = $this->queryPermissions();
            $cache->set($cache_key, $permissions);
        }

        return $permissions;
    }

    /**
     * Query
     */
    protected function queryPermissions(): array
    {
        $rows = db()->query("
        SELECT
         role_id ,
         label_id
        FROM `#__users_roles_labels_map`
        WHERE status > 0")->fetchAll();

        $permissions = [];
        foreach ($rows as $row) {
            $permissions[$row['role_id']][] = $row['label_id'];
        }

        return $permissions;
    }

    /**
     * Get
     */
    protected function getRoles(): array
    {
        if ($this->roles === null) {
            $this->roles = $this->id
                ? db()->query("SELECT role_id FROM `#__users_roles_map` WHERE user_id = ?", $this->id)->fetchAll(Database::FETCH_COLUMN)
                : [];
        }

        return $this->roles;
    }
}
