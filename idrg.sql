/*
SQLyog Community v13.2.1 (64 bit)
MySQL - 10.11.11-MariaDB : Database - idrg
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`idrg` /*!40100 DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci */;

USE `idrg`;

/*Table structure for table `clinical_data` */

DROP TABLE IF EXISTS `clinical_data`;

CREATE TABLE `clinical_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kunjungan_id` int(11) NOT NULL COMMENT 'ID Kunjungan Pasien',
  `sistole` int(11) DEFAULT NULL COMMENT 'Tekanan Darah Sistole (mmHg)',
  `diastole` int(11) DEFAULT NULL COMMENT 'Tekanan Darah Diastole (mmHg)',
  `heart_rate` int(11) DEFAULT NULL COMMENT 'Detak Jantung (bpm)',
  `temperature` decimal(4,1) DEFAULT NULL COMMENT 'Suhu Tubuh (°C)',
  `oxygen_saturation` int(11) DEFAULT NULL COMMENT 'Saturasi Oksigen (%)',
  `respiratory_rate` int(11) DEFAULT NULL COMMENT 'Laju Pernapasan (per menit)',
  `blood_glucose` int(11) DEFAULT NULL COMMENT 'Gula Darah (mg/dL)',
  `notes` text DEFAULT NULL COMMENT 'Catatan Klinis',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `kunjungan_id` (`kunjungan_id`),
  CONSTRAINT `clinical_data_ibfk_1` FOREIGN KEY (`kunjungan_id`) REFERENCES `kunjungan_pasien` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `cob` */

DROP TABLE IF EXISTS `cob`;

CREATE TABLE `cob` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cob_cd` varchar(10) NOT NULL COMMENT 'Kode COB',
  `cob_nm` varchar(255) NOT NULL COMMENT 'Nama COB',
  `description` text DEFAULT NULL COMMENT 'Deskripsi COB',
  `status` enum('active','inactive') DEFAULT 'active' COMMENT 'Status COB',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `cob_cd` (`cob_cd`),
  KEY `idx_cob_cd` (`cob_cd`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `delivery` */

DROP TABLE IF EXISTS `delivery`;

CREATE TABLE `delivery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kunjungan_id` int(11) NOT NULL,
  `delivery_sequence` int(11) NOT NULL COMMENT 'Urutan Kelahiran',
  `delivery_method` enum('vaginal','sc') NOT NULL COMMENT 'Metode Persalinan',
  `delivery_dttm` datetime NOT NULL COMMENT 'Waktu Kelahiran',
  `letak_janin` enum('kepala','sungsang','lintang') DEFAULT NULL COMMENT 'Letak Janin',
  `kondisi` enum('livebirth','stillbirth') DEFAULT 'livebirth' COMMENT 'Kondisi Bayi',
  `use_manual` tinyint(1) DEFAULT 0 COMMENT 'Bantuan Manual',
  `use_forcep` tinyint(1) DEFAULT 0 COMMENT 'Penggunaan Forcep',
  `use_vacuum` tinyint(1) DEFAULT 0 COMMENT 'Penggunaan Vacuum',
  `shk_spesimen_ambil` enum('ya','tidak') DEFAULT NULL COMMENT 'SHK Spesimen Ambil',
  `shk_lokasi` enum('tumit','vena') DEFAULT NULL COMMENT 'SHK Lokasi',
  `shk_alasan` enum('tidak-dapat','akses-sulit') DEFAULT NULL COMMENT 'SHK Alasan',
  `shk_spesimen_dttm` datetime DEFAULT NULL COMMENT 'SHK Waktu Pengambilan',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_kunjungan_id` (`kunjungan_id`),
  KEY `idx_delivery_sequence` (`delivery_sequence`),
  CONSTRAINT `delivery_ibfk_1` FOREIGN KEY (`kunjungan_id`) REFERENCES `kunjungan_pasien` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `detail_tarif` */

DROP TABLE IF EXISTS `detail_tarif`;

