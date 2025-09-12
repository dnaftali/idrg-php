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
require_once '../config/database.php';
require_once '../includes/import_coding_db.php';
require_once '../functions/eklaim_method_tracking.php';

/**
 * Check status grouping untuk nomor SEP
 */
function getGroupingStatus($nomorSep) {
    try {
        $pdo = getConnection();
        $sql = "SELECT grouping_status, grouping_result, grouped_at, grouping_error_message 
                FROM kunjungan_pasien 
                WHERE nomor_sep = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nomorSep]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error getting grouping status: " . $e->getMessage());
        return false;
    }
}

/**
 * Update status grouping untuk nomor SEP
 */
function updateGroupingStatus($nomorSep, $status, $result = null, $errorMessage = null) {
    try {
        $pdo = getConnection();
        $sql = "UPDATE kunjungan_pasien 
                SET grouping_status = ?, 
                    grouping_result = ?, 
                    grouped_at = NOW(), 
                    grouping_error_message = ?
                WHERE nomor_sep = ?";
        
        $stmt = $pdo->prepare($sql);
        $resultJson = $result ? json_encode($result, JSON_UNESCAPED_SLASHES) : null;
        
        return $stmt->execute([$status, $resultJson, $errorMessage, $nomorSep]);
    } catch (Exception $e) {
        error_log("Error updating grouping status: " . $e->getMessage());
        return false;
    }
}

/**
 * Simpan log web service ke database
 */
