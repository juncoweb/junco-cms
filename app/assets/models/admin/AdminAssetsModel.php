<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
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
        $data = $this->filter(POST, ['id' => 'array|required:abort']);

        return [
            'fixurl_options' => UrlFixer::getOptions(),
            'precompile_options' => ScssCompiler::getOptions(),
            'values' => [
                'minify'     => config('assets.minify'),
                'fixurl'     => config('assets.fixurl'),
                'precompile' => config('assets.precompile'),
                'keys'       => $data['id'],
            ]
        ];
    }

    /**
     * Get
     */
    public function getListData()
    {
        $data = $this->filter(POST, [
            'search'  => 'text',
            'type'    => '',
            'verify'  => 'bool',
            'compare' => 'bool'
        ]);

        extract($data);

        // vars
        $find = $this->getFindFromSearch($search);
        $types = $this->getTypes();

        if ($type && !isset($types[$type])) {
            $type = false;
        }

        $rows = (new AssetsBasic)->fetchAll(function ($row) use ($search, $type, $compare, $verify, $find) {
            return !(
                ($search && false === strpos($row['name'], $search))
                || ($type && $type != $row['type'])
                || ($compare && $row['assets'] == $row['default_assets'])
                || ($verify && !$row['to_verify'])
                || ($find && false === strpos($row['assets'], $find))
            );
        });

        // query
        $pagi = new Pagination();
        $pagi->slice($rows);

        $rows = [];
        foreach ($pagi->fetchAll() as $row) {
            $rows[] = $row;
        }

        return $data + [
            'types' => $types,
            'rows'  => $rows,
            'pagi'  => $pagi,
        ];
    }

    /**
     * Get
     */
    public function getCreateData()
    {
        return [
            'title'      => _t('Create'),
            'values'     => ['status' => true],
            'extensions' => $this->getExtensions(),
            'is_edit'    => false,
        ];
    }

    /**
     * Get
     */
    public function getEditData()
    {
        $data = $this->filter(POST, ['id' => 'array:first|required:abort']);

        //
        $data = (new AssetsBasic)->fetch($data['id']) or abort();

        if ($data) {
            $data = $this->explodeName($data);
        }

        return [
            'title'      => _t('Edit'),
            'values'     => $data,
            'extensions' => $this->getExtensions(),
            'is_edit'    => true,
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
        return $this->filter(POST, ['id' => 'array|required:abort']);
    }

    /**
     * Get
     */
    protected function getTypes(): array
    {
        return [
            ''    => _t('All'),
            'css' => 'Css',
            'js'  => 'Js'
        ];
    }

    /**
     * Get
     */
    protected function getFindFromSearch(string &$search): string
    {
        if (!$search || $search[0] != '#') {
            return '';
        }

        $find = substr($search, 1);
        $search = '';

        return $find;
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

    /**
     * Get
     */
    protected function explodeName(array $data): array
    {
        $name = explode('-', $data['name'], 2);

        return array_merge($data, [
            'extension_alias' => $name[0],
            'name'            => $name[1] ?? ''
        ]);
    }
}
