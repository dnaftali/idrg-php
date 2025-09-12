# E-KLAIM IDRG PHP Integration

Proyek ini menyediakan integrasi lengkap dengan sistem E-Klaim IDRG menggunakan PHP, berdasarkan koleksi Postman E-KLAIM IDRG.

## 📁 Struktur File

```
├── config/
│   └── eklaim_config.php          # Konfigurasi dan fungsi E-Klaim lengkap
├── logs/                          # Direktori log (dibuat otomatis)
├── EKLAIM_FUNCTIONS_GUIDE.md      # Panduan penggunaan fungsi
├── contoh_penggunaan_eklaim.php   # Contoh penggunaan lengkap
└── README.md                      # File ini
```

## 🚀 Fitur Utama

### ✅ 21 Fungsi E-Klaim Lengkap
Berdasarkan koleksi Postman E-KLAIM IDRG, semua fungsi telah diimplementasikan:

1. **#00 NEW CLAIM** - `createNewClaim()`
2. **#01 SET CLAIM DATA** - `setClaimData()`
3. **#02 IDRG DIAGNOSA SET** - `setIdrgDiagnosa()`
4. **#03 IDRG DIAGNOSA GET** - `getIdrgDiagnosa()`
5. **#04 IDRG PROCEDURE SET** - `setIdrgProcedure()`
6. **#05 IDRG PROCEDURE GET** - `getIdrgProcedure()`
7. **#06 GROUPING IDRG** - `groupIdrg()`
8. **#07 FINAL IDRG** - `finalizeIdrg()`
9. **#08 RE-EDIT IDRG** - `reeditIdrg()`
10. **#09 IDRG TO INACBG IMPORT** - `importIdrgToInacbg()`
11. **#10 INACBG DIAGNOSA GET** - `getInacbgDiagnosa()`
12. **#11 INACBG DIAGNOSA SET** - `setInacbgDiagnosa()`
13. **#12 INACBG PROCEDURE SET** - `setInacbgProcedure()`
14. **#13 INACBG PROCEDURE GET** - `getInacbgProcedure()`
15. **#14 GROUPING INACBG STAGE 1** - `groupInacbgStage1()`
16. **#15 GROUPING INACBG STAGE 2** - `groupInacbgStage2()`
17. **#16 FINAL INACBG** - `finalizeInacbg()`
18. **#17 RE-EDIT INACBG** - `reeditInacbg()`
19. **#18 CLAIM FINAL** - `finalizeClaim()`
20. **#19 CLAIM RE-EDIT** - `reeditClaim()`
21. **#20 CLAIM SEND** - `sendClaim()`
22. **#21 GET CLAIM DATA** - `getClaimData()`

### 🔧 Fungsi Helper
- **Validasi**: `validateNomorSep()`, `validateDiagnosa()`, `validateProcedure()`
- **Utilitas**: `formatEklaimDate()`, `getClaimStatus()`, `getErrorMessage()`, `getErrorCode()`

### 📝 Logging Otomatis
Semua request dan response otomatis di-log ke file `logs/eklaim_YYYY-MM-DD.log`

### 🛡️ Error Handling
Response standar dengan format:
```php
[
    'success' => true/false,
    'data' => [...], // jika success
    'error' => 'error message', // jika failed
    'http_code' => 200
]
```

## ⚙️ Konfigurasi

Edit file `config/eklaim_config.php` untuk mengatur:

```php
// Konfigurasi Server E-Klaim
define('EKLAIM_BASE_URL', 'http://10.10.1.63');  // Ganti dengan URL server Anda
define('EKLAIM_ENDPOINT', '/E-Klaim/ws.php');
define('EKLAIM_DEBUG_MODE', true);  // Set false untuk production
define('EKLAIM_TIMEOUT', 30);
```

## 📖 Cara Penggunaan

### 1. Include Konfigurasi
```php
require_once 'config/eklaim_config.php';
```

### 2. Contoh Penggunaan Dasar
```php
// Buat klaim baru
$patientData = [
    'nomor_kartu' => '0000097208276',
    'nomor_sep' => 'UJICOBA6',
    'nomor_rm' => 'A002122',
    'nama_pasien' => 'PASIEN UJICOBA IDRG 2',
    'tgl_lahir' => '2000-01-01 02:00:00',
    'gender' => '2'
];

$result = createNewClaim($patientData);
if ($result['success']) {
    echo "Klaim berhasil dibuat\n";
} else {
    echo "Error: " . getErrorMessage($result) . "\n";
}
```