function saveWebServiceLog($method, $nomorSep, $requestData, $responseData, $status, $errorCode = null, $errorMessage = null, $executionTimeMs = null) {
    try {
        // Get database connection
        $pdo = getConnection();
        
        $sql = "INSERT INTO web_service_logs (
            method, 
            nomor_sep, 
            request_data, 
            response_data, 
            status, 
            error_code, 
            error_message, 
            execution_time_ms, 
            ip_address, 
            user_agent
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            $method,
            $nomorSep,
            json_encode($requestData, JSON_UNESCAPED_SLASHES),
            json_encode($responseData, JSON_UNESCAPED_SLASHES),
            $status,
            $errorCode,
            $errorMessage,
            $executionTimeMs,
            $ipAddress,
            $userAgent
        ]);
        
        if ($result) {
            error_log("Web service log saved successfully for method: $method, nomor_sep: $nomorSep");
            return $pdo->lastInsertId();
        } else {
            error_log("Failed to save web service log for method: $method, nomor_sep: $nomorSep");
            return false;
        }
        
    } catch (Exception $e) {
        error_log("Error saving web service log: " . $e->getMessage());
        return false;
    }
}

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
            
        case 'setInacbgDiagnosa':
            $result = handleSetInacbgDiagnosa($input);
            break;
            
        case 'setInacbgProcedure':
            $result = handleSetInacbgProcedure($input);
            break;
            
        case 'grouper':
            $result = handleGrouper($input);
            break;
            
        case 'idrg_grouper':
            // Same as grouper but specifically for IDRG
            $input['grouper'] = 'idrg';
            $input['stage'] = '1';
            $result = handleGrouper($input);
            break;
            
        case 'idrg_grouper_final':
            $result = handleIdrgGrouperFinal($input);
            break;
            
        case 'inacbg_grouper_final':
            $result = handleInacbgGrouperFinal($input);
            break;
            
        case 'idrg_grouper_reedit':
            $result = handleIdrgGrouperReedit($input);
            break;
            
        case 'inacbg_grouper_reedit':
            $result = handleInacbgGrouperReedit($input);
            break;
            
        case 'final_claim':
            $result = handleFinalClaim($input);
            break;
            
        case 'send_claim_online':
            $result = handleSendClaimOnline($input);
            break;
            
        case 'getImportHistory':
            $result = handleGetImportHistory($input);
            break;
            
        case 'get_inacbg_import_data':
            $result = handleGetInacbgImportData($input);
            break;
            
        case 'save_inacbg_to_import':
            $result = handleSaveInacbgToImport($input);
            break;
            
        case 'idrg_to_inacbg_import':
            $result = handleIdrgToInacbgImport($input);
            break;
            
        case 'checkGroupingStatus':
            $result = handleCheckGroupingStatus($input);
            break;
            
        case 'createNewClaim':
            $result = handleCreateNewClaim($input);
            break;
            
        case 'new_claim':
            $result = handleNewClaim($input);
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
    
    // Log untuk debugging
    error_log("handleSetClaimData called with nomor_sep: " . $nomorSep);
    error_log("handleSetClaimData claim_data: " . json_encode($claimData, JSON_UNESCAPED_SLASHES));
    
    // Log khusus untuk tarif_rs
    if (isset($claimData['tarif_rs'])) {
        error_log("handleSetClaimData tarif_rs found: " . json_encode($claimData['tarif_rs'], JSON_UNESCAPED_SLASHES));
    } else {
        error_log("handleSetClaimData tarif_rs NOT FOUND in claim_data");
    }
    
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
    
    // Normalisasi format data untuk E-Klaim
    $normalizedClaimData = normalizeClaimDataForEklaim($claimData);
    error_log("Normalized claim data: " . json_encode($normalizedClaimData, JSON_UNESCAPED_SLASHES));
    
    // Log khusus untuk tarif fields setelah normalisasi
    $tarifFields = [
        'prosedur_non_bedah', 'prosedur_bedah', 'konsultasi', 'tenaga_ahli',
        'keperawatan', 'penunjang', 'radiologi', 'laboratorium', 'pelayanan_darah',
        'rehabilitasi', 'kamar', 'rawat_intensif', 'obat', 'obat_kronis',
        'obat_kemoterapi', 'alkes', 'bmhp', 'sewa_alat'
    ];
    
    $tarifFound = 0;
    foreach ($tarifFields as $field) {
        if (isset($normalizedClaimData[$field])) {
            $tarifFound++;
        }
    }
    error_log("handleSetClaimData tarif fields found after normalization: $tarifFound out of " . count($tarifFields));
    
    // Periksa apakah nomor SEP ada di database lokal
    try {
        require_once '../config/database.php';
        $pdo = getConnection();
        $stmt = $pdo->prepare("SELECT id, nomor_sep, nama_pasien, eklaim_status FROM kunjungan_pasien WHERE nomor_sep = ?");
        $stmt->execute([$nomorSep]);
        $localClaim = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($localClaim) {
            error_log("SEP found in local database: " . json_encode($localClaim));
            
            // Periksa status E-Klaim
            if (isset($localClaim['eklaim_status'])) {
                error_log("E-Klaim status: " . $localClaim['eklaim_status']);
            }
        } else {
            error_log("SEP NOT found in local database: " . $nomorSep);
            
            // Cek SEP yang mirip
            $stmt = $pdo->prepare("SELECT nomor_sep, nama_pasien FROM kunjungan_pasien WHERE nomor_sep LIKE ? LIMIT 5");
            $stmt->execute(['%' . $nomorSep . '%']);
            $similar = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (!empty($similar)) {
                error_log("Similar SEPs found: " . json_encode($similar));
            } else {
                error_log("No similar SEPs found");
            }
        }
    } catch (Exception $e) {
        error_log("Database check error: " . $e->getMessage());
    }
    
    // Periksa konfigurasi E-Klaim
    error_log("E-Klaim configuration:");
    error_log("- EKLAIM_FULL_URL: " . EKLAIM_FULL_URL);
    error_log("- EKLAIM_TIMEOUT: " . EKLAIM_TIMEOUT);
    error_log("- EKLAIM_DEBUG_MODE: " . (EKLAIM_DEBUG_MODE ? 'true' : 'false'));
    
    // Log data yang akan dikirim ke E-Klaim
    error_log("Sending to E-Klaim - nomor_sep: " . $nomorSep);
    error_log("Sending to E-Klaim - normalized_claim_data: " . json_encode($normalizedClaimData, JSON_UNESCAPED_SLASHES));
    
    // Panggil fungsi setClaimData dari konfigurasi E-Klaim dengan data yang sudah dinormalisasi
    $result = setClaimData($nomorSep, $normalizedClaimData);
    
    // Log response dari E-Klaim
    error_log("E-Klaim response: " . json_encode($result, JSON_UNESCAPED_SLASHES));
    
    // Jika setClaimData berhasil, lanjutkan dengan IDRG methods
    if ($result['success'] && isset($result['data']['metadata']['code']) && $result['data']['metadata']['code'] === 200) {
        error_log("setClaimData successful, proceeding with IDRG methods");
        
        // Set IDRG Diagnosa jika ada data diagnosa
        if (isset($claimData['diagnosis']) && !empty($claimData['diagnosis'])) {
            $diagnosaString = formatDiagnosaForEklaim($claimData['diagnosis']);
            if (!empty($diagnosaString)) {
                error_log("Setting IDRG Diagnosa: " . $diagnosaString);
                $idrgDiagnosaResult = setIdrgDiagnosa($nomorSep, $diagnosaString);
                error_log("IDRG Diagnosa result: " . json_encode($idrgDiagnosaResult, JSON_UNESCAPED_SLASHES));
                
                // Tambahkan hasil IDRG diagnosa ke response
                $result['idrg_diagnosa'] = $idrgDiagnosaResult;
            }
        }
        
        // Set IDRG Procedure jika ada data prosedur
        if (isset($claimData['procedures']) && !empty($claimData['procedures'])) {
            $procedureString = formatProcedureForEklaim($claimData['procedures']);
            if (!empty($procedureString)) {
                error_log("Setting IDRG Procedure: " . $procedureString);
                $idrgProcedureResult = setIdrgProcedure($nomorSep, $procedureString);
                error_log("IDRG Procedure result: " . json_encode($idrgProcedureResult, JSON_UNESCAPED_SLASHES));
                
                // Tambahkan hasil IDRG prosedur ke response
                $result['idrg_procedure'] = $idrgProcedureResult;
            }
        }
    }
    
    // Handle error E2004 khusus
    if (isset($result['data']['metadata']['error_no']) && $result['data']['metadata']['error_no'] === 'E2004') {
        error_log("E2004 Error detected - SEP not found in E-Klaim server");
        error_log("Local SEP exists but E-Klaim server doesn't recognize it");
        
        // Tambahkan informasi tambahan ke response
        $result['debug_info'] = [
            'local_sep_exists' => true,
            'sep_number' => $nomorSep,
            'error_type' => 'E2004_SEP_NOT_FOUND_IN_EKLAIM_SERVER',
            'suggestion' => 'Please verify SEP registration in E-Klaim server or contact E-Klaim administrator'
        ];
    }
    
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
    
    // Validasi format diagnosa sederhana
    if (!preg_match('/^[A-Z0-9\.]+(\#[A-Z0-9\.]+)*$/', $diagnosa)) {
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
 * Handle Grouper request
 */
function handleIdrgGrouperFinal($input) {
    $nomorSep = $input['nomor_sep'] ?? '';
    
    if (empty($nomorSep)) {
        return [
            'success' => false,
            'error' => 'Nomor SEP tidak boleh kosong'
        ];
    }
    
    try {
        // Call finalizeIdrg function from eklaim_config.php
        $response = finalizeIdrg($nomorSep);
        
        // Log response untuk debugging
        error_log("handleIdrgGrouperFinal response: " . json_encode($response, JSON_UNESCAPED_SLASHES));
        
        // Check if response is successful from sendEklaimRequest
        if ($response && $response['success'] === true) {
            // Check metadata structure - bisa ada di response['data']['metadata'] atau response['metadata']
            $metadata = null;
            $metadataCode = null;
            $errorNo = null;
            $message = null;
            
            if (isset($response['data']['metadata'])) {
                $metadata = $response['data']['metadata'];
                $metadataCode = $metadata['code'] ?? null;
                $errorNo = $metadata['error_no'] ?? null;
                $message = $metadata['message'] ?? null;
            } elseif (isset($response['metadata'])) {
                $metadata = $response['metadata'];
                $metadataCode = $metadata['code'] ?? null;
                $errorNo = $metadata['error_no'] ?? null;
                $message = $metadata['message'] ?? null;
            }
            
            if ($metadataCode == 200) {
                return [
                    'success' => true,
                    'data' => $response,
                    'message' => 'Final iDRG berhasil diproses'
                ];
            } elseif ($metadataCode == 400 && $errorNo == 'E2102') {
                // iDRG sudah final - ini bukan error, tapi status yang valid
                return [
                    'success' => true,
                    'data' => $response,
                    'message' => 'iDRG sudah dalam status final',
                    'already_final' => true
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $message ?? 'Error processing Final iDRG',
                    'data' => $response
                ];
            }
        } else {
            // Response tidak successful dari sendEklaimRequest
            return [
                'success' => false,
                'error' => $response['error'] ?? 'Invalid response from E-Klaim',
                'data' => $response
            ];
        }
    } catch (Exception $e) {
        error_log("handleIdrgGrouperFinal exception: " . $e->getMessage());
        return [
            'success' => false,
            'error' => 'Error calling Final iDRG: ' . $e->getMessage()
        ];
    }
}

/**
 * Handle INACBG Grouper Final request
 */
function handleInacbgGrouperFinal($input) {
    $nomorSep = $input['nomor_sep'] ?? '';
    
    if (empty($nomorSep)) {
        return [
            'success' => false,
            'error' => 'Nomor SEP tidak boleh kosong'
        ];
    }
    
    try {
        // Call groupInacbgFinal function from eklaim_config.php
        $response = groupInacbgFinal($nomorSep);
        
        // Log response untuk debugging
        error_log("handleInacbgGrouperFinal response: " . json_encode($response, JSON_UNESCAPED_SLASHES));
        
        // Check if response is successful from sendEklaimRequest
        if ($response && $response['success'] === true) {
            // Check metadata structure - bisa ada di response['data']['metadata'] atau response['metadata']
            $metadata = null;
            $metadataCode = null;
            $errorNo = null;
            $message = null;
            
            if (isset($response['data']['metadata'])) {
                $metadata = $response['data']['metadata'];
                $metadataCode = $metadata['code'] ?? null;
                $errorNo = $metadata['error_no'] ?? null;
                $message = $metadata['message'] ?? null;
            } elseif (isset($response['metadata'])) {
                $metadata = $response['metadata'];
                $metadataCode = $metadata['code'] ?? null;
                $errorNo = $metadata['error_no'] ?? null;
                $message = $metadata['message'] ?? null;
            }
            
            if ($metadataCode == 200) {
                return [
                    'success' => true,
                    'data' => $response,
                    'message' => 'Final INACBG berhasil diproses'
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $message ?? 'Gagal melakukan Final INACBG',
                    'error_no' => $errorNo,
                    'data' => $response
                ];
            }
        } else {
            return [
                'success' => false,
                'error' => $response['error'] ?? 'Invalid response from E-Klaim',
                'data' => $response
            ];
        }
    } catch (Exception $e) {
        error_log("handleInacbgGrouperFinal exception: " . $e->getMessage());
        return [
            'success' => false,
            'error' => 'Error calling Final INACBG: ' . $e->getMessage()
        ];
    }
}

/**
 * Handle get INACBG import data request
 */
function handleGetInacbgImportData($input) {
    $nomorSep = $input['nomor_sep'] ?? '';
    
    if (empty($nomorSep)) {
        return [
            'success' => false,
            'error' => 'Nomor SEP tidak boleh kosong'
        ];
    }
    
    try {
        $importDB = new ImportCodingDB();
        $result = $importDB->getLatestImportDataForInacbg($nomorSep);
        
        return $result;
        
    } catch (Exception $e) {
        error_log("Error in handleGetInacbgImportData: " . $e->getMessage());
        return [
            'success' => false,
            'error' => 'Internal server error: ' . $e->getMessage()
        ];
    }
}

/**
 * Handle save INACBG data to import tables request
 */
function handleSaveInacbgToImport($input) {
    $nomorSep = $input['nomor_sep'] ?? '';
    $diagnosisData = $input['diagnosis'] ?? [];
    $procedureData = $input['procedure'] ?? [];
    
    if (empty($nomorSep)) {
        return [
            'success' => false,
            'error' => 'Nomor SEP tidak boleh kosong'
        ];
    }
    
    try {
        // Lakukan import delete terlebih dahulu untuk menghapus data lama
        $importDB = new ImportCodingDB();
        $deleteResult = $importDB->deleteImportDataBySep($nomorSep);
        
        if (!$deleteResult['success']) {
            return [
                'success' => false,
                'error' => 'Gagal menghapus data import lama: ' . $deleteResult['error']
            ];
        }
        
        // Log hasil delete
        error_log("INACBG import delete result for SEP {$nomorSep}: " . json_encode($deleteResult, JSON_UNESCAPED_SLASHES));
        
        // Simpan data baru ke import tables
        $saveResult = $importDB->saveImportData($nomorSep, [
            'diagnosis' => $diagnosisData,
            'procedure' => $procedureData,
            'metadata' => [
                'source' => 'inacbg_grouping',
                'method' => 'manual_input'
            ],
            'message' => 'Data INACBG disimpan dari grouping process'
        ]);
        
        if ($saveResult['success']) {
            return [
                'success' => true,
                'message' => 'Data INACBG berhasil disimpan ke import tables',
                'database' => [
                    'import_log_id' => $saveResult['import_log_id'],
                    'saved_diagnosis_count' => $saveResult['saved_diagnosis_count'],
                    'saved_procedure_count' => $saveResult['saved_procedure_count'],
                    'status' => $saveResult['status']
                ],
                'delete_info' => [
                    'deleted_log_count' => $deleteResult['deleted_log_count'],
                    'deleted_diagnosis_count' => $deleteResult['deleted_diagnosis_count'],
                    'deleted_procedure_count' => $deleteResult['deleted_procedure_count']
                ]
            ];
        } else {
            return [
                'success' => false,
                'error' => 'Gagal menyimpan data INACBG: ' . $saveResult['error']
            ];
        }
        
    } catch (Exception $e) {
        error_log("Error in handleSaveInacbgToImport: " . $e->getMessage());
        return [
            'success' => false,
            'error' => 'Internal server error: ' . $e->getMessage()
        ];
    }
}

/**
 * Handle IDRG to INACBG Import request
 */
function handleIdrgToInacbgImport($input) {
    $nomorSep = $input['nomor_sep'] ?? '';
    
    if (empty($nomorSep)) {
        return [
            'success' => false,
            'error' => 'Nomor SEP tidak boleh kosong'
        ];
    }
    
    try {
        // Lakukan import delete terlebih dahulu untuk menghapus data lama
        $importDB = new ImportCodingDB();
        $deleteResult = $importDB->deleteImportDataBySep($nomorSep);
        
        if (!$deleteResult['success']) {
            return [
                'success' => false,
                'error' => 'Gagal menghapus data import lama: ' . $deleteResult['error']
            ];
        }
        
        // Log hasil delete
        error_log("Import delete result for SEP {$nomorSep}: " . json_encode($deleteResult, JSON_UNESCAPED_SLASHES));
        
        // Call importIdrgToInacbg function from eklaim_config.php
        $response = importIdrgToInacbg($nomorSep);
        
        // Log response untuk debugging
        error_log("handleIdrgToInacbgImport response: " . json_encode($response, JSON_UNESCAPED_SLASHES));
        
        // Check if response is successful from sendEklaimRequest
        if ($response && $response['success'] === true) {
            // Check metadata structure
            $metadata = null;
            $metadataCode = null;
            $errorNo = null;
            $message = null;
            
            if (isset($response['data']['metadata'])) {
                $metadata = $response['data']['metadata'];
                $metadataCode = $metadata['code'] ?? null;
                $errorNo = $metadata['error_no'] ?? null;
                $message = $metadata['message'] ?? null;
            } elseif (isset($response['metadata'])) {
                $metadata = $response['metadata'];
                $metadataCode = $metadata['code'] ?? null;
                $errorNo = $metadata['error_no'] ?? null;
                $message = $metadata['message'] ?? null;
            }
            
            // Check if metadata indicates success
            if ($metadataCode == 200) {
                // Parse response data untuk diagnosa dan prosedur
                $data = $response['data']['data'] ?? [];
                $diagnosisData = [];
                $procedureData = [];
                
                // Parse diagnosa data
                if (isset($data['diagnosa']['expanded']) && is_array($data['diagnosa']['expanded'])) {
                    foreach ($data['diagnosa']['expanded'] as $diagnosis) {
                        $diagnosisData[] = [
                            'diagnosis_type' => '1', // Default primer
                            'icd_code' => $diagnosis['code'] ?? '',
                            'icd_description' => $diagnosis['display'] ?? ''
                        ];
                    }
                }
                
                // Parse prosedur data
                if (isset($data['procedure']['expanded']) && is_array($data['procedure']['expanded'])) {
                    foreach ($data['procedure']['expanded'] as $procedure) {
                        $procedureData[] = [
                            'procedure_type' => '1', // Default primer
                            'icd_code' => $procedure['code'] ?? '',
                            'icd_description' => $procedure['display'] ?? '',
                            'quantity' => 1
                        ];
                    }
                }
                
                // Simpan data import ke database
                $importDB = new ImportCodingDB();
                $saveResult = $importDB->saveImportData($nomorSep, [
                    'diagnosis' => $diagnosisData,
                    'procedure' => $procedureData,
                    'metadata' => $metadata,
                    'message' => 'Import coding berhasil'
                ]);
                
                if ($saveResult['success']) {
                    return [
                        'success' => true,
                        'message' => 'Import coding berhasil',
                        'data' => [
                            'diagnosis' => $diagnosisData,
                            'procedure' => $procedureData
                        ],
                        'metadata' => $metadata,
                        'database' => [
                            'import_log_id' => $saveResult['import_log_id'],
                            'saved_diagnosis_count' => $saveResult['saved_diagnosis_count'],
                            'saved_procedure_count' => $saveResult['saved_procedure_count'],
                            'status' => $saveResult['status']
                        ],
                        'delete_info' => [
                            'deleted_log_count' => $deleteResult['deleted_log_count'],
                            'deleted_diagnosis_count' => $deleteResult['deleted_diagnosis_count'],
                            'deleted_procedure_count' => $deleteResult['deleted_procedure_count']
                        ]
                    ];
                } else {
                    // Jika gagal menyimpan ke database, tetap return data tapi dengan warning
                    return [
                        'success' => true,
                        'message' => 'Import coding berhasil, namun gagal menyimpan ke database',
                        'data' => [
                            'diagnosis' => $diagnosisData,
                            'procedure' => $procedureData
                        ],
                        'metadata' => $metadata,
                        'database_error' => $saveResult['error']
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'error' => $message ?? 'Import coding gagal',
                    'error_code' => $errorNo,
                    'metadata' => $metadata
                ];
            }
        } else {
            return [
                'success' => false,
                'error' => $response['error'] ?? 'Gagal mengimport coding',
                'response' => $response
            ];
        }
    } catch (Exception $e) {
        error_log("handleIdrgToInacbgImport exception: " . $e->getMessage());
        return [
            'success' => false,
            'error' => 'Error calling IDRG to INACBG Import: ' . $e->getMessage()
        ];
    }
}

function handleGrouper($input) {
    $nomorSep = $input['nomor_sep'] ?? '';
    $stage = $input['stage'] ?? '1';
    $grouper = $input['grouper'] ?? 'idrg';
    $forceApiCall = $input['force_api_call'] ?? false;
    
    // Start timing untuk execution time
    $startTime = microtime(true);
    
    // Prepare request data untuk logging
    $requestData = [
        'nomor_sep' => $nomorSep,
        'stage' => $stage,
        'grouper' => $grouper
    ];
    
    if (empty($nomorSep)) {
        $errorResponse = [
            'success' => false,
            'error' => 'nomor_sep is required'
        ];
        
        // Log error ke database
        $executionTime = round((microtime(true) - $startTime) * 1000);
        saveWebServiceLog('grouper', $nomorSep, $requestData, $errorResponse, 'error', 'E4001', 'nomor_sep is required', $executionTime);
        
        return $errorResponse;
    }
    
    // Validasi stage
    if (!in_array($stage, ['1', '2', 'final'])) {
        $errorResponse = [
            'success' => false,
            'error' => 'Invalid stage. Must be 1, 2, or final'
        ];
        
        // Log error ke database
        $executionTime = round((microtime(true) - $startTime) * 1000);
        saveWebServiceLog('grouper', $nomorSep, $requestData, $errorResponse, 'error', 'E4002', 'Invalid stage. Must be 1, 2, or final', $executionTime);
        
        return $errorResponse;
    }
    
    // Validasi grouper type
    if (!in_array($grouper, ['idrg', 'inacbg'])) {
        $errorResponse = [
            'success' => false,
            'error' => 'Invalid grouper type. Must be idrg or inacbg'
        ];
        
        // Log error ke database
        $executionTime = round((microtime(true) - $startTime) * 1000);
        saveWebServiceLog('grouper', $nomorSep, $requestData, $errorResponse, 'error', 'E4003', 'Invalid grouper type. Must be idrg or inacbg', $executionTime);
        
        return $errorResponse;
    }
    
    // Log request untuk debugging
    error_log("handleGrouper called with nomor_sep: " . $nomorSep . ", stage: " . $stage . ", grouper: " . $grouper);
    
    // Panggil fungsi grouper sesuai tipe
    if ($grouper === 'idrg') {
        $result = groupIdrg($nomorSep, $forceApiCall);
    } else {
        // Untuk INACBG, perlu handle stage 1, 2, dan final
        if ($stage === '1') {
            $result = groupInacbgStage1($nomorSep);
        } elseif ($stage === '2') {
            $specialCmg = $input['special_cmg'] ?? '';
            $result = groupInacbgStage2($nomorSep, $specialCmg);
        } elseif ($stage === 'final') {
            $result = groupInacbgFinal($nomorSep);
        }
    }
    
    // Calculate execution time
    $executionTime = round((microtime(true) - $startTime) * 1000);
    
    // Log response untuk debugging
    error_log("Grouper response: " . json_encode($result, JSON_UNESCAPED_SLASHES));
    
    // Determine status dan error info untuk logging
    $status = 'success';
    $errorCode = null;
    $errorMessage = null;
    
    if (isset($result['success']) && !$result['success']) {
        $status = 'error';
        $errorMessage = $result['error'] ?? 'Unknown error';
        $errorCode = 'E5000'; // Generic error code
    }
    
    // Update grouping status di database
    if ($status === 'success') {
        updateGroupingStatus($nomorSep, 'success', $result);
    } else {
        updateGroupingStatus($nomorSep, 'error', null, $errorMessage);
    }
    
    // Log ke database
    saveWebServiceLog('grouper', $nomorSep, $requestData, $result, $status, $errorCode, $errorMessage, $executionTime);
    
    return $result;
}

/**
 * Handle checkGroupingStatus request
 */
function handleCheckGroupingStatus($input) {
    $nomorSep = $input['nomor_sep'] ?? '';
    
    if (empty($nomorSep)) {
        return [
            'success' => false,
            'error' => 'nomor_sep is required'
        ];
    }
    
    $status = getGroupingStatus($nomorSep);
    
    if ($status === false) {
        return [
            'success' => false,
            'error' => 'Failed to get grouping status'
        ];
    }
    
    return [
        'success' => true,
        'data' => $status
    ];
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
    
    // Validasi format prosedur sederhana
    // Menerima format "#" untuk prosedur kosong atau format ICD9#ICD9+multiplier#ICD9
    if ($procedure !== '#' && !preg_match('/^[0-9\.]+(\+[0-9]+)?(\#[0-9\.]+(\+[0-9]+)?)*$/', $procedure)) {
        return [
            'success' => false,
            'error' => 'Invalid procedure format. Expected format: ICD9#ICD9+multiplier#ICD9 or "#" for empty procedure'
        ];
    }
    
    // Panggil fungsi setIdrgProcedure dari konfigurasi E-Klaim
    $result = setIdrgProcedure($nomorSep, $procedure);
    
    return $result;
}

/**
 * Handle setInacbgDiagnosa request
 */
function handleSetInacbgDiagnosa($input) {
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
    
    // Validasi format diagnosa ICD-10
    if (!validateDiagnosa($diagnosa)) {
        return [
            'success' => false,
            'error' => 'Invalid diagnosa format. Expected format: ICD10#ICD10'
        ];
    }
    
    // Panggil fungsi setInacbgDiagnosa dari konfigurasi E-Klaim
    $result = setInacbgDiagnosa($nomorSep, $diagnosa);
    
    return $result;
}

/**
 * Handle setInacbgProcedure request
 */
function handleSetInacbgProcedure($input) {
    $nomorSep = $input['nomor_sep'] ?? '';
    $procedure = $input['procedure'] ?? '';
    
    if (empty($nomorSep)) {
        return [
            'success' => false,
            'error' => 'Nomor SEP is required'
        ];
    }
    
    // Validasi format prosedur ICD-9 (INACBG tidak menggunakan multiplier)
    if (!empty($procedure) && $procedure !== '#') {
        if (!validateProcedure($procedure)) {
            return [
                'success' => false,
                'error' => 'Invalid procedure format. Expected format: ICD9#ICD9#ICD9 or "#" for empty procedure'
            ];
        }
    }
    
    // Panggil fungsi setInacbgProcedure dari konfigurasi E-Klaim
    $result = setInacbgProcedure($nomorSep, $procedure);
    
    return $result;
}

/**
 * Handle createNewClaim request
 */
function handleCreateNewClaim($input) {
    $patientId = $input['patient_id'] ?? '';
    
    if (empty($patientId)) {
        return [
            'success' => false,
            'error' => 'Patient ID is required'
        ];
    }
    
    try {
        // Include database connection
        require_once '../config/database.php';
        
        // Get patient data from database
        $pdo = getConnection();
        $stmt = $pdo->prepare("
            SELECT 
                id, nomor_sep, nomor_kartu, nomor_rm, nama_pasien, 
                tgl_lahir, gender, tgl_masuk, tgl_pulang, cara_masuk,
                jenis_rawat, kelas_rawat, discharge_status, adl_sub_acute
            FROM kunjungan_pasien 
            WHERE id = ?
        ");
        $stmt->execute([$patientId]);
        $patient = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$patient) {
            return [
                'success' => false,
                'error' => 'Patient not found'
            ];
        }
        
        // Prepare claim data for E-Klaim
        $claimData = [
            'nomor_kartu' => $patient['nomor_kartu'],
            'nomor_sep' => $patient['nomor_sep'],
            'nomor_rm' => $patient['nomor_rm'],
            'nama_pasien' => $patient['nama_pasien'],
            'tgl_lahir' => $patient['tgl_lahir'],
            'gender' => $patient['gender'],
            'tgl_masuk' => $patient['tgl_masuk'],
            'tgl_pulang' => $patient['tgl_pulang'],
            'cara_masuk' => $patient['cara_masuk'],
            'jenis_rawat' => $patient['jenis_rawat'],
            'kelas_rawat' => $patient['kelas_rawat'],
            'discharge_status' => $patient['discharge_status'],
            'adl_sub_acute' => $patient['adl_sub_acute']
        ];
        
        // For now, just return success since we're not actually calling E-Klaim
        // In real implementation, you would call the E-Klaim web service here
        return [
            'success' => true,
            'message' => 'New claim prepared successfully',
            'patient_id' => $patientId,
            'nomor_sep' => $patient['nomor_sep'],
            'claim_data' => $claimData
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => 'Failed to create new claim: ' . $e->getMessage()
        ];
    }
}

/**
 * Normalisasi data claim untuk E-Klaim
 * Memastikan format data sesuai dengan yang diharapkan server E-Klaim
 */
function normalizeClaimDataForEklaim($claimData) {
    $normalized = [];
    
    // Field yang diperlukan untuk E-Klaim set_claim_data (berdasarkan Postman collection)
    $requiredFields = [
        'nomor_kartu', 'tgl_masuk', 'tgl_pulang', 'cara_masuk',
        'jenis_rawat', 'kelas_rawat', 'discharge_status'
    ];
    
    // Copy field yang diperlukan
    foreach ($requiredFields as $field) {
        if (isset($claimData[$field])) {
            $normalized[$field] = $claimData[$field];
        }
    }
    
    // Field opsional yang diperbolehkan
    $optionalFields = [
        'adl_sub_acute', 'adl_chronic', 'icu_indikator', 'icu_los', 
        'upgrade_class_ind', 'add_payment_pct', 'birth_weight', 
        'sistole', 'diastole', 'coder_nik', 'payor_id', 'payor_cd',
        'nama_dokter', 'tarif_poli_eks', 'kode_tarif', 'cob_cd'
    ];
    
    foreach ($optionalFields as $field) {
        if (isset($claimData[$field])) {
            $normalized[$field] = (string)$claimData[$field];
        }
    }
    
    // Handle tarif_rs jika ada - pertahankan sebagai nested object
    if (isset($claimData['tarif_rs']) && is_array($claimData['tarif_rs'])) {
        $tarifRs = $claimData['tarif_rs'];
        $tarifFields = [
            'prosedur_non_bedah', 'prosedur_bedah', 'konsultasi', 'tenaga_ahli',
            'keperawatan', 'penunjang', 'radiologi', 'laboratorium', 'pelayanan_darah',
            'rehabilitasi', 'kamar', 'rawat_intensif', 'obat', 'obat_kronis',
            'obat_kemoterapi', 'alkes', 'bmhp', 'sewa_alat'
        ];
        
        // Buat tarif_rs object yang sudah dinormalisasi
        $normalizedTarifRs = [];
        foreach ($tarifFields as $field) {
            if (isset($tarifRs[$field])) {
                $normalizedTarifRs[$field] = (string)$tarifRs[$field];
            }
        }
        
        // Tambahkan tarif_rs sebagai nested object
        $normalized['tarif_rs'] = $normalizedTarifRs;
    }
    
    // Tambahkan field default jika tidak ada
    $defaults = [
        'adl_sub_acute' => '0',
        'adl_chronic' => '0',
        'icu_indikator' => '0',
        'icu_los' => '0',
        'upgrade_class_ind' => '0',
        'add_payment_pct' => '0',
        'birth_weight' => '0',
        'sistole' => '0',
        'diastole' => '0',
        'payor_id' => '3',
        'payor_cd' => 'JKN'
    ];
    
    foreach ($defaults as $field => $defaultValue) {
        if (!isset($normalized[$field])) {
            $normalized[$field] = $defaultValue;
        }
    }
    
    return $normalized;
}


/**
 * Format diagnosa data untuk E-Klaim IDRG
 * Mengkonversi array diagnosa menjadi string format E-Klaim
 */
function formatDiagnosaForEklaim($diagnosisData) {
    if (empty($diagnosisData) || !is_array($diagnosisData)) {
        return '';
    }
    
    $diagnosaCodes = [];
    foreach ($diagnosisData as $diagnosis) {
        if (isset($diagnosis['icd_code']) && !empty($diagnosis['icd_code'])) {
            $diagnosaCodes[] = $diagnosis['icd_code'];
        }
    }
    
    return implode('#', $diagnosaCodes);
}

/**
 * Format prosedur data untuk E-Klaim IDRG
 * Mengkonversi array prosedur menjadi string format E-Klaim
 */
function formatProcedureForEklaim($procedureData) {
    if (empty($procedureData) || !is_array($procedureData)) {
        return '';
    }
    
    $procedureCodes = [];
    foreach ($procedureData as $procedure) {
        if (isset($procedure['icd_code']) && !empty($procedure['icd_code'])) {
            $code = $procedure['icd_code'];
            $quantity = isset($procedure['quantity']) ? intval($procedure['quantity']) : 1;
            
            if ($quantity > 1) {
                $code .= '+' . $quantity . '#' . $code;
            }
            
            $procedureCodes[] = $code;
        }
    }
    
    return implode('#', $procedureCodes);
}

/**
 * Handle new_claim request
 * Mendaftarkan SEP baru ke server E-Klaim
 */
function handleNewClaim($input) {
    $patientId = $input['patient_id'] ?? '';
    
    if (empty($patientId)) {
        return [
            'success' => false,
            'error' => 'Patient ID is required'
        ];
    }
    
    try {
        // Include database connection
        require_once '../config/database.php';
        
        // Get patient data from database
        $pdo = getConnection();
        $stmt = $pdo->prepare("
            SELECT 
                id, nomor_sep, nomor_kartu, nomor_rm, nama_pasien, 
                tgl_lahir, gender, tgl_masuk, tgl_pulang, cara_masuk,
                jenis_rawat, kelas_rawat, discharge_status, adl_sub_acute
            FROM kunjungan_pasien 
            WHERE id = ?
        ");
        $stmt->execute([$patientId]);
        $patient = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$patient) {
            return [
                'success' => false,
                'error' => 'Patient not found'
            ];
        }
        
        // Log untuk debugging
        error_log("handleNewClaim called for patient_id: " . $patientId);
        error_log("Patient data: " . json_encode($patient, JSON_UNESCAPED_SLASHES));
        
        // Prepare patient data for E-Klaim new_claim
        $patientData = [
            'nomor_kartu' => $patient['nomor_kartu'],
            'nomor_sep' => $patient['nomor_sep'],
            'nomor_rm' => $patient['nomor_rm'],
            'nama_pasien' => $patient['nama_pasien'],
            'tgl_lahir' => $patient['tgl_lahir'],
            'gender' => $patient['gender']
        ];
        
        // Log data yang akan dikirim ke E-Klaim
        error_log("Sending new_claim to E-Klaim: " . json_encode($patientData, JSON_UNESCAPED_SLASHES));
        
        // Panggil fungsi createNewClaim dari konfigurasi E-Klaim
        $result = createNewClaim($patientData);
        
        // Log response dari E-Klaim
        error_log("E-Klaim new_claim response: " . json_encode($result, JSON_UNESCAPED_SLASHES));
        
        // Jika berhasil, update status E-Klaim di database
        if ($result['success'] && isset($result['data']['metadata']['code']) && $result['data']['metadata']['code'] === 200) {
            $stmt = $pdo->prepare("UPDATE kunjungan_pasien SET eklaim_status = 'registered' WHERE id = ?");
            $stmt->execute([$patientId]);
            error_log("Updated E-Klaim status to 'registered' for patient_id: " . $patientId);
        }
        
        return [
            'success' => true,
            'message' => 'New claim processed successfully',
            'patient_id' => $patientId,
            'nomor_sep' => $patient['nomor_sep'],
            'eklaim_result' => $result
        ];
        
    } catch (Exception $e) {
        error_log("Error in handleNewClaim: " . $e->getMessage());
        return [
            'success' => false,
            'error' => 'Failed to process new claim: ' . $e->getMessage()
        ];
    }
}

/**
 * Handle IDRG Grouper Re-edit request
 */
function handleIdrgGrouperReedit($input) {
    $nomorSep = $input['nomor_sep'] ?? '';
    
    if (empty($nomorSep)) {
        return [
            'success' => false,
            'error' => 'Nomor SEP tidak boleh kosong'
        ];
    }
    
    try {
        // Call reeditIdrg function from eklaim_config.php
        $response = reeditIdrg($nomorSep);
        
        // Log response untuk debugging
        error_log("handleIdrgGrouperReedit response: " . json_encode($response, JSON_UNESCAPED_SLASHES));
        
        // Check if response is successful from sendEklaimRequest
        if ($response && $response['success'] === true) {
            // Check metadata structure
            $metadata = null;
            $metadataCode = null;
            $errorNo = null;
            $message = null;
            
            if (isset($response['data']['metadata'])) {
                $metadata = $response['data']['metadata'];
                $metadataCode = $metadata['code'] ?? null;
                $errorNo = $metadata['error_no'] ?? null;
                $message = $metadata['message'] ?? null;
            } elseif (isset($response['metadata'])) {
                $metadata = $response['metadata'];
                $metadataCode = $metadata['code'] ?? null;
                $errorNo = $metadata['error_no'] ?? null;
                $message = $metadata['message'] ?? null;
            }
            
            // Check if metadata indicates success
            if ($metadataCode == 200) {
                // Hapus record final_idrg dari tracking setelah re-edit berhasil
                $deleteResult = deleteFinalIdrgTracking($nomorSep);
                
                // Log hasil penghapusan
                error_log("deleteFinalIdrgTracking result: " . json_encode($deleteResult, JSON_UNESCAPED_SLASHES));
                
                return [
                    'success' => true,
                    'message' => 'Re-edit iDRG berhasil',
                    'metadata' => $metadata,
                    'tracking' => [
                        'final_idrg_deleted' => $deleteResult['success'],
                        'deleted_rows' => $deleteResult['deleted_rows'] ?? 0,
                        'delete_message' => $deleteResult['message'] ?? $deleteResult['error'] ?? 'Unknown'
                    ]
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $message ?? 'Re-edit iDRG gagal',
                    'error_code' => $errorNo,
                    'metadata' => $metadata
                ];
            }
        } else {
            return [
                'success' => false,
                'error' => $response['error'] ?? 'Gagal melakukan re-edit iDRG',
                'response' => $response
            ];
        }
    } catch (Exception $e) {
        error_log("handleIdrgGrouperReedit exception: " . $e->getMessage());
        return [
            'success' => false,
            'error' => 'Error calling Re-edit iDRG: ' . $e->getMessage()
        ];
    }
}

/**
 * Handle INACBG Grouper Re-edit request
 */
function handleInacbgGrouperReedit($input) {
    $nomorSep = $input['nomor_sep'] ?? '';
    
    if (empty($nomorSep)) {
        return [
            'success' => false,
            'error' => 'Nomor SEP tidak boleh kosong'
        ];
    }
    
    try {
        // Call reeditInacbg function from eklaim_config.php
        $response = reeditInacbg($nomorSep);
        
        // Log response untuk debugging
        error_log("handleInacbgGrouperReedit response: " . json_encode($response, JSON_UNESCAPED_SLASHES));
        
        // Check if response is successful from sendEklaimRequest
        if ($response && $response['success'] === true) {
            // Check metadata structure
            $metadata = null;
            $metadataCode = null;
            $errorNo = null;
            $message = null;
            
            if (isset($response['data']['metadata'])) {
                $metadata = $response['data']['metadata'];
                $metadataCode = $metadata['code'] ?? null;
                $errorNo = $metadata['error_no'] ?? null;
                $message = $metadata['message'] ?? null;
            } elseif (isset($response['metadata'])) {
                $metadata = $response['metadata'];
                $metadataCode = $metadata['code'] ?? null;
                $errorNo = $metadata['error_no'] ?? null;
                $message = $metadata['message'] ?? null;
            }
            
            if ($metadataCode == 200) {
                return [
                    'success' => true,
                    'data' => $response,
                    'message' => 'Re-edit INACBG berhasil diproses'
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $message ?? 'Gagal melakukan Re-edit INACBG',
                    'error_no' => $errorNo,
                    'data' => $response
                ];
            }
        } else {
            return [
                'success' => false,
                'error' => $response['error'] ?? 'Gagal melakukan Re-edit INACBG',
                'response' => $response
            ];
        }
    } catch (Exception $e) {
        error_log("handleInacbgGrouperReedit exception: " . $e->getMessage());
        return [
            'success' => false,
            'error' => 'Error calling Re-edit INACBG: ' . $e->getMessage()
        ];
    }
}

/**
 * Handle Final Claim request
 */
function handleFinalClaim($input) {
    $nomorSep = $input['nomor_sep'] ?? '';
    
    if (empty($nomorSep)) {
        return [
            'success' => false,
            'error' => 'Nomor SEP tidak boleh kosong'
        ];
    }
    
    try {
        // Get coder_nik from input (required for claim_final)
        $coderNik = $input['coder_nik'] ?? '';
        
        if (empty($coderNik)) {
            return [
                'success' => false,
                'error' => 'NIK Coder tidak boleh kosong untuk Final Klaim'
            ];
        }
        
        // Call claimFinal function from eklaim_config.php
        $response = claimFinal($nomorSep, $coderNik);
        
        // Log response untuk debugging
        error_log("handleFinalClaim response: " . json_encode($response, JSON_UNESCAPED_SLASHES));
        
        // Check if response is successful from sendEklaimRequest
        if ($response && $response['success'] === true) {
            // Check metadata structure
            $metadata = null;
            $metadataCode = null;
            $errorNo = null;
            $message = null;
            
            if (isset($response['data']['metadata'])) {
                $metadata = $response['data']['metadata'];
                $metadataCode = $metadata['code'] ?? null;
                $errorNo = $metadata['error_no'] ?? null;
                $message = $metadata['message'] ?? null;
            } elseif (isset($response['metadata'])) {
                $metadata = $response['metadata'];
                $metadataCode = $metadata['code'] ?? null;
                $errorNo = $metadata['error_no'] ?? null;
                $message = $metadata['message'] ?? null;
            }
            
            if ($metadataCode == 200) {
                return [
                    'success' => true,
                    'data' => $response,
                    'message' => 'Final Klaim berhasil diproses'
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $message ?? 'Gagal melakukan Final Klaim',
                    'error_no' => $errorNo,
                    'data' => $response
                ];
            }
        } else {
            return [
                'success' => false,
                'error' => $response['error'] ?? 'Gagal melakukan Final Klaim',
                'response' => $response
            ];
        }
    } catch (Exception $e) {
        error_log("handleFinalClaim exception: " . $e->getMessage());
        return [
            'success' => false,
            'error' => 'Error calling Final Klaim: ' . $e->getMessage()
        ];
    }
}

/**
 * Handle Send Claim Online request
 */
function handleSendClaimOnline($input) {
    $nomorSep = $input['nomor_sep'] ?? '';
    
    if (empty($nomorSep)) {
        return [
            'success' => false,
            'error' => 'Nomor SEP tidak boleh kosong'
        ];
    }
    
    try {
        // Call sendClaim function from eklaim_config.php
        $response = sendClaim($nomorSep);
        
        // Log response untuk debugging
        error_log("handleSendClaimOnline response: " . json_encode($response, JSON_UNESCAPED_SLASHES));
        
        // Check if response is successful from sendEklaimRequest
        if ($response && $response['success'] === true) {
            // Check metadata structure
            $metadata = null;
            $metadataCode = null;
            $errorNo = null;
            $message = null;
            
            if (isset($response['data']['metadata'])) {
                $metadata = $response['data']['metadata'];
                $metadataCode = $metadata['code'] ?? null;
                $errorNo = $metadata['error_no'] ?? null;
                $message = $metadata['message'] ?? null;
            } elseif (isset($response['metadata'])) {
                $metadata = $response['metadata'];
                $metadataCode = $metadata['code'] ?? null;
                $errorNo = $metadata['error_no'] ?? null;
                $message = $metadata['message'] ?? null;
            }
            
            if ($metadataCode == 200) {
                return [
                    'success' => true,
                    'data' => $response,
                    'message' => 'Klaim berhasil dikirim online ke BPJS Kesehatan'
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $message ?? 'Gagal mengirim klaim online',
                    'error_no' => $errorNo,
                    'data' => $response
                ];
            }
        } else {
            return [
                'success' => false,
                'error' => $response['error'] ?? 'Gagal mengirim klaim online',
                'response' => $response
            ];
        }
    } catch (Exception $e) {
        error_log("handleSendClaimOnline exception: " . $e->getMessage());
        return [
            'success' => false,
            'error' => 'Error calling Send Claim Online: ' . $e->getMessage()
        ];
    }
}

/**
 * Handle Get Import History request
 */
function handleGetImportHistory($input) {
    $nomorSep = $input['nomor_sep'] ?? '';
    
    if (empty($nomorSep)) {
        return [
            'success' => false,
            'error' => 'Nomor SEP tidak boleh kosong'
        ];
    }
    
    try {
        $importDB = new ImportCodingDB();
        
        // Ambil data import history
        $importHistory = $importDB->getImportDataBySep($nomorSep);
        
        if (empty($importHistory)) {
            return [
                'success' => true,
                'message' => 'Tidak ada data import untuk nomor SEP ini',
                'data' => []
            ];
        }
        
        // Ambil detail untuk setiap import
        $detailedHistory = [];
        foreach ($importHistory as $import) {
            $diagnosis = $importDB->getImportDiagnosis($import['id']);
            $procedure = $importDB->getImportProcedure($import['id']);
            
            $detailedHistory[] = [
                'import_log' => $import,
                'diagnosis' => $diagnosis,
                'procedure' => $procedure
            ];
        }
        
        return [
            'success' => true,
            'message' => 'Data import history berhasil diambil',
            'data' => $detailedHistory
        ];
        
    } catch (Exception $e) {
        error_log("handleGetImportHistory exception: " . $e->getMessage());
        return [
            'success' => false,
            'error' => 'Error getting import history: ' . $e->getMessage()
        ];
    }
}
?>
