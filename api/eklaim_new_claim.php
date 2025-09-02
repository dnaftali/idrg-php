<?php
/**
 * API Endpoint untuk E-Klaim Integration
 * Menangani request ke server E-Klaim untuk operasi klaim baru
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include konfigurasi E-Klaim
require_once '../config/eklaim_config.php';

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Invalid JSON input'
    ]);
    exit();
}

$action = $input['action'] ?? '';

try {
    switch ($action) {
        case 'setClaimData':
            $result = handleSetClaimData($input);
            break;
            
        case 'setIdrgDiagnosa':
            $result = handleSetIdrgDiagnosa($input);
            break;
            
        case 'setIdrgProcedure':
            $result = handleSetIdrgProcedure($input);
            break;
            
        default:
            $result = [
                'success' => false,
                'error' => 'Invalid action: ' . $action
            ];
    }
    
    echo json_encode($result);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Internal server error: ' . $e->getMessage()
    ]);
}

/**
 * Handle setClaimData request
 */
function handleSetClaimData($input) {
    $nomorSep = $input['nomor_sep'] ?? '';
    $claimData = $input['claim_data'] ?? [];
    
    if (empty($nomorSep)) {
        return [
            'success' => false,
            'error' => 'Nomor SEP is required'
        ];
    }
    
    if (empty($claimData)) {
        return [
            'success' => false,
            'error' => 'Claim data is required'
        ];
    }
    
    // Validasi data yang diperlukan
    $requiredFields = [
        'nomor_kartu', 'tgl_masuk', 'tgl_pulang', 'cara_masuk',
        'jenis_rawat', 'kelas_rawat', 'discharge_status'
    ];
    
    foreach ($requiredFields as $field) {
        if (empty($claimData[$field])) {
            return [
                'success' => false,
                'error' => "Required field missing: $field"
            ];
        }
    }
    
    // Panggil fungsi setClaimData dari konfigurasi E-Klaim
    $result = setClaimData($nomorSep, $claimData);
    
    return $result;
}

/**
 * Handle setIdrgDiagnosa request
 */
function handleSetIdrgDiagnosa($input) {
    $nomorSep = $input['nomor_sep'] ?? '';
    $diagnosa = $input['diagnosa'] ?? '';
    
    if (empty($nomorSep)) {
        return [
            'success' => false,
            'error' => 'Nomor SEP is required'
        ];
    }
    
    if (empty($diagnosa)) {
        return [
            'success' => false,
            'error' => 'Diagnosa is required'
        ];
    }
    
    // Validasi format diagnosa
    if (!validateDiagnosa($diagnosa)) {
        return [
            'success' => false,
            'error' => 'Invalid diagnosa format. Expected format: ICD10#ICD10'
        ];
    }
    
    // Panggil fungsi setIdrgDiagnosa dari konfigurasi E-Klaim
    $result = setIdrgDiagnosa($nomorSep, $diagnosa);
    
    return $result;
}

/**
 * Handle setIdrgProcedure request
 */
function handleSetIdrgProcedure($input) {
    $nomorSep = $input['nomor_sep'] ?? '';
    $procedure = $input['procedure'] ?? '';
    
    if (empty($nomorSep)) {
        return [
            'success' => false,
            'error' => 'Nomor SEP is required'
        ];
    }
    
    if (empty($procedure)) {
        return [
            'success' => false,
            'error' => 'Procedure is required'
        ];
    }
    
    // Validasi format prosedur
    if (!validateProcedure($procedure)) {
        return [
            'success' => false,
            'error' => 'Invalid procedure format. Expected format: ICD9#ICD9+multiplier#ICD9'
        ];
    }
    
    // Panggil fungsi setIdrgProcedure dari konfigurasi E-Klaim
    $result = setIdrgProcedure($nomorSep, $procedure);
    
    return $result;
}
?>
