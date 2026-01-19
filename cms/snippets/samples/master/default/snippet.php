<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Samples\Snippet\ColorsSample;
use Junco\Samples\Snippet\HtmlSample;
use Junco\Samples\Snippet\JsSample;
use Junco\Samples\Snippet\SamplesInterface;
use Junco\Samples\Snippet\SizesSample;

class samples_master_default_snippet implements SamplesInterface
{
    // vars
    protected array $blocks  = [];
    protected array $samples = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        $assets = app('assets');
        $assets->css('assets/samples.min.css');
        $assets->js('assets/samples.min.js');
        $assets->domready('JsSamples()');
    }

    /**
     * Html
     * 
     * @param string $code
     * 
     * @return HtmlSample
     */
    public function html(string $code = ''): HtmlSample
    {
        return $this->samples[] = new HtmlSample($code);
    }

    /**
     * Js
     * 
     * @param string $code
     * 
     * @return JsSample
     */
    public function js(string $code = ''): JsSample
    {
        return $this->samples[] = new JsSample($code);
    }

    /**
     * Colors
     * 
     * @param string $code
     * 
     * @return ColorsSample
     */
    public function colors(string $code = ''): ColorsSample
    {
        return $this->samples[] = new ColorsSample($code);
    }

    /**
     * Sizes
     * 
     * @param string $code
     * 
     * @return SizesSample
     */
    public function sizes(string $code = ''): SizesSample
    {
        return $this->samples[] = new SizesSample($code);
    }

    /**
     * Separate
     * 
     * @param string $title
     * 
     * @return void
     */
    public function separate(string $title = ''): void
    {
        if (!$this->samples) {
            return;
        }
        $this->blocks[] = [
            'title' => $title,
            'samples' => $this->samples
        ];
        $this->samples = [];
    }

    /**
     * Render
     * 
     * @return bool $full
     * 
     * @return string
     */
    public function render(bool $full = false): string
    {
        $this->separate();

        $html = '';
        foreach ($this->blocks as $block) {
            if ($block['title']) {
                $html .= '<h2 class="samples-title">' . $block['title'] . '</h2>';
            }

            foreach ($block['samples'] as $sample) {
                $html .= $sample->render();
            }
        }

        Plugins::get('posting', 'load', ['prism'])?->run($html);

        return $this->wrapper($html, $full);
    }

    /**
     * Wrapper
     */
    protected function wrapper(string $html, bool $full = false): string
    {
        return '<div class="panel samples-wrapper' . ($full ? ' sample-full' : '') . '">'
            . '<div class="panel-body">' . $html . '</div>'
            . '</div>';
    }
}
