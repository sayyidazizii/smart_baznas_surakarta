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

/*Table structure for table `system_menu` */

DROP TABLE IF EXISTS `system_menu`;

CREATE TABLE `system_menu` (
  `id_menu` varchar(10) NOT NULL,
  `id` varchar(100) DEFAULT NULL,
  `type` enum('folder','file','function') DEFAULT NULL,
  `indent_level` int(1) DEFAULT NULL,
  `text` varchar(50) DEFAULT NULL,
  `image` varchar(50) DEFAULT NULL,
  `last_update` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_menu`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Data for the table `system_menu` */

insert  into `system_menu`(`id_menu`,`id`,`type`,`indent_level`,`text`,`image`,`last_update`) values 
('0','home','file',1,'Beranda',NULL,'2021-12-18 11:09:42'),
('1','#','folder',1,'System',NULL,'2021-12-18 10:37:18'),
('11','system-user','file',2,'System User',NULL,'2021-12-18 10:37:19'),
('12','system-user-group','file',2,'System User Group',NULL,'2021-12-18 10:37:22'),
('2','#','folder',1,'Layanan',NULL,'2021-10-26 09:59:19'),
('21','#','folder',2,'Preferensi',NULL,'2021-10-26 09:59:36'),
('211','section','file',3,'Bagian',NULL,'2021-10-26 11:18:27'),
('212','service','file',3,'Bantuan',NULL,'2023-09-26 10:53:56'),
('213','kecamatan','file',3,'Kecamatan',NULL,'2023-05-11 10:11:29'),
('22','#','folder',2,'Pengajuan',NULL,'2021-12-11 11:16:28'),
('221','trans-service-requisition','file',3,'Pengajuan Bantuan',NULL,'2021-12-16 10:11:30'),
('222','trans-service-general','file',3,'Pengajuan Surat Umum',NULL,'2022-01-25 14:02:28'),
('23','#','folder',2,'Disposisi',NULL,'2021-12-18 12:09:39'),
('231','trans-service-disposition','file',3,'Disposisi Bantuan',NULL,'2021-12-18 12:10:05'),
('232','trans-service-disposition-approval','file',3,'Persetujuan Disposisi Bantuan',NULL,'2021-12-18 12:10:13'),
('233','trans-service-disposition-review','file',3,'Review Disposisi Bantuan',NULL,'2021-12-18 12:10:19'),
('234','trans-service-disposition-funds','file',3,'Pencairan Disposisi Bantuan',NULL,'2022-03-19 11:24:57'),
('235','mustahik-worksheet-result','file',2,'Data Mustappa',NULL,'2022-12-20 09:34:06'),
('24','#','folder',2,'ZIS',NULL,'2023-10-02 09:22:43'),
('25','trans-service-zis','file',3,'Zakat Infaq Sedekah',NULL,'2023-10-02 09:29:24'),
('3','#','folder',1,'Surat Umum',NULL,'2022-01-25 14:07:37'),
('31','service-general-parameter','file',2,'Preferensi',NULL,'2022-01-25 14:09:51'),
('32','trans-service-general','file',2,'Pengajuan',NULL,'2022-01-25 14:08:41'),
('33','trans-service-general-approval','file',2,'Persetujuan',NULL,'2022-01-25 14:09:04'),
('4','#','folder',1,'Cetak',NULL,'2022-01-25 14:07:21'),
('41','print-service','file',2,'Cetak Data Bantuan',NULL,'2022-01-25 14:07:19'),
('42','print-service-general','file',2,'Cetak Data Surat Umum',NULL,'2022-02-08 10:12:24'),
('5','#','folder',1,'Notifikasi',NULL,'2022-01-25 14:07:08'),
('51','messages','file',2,'Pesan Notifikasi',NULL,'2022-01-25 14:07:06'),
('52','scan-qr','file',2,'Scan QR',NULL,'2022-01-25 14:07:06'),
('53','scan-qr/reload','file',2,'Reload Service',NULL,'2022-01-25 14:07:04'),
('6','dashboard-review','file',1,'Dashboard Review',NULL,'2022-01-25 14:07:02');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
