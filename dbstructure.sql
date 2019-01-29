-- Adminer 4.7.0 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `crw_instances`;
CREATE TABLE `crw_instances` (
  `id` int(6) DEFAULT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL,
  `path` text NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_exists` int(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `crw_queue`;
CREATE TABLE `crw_queue` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `url` longtext,
  `type` int(1) DEFAULT NULL,
  `settings` text,
  `command` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `instance_id` int(6) DEFAULT NULL,
  `status` int(3) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `crw_settings`;
CREATE TABLE `crw_settings` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `settings` text NOT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `crw_users`;
CREATE TABLE `crw_users` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `reset_hash` varchar(255) NOT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 2019-01-01 22:01:22