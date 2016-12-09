/*
Navicat MySQL Data Transfer

Source Server         : Local
Source Server Version : 50621
Source Host           : localhost:3306
Source Database       : thithien

Target Server Type    : MYSQL
Target Server Version : 50621
File Encoding         : 65001

Date: 2016-12-09 17:24:44
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `account_info`
-- ----------------------------
DROP TABLE IF EXISTS `account_info`;
CREATE TABLE `account_info` (
  `account_id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `gender` int(11) NOT NULL DEFAULT '0',
  `birthday` date DEFAULT NULL,
  `place_of_birth` int(11) DEFAULT '0',
  `home_town` int(11) DEFAULT '0',
  `address` varchar(225) NOT NULL,
  `identity` varchar(225) DEFAULT NULL,
  `identity_date` date DEFAULT '0000-00-00',
  `identity_place` int(11) DEFAULT '0',
  `passport` varchar(20) DEFAULT ' ',
  `passport_date` date DEFAULT '0000-00-00',
  `passport_place` int(11) DEFAULT '0',
  `social_insurance` varchar(20) DEFAULT ' ',
  `tax_code` varchar(225) DEFAULT ' ',
  `bank_account` varchar(20) DEFAULT ' ',
  `bank_account_id` int(11) DEFAULT '0',
  `bank_account_branch` int(11) DEFAULT '0',
  `marital_status` smallint(6) DEFAULT '0',
  `no_of_children` smallint(6) DEFAULT '0',
  `contract_type` int(4) DEFAULT '0',
  `level` smallint(6) NOT NULL DEFAULT '0' COMMENT '0 => ''member'',1 => ''VIP''',
  `email` varchar(225) NOT NULL DEFAULT ' ',
  `phone` varchar(225) NOT NULL DEFAULT ' ',
  `picture` varchar(225) DEFAULT ' ',
  `avatar` varchar(225) DEFAULT ' ',
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `skype_account` varchar(225) DEFAULT ' ',
  `google_account` varchar(225) DEFAULT ' ',
  `facebook_account` varchar(225) DEFAULT ' ',
  `yahoo_account` varchar(225) DEFAULT ' ',
  `country_id` int(20) unsigned DEFAULT NULL,
  `contract_sign_date` date DEFAULT NULL,
  `name` varchar(225) NOT NULL,
  `first_name` varchar(255) DEFAULT ' ',
  `last_name` varchar(255) DEFAULT ' ',
  `email1` varchar(255) DEFAULT ' ',
  `contact_name` varchar(255) DEFAULT ' ' COMMENT 'relationship with user',
  `contact_relationship` varchar(255) DEFAULT ' ',
  `contact_address` varchar(255) DEFAULT ' ' COMMENT 'relationship with user',
  `contact_phone` varchar(11) DEFAULT ' ' COMMENT 'relationship with user',
  `description` text,
  `status` int(4) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `upper_times` int(11) NOT NULL,
  `username` varchar(225) NOT NULL,
  `lang` varchar(10) NOT NULL DEFAULT 'vn',
  `last_login_date` int(11) DEFAULT NULL,
  `create_date` int(11) NOT NULL,
  `update_date` int(11) NOT NULL,
  PRIMARY KEY (`account_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of account_info
-- ----------------------------

-- ----------------------------
-- Table structure for `action_log`
-- ----------------------------
DROP TABLE IF EXISTS `action_log`;
CREATE TABLE `action_log` (
  `log_id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `log_account_id` int(11) NOT NULL,
  `log_action` varchar(50) NOT NULL,
  `log_note` varchar(255) NOT NULL,
  `log_useragent` varchar(255) NOT NULL,
  `log_ip` varchar(30) NOT NULL,
  `log_create_date` int(11) NOT NULL,
  `log_update_date` int(11) NOT NULL,
  PRIMARY KEY (`log_id`),
  KEY `search_log_key` (`log_account_id`,`log_action`,`log_note`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of action_log
-- ----------------------------

-- ----------------------------
-- Table structure for `admin`
-- ----------------------------
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `admin_id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(20) unsigned NOT NULL,
  `modules` varchar(255) NOT NULL DEFAULT ' ',
  `roles` varchar(50) NOT NULL DEFAULT ' ',
  `super_admin` tinyint(1) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `create_date` int(11) NOT NULL,
  `update_date` int(11) NOT NULL,
  PRIMARY KEY (`admin_id`),
  KEY `account_id` (`account_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of admin
-- ----------------------------

-- ----------------------------
-- Table structure for `approve_news_log`
-- ----------------------------
DROP TABLE IF EXISTS `approve_news_log`;
CREATE TABLE `approve_news_log` (
  `app_id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `app_account_id` int(11) NOT NULL,
  `app_news_id` int(11) NOT NULL,
  `app_is_approved` tinyint(1) NOT NULL DEFAULT '0',
  `app_comment` text,
  `app_useragent` varchar(255) NOT NULL,
  `app_ip` varchar(30) NOT NULL,
  `app_create_date` int(11) NOT NULL,
  `app_update_date` int(11) NOT NULL,
  PRIMARY KEY (`app_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of approve_news_log
-- ----------------------------

-- ----------------------------
-- Table structure for `banner_info`
-- ----------------------------
DROP TABLE IF EXISTS `banner_info`;
CREATE TABLE `banner_info` (
  `b_id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `b_name` int(11) NOT NULL DEFAULT '0',
  `b_image_path` varchar(255) NOT NULL,
  `b_popup_image_path` varchar(255) NOT NULL,
  `b_url` varchar(255) NOT NULL DEFAULT '0',
  `b_pos` varchar(20) NOT NULL DEFAULT 'TOP' COMMENT 'TOP, BOTTOM, LEFT, RIGHT, MIDDLE',
  `b_controller` varchar(255) NOT NULL DEFAULT 'trang-chu' COMMENT 'controller will present',
  `b_order` int(2) NOT NULL DEFAULT '1' COMMENT 'ORDER BY',
  `b_active` tinyint(1) NOT NULL DEFAULT '1',
  `b_create_date` int(11) NOT NULL,
  `b_update_date` int(11) NOT NULL,
  PRIMARY KEY (`b_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of banner_info
-- ----------------------------

-- ----------------------------
-- Table structure for `post_news`
-- ----------------------------
DROP TABLE IF EXISTS `post_news`;
CREATE TABLE `post_news` (
  `news_id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `news_type` tinyint(1) NOT NULL COMMENT '0: normal, 1: bds, 2: vl, 3: oto, 4: xe may, 6:dien tu',
  `news_account_id` int(11) NOT NULL,
  `news_cat_id` int(4) NOT NULL,
  `news_sub_cat_id` int(4) NOT NULL,
  `news_city_id` int(4) NOT NULL,
  `news_district_id` int(4) NOT NULL,
  `news_ward_id` int(4) DEFAULT NULL,
  `news_price` int(11) DEFAULT '0',
  `news_tensp` varchar(255) DEFAULT NULL,
  `news_tittle` varchar(255) DEFAULT NULL,
  `news_contact_name` varchar(255) DEFAULT NULL,
  `news_mobile` varchar(14) NOT NULL,
  `news_email` varchar(255) NOT NULL,
  `news_address` varchar(255) NOT NULL,
  `news_detail` text,
  `job_gender_id` int(2) DEFAULT NULL,
  `job_birth_year` int(4) DEFAULT NULL,
  `job_type_id` int(2) DEFAULT NULL,
  `job_cat_id` int(2) DEFAULT NULL COMMENT 'nganh nghe',
  `job_birth_year_from` int(2) DEFAULT NULL,
  `job_birth_year_to` int(2) DEFAULT NULL,
  `job_experience` varchar(255) DEFAULT NULL,
  `job_salary_from` varchar(255) DEFAULT NULL,
  `job_salary_to` varchar(255) DEFAULT NULL,
  `vehicle_model` varchar(255) DEFAULT NULL,
  `vehicle_made_by_id` int(2) DEFAULT NULL COMMENT 'Hang xe',
  `vehicle_body_style_id` int(2) DEFAULT NULL COMMENT 'Dong xe',
  `vehicle_made_year_id` int(2) DEFAULT NULL COMMENT 'nam sx',
  `vehicle_origin_id` int(2) DEFAULT NULL COMMENT 'xuat xu',
  `vehicle_new_percen` varchar(4) DEFAULT NULL,
  `vehicle_transmission_id` int(2) DEFAULT NULL COMMENT 'hop so',
  `vehicle_driver_type_id` int(2) DEFAULT NULL COMMENT 'kieu dan dong',
  `vehicle_fuel_type_id` int(2) DEFAULT NULL COMMENT 'nhien lieu',
  `vehicle_color_id` int(2) DEFAULT NULL,
  `vehicle_safety` varchar(255) DEFAULT NULL,
  `vehicle_feature` varchar(255) DEFAULT NULL,
  `proper_type_id` int(2) DEFAULT NULL,
  `proper_address` varchar(255) DEFAULT NULL,
  `proper_ward_id` int(4) DEFAULT NULL,
  `proper_project` varchar(255) DEFAULT NULL,
  `proper_CT1` varchar(255) DEFAULT NULL,
  `proper_CT2` varchar(255) DEFAULT NULL,
  `proper_CT3_id` int(2) DEFAULT NULL,
  `proper_CT4_id` int(2) DEFAULT NULL,
  `proper_CT5` varchar(255) DEFAULT NULL,
  `proper_CT6` varchar(255) DEFAULT NULL,
  `proper_CT7` varchar(255) DEFAULT NULL,
  `news_is_upper_today` tinyint(1) NOT NULL,
  `news_create_date` int(11) NOT NULL,
  `news_update_date` int(11) NOT NULL,
  PRIMARY KEY (`news_id`),
  KEY `search_key` (`news_tittle`,`news_email`),
  KEY `search_key1` (`news_contact_name`,`news_address`),
  KEY `search_key2` (`news_price`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of post_news
-- ----------------------------
INSERT INTO post_news VALUES ('1', '2', '3', '2', '2', '3', '86', null, '50000', null, 'Ban hang ko can hoi', 'Hoang Trung', '0948756556', 'danghek@kjdj.com', '98 Hang Ding Da', 'MUA HÀNG AN TOÀN\r\nKHÔNG trả tiền trước khi nhận hàng.\r\nKiểm tra hàng cẩn thận, đặc biệt với hàng đắt tiền.\r\nHẹn gặp ở nơi công cộng.\r\nNếu bạn mua hàng hiệu, hãy gặp mặt tại cửa hàng để nhờ xác minh, tránh mua phải hàng giả.', '0', '1987', '4', '5', '28', '35', '5 năm', '10tr', '30tr', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '1', '2147483647', '2147483647');
INSERT INTO post_news VALUES ('2', '2', '3', '2', '2', '3', '86', '344', '50000', null, 'Tổ 7 Nguyễn Chí Thanh. 30m ra phố.nhà 3 tầng, thông', 'A Thành', '0911481802', 'danghek@kjdj.com', '98 Hang Ding Da', 'DT: 28.34m x 3 tầng. XDTT: 30m. khung cột chắc chắn. ngõ 2m xe máy chánh nhau thoải mãi. Trên mặt ngõ thẳng, TK 3 tầng khung cột chắc chắn, nội thất hoàn hảo.\r\nSDCC. Chủ nhà cần vốn KD lên bán gấp. rất cần bán\r\nGiá: 2.59 tỷ\r\nLH: a thành. 0911481802', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '3', '276 Ham Nghi', '876', '', 'CT1 value', 'CT2 value', '3', '2', 'CT5 val', 'CT6 val', 'CT7 val', '1', '2147483647', '2147483647');
INSERT INTO post_news VALUES ('3', '2', '3', '2', '2', '3', '86', '344', '50000', null, 'Tổ 7 Nguyễn Chí Thanh. 30m ra phố.nhà 3 tầng, thông', 'A Thành', '0911481802', 'danghek@kjdj.com', '98 Hang Ding Da', 'DT: 28.34m x 3 tầng. XDTT: 30m. khung cột chắc chắn. ngõ 2m xe máy chánh nhau thoải mãi. Trên mặt ngõ thẳng, TK 3 tầng khung cột chắc chắn, nội thất hoàn hảo.\r\nSDCC. Chủ nhà cần vốn KD lên bán gấp. rất cần bán\r\nGiá: 2.59 tỷ\r\nLH: a thành. 0911481802', null, null, null, null, null, null, null, null, null, 'Civic', '5', '2', '1975', '1', '90', '2', '3', '3', '2', '{{\"id\":\"3\",\"name\":\"Khóa cửa tự động\"},{\"id\":\"6\",\"name\":\"Hệ thống báo trộm ngoại vi\"},{\"id\":\"9\",\"name\":\"Trợ lực phanh khẩn cấp(EBA)\"}}', '{{\"id\":\"2\",\"name\":\"Kính chỉnh điện\"},{\"id\":\"4\",\"name\":\"Sấy kính sau\"},{\"id\":\"5\",\"name\":\"Gạt kính phía sau\"}}', null, null, null, null, null, null, null, null, null, null, null, '1', '2147483647', '2147483647');
INSERT INTO post_news VALUES ('4', '2', '3', '2', '2', '3', '86', '344', '260000000', null, 'Tổ 7 Nguyễn Chí Thanh. 30m ra phố.nhà 3 tầng, thông', 'A Thành', '0909558012', 'danghek@kjdj.com', '53/3 Lê Liễu', 'Nhà bán 53/3 Lê Liễu, phường Tân Quý, quận Tân Phú, 4x12m, 1 trệt, 1 lầu đúc thật, hẻm xe hơi 4m, hết lộ giới, an ninh, thông thoáng, gần Tân Kỳ Tân Quý, giá 2,6 tỷ. Liên hệ: 0909558012', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '3', '276 Ham Nghi', '876', '', 'CT1 value', 'CT2 value', '3', '2', 'CT5 val', 'CT6 val', 'CT7 val', '1', '2147483647', '2147483647');
INSERT INTO post_news VALUES ('5', '2', '3', '2', '3', '86', '344', null, '50000', null, 'Bán gấp xe Honda SH 150i tại TPHCM, màu đỏ đô, đời 2007, giá rẻ', 'Anh Phát', '0933316839', 'dansghsdsdek@kjdj.com', '98 Hang Ding Da', 'Cần bán gấp xe Honda SH 150i tại TPHCM, màu đỏ đô, đời 2007, chính chủ đứng tên, giá rẻ, mong tiếp người thiện chí mua. Tel. Điện thoại 0933316839 - Anh Phát', null, null, null, null, null, null, null, null, null, null, '6', null, '2013', '0', '95', null, null, null, '6', null, null, null, null, null, null, null, null, null, null, null, null, null, '1', '2147483647', '2147483647');
INSERT INTO post_news VALUES ('6', '4', '3', '4', '4', '4', '105', null, '500000', null, 'Cho thuê xe Honda SH 150i tại TPHCM, màu đỏ đô, đời 2007, giá rẻ', 'Anh Phát', '0933316839', 'sdsdek@kjdj.com', '98 Hngun nhadre', 'Cần cho thuê xe Honda SH 150i tại TPHCM, màu đỏ đô, đời 2007, chính chủ đứng tên, giá rẻ, mong tiếp người thiện chí mua. Tel. Điện thoại 0933316839 - Anh Phát', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '1', '2147483647', '2147483647');

-- ----------------------------
-- Table structure for `spam_reports`
-- ----------------------------
DROP TABLE IF EXISTS `spam_reports`;
CREATE TABLE `spam_reports` (
  `spam_id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `spam_news_id` int(11) NOT NULL,
  `spam_reson_id` int(2) NOT NULL,
  `spam_comment` text,
  `spam_read` tinyint(1) NOT NULL DEFAULT '0',
  `spam_create_date` int(11) NOT NULL,
  `spam_update_date` int(11) NOT NULL,
  PRIMARY KEY (`spam_id`),
  KEY `spam_search_key` (`spam_news_id`,`spam_reson_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of spam_reports
-- ----------------------------

-- ----------------------------
-- Table structure for `token`
-- ----------------------------
DROP TABLE IF EXISTS `token`;
CREATE TABLE `token` (
  `token_id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(50) NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `account_id` int(20) NOT NULL DEFAULT '0',
  `username` varchar(255) NOT NULL DEFAULT ' ',
  `avatar` varchar(255) DEFAULT NULL,
  `ps` varchar(255) NOT NULL,
  `IPOwner` varchar(255) DEFAULT NULL,
  `IPClient` varchar(255) DEFAULT NULL,
  `expired` int(11) DEFAULT NULL,
  `create_date` int(11) DEFAULT NULL,
  `update_date` int(11) DEFAULT NULL,
  PRIMARY KEY (`token_id`),
  KEY `token_search_1` (`key`,`IPClient`,`username`,`expired`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of token
-- ----------------------------
INSERT INTO token VALUES ('1', '57e55d5abb8511e69b31496e7465726e', '0', '1', ' danghien', null, 'skjdjdfhfjdhf', '127.0.0.1', '127.0.0.1', '3000', '384734737', null);
