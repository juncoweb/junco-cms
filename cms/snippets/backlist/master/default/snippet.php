<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Backlist\Contract\BacklistInterface;
use Junco\Backlist\Contract\FiltersInterface;

class backlist_master_default_snippet implements BacklistInterface
{
    // vars
    protected array  $th           = [];
    protected int    $th_i         = 0;
    protected array  $td           = [];
    protected int    $td_i         = 0;
    protected int    $tr_i         = 0;
    protected string $index        = 'id';
    protected array  $labels       = [];
    protected array  $not_repeated = [];
    protected string $hiddens      = '';
    protected string $title_tag    = '';
    //
    protected FiltersInterface $filters;

    /**
     * Constructor
     */
    public function __construct()
    {
        $btn_caption = config('backlist.btn_caption');

        if ($btn_caption == 'responsive') {
            $this->title_tag = '<span class="visually-responsive-hidden"> %s</span>';
        } elseif ($btn_caption == 'hidden') {
            $this->title_tag = '<span class="visually-hidden">%s</span>';
        } else {
            $this->title_tag = ' %s';
        }
    }

    /**
     * Filters
     * 
     * @param string $snippet
     * 
     * @return FiltersInterface
     */
    public function getFilters(string $snippet = ''): FiltersInterface
    {
        return $this->filters = snippet('backlist#filters', $snippet);
    }

    /**
     * Table header
     * 
     * @param string|array|null $content
     * @param ?array            $options
     */
    public function th(string|array|null $content = '', ?array $options = null)
    {
        if (is_array($content)) {
            $options = $content;
            $options['content'] ??= '';
        } elseif ($options === null) {
            $options = ['content' => $content];
        } else {
            $options['content'] = $content;
        }

        if (!empty($options['sort'])) {
            $options['content'] = $this->filters->sort_h($options['content']);
        }

        if (isset($options['priority']) && $options['priority'] == 2) {
            if (empty($options['class'])) {
                $options['class'] = 'table-dimmed';
            } else {
                $options['class'] .= ' table-dimmed';
            }
        }

        $width = '';
        if (isset($options['width'])) {
            if (is_numeric($options['width'])) {
                $options['width'] .= 'px';
            }

            $width = ' style="width: ' . $options['width'] . ';"';
            unset($options['width']);
        }

        $options['class'] = empty($options['class']) ? '' : ' class="' . $options['class'] . '"';
        $options['html'] = '<th' . $width . $options['class'] . '>' . $options['content'] . '</th>';

        $this->th[] = $options;
        $this->th_i++;
    }

    /**
     * Cell
     * 
     * @param string $html
     */
    public function td(?string $html = '')
    {
        $this->td[$this->tr_i][] = '<td' . $this->th[$this->td_i]['class'] . '>' . $html . '</td>';
        $this->td_i++;

        if ($this->td_i == $this->th_i) {
            $this->td_i = 0;
            $this->tr_i++;
        }
    }

    /**
     * Body
     * 
     * @param string $html_1
     */
    public function body(string $html_1 = '', $html_2 = true, $length = 140)
    {
        if ($html_2 === true) {
            if (!$html_1 || (($html_3 = strip_tags($html_1)) == $html_1 && mb_strlen($html_3) < $length)) {
                $html_2 = false;
            } else {
                $html_2 = $html_1;
                $html_1 = mb_substr($html_3, 0, mb_strrpos(mb_substr($html_3, 0, $length), ' '));
            }
        }
        if ($html_2) {
            $html_1 = '<div>' . $html_1 . '</div><div>' . $html_2 . '</div><a href="javascript:void(0)" control-list="tg-body" class="kl-tg"></a>';
        }
        return '<div class="kl-body">' . $html_1 . '<div>';
    }

    /**
     * Is repeated
     */
    public function isRepeated($value, bool $return_bool = false)
    {
        $this->not_repeated[$this->td_i] ??= false;

        if ($this->not_repeated[$this->td_i] === $value) {
            return '<span class="quo">" "</span>';
        }
        $this->not_repeated[$this->td_i] = $value;

        return $return_bool ? false : $value;
    }

