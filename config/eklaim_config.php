<?php
/**
 * Konfigurasi Server E-Klaim
 * File konfigurasi untuk koneksi ke server E-Klaim
 * Berdasarkan koleksi Postman E-KLAIM IDRG
 */

// Konfigurasi Server E-Klaim
define('EKLAIM_BASE_URL', 'http://10.10.1.63');
define('EKLAIM_ENDPOINT', '/E-Klaim/ws.php');
define('EKLAIM_DEBUG_MODE', true); // Set false untuk production

// URL lengkap endpoint E-Klaim
define('EKLAIM_FULL_URL', EKLAIM_BASE_URL . EKLAIM_ENDPOINT . (EKLAIM_DEBUG_MODE ? '?mode=debug' : ''));

// Timeout untuk request (dalam detik)
define('EKLAIM_TIMEOUT', 30);

// Konfigurasi header untuk request
define('EKLAIM_CONTENT_TYPE', 'application/json');
define('EKLAIM_ACCEPT', 'application/json');

// Error messages
define('EKLAIM_ERROR_TIMEOUT', 'Timeout koneksi ke server E-Klaim');
define('EKLAIM_ERROR_CONNECTION', 'Gagal terhubung ke server E-Klaim');
define('EKLAIM_ERROR_INVALID_RESPONSE', 'Response tidak valid dari server E-Klaim');

/**
 * Fungsi untuk membuat request ke E-Klaim
 * @param array $data Data yang akan dikirim
 * @return array Response dari server E-Klaim
 */
function sendEklaimRequest($data) {
    $url = EKLAIM_FULL_URL;
    
    // Prepare request data
    $requestData = json_encode($data, JSON_UNESCAPED_SLASHES);
    
    // Initialize cURL
    $ch = curl_init();
    
    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $requestData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, EKLAIM_TIMEOUT);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: ' . EKLAIM_CONTENT_TYPE,
        'Accept: ' . EKLAIM_ACCEPT,
        'Content-Length: ' . strlen($requestData)
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
    // Execute request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    
    curl_close($ch);
    
    // Handle errors
    if ($error) {
        return [
            'success' => false,
            'error' => EKLAIM_ERROR_CONNECTION . ': ' . $error,
            'http_code' => $httpCode
        ];
    }
    
    if ($httpCode !== 200) {
        return [
            'success' => false,
            'error' => 'HTTP Error: ' . $httpCode,
            'http_code' => $httpCode,
            'response' => $response
        ];
    }
    
    // Parse JSON response
    $responseData = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return [
            'success' => false,
            'error' => EKLAIM_ERROR_INVALID_RESPONSE . ': ' . json_last_error_msg(),
            'http_code' => $httpCode,
            'raw_response' => $response
        ];
    }
    
    return [
        'success' => true,
        'data' => $responseData,
        'http_code' => $httpCode
    ];
}

/**
 * Fungsi untuk log request/response E-Klaim
 * @param string $method Method yang dipanggil
 * @param array $requestData Data request
 * @param array $responseData Data response
 */
function logEklaimRequest($method, $requestData, $responseData) {
    $logData = [
        'timestamp' => date('Y-m-d H:i:s'),
        'method' => $method,
        'request' => $requestData,
        'response' => $responseData
    ];
    
    $logFile = __DIR__ . '/../logs/eklaim_' . date('Y-m-d') . '.log';
    $logDir = dirname($logFile);
    
    // Create log directory if not exists
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    file_put_contents($logFile, json_encode($logData, JSON_PRETTY_PRINT) . "\n", FILE_APPEND | LOCK_EX);
}

// ============================================================================
// FUNGSI-FUNGSI E-KLAIM BERDASARKAN KOLEKSI POSTMAN
// ============================================================================

/**
 * #00 NEW CLAIM - Membuat klaim baru
 * @param array $patientData Data pasien (nomor_kartu, nomor_sep, nomor_rm, nama_pasien, tgl_lahir, gender)
 * @return array Response dari server E-Klaim
 */
