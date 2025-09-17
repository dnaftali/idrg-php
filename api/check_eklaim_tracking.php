<?php
/**
 * API Endpoint untuk mengecek status E-Klaim method tracking
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include database connection and tracking functions
require_once '../config/database.php';
require_once '../functions/eklaim_method_tracking.php';

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid JSON input');
    }
    
    $nomorSep = $input['nomor_sep'] ?? '';
    $methodCode = $input['method_code'] ?? null; // Optional method code filter
    
    if (empty($nomorSep)) {
        throw new Exception('Nomor SEP is required');
    }
    
    // Get tracking status
    $trackingStatus = getEklaimMethodStatus($nomorSep);
    
    if (!$trackingStatus['success']) {
        throw new Exception('Failed to get tracking status: ' . $trackingStatus['error']);
    }
    
    // If specific method_code is requested, return only that method
    if ($methodCode) {
        $methodRecord = null;
        foreach ($trackingStatus['methods'] as $method) {
            if ($method['method_code'] === $methodCode) {
                $methodRecord = $method;
                break;
            }
        }
        
        if ($methodRecord) {
            echo json_encode([
                'success' => true,
                'nomor_sep' => $nomorSep,
                'method_code' => $methodCode,
                'method_record' => $methodRecord
            ]);
            exit();
        } else {
            echo json_encode([
                'success' => false,
                'error' => "Method code $methodCode not found for nomor_sep $nomorSep"
            ]);
            exit();
        }
    }
    
    // Check if final_idrg (method_code 08) is successful
    $finalIdrgSuccess = false;
    $finalIdrgRecord = null;
    
    foreach ($trackingStatus['methods'] as $method) {
        if ($method['method_code'] === '08' && $method['method_name'] === 'final_idrg') {
            // Prioritize response_data over status field for determining success
            $isResponseSuccess = false;
            
            // Check response_data for success indication (primary check)
            if (!empty($method['response_data'])) {
                $responseData = json_decode($method['response_data'], true);
                if (isset($responseData['success']) && $responseData['success'] === true) {
                    $isResponseSuccess = true;
                }
            }
            
            // Fallback to status field if response_data is not available
            $isStatusSuccess = ($method['status'] === 'success');
            
            // Consider final_idrg successful if either response_data shows success OR status is success
            if ($isResponseSuccess || $isStatusSuccess) {
                $finalIdrgSuccess = true;
                $finalIdrgRecord = $method;
                break;
            }
        }
    }
    
    // Prepare response
    $response = [
        'success' => true,
        'nomor_sep' => $nomorSep,
        'final_idrg_success' => $finalIdrgSuccess,
        'final_idrg_record' => $finalIdrgRecord,
        'tracking_summary' => [
            'total_methods' => $trackingStatus['total_methods'],
            'completed_methods' => $trackingStatus['completed_methods'],
            'failed_methods' => $trackingStatus['failed_methods'],
            'pending_methods' => $trackingStatus['pending_methods'],
            'skipped_methods' => $trackingStatus['skipped_methods']
        ],
        'all_methods' => $trackingStatus['methods']
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
