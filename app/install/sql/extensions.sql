-- 
-- extensions
-- 

-- DROP TABLE IF EXISTS `extensions`;
CREATE TABLE IF NOT EXISTS `#__extensions` (
  `id` smallint unsigned NOT NULL AUTO_INCREMENT,
  `developer_id` smallint unsigned NOT NULL,
  `package_id` int NOT NULL DEFAULT '0' COMMENT 'Used in packaging',
  `extension_alias` varchar(16) COLLATE utf8mb4_general_ci NOT NULL,
  `extension_name` varchar(64) COLLATE utf8mb4_general_ci NOT NULL,
  `extension_version` varchar(32) COLLATE utf8mb4_general_ci NOT NULL,
  `extension_credits` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `extension_license` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `extension_abstract` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `extension_require` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `extension_key` varchar(64) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `components` varchar(16) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `db_queries` text COLLATE utf8mb4_general_ci,
  `db_history` text COLLATE utf8mb4_general_ci,
  `xdata` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('public','private','deprecated') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'public',
  PRIMARY KEY (`id`),
  UNIQUE KEY `extension_alias` (`extension_alias`),
  KEY `developer_id` (`developer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- extensions_changes
-- 

-- DROP TABLE IF EXISTS `extensions_changes`;
CREATE TABLE IF NOT EXISTS `#__extensions_changes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `extension_id` int unsigned NOT NULL,
  `change_description` tinytext COLLATE utf8mb4_general_ci NOT NULL,
  `is_compatible` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- extensions_developers
-- 

-- DROP TABLE IF EXISTS `extensions_developers`;
CREATE TABLE IF NOT EXISTS `#__extensions_developers` (
  `id` smallint unsigned NOT NULL AUTO_INCREMENT,
  `developer_name` varchar(32) COLLATE utf8mb4_general_ci NOT NULL,
  `project_url` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `webstore_url` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `webstore_token` varchar(128) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `default_credits` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `default_license` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_protected` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- extensions_updates
-- 

-- DROP TABLE IF EXISTS `extensions_updates`;
CREATE TABLE IF NOT EXISTS `#__extensions_updates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `extension_id` int NOT NULL,
  `update_version` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `released_at` datetime DEFAULT NULL,
  `checked_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `has_failed` tinyint unsigned NOT NULL DEFAULT '0',
  `failure_msg` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `status` enum('canceled','available','installed') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'canceled',
  PRIMARY KEY (`id`),
  KEY `extension_id` (`extension_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