    /**
     * Labels
     * 
     * @param array $labels
     */
    public function setLabels(array $labels)
    {
        if (isset($this->labels[$this->tr_i])) {
            $this->labels[$this->tr_i] = array_merge($this->labels[$this->tr_i], $labels);
        } else {
            $this->labels[$this->tr_i] = $labels;
        }
    }

    /**
     * Label
     * 
     * @param string ...$labels
     */
    public function setLabel(string ...$labels)
    {
        if (isset($this->labels[$this->tr_i])) {
            $this->labels[$this->tr_i] = array_merge($this->labels[$this->tr_i], $labels);
        } else {
            $this->labels[$this->tr_i] = $labels;
        }
    }

    /**
     * Set index
     * 
     * @param string $value
     */
    public function setIndex(string $value)
    {
        $this->index = $value;
    }

    /**
     * Check header
     */
    public function check_h()
    {
        $this->th([
            'width' => 20,
            'content' => '<input type="checkbox" control-row="check-all" aria-label="' . _t('Select all') . '" class="input-checkbox"/>',
            'tag' => '<input type="checkbox" name="%s[]" value="%s" title="ID %s"%s aria-label="' . _t('Select row') . '" class="input-checkbox"/>'
        ]);
    }

    /**
     * Check
     * 
     * @param string $id
     * @param bool   $is_enabled
     */
    public function check(string $id = '', bool $is_enabled = true)
    {
        $this->td(sprintf($this->th[$this->td_i]['tag'], $this->index, $id, $id, ($is_enabled ? '' : ' disabled')));
    }

    /**
     * Up down header
     */
    public function up_down_h()
    {
        $attr = [
            'href'         => 'javascript:void(0)',
            'control-list' => 'updown',
            'data-value'   => 'up',
            'class'        => 'btn-inline',
            'title'        => ($title = _t('Up')),
            'role'         => 'button',
        ];

        $this->th([
            'width' => 20,
            'tag' => '<a' . $this->attr($attr) . '><i class="fa-solid fa-chevron-up" aria-hidden="true"></i>' . sprintf($this->title_tag, $title) . '</a>'
        ]);

        $attr = array_merge($attr, [
            'data-value' => 'down',
            'title'      => ($title = _t('Down')),
        ]);
        $this->th([
            'width' => 20,
            'tag' => '<a' . $this->attr($attr) . '><i class="fa-solid fa-chevron-down" aria-hidden="true">' . sprintf($this->title_tag, $title) . '</i></a>'
        ]);
    }

    /**
     * Up down
     */
    public function up_down($up, $down)
    {
        $this->td($up ? $this->th[$this->td_i]['tag'] : '');
        $this->td($down ? $this->th[$this->td_i]['tag'] : '');
    }

    /**
     * Link header
     *
     * @param string|array $content
     * @param array        $data
     */
    public function link_h(string|array $content = '', array $data = [])
    {
        if (is_array($content)) {
            $data = $content;
            $content = null;
        }
        if (isset($data['options'])) {
            $options = $data['options'];
        } else {
            $options = [];
        }
        if ($content !== null) {
            $options['content'] = $content;
        }

        $attr = [];
        if (isset($data['control'])) {
            $attr['href'] = 'javascript:void(0)';
            $attr['control-list'] = $data['control'] ?: '{{ control }}';
        } else {
            $attr['href'] = empty($data['url']) ? '{{ url }}' : $data['url'];
        }
        if (isset($data['title'])) {
            $attr['title'] = $data['title'] ?: '{{ title }}';
        } else {
            $attr['title'] = _t('Show');
        }
        if (isset($data['class'])) {
            $attr['class'] = $data['class'] ?: '{{ class }}';
        } else {
            $attr['class'] = 'table-linked';
        }
        if (!empty($data['attr'])) {
            $attr = array_merge($attr, $data['attr']);
        }

        $caption = '';
        if (isset($data['icon'])) {
            if (!$data['icon']) {
                $data['icon'] = '{{ icon }}';
            }
            if (isset($data['caption'])) {
                $caption = '<i class="' . $data['icon'] . '" aria-hidden="true"></i> ';
            } else {
                $caption = '<i class="' . $data['icon'] . '" aria-label="' . $attr['title'] . '"></i>';
                $options['width'] ??= 20;
            }
        }
        if (isset($data['caption'])) {
            $caption .= $data['caption'] ?: '{{ caption }}';
        } elseif (!$caption) {
            $caption = '{{ caption }}';
        }

        $options['tag'] = '<a' . $this->attr($attr) . '>' . $caption . '</a>';

        $this->th($options);
    }

