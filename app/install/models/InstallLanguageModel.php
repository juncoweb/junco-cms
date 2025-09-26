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
        $data = $this->filter(POST, ['lang' => 'text']);

        // vars
        $result = (new LanguageHelper)->change($data['lang']);

        if (!$result) {
            return $this->unprocessable(_t('Error! the task has not been realized.'));
        }
    }
}