function createNewClaim($patientData) {
    $requestData = [
        'metadata' => [
            'method' => 'new_claim'
        ],
        'data' => [
            'nomor_kartu' => $patientData['nomor_kartu'] ?? '',
            'nomor_sep' => $patientData['nomor_sep'] ?? '',
            'nomor_rm' => $patientData['nomor_rm'] ?? '',
            'nama_pasien' => $patientData['nama_pasien'] ?? '',
            'tgl_lahir' => $patientData['tgl_lahir'] ?? '',
            'gender' => $patientData['gender'] ?? ''
        ]
    ];
    
    $response = sendEklaimRequest($requestData);
    logEklaimRequest('new_claim', $requestData, $response);
    
    return $response;
}

/**
 * #01 SET CLAIM DATA - Mengatur data klaim
 * @param string $nomorSep Nomor SEP
 * @param array $claimData Data klaim lengkap
 * @return array Response dari server E-Klaim
 */
function setClaimData($nomorSep, $claimData) {
    $requestData = [
        'metadata' => [
            'method' => 'set_claim_data',
            'nomor_sep' => $nomorSep
        ],
        'data' => array_merge(['nomor_sep' => $nomorSep], $claimData)
    ];
    
    $response = sendEklaimRequest($requestData);
    logEklaimRequest('set_claim_data', $requestData, $response);
    
    return $response;
}

/**
 * #02 IDRG DIAGNOSA SET - Mengatur diagnosa IDRG
 * @param string $nomorSep Nomor SEP
 * @param string $diagnosa String diagnosa (format: "ICD10#ICD10")
 * @return array Response dari server E-Klaim
 */
function setIdrgDiagnosa($nomorSep, $diagnosa) {
    $requestData = [
        'metadata' => [
            'method' => 'idrg_diagnosa_set',
            'nomor_sep' => $nomorSep
        ],
        'data' => [
            'diagnosa' => $diagnosa
        ]
    ];
    
    $response = sendEklaimRequest($requestData);
    logEklaimRequest('idrg_diagnosa_set', $requestData, $response);
    
    return $response;
}

/**
 * #03 IDRG DIAGNOSA GET - Mengambil diagnosa IDRG
 * @param string $nomorSep Nomor SEP
 * @return array Response dari server E-Klaim
 */
function getIdrgDiagnosa($nomorSep) {
    $requestData = [
        'metadata' => [
            'method' => 'idrg_diagnosa_get'
        ],
        'data' => [
            'nomor_sep' => $nomorSep
        ]
    ];
    
    $response = sendEklaimRequest($requestData);
    logEklaimRequest('idrg_diagnosa_get', $requestData, $response);
    
    return $response;
}

/**
 * #04 IDRG PROCEDURE SET - Mengatur prosedur IDRG
 * @param string $nomorSep Nomor SEP
 * @param string $procedure String prosedur (format: "ICD9#ICD9+multiplier#ICD9")
 * @return array Response dari server E-Klaim
 */
function setIdrgProcedure($nomorSep, $procedure) {
    $requestData = [
        'metadata' => [
            'method' => 'idrg_procedure_set',
            'nomor_sep' => $nomorSep
        ],
        'data' => [
            'procedure' => $procedure
        ]
    ];
    
    $response = sendEklaimRequest($requestData);
    logEklaimRequest('idrg_procedure_set', $requestData, $response);
    
    return $response;
}

/**
 * #05 IDRG PROCEDURE GET - Mengambil prosedur IDRG
 * @param string $nomorSep Nomor SEP
 * @return array Response dari server E-Klaim
 */
function getIdrgProcedure($nomorSep) {
    $requestData = [
        'metadata' => [
            'method' => 'idrg_procedure_get'
        ],
        'data' => [
            'nomor_sep' => $nomorSep
        ]
    ];
    
    $response = sendEklaimRequest($requestData);
    logEklaimRequest('idrg_procedure_get', $requestData, $response);
    
    return $response;
}

/**
 * #06 GROUPING IDRG - Melakukan grouping IDRG
 * @param string $nomorSep Nomor SEP
 * @return array Response dari server E-Klaim
 */
