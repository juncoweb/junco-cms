<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Filter\Filters;

use Junco\Filesystem\UploadedImageManager;

class Images extends FilterAbstract
{
	// vars
	protected array $__data = [];

	/**
	 * Constructor
	 * 
	 * @param string|array|null $filter_value
	 */
	public function __construct(string|array|null $filter_value = null)
	{
		$this->type = 'file';
		$this->isFile = true;
		$this->altValue = true;
		/* $this->argument = [
			'filter' => FILTER_DEFAULT
		]; */

		if ($filter_value) {
			if (is_string($filter_value)) {
				$filter_value = $this->strToArr($filter_value);
			}

			$this->callback[] = function (UploadedImageManager $value) use ($filter_value) {
				$value->validate(['allow_extensions' => $filter_value]);
			};
		}
	}

	/**
	 * Set modifiers
	 * 
	 * @param array $modifiers
	 */
	public function setModifiers(array $modifiers): void
	{
		$this->accept($modifiers, ['required']);

		parent::setModifiers($modifiers);
	}

	/**
	 * Filter
	 * 
	 * @param mixed $value
	 * @param mixed $file
	 * @param mixed $altValue
	 * 
	 * @return mixed
	 */
	public function filter($value, $file = null, $altValue = null): mixed
	{
		$manager = new UploadedImageManager($file, true);

		if ($altValue) {
			$manager->setOrder($altValue);
		}

		if ($this->required) {
			$this->required = false;

			$manager->verifyIsEmpty();
		}
		$manager->validate();

		foreach ($this->callback as $fn) {
			$fn($manager);
		}

		return $manager;
	}
}
