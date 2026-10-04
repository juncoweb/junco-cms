<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class AdminAssetsColorsModel extends Model
{
	protected $db;

	/**
     * Get
     */
	public function getIndexData()
	{
		return [];
	}

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->db = db();
	}

	/**
     * Get palette data
     */
	public function getPaletteData()
	{
		//$data = $this->filter(GET, ['id' => 'id|required:abort']);
        
        //return $data;
        return [
            'options' => [],
            'title' => _t('Page'),
        ];
	}

	/**
     * Get contrast data
     */
	public function getContrastData()
	{
		//$data = $this->filter(GET, ['id' => 'id|required:abort']);
        
        //return $data;
        return [
            'options' => [],
            'title' => _t('Page'),
        ];
	}

	/**
     * Get checker data
     */
	public function getCheckerData()
	{
		//$data = $this->filter(GET, ['id' => 'id|required:abort']);
        
        //return $data;
        return [
            'options' => [],
            'title' => _t('Page'),
        ];
	}
}
