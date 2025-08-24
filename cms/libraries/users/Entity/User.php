<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Users\Entity;

use Junco\Users\Enum\UserStatus;

class User
{
    protected int        $id          = 0;
    protected string     $username    = '';
    protected string     $fullname    = '';
    protected string     $email       = '';
    protected string     $password    = '';
    protected UserStatus $status      = UserStatus::inactive;
    protected bool       $is_creation = false;

    /**
     * Get
     */
    public function __construct(
        int    $id,
        string $username,
        string $fullname,
        string $email,
        string $password,
        UserStatus|string $status
    ) {
        $this->id       = $id;
        $this->username = $username;
        $this->fullname = $fullname;
        $this->email    = $email;
        $this->password = $password;
        $this->status   = is_string($status)
            ? UserStatus::get($status)
            : $status;
    }

    /**
     * Get
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get
     */
    public function getName(): string
    {
        return $this->fullname;
    }

    /**
     * Get
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Get
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Get
     */
    public function getStatus(): UserStatus
    {
        return $this->status;
    }

    /**
     * Verify
     */
    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    /**
     * Is
     */
    public function isActive(): bool
    {
        return $this->status === UserStatus::active;
    }

    /**
     * Is
     */
    public function isCreation(): bool
    {
        return $this->is_creation;
    }

    /**
     * Set
     */
    public function setCreation(bool $is_creation = true): self
    {
        $this->is_creation = $is_creation;
        return $this;
    }
}
