<?php


/** this is just demo 

$sql="
-- phpMyAdmin SQL Dump
SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = \"+00:00\";

-- -------------------------------------------------
--
-- Table structure for table `my_message`
--
DROP TABLE IF EXISTS `my_message`;
CREATE TABLE `sitemin_message` (
  `id` bigint(20) NOT NULL,
  `from` bigint(20) NOT NULL,
  `to` bigint(20) NOT NULL DEFAULT '0',
  `to_group` varchar(222) DEFAULT NULL,
  `title` varchar(222) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(11) CHARACTER SET latin1 NOT NULL DEFAULT 'active'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
--
-- Indexes for table `sitemin_message`
--
ALTER TABLE `sitemin_message`
  ADD PRIMARY KEY (`id`),
  ADD KEY `from` (`from`),
  ADD KEY `date_created` (`created`),
  ADD KEY `status` (`status`),
  ADD KEY `to_group` (`to_group`),
  ADD KEY `title` (`title`),
  ADD KEY `to` (`to`);
--
-- AUTO_INCREMENT for table `sitemin_message`
--
ALTER TABLE `sitemin_message`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
-- --------------------------------------------------------
";

# note: this script used default database configuration, so no cfg been set.
 
xpPdo::conn()->exec($sql);
 */
