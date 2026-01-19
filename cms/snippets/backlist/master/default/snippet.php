<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Backlist\Column\Button;
use Junco\Backlist\Column\Check;
use Junco\Backlist\Column\Column;
use Junco\Backlist\Column\Control;
use Junco\Backlist\Column\Link;
use Junco\Backlist\Column\Search;
use Junco\Backlist\Contract\BacklistInterface;
use Junco\Backlist\Contract\FiltersInterface;

class backlist_master_default_snippet implements BacklistInterface
{
    // vars
    protected array  $rows    = [];
    protected array  $columns = [];
    protected string $labels  = '';
    protected string $hiddens = '';
    //
    protected FiltersInterface $filters;

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
     * Set
     * 
     * @param array $rows
     * 
     * @return void
     */
    public function setRows(array $rows): void
    {
        $this->rows = $rows;
    }

    /**
     * Labels
     * 
     * @param string $name
     * 
     * @return void
     */
    public function setLabels(string $name): void
    {
        // legacy!!!
        foreach ($this->rows as $i => $row) {
            if (!isset($row[$name])) {
                $this->rows[$i][$name] = '';
            } elseif (is_array($row[$name])) {
                $this->rows[$i][$name] = implode(' ', $row[$name]);
            }
        }

        $this->labels = '  labels-list="{{ ' . $name . ' }}"';
    }

