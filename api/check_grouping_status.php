<?php
/**
 * API untuk memeriksa status grouping dari tabel eklaim_method_tracking
 * File: api/check_grouping_status.php
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/database.php';
require_once '../functions/eklaim_method_tracking.php';

try {
    // Get input data
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid JSON input');
    }
    
    $nomorSep = $input['nomor_sep'] ?? '';
    $methodCode = $input['method_code'] ?? '07'; // Default method 07 (grouping)
    
    if (empty($nomorSep)) {
        throw new Exception('Nomor SEP is required');
    }
    
    // Get connection
    $pdo = getConnection();
    
    // Query untuk mendapatkan data tracking method
    $sql = "SELECT 
                t.id,
                t.nomor_sep,
                t.method_code,
                t.method_name,
                t.request_data,
                t.response_data,
                t.status,
                t.error_code,
                t.error_message,
                t.execution_time_ms,
                t.retry_count,
                t.first_attempt_at,
                t.last_attempt_at,
                t.completed_at,
                t.created_at,
                t.updated_at
            FROM eklaim_method_tracking t
            WHERE t.nomor_sep = ? AND t.method_code = ?
            ORDER BY t.last_attempt_at DESC
            LIMIT 1";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nomorSep, $methodCode]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        // Parse JSON fields
        $result['request_data'] = $result['request_data'] ? json_decode($result['request_data'], true) : null;
        $result['response_data'] = $result['response_data'] ? json_decode($result['response_data'], true) : null;
        
        // Check if the response indicates success (prioritize response_data over status field)
        $isSuccessful = false;
        
        if (!empty($result['response_data'])) {
            // Check if response_data contains success indication
            if (isset($result['response_data']['success']) && $result['response_data']['success'] === true) {
                $isSuccessful = true;
            }
        } else {
            // Fallback to status field if response_data is not available
            $isSuccessful = ($result['status'] === 'success');
        }
        
        // Only return data if the method was actually successful
        if ($isSuccessful) {
            echo json_encode([
                'success' => true,
                'data' => $result,
                'message' => 'Grouping status found'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'data' => null,
                'message' => 'Grouping found but not successful (status: ' . $result['status'] . ')'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'data' => null,
            'message' => 'No grouping found for this nomor SEP'
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
