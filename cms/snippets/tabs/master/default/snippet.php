<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Tabs\TabsBase;

class tabs_master_default_snippet extends TabsBase
{
    /**
     * Render
     * 
     * @return string
     */
    public function render(): string
    {
        $tablist  = '';
        $tabpanel = '';

        foreach ($this->tablist as $i => $tab) {
            $panel_id = "{$this->options['id']}-panel-{$i}";
            $row = $tab->toArray();

            if ($row['icon']) {
                if ($row['responsive']) {
                    $row['icon'] .= ' on-small';
                    $row['label'] = '<span class="on-large">' . $row['label'] . '</span>';
                } else {
                    $row['label'] = '<span class="visually-hidden">' . $row['label'] . '</span>';
                }

                $row['label'] = '<i class="' . $row['icon'] . ' on-" aria-hidden="true"></i>' . $row['label'];
            }

            $tablist  .= '<li role="tab" aria-controls="' . $panel_id . '">' . $row['label'] . '</li>';
            $tabpanel .= '<div role="tabpanel" id="' . $panel_id . '">' . $row['panel'] . '</div>';
        }

        // free memory
        $this->tablist = [];

        $class = 'tablist';
        if (!empty($this->options['class'])) {
            $class .= ' ' . $this->options['class'];
        }

        //
        return '<ul id="' . $this->options['id'] . '" role="tablist" class="' . $class . '">' . $tablist . '</ul>'
            . '<div id="' . $this->options['id'] . '-panel" class="tabpanel-group">' . $tabpanel . '</div>';
    }
}
