<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class InstallLanguageModel extends Model
{
    /**
     * Change language
     */
    public function change()
    {
        // data
        $this->filter(POST, ['lang' => 'text']);

        // vars
        $result = (new LanguageHelper)->change($this->data['lang']);

        if (!$result) {
            throw new Exception(_t('Error! the task has not been realized.'));
        }
    }
}
