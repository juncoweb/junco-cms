<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Extensions;

class Components
{
    // vars
    protected string $abspath;
    protected array  $components = [
        'a' => [
            'name' => 'Application',
            'path' => 'app/%s/'
        ],
        'j' => [
            'name' => 'Scripts',
            'path' => 'cms/scripts/%s/'
        ],
        'k' => [
            'name' => 'Snippets',
            'path' => 'cms/snippets/%s/'
        ],
        'l' => [
            'name' => 'Libraries',
            'path' => 'cms/libraries/%s/'
        ],
        'p' => [
            'name' => 'Plugins',
            'path' => 'cms/plugins/%s/'
        ],
        'm' => [
            'name' => 'Media',
            'path' => 'media/%s/',
            'local' => SYSTEM_MEDIA_PATH . '%s/'
        ],
        'v' => [
            'name' => 'Vendor',
            'path' => 'vendor/%s/'
        ],
    ];

    /**
     * Constructor
     */
    public function __construct(?string $abspath = null)
    {
        $this->abspath = $abspath ?? SYSTEM_ABSPATH;
    }

    /**
     * Get
     * 
     * @param string|array $keys
     * 
     * @return array
     */
    public function getKeys(string|array $keys = ''): array
    {
        $current = array_keys($this->components);

        if ($keys) {
            if (is_string($keys)) {
                $keys = str_split($keys);
            }

            return array_values(
                array_intersect($current, $keys)
            );
        }

        return $current;
    }

    /**
     * Get
     * 
     * @return array
     */
    public function getNames(): array
    {
        return array_combine(
            array_keys($this->components),
            array_column($this->components, 'name')
        );
    }

    /**
     * Returns all existing local paths.
     * 
     * @param string       $extension
     * @param string|array $keys
     * 
     * @return array
     */
    public function getDirectories(string $extension, string|array $keys = ''): array
    {
        $keys = $this->getKeys($keys);
        $dirs = [];

        foreach ($keys as $key) {
            $this->components[$key]['local'] ??= $this->components[$key]['path'];
            $dir = sprintf($this->components[$key]['local'], $extension);

            if ($this->isDir($dir)) {
                $dirs[$key] = $dir;
            }
        }

        return $dirs;
    }

    /**
     * Returns the local and default paths. It does not check for their existence.
     * 
     * @param string       $extension
     * @param string|array $keys
     * 
     * @return array
     */
    public function fetchAll(string $extension, string|array $keys = ''): array
    {
        $keys = $this->getKeys($keys);
        $rows = [];

        foreach ($keys as $key) {
            $rows[$key] = [
                'source' => sprintf($this->components[$key]['path'], $extension),
                'local' => sprintf($this->components[$key]['local'] ??= $this->components[$key]['path'], $extension)
            ];
        }

        return $rows;
    }

    /**
     * Get
     * 
     * @return array
     */
    public function getCleanables(): array
    {
        return ['a', 'j', 'k', 'l', 'p', 'v'];
    }

    /**
     * Is directory
     * 
     * @param string $dir
     * 
     * @return bool
     */
    protected function isDir(string $dir): bool
    {
        return is_dir($this->abspath . $dir);
    }
}
