/*
SQLyog v10.2 
MySQL - 5.5.44-0ubuntu0.14.04.1 : Database - xiaoli_chat2
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`xiaoli_chat2` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_bin */;

USE `xiaoli_chat2`;

/*Table structure for table `xl_config` */

DROP TABLE IF EXISTS `xl_config`;

CREATE TABLE `xl_config` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `name` varchar(100) CHARACTER SET latin1 NOT NULL COMMENT '配置名称（英文）',
  `display` varchar(100) CHARACTER SET latin1 NOT NULL COMMENT '配置名称（中文）',
  `type` varchar(100) CHARACTER SET latin1 NOT NULL COMMENT '配置类型',
  `value` varchar(200) CHARACTER SET latin1 NOT NULL COMMENT '配置值',
  `comment` varchar(1000) CHARACTER SET latin1 DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/*Data for the table `xl_config` */

insert  into `xl_config`(`id`,`name`,`display`,`type`,`value`,`comment`) values (1,'WECHAT_TOKEN','TOKEN','string','1e940e92a8278bc211ce8393262083ca',NULL);

/*Table structure for table `xl_dialog` */

DROP TABLE IF EXISTS `xl_dialog`;

CREATE TABLE `xl_dialog` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `username` varchar(50) COLLATE utf8_bin NOT NULL COMMENT '用户名',
  `question` text COLLATE utf8_bin NOT NULL COMMENT '问题',
  `answer` text COLLATE utf8_bin NOT NULL COMMENT '答案',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/*Data for the table `xl_dialog` */

/*Table structure for table `xl_post_msg` */

DROP TABLE IF EXISTS `xl_post_msg`;

CREATE TABLE `xl_post_msg` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `to_user_name` varchar(512) CHARACTER SET latin1 NOT NULL COMMENT '开发者微信号',
  `from_user_name` varchar(512) CHARACTER SET latin1 NOT NULL COMMENT '发送方账号（一个OpenID）',
  `create_time` int(11) NOT NULL COMMENT '消息创建时间（整型）',
  `msg_type` varchar(100) CHARACTER SET latin1 NOT NULL COMMENT '消息类型（text）',
  `content` text COLLATE utf8_bin COMMENT '文本消息内容',
  `msg_id` bigint(20) NOT NULL COMMENT '消息Id，64位',
  `pic_url` varchar(512) CHARACTER SET latin1 DEFAULT NULL COMMENT '用户发送图片消息时的链接',
  `media_id` bigint(20) DEFAULT NULL COMMENT '图片消息媒体id，可以调用多媒体文件下载接口拉取数据。',
  `format` varchar(100) CHARACTER SET latin1 DEFAULT NULL COMMENT '语音消息格式',
  `recognition` text CHARACTER SET latin1 COMMENT '语音识别',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2196 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/*Data for the table `xl_post_msg` */

/*Table structure for table `xl_push_msg` */

DROP TABLE IF EXISTS `xl_push_msg`;

CREATE TABLE `xl_push_msg` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `post_msg_id` bigint(20) NOT NULL COMMENT '接受消息编号',
  `to_user_name` varchar(512) CHARACTER SET latin1 NOT NULL COMMENT '接收方微信号（收到的OpenID）',
  `from_user_name` varchar(512) CHARACTER SET latin1 NOT NULL COMMENT '开发者微信号',
  `create_time` int(11) NOT NULL COMMENT '消息创建时间（整型）',
  `msg_type` varchar(100) CHARACTER SET latin1 NOT NULL COMMENT '消息类型（text）',
  `content` text COLLATE utf8_bin COMMENT '文本消息内容',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2192 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/*Data for the table `xl_push_msg` */

/*Table structure for table `xl_user` */

DROP TABLE IF EXISTS `xl_user`;

CREATE TABLE `xl_user` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '用户表编号',
  `username` varchar(50) COLLATE utf8_bin DEFAULT NULL COMMENT '用户微信OpenID号',
  `nickname` varchar(50) COLLATE utf8_bin DEFAULT NULL COMMENT '用户昵称（自己设置）',
  `experience` bigint(20) DEFAULT '0' COMMENT '用户经验',
  `gold` bigint(20) DEFAULT '0' COMMENT '用户金币',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/*Data for the table `xl_user` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
