-- 
-- notifications
-- 

-- DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `#__notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `notification_type` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `notification_id` int NOT NULL DEFAULT '0',
  `notification_message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `read_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `notification_type` (`notification_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

