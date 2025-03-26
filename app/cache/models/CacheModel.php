<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
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
        // data
        $this->filter(POST, ['search' => 'text']);

        // vars
        $keys = cache()->getKeys();

        if (
            $keys
            && $this->data['search']
            && preg_match('@[\w-]+@', preg_quote($this->data['search'], '@'))
        ) {
            $filter = '@' . $this->data['search'] . '@i';

            foreach ($keys as $index => $has) {
                if (!preg_match($filter, $has)) {
                    unset($keys[$index]);
                }
            }
        }

        return $this->data + ['keys' => $keys];
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
     * Delete
     */
    public function delete()
    {
        // data
        $this->filter(POST, ['keys' => 'array|required:abort']);

        if (!cache()->deleteMultiple($this->data['keys'])) {
            throw new Exception(_t('Error! the task has not been realized.'));
        }
    }
}
