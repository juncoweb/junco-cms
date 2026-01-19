<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Samples\Snippet;

class HtmlSample extends SampleBase
{
    /**
     * Render
     */
    public function render(): string
    {
        $this->btn('toggle', 'fa-solid fa-code', 'Show/Hide code');
        $this->btn('copy', 'fa-solid fa-copy', 'Copy code');

        return $this->panels(
            $this->code,
            '<pre class="language-markup"><code>' . htmlentities($this->code) . '</code></pre>'
        );
    }
}
