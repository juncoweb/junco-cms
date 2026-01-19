<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

class tiles_master_default_snippet extends TilesBase
{
    /**
     * Render
     */
    public function render(): string
    {
        $this->separate();
        $this->options['size'] ??= 'medium';

        $html = '';

        foreach ($this->blocks as $block) {
            $lines = '';

            foreach ($block['lines'] as $line) {
                $tiles = '';

                foreach ($line as $tile) {
                    $content = '<div class="tile-icon" aria-hidden="true"><i class="' . $tile['icon'] . '"></i></div>';

                    if ($tile['badge'] !== null) {
                        $content = '<div class="tile-wrapper">'
                            . $content
                            . '<div class="tile-badge badge badge-small badge-solid badge-secondary">' . $tile['badge'] . '</div>'
                            . '</div>';
                    }

                    if ($tile['caption']) {
                        $content .= '<div class="tile-caption">' . $tile['caption'] . '</div>';
                    }

                    $tiles .= '<div><a href="' . $tile['href'] . '" id="' . $tile['id'] . '"><div class="tile-content">' . $content . '</div></a></div>';
                }

                $lines .= '<div class="tiles tiles-' . $this->options['size'] . '">' . $tiles . '</div>';
            }

            if ($block['legend'] !== null) {
                $html .= '<fieldset class="tiles-fieldset">'
                    . ($block['legend'] ? '<legend>' . $block['legend'] . '</legend>' : '')
                    . $lines
                    . '</fieldset>';
            } else {
                $html .= '<div>' . $lines . '</div>';
            }
        }

        $this->blocks = [];

        return $html;
    }
}
