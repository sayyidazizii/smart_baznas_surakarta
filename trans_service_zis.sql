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

/*Table structure for table `trans_service_zis` */

DROP TABLE IF EXISTS `trans_service_zis`;

CREATE TABLE `trans_service_zis` (
  `service_zis_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `service_zis_type` varchar(255) DEFAULT NULL,
  `service_zis_category` varchar(255) DEFAULT NULL,
  `service_zis_date` date DEFAULT NULL,
  `service_zis_name` varchar(255) DEFAULT NULL,
  `service_zis_address` text DEFAULT NULL,
  `kelurahan_id` int(11) DEFAULT NULL,
  `kecamatan_id` int(11) DEFAULT NULL,
  `service_zis_phone` varchar(255) DEFAULT NULL,
  `service_zis_remark` text DEFAULT NULL,
  `data_state` int(11) NOT NULL DEFAULT 0,
  `created_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  KEY `service_zis_id` (`service_zis_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `trans_service_zis` */

insert  into `trans_service_zis`(`service_zis_id`,`service_zis_type`,`service_zis_category`,`service_zis_date`,`service_zis_name`,`service_zis_address`,`kelurahan_id`,`kecamatan_id`,`service_zis_phone`,`service_zis_remark`,`data_state`,`created_id`,`created_at`,`updated_at`) values 
(17,'2','1','2023-10-02','Sayyid','fdsjflaj',NULL,NULL,'32457980375',NULL,1,55,'2023-10-02 08:36:38','2023-10-02 09:15:51'),
(20,'2','2','2023-10-02','Dafa','Solo',9,1,'085602678871',NULL,0,55,'2023-10-02 09:49:03','2023-10-04 03:24:48'),
(21,'2','1','2023-10-03','Sayyid','Jebres',20,2,'083145540378',NULL,0,NULL,'2023-10-03 06:27:24','2023-10-04 03:17:02'),
(22,'1','1','2023-10-03','Baznas Solo','Surakarta',34,3,'083745141',NULL,0,NULL,'2023-10-03 06:29:26','2023-10-04 03:17:07'),
(23,'2','1','2023-10-04','Arka vincent','Kauman,surakarta',41,4,'932759482765',NULL,0,55,'2023-10-04 02:28:25','2023-10-04 03:17:11'),
(24,'2','3','2023-10-04','bu ani','Banjarsari',2,1,'247981275',NULL,0,55,'2023-10-04 03:21:44','2023-10-04 03:22:03');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
