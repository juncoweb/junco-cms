<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Controller;

class FrontUsysEmailController extends Controller
{
    /**
     * Save
     */
    public function save()
    {
        return $this->view(null, (new FrontUsysEmailModel)->getSaveData());
    }
}
