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
            $btn = [];

            if ($allow_reply) {
                $reply = _t('Reply');
                $btn[] = '<a href="javascript:void(0)" role="button" control-list="reply" title="' . $reply . '">' . $reply . '</a>';
            }

            if ($allow_vote) {
                $btn[] = '<a href="javascript:void(0)" role="button" control-list="vote_up" title="' . _t('Add a vote') . '"><i class="fa-solid fa-thumbs-up"></i></a>'
                    . '<a href="javascript:void(0)" role="button" control-list="vote_down" title="' . _t('Subtract vote') . '"><i class="fa-solid fa-thumbs-down"></i></a>'
                    . '<div class="votes"><div class="visually-hidden">' . _t('Total votes %s') . '</div>%s</div>';
            }

            if ($allow_report) {
                $btn[] = '<a href="javascript:void(0)" role="button" control-list="report" title="' . _t('Report') . '"><i class="fa-solid fa-flag"></i></a>';
            }

            if ($allow_delete) {
                $btn[] = '<a href="javascript:void(0)" role="button" control-list="trash" title="' . _t('Trash') . '"><i class="fa-solid fa-trash"></i></a>';
            }

            $actions = '<span aria-hidden="true">·</span>' . implode('<span aria-hidden="true">·</span>', $btn);

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

                $num_votes = '';
                if ($row['num_votes'] > 0) {
                    $num_votes = '<span class="color-success" aria-hidden="true">+' . $row['num_votes'] . '</span>';
                } elseif ($row['num_votes'] < 0) {
                    $num_votes = '<span class="color-danger" aria-hidden="true">' . $row['num_votes'] . '</span>';
                }

                $html .= "\n\t" . '<div control-row="' . $row['id'] . '"  class="list-row' . $class . '">';
                $html .= '<div class="header">' . implode(' · ', $header) . '</div>'
                    . '<div class="message">' . $row['response_to'] . $row['message'] . '</div>'
                    . '<div class="actions">' . sprintf($actions, $row['num_votes'], $num_votes) . '</div>';

                if ($allow_reply) {
                    $html .= '<div control-form></div>';
                }

                $html .= '</div>';
            }

            $this->rows = []; // freeing memory
        } else {
            $html = '<div class="empty-list">' . ($this->empty_list ?: _t('Empty list')) . '</div>';
        }

        $html = "\n" . '<div class="comments-list">' . $html . "\n" . '</div>' . "\n";

        if (isset($this->filters)) {
            $html = $this->filters->render() . "\n" . $html;
        }

        if ($pagi) {
            $html = '<div class="article-pagination">' . $pagi . '</div>' . "\n" . $html;
        }

        return $html;
    }
}
