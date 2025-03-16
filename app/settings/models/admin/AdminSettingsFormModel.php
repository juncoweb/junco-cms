<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class AdminSettingsFormModel extends Model
{
	// vars
	protected $snippets		= [];
	protected $plugins		= [];
	protected $row			= null;


	/**
	 * Get
	 */
	public function getFormData()
	{
		// data
		$this->filter(GET, ['key' => 'text']);

		//
		$settings = new Settings($this->data['key']);

		if (!$settings->security()) {
			if ($this->data['key']) {
				return ['error' => true];
			}

			return ['home' => true];
		}

		//
		$data = $settings->getData(true);
		$developer_mode = SYSTEM_DEVELOPER_MODE;
		$this->data['__key'] = $this->data['key'];
		$this->data += [
			'developer_mode'	=> $developer_mode,
			'title'				=> $data['title'] ? _t($data['title']) : ucfirst($this->data['key']),
			'description'		=> $data['description'],
			'warning'			=> $data['warning'],
			'keys'				=> $this->getKeys($settings, $this->data['key']),
			'values'			=> $this->data,
			'groups'			=> [],
		];
		$restore = [];
		$restore_types_1 = [
			'input-integer',
			'input-range',
			'input-text',
			'input-email',
			'input-password',
			'input-phone',
			'input-url',
			'input-color',
			'textarea',
			'boolean',
			'list',
			'json'
		];
		$restore_types_2 = [
			'select-integer',
			'select-text',
			'snippet',
			'plugin',
			'select-multiple-integer',
			'select-multiple-text',
			'plugins'
		];

		// prepare groups
		foreach ($data['rows'] as $name => $row) {
			if ($developer_mode || $row['status']) {
				$row['name'] = $name;
				$this->row = $row;

				// help
				if (!empty($row['alter_help'])) {
					$row['help'] = $row['alter_help'];
				} elseif ($row['help']) {
					$row['help'] = _t($row['help']);
				}

				switch ($row['type']) {
					case 'snippet';
						$row['options'] = $this->getSnippets();

						if (!$row['help']) {
							$row['help'] = _t('Select a snippet to display.');
						}
						break;

					case 'plugin':
						$row['options'] = $this->getPlugin();

						if (!$row['help']) {
							$row['help'] = _t('The plugins allow additional functions.');
						}
						break;

					case 'plugins':
						$row['options'] = $this->getPlugins();

						if (!$row['help']) {
							$row['help'] = _t('The plugins allow additional functions.');
						}
						break;

					case 'select-multiple-integer':
					case 'select-multiple-text':
					case 'select-integer':
					case 'select-text':
						$this->sanitizeArray($row['options']);

						if (!$row['help']) {
							$row['help'] = _t('Select a list item.');
						}
						break;

					case 'list':
						$row['value']			= $this->getArrayValue($row['value']);
						$row['default_value']	= $this->getArrayValue($row['default_value']);
						break;

					case 'json':
						$this->sanitizeArray($row['options']);

						if ($row['options'] !== null) {
							$row['options'] = $this->getArrayValue($row['options']);
						}

						$row['value']			= $this->getJsonValue($row['value']);
						$row['default_value']	= $this->getJsonValue($row['default_value']);

						break;

					case 'input-range':
						$row['min'] ??= 0;
						$row['max'] ??= 100;
						break;
				}

				// restore
				if ((in_array($row['type'], $restore_types_1)
						|| (in_array($row['type'], $restore_types_2) && $row['options'] !== null)
					) && ($row['value'] != $row['default_value']
					)
				) {
					$restore[$row['name']] = $row['default_value'];
					$row['restore'] = true;
				} else {
					$row['restore'] = false;
				}

				$this->data['groups'][$row['group']] ??= [
					'legend'		=> !empty($data['groups'][$row['group']]) ? _t($data['groups'][$row['group']]) : '',
					'description'	=> !empty($data['descriptions'][$row['group']]) ? _t($data['descriptions'][$row['group']]) : '',
					'rows'			=> []
				];
				$this->data['groups'][$row['group']]['rows'][] = $row;
				$this->data['values'][$row['name']] = $row['value'];
			}
		}

		return $this->data + [
			'restore' => $restore ? json_encode($restore) : ''
		];
	}

	/**
	 * Get
	 */
	protected function getKeys(Settings $settings, string $curKey): array
	{
		$rows = $settings->getAllData();

		if (count($rows) < 2) {
			return [];
		}

		$url = url('admin/settings/', ['key' => '%s']);

		foreach ($rows as $key => $data) {
			$rows[$key] = [
				'label' => $data['title'] ?? $key,
				'url' => sprintf($url, $key),
				'selected' => ($key === $curKey)
			];
		}

		return $rows;
	}

	/**
	 * Get
	 */
	protected function getSnippets()
	{
		$name = $this->resolveName('snippet');
		$this->snippets[$name] ??= SystemHelper::scanSnippets($name);

		return $this->snippets[$name];
	}

	/**
	 * Get
	 */
	protected function getPlugin()
	{
		$name = $this->resolveName('plugin');
		$this->plugins[$name] ??= SystemHelper::scanPlugins($name);

		return array_merge(['' => _t('None')], $this->plugins[$name]);
	}

	/**
	 * Get
	 */
	protected function getPlugins()
	{
		$name = $this->resolveName('plugins');
		$this->plugins[$name] ??= SystemHelper::scanPlugins($name);

		return $this->plugins[$name];
	}

	/**
	 * Get
	 */
	protected function resolveName(string $resolve)
	{
		if (!empty($this->row[$resolve])) {
			return $this->row[$resolve];
		} elseif ($this->row['name'] == $resolve) {
			return $this->data['key'];
		} else {
			$pos = strpos($this->row['name'], '_');
			if ($pos) {
				return substr($this->row['name'], 0, $pos);
			}
		}

		return $this->row['name'];
	}

	/**
	 * 
	 */
	protected function sanitizeArray(mixed &$value = null): void
	{
		if (!is_array($value)) {
			$value = null;
		}
	}

	/**
	 * Get
	 */
	protected function getArrayValue(mixed $value): string
	{
		return $value && is_array($value) ? implode(',', $value) : '';
	}

	/**
	 * Get
	 */
	protected function getJsonValue(mixed $value): string
	{
		return $value && is_array($value)
			? json_encode($value, JSON_UNESCAPED_SLASHES)
			: '';
	}
}
