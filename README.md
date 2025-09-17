# IDRG PHP - Sistem Pengelolaan Pasien dan Coding E-Klaim

Sistem web berbasis PHP untuk pengelolaan pasien rawat inap/jalan dan integrasi lengkap dengan E-Klaim IDRG/INACBG menggunakan API eksternal.

## 📁 Struktur File

```
├── api/                              # API Endpoints
│   ├── eklaim_new_claim.php         # Endpoint utama E-Klaim integration
│   ├── patients.php                 # API untuk data pasien
│   ├── search.php                   # API pencarian kode ICD
│   ├── get_diagnosis.php            # API untuk mendapatkan diagnosa
│   ├── get_procedure.php            # API untuk mendapatkan prosedur
│   ├── get_inacbg_codes.php         # API untuk kode INACBG
│   ├── save_all_coding_data.php     # API untuk menyimpan data coding
│   ├── check_eklaim_tracking.php    # API untuk tracking E-Klaim
│   └── check_grouping_status.php    # API untuk status grouping
├── assets/                          # Assets statis
│   ├── coding-idrg.css             # Stylesheet utama
│   └── dody.ico                    # Favicon
├── config/                          # Konfigurasi
│   ├── database.php                # Konfigurasi database
│   ├── eklaim_config.php           # Konfigurasi E-Klaim API
│   ├── cara_masuk_mapping.php      # Mapping cara masuk
│   └── kode_tarif_mapping.php      # Mapping kode tarif
├── database/                        # Database schema
│   └── import_coding_tables.sql    # Schema tabel import coding
├── functions/                       # Fungsi utilitas
│   └── eklaim_method_tracking.php  # Tracking method E-Klaim
├── includes/                        # Include files
│   ├── import_coding_db.php        # Fungsi database import coding
│   └── logging_functions.php       # Fungsi logging
├── logs/                           # Direktori log (auto-generated)
├── index.php                       # Halaman utama daftar pasien
├── coding_idrg.php                 # Interface coding IDRG/INACBG
└── E-KLAIM IDRG.postman_collection.json # Koleksi Postman untuk referensi API
```

## 🚀 Fitur Utama

### 1. **Sistem Pengelolaan Pasien**
- Daftar pasien rawat inap dan rawat jalan
- Informasi lengkap pasien (SEP, kartu BPJS, RM, dll)
- Status kunjungan dan discharge

### 2. **Interface Coding IDRG**
- **Diagnosa ICD-10-IM**: Pencarian dan pemilihan diagnosa dengan validasi
- **Prosedur ICD-9**: Pencarian dan pemilihan prosedur dengan quantity
- **Validasi Real-time**: Validasi kode terhadap database `idr_codes` dan `inacbg_codes`
- **Import Coding**: Transfer data dari IDRG ke INACBG dengan delete-insert operation

### 3. **Workflow E-Klaim Lengkap**
- **IDRG Process**: New Claim → Set Data → Coding → Grouping → Final
- **INACBG Process**: Import → Coding → Grouping Stage 1 → Stage 2 → Final
- **Claim Management**: Final Claim → Send Online → Print → Re-edit

### 4. **Sistem Tracking dan Logging**
- **Method Tracking**: Log semua method E-Klaim di `eklaim_method_tracking`
- **Import Logging**: Log operasi import di `import_coding_log`
- **Error Logging**: Log error dan response di `logs/`

### 5. **Validasi dan Error Handling**
- Validasi MDC/DRG dengan logika khusus (MDC 36 = invalid)
- Fallback description dari `idr_codes` jika tidak ditemukan di `inacbg_codes`
- Error handling lengkap dengan pesan yang informatif

## 🔧 API Endpoints

### E-Klaim Integration (`api/eklaim_new_claim.php`)
```php
// Actions yang tersedia:
- setClaimData          // Set data klaim lengkap
- setIdrgDiagnosa       // Set diagnosa IDRG
- setIdrgProcedure      // Set prosedur IDRG
- setInacbgDiagnosa     // Set diagnosa INACBG
- setInacbgProcedure    // Set prosedur INACBG
- grouper               // Grouping IDRG/INACBG
- idrg_grouper_final    // Finalisasi IDRG
- inacbg_grouper_final  // Finalisasi INACBG
- idrg_grouper_reedit   // Re-edit IDRG
- inacbg_grouper_reedit // Re-edit INACBG
- final_claim           // Finalisasi klaim
- send_claim_online     // Kirim klaim online
- idrg_to_inacbg_import // Import IDRG ke INACBG
- checkGroupingStatus   // Cek status grouping
- createNewClaim        // Buat klaim baru
```

### Utility APIs
```php
// api/search.php - Pencarian kode ICD
GET ?system=idrg&search=term&limit=20

// api/patients.php - Data pasien
GET ?action=get_patients&type=inpatient|outpatient

// api/get_diagnosis.php - Data diagnosa
GET ?nomor_sep=xxx

// api/get_procedure.php - Data prosedur  
GET ?nomor_sep=xxx
```

## 🗄️ Struktur Database

### Tabel Utama
- **`kunjungan_pasien`**: Data pasien dan status grouping
- **`idr_codes`**: Master kode ICD-10 dan ICD-9
- **`inacbg_codes`**: Master kode INACBG
- **`eklaim_method_tracking`**: Log method E-Klaim
- **`eklaim_method_mapping`**: Mapping method code ke nama method

