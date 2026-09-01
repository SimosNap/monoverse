/*M!999999\- enable the sandbox mode */

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `administrators`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `administrators` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'administrator',
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` int(10) unsigned NOT NULL,
  `last_login_at` int(10) unsigned DEFAULT NULL,
  `last_login_ip` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `community_link_cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `community_link_cache` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(2048) NOT NULL,
  `url_hash` char(64) NOT NULL,
  `provider` varchar(50) NOT NULL,
  `title` varchar(512) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(2048) DEFAULT NULL,
  `site_name` varchar(255) DEFAULT NULL,
  `favicon` varchar(2048) DEFAULT NULL,
  `embed` varchar(2048) DEFAULT NULL,
  `fetched_at` datetime NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_link_cache_hash` (`url_hash`),
  KEY `idx_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `community_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `community_notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `recipient_sub` varchar(255) NOT NULL,
  `actor_sub` varchar(255) NOT NULL,
  `type` varchar(32) NOT NULL,
  `object_type` varchar(32) NOT NULL,
  `object_uuid` varchar(128) NOT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  `read_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_uuid` (`uuid`),
  KEY `idx_recipient` (`recipient_sub`,`is_read`,`created_at`),
  KEY `idx_actor` (`actor_sub`),
  KEY `idx_object` (`object_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `community_post_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `community_post_comments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `post_id` bigint(20) unsigned NOT NULL,
  `author_sub` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'published',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `edited_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_community_post_comments_uuid` (`uuid`),
  KEY `idx_community_post_comments_post` (`post_id`,`status`,`created_at`),
  KEY `idx_community_post_comments_author` (`author_sub`),
  CONSTRAINT `fk_community_post_comments_post` FOREIGN KEY (`post_id`) REFERENCES `community_posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `community_post_media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `community_post_media` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) unsigned NOT NULL,
  `uuid` char(36) NOT NULL,
  `media_type` enum('image','video','audio','document') NOT NULL,
  `storage_path` varchar(500) NOT NULL,
  `public_url` varchar(1000) DEFAULT NULL,
  `preview_path` varchar(500) DEFAULT NULL,
  `original_name` varchar(255) DEFAULT NULL,
  `mime_type` varchar(150) DEFAULT NULL,
  `file_size` bigint(20) unsigned DEFAULT NULL,
  `hash` char(64) DEFAULT NULL,
  `width` int(10) unsigned DEFAULT NULL,
  `height` int(10) unsigned DEFAULT NULL,
  `duration_seconds` int(10) unsigned DEFAULT NULL,
  `audio_title` varchar(255) DEFAULT NULL,
  `audio_artist` varchar(255) DEFAULT NULL,
  `audio_tracklist` text DEFAULT NULL,
  `alt_text` varchar(500) DEFAULT NULL,
  `caption` text DEFAULT NULL,
  `sort_order` smallint(5) unsigned NOT NULL DEFAULT 0,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_community_post_media_uuid` (`uuid`),
  KEY `idx_community_post_media_post` (`post_id`,`sort_order`),
  KEY `idx_community_post_media_status` (`status`),
  CONSTRAINT `fk_community_post_media_post` FOREIGN KEY (`post_id`) REFERENCES `community_posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `community_post_votes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `community_post_votes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) unsigned NOT NULL,
  `author_sub` varchar(255) NOT NULL,
  `vote` tinyint(4) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_community_post_votes` (`post_id`,`author_sub`),
  KEY `idx_community_post_votes_post` (`post_id`),
  KEY `idx_community_post_votes_author` (`author_sub`),
  CONSTRAINT `fk_community_post_votes_post` FOREIGN KEY (`post_id`) REFERENCES `community_posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `chk_community_post_votes_vote` CHECK (`vote` in (-1,1))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `community_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `community_posts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `author_sub` varchar(255) NOT NULL,
  `source` varchar(32) NOT NULL DEFAULT 'user',
  `content` text NOT NULL,
  `visibility` varchar(20) NOT NULL DEFAULT 'public',
  `status` varchar(20) NOT NULL DEFAULT 'published',
  `comments_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `is_pinned` tinyint(1) NOT NULL DEFAULT 0,
  `is_sensitive` tinyint(1) NOT NULL DEFAULT 0,
  `published_at` datetime NOT NULL DEFAULT current_timestamp(),
  `edited_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_community_posts_uuid` (`uuid`),
  KEY `idx_community_posts_author` (`author_sub`),
  KEY `idx_community_posts_timeline` (`status`,`visibility`,`published_at`),
  KEY `idx_community_posts_deleted` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `community_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `community_reports` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `reporter_sub` varchar(255) NOT NULL,
  `target_type` enum('ping','pong') NOT NULL,
  `target_uuid` char(36) NOT NULL,
  `reason` varchar(32) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('open','reviewed','closed') NOT NULL DEFAULT 'open',
  `reviewed_by` varchar(255) DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `idx_status` (`status`),
  KEY `idx_target` (`target_type`,`target_uuid`),
  KEY `idx_reporter` (`reporter_sub`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `community_saved_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `community_saved_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `user_sub` varchar(255) NOT NULL,
  `object_type` varchar(32) NOT NULL,
  `object_uuid` char(36) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_community_saved_items_uuid` (`uuid`),
  UNIQUE KEY `uk_community_saved_items_user_object` (`user_sub`,`object_type`,`object_uuid`),
  KEY `idx_community_saved_items_user` (`user_sub`,`created_at`),
  KEY `idx_community_saved_items_object` (`object_type`,`object_uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `moderators`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `moderators` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `oauth_sub` varchar(191) NOT NULL,
  `username` varchar(100) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'moderator',
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` int(10) unsigned NOT NULL,
  `updated_at` int(10) unsigned NOT NULL,
  `last_login_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_oauth_sub` (`oauth_sub`),
  KEY `idx_enabled` (`enabled`),
  KEY `idx_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `mv_articles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mv_articles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `excerpt` text DEFAULT NULL,
  `content` mediumtext NOT NULL,
  `cover` varchar(255) DEFAULT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `status` enum('draft','submitted','published','rejected') NOT NULL DEFAULT 'draft',
  `published_at` datetime DEFAULT NULL,
  `ping_uuid` char(36) DEFAULT NULL,
  `submitted_by_sub` varchar(190) DEFAULT NULL,
  `submitted_by_nickname` varchar(100) DEFAULT NULL,
  `submitted_at` datetime DEFAULT NULL,
  `rejected_at` datetime DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_status_published` (`status`,`published_at`),
  KEY `idx_category_id` (`category_id`),
  KEY `idx_submitted_by_sub` (`submitted_by_sub`),
  KEY `idx_status_submitted` (`status`,`submitted_at`),
  CONSTRAINT `fk_mv_articles_category` FOREIGN KEY (`category_id`) REFERENCES `mv_categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `mv_blocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mv_blocks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `area` varchar(120) NOT NULL,
  `type` varchar(100) NOT NULL,
  `name` varchar(150) DEFAULT NULL,
  `page` varchar(120) NOT NULL,
  `title` varchar(180) DEFAULT NULL,
  `settings` longtext DEFAULT NULL,
  `width` tinyint(3) unsigned NOT NULL DEFAULT 12,
  `position` int(10) unsigned NOT NULL DEFAULT 10,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `area_enabled_position` (`area`,`enabled`,`position`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `mv_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mv_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `type` varchar(32) NOT NULL,
  `name` varchar(120) NOT NULL,
  `description` text DEFAULT NULL,
  `slug` varchar(120) NOT NULL,
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `uniq_type_slug` (`type`,`slug`),
  KEY `idx_type_sort` (`type`,`sort_order`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `mv_content_translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mv_content_translations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `entity_type` varchar(32) NOT NULL,
  `entity_id` bigint(20) unsigned NOT NULL,
  `locale` varchar(16) NOT NULL,
  `field` varchar(64) NOT NULL,
  `value` mediumtext DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_entity_locale_field` (`entity_type`,`entity_id`,`locale`,`field`),
  KEY `idx_entity` (`entity_type`,`entity_id`),
  KEY `idx_locale` (`locale`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `mv_modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mv_modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module_key` varchar(100) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `version` varchar(30) DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 0,
  `allowed_modes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`allowed_modes`)),
  `config_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`config_json`)),
  `installed_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `module_key` (`module_key`),
  KEY `idx_enabled` (`enabled`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `mv_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mv_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(190) NOT NULL,
  `slug` varchar(190) NOT NULL,
  `content` mediumtext DEFAULT NULL,
  `status` enum('draft','published','private') NOT NULL DEFAULT 'published',
  `show_in_navigation` tinyint(1) NOT NULL DEFAULT 1,
  `menu_label` varchar(100) DEFAULT NULL,
  `navigation_group` varchar(50) NOT NULL DEFAULT 'default',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `meta_title` varchar(190) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_status` (`status`),
  KEY `fk_pages_created_by` (`created_by`),
  KEY `fk_pages_updated_by` (`updated_by`),
  CONSTRAINT `fk_pages_created_by` FOREIGN KEY (`created_by`) REFERENCES `mv_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_pages_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `mv_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `mv_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mv_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` varchar(30) NOT NULL DEFAULT 'string',
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `mv_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mv_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provider` varchar(50) NOT NULL DEFAULT 'simosnap',
  `provider_uid` varchar(100) NOT NULL,
  `simosnap_uid` int(11) DEFAULT NULL,
  `sub` varchar(190) DEFAULT NULL,
  `nickname` varchar(100) DEFAULT NULL,
  `preferred_username` varchar(100) DEFAULT NULL,
  `irc_account` varchar(100) DEFAULT NULL,
  `profile_url` varchar(255) DEFAULT NULL,
  `is_oper` tinyint(1) NOT NULL DEFAULT 0,
  `role` varchar(30) NOT NULL DEFAULT 'user',
  `permissions_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions_json`)),
  `last_login_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_provider_user` (`provider`,`provider_uid`),
  KEY `idx_simosnap_uid` (`simosnap_uid`),
  KEY `idx_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `profiles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `oauth_sub` varchar(128) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `oauth_uid` varchar(64) DEFAULT NULL,
  `nickname` varchar(100) NOT NULL,
  `avatar_url` text DEFAULT NULL,
  `aliases` text DEFAULT NULL,
  `age` varchar(10) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `sex` char(1) NOT NULL DEFAULT 'U',
  `bio` text DEFAULT NULL,
  `motto` varchar(120) DEFAULT NULL,
  `interests` text DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `telegram` varchar(100) DEFAULT NULL,
  `public_profile` tinyint(1) NOT NULL DEFAULT 0,
  `allow_indexing` tinyint(1) NOT NULL DEFAULT 0,
  `show_avatar` tinyint(1) NOT NULL DEFAULT 0,
  `show_aliases` tinyint(1) NOT NULL DEFAULT 0,
  `show_age` tinyint(1) NOT NULL DEFAULT 0,
  `show_city` tinyint(1) NOT NULL DEFAULT 0,
  `show_sex` tinyint(1) NOT NULL DEFAULT 0,
  `show_irc_stats` tinyint(1) NOT NULL DEFAULT 0,
  `doge_tip_source` enum('mydogemask','simosnap') DEFAULT NULL,
  `doge_tip_address` varchar(64) DEFAULT NULL,
  `external_account_exists` tinyint(1) NOT NULL DEFAULT 1,
  `external_account_checked_at` int(10) unsigned DEFAULT NULL,
  `external_account_missing_at` int(10) unsigned DEFAULT NULL,
  `created_at` int(10) unsigned NOT NULL,
  `updated_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `oauth_sub` (`oauth_sub`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `created_at` int(10) unsigned NOT NULL,
  `updated_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_blocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_blocks` (
  `blocker_sub` char(64) NOT NULL,
  `blocked_sub` char(64) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`blocker_sub`,`blocked_sub`),
  KEY `idx_blocker` (`blocker_sub`),
  KEY `idx_blocked` (`blocked_sub`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_follows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_follows` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `follower_sub` char(64) NOT NULL,
  `followed_sub` char(64) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_follow` (`follower_sub`,`followed_sub`),
  KEY `idx_follower` (`follower_sub`),
  KEY `idx_followed` (`followed_sub`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_moderation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_moderation` (
  `oauth_sub` varchar(255) NOT NULL,
  `muted` tinyint(1) NOT NULL DEFAULT 0,
  `banned` tinyint(1) NOT NULL DEFAULT 0,
  `mute_reason` varchar(255) DEFAULT NULL,
  `ban_reason` varchar(255) DEFAULT NULL,
  `muted_by` varchar(255) DEFAULT NULL,
  `banned_by` varchar(255) DEFAULT NULL,
  `mute_expires_at` int(10) unsigned DEFAULT NULL,
  `ban_expires_at` int(10) unsigned DEFAULT NULL,
  `created_at` int(10) unsigned NOT NULL,
  `updated_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`oauth_sub`),
  KEY `idx_muted` (`muted`),
  KEY `idx_banned` (`banned`),
  KEY `idx_mute_expires_at` (`mute_expires_at`),
  KEY `idx_ban_expires_at` (`ban_expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
