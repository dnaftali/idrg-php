<?php
/**
 * Web Service Endpoint untuk Integrasi E-Klaim INA-CBG
 * Berdasarkan Manual Web Service 5.9
 * 
 * Endpoint: /api/web_service_endpoint.php
 * Method: POST
 * Content-Type: application/x-www-form-urlencoded
 */

// Set headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include database configuration
require_once '../config/database.php';

// Include encryption functions
require_once '../includes/encryption_functions.php';

// Include web service functions
require_once '../includes/web_service_functions.php';

// Include logging functions
require_once '../includes/logging_functions.php';

// Get request data
$input = file_get_contents('php://input');
$requestData = null;

try {
    // Check if request is encrypted
    if (strpos($input, '----BEGIN ENCRYPTED DATA----') !== false) {
        // Decrypt request
        $requestData = decryptWebServiceRequest($input);
    } else {
        // Direct JSON request (for testing)
        $requestData = json_decode($input, true);
    }
    
    if (!$requestData) {
        throw new Exception('Invalid request data');
    }
    
    // Validate metadata
    if (!isset($requestData['metadata']) || !isset($requestData['metadata']['method'])) {
        throw new Exception('Missing metadata or method');
    }
    
    $method = $requestData['metadata']['method'];
    $data = $requestData['data'] ?? [];
    
    // Log incoming request
    logWebServiceRequest($method, $data);
    
    // Route to appropriate method handler
    $response = routeWebServiceMethod($method, $data);
    
    // Log response
    logWebServiceResponse($method, $data, $response);
    
    // Encrypt response if needed
    if (shouldEncryptResponse()) {
        $encryptedResponse = encryptWebServiceResponse($response);
        echo $encryptedResponse;
    } else {
        echo json_encode($response);
    }
    
} catch (Exception $e) {
    // Log error
    logWebServiceError($method ?? 'unknown', $data ?? [], $e->getMessage());
    
    // Return error response
    $errorResponse = [
        'metadata' => [
            'code' => 400,
            'message' => $e->getMessage(),
            'error_no' => 'E2099'
        ]
    ];
    
    if (shouldEncryptResponse()) {
        $encryptedError = encryptWebServiceResponse($errorResponse);
        echo $encryptedError;
    } else {
        echo json_encode($errorResponse);
    }
}

/**
 * Route web service method to appropriate handler
 */
function routeWebServiceMethod($method, $data) {
    switch ($method) {
        case 'new_claim':
            return handleNewClaim($data);
            
        case 'set_claim_data':
            return handleSetClaimData($data);
            
        case 'get_claim_data':
            return handleGetClaimData($data);
            
        case 'grouper':
            return handleGrouper($data);
            
        case 'claim_final':
            return handleClaimFinal($data);
            
        case 'send_claim':
            return handleSendClaim($data);
            
        case 'file_upload':
            return handleFileUpload($data);
            
        case 'file_delete':
            return handleFileDelete($data);
            
        case 'file_get':
            return handleFileGet($data);
            
        case 'search_diagnosis':
            return handleSearchDiagnosis($data);
            
        case 'search_procedures':
            return handleSearchProcedures($data);
            
        case 'generate_claim_number':
            return handleGenerateClaimNumber($data);
            
        default:
            throw new Exception("Method '$method' tidak ditemukan");
    }
}

/**
 * Handle new_claim method
 */
