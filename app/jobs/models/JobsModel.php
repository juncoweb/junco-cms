<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class JobsModel extends Model
{
    // vars
    protected $db = null;
    protected $job_id = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->db = db();
    }

    /**
     * Toggle
     */
    public function status()
    {
        // data
        $this->filter(POST, ['id' => 'id|array|required:abort']);

        // query
        $this->db->safeExec("UPDATE `#__jobs` SET status = IF(status > 0, 0, 1) WHERE id IN (?..)", $this->data['id']);
    }

    /**
     * Delete
     */
    public function delete()
    {
        // data
        $this->filter(POST, ['id' => 'id|array|required:abort']);

        // query
        $this->db->safeExec("DELETE FROM `#__jobs` WHERE id IN (?..)", $this->data['id']);
    }

    /**
     * Verify Unique
     */
    protected function verifyUniqueSlug(string $current_slug)
    {
        if ($current_slug != $this->data['job_slug']) {
            $current_id = $this->db->safeFind(
                "SELECT id FROM `#__jobs` WHERE job_slug = ?",
                $this->data['job_slug']
            )->fetchColumn();

            if ($current_id && $current_id != $this->job_id) {
                throw new Exception(_t('The slug already exists.'));
            }
        }
    }
}
