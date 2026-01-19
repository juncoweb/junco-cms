<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Backlist\Column;

use Junco\Backlist\Contract\LinkInterface;

class Link extends Column implements LinkInterface
{
    use IconTextTrait;

    /**
     * Constructor
     * 
     * @param string $url
     */
    public function __construct(string $url = '')
    {
        if ($url) {
            $this->attr['href'] = $this->normalize($url);
        }
    }

    /**
     * Target
     * 
     * @return static
     */
    public function setTarget(string $target = '_blank'): static
    {
        $this->attr['target'] = $target;

        return $this;
    }

    /**
     * Set
     * 
     * @param string $icon
     * 
     * @return string
     */
    public function td(): string
    {
        $this->attr['title'] ??= _t('Show');
        $this->attr['class'] ??= 'table-linked';

        $caption = $this->getCaption($this->text, $this->icon, $this->attr['title']);

        $this->td = '<a' . $this->attr($this->attr) . '>' . $caption . '</a>';

        return parent::td();
    }
}
