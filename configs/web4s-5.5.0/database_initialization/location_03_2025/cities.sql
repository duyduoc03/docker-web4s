/*
 Navicat Premium Data Transfer

 Source Server         : 103.28.38.185
 Source Server Type    : MySQL
 Source Server Version : 100509
 Source Host           : 103.28.38.185:3306
 Source Schema         : dev_tgtt

 Target Server Type    : MySQL
 Target Server Version : 100509
 File Encoding         : 65001

 Date: 03/04/2025 15:47:58
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for cities
-- ----------------------------
DROP TABLE IF EXISTS `cities`;
CREATE TABLE `cities`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `type` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `country_id` int NOT NULL,
  `position` int NULL DEFAULT NULL,
  `status` int NULL DEFAULT NULL,
  `deleted` int NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `status`(`status`) USING BTREE,
  INDEX `del_flg`(`deleted`) USING BTREE,
  INDEX `id`(`id`) USING BTREE,
  INDEX `position`(`position`) USING BTREE,
  INDEX `position_2`(`position`, `deleted`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 97 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cities
-- ----------------------------
INSERT INTO `cities` VALUES (1, 'Hà Nội', '2', 1, 5, 1, 0);
INSERT INTO `cities` VALUES (2, 'Hà Giang', '1', 1, 5, 1, 0);
INSERT INTO `cities` VALUES (4, 'Cao Bằng', '1', 1, 3, 1, 0);
INSERT INTO `cities` VALUES (6, 'Bắc Kạn', '1', 1, 10, 1, 0);
INSERT INTO `cities` VALUES (8, 'Tuyên Quang', '1', 1, 11, 1, 0);
INSERT INTO `cities` VALUES (10, 'Lào Cai', '1', 1, 12, 1, 0);
INSERT INTO `cities` VALUES (11, 'Điện Biên', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (12, 'Lai Châu', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (14, 'Sơn La', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (15, 'Yên Bái', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (17, 'Hòa Bình', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (19, 'Thái Nguyên', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (20, 'Lạng Sơn', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (22, 'Quảng Ninh', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (24, 'Bắc Giang', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (25, 'Phú Thọ', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (26, 'Vĩnh Phúc', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (27, 'Bắc Ninh', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (30, 'Hải Dương', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (31, 'Hải Phòng', '2', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (33, 'Hưng Yên', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (34, 'Thái Bình', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (35, 'Hà Nam', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (36, 'Nam Định', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (37, 'Ninh Bình', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (38, 'Thanh Hóa', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (40, 'Nghệ An', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (42, 'Hà Tĩnh', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (44, 'Quảng Bình', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (45, 'Quảng Trị', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (46, 'Huế', '2', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (48, 'Đà Nẵng', '2', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (49, 'Quảng Nam', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (51, 'Quảng Ngãi', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (52, 'Bình Định', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (54, 'Phú Yên', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (56, 'Khánh Hòa', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (58, 'Ninh Thuận', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (60, 'Bình Thuận', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (62, 'Kon Tum', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (64, 'Gia Lai', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (66, 'Đắk Lắk', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (67, 'Đắk Nông', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (68, 'Lâm Đồng', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (70, 'Bình Phước', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (72, 'Tây Ninh', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (74, 'Bình Dương', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (75, 'Đồng Nai', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (77, 'Bà Rịa - Vũng Tàu', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (79, 'Hồ Chí Minh', '2', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (80, 'Long An', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (82, 'Tiền Giang', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (83, 'Bến Tre', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (84, 'Trà Vinh', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (86, 'Vĩnh Long', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (87, 'Đồng Tháp', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (89, 'An Giang', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (91, 'Kiên Giang', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (92, 'Cần Thơ', '2', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (93, 'Hậu Giang', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (94, 'Sóc Trăng', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (95, 'Bạc Liêu', '1', 1, NULL, 1, 0);
INSERT INTO `cities` VALUES (96, 'Cà Mau', '1', 1, NULL, 1, 0);

SET FOREIGN_KEY_CHECKS = 1;
