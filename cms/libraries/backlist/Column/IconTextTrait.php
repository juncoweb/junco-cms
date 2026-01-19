<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Backlist\Column;

trait IconTextTrait
{
    protected string $icon = '';
    protected string $text = '';
    protected array  $attr = [];

    /**
     * Set
     * 
     * @param string $text
     * @param string $title
     * 
     * @return static
     */
    public function setText(string $text, string $title = ''): static
    {
        if ($text) {
            $this->text = $this->normalize($text);
        }

        if ($title) {
            $this->attr['title'] = $this->normalize($title);
        }

        return $this;
    }

    /**
     * Set
     * 
     * @param string $icon
     * @param string $title
     * 
     * @return static
     */
    public function setIcon(string $icon, string $title = ''): static
    {
        $this->width = 20;

        if ($icon) {
            $this->icon = '<i class="' . $this->normalize($icon) . '" aria-hidden="true"></i>';
        }

        if ($title) {
            $this->attr['title'] = $this->normalize($title);
        }

        return $this;
    }

    /**
     * Set
     * 
     * @param array $attr
     */
    public function setAttr(array $attr): static
    {
        $this->attr = array_merge($this->attr, $attr);

        return $this;
    }

    /**
     * Get
     */
    protected function getCaption(string $text, string $icon, string $title): string
    {
        if ($icon) {
            if (!$text) {
                $text = $this->getCaptionTag($title);
            } else {
                $text = ' ' . $text;
            }
            $text = $icon . $text;
        } elseif (!$text) {
            $text = $title;
        }

        return $text;
    }

    /**
     * Get
     */
    private function getCaptionTag(string $title): string
    {
        return sprintf(match (config('backlist.btn_caption')) {
            'responsive' => '<span class="visually-responsive-hidden"> %s</span>',
            'hidden' => '<span class="visually-hidden">%s</span>',
            default => ' %s'
        }, $title);
    }
}
