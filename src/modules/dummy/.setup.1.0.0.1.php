<?php

$sql = "
-- phpMyAdmin SQL Dump
SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = \"+00:00\";

--
-- Table structure for table `api_user`
--
DROP TABLE IF EXISTS `api_user`;
CREATE TABLE `api_user` (
  `id` bigint(20) NOT NULL,
  `login_id` varchar(222) NOT NULL,
  `password` varchar(222) CHARACTER SET latin1 NOT NULL,
  `detail` text NOT NULL,
  `quota` bigint(20) NOT NULL DEFAULT 0,
  `quota_type` enum('day','month','year','topup') NOT NULL,
  `quota_start` date,
  `quota_remain` bigint(20) NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `modified` timestamp NULL DEFAULT NULL,
  `status` varchar(22) CHARACTER SET latin1 DEFAULT 'pending'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
--
-- Indexes for table `api_user`
--
ALTER TABLE `api_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login_id` (`login_id`),
  ADD KEY `user_id` (`login_id`),
  ADD KEY `status` (`status`),
  ADD KEY `quota_type` (`quota_type`),
  ADD KEY `quota_start` (`quota_start`);
--
-- AUTO_INCREMENT for table `api_user`
--
ALTER TABLE `api_user`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- Dumping data for table `api_user`
--
INSERT INTO `api_user` (`id`, `login_id`, `password`, `detail`, `quota`, `quota_type`, `quota_start`, `quota_remain`, `created`, `modified`, `status`) VALUES
(1, 'testing', '88ea39439e74fa27c09a4fc0bc8ebe6d00978392', 'test', 20, 'day', '2020-03-03', 11, '2012-08-10 15:05:06', '2012-08-10 05:05:09', 'suspend'),
(2, 'server', 'e7ba2fb7e35a89a980d85b11ffb2c520', 'monitor api', 90, 'day', '2020-03-03', 11, '2020-03-03 14:07:40', NULL, 'active'),
(3, 'tineyetest', 'b1b382f135291867fb28f7cc919568d6', 'internal RI', 20, 'day', '2020-03-03', 12, '2020-03-03 14:12:46', '2018-02-19 05:43:22', 'active'),
(4, 'tospub', '', 't1234', 11111, 'day', '2020-03-03', 1, '2020-03-03 14:13:17', NULL, 'suspend'),
(5, 'machone', '34cdccc18144326d22c9567f42536a36', 'speed', 11111, 'day', '2020-03-03', 0, '2020-03-03 14:07:54', NULL, 'suspend');
-- --------------------------------------------------------
";


/**
 * note: this script used default configuration, $config so no cfg been set.
 */
xpPdo::conn()->exec($sql);
