-- 
-- #__users
-- 

-- DROP TABLE IF EXISTS `#__users`;
CREATE TABLE IF NOT EXISTS `#__users` (
  `id` int unsigned NOT NULL auto_increment,
  `fullname` varchar(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `email` varchar(148) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `verified_email` enum('no','yes') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `user_slug` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `avatar_id` int unsigned NOT NULL DEFAULT 0,
  `avatar_file` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP  on update CURRENT_TIMESTAMP,
  `status` enum('autosignup','inactive','active') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'inactive',
  PRIMARY KEY (`id`),
  UNIQUE `email` (`email`),
  UNIQUE `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- 
-- #__users_activities
-- 

-- DROP TABLE IF EXISTS `#__users_activities`;
CREATE TABLE IF NOT EXISTS `#__users_activities` (
  `id` bigint unsigned NOT NULL auto_increment,
  `user_id` int unsigned NOT NULL,
  `user_ip` varbinary(16) NOT NULL,
  `activity_type` enum('signup','activation','login','autologin','savepwd','savemail','validation') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `activity_code` smallint NOT NULL DEFAULT 0,
  `activity_context` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `created_at` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- 
-- #__users_activities_locks
-- 

-- DROP TABLE IF EXISTS `#__users_activities_locks`;
CREATE TABLE IF NOT EXISTS `#__users_activities_locks` (
  `id` bigint unsigned NOT NULL auto_increment,
  `user_id` int unsigned NOT NULL DEFAULT 0,
  `user_ip` varbinary(16) NOT NULL,
  `lock_type` enum('signup','activation','login','autologin','savepwd','savemail','token') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `lock_counter` smallint unsigned NOT NULL DEFAULT 0,
  `expires_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- 
-- #__users_activities_tokens
-- 

-- DROP TABLE IF EXISTS `#__users_activities_tokens`;
CREATE TABLE IF NOT EXISTS `#__users_activities_tokens` (
  `activity_id` bigint unsigned NOT NULL,
  `token_selector` char(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `token_validator` char(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `token_to` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `modified_at` datetime NULL on update CURRENT_TIMESTAMP,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`activity_id`),
  UNIQUE `session_selector` (`token_selector`)
) ENGINE=InnoDB  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- 
-- #__users_roles
-- 

-- DROP TABLE IF EXISTS `#__users_roles`;
CREATE TABLE IF NOT EXISTS `#__users_roles` (
  `id` smallint unsigned NOT NULL auto_increment,
  `role_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `role_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- 
-- #__users_roles_labels
-- 

-- DROP TABLE IF EXISTS `#__users_roles_labels`;
CREATE TABLE IF NOT EXISTS `#__users_roles_labels` (
  `id` smallint unsigned NOT NULL auto_increment,
  `extension_id` smallint unsigned NOT NULL,
  `label_key` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `label_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `label_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  PRIMARY KEY (`id`),
  UNIQUE `uni` (`extension_id`, `label_key`)
) ENGINE=InnoDB  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- 
-- #__users_roles_labels_map
-- 

-- DROP TABLE IF EXISTS `#__users_roles_labels_map`;
CREATE TABLE IF NOT EXISTS `#__users_roles_labels_map` (
  `id` int unsigned NOT NULL auto_increment,
  `role_id` tinyint unsigned NOT NULL,
  `label_id` tinyint unsigned NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE `uniq` (`role_id`, `label_id`),
  KEY `label_id` (`label_id`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- 
-- #__users_roles_map
-- 

-- DROP TABLE IF EXISTS `#__users_roles_map`;
CREATE TABLE IF NOT EXISTS `#__users_roles_map` (
  `user_id` int unsigned NOT NULL,
  `role_id` int unsigned NOT NULL,
  PRIMARY KEY (`user_id`, `role_id`)
) ENGINE=InnoDB  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

