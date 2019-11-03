-- ----------------------------
-- Table structure for `#__bt_portfolio_categories`
-- ----------------------------
DROP TABLE IF EXISTS `#__bt_portfolio_categories`;
CREATE TABLE `#__bt_portfolio_categories` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) default NULL,
  `alias` varchar(255) default NULL,
  `main_image` varchar(255) default NULL,
  `description` text,
  `parent_id` int(11) NOT NULL default '0',
  `published` tinyint(4) default NULL,
  `ordering` int(11) default NULL,
  `extra_fields` varchar(500) default NULL,
  `checked_out` int(11) default NULL,
  `checked_out_time` datetime default NULL,
  `access` int(11) default NULL,
  `language` char(7) default NULL,
  `inherit` int(1) default NULL,
  `params` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__bt_portfolio_categories
-- ----------------------------
INSERT INTO `#__bt_portfolio_categories` VALUES ('1', 'Portrait', 'portrait','', '', '0', '1', '1', ',', '0', '0000-00-00 00:00:00', '1', '*', '1', '{\"layout\":\"\",\"show_nav\":\"1\"}');
INSERT INTO `#__bt_portfolio_categories` VALUES ('2', 'Fashion', 'fashion', '','', '0', '1', '2', ',', '0', '0000-00-00 00:00:00', '1', '*', '1', '{\"layout\":\"\",\"show_nav\":\"1\"}');
INSERT INTO `#__bt_portfolio_categories` VALUES ('3', 'Natura', 'natura', '', '', '0', '1', '3', ',', '0', '0000-00-00 00:00:00', '1', '*', '1', '{\"layout\":\"\",\"show_nav\":\"1\"}');
INSERT INTO `#__bt_portfolio_categories` VALUES ('4', 'Landscape ', 'landscape', '', '', '0', '1', '4', ',', '0', '0000-00-00 00:00:00', '1', '*', '1', '{\"layout\":\"\",\"show_nav\":\"1\"}');
INSERT INTO `#__bt_portfolio_categories` VALUES ('5', 'Streetlife', 'streetlife', '', '', '0', '1', '5', ',', '0', '0000-00-00 00:00:00', '1', '*', '1', '{\"layout\":\"\",\"show_nav\":\"1\"}');

-- ----------------------------
-- Table structure for `#__bt_portfolio_comments`
-- ----------------------------
DROP TABLE IF EXISTS `#__bt_portfolio_comments`;
CREATE TABLE `#__bt_portfolio_comments` (
  `id` int(11) NOT NULL auto_increment,
  `item_id` int(11) default NULL,
  `user_id` int(11) default NULL,
  `name` varchar(255) default NULL,
  `email` varchar(50) default NULL,
  `website` varchar(50) default NULL,
  `title` varchar(255) default NULL,
  `content` text,
  `published` int(1) default '0',
  `created` datetime default NULL,
  `ip` varchar(15) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


-- ----------------------------
-- Table structure for `#__bt_portfolio_extrafields`
-- ----------------------------
DROP TABLE IF EXISTS `#__bt_portfolio_extrafields`;
CREATE TABLE `#__bt_portfolio_extrafields` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `type` varchar(20) default NULL,
  `all` int(4) default 0,
  `default_value` text,
  `description` text,
  `ordering` int(11) default NULL,
  `published` int(11) default NULL,
  `checked_out` int(11) default NULL,
  `checked_out_time` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `#__bt_portfolio_extrafields_values`
-- ----------------------------
DROP TABLE IF EXISTS `#__bt_portfolio_extrafields_values`;
CREATE TABLE `#__bt_portfolio_extrafields_values` (
  `portfolio_id` int(11) NOT NULL,
  `extrafields_id` int(11) NOT NULL,
  `value` text
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__bt_portfolio_extrafields
-- ----------------------------
INSERT INTO `#__bt_portfolio_extrafields` VALUES ('1', 'Client', 'string',1, 'Bow Themes', '', '1', '1', '0', '0000-00-00 00:00:00');
INSERT INTO `#__bt_portfolio_extrafields` VALUES ('2', 'Services provided', 'string', '1', 'HTML5/Photoshop', '', '2', '1', '0', '0000-00-00 00:00:00');
INSERT INTO `#__bt_portfolio_extrafields` VALUES ('3', 'Stylist ', 'string',1, 'Jonathan Dosantos', '', '3', '1', null, null);
INSERT INTO `#__bt_portfolio_extrafields` VALUES ('4', 'Summary', 'text',1, '<p>Donec aliquam vulputate malesuada. Nunc felis leo, scelerisque in adipiscing in, varius sed lacus.Ut feugiat, ante et convallis dictum</p>', '', '4', '1', null, null);

-- ----------------------------
-- Table structure for `#__bt_portfolio_images`
-- ----------------------------
DROP TABLE IF EXISTS `#__bt_portfolio_images`;
CREATE TABLE `#__bt_portfolio_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) DEFAULT NULL,
  `title` text,
  `filename` text,
  `youid` varchar(200) NOT NULL,  
  `youdesc` text NOT NULL,
  `youembed` text NOT NULL,
  `default` int(1) DEFAULT '0',
  `ordering` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


-- ----------------------------
-- Table structure for `#__bt_portfolio_vote`
-- ----------------------------
DROP TABLE IF EXISTS `#__bt_portfolio_vote`;
CREATE TABLE `#__bt_portfolio_vote` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) default NULL,
  `item_id` int(11) default NULL,
  `vote` int(2) default NULL,
  `ip` varchar(255) default NULL,
  `created` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `#__bt_portfolios`
-- ----------------------------
DROP TABLE IF EXISTS `#__bt_portfolios`;
CREATE TABLE `#__bt_portfolios` (
  `id` int(11) NOT NULL auto_increment,
  `catids` text NOT NULL,
  `title` varchar(255) default NULL,
  `image` varchar(255) default NULL,
  `youembed` varchar(255) default NULL,
  `alias` varchar(255) default NULL,
  `description` text,
  `full_description` text,
  `url` varchar(255) default NULL,
  `featured` int(3) NOT NULL default '0',
  `hits` int(25) NOT NULL,
  `published` int(1) NOT NULL default '1',
  `vote_count` int(11) NOT NULL default '0',
  `vote_sum` int(11) NOT NULL default '0',
  `review_count` int(11) NOT NULL default '0',
  `created` date default NULL,
  `created_by` int(11) default NULL,
  `modified` date default NULL,
  `modified_by` int(11) default NULL,
  `ordering` int(11) NOT NULL,
  `checked_out` int(11) default NULL,
  `checked_out_time` datetime default NULL,
  `access` int(10) default NULL,
  `language` char(7) default NULL,
  `extra_fields` text,
  `params` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
