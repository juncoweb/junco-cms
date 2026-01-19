<?php

use PhpParser\Node\Expr\Instanceof_;

/**
 * @copyright (c) 2009-2026 by Junco CMS
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
     * @param string $status
     * @param string $color
     * 
     * @return ZoomGroup
     */
    public function status(mixed $status, string $color = ''): ZoomGroup
    {
        if (!is_string($status)) {
            // @deprecated
            $color  = $status->color();
            $status = $status->title();
        }

        if ($color) {
            $status = '<span class="color-' . $color . '"><i class="fa-solid fa-circle" aria-hidden="true"></i> ' . $status . '</span>';
        }

        return $this->groups[] = (new ZoomGroup($status))->setLabel(_t('Status'));
    }

    /**
     * Date
     * 
     * @param ?string $date
     * 
     * @return ZoomGroup
     */
    public function date(?string $date): ZoomGroup
    {
        $content = '-';
        if ($date) {
            $date = new Date($date);

            $content = '<time datetime="' . $date->format('Y-m-d H:i:s') . '" class="text-nowrap">'
                .   $date->format(_t('Y-M-d')) . ' <span class="color-subtle-default">' . $date->format('H:i:s') . '</span>'
                . '</time>';
        }

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
