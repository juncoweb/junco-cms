<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Assets\JsOptions;
use Junco\Mvc\Model;

class AssetsModel extends Model
{
    /**
     * Compile
     */
    public function compile()
    {
        $data = $this->filter(POST, [
            'keys'       => 'array|required:abort',
            'minify'     => 'bool',
            'fixurl'     => 'int',
            'precompile' => 'int',
        ]);

        (new AssetsBasic)->compileFromKeys(
            $data['keys'],
            $data['minify'],
            $data['fixurl'],
            $data['precompile']
        );
    }

    /**
     * Inspect
     */
    public function inspectAll()
    {
        $data = $this->filter(POST, ['id' => 'array|required:abort']);

        (new AssetsInspector)->inspectAll($data['id']);
    }

    /**
     * Save
     */
    public function save()
    {
        $data = $this->filter(POST, [
            'key'             => 'text',
            'extension_alias' => 'text',
            'name'            => 'text',
            'type'            => 'in:css,js|required',
            //
            'assets'          => '',
            'default_assets'  => '',
            'to_verify'       => 'bool:0/1',
        ]);

        // slice
        $key             = $this->slice($data, 'key');
        $extension_alias = $this->slice($data, 'extension_alias');
        $name            = $this->slice($data, 'name');
        $type            = $this->slice($data, 'type');
        $newKey          = $extension_alias . ($name ? '-' . $name : '') . '.' . $type;

        // validate
        if (!$extension_alias) {
            return $this->unprocessable(_t('Please, fill in the extension.'));
        }

        // store
        (new AssetsStorage)->store($newKey, $data, $key);
    }

    /**
     * Options
     */
    public function options()
    {
        $data = $this->filter(POST, ['contents' => '']);

        if (!(new JsOptions)->put($data['contents'])) {
            return $this->unprocessable(_t('Error! the task has not been realized.'));
        }
    }

    /**
     * Delete
     */
    public function delete()
    {
        $data = $this->filter(POST, ['keys' => 'array|required:abort']);

        (new AssetsStorage)->removeAll($data['keys']);
    }
}
