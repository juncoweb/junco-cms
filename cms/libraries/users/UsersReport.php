<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

class UsersReport
{
	// vars
	protected $db;
	protected string $monthFormat = 'm/Y';

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->db = db();
	}

	/**
	 * Get the charts data from the database
	 * 
	 * @return array
	 */
	public function getChartData(int $total = 12): array
	{
		$date = $this->getStartDate($total - 1);
		$data = $this->getEmptyData($date, [/* _t('Month') */'', _t('Users')], $total);

		// query
		$rows = $this->db->safeFind("
		SELECT
		 COUNT(*) AS total,
		 MAX(created_at) AS created_at
		FROM `#__users`
		WHERE created_at > ?
		GROUP BY MONTH(created_at)
		ORDER BY created_at", $date->format('Y-m-1 00:00:00'))->fetchAll();

		foreach ($rows as $row) {
			$index = (new DateTime($row['created_at']))->format($this->monthFormat);
			$data[$index][1] = (int)$row['total'];
		}

		return array_values($data);
	}

	/**
	 * Get
	 * 
	 * @return array
	 */
	public function getData(): array
	{
		$data = $this->db->safeFind("
		SELECT
		 created_at ,
		 (SELECT COUNT(*) FROM `#__users`) AS num_users
		FROM `#__users`
		ORDER BY created_at DESC
		LIMIT 1")->fetch();

		if ($data) {
			$data['created_at'] = new Date($data['created_at']);
			return $data;
		}

		return [
			'created_at' => false,
			'num_users' => 0
		];
	}

	/**
	 * Get
	 */
	protected function getStartDate(int $total = 12): Datetime
	{
		return (new Datetime)->sub(new DateInterval("P{$total}M"));
	}

	/**
	 * Get
	 */
	protected function getEmptyData(Datetime $date, array $head, int $total = 12): array
	{
		$data   = [];
		$data[] = $head;
		$row	= array_fill(0, count($head), 0);
		//
		$date = clone $date;
		$interval = new DateInterval('P1M');

		for ($i = 0; $i < $total; $i++) {
			$index = $date->format($this->monthFormat);
			$data[$index]    = $row;
			$data[$index][0] = $index;
			$date->add($interval);
		}

		return $data;
	}
}
