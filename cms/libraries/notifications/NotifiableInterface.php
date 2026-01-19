<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Notifications;

interface NotifiableInterface
{
    /**
     * Notify
     */
    public function notify(NotificationInterface $notification);

    /**
     * Get
     */
    public function getData(string $name);

    /**
     * Get
     */
    public function getId(): int;

    /**
     * Get
     */
    public function getEmail(): string;

    /**
     * Get
     */
    public function getName(): string;
}
