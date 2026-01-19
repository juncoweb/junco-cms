<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
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
        $data = $this->filter(POST, [
            'extension_alias' => '',
            'name' => '',
            'from' => '',
        ]);

        // validate
        if (!$data['extension_alias']) {
            return $this->unprocessable(_t('Please, fill in the extension.'));
        }

        $key    = $data['extension_alias'] . '-' . ($data['name'] ?: 'default');
        $themes = new AssetsThemes;

        if ($data['from']) {
            $themes->copy($data['from'], $key);
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
        $data = $this->filter(POST, [
            'id'     => 'required:abort',
            'minify' => 'bool',
            'fixurl' => 'int'
        ]);

        (new AssetsThemes)->compileTheme(
            $data['id'],
            $data['minify'],
            $data['fixurl']
        );
    }

    /**
     * Delete
     */
    public function delete()
    {
        $data = $this->filter(POST, ['themes' => 'array|required:abort']);

        (new AssetsThemes)->delete($data['themes']);
    }

    /**
     * Select
     */
    public function select()
    {
        $data = $this->filter(POST, [
            'id' => 'text|required:abort',
            'disable_explanation' => 'bool'
        ]);

        // verify
        if ($data['id'] == config('frontend.theme')) {
            $data['id'] = '';
        } else {
            $themes = (new AssetsThemes)->scanAll();

            if (!array_key_exists($data['id'], $themes)) {
                return $this->unprocessable(_t('The theme does not exist.'));
            }
        }

        if ($data['disable_explanation']) {
            (new Settings('template'))->update([
                'explain_assets' => false
            ]);
        }

        (new Settings('frontend'))->update([
            'theme' => $data['id']
        ]);
    }
}
