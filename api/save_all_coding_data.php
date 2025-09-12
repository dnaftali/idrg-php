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
    
    // Validate diagnosis and procedures
    $diagnosis = $input['diagnosis'] ?? [];
    $procedures = $input['procedures'] ?? [];
    
    // Check if procedures contain '#' (indicates no procedures)
    $hasNoProcedures = false;
    if (!empty($procedures)) {
        foreach ($procedures as $procedure) {
            if (isset($procedure['icd_code']) && $procedure['icd_code'] === '#') {
                $hasNoProcedures = true;
                break;
            }
        }
    }
    
    if (empty($diagnosis) || count($diagnosis) < 1) {
        throw new Exception('Diagnosa (ICD-10-IM) harus berisi minimal 1 record');
    }
    
    // Procedures boleh kosong atau tidak ada record
    
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
                        covid19_status_cd = ?,
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
        $input['covid19_status_cd'] ?? '0',
        $patientId
    ]);
    error_log("Updated kunjungan_pasien record for id: " . $patientId);
    
    // 2. Save diagnosis data
    if (isset($input['diagnosis']) && is_array($input['diagnosis'])) {
        // Delete existing diagnosis
        $deleteDiagnosis = "DELETE FROM diagnosis_details WHERE kunjungan_id = ?";
        $stmt = $pdo->prepare($deleteDiagnosis);
        $stmt->execute([$patientId]);
        error_log("Deleted existing diagnosis records for kunjungan_id: " . $patientId);
        
        // Insert new diagnosis
        $insertDiagnosis = "INSERT INTO diagnosis_details (kunjungan_id, icd_code_id, diagnosis_order, diagnosis_type, icd_code, icd_description, validcode, accpdx, asterisk, im) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($insertDiagnosis);
        
        foreach ($input['diagnosis'] as $index => $diagnosis) {
            // Get icd_code_id from idr_codes table or use default
            $icdCodeId = 1; // Default value, should be replaced with actual lookup
            if (!empty($diagnosis['icd_code'])) {
                $lookupIcd = "SELECT id FROM idr_codes WHERE code = ? LIMIT 1";
                $lookupStmt = $pdo->prepare($lookupIcd);
                $lookupStmt->execute([$diagnosis['icd_code']]);
                $icdResult = $lookupStmt->fetch();
                if ($icdResult) {
                    $icdCodeId = $icdResult['id'];
                }
            }
            
            $stmt->execute([
                $patientId,
                $icdCodeId,
                $index + 1,
                $diagnosis['diagnosis_type'] ?? 'primary',
                $diagnosis['icd_code'] ?? '',
                $diagnosis['icd_description'] ?? '',
                $diagnosis['validcode'] ?? 1,
                $diagnosis['accpdx'] ?? 'Y',
                $diagnosis['asterisk'] ?? 0,
                $diagnosis['im'] ?? 0
            ]);
            error_log("Inserted diagnosis: " . ($diagnosis['icd_code'] ?? '') . " - " . ($diagnosis['icd_description'] ?? ''));
        }
        error_log("Total diagnosis records inserted: " . count($input['diagnosis']));
    }
    
    // 3. Save procedure data
    if (isset($input['procedures']) && is_array($input['procedures'])) {
        // Delete existing procedures
        $deleteProcedures = "DELETE FROM procedure_details WHERE kunjungan_id = ?";
        $stmt = $pdo->prepare($deleteProcedures);
        $stmt->execute([$patientId]);
        error_log("Deleted existing procedure records for kunjungan_id: " . $patientId);
        
        // Check if procedures contain '#' (indicates no procedures)
        $hasNoProcedures = false;
        foreach ($input['procedures'] as $procedure) {
            if (isset($procedure['icd_code']) && $procedure['icd_code'] === '#') {
                $hasNoProcedures = true;
                break;
            }
        }
        
        // Only insert procedures if not marked as 'no procedures'
        if (!$hasNoProcedures) {
            // Insert new procedures
            $insertProcedures = "INSERT INTO procedure_details (kunjungan_id, icd_code_id, procedure_order, procedure_type, icd_code, icd_description, quantity, validcode, accpdx, asterisk, im) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($insertProcedures);
            
            foreach ($input['procedures'] as $index => $procedure) {
                // Skip procedures with '#' code
                if (isset($procedure['icd_code']) && $procedure['icd_code'] === '#') {
                    continue;
                }
                
                // Get icd_code_id from idr_codes table or use default
                $icdCodeId = 1; // Default value, should be replaced with actual lookup
                if (!empty($procedure['icd_code'])) {
                    $lookupIcd = "SELECT id FROM idr_codes WHERE code = ? LIMIT 1";
                    $lookupStmt = $pdo->prepare($lookupIcd);
                    $lookupStmt->execute([$procedure['icd_code']]);
                    $icdResult = $lookupStmt->fetch();
                    if ($icdResult) {
                        $icdCodeId = $icdResult['id'];
                    }
                }
                
                $stmt->execute([
                    $patientId,
                    $icdCodeId,
                    $index + 1,
                    $procedure['procedure_type'] ?? 'primary',
                    $procedure['icd_code'] ?? '',
                    $procedure['icd_description'] ?? '',
                    $procedure['quantity'] ?? 1,
                    $procedure['validcode'] ?? 1,
                    $procedure['accpdx'] ?? 'Y',
                    $procedure['asterisk'] ?? 0,
                    $procedure['im'] ?? 0
                ]);
                error_log("Inserted procedure: " . ($procedure['icd_code'] ?? '') . " - " . ($procedure['icd_description'] ?? ''));
            }
            error_log("Total procedure records inserted: " . count($input['procedures']));
        } else {
            error_log("No procedures to insert - marked as 'no procedures' (#)");
        }
    }
    
    // 4. Save detail_tarif data
    if (isset($input['detail_tarif'])) {
        $tarif = $input['detail_tarif'];
        
        // Delete existing detail_tarif
        $deleteTarif = "DELETE FROM detail_tarif WHERE kunjungan_id = ?";
        $stmt = $pdo->prepare($deleteTarif);
        $stmt->execute([$patientId]);
        error_log("Deleted existing detail_tarif records for kunjungan_id: " . $patientId);
        
        // Insert new detail_tarif
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
        error_log("Inserted detail_tarif for kunjungan_id: " . $patientId);
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