function handleNewClaim($data) {
    // Validate required fields
    $requiredFields = ['nomor_kartu', 'nomor_sep', 'nomor_rm', 'nama_pasien', 'tgl_lahir', 'gender'];
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            throw new Exception("Field '$field' wajib diisi");
        }
    }
    
    // Check for duplicate SEP
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT id, nama_pasien, nomor_rm, tgl_masuk FROM kunjungan_pasien WHERE nomor_sep = ?");
    $stmt->execute([$data['nomor_sep']]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        return [
            'metadata' => [
                'code' => 400,
                'message' => 'Duplikasi nomor SEP',
                'error_no' => 'E2007'
            ],
            'duplicate' => [
                [
                    'nama_pasien' => $existing['nama_pasien'],
                    'nomor_rm' => $existing['nomor_rm'],
                    'tgl_masuk' => $existing['tgl_masuk']
                ]
            ]
        ];
    }
    
    // Create new claim
    $pdo->beginTransaction();
    
    try {
        // Insert to kunjungan_pasien
        $sql = "INSERT INTO kunjungan_pasien (
            nomor_kartu, nomor_sep, nomor_rm, nama_pasien, 
            tgl_lahir, gender, coder_nik, tgl_masuk, 
            payor_id, payor_cd, jenis_rawat, kelas_rawat
        ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['nomor_kartu'],
            $data['nomor_sep'],
            $data['nomor_rm'],
            $data['nama_pasien'],
            $data['tgl_lahir'],
            $data['gender'],
            $data['coder_nik'] ?? '123123123123',
            $data['payor_id'] ?? '3',
            $data['payor_cd'] ?? 'JKN',
            $data['jenis_rawat'] ?? '1',
            $data['kelas_rawat'] ?? '3'
        ]);
        
        $kunjunganId = $pdo->lastInsertId();
        
        // Insert to detail_tarif
        $sql = "INSERT INTO detail_tarif (kunjungan_id) VALUES (?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$kunjunganId]);
        
        $pdo->commit();
        
        return [
            'metadata' => [
                'code' => 200,
                'message' => 'Ok'
            ],
            'response' => [
                'patient_id' => $kunjunganId,
                'admission_id' => 1,
                'hospital_admission_id' => $kunjunganId
            ]
        ];
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

/**
 * Handle set_claim_data method
 */
