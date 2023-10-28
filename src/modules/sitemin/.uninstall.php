<?php

$sql = "
-- phpMyAdmin SQL Dump
SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = \"+00:00\";

--
-- Table structure for table `acl_menu`
--
DROP TABLE IF EXISTS `acl_menu`;
DROP TABLE IF EXISTS `acl_role`;
DROP TABLE IF EXISTS `acl_router`;
DROP TABLE IF EXISTS `sitemin_crontab`;
DROP TABLE IF EXISTS `sitemin_log`;
DROP TABLE IF EXISTS `sitemin_mail`;
DROP TABLE IF EXISTS `sitemin_mail_attachment`;
DROP TABLE IF EXISTS `sitemin_mail_recipient`;
DROP TABLE IF EXISTS `sitemin_message`;
DROP TABLE IF EXISTS `sitemin_message_receiver`;
DROP TABLE IF EXISTS `sitemin_role`;
DROP TABLE IF EXISTS `sitemin_user`;
DROP TABLE IF EXISTS `var`;
DROP TABLE IF EXISTS `api`;
DROP TABLE IF EXISTS `api_acl`;
DROP TABLE IF EXISTS `api_user`;
";

/**
 * note: this script used default database configuration, global $config['db'].
 */
xpPdo::conn()->exec($sql);
