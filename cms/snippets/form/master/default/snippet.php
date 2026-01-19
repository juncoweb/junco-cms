<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

require_once SYSTEM_ABSPATH . 'cms/snippets/form/master/default/elements.php';

use Junco\Form\Contract\FormActionsInterface;
use Junco\Form\Contract\FormElementInterface;
use Junco\Form\Contract\FormInterface;
use Junco\Form\Contract\HiddenInterface;

class form_master_default_snippet extends form_master_default_elements implements FormInterface
{
    // vars
    protected string|int|false $id;
    //
    protected string $header      = '';
    protected string $toggleClass = '';
    protected string $panelClass  = '';
    protected array  $blocks      = [];
    protected string $hiddens     = '';
    protected ?FormActionsInterface $actions = null;

    /**
     * Constructor
     * 
     * @param string|int|null $id
     */
    public function __construct(string|int|false $id = '')
    {
        $this->id = $id;
    }

    /**
     * Options
     * 
     * @param string $snippet
     */
    public function getActions(string $snippet = ''): FormActionsInterface
    {
        return $this->actions = snippet('form#actions', $snippet ?: '');
    }

    /**
     * Header
     * 
     * @param mixed $attr
     * @param ?bool $toggle
     */
    public function header(mixed $attr = null, ?bool $toggle = null): void
    {
        $boxes = [];
        $data = [
            'label'    => null,
            'content'  => null,
            'required' => false
        ];

        if ($attr instanceof FormElementInterface) {
            $this->extractElement($attr);

            $data['content']  = $attr->render();
            $data['label']    = $attr->getLabel();
            $data['required'] = $attr->isRequired();
            //$data['help']   = $attr->getHelp();
        } elseif (is_string($attr)) {
            $data['content'] = $attr;
        } elseif (is_array($attr)) {
            $data = array_merge($data, $attr);
        } else {
            $toggle = $attr;
        }

        if ($data['label']) {
            $boxes[] = '<div class="fh-label">' . $data['label'] . '</div>';
        }

        if ($data['content']) {
            $boxes[] = '<div class="fh-content">' . $data['content'] . '</div>';
        }

        if ($toggle === null) {
            $this->toggleClass = ' expanded';
        } else {
            $this->toggleClass = $toggle ? ' expanded' : '';
            $this->counter++;
            $boxes[] = '<div class="fh-toggle">'
                .  '<a'
                .   ' href="javascript:void(0)"'
                .   ' control-form="toggle-body"'
                .   ' role="button"'
                .   ' aria-expanded="' . ($toggle ? 'true' : 'false') . '"'
                .   ' aria-controls="form-body-' . $this->counter . '"'
                .  '><i class="fa-solid fa-chevron-down' . $this->toggleClass . '"></i></a>'
                . '</div>';
        }

        $this->header = '<div class="form-header">' . implode($boxes) . '</div>';
    }

    /**
     * Get the las element
     * 
     * @return FormElementInterface
     */
    public function getLastElement(): FormElementInterface
    {
        return array_pop($this->rows);
    }

    /**
     * Separate
     * 
     * @param array|string|null $attr
     * 
     * @return void
     */
    public function separate(array|string|null $attr = null): void
    {
        if (!is_array($attr)) {
            $attr = [
                'class'  => 'form-fieldset',
                'legend' => $attr
            ];
        } else {
            $attr['class'] = 'form-fieldset' . (empty($attr['class']) ? '' : ' ' . $attr['class']);
            $attr['legend'] ??= null;
        }

        $html = '';
        foreach ($this->rows as $row) {
            if (is_array($row)) {
                $partial = '';
                foreach ($row as $element) {
                    $partial .= '<div>' . $this->renderElement($element) . '</div>';
                }
                $html .= '<div class="form-group"><div class="form-columns">' . $partial . '</div></div>';
            } else {
                $html .= $this->renderElement($row);
            }
        }

        if ($this->header) {
            $html = $this->header . '<div id="form-body-' . $this->counter . '" class="form-body">' . $html . '</div>';
            $attr['class'] .= ' panel';
            $this->header = '';
        }

        if ($attr['legend'] !== null) {
            $html = '<fieldset class="' . $attr['class'] . '">'
                . ($attr['legend'] ? '<legend>' . $attr['legend'] . '</legend>' : '')
                . $html
                . '</fieldset>';
        } else {
            $html = '<div class="' . $attr['class'] . $this->toggleClass . '">' . $html . '</div>';
        }

        $this->rows = [];
        $this->blocks[] = $html;
    }

    /**
     * Columns
     * 
     * @param FormElementInterface[]
     * 
     * @return void
     */
    public function columns(FormElementInterface ...$elements): void
    {
        foreach ($elements as $element) {
            $this->extractElement($element);
        }

        $this->rows[] = count($elements) == 1
            ? $elements[0]
            : $elements;
    }

    /**
     * Adds a block
     * 
     * @param string $html
     * 
     * @return void
     */
    public function addBlock(string $html): void
    {
        $this->blocks[] = $html;
    }

    /**
     * Render
     * 
     * @return string
     */
    public function renderForm(string $html, string|int $form_id = ''): string
    {
        if (!$form_id) {
            $form_id = 'js-form';
        } elseif (is_numeric($form_id)) {
            $form_id = 'js-form' . $form_id;
        }

        return '<form id="' . $form_id . '">'
            . $html
            . FormSecurity::getToken()
            . '</form>';
    }

    /**
     * Render
     * 
     * @return string
     */
    public function render(): string
    {
        $html = isset($this->actions)
            ? $this->actions->render()
            : '';

        if ($this->rows) {
            $this->separate();
        }

        if ($this->blocks) {
            $html .= implode($this->blocks);
            $this->blocks = [];
        }

        if ($this->hiddens) {
            $html .= $this->hiddens;
            $this->hiddens = '';
        }

        $html = '<div class="form-wrapper"><div>' . $html . '</div></div>';

        if ($this->id === false) {
            return $html;
        }

        return $this->renderForm($html, $this->id);
    }

    /**
     * Render
     */
    protected function renderElement(FormElementInterface $element): string
    {
        $content = $element->render();
        $label   = $element->getLabel();
        $help    = $element->getHelp();
        $html    = '';

        if ($label !== null) {
            if ($element->isRequired()) {
                $label .= '<i class="fa-regular fa-asterisk color-primary ml-2 mr-2" title="' . _t('Required') . '" aria-hidden="true"></i>';
            }

            $html .= '<div class="form-label">' . $label . '</div>';
        }

        if ($content) {
            $html .= '<div class="form-content">' . $content . '</div>';
        }

        if ($help) {
            $html .= '<div class="form-help">' . $help . '</div>';
        }

        return '<div class="form-group">' . $html . '</div>';
    }

    /**
     * 
     */
    protected function addHidden(HiddenInterface $element): HiddenInterface
    {
        $this->hiddens .= $element;
        return $element;
    }
}
