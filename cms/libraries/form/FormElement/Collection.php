<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Form\FormElement;

class Collection extends FormElement
{
    /**
     * Constructor
     *
     * @param string       $control
     * @param string       $name
     * @param string|array $value
     * @param string       $caption
     */
    public function __construct(
        string $control,
        protected string $name,
        string|array $value = '',
        string $caption = '',
    ) {
        $is_multiple = is_array($value);
        $html = '';

        if ($is_multiple) {
            $html .= '<div class="input-collection-group"><ul class="input-tag-wrap" role="listbox" aria-labelledby="' . $name . '"></ul></div>';
            $html .= '<span id="collection-delete-option" aria-label="' . _t('Delete') . '"></span>';
        }

        $html .= '<div class="input-group">'
            .   '<input'
            .     ' type="text"'
            .     ' id="' . $name . '"'
            .     ' name="__' . $name . '"';

        if (!$is_multiple) {
            $html .= ' value="' . $caption . '"';
        }

        $html .=  ' placeholder="' . _t('Find and select from the database.') . '"'
            .     ' control-felem="' . $control . '"';

        if ($is_multiple) {
            $html .= ' data-multiple="true"';
            $html .= ' data-options="' . htmlentities(json_encode($value, JSON_FORCE_OBJECT)) . '"';
        }

        $html .=  ' class="input-field"'
            .     ' role="combobox"'
            .     ' autocomplete="off"'
            .     ' aria-controls="aria-controls-' . $name . '"'
            .     ' aria-autocomplete="list"'
            .   '/>';

        if (!$is_multiple) {
            $html .= '<button type="button" aria-label="' . _t('Clean the input') . '" class="btn"><i class="fa-solid fa-xmark"></i></button>';
        }

        $html .= '</div>';

        if (!$is_multiple) {
            $html .= '<input type="hidden" name="' . $name . '" value="' . $value . '" />';
        }

        $html .= '<div id="aria-controls-' . $name . '" class="dropdown-menu" style="display: none;" role="listbox" aria-labelledby="' . $name . '"></div>';
        $html .= '<span id="collection-just-use" aria-label="' . _t('Just use') . '"></span>';

        $this->content = '<div class="input-collection">' . $html . '</div>';
    }
}
