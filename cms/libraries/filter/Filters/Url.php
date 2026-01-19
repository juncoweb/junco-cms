<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Filter\Filters;

class Url extends FilterAbstract
{
    /**
     * Constructor
     * 
     * @param string|array|null $filter_value
     */
    public function __construct(string|array|null $filter_value = null)
    {
        $this->type = 'string';
        $this->default  = '';
        $this->argument = [
            'filter' => FILTER_VALIDATE_URL
        ];

        // I validate schemes
        if ($filter_value) {
            if (is_string($filter_value)) {
                $filter_value = $this->strToArr($filter_value);
            }

            $this->callback[] = function (&$value) use ($filter_value) {
                $scheme = explode(':', $value)[0];

                if (!in_array($scheme, $filter_value)) {
                    $value = false;
                }
            };
        }
    }

    /**
     * Validate
     */
    /* public function validate(?string $value = null)
	{
		if ($value === null) {
			return null;
		}

		$scheme = parse_url($value, PHP_URL_SCHEME);

		if (
			!$scheme
			|| ($this->schemes && !in_array($scheme, $this->schemes))
		) {
			return false;
		}

		return $value;
	} */
}
