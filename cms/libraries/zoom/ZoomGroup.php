<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

class ZoomGroup
{
    // vars
    protected string $label = '';
    protected string $content = '';

    /**
     * Set
     */
    public function __construct(string $content = '')
    {
        $this->content = $content;
    }

    /**
     * Get
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Set
     */
    public function setLabel(string $label = ''): self
    {
        $this->label = $label;
        return $this;
    }

    /**
     * Get
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Set
     */
    public function setLink(?string $href, $attr = []): self
    {
        $attr = array_merge([
            'icon'   => 'fa-solid fa-external-link',
            'href'   => $href,
            'title'  => _t('Go'),
            'target' => '_blank'
        ], $attr);

        return $attr['href']
            ? $this->setIcon($attr)
            : $this;
    }

    /**
     * Set
     */
    public function setIcon(array $attr): self
    {
        $icon = '<i class="' . $this->extract($attr, 'icon', 'fa-solid fa-chevron-right') . '" aria-hidden="true"></i>';
        $title = $this->extract($attr, 'title', '');

        if ($title) {
            $icon .= ' <span class="visually-hidden">' . $title . '</span>';
        }

        if (isset($attr['href'])) {
            $icon = '<a' . $this->attr($attr) . '>' . $icon . '</a>';
        } else {
            $icon = '<button' . $this->attr($attr) . '>' . $icon . '</button>';
        }


        $this->content .= ' ' . $icon;

        return $this;
    }

    /**
     * Merge attributes
     */
    protected function attr(array $a, array $b = []): string
    {
        if ($b) {
            if (isset($b['class'])) {
                $a['class'] .= ' ' . $b['class'];
                unset($b['class']);
            }

            $a = array_merge($a, $b);
        }

        $output  = '';
        foreach ($a as $n => $v) {
            $output .=  ' ' . $n . '="' . $v . '"';
        }

        return $output;
    }

    /**
     * Extract attributes
     */
    protected function extract(array &$attr, string $name, mixed $value = ''): mixed
    {
        if (isset($attr[$name])) {
            $value = $attr[$name];
            unset($attr[$name]);
        }

        return $value;
    }
}
