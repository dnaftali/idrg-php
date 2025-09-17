<?php
/**
 * Konfigurasi Server E-Klaim
 * File konfigurasi untuk koneksi ke server E-Klaim
 * Berdasarkan koleksi Postman E-KLAIM IDRG
 */

// Include function tracking method
require_once __DIR__ . '/../functions/eklaim_method_tracking.php';

// Konfigurasi Server E-Klaim
//define('EKLAIM_BASE_URL', 'http://10.10.1.63');
define('EKLAIM_BASE_URL', 'http://192.168.100.27');
define('EKLAIM_ENDPOINT', '/E-Klaim/ws.php');
define('EKLAIM_DEBUG_MODE', true); // Set false untuk production

// URL lengkap endpoint E-Klaim
define('EKLAIM_FULL_URL', EKLAIM_BASE_URL . EKLAIM_ENDPOINT . (EKLAIM_DEBUG_MODE ? '?mode=debug' : ''));

// Timeout untuk request (dalam detik)
define('EKLAIM_TIMEOUT', 30);

// Konfigurasi header untuk request
define('EKLAIM_CONTENT_TYPE', 'application/json');
define('EKLAIM_ACCEPT', 'application/json');

// Konfigurasi Coder NIK
define('EKLAIM_CODER_NIK', '123123123123');

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
    
    // Log request details
    error_log("sendEklaimRequest called with URL: " . $url);
    error_log("sendEklaimRequest data: " . json_encode($data, JSON_UNESCAPED_SLASHES));
    
    // Prepare request data
    $requestData = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    error_log("sendEklaimRequest JSON data: " . $requestData);
    
    // Validate JSON before sending
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("sendEklaimRequest JSON encoding error: " . json_last_error_msg());
        return [
            'success' => false,
            'error' => 'JSON encoding error: ' . json_last_error_msg(),
            'http_code' => 0
        ];
    }
    
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
    
    // Log response details
    error_log("sendEklaimRequest HTTP Code: " . $httpCode);
    error_log("sendEklaimRequest Response: " . $response);
    error_log("sendEklaimRequest cURL Error: " . ($error ?: 'None'));
    
    curl_close($ch);
    
    // Handle errors
    if ($error) {
        error_log("sendEklaimRequest cURL Error occurred: " . $error);
        return [
            'success' => false,
            'error' => EKLAIM_ERROR_CONNECTION . ': ' . $error,
            'http_code' => $httpCode
        ];
    }
    
    if ($httpCode !== 200) {
        error_log("sendEklaimRequest HTTP Error: " . $httpCode);
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
        error_log("sendEklaimRequest JSON Parse Error: " . json_last_error_msg());
        return [
            'success' => false,
            'error' => EKLAIM_ERROR_INVALID_RESPONSE . ': ' . json_last_error_msg(),
            'http_code' => $httpCode,
            'raw_response' => $response
        ];
    }
    
    error_log("sendEklaimRequest Parsed Response: " . json_encode($responseData, JSON_UNESCAPED_SLASHES));
    
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
    // Log input data
    error_log("createNewClaim called with patient data: " . json_encode($patientData, JSON_UNESCAPED_SLASHES));
    
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
    
    // Log request data yang akan dikirim
    error_log("createNewClaim request data: " . json_encode($requestData, JSON_UNESCAPED_SLASHES));
    error_log("createNewClaim sending to URL: " . EKLAIM_FULL_URL);
    
    $response = sendEklaimRequest($requestData);
    
    // Log response
    error_log("createNewClaim response: " . json_encode($response, JSON_UNESCAPED_SLASHES));
    
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
    $startTime = microtime(true);
    $methodCode = '02'; // Method 02: set_claim_data
    
    try {
        // Simpan tracking sebagai pending
        saveEklaimMethodTracking($nomorSep, $methodCode, 'pending', $claimData);
        
        // Log input data
        error_log("setClaimData called with nomor_sep: " . $nomorSep);
        error_log("setClaimData claim_data: " . json_encode($claimData, JSON_UNESCAPED_SLASHES));
        
        $requestData = [
            'metadata' => [
                'method' => 'set_claim_data',
                'nomor_sep' => $nomorSep
            ],
            'data' => array_merge(['nomor_sep' => $nomorSep], $claimData)
        ];
        
        // Log request data yang akan dikirim
        error_log("setClaimData request data: " . json_encode($requestData, JSON_UNESCAPED_SLASHES));
        error_log("setClaimData sending to URL: " . EKLAIM_FULL_URL);
        
        $response = sendEklaimRequest($requestData);
        
        // Hitung execution time
        $executionTime = round((microtime(true) - $startTime) * 1000);
        
        // Log response
        error_log("setClaimData response: " . json_encode($response, JSON_UNESCAPED_SLASHES));
        
        logEklaimRequest('set_claim_data', $requestData, $response);
        
        // Update tracking berdasarkan response
        if (isset($response['metadata']['code']) && $response['metadata']['code'] == 200) {
            // Success
            saveEklaimMethodTracking(
                $nomorSep, 
                $methodCode, 
                'success', 
                $requestData, 
                $response, 
                null, 
                null, 
                $executionTime
            );
        } else {
            // Error
            $errorCode = $response['metadata']['error_no'] ?? 'UNKNOWN';
            $errorMessage = $response['metadata']['message'] ?? 'Unknown error';
            
            saveEklaimMethodTracking(
                $nomorSep, 
                $methodCode, 
                'error', 
                $requestData, 
                $response, 
                $errorCode, 
                $errorMessage, 
                $executionTime
            );
        }
        
        return $response;
        
    } catch (Exception $e) {
        // Hitung execution time
        $executionTime = round((microtime(true) - $startTime) * 1000);
        
        // Update tracking dengan error
        saveEklaimMethodTracking(
            $nomorSep, 
            $methodCode, 
            'error', 
            $requestData ?? null, 
            null, 
            'EXCEPTION', 
            $e->getMessage(), 
            $executionTime
        );
        
        error_log("setClaimData exception: " . $e->getMessage());
        throw $e;
    }
}