    /**
     * Link
     *
     * @param string|array $data
     * @param bool         $is_enabled
     */
    public function link(string|array|null $data = null, $is_enabled = true)
    {
        if (!$is_enabled) {
            return $this->td();
        }
        if ($data === null) {
            return $this->td($this->th[$this->td_i]['tag']);
        }

        $before = '';
        $after  = '';
        if (is_array($data)) {
            if (isset($data['before'])) {
                $before = $data['before'];
                unset($data['before']);
            }
            if (isset($data['after'])) {
                $after = $data['after'];
                unset($data['after']);
            }
        } else {
            $data = ['url' => $data];
        }

        $replace = [];
        foreach ($data as $key => $value) {
            $replace["{{ $key }}"] = $value;
            $replace["__{$key}__"] = $value;
        }

        $this->td($before . strtr($this->th[$this->td_i]['tag'], $replace) . $after);
    }

    /**
     * Search header
     *
     * @param string $content
     * @param array  $data
     */
    public function search_h(string $content = '', array $data = [])
    {
        $base = [
            'url'   => 'javascript:void(0)',
            'title' => _t('Search'),
            'attr'  => [
                'control-filter' => 'search',
                'data-value' => '{{ value }}'
            ],
            'options' => ['content' => $content]
        ];

        if (isset($data['field'])) {
            $base['attr']['data-field'] = $data['field'];
            unset($data['field']);
        }
        if (isset($data['control'])) {
            $base['attr']['control-filter'] = $data['control'];
            unset($data['control']);
        }
        if ($data) {
            $base = array_merge_recursive($base, $data);
        }

        $this->link_h($base);
    }

    /**
     * Search
     *
     * @param string|array|null $data
     * @param ?string           $caption
     * @param bool              $is_enabled
     */
    public function search(string|array|null $data = null, ?string $caption = null, bool $is_enabled = true)
    {
        if (!is_array($data)) {
            $data = ['value' => $data];
            if ($caption !== null) {
                $data['caption'] = $caption;
            }
        }

        $this->link($data, $is_enabled);
    }

    /**
     * Button h
     *
     * @param string|array $control
     * @param string       $title
     * @param string       $icon
     */
    public function button_h(string|array $control = '', string $title = '', string $icon = '')
    {
        $options = ['width' => 20];
        $attr    = [];
        $caption = '';

        if (is_array($control)) {
            $data    = $control;
            $control = $data['control'] ?? null;
            $title   = $data['title'] ?? '';
            $icon    = $data['icon'] ?? null;

            if (isset($data['options'])) {
                $options = array_merge($options, $data['options']);
            }
            if (isset($data['attr'])) {
                $attr = $data['attr'];
            }
            if (isset($data['caption'])) {
                $caption .= ($data['caption'] ?: '{{ caption }}');
            }
        }

        if (!$title) {
            $title = '{{ title }}';
        }
        if (isset($icon)) {
            if (!$caption) {
                $caption = sprintf($this->title_tag, $title);
            } else {
                $caption = ' ' . $caption;
            }
            $caption = '<i class="' . ($icon ?: '{{ icon }}') . '" aria-hidden="true"></i>' . $caption;
        } elseif (!$caption) {
            $caption = $title;
        }

        if (isset($control)) {
            $attr = array_merge([
                'href'         => 'javascript:void(0)',
                'control-list' => ($control ?: '{{ control }}'),
                'class'        => 'btn-inline',
                'role'         => 'button',
                'title'        => $title
            ], $attr);

            $options['tag'] = '<a' . $this->attr($attr) . '>' . $caption . '</a>';
        } else {
            $attr = array_merge([
                'class' => 'btn-inline',
                'title' => $title
            ], $attr);

            $options['tag'] = '<div' . $this->attr($attr) . '>' . $caption . '</div>';
        }

        $this->th($options);
    }

