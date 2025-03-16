<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Filter\Filters;

class Datetime extends FilterAbstract
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->type = 'datetime';
		$this->default  = null;
		$this->argument = [
			'filter' => FILTER_CALLBACK,
			'options' => [$this, 'validateDatetime']
		];
	}

	/**
	 * Set modifiers
	 * 
	 * @param array $modifiers
	 */
	public function setModifiers(array $modifiers): void
	{
		$this->accept($modifiers, ['min', 'max', 'array', 'default', 'required', 'only_if', 'only_if_not']);

		parent::setModifiers($modifiers);
	}

	/**
	 * Validate date
	 */
	public function validateDatetime($value)
	{
		if ($value === null) {
			return $value;
		} elseif (preg_match('#^\d{4}-\d{2}-\d{2}T(?:[0-1][0-9]|2[0-3]):[0-5][0-9]$#', $value)) {
			$part = explode('-', substr($value, 0, 10));
			if (checkdate($part[1], $part[2], $part[0])) {
				return $value;
			}
		}
		return false;
	}

	/**
	 * Set
	 */
	protected function setDefaultModifier(mixed $rule_value)
	{
		if ($rule_value) {
			throw new \Exception(sprintf(
				'The «%s» filter does not accept values in the «default» rule',
				__CLASS__
			));
		}

		$this->default = date('Y-m-d H:i');
	}
}
