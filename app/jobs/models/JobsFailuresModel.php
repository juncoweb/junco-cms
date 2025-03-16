<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class JobsFailuresModel extends Model
{
	// vars
	protected $db = null;
	protected $failure_id = null;


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
		$this->filter(POST, [
			'failure_id'		=> 'id',
			'failure_slug'	=> 'text',
			'failure_title'		=> 'text|required',
			'failure_image'		=> 'image',
			'failure_description'	=> 'multiline',
			'status'					=> 'bool:0/1',
		]);

		// extract
		$this->extract('failure_id');
		$this->data['failure_slug'] = Utils::sanitizeSlug($this->data['failure_slug'] ?: $this->data['failure_title']);

		// query - security
		if ($this->failure_id) {
			$row = $this->db->safeFind("
			SELECT failure_slug, failure_image
			FROM `#__jobs_failures`
			WHERE id = ?", $this->failure_id)->fetch() or abort();
		}

		// validate
		if ($this->data['failure_slug'] != ($row['failure_slug'] ?? '')) {
			$current_id = $this->db->safeFind(
				"SELECT id FROM `#__jobs_failures` WHERE failure_slug = ?",
				$this->data['failure_slug']
			)->fetchColumn();

			if ($current_id && $current_id != $this->failure_id) {
				throw new Exception(_t('The slug already exists.'));
			}
		}

		// upload image
		$config = config('jobs');
		$this->data['failure_image'] = $this->data['failure_image']
			->moveTo($config['jobs.original_path'])
			->resize(
				$config['jobs.image_path'] . '{filename}',
				$config['jobs.image_max_wh'],
				$config['jobs.image_resize_mode'],
				$config['jobs.save_original']
			)
			->setCurrentImage($row['failure_image'] ?? '')
			->getFilename() ?: '';

		// query
		if ($this->failure_id) {
			$this->db->safeExec("UPDATE `#__jobs_failures` SET ?? WHERE id = ?", $this->data, $this->failure_id);
		} else {
			$this->db->safeExec("INSERT INTO `#__jobs_failures` (??) VALUES (??)", $this->data);
		}
	}

	/**
	 * Toggle
	 */
	public function status()
	{
		// data
		$this->filter(POST, ['id' => 'id|array|required:abort']);

		// query
		$this->db->safeExec("UPDATE `#__jobs_failures` SET status = IF(status > 0, 0, 1) WHERE id IN (?..)", $this->data['id']);
	}

	/**
	 * Delete
	 */
	public function delete()
	{
		// data
		$this->filter(POST, ['id' => 'id|array|required:abort']);

		// query
		$this->db->safeExec("DELETE FROM `#__jobs_failures` WHERE id IN (?..)", $this->data['id']);
	}
}
