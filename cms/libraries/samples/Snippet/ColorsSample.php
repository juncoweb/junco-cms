<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Samples\Snippet;

class ColorsSample extends SampleBase implements TypeInterface
{
    use TypeTrait;

    protected array $colors = [
        'default',
        'primary',
        'secondary',
        'success',
        'info',
        'warning',
        'danger'
    ];

    /**
     * Render
     */
    public function render(): string
    {
        $type = $this->getType();
        $html = '';

        foreach ($this->colors as $color) {
            $html .= '<div class="sample-box' . $type . '">' . strtr($this->code, [
                '{{ color }}' => $color,
                '{{ caption }}' => ucfirst($color)
            ]) . '</div>';
        }

        return $this->panels($html);
    }
}
