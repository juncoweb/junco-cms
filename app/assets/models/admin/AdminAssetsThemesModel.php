<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;
use Junco\Assets\Compilation\UrlFixer;

class AdminAssetsThemesModel extends Model
{
    /**
     * Get
     */
    public function getListData()
    {
        $rows = (new AssetsThemes)->getAll();

        if ($rows) {
            $theme = config('frontend.theme');
            $url   = url('admin/assets.variables', ['key' => '%s']);

            foreach ($rows as $i => $row) {
                $rows[$i]['url']        = sprintf($url, $row['key']);
                $rows[$i]['is_default'] = ($row['alias'] . '/' . $row['name']) == $theme ? 1 : 0;
            }
        }

        return ['rows' => $rows];
    }

    /**
     * Get
     */
    public function getCopyData()
    {
        // data
        $this->filter(POST, ['id' => 'array:first|required:abort']);

        //
        $part = explode('-', $this->data['id']);

        return [
            'title' => _t('Copy'),
            'values' => [
                'extension_alias' => $part[0],
                'name'            => $part[1] ?? '',
                'from'            => $this->data['id'],
            ],
            'extensions' => $this->getExtensions(),
        ];
    }

    /**
     * Get
     */
    public function getCreateData()
    {
        return [
            'title' => _t('Create'),
            'values' => null,
            'extensions' => $this->getExtensions(),
        ];
    }

    /**
     * Get
     */
    public function getConfirmCompileData()
    {
        // data
        $this->filter(POST, ['id' => 'array:first|required:abort']);
        $config = config('assets');

        return [
            'fixurl_options' => UrlFixer::getOptions(),
            'values' => $this->data + [
                'minify' => $config['assets.minify'],
                'fixurl' => $config['assets.fixurl'],
            ]
        ];
    }

    /**
     * Get confirm select data
     */
    public function getConfirmSelectData()
    {
        // data
        $this->filter(POST, [
            'id' => 'text|array:first|required:abort',
        ]);

        $id = explode('-', $this->data['id'], 2);
        if (count($id) == 1) {
            $id[] = 'default';
        }
        $id = implode('/', $id);

        return [
            'values' => [
                'id' => $id,
                'disable_explanation' => true,
            ],
            'theme' => $this->data['id'],
            'is_used' => ($id == config('frontend.theme')),
            'explain_is_active' => config('template.explain_assets')
        ];
    }

    /**
     * Get
     */
    public function getConfirmDeleteData()
    {
        // data
        $this->filter(POST, ['id' => 'text|array|required:abort']);

        return $this->data;
    }

    /**
     * Get
     */
    protected function getExtensions()
    {
        return db()->query("
		SELECT extension_alias, extension_name
		FROM `#__extensions`
		ORDER BY extension_name")->fetchAll(Database::FETCH_COLUMN, [0 => 1], ['--- ' . _t('Select') . ' ---']);
    }
}
