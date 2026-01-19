<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Frontlist\FrontlistBase;

class frontlist_master_comments_snippet extends FrontlistBase
{
    // vars
    protected array $row = [
        'id'          => '',
        'date'        => '',
        'author'      => '',
        'response_to' => '',
        'message'     => '',
        'rating'      => '',
        'num_votes'   => 0,
    ];

    /**
     * Render
     * 
     * @param string $pagi
     * 
     * @return string
     */
    public function render(string $pagi = ''): string
    {
        $html = '';

        if ($this->rows) {
            $allow_reply  = $this->getOption('allow_reply');
            $allow_vote   = $this->getOption('allow_vote');
            $allow_report = $this->getOption('allow_report');
            $allow_delete = $this->getOption('allow_delete');
            $btn          = '';

            if ($allow_reply) {
                $reply = _t('Reply');
                $btn .= '·<a href="javascript:void(0)" control-list="reply" title="' . $reply . '">' . $reply . '</a>';
            }

            if ($allow_vote) {
                $btn .= '·<a href="javascript:void(0)" control-list="vote_up" title="' . _t('Add a vote') . '"><i class="fa-solid fa-thumbs-up"></i></a>'
                    . '<a href="javascript:void(0)" control-list="vote_down" title="' . _t('Subtract vote') . '"><i class="fa-solid fa-thumbs-down"></i></a>'
                    . '<div class="votes">%s</div>';
            }

            if ($allow_report) {
                $btn .= '·<a href="javascript:void(0)" control-list="report" title="' . _t('Report') . '"><i class="fa-solid fa-flag"></i></a>';
            }

            if ($allow_delete) {
                $btn .= '·<a href="javascript:void(0)" control-list="trash" title="' . _t('Trash') . '"><i class="fa-solid fa-trash"></i></a>';
            }

            foreach ($this->rows as $row) {
                $header = [];
                if ($row['author']) {
                    $header[] = '<span id="' . $row['id'] . '" class="author">' . $row['author'] . '</span>';
                }

                if ($row['date']) {
                    $header[] = '<span class="date">' . $row['date'] . '</span>';
                }

                if ($row['rating']) {
                    $header[] = '<span class="l-rating">' . $row['rating'] . '</span>';
                }

                $class = '';

                if ($row['response_to']) {
                    $class = ' response';
                    $row['response_to'] = '<span class="to">@' . $row['response_to'] . '</span> ';
                }

                if ($row['num_votes'] > 0) {
                    $row['num_votes'] = '<span class="color-success">+' . $row['num_votes'] . '</span>';
                } elseif ($row['num_votes'] < 0) {
                    $row['num_votes'] = '<span class="color-danger">' . $row['num_votes'] . '</span>';
                } else {
                    $row['num_votes'] = '';
                }

                $html .= "\n\t" . '<div control-row="' . $row['id'] . '"  class="list-row' . $class . '">';
                $html .= '<div class="header">' . implode(' · ', $header) . '</div>'
                    . '<div class="message">' . $row['response_to'] . $row['message'] . '</div>'
                    . '<div class="options">' . sprintf($btn, $row['num_votes']) . '</div>';

                if ($allow_reply) {
                    $html .= '<div control-form></div>';
                }

                $html .= '</div>';
            }

            $html = "\n" . '<div class="fl-comments">' . $html . "\n" . '</div>' . "\n";
            $this->rows = []; // freeing memory
        } else {
            $html = '<div class="empty-list">' . ($this->empty_list ?: _t('Empty list')) . '</div>' . "\n";
        }

        if (isset($this->filters)) {
            $html = $this->filters->render() . "\n" . $html;
        }

        if ($pagi) {
            $html = '<div class="article-pagination">' . $pagi . '</div>' . "\n" . $html;
        }

        return $html;
    }
}
