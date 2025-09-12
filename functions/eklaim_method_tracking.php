<?php
/**
 * Function untuk tracking method E-Klaim 01-21
 * File: functions/eklaim_method_tracking.php
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Simpan atau update tracking method E-Klaim
 * 
 * @param string $nomorSep Nomor SEP
 * @param string $methodCode Kode method (01-21)
 * @param string $status Status method (pending, success, error, skipped)
 * @param array $requestData Data request (optional)
 * @param array $responseData Data response (optional)
 * @param string $errorCode Kode error (optional)
 * @param string $errorMessage Pesan error (optional)
 * @param int $executionTime Waktu eksekusi dalam ms (optional)
 * @return array Hasil operasi
 */
function saveEklaimMethodTracking($nomorSep, $methodCode, $status, $requestData = null, $responseData = null, $errorCode = null, $errorMessage = null, $executionTime = null) {
    $pdo = getConnection();
    
    try {
        // Validasi input
        if (empty($nomorSep) || empty($methodCode) || empty($status)) {
            return [
                'success' => false,
                'error' => 'Nomor SEP, method code, dan status harus diisi'
            ];
        }
        
        // Validasi method code
        if (!preg_match('/^(0[1-9]|1[0-9]|2[01])$/', $methodCode)) {
            return [
                'success' => false,
                'error' => 'Method code harus antara 01-21'
            ];
        }
        
        // Validasi status
        if (!in_array($status, ['pending', 'success', 'error', 'skipped'])) {
            return [
                'success' => false,
                'error' => 'Status harus pending, success, error, atau skipped'
            ];
        }
        
        // Cek apakah method sudah ada
        $checkSql = "SELECT id, retry_count, status FROM eklaim_method_tracking 
                     WHERE nomor_sep = ? AND method_code = ?";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([$nomorSep, $methodCode]);
        $existingRecord = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        // Get method name dari mapping
        $methodSql = "SELECT method_name FROM eklaim_method_mapping WHERE method_code = ?";
        $methodStmt = $pdo->prepare($methodSql);
        $methodStmt->execute([$methodCode]);
        $methodInfo = $methodStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$methodInfo) {
            return [
                'success' => false,
                'error' => 'Method code tidak ditemukan di mapping'
            ];
        }
        
        $methodName = $methodInfo['method_name'];
        
        if ($existingRecord) {
            // Update existing record
            $retryCount = $existingRecord['retry_count'];
            if ($status === 'error' && $existingRecord['status'] !== 'error') {
                $retryCount++;
            }
            
            $updateSql = "UPDATE eklaim_method_tracking SET 
                         method_name = ?,
                         request_data = ?,
                         response_data = ?,
                         status = ?,
                         error_code = ?,
                         error_message = ?,
                         execution_time_ms = ?,
                         retry_count = ?,
                         last_attempt_at = CURRENT_TIMESTAMP,
                         completed_at = CASE WHEN ? = 'success' THEN CURRENT_TIMESTAMP ELSE completed_at END,
                         updated_at = CURRENT_TIMESTAMP
                         WHERE nomor_sep = ? AND method_code = ?";
            
            $updateStmt = $pdo->prepare($updateSql);
            $result = $updateStmt->execute([
                $methodName,
                $requestData ? json_encode($requestData) : null,
                $responseData ? json_encode($responseData) : null,
                $status,
                $errorCode,
                $errorMessage,
                $executionTime,
                $retryCount,
                $status,
                $nomorSep,
                $methodCode
            ]);
            
            if ($result) {
                return [
                    'success' => true,
                    'action' => 'updated',
                    'retry_count' => $retryCount,
                    'message' => "Method $methodCode ($methodName) berhasil diupdate"
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Gagal update tracking method'
                ];
            }
        } else {
            // Insert new record
            $insertSql = "INSERT INTO eklaim_method_tracking 
                         (nomor_sep, method_code, method_name, request_data, response_data, 
                          status, error_code, error_message, execution_time_ms, retry_count, 
                          first_attempt_at, last_attempt_at, completed_at) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 
                         CASE WHEN ? = 'success' THEN CURRENT_TIMESTAMP ELSE NULL END)";
            
            $insertStmt = $pdo->prepare($insertSql);
            $result = $insertStmt->execute([
                $nomorSep,
                $methodCode,
                $methodName,
                $requestData ? json_encode($requestData) : null,
                $responseData ? json_encode($responseData) : null,
                $status,
                $errorCode,
                $errorMessage,
                $executionTime,
                0,
                $status
            ]);
            
            if ($result) {
                return [
                    'success' => true,
                    'action' => 'inserted',
                    'retry_count' => 0,
                    'message' => "Method $methodCode ($methodName) berhasil disimpan"
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Gagal insert tracking method'
                ];
            }
        }
        
    } catch (PDOException $e) {
        return [
            'success' => false,
            'error' => 'Database error: ' . $e->getMessage()
        ];
    }
}

/**
 * Get tracking status untuk nomor SEP
 * 
 * @param string $nomorSep Nomor SEP
 * @return array Status tracking
 */
