<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Filter\Filters;

use Junco\Filesystem\UploadedFileManager;

abstract class FileFilterAbstract extends FilterAbstract
{
    /**
     * Constructor
     * 
     * @param string|array|null $filter_value
     */
    public function __construct(string|array|null $filter_value = null)
    {
        $this->type = 'file';
        $this->isFile = true;
        $this->accept = ['min', 'max', 'required'];

        if ($filter_value) {
            if (is_string($filter_value)) {
                $filter_value = $this->strToArr($filter_value);
            }

            $this->callback[] = function (UploadedFileManager $value) use ($filter_value) {
                $value->validate(['allow_extensions' => $filter_value]);
            };
        }
    }

    /**
     * Set
     * 
     * @param mixed $rule_value
     */
    protected function setMinModifier(mixed $rule_value)
    {
        $this->callback[] = function (&$value) use ($rule_value) {
            $value->validate(['min_size' => (int)$rule_value]);
        };
    }

    /**
     * Set
     * 
     * @param mixed $rule_value
     */
    protected function setMaxModifier(mixed $rule_value)
    {
        $this->callback[] = function (&$value) use ($rule_value) {
            $value->validate(['max_size' => (int)$rule_value]);
        };
    }
}