CREATE TABLE `detail_tarif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kunjungan_id` int(11) NOT NULL,
  `prosedur_non_bedah` decimal(15,2) DEFAULT 0.00 COMMENT 'Prosedur Non Bedah',
  `prosedur_bedah` decimal(15,2) DEFAULT 0.00 COMMENT 'Prosedur Bedah',
  `konsultasi` decimal(15,2) DEFAULT 0.00 COMMENT 'Konsultasi',
  `tenaga_ahli` decimal(15,2) DEFAULT 0.00 COMMENT 'Tenaga Ahli',
  `keperawatan` decimal(15,2) DEFAULT 0.00 COMMENT 'Keperawatan',
  `penunjang` decimal(15,2) DEFAULT 0.00 COMMENT 'Penunjang',
  `radiologi` decimal(15,2) DEFAULT 0.00 COMMENT 'Radiologi',
  `laboratorium` decimal(15,2) DEFAULT 0.00 COMMENT 'Laboratorium',
  `pelayanan_darah` decimal(15,2) DEFAULT 0.00 COMMENT 'Pelayanan Darah',
  `rehabilitasi` decimal(15,2) DEFAULT 0.00 COMMENT 'Rehabilitasi',
  `kamar` decimal(15,2) DEFAULT 0.00 COMMENT 'Kamar',
  `rawat_intensif` decimal(15,2) DEFAULT 0.00 COMMENT 'Rawat Intensif',
  `obat` decimal(15,2) DEFAULT 0.00 COMMENT 'Obat',
  `obat_kronis` decimal(15,2) DEFAULT 0.00 COMMENT 'Obat Kronis',
  `obat_kemoterapi` decimal(15,2) DEFAULT 0.00 COMMENT 'Obat Kemoterapi',
  `alkes` decimal(15,2) DEFAULT 0.00 COMMENT 'Alat Kesehatan',
  `bmhp` decimal(15,2) DEFAULT 0.00 COMMENT 'BMHP',
  `sewa_alat` decimal(15,2) DEFAULT 0.00 COMMENT 'Sewa Alat',
  `total_tarif` decimal(15,2) DEFAULT 0.00 COMMENT 'Total Tarif RS',
  `kategori_tarif` varchar(50) DEFAULT NULL COMMENT 'Kategori Tarif',
  `nama_layanan` varchar(100) DEFAULT NULL COMMENT 'Nama Layanan',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_kunjungan_id` (`kunjungan_id`),
  KEY `idx_kategori_tarif` (`kategori_tarif`),
  CONSTRAINT `detail_tarif_ibfk_1` FOREIGN KEY (`kunjungan_id`) REFERENCES `kunjungan_pasien` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `diagnosis_details` */

DROP TABLE IF EXISTS `diagnosis_details`;

CREATE TABLE `diagnosis_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kunjungan_id` int(11) NOT NULL COMMENT 'ID Kunjungan Pasien',
  `icd_code_id` int(11) NOT NULL COMMENT 'ID dari tabel idr_codes',
  `diagnosis_order` int(11) NOT NULL COMMENT 'Urutan Diagnosa (1=Primary, 2=Secondary, dst)',
  `diagnosis_type` enum('primary','secondary') NOT NULL DEFAULT 'secondary' COMMENT 'Jenis Diagnosa',
  `icd_code` varchar(20) NOT NULL COMMENT 'Kode ICD-10',
  `icd_description` text NOT NULL COMMENT 'Deskripsi ICD-10',
  `validcode` tinyint(1) DEFAULT 1 COMMENT 'Status Valid Kode',
  `accpdx` char(1) DEFAULT 'Y' COMMENT 'Status ACCPDX (Y/N)',
  `asterisk` tinyint(1) DEFAULT 0 COMMENT 'Status Asterisk',
  `im` tinyint(1) DEFAULT 0 COMMENT 'Status IM',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_kunjungan_id` (`kunjungan_id`),
  KEY `idx_icd_code_id` (`icd_code_id`),
  KEY `idx_diagnosis_order` (`diagnosis_order`),
  KEY `idx_diagnosis_type` (`diagnosis_type`),
  KEY `idx_icd_code` (`icd_code`),
  CONSTRAINT `diagnosis_details_ibfk_1` FOREIGN KEY (`kunjungan_id`) REFERENCES `kunjungan_pasien` (`id`) ON DELETE CASCADE,
  CONSTRAINT `diagnosis_details_ibfk_2` FOREIGN KEY (`icd_code_id`) REFERENCES `idr_codes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `encryption_keys` */

DROP TABLE IF EXISTS `encryption_keys`;

CREATE TABLE `encryption_keys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key_name` varchar(100) NOT NULL COMMENT 'Nama Key',
  `key_value` text NOT NULL COMMENT 'Nilai Key (Hex)',
  `key_type` enum('simrs','bpjs','kemkes') DEFAULT 'simrs' COMMENT 'Tipe Key',
  `description` text DEFAULT NULL COMMENT 'Deskripsi Key',
  `is_active` tinyint(1) DEFAULT 1 COMMENT 'Status Aktif',
  `expires_at` datetime DEFAULT NULL COMMENT 'Waktu Kadaluarsa',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `key_name` (`key_name`),
  KEY `idx_key_name` (`key_name`),
  KEY `idx_key_type` (`key_type`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `episode_covid19` */

DROP TABLE IF EXISTS `episode_covid19`;

CREATE TABLE `episode_covid19` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kunjungan_id` int(11) NOT NULL,
  `episode_id` int(11) NOT NULL COMMENT 'ID Episode',
  `episode_class_cd` int(11) NOT NULL COMMENT 'Kode Jenis Ruangan',
  `episode_class_nm` varchar(100) DEFAULT NULL COMMENT 'Nama Jenis Ruangan',
  `los` int(11) NOT NULL COMMENT 'Lama Rawat (hari)',
  `tariff` decimal(15,2) DEFAULT 0.00 COMMENT 'Tarif Episode',
  `order_no` int(11) DEFAULT 0 COMMENT 'Urutan Episode',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_kunjungan_id` (`kunjungan_id`),
  KEY `idx_episode_id` (`episode_id`),
  CONSTRAINT `episode_covid19_ibfk_1` FOREIGN KEY (`kunjungan_id`) REFERENCES `kunjungan_pasien` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `file_pendukung` */

DROP TABLE IF EXISTS `file_pendukung`;

CREATE TABLE `file_pendukung` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kunjungan_id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL COMMENT 'ID File Urut',
  `file_name` varchar(255) NOT NULL COMMENT 'Nama File',
  `file_type` varchar(100) DEFAULT NULL COMMENT 'MIME Type',
  `file_size` bigint(20) DEFAULT NULL COMMENT 'Ukuran File (bytes)',
  `file_class` enum('resume_medis','ruang_rawat','laboratorium','radiologi','penunjang_lain','resep_obat','tagihan','kartu_identitas','dokumen_kipi','bebas_biaya','surat_kematian','lain_lain') NOT NULL COMMENT 'Klasifikasi File',
  `file_data` longtext DEFAULT NULL COMMENT 'File dalam Base64',
  `upload_dc_bpjs` tinyint(1) DEFAULT 0 COMMENT 'Status Upload ke DC BPJS',
  `upload_dc_bpjs_response` text DEFAULT NULL COMMENT 'Response Upload BPJS',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_kunjungan_id` (`kunjungan_id`),
  KEY `idx_file_id` (`file_id`),
  KEY `idx_file_class` (`file_class`),
  CONSTRAINT `file_pendukung_ibfk_1` FOREIGN KEY (`kunjungan_id`) REFERENCES `kunjungan_pasien` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `grouping_result` */

