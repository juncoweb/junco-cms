-- 
-- menus
-- 

-- DROP TABLE IF EXISTS `menus`;
CREATE TABLE IF NOT EXISTS `#__menus` (
  `id` smallint unsigned NOT NULL AUTO_INCREMENT,
  `extension_id` smallint unsigned NOT NULL,
  `menu_key` varchar(64) COLLATE utf8mb4_general_ci NOT NULL,
  `menu_default_path` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `menu_path` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `menu_order` tinyint NOT NULL DEFAULT '0',
  `menu_url` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `menu_image` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `menu_hash` varchar(48) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `menu_params` text COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_distributed` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `menu_key` (`menu_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

