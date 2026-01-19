<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Backlist\Column;

use Junco\Backlist\Contract\SearchInterface;

class Search extends Column implements SearchInterface
{
    use IconTextTrait;

    /**
     * Constructor
     * 
     * @param string $column
     */
    public function __construct(string $column, string $value, string $field)
    {
        if ($column) {
            $this->text = $this->normalize($column);
        }

        $this->attr['href']           = 'javascript:void(0)';
        $this->attr['control-filter'] = 'search';

        if ($value) {
            $this->attr['data-value'] = $this->normalize($value);
        }

        if ($field) {
            $this->attr['data-field'] = $this->normalize($field);
        }
    }

    /**
     * Set
     * 
     * @param string $icon
     * 
     * @return string
     */
    public function td(): string
    {
        $this->attr['title'] ??= _t('Search');
        $this->attr['class'] ??= 'table-linked';

        $caption = $this->getCaption($this->text, $this->icon, $this->attr['title']);

        $this->td = '<a' . $this->attr($this->attr) . '>' . $caption . '</a>';

        return parent::td();
    }
}
