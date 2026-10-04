<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Tabs;

abstract class TabsBase implements TabsInterface
{
    protected array $options = [];
    protected array $tablist = [];

    /**
     * Constructor
     * 
     * @param string|array $id
     * @param array        $options
     */
    public function __construct(string|array $id = '', array $options = [])
    {
        if (is_array($id)) {
            $options = $id;
        } elseif ($id) {
            $options['id'] = $id;
        }

        $this->options = array_merge([
            'id' => 'tabs',
        ], $options);
    }

    /**
     * Tab
     * 
     * @param string $label
     * @param string $panel
     * 
     * @return Tab
     */
    public function tab(string $label, string $panel = ''): Tab
    {
        return $this->tablist[] = new Tab($label, $panel);
    }
}