function getEklaimMethodStatus($nomorSep) {
    $pdo = getConnection();
    
    try {
        $sql = "SELECT 
                    t.method_code,
                    m.method_name,
                    m.method_description,
                    m.is_required,
                    m.stage_order,
                    t.status,
                    t.error_code,
                    t.error_message,
                    t.execution_time_ms,
                    t.retry_count,
                    t.first_attempt_at,
                    t.last_attempt_at,
                    t.completed_at,
                    t.response_data
                FROM eklaim_method_tracking t
                JOIN eklaim_method_mapping m ON t.method_code = m.method_code
                WHERE t.nomor_sep = ?
                ORDER BY m.stage_order";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nomorSep]);
        $methods = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Hitung statistik
        $totalMethods = count($methods);
        $completedMethods = count(array_filter($methods, function($m) { return $m['status'] === 'success'; }));
        $failedMethods = count(array_filter($methods, function($m) { return $m['status'] === 'error'; }));
        $pendingMethods = count(array_filter($methods, function($m) { return $m['status'] === 'pending'; }));
        $skippedMethods = count(array_filter($methods, function($m) { return $m['status'] === 'skipped'; }));
        
        // Cari method error
        $errorMethods = array_filter($methods, function($m) { return $m['status'] === 'error'; });
        $errorList = array_map(function($m) { 
            return $m['method_name'] . ' (' . $m['error_code'] . ')'; 
        }, $errorMethods);
        
        // Cari next stage
        $nextStage = null;
        foreach ($methods as $method) {
            if ($method['status'] === 'pending') {
                $nextStage = $method;
                break;
            }
        }
        
        return [
            'success' => true,
            'nomor_sep' => $nomorSep,
            'total_methods' => $totalMethods,
            'completed_methods' => $completedMethods,
            'failed_methods' => $failedMethods,
            'pending_methods' => $pendingMethods,
            'skipped_methods' => $skippedMethods,
            'progress_percentage' => $totalMethods > 0 ? round(($completedMethods / $totalMethods) * 100, 2) : 0,
            'error_methods' => $errorList,
            'next_stage' => $nextStage,
            'methods' => $methods
        ];
        
    } catch (PDOException $e) {
        return [
            'success' => false,
            'error' => 'Database error: ' . $e->getMessage()
        ];
    }
}

/**
 * Get semua method yang belum selesai untuk nomor SEP
 * 
 * @param string $nomorSep Nomor SEP
 * @return array List method yang belum selesai
 */
function getPendingEklaimMethods($nomorSep) {
    $pdo = getConnection();
    
    try {
        $sql = "SELECT 
                    m.method_code,
                    m.method_name,
                    m.method_description,
                    m.is_required,
                    m.stage_order,
                    COALESCE(t.status, 'pending') as status,
                    COALESCE(t.retry_count, 0) as retry_count
                FROM eklaim_method_mapping m
                LEFT JOIN eklaim_method_tracking t ON m.method_code = t.method_code AND t.nomor_sep = ?
                WHERE COALESCE(t.status, 'pending') IN ('pending', 'error')
                ORDER BY m.stage_order";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nomorSep]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        return [
            'success' => false,
            'error' => 'Database error: ' . $e->getMessage()
        ];
    }
}

/**
 * Reset tracking untuk nomor SEP (hapus semua record)
 * 
 * @param string $nomorSep Nomor SEP
 * @return array Hasil operasi
 */
function resetEklaimMethodTracking($nomorSep) {
    $pdo = getConnection();
    
    try {
        $sql = "DELETE FROM eklaim_method_tracking WHERE nomor_sep = ?";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$nomorSep]);
        
        if ($result) {
            return [
                'success' => true,
                'message' => "Tracking untuk nomor SEP $nomorSep berhasil direset"
            ];
        } else {
            return [
                'success' => false,
                'error' => 'Gagal reset tracking'
            ];
        }
        
    } catch (PDOException $e) {
        return [
            'success' => false,
            'error' => 'Database error: ' . $e->getMessage()
        ];
    }
}

/**
 * Get summary progress untuk semua nomor SEP
 * 
 * @param int $limit Limit hasil (default 100)
 * @return array Summary progress
 */
function getEklaimProgressSummary($limit = 100) {
    $pdo = getConnection();
    
    try {
        $sql = "SELECT * FROM v_eklaim_progress ORDER BY last_activity DESC LIMIT ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        return [
            'success' => false,
            'error' => 'Database error: ' . $e->getMessage()
        ];
    }
}

/**
 * Hapus record method_code 08 (final_idrg) untuk nomor SEP
 * Digunakan saat re-edit iDRG berhasil
 * 
 * @param string $nomorSep Nomor SEP
 * @return array Hasil operasi
 */
function deleteFinalIdrgTracking($nomorSep) {
    $pdo = getConnection();
    
    try {
        // Validasi input
        if (empty($nomorSep)) {
            return [
                'success' => false,
                'error' => 'Nomor SEP harus diisi'
            ];
        }
        
        // Hapus record method_code 08 dengan method_name final_idrg
        $sql = "DELETE FROM eklaim_method_tracking 
                WHERE nomor_sep = ? AND method_code = '08' AND method_name = 'final_idrg'";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$nomorSep]);
        
        if ($result) {
            $deletedRows = $stmt->rowCount();
            return [
                'success' => true,
                'deleted_rows' => $deletedRows,
                'message' => "Record final_idrg (method_code 08) berhasil dihapus untuk nomor SEP: $nomorSep"
            ];
        } else {
            return [
                'success' => false,
                'error' => 'Gagal menghapus record final_idrg'
            ];
        }
        
    } catch (PDOException $e) {
        return [
            'success' => false,
            'error' => 'Database error: ' . $e->getMessage()
        ];
    }
}