DROP DATABASE IF EXISTS `cy_cinemas`;
CREATE DATABASE `cy_cinemas` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 選擇資料庫
USE `cy_cinemas`;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

-- 最新消息
DROP TABLE IF EXISTS `cy_cinemas_news`;
CREATE TABLE `cy_cinemas_news` (
    `cy_cinemas_news_id` INT AUTO_INCREMENT,
    `cy_cinemas_news_title` VARCHAR(50) NOT NUll,
    `cy_cinemas_news_content` TEXT NOT NUll,
    `cy_cinemas_news_imgurl` VARCHAR(50) NOT NUll,
    `cy_cinemas_news_date` TIMESTAMP NOT NUll,
    PRIMARY KEY (`cy_cinemas_news_id`)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 會員
DROP TABLE IF EXISTS `cy_cinemas_members`;
CREATE TABLE `cy_cinemas_members` (
    `cy_cinemas_members_id` INT AUTO_INCREMENT NOT NUll,
    `cy_cinemas_members_name` VARCHAR(50) NOT NUll,
    `cy_cinemas_members_account` VARCHAR(20) NOT NUll,
    `cy_cinemas_members_password` VARCHAR(20) NOT NUll,
    `cy_cinemas_members_email` VARCHAR(50) NOT NUll,
    `cy_cinemas_members_phone` VARCHAR(10) NOT NUll,
    `cy_cinemas_members_wallet` INT DEFAULT 0,
    `cy_cinemas_members_point`, INT DEFAULT 0,
    PRIMARY KEY (`cy_cinemas_members_id`),
    UNIQUE KEY (`cy_cinemas_members_account`)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 餐點
DROP TABLE IF EXISTS `cy_cinemas_food`;
CREATE TABLE `cy_cinemas_foods` (
    `cy_cinemas_foods_id` INT AUTO_INCREMENT,
    `cy_cinemas_foods_name` VARCHAR(20) NOT NULL,
    `cy_cinemas_foods_size` VARCHAR(5) NOT NULL,
    `cy_cinemas_foods_price` INT NOT NULL,
    PRIMARY KEY (`cy_cinemas_food_id`)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- (要再確認)
DROP TABLE IF EXISTS `cy_cinemas_screen`;
CREATE TABLE `cy_cinemas_screen` (
    cy_cinemas_screen_id INT AUTO_INCREMENT,
    cy_cinemas_movie_id INT NOT NULL,
    PRIMARY KEY (`cy_cinemas_screen_id`)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 訂單明細
DROP TABLE IF EXISTS `cy_cinemas_order_details`;
CREATE TABLE `cy_cinemas_order_details` (
    `cy_cinemas_order_details_id` INT AUTO_INCREMENT,
    `cy_cinemas_screen_id` INT NOT NULL,
    `cy_cinemas_members_account` VARCHAR(20) NOT NULL,
    `cy_cinemas_order_details_num` INT NOT NULL,
    `cy_cinemas_order_details_seat` VARCHAR(5) NOT NULL,
    `cy_cinemas_order_details_price` INT NOT NULL,
    `cy_cinemas_order_details_realprice` INT NOT NULL,
    `cy_cinemas_order_details_ticket_military` INT NOT NULL DEFAULT 0,
    `cy_cinemas_order_details_ticket_love` INT NOT NULL DEFAULT 0,
    `cy_cinemas_order_details_ticket_elder` INT NOT NULL DEFAULT 0,
    `cy_cinemas_order_details_ticket_student` INT NOT NULL DEFAULT 0,
    `cy_cinemas_order_details_order_time` TIMESTAMP,
    PRIMARY KEY (`cy_cinemas_order_details_id`),
    FOREIGN KEY (`cy_cinemas_members_account`) REFERENCES `cy_cinemas_members`(`cy_cinemas_members_account`),
    FOREIGN KEY (`cy_cinemas_screen_id`) REFERENCES `cy_cinemas_screen`(`cy_cinemas_screen_id`),
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;