    /**
     * Fix
     *
     * @param string       $name
     * @param string       $name
     * @param array|string $formats
     * 
     * @return void
     */
    public function fixDate(string $name, string $date_format, array|string $formats = ''): void
    {
        if ($formats) {
            if (is_string($formats)) {
                $formats = ['time' => $formats];
            }
            $formats['date'] = $date_format;

            foreach ($this->rows as $i => $row) {
                $dt = $row[$name] ?? null;

                if (is_string($dt)) {
                    $dt = new Date($dt);
                } else {
                    // @deprecated on v15.1
                    $trace = debug_backtrace();
                    trigger_error(
                        'Deprecated property type "Date" for Backlist::fixDate() in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'],
                        E_USER_NOTICE
                    );
                }

                foreach ($formats as $key => $format) {
                    $this->rows[$i]["$name.$key"] = $dt->format($format);
                }
            }
        } else {
            foreach ($this->rows as $i => $row) {
                $dt = $row[$name] ?? null;

                if (is_string($dt)) {
                    $dt = new Date($dt);
                } else {
                    // @deprecated on v15.1
                    $trace = debug_backtrace();
                    trigger_error(
                        'Deprecated property type "Date" for Backlist::fixDate() in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'],
                        E_USER_NOTICE
                    );
                }

                $this->rows[$i][$name] = $dt?->format($date_format);
            }
        }
    }

    /**
     * Fix
     *
     * @param string $name
     * @param string $separator
     * 
     * @return void
     */
    public function fixList(string $name, string $separator = ', '): void
    {
        foreach ($this->rows as $i => $row) {
            $this->rows[$i][$name] = implode($separator, $row[$name]);
        }
    }

    /**
     * Fix
     *
     * @param string $name
     * 
     * @return void
     */
    public function fixEnum(string $name = 'status', ?array $options = null): void
    {
        foreach ($this->rows as $i => $row) {
            $value = $row[$name];

            if (!is_array($value)) {
                $options ??= [
                    ['title' => _t('Private'), 'color' => 'red'],
                    ['title' => _t('Public'), 'color' => 'green'],
                ];

                if (!isset($options[$value])) {
                    if ($value == 'yes') {
                        $value = 1;
                    } elseif ($value == 'no') {
                        $value = 0;
                    }
                }

                $value = $options[$value];
            }

            foreach ($value as $k => $v) {
                $this->rows[$i]["$name.$k"] = $v;
            }
        }
    }

    /**
     * Fix
     *
     * @param string  $name
     * @param ?string $replace
     * 
     * @return void
     */
    public function fixRepeats(string $name, ?string $replace = null): void
    {
        $current = null;

        foreach ($this->rows as &$row) {
            if ($row[$name] == $current) {
                $row[$name] = ($replace ??= '<span class="quo">" "</span>');
            } else {
                $current = $row[$name];
            }
        }
    }

    /**
     * Apply
     *
     * @param callable $fn
     * 
     * @return void
     */
    public function apply(callable $fn): void
    {
        foreach ($this->rows as &$row) {
            $fn($row);
        }
    }

    /**
     * Column
     * 
     * @param string $column
     * 
     * @return Column
     */
    public function column(string $column = ''): Column
    {
        return $this->columns[] = new Column($column);
    }

    /**
     * Link
     *
     * @param string $url
     * 
     * @return Link
     */
    public function link(string $url = ''): Link
    {
        return $this->columns[] = new Link($url);
    }

    /**
     * Control
     *
     * @param string $control
     * 
     * @return Control
     */
    public function control(string $control): Control
    {
        return $this->columns[] = new Control($control);
    }

    /**
     * Button
     *
     * @param string $control
     * 
     * @return Button
     */
    public function button(string $control = ''): Button
    {
        return $this->columns[] = new Button($control);
    }

    /**
     * Search
     *
     * @param string $column
     * @param string $value
     * @param string $field
     * 
     * @return SearchInterface
     */
    public function search(string $column, string $value, string $field = ''): Search
    {
        return $this->columns[] = new Search($column, $value, $field);
    }

    /**
     * Check
     * 
     * @param string $value
     * @param string $index
     * 
     * @return void
     */
    public function check(string $value = ':id', string $index = ''): void
    {
        $this->columns[] = new Check($value, $index);
    }

    /**
     * Up
     * 
     * @param string $control
     * @param string $name
     * 
     * @return void
     */
    public function up(string $control = '', string $name = ''): void
    {
        $this->columns[] = (new Button($control ?: 'up'))
            ->setIcon('fa-solid fa-chevron-up', _('Up'))
            ->setAttr(['data-value' => 'up'])
            ->keep($name ?: 'up');
    }

    /**
     * Down
     * 
     * @param string $control
     * @param string $name
     * 
     * @return void
     */
    public function down(string $control = '', string $name = ''): void
    {
        $this->columns[] = (new Button($control ?: 'down'))
            ->setIcon('fa-solid fa-chevron-down', _('Down'))
            ->setAttr(['data-value' => 'down'])
            ->keep($name ?: 'down');
    }

    /**
     * Status
     * 
     * @param string $control
     * @param string $name
     * 
     * @return void
     */
    public function status(string $control = '', string $name = 'status'): void
    {
        $this->columns[] = (new Button($control))
            ->setIcon('fa-solid fa-circle color-{{ ' . $name . '.color }}', '{{ ' . $name . '.title }}');
    }

    /**
     * Hidden
     * 
     * @param string $name
     * @param string $value
     * 
     * @return void
     */
    public function hidden(string $name, string $value = ''): void
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
        $thead = array_reduce($this->columns, fn($th, $column) => $th .= $column->th(), '');
        $tbody = $this->rows
            ? $this->tbody()
            : '';

        $html = '<form backlist-form>'
            . '<div class="table-responsive">'
            . '<table class="table table-highlight table-striped">'
            .  '<thead><tr>' . $thead . '</tr></thead>'
            .  '<tbody>' . $tbody . '</tbody>'
            . '</table></div>'
            . $this->hiddens
            . FormSecurity::getToken()
            . '</form>';

        if (!$this->rows) {
            $html .= '<div class="empty-list"><p>' . ($empty_list ?: _t('Empty list')) . '</p></div>';
        }

        /*if (isset($this->pagination)) {
			$pagi = $this->pagination->render();
		}*/

        if ($pagi) {
            $html .= '<div class="footer">' . $pagi . '</div>';
        }

        return (isset($this->filters) ? $this->filters->render() : '')
            . '<div class="backlist-wrapper">' . $html . '</div>';
    }

    /**
     * Table body
     * 
     * @return string
     */
    protected function tbody(): string
    {
        if (!$this->rows) {
            return '';
        }

        $tbody = '';
        $tr = '<tr control-row' . $this->labels . '>'
            .  array_reduce($this->columns, fn($td, $column) => $td .= $column->td(), '')
            . '</tr>';
        $map = [];

        $this->extractReplaces($tr, $replaces, $map);

        foreach ($this->rows as $row) {
            foreach ($map as $key => $index) {
                $replaces[$key] = $row[$index] ?? '';
            }

            $tbody .= strtr($tr, $replaces);
        }

        return $this->filterKeep($tbody);
    }

    /**
     * Extract
     * 
     * @param string $tr
     * @param ?array &$replaces
     * @param ?array &$map
     * 
     * @return void
     */
    protected function extractReplaces(string $tr, ?array &$replaces = [], ?array &$map = []): void
    {
        if (!preg_match_all('/\{\{ (.*?) \}\}/', $tr, $matches, PREG_SET_ORDER)) {
            return;
        }

        foreach ($matches as $match) {
            $replaces[$match[0]] = null;
            $map[$match[0]] = $match[1];
        }
    }

    /**
     * Filter
     * 
     * @return string
     */
    protected function filterKeep(string $tbody): string
    {
        if (false !== strpos($tbody, '<!-- keep=')) {
            $tbody = preg_replace('/<!-- keep=(|0|off|no) -->(.*?)<\/td>/', '</td>', $tbody);
            $tbody = preg_replace('/<!-- keep=(.*?) -->/', '', $tbody);
        }

        return $tbody;
    }
}