function groupIdrg($nomorSep) {
    $requestData = [
        'metadata' => [
            'method' => 'grouper',
            'stage' => '1',
            'grouper' => 'idrg'
        ],
        'data' => [
            'nomor_sep' => $nomorSep
        ]
    ];
    
    $response = sendEklaimRequest($requestData);
    logEklaimRequest('grouper_idrg', $requestData, $response);
    
    return $response;
}

/**
 * #07 FINAL IDRG - Finalisasi grouping IDRG
 * @param string $nomorSep Nomor SEP
 * @return array Response dari server E-Klaim
 */
function finalizeIdrg($nomorSep) {
    $requestData = [
        'metadata' => [
            'method' => 'idrg_grouper_final'
        ],
        'data' => [
            'nomor_sep' => $nomorSep
        ]
    ];
    
    $response = sendEklaimRequest($requestData);
    logEklaimRequest('idrg_grouper_final', $requestData, $response);
    
    return $response;
}

/**
 * #08 RE-EDIT IDRG - Mengizinkan re-edit IDRG
 * @param string $nomorSep Nomor SEP
 * @return array Response dari server E-Klaim
 */
function reeditIdrg($nomorSep) {
    $requestData = [
        'metadata' => [
            'method' => 'idrg_grouper_reedit'
        ],
        'data' => [
            'nomor_sep' => $nomorSep
        ]
    ];
    
    $response = sendEklaimRequest($requestData);
    logEklaimRequest('idrg_grouper_reedit', $requestData, $response);
    
    return $response;
}

/**
 * #09 IDRG TO INACBG IMPORT - Import data IDRG ke INACBG
 * @param string $nomorSep Nomor SEP
 * @return array Response dari server E-Klaim
 */
function importIdrgToInacbg($nomorSep) {
    $requestData = [
        'metadata' => [
            'method' => 'idrg_to_inacbg_import'
        ],
        'data' => [
            'nomor_sep' => $nomorSep
        ]
    ];
    
    $response = sendEklaimRequest($requestData);
    logEklaimRequest('idrg_to_inacbg_import', $requestData, $response);
    
    return $response;
}

/**
 * #10 INACBG DIAGNOSA GET - Mengambil diagnosa INACBG
 * @param string $nomorSep Nomor SEP
 * @return array Response dari server E-Klaim
 */
function getInacbgDiagnosa($nomorSep) {
    $requestData = [
        'metadata' => [
            'method' => 'inacbg_diagnosa_get'
        ],
        'data' => [
            'nomor_sep' => $nomorSep
        ]
    ];
    
    $response = sendEklaimRequest($requestData);
    logEklaimRequest('inacbg_diagnosa_get', $requestData, $response);
    
    return $response;
}

/**
 * #11 INACBG DIAGNOSA SET - Mengatur diagnosa INACBG
 * @param string $nomorSep Nomor SEP
 * @param string $diagnosa String diagnosa (format: "ICD10#ICD10")
 * @return array Response dari server E-Klaim
 */
function setInacbgDiagnosa($nomorSep, $diagnosa) {
    $requestData = [
        'metadata' => [
            'method' => 'inacbg_diagnosa_set',
            'nomor_sep' => $nomorSep
        ],
        'data' => [
            'diagnosa' => $diagnosa
        ]
    ];
    
    $response = sendEklaimRequest($requestData);
    logEklaimRequest('inacbg_diagnosa_set', $requestData, $response);
    
    return $response;
}

/**
 * #12 INACBG PROCEDURE SET - Mengatur prosedur INACBG
 * @param string $nomorSep Nomor SEP
 * @param string $procedure String prosedur (format: "ICD9#ICD9#ICD9")
 * @return array Response dari server E-Klaim
 */
function setInacbgProcedure($nomorSep, $procedure) {
    $requestData = [
        'metadata' => [
            'method' => 'inacbg_procedure_set',
            'nomor_sep' => $nomorSep
        ],
        'data' => [
            'procedure' => $procedure
        ]
    ];
    
    $response = sendEklaimRequest($requestData);
    logEklaimRequest('inacbg_procedure_set', $requestData, $response);
    
    return $response;
}

