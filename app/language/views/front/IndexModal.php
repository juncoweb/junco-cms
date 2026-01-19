<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// modal
$modal = Modal::get();
$modal->close();
$modal->title(_t('Language'));
$modal->content($this->content());

return $modal->response();
