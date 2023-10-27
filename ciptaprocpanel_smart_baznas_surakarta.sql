-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 11 Okt 2023 pada 11.55
-- Versi server: 10.4.28-MariaDB
-- Versi PHP: 8.1.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ciptaprocpanel_smart_baznas_surakarta`
--

DELIMITER $$
--
-- Fungsi
--
CREATE DEFINER=`root`@`localhost` FUNCTION `getNewUserGroupID` () RETURNS INT(11)  BEGIN
	DECLARE prev_id INT;
	DECLARE next_id INT;
	SELECT user_group_id INTO prev_id FROM system_user_group ORDER BY user_group_id DESC LIMIT 0,1;
	IF prev_id IS NULL THEN
		SET prev_id = 0;
	END IF;
	SET next_id = prev_id + 1;
	RETURN next_id;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `getNewUserLogId` () RETURNS INT(11)  BEGIN
	DECLARE prev_id INT;
	DECLARE next_id INT;
	SELECT user_log_id INTO prev_id FROM system_log_user ORDER BY user_log_id DESC LIMIT 0,1;
	IF prev_id IS NULL THEN
		SET prev_id = 0;
	END IF;
	SET next_id = prev_id + 1;
	RETURN next_id;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `ci_sessions`
--

CREATE TABLE `ci_sessions` (
  `id` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `data` blob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `core_kecamatan`
--

CREATE TABLE `core_kecamatan` (
  `kecamatan_id` int(10) NOT NULL,
  `kecamatan_name` varchar(250) DEFAULT '',
  `kecamatan_token` varchar(250) DEFAULT '',
  `data_state` int(1) DEFAULT 0,
  `created_id` int(10) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_id` int(10) DEFAULT 0,
  `updated_at` datetime DEFAULT NULL,
  `deleted_id` int(10) DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data untuk tabel `core_kecamatan`
--

INSERT INTO `core_kecamatan` (`kecamatan_id`, `kecamatan_name`, `kecamatan_token`, `data_state`, `created_id`, `created_at`, `updated_id`, `updated_at`, `deleted_id`, `deleted_at`, `last_update`) VALUES
(1, 'Banjarsari', '2541a0119703d600eae05ba8beb2c801', 0, 55, NULL, 0, NULL, 0, NULL, '2023-05-06 03:58:49'),
(2, 'Jebres', '2541a0119703d600eae05ba8beb2c802', 0, 55, NULL, 0, NULL, 0, NULL, '2023-05-06 03:58:52'),
(3, 'Laweyan', '2541a0119703d600eae05ba8beb2c803', 0, 55, NULL, 0, NULL, 0, NULL, '2023-05-06 03:58:58'),
(4, 'Pasar Kliwon', '2541a0119703d600eae05ba8beb2c804', 0, 55, NULL, 0, NULL, 0, NULL, '2023-05-06 03:59:01'),
(5, 'Serengan', '2541a0119703d600eae05ba8beb2c805', 0, 55, NULL, 0, NULL, 0, NULL, '2023-05-06 03:59:06');

-- --------------------------------------------------------

--
-- Struktur dari tabel `core_kelurahan`
--

