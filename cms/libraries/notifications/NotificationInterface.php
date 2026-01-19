<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Notifications;

interface NotificationInterface
{
    /**
     * Via
     */
    public function via(): array;

    /**
     * Language
     */
    public function getLanguage(): ?string;

    /**
     * Returns a unique identifier string.
     */
    public function getUuid(): string;

    /**
     * Returns the type.
     */
    public function getType(): string;

    /**
     * Returns an identifier number (optional).
     */
    public function getId(): int;

    /**
     * Returns the necessary data for Database channel.
     */
    public function toDatabase();

    /**
     * Returns the necessary data for Email channel.
     */
    public function toEmail();
}