DROP TABLE IF EXISTS `grouping_result`;

CREATE TABLE `grouping_result` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kunjungan_id` int(11) NOT NULL,
  `stage` int(11) NOT NULL COMMENT 'Stage Grouping (1 atau 2)',
  `cbg_code` varchar(20) DEFAULT NULL COMMENT 'Kode CBG',
  `cbg_description` text DEFAULT NULL COMMENT 'Deskripsi CBG',
  `cbg_tariff` decimal(15,2) DEFAULT 0.00 COMMENT 'Tarif CBG',
  `sub_acute_code` varchar(20) DEFAULT NULL COMMENT 'Kode Sub Acute',
  `sub_acute_description` text DEFAULT NULL COMMENT 'Deskripsi Sub Acute',
  `sub_acute_tariff` decimal(15,2) DEFAULT 0.00 COMMENT 'Tarif Sub Acute',
  `chronic_code` varchar(20) DEFAULT NULL COMMENT 'Kode Chronic',
  `chronic_description` text DEFAULT NULL COMMENT 'Deskripsi Chronic',
  `chronic_tariff` decimal(15,2) DEFAULT 0.00 COMMENT 'Tarif Chronic',
  `kelas` varchar(20) DEFAULT NULL COMMENT 'Kelas Rawat',
  `add_payment_amt` decimal(15,2) DEFAULT 0.00 COMMENT 'Tambahan Biaya',
  `inacbg_version` varchar(50) DEFAULT NULL COMMENT 'Versi INA-CBG',
  `special_cmg` text DEFAULT NULL COMMENT 'Special CMG (delimiter #)',
  `mdc_number` varchar(10) DEFAULT NULL COMMENT 'MDC Number',
  `mdc_description` text DEFAULT NULL COMMENT 'MDC Description',
  `drg_code` varchar(20) DEFAULT NULL COMMENT 'DRG Code',
  `drg_description` text DEFAULT NULL COMMENT 'DRG Description',
  `tarif_kelas_1` decimal(15,2) DEFAULT 0.00 COMMENT 'Tarif Kelas 1',
  `tarif_kelas_2` decimal(15,2) DEFAULT 0.00 COMMENT 'Tarif Kelas 2',
  `tarif_kelas_3` decimal(15,2) DEFAULT 0.00 COMMENT 'Tarif Kelas 3',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_kunjungan_id` (`kunjungan_id`),
  KEY `idx_stage` (`stage`),
  KEY `idx_cbg_code` (`cbg_code`),
  CONSTRAINT `grouping_result_ibfk_1` FOREIGN KEY (`kunjungan_id`) REFERENCES `kunjungan_pasien` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `idr_codes` */

DROP TABLE IF EXISTS `idr_codes`;

