<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Form\FilterElement;

class Input extends FilterElement
{
    /**
     * Constructor
     *
     * @param string  $name
     * @param ?string $label
     * @param array	  $attr
     */
    public function __construct(
        protected string $name,
        string $value,
        array  $attr = []
    ) {
        $icon = $this->extract($attr, 'icon');
        $html = '<input' . $this->attr([
            'type'  => 'text',
            'name'  => $name,
            'id'    => $name,
            'value' => $value,
            'class' => 'btn'
        ], $attr) . '/>';

        $this->html = $icon
            ? $this->withIcon($icon, $html)
            : $html;;
    }

    /**
     * With icon
     */
    protected function withIcon(array|string $icon, string $html): string
    {
        $tagName  = 'span';
        $position = 'left';
        $class    = '';

        if (is_array($icon)) {
            $iconClass = $this->extract($icon, 'name');
            $position  = $this->extract($icon, 'position');
            $class     = $this->extract($icon, 'class');

            if (isset($icon['type'])) {
                $tagName = 'button';
            }
            if ($class) {
                $class = ' ' . $class;
            }
        } else {
            $iconClass = $icon;
            $icon = [];
        }

        $icon = '<' . $tagName . $this->attr([
            'class' => 'input-icon'
        ], $icon) . '><i class="' . $iconClass . '"></i></' . $tagName . '>';

        if ($position === 'right') {
            $html .= $icon;
        } else {
            $html = $icon . $html;
        }

        return '<div class="input-icon-group' . $class . '">' . $html . '</div>';
    }
}
