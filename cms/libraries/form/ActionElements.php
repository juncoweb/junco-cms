<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Form;

use Junco\Form\Contract\ActionElementsInterface;
use Junco\Form\ActionElement\Button;
use Junco\Form\ActionElement\Dropdown;

abstract class ActionElements implements ActionElementsInterface
{
    // vars
    protected string $controlName = 'control-form';
    protected string $btn_caption = '';
    protected string $label_tag   = '';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->btn_caption = config('form.btn_caption');

        if ($this->btn_caption == 'responsive') {
            $this->label_tag = '<span class="visually-responsive-hidden"> %s</span>';
        } elseif ($this->btn_caption == 'hidden') {
            $this->label_tag = '<span class="visually-hidden">%s</span>';
        } else {
            $this->label_tag = ' %s';
        }
    }

    /**
     * Button
     *
     * @param string       $control
     * @param string       $title
     * @param array|string $attr
     */
    public function button(string $control = '', string $title = '', array|string $attr = [])
    {
        if (!is_array($attr)) {
            $attr = ['icon' => $attr];
        }
        $attr['title'] ??= $title ?: $control;
        $attr['label'] = $this->getLabel($attr);

        if ($this->isControl($control)) {
            $attr = [
                'type' => 'button',
                $this->controlName => $control,
                ...$attr
            ];
        } else {
            $attr['href'] = $control;
        }


        return new Button($attr);
    }

    /**
     * Dropdown
     * 
     * @param string|array	$menu  		The array format is [label(, href, contol, value)]
     * @param string 		$title
     * @param array			$attr
     */
    public function dropdown(string|array $menu = '', string $title = '', array|string $attr = [])
    {
        if (is_array($menu)) {
            $menu = $this->renderMenu($menu);
        }

        $showCaption = true;

        if ($attr === []) {
            $showCaption = false;
            $attr = [
                'icon' => 'fa-solid fa-ellipsis-vertical',
                'caret' => false
            ];

            if (!$title) {
                $title = _t('More');
            }
        } elseif (!is_array($attr)) {
            $attr = [
                'icon' => $attr,
                'caret' => false
            ];
        } elseif (!isset($attr['caret'])) {
            $attr['caret'] = empty($attr['icon']);
        }

        $attr['title'] ??= $title;
        $attr['label'] = $this->getLabel($attr, $showCaption);

        return new Dropdown($menu, $attr);
    }

    /**
     * Get
     *
     * @param array	&$attr
     */
    protected function getLabel(array &$attr, bool $showCaption = true): string
    {
        $icon    = $this->extract($attr, 'icon');
        $label   = $this->extract($attr, 'label', '{{ icon }}{{ caption }}');
        $caption = $showCaption ? ($icon ? sprintf($this->label_tag, $attr['title']) : $attr['title']) : '';

        if ($icon) {
            $icon = '<i class="' . $icon . '" aria-hidden="true"></i>' . ($caption ? ' ' : '');
        }

        return strtr($label, [
            '{{ icon }}' => $icon,
            '{{ caption }}' => $caption,
        ]);
    }

    /**
     * Extract attributes
     */
    protected function extract(array &$attr, string $name, $value = '')
    {
        if (isset($attr[$name])) {
            $value = $attr[$name];
            unset($attr[$name]);
        }

        return $value;
    }

    /**
     * 
     */
    protected function isControl(string $control): bool
    {
        return !$control || (bool)preg_match('/^[a-z\-_]+$/', $control);
    }

    /**
     * Render the dropdown menu.
     *
     * @param array	$menu  The array format is [label(, href, contol, value)]
     *
     */
    protected function renderMenu(array $menu): string
    {
        $html = '';
        foreach ($menu as $row) {
            if (empty($row['label'])) {
                $html .= '<li class="separator" role="separator"></li>';
            } else {
                $html .= '<li' . (empty($row['selected']) ? '' : ' class="selected"') . '>';

                if (empty($row['href'])) {
                    $html .= '<a href="javascript:void(0)" role="button"'
                        . (empty($row['control']) ? '' : ' ' . $this->controlName . '="' . $row['control'] . '"')
                        . (empty($row['value']) ? '' : ' data-value="' . $row['value'] . '"')
                        . (empty($row['name']) ? '' : ' data-name="' . $row['name'] . '"')
                        . '>';
                } else {
                    $html .= '<a href="' . $row['href'] . '">';
                }

                if (!empty($row['icon'])) {
                    $html .= '<i class="' . $row['icon'] . '" aria-hidden="true"></i>';
                }

                $html .= $row['label'];
                $html .= '</a></li>';
            }
        }

        return '<ul>' . $html . '</ul>';
    }
}
