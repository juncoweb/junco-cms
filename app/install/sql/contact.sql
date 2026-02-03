-- 
-- #__contact
-- 

-- DROP TABLE IF EXISTS `#__contact`;
CREATE TABLE IF NOT EXISTS `#__contact` (
  `id` int unsigned NOT NULL auto_increment,
  `user_id` int unsigned NOT NULL,
  `user_ip` varbinary(16) NOT NULL,
  `contact_name` varchar(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `contact_email` varchar(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `contact_message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

