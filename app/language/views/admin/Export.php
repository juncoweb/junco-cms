<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// blank
$blank = Responder::asHttpBlank();
$blank->appendFile($file, $filename) and unlink($file);

return $blank->response();
