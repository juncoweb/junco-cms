<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Form\FormElement;

class Suite extends FormElement
{
    /**
     * Constructor
     *
     * @param string       $name
     * @param array|string $default
     * @param array	       $options
     * @param array	       $attr
     */
    public function __construct(
        protected string $name,
        array|string $default = [],
        array $options = [],
        array $attr = []
    ) {
        $html = '';

        if (!is_array($default)) {
            $default = $default
                ? explode(',', $default)
                : [];
        }

        foreach ($options as $value => $caption) {
            $html .= '<label class="btn btn-small">'
                . '<input type="checkbox" class="input-hidden" value="' . $value . '"' . (in_array($value, $default) ? ' checked' : '') . ' />'
                . '<span>' . $caption . '</span>'
                . '</label>';
        }

        $this->content = '<div control-felem="suite" data-name="' . $name . '" data-selected="' . implode(',', $default) . '" class="fe-suite">'
            . '<ul class="btn-tablist tab-small">'
            .   '<li role="tab" aria-controls="' . $name . '-panel-0"><i class="fa-solid fa-check" aria-hidden="true"></i> ' . _t('Select') . '</li>'
            .   '<li role="tab" aria-controls="' . $name . '-panel-1"><i class="fa-solid fa-arrows-turn-to-dots" aria-hidden="true"></i> ' . _t('Sort') . '</li>'
            . '</ul>'
            . '<div id="' . $name . '-panel" class="btn-tabpanel-group">'
            .   '<div role="tabpanel" id="' . $name . '-panel-0">'
            .     '<div class="box-group">'
            .       '<div class="box box-0">' . $html . '</div>'
            .       '<div class="actions">'
            .         '<div><label title="' . ($t = _t('Check all')) . '"><input type="checkbox" class="input-checkbox"><span class="visually-hidden">' . $t . '</span></label></div>'
            .         '<div><button type="button" class="btn-inline" title="' . ($t = _t('Reset')) . '" aria-label="' . $t . '"><i class="fa-solid fa-rotate-left"></i></button></div>'
            .       '</div>'
            .      '</div>'
            .     '</div>'
            .   '<div role="tabpanel" id="' . $name . '-panel-1"><div class="box box-1"></div></div>'
            . '</div>'
            . '</div>';
    }

    /**
     * Get
     */
    public function getLabel(): string
    {
        if ($this->label) {
            return '<label class="input-label">' . $this->label . '</label>';
        }

        return $this->label;
    }
}
