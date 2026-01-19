<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Samples\Snippet;

class SizesSample extends SampleBase implements TypeInterface
{
    use TypeTrait;

    protected array $sizes = [
        'small',
        'medium',
        'large',
    ];

    /**
     * Render
     */
    public function render(): string
    {
        $html = '';
        $type = $this->getType();

        foreach ($this->sizes as $size) {
            $html .= '<div class="sample-box' . $type . '">' . strtr($this->code, [
                '{{ size }}' => $size,
                '{{ caption }}' => ucfirst($size)
            ]) . '</div>';
        }

        return $this->panels($html);
    }
}
