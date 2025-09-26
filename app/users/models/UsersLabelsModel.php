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
    protected $db;

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
        $data_array = $this->filterArray(POST, [
            'extension_id'      => 'id|required',
            'label_key'         => 'text',
            'label_name'        => 'text',
            'label_description' => 'text',
        ]) or abort();

        $data = $this->filter(POST, [
            'is_edit' => '',
            'label_id' => 'id|array|only_if:is_edit|required:abort'
        ]);

        // security
        $this->isProtected(array_unique(array_column($data_array, 'extension_id'))) and abort();

        // validate
        foreach ($data_array as $i => $row) {
            if ($row['label_key']) {
                $row['label_key'] = $this->sanitizeLabelKey($row['label_key']);

                if (!$row['label_key']) {
                    return $this->unprocessable(_t('The key must be alphanumeric.') . sprintf(' (%d)', $i + 1));
                }
            }

            if (!$this->isUniqueLabelKey($row['extension_id'], $row['label_key'], $data['label_id'][$i] ?? 0)) {
                return $this->unprocessable(_t('The key is being used.') . sprintf(' (%d)', $i + 1));
            }

            $data_array[$i] = $row;
        }

        // query
        if ($data['is_edit']) {
            $this->db->execAll("UPDATE `#__users_roles_labels` SET ?? WHERE id = ?", $data_array, $data['label_id']);
        } else {
            $this->db->execAll("INSERT INTO `#__users_roles_labels` (??) VALUES (??)", $data_array);
        }

        // cache
        (new LabelsCache)->update();
    }

    /**
     * Delete
     */
    public function delete()
    {
        $data = $this->filter(POST, ['id' => 'id|array|required:abort']);

        // security
        $this->isProtectedFromLabelId($data['id']) and abort();

        // query
        $this->db->exec("DELETE FROM `#__users_roles_labels` WHERE id IN (?..)", $data['id']);
        $this->db->exec("DELETE FROM `#__users_roles_labels_map` WHERE label_id IN (?..)", $data['id']);

        // cache
        (new LabelsCache)->update();
    }

    /**
     * Sanitize
     */
    protected function sanitizeLabelKey(string $label_key): string
    {
        return preg_match('/[^a-zA-Z0-9_]/', $label_key)
            ? ''
            : strtolower($label_key);
    }

    /**
     * Verify
     */
    protected function isUniqueLabelKey(int $extension_id, string $label_key, int $label_id): bool
    {
        $current_id = $this->db->query("
		SELECT id
		FROM `#__users_roles_labels`
		WHERE extension_id = ?
		AND label_key = ?", $extension_id, $label_key)->fetchColumn();

        return !$current_id || $current_id == $label_id;
    }

    /**
     * Get
     */
    protected function isProtected(array $extension_id): bool
    {
        return (bool)$this->db->query("
		SELECT COUNT(*)
		FROM `#__extensions` e
		LEFT JOIN `#__extensions_developers` d ON (e.developer_id = d.id)
		WHERE e.id = ?
		AND d.is_protected = 1", $extension_id)->fetchColumn();
    }

    /**
     * Get
     */
    protected function isProtectedFromLabelId(array $label_id): bool
    {
        return (bool)$this->db->query("
		SELECT COUNT(*)
		FROM `#__users_roles_labels` l
		LEFT JOIN `#__extensions` e ON (l.extension_id = e.id)
		LEFT JOIN `#__extensions_developers` d ON (e.developer_id = d.id)
		WHERE l.id IN ( ?.. )
		AND d.is_protected = 1", $label_id)->fetchColumn();
    }
}
