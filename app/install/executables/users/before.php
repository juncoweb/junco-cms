<?php

/**
 * Migrating from version 14.0 to 14.1
 */

$db = db();
$rows = $db->query("
SELECT
 id ,
 activity_code ,
 activity_context
FROM `#__users_activities`
WHERE activity_type = 'token'")->fetchAll();

$stmt = $db->prepare("UPDATE `#__users_activities` SET activity_type = ?, activity_code = ?, activity_context = ? WHERE id = ?");

foreach ($rows as $row) {
    $context = (array)json_decode($row['activity_context'], true);
    $row['activity_context'] = json_encode([
        'activity_id' => $context['activity_id'],
        'code'        => $row['activity_code'],
        'token'       => $context['token'],
    ]);

    $db->exec($stmt, $context['type'], -20, $row['activity_context'], $row['id']);
}
