-- 
-- contact
-- 

-- DROP TABLE IF EXISTS `contact`;
CREATE TABLE IF NOT EXISTS `#__contact` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `user_ip` varbinary(16) NOT NULL,
  `contact_name` varchar(48) COLLATE utf8mb4_general_ci NOT NULL,
  `contact_email` varchar(48) COLLATE utf8mb4_general_ci NOT NULL,
  `contact_message` text COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

