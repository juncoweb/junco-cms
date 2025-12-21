<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Backlist\Contract\ActionsInterface;
use Junco\Form\ActionElements;

class backlist_master_default_actions extends ActionElements implements ActionsInterface
{
    // vars
    protected array  $mainbox        = [];
    protected array  $sidebox        = [];
    protected int    $groupIndex    = 0;
    protected bool   $refresh        = false;
    protected bool   $filters        = false;
    protected string $controlName    = 'control-list';

    /**
     * Button
     *
     * @param string       $control
     * @param string       $title
     * @param array|string $attr
     */
    public function button(string $control = '', string $title = '', array|string $attr = [])
    {
        return $this->addElement(parent::button($control, $title, $attr));
    }

    /**
     * Dropdown
     * 
     * @param string|array	$menu  		The array format is [label(, href, contol, value)]
     * @param string 		$title
     * @param string|bool	$caption
     * @param bool	 		$caret
     */
    public function dropdown(string|array $menu = '', string $title = '', array|string $attr = [])
    {
        return $this->addElement(parent::dropdown($menu, $title, $attr));
    }

    /**
     * Create
     */
    public function create(array|int $attr = 0)
    {
        if (!is_array($attr)) {
            $attr = ['num_rows' => $attr];
        }

        $attr['title']   ??= _t('Create');
        $attr['options'] ??= null;
        $highlight    = config('form.highlight_create_button');
        $attr['attr'] = ['icon' => 'fa-solid fa-plus'];

        if ($highlight) {
            $attr['attr'] += ['class' => 'btn-primary btn-solid'];
        }

        if ($attr['options'] !== null) {
            $this->addElement(parent::dropdown($attr['options'], $attr['title'], $attr['attr']));
            $this->separate();
        } else {
            $attr['control'] ??= 'create';
            $attr['num_rows'] ??= 0;

            $element = parent::button($attr['control'], $attr['title'], $attr['attr']);

            if ($attr['num_rows'] > 0) {
                $this->addElement('<form class="btn-group btn-create">'
                    . $element
                    . '<input type="text" name="num_rows" value="' . $attr['num_rows'] . '"  class="btn btn-primary btn-solid" maxlength="2"/>'
                    . '</form>');
            } else {
                $this->addElement($element);
            }

            if ($highlight) {
                $this->separate();
            }
        }
    }

    /**
     * Edit
     */
    public function edit()
    {
        $this->addElement(parent::button('edit', _t('Edit'), 'fa-solid fa-pencil'));
    }

    /**
     * Delete
     */
    public function delete()
    {
        $this->addElement(parent::button('confirm_delete', _t('Delete'), 'fa-solid fa-trash'));
    }

    /**
     * Back
     * 
     * @param string $href
     * @param string $title
     */
    public function back(string $href = '', string $title = '')
    {
        $this->addElement(parent::button(
            $href ?: 'javascript:history.go(-1)',
            $title ?: _t('Back'),
            'fa-solid fa-arrow-left'
        ));
    }

    /**
     * Toggle
     * 
     * @param array|string $control
     * @param string       $title
     * @param array|string $attr
     */
    public function toggle(array|string $control = '', string $title = '', array|string $attr = [])
    {
        if (!$title) {
            $title = _t('Status');
        }

        if (!is_array($attr)) {
            $attr = ['icon' => $attr];
        } elseif (empty($attr['icon'])) {
            $attr['icon'] = 'fa-solid fa-circle-dot';
        }

        if (is_array($control)) {
            $attr['caret'] ??= false;
            $element = parent::dropdown($control, $title, $attr);
        } else {
            $element = parent::button($control ?: 'status', $title, $attr);
        }

        $this->addElement($element);
    }

    /**
     * Separate
     */
    public function separate(): void
    {
        $this->groupIndex++;
    }

    /**
     * Filters
     */
    public function filters()
    {
        $this->filters = true;
    }

    /**
     * Refresh
     */
    public function refresh()
    {
        $this->refresh = true;
    }

    /**
     * Render
     */
    public function render()
    {
        // main
        $html_1 = '';
        if ($this->mainbox) {
            foreach ($this->mainbox as $group) {
                $html_1 .= '<div class="btn-group">';
                foreach ($group as $element) {
                    $html_1 .= $element;
                }
                $html_1 .= '</div>';
            }
            $html_1 = '<div>' . $html_1 . '</div>';
            $this->mainbox = [];
        }

        // right
        $html_2 = '';
        if ($this->filters) {
            $html_2 .= '<div class="btn-group">'
                . parent::button('filters', _t('Filters'), 'fa-solid fa-filter')
                . parent::button('filters-reset', $t = _t('Reset'), ['icon' => 'fa-solid fa-xmark', 'label' => '{{ icon }}<div class="visually-hidden">' . $t . '</div>'])
                . '</div>';
        }

        if ($this->refresh) {
            $html_2 .= '<div class="btn-group">'
                . parent::button('refresh', _t('Refresh'), 'fa-solid fa-arrows-rotate')
                . '</div>';
        }

        if ($html_2) {
            $html_2 = '<div class="text-right">' . $html_2 . '</div>';
        }

        return '<div backlist-actions class="backlist-actions">'
            . '<div>' . $html_1 . $html_2 . '</div>'
            . '<div></div>'
            . '</div>';
    }

    /**
     * Element
     */
    protected function addElement($element)
    {
        return $this->mainbox[$this->groupIndex][] = $element;
    }
}
