<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Users\Notification;

use Junco\Notifications\Notifiable;
use Junco\Notifications\Notification;
use Junco\Users\Enum\UserStatus;

class UserNotifiable extends Notifiable
{
    // vars
    protected int   $user_id;
    protected array $data;

    /**
     * Get
     */
    protected function __construct(int $user_id, array $data = [])
    {
        $this->user_id = $user_id;

        if ($data) {
            $this->data = $data;
        }
    }

    /**
     * Get
     */
    public function getData(string $name)
    {
        if ($this->data === null) {
            $this->data = db()->query("
			SELECT
			 id ,
			 fullname ,
			 email
			FROM `#__users`
			WHERE id = ?", $this->user_id)->fetch();
        }
        return $this->data[$name] ?? null;
    }

    /**
     * Get
     */
    public function getId(): int
    {
        return $this->user_id;
    }

    /**
     * Get
     */
    public function getEmail(): string
    {
        return $this->getData('email') ?? '';
    }

    /**
     * Get
     */
    public function getName(): string
    {
        return $this->getData('fullname') ?? '';
    }

    /**
     * Get
     */
    public static function notifyByLabel(array|int $labels, Notification $notification, bool $send_now = false): void
    {
        if (is_int($labels)) {
            $labels = [$labels];
        }

        $users = db()->query("
		SELECT
		 u.id ,
		 u.fullname ,
		 u.email
		FROM `#__users_roles_labels_map` m1
		LEFT JOIN `#__users_roles_map` m2 ON ( m1.role_id = m2.role_id )
		LEFT JOIN `#__users` u ON ( m2.user_id = u.id )
		WHERE m1.label_id IN (?..)
		AND u.status = ?", $labels, UserStatus::active)->fetchAll();

        $notifiables = [];
        foreach ($users as $user) {
            $notifiables[] = new self($user['id'], $user);
        }

        if ($send_now) {
            app('notifications')->sendNow($notifiables, $notification);
        } else {
            app('notifications')->send($notifiables, $notification);
        }
    }
}
