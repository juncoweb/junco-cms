<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

return function (&$rows) {
    // query
    $rows['default_ucid']['options'] = db()->query("
	SELECT id, role_name
	FROM `#__users_roles`
	WHERE id NOT IN (
		SELECT role_id
		FROM `#__users_roles_labels_map`
		WHERE label_id = ?
		AND status = 1
	)
	ORDER BY role_name", L_SYSTEM_ADMIN)->fetchAll(Database::FETCH_COLUMN, [0 => 1], ['--- ' . _t('Select') . ' ---']);

    $rows['password_level']['options'] = [
        '0 - ' . _t('No requirement'),
        '1 - ' . _t('At least one number'),
        '2 - ' . _t('At least one number and one uppercase'),
        '3 - ' . _t('At least one number, one uppercase and one symbol'),
    ];

    $rows['locks_level']['options'] = [
        '0 - ' . _t('Do not lock'),
        '1 - ' . _t('Low'),
        '2 - ' . _t('Medium'),
        '3 - ' . _t('High'),
    ];
};
