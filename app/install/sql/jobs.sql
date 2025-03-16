-- 
-- jobs
-- 

-- DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `#__jobs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `job_queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `job_payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `num_attempts` tinyint unsigned NOT NULL DEFAULT '0',
  `reserved_at` datetime DEFAULT NULL,
  `available_at` datetime NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `job_queue` (`job_queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- jobs_failures
-- 

-- DROP TABLE IF EXISTS `jobs_failures`;
CREATE TABLE IF NOT EXISTS `#__jobs_failures` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `job_id` int unsigned NOT NULL,
  `job_uuid` varchar(64) COLLATE utf8mb4_general_ci NOT NULL,
  `job_queue` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `job_payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `job_error` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

