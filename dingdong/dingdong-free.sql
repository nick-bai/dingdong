/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50651
 Source Host           : localhost:3306
 Source Schema         : dingdong-free

 Target Server Type    : MySQL
 Target Server Version : 50651
 File Encoding         : 65001

 Date: 19/05/2022 15:35:49
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for df_chat_log
-- ----------------------------
DROP TABLE IF EXISTS `df_chat_log`;
CREATE TABLE `df_chat_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '日志id',
  `from_id` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '网页用户随机编号(仅为记录参考记录)',
  `from_name` varchar(55) CHARACTER SET utf8 NOT NULL COMMENT '发送者名称',
  `from_avatar` varchar(155) CHARACTER SET utf8 NOT NULL COMMENT '发送者头像',
  `to_id` varchar(55) CHARACTER SET utf8 NOT NULL COMMENT '接收方',
  `to_name` varchar(55) CHARACTER SET utf8 NOT NULL COMMENT '接收者名称',
  `to_avatar` varchar(155) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '接收者头像',
  `content` text CHARACTER SET utf8mb4 NOT NULL COMMENT '发送的内容',
  `msg_type` varchar(55) CHARACTER SET utf8mb4 DEFAULT 'text' COMMENT '消息类型',
  `create_time` datetime NOT NULL COMMENT '记录时间',
  PRIMARY KEY (`id`),
  KEY `from_id` (`from_id`) USING BTREE,
  KEY `to_id` (`to_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='聊天记录表';

