<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;
use Junco\Users\LabelsCache;

class UsersLabelsModel extends Model
{
    // vars
    protected $db = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->db = db();
    }

    /**
     * Save
     */
    public function save()
    {
        // data
        $this->filterArray(POST, [
            'extension_id'        => 'id|required',
            'label_key'            => 'text',
            'label_name'        => 'text',
            'label_description'    => 'text',
        ]) or abort();

        $this->filter(POST, [
            'is_edit' => '',
            'label_id' => 'id|array|only_if:is_edit|required:abort'
        ]);

        // security
        $this->security(array_unique(array_column($this->data_array, 'extension_id')));

        // validate
        foreach ($this->data_array as $i => $data) {
            if ($data['label_key']) {
                $this->filterLabelKey($data['label_key'], $i);
            }
            $this->verifyUniqueLabelKey($data['extension_id'], $data['label_key'], $this->data['label_id'][$i] ?? 0, $i);
            $this->data_array[$i] = $data;
        }

        // query
        if ($this->data['is_edit']) {
            $this->db->safeExecAll("UPDATE `#__users_roles_labels` SET ?? WHERE id = ?", $this->data_array, $this->data['label_id']);
        } else {
            $this->db->safeExecAll("INSERT INTO `#__users_roles_labels` (??) VALUES (??)", $this->data_array);
        }

        // cache
        (new LabelsCache)->update();
    }

    /**
     * Delete
     */
    public function delete()
    {
        // data
        $this->filter(POST, ['id' => 'id|array|required:abort']);

        // security
        $this->db->safeFind("
		SELECT COUNT(*)
		FROM `#__users_roles_labels` l
		LEFT JOIN `#__extensions` e ON (l.extension_id = e.id)
		LEFT JOIN `#__extensions_developers` d ON (e.developer_id = d.id)
		WHERE l.id = ?
		AND d.is_protected = 1", $this->data['id'])->fetchColumn() and abort();

        // query
        $this->db->safeExec("DELETE FROM `#__users_roles_labels` WHERE id IN (?..)", $this->data['id']);
        $this->db->safeExec("DELETE FROM `#__users_roles_labels_map` WHERE label_id IN (?..)", $this->data['id']);

        // cache
        (new LabelsCache)->update();
    }

    /**
     * Get
     */
    protected function security(array $extension_id)
    {
        $this->db->safeFind("
		SELECT COUNT(*)
		FROM `#__extensions` e
		LEFT JOIN `#__extensions_developers` d ON (e.developer_id = d.id)
		WHERE e.id = ?
		AND d.is_protected = 1", $extension_id)->fetchColumn() and abort();
    }

    /**
     * Filter
     */
    protected function filterLabelKey(string &$label_key, int $i)
    {
        $label_key = strtolower($label_key);

        if (preg_match('/[^a-z0-9_]/', $label_key)) {
            throw new Exception(_t('The key must be alphanumeric.') . sprintf(' (%d)', $i + 1));
        }
    }

    /**
     * Verify
     */
    protected function verifyUniqueLabelKey(int $extension_id, string $label_key, int $label_id, int $i)
    {
        // query
        $current_id = $this->db->safeFind("
		SELECT id
		FROM `#__users_roles_labels`
		WHERE extension_id = ?
		AND label_key = ?", $extension_id, $label_key)->fetchColumn();

        if ($current_id && $current_id != $label_id) {
            throw new Exception(_t('The key is being used.') . sprintf(' (%d)', $i + 1));
        }
    }
}
