<?php
/**
 * Logging Functions untuk Web Service E-Klaim INA-CBG
 */

/**
 * Log web service request
 */
function logWebServiceRequest($method, $data) {
    $logData = [
        'timestamp' => date('Y-m-d H:i:s'),
        'type' => 'request',
        'method' => $method,
        'data' => $data,
        'ip_address' => getClientIP(),
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
    ];
    
    // Log ke file
    logToFile('web_service_requests.log', $logData);
    
    // Log ke database jika diperlukan
    logToDatabase('request', $method, $data);
}

/**
 * Log web service response
 */
function logWebServiceResponse($method, $data, $response) {
    $logData = [
        'timestamp' => date('Y-m-d H:i:s'),
        'type' => 'response',
        'method' => $method,
        'request_data' => $data,
        'response_data' => $response,
        'ip_address' => getClientIP(),
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
    ];
    
    // Log ke file
    logToFile('web_service_responses.log', $logData);
    
    // Log ke database jika diperlukan
    logToDatabase('response', $method, $data, $response);
}

/**
 * Log web service error
 */
function logWebServiceError($method, $data, $error, $errorCode = null) {
    $logData = [
        'timestamp' => date('Y-m-d H:i:s'),
        'type' => 'error',
        'method' => $method,
        'request_data' => $data,
        'error_message' => $error,
        'error_code' => $errorCode,
        'ip_address' => getClientIP(),
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
    ];
    
    // Log ke file
    logToFile('web_service_errors.log', $logData);
    
    // Log ke database
    logToDatabase('error', $method, $data, null, $error, $errorCode);
}

/**
 * Log ke file
 */
function logToFile($filename, $data) {
    $logDir = '../logs/';
    
    // Buat direktori logs jika belum ada
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $logFile = $logDir . $filename;
    $logEntry = date('Y-m-d H:i:s') . ' - ' . json_encode($data) . PHP_EOL;
    
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

/**
 * Log ke database
 */
function logToDatabase($type, $method, $requestData, $responseData = null, $errorMessage = null, $errorCode = null) {
    try {
        $pdo = getConnection();
        
        $sql = "INSERT INTO web_service_logs (
            method, nomor_sep, request_data, response_data, 
            status, error_code, error_message, ip_address, user_agent
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        
        // Extract nomor_sep from request data if available
        $nomorSep = null;
        if (is_array($requestData) && isset($requestData['nomor_sep'])) {
            $nomorSep = $requestData['nomor_sep'];
        }
        
        $stmt->execute([
            $method,
            $nomorSep,
            is_array($requestData) ? json_encode($requestData) : $requestData,
            is_array($responseData) ? json_encode($responseData) : $responseData,
            $type === 'error' ? 'error' : 'success',
            $errorCode,
            $errorMessage,
            getClientIP(),
            $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
        ]);
        
    } catch (Exception $e) {
        // Jika gagal log ke database, log ke file error
        error_log("Failed to log to database: " . $e->getMessage());
    }
}

/**
 * Get client IP address
 */
function getClientIP() {
    $ipKeys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
    
    foreach ($ipKeys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    return $ip;
                }
            }
        }
    }
    
    return $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
}

/**
 * Clean old logs
 */
function cleanOldLogs($days = 30) {
    $logDir = '../logs/';
    
    if (!is_dir($logDir)) {
        return;
    }
    
    $files = glob($logDir . '*.log');
    $cutoff = time() - ($days * 24 * 60 * 60);
    
    foreach ($files as $file) {
        if (filemtime($file) < $cutoff) {
            unlink($file);
        }
    }
}

/**
 * Get log statistics
 */
function getLogStatistics($days = 7) {
    try {
        $pdo = getConnection();
        
        $sql = "SELECT 
                    method,
                    COUNT(*) as total_calls,
                    SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as success,
                    SUM(CASE WHEN status = 'error' THEN 1 ELSE 0 END) as errors,
                    AVG(execution_time_ms) as avg_execution_time
                FROM web_service_logs 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                GROUP BY method";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$days]);
        
        return $stmt->fetchAll();
        
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Get error logs
 */
function getErrorLogs($limit = 100) {
    try {
        $pdo = getConnection();
        
        $sql = "SELECT 
                    method,
                    nomor_sep,
                    error_code,
                    error_message,
                    created_at
                FROM web_service_logs 
                WHERE status = 'error'
                ORDER BY created_at DESC
                LIMIT ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$limit]);
        
        return $stmt->fetchAll();
        
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Log performance metrics
 */
function logPerformanceMetrics($method, $startTime, $endTime = null) {
    if (!$endTime) {
        $endTime = microtime(true);
    }
    
    $executionTime = round(($endTime - $startTime) * 1000, 2); // Convert to milliseconds
    
    try {
        $pdo = getConnection();
        
        $sql = "UPDATE web_service_logs 
                SET execution_time_ms = ? 
                WHERE method = ? 
                AND created_at = (
                    SELECT created_at FROM (
                        SELECT created_at FROM web_service_logs 
                        WHERE method = ? 
                        ORDER BY created_at DESC 
                        LIMIT 1
                    ) as sub
                )";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$executionTime, $method, $method]);
        
    } catch (Exception $e) {
        // Ignore performance logging errors
    }
}

/**
 * Log audit trail
 */
function logAuditTrail($action, $table, $recordId, $oldData = null, $newData = null, $userId = null) {
    try {
        $pdo = getConnection();
        
        $sql = "INSERT INTO audit_trail (
            action, table_name, record_id, old_data, new_data, 
            user_id, ip_address, user_agent, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $action,
            $table,
            $recordId,
            $oldData ? json_encode($oldData) : null,
            $newData ? json_encode($newData) : null,
            $userId,
            getClientIP(),
            $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
        ]);
        
    } catch (Exception $e) {
        // Log audit trail error
        error_log("Failed to log audit trail: " . $e->getMessage());
    }
}

/**
 * Get audit trail
 */
function getAuditTrail($table = null, $recordId = null, $limit = 100) {
    try {
        $pdo = getConnection();
        
        $sql = "SELECT * FROM audit_trail WHERE 1=1";
        $params = [];
        
        if ($table) {
            $sql .= " AND table_name = ?";
            $params[] = $table;
        }
        
        if ($recordId) {
            $sql .= " AND record_id = ?";
            $params[] = $recordId;
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT ?";
        $params[] = $limit;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
        
    } catch (Exception $e) {
        return [];
    }
}
?>
