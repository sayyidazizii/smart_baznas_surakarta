/*
SQLyog Professional v13.1.1 (64 bit)
MySQL - 10.4.28-MariaDB : Database - ciptaprocpanel_smart_baznas_surakarta
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
USE `ciptaprocpanel_smart_baznas_surakarta`;

/*Table structure for table `trans_service_zis_item` */

DROP TABLE IF EXISTS `trans_service_zis_item`;

CREATE TABLE `trans_service_zis_item` (
  `service_zis_item_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `service_zis_id` bigint(20) DEFAULT NULL,
  `service_zis_item_type` varchar(255) DEFAULT NULL,
  `service_zis_item_amount` decimal(20,2) DEFAULT NULL,
  `service_zis_item_remark` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  KEY `trans_service_zis_item_id` (`service_zis_item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `trans_service_zis_item` */

insert  into `trans_service_zis_item`(`service_zis_item_id`,`service_zis_id`,`service_zis_item_type`,`service_zis_item_amount`,`service_zis_item_remark`,`created_at`,`updated_at`) values 
(4,17,'2',NULL,'10kg','2023-10-02 08:36:38','2023-10-02 08:36:38'),
(5,17,'1',100000.00,NULL,'2023-10-02 08:36:38','2023-10-02 08:36:38'),
(6,20,'1',100000.00,NULL,'2023-10-02 09:49:03','2023-10-02 09:49:03'),
(7,20,'1',1000000.00,NULL,'2023-10-02 09:49:03','2023-10-02 09:49:03'),
(8,21,'2',NULL,'100 kg','2023-10-03 06:27:24','2023-10-03 06:27:24'),
(9,21,'1',1000000.00,NULL,'2023-10-03 06:27:24','2023-10-03 06:27:24'),
(10,22,'2',NULL,'100 kg','2023-10-03 06:29:26','2023-10-03 06:29:26'),
(11,23,'2',NULL,'100kg','2023-10-04 02:28:25','2023-10-04 02:28:25'),
(12,24,'1',1000000.00,NULL,'2023-10-04 03:21:44','2023-10-04 03:21:44');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
