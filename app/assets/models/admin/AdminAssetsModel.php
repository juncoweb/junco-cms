<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;
use Junco\Assets\Compilation\ScssCompiler;
use Junco\Assets\Compilation\UrlFixer;
use Junco\Assets\JsOptions;

class AdminAssetsModel extends Model
{
    // vars
    protected $assets = null;

    /**
     * Get
     */
    public function getConfirmCompileData()
    {
        // data
        $this->filter(POST, ['id' => 'array|required:abort']);

        $config = config('assets');
        return [
            'fixurl_options' => UrlFixer::getOptions(),
            'precompile_options' => ScssCompiler::getOptions(),
            'values' => [
                'minify'     => $config['assets.minify'],
                'fixurl'     => $config['assets.fixurl'],
                'precompile' => $config['assets.precompile'],
                'keys'       => $this->data['id'],
            ]
        ];
    }

    /**
     * Get
     */
    public function getListData()
    {
        // data
        $this->filter(POST, [
            'search'  => 'text',
            'type'    => '',
            'verify'  => 'bool',
            'compare' => 'bool'
        ]);

        // extract
        extract($this->data);

        if (
            $search
            && '#' != $search
            && '#' == substr($search, 0, 1)
        ) {
            $find   = substr($search, 1);
            $search = null;
        } else {
            $find   = '';
        }

        // vars
        $types = [_t('All'), 'css' => 'Css', 'js' => 'Js'];

        if ($type && !isset($types[$type])) {
            $type = false;
        }

        $rows = (new AssetsBasic)->fetchAll(function ($data) use ($search, $type, $compare, $verify, $find) {
            return !(
                ($search && false === strpos($data['name'], $search))
                || ($type && $type != $data['type'])
                || ($compare && $data['assets'] == $data['default_assets'])
                || ($verify && !$data['to_verify'])
                || ($find && false === strpos($data['assets'], $find))
            );
        });

        // query
        $pagi = new Pagination();
        $pagi->slice($rows);

        return $this->data + [
            'types' => $types,
            'pagi' => $pagi,
        ];
    }

    /**
     * Get
     */
    public function getCreateData()
    {
        return [
            'title' => _t('Create'),
            'values' => ['status' => true],
            'extensions' => $this->getExtensions(),
            'is_edit' => false,
        ];
    }

    /**
     * Get
     */
    public function getEditData()
    {
        // data
        $this->filter(POST, ['id' => 'array:first|required:abort']);

        $data = (new AssetsBasic)->fetch($this->data['id']);

        if ($data) {
            $name = explode('-', $data['name'], 2);
            $data = array_merge($data, [
                'extension_alias' => $name[0],
                'name'            => $name[1] ?? ''
            ]);
        }

        return [
            'title' => _t('Edit'),
            'values' => $data,
            'extensions' => $this->getExtensions(),
            'is_edit' => true,
        ];
    }

    /**
     * Get
     */
    public function getConfirmOptionsData()
    {
        $contents = (new JsOptions)->get();

        return [
            'values' => [
                'contents' => htmlspecialchars($contents)
            ]
        ];
    }

    /**
     * Get
     */
    public function getConfirmDeleteData()
    {
        // data
        $this->filter(POST, ['id' => 'array|required:abort']);

        return $this->data;
    }

    /**
     * Get
     */
    protected function getExtensions()
    {
        return db()->safeFind("
		SELECT extension_alias, extension_name
		FROM `#__extensions`
		ORDER BY extension_name")->fetchAll(Database::FETCH_COLUMN, [0 => 1], ['--- ' . _t('Select') . ' ---']);
    }
}
