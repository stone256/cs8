<?php

echo "\nRun uninstall script.. \n";

/** this is just demo 

$sql="
-- phpMyAdmin SQL Dump
SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = \"+00:00\";

-- -------------------------------------------------
DROP TABLE IF EXISTS `my_message`;
-- --------------------------------------------------------
";

# note: this script used default database configuration, so no cfg been set.
 
xpPdo::conn()->exec($sql);
 */
