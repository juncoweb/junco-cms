<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Form\ActionElements;
use Junco\Frontlist\Contract\ActionsInterface;

class frontlist_master_default_actions extends ActionElements implements ActionsInterface
{
    // vars
    protected array  $boxes            = [];
    protected int    $groupIndex    = 0;
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
     * @param array			$attr
     */
    public function dropdown(string|array $content = '', string $title = '', array|string $attr = [])
    {
        return $this->addElement(parent::dropdown($content, $title, $attr));
    }

    /**
     * Create
     */
    public function create(array|string $attr = [])
    {
        if (is_string($attr)) {
            $attr = ['title' => $attr];
        }
        $attr['icon'] = 'fa-solid fa-plus';

        $this->addElement(parent::button('create', $attr['title'] ?? _t('Create'), $attr));
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
     * Separate
     */
    public function separate(): void
    {
        $this->groupIndex++;
    }

    /**
     * Refresh
     */
    public function refresh()
    {
        $this->addElement(parent::button('refresh', _t('Refresh'), 'fa-solid fa-arrows-rotate'));
    }

    /**
     * Render
     */
    public function render(): string
    {
        $html = '';

        if ($this->boxes) {
            foreach ($this->boxes as $group) {
                $html .= '<div class="btn-group">' . implode($group) . '</div>';
            }

            $this->boxes = [];
        }

        return '<div class="frontlist-actions" frontlist-actions>' . $html  . '</div>';
    }

    /**
     * Element
     */
    protected function addElement($element)
    {
        return $this->boxes[$this->groupIndex][] = $element;
    }
}
