<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class AssetsThemesModel extends Model
{
    /**
     * Save
     */
    public function save()
    {
        // data
        $this->filter(POST, [
            'extension_alias' => '',
            'name' => '',
            'from' => '',
        ]);

        // validate
        if (!$this->data['extension_alias']) {
            return $this->unprocessable(_t('Please, fill in the extension.'));
        }

        $key    = $this->data['extension_alias'] . '-' . ($this->data['name'] ?: 'default');
        $themes = new AssetsThemes;

        if ($this->data['from']) {
            $themes->copy($this->data['from'], $key);
        } else {
            if ($themes->has($key)) {
                return $this->unprocessable(_t('The theme already exists.'));
            }

            $themes->save($key);
        }
    }

    /**
     * Compile
     */
    public function compile()
    {
        // data
        $this->filter(POST, [
            'id'     => 'required:abort',
            'minify' => 'bool',
            'fixurl' => 'int'
        ]);

        (new AssetsThemes)->compileTheme(
            $this->data['id'],
            $this->data['minify'],
            $this->data['fixurl']
        );
    }

    /**
     * Delete
     */
    public function delete()
    {
        // data
        $this->filter(POST, ['themes' => 'array|required:abort']);

        (new AssetsThemes)->delete($this->data['themes']);
    }

    /**
     * Select
     */
    public function select()
    {
        // data
        $this->filter(POST, [
            'id' => 'text|required:abort',
            'disable_explanation' => 'bool'
        ]);

        // verify
        if ($this->data['id'] == config('frontend.theme')) {
            $this->data['id'] = '';
        } else {
            $themes = (new AssetsThemes)->scanAll();

            if (!array_key_exists($this->data['id'], $themes)) {
                return $this->unprocessable(_t('The theme does not exist.'));
            }
        }

        if ($this->data['disable_explanation']) {
            (new Settings('template'))->update([
                'explain_assets' => false
            ]);
        }

        (new Settings('frontend'))->update([
            'theme' => $this->data['id']
        ]);
    }
}
