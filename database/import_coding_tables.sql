-- Tabel untuk menyimpan data import coding dari IDRG ke INACBG
-- Tabel utama untuk tracking import
CREATE TABLE `import_coding_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nomor_sep` varchar(50) NOT NULL,
  `import_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('success','failed','partial') NOT NULL DEFAULT 'success',
  `total_diagnosis` int(11) DEFAULT 0,
  `total_procedure` int(11) DEFAULT 0,
  `response_message` text,
  `metadata` json,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_nomor_sep` (`nomor_sep`),
  KEY `idx_import_date` (`import_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel untuk menyimpan data diagnosa yang diimport
CREATE TABLE `import_coding_diagnosis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `import_log_id` int(11) NOT NULL,
  `nomor_sep` varchar(50) NOT NULL,
  `diagnosis_type` varchar(10) NOT NULL COMMENT '1=Primary, 2=Secondary, 3=Comorbid',
  `icd_code` varchar(20) NOT NULL,
  `icd_description` text NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0 COMMENT '0=No, 1=Yes',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_import_log_id` (`import_log_id`),
  KEY `idx_nomor_sep` (`nomor_sep`),
  KEY `idx_icd_code` (`icd_code`),
  CONSTRAINT `fk_import_diagnosis_log` FOREIGN KEY (`import_log_id`) REFERENCES `import_coding_log` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel untuk menyimpan data prosedur yang diimport
CREATE TABLE `import_coding_procedure` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `import_log_id` int(11) NOT NULL,
  `nomor_sep` varchar(50) NOT NULL,
  `procedure_type` varchar(10) NOT NULL COMMENT '1=Primary, 2=Secondary, 3=Additional',
  `icd_code` varchar(20) NOT NULL,
  `icd_description` text NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `is_primary` tinyint(1) DEFAULT 0 COMMENT '0=No, 1=Yes',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_import_log_id` (`import_log_id`),
  KEY `idx_nomor_sep` (`nomor_sep`),
  KEY `idx_icd_code` (`icd_code`),
  CONSTRAINT `fk_import_procedure_log` FOREIGN KEY (`import_log_id`) REFERENCES `import_coding_log` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
