-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 26, 2012 at 05:08 AM
-- Server version: 5.5.24-log
-- PHP Version: 5.4.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `playbasis_v_1`
--

-- --------------------------------------------------------

--
-- Table structure for table `playbasis_action`
--

CREATE TABLE IF NOT EXISTS `playbasis_action` (
  `action_id` int(11) NOT NULL AUTO_INCREMENT,
  `action_name` varchar(100) NOT NULL,
  `action_description` varchar(500) NOT NULL,
  `access_level` int(5) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`action_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='action collection' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `playbasis_action2client`
--

CREATE TABLE IF NOT EXISTS `playbasis_action2client` (
  `action_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `site_id` int(11) NOT NULL,
  `target` text NOT NULL,
  `target_source` text NOT NULL,
  `action_name` varchar(100) NOT NULL,
  `action_description` varchar(500) NOT NULL,
  `active_status` tinyint(1) NOT NULL DEFAULT '1',
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`action_id`,`client_id`,`site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='relate action and client';

-- --------------------------------------------------------

--
-- Table structure for table `playbasis_badge`
--

CREATE TABLE IF NOT EXISTS `playbasis_badge` (
  `badge_id` int(11) NOT NULL AUTO_INCREMENT,
  `stackable` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'stackable status - use to identify which badge is unique for each player',
  `substract` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'substract badge if set to 0 mean this badge are infinite',
  `quantity` int(10) NOT NULL DEFAULT '1' COMMENT 'limit amount of badge',
  `image` varchar(255) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`badge_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='playbasis badge warehouse' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `playbasis_badge2client`
--

CREATE TABLE IF NOT EXISTS `playbasis_badge2client` (
  `badge_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `site_id` int(11) NOT NULL,
  PRIMARY KEY (`badge_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='relate badge to client';

-- --------------------------------------------------------

--
-- Table structure for table `playbasis_badge2collection`
--

CREATE TABLE IF NOT EXISTS `playbasis_badge2collection` (
  `badge_id` int(11) NOT NULL,
  `collection_id` varchar(45) NOT NULL,
  PRIMARY KEY (`badge_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='relate badge and collection';

-- --------------------------------------------------------

--
-- Table structure for table `playbasis_badge2player`
--

CREATE TABLE IF NOT EXISTS `playbasis_badge2player` (
  `pb_player_id` int(11) NOT NULL,
  `badge_id` int(11) NOT NULL,
  `amount` int(3) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  PRIMARY KEY (`pb_player_id`,`badge_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `playbasis_badge_collection`
--

CREATE TABLE IF NOT EXISTS `playbasis_badge_collection` (
  `collection_id` int(11) NOT NULL AUTO_INCREMENT,
  `collection_image` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`collection_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='group of badge ';

-- --------------------------------------------------------

--
-- Table structure for table `playbasis_badge_collection2client`
--

CREATE TABLE IF NOT EXISTS `playbasis_badge_collection2client` (
  `collection_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `site_id` int(11) NOT NULL,
  PRIMARY KEY (`collection_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='relate collection to client';

-- --------------------------------------------------------

--
-- Table structure for table `playbasis_badge_collection_description`
--

CREATE TABLE IF NOT EXISTS `playbasis_badge_collection_description` (
  `collection_id` int(11) NOT NULL,
  `collection_name` varchar(255) NOT NULL,
  `collection_description` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`collection_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `playbasis_badge_description`
--

CREATE TABLE IF NOT EXISTS `playbasis_badge_description` (
  `badge_id` int(11) NOT NULL,
  `badge_name` varchar(150) NOT NULL,
  `badge_description` varchar(500) NOT NULL,
  `badge_tip` varchar(500) NOT NULL,
  PRIMARY KEY (`badge_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='description for each badge';

-- --------------------------------------------------------

--
-- Table structure for table `playbasis_client`
--

CREATE TABLE IF NOT EXISTS `playbasis_client` (
  `client_id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `email` varchar(150) NOT NULL,
  `company` varchar(150) DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='all about client here' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `playbasis_client_site`
--

CREATE TABLE IF NOT EXISTS `playbasis_client_site` (
  `site_id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `domain_name` varchar(255) NOT NULL,
  `site_name` varchar(255) DEFAULT NULL,
  `api_key` varchar(100) NOT NULL,
  `api_secret` varchar(100) NOT NULL,
  `date_start` datetime NOT NULL,
  `date_expire` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='relate client and site' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `playbasis_feature`
--

CREATE TABLE IF NOT EXISTS `playbasis_feature` (
  `feature_id` int(11) NOT NULL AUTO_INCREMENT,
  `feature_name` varchar(50) NOT NULL,
  `feature_description` varchar(500) DEFAULT NULL,
  `access_level` int(5) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`feature_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='playbasis  feature ' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `playbasis_game_jigsaw`
--

CREATE TABLE IF NOT EXISTS `playbasis_game_jigsaw` (
  `jigsaw_id` int(11) NOT NULL AUTO_INCREMENT,
  `jigsaw_name` varchar(50) NOT NULL,
  `jigsaw_description` varchar(500) NOT NULL,
  `category` set('ACTION','CONDITION','REWARD') NOT NULL,
  `access_level` int(5) NOT NULL,
  `class_path` varchar(250) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`jigsaw_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='peice of condition for combination to game rule' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `playbasis_game_jigsaw2client`
--

CREATE TABLE IF NOT EXISTS `playbasis_game_jigsaw2client` (
  `jigsaw_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `site_id` int(11) NOT NULL,
  `jigsaw_name` varchar(50) NOT NULL,
  `jigsaw_description` varchar(500) NOT NULL,
  `category` set('ACTION','CONDITION','REWARD') NOT NULL,
  `class_path` varchar(250) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`jigsaw_id`,`client_id`,`site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='relate game jigsaw to client';

-- --------------------------------------------------------

--
-- Table structure for table `playbasis_history`
--

CREATE TABLE IF NOT EXISTS `playbasis_history` (
  `id` bigint(30) NOT NULL,
  `pb_player_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `rule_id` int(11) NOT NULL,
  `rule_name` varchar(150) NOT NULL,
  `jigsaw_set` text NOT NULL,
  `action_id` int(11) NOT NULL,
  `action_name` varchar(100) NOT NULL,
  `client_id` int(11) NOT NULL,
  `site_id` int(11) NOT NULL,
  `domain_name` varchar(255) NOT NULL,
  `site_name` varchar(255) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='store all content of player activity';

-- --------------------------------------------------------

--
-- Table structure for table `playbasis_jigsaw2rule`
--

CREATE TABLE IF NOT EXISTS `playbasis_jigsaw2rule` (
  `rule_id` int(11) NOT NULL,
  `jigsaw_id` int(11) NOT NULL,
  `sort_order` int(2) NOT NULL,
  `dataset` text NOT NULL COMMENT 'serialize of input to process for that rule',
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`rule_id`,`jigsaw_id`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='relate jigsaw and rule';

-- --------------------------------------------------------

--
-- Table structure for table `playbasis_level_table`
--

CREATE TABLE IF NOT EXISTS `playbasis_level_table` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `exp` int(15) NOT NULL,
  `level` int(3) NOT NULL,
  `title` varchar(150) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='level table ' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `playbasis_permission`
--

CREATE TABLE IF NOT EXISTS `playbasis_permission` (
  `plan_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `site_id` int(11) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL,
  PRIMARY KEY (`plan_id`,`client_id`,`site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='playbasis feature access permission';

-- --------------------------------------------------------

--
-- Table structure for table `playbasis_plan`
--

CREATE TABLE IF NOT EXISTS `playbasis_plan` (
  `plan_id` int(11) NOT NULL AUTO_INCREMENT,
  `plan_name` varchar(50) NOT NULL,
  `plan_description` varchar(500) DEFAULT NULL,
  `access_level` int(5) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  PRIMARY KEY (`plan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='about playbasis plan for client to pay' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `playbasis_player`
--

CREATE TABLE IF NOT EXISTS `playbasis_player` (
  `pb_player_id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `site_id` int(11) NOT NULL,
  `cl_player_id` varchar(30) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `image` varchar(255) NOT NULL,
  `exp` int(10) NOT NULL DEFAULT '0',
  `level` int(3) NOT NULL DEFAULT '0',
  `email` varchar(255) DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`pb_player_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='information about playbasis player' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `playbasis_reward`
--

CREATE TABLE IF NOT EXISTS `playbasis_reward` (
  `reward_id` int(11) NOT NULL,
  `reward_group` set('point','nonpoint') NOT NULL,
  `reward_name` varchar(50) NOT NULL,
  `access_level` int(5) NOT NULL,
  `limit_status` tinyint(1) NOT NULL DEFAULT '0',
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`reward_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='all of reward in playbasis world';

-- --------------------------------------------------------

--
-- Table structure for table `playbasis_reward2client`
--

CREATE TABLE IF NOT EXISTS `playbasis_reward2client` (
  `reward_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `site_id` int(11) NOT NULL,
  `limit_status` tinyint(1) NOT NULL DEFAULT '0',
  `limit` int(15) NOT NULL DEFAULT '0',
  `used` int(15) NOT NULL DEFAULT '0',
  `reward_name` varchar(50) NOT NULL,
  `reward_group` set('point','nonpoint') NOT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`reward_id`,`client_id`,`site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='realte reward and client';

-- --------------------------------------------------------

--
-- Table structure for table `playbasis_reward2plan`
--

CREATE TABLE IF NOT EXISTS `playbasis_reward2plan` (
  `reward_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `limit_default` int(10) NOT NULL,
  PRIMARY KEY (`reward_id`,`plan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='relate reward to any plan';

-- --------------------------------------------------------

--
-- Table structure for table `playbasis_reward2player`
--

CREATE TABLE IF NOT EXISTS `playbasis_reward2player` (
  `pb_player_id` int(11) NOT NULL,
  `reward_id` int(11) NOT NULL,
  `value` int(10) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  PRIMARY KEY (`pb_player_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='realate reward(none-point) and player';

-- --------------------------------------------------------

--
-- Table structure for table `playbasis_rule`
--

CREATE TABLE IF NOT EXISTS `playbasis_rule` (
  `rule_id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `rule_name` varchar(150) NOT NULL,
  `rule_description` varchar(1500) NOT NULL,
  `jigsaw_set` text NOT NULL,
  `active_status` tinyint(1) NOT NULL DEFAULT '0',
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`rule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='rule for each game of every client' AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