function handleSetClaimData($data) {
    // Validate required fields
    if (!isset($data['nomor_sep']) || !isset($data['coder_nik'])) {
        throw new Exception("Field nomor_sep dan coder_nik wajib diisi");
    }
    
    $pdo = getConnection();
    
    // Check if claim exists
    $stmt = $pdo->prepare("SELECT id FROM kunjungan_pasien WHERE nomor_sep = ?");
    $stmt->execute([$data['nomor_sep']]);
    $claim = $stmt->fetch();
    
    if (!$claim) {
        throw new Exception("Nomor SEP tidak ditemukan");
    }
    
    // Check if coder exists
    $stmt = $pdo->prepare("SELECT id FROM personnel WHERE nik = ? AND status = 'active'");
    $stmt->execute([$data['coder_nik']]);
    $coder = $stmt->fetch();
    
    if (!$coder) {
        throw new Exception("NIK Coder tidak ditemukan");
    }
    
    $pdo->beginTransaction();
    
    try {
        // Update claim data
        $updateFields = [];
        $updateValues = [];
        
        $fieldsToUpdate = [
            'tgl_pulang', 'cara_masuk', 'jenis_rawat', 'kelas_rawat',
            'adl_sub_acute', 'adl_chronic', 'icu_indikator', 'icu_los',
            'ventilator_hour', 'diagnosa', 'procedure', 'discharge_status'
        ];
        
        foreach ($fieldsToUpdate as $field) {
            if (isset($data[$field])) {
                $updateFields[] = "$field = ?";
                $updateValues[] = $data[$field];
            }
        }
        
        if (!empty($updateFields)) {
            $updateValues[] = $data['coder_nik'];
            $updateValues[] = $data['nomor_sep'];
            
            $sql = "UPDATE kunjungan_pasien SET " . implode(', ', $updateFields) . 
                   ", coder_nik = ?, updated_at = NOW() WHERE nomor_sep = ?";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($updateValues);
        }
        
        // Update tarif if provided
        if (isset($data['tarif_rs'])) {
            $tarifFields = [];
            $tarifValues = [];
            
            $tarifComponents = [
                'prosedur_non_bedah', 'prosedur_bedah', 'konsultasi',
                'tenaga_ahli', 'keperawatan', 'penunjang', 'radiologi',
                'laboratorium', 'pelayanan_darah', 'rehabilitasi',
                'kamar', 'rawat_intensif', 'obat', 'obat_kronis',
                'obat_kemoterapi', 'alkes', 'bmhp', 'sewa_alat'
            ];
            
            foreach ($tarifComponents as $component) {
                if (isset($data['tarif_rs'][$component])) {
                    $tarifFields[] = "$component = ?";
                    $tarifValues[] = $data['tarif_rs'][$component];
                }
            }
            
            if (!empty($tarifFields)) {
                $tarifValues[] = $claim['id'];
                
                $sql = "UPDATE detail_tarif SET " . implode(', ', $tarifFields) . 
                       ", updated_at = NOW() WHERE kunjungan_id = ?";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute($tarifValues);
            }
        }
        
        $pdo->commit();
        
        return [
            'metadata' => [
                'code' => 200,
                'message' => 'Ok'
            ]
        ];
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

/**
 * Handle grouper method
 */
function handleGrouper($data) {
    if (!isset($data['nomor_sep'])) {
        throw new Exception("Field nomor_sep wajib diisi");
    }
    
    $stage = $data['stage'] ?? '1';
    
    $pdo = getConnection();
    
    // Get claim data
    $stmt = $pdo->prepare("SELECT * FROM kunjungan_pasien WHERE nomor_sep = ?");
    $stmt->execute([$data['nomor_sep']]);
    $claim = $stmt->fetch();
    
    if (!$claim) {
        throw new Exception("Nomor SEP tidak ditemukan");
    }
    
    // Simulate grouping result (in real implementation, call E-Klaim web service)
    $groupingResult = simulateGrouping($claim, $stage, $data['special_cmg'] ?? null);
    
    // Save grouping result
    $sql = "INSERT INTO grouping_result (
        kunjungan_id, stage, cbg_code, cbg_description, cbg_tariff,
        sub_acute_code, sub_acute_description, sub_acute_tariff,
        chronic_code, chronic_description, chronic_tariff,
        kelas, add_payment_amt, inacbg_version, special_cmg
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $claim['id'],
        $stage,
        $groupingResult['cbg']['code'],
        $groupingResult['cbg']['description'],
        $groupingResult['cbg']['tariff'],
        $groupingResult['sub_acute']['code'],
        $groupingResult['sub_acute']['description'],
        $groupingResult['sub_acute']['tariff'],
        $groupingResult['chronic']['code'],
        $groupingResult['chronic']['description'],
        $groupingResult['chronic']['tariff'],
        $groupingResult['kelas'],
        $groupingResult['add_payment_amt'],
        $groupingResult['inacbg_version'],
        $data['special_cmg'] ?? null
    ]);
    
    // Update claim status
    $sql = "UPDATE kunjungan_pasien SET klaim_status = 'grouped' WHERE nomor_sep = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$data['nomor_sep']]);
    
    return [
        'metadata' => [
            'code' => 200,
            'message' => 'Ok'
        ],
        'response' => $groupingResult
    ];
}

/**
 * Simulate grouping result (replace with actual E-Klaim call)
 */
function simulateGrouping($claim, $stage, $specialCmg = null) {
    // This is a simulation - replace with actual E-Klaim web service call
    return [
        'cbg' => [
            'code' => 'M-1-04-II',
            'description' => 'PROSEDUR PADA SENDI TUNGKAI BAWAH (SEDANG)',
            'tariff' => '40388100'
        ],
        'sub_acute' => [
            'code' => 'SF-4-10-I',
            'description' => 'ADL Score: 15 (61 hari)',
            'tariff' => 5027400
        ],
        'chronic' => [
            'code' => 'CF-4-10-I',
            'description' => 'ADL Score: 12 (41 hari)',
            'tariff' => 1802200
        ],
        'kelas' => 'kelas_2',
        'add_payment_amt' => 18792000,
        'inacbg_version' => '5.4.2.202004202041'
    ];
}

