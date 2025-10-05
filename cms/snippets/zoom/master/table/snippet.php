<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

class zoom_master_table_snippet extends ZoomBase
{
    /**
     * Render
     * 
     * @return string
     */
    public function render(): string
    {
        // prepare
        foreach ($this->groups as $i => $group) {
            if (is_array($group)) {
                $this->groups[$i] = [
                    'columns'  => $group,
                    'num_cols' => count($group)
                ];
            } else {
                $this->groups[$i] = [
                    'columns' => [$group],
                    'num_cols'    => 1
                ];
            }
        }

        $html = '';
        $max_cols = array_reduce($this->groups, fn($carry, $group) => max($carry, $group['num_cols']), 1);

        foreach ($this->groups as $group) {
            $tr = '';

            foreach ($group['columns'] as $i => $column) {
                $colspan = !$i
                    ? ($max_cols - $group['num_cols'])
                    : 0;

                $tr .= $this->renderGroup($column, $colspan);
            }


            $html .= '<tr>' . $tr . '</tr>';
        }

        return '<div class="table-responsive"><table class="table table-bordered">'
            . '<tbody>' . $html . '</tbody>'
            . '</table></div>';
    }

    /**
     * Render
     */
    protected function renderGroup(ZoomGroup $group, int $colspan): string
    {
        $html = '<th width="15%">' . $group->getLabel() . '</th>';
        $html .= '<td' . ($colspan ? ' colspan="' . ($colspan * 2 + 1) . '"' : '') . '>' . $group->getContent() . '</td>';

        return $html;
    }
}
