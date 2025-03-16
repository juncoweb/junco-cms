-- 
-- session
-- 

-- DROP TABLE IF EXISTS `session`;
CREATE TABLE IF NOT EXISTS `#__session` (
  `session_id` varchar(32) COLLATE utf8mb4_general_ci NOT NULL,
  `session_data` text COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