CREATE TABLE `core_kelurahan` (
  `kelurahan_id` int(10) NOT NULL,
  `kecamatan_id` int(10) DEFAULT NULL,
  `kelurahan_name` varchar(250) DEFAULT '',
  `kelurahan_token` varchar(250) DEFAULT '',
  `data_state` int(1) DEFAULT 0,
  `created_id` int(10) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_id` int(10) DEFAULT 0,
  `updated_at` datetime DEFAULT NULL,
  `deleted_id` int(10) DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data untuk tabel `core_kelurahan`
--

INSERT INTO `core_kelurahan` (`kelurahan_id`, `kecamatan_id`, `kelurahan_name`, `kelurahan_token`, `data_state`, `created_id`, `created_at`, `updated_id`, `updated_at`, `deleted_id`, `deleted_at`, `last_update`) VALUES
(1, 1, 'Timuran', '2541a0119703d600eae05ba8beb2c801', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-05-06 03:58:49'),
(2, 1, 'Kepabron', '2541a0119703d600eae05ba8beb2c802', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-05-06 03:58:52'),
(3, 1, 'Ketelan', '2541a0119703d600eae05ba8beb2c803', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-05-06 03:58:58'),
(4, 1, 'Punggawan', '2541a0119703d600eae05ba8beb2c804', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-05-06 03:59:01'),
(5, 1, 'Kestalan', '2541a0119703d600eae05ba8beb2c805', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-05-06 03:59:06'),
(6, 1, 'Setabelan', '2541a0119703d600eae05ba8beb2c806', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:01:05'),
(7, 1, 'Gilingan', '2541a0119703d600eae05ba8beb2c807', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:01:09'),
(8, 1, 'Nusukan', '2541a0119703d600eae05ba8beb2c808', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:01:11'),
(9, 1, 'Banjarsari', '2541a0119703d600eae05ba8beb2c809', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:01:15'),
(10, 1, 'Kadipiro', '2541a0119703d600eae05ba8beb2c810', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:01:23'),
(11, 1, 'Joglo', '2541a0119703d600eae05ba8beb2c811', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:01:26'),
(12, 1, 'Banyuanyar', '2541a0119703d600eae05ba8beb2c812', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:01:31'),
(13, 1, 'Sumber', '2541a0119703d600eae05ba8beb2c813', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:01:35'),
(14, 1, 'Manahan', '2541a0119703d600eae05ba8beb2c814', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:01:42'),
(15, 2, 'Sudiroprajan', '2541a0119703d600eae05ba8beb2c815', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:02:17'),
(16, 2, 'Gandekan', '2541a0119703d600eae05ba8beb2c816', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:02:19'),
(17, 2, 'Sewu', '2541a0119703d600eae05ba8beb2c817', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:02:22'),
(18, 2, 'Jagalan', '2541a0119703d600eae05ba8beb2c818', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:02:25'),
(19, 2, 'Pucang Sawit', '2541a0119703d600eae05ba8beb2c819', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:02:29'),
(20, 2, 'Jebres', '2541a0119703d600eae05ba8beb2c820', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:02:38'),
(21, 2, 'Mojosongo', '2541a0119703d600eae05ba8beb2c821', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:02:41'),
(22, 2, 'Tegalharjo', '2541a0119703d600eae05ba8beb2c822', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:02:45'),
(23, 2, 'Purwadiningratan', '2541a0119703d600eae05ba8beb2c823', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:02:53'),
(24, 2, 'Kepatihan Wetan', '2541a0119703d600eae05ba8beb2c824', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:03:01'),
(25, 2, 'Kepatihan Kulon', '2541a0119703d600eae05ba8beb2c825', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:03:05'),
(26, 3, 'Sriwedari', '2541a0119703d600eae05ba8beb2c826', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:03:16'),
(27, 3, 'Penumping', '2541a0119703d600eae05ba8beb2c827', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:03:32'),
(28, 3, 'Purwosari', '2541a0119703d600eae05ba8beb2c828', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:03:36'),
(29, 3, 'Kerten', '2541a0119703d600eae05ba8beb2c829', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:03:43'),
(30, 3, 'Jajar', '2541a0119703d600eae05ba8beb2c830', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:03:48'),
(31, 3, 'Karangasem', '2541a0119703d600eae05ba8beb2c831', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:03:52'),
(32, 3, 'Pajang', '2541a0119703d600eae05ba8beb2c832', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:03:56'),
(33, 3, 'Sondakan', '2541a0119703d600eae05ba8beb2c833', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:03:57'),
(34, 3, 'Laweyan', '2541a0119703d600eae05ba8beb2c834', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:04:02'),
(35, 3, 'Bumi', '2541a0119703d600eae05ba8beb2c835', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:04:05'),
(36, 3, 'Penularan', '2541a0119703d600eae05ba8beb2c836', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:04:10'),
(37, 4, 'Kampung Baru', '2541a0119703d600eae05ba8beb2c837', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:04:15'),
(38, 4, 'Kauman', '2541a0119703d600eae05ba8beb2c838', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:04:38'),
(39, 4, 'Kedung Lumbu', '2541a0119703d600eae05ba8beb2c839', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:04:44'),
(40, 4, 'Baluwati', '2541a0119703d600eae05ba8beb2c840', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:04:47'),
(41, 4, 'Gajahan', '2541a0119703d600eae05ba8beb2c841', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:04:50'),
(42, 4, 'Joyosuran', '2541a0119703d600eae05ba8beb2c842', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:04:58'),
(43, 4, 'Semanggi', '2541a0119703d600eae05ba8beb2c843', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:05:03'),
(44, 4, 'Mojo', '2541a0119703d600eae05ba8beb2c844', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:05:07'),
(45, 4, 'Pasar Kliwon', '2541a0119703d600eae05ba8beb2c845', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:05:11'),
(46, 4, 'Sangkrah', '2541a0119703d600eae05ba8beb2c846', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:05:23'),
(47, 5, 'Kemlayan', '2541a0119703d600eae05ba8beb2c847', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:05:29'),
(48, 5, 'Jayengan', '2541a0119703d600eae05ba8beb2c848', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:05:45'),
(49, 5, 'Kratonan', '2541a0119703d600eae05ba8beb2c849', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:05:50'),
(50, 5, 'Tipes', '2541a0119703d600eae05ba8beb2c850', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:05:53'),
(51, 5, 'Serengan', '2541a0119703d600eae05ba8beb2c851', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:05:59'),
(52, 5, 'Danukusuman', '2541a0119703d600eae05ba8beb2c852', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:06:12'),
(53, 5, 'Joyotakan', '2541a0119703d600eae05ba8beb2c853', 0, 55, '2023-09-27 14:10:29', 0, NULL, 0, NULL, '2023-09-27 07:06:15');

-- --------------------------------------------------------

--
-- Struktur dari tabel `core_messages`
--

CREATE TABLE `core_messages` (
  `messages_id` int(10) NOT NULL,
  `messages_name` varchar(250) DEFAULT NULL,
  `messages_text` text DEFAULT NULL,
  `messages_status` int(1) DEFAULT 1,
  `messages_token` varchar(250) DEFAULT '',
  `data_state` int(1) DEFAULT 0,
  `created_id` int(10) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_id` int(10) DEFAULT 0,
  `updated_at` datetime DEFAULT NULL,
  `deleted_id` int(10) DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data untuk tabel `core_messages`
--

INSERT INTO `core_messages` (`messages_id`, `messages_name`, `messages_text`, `messages_status`, `messages_token`, `data_state`, `created_id`, `created_at`, `updated_id`, `updated_at`, `deleted_id`, `deleted_at`, `last_update`) VALUES
(1, 'Pengajuan', 'Pengajuan Masuk', 1, '', 0, 0, NULL, 55, '2022-01-07 07:03:08', 0, NULL, '2022-01-05 07:50:51'),
(2, 'Disposisi', 'Diteruskan ke Bagian Terkait', 1, '', 0, 0, NULL, 55, '2022-04-06 06:13:02', 0, NULL, '2022-01-05 07:50:57'),
(3, 'Persetujuan Disposisi', 'Sudah di Review Bagian', 1, '', 0, 0, NULL, 55, '2022-04-06 01:58:58', 0, NULL, '2022-01-05 07:51:03'),
(4, 'Review', 'Sudah Disetujui, Mohon Tunggu Pemberitahuan Berikutnya', 1, '', 0, 0, NULL, 55, '2022-01-05 07:59:23', 0, NULL, '2022-01-05 07:51:06'),
(5, 'Butuh Dokumen Tambahan', 'Butuh Dokumen Tambahan', 1, '', 0, 0, NULL, 55, '2022-01-05 08:25:31', 0, NULL, '2022-01-05 08:20:15'),
(6, 'Pembatalan Persetujuan Disposisi', 'Diproses Ulang Oleh Bagian', 1, '', 0, 0, NULL, 55, '2022-04-05 01:59:40', 0, NULL, '2022-01-05 08:20:33'),
(7, 'Pembatalan Review', 'Pembatalan Persetujuan', 1, '', 0, 0, NULL, 55, '2022-01-05 08:26:16', 0, NULL, '2022-01-05 08:20:48'),
(8, 'Penolakan Review', 'Tidak Disetujui', 1, '', 0, 0, NULL, 55, '2022-02-22 04:01:17', 0, NULL, '2022-02-07 09:08:14'),
(9, 'Penolakan Bagian Disposisi', 'Ditolak oleh Bagian', 1, '', 0, 0, NULL, 55, '2022-04-05 02:00:25', 0, NULL, '2022-02-28 12:08:35'),
(10, 'Pencairan Dana Bantuan', 'Dana Bantuan telah Tersedia', 1, '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-03-19 04:13:44'),
(11, 'Pemberian Dana Bantuan', 'Dana Bantuan telah Diberikan', 1, '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-03-19 04:13:44');

-- --------------------------------------------------------

--
-- Struktur dari tabel `core_section`
--

CREATE TABLE `core_section` (
  `section_id` int(10) NOT NULL,
  `section_name` varchar(250) DEFAULT '',
  `section_token` varchar(250) DEFAULT '',
  `data_state` int(1) DEFAULT 0,
  `created_id` int(10) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_id` int(10) DEFAULT 0,
  `updated_at` datetime DEFAULT NULL,
  `deleted_id` int(10) DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data untuk tabel `core_section`
--

INSERT INTO `core_section` (`section_id`, `section_name`, `section_token`, `data_state`, `created_id`, `created_at`, `updated_id`, `updated_at`, `deleted_id`, `deleted_at`, `last_update`) VALUES
(1, 'Bagian SDM, Umum, Administrasi', '35486a916fb819657179678385878434', 0, 55, '2021-10-28 09:34:15', 55, '2021-10-30 03:10:27', 0, NULL, '2021-10-28 09:34:15'),
(2, 'Bagian Penyuluhan', 'd1b3e96d3c945d24177052ac41a48eff', 1, 55, '2021-10-28 09:34:33', 55, '2022-01-13 07:09:13', 55, '2022-01-13 07:09:13', '2021-10-28 09:34:33'),
(8, 'Bagian Tes', 'd8c5bb6f76b6f508b42f431397f1731b', 1, 55, '2022-01-12 07:48:38', 0, '2022-01-12 07:48:42', 55, '2022-01-12 07:48:42', '2022-01-12 07:48:38'),
(9, 'Bagian Pendistribusian dan Pendayagunaan', '9224a3c764b4b290579978edd3893977', 0, 55, '2022-01-13 06:51:18', 0, '2022-01-13 06:51:18', 0, NULL, '2022-01-13 06:51:18'),
(10, 'Bagian Pengumpulan', '9cd79ce0e14ab07fb58fbdc081816a49', 0, 55, '2022-01-13 06:51:30', 0, '2022-01-13 06:51:30', 0, NULL, '2022-01-13 06:51:30'),
(11, 'Bagian Keuangan dan Pelaporan', '289478003da4f071994e05d7a6b5c4f3', 0, 55, '2022-01-13 06:51:44', 0, '2022-01-13 06:51:44', 0, NULL, '2022-01-13 06:51:44'),
(12, 'tes bagian 2', '9c7041ead31033a1d1c12cc382288e50', 1, 55, '2022-02-22 02:39:28', 55, '2022-02-22 02:39:45', 55, '2022-02-22 02:39:45', '2022-02-22 02:39:28');

-- --------------------------------------------------------

--
-- Struktur dari tabel `core_service`
--

CREATE TABLE `core_service` (
  `service_id` int(10) NOT NULL,
  `service_name` varchar(250) DEFAULT '',
  `service_token` varchar(250) DEFAULT '',
  `service_token_edit` varchar(250) DEFAULT '',
  `data_state` int(1) DEFAULT 0,
  `created_id` int(10) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_id` int(10) DEFAULT 0,
  `updated_at` datetime DEFAULT NULL,
  `deleted_id` int(10) DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data untuk tabel `core_service`
--

INSERT INTO `core_service` (`service_id`, `service_name`, `service_token`, `service_token_edit`, `data_state`, `created_id`, `created_at`, `updated_id`, `updated_at`, `deleted_id`, `deleted_at`, `last_update`) VALUES
(1, 'Pendidikan', '724bec3f910ea4b61cc0e268a693a976', '01be0f41b79943108589847b19e32c6c', 0, 55, '2021-11-26 04:15:10', 55, '2023-09-27 08:25:13', 0, NULL, '2021-11-26 04:15:10'),
(2, 'Kesehatan', 'c252d64a7b24c46ce3e24005d9d8b4de', 'bedae8d7bb3156d14b2fc46eafcd3c85', 0, 55, '2021-11-26 04:19:08', 55, '2023-09-27 08:29:27', 0, '2023-09-26 04:16:11', '2021-11-26 04:19:08'),
(3, 'Modal Usaha', '38d0a223ec418ad4e0819d95d86e0dae', 'f415e89eb27bf135efc057b77217aac6', 0, 55, '2022-01-13 06:40:51', 55, '2023-09-27 08:32:46', 0, NULL, '2022-01-13 06:40:51'),
(4, 'Alat Usaha', 'd8872efe7c0f283207785cf9a48cdc96', '22f76ac5f9abbe9e002b936a495483c4', 0, 55, '2023-09-27 08:02:06', 55, '2023-09-27 08:35:09', 0, NULL, '2023-09-27 08:02:06'),
(5, 'Kemanusiaan', '4f3d1a77c64f1ae62dcf4302b0898152', '2216bc215fb5f3ac4cf74e0e703a7bf8', 0, 55, '2022-01-28 05:50:00', 55, '2023-09-27 08:34:11', 0, NULL, '2022-01-28 05:50:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `core_service_general_parameter`
--

CREATE TABLE `core_service_general_parameter` (
  `service_general_parameter_id` int(10) NOT NULL,
  `service_general_parameter_no` int(10) DEFAULT 0,
  `service_general_parameter_name` varchar(250) DEFAULT '',
  `service_general_parameter_token` varchar(250) DEFAULT '',
  `service_general_parameter_token_edit` varchar(250) DEFAULT '',
  `data_state` int(1) DEFAULT 0,
  `created_id` int(10) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_id` int(10) DEFAULT 0,
  `updated_at` datetime DEFAULT NULL,
  `deleted_id` int(10) DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data untuk tabel `core_service_general_parameter`
--

INSERT INTO `core_service_general_parameter` (`service_general_parameter_id`, `service_general_parameter_no`, `service_general_parameter_name`, `service_general_parameter_token`, `service_general_parameter_token_edit`, `data_state`, `created_id`, `created_at`, `updated_id`, `updated_at`, `deleted_id`, `deleted_at`, `last_update`) VALUES
(1, 1, 'Nomor Surat', '2541a0119703d600eae05ba8beb2c8af', '59d898527ede1e79c37d9d65f4cba2bb', 0, 55, '2022-01-22 02:44:25', 55, '2022-01-22 03:02:27', 0, NULL, '2022-01-22 02:44:25'),
(3, 2, 'Tanggal Surat', '50fa48c1cd75a18ee35c3e4fb146ed1f', '', 0, 55, '2022-01-22 03:04:12', 0, '2022-01-22 03:04:12', 0, NULL, '2022-01-22 03:04:12'),
(4, 3, 'Keperluan', '62d5b2f0b5523f119142ec60d4b24f4d', '', 0, 55, '2022-01-22 03:04:21', 0, '2022-01-22 03:04:21', 0, NULL, '2022-01-22 03:04:21'),
(5, 4, 'Perihal', 'ab1f1fe68724a8b2abd1eebcb950a12a', '', 0, 55, '2022-01-22 03:04:26', 0, '2022-01-22 03:04:26', 0, NULL, '2022-01-22 03:04:26'),
(6, 64, 'testes', '70f80d28c885100c4fc5a1b0d726fa51', '740c827e172cb32c2130bbead36f2e4a', 1, 55, '2022-02-22 03:53:55', 55, '2022-02-22 03:54:05', 55, '2022-02-22 03:54:05', '2022-02-22 03:53:55');

-- --------------------------------------------------------

--
-- Struktur dari tabel `core_service_general_priority`
--

CREATE TABLE `core_service_general_priority` (
  `service_general_priority_id` int(10) NOT NULL,
  `service_general_priority_scale` int(10) DEFAULT 0,
  `service_general_priority_name` varchar(250) DEFAULT '',
  `service_general_priority_token` varchar(250) DEFAULT '',
  `service_general_priority_token_edit` varchar(250) DEFAULT '',
  `data_state` int(1) DEFAULT 0,
  `created_id` int(10) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_id` int(10) DEFAULT 0,
  `updated_at` datetime DEFAULT NULL,
  `deleted_id` int(10) DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data untuk tabel `core_service_general_priority`
--

INSERT INTO `core_service_general_priority` (`service_general_priority_id`, `service_general_priority_scale`, `service_general_priority_name`, `service_general_priority_token`, `service_general_priority_token_edit`, `data_state`, `created_id`, `created_at`, `updated_id`, `updated_at`, `deleted_id`, `deleted_at`, `last_update`) VALUES
(1, 1, 'Biasa', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-01-22 03:07:21'),
(2, 2, 'Penting', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-01-22 03:07:26'),
(3, 3, 'Sangat Penting', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-01-22 03:07:30');

-- --------------------------------------------------------

--
-- Struktur dari tabel `core_service_parameter`
--

CREATE TABLE `core_service_parameter` (
  `service_parameter_id` int(10) NOT NULL,
  `service_id` int(10) DEFAULT 0,
  `service_parameter_no` int(10) DEFAULT 0,
  `service_parameter_description` varchar(250) DEFAULT '',
  `service_parameter_token` varchar(250) DEFAULT '',
  `service_parameter_token_edit` varchar(250) DEFAULT '',
  `data_state` int(1) DEFAULT 0,
  `created_id` int(10) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_id` int(10) DEFAULT 0,
  `updated_at` datetime DEFAULT NULL,
  `deleted_id` int(10) DEFAULT 0,
  `deleted_on` datetime DEFAULT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data untuk tabel `core_service_parameter`
--

INSERT INTO `core_service_parameter` (`service_parameter_id`, `service_id`, `service_parameter_no`, `service_parameter_description`, `service_parameter_token`, `service_parameter_token_edit`, `data_state`, `created_id`, `created_at`, `updated_id`, `updated_at`, `deleted_id`, `deleted_on`, `last_update`) VALUES
(3, 1, 1, 'Nama Responden', 'dbdc2d3539c4445336b8766677c9ba8e20211207070600', '', 0, 0, '2021-12-07 14:12:36', 0, '2021-12-07 14:12:36', 0, NULL, '2021-12-07 07:12:36'),
(25, 3, 1, 'Nama Responden', '38d0a223ec418ad4e0819d95d86e0dae20220113064043', '', 0, 0, '2022-01-13 13:40:51', 0, '2022-03-28 14:47:29', 0, NULL, '2022-01-13 06:40:51'),
(26, 5, 1, 'Nama Responden', '4f3d1a77c64f1ae62dcf4302b089815220220128053844', '', 0, 0, '2022-01-28 12:50:00', 0, '2022-01-28 12:50:00', 0, NULL, '2022-01-28 05:50:00'),
(81, 2, 1, 'Nama Responden', '7c074057d541b11037ecde58bb7dafca20220328082225', '', 0, 0, '2022-03-28 15:23:12', 0, '2022-03-28 15:23:12', 0, NULL, '2022-03-28 08:23:12'),
(98, 4, 1, 'Nama Responden', 'd8872efe7c0f283207785cf9a48cdc9620230927080203', '', 0, 0, '2023-09-27 15:02:06', 0, '2023-09-27 15:02:06', 0, NULL, '2023-09-27 08:02:06'),
(99, 1, 2, 'Tempat, Tanggal Lahir', 'e0c7637f3cd4da0118097761970ba80420230927082350', '', 0, 0, '2023-09-27 15:24:28', 0, '2023-09-27 15:24:28', 0, NULL, '2023-09-27 08:24:28'),
(100, 1, 3, 'Agama', 'e0c7637f3cd4da0118097761970ba80420230927082406', '', 0, 0, '2023-09-27 15:24:28', 0, '2023-09-27 15:24:28', 0, NULL, '2023-09-27 08:24:28'),
(101, 1, 4, 'Pekerjaan', 'e0c7637f3cd4da0118097761970ba80420230927082414', '', 0, 0, '2023-09-27 15:24:28', 0, '2023-09-27 15:24:28', 0, NULL, '2023-09-27 08:24:28'),
(102, 1, 5, 'Alamat', 'e0c7637f3cd4da0118097761970ba80420230927082423', '', 0, 0, '2023-09-27 15:24:28', 0, '2023-09-27 15:24:28', 0, NULL, '2023-09-27 08:24:28'),
(103, 1, 6, 'NIK', '01be0f41b79943108589847b19e32c6c20230927082441', '', 0, 0, '2023-09-27 15:25:13', 0, '2023-09-27 15:25:13', 0, NULL, '2023-09-27 08:25:13'),
(104, 1, 7, 'Tlp / HP', '01be0f41b79943108589847b19e32c6c20230927082456', '', 0, 0, '2023-09-27 15:25:13', 0, '2023-09-27 15:25:13', 0, NULL, '2023-09-27 08:25:13'),
(105, 1, 8, 'Masjid Terdekat', '01be0f41b79943108589847b19e32c6c20230927082504', '', 0, 0, '2023-09-27 15:25:13', 0, '2023-09-27 15:25:13', 0, NULL, '2023-09-27 08:25:13'),
(106, 2, 2, 'Tempat, Tanggal Lahir', 'f08c1bacf6cd57097d84631a302043d920230927082806', '', 0, 0, '2023-09-27 15:28:34', 0, '2023-09-27 15:28:34', 0, NULL, '2023-09-27 08:28:34'),
(107, 2, 3, 'Agama', 'f08c1bacf6cd57097d84631a302043d920230927082811', '', 0, 0, '2023-09-27 15:28:34', 0, '2023-09-27 15:28:34', 0, NULL, '2023-09-27 08:28:34'),
(108, 2, 4, 'Pekerjaan', 'f08c1bacf6cd57097d84631a302043d920230927082817', '', 0, 0, '2023-09-27 15:28:34', 0, '2023-09-27 15:28:34', 0, NULL, '2023-09-27 08:28:34'),
(109, 2, 5, 'Alamat', 'f08c1bacf6cd57097d84631a302043d920230927082826', '', 0, 0, '2023-09-27 15:28:34', 0, '2023-09-27 15:28:34', 0, NULL, '2023-09-27 08:28:34'),
(110, 2, 6, 'NIK', 'bedae8d7bb3156d14b2fc46eafcd3c8520230927082901', '', 0, 0, '2023-09-27 15:29:27', 0, '2023-09-27 15:29:27', 0, NULL, '2023-09-27 08:29:27'),
(111, 2, 7, 'Tlp / HP', 'bedae8d7bb3156d14b2fc46eafcd3c8520230927082914', '', 0, 0, '2023-09-27 15:29:27', 0, '2023-09-27 15:29:27', 0, NULL, '2023-09-27 08:29:27'),
(112, 2, 8, 'Masjid Terdekat', 'bedae8d7bb3156d14b2fc46eafcd3c8520230927082924', '', 0, 0, '2023-09-27 15:29:27', 0, '2023-09-27 15:29:27', 0, NULL, '2023-09-27 08:29:27'),
(113, 3, 2, 'Tempat, Tanggal Lahir', 'f415e89eb27bf135efc057b77217aac620230927083202', '', 0, 0, '2023-09-27 15:32:46', 0, '2023-09-27 15:32:46', 0, NULL, '2023-09-27 08:32:46'),
(114, 3, 3, 'Agama', 'f415e89eb27bf135efc057b77217aac620230927083208', '', 0, 0, '2023-09-27 15:32:46', 0, '2023-09-27 15:32:46', 0, NULL, '2023-09-27 08:32:46'),
(115, 3, 4, 'Pekerjaan', 'f415e89eb27bf135efc057b77217aac620230927083214', '', 0, 0, '2023-09-27 15:32:46', 0, '2023-09-27 15:32:46', 0, NULL, '2023-09-27 08:32:46'),
(116, 3, 5, 'Alamat', 'f415e89eb27bf135efc057b77217aac620230927083219', '', 0, 0, '2023-09-27 15:32:46', 0, '2023-09-27 15:32:46', 0, NULL, '2023-09-27 08:32:46'),
(117, 3, 6, 'NIK', 'f415e89eb27bf135efc057b77217aac620230927083225', '', 0, 0, '2023-09-27 15:32:46', 0, '2023-09-27 15:32:46', 0, NULL, '2023-09-27 08:32:46'),
(118, 3, 7, 'Tlp / HP', 'f415e89eb27bf135efc057b77217aac620230927083232', '', 0, 0, '2023-09-27 15:32:46', 0, '2023-09-27 15:32:46', 0, NULL, '2023-09-27 08:32:46'),
(119, 3, 8, 'Masjid Terdekat', 'f415e89eb27bf135efc057b77217aac620230927083243', '', 0, 0, '2023-09-27 15:32:46', 0, '2023-09-27 15:32:46', 0, NULL, '2023-09-27 08:32:46'),
(120, 5, 2, 'Tempat, Tanggal Lahir', '2216bc215fb5f3ac4cf74e0e703a7bf820230927083308', '', 0, 0, '2023-09-27 15:34:11', 0, '2023-09-27 15:34:11', 0, NULL, '2023-09-27 08:34:11'),
(121, 5, 3, 'Agama', '2216bc215fb5f3ac4cf74e0e703a7bf820230927083315', '', 0, 0, '2023-09-27 15:34:11', 0, '2023-09-27 15:34:11', 0, NULL, '2023-09-27 08:34:11'),
(122, 5, 4, 'Pekerjaan', '2216bc215fb5f3ac4cf74e0e703a7bf820230927083321', '', 0, 0, '2023-09-27 15:34:11', 0, '2023-09-27 15:34:11', 0, NULL, '2023-09-27 08:34:11'),
(123, 5, 5, 'Alamat', '2216bc215fb5f3ac4cf74e0e703a7bf820230927083336', '', 0, 0, '2023-09-27 15:34:11', 0, '2023-09-27 15:34:11', 0, NULL, '2023-09-27 08:34:11'),
(124, 5, 6, 'NIK', '2216bc215fb5f3ac4cf74e0e703a7bf820230927083342', '', 0, 0, '2023-09-27 15:34:11', 0, '2023-09-27 15:34:11', 0, NULL, '2023-09-27 08:34:11'),
(125, 5, 7, 'Tlp / HP', '2216bc215fb5f3ac4cf74e0e703a7bf820230927083351', '', 0, 0, '2023-09-27 15:34:11', 0, '2023-09-27 15:34:11', 0, NULL, '2023-09-27 08:34:11'),
(126, 5, 8, 'Masjid Terdekat', '2216bc215fb5f3ac4cf74e0e703a7bf820230927083357', '', 0, 0, '2023-09-27 15:34:11', 0, '2023-09-27 15:34:11', 0, NULL, '2023-09-27 08:34:11'),
(127, 4, 2, 'Tempat, Tanggal Lahir', '22f76ac5f9abbe9e002b936a495483c420230927083427', '', 0, 0, '2023-09-27 15:35:09', 0, '2023-09-27 15:35:09', 0, NULL, '2023-09-27 08:35:09'),
(128, 4, 3, 'Agama', '22f76ac5f9abbe9e002b936a495483c420230927083431', '', 0, 0, '2023-09-27 15:35:09', 0, '2023-09-27 15:35:09', 0, NULL, '2023-09-27 08:35:09'),
(129, 4, 4, 'Pekerjaan', '22f76ac5f9abbe9e002b936a495483c420230927083439', '', 0, 0, '2023-09-27 15:35:09', 0, '2023-09-27 15:35:09', 0, NULL, '2023-09-27 08:35:09'),
(130, 4, 5, 'Alamat', '22f76ac5f9abbe9e002b936a495483c420230927083444', '', 0, 0, '2023-09-27 15:35:09', 0, '2023-09-27 15:35:09', 0, NULL, '2023-09-27 08:35:09'),
(131, 4, 6, 'NIK', '22f76ac5f9abbe9e002b936a495483c420230927083450', '', 0, 0, '2023-09-27 15:35:09', 0, '2023-09-27 15:35:09', 0, NULL, '2023-09-27 08:35:09'),
(132, 4, 7, 'Tlp / HP', '22f76ac5f9abbe9e002b936a495483c420230927083457', '', 0, 0, '2023-09-27 15:35:09', 0, '2023-09-27 15:35:09', 0, NULL, '2023-09-27 08:35:09'),
(133, 4, 8, 'Masjid Terdekat', '22f76ac5f9abbe9e002b936a495483c420230927083505', '', 0, 0, '2023-09-27 15:35:09', 0, '2023-09-27 15:35:09', 0, NULL, '2023-09-27 08:35:09');

-- --------------------------------------------------------

--
-- Struktur dari tabel `core_service_status`
--

CREATE TABLE `core_service_status` (
  `service_status_id` int(10) NOT NULL,
  `service_status_name` varchar(250) DEFAULT NULL,
  `service_status_text` text DEFAULT NULL,
  `service_status_status` int(1) DEFAULT 1,
  `service_status_token` varchar(250) DEFAULT '',
  `data_state` int(1) DEFAULT 0,
  `created_id` int(10) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_id` int(10) DEFAULT 0,
  `updated_at` datetime DEFAULT NULL,
  `deleted_id` int(10) DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data untuk tabel `core_service_status`
--

INSERT INTO `core_service_status` (`service_status_id`, `service_status_name`, `service_status_text`, `service_status_status`, `service_status_token`, `data_state`, `created_id`, `created_at`, `updated_id`, `updated_at`, `deleted_id`, `deleted_at`, `last_update`) VALUES
(1, 'Pengajuan', 'Pengajuan Masuk', 1, '', 0, 0, NULL, 55, '2022-01-07 07:03:08', 0, NULL, '2022-01-05 07:50:51'),
(2, 'Disposisi', 'Diteruskan ke Bagian Untuk di Review', 1, '', 0, 0, NULL, 55, '2022-01-05 07:56:49', 0, NULL, '2022-01-05 07:50:57'),
(3, 'Persetujuan Disposisi', 'Sudah di Review Bagian', 1, '', 0, 0, NULL, 55, '2022-01-05 07:59:39', 0, NULL, '2022-01-05 07:51:03'),
(4, 'Sudah di Review', 'Sudah Disetujui', 1, '', 0, 0, NULL, 55, '2022-01-05 07:59:23', 0, NULL, '2022-01-05 07:51:06'),
(5, 'Butuh Dokumen Tambahan', 'Butuh Dokumen Tambahan', 1, '', 0, 0, NULL, 55, '2022-01-05 08:25:31', 0, NULL, '2022-01-05 08:20:15'),
(6, 'Pembatalan Persetujuan Disposisi', 'Diproses Ulang Oleh Bagian Disposisi', 1, '', 0, 0, NULL, 55, '2022-01-05 08:26:55', 0, NULL, '2022-01-05 08:20:33'),
(7, 'Pembatalan Review', 'Pembatalan Persetujuan', 1, '', 0, 0, NULL, 55, '2022-01-05 08:26:16', 0, NULL, '2022-01-05 08:20:48'),
(8, 'Upload Dokumen Tambahan', 'Upload Dokumen Tambahan', 1, '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-01-12 09:27:03'),
(9, 'Ditolak oleh Reviewer', 'Tidak Disetujui', 1, '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-02-28 12:10:00'),
(10, 'Ditolak oleh Bagian Disposisi', 'Ditolak oleh Bagian', 1, '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-02-28 12:10:00'),
(11, 'Dana Bantuan Telah Tersedia', 'Dana Bantuan Telah Tersedia', 1, '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-03-19 04:14:26'),
(12, 'Dana Bantuan Telah Diberikan', 'Dana Bantuan Telah Diberikan', 1, '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-03-19 04:14:26');

-- --------------------------------------------------------

--
-- Struktur dari tabel `core_service_term`
--

CREATE TABLE `core_service_term` (
  `service_term_id` int(10) NOT NULL,
  `service_id` int(10) DEFAULT 0,
  `service_term_no` int(10) DEFAULT 0,
  `service_term_description` varchar(250) DEFAULT '',
  `service_term_token` varchar(250) DEFAULT '',
  `service_term_token_edit` varchar(250) DEFAULT '',
  `data_state` int(1) DEFAULT 0,
  `created_id` int(10) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_id` int(10) DEFAULT 0,
  `updated_at` datetime DEFAULT NULL,
  `deleted_id` int(10) DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data untuk tabel `core_service_term`
--

INSERT INTO `core_service_term` (`service_term_id`, `service_id`, `service_term_no`, `service_term_description`, `service_term_token`, `service_term_token_edit`, `data_state`, `created_id`, `created_at`, `updated_id`, `updated_at`, `deleted_id`, `deleted_at`, `last_update`) VALUES
(1, 1, 1, 'FC KTP', '724bec3f910ea4b61cc0e268a693a97620211126041349', '', 0, 0, '2021-11-26 11:15:10', 0, '2021-11-26 11:15:10', 0, NULL, '2021-11-26 04:15:10'),
(6, 2, 1, 'FC KTP', 'c252d64a7b24c46ce3e24005d9d8b4de20211126041711', '', 0, 0, '2021-11-26 11:19:08', 0, '2021-11-26 11:19:08', 0, NULL, '2021-11-26 04:19:08'),
(25, 3, 1, 'FC KTP', '38d0a223ec418ad4e0819d95d86e0dae20220113064021', '', 0, 0, '2022-01-13 13:40:51', 0, '2022-01-13 13:40:51', 0, NULL, '2022-01-13 06:40:51'),
(27, 5, 1, 'FC KTP', '4f3d1a77c64f1ae62dcf4302b089815220220128051312', '', 0, 0, '2022-01-28 12:50:00', 0, '2022-01-28 12:50:00', 0, NULL, '2022-01-28 05:50:00'),
(72, 4, 1, 'FC KTP', 'd8872efe7c0f283207785cf9a48cdc9620230927080154', '', 0, 0, '2023-09-27 15:02:06', 0, '2023-09-27 15:02:06', 0, NULL, '2023-09-27 08:02:06');

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `mustahik_worksheet`
--

CREATE TABLE `mustahik_worksheet` (
  `worksheet_id` int(10) NOT NULL,
  `service_id` varchar(250) DEFAULT '',
  `worksheet_no` int(10) DEFAULT NULL,
  `worksheet_name` varchar(250) DEFAULT '',
  `worksheet_type` int(10) DEFAULT NULL COMMENT '1 = isian, 2 = checkbox, 3 = checklist',
  `worksheet_code` varchar(250) DEFAULT '',
  `worksheet_token` varchar(250) DEFAULT '',
  `worksheet_token_edit` varchar(250) DEFAULT '',
  `data_state` int(1) DEFAULT 0,
  `created_id` int(10) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_id` int(10) DEFAULT 0,
  `updated_at` datetime DEFAULT NULL,
  `deleted_id` int(10) DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data untuk tabel `mustahik_worksheet`
--

INSERT INTO `mustahik_worksheet` (`worksheet_id`, `service_id`, `worksheet_no`, `worksheet_name`, `worksheet_type`, `worksheet_code`, `worksheet_token`, `worksheet_token_edit`, `data_state`, `created_id`, `created_at`, `updated_id`, `updated_at`, `deleted_id`, `deleted_at`, `last_update`) VALUES
(1, '7', 1, 'Nama Pengaju', 1, 'service_requisition_name', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-09 09:31:39'),
(2, '7', 2, 'Nomor Telepon/HP', 1, 'service_requisition_phone', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-09 09:32:29'),
(3, '7', 3, 'Alamat', 1, 'service_requisition_address', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-09 09:34:41'),
(4, '7', 4, 'Jumlah Jiwa', 1, 'worksheet_occupant', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-09 09:51:45'),
(5, '7', 5, 'Kode Ashnaf', 2, 'worksheet_ashnaf', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-09 09:51:59'),
(6, '7', 6, 'Surat Permohonan', 3, 'worksheet_application_letter', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-09 09:35:56'),
(7, '7', 7, 'SKTM / FC Kartu Saraswati', 3, 'worksheet_sktm', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-09 09:36:32'),
(8, '7', 8, 'Fotocopy KTP Pemohon', 3, 'worksheet_ktp', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-09 09:36:08'),
(9, '7', 9, 'Fotocopy KK Pemohon', 3, 'worksheet_kk', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-09 09:36:19'),
(10, '7', 10, 'Dkmn Kpmlkan Rumah / FC SHM', 3, 'worksheet_ownership_document', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-09 09:36:42'),
(11, '7', 11, 'Surat Perjanjian Menempati Lahan', 3, 'worksheet_occupancy_agreement', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-09 09:36:55'),
(12, '7', 12, 'Fotocopy KTP Pemilik Lahan', 3, 'worksheet_land_owner', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-09-01 07:31:50'),
(14, '7', 14, 'Ukuran Rumah (Jenis Bangunan)', 1, 'worksheet_home_size_type', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-09 09:38:31'),
(15, '7', 15, 'Ukuran Rumah (Kondisi Bangunan)', 1, 'worksheet_home_size_condition', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-12 02:44:24'),
(16, '7', 16, 'Dinding Rumah', 2, 'worksheet_wall', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-09 09:38:35'),
(17, '7', 17, 'Lantai', 2, 'worksheet_floor', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-09 09:38:39'),
(18, '7', 18, 'Atap', 2, 'worksheet_roof', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-09 09:38:41'),
(19, '7', 19, 'Sanitasi', 2, 'worksheet_sanitation', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-09 09:38:43'),
(20, '7', 20, 'Listrik', 2, 'worksheet_electricity', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-09 09:47:26'),
(21, '7', 21, 'Kepemilikan Rumah', 2, 'worksheet_ownership', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-09 09:47:52'),
(22, '7', 22, 'Catatan Ukuran Rumah', 1, 'worksheet_home_size_remark', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-12 02:51:21'),
(23, '7', 23, 'Catatan Dinding Rumah', 1, 'worksheet_wall_remark', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-12 02:51:26'),
(24, '7', 24, 'Catatan Lantai', 1, 'worksheet_floor_remark', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-12 02:51:31'),
(25, '7', 25, 'Catatan Atap', 1, 'worksheet_roof_remark', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-12 02:51:35'),
(26, '7', 26, 'Catatan Sanitasi', 1, 'worksheet_sanitation_remark', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-12 02:51:38'),
(27, '7', 27, 'Catatan Listrik', 1, 'worksheet_electricity_remark', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-12 02:51:44'),
(28, '7', 28, 'Catatan Kepemilikan Rumah', 1, 'worksheet_ownership_remark', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-12 02:51:53'),
(29, '7', 29, 'Usaha Suami / Bulan', 1, 'worksheet_husband_business_monthly', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-09 09:48:08'),
(30, '7', 30, 'Usaha Suami / Tahun', 1, 'worksheet_husband_business_yearly', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-09 09:48:14'),
(31, '7', 31, 'Usaha Istri / Bulan', 1, 'worksheet_wife_business_monthly', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-09 09:48:20'),
(32, '7', 32, 'Usaha Istri / Tahun', 1, 'worksheet_wife_business_yearly', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-09 09:48:26'),
(33, '7', 33, 'Dari Orang Tua / Bulan', 1, 'worksheet_parents_monthly', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-09 09:49:21'),
(34, '7', 34, 'Dari Orang Tua / Tahun', 1, 'worksheet_parents_yearly', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-09 09:50:20'),
(35, '7', 35, 'Dari Anak atau Menantu / Bulan', 1, 'worksheet_childs_monthly', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-10 08:52:32'),
(36, '7', 36, 'Dari Anak atau Menantu / Tahun', 1, 'worksheet_childs_yearly', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-10 08:52:40'),
(37, '7', 37, 'Penghasilan Lainnya / Bulan', 1, 'worksheet_other_monthly', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-10 08:52:49'),
(38, '7', 38, 'Penghasilan Lainnya / Tahun', 1, 'worksheet_other_yearly', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-10 08:53:02'),
(41, '7', 41, 'Responden', 1, 'worksheet_respondent', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-10 08:53:53'),
(42, '7', 42, 'Foto Rumah Atas Nama', 1, 'worksheet_photos_name', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-12 03:31:04'),
(43, '7', 44, 'Tampak Depan', 4, 'worksheet_photos_front', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-12 03:31:23'),
(44, '7', 45, 'Tampak Samping Kanan', 4, 'worksheet_photos_right', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-12 03:31:29'),
(45, '7', 46, 'Tampak Samping Kiri', 4, 'worksheet_photos_left', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-12 03:31:40'),
(46, '7', 47, 'Tampak Belakang', 4, 'worksheet_photos_back', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-12 03:31:43'),
(47, '7', 48, 'Tampak Bagian Dalam', 4, 'worksheet_photos_inside', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-12 03:31:54'),
(48, '7', 49, 'Tampak Bagian MCK', 4, 'worksheet_photos_mck', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-12 03:32:01'),
(49, '1', 1, 'KELENGKAPAN BERKAS', 0, 'file', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-09-01 08:17:10'),
(50, '1', 2, 'Surat Permohonan', 3, 'application_letter', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-09-01 08:13:40'),
(51, '1', 3, 'Susunan Pengurus Takmir', 3, 'takmir_structural', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-09-01 08:13:40'),
(52, '1', 4, 'RAB', 3, 'rab', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-09-01 08:13:41'),
(53, '1', 5, 'FC Stfkt Wakaf / Bkti Proses Wakaf', 3, 'fc_wakaf', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-09-01 08:13:41'),
(54, '1', 6, 'Foto Masjid', 3, 'masjid_photos', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-09-01 08:13:45'),
(55, '1', 7, 'IDENTITAS TAKMIR', 0, 'takmir_identity', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-29 08:23:25'),
(56, '1', 8, 'Nama', 1, 'takmir_name', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-29 08:23:27'),
(57, '1', 9, 'Alamat', 1, 'takmir_address', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-29 08:30:55'),
(58, '1', 10, 'Kedudukan dalam Kepengurusan', 1, 'takmir_position', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-29 08:31:17'),
(59, '1', 11, 'No. HP', 1, 'takmir_phone_number', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-29 08:31:25'),
(60, '1', 12, 'IDENTITAS MASJID', 0, 'mosque_identity', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-29 08:34:02'),
(61, '1', 13, 'Nama Masjid', 1, 'mosque_name', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-29 08:34:25'),
(62, '1', 14, 'Ijin Masjid', 2, 'mosque_permission', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-29 08:34:34'),
(63, '1', 15, 'Sertifikat Wakaf', 2, 'mosque_wakaf_certificate', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-09-01 08:20:47'),
(64, '1', 16, 'Jumlah KK Miskin di Lokasi Masjid', 1, 'mosque_poor_KK', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-29 08:35:03'),
(65, '1', 17, 'Luas Masjid', 1, 'mosque_area', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-29 08:35:14'),
(66, '1', 18, 'Jumlah Jamaah', 1, 'mosque_jamaah', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-29 08:35:25'),
(67, '1', 19, 'Kegiatan Masjid', 1, 'mosque_activity', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-29 08:35:35'),
(68, '1', 20, 'PEMBANGUNAN / RENOVASI MASJID', 0, 'mosque_build', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-29 08:52:03'),
(69, '1', 21, 'Pembangunan / Renovasi Masjid', 1, 'mosque_build_renovation', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-29 08:52:43'),
(70, '1', 22, 'Progress Pembangunan / Renovasi Masjid', 1, 'mosque_progress', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-29 08:56:21'),
(71, '1', 23, 'Dana yang Dimiliki Untuk Pembangunan Renovasi Masjid', 1, 'mosque_fund', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-29 08:56:54'),
(72, '1', 24, 'Responden', 1, 'respondent', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-29 08:57:35'),
(73, '1', 26, 'Gambar Masjid 1', 4, 'mosque_image_1', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-30 02:31:04'),
(74, '1', 27, 'Gambar Masjid 2', 4, 'mosque_image_2', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-09-01 08:18:48'),
(100, '7', 43, 'Catatan Surveyor', 1, 'surveyor_remark', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-10-15 03:22:28'),
(101, '1', 25, 'Catatan Surveyor', 1, 'surveyor_remark', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-10-15 03:22:43'),
(102, '7', 50, 'Foto Bersama Responden', 4, 'surveyor_respondent_photos', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-10-15 03:30:32'),
(103, '1', 28, 'Foto Bersama Responden', 4, 'surveyor_respondent_photos', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-10-15 03:30:34'),
(104, '7', 51, 'Gambar Lainnya 1', 4, 'other_image_1', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-10-15 03:31:01'),
(105, '7', 52, 'Gambar Lainnya 2', 4, 'other_image_2', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-10-15 03:31:03'),
(106, '1', 29, 'Gambar Lainnya 1', 4, 'other_image_1', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-10-15 03:31:06'),
(107, '1', 30, 'Gambar Lainnya 2', 4, 'other_image_2', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-10-15 03:31:06'),
(109, '6', 1, 'PROFIL PRIBADI', 0, 'respondent_data', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-18 03:11:32'),
(110, '6', 2, 'Nama Responden', 1, 'respondent_name', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-18 03:09:27'),
(111, '6', 3, 'Alamat Responden', 1, 'respondent_address', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-18 03:09:41'),
(112, '6', 4, 'Nomor Telepon', 1, 'respondent_phone', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-18 03:09:57'),
(113, '6', 5, 'Pekerjaan Responden', 1, 'respondent_job', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-18 03:10:33'),
(114, '6', 6, 'Status Pernikahan', 1, 'respondent_marriage_status', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-18 03:11:55'),
(115, '6', 7, 'Pendidikan Terakhir', 1, 'respondent_education', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-18 03:12:01'),
(116, '6', 8, 'Jumlah Tanggungan Keluarga', 1, 'respondent_family', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-18 03:12:17'),
(117, '6', 10, 'Aktif Kegiatan Islami (Apabila Asnaf Fisabililah)', 2, 'respondent_islam_activity', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-18 03:12:26'),
(118, '6', 12, 'PROFIL USAHA', 0, 'business_profile', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-18 03:13:32'),
(119, '6', 13, 'Jenis Usaha', 1, 'business_type', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-18 03:14:06'),
(120, '6', 14, 'Nama Usaha', 1, 'business_name', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-18 03:14:26'),
(121, '6', 15, 'Alamat Tempat Usaha', 1, 'business_address', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-18 03:14:26'),
(122, '6', 16, 'Lokasi Usaha', 2, 'business_location', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-18 03:20:55'),
(123, '6', 17, 'Tipe Usaha', 2, 'business_community', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-18 03:14:26'),
(124, '6', 18, 'Lama Usaha (angka dalam bulan)', 1, 'business_age', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-18 03:14:27'),
(125, '6', 19, 'Jumlah Karyawan / Anggota', 1, 'business_employee', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-18 03:14:27'),
(126, '6', 20, 'Sumber Modal', 1, 'business_modal', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-18 03:14:27'),
(127, '6', 21, 'Biaya Produksi', 1, 'business_fee', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-18 03:14:28'),
(128, '6', 22, 'Proses Produksi', 1, 'business_process', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-18 03:14:28'),
(129, '6', 23, 'Harga Jual', 1, 'business_selling_price', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-18 03:14:29'),
(130, '6', 24, 'Margin Keuntungan (angka dalam %)', 1, 'business_interest', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-18 03:14:29'),
(131, '6', 25, 'Kendala Dalam Menjalankan Usaha', 1, 'business_constraint', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-18 03:14:29'),
(132, '6', 26, 'Rencana Kedepan Terhadap Usaha', 1, 'business_future_plan', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-18 03:14:30'),
(133, '6', 27, 'Pernah Mendapat', 2, 'business_assistance_loans', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-18 03:14:30'),
(134, '6', 28, 'Kebutuhan Pengajuan Bantuan 1', 1, 'business_need_1', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-18 03:14:31'),
(135, '6', 29, 'Kebutuhan Pengajuan Bantuan 2', 1, 'business_need_2', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-18 03:23:09'),
(136, '6', 30, 'Kebutuhan pengajuan Bantuan 3', 1, 'business_need_3', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-18 03:23:17'),
(137, '6', 31, 'Total Kebutuhan (Rp)', 1, 'business_need_total', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-18 03:23:24'),
(139, '6', 33, 'Keterangan Lain', 1, 'surveyor_remark', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-18 03:23:56'),
(142, '6', 34, 'Gambar 1', 4, 'image_1', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-18 03:28:33'),
(143, '6', 35, 'Gambar 2', 4, 'image_2', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-18 03:43:46'),
(144, '6', 9, 'Kategori Asnaf', 2, 'category', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-18 04:04:04'),
(145, '6', 11, 'SKTM (Apabila Asnaf Miskin)', 2, 'sktm', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-18 04:04:05'),
(146, '6', 36, 'Foto Bersama Responden', 4, 'surveyor_respondent_photos', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-19 03:12:16'),
(147, '1', 31, 'Sumber Air', 2, 'water_source', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-19 08:33:26'),
(148, '1', 32, 'Lantai', 2, 'floor', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-19 08:33:31'),
(149, '1', 33, 'Dinding', 2, 'wall', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-19 08:33:33'),
(150, '1', 34, 'Atap', 2, 'roof', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-19 08:33:35'),
(151, '1', 35, 'Pagar', 2, 'fence', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-19 08:33:41'),
(152, '1', 36, 'Sarpras', 2, 'sarpras', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-19 08:33:46');

-- --------------------------------------------------------

--
-- Struktur dari tabel `mustahik_worksheet_item`
--

CREATE TABLE `mustahik_worksheet_item` (
  `worksheet_item_id` int(10) NOT NULL,
  `worksheet_id` int(10) DEFAULT NULL,
  `section_name` varchar(250) DEFAULT '',
  `worksheet_item_name` varchar(250) DEFAULT '',
  `worksheet_item_code` varchar(250) DEFAULT '',
  `worksheet_item_token` varchar(250) DEFAULT '',
  `worksheet_item_token_edit` varchar(250) DEFAULT '',
  `data_state` int(1) DEFAULT 0,
  `created_id` int(10) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_id` int(10) DEFAULT 0,
  `updated_at` datetime DEFAULT NULL,
  `deleted_id` int(10) DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data untuk tabel `mustahik_worksheet_item`
--

INSERT INTO `mustahik_worksheet_item` (`worksheet_item_id`, `worksheet_id`, `section_name`, `worksheet_item_name`, `worksheet_item_code`, `worksheet_item_token`, `worksheet_item_token_edit`, `data_state`, `created_id`, `created_at`, `updated_id`, `updated_at`, `deleted_id`, `deleted_at`, `last_update`) VALUES
(1, 5, 'Pilih', 'Fakir', 'fakir_ashnaf', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-10-15 03:35:14'),
(2, 5, 'Pilih', 'Miskin', 'poor_ashnaf', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-10-15 03:35:21'),
(3, 16, 'Jenis', 'Bilik Bambu', 'wall_bamboo', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-12 02:38:26'),
(4, 16, 'Jenis', 'Kayu/Rotan', 'wall_wood', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-12 02:38:32'),
(5, 16, 'Jenis', 'Campuran Tembok Kayu', 'wall_mix', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-12 02:38:39'),
(6, 16, 'Jenis', 'Tembok Plester', 'wall_plaster', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-05-09 04:36:10'),
(11, 17, 'Jenis', 'Tanah', 'floor_sand', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-12 02:38:43'),
(12, 17, 'Jenis', 'Kayu', 'floor_wood', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-12 02:38:46'),
(13, 17, 'Jenis', 'Keramik', 'floor_ceramic', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-12 02:38:50'),
(14, 17, 'Jenis', 'Semen', 'floor_cement', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-09-01 07:52:11'),
(15, 18, 'Jenis', 'Asbes', 'roof_asbes', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-12 02:38:58'),
(16, 18, 'Jenis', 'Genteng', 'roof_tile', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-12 02:39:02'),
(17, 18, 'Jenis', 'Seng Metal', 'roof_metal', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-12 02:39:04'),
(18, 19, 'Jenis', 'Kamar Mandi', 'sanitation_bath_room', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-12 02:39:08'),
(19, 19, 'Jenis', 'MCK', 'sanitation_mck', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-12 02:39:10'),
(20, 19, 'Jenis', 'Sumur/Sumber Air', 'sanitation_well', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-12 02:39:15'),
(21, 20, 'Jenis', 'Kwh Pribadi', 'electricity_private', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-12 02:39:19'),
(22, 20, 'Jenis', 'Menyambung', 'electricity_connect', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-12 02:39:21'),
(23, 21, 'Jenis', 'Milik Sendiri', 'ownership_self', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-12 02:39:26'),
(24, 21, 'Jenis', 'Keluarga', 'ownership_family', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-12 02:39:28'),
(25, 21, 'Jenis', 'Sewa', 'ownership_rent', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-12 02:39:32'),
(26, 16, 'Kondisi', 'Layak', 'wall_decent', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-12 02:40:25'),
(27, 16, 'Kondisi', 'Tidak Layak', 'wall_not_decent', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-12 02:40:28'),
(28, 17, 'Kondisi', 'Layak', 'floor_decent', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-12 02:40:31'),
(29, 17, 'Kondisi', 'Tidak Layak', 'floor_not_decent', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-12 02:40:37'),
(30, 18, 'Kondisi', 'Layak', 'roof_decent', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-12 02:40:39'),
(31, 18, 'Kondisi', 'Tidak Layak', 'roof_not_decent', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-12 02:40:40'),
(32, 19, 'Kondisi', 'Layak', 'sanitation_decent', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-12 02:40:43'),
(33, 19, 'Kondisi', 'Tidak Layak', 'sanitation_not_decent', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-12 02:40:43'),
(34, 20, 'Kondisi', 'Layak', 'electricity_decent', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-12 02:40:45'),
(35, 20, 'Kondisi', 'Tidak Layak', 'electricity_not_decent', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-12 02:40:47'),
(36, 21, 'Kondisi', 'Layak', 'ownership_decent', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-12 02:40:53'),
(37, 21, 'Kondisi', 'Tidak Layak', 'ownership_not_decent', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2022-08-12 02:40:56'),
(38, 117, 'Pilih', 'Takmir Masjid', 'activity_takmir', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-18 04:35:51'),
(39, 117, 'Pilih', 'Guru Ngaji', 'activity_teacher', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-18 04:35:55'),
(40, 117, 'Pilih', 'Pengasuh Ponpes', 'activity_ponpes', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-19 02:27:41'),
(41, 117, 'Pilih', 'Penjaga Masjid', 'activity_guard', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-19 02:27:53'),
(42, 122, 'Pilih', 'Strategis', 'location_strategic', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-18 04:39:23'),
(43, 122, 'Pilih', 'Tidak Strategis', 'location_not_strategic', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-18 04:39:29'),
(44, 133, 'Pilih', 'Bantuan dan Pinjaman', 'assistance_loans', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-18 04:40:21'),
(45, 133, 'Pilih', 'Pinjaman', 'loans', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-18 04:40:28'),
(46, 133, 'Pilih', 'Bantuan', 'assistance', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-18 04:40:39'),
(47, 133, 'Pilih', 'Belum Pernah', 'no_assistance_loans', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-18 04:40:50'),
(48, 144, 'Pilih', 'Fisabililah', 'asnaf_fisabililah', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-18 04:42:56'),
(49, 144, 'Pilih', 'Miskin', 'asnaf_poor', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-18 04:42:57'),
(50, 145, 'Pilih', 'Ada', 'sktm', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-18 04:43:49'),
(51, 145, 'Pilih', 'Tidak Ada', 'no_sktm', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-18 04:43:50'),
(52, 123, 'Pilih', 'Kelompok', 'community_group', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-19 02:41:39'),
(53, 123, 'Pilih', 'Perorangan', 'community_individual', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-19 02:41:40'),
(54, 147, 'Pilih', 'Mesin', 'water_source_machine', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-19 08:38:08'),
(55, 147, 'Pilih', 'Alam / Manual', 'water_source_manual', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-19 08:38:11'),
(56, 148, 'Pilih', 'Ubin/Tegel', 'floor_1', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-19 08:38:45'),
(57, 148, 'Pilih', 'Keramik', 'floor_2', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-19 08:38:49'),
(58, 148, 'Pilih', 'Marmer/Granit', 'floor_3', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-05-09 06:18:42'),
(59, 149, 'Pilih', 'Bagus', 'wall_1', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-19 08:39:04'),
(60, 149, 'Pilih', 'Tidak Bagus', 'wall_2', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-19 08:39:06'),
(61, 150, 'Pilih', 'Genteng, Pvc, Spandek', 'roof_1', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-19 08:39:19'),
(62, 150, 'Pilih', 'Seng', 'roof_2', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-19 08:39:20'),
(63, 150, 'Pilih', 'Bahan lebih jelek dr diatas', 'roof_3', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-19 08:39:22'),
(64, 151, 'Pilih', 'Ada', 'fence', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-19 08:39:33'),
(65, 151, 'Pilih', 'Tidak Ada', 'no_fence', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-19 08:39:34'),
(66, 152, 'Pilih', 'Tikar/Karpet', 'mat', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-19 08:39:38'),
(67, 152, 'Pilih', 'AC', 'ac', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-19 08:39:39'),
(68, 152, 'Pilih', 'Kipas Angin', 'fan', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-19 08:39:40'),
(69, 152, 'Pilih', 'Sound', 'sound', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-05-09 06:29:56'),
(70, 152, 'Pilih', 'Tempat Wudhu', 'wudhu', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-05-09 06:30:03'),
(71, 152, 'Pilih', 'Tempat Parkir', 'parking', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-05-09 06:30:07'),
(72, 152, 'Pilih', 'Kanopi', 'canopy', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-05-09 06:30:10'),
(73, 62, 'Pilih', 'Ada', 'permission', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-19 08:57:25'),
(74, 62, 'Pilih', 'Tidak Ada', 'no_permission', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-19 08:57:27'),
(75, 63, 'Pilih', 'Ada', 'certificate', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-19 08:57:36'),
(76, 63, 'Pilih', 'Tidak Ada', 'no_certificate', '', '', 0, 0, NULL, 0, NULL, 0, NULL, '2023-01-19 08:57:37');

-- --------------------------------------------------------

--
-- Struktur dari tabel `mustahik_worksheet_requisition`
--

CREATE TABLE `mustahik_worksheet_requisition` (
  `worksheet_requisition_id` int(10) NOT NULL,
  `service_requisition_id` int(10) DEFAULT NULL,
  `service_id` int(10) DEFAULT NULL,
  `user_id` int(10) DEFAULT NULL,
  `worksheet_requisition_date` date DEFAULT NULL,
  `worksheet_requisition_token` varchar(250) DEFAULT '',
  `worksheet_requisition_token_edit` varchar(250) DEFAULT '',
  `data_state` int(1) DEFAULT 0,
  `created_id` int(10) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_id` int(10) DEFAULT 0,
  `updated_at` datetime DEFAULT NULL,
  `deleted_id` int(10) DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `mustahik_worksheet_result`
--

CREATE TABLE `mustahik_worksheet_result` (
  `worksheet_result_id` int(10) NOT NULL,
  `worksheet_requisition_id` int(10) DEFAULT NULL,
  `user_id` int(10) DEFAULT NULL,
  `worksheet_result_data` longtext DEFAULT NULL,
  `worksheet_result_date` date DEFAULT NULL,
  `worksheet_result_token` varchar(250) DEFAULT '',
  `worksheet_result_token_edit` varchar(250) DEFAULT '',
  `data_state` int(1) DEFAULT 0,
  `created_id` int(10) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_id` int(10) DEFAULT 0,
  `updated_at` datetime DEFAULT NULL,
  `deleted_id` int(10) DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `preference_company`
--

CREATE TABLE `preference_company` (
  `company_id` int(10) NOT NULL,
  `company_name` varchar(50) DEFAULT '',
  `company_address` text DEFAULT NULL,
  `company_home_phone1` varchar(30) DEFAULT '',
  `company_home_phone2` varchar(30) DEFAULT '',
  `company_fax_number` varchar(30) DEFAULT '',
  `company_logo` varchar(200) DEFAULT '',
  `company_slogan` varchar(250) DEFAULT '',
  `company_footer` text DEFAULT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data untuk tabel `preference_company`
--

INSERT INTO `preference_company` (`company_id`, `company_name`, `company_address`, `company_home_phone1`, `company_home_phone2`, `company_fax_number`, `company_logo`, `company_slogan`, `company_footer`, `last_update`) VALUES
(1, 'Baznas Sragen', 'Komplek Masjid Bazis, Pilangsari, Kebayanan Jetis, Pilangsari, Kec. Sragen, Kabupaten Sragen, Jawa Tengah 57252', '62 271 8825250', '', '', '', 'ANDA SEHAT, KAMI BAHAGIA ', 'Tetap lakukan Prokes 5M : Mencuci Tangan, Memakai Masker, Menjaga Jarak, Menjauhi Kerumunan, Mengurangi Mobilitas', '2021-08-23 12:05:20');

-- --------------------------------------------------------

--
-- Struktur dari tabel `p_p_o_b_s`
--

CREATE TABLE `p_p_o_b_s` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `system_activity_log`
--

CREATE TABLE `system_activity_log` (
  `user_log_id` bigint(20) NOT NULL,
  `user_id` int(10) DEFAULT 0,
  `transaction_id` bigint(22) DEFAULT 0,
  `transaction_code` int(10) DEFAULT 0,
  `transaction_name` varchar(250) DEFAULT '',
  `transaction_remark` varchar(250) DEFAULT '',
  `transaction_date` date DEFAULT NULL,
  `created_on` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `system_change_log`
--

CREATE TABLE `system_change_log` (
  `change_log_id` int(11) NOT NULL DEFAULT 0,
  `user_log_id` int(11) DEFAULT NULL,
  `kode` varchar(15) DEFAULT NULL,
  `old_data` mediumtext DEFAULT NULL,
  `new_data` mediumtext DEFAULT NULL,
  `log_time` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `system_log_user`
--

CREATE TABLE `system_log_user` (
  `user_log_id` bigint(20) NOT NULL,
  `user_id` int(10) DEFAULT 0,
  `username` varchar(50) DEFAULT '',
  `id_previllage` int(4) DEFAULT 0,
  `log_stat` enum('0','1') DEFAULT NULL,
  `class_name` varchar(250) DEFAULT '',
  `pk` varchar(20) DEFAULT '',
  `remark` varchar(50) DEFAULT '',
  `log_time` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `system_log_user`
--

INSERT INTO `system_log_user` (`user_log_id`, `user_id`, `username`, `id_previllage`, `log_stat`, `class_name`, `pk`, `remark`, `log_time`, `created_at`, `updated_at`) VALUES
(1, 0, 'administrator', 1089, '1', 'Application.CoreKelurahan.processAddCoreKelurahan', 'administrator', 'Add Core Service', '2023-09-27 14:52:47', '2023-09-27 14:52:47', '2023-09-27 14:52:47'),
(2, 0, 'administrator', 1089, '1', 'Application.CoreService.processEditCoreService', 'administrator', 'Edit Core Service', '2023-09-27 14:59:12', '2023-09-27 14:59:12', '2023-09-27 14:59:12'),
(3, 0, 'administrator', 1089, '1', 'Application.CoreService.processEditCoreService', 'administrator', 'Edit Core Service', '2023-09-27 14:59:32', '2023-09-27 14:59:32', '2023-09-27 14:59:32'),
(4, 0, 'administrator', 1089, '1', 'Application.CoreService.processEditCoreService', 'administrator', 'Edit Core Service', '2023-09-27 15:00:47', '2023-09-27 15:00:47', '2023-09-27 15:00:47'),
(5, 0, 'administrator', 1089, '1', 'Application.CoreService.processAddCoreService', 'administrator', 'Add Core Service', '2023-09-27 15:02:06', '2023-09-27 15:02:06', '2023-09-27 15:02:06'),
(6, 0, 'administrator', 1089, '1', 'Application.CoreService.processAddCoreService', 'administrator', 'Add Core Service', '2023-09-27 15:02:06', '2023-09-27 15:02:06', '2023-09-27 15:02:06'),
(7, 0, 'administrator', 1089, '1', 'Application.CoreService.processAddCoreService', 'administrator', 'Add Core Service', '2023-09-27 15:02:06', '2023-09-27 15:02:06', '2023-09-27 15:02:06'),
(8, 0, 'administrator', 1089, '1', 'Application.CoreService.processEditCoreService', 'administrator', 'Edit Core Service', '2023-09-27 15:24:28', '2023-09-27 15:24:28', '2023-09-27 15:24:28'),
(9, 0, 'administrator', 1089, '1', 'Application.CoreService.processEditCoreService', 'administrator', 'Edit Core Service', '2023-09-27 15:24:35', '2023-09-27 15:24:35', '2023-09-27 15:24:35'),
(10, 0, 'administrator', 1089, '1', 'Application.CoreService.processEditCoreService', 'administrator', 'Edit Core Service', '2023-09-27 15:25:13', '2023-09-27 15:25:13', '2023-09-27 15:25:13'),
(11, 0, 'administrator', 1089, '1', 'Application.CoreService.processEditCoreService', 'administrator', 'Edit Core Service', '2023-09-27 15:28:34', '2023-09-27 15:28:34', '2023-09-27 15:28:34'),
(12, 0, 'administrator', 1089, '1', 'Application.CoreService.processEditCoreService', 'administrator', 'Edit Core Service', '2023-09-27 15:29:27', '2023-09-27 15:29:27', '2023-09-27 15:29:27'),
(13, 0, 'administrator', 1089, '1', 'Application.CoreService.processEditCoreService', 'administrator', 'Edit Core Service', '2023-09-27 15:32:46', '2023-09-27 15:32:46', '2023-09-27 15:32:46'),
(14, 0, 'administrator', 1089, '1', 'Application.CoreService.processEditCoreService', 'administrator', 'Edit Core Service', '2023-09-27 15:34:11', '2023-09-27 15:34:11', '2023-09-27 15:34:11'),
(15, 0, 'administrator', 1089, '1', 'Application.CoreService.processEditCoreService', 'administrator', 'Edit Core Service', '2023-09-27 15:35:09', '2023-09-27 15:35:09', '2023-09-27 15:35:09'),
(16, 0, 'administrator', 1089, '1', 'Application.TransServiceRequisition.processAddTransServiceRequisition', 'administrator', 'Add Trans Service Requisition', '2023-09-29 09:30:57', '2023-09-29 09:30:57', '2023-09-29 09:30:57'),
(17, 0, 'administrator', 1089, '1', 'Application.TransServiceRequisition.processAddTransServiceRequisition', 'administrator', 'Add Trans Service Requisition', '2023-09-29 09:30:57', '2023-09-29 09:30:57', '2023-09-29 09:30:57'),
(18, 0, 'administrator', 1089, '1', 'Application.TransServiceRequisition.processAddTransServiceRequisition', 'administrator', 'Add Trans Service Requisition', '2023-09-29 09:30:57', '2023-09-29 09:30:57', '2023-09-29 09:30:57'),
(19, 0, 'administrator', 1089, '1', 'Application.TransServiceRequisition.processAddTransServiceRequisition', 'administrator', 'Add Trans Service Requisition', '2023-09-29 09:30:57', '2023-09-29 09:30:57', '2023-09-29 09:30:57'),
(20, 0, 'administrator', 1089, '1', 'Application.TransServiceRequisition.processAddTransServiceRequisition', 'administrator', 'Add Trans Service Requisition', '2023-09-29 09:30:57', '2023-09-29 09:30:57', '2023-09-29 09:30:57'),
(21, 0, 'administrator', 1089, '1', 'Application.TransServiceRequisition.processAddTransServiceRequisition', 'administrator', 'Add Trans Service Requisition', '2023-09-29 09:30:58', '2023-09-29 09:30:58', '2023-09-29 09:30:58'),
(22, 0, 'administrator', 1089, '1', 'Application.TransServiceRequisition.processAddTransServiceRequisition', 'administrator', 'Add Trans Service Requisition', '2023-09-29 09:30:58', '2023-09-29 09:30:58', '2023-09-29 09:30:58'),
(23, 0, 'administrator', 1089, '1', 'Application.TransServiceRequisition.processAddTransServiceRequisition', 'administrator', 'Add Trans Service Requisition', '2023-09-29 09:30:58', '2023-09-29 09:30:58', '2023-09-29 09:30:58'),
(24, 0, 'administrator', 1089, '1', 'Application.TransServiceRequisition.processAddTransServiceRequisition', 'administrator', 'Add Trans Service Requisition', '2023-09-29 09:30:58', '2023-09-29 09:30:58', '2023-09-29 09:30:58'),
(25, 0, 'administrator', 1089, '1', 'Application.TransServiceRequisition.processAddTransServiceRequisition', 'administrator', 'Add Trans Service Requisition', '2023-09-29 09:30:58', '2023-09-29 09:30:58', '2023-09-29 09:30:58'),
(26, 0, 'administrator', 1089, '1', 'Application.TransServiceRequisition.processEditTransServiceRequisition', 'administrator', 'Edit Trans Service Requisition', '2023-09-29 09:44:21', '2023-09-29 09:44:21', '2023-09-29 09:44:21'),
(27, 0, 'administrator', 1089, '1', 'Application.TransServiceRequisition.processEditTransServiceRequisition', 'administrator', 'Edit Trans Service Requisition', '2023-09-29 09:44:21', '2023-09-29 09:44:21', '2023-09-29 09:44:21'),
(28, 0, 'administrator', 1089, '1', 'Application.TransServiceRequisition.processEditTransServiceRequisition', 'administrator', 'Edit Trans Service Requisition', '2023-09-29 09:44:21', '2023-09-29 09:44:21', '2023-09-29 09:44:21'),
(29, 0, 'administrator', 1089, '1', 'Application.TransServiceRequisition.processEditTransServiceRequisition', 'administrator', 'Edit Trans Service Requisition', '2023-09-29 09:44:21', '2023-09-29 09:44:21', '2023-09-29 09:44:21'),
(30, 0, 'administrator', 1089, '1', 'Application.TransServiceRequisition.processEditTransServiceRequisition', 'administrator', 'Edit Trans Service Requisition', '2023-09-29 09:44:21', '2023-09-29 09:44:21', '2023-09-29 09:44:21'),
(31, 0, 'administrator', 1089, '1', 'Application.TransServiceRequisition.processEditTransServiceRequisition', 'administrator', 'Edit Trans Service Requisition', '2023-09-29 09:44:21', '2023-09-29 09:44:21', '2023-09-29 09:44:21'),
(32, 0, 'administrator', 1089, '1', 'Application.TransServiceRequisition.processEditTransServiceRequisition', 'administrator', 'Edit Trans Service Requisition', '2023-09-29 09:44:21', '2023-09-29 09:44:21', '2023-09-29 09:44:21'),
(33, 0, 'administrator', 1089, '1', 'Application.TransServiceRequisition.processEditTransServiceRequisition', 'administrator', 'Edit Trans Service Requisition', '2023-09-29 09:44:21', '2023-09-29 09:44:21', '2023-09-29 09:44:21'),
(34, 0, 'administrator', 1089, '1', 'Application.TransServiceRequisition.processEditTransServiceRequisition', 'administrator', 'Edit Trans Service Requisition', '2023-09-29 09:44:21', '2023-09-29 09:44:21', '2023-09-29 09:44:21'),
(35, 0, 'administrator', 1089, '1', 'Application.TransServiceRequisition.processEditTransServiceRequisition', 'administrator', 'Edit Trans Service Requisition', '2023-09-29 09:44:21', '2023-09-29 09:44:21', '2023-09-29 09:44:21'),
(36, 0, 'administrator', 1089, '1', 'Application.TransServiceDisposition.processAddTransServiceDisposition', 'administrator', 'Add Trans Service Disposition', '2023-09-29 09:50:03', '2023-09-29 09:50:03', '2023-09-29 09:50:03'),
(37, 0, 'administrator', 1089, '1', 'Application.TransServiceDisposition.processAddTransServiceDisposition', 'administrator', 'Add Trans Service Disposition', '2023-09-29 09:50:03', '2023-09-29 09:50:03', '2023-09-29 09:50:03'),
(38, 0, 'administrator', 1089, '1', 'Application.TransServiceDisposition.processAddTransServiceDisposition', 'administrator', 'Add Trans Service Disposition', '2023-09-29 09:50:03', '2023-09-29 09:50:03', '2023-09-29 09:50:03'),
(39, 0, 'administrator', 1089, '1', 'Application.TransServiceDisposition.processAddTransServiceDisposition', 'administrator', 'Add Trans Service Disposition', '2023-09-29 09:50:03', '2023-09-29 09:50:03', '2023-09-29 09:50:03'),
(40, 0, 'administrator', 1089, '1', 'Application.TransServiceDisposition.processAddTransServiceDisposition', 'administrator', 'Add Trans Service Disposition', '2023-09-29 09:50:03', '2023-09-29 09:50:03', '2023-09-29 09:50:03'),
(41, 0, 'administrator', 1089, '1', 'Application.TransServiceDisposition.processAddTransServiceDisposition', 'administrator', 'Add Trans Service Disposition', '2023-09-29 09:50:03', '2023-09-29 09:50:03', '2023-09-29 09:50:03'),
(42, 0, 'administrator', 1089, '1', 'Application.TransServiceDisposition.processAddTransServiceDisposition', 'administrator', 'Add Trans Service Disposition', '2023-09-29 09:50:03', '2023-09-29 09:50:03', '2023-09-29 09:50:03'),
(43, 0, 'administrator', 1089, '1', 'Application.TransServiceDisposition.processAddTransServiceDisposition', 'administrator', 'Add Trans Service Disposition', '2023-09-29 09:50:03', '2023-09-29 09:50:03', '2023-09-29 09:50:03'),
(44, 0, 'administrator', 1089, '1', 'Application.TransServiceDisposition.processAddTransServiceDisposition', 'administrator', 'Add Trans Service Disposition', '2023-09-29 09:50:03', '2023-09-29 09:50:03', '2023-09-29 09:50:03'),
(45, 0, 'administrator', 1089, '1', 'Application.TransServiceDisposition.processAddTransServiceDisposition', 'administrator', 'Add Trans Service Disposition', '2023-09-29 09:50:03', '2023-09-29 09:50:03', '2023-09-29 09:50:03'),
(46, 0, 'administrator', 1089, '1', 'Application.TransServiceDispositionApproval.processAddTransServiceDispositionApproval', 'administrator', 'Add Trans Service Disposition Approval', '2023-09-29 10:02:16', '2023-09-29 10:02:16', '2023-09-29 10:02:16'),
(47, 0, 'administrator', 1089, '1', 'Application.TransServiceDispositionReview.processAddTransServiceDispositionReview', 'administrator', 'Add Trans Service Disposition Review', '2023-09-29 10:06:19', '2023-09-29 10:06:19', '2023-09-29 10:06:19'),
(48, 0, 'administrator', 1089, '1', 'Application.TransServiceDispositionFunds.processAddTransServiceDispositionFunds', 'administrator', 'Add Trans Service Disposition Funds', '2023-09-29 10:08:48', '2023-09-29 10:08:48', '2023-09-29 10:08:48');

-- --------------------------------------------------------

--
-- Struktur dari tabel `system_menu`
--

CREATE TABLE `system_menu` (
  `id_menu` varchar(10) NOT NULL,
  `id` varchar(100) DEFAULT NULL,
  `type` enum('folder','file','function') DEFAULT NULL,
  `indent_level` int(1) DEFAULT NULL,
  `text` varchar(50) DEFAULT NULL,
  `image` varchar(50) DEFAULT NULL,
  `last_update` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `system_menu`
--

INSERT INTO `system_menu` (`id_menu`, `id`, `type`, `indent_level`, `text`, `image`, `last_update`) VALUES
('0', 'home', 'file', 1, 'Beranda', NULL, '2021-12-18 04:09:42'),
('1', '#', 'folder', 1, 'System', NULL, '2021-12-18 03:37:18'),
('11', 'system-user', 'file', 2, 'System User', NULL, '2021-12-18 03:37:19'),
('12', 'system-user-group', 'file', 2, 'System User Group', NULL, '2021-12-18 03:37:22'),
('2', '#', 'folder', 1, 'Layanan', NULL, '2021-10-26 02:59:19'),
('21', '#', 'folder', 2, 'Preferensi', NULL, '2021-10-26 02:59:36'),
('211', 'section', 'file', 3, 'Bagian', NULL, '2021-10-26 04:18:27'),
('212', 'service', 'file', 3, 'Bantuan', NULL, '2023-09-26 03:53:56'),
('213', 'kecamatan', 'file', 3, 'Kecamatan', NULL, '2023-05-11 03:11:29'),
('22', '#', 'folder', 2, 'Pengajuan', NULL, '2021-12-11 04:16:28'),
('221', 'trans-service-requisition', 'file', 3, 'Pengajuan Bantuan', NULL, '2021-12-16 03:11:30'),
('222', 'trans-service-general', 'file', 3, 'Pengajuan Surat Umum', NULL, '2022-01-25 07:02:28'),
('23', '#', 'folder', 2, 'Disposisi', NULL, '2021-12-18 05:09:39'),
('231', 'trans-service-disposition', 'file', 3, 'Disposisi Bantuan', NULL, '2021-12-18 05:10:05'),
('232', 'trans-service-disposition-approval', 'file', 3, 'Persetujuan Disposisi Bantuan', NULL, '2021-12-18 05:10:13'),
('233', 'trans-service-disposition-review', 'file', 3, 'Review Disposisi Bantuan', NULL, '2021-12-18 05:10:19'),
('234', 'trans-service-disposition-funds', 'file', 3, 'Pencairan Disposisi Bantuan', NULL, '2022-03-19 04:24:57'),
('235', 'mustahik-worksheet-result', 'file', 2, 'Data Mustappa', NULL, '2022-12-20 02:34:06'),
('24', '#', 'folder', 2, 'ZIS', NULL, '2023-10-02 02:22:43'),
('25', 'trans-service-zis', 'file', 3, 'Zakat Infaq Sedekah', NULL, '2023-10-02 02:29:24'),
('3', '#', 'folder', 1, 'Surat Umum', NULL, '2022-01-25 07:07:37'),
('31', 'service-general-parameter', 'file', 2, 'Preferensi', NULL, '2022-01-25 07:09:51'),
('32', 'trans-service-general', 'file', 2, 'Pengajuan', NULL, '2022-01-25 07:08:41'),
('33', 'trans-service-general-approval', 'file', 2, 'Persetujuan', NULL, '2022-01-25 07:09:04'),
('4', '#', 'folder', 1, 'Cetak', NULL, '2022-01-25 07:07:21'),
('41', 'print-service', 'file', 2, 'Cetak Data Bantuan', NULL, '2022-01-25 07:07:19'),
('42', 'print-service-general', 'file', 2, 'Cetak Data Surat Umum', NULL, '2022-02-08 03:12:24'),
('5', '#', 'folder', 1, 'Notifikasi', NULL, '2022-01-25 07:07:08'),
('51', 'messages', 'file', 2, 'Pesan Notifikasi', NULL, '2022-01-25 07:07:06'),
('52', 'scan-qr', 'file', 2, 'Scan QR', NULL, '2022-01-25 07:07:06'),
('53', 'scan-qr/reload', 'file', 2, 'Reload Service', NULL, '2022-01-25 07:07:04'),
('6', 'dashboard-review', 'file', 1, 'Dashboard Review', NULL, '2022-01-25 07:07:02');

-- --------------------------------------------------------

--
-- Struktur dari tabel `system_menu_mapping`
--

CREATE TABLE `system_menu_mapping` (
  `menu_mapping_id` int(10) NOT NULL,
  `user_group_level` int(3) DEFAULT NULL,
  `id_menu` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data untuk tabel `system_menu_mapping`
--

INSERT INTO `system_menu_mapping` (`menu_mapping_id`, `user_group_level`, `id_menu`, `created_at`, `updated_at`) VALUES
(39, 1, '1', '2022-01-07 04:16:59', '2022-01-06 21:16:59'),
(40, 1, '11', '2022-01-07 04:16:59', '2022-01-06 21:16:59'),
(41, 1, '12', '2022-01-07 04:16:59', '2022-01-06 21:16:59'),
(42, 1, '2', '2022-01-07 04:16:59', '2022-01-06 21:16:59'),
(43, 1, '21', '2022-01-07 04:16:59', '2022-01-06 21:16:59'),
(44, 1, '211', '2022-01-07 04:16:59', '2022-01-06 21:16:59'),
(45, 1, '212', '2022-01-07 04:16:59', '2022-01-06 21:16:59'),
(46, 1, '22', '2022-01-07 04:16:59', '2022-01-06 21:16:59'),
(47, 1, '221', '2022-01-07 04:16:59', '2022-01-06 21:16:59'),
(48, 1, '23', '2022-01-07 04:16:59', '2022-01-06 21:16:59'),
(49, 1, '231', '2022-01-07 04:16:59', '2022-01-06 21:16:59'),
(50, 1, '232', '2022-01-07 04:16:59', '2022-01-06 21:16:59'),
(51, 1, '233', '2022-01-07 04:16:59', '2022-01-06 21:16:59'),
(52, 1, '4', '2022-01-07 04:16:59', '2022-01-06 21:16:59'),
(53, 1, '41', '2022-01-07 04:16:59', '2022-01-06 21:16:59'),
(54, 1, '6', '2022-01-07 04:16:59', '2022-01-06 21:16:59'),
(55, 1, '5', '2022-01-07 04:16:59', '2022-01-06 21:16:59'),
(56, 1, '51', '2022-01-07 04:16:59', '2022-01-06 21:16:59'),
(57, 1, '52', '2022-01-07 04:16:59', '2022-01-06 21:16:59'),
(58, 1, '53', '2022-01-07 04:16:59', '2022-01-06 21:16:59'),
(59, 1, '3', NULL, '2022-01-25 07:09:19'),
(60, 1, '31', NULL, '2022-01-25 07:09:22'),
(61, 1, '32', NULL, '2022-01-25 07:09:24'),
(62, 1, '33', NULL, '2022-01-25 07:09:26'),
(63, 1, '42', NULL, '2022-02-08 03:13:55'),
(98, 2, '2', '2022-03-17 06:02:30', '2022-03-16 23:02:30'),
(99, 2, '22', '2022-03-17 06:02:30', '2022-03-16 23:02:30'),
(100, 2, '221', '2022-03-17 06:02:30', '2022-03-16 23:02:30'),
(121, 4, '0', '2022-03-17 06:45:12', '2022-03-16 23:45:12'),
(122, 4, '2', '2022-03-17 06:45:12', '2022-03-16 23:45:12'),
(123, 4, '23', '2022-03-17 06:45:12', '2022-03-16 23:45:12'),
(124, 4, '232', '2022-03-17 06:45:12', '2022-03-16 23:45:12'),
(130, 1, '234', NULL, '2022-03-19 04:25:43'),
(131, 7, '0', '2022-03-21 03:40:54', '2022-03-20 20:40:54'),
(132, 7, '2', '2022-03-21 03:40:54', '2022-03-20 20:40:54'),
(133, 7, '23', '2022-03-21 03:40:54', '2022-03-20 20:40:54'),
(134, 7, '232', '2022-03-21 03:40:54', '2022-03-20 20:40:54'),
(135, 7, '234', '2022-03-21 03:40:54', '2022-03-20 20:40:54'),
(136, 6, '0', '2022-03-28 06:53:02', '2022-03-27 23:53:02'),
(137, 6, '2', '2022-03-28 06:53:02', '2022-03-27 23:53:02'),
(138, 6, '22', '2022-03-28 06:53:02', '2022-03-27 23:53:02'),
(139, 6, '221', '2022-03-28 06:53:02', '2022-03-27 23:53:02'),
(140, 6, '232', '2022-03-28 06:53:02', '2022-03-27 23:53:02'),
(141, 6, '3', '2022-03-28 06:53:02', '2022-03-27 23:53:02'),
(142, 6, '32', '2022-03-28 06:53:02', '2022-03-27 23:53:02'),
(154, 5, '0', '2022-08-25 02:11:13', '2022-08-24 19:11:13'),
(155, 5, '2', '2022-08-25 02:11:13', '2022-08-24 19:11:13'),
(156, 5, '22', '2022-08-25 02:11:13', '2022-08-24 19:11:13'),
(157, 5, '221', '2022-08-25 02:11:13', '2022-08-24 19:11:13'),
(158, 5, '222', '2022-08-25 02:11:13', '2022-08-24 19:11:13'),
(159, 5, '23', '2022-08-25 02:11:13', '2022-08-24 19:11:13'),
(160, 5, '231', '2022-08-25 02:11:13', '2022-08-24 19:11:13'),
(161, 5, '232', '2022-08-25 02:11:13', '2022-08-24 19:11:13'),
(162, 5, '4', '2022-08-25 02:11:13', '2022-08-24 19:11:13'),
(163, 5, '41', '2022-08-25 02:11:13', '2022-08-24 19:11:13'),
(164, 5, '42', '2022-08-25 02:11:13', '2022-08-24 19:11:13'),
(165, 5, '5', '2022-08-25 02:11:13', '2022-08-24 19:11:13'),
(166, 5, '52', '2022-08-25 02:11:13', '2022-08-24 19:11:13'),
(167, 5, '53', '2022-08-25 02:11:13', '2022-08-24 19:11:13'),
(168, 1, '235', '2022-10-05 13:52:52', '2022-08-31 02:41:51'),
(169, 5, '235', '2022-10-05 13:53:14', '2022-10-05 06:53:31'),
(170, 4, '235', NULL, '2022-11-15 06:34:34'),
(171, 6, '235', NULL, '2022-11-15 06:34:34'),
(174, 1, '213', '2023-05-11 10:12:15', '2023-05-11 03:12:42'),
(175, 5, '213', '2023-05-11 10:12:15', '2023-05-11 03:12:42'),
(176, 1, '24', '2023-09-30 12:23:04', '2023-09-30 05:23:02'),
(177, 1, '25', '2023-10-02 09:24:46', '2023-10-02 02:24:48');

-- --------------------------------------------------------

--
-- Struktur dari tabel `system_user`
--

CREATE TABLE `system_user` (
  `user_id` int(10) NOT NULL,
  `user_group_id` int(3) DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT '',
  `phone_number` varchar(255) DEFAULT NULL,
  `branch_id` int(1) DEFAULT 0,
  `section_id` int(10) DEFAULT 0,
  `email` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `mustahik_surveyor` int(1) NOT NULL DEFAULT 0 COMMENT '1 = surveyor, 0 = bukan',
  `remember_token` varchar(100) DEFAULT NULL,
  `data_state` int(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `system_user`
--

INSERT INTO `system_user` (`user_id`, `user_group_id`, `full_name`, `name`, `phone_number`, `branch_id`, `section_id`, `email`, `email_verified_at`, `password`, `mustahik_surveyor`, `remember_token`, `data_state`, `created_at`, `updated_at`) VALUES
(55, 1, 'Administrator', 'administrator', '08812792729', 0, 0, NULL, NULL, '$2y$10$ogik4w1K0zVAvSqnUP/1p.ASh089J4cuMJs/.iPh9WsV07ndO1orm', 1, NULL, 0, '2021-10-25 20:03:14', '2023-05-02 01:01:05'),
(62, 22, 'Sri Indriyani Dian', 'dian', '-', 0, 1, NULL, NULL, '$2y$10$yPtbhkXvMKMZY3lPaMtMU.Ecz8JKbgnNhAdJMnwnL68t7.HN/Cc/2', 0, NULL, 0, '2022-03-16 21:02:57', '2022-03-16 23:24:41'),
(64, 21, 'Anggam Sambakarim', 'anggam', '-', 0, 9, NULL, NULL, '$2y$10$yPtbhkXvMKMZY3lPaMtMU.Ecz8JKbgnNhAdJMnwnL68t7.HN/Cc/2', 1, NULL, 0, '2022-03-16 21:05:03', '2022-08-22 20:29:01'),
(66, 24, 'Ahmad Miftahul Falah', 'ahmad', '-', 0, 11, NULL, NULL, '$2y$10$yPtbhkXvMKMZY3lPaMtMU.Ecz8JKbgnNhAdJMnwnL68t7.HN/Cc/2', 0, NULL, 0, '2022-03-16 21:06:08', '2022-03-22 00:15:01'),
(67, 17, 'Rizki Miskia', 'rizki', '-', 0, 10, NULL, NULL, '$2y$10$yPtbhkXvMKMZY3lPaMtMU.Ecz8JKbgnNhAdJMnwnL68t7.HN/Cc/2', 0, NULL, 0, '2022-03-16 21:21:46', '2022-03-16 22:56:46'),
(68, 1, 'Syarif', 'syarif', '087832066397', 0, 0, NULL, NULL, '$2y$10$yPtbhkXvMKMZY3lPaMtMU.Ecz8JKbgnNhAdJMnwnL68t7.HN/Cc/2', 0, NULL, 0, '2022-03-20 17:31:51', '2022-03-20 17:31:51'),
(71, 1, 'Almunawar', 'almunawar', '-', 0, 0, NULL, NULL, '$2y$10$yPtbhkXvMKMZY3lPaMtMU.Ecz8JKbgnNhAdJMnwnL68t7.HN/Cc/2', 0, NULL, 0, NULL, NULL),
(72, 1, 'Qoyim', 'qoyim', '-', 0, 0, NULL, NULL, '$2y$10$yPtbhkXvMKMZY3lPaMtMU.Ecz8JKbgnNhAdJMnwnL68t7.HN/Cc/2', 0, NULL, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `system_user_group`
--

CREATE TABLE `system_user_group` (
  `user_group_id` int(3) NOT NULL,
  `user_group_level` int(11) DEFAULT NULL,
  `user_group_name` varchar(50) DEFAULT NULL,
  `user_group_token` varchar(250) DEFAULT '',
  `data_state` int(1) DEFAULT 0,
  `created_id` int(10) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_id` int(10) DEFAULT 0,
  `updated_on` datetime DEFAULT NULL,
  `deleted_id` int(10) DEFAULT 0,
  `deleted_on` datetime DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `system_user_group`
--

INSERT INTO `system_user_group` (`user_group_id`, `user_group_level`, `user_group_name`, `user_group_token`, `data_state`, `created_id`, `created_at`, `updated_id`, `updated_on`, `deleted_id`, `deleted_on`, `updated_at`) VALUES
(1, 1, 'Administrator', '', 0, 0, NULL, 0, NULL, 0, NULL, '2021-10-26 03:02:23'),
(17, 2, 'FrontDesk', '', 0, 0, '2022-01-07 03:37:13', 0, NULL, 0, NULL, '2022-01-06 20:37:13'),
(20, 3, 'SDM dan Administrasi', '', 1, 0, '2022-03-17 04:01:02', 0, NULL, 0, NULL, '2022-03-16 23:40:19'),
(21, 4, 'Staff Disposisi', '', 0, 0, '2022-03-17 04:01:36', 0, NULL, 0, NULL, '2022-03-16 21:01:36'),
(22, 5, 'SDM dan Administrasi', '', 0, 0, '2022-03-17 06:23:51', 0, NULL, 0, NULL, '2022-03-16 23:24:07'),
(23, 6, 'Rangkap Disposisi & FO', '', 0, 0, '2022-03-17 07:53:50', 0, NULL, 0, NULL, '2022-03-17 00:53:50'),
(24, 7, 'Keuangan & Pelaporan', '', 0, 0, '2022-03-21 03:40:54', 0, NULL, 0, NULL, '2022-03-20 20:40:54');

-- --------------------------------------------------------

--
-- Struktur dari tabel `trans_service_disposition`
--

CREATE TABLE `trans_service_disposition` (
  `service_disposition_id` int(10) NOT NULL,
  `service_requisition_id` int(10) DEFAULT NULL,
  `service_requisition_no` varchar(250) DEFAULT NULL,
  `service_register_no` varchar(25) NOT NULL DEFAULT '',
  `service_requisition_name` varchar(250) DEFAULT NULL,
  `service_requisition_phone` varchar(250) DEFAULT NULL,
  `service_requisition_nik` varchar(55) DEFAULT NULL,
  `service_requisition_address` text DEFAULT NULL,
  `service_id` int(10) DEFAULT NULL,
  `kecamatan_id` int(10) DEFAULT NULL,
  `kelurahan_id` int(10) DEFAULT NULL,
  `section_id` int(10) DEFAULT NULL,
  `service_disposition_remark` text DEFAULT NULL,
  `service_disposition_token` varchar(250) DEFAULT '',
  `service_disposition_token_edit` varchar(250) DEFAULT '',
  `service_disposition_status` int(1) DEFAULT 0,
  `service_disposition_amount` decimal(20,2) NOT NULL DEFAULT 0.00,
  `service_disposition_funds_status` int(1) NOT NULL DEFAULT 0,
  `approved_status` int(1) DEFAULT 0 COMMENT '0 = draft, 1 = approved, 2 = dikembalikan ke tahap sebelumnya',
  `approved_remark` text DEFAULT NULL,
  `approved_id` int(10) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `unapprove_remark` text DEFAULT NULL,
  `unapprove_id` int(10) DEFAULT NULL,
  `unapprove_at` datetime DEFAULT NULL,
  `review_status` int(1) DEFAULT 0 COMMENT '0 = draft, 1 = approve, 2 = dikembalikan ke tahap sebelumnya',
  `review_id` int(10) DEFAULT NULL,
  `review_at` datetime DEFAULT NULL,
  `review_remark` text DEFAULT NULL,
  `unreview_id` int(11) DEFAULT NULL,
  `unreview_at` datetime DEFAULT NULL,
  `unreview_remark` text DEFAULT NULL,
  `disapprove_id` int(11) DEFAULT NULL,
  `disapprove_at` datetime DEFAULT NULL,
  `disapprove_remark` text DEFAULT NULL,
  `funds_amount_id` int(11) DEFAULT NULL,
  `funds_amount_at` datetime DEFAULT NULL,
  `funds_status_id` int(11) DEFAULT NULL,
  `funds_status_at` datetime DEFAULT NULL,
  `file_sk` varchar(250) NOT NULL DEFAULT '',
  `file_funds_application` varchar(250) DEFAULT NULL,
  `file_funds_order` varchar(250) DEFAULT NULL,
  `delete_remark` varchar(250) DEFAULT '',
  `mustahik_status` int(1) NOT NULL DEFAULT 0,
  `data_state` int(1) DEFAULT 0,
  `created_id` int(10) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_id` int(10) DEFAULT 0,
  `updated_at` datetime DEFAULT NULL,
  `deleted_id` int(10) DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data untuk tabel `trans_service_disposition`
--

INSERT INTO `trans_service_disposition` (`service_disposition_id`, `service_requisition_id`, `service_requisition_no`, `service_register_no`, `service_requisition_name`, `service_requisition_phone`, `service_requisition_nik`, `service_requisition_address`, `service_id`, `kecamatan_id`, `kelurahan_id`, `section_id`, `service_disposition_remark`, `service_disposition_token`, `service_disposition_token_edit`, `service_disposition_status`, `service_disposition_amount`, `service_disposition_funds_status`, `approved_status`, `approved_remark`, `approved_id`, `approved_at`, `unapprove_remark`, `unapprove_id`, `unapprove_at`, `review_status`, `review_id`, `review_at`, `review_remark`, `unreview_id`, `unreview_at`, `unreview_remark`, `disapprove_id`, `disapprove_at`, `disapprove_remark`, `funds_amount_id`, `funds_amount_at`, `funds_status_id`, `funds_status_at`, `file_sk`, `file_funds_application`, `file_funds_order`, `delete_remark`, `mustahik_status`, `data_state`, `created_id`, `created_at`, `updated_id`, `updated_at`, `deleted_id`, `deleted_at`, `last_update`) VALUES
(723, 1, '000001/SR/IX/2023', '2023001', 'Tes', '08812792729', '1000010110', 'tesss', 1, 2, 17, 9, NULL, '6462644295a8591cd3c6ae64d6a12949', '', 0, 100000.00, 1, 1, 'stju', 55, '2023-09-29 03:02:16', NULL, NULL, NULL, 1, 55, '2023-09-29 03:06:19', 'tes', NULL, NULL, NULL, NULL, NULL, NULL, 55, '2023-09-29 03:08:48', NULL, NULL, 'company_1695956779.png', 'company_1695956928.png', 'company-white_1695956928.png', '', 0, 0, 55, '2023-09-29 02:50:03', 0, '2023-09-29 03:08:48', 0, NULL, '2023-09-29 02:50:03');

--
-- Trigger `trans_service_disposition`
--
DELIMITER $$
CREATE TRIGGER `update_trans_service_disposition` BEFORE UPDATE ON `trans_service_disposition` FOR EACH ROW BEGIN
	DECLARE year_period 				VARCHAR(20);
	DECLARE PERIOD 					VARCHAR(20);
	DECLARE tPeriod					INT;
	DECLARE nTransServiceRegisterNo			VARCHAR(20);
	
	IF (new.service_disposition_funds_status = 1) THEN
		SET year_period = (YEAR(new.funds_amount_at));
			
		SET PERIOD = (SELECT RIGHT(TRIM(service_register_no), 3) 
				FROM trans_service_disposition
				WHERE LEFT(TRIM(service_register_no), 4) = year_period
				ORDER BY service_register_no DESC 
				LIMIT 1);
			
		IF (PERIOD IS NULL ) THEN 
			SET PERIOD = "000";
		END IF;
		
		SET tPeriod = CAST(PERIOD AS DECIMAL(3));
		
		SET tPeriod = tPeriod + 1;
		
		SET PERIOD = RIGHT(CONCAT('000', TRIM(CAST(tPeriod AS CHAR(3)))), 3);
		
		SET nTransServiceRegisterNo = CONCAT(year_period, PERIOD);
		SET new.service_register_no = nTransServiceRegisterNo;
	end if;
    END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `trans_service_disposition_parameter`
--

CREATE TABLE `trans_service_disposition_parameter` (
  `service_disposition_parameter_id` int(10) NOT NULL,
  `service_disposition_id` int(10) DEFAULT NULL,
  `service_parameter_id` int(10) DEFAULT NULL,
  `service_requisition_parameter_id` int(10) DEFAULT NULL,
  `service_disposition_parameter_value` text DEFAULT NULL,
  `service_disposition_parameter_token` varchar(250) DEFAULT '',
  `service_disposition_parameter_token_edit` varchar(250) DEFAULT '',
  `data_state` int(1) DEFAULT 0,
  `created_id` int(10) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_id` int(10) DEFAULT 0,
  `updated_at` datetime DEFAULT NULL,
  `deleted_id` int(10) DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data untuk tabel `trans_service_disposition_parameter`
--

INSERT INTO `trans_service_disposition_parameter` (`service_disposition_parameter_id`, `service_disposition_id`, `service_parameter_id`, `service_requisition_parameter_id`, `service_disposition_parameter_value`, `service_disposition_parameter_token`, `service_disposition_parameter_token_edit`, `data_state`, `created_id`, `created_at`, `updated_id`, `updated_at`, `deleted_id`, `deleted_at`, `last_update`) VALUES
(1, 723, 3, 1, 'test', '6462644295a8591cd3c6ae64d6a129493', '', 0, 0, '2023-09-29 09:50:03', 0, '2023-09-29 09:50:03', 0, NULL, '2023-09-29 02:50:03'),
(2, 723, 99, 2, 'set', '6462644295a8591cd3c6ae64d6a1294999', '', 0, 0, '2023-09-29 09:50:03', 0, '2023-09-29 09:50:03', 0, NULL, '2023-09-29 02:50:03'),
(3, 723, 100, 3, '3', '6462644295a8591cd3c6ae64d6a12949100', '', 0, 0, '2023-09-29 09:50:03', 0, '2023-09-29 09:50:03', 0, NULL, '2023-09-29 02:50:03'),
(4, 723, 101, 4, 'asd', '6462644295a8591cd3c6ae64d6a12949101', '', 0, 0, '2023-09-29 09:50:03', 0, '2023-09-29 09:50:03', 0, NULL, '2023-09-29 02:50:03'),
(5, 723, 102, 5, 'xzc', '6462644295a8591cd3c6ae64d6a12949102', '', 0, 0, '2023-09-29 09:50:03', 0, '2023-09-29 09:50:03', 0, NULL, '2023-09-29 02:50:03'),
(6, 723, 103, 6, 'xc', '6462644295a8591cd3c6ae64d6a12949103', '', 0, 0, '2023-09-29 09:50:03', 0, '2023-09-29 09:50:03', 0, NULL, '2023-09-29 02:50:03'),
(7, 723, 104, 7, 'asd', '6462644295a8591cd3c6ae64d6a12949104', '', 0, 0, '2023-09-29 09:50:03', 0, '2023-09-29 09:50:03', 0, NULL, '2023-09-29 02:50:03'),
(8, 723, 105, 8, 'xz', '6462644295a8591cd3c6ae64d6a12949105', '', 0, 0, '2023-09-29 09:50:03', 0, '2023-09-29 09:50:03', 0, NULL, '2023-09-29 02:50:03');

-- --------------------------------------------------------

--
-- Struktur dari tabel `trans_service_disposition_term`
--

CREATE TABLE `trans_service_disposition_term` (
  `service_disposition_term_id` int(10) NOT NULL,
  `service_disposition_id` int(10) DEFAULT NULL,
  `service_term_id` int(10) DEFAULT NULL,
  `service_requisition_term_id` int(10) DEFAULT NULL,
  `service_disposition_term_status` int(1) DEFAULT 0,
  `service_disposition_term_value` text DEFAULT NULL,
  `service_disposition_term_token` varchar(250) DEFAULT '',
  `service_disposition_term_token_edit` varchar(250) DEFAULT '',
  `data_state` int(1) DEFAULT 0,
  `created_id` int(10) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_id` int(10) DEFAULT 0,
  `updated_at` datetime DEFAULT NULL,
  `deleted_id` int(10) DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data untuk tabel `trans_service_disposition_term`
--

INSERT INTO `trans_service_disposition_term` (`service_disposition_term_id`, `service_disposition_id`, `service_term_id`, `service_requisition_term_id`, `service_disposition_term_status`, `service_disposition_term_value`, `service_disposition_term_token`, `service_disposition_term_token_edit`, `data_state`, `created_id`, `created_at`, `updated_id`, `updated_at`, `deleted_id`, `deleted_at`, `last_update`) VALUES
(1, 723, 1, 1, 1, 'alien-1579687978_1695954657.jpg', '6462644295a8591cd3c6ae64d6a129491', '', 0, 0, '2023-09-29 09:50:03', 0, '2023-09-29 09:50:03', 0, NULL, '2023-09-29 02:50:03');

-- --------------------------------------------------------

--
-- Struktur dari tabel `trans_service_document_requisition`
--

CREATE TABLE `trans_service_document_requisition` (
  `service_document_requisition_id` int(10) NOT NULL,
  `service_requisition_id` int(10) DEFAULT NULL,
  `service_requisition_term_id` int(10) DEFAULT NULL,
  `service_term_id` int(10) DEFAULT NULL,
  `service_document_requisition_remark` text DEFAULT NULL,
  `service_document_requisition_token` varchar(250) DEFAULT '',
  `delete_remark` varchar(250) DEFAULT '',
  `data_state` int(1) DEFAULT 0,
  `created_id` int(10) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_id` int(10) DEFAULT 0,
  `updated_at` datetime DEFAULT NULL,
  `deleted_id` int(10) DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `trans_service_general`
--

CREATE TABLE `trans_service_general` (
  `service_general_id` int(10) NOT NULL,
  `service_general_no` varchar(250) DEFAULT NULL,
  `service_general_agency` varchar(250) DEFAULT NULL,
  `service_general_phone` varchar(250) DEFAULT NULL,
  `service_general_file` varchar(250) DEFAULT NULL,
  `service_general_sk_file` varchar(250) DEFAULT NULL,
  `service_general_priority` int(10) NOT NULL,
  `service_id` int(10) DEFAULT NULL,
  `kecamatan_id` int(10) DEFAULT NULL,
  `service_general_token` varchar(250) DEFAULT '',
  `service_general_token_edit` varchar(250) DEFAULT '',
  `service_general_token_approval` varchar(250) DEFAULT '',
  `service_general_status` int(1) DEFAULT 0 COMMENT '0 = belum di assign, 1 = sudah di assign, 2 = perlu susulan file, 3 = disetujui bagian disposisi, 4 = disetujui reviewer, 5 = ditolak review',
  `service_general_remark` text DEFAULT NULL,
  `delete_remark` varchar(250) DEFAULT '',
  `data_state` int(1) DEFAULT 0,
  `created_id` int(10) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_id` int(10) DEFAULT 0,
  `updated_at` datetime DEFAULT NULL,
  `approved_id` int(10) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `disapproved_id` int(10) DEFAULT NULL,
  `disapproved_at` datetime DEFAULT NULL,
  `revision_id` int(10) DEFAULT NULL,
  `revision_at` datetime DEFAULT NULL,
  `deleted_id` int(10) DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Trigger `trans_service_general`
--
DELIMITER $$
CREATE TRIGGER `insert_trans_service_general` BEFORE INSERT ON `trans_service_general` FOR EACH ROW BEGIN
	DECLARE year_period 				VARCHAR(20);
	DECLARE month_period 				VARCHAR(20);
	DECLARE PERIOD 					VARCHAR(20);
	DECLARE tPeriod					INT;
	DECLARE nTransServiceGeneralNo			VARCHAR(20);
	DECLARE monthPeriod				VARCHAR(20);
	DECLARE lenTransServiceGeneralNo		DECIMAL(10);
	
	SET year_period = (YEAR(new.created_at));
	
	SET month_period = (SELECT RIGHT(CONCAT('0', MONTH(new.created_at)), 2));
	
	IF (month_period) = '01' THEN 
		SET monthPeriod = 'I';
	END IF;
	
	IF (month_period) = '02' THEN 
		SET monthPeriod = 'II';
	END IF;
	
	IF (month_period) = '03' THEN 
		SET monthPeriod = 'III';
	END IF;
	
	IF (month_period) = '04' THEN 
		SET monthPeriod = 'IV';
	END IF;	
	
	IF (month_period) = '05' THEN 
		SET monthPeriod = 'V';
	END IF;
	
	IF (month_period) = '06' THEN 
		SET monthPeriod = 'VI';
	END IF;
	
	IF (month_period) = '07' THEN 
		SET monthPeriod = 'VII';
	END IF;
	
	IF (month_period) = '08' THEN 
		SET monthPeriod = 'VIII';
	END IF;
	
	IF (month_period) = '09' THEN 
		SET monthPeriod = 'IX';
	END IF;
	
	IF (month_period) = '10' THEN 
		SET monthPeriod = 'X';
	END IF;
	
	IF (month_period) = '11' THEN 
		SET monthPeriod = 'XI';
	END IF;
	
	IF (month_period) = '12' THEN 
		SET monthPeriod = 'XII';
	END IF;
		
	SET PERIOD = (SELECT LEFT(TRIM(service_general_no), 6) 
			FROM trans_service_general
			WHERE RIGHT(TRIM(service_general_no), 4) = year_period
			ORDER BY service_general_id DESC 
			LIMIT 1);
		
	IF (PERIOD IS NULL ) THEN 
		SET PERIOD = "000000";
	END IF;
	
	SET tPeriod = CAST(PERIOD AS DECIMAL(10));
	
	SET tPeriod = tPeriod + 1;
	
	SET PERIOD = RIGHT(CONCAT('000000', TRIM(CAST(tPeriod AS CHAR(6)))), 6);
	
	SET nTransServiceGeneralNo = CONCAT(PERIOD, '/SG/', monthPeriod, '/', year_period);
		
	SET new.service_general_no = nTransServiceGeneralNo;
    END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `trans_service_general_parameter`
--

CREATE TABLE `trans_service_general_parameter` (
  `service_general_parameter_id` int(10) NOT NULL,
  `service_general_id` int(10) DEFAULT NULL,
  `general_parameter_id` int(10) DEFAULT NULL,
  `service_general_parameter_value` text DEFAULT NULL,
  `service_general_parameter_token` varchar(250) DEFAULT '',
  `service_general_parameter_token_edit` varchar(250) DEFAULT '',
  `data_state` int(1) DEFAULT 0,
  `created_id` int(10) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_id` int(10) DEFAULT 0,
  `updated_at` datetime DEFAULT NULL,
  `deleted_id` int(10) DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `trans_service_log`
--

CREATE TABLE `trans_service_log` (
  `service_log_id` int(10) NOT NULL,
  `service_requisition_no` varchar(250) DEFAULT '',
  `service_status` varchar(250) DEFAULT '',
  `section_id` int(10) DEFAULT NULL,
  `data_state` int(1) DEFAULT 0,
  `created_id` int(10) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data untuk tabel `trans_service_log`
--

INSERT INTO `trans_service_log` (`service_log_id`, `service_requisition_no`, `service_status`, `section_id`, `data_state`, `created_id`, `created_at`, `updated_at`) VALUES
(1, '000001/SR/IX/2023', '1', 1, 0, 55, '2023-09-29 09:30:58', '2023-09-29 02:30:58'),
(2, '000001/SR/IX/2023', '2', 9, 0, 55, '2023-09-29 09:50:03', '2023-09-29 02:50:03'),
(3, '000001/SR/IX/2023', '3', 9, 0, 55, '2023-09-29 10:02:16', '2023-09-29 03:02:16'),
(4, '000001/SR/IX/2023', '4', 9, 0, 55, '2023-09-29 10:06:19', '2023-09-29 03:06:19'),
(5, '000001/SR/IX/2023', '11', 11, 0, 55, '2023-09-29 10:08:48', '2023-09-29 03:08:48');

-- --------------------------------------------------------

--
-- Struktur dari tabel `trans_service_requisition`
--

CREATE TABLE `trans_service_requisition` (
  `service_requisition_id` int(10) NOT NULL,
  `service_requisition_no` varchar(250) DEFAULT NULL,
  `service_requisition_name` varchar(250) DEFAULT NULL,
  `service_requisition_phone` varchar(250) DEFAULT NULL,
  `service_requisition_nik` varchar(55) DEFAULT NULL,
  `service_requisition_address` text DEFAULT NULL,
  `service_id` int(10) DEFAULT NULL,
  `kecamatan_id` int(10) DEFAULT NULL,
  `kelurahan_id` int(10) DEFAULT NULL,
  `service_requisition_token` varchar(250) DEFAULT '',
  `service_requisition_token_edit` varchar(250) DEFAULT '',
  `service_requisition_status` int(1) DEFAULT 0 COMMENT '0 = belum di assign, 1 = sudah di assign, 2 = perlu susulan file, 3 = disetujui bagian disposisi, 4 = disetujui reviewer, 5 = ditolak review',
  `delete_remark` varchar(250) DEFAULT '',
  `data_state` int(1) DEFAULT 0,
  `created_id` int(10) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_id` int(10) DEFAULT 0,
  `updated_at` datetime DEFAULT NULL,
  `deleted_id` int(10) DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data untuk tabel `trans_service_requisition`
--

INSERT INTO `trans_service_requisition` (`service_requisition_id`, `service_requisition_no`, `service_requisition_name`, `service_requisition_phone`, `service_requisition_nik`, `service_requisition_address`, `service_id`, `kecamatan_id`, `kelurahan_id`, `service_requisition_token`, `service_requisition_token_edit`, `service_requisition_status`, `delete_remark`, `data_state`, `created_id`, `created_at`, `updated_id`, `updated_at`, `deleted_id`, `deleted_at`, `last_update`) VALUES
(1, '000001/SR/IX/2023', 'Tes', '08812792729', '1000010110', 'tess', 1, 2, 17, '25cecb92ba18ae9a6f4895df4e4ca996', 'a7bce7442ae6064269cfadb35e89f0fc', 7, '', 0, 55, '2023-09-29 02:30:57', 55, '2023-09-29 10:08:48', 0, NULL, '2023-09-29 02:30:57');

--
-- Trigger `trans_service_requisition`
--
DELIMITER $$
CREATE TRIGGER `insert_trans_service_requisition` BEFORE INSERT ON `trans_service_requisition` FOR EACH ROW BEGIN
	DECLARE year_period 				VARCHAR(20);
	DECLARE month_period 				VARCHAR(20);
	DECLARE PERIOD 					VARCHAR(20);
	DECLARE tPeriod					INT;
	DECLARE nTransServiceRequisitionNo		VARCHAR(20);
	DECLARE monthPeriod				VARCHAR(20);
	DECLARE lenTransServiceRequisitionNo		DECIMAL(10);
	
	SET year_period = (YEAR(new.created_at));
	
	SET month_period = (SELECT RIGHT(CONCAT('0', MONTH(new.created_at)), 2));
	
	IF (month_period) = '01' THEN 
		SET monthPeriod = 'I';
	END IF;
	
	IF (month_period) = '02' THEN 
		SET monthPeriod = 'II';
	END IF;
	
	IF (month_period) = '03' THEN 
		SET monthPeriod = 'III';
	END IF;
	
	IF (month_period) = '04' THEN 
		SET monthPeriod = 'IV';
	END IF;	
	
	IF (month_period) = '05' THEN 
		SET monthPeriod = 'V';
	END IF;
	
	IF (month_period) = '06' THEN 
		SET monthPeriod = 'VI';
	END IF;
	
	IF (month_period) = '07' THEN 
		SET monthPeriod = 'VII';
	END IF;
	
	IF (month_period) = '08' THEN 
		SET monthPeriod = 'VIII';
	END IF;
	
	IF (month_period) = '09' THEN 
		SET monthPeriod = 'IX';
	END IF;
	
	IF (month_period) = '10' THEN 
		SET monthPeriod = 'X';
	END IF;
	
	IF (month_period) = '11' THEN 
		SET monthPeriod = 'XI';
	END IF;
	
	IF (month_period) = '12' THEN 
		SET monthPeriod = 'XII';
	END IF;
		
	SET PERIOD = (SELECT LEFT(TRIM(service_requisition_no), 6) 
			FROM trans_service_requisition
			WHERE RIGHT(TRIM(service_requisition_no), 4) = year_period
			ORDER BY service_requisition_id DESC 
			LIMIT 1);
		
	IF (PERIOD IS NULL ) THEN 
		SET PERIOD = "000000";
	END IF;
	
	SET tPeriod = CAST(PERIOD AS DECIMAL(10));
	
	SET tPeriod = tPeriod + 1;
	
	SET PERIOD = RIGHT(CONCAT('000000', TRIM(CAST(tPeriod AS CHAR(6)))), 6);
	
	SET nTransServiceRequisitionNo = CONCAT(PERIOD, '/SR/', monthPeriod, '/', year_period);
		
	SET new.service_requisition_no = nTransServiceRequisitionNo;
    END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `trans_service_requisition_parameter`
--

CREATE TABLE `trans_service_requisition_parameter` (
  `service_requisition_parameter_id` int(10) NOT NULL,
  `service_requisition_id` int(10) DEFAULT NULL,
  `service_parameter_id` int(10) DEFAULT NULL,
  `service_requisition_parameter_value` text DEFAULT NULL,
  `service_requisition_parameter_token` varchar(250) DEFAULT '',
  `service_requisition_parameter_token_edit` varchar(250) DEFAULT '',
  `data_state` int(1) DEFAULT 0,
  `created_id` int(10) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_id` int(10) DEFAULT 0,
  `updated_at` datetime DEFAULT NULL,
  `deleted_id` int(10) DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data untuk tabel `trans_service_requisition_parameter`
--

INSERT INTO `trans_service_requisition_parameter` (`service_requisition_parameter_id`, `service_requisition_id`, `service_parameter_id`, `service_requisition_parameter_value`, `service_requisition_parameter_token`, `service_requisition_parameter_token_edit`, `data_state`, `created_id`, `created_at`, `updated_id`, `updated_at`, `deleted_id`, `deleted_at`, `last_update`) VALUES
(1, 1, 3, 'test', '25cecb92ba18ae9a6f4895df4e4ca9963', '', 0, 0, '2023-09-29 09:30:57', 55, '2023-09-29 09:44:21', 0, NULL, '2023-09-29 02:30:57'),
(2, 1, 99, 'set', '25cecb92ba18ae9a6f4895df4e4ca99699', '', 0, 0, '2023-09-29 09:30:57', 55, '2023-09-29 09:44:21', 0, NULL, '2023-09-29 02:30:57'),
(3, 1, 100, '3', '25cecb92ba18ae9a6f4895df4e4ca996100', '', 0, 0, '2023-09-29 09:30:57', 55, '2023-09-29 09:44:21', 0, NULL, '2023-09-29 02:30:57'),
(4, 1, 101, 'asd', '25cecb92ba18ae9a6f4895df4e4ca996101', '', 0, 0, '2023-09-29 09:30:57', 55, '2023-09-29 09:44:21', 0, NULL, '2023-09-29 02:30:57'),
(5, 1, 102, 'xzc', '25cecb92ba18ae9a6f4895df4e4ca996102', '', 0, 0, '2023-09-29 09:30:58', 55, '2023-09-29 09:44:21', 0, NULL, '2023-09-29 02:30:58'),
(6, 1, 103, 'xc', '25cecb92ba18ae9a6f4895df4e4ca996103', '', 0, 0, '2023-09-29 09:30:58', 55, '2023-09-29 09:44:21', 0, NULL, '2023-09-29 02:30:58'),
(7, 1, 104, 'asd', '25cecb92ba18ae9a6f4895df4e4ca996104', '', 0, 0, '2023-09-29 09:30:58', 55, '2023-09-29 09:44:21', 0, NULL, '2023-09-29 02:30:58'),
(8, 1, 105, 'xz', '25cecb92ba18ae9a6f4895df4e4ca996105', '', 0, 0, '2023-09-29 09:30:58', 55, '2023-09-29 09:44:21', 0, NULL, '2023-09-29 02:30:58');

-- --------------------------------------------------------

--
-- Struktur dari tabel `trans_service_requisition_term`
--

CREATE TABLE `trans_service_requisition_term` (
  `service_requisition_term_id` int(10) NOT NULL,
  `service_requisition_id` int(10) DEFAULT NULL,
  `service_term_id` int(10) DEFAULT NULL,
  `service_requisition_term_status` int(1) DEFAULT 0,
  `service_requisition_term_value` text DEFAULT NULL,
  `service_requisition_term_token` varchar(250) DEFAULT '',
  `service_requisition_term_token_edit` varchar(250) DEFAULT '',
  `data_state` int(1) DEFAULT 0,
  `created_id` int(10) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_id` int(10) DEFAULT 0,
  `updated_at` datetime DEFAULT NULL,
  `deleted_id` int(10) DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data untuk tabel `trans_service_requisition_term`
--

INSERT INTO `trans_service_requisition_term` (`service_requisition_term_id`, `service_requisition_id`, `service_term_id`, `service_requisition_term_status`, `service_requisition_term_value`, `service_requisition_term_token`, `service_requisition_term_token_edit`, `data_state`, `created_id`, `created_at`, `updated_id`, `updated_at`, `deleted_id`, `deleted_at`, `last_update`) VALUES
(1, 1, 1, 1, 'alien-1579687978_1695954657.jpg', '25cecb92ba18ae9a6f4895df4e4ca9961', '', 0, 0, '2023-09-29 09:30:57', 55, '2023-09-29 09:44:21', 0, NULL, '2023-09-29 02:30:57');

-- --------------------------------------------------------

--
-- Struktur dari tabel `trans_service_zis`
--

CREATE TABLE `trans_service_zis` (
  `service_zis_id` bigint(20) NOT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `trans_service_zis`
--

INSERT INTO `trans_service_zis` (`service_zis_id`, `service_zis_type`, `service_zis_category`, `service_zis_date`, `service_zis_name`, `service_zis_address`, `kelurahan_id`, `kecamatan_id`, `service_zis_phone`, `service_zis_remark`, `data_state`, `created_id`, `created_at`, `updated_at`) VALUES
(17, '2', '1', '2023-10-02', 'Sayyid', 'fdsjflaj', NULL, NULL, '32457980375', NULL, 1, 55, '2023-10-02 01:36:38', '2023-10-02 02:15:51'),
(20, '2', '2', '2023-10-02', 'Dafa', 'Solo', 9, 1, '085602678871', NULL, 0, 55, '2023-10-02 02:49:03', '2023-10-03 20:24:48'),
(21, '2', '1', '2023-10-03', 'Sayyid', 'Jebres', 20, 2, '083145540378', NULL, 0, NULL, '2023-10-02 23:27:24', '2023-10-03 20:17:02'),
(22, '1', '1', '2023-10-03', 'Baznas Solo', 'Surakarta', 34, 3, '083745141', NULL, 1, NULL, '2023-10-02 23:29:26', '2023-10-11 02:49:07'),
(23, '2', '1', '2023-10-04', 'Arka vincent', 'Kauman,surakarta', 41, 4, '932759482765', NULL, 1, 55, '2023-10-03 19:28:25', '2023-10-11 02:43:02'),
(24, '2', '3', '2023-10-04', 'bu ani', 'Banjarsari', 2, 1, '247981275', NULL, 1, 55, '2023-10-03 20:21:44', '2023-10-11 02:42:58');

-- --------------------------------------------------------

--
-- Struktur dari tabel `trans_service_zis_item`
--

CREATE TABLE `trans_service_zis_item` (
  `service_zis_item_id` bigint(20) NOT NULL,
  `service_zis_id` bigint(20) DEFAULT NULL,
  `service_zis_item_type` varchar(255) DEFAULT NULL,
  `service_zis_item_amount` decimal(20,2) DEFAULT NULL,
  `service_zis_item_remark` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `trans_service_zis_item`
--

INSERT INTO `trans_service_zis_item` (`service_zis_item_id`, `service_zis_id`, `service_zis_item_type`, `service_zis_item_amount`, `service_zis_item_remark`, `created_at`, `updated_at`) VALUES
(4, 17, '2', NULL, '10kg', '2023-10-02 01:36:38', '2023-10-02 01:36:38'),
(5, 17, '1', 100000.00, NULL, '2023-10-02 01:36:38', '2023-10-02 01:36:38'),
(6, 20, '1', 100000.00, NULL, '2023-10-02 02:49:03', '2023-10-02 02:49:03'),
(7, 20, '1', 1000000.00, NULL, '2023-10-02 02:49:03', '2023-10-02 02:49:03'),
(8, 21, '2', NULL, '100 kg', '2023-10-02 23:27:24', '2023-10-02 23:27:24'),
(9, 21, '1', 1000000.00, NULL, '2023-10-02 23:27:24', '2023-10-02 23:27:24'),
(10, 22, '2', NULL, '100 kg', '2023-10-02 23:29:26', '2023-10-02 23:29:26'),
(11, 23, '2', NULL, '100kg', '2023-10-03 19:28:25', '2023-10-03 19:28:25'),
(12, 24, '1', 1000000.00, NULL, '2023-10-03 20:21:44', '2023-10-03 20:21:44');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `ci_sessions`
--
ALTER TABLE `ci_sessions`
  ADD KEY `ci_sessions_timestamp` (`timestamp`);

--
-- Indeks untuk tabel `core_kecamatan`
--
ALTER TABLE `core_kecamatan`
  ADD PRIMARY KEY (`kecamatan_id`),
  ADD KEY `data_state` (`data_state`),
  ADD KEY `created_id` (`created_id`),
  ADD KEY `section_token` (`kecamatan_token`);

--
-- Indeks untuk tabel `core_kelurahan`
--
ALTER TABLE `core_kelurahan`
  ADD PRIMARY KEY (`kelurahan_id`),
  ADD KEY `data_state` (`data_state`),
  ADD KEY `created_id` (`created_id`),
  ADD KEY `section_token` (`kelurahan_token`);

--
-- Indeks untuk tabel `core_messages`
--
ALTER TABLE `core_messages`
  ADD PRIMARY KEY (`messages_id`),
  ADD KEY `data_state` (`data_state`),
  ADD KEY `created_id` (`created_id`),
  ADD KEY `section_token` (`messages_token`);

--
-- Indeks untuk tabel `core_section`
--
ALTER TABLE `core_section`
  ADD PRIMARY KEY (`section_id`),
  ADD KEY `data_state` (`data_state`),
  ADD KEY `created_id` (`created_id`),
  ADD KEY `section_token` (`section_token`);

--
-- Indeks untuk tabel `core_service`
--
ALTER TABLE `core_service`
  ADD PRIMARY KEY (`service_id`),
  ADD KEY `data_state` (`data_state`),
  ADD KEY `created_id` (`created_id`),
  ADD KEY `service_token` (`service_token`),
  ADD KEY `service_token_edit` (`service_token_edit`);

--
-- Indeks untuk tabel `core_service_general_parameter`
--
ALTER TABLE `core_service_general_parameter`
  ADD PRIMARY KEY (`service_general_parameter_id`),
  ADD KEY `service_parameter_token` (`service_general_parameter_token`),
  ADD KEY `service_parameter_token_edit` (`service_general_parameter_token_edit`),
  ADD KEY `data_state` (`data_state`),
  ADD KEY `created_id` (`created_id`);

--
-- Indeks untuk tabel `core_service_general_priority`
--
ALTER TABLE `core_service_general_priority`
  ADD PRIMARY KEY (`service_general_priority_id`),
  ADD KEY `data_state` (`data_state`),
  ADD KEY `created_id` (`created_id`),
  ADD KEY `service_token` (`service_general_priority_token`),
  ADD KEY `service_token_edit` (`service_general_priority_token_edit`);

--
-- Indeks untuk tabel `core_service_parameter`
--
ALTER TABLE `core_service_parameter`
  ADD PRIMARY KEY (`service_parameter_id`),
  ADD KEY `service_parameter_token` (`service_parameter_token`),
  ADD KEY `service_parameter_token_edit` (`service_parameter_token_edit`),
  ADD KEY `data_state` (`data_state`),
  ADD KEY `created_id` (`created_id`),
  ADD KEY `FK_core_service_parameter_service_id` (`service_id`);

--
-- Indeks untuk tabel `core_service_status`
--
ALTER TABLE `core_service_status`
  ADD PRIMARY KEY (`service_status_id`),
  ADD KEY `data_state` (`data_state`),
  ADD KEY `created_id` (`created_id`),
  ADD KEY `section_token` (`service_status_token`);

--
-- Indeks untuk tabel `core_service_term`
--
ALTER TABLE `core_service_term`
  ADD PRIMARY KEY (`service_term_id`),
  ADD KEY `service_term_token` (`service_term_token`),
  ADD KEY `data_state` (`data_state`),
  ADD KEY `created_id` (`created_id`),
  ADD KEY `FK_core_service_term_service_id` (`service_id`),
  ADD KEY `service_term_token_edit` (`service_term_token_edit`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `mustahik_worksheet`
--
ALTER TABLE `mustahik_worksheet`
  ADD PRIMARY KEY (`worksheet_id`),
  ADD KEY `data_state` (`data_state`),
  ADD KEY `created_id` (`created_id`),
  ADD KEY `service_token` (`worksheet_name`),
  ADD KEY `service_token_edit` (`worksheet_token_edit`);

--
-- Indeks untuk tabel `mustahik_worksheet_item`
--
ALTER TABLE `mustahik_worksheet_item`
  ADD PRIMARY KEY (`worksheet_item_id`),
  ADD KEY `data_state` (`data_state`),
  ADD KEY `created_id` (`created_id`),
  ADD KEY `service_token_edit` (`worksheet_item_token_edit`);

--
-- Indeks untuk tabel `mustahik_worksheet_requisition`
--
ALTER TABLE `mustahik_worksheet_requisition`
  ADD PRIMARY KEY (`worksheet_requisition_id`),
  ADD KEY `data_state` (`data_state`),
  ADD KEY `created_id` (`created_id`),
  ADD KEY `service_token_edit` (`worksheet_requisition_token_edit`);

--
-- Indeks untuk tabel `mustahik_worksheet_result`
--
ALTER TABLE `mustahik_worksheet_result`
  ADD PRIMARY KEY (`worksheet_result_id`),
  ADD KEY `data_state` (`data_state`),
  ADD KEY `created_id` (`created_id`),
  ADD KEY `service_token_edit` (`worksheet_result_token_edit`);

--
-- Indeks untuk tabel `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indeks untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indeks untuk tabel `preference_company`
--
ALTER TABLE `preference_company`
  ADD PRIMARY KEY (`company_id`);

--
-- Indeks untuk tabel `p_p_o_b_s`
--
ALTER TABLE `p_p_o_b_s`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `system_activity_log`
--
ALTER TABLE `system_activity_log`
  ADD PRIMARY KEY (`user_log_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `transaction_code` (`transaction_code`),
  ADD KEY `transaction_date` (`transaction_date`);

--
-- Indeks untuk tabel `system_change_log`
--
ALTER TABLE `system_change_log`
  ADD PRIMARY KEY (`change_log_id`);

--
-- Indeks untuk tabel `system_log_user`
--
ALTER TABLE `system_log_user`
  ADD PRIMARY KEY (`user_log_id`),
  ADD KEY `FK_system_log_user` (`username`);

--
-- Indeks untuk tabel `system_menu`
--
ALTER TABLE `system_menu`
  ADD PRIMARY KEY (`id_menu`);

--
-- Indeks untuk tabel `system_menu_mapping`
--
ALTER TABLE `system_menu_mapping`
  ADD PRIMARY KEY (`menu_mapping_id`),
  ADD KEY `FK_system_menu_mapping` (`id_menu`) USING BTREE;

--
-- Indeks untuk tabel `system_user`
--
ALTER TABLE `system_user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `fk_system_user_user_group_id` (`user_group_id`);

--
-- Indeks untuk tabel `system_user_group`
--
ALTER TABLE `system_user_group`
  ADD PRIMARY KEY (`user_group_id`),
  ADD UNIQUE KEY `user_group_level` (`user_group_level`),
  ADD KEY `user_group_token` (`user_group_token`),
  ADD KEY `data_state` (`data_state`),
  ADD KEY `created_id` (`created_id`);

--
-- Indeks untuk tabel `trans_service_disposition`
--
ALTER TABLE `trans_service_disposition`
  ADD PRIMARY KEY (`service_disposition_id`),
  ADD KEY `data_state` (`data_state`),
  ADD KEY `created_id` (`created_id`),
  ADD KEY `service_token` (`service_disposition_token`),
  ADD KEY `service_token_edit` (`service_disposition_token_edit`);

--
-- Indeks untuk tabel `trans_service_disposition_parameter`
--
ALTER TABLE `trans_service_disposition_parameter`
  ADD PRIMARY KEY (`service_disposition_parameter_id`),
  ADD KEY `data_state` (`data_state`),
  ADD KEY `created_id` (`created_id`),
  ADD KEY `service_token` (`service_disposition_parameter_token`),
  ADD KEY `service_token_edit` (`service_disposition_parameter_token_edit`),
  ADD KEY `FK_trans_service_disposition_parameter_service_disposition_id` (`service_disposition_id`);

--
-- Indeks untuk tabel `trans_service_disposition_term`
--
ALTER TABLE `trans_service_disposition_term`
  ADD PRIMARY KEY (`service_disposition_term_id`),
  ADD KEY `data_state` (`data_state`),
  ADD KEY `created_id` (`created_id`),
  ADD KEY `service_token` (`service_disposition_term_token`),
  ADD KEY `service_token_edit` (`service_disposition_term_token_edit`),
  ADD KEY `FK_trans_service_disposition_term_service_disposition_id` (`service_disposition_id`);

--
-- Indeks untuk tabel `trans_service_document_requisition`
--
ALTER TABLE `trans_service_document_requisition`
  ADD PRIMARY KEY (`service_document_requisition_id`),
  ADD KEY `data_state` (`data_state`),
  ADD KEY `created_id` (`created_id`),
  ADD KEY `service_token` (`service_document_requisition_token`);

--
-- Indeks untuk tabel `trans_service_general`
--
ALTER TABLE `trans_service_general`
  ADD PRIMARY KEY (`service_general_id`),
  ADD KEY `data_state` (`data_state`),
  ADD KEY `created_id` (`created_id`),
  ADD KEY `service_token` (`service_general_token`),
  ADD KEY `service_token_edit` (`service_general_token_edit`);

--
-- Indeks untuk tabel `trans_service_general_parameter`
--
ALTER TABLE `trans_service_general_parameter`
  ADD PRIMARY KEY (`service_general_parameter_id`),
  ADD KEY `data_state` (`data_state`),
  ADD KEY `created_id` (`created_id`),
  ADD KEY `service_token` (`service_general_parameter_token`),
  ADD KEY `service_token_edit` (`service_general_parameter_token_edit`);

--
-- Indeks untuk tabel `trans_service_log`
--
ALTER TABLE `trans_service_log`
  ADD PRIMARY KEY (`service_log_id`);

--
-- Indeks untuk tabel `trans_service_requisition`
--
ALTER TABLE `trans_service_requisition`
  ADD PRIMARY KEY (`service_requisition_id`),
  ADD KEY `data_state` (`data_state`),
  ADD KEY `created_id` (`created_id`),
  ADD KEY `service_token` (`service_requisition_token`),
  ADD KEY `service_token_edit` (`service_requisition_token_edit`);

--
-- Indeks untuk tabel `trans_service_requisition_parameter`
--
ALTER TABLE `trans_service_requisition_parameter`
  ADD PRIMARY KEY (`service_requisition_parameter_id`),
  ADD KEY `data_state` (`data_state`),
  ADD KEY `created_id` (`created_id`),
  ADD KEY `service_token` (`service_requisition_parameter_token`),
  ADD KEY `service_token_edit` (`service_requisition_parameter_token_edit`);

--
-- Indeks untuk tabel `trans_service_requisition_term`
--
ALTER TABLE `trans_service_requisition_term`
  ADD PRIMARY KEY (`service_requisition_term_id`),
  ADD KEY `data_state` (`data_state`),
  ADD KEY `created_id` (`created_id`),
  ADD KEY `service_token` (`service_requisition_term_token`),
  ADD KEY `service_token_edit` (`service_requisition_term_token_edit`);

--
-- Indeks untuk tabel `trans_service_zis`
--
ALTER TABLE `trans_service_zis`
  ADD KEY `service_zis_id` (`service_zis_id`);

--
-- Indeks untuk tabel `trans_service_zis_item`
--
ALTER TABLE `trans_service_zis_item`
  ADD KEY `trans_service_zis_item_id` (`service_zis_item_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `core_kecamatan`
--
ALTER TABLE `core_kecamatan`
  MODIFY `kecamatan_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `core_kelurahan`
--
ALTER TABLE `core_kelurahan`
  MODIFY `kelurahan_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT untuk tabel `core_messages`
--
ALTER TABLE `core_messages`
  MODIFY `messages_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `core_section`
--
ALTER TABLE `core_section`
  MODIFY `section_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `core_service`
--
ALTER TABLE `core_service`
  MODIFY `service_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT untuk tabel `core_service_general_parameter`
--
ALTER TABLE `core_service_general_parameter`
  MODIFY `service_general_parameter_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `core_service_general_priority`
--
ALTER TABLE `core_service_general_priority`
  MODIFY `service_general_priority_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `core_service_parameter`
--
ALTER TABLE `core_service_parameter`
  MODIFY `service_parameter_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;

--
-- AUTO_INCREMENT untuk tabel `core_service_status`
--
ALTER TABLE `core_service_status`
  MODIFY `service_status_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `core_service_term`
--
ALTER TABLE `core_service_term`
  MODIFY `service_term_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `mustahik_worksheet`
--
ALTER TABLE `mustahik_worksheet`
  MODIFY `worksheet_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=153;

--
-- AUTO_INCREMENT untuk tabel `mustahik_worksheet_item`
--
ALTER TABLE `mustahik_worksheet_item`
  MODIFY `worksheet_item_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT untuk tabel `mustahik_worksheet_requisition`
--
ALTER TABLE `mustahik_worksheet_requisition`
  MODIFY `worksheet_requisition_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=206;

--
-- AUTO_INCREMENT untuk tabel `mustahik_worksheet_result`
--
ALTER TABLE `mustahik_worksheet_result`
  MODIFY `worksheet_result_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- AUTO_INCREMENT untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT untuk tabel `preference_company`
--
ALTER TABLE `preference_company`
  MODIFY `company_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `p_p_o_b_s`
--
ALTER TABLE `p_p_o_b_s`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `system_activity_log`
--
ALTER TABLE `system_activity_log`
  MODIFY `user_log_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `system_log_user`
--
ALTER TABLE `system_log_user`
  MODIFY `user_log_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT untuk tabel `system_menu_mapping`
--
ALTER TABLE `system_menu_mapping`
  MODIFY `menu_mapping_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=178;

--
-- AUTO_INCREMENT untuk tabel `system_user`
--
ALTER TABLE `system_user`
  MODIFY `user_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT untuk tabel `system_user_group`
--
ALTER TABLE `system_user_group`
  MODIFY `user_group_id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT untuk tabel `trans_service_disposition`
--
ALTER TABLE `trans_service_disposition`
  MODIFY `service_disposition_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=724;

--
-- AUTO_INCREMENT untuk tabel `trans_service_disposition_parameter`
--
ALTER TABLE `trans_service_disposition_parameter`
  MODIFY `service_disposition_parameter_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `trans_service_disposition_term`
--
ALTER TABLE `trans_service_disposition_term`
  MODIFY `service_disposition_term_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `trans_service_document_requisition`
--
ALTER TABLE `trans_service_document_requisition`
  MODIFY `service_document_requisition_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `trans_service_general`
--
ALTER TABLE `trans_service_general`
  MODIFY `service_general_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `trans_service_general_parameter`
--
ALTER TABLE `trans_service_general_parameter`
  MODIFY `service_general_parameter_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `trans_service_log`
--
ALTER TABLE `trans_service_log`
  MODIFY `service_log_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `trans_service_requisition`
--
ALTER TABLE `trans_service_requisition`
  MODIFY `service_requisition_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `trans_service_requisition_parameter`
--
ALTER TABLE `trans_service_requisition_parameter`
  MODIFY `service_requisition_parameter_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `trans_service_requisition_term`
--
ALTER TABLE `trans_service_requisition_term`
  MODIFY `service_requisition_term_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `trans_service_zis`
--
ALTER TABLE `trans_service_zis`
  MODIFY `service_zis_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT untuk tabel `trans_service_zis_item`
--
ALTER TABLE `trans_service_zis_item`
  MODIFY `service_zis_item_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `core_service_parameter`
--
ALTER TABLE `core_service_parameter`
  ADD CONSTRAINT `FK_core_service_parameter_service_id` FOREIGN KEY (`service_id`) REFERENCES `core_service` (`service_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `core_service_term`
--
ALTER TABLE `core_service_term`
  ADD CONSTRAINT `FK_core_service_term_service_id` FOREIGN KEY (`service_id`) REFERENCES `core_service` (`service_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `system_menu_mapping`
--
ALTER TABLE `system_menu_mapping`
  ADD CONSTRAINT `FK_system_menu_mapping_id_menu` FOREIGN KEY (`id_menu`) REFERENCES `system_menu` (`id_menu`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `system_user`
--
ALTER TABLE `system_user`
  ADD CONSTRAINT `fk_system_user_user_group_id` FOREIGN KEY (`user_group_id`) REFERENCES `system_user_group` (`user_group_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `trans_service_disposition_parameter`
--
ALTER TABLE `trans_service_disposition_parameter`
  ADD CONSTRAINT `FK_trans_service_disposition_parameter_service_disposition_id` FOREIGN KEY (`service_disposition_id`) REFERENCES `trans_service_disposition` (`service_disposition_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `trans_service_disposition_term`
--
ALTER TABLE `trans_service_disposition_term`
  ADD CONSTRAINT `FK_trans_service_disposition_term_service_disposition_id` FOREIGN KEY (`service_disposition_id`) REFERENCES `trans_service_disposition` (`service_disposition_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