-- ----------------------------
-- Records of df_chat_log
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for df_customer
-- ----------------------------
DROP TABLE IF EXISTS `df_customer`;
CREATE TABLE `df_customer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '访客id',
  `customer_name` varchar(55) CHARACTER SET utf8 NOT NULL COMMENT '访客名称',
  `customer_avatar` varchar(155) CHARACTER SET utf8 NOT NULL COMMENT '访客头像',
  `customer_ip` varchar(15) CHARACTER SET utf8 NOT NULL COMMENT '访客ip',
  `pre_kefu_id` int(11) DEFAULT NULL COMMENT '上次服务的客服id',
  `client_id` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '客户端标识',
  `online_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 离线 1 在线',
  `create_time` datetime NOT NULL COMMENT '访问时间',
  `province` varchar(55) CHARACTER SET utf8 DEFAULT NULL COMMENT '访客所在省份',
  `city` varchar(55) CHARACTER SET utf8 DEFAULT NULL COMMENT '访客所在城市',
  PRIMARY KEY (`id`),
  KEY `visiter` (`customer_id`) USING BTREE,
  KEY `time` (`create_time`) USING BTREE,
  KEY `idx_customer_list` (`customer_id`,`customer_name`,`customer_avatar`,`online_status`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='访客表';

-- ----------------------------
-- Records of df_customer
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for df_kefu
-- ----------------------------
DROP TABLE IF EXISTS `df_kefu`;
CREATE TABLE `df_kefu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(32) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '客服标识',
  `phone` varchar(11) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '手机号',
  `name` varchar(155) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '昵称',
  `avatar` varchar(155) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '头像',
  `password` varchar(32) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '密码',
  `status` tinyint(2) DEFAULT '2' COMMENT '1:在线 2:离线',
  `last_login_time` datetime DEFAULT NULL COMMENT '上次登录时间',
  `last_ip` varchar(11) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '登录的ip',
  `is_del` tinyint(2) DEFAULT '1' COMMENT '1 正常 2 删除',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='客服表';

-- ----------------------------
-- Records of df_kefu
-- ----------------------------
BEGIN;
INSERT INTO `df_kefu` (`id`, `code`, `phone`, `name`, `avatar`, `password`, `status`, `last_login_time`, `last_ip`, `is_del`, `create_time`, `update_time`) VALUES (1, '60f7f2a3d9337', '15500000000', '官方客服', 'http://www.df.com/storage/upload/20220515/6c72d7f2d940131a17a0d8024ac5c276.png', 'e4ef80584f03f24d886e34fb24e41cec', 1, '2022-05-17 22:16:01', '127.0.0.1', 1, '2022-05-15 18:45:24', '2022-05-15 23:50:45');
INSERT INTO `df_kefu` (`id`, `code`, `phone`, `name`, `avatar`, `password`, `status`, `last_login_time`, `last_ip`, `is_del`, `create_time`, `update_time`) VALUES (2, '6280ffae34e9a', '15695212876', '小李', 'http://www.df.com/storage/upload/20220515/1e1317b78531b4e0d315f55a43be3d4a.png', 'e4ef80584f03f24d886e34fb24e41cec', 2, '2022-05-16 23:08:30', '127.0.0.1', 1, '2022-05-15 21:27:10', '2022-05-15 21:37:57');
COMMIT;

-- ----------------------------
-- Table structure for df_kefu_distribution
-- ----------------------------
DROP TABLE IF EXISTS `df_kefu_distribution`;
CREATE TABLE `df_kefu_distribution` (
  `distribute_id` int(11) NOT NULL AUTO_INCREMENT,
  `kefu_map` longtext CHARACTER SET utf8 COMMENT '待分配的客服数组',
  PRIMARY KEY (`distribute_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='客服分配表';

-- ----------------------------
-- Records of df_kefu_distribution
-- ----------------------------
BEGIN;
INSERT INTO `df_kefu_distribution` (`distribute_id`, `kefu_map`) VALUES (1, '[{\"id\":1,\"role_id\":1,\"code\":\"60f7f2a3d9337\",\"name\":\"官方客服\",\"avatar\":\"http://www.df.com/storage/upload/20220515/6c72d7f2d940131a17a0d8024ac5c276.png\"}]');
COMMIT;

-- ----------------------------
-- Table structure for df_leave_msg
-- ----------------------------
DROP TABLE IF EXISTS `df_leave_msg`;
CREATE TABLE `df_leave_msg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(155) CHARACTER SET utf8 NOT NULL COMMENT '留言人名称',
  `phone` char(11) CHARACTER SET utf8 NOT NULL COMMENT '留言人手机号',
  `content` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '留言内容',
  `add_time` datetime DEFAULT NULL COMMENT '留言时间',
  `status` tinyint(1) DEFAULT '1' COMMENT '留言是否已读 1 未读 2 已读',
  `update_time` datetime DEFAULT NULL COMMENT '已读处理时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC COMMENT='离线留言表';

-- ----------------------------
-- Records of df_leave_msg
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for df_now_service
-- ----------------------------
DROP TABLE IF EXISTS `df_now_service`;
CREATE TABLE `df_now_service` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `kefu_id` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '客服标识',
  `customer_id` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '访客id',
  `client_id` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '访客的客户端id',
  `create_time` int(10) NOT NULL COMMENT '记录添加时间',
  `service_log_id` int(11) DEFAULT '0' COMMENT '当前服务的日志id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=127 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='当前服务信息表';

-- ----------------------------
-- Records of df_now_service
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for df_service_log
-- ----------------------------
DROP TABLE IF EXISTS `df_service_log`;
CREATE TABLE `df_service_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '服务编号',
  `customer_id` varchar(55) CHARACTER SET utf8 DEFAULT NULL COMMENT '访客id',
  `client_id` varchar(32) CHARACTER SET utf8 DEFAULT NULL COMMENT '访客的客户端标识',
  `customer_name` varchar(55) CHARACTER SET utf8 DEFAULT NULL COMMENT '访客名称',
  `customer_avatar` varchar(155) CHARACTER SET utf8 DEFAULT NULL COMMENT '访客头像',
  `customer_ip` varchar(15) CHARACTER SET utf8 DEFAULT NULL COMMENT '访客的ip',
  `kefu_code` varchar(32) CHARACTER SET utf8 DEFAULT '0' COMMENT '接待的客服标识',
  `kefu_name` varchar(155) CHARACTER SET utf8 DEFAULT NULL COMMENT '接待客服名称',
  `start_time` datetime DEFAULT NULL COMMENT '开始服务时间',
  `end_time` datetime DEFAULT NULL COMMENT '结束服务时间',
  `chat_time` int(11) DEFAULT NULL COMMENT '对话时长（秒数）',
  `status` tinyint(2) DEFAULT '1' COMMENT '1 接待中 2 已结束',
  PRIMARY KEY (`id`),
  KEY `idx_chat_time` (`chat_time`) USING BTREE,
  KEY `idx_start_time` (`start_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='访客历史接待记录表';

-- ----------------------------
-- Records of df_service_log
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for df_word
-- ----------------------------
DROP TABLE IF EXISTS `df_word`;
CREATE TABLE `df_word` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(155) CHARACTER SET utf8 NOT NULL COMMENT '简略标题',
  `word` text CHARACTER SET utf8 NOT NULL COMMENT '常用语内容',
  `cate_id` int(11) NOT NULL COMMENT '所属分类id',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='常用语表';

-- ----------------------------
-- Records of df_word
-- ----------------------------
BEGIN;
INSERT INTO `df_word` (`id`, `title`, `word`, `cate_id`, `create_time`, `update_time`) VALUES (2, '怎么购买东西', '签订合同', 1, '2022-05-15 23:47:50', NULL);
COMMIT;

-- ----------------------------
-- Table structure for df_word_cate
-- ----------------------------
DROP TABLE IF EXISTS `df_word_cate`;
CREATE TABLE `df_word_cate` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '问题分类id',
  `cate_name` varchar(55) CHARACTER SET utf8mb4 NOT NULL COMMENT '问题分类名称',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 启用 2 禁用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='常用语分类表';

-- ----------------------------
-- Records of df_word_cate
-- ----------------------------
BEGIN;
INSERT INTO `df_word_cate` (`id`, `cate_name`, `status`) VALUES (1, '购买问题', 1);
INSERT INTO `df_word_cate` (`id`, `cate_name`, `status`) VALUES (2, '使用问题', 1);
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
