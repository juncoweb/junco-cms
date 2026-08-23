<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Samples\Snippet;

class JsSample extends SampleBase
{
    /**
     * Render
     */
    public function render(): string
    {
        $this->btn('runjs', 'fa-solid fa-play', _t('Run'));
        $this->btn('copyjs', 'fa-solid fa-copy', _t('Copy code'));
        $this->btn('toggle', 'fa-solid fa-code', _t('Show / Hide code'));

        return $this->panels(
            '<pre class="language-javascript"><code>' . htmlentities($this->code) . '</code></pre>',
            '<div class="flex">'
                .   '<form class="flex-auto">'
                .     '<textarea name="code" class="input-field" control-felem="auto-grow">' . $this->code . '</textarea>'
                .   '</form>'
                .   '<div class="pl-2">' . $this->btn('resetjs', 'fa-solid fa-delete-left', _t('Reset'), true) . '</div>'
                . '</div>'
        );
    }
}
