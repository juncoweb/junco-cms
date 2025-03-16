<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;
use Junco\Extensions\Extensions;
use Junco\Extensions\Compiler\PreCompiler;
use Junco\Extensions\Updater\Carrier;

class AdminExtensionsModel extends Model
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
	 * Get
	 */
	public function getIndexData()
	{
		return [
			'developer_mode' => SYSTEM_DEVELOPER_MODE,
		];
	}

	/**
	 * Get
	 */
	public function getListData()
	{
		// data
		$this->filter(POST, [
			'search'		=> 'text',
			'status'		=> 'text|in:all,public,private,deprecated|default:all',
			'developer_id'	=> 'id',
			'option'		=> 'int',
		]);

		// query
		if ($this->data['option'] == 1) {
			$this->db->where("u.has_update IS NOT NULL");
		}
		if ($this->data['option'] == 2) {
			$this->db->where("e.package_id = -1");
		}
		$sql_table = '';
		if ($this->data['option'] == 3) {
			$sql_table = "LEFT JOIN (
				SELECT COUNT(*) AS total, extension_id
				FROM `#__extensions_changes`
				WHERE status = 0
				GROUP BY (extension_id)
			) AS c ON (c.extension_id = e.id)";
			$this->db->where("c.total > 0");
		}
		if ($this->data['search']) {
			$this->db->where("e.extension_alias LIKE %?|e.extension_name LIKE %?", $this->data['search']);
		}
		if ($this->data['developer_id']) {
			$this->db->where("e.developer_id = ?", $this->data['developer_id']);
		}
		if ($this->data['status'] != 'all') {
			$this->db->where("e.status = ?", $this->data['status']);
		}
		$pagi = $this->db->paginate("
		SELECT [
		 e.id ,
		 e.developer_id ,
		 e.extension_alias ,
		 e.extension_name ,
		 e.extension_version ,
		 e.extension_credits ,
		 e.extension_license ,
		 e.extension_abstract ,
		 e.components ,
		 e.db_queries ,
		 e.xdata ,
		 e.status ,
		 e.package_id ,
		 d.developer_name ,
		 d.project_url ,
		 d.is_protected ,
		 IF (u.has_update, TRUE, FALSE) AS has_update
		]* FROM `#__extensions` e
		LEFT JOIN `#__extensions_developers` d ON (e.developer_id = d.id)
		LEFT JOIN (
			SELECT
			 extension_id ,
			 TRUE AS has_update
			FROM `#__extensions_updates`
			WHERE status = 'available'
			GROUP BY extension_id
		) u ON (u.extension_id = e.id)
		$sql_table
		[WHERE]
		ORDER BY extension_name");
		$rows = $pagi->fetchAll();

		if ($rows && SYSTEM_DEVELOPER_MODE) {
			$this->setDevelopersData($rows);
		}

		return $this->data + [
			'developers'		=> $this->getListDevelopers(),
			'developer_mode'	=> SYSTEM_DEVELOPER_MODE,
			'rows'				=> $rows,
			'pagi'				=> $pagi
		];
	}

	/**
	 * Get
	 */
	public function getCreateData()
	{
		$developers = $this->getDevelopers();

		return [
			'title' => _t('Create'),
			'values' => [
				'extension_require' => $this->getSince(),
				'developer_id' => array_key_last($developers)
			],
			'is_edit' => false,
			'is_protected' => false,
			'developers' => $developers,
			'can_be_a_package' => true,
		];
	}

	/**
	 * Get
	 */
	public function getEditData()
	{
		// data
		$this->filter(POST, ['id' => 'id|array:first|required:abort']);

		// query
		$data = $this->db->safeFind("
		SELECT
		 id ,
		 developer_id ,
		 extension_alias ,
		 extension_name ,
		 extension_credits ,
		 extension_license ,
		 extension_abstract ,
		 extension_require ,
		 package_id ,
		 IF (package_id = -1, TRUE, FALSE) AS is_package ,
		 IF(package_id > 0, (
			SELECT a.extension_name 
			FROM `#__extensions` a
			WHERE e.package_id = a.id
		 ), NULL) AS annexed_to,
		 (SELECT is_protected FROM `#__extensions_developers` d WHERE developer_id = d.id) AS is_protected
		FROM `#__extensions` e WHERE id = ?", $this->data['id'])->fetch() or abort();

		return [
			'title' => _t('Edit'),
			'values' => $data,
			'is_edit' => true,
			'is_protected' => $data['is_protected'],
			'developers' => $this->getDevelopers(),
			'can_be_a_package' => !($data['package_id'] > 0),
			'annexed_to' => $data['annexed_to']
		];
	}

	/**
	 * Get confirm status data
	 */
	public function getConfirmStatusData()
	{
		// data
		$this->filter(POST, [
			'id' => 'id|array|required:abort',
			'status' => 'in:public,private,deprecated|required:abort'
		]);

		switch ($this->data['status']) {
			case 'public':
				$this->data['status_name'] = _t('Public');
				break;

			case 'private':
				$this->data['status_name'] = _t('Private');
				break;

			case 'deprecated':
				$this->data['status_name'] = _t('Deprecated');
				break;
		}

		return $this->data;
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
	public function getAppendData()
	{
		// data
		$this->filter(POST, ['id' => 'id|array:first|required:abort']);

		// query
		$data = $this->db->safeFind("
		SELECT
		 id ,
		 developer_id ,
		 extension_name
		FROM `#__extensions`
		WHERE id = ?", $this->data['id'])->fetch() or abort();

		// query
		$rows = $this->db->safeFind(
			"
		SELECT
		 id,
		 extension_name ,
		 package_id
		FROM `#__extensions`
		WHERE developer_id = ?
		AND package_id IN (0, ?)
		ORDER BY extension_name",
			$data['developer_id'],
			$data['id']
		)->fetchAll();

		$extensions = [];
		$selected = [];
		foreach ($rows as $row) {
			$extensions[$row['id']] = $row['extension_name'];

			if ($row['package_id'] > 0) {
				$selected[] = $row['id'];
			}
		}

		return [
			'title' => $data['extension_name'],
			'values' => [
				'id' => $data['id'],
				'extensions' => $selected
			],
			'extensions' => $extensions
		];
	}

	/**
	 * Get
	 */
	public function getCompileData()
	{
		// data
		$this->filter(POST, [
			'id'				=> 'id|array|required:abort',
			'update_versions'	=> 'text|in:no,yes',
			'update_requires'	=> 'text|in:no,yes',
		]);

		// compiler
		$compiler = new PreCompiler(
			$this->data['update_versions'],
			$this->data['update_requires']
		);
		$data = [
			'enter'  => true,
			'status' => -3,
		];

		foreach ($this->data['id'] as $package_id) {
			$status = $compiler->getPackage($package_id);

			if ($data['status'] < $status) {
				$data['status'] = $status;
			}
		}
		$data['errors'] = $compiler->getErrors();

		switch ($data['status']) {
			default: // there are errors.
			case 0:
				$data['enter'] = false;
				return $data;

			case -1: // Question: update requires
				return $data + [
					'changes' => $compiler->getChanges(),
					'values' => [
						'update_requires'	=> 'yes',
						'id'				=> $this->data['id']
					],
				];

			case -2: // Question: update versions
				return $data + [
					'updates' => $compiler->getUpdates(),
					'values' => [
						'update_versions'	=> 'yes',
						'update_requires'	=> $this->data['update_requires'],
						'id'				=> $this->data['id']
					],
				];

			case -3:
				return $data + [
					'install_package'	=> $compiler->getInstallPackage(),
					'repairs'			=> $compiler->getRepairs(),
					'compiler_plugins'	=> $compiler->getplugins(),
					'values' => [
						'package_name_format'	=> true,
						'compress'				=> true,
						'id'					=> $this->data['id'],
					],
				];
		}
	}

	/**
	 * Get
	 */
	public function getDbHistoryData()
	{
		// data
		$this->filter(POST, ['id' => 'id|array:first|required:abort']);

		// query
		$extension = $this->db->safeFind("
		SELECT
		extension_alias AS alias,
		extension_name AS name,
		db_history ,
		(SELECT is_protected FROM `#__extensions_developers` d WHERE developer_id = d.id) AS is_protected
		FROM `#__extensions` WHERE id = ?", $this->data['id'])->fetch();

		// vars
		$db_history = $extension['db_history'] ? (array)json_decode($extension['db_history'], true) : [];
		$_queries = Extensions::getQueries($extension['alias']);
		$queries = [];

		foreach ($_queries as $value) {
			$value = explode(':', trim($value));
			$Name = $value[1] ?? $value[0];
			$Type = isset($value[1]) ? $value[0] : 'TABLE';

			if ($Type == 'TABLE') {
				// query
				$rows = $this->db->getSchema()->fields()->fetchAll($Name);

				$Fields = [];
				foreach ($rows as $row) {
					$Field = $row['Field'];
					$key = "db_history[$Type][$Name][Fields][$Field][History]";

					if (isset($db_history[$Type][$Name]['Fields'][$Field]['History'])) {
						$value = $db_history[$Type][$Name]['Fields'][$Field]['History'];
						unset($db_history[$Type][$Name]['Fields'][$Field]);
						if (is_array($value)) {
							$value = implode(',', $value);
						}
					} else {
						$value = '';
					}

					$this->data[$key] = $value;
					$Fields[$key] = $Field;
				}

				$key = "db_history[$Type][$Name][History]";
				if (isset($db_history[$Type][$Name]['History'])) {
					$value = $db_history[$Type][$Name]['History'];
					unset($db_history[$Type][$Name]['History']);
					$this->data[$key] = is_array($value) ? implode(',', $value) : $value;
				}

				$queries[] = [
					'Name' => $Name,
					'Type' => $Type,
					'Fields' => $Fields,
					'key' => $key
				];
			}
		}

		return [
			'title'	=> $extension['name'] ?: $extension['alias'],
			'values' => $this->data,
			'queries' => $queries,
			'db_history' => $db_history,
			'is_protected' => $extension['is_protected']
		];
	}

	/**
	 * Get
	 */
	public function getEditReadmeData()
	{
		// data
		$this->filter(POST, ['id' => 'id|array:first|required:abort']);

		// query
		$data = $this->db->safeFind("
		SELECT
		 extension_alias ,
		 extension_name
		FROM `#__extensions`
		WHERE id = ?", $this->data['id'])->fetch();


		// vars
		$file = SYSTEM_STORAGE . sprintf('readme/%s/README.html', $data['extension_alias']);
		$readme = '';

		if (is_file($file)) {
			$readme = file_get_contents($file);
		}

		return [
			'title' => $data['extension_name'],
			'values' => [
				'alias' => $data['extension_alias'],
				'readme' => $readme
			],
		];
	}

	/**
	 * Get
	 */
	protected function setDevelopersData(array &$rows)
	{
		$filepath   = (new Carrier)->getTargetPath() . '%s_%s.zip';
		$statuses   = ['public', 'private'];
		$components = Extensions::getComponents();

		foreach ($rows as $i => $row) {
			$rows[$i]['can_compile']    = !$row['is_protected'] && $row['package_id'] == -1;
			$rows[$i]['package_exists'] =
				in_array($row['status'], $statuses)
				&& is_file(sprintf($filepath, $row['extension_alias'], $row['extension_version']));

			if ($row['components']) {
				$x = [];
				foreach (str_split($row['components']) as $j) {
					if (isset($components[$j])) {
						$x[$j] = $components[$j]['name'];
					}
				}
				$rows[$i]['components'] = $x;
			}
		}
	}

	/**
	 * Get
	 */
	protected function getListDevelopers()
	{
		return $this->db->safeFind("
		SELECT id, developer_name
		FROM `#__extensions_developers`
		ORDER BY developer_name")->fetchAll(Database::FETCH_COLUMN, [0 => 1], [_t('All developers')]);
	}

	/**
	 * Get
	 */
	protected function getDevelopers()
	{
		return $this->db->safeFind("
		SELECT id, developer_name
		FROM `#__extensions_developers`
		WHERE is_protected = 0
		ORDER BY developer_name")->fetchAll(Database::FETCH_COLUMN, [0 => 1], ['--- ' . _t('Select') . ' ---']);
	}

	/**
	 * Get
	 */
	protected function getSince()
	{
		return $this->db->safeFind("
		SELECT extension_version
		FROM `#__extensions`
		WHERE extension_alias = 'system'")->fetchColumn();
	}
}