/**
 * #02 IDRG DIAGNOSA SET - Mengatur diagnosa IDRG
 * @param string $nomorSep Nomor SEP
 * @param string $diagnosa String diagnosa (format: "ICD10#ICD10")
 * @return array Response dari server E-Klaim
 */
function setIdrgDiagnosa($nomorSep, $diagnosa) {
    $startTime = microtime(true);
    $methodCode = '03'; // Method 03: idrg_diagnosa_set
    
    try {
        // Simpan tracking sebagai pending
        saveEklaimMethodTracking($nomorSep, $methodCode, 'pending', ['diagnosa' => $diagnosa]);
        
        // Log input data
        error_log("setIdrgDiagnosa called with nomor_sep: " . $nomorSep);
        error_log("setIdrgDiagnosa diagnosa: " . $diagnosa);
        
        $requestData = [
            'metadata' => [
                'method' => 'idrg_diagnosa_set',
                'nomor_sep' => $nomorSep
            ],
            'data' => [
                'diagnosa' => $diagnosa
            ]
        ];
        
        // Log request data yang akan dikirim
        error_log("setIdrgDiagnosa request data: " . json_encode($requestData, JSON_UNESCAPED_SLASHES));
        error_log("setIdrgDiagnosa sending to URL: " . EKLAIM_FULL_URL);
        
        $response = sendEklaimRequest($requestData);
        
        // Hitung execution time
        $executionTime = round((microtime(true) - $startTime) * 1000);
        
        // Log response
        error_log("setIdrgDiagnosa response: " . json_encode($response, JSON_UNESCAPED_SLASHES));
        
        logEklaimRequest('idrg_diagnosa_set', $requestData, $response);
        
        // Update tracking berdasarkan response
        if (isset($response['metadata']['code']) && $response['metadata']['code'] == 200) {
            // Success
            saveEklaimMethodTracking(
                $nomorSep, 
                $methodCode, 
                'success', 
                $requestData, 
                $response, 
                null, 
                null, 
                $executionTime
            );
        } else {
            // Error
            $errorCode = $response['metadata']['error_no'] ?? 'UNKNOWN';
            $errorMessage = $response['metadata']['message'] ?? 'Unknown error';
            
            saveEklaimMethodTracking(
                $nomorSep, 
                $methodCode, 
                'error', 
                $requestData, 
                $response, 
                $errorCode, 
                $errorMessage, 
                $executionTime
            );
        }
        
        return $response;
        
    } catch (Exception $e) {
        // Hitung execution time
        $executionTime = round((microtime(true) - $startTime) * 1000);
        
        // Update tracking dengan error
        saveEklaimMethodTracking(
            $nomorSep, 
            $methodCode, 
            'error', 
            $requestData ?? null, 
            null, 
            'EXCEPTION', 
            $e->getMessage(), 
            $executionTime
        );
        
        error_log("setIdrgDiagnosa exception: " . $e->getMessage());
        throw $e;
    }
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
    $startTime = microtime(true);
    $methodCode = '05'; // Method 05: idrg_procedure_set
    
    try {
        // Simpan tracking sebagai pending
        saveEklaimMethodTracking($nomorSep, $methodCode, 'pending', ['procedure' => $procedure]);
        
        // Log input data
        error_log("setIdrgProcedure called with nomor_sep: " . $nomorSep);
        error_log("setIdrgProcedure procedure: " . $procedure);
        
        $requestData = [
            'metadata' => [
                'method' => 'idrg_procedure_set',
                'nomor_sep' => $nomorSep
            ],
            'data' => [
                'procedure' => $procedure
            ]
        ];
        
        // Log request data yang akan dikirim
        error_log("setIdrgProcedure request data: " . json_encode($requestData, JSON_UNESCAPED_SLASHES));
        error_log("setIdrgProcedure sending to URL: " . EKLAIM_FULL_URL);
        
        $response = sendEklaimRequest($requestData);
        
        // Hitung execution time
        $executionTime = round((microtime(true) - $startTime) * 1000);
        
        // Log response
        error_log("setIdrgProcedure response: " . json_encode($response, JSON_UNESCAPED_SLASHES));
        
        logEklaimRequest('idrg_procedure_set', $requestData, $response);
        
        // Update tracking berdasarkan response
        if (isset($response['metadata']['code']) && $response['metadata']['code'] == 200) {
            // Success
            saveEklaimMethodTracking(
                $nomorSep, 
                $methodCode, 
                'success', 
                $requestData, 
                $response, 
                null, 
                null, 
                $executionTime
            );
        } else {
            // Error
            $errorCode = $response['metadata']['error_no'] ?? 'UNKNOWN';
            $errorMessage = $response['metadata']['message'] ?? 'Unknown error';
            
            saveEklaimMethodTracking(
                $nomorSep, 
                $methodCode, 
                'error', 
                $requestData, 
                $response, 
                $errorCode, 
                $errorMessage, 
                $executionTime
            );
        }
        
        return $response;
        
    } catch (Exception $e) {
        // Hitung execution time
        $executionTime = round((microtime(true) - $startTime) * 1000);
        
        // Update tracking dengan error
        saveEklaimMethodTracking(
            $nomorSep, 
            $methodCode, 
            'error', 
            $requestData ?? null, 
            null, 
            'EXCEPTION', 
            $e->getMessage(), 
            $executionTime
        );
        
        error_log("setIdrgProcedure exception: " . $e->getMessage());
        throw $e;
    }
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
function groupIdrg($nomorSep, $forceApiCall = false) {
    $startTime = microtime(true);
    $methodCode = '07'; // Method 07: grouping_idrg
    
    try {
        // Jika forceApiCall = true, skip cache check dan langsung ke API
        if (!$forceApiCall) {
            // Cek apakah data grouping sudah ada di database
            $existingData = getEklaimMethodStatus($nomorSep);
        
        if ($existingData['success']) {
            // Cari method 07 (grouping_idrg) yang sudah berhasil
            foreach ($existingData['methods'] as $method) {
                if ($method['method_code'] === '07') {
                    // Periksa apakah response_data menunjukkan success
                    $isSuccessful = false;
                    
                    if (!empty($method['response_data'])) {
                        $responseData = json_decode($method['response_data'], true);
                        // Periksa apakah response benar-benar berhasil (bukan hanya HTTP success)
                        if (isset($responseData['success']) && $responseData['success'] === true) {
                            // Periksa apakah ada error dalam metadata
                            if (isset($responseData['data']['metadata']['code']) && $responseData['data']['metadata']['code'] == 200) {
                                $isSuccessful = true;
                            } else {
                                // Ada error dalam response, tidak dianggap sukses
                                $isSuccessful = false;
                            }
                        }
                    } else {
                        // Fallback ke status field
                        $isSuccessful = ($method['status'] === 'success');
                    }
                    
                    if ($isSuccessful) {
                        // Data sudah ada dan berhasil, kembalikan data yang sudah ada
                        error_log("Grouping data already exists for nomor_sep: " . $nomorSep . ", returning cached data");
                        
                        $responseData = json_decode($method['response_data'], true);
                        return [
                            'success' => true,
                            'data' => $responseData,
                            'message' => 'Grouping data retrieved from cache',
                            'cached' => true
                        ];
                    }
                }
            }
        }
        } else {
            error_log("Force API call requested for nomor_sep: " . $nomorSep . ", skipping cache check");
        }
        
        // Jika tidak ada data yang berhasil atau forceApiCall = true, lanjutkan dengan API call
        error_log("Calling external API for nomor_sep: " . $nomorSep . ($forceApiCall ? " (forced)" : " (no cache found)"));
        
        // Simpan tracking sebagai pending
        saveEklaimMethodTracking($nomorSep, $methodCode, 'pending');
        
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
        
        // Hitung execution time
        $executionTime = round((microtime(true) - $startTime) * 1000);
        
        logEklaimRequest('grouper_idrg', $requestData, $response);
        
        // Update tracking berdasarkan response
        if (isset($response['metadata']['code']) && $response['metadata']['code'] == 200) {
            // Success
            saveEklaimMethodTracking(
                $nomorSep, 
                $methodCode, 
                'success', 
                $requestData, 
                $response, 
                null, 
                null, 
                $executionTime
            );
        } else {
            // Error
            $errorCode = $response['metadata']['error_no'] ?? 'UNKNOWN';
            $errorMessage = $response['metadata']['message'] ?? 'Unknown error';
            
            saveEklaimMethodTracking(
                $nomorSep, 
                $methodCode, 
                'error', 
                $requestData, 
                $response, 
                $errorCode, 
                $errorMessage, 
                $executionTime
            );
        }
        
        return $response;
        
    } catch (Exception $e) {
        // Hitung execution time
        $executionTime = round((microtime(true) - $startTime) * 1000);
        
        // Update tracking dengan error
        saveEklaimMethodTracking(
            $nomorSep, 
            $methodCode, 
            'error', 
            $requestData ?? null, 
            null, 
            'EXCEPTION', 
            $e->getMessage(), 
            $executionTime
        );
        
        error_log("groupIdrg exception: " . $e->getMessage());
        throw $e;
    }
}

/**
 * #07 FINAL IDRG - Finalisasi grouping IDRG
 * @param string $nomorSep Nomor SEP
 * @return array Response dari server E-Klaim
 */
function finalizeIdrg($nomorSep) {
    $startTime = microtime(true);
    $methodCode = '08'; // Method 08: final_idrg
    
    try {
        // Cek apakah data final_idrg sudah ada di database
        $existingData = getEklaimMethodStatus($nomorSep);
        
        if ($existingData['success']) {
            // Cari method 08 (final_idrg) yang sudah berhasil
            foreach ($existingData['methods'] as $method) {
                if ($method['method_code'] === '08') {
                    // Periksa apakah response_data menunjukkan success
                    $isSuccessful = false;
                    
                    if (!empty($method['response_data'])) {
                        $responseData = json_decode($method['response_data'], true);
                        // Periksa apakah response benar-benar berhasil (bukan hanya HTTP success)
                        if (isset($responseData['success']) && $responseData['success'] === true) {
                            // Periksa apakah ada error dalam metadata
                            if (isset($responseData['data']['metadata']['code']) && $responseData['data']['metadata']['code'] == 200) {
                                $isSuccessful = true;
                            } else {
                                // Ada error dalam response, tidak dianggap sukses
                                $isSuccessful = false;
                            }
                        }
                    } else {
                        // Fallback ke status field
                        $isSuccessful = ($method['status'] === 'success');
                    }
                    
                    if ($isSuccessful) {
                        // Data sudah ada dan berhasil, kembalikan data yang sudah ada
                        error_log("Final iDRG data already exists for nomor_sep: " . $nomorSep . ", returning cached data");
                        
                        $responseData = json_decode($method['response_data'], true);
                        return [
                            'success' => true,
                            'data' => $responseData,
                            'message' => 'Final iDRG data retrieved from cache',
                            'cached' => true
                        ];
                    }
                }
            }
        }
        
        // Jika tidak ada data yang berhasil, lanjutkan dengan API call
        error_log("No successful final_idrg data found for nomor_sep: " . $nomorSep . ", calling external API");
        
        // Simpan tracking sebagai pending
        saveEklaimMethodTracking($nomorSep, $methodCode, 'pending');
        
        $requestData = [
            'metadata' => [
                'method' => 'idrg_grouper_final'
            ],
            'data' => [
                'nomor_sep' => $nomorSep
            ]
        ];
        
        $response = sendEklaimRequest($requestData);
        
        // Hitung execution time
        $executionTime = round((microtime(true) - $startTime) * 1000);
        
        logEklaimRequest('idrg_grouper_final', $requestData, $response);
        
        // Update tracking berdasarkan response
        if (isset($response['metadata']['code']) && $response['metadata']['code'] == 200) {
            // Success
            saveEklaimMethodTracking(
                $nomorSep, 
                $methodCode, 
                'success', 
                $requestData, 
                $response, 
                null, 
                null, 
                $executionTime
            );
        } else {
            // Error
            $errorCode = $response['metadata']['error_no'] ?? 'UNKNOWN';
            $errorMessage = $response['metadata']['message'] ?? 'Unknown error';
            
            saveEklaimMethodTracking(
                $nomorSep, 
                $methodCode, 
                'error', 
                $requestData, 
                $response, 
                $errorCode, 
                $errorMessage, 
                $executionTime
            );
        }
        
        return $response;
        
    } catch (Exception $e) {
        // Hitung execution time
        $executionTime = round((microtime(true) - $startTime) * 1000);
        
        // Update tracking dengan error
        saveEklaimMethodTracking(
            $nomorSep, 
            $methodCode, 
            'error', 
            $requestData ?? null, 
            null, 
            'EXCEPTION', 
            $e->getMessage(), 
            $executionTime
        );
        
        error_log("finalizeIdrg exception: " . $e->getMessage());
        throw $e;
    }
}

/**
 * #08 RE-EDIT IDRG - Mengizinkan re-edit IDRG
 * @param string $nomorSep Nomor SEP
 * @return array Response dari server E-Klaim
 */
function reeditIdrg($nomorSep) {
    $startTime = microtime(true);
    $methodCode = '11'; // Method 11: idrg_grouper_reedit
    
    try {
        // Simpan tracking sebagai pending
        saveEklaimMethodTracking($nomorSep, $methodCode, 'pending');
        
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
        
        // Hitung execution time
        $executionTime = round((microtime(true) - $startTime) * 1000);
        
        // Update tracking berdasarkan response
        if (isset($response['metadata']['code']) && $response['metadata']['code'] == 200) {
            // Success
            saveEklaimMethodTracking(
                $nomorSep, 
                $methodCode, 
                'success', 
                $requestData, 
                $response, 
                null, 
                null, 
                $executionTime
            );
        } else {
            // Error
            $errorCode = $response['metadata']['error_no'] ?? 'UNKNOWN';
            $errorMessage = $response['metadata']['message'] ?? 'Unknown error';
            
            saveEklaimMethodTracking(
                $nomorSep, 
                $methodCode, 
                'error', 
                $requestData, 
                $response, 
                $errorCode, 
                $errorMessage, 
                $executionTime
            );
        }
        
        return $response;
        
    } catch (Exception $e) {
        // Hitung execution time
        $executionTime = round((microtime(true) - $startTime) * 1000);
        
        // Update tracking dengan error
        saveEklaimMethodTracking(
            $nomorSep, 
            $methodCode, 
            'error', 
            $requestData ?? null, 
            null, 
            'EXCEPTION', 
            $e->getMessage(), 
            $executionTime
        );
        
        throw $e;
    }
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
    $startTime = microtime(true);
    $methodCode = '22'; // Method 22: grouper_inacbg_stage1
    
    try {
        // Simpan tracking sebagai pending
        saveEklaimMethodTracking($nomorSep, $methodCode, 'pending');
        
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
        
        // Hitung execution time
        $executionTime = round((microtime(true) - $startTime) * 1000);
        
        // Update tracking berdasarkan response
        if (isset($response['metadata']['code']) && $response['metadata']['code'] == 200) {
            // Success
            saveEklaimMethodTracking(
                $nomorSep, 
                $methodCode, 
                'success', 
                $requestData, 
                $response, 
                null, 
                null, 
                $executionTime
            );
        } else {
            // Error
            $errorCode = $response['metadata']['error_no'] ?? 'UNKNOWN';
            $errorMessage = $response['metadata']['message'] ?? 'Unknown error';
            
            saveEklaimMethodTracking(
                $nomorSep, 
                $methodCode, 
                'error', 
                $requestData, 
                $response, 
                $errorCode, 
                $errorMessage, 
                $executionTime
            );
        }
        
        return $response;
        
    } catch (Exception $e) {
        // Hitung execution time
        $executionTime = round((microtime(true) - $startTime) * 1000);
        
        // Update tracking dengan error
        saveEklaimMethodTracking(
            $nomorSep, 
            $methodCode, 
            'error', 
            $requestData ?? null, 
            null, 
            'EXCEPTION', 
            $e->getMessage(), 
            $executionTime
        );
        
        throw $e;
    }
}

/**
 * #15 GROUPING INACBG STAGE 2 - Melakukan grouping INACBG tahap 2
 * @param string $nomorSep Nomor SEP
 * @param string $specialCmg Special CMG (format: "CMG1#CMG2")
 * @return array Response dari server E-Klaim
 */
function groupInacbgStage2($nomorSep, $specialCmg) {
    $startTime = microtime(true);
    $methodCode = '23'; // Method 23: grouper_inacbg_stage2
    
    try {
        // Simpan tracking sebagai pending
        saveEklaimMethodTracking($nomorSep, $methodCode, 'pending', ['special_cmg' => $specialCmg]);
        
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
        
        // Hitung execution time
        $executionTime = round((microtime(true) - $startTime) * 1000);
        
        // Update tracking berdasarkan response
        if (isset($response['metadata']['code']) && $response['metadata']['code'] == 200) {
            // Success
            saveEklaimMethodTracking(
                $nomorSep, 
                $methodCode, 
                'success', 
                $requestData, 
                $response, 
                null, 
                null, 
                $executionTime
            );
        } else {
            // Error
            $errorCode = $response['metadata']['error_no'] ?? 'UNKNOWN';
            $errorMessage = $response['metadata']['message'] ?? 'Unknown error';
            
            saveEklaimMethodTracking(
                $nomorSep, 
                $methodCode, 
                'error', 
                $requestData, 
                $response, 
                $errorCode, 
                $errorMessage, 
                $executionTime
            );
        }
        
        return $response;
        
    } catch (Exception $e) {
        // Hitung execution time
        $executionTime = round((microtime(true) - $startTime) * 1000);
        
        // Update tracking dengan error
        saveEklaimMethodTracking(
            $nomorSep, 
            $methodCode, 
            'error', 
            $requestData ?? null, 
            null, 
            'EXCEPTION', 
            $e->getMessage(), 
            $executionTime
        );
        
        throw $e;
    }
}

/**
 * #15.1 GROUPING INACBG FINAL - Melakukan finalisasi grouping INACBG
 * @param string $nomorSep Nomor SEP
 * @return array Response dari server E-Klaim
 */
function groupInacbgFinal($nomorSep) {
    $requestData = [
        'metadata' => [
            'method' => 'inacbg_grouper_final'
        ],
        'data' => [
            'nomor_sep' => $nomorSep
        ]
    ];
    
    // Log request data untuk debugging
    error_log("groupInacbgFinal Request Data: " . json_encode($requestData, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    
    $response = sendEklaimRequest($requestData);
    
    // Log response untuk debugging
    error_log("groupInacbgFinal Response: " . json_encode($response, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    
    logEklaimRequest('inacbg_grouper_final', $requestData, $response);
    
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
 * #19.1 CLAIM FINAL - Finalisasi klaim
 * @param string $nomorSep Nomor SEP
 * @param string $coderNik NIK Coder
 * @return array Response dari server E-Klaim
 */
function claimFinal($nomorSep, $coderNik) {
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
    if (empty($diagnosa)) {
        return false;
    }
    
    // Split diagnosa dengan delimiter #
    $codes = explode('#', $diagnosa);
    
    foreach ($codes as $code) {
        $code = trim($code);
        
        // Validasi format ICD-10: huruf diikuti angka, bisa ada titik dan angka lagi
        // Contoh: A00.1, S71.0, D32.1, A34, A15.00
        if (!preg_match('/^[A-Z][0-9]{2}(\.[0-9]{1,2})?$/', $code)) {
            return false;
        }
    }
    
    return true;
}

/**
 * Validasi format prosedur ICD-9
 * @param string $procedure String prosedur
 * @return bool True jika valid
 */
function validateProcedure($procedure) {
    if (empty($procedure)) {
        return false;
    }
    
    // Split prosedur dengan delimiter #
    $codes = explode('#', $procedure);
    
    foreach ($codes as $code) {
        $code = trim($code);
        
        // Validasi format ICD-9: angka, titik, angka, bisa ada +multiplier
        // Contoh: 06.2, 88.01, 86.22, 90.090+2
        if (!preg_match('/^[0-9]{2}\.[0-9]{1,3}(\+[0-9]+)?$/', $code)) {
            return false;
        }
    }
    
    return true;
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

/**
 * Function untuk menjalankan semua method secara berurutan dengan tracking
 * @param string $nomorSep Nomor SEP
 * @param array $claimData Data klaim
 * @param string $diagnosa String diagnosa
 * @param string $procedure String prosedur (default: '#')
 * @return array Hasil semua method
 */
function runEklaimProcessWithTracking($nomorSep, $claimData, $diagnosa, $procedure = '#') {
    $results = [];
    
    try {
        // Method 02: Set Claim Data
        $results['set_claim_data'] = setClaimData($nomorSep, $claimData);
        
        // Method 03: Set IDRG Diagnosa
        $results['idrg_diagnosa_set'] = setIdrgDiagnosa($nomorSep, $diagnosa);
        
        // Method 05: Set IDRG Procedure
        $results['idrg_procedure_set'] = setIdrgProcedure($nomorSep, $procedure);
        
        // Method 07: Grouping IDRG
        $results['grouping_idrg'] = groupIdrg($nomorSep);
        
        // Method 08: Final IDRG
        $results['final_idrg'] = finalizeIdrg($nomorSep);
        
        return [
            'success' => true,
            'message' => 'Semua method berhasil dijalankan',
            'results' => $results
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage(),
            'results' => $results
        ];
    }
}

/**
 * Function untuk resume process dari method yang gagal
 * @param string $nomorSep Nomor SEP
 * @return array Hasil resume process
 */
function resumeEklaimProcess($nomorSep) {
    // Get status tracking
    $status = getEklaimMethodStatus($nomorSep);
    
    if (!$status['success']) {
        return [
            'success' => false,
            'error' => 'Gagal mendapatkan status tracking: ' . $status['error']
        ];
    }
    
    // Cari method yang gagal atau pending
    $pendingMethods = array_filter($status['methods'], function($method) {
        return in_array($method['status'], ['pending', 'error']);
    });
    
    if (empty($pendingMethods)) {
        return [
            'success' => true,
            'message' => 'Semua method sudah selesai',
            'status' => $status
        ];
    }
    
    // Jalankan method yang pending/error
    $results = [];
    foreach ($pendingMethods as $method) {
        try {
            switch ($method['method_code']) {
                case '02':
                    // Set Claim Data - perlu data dari database
                    $claimData = getClaimDataFromDatabase($nomorSep);
                    $results[$method['method_code']] = setClaimData($nomorSep, $claimData);
                    break;
                case '03':
                    // Set IDRG Diagnosa - perlu data dari database
                    $diagnosa = getDiagnosaFromDatabase($nomorSep);
                    $results[$method['method_code']] = setIdrgDiagnosa($nomorSep, $diagnosa);
                    break;
                case '05':
                    // Set IDRG Procedure - perlu data dari database
                    $procedure = getProcedureFromDatabase($nomorSep);
                    $results[$method['method_code']] = setIdrgProcedure($nomorSep, $procedure);
                    break;
                case '07':
                    // Grouping IDRG
                    $results[$method['method_code']] = groupIdrg($nomorSep);
                    break;
                case '08':
                    // Final IDRG
                    $results[$method['method_code']] = finalizeIdrg($nomorSep);
                    break;
                default:
                    // Skip method yang tidak diimplementasi
                    saveEklaimMethodTracking($nomorSep, $method['method_code'], 'skipped');
                    break;
            }
        } catch (Exception $e) {
            $results[$method['method_code']] = [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    return [
        'success' => true,
        'message' => 'Resume process selesai',
        'results' => $results
    ];
}

/**
 * Helper function untuk mendapatkan claim data dari database
 * @param string $nomorSep Nomor SEP
 * @return array Claim data
 */
function getClaimDataFromDatabase($nomorSep) {
    // Implementasi untuk mendapatkan claim data dari database
    // Return array dengan data yang diperlukan
    return [];
}

/**
 * Helper function untuk mendapatkan diagnosa dari database
 * @param string $nomorSep Nomor SEP
 * @return string Diagnosa string
 */
function getDiagnosaFromDatabase($nomorSep) {
    // Implementasi untuk mendapatkan diagnosa dari database
    // Return string diagnosa
    return '';
}

/**
 * Helper function untuk mendapatkan prosedur dari database
 * @param string $nomorSep Nomor SEP
 * @return string Prosedur string
 */
function getProcedureFromDatabase($nomorSep) {
    // Implementasi untuk mendapatkan prosedur dari database
    // Return string prosedur
    return '#';
}
