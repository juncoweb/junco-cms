<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Users\Token;

use Junco\Utils\Aleatory;

class Token
{
    // const
    const SELECTOR_LENGTH  = 12;
    const VALIDATOR_LENGTH = 20;

    /**
     * Constructor
     */
    public function __construct(protected ?string $token = null) {}

    /**
     * Get
     * 
     * @return string
     */
    public function generate(): string
    {
        return $this->token = Aleatory::token(self::SELECTOR_LENGTH + self::VALIDATOR_LENGTH);
    }

    /**
     * Verify
     *
     * @return bool
     */
    public function verify(): bool
    {
        return $this->token && preg_match('/^[\w-]{' . (self::SELECTOR_LENGTH + self::VALIDATOR_LENGTH) . '}$/i', $this->token);
    }

    /**
     * Get
     *
     * @return string
     */
    public function getSelector(): string
    {
        return substr($this->token, 0, self::SELECTOR_LENGTH);
    }

    /**
     * Get
     *
     * @return string
     */
    public function getValidator(): string
    {
        $validator = substr($this->token, -self::VALIDATOR_LENGTH);

        return hash('sha256', $validator);
    }

    /**
     * Get
     *
     * @return bool
     */
    public function validate(string $validator): bool
    {
        return hash_equals($validator, $this->getValidator());
    }
}
