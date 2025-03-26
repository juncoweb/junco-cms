<?php

/*
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Form\ActionElements;
use Junco\Form\Contract\FormActionsInterface;

class form_master_default_actions extends ActionElements implements FormActionsInterface
{
    // vars
    protected array $mainbox    = [];
    protected array $leftbox    = [];
    protected int   $group        = 0;

    /**
     * Button
     *
     * @param string       $control
     * @param string       $title
     * @param array|string $attr
     */
    public function button(string $control = '', string $title = '', array|string $attr = [])
    {
        $this->mainbox[$this->group][] = parent::button($control, $title, $attr);
    }

    /**
     * Dropdown
     * 
     * @param string|array	$menu  		The array format is [label(, href, contol, value)]
     * @param string 		$title
     * @param array			$attr
     */
    public function dropdown(string|array|null $html = null, string $title = '', array|string $attr = [])
    {
        $this->mainbox[$this->group][] = parent::dropdown($html, $title, $attr);
    }

    /**
     * Separate
     */
    public function separate(): void
    {
        $this->group++;
    }

    /**
     * Nav
     * 
     * @param ?string $html
     */
    public function nav(?string $html = null): void
    {
        $this->leftbox[1][] = ($html === null
            ? parent::button('nav-prev', _t('Previous'), 'fa-solid fa-chevron-left')
            . parent::button('nav-next', _t('Next'), 'fa-solid fa-chevron-right')
            : $html);
    }

    /**
     * Refresh
     */
    public function refresh(): void
    {
        $this->mainbox[$this->group][] = parent::button('refresh', _t('Refresh'), 'fa-solid fa-arrows-rotate');
    }

    /**
     * Refresh
     * 
     * @param string 		$title
     * @param string|bool	$caption
     */
    public function enter(string $title = '', string $caption = '')
    {
        if (!$title) {
            $title = _t('Enter');
        }

        $this->mainbox[$this->group][] = '<button type="submit" class="btn btn-primary btn-solid" title="' . $title . '">' . ($caption ?: $title) . '</button>';
    }

    /**
     * Cancel
     * 
     * @param string $control
     */
    public function cancel(string $control = '')
    {
        if (!$control) {
            if (router()->isFormat('modal')) {
                $this->controlName = 'control-modal';
                $element = parent::button('close', _t('Cancel'));
                $this->controlName = 'control-form';
            } else {
                $element = parent::button('javascript:history.go(-1)', _t('Cancel'));
            }
        } else {
            $element = parent::button($control, _t('Cancel'));
        }

        $this->leftbox[0][] = $element;
    }

    /**
     * Help
     */
    public function help()
    {
        $this->leftbox[0][] = parent::button('help', _t('Help'));
    }

    /**
     * Render
     */
    public function render(): string
    {
        $html = '';

        // main
        if (!empty($this->mainbox)) {
            foreach ($this->mainbox as $group) {
                $html .= '<div class="btn-group">';
                foreach ($group as $element) {
                    $html .= $element;
                }
                $html .= '</div>';
            }
            $html = '<div class="form-col-1">' . $html . '</div>';
        }

        if (!empty($this->leftbox)) {
            ksort($this->leftbox);

            $html .= '<div class="form-col-2">';

            foreach ($this->leftbox as $group) {
                $html .= '<div class="btn-group">';
                foreach ($group as $element) {
                    $html .= $element;
                }
                $html .= '</div>';
            }

            $html .= '</div>';
        }

        return '<div class="form-actions">' . $html . '</div>';
    }
}
