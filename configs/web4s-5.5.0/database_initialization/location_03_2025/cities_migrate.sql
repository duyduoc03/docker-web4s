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

 Date: 03/04/2025 15:14:26
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for cities_migrate
-- ----------------------------
DROP TABLE IF EXISTS `cities_migrate`;
CREATE TABLE `cities_migrate`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `code` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `type` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL COMMENT '1->Tỉnh, 2-> Thành phố',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 64 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cities_migrate
-- ----------------------------
INSERT INTO `cities_migrate` VALUES (1, 'Hà Nội', '01', '2');
INSERT INTO `cities_migrate` VALUES (2, 'Hà Giang', '02', '1');
INSERT INTO `cities_migrate` VALUES (3, 'Cao Bằng', '04', '1');
INSERT INTO `cities_migrate` VALUES (4, 'Bắc Kạn', '06', '1');
INSERT INTO `cities_migrate` VALUES (5, 'Tuyên Quang', '08', '1');
INSERT INTO `cities_migrate` VALUES (6, 'Lào Cai', '10', '1');
INSERT INTO `cities_migrate` VALUES (7, 'Điện Biên', '11', '1');
INSERT INTO `cities_migrate` VALUES (8, 'Lai Châu', '12', '1');
INSERT INTO `cities_migrate` VALUES (9, 'Sơn La', '14', '1');
INSERT INTO `cities_migrate` VALUES (10, 'Yên Bái', '15', '1');
INSERT INTO `cities_migrate` VALUES (11, 'Hòa Bình', '17', '1');
INSERT INTO `cities_migrate` VALUES (12, 'Thái Nguyên', '19', '1');
INSERT INTO `cities_migrate` VALUES (13, 'Lạng Sơn', '20', '1');
INSERT INTO `cities_migrate` VALUES (14, 'Quảng Ninh', '22', '1');
INSERT INTO `cities_migrate` VALUES (15, 'Bắc Giang', '24', '1');
INSERT INTO `cities_migrate` VALUES (16, 'Phú Thọ', '25', '1');
INSERT INTO `cities_migrate` VALUES (17, 'Vĩnh Phúc', '26', '1');
INSERT INTO `cities_migrate` VALUES (18, 'Bắc Ninh', '27', '1');
INSERT INTO `cities_migrate` VALUES (19, 'Hải Dương', '30', '1');
INSERT INTO `cities_migrate` VALUES (20, 'Hải Phòng', '31', '2');
INSERT INTO `cities_migrate` VALUES (21, 'Hưng Yên', '33', '1');
INSERT INTO `cities_migrate` VALUES (22, 'Thái Bình', '34', '1');
INSERT INTO `cities_migrate` VALUES (23, 'Hà Nam', '35', '1');
INSERT INTO `cities_migrate` VALUES (24, 'Nam Định', '36', '1');
INSERT INTO `cities_migrate` VALUES (25, 'Ninh Bình', '37', '1');
INSERT INTO `cities_migrate` VALUES (26, 'Thanh Hóa', '38', '1');
INSERT INTO `cities_migrate` VALUES (27, 'Nghệ An', '40', '1');
INSERT INTO `cities_migrate` VALUES (28, 'Hà Tĩnh', '42', '1');
INSERT INTO `cities_migrate` VALUES (29, 'Quảng Bình', '44', '1');
INSERT INTO `cities_migrate` VALUES (30, 'Quảng Trị', '45', '1');
INSERT INTO `cities_migrate` VALUES (31, 'Huế', '46', '2');
INSERT INTO `cities_migrate` VALUES (32, 'Đà Nẵng', '48', '2');
INSERT INTO `cities_migrate` VALUES (33, 'Quảng Nam', '49', '1');
INSERT INTO `cities_migrate` VALUES (34, 'Quảng Ngãi', '51', '1');
INSERT INTO `cities_migrate` VALUES (35, 'Bình Định', '52', '1');
INSERT INTO `cities_migrate` VALUES (36, 'Phú Yên', '54', '1');
INSERT INTO `cities_migrate` VALUES (37, 'Khánh Hòa', '56', '1');
INSERT INTO `cities_migrate` VALUES (38, 'Ninh Thuận', '58', '1');
INSERT INTO `cities_migrate` VALUES (39, 'Bình Thuận', '60', '1');
INSERT INTO `cities_migrate` VALUES (40, 'Kon Tum', '62', '1');
INSERT INTO `cities_migrate` VALUES (41, 'Gia Lai', '64', '1');
INSERT INTO `cities_migrate` VALUES (42, 'Đắk Lắk', '66', '1');
INSERT INTO `cities_migrate` VALUES (43, 'Đắk Nông', '67', '1');
INSERT INTO `cities_migrate` VALUES (44, 'Lâm Đồng', '68', '1');
INSERT INTO `cities_migrate` VALUES (45, 'Bình Phước', '70', '1');
INSERT INTO `cities_migrate` VALUES (46, 'Tây Ninh', '72', '1');
INSERT INTO `cities_migrate` VALUES (47, 'Bình Dương', '74', '1');
INSERT INTO `cities_migrate` VALUES (48, 'Đồng Nai', '75', '1');
INSERT INTO `cities_migrate` VALUES (49, 'Bà Rịa - Vũng Tàu', '77', '1');
INSERT INTO `cities_migrate` VALUES (50, 'Hồ Chí Minh', '79', '2');
INSERT INTO `cities_migrate` VALUES (51, 'Long An', '80', '1');
INSERT INTO `cities_migrate` VALUES (52, 'Tiền Giang', '82', '1');
INSERT INTO `cities_migrate` VALUES (53, 'Bến Tre', '83', '1');
INSERT INTO `cities_migrate` VALUES (54, 'Trà Vinh', '84', '1');
INSERT INTO `cities_migrate` VALUES (55, 'Vĩnh Long', '86', '1');
INSERT INTO `cities_migrate` VALUES (56, 'Đồng Tháp', '87', '1');
INSERT INTO `cities_migrate` VALUES (57, 'An Giang', '89', '1');
INSERT INTO `cities_migrate` VALUES (58, 'Kiên Giang', '91', '1');
INSERT INTO `cities_migrate` VALUES (59, 'Cần Thơ', '92', '2');
INSERT INTO `cities_migrate` VALUES (60, 'Hậu Giang', '93', '1');
INSERT INTO `cities_migrate` VALUES (61, 'Sóc Trăng', '94', '1');
INSERT INTO `cities_migrate` VALUES (62, 'Bạc Liêu', '95', '1');
INSERT INTO `cities_migrate` VALUES (63, 'Cà Mau', '96', '1');

SET FOREIGN_KEY_CHECKS = 1;
