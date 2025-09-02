<?php
/**
 * Web Service Functions untuk Integrasi E-Klaim INA-CBG
 */

/**
 * Kirim data ke web service E-Klaim
 */
function sendToEklaimWebService($encryptedData, $endpoint = null) {
    if (!$endpoint) {
        $endpoint = getEklaimEndpoint();
    }
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $endpoint);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $encryptedData);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        throw new Exception("CURL Error: $error");
    }
    
    if ($httpCode !== 200) {
        throw new Exception("HTTP Error: $httpCode");
    }
    
    return $response;
}

/**
 * Ambil endpoint E-Klaim dari konfigurasi
 */
function getEklaimEndpoint() {
    // Dalam implementasi nyata, ambil dari konfigurasi
    return 'http://your_eklaim_server/E-Klaim/ws.php';
}

/**
 * Validasi data klaim
 */
function validateClaimData($data, $requiredFields = []) {
    $errors = [];
    
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            $errors[] = "Field '$field' wajib diisi";
        }
    }
    
    if (!empty($errors)) {
        throw new Exception(implode(', ', $errors));
    }
    
    return true;
}

/**
 * Format response sesuai standar E-Klaim
 */
function formatEklaimResponse($code = 200, $message = 'Ok', $data = null, $errorNo = null) {
    $response = [
        'metadata' => [
            'code' => $code,
            'message' => $message
        ]
    ];
    
    if ($errorNo) {
        $response['metadata']['error_no'] = $errorNo;
    }
    
    if ($data !== null) {
        $response['response'] = $data;
    }
    
    return $response;
}

/**
 * Format error response
 */
function formatEklaimError($code, $message, $errorNo) {
    return formatEklaimResponse($code, $message, null, $errorNo);
}

/**
 * Check if claim exists
 */
function claimExists($nomorSep) {
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT id FROM kunjungan_pasien WHERE nomor_sep = ?");
    $stmt->execute([$nomorSep]);
    return $stmt->fetch() !== false;
}

/**
 * Check if coder exists and is active
 */
function coderExists($nik) {
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT id FROM personnel WHERE nik = ? AND status = 'active'");
    $stmt->execute([$nik]);
    return $stmt->fetch() !== false;
}

/**
 * Get claim by SEP
 */
function getClaimBySep($nomorSep) {
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT * FROM kunjungan_pasien WHERE nomor_sep = ?");
    $stmt->execute([$nomorSep]);
    return $stmt->fetch();
}

/**
 * Update claim status
 */
function updateClaimStatus($nomorSep, $status, $coderNik = null) {
    $pdo = getConnection();
    
    $sql = "UPDATE kunjungan_pasien SET klaim_status = ?, updated_at = NOW()";
    $params = [$status];
    
    if ($coderNik) {
        $sql .= ", coder_nik = ?";
        $params[] = $coderNik;
    }
    
    $sql .= " WHERE nomor_sep = ?";
    $params[] = $nomorSep;
    
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($params);
}

/**
 * Calculate LOS (Length of Stay)
 */
function calculateLOS($tglMasuk, $tglPulang) {
    if (!$tglMasuk || !$tglPulang) {
        return 0;
    }
    
    $masuk = new DateTime($tglMasuk);
    $pulang = new DateTime($tglPulang);
    $diff = $masuk->diff($pulang);
    
    return $diff->days;
}

/**
 * Validate ICD-10 code
 */
function validateICD10Code($code) {
    // Implementasi validasi kode ICD-10
    // Dalam implementasi nyata, cek ke database atau API ICD-10
    return !empty($code) && preg_match('/^[A-Z][0-9]{2}\.[0-9X]$/', $code);
}

/**
 * Validate ICD-9-CM code
 */
function validateICD9CMCode($code) {
    // Implementasi validasi kode ICD-9-CM
    // Dalam implementasi nyata, cek ke database atau API ICD-9-CM
    return !empty($code) && preg_match('/^[0-9]{2}\.[0-9X]{2}$/', $code);
}

/**
 * Format diagnosa string
 */
function formatDiagnosaString($diagnosaArray) {
    if (is_array($diagnosaArray)) {
        return implode('#', $diagnosaArray);
    }
    return $diagnosaArray;
}

/**
 * Parse diagnosa string
 */
function parseDiagnosaString($diagnosaString) {
    if (empty($diagnosaString)) {
        return [];
    }
    return explode('#', $diagnosaString);
}

/**
 * Format procedure string
 */
function formatProcedureString($procedureArray) {
    if (is_array($procedureArray)) {
        return implode('#', $procedureArray);
    }
    return $procedureArray;
}

/**
 * Parse procedure string
 */
function parseProcedureString($procedureString) {
    if (empty($procedureString)) {
        return [];
    }
    return explode('#', $procedureString);
}

/**
 * Get claim statistics
 */
function getClaimStatistics() {
    $pdo = getConnection();
    
    $sql = "SELECT 
                klaim_status,
                COUNT(*) as total,
                SUM(CASE WHEN jenis_rawat = '1' THEN 1 ELSE 0 END) as rawat_inap,
                SUM(CASE WHEN jenis_rawat = '2' THEN 1 ELSE 0 END) as rawat_jalan,
                SUM(CASE WHEN jenis_rawat = '3' THEN 1 ELSE 0 END) as rawat_igd
            FROM kunjungan_pasien 
            GROUP BY klaim_status";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    return $stmt->fetchAll();
}

/**
 * Get claims by status
 */
function getClaimsByStatus($status, $limit = 100) {
    $pdo = getConnection();
    
    $sql = "SELECT 
                nomor_sep,
                nama_pasien,
                nomor_rm,
                tgl_masuk,
                tgl_pulang,
                jenis_rawat,
                kelas_rawat,
                klaim_status,
                created_at
            FROM kunjungan_pasien 
            WHERE klaim_status = ?
            ORDER BY created_at DESC
            LIMIT ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$status, $limit]);
    
    return $stmt->fetchAll();
}

/**
 * Get claims ready for DC
 */
function getClaimsReadyForDC() {
    $pdo = getConnection();
    
    $sql = "SELECT 
                nomor_sep,
                nama_pasien,
                tgl_pulang,
                klaim_status,
                kemenkes_dc_status_cd,
                bpjs_dc_status_cd
            FROM kunjungan_pasien 
            WHERE klaim_status = 'final' 
            AND (kemenkes_dc_status_cd = 'unsent' OR bpjs_dc_status_cd = 'unsent')
            ORDER BY tgl_pulang ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    return $stmt->fetchAll();
}
?>
