<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Notifications;

use Junco\Utils\Aleatory;

abstract class Notification implements NotificationInterface
{
    protected ?string $language = null;
    protected string  $uuid;
    protected string  $type;
    protected int     $id = 0;

    /**
     * Returns a list of channels via which the notification will be sent.
     */
    public function via(): array
    {
        return ['database', 'email'];
    }

    /**
     * Language
     */
    public function getLanguage(): ?string
    {
        return $this->language;
    }

    /**
     * Returns a unique identifier string.
     */
    public function getUuid(): string
    {
        return $this->uuid ??= Aleatory::uuid();
    }

    /**
     * Returns the type.
     */
    public function getType(): string
    {
        return $this->type ??= preg_replace('#[A-Z]#', '-$0', lcfirst(substr((new \ReflectionClass($this))->getShortName(), 0, -12)));
    }

    /**
     * Returns an identifier number (optional).
     */
    public function getId(): int
    {
        return $this->id;
    }
}
