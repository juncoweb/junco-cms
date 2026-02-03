-- 
-- #__notifications
-- 

-- DROP TABLE IF EXISTS `#__notifications`;
CREATE TABLE IF NOT EXISTS `#__notifications` (
  `id` int NOT NULL auto_increment,
  `user_id` int unsigned NOT NULL,
  `notification_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `notification_id` int NOT NULL DEFAULT 0,
  `notification_message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NULL on update CURRENT_TIMESTAMP,
  `read_at` datetime NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `notification_type` (`notification_type`)
) ENGINE=InnoDB  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

