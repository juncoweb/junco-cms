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
        'a' => ['name' => 'Application', 'source' => 'app/%s/', 'local' => 'app/%s/'],
        'j' => ['name' => 'Scripts', 'source' => 'cms/scripts/%s/', 'local' => 'cms/scripts/%s/'],
        'k' => ['name' => 'Snippets', 'source' => 'cms/snippets/%s/', 'local' => 'cms/snippets/%s/'],
        'l' => ['name' => 'Libraries', 'source' => 'cms/libraries/%s/', 'local' => 'cms/libraries/%s/'],
        'p' => ['name' => 'Plugins', 'source' => 'cms/plugins/%s/', 'local' => 'cms/plugins/%s/'],
        'm' => ['name' => 'Media', 'source' => 'media/%s/', 'local' => SYSTEM_MEDIA_PATH . '%s/'],
        'v' => ['name' => 'Vendor', 'source' => 'vendor/%s/', 'local' => 'vendor/%s/'],
    ];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->abspath = SYSTEM_ABSPATH;
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
            $dir = sprintf($this->components[$key]['local'], $extension);

            if (is_dir($this->abspath . $dir)) {
                $dirs[$key] = $dir;
            }
        }

        return $dirs;
    }

    /**
     * Returns the local and source paths. It does not check for their existence.
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
                'local' => sprintf($this->components[$key]['local'], $extension),
                'source' => sprintf($this->components[$key]['source'], $extension)
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
}
