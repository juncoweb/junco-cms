-- 
-- #__usys_sessions
-- 

-- DROP TABLE IF EXISTS `#__usys_sessions`;
CREATE TABLE IF NOT EXISTS `#__usys_sessions` (
  `id` bigint unsigned NOT NULL auto_increment,
  `user_id` int unsigned NOT NULL,
  `session_selector` char(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `session_validator` char(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `session_hash` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `session_ip` varbinary(16) NOT NULL,
  `session_ua` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `accessed_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE `session_selector` (`session_selector`),
  UNIQUE `session_validator` (`session_validator`, `session_hash`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

