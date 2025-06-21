<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class AdminUsersLabelsModel extends Model
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
     * Get
     */
    public function getListData()
    {
        // data
        $this->filter(POST, ['search' => 'text']);

        // query
        if ($this->data['search']) {
            $this->db->where("e.extension_name LIKE %?", $this->data['search']);
        }
        $pagi = $this->db->paginate("
		SELECT [
		 l.id ,
		 l.label_key ,
		 l.label_name ,
		 e.extension_alias ,
		 e.extension_name ,
		 d.is_protected
		]* FROM `#__users_roles_labels` l
		LEFT JOIN `#__extensions` e ON (l.extension_id = e.id)
		LEFT JOIN `#__extensions_developers` d ON (e.developer_id = d.id)
		[WHERE]
		[ORDER BY extension_name]");

        $rows = [];
        foreach ($pagi->fetchAll() as $row) {
            if (!$row['label_name']) {
                $row['label_name'] = $row['extension_name'] . ($row['label_key'] ? ' - ' . ucfirst($row['label_key']) : '');
            }
            if ($row['label_key']) {
                $row['label_key'] = '-' . $row['label_key'];
            }
            $row['label_key'] = $row['extension_alias'] . $row['label_key'];

            $rows[] = $row;
        }

        return $this->data + [
            'rows' => $rows,
            'pagi' => $pagi
        ];
    }

    /**
     * Get
     */
    public function getCreateData()
    {
        // data
        $this->filter(POST, ['num_rows' => 'int|min:1|default:1']);

        return [
            'title' => _t('Create'),
            'values' => array_fill(0, $this->data['num_rows'], null),
            'extensions' => $this->getExtensions(),
            'is_edit' => false,
        ];
    }

    /**
     * Get
     */
    public function getEditData()
    {
        // data
        $this->filter(POST, ['id' => 'id|array|required:abort']);

        // query
        $rows = $this->db->safeFind("
		SELECT
		 id AS label_id,
		 extension_id ,
		 label_key ,
		 label_name ,
		 label_description
		FROM `#__users_roles_labels`
		WHERE id IN ( ?.. )", $this->data['id'])->fetchAll();

        foreach ($rows as $i => $row) {
            $rows[$i] = $row;
        }

        return [
            'title' => _t('Edit'),
            'values' => $rows,
            'extensions' => $this->getExtensions(),
            'is_edit' => true,
        ];
    }

    /**
     * Get
     */
    public function getConfirmDeleteData()
    {
        // data
        $this->filter(POST, ['id' => 'id|array|required:abort']);

        return $this->data;
    }

    /**
     * Get
     */
    protected function getExtensions(): array
    {
        return $this->db->safeFind("
		SELECT e.id, e.extension_name
		FROM `#__extensions` e
		LEFT JOIN `#__extensions_developers` d ON (e.developer_id = d.id)
		WHERE d.is_protected = 0
		ORDER BY e.extension_name")->fetchAll(Database::FETCH_COLUMN, [0 => 1], ['-- ' . _t('Select') . ' --']);
    }
}