/**
 * Handle file_upload method
 */
function handleFileUpload($data) {
    if (!isset($data['nomor_sep']) || !isset($data['file_class']) || !isset($data['file_name'])) {
        throw new Exception("Field nomor_sep, file_class, dan file_name wajib diisi");
    }
    
    $pdo = getConnection();
    
    // Get kunjungan_id
    $stmt = $pdo->prepare("SELECT id FROM kunjungan_pasien WHERE nomor_sep = ?");
    $stmt->execute([$data['nomor_sep']]);
    $kunjungan = $stmt->fetch();
    
    if (!$kunjungan) {
        throw new Exception("Nomor SEP tidak ditemukan");
    }
    
    // Get next file_id
    $stmt = $pdo->prepare("SELECT COALESCE(MAX(file_id), 0) + 1 as next_file_id FROM file_pendukung WHERE kunjungan_id = ?");
    $stmt->execute([$kunjungan['id']]);
    $result = $stmt->fetch();
    $fileId = $result['next_file_id'];
    
    // Insert file
    $sql = "INSERT INTO file_pendukung (
        kunjungan_id, file_id, file_name, file_type, file_size, 
        file_class, file_data
    ) VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $kunjungan['id'],
        $fileId,
        $data['file_name'],
        $data['file_type'] ?? 'application/pdf',
        strlen(base64_decode($data['data'] ?? '')),
        $data['file_class'],
        $data['data'] ?? ''
    ]);
    
    return [
        'metadata' => [
            'code' => 200,
            'message' => 'Ok'
        ],
        'response' => [
            'file_id' => $fileId,
            'file_name' => $data['file_name'],
            'file_type' => $data['file_type'] ?? 'application/pdf',
            'file_size' => strlen(base64_decode($data['data'] ?? '')),
            'file_class' => $data['file_class']
        ]
    ];
}

/**
 * Handle other methods (implement as needed)
 */
function handleGetClaimData($data) {
    // Implementation for get_claim_data
    throw new Exception("Method get_claim_data belum diimplementasi");
}

function handleClaimFinal($data) {
    // Implementation for claim_final
    throw new Exception("Method claim_final belum diimplementasi");
}

function handleSendClaim($data) {
    // Implementation for send_claim
    throw new Exception("Method send_claim belum diimplementasi");
}

function handleFileDelete($data) {
    // Implementation for file_delete
    throw new Exception("Method file_delete belum diimplementasi");
}

function handleFileGet($data) {
    // Implementation for file_get
    throw new Exception("Method file_get belum diimplementasi");
}

function handleSearchDiagnosis($data) {
    // Implementation for search_diagnosis
    throw new Exception("Method search_diagnosis belum diimplementasi");
}

function handleSearchProcedures($data) {
    // Implementation for search_procedures
    throw new Exception("Method search_procedures belum diimplementasi");
}

function handleGenerateClaimNumber($data) {
    // Implementation for generate_claim_number
    throw new Exception("Method generate_claim_number belum diimplementasi");
}

/**
 * Helper functions
 */
function shouldEncryptResponse() {
    // Check if response should be encrypted (not in debug mode)
    return !isset($_GET['debug']) || $_GET['debug'] !== '1';
}

function logWebServiceRequest($method, $data) {
    // Implementation for logging web service requests
    error_log("Web Service Request: $method - " . json_encode($data));
}

function logWebServiceResponse($method, $data, $response) {
    // Implementation for logging web service responses
    error_log("Web Service Response: $method - " . json_encode($response));
}

function logWebServiceError($method, $data, $error) {
    // Implementation for logging web service errors
    error_log("Web Service Error: $method - $error - " . json_encode($data));
}
?>
