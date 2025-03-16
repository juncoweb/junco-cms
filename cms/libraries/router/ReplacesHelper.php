<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Router;

use Exception;

class ReplacesHelper
{
	/**
	 * 
	 */
	public function before(mixed &$input): self
	{
		if (is_array($input)) {
			$partials = [];

			foreach ($input['component'] ?? [] as $component => $renamed) {
				$partial = $component . ':' . $renamed;

				if (isset($input['task'][$component])) {
					$endpoint = [];

					foreach ($input['task'][$component] as $_key => $_value) {
						$endpoint[] = $_key . ':' . $_value;
					}

					$partial .= '{' . implode(',', $endpoint) . '}';
				}

				$partials[] = $partial;
			}

			$input = implode(',', $partials);
		}

		return $this;
	}

	/**
	 * 
	 */
	public function after(mixed &$input): void
	{
		if (empty($input)) {
			$input = [];
			return;
		}

		if (!preg_match_all('/(\w*?):(.*?)(?:\{(.*?)\})?(?:,|$)/', $input, $matches, PREG_SET_ORDER)) {
			throw new Exception('Replace url has an error.');
		}

		$value = [
			'component' => [],
			'task'  => []
		];

		foreach ($matches as $match) {
			$value['component'][$match[1]] = $match[2];

			if (isset($match[3])) {
				$value['task'][$match[1]] = [];

				foreach (explode(',', $match[3]) as $row) {
					$row = explode(':', $row, 2);

					if (isset($row[1])) {
						$value['task'][$match[1]][$row[0]] = $row[1];
					}
				}
			}
		}

		$input = $value;
	}
}
