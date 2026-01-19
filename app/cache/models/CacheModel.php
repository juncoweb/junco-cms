<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class CacheModel extends Model
{
    /**
     * Get
     */
    public function getListData()
    {
        $data = $this->filter(POST, ['search' => 'text']);

        // vars
        $keys = cache()->getKeys();

        if (
            $keys
            && $data['search']
            && preg_match('@[\w-]+@', preg_quote($data['search'], '@'))
        ) {
            $filter = '@' . $data['search'] . '@i';

            foreach ($keys as $index => $has) {
                if (!preg_match($filter, $has)) {
                    unset($keys[$index]);
                }
            }
        }

        $rows = [];
        foreach ($keys as $key) {
            $rows[] = [
                'id' => $key,
                'name' => $key
            ];
        }

        return $data + ['rows' => $rows];
    }

    /**
     * Get
     */
    public function getConfirmDeleteData()
    {
        return $this->filter(POST, ['id' => 'array|required:abort']);
    }

    /**
     * Delete
     */
    public function delete()
    {
        $data = $this->filter(POST, ['keys' => 'array|required:abort']);

        cache()->deleteMultiple($data['keys']);
    }
}
