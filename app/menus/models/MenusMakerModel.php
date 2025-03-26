<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class MenusMakerModel extends Model
{
    // vars
    protected $db = null;
    protected array $keys = [
        'backend'    => 'backend-Default',
        'frontend'    => 'frontend-Main',
        'dashboard'    => 'dashboard',
        'my'         => 'my-Default',
        'audit'        => 'my-Default',
        'settings'    => 'settings-Default',
        'sitemap'    => 'sitemap-Default',
    ];
    protected array $folders = [
        'Contents',
        'Media',
        'More',
        'Security',
        'Site spaces',
        'System',
        'Templates',
        'Tools',
        'User spaces',
        'Usys'
    ];
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->db = db();
    }

    /**
     * Get
     */
    public function getConfirmData()
    {
        return [
            'values' => $this->data,
            'extensions' => $this->getExtensions(),
            'folders' => $this->getFolders(),
            'keys' => $this->getKeys()
        ];
    }

    /**
     * Store
     */
    public function store()
    {
        // data
        $this->filter(POST, [
            'extension_id'        => 'id|required',
            'menu_title'        => '',
            'menu_subcomponent'    => '',
            'menu_keys'            => 'array|required',
            'menu_folder'        => 'in:Contents,Media,More,Security,Site spaces,System,Templates,Tools,User spaces,Usys|required:abort',
            'menu_image'        => 'text',
        ]);

        $extension = $this->getExtension($this->data['extension_id']) or abort();

        if (!$this->validateSubcomponent($this->data['menu_subcomponent'])) {
            throw new Exception(sprintf(_t('The «%s» is incorrect.'), _t('Component')));
        }

        if (!$this->data['menu_title']) {
            $this->data['menu_title'] = $extension['name'] ?: $extension['alias'];
        }

        if (!$this->data['menu_image']) {
            $this->data['menu_image'] = 'fa-solid fa-file-lines';
        }

        $component = $extension['alias'];
        $menu_hash = $extension['alias'];

        if ($this->data['menu_subcomponent']) {
            $component .= '.' . $this->data['menu_subcomponent'];
            $menu_hash .= '-' . $this->data['menu_subcomponent'];
        }

        //
        $data = [];
        foreach ($this->data['menu_keys'] as $key) {
            $data[] = [
                'menu_key'        => $this->getKey($key),
                'menu_path'        => $this->getPath($key, $this->data['menu_folder'], $this->data['menu_title']),
                'menu_order'    => $this->getOrder($key),
                'menu_url'        => $this->getUrl($key, $component),
                'menu_image'    => $this->getImage($key, $this->data['menu_image']),
                'menu_hash'        => $menu_hash,
                'menu_params'    => '',
                'status'        => 1
            ];
        }

        $xdata = null;
        $data = [
            'data' => $data,
            'extension_id' => $extension['id'],
            'extension_alias' => $extension['alias']
        ];

        Plugins::get('xdata', 'import', 'menus')->run($xdata, $data);
    }

    /**
     * Get
     */
    protected function getExtensions()
    {
        // extensions
        return $this->db->safeFind("
		SELECT id, extension_name
		FROM `#__extensions`
		ORDER BY extension_name")->fetchAll(Database::FETCH_COLUMN, [0 => 1], ['--- ' . _t('Select') . ' ---']);
    }

    /**
     * Get
     */
    protected function getExtension(int $extension_id): false|array
    {
        return $this->db->safeFind("
		SELECT
		 id,
		 extension_alias AS alias,
		 extension_name AS name
		FROM `#__extensions`
		WHERE id = ?", $extension_id)->fetch();
    }

    /**
     * Get
     */
    protected function getFolders(): array
    {
        return array_combine($this->folders, $this->folders);
    }

    /**
     * Get
     */
    protected function getKeys(): array
    {
        $keys = array_keys($this->keys);

        return array_combine($keys, array_map('ucfirst', $keys));
    }

    /**
     * Validate
     */
    protected function validateSubcomponent(string $subcomponent): bool
    {
        if (!$subcomponent) {
            return true;
        }

        return preg_match('/^[a-z][a-z0-9]*$/', $subcomponent);
    }

    /**
     * Get
     */
    protected function getKey(string $key): string
    {
        return $this->keys[$key] ?? abort();
    }

    /**
     * Get
     */
    protected function getPath(string $key, string $menu_folder, string $menu_title): string
    {
        if (in_array($key, ['backend', 'settings'])) {
            return $menu_folder . '|' . $menu_title;
        }

        return $menu_title;
    }

    /**
     * Get
     */
    protected function getOrder(string $key): int
    {
        if (in_array($key, ['frontend', 'my'])) {
            return 10;
        }

        if ($key === 'audit') {
            return 20;
        }

        return 0;
    }

    /**
     * Get
     */
    protected function getUrl(string $key, string $component): string
    {
        switch ($key) {
            case 'settings':
                return sprintf('admin/settings,key=%s', $component);

            case 'backend':
            case 'dashboard':
                return sprintf('admin/%s,', $component);

            case 'my':
                return sprintf('my/%s,', $component);

            case 'audit':
                return sprintf('audit/%s,', $component);
        }

        return sprintf('/%s,', $component);
    }

    /**
     * Get
     */
    protected function getImage(string $key, string $menu_image): string
    {
        if (in_array($key, ['backend', 'frontend'])) {
            return '';
        }

        return $menu_image;
    }
}
