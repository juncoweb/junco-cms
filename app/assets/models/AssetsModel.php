<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Assets\JsOptions;
use Junco\Mvc\Model;

class AssetsModel extends Model
{
    // vars
    protected string $key             = '';
    protected string $extension_alias = '';
    protected string $name            = '';
    protected string $type            = '';

    /**
     * Compile
     */
    public function compile()
    {
        // data
        $this->filter(POST, [
            'keys'       => 'array|required:abort',
            'minify'     => 'bool',
            'fixurl'     => 'int',
            'precompile' => 'int',
        ]);

        (new AssetsBasic)->compileFromKeys(
            $this->data['keys'],
            $this->data['minify'],
            $this->data['fixurl'],
            $this->data['precompile']
        );
    }

    /**
     * Inspect
     */
    public function inspectAll()
    {
        // data
        $this->filter(POST, ['id' => 'array|required:abort']);

        (new AssetsInspector)->inspectAll($this->data['id']);
    }

    /**
     * Save
     */
    public function save()
    {
        // data
        $this->filter(POST, [
            'key'             => 'text',
            'extension_alias' => 'text',
            'name'            => 'text',
            'type'            => 'in:css,js|required',
            //
            'assets'          => '',
            'default_assets'  => '',
            'to_verify'       => 'bool:0/1',
        ]);

        // extract
        $this->extract('key', 'extension_alias', 'name', 'type');

        // validate
        if (!$this->extension_alias) {
            return $this->unprocessable(_t('Please, fill in the extension.'));
        }

        // store
        (new AssetsStorage)->store(
            $this->extension_alias . ($this->name ? '-' . $this->name : '') . '.' . $this->type,
            $this->data,
            $this->key
        );
    }

    /**
     * Options
     */
    public function options()
    {
        // data
        $this->filter(POST, ['contents' => '']);

        if (!(new JsOptions)->put($this->data['contents'])) {
            return $this->unprocessable(_t('Error! the task has not been realized.'));
        }
    }

    /**
     * Delete
     */
    public function delete()
    {
        // data
        $this->filter(POST, ['keys' => 'array|required:abort']);

        (new AssetsStorage)->removeAll($this->data['keys']);
    }
}
