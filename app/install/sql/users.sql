-- 
-- users
-- 

-- DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `#__users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `fullname` varchar(48) COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(24) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `email` varchar(148) COLLATE utf8mb4_general_ci NOT NULL,
  `verified_email` enum('no','yes') COLLATE utf8mb4_general_ci NOT NULL,
  `user_slug` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `avatar_id` int unsigned NOT NULL DEFAULT '0',
  `avatar_file` varchar(64) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('autosignup','inactive','active') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'inactive',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- users_activities
-- 

-- DROP TABLE IF EXISTS `users_activities`;
CREATE TABLE IF NOT EXISTS `#__users_activities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `user_ip` varbinary(16) NOT NULL,
  `activity_type` enum('signup','activation','login','autologin','savepwd','savemail','token','validation') COLLATE utf8mb4_general_ci NOT NULL,
  `activity_code` smallint NOT NULL DEFAULT '0',
  `activity_context` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- users_activities_locks
-- 

-- DROP TABLE IF EXISTS `users_activities_locks`;
CREATE TABLE IF NOT EXISTS `#__users_activities_locks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL DEFAULT '0',
  `user_ip` varbinary(16) NOT NULL,
  `lock_type` enum('signup','activation','login','autologin','savepwd','savemail','token') COLLATE utf8mb4_general_ci NOT NULL,
  `lock_counter` smallint unsigned NOT NULL DEFAULT '0',
  `expires_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- users_activities_tokens
-- 

-- DROP TABLE IF EXISTS `users_activities_tokens`;
CREATE TABLE IF NOT EXISTS `#__users_activities_tokens` (
  `activity_id` bigint unsigned NOT NULL,
  `token_selector` char(12) COLLATE utf8mb4_general_ci NOT NULL,
  `token_validator` char(64) COLLATE utf8mb4_general_ci NOT NULL,
  `token_to` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `modified_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`activity_id`),
  UNIQUE KEY `session_selector` (`token_selector`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- users_roles
-- 

-- DROP TABLE IF EXISTS `users_roles`;
CREATE TABLE IF NOT EXISTS `#__users_roles` (
  `id` smallint unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `role_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- users_roles_labels
-- 

-- DROP TABLE IF EXISTS `users_roles_labels`;
CREATE TABLE IF NOT EXISTS `#__users_roles_labels` (
  `id` smallint unsigned NOT NULL AUTO_INCREMENT,
  `extension_id` smallint unsigned NOT NULL,
  `label_key` varchar(16) COLLATE utf8mb4_general_ci NOT NULL,
  `label_name` varchar(32) COLLATE utf8mb4_general_ci NOT NULL,
  `label_description` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- users_roles_labels_map
-- 

-- DROP TABLE IF EXISTS `users_roles_labels_map`;
CREATE TABLE IF NOT EXISTS `#__users_roles_labels_map` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `role_id` tinyint unsigned NOT NULL,
  `label_id` tinyint unsigned NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq` (`role_id`,`label_id`),
  KEY `label_id` (`label_id`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- users_roles_map
-- 

-- DROP TABLE IF EXISTS `users_roles_map`;
CREATE TABLE IF NOT EXISTS `#__users_roles_map` (
  `user_id` int unsigned NOT NULL,
  `role_id` int unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

