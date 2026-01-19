<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Samples\Snippet;

abstract class SampleBase implements SampleInterface
{
    private string $label   = '';
    private string $context = '';
    private array  $buttons = [];

    /**
     * Constructor
     */
    public function __construct(protected string $code = '') {}

    /**
     * Label
     * 
     * @param string $label
     * 
     * @return static
     */
    public function setLabel(string $label): static
    {
        $this->label = $label;
        return $this;
    }

    /**
     * Context
     * 
     * @param string $context
     * 
     * @return static
     */
    public function setContext(string $context): static
    {
        $this->context = $context;
        return $this;
    }

    /**
     * Button
     */
    protected function btn(
        string $control,
        string $icon,
        string $label = '',
        bool $return = false
    ): string {
        $btn = '<a href="javascript:void(0)" class="sample-btn" control-sample="' . $control . '" title="' . $label . '">'
            .  '<i class="' . $icon . '"></i>'
            . '</a>';

        if ($return) {
            return $btn;
        }

        return $this->buttons[$control] = $btn;
    }

    /**
     * Panels
     */
    protected function panels(string ...$panels): string
    {
        foreach ($panels as $i => $content) {
            $panels[$i] = '<div class="sample-panel-' . ($i + 1) . '">' . $content . '</div>';
        }

        return '<div class="sample-row">'
            .  $this->label()
            .  implode($panels)
            .  $this->context()
            . '</div>';
    }

    /**
     * Label
     */
    private function label(): string
    {
        if (!$this->label && !$this->buttons) {
            return '';
        }

        $label = $this->label
            ? '<h6 class="bold">' . $this->label . '</h6>'
            : '';

        $buttons = $this->buttons
            ? '<div class="text-right">' . implode($this->buttons) . '</div>'
            : '';

        return '<div class="flex">'
            .  '<div class="flex-auto">' . $label . '</div>'
            .   $buttons
            . '</div>';
    }

    /**
     * Context
     */
    private function context(): string
    {
        return $this->context
            ? '<div class="sample-context">' . $this->context . '</div>'
            : '';
    }
}