/**
 * #13 INACBG PROCEDURE GET - Mengambil prosedur INACBG
 * @param string $nomorSep Nomor SEP
 * @return array Response dari server E-Klaim
 */
function getInacbgProcedure($nomorSep) {
    $requestData = [
        'metadata' => [
            'method' => 'inacbg_procedure_get'
        ],
        'data' => [
            'nomor_sep' => $nomorSep
        ]
    ];
    
    $response = sendEklaimRequest($requestData);
    logEklaimRequest('inacbg_procedure_get', $requestData, $response);
    
    return $response;
}

/**
 * #14 GROUPING INACBG STAGE 1 - Melakukan grouping INACBG tahap 1
 * @param string $nomorSep Nomor SEP
 * @return array Response dari server E-Klaim
 */
function groupInacbgStage1($nomorSep) {
    $requestData = [
        'metadata' => [
            'method' => 'grouper',
            'stage' => '1',
            'grouper' => 'inacbg'
        ],
        'data' => [
            'nomor_sep' => $nomorSep
        ]
    ];
    
    $response = sendEklaimRequest($requestData);
    logEklaimRequest('grouper_inacbg_stage1', $requestData, $response);
    
    return $response;
}

/**
 * #15 GROUPING INACBG STAGE 2 - Melakukan grouping INACBG tahap 2
 * @param string $nomorSep Nomor SEP
 * @param string $specialCmg Special CMG (format: "CMG1#CMG2")
 * @return array Response dari server E-Klaim
 */
function groupInacbgStage2($nomorSep, $specialCmg) {
    $requestData = [
        'metadata' => [
            'method' => 'grouper',
            'stage' => '2',
            'grouper' => 'inacbg'
        ],
        'data' => [
            'nomor_sep' => $nomorSep,
            'special_cmg' => $specialCmg
        ]
    ];
    
    $response = sendEklaimRequest($requestData);
    logEklaimRequest('grouper_inacbg_stage2', $requestData, $response);
    
    return $response;
}

/**
 * #16 FINAL INACBG - Finalisasi grouping INACBG
 * @param string $nomorSep Nomor SEP
 * @return array Response dari server E-Klaim
 */
function finalizeInacbg($nomorSep) {
    $requestData = [
        'metadata' => [
            'method' => 'inacbg_grouper_final'
        ],
        'data' => [
            'nomor_sep' => $nomorSep
        ]
    ];
    
    $response = sendEklaimRequest($requestData);
    logEklaimRequest('inacbg_grouper_final', $requestData, $response);
    
    return $response;
}

/**
 * #17 RE-EDIT INACBG - Mengizinkan re-edit INACBG
 * @param string $nomorSep Nomor SEP
 * @return array Response dari server E-Klaim
 */
function reeditInacbg($nomorSep) {
    $requestData = [
        'metadata' => [
            'method' => 'inacbg_grouper_reedit'
        ],
        'data' => [
            'nomor_sep' => $nomorSep
        ]
    ];
    
    $response = sendEklaimRequest($requestData);
    logEklaimRequest('inacbg_grouper_reedit', $requestData, $response);
    
    return $response;
}

/**
 * #18 CLAIM FINAL - Finalisasi klaim
 * @param string $nomorSep Nomor SEP
 * @param string $coderNik NIK coder
 * @return array Response dari server E-Klaim
 */
function finalizeClaim($nomorSep, $coderNik) {
    $requestData = [
        'metadata' => [
            'method' => 'claim_final'
        ],
        'data' => [
            'nomor_sep' => $nomorSep,
            'coder_nik' => $coderNik
        ]
    ];
    
    $response = sendEklaimRequest($requestData);
    logEklaimRequest('claim_final', $requestData, $response);
    
    return $response;
}

/**
 * #19 CLAIM RE-EDIT - Mengizinkan re-edit klaim
 * @param string $nomorSep Nomor SEP
 * @return array Response dari server E-Klaim
 */
