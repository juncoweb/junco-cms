<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// list
$bls = Backlist::get();

// filters
$filters = $bls->getFilters();
$filters->setValues($data);
$filters->search();

// table
$bls->setRows($rows);
//
$bls->check();
$bls->column(':role_name')
    ->setLabel(_t('Name'));

return $bls->render($pagi);