    /**
     * Button
     *
     * @param array $data
     */
    public function button(array $data = [], bool $is_enabled = true)
    {
        if (!$is_enabled) {
            return $this->td();
        }
        if (!$data) {
            return $this->td($this->th[$this->td_i]['tag']);
        }

        $replace = [];
        foreach ($data as $key => $value) {
            $replace["{{ $key }}"] = $value;
        }

        $this->td(strtr($this->th[$this->td_i]['tag'], $replace));
    }

    /**
     * Status header
     *
     * @param string $control
     */
    public function status_h(string|false $control = '')
    {
        $t0 = _t('Private');
        $t1 = _t('Public');

        if ($control === false) {
            $tag = [
                '<div class="btn-inline" title="' . $t0 . '"><i class="fa-solid fa-circle color-red"></i>' . sprintf($this->title_tag, $t0) . '</div>',
                '<div class="btn-inline" title="' . $t1 . '"><i class="fa-solid fa-circle color-green"></i>' . sprintf($this->title_tag, $t1) . '</div>',
            ];
        } else {
            if (!$control) {
                $control = 'status';
            }
            $tag = [
                '<a href="javascript:void(0)" control-list="' . $control . '" class="btn-inline" title="' . $t0 . '" role="button"><i class="fa-solid fa-circle color-red"></i>' . sprintf($this->title_tag, $t0) . '</a>',
                '<a href="javascript:void(0)" control-list="' . $control . '" class="btn-inline" title="' . $t1 . '" role="button"><i class="fa-solid fa-circle color-green"></i>' . sprintf($this->title_tag, $t1) . '</a>',
            ];
        }

        $this->th([
            'width' => 20,
            'tag' => $tag
        ]);
    }

    /**
     * Status
     *
     * @param string $index
     */
    public function status(string $index)
    {
        if ($index == 'yes') {
            $index = 1;
        } elseif ($index == 'no') {
            $index = 0;
        }

        $this->td($this->th[$this->td_i]['tag'][$index]);
    }

    /**
     * Hidden
     * 
     * @param string $name
     * @param string $value
     */
    public function hidden(string $name, string $value = '')
    {
        $this->hiddens .= '<input type="hidden" name="' . $name . '"value="' . $value . '"/>';
    }

    /**
     * Render
     * 
     * @param string $pagi
     * @param string $empty_list
     * 
     * @return string
     */
    public function render(string $pagi = '', string $empty_list = ''): string
    {
        $html = '';

        if ($this->tr_i) {
            foreach ($this->td as $i => $td) {
                $html .= '<tr control-row' . (isset($this->labels[$i]) ? ' row-labels="' . implode(',', $this->labels[$i]) . '"' : '') . '>' . implode('', $td) . '</tr>';
            }
        }

        $html = '<form backlist-form>'
            . '<div class="table-responsive">'
            . '<table class="table table-highlight table-striped">'
            .  '<thead><tr>' . implode(array_column($this->th, 'html')) . '</tr></thead>'
            .  '<tbody>' . $html . '</tbody>'
            . '</table></div>'
            . $this->hiddens
            . FormSecurity::getToken()
            . '</form>';

        if (!$this->tr_i) {
            $html .= '<div class="empty-list"><p>' . ($empty_list ?: _t('Empty list')) . '</p></div>';
        }

        /*if (isset($this->pagination)) {
			$pagi = $this->pagination->render();
		}*/

        if ($pagi) {
            $html .= '<div class="footer">' . $pagi . '</div>';
        }

        // freeing memory
        $this->th           = [];
        $this->th_i         = 0;
        $this->td           = [];
        $this->td_i         = 0;
        $this->tr_i         = 0;
        $this->labels       = [];
        $this->not_repeated = [];
        $this->hiddens      = '';

        return (isset($this->filters) ? $this->filters->render() : '')
            . '<div class="backlist-wrapper">' . $html . '</div>';
    }

    /**
     * Attr
     * 
     * @param array $attr
     * 
     * @return string
     */
    protected function attr(array $attr): string
    {
        $html = '';
        foreach ($attr as $k => $v) {
            $html .=  ' ' . $k . '="' . $v . '"';
        }

        return $html;
    }
}