### Tabel Import Coding
- **`import_coding_log`**: Log operasi import
- **`import_coding_diagnosis`**: Data diagnosa yang diimport
- **`import_coding_procedure`**: Data prosedur yang diimport

## ⚙️ Konfigurasi

### Database (`config/database.php`)
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'idrg');
define('DB_USER', 'root');
define('DB_PASS', 'Naftali123!');
```

### E-Klaim (`config/eklaim_config.php`)
```php
define('EKLAIM_BASE_URL', 'http://10.10.11.173');
define('EKLAIM_ENDPOINT', '/E-Klaim/ws.php');
define('EKLAIM_DEBUG_MODE', true);
define('EKLAIM_CODER_NIK', '123123123123');
```

## 🔄 Workflow Sistem

### 1. **Pembuatan Klaim**
1. Pilih pasien dari daftar (`index.php`)
2. Klik "Coding IDRG" untuk masuk ke interface coding
3. Sistem otomatis membuat klaim baru di E-Klaim

### 2. **Coding IDRG**
1. **Diagnosa**: Tambah diagnosa ICD-10-IM (minimal 1)
2. **Prosedur**: Tambah prosedur ICD-9 (opsional)
3. **Grouping**: Klik "Grouping iDRG" untuk proses grouping
4. **Final**: Klik "Final iDRG" untuk finalisasi

### 3. **Import ke INACBG**
1. Klik "Import Coding" untuk transfer data
2. Sistem melakukan delete-insert ke tabel import
3. Data tersimpan dengan validasi kode

### 4. **Coding INACBG**
1. **Diagnosa**: Set diagnosa INACBG
2. **Prosedur**: Set prosedur INACBG
3. **Grouping Stage 1**: Klik "Grouping INACBG"
4. **Grouping Stage 2**: Pilih Special CMG options
5. **Final**: Klik "Final INACBG"

### 5. **Finalisasi Klaim**
1. **Final Klaim**: Klik "Final Klaim"
2. **Status Klaim**: Tampil layout dengan tombol:
   - Cetak Klaim
   - Kirim Klaim Online
   - Edit Ulang Klaim

## 🎯 Fitur Khusus

### Validasi Grouping
```javascript
// Logika validasi: MDC 36 = invalid, selain itu valid
const invalidMdcCode = '36';
const isValidResult = mdcNumber !== invalidMdcCode && drgCode;
```

### Fallback Description
- Jika kode tidak ditemukan di `inacbg_codes`
- Cari di `idr_codes` dengan append "(IM tidak berlaku)" berwarna merah

### Method Tracking
- Setiap method E-Klaim di-track dengan status success/failed
- Method codes: 22 (INACBG Stage 1), 23 (INACBG Stage 2), 11 (Re-edit IDRG)

### Import Operation
- Delete-insert operation berdasarkan `nomor_sep`
- Populate `icd_description` dari master data
- Log operasi di `import_coding_log`

## 🚨 Error Codes E-Klaim

- **E2004**: Nomor SEP tidak ditemukan
- **E2007**: Duplikasi nomor SEP  
- **E2018**: Klaim masih belum final
- **E2101**: IM tidak berlaku
- **E2102**: iDRG/INACBG coding sudah final
- **E2103**: iDRG/INACBG coding belum final
- **E2104**: INACBG coding belum final
- **E2105**: iDRG error ungroupable

## 📊 Logging

### File Log
- `logs/eklaim_YYYY-MM-DD.log`: Log request/response E-Klaim
- `logs/web_service_requests.log`: Log request web service
- `logs/web_service_responses.log`: Log response web service

### Database Log
- `eklaim_method_tracking`: Tracking method dengan status
- `import_coding_log`: Log operasi import dengan metadata

## 🧪 Testing

### Akses Aplikasi
1. **Beranda**: `http://localhost/idrg-php/`
2. **Coding**: `http://localhost/idrg-php/coding_idrg.php?patient_id=19`

### Testing Workflow
1. Pilih pasien dari daftar
2. Lakukan coding IDRG lengkap
3. Test import ke INACBG
4. Lakukan coding INACBG lengkap
5. Test finalisasi klaim

## 🔧 Requirements

- **PHP**: 7.0+
- **Database**: MySQL 5.7+
- **Extensions**: PDO, cURL, JSON
- **Server**: Apache/Nginx dengan mod_rewrite
- **Akses**: Koneksi ke server E-Klaim

## 📝 Setup

1. **Database**: Import schema dan data master
2. **Konfigurasi**: Update `config/database.php` dan `config/eklaim_config.php`
3. **Permissions**: Set write permission untuk direktori `logs/`
4. **Testing**: Akses `index.php` untuk memulai

## 🤝 Support

Sistem ini dikembangkan berdasarkan koleksi Postman E-KLAIM IDRG untuk integrasi sistem klaim rumah sakit dengan BPJS Kesehatan.

Untuk pertanyaan teknis, silakan merujuk ke:
- Log file di direktori `logs/`
- Database tracking di `eklaim_method_tracking`
- Postman collection untuk referensi API eksternal