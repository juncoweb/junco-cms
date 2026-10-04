<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

class rating_detail_form_default_snippet
{
    /**
     * Render
     * 
     * @param array $value
     */
    public function render(array $value): string
    {
        $num_ratings = array_sum($value);
        $rating_value = implode('|', $value);

        $html = '';
        $html .= '<div class="rating-main">';
        $html .=   '<div><span class="rating-average" aria-hidden="true"></span></div>';
        $html .=   '<div>';
        $html .=      '<div class="rating rating-large rating-warning" data-rating="' . $rating_value . '" aria-label="' . _t('Rating $val of 5') . '"></div>';
        $html .=      '<div class="color-subtle">' . sprintf(_nt('%d rating', '%d ratings', $num_ratings), $num_ratings) . '</div>';
        $html .=   '</div>';
        $html .= '</div>';

        return '<div class="rating-detail" data-rating-detail="' . $rating_value . '">' . $html . '</div>';
    }
}
