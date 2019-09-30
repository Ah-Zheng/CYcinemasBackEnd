DROP DATABASE IF EXISTS `cy_cinemas`;
CREATE DATABASE `cy_cinemas` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 選擇資料庫
USE `cy_cinemas`;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

-- 最新消息
DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
    `id` INT AUTO_INCREMENT NOT NUll,
    `title` VARCHAR(25) NOT NUll,
    `content` TEXT NOT NUll,
    `img_normal_url` VARCHAR(255) NOT NUll,
    `img_thumbs_url` VARCHAR(255) NOT NUll,
    `start_time` DATE NOT NULL,
    `end_time` DATE NOT NULL,
    `release_time` TIMESTAMP NOT NUll DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 會員
DROP TABLE IF EXISTS `members`;
CREATE TABLE `members` (
    `id` INT AUTO_INCREMENT NOT NUll,
    `name` VARCHAR(20) NOT NUll,
    `account` VARCHAR(20) NOT NUll,
    `password` VARCHAR(100) NOT NUll,
    `email` VARCHAR(100) NOT NUll,
    `phone` VARCHAR(10) NOT NUll,
    `wallet` TINYINT DEFAULT 0,
    `point`  TINYINT DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY (`account`)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 餐點
DROP TABLE IF EXISTS `food_drinks`;
CREATE TABLE `food_drinks` (
    `id` INT AUTO_INCREMENT NOT NUll,
    `name` VARCHAR(20) NOT NULL,
    `size` VARCHAR(5) NOT NULL,
    `price` INT NOT NULL,
    PRIMARY KEY (`id`)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 正在上映的電影 a
DROP TABLE IF EXISTS `movies`;
CREATE TABLE `movies`
(
  `id` INT AUTO_INCREMENT NOT NUll,
  `encoded_id` VARCHAR(50),
  `name` VARCHAR(20),
  `enname` VARCHAR(30),
  `rating` VARCHAR(20),
  `run_time` VARCHAR(10),
  `info` TEXT,
  `actor` VARCHAR(20),
  `genre` VARCHAR(10),
  `play_date` VARCHAR(20),
  `poster` TEXT,
  `trailer` TEXT,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`encoded_id`)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;


-- 影城資訊 a
DROP TABLE IF EXISTS `theaters`;
CREATE TABLE `theaters`
(
 `id` INT AUTO_INCREMENT NOT NULL,
 `name` VARCHAR(20),
 `address` VARCHAR(30),
 `phone` VARCHAR(20),
 `imgurl` TEXT,
 PRIMARY KEY (`id`)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 電影時刻 a
DROP TABLE IF EXISTS `movie_time`;
CREATE TABLE `movie_time`
(
  `id` INT AUTO_INCREMENT NOT NULL,
  `movies_encoded_id` VARCHAR(50),
  `theaters_name` VARCHAR(20),
  `seat_tag` VARCHAR(20),
  `time` VARCHAR(10),
  `seat_info` VARCHAR(10),
  PRIMARY KEY (`id`),
  FOREIGN KEY(`movies_encoded_id`) REFERENCES `movies`(`encoded_id`)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 電影日期 a
DROP TABLE IF EXISTS `movie_day`;
CREATE TABLE `movie_day`
(
  `id` INT AUTO_INCREMENT NOT NULL,
  `movies_encoded_id` VARCHAR(50),
  `weekday` VARCHAR(20),
  `date` DATE,
  PRIMARY KEY (`id`),
  FOREIGN KEY(`movies_encoded_id`) REFERENCES `movies`(`encoded_id`)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 廳
DROP TABLE IF EXISTS `courts`;
CREATE TABLE `courts`
(
  `id` INT AUTO_INCREMENT NOT NULL,
  `seats` VARCHAR(50),
  `name` varchar(10),
  PRIMARY KEY (`id`)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 放映場次
DROP TABLE IF EXISTS `screenings`;
CREATE TABLE `screenings`
(
  `id` INT AUTO_INCREMENT NOT NULL,
  `movies_encoded_id` VARCHAR(50),
  `movie_time_time` VARCHAR(20),
  `movie_day_date` DATE,
  `courts_id` INT,
  PRIMARY KEY (`id`),
  FOREIGN KEY(`movies_encoded_id`) REFERENCES `movies`(`encoded_id`),
  FOREIGN KEY(`courts_id`) REFERENCES `courts`(`id`)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 票種
DROP TABLE IF EXISTS `tickets`;
CREATE TABLE `tickets`
(
  `id` INT AUTO_INCREMENT NOT NULL,
  `name` VARCHAR(50),
  `price` INT,
  PRIMARY KEY (`id`)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 訂單
DROP TABLE IF EXISTS `order_details`;
CREATE TABLE `order_details`
(
  `id` INT AUTO_INCREMENT NOT NULL,
  `screenings_id` INT,
  `members_id` INT,
  `num` INT,
  `seat` VARCHAR(50),
  `total_price` INT,
  `discounted_price` INT,
  `tickets_num` VARCHAR(30),
  `food_drinks_num` VARCHAR(30),
  `datetime`  TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY(`screenings_id`) REFERENCES `screenings`(`id`),
  FOREIGN KEY(`members_id`) REFERENCES `members`(`id`)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 儲值紀錄
DROP TABLE IF EXISTS `wallet_record`;
CREATE TABLE `wallet_record`
(
  `id` INT AUTO_INCREMENT NOT NULL,
  `members_id` INT,
  `update_wallet` INT,
  `current_wallet` INT,
  `desc` VARCHAR(50),
  `update_time` TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY(`members_id`) REFERENCES `members`(`id`)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 點數紀錄
DROP TABLE IF EXISTS `point_record`;
CREATE TABLE `point_record`
(
  `id` INT AUTO_INCREMENT NOT NULL,
  `members_id` INT,
  `update_point` INT,
  `current_point` INT,
  `desc` VARCHAR(50),
  `update_time` TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY(`members_id`) REFERENCES `members`(`id`)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 套票
DROP TABLE IF EXISTS `ticket_set`;
CREATE TABLE `ticket_set`
(
  `id` INT AUTO_INCREMENT NOT NULL,
  `name` INT,
  `food_drinks_num` INT,
  `tickets_num` INT,
  `desc` VARCHAR(50),
  `price` TIMESTAMP,
  PRIMARY KEY (`id`)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;