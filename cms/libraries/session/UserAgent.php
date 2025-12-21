<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Session;

class UserAgent
{
    // vars
    protected string $original;
    protected string $platform = 'Unknown';
    protected string $browser = 'Unknown';
    protected string $version = '';

    /**
     * Constructor
     */
    public function __construct(string $user_agent)
    {
        $this->original = $user_agent;

        $user_agent = strtolower($this->original);

        // platform
        foreach (
            [
                'android'   => 'android',
                'linux'     => 'linux',
                'macintosh' => 'mac',
                'mac os x'  => 'mac',
                'windows'   => 'windows',
                'win32'     => 'windows',
            ] as $needle => $value
        ) {
            if (false !== strpos($user_agent, $needle)) {
                $this->platform = $value;
                break;
            }
        }

        // browser
        foreach (
            [
                'opera'     => ['Opera', 'opera/'],
                'opr/'      => ['Opera', 'opr/'],
                'edge'      => ['Edge', 'edge/'],
                'chrome'    => ['Chrome', 'chrome/'],
                'safari'    => ['Safari', 'version/'],
                'firefox'   => ['Firefox', 'firefox/'],
                'msie'      => ['Internet Explorer', 'msie/'],
                'trident/7' => ['Internet Explorer', 'rv:'],
            ] as $needle => $value
        ) {
            if (false !== strpos($user_agent, $needle)) {
                $this->browser = $value[0];
                break;
            }
        }

        // version
        if (
            $this->browser != 'Unknown'
            && (false != ($i = strpos($user_agent, $value[1])))
        ) {
            $version = preg_split('#[^0-9.]#', substr($user_agent, $i + strlen($value[1])), 2);
            $this->version = $version[0];
        }
    }

    /**
     * Get
     * 
     * @return string
     */
    public function getBrowser(): string
    {
        return $this->browser;
    }

    /**
     * Get
     * 
     * @return string
     */
    public function getPlatform(): string
    {
        return $this->platform;
    }

    /**
     * Get
     * 
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Is bot
     */
    public function isBot(): bool
    {
        return $this->original
            && preg_match('/bot|crawl|slurp|spider|mediapartners|curl/i', $this->original);
    }

    /**
     * Compare whith other user agents
     * 
     * @param UserAgent $user_agent
     * @param ?string   $operator
     * 
     * @return int|bool
     */
    public function compareTo(self $user_agent, ?string $operator = null): int|bool
    {
        if ($this->getPlatform() != $user_agent->getPlatform()) {
            return false;
        }

        if ($this->getBrowser() != $user_agent->getBrowser()) {
            return false;
        }
        if (!$this->getBrowser() || !$user_agent->getBrowser()) {
            return false;
        }

        return version_compare($this->getVersion(), $user_agent->getVersion(), $operator);
    }

    /**
     * To String
     * 
     * @return string
     */
    public function __toString(): string
    {
        return $this->original;
    }
}
