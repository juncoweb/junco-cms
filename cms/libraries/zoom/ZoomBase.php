<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

abstract class ZoomBase implements ZoomInterface
{
    // vars
    protected string $id;
    protected array  $groups = [];

    /**
     * Constructor
     * 
     * @param string $id
     */
    public function __construct(string $id = '')
    {
        $this->id = $id;
    }

    /**
     * Group
     * 
     * @param string $content
     * 
     * @return array
     */
    public function group(string $content = ''): ZoomGroup
    {
        return $this->groups[] = new ZoomGroup($content);
    }

    /**
     * Status
     * 
     * @param $status
     * 
     * @return array
     */
    public function status($status): ZoomGroup
    {
        return $this->groups[] = (new ZoomGroup('<span class="color-' . $status->color() . '"><i class="fa-solid fa-circle"></i> ' . $status->title() . '</span>'))->setLabel(_t('Status'));
    }

    /**
     * Date
     * 
     * @param Date $date
     * 
     * @return array
     */
    public function date(?Date $date): ZoomGroup
    {
        $content = $date
            ? '<time datetime="' . $date->format('Y-m-d H:i:s') . '" class="text-nowrap">'
            .   $date->format(_t('Y-M-d')) . ' <span class="color-light">' . $date->format('H:i:s') . '</span>'
            . '</time>'
            : '-';

        return $this->groups[] = (new ZoomGroup($content));
    }

    /**
     * Group
     * 
     * @param array ...$group
     * 
     * @return void
     */
    public function columns(ZoomGroup ...$group): void
    {
        foreach ($group as $x) {
            array_pop($this->groups);
        }

        $this->groups[] = $group;
    }
}
