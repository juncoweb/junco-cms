<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// modal
$modal = Modal::get();
$modal->title(($extension_name ?: $extension_alias) . ' (' . $update_version . ')');
$modal->close();
$modal->content = $failure_msg;

return $modal->response();
