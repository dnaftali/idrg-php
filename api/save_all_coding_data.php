<?php
/**
 * API Endpoint untuk menyimpan semua data coding (diagnosa, prosedur, data klinis)
 * dalam satu transaksi untuk menghindari deadlock
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include database connection
require_once '../config/database.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid JSON input');
    }
    
    $patientId = $input['patient_id'] ?? null;
    if (!$patientId) {
        throw new Exception('Patient ID is required');
    }
    
    // Validate diagnosis and procedures - must have at least 1 record each
    $diagnosis = $input['diagnosis'] ?? [];
    $procedures = $input['procedures'] ?? [];
    
    if (empty($diagnosis) || count($diagnosis) < 1) {
        throw new Exception('Diagnosa (ICD-10-IM) harus berisi minimal 1 record');
    }
    
    if (empty($procedures) || count($procedures) < 1) {
        throw new Exception('Prosedur (ICD-9CM-IM) harus berisi minimal 1 record');
    }
    
    // Validate discharge_status
    $dischargeStatus = $input['discharge_status'] ?? '1';
    if (!in_array($dischargeStatus, ['1', '2', '3', '4', '5'])) {
        $dischargeStatus = '1'; // Default to '1' (atas persetujuan dokter) if invalid
    }
    
    // Validate ADL scores (12-60)
    $adlSubAcute = $input['adl_sub_acute'] ?? 0;
    $adlChronic = $input['adl_chronic'] ?? 0;
    
    // Ensure ADL scores are within valid range (12-60)
    if ($adlSubAcute < 12 || $adlSubAcute > 60) {
        $adlSubAcute = 0; // Default to 0 if invalid
    }
    if ($adlChronic < 12 || $adlChronic > 60) {
        $adlChronic = 0; // Default to 0 if invalid
    }
    
    $pdo->beginTransaction();
    
    // 1. Update kunjungan_pasien table
    $updateKunjungan = "UPDATE kunjungan_pasien SET 
                        jaminan_cara_bayar = ?,
                        jenis_rawat = ?,
                        nama_dokter = ?,
                        nomor_kartu = ?,
                        nomor_sep = ?,
                        tgl_masuk = ?,
                        tgl_pulang = ?,
                        adl_sub_acute = ?,
                        adl_chronic = ?,
                        discharge_status = ?,
                        kelas_rawat = ?,
                        cara_masuk = ?,
                        kode_tarif = ?,
                        updated_at = NOW()
                        WHERE id = ?";
    
    $stmt = $pdo->prepare($updateKunjungan);
    $stmt->execute([
        $input['jaminan_cara_bayar'] ?? 'JKN',
        $input['jenis_rawat'] ?? '2',
        $input['nama_dokter'] ?? '',
        $input['nomor_kartu'] ?? '',
        $input['nomor_sep'] ?? '',
        $input['tgl_masuk'] ?? null,
        $input['tgl_pulang'] ?? null,
        $adlSubAcute,
        $adlChronic,
        $dischargeStatus,
        $input['kelas_rawat'] ?? '3',
        $input['cara_masuk'] ?? 'gp',
        $input['kode_tarif'] ?? 'CP',
        $patientId
    ]);
    
    // 2. Save diagnosis data
    if (isset($input['diagnosis']) && is_array($input['diagnosis'])) {
        // Delete existing diagnosis
        $deleteDiagnosis = "DELETE FROM diagnosis_details WHERE kunjungan_id = ?";
        $stmt = $pdo->prepare($deleteDiagnosis);
        $stmt->execute([$patientId]);
        
        // Insert new diagnosis
        $insertDiagnosis = "INSERT INTO diagnosis_details (kunjungan_id, icd_code_id, diagnosis_order, diagnosis_type, icd_code, icd_description, validcode, accpdx, asterisk, im) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($insertDiagnosis);
        
        foreach ($input['diagnosis'] as $index => $diagnosis) {
            // Get icd_code_id from idr_codes table or use default
            $icdCodeId = 1; // Default value, should be replaced with actual lookup
            if (!empty($diagnosis['code'])) {
                $lookupIcd = "SELECT id FROM idr_codes WHERE icd_code = ? LIMIT 1";
                $lookupStmt = $pdo->prepare($lookupIcd);
                $lookupStmt->execute([$diagnosis['code']]);
                $icdResult = $lookupStmt->fetch();
                if ($icdResult) {
                    $icdCodeId = $icdResult['id'];
                }
            }
            
            $stmt->execute([
                $patientId,
                $icdCodeId,
                $index + 1,
                $diagnosis['type'] ?? 'primary',
                $diagnosis['code'] ?? '',
                $diagnosis['description'] ?? '',
                $diagnosis['validcode'] ?? 1,
                $diagnosis['accpdx'] ?? 'Y',
                $diagnosis['asterisk'] ?? 0,
                $diagnosis['im'] ?? 0
            ]);
        }
    }
    
    // 3. Save procedure data
    if (isset($input['procedures']) && is_array($input['procedures'])) {
        // Delete existing procedures
        $deleteProcedures = "DELETE FROM procedure_details WHERE kunjungan_id = ?";
        $stmt = $pdo->prepare($deleteProcedures);
        $stmt->execute([$patientId]);
        
        // Insert new procedures
        $insertProcedures = "INSERT INTO procedure_details (kunjungan_id, icd_code_id, procedure_order, procedure_type, icd_code, icd_description, quantity, validcode, accpdx, asterisk, im) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($insertProcedures);
        
        foreach ($input['procedures'] as $index => $procedure) {
            // Get icd_code_id from idr_codes table or use default
            $icdCodeId = 1; // Default value, should be replaced with actual lookup
            if (!empty($procedure['code'])) {
                $lookupIcd = "SELECT id FROM idr_codes WHERE icd_code = ? LIMIT 1";
                $lookupStmt = $pdo->prepare($lookupIcd);
                $lookupStmt->execute([$procedure['code']]);
                $icdResult = $lookupStmt->fetch();
                if ($icdResult) {
                    $icdCodeId = $icdResult['id'];
                }
            }
            
            $stmt->execute([
                $patientId,
                $icdCodeId,
                $index + 1,
                $procedure['type'] ?? 'primary',
                $procedure['code'] ?? '',
                $procedure['description'] ?? '',
                $procedure['quantity'] ?? 1,
                $procedure['validcode'] ?? 1,
                $procedure['accpdx'] ?? 'Y',
                $procedure['asterisk'] ?? 0,
                $procedure['im'] ?? 0
            ]);
        }
    }
    
    // 4. Save detail_tarif data
    if (isset($input['detail_tarif'])) {
        $tarif = $input['detail_tarif'];
        
        // Check if detail_tarif record exists
        $checkTarif = "SELECT id FROM detail_tarif WHERE kunjungan_id = ?";
        $stmt = $pdo->prepare($checkTarif);
        $stmt->execute([$patientId]);
        $existingTarif = $stmt->fetch();
        
        if ($existingTarif) {
            // Update existing record
            $updateTarif = "UPDATE detail_tarif SET 
                            prosedur_non_bedah = ?,
                            prosedur_bedah = ?,
                            konsultasi = ?,
                            tenaga_ahli = ?,
                            keperawatan = ?,
                            penunjang = ?,
                            radiologi = ?,
                            laboratorium = ?,
                            pelayanan_darah = ?,
                            rehabilitasi = ?,
                            kamar = ?,
                            rawat_intensif = ?,
                            obat = ?,
                            obat_kronis = ?,
                            obat_kemoterapi = ?,
                            alkes = ?,
                            bmhp = ?,
                            sewa_alat = ?,
                            total_tarif = ?,
                            kategori_tarif = ?,
                            nama_layanan = ?,
                            updated_at = NOW()
                            WHERE kunjungan_id = ?";
            
            $stmt = $pdo->prepare($updateTarif);
            $stmt->execute([
                $tarif['prosedur_non_bedah'] ?? 0,
                $tarif['prosedur_bedah'] ?? 0,
                $tarif['konsultasi'] ?? 0,
                $tarif['tenaga_ahli'] ?? 0,
                $tarif['keperawatan'] ?? 0,
                $tarif['penunjang'] ?? 0,
                $tarif['radiologi'] ?? 0,
                $tarif['laboratorium'] ?? 0,
                $tarif['pelayanan_darah'] ?? 0,
                $tarif['rehabilitasi'] ?? 0,
                $tarif['kamar'] ?? 0,
                $tarif['rawat_intensif'] ?? 0,
                $tarif['obat'] ?? 0,
                $tarif['obat_kronis'] ?? 0,
                $tarif['obat_kemoterapi'] ?? 0,
                $tarif['alkes'] ?? 0,
                $tarif['bmhp'] ?? 0,
                $tarif['sewa_alat'] ?? 0,
                $tarif['total_tarif'] ?? 0,
                $tarif['kategori_tarif'] ?? null,
                $tarif['nama_layanan'] ?? null,
                $patientId
            ]);
        } else {
            // Insert new record
            $insertTarif = "INSERT INTO detail_tarif (
                                kunjungan_id, prosedur_non_bedah, prosedur_bedah, konsultasi,
                                tenaga_ahli, keperawatan, penunjang, radiologi, laboratorium,
                                pelayanan_darah, rehabilitasi, kamar, rawat_intensif,
                                obat, obat_kronis, obat_kemoterapi, alkes, bmhp, sewa_alat,
                                total_tarif, kategori_tarif, nama_layanan, created_at, updated_at
                            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
            
            $stmt = $pdo->prepare($insertTarif);
            $stmt->execute([
                $patientId,
                $tarif['prosedur_non_bedah'] ?? 0,
                $tarif['prosedur_bedah'] ?? 0,
                $tarif['konsultasi'] ?? 0,
                $tarif['tenaga_ahli'] ?? 0,
                $tarif['keperawatan'] ?? 0,
                $tarif['penunjang'] ?? 0,
                $tarif['radiologi'] ?? 0,
                $tarif['laboratorium'] ?? 0,
                $tarif['pelayanan_darah'] ?? 0,
                $tarif['rehabilitasi'] ?? 0,
                $tarif['kamar'] ?? 0,
                $tarif['rawat_intensif'] ?? 0,
                $tarif['obat'] ?? 0,
                $tarif['obat_kronis'] ?? 0,
                $tarif['obat_kemoterapi'] ?? 0,
                $tarif['alkes'] ?? 0,
                $tarif['bmhp'] ?? 0,
                $tarif['sewa_alat'] ?? 0,
                $tarif['total_tarif'] ?? 0,
                $tarif['kategori_tarif'] ?? null,
                $tarif['nama_layanan'] ?? null
            ]);
        }
    }
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Data berhasil disimpan',
        'patient_id' => $patientId
    ]);
    
} catch (Exception $e) {
    if (isset($pdo)) {
        $pdo->rollBack();
    }
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