### 3. Contoh Penggunaan Lengkap
Jalankan file `contoh_penggunaan_eklaim.php` untuk melihat contoh penggunaan lengkap semua fungsi.

## 🔄 Urutan Proses E-Klaim

### Tahap 1: Pembuatan Klaim
1. `createNewClaim()` - Buat klaim baru
2. `setClaimData()` - Set data klaim lengkap

### Tahap 2: Coding IDRG
3. `setIdrgDiagnosa()` - Set diagnosa IDRG
4. `getIdrgDiagnosa()` - Get diagnosa IDRG
5. `setIdrgProcedure()` - Set prosedur IDRG
6. `getIdrgProcedure()` - Get prosedur IDRG

### Tahap 3: Grouping IDRG
7. `groupIdrg()` - Grouping IDRG
8. `finalizeIdrg()` - Finalisasi IDRG
9. `reeditIdrg()` - Re-edit IDRG (opsional)

### Tahap 4: Import ke INACBG
10. `importIdrgToInacbg()` - Import data IDRG ke INACBG

### Tahap 5: Coding INACBG
11. `getInacbgDiagnosa()` - Get diagnosa INACBG
12. `setInacbgDiagnosa()` - Set diagnosa INACBG
13. `setInacbgProcedure()` - Set prosedur INACBG
14. `getInacbgProcedure()` - Get prosedur INACBG

### Tahap 6: Grouping INACBG
15. `groupInacbgStage1()` - Grouping INACBG tahap 1
16. `groupInacbgStage2()` - Grouping INACBG tahap 2
17. `finalizeInacbg()` - Finalisasi INACBG
18. `reeditInacbg()` - Re-edit INACBG (opsional)

### Tahap 7: Finalisasi dan Pengiriman
19. `finalizeClaim()` - Finalisasi klaim
20. `reeditClaim()` - Re-edit klaim (opsional)
21. `sendClaim()` - Kirim klaim ke BPJS
22. `getClaimData()` - Get data klaim lengkap

## 📋 Validasi Format

### Diagnosa ICD-10
- Format: `"ICD10#ICD10"` (contoh: `"S71.0#A00.1"`)
- Validasi: `validateDiagnosa($diagnosa)`

### Prosedur ICD-9
- Format IDRG: `"ICD9#ICD9+multiplier#ICD9"` (contoh: `"81.52#86.22+2#86.22"`)
- Format INACBG: `"ICD9#ICD9#ICD9"` (contoh: `"81.52#86.22#90.09"`)
- Validasi: `validateProcedure($procedure)`

### Tanggal
- Format: `"YYYY-MM-DD HH:mm:ss"`
- Helper: `formatEklaimDate($date)`

## 🚨 Error Codes

Berdasarkan koleksi Postman, error codes yang umum:
- `E2004`: Nomor SEP tidak ditemukan
- `E2007`: Duplikasi nomor SEP
- `E2018`: Klaim masih belum final
- `E2044`: Parameter tidak berlaku
- `E2101`: IM tidak berlaku
- `E2102`: iDRG/INACBG coding sudah final
- `E2103`: iDRG/INACBG coding belum final
- `E2104`: INACBG coding belum final
- `E2105`: iDRG error ungroupable

## 📊 Logging

Log otomatis tersimpan di `logs/eklaim_YYYY-MM-DD.log` dengan format:
```json
{
    "timestamp": "2025-01-05 10:30:00",
    "method": "new_claim",
    "request": {...},
    "response": {...}
}
```

## 🧪 Testing

### Menjalankan Contoh
```bash
php contoh_penggunaan_eklaim.php
```

### Menu Testing
1. Contoh penggunaan lengkap E-Klaim
2. Contoh penggunaan fungsi re-edit
3. Contoh penggunaan fungsi validasi
4. Contoh penggunaan fungsi utilitas
5. Jalankan semua contoh

## 🔧 Requirements

- PHP 7.0+
- cURL extension
- JSON extension
- Akses ke server E-Klaim

## 📝 License

Proyek ini dibuat berdasarkan koleksi Postman E-KLAIM IDRG untuk integrasi sistem klaim rumah sakit.

## 🤝 Support

Untuk pertanyaan atau dukungan, silakan merujuk ke dokumentasi atau file contoh yang disediakan.