CREATE TABLE `idr_codes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `code2` varchar(20) NOT NULL,
  `description` text DEFAULT NULL,
  `system` varchar(50) DEFAULT NULL,
  `validcode` tinyint(1) DEFAULT NULL,
  `accpdx` char(1) DEFAULT NULL,
  `asterisk` tinyint(1) DEFAULT NULL,
  `im` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=47421 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Table structure for table `kunjungan_pasien` */

DROP TABLE IF EXISTS `kunjungan_pasien`;

CREATE TABLE `kunjungan_pasien` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nomor_kartu` varchar(20) NOT NULL COMMENT 'Nomor Kartu Peserta JKN',
  `nomor_sep` varchar(30) NOT NULL COMMENT 'Nomor SEP',
  `nomor_rm` varchar(20) NOT NULL COMMENT 'Nomor Rekam Medis',
  `nama_pasien` varchar(255) NOT NULL COMMENT 'Nama Lengkap Pasien',
  `tgl_lahir` datetime NOT NULL COMMENT 'Tanggal Lahir',
  `gender` enum('1','2') NOT NULL COMMENT '1=Laki-laki, 2=Perempuan',
  `jaminan_cara_bayar` enum('JKN','BPJS','UMUM','ASURANSI') DEFAULT 'JKN',
  `payor_id` varchar(10) DEFAULT '3' COMMENT 'ID Jaminan dari E-Klaim',
  `payor_cd` varchar(10) DEFAULT 'JKN' COMMENT 'Kode Jaminan',
  `cob_cd` varchar(10) DEFAULT NULL COMMENT 'Coordination of Benefit',
  `jenis_rawat` enum('1','2','3') DEFAULT '1' COMMENT '1=Inap, 2=Jalan, 3=IGD',
  `kelas_rawat` enum('1','2','3') DEFAULT '3' COMMENT '1=Kelas 1, 2=Kelas 2, 3=Kelas 3',
  `tgl_masuk` datetime NOT NULL COMMENT 'Tanggal Masuk',
  `tgl_pulang` datetime DEFAULT NULL COMMENT 'Tanggal Pulang',
  `los_hari` int(11) DEFAULT 0 COMMENT 'Length of Stay (hari)',
  `los_jam` varchar(10) DEFAULT '0' COMMENT 'Length of Stay (jam)',
  `adl_sub_acute` int(11) DEFAULT 0 COMMENT 'ADL Score Sub Acute (12-60)',
  `adl_chronic` int(11) DEFAULT 0 COMMENT 'ADL Score Chronic (12-60)',
  `icu_indikator` tinyint(1) DEFAULT 0 COMMENT 'Indikator ICU',
  `icu_los` int(11) DEFAULT 0 COMMENT 'Lama Rawat ICU (hari)',
  `ventilator_hour` int(11) DEFAULT 0 COMMENT 'Jam Pemakaian Ventilator',
  `ventilator_use_ind` tinyint(1) DEFAULT 0 COMMENT 'Indikator Penggunaan Ventilator',
  `ventilator_start_dttm` datetime DEFAULT NULL COMMENT 'Waktu Mulai Ventilator',
  `ventilator_stop_dttm` datetime DEFAULT NULL COMMENT 'Waktu Selesai Ventilator',
  `upgrade_class_ind` tinyint(1) DEFAULT 0 COMMENT 'Indikator Naik Kelas',
  `upgrade_class_class` enum('kelas_1','kelas_2','vip','vvip') DEFAULT NULL COMMENT 'Kelas Tujuan',
  `upgrade_class_los` int(11) DEFAULT 0 COMMENT 'Lama Rawat Naik Kelas',
  `upgrade_class_payor` enum('peserta','pemberi_kerja','asuransi_tambahan') DEFAULT NULL COMMENT 'Pembayar Naik Kelas',
  `add_payment_pct` decimal(5,2) DEFAULT 0.00 COMMENT 'Persentase Tambahan Biaya',
  `diagnosa` text DEFAULT NULL COMMENT 'Kode Diagnosa ICD-10 (delimiter #)',
  `procedures` text DEFAULT NULL COMMENT 'Kode Prosedur ICD-9-CM (delimiter #)',
  `diagnosa_inagrouper` text DEFAULT NULL COMMENT 'Kode Diagnosa INA Grouper',
  `procedure_inagrouper` text DEFAULT NULL COMMENT 'Kode Prosedur INA Grouper',
  `berat_lahir_gram` int(11) DEFAULT 0 COMMENT 'Berat Lahir (gram)',
  `sistole` int(11) DEFAULT 0 COMMENT 'Tekanan Darah Sistole',
  `diastole` int(11) DEFAULT 0 COMMENT 'Tekanan Darah Diastole',
  `discharge_status` enum('1','2','3','4','5') DEFAULT '1' COMMENT '1=Dokter, 2=Dirujuk, 3=Sendiri, 4=Meninggal, 5=Lain-lain',
  `covid19_status_cd` enum('1','2','3','4','5') DEFAULT NULL COMMENT '1=ODP, 2=PDP, 3=Terkonfirmasi, 4=Suspek, 5=Probabel',
  `covid19_cc_ind` tinyint(1) DEFAULT 0 COMMENT 'Indikator Comorbidity/Complexity',
  `covid19_rs_darurat_ind` tinyint(1) DEFAULT 0 COMMENT 'Indikator RS Darurat/Lapangan',
  `covid19_co_insidense_ind` tinyint(1) DEFAULT 0 COMMENT 'Indikator Co-Insidense',
  `covid19_no_sep` varchar(30) DEFAULT NULL COMMENT 'Nomor SEP COVID-19 untuk Co-Insidense',
  `lab_asam_laktat` tinyint(1) DEFAULT 1 COMMENT 'Lab Asam Laktat',
  `lab_procalcitonin` tinyint(1) DEFAULT 1 COMMENT 'Lab Procalcitonin',
  `lab_crp` tinyint(1) DEFAULT 1 COMMENT 'Lab CRP',
  `lab_kultur` tinyint(1) DEFAULT 1 COMMENT 'Lab Kultur MO',
  `lab_d_dimer` tinyint(1) DEFAULT 1 COMMENT 'Lab D Dimer',
  `lab_pt` tinyint(1) DEFAULT 1 COMMENT 'Lab PT',
  `lab_aptt` tinyint(1) DEFAULT 1 COMMENT 'Lab APTT',
  `lab_waktu_pendarahan` tinyint(1) DEFAULT 1 COMMENT 'Lab Waktu Pendarahan',
  `lab_anti_hiv` tinyint(1) DEFAULT 1 COMMENT 'Lab Anti HIV',
  `lab_analisa_gas` tinyint(1) DEFAULT 1 COMMENT 'Lab Analisa Gas',
  `lab_albumin` tinyint(1) DEFAULT 1 COMMENT 'Lab Albumin',
  `rad_thorax_ap_pa` tinyint(1) DEFAULT 1 COMMENT 'Radiologi Thorax AP/PA',
  `terapi_konvalesen` decimal(15,2) DEFAULT 0.00 COMMENT 'Terapi Plasma Konvalesen',
  `akses_naat` enum('A','B','C') DEFAULT NULL COMMENT 'Kategori Akses NAAT',
  `isoman_ind` tinyint(1) DEFAULT 0 COMMENT 'Indikator Isolasi Mandiri',
  `bayi_lahir_status_cd` enum('1','2') DEFAULT NULL COMMENT '1=Tanpa Kelainan, 2=Dengan Kelainan',
  `apgar_menit_1_appearance` tinyint(4) DEFAULT 0 COMMENT 'APGAR 1 menit - Appearance',
  `apgar_menit_1_pulse` tinyint(4) DEFAULT 0 COMMENT 'APGAR 1 menit - Pulse',
  `apgar_menit_1_grimace` tinyint(4) DEFAULT 0 COMMENT 'APGAR 1 menit - Grimace',
  `apgar_menit_1_activity` tinyint(4) DEFAULT 0 COMMENT 'APGAR 1 menit - Activity',
  `apgar_menit_1_respiration` tinyint(4) DEFAULT 0 COMMENT 'APGAR 1 menit - Respiration',
  `apgar_menit_5_appearance` tinyint(4) DEFAULT 0 COMMENT 'APGAR 5 menit - Appearance',
  `apgar_menit_5_pulse` tinyint(4) DEFAULT 0 COMMENT 'APGAR 5 menit - Pulse',
  `apgar_menit_5_grimace` tinyint(4) DEFAULT 0 COMMENT 'APGAR 5 menit - Grimace',
  `apgar_menit_5_activity` tinyint(4) DEFAULT 0 COMMENT 'APGAR 5 menit - Activity',
  `apgar_menit_5_respiration` tinyint(4) DEFAULT 0 COMMENT 'APGAR 5 menit - Respiration',
  `usia_kehamilan` int(11) DEFAULT 0 COMMENT 'Usia Kehamilan (minggu)',
  `gravida` int(11) DEFAULT 0 COMMENT 'Jumlah Kehamilan',
  `partus` int(11) DEFAULT 0 COMMENT 'Jumlah Kelahiran',
  `abortus` int(11) DEFAULT 0 COMMENT 'Jumlah Keguguran',
  `onset_kontraksi` enum('spontan','induksi','non_spontan_non_induksi') DEFAULT NULL COMMENT 'Onset Kontraksi',
  `pemulasaraan_jenazah` tinyint(1) DEFAULT 0 COMMENT 'Pemulasaraan Jenazah',
  `kantong_jenazah` tinyint(1) DEFAULT 0 COMMENT 'Kantong Jenazah',
  `peti_jenazah` tinyint(1) DEFAULT 0 COMMENT 'Peti Jenazah',
  `plastik_erat` tinyint(1) DEFAULT 0 COMMENT 'Plastik Erat',
  `desinfektan_jenazah` tinyint(1) DEFAULT 0 COMMENT 'Desinfektan Jenazah',
  `mobil_jenazah` tinyint(1) DEFAULT 0 COMMENT 'Mobil Jenazah',
  `desinfektan_mobil_jenazah` tinyint(1) DEFAULT 0 COMMENT 'Desinfektan Mobil Jenazah',
  `dializer_single_use` tinyint(1) DEFAULT 0 COMMENT 'Dializer Single Use',
  `kantong_darah` int(11) DEFAULT 0 COMMENT 'Jumlah Kantong Darah',
  `alteplase_ind` tinyint(1) DEFAULT 0 COMMENT 'Indikator Alteplase',
  `tarif_poli_eks` decimal(15,2) DEFAULT 0.00 COMMENT 'Tarif Poli Eksekutif',
  `nama_dokter` varchar(255) DEFAULT NULL COMMENT 'Nama DPJP',
  `kode_tarif` varchar(10) DEFAULT 'AP' COMMENT 'Kode Tarif RS',
  `coder_nik` varchar(20) NOT NULL COMMENT 'NIK Coder',
  `episodes` text DEFAULT NULL COMMENT 'Episode Ruangan Rawat (format: 1;12#2;3#6;5)',
  `klaim_status` enum('draft','grouped','final','sent','processed') DEFAULT 'draft',
  `eklaim_status` enum('pending','created','error','final') DEFAULT 'pending' COMMENT 'Status klaim di E-Klaim: pending=belum dibuat, created=berhasil dibuat, error=gagal dibuat, final=sudah final',
  `eklaim_patient_id` varchar(50) DEFAULT NULL,
  `eklaim_admission_id` int(11) DEFAULT NULL,
  `eklaim_hospital_admission_id` int(11) DEFAULT NULL,
  `eklaim_error_message` text DEFAULT NULL,
  `eklaim_created_at` timestamp NULL DEFAULT NULL,
  `eklaim_updated_at` timestamp NULL DEFAULT NULL,
  `bpjs_klaim_status_cd` varchar(10) DEFAULT '40' COMMENT 'Status Klaim BPJS',
  `bpjs_klaim_status_nm` varchar(100) DEFAULT '40_Proses_Cabang' COMMENT 'Nama Status Klaim BPJS',
  `kemenkes_dc_status_cd` enum('unsent','sent','processed') DEFAULT 'unsent',
  `kemenkes_dc_sent_dttm` datetime DEFAULT NULL COMMENT 'Waktu Kirim ke DC Kemenkes',
  `bpjs_dc_status_cd` enum('unsent','sent','processed') DEFAULT 'unsent',
  `bpjs_dc_sent_dttm` datetime DEFAULT NULL COMMENT 'Waktu Kirim ke DC BPJS',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `cara_masuk` enum('gp','hosp-trans','mp','outp','inp','emd','born','nursing','psych','rehab','other') DEFAULT 'gp' COMMENT 'Cara masuk pasien sesuai mapping E-Klaim',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nomor_sep` (`nomor_sep`),
  KEY `idx_nomor_sep` (`nomor_sep`),
  KEY `idx_nomor_rm` (`nomor_rm`),
  KEY `idx_nomor_kartu` (`nomor_kartu`),
  KEY `idx_tgl_masuk` (`tgl_masuk`),
  KEY `idx_tgl_pulang` (`tgl_pulang`),
  KEY `idx_jenis_rawat` (`jenis_rawat`),
  KEY `idx_klaim_status` (`klaim_status`),
  KEY `idx_coder_nik` (`coder_nik`),
  KEY `idx_payor_id` (`payor_id`),
  KEY `idx_eklaim_status` (`eklaim_status`),
  KEY `idx_eklaim_patient_id` (`eklaim_patient_id`),
  KEY `idx_cara_masuk` (`cara_masuk`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `payor` */

DROP TABLE IF EXISTS `payor`;

CREATE TABLE `payor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payor_id` varchar(10) NOT NULL COMMENT 'ID Jaminan',
  `payor_cd` varchar(10) NOT NULL COMMENT 'Kode Jaminan',
  `payor_nm` varchar(255) NOT NULL COMMENT 'Nama Jaminan',
  `description` text DEFAULT NULL COMMENT 'Deskripsi Jaminan',
  `status` enum('active','inactive') DEFAULT 'active' COMMENT 'Status Jaminan',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `payor_id` (`payor_id`),
  KEY `idx_payor_id` (`payor_id`),
  KEY `idx_payor_cd` (`payor_cd`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `personnel` */

DROP TABLE IF EXISTS `personnel`;

CREATE TABLE `personnel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nik` varchar(20) NOT NULL COMMENT 'NIK Coder',
  `nama` varchar(255) NOT NULL COMMENT 'Nama Coder',
  `email` varchar(255) DEFAULT NULL COMMENT 'Email Coder',
  `phone` varchar(20) DEFAULT NULL COMMENT 'Telepon Coder',
  `status` enum('active','inactive') DEFAULT 'active' COMMENT 'Status Coder',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nik` (`nik`),
  KEY `idx_nik` (`nik`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `procedure_details` */

DROP TABLE IF EXISTS `procedure_details`;

CREATE TABLE `procedure_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kunjungan_id` int(11) NOT NULL COMMENT 'ID Kunjungan Pasien',
  `icd_code_id` int(11) NOT NULL COMMENT 'ID dari tabel idr_codes',
  `procedure_order` int(11) NOT NULL COMMENT 'Urutan Prosedur (1=Primary, 2=Secondary, dst)',
  `procedure_type` enum('primary','secondary') NOT NULL DEFAULT 'secondary' COMMENT 'Jenis Prosedur',
  `icd_code` varchar(20) NOT NULL COMMENT 'Kode ICD-9-CM',
  `icd_description` text NOT NULL COMMENT 'Deskripsi ICD-9-CM',
  `quantity` int(11) NOT NULL DEFAULT 1 COMMENT 'Jumlah Prosedur',
  `validcode` tinyint(1) DEFAULT 1 COMMENT 'Status Valid Kode',
  `accpdx` char(1) DEFAULT 'Y' COMMENT 'Status ACCPDX (Y/N)',
  `asterisk` tinyint(1) DEFAULT 0 COMMENT 'Status Asterisk',
  `im` tinyint(1) DEFAULT 0 COMMENT 'Status IM',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_kunjungan_id` (`kunjungan_id`),
  KEY `idx_icd_code_id` (`icd_code_id`),
  KEY `idx_procedure_order` (`procedure_order`),
  KEY `idx_procedure_type` (`procedure_type`),
  KEY `idx_icd_code` (`icd_code`),
  CONSTRAINT `procedure_details_ibfk_1` FOREIGN KEY (`kunjungan_id`) REFERENCES `kunjungan_pasien` (`id`) ON DELETE CASCADE,
  CONSTRAINT `procedure_details_ibfk_2` FOREIGN KEY (`icd_code_id`) REFERENCES `idr_codes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `web_service_logs` */

DROP TABLE IF EXISTS `web_service_logs`;

CREATE TABLE `web_service_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `method` varchar(100) NOT NULL COMMENT 'Method Web Service',
  `nomor_sep` varchar(30) DEFAULT NULL COMMENT 'Nomor SEP',
  `request_data` text DEFAULT NULL COMMENT 'Data Request',
  `response_data` text DEFAULT NULL COMMENT 'Data Response',
  `status` enum('success','error') NOT NULL COMMENT 'Status Response',
  `error_code` varchar(10) DEFAULT NULL COMMENT 'Kode Error',
  `error_message` text DEFAULT NULL COMMENT 'Pesan Error',
  `execution_time_ms` int(11) DEFAULT NULL COMMENT 'Waktu Eksekusi (ms)',
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'IP Address Client',
  `user_agent` text DEFAULT NULL COMMENT 'User Agent Client',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_method` (`method`),
  KEY `idx_nomor_sep` (`nomor_sep`),
  KEY `idx_status` (`status`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Procedure structure for procedure `HitungTotalTarif` */

/*!50003 DROP PROCEDURE IF EXISTS  `HitungTotalTarif` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` PROCEDURE `HitungTotalTarif`(
    IN p_kunjungan_id INT
)
BEGIN
    UPDATE detail_tarif 
    SET total_tarif = (
        COALESCE(prosedur_non_bedah, 0) +
        COALESCE(prosedur_bedah, 0) +
        COALESCE(konsultasi, 0) +
        COALESCE(tenaga_ahli, 0) +
        COALESCE(keperawatan, 0) +
        COALESCE(penunjang, 0) +
        COALESCE(radiologi, 0) +
        COALESCE(laboratorium, 0) +
        COALESCE(pelayanan_darah, 0) +
        COALESCE(rehabilitasi, 0) +
        COALESCE(kamar, 0) +
        COALESCE(rawat_intensif, 0) +
        COALESCE(obat, 0) +
        COALESCE(obat_kronis, 0) +
        COALESCE(obat_kemoterapi, 0) +
        COALESCE(alkes, 0) +
        COALESCE(bmhp, 0) +
        COALESCE(sewa_alat, 0)
    )
    WHERE kunjungan_id = p_kunjungan_id;
    
    SELECT ROW_COUNT() as affected_rows;
END */$$
DELIMITER ;

/* Procedure structure for procedure `UpdateKlaimStatus` */

/*!50003 DROP PROCEDURE IF EXISTS  `UpdateKlaimStatus` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateKlaimStatus`(
    IN p_nomor_sep VARCHAR(30),
    IN p_status ENUM('draft', 'grouped', 'final', 'sent', 'processed'),
    IN p_coder_nik VARCHAR(20)
)
BEGIN
    UPDATE kunjungan_pasien 
    SET klaim_status = p_status, 
        updated_at = CURRENT_TIMESTAMP
    WHERE nomor_sep = p_nomor_sep 
    AND coder_nik = p_coder_nik;
    
    SELECT ROW_COUNT() as affected_rows;
END */$$
DELIMITER ;

/*Table structure for table `v_diagnosis_complete` */

DROP TABLE IF EXISTS `v_diagnosis_complete`;

/*!50001 DROP VIEW IF EXISTS `v_diagnosis_complete` */;
/*!50001 DROP TABLE IF EXISTS `v_diagnosis_complete` */;

/*!50001 CREATE TABLE  `v_diagnosis_complete`(
 `id` int(11) ,
 `kunjungan_id` int(11) ,
 `icd_code_id` int(11) ,
 `diagnosis_order` int(11) ,
 `diagnosis_type` enum('primary','secondary') ,
 `icd_code` varchar(20) ,
 `icd_description` text ,
 `validcode` tinyint(1) ,
 `accpdx` char(1) ,
 `asterisk` tinyint(1) ,
 `im` tinyint(1) ,
 `created_at` timestamp ,
 `updated_at` timestamp ,
 `system` varchar(14) 
)*/;

/*Table structure for table `v_procedure_complete` */

DROP TABLE IF EXISTS `v_procedure_complete`;

/*!50001 DROP VIEW IF EXISTS `v_procedure_complete` */;
/*!50001 DROP TABLE IF EXISTS `v_procedure_complete` */;

/*!50001 CREATE TABLE  `v_procedure_complete`(
 `id` int(11) ,
 `kunjungan_id` int(11) ,
 `icd_code_id` int(11) ,
 `procedure_order` int(11) ,
 `procedure_type` enum('primary','secondary') ,
 `icd_code` varchar(20) ,
 `icd_description` text ,
 `quantity` int(11) ,
 `validcode` tinyint(1) ,
 `accpdx` char(1) ,
 `asterisk` tinyint(1) ,
 `im` tinyint(1) ,
 `created_at` timestamp ,
 `updated_at` timestamp ,
 `system` varchar(15) 
)*/;

/*Table structure for table `v_status_klaim` */

DROP TABLE IF EXISTS `v_status_klaim`;

/*!50001 DROP VIEW IF EXISTS `v_status_klaim` */;
/*!50001 DROP TABLE IF EXISTS `v_status_klaim` */;

/*!50001 CREATE TABLE  `v_status_klaim`(
 `id` int(11) ,
 `nomor_sep` varchar(30) ,
 `nama_pasien` varchar(255) ,
 `klaim_status` enum('draft','grouped','final','sent','processed') ,
 `bpjs_klaim_status_cd` varchar(10) ,
 `bpjs_klaim_status_nm` varchar(100) ,
 `kemenkes_dc_status_cd` enum('unsent','sent','processed') ,
 `bpjs_dc_status_cd` enum('unsent','sent','processed') ,
 `created_at` timestamp ,
 `updated_at` timestamp 
)*/;

/*View structure for view v_diagnosis_complete */

/*!50001 DROP TABLE IF EXISTS `v_diagnosis_complete` */;
/*!50001 DROP VIEW IF EXISTS `v_diagnosis_complete` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_diagnosis_complete` AS select `diagnosis_details`.`id` AS `id`,`diagnosis_details`.`kunjungan_id` AS `kunjungan_id`,`diagnosis_details`.`icd_code_id` AS `icd_code_id`,`diagnosis_details`.`diagnosis_order` AS `diagnosis_order`,`diagnosis_details`.`diagnosis_type` AS `diagnosis_type`,`diagnosis_details`.`icd_code` AS `icd_code`,`diagnosis_details`.`icd_description` AS `icd_description`,`diagnosis_details`.`validcode` AS `validcode`,`diagnosis_details`.`accpdx` AS `accpdx`,`diagnosis_details`.`asterisk` AS `asterisk`,`diagnosis_details`.`im` AS `im`,`diagnosis_details`.`created_at` AS `created_at`,`diagnosis_details`.`updated_at` AS `updated_at`,'ICD_10_2010_IM' AS `system` from `diagnosis_details` */;

/*View structure for view v_procedure_complete */

/*!50001 DROP TABLE IF EXISTS `v_procedure_complete` */;
/*!50001 DROP VIEW IF EXISTS `v_procedure_complete` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_procedure_complete` AS select `procedure_details`.`id` AS `id`,`procedure_details`.`kunjungan_id` AS `kunjungan_id`,`procedure_details`.`icd_code_id` AS `icd_code_id`,`procedure_details`.`procedure_order` AS `procedure_order`,`procedure_details`.`procedure_type` AS `procedure_type`,`procedure_details`.`icd_code` AS `icd_code`,`procedure_details`.`icd_description` AS `icd_description`,`procedure_details`.`quantity` AS `quantity`,`procedure_details`.`validcode` AS `validcode`,`procedure_details`.`accpdx` AS `accpdx`,`procedure_details`.`asterisk` AS `asterisk`,`procedure_details`.`im` AS `im`,`procedure_details`.`created_at` AS `created_at`,`procedure_details`.`updated_at` AS `updated_at`,'ICD_9CM_2010_IM' AS `system` from `procedure_details` */;

/*View structure for view v_status_klaim */

/*!50001 DROP TABLE IF EXISTS `v_status_klaim` */;
/*!50001 DROP VIEW IF EXISTS `v_status_klaim` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_status_klaim` AS select `kp`.`id` AS `id`,`kp`.`nomor_sep` AS `nomor_sep`,`kp`.`nama_pasien` AS `nama_pasien`,`kp`.`klaim_status` AS `klaim_status`,`kp`.`bpjs_klaim_status_cd` AS `bpjs_klaim_status_cd`,`kp`.`bpjs_klaim_status_nm` AS `bpjs_klaim_status_nm`,`kp`.`kemenkes_dc_status_cd` AS `kemenkes_dc_status_cd`,`kp`.`bpjs_dc_status_cd` AS `bpjs_dc_status_cd`,`kp`.`created_at` AS `created_at`,`kp`.`updated_at` AS `updated_at` from `kunjungan_pasien` `kp` order by `kp`.`updated_at` desc */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
