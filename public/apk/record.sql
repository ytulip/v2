/*
Navicat MySQL Data Transfer

Source Server         : zhuyan
Source Server Version : 50547
Source Host           : 121.43.60.78:3306
Source Database       : app_listenbook

Target Server Type    : MYSQL
Target Server Version : 50547
File Encoding         : 65001

Date: 2017-07-31 09:52:10
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for record
-- ----------------------------
DROP TABLE IF EXISTS `record`;
CREATE TABLE `record` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '播放记录ID',
  `user_id` int(10) DEFAULT NULL COMMENT '用户ID',
  `addtime` datetime DEFAULT NULL COMMENT '播放时间',
  `audio_id` int(10) DEFAULT NULL COMMENT '播放文件ID',
  `room_id` int(10) DEFAULT NULL COMMENT '房间ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9750705 DEFAULT CHARSET=utf8 COMMENT='播放记录';