function reeditClaim($nomorSep) {
    $requestData = [
        'metadata' => [
            'method' => 'reedit_claim'
        ],
        'data' => [
            'nomor_sep' => $nomorSep
        ]
    ];
    
    $response = sendEklaimRequest($requestData);
    logEklaimRequest('reedit_claim', $requestData, $response);
    
    return $response;
}

/**
 * #20 CLAIM SEND - Mengirim klaim ke BPJS
 * @param string $nomorSep Nomor SEP
 * @return array Response dari server E-Klaim
 */
function sendClaim($nomorSep) {
    $requestData = [
        'metadata' => [
            'method' => 'send_claim_individual'
        ],
        'data' => [
            'nomor_sep' => $nomorSep
        ]
    ];
    
    $response = sendEklaimRequest($requestData);
    logEklaimRequest('send_claim_individual', $requestData, $response);
    
    return $response;
}

/**
 * #21 GET CLAIM DATA - Mengambil data klaim lengkap
 * @param string $nomorSep Nomor SEP
 * @return array Response dari server E-Klaim
 */
function getClaimData($nomorSep) {
    $requestData = [
        'metadata' => [
            'method' => 'get_claim_data'
        ],
        'data' => [
            'nomor_sep' => $nomorSep
        ]
    ];
    
    $response = sendEklaimRequest($requestData);
    logEklaimRequest('get_claim_data', $requestData, $response);
    
    return $response;
}

// ============================================================================
// FUNGSI HELPER UNTUK VALIDASI DAN UTILITAS
// ============================================================================

/**
 * Validasi format nomor SEP
 * @param string $nomorSep Nomor SEP
 * @return bool True jika valid
 */
function validateNomorSep($nomorSep) {
    return !empty($nomorSep) && strlen($nomorSep) >= 5;
}

/**
 * Validasi format diagnosa ICD-10
 * @param string $diagnosa String diagnosa
 * @return bool True jika valid
 */
function validateDiagnosa($diagnosa) {
    return !empty($diagnosa) && preg_match('/^[A-Z]\d{2}\.\d+(\#[A-Z]\d{2}\.\d+)*$/', $diagnosa);
}

/**
 * Validasi format prosedur ICD-9
 * @param string $procedure String prosedur
 * @return bool True jika valid
 */
function validateProcedure($procedure) {
    return !empty($procedure) && preg_match('/^\d{2}\.\d+(\#\d{2}\.\d+)*(\+\d+\#\d{2}\.\d+)*$/', $procedure);
}

/**
 * Format tanggal untuk E-Klaim (YYYY-MM-DD HH:mm:ss)
 * @param string $date Tanggal dalam format apapun
 * @return string Tanggal dalam format E-Klaim
 */
function formatEklaimDate($date) {
    if (empty($date)) return '';
    
    $timestamp = strtotime($date);
    if ($timestamp === false) return '';
    
    return date('Y-m-d H:i:s', $timestamp);
}

/**
 * Mendapatkan status klaim dari response
 * @param array $response Response dari E-Klaim
 * @return string Status klaim
 */
function getClaimStatus($response) {
    if (!$response['success']) return 'error';
    
    $data = $response['data'] ?? [];
    $metadata = $data['metadata'] ?? [];
    
    return $metadata['code'] == 200 ? 'success' : 'failed';
}

/**
 * Mendapatkan pesan error dari response
 * @param array $response Response dari E-Klaim
 * @return string Pesan error
 */
function getErrorMessage($response) {
    if ($response['success']) return '';
    
    $data = $response['data'] ?? [];
    $metadata = $data['metadata'] ?? [];
    
    return $metadata['message'] ?? $response['error'] ?? 'Unknown error';
}

/**
 * Mendapatkan kode error dari response
 * @param array $response Response dari E-Klaim
 * @return string Kode error
 */
function getErrorCode($response) {
    if ($response['success']) return '';
    
    $data = $response['data'] ?? [];
    $metadata = $data['metadata'] ?? [];
    
    return $metadata['error_no'] ?? '';
}
