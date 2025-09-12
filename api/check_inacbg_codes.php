<?php
/**
 * API untuk mengecek kode INACBG di database
 * File: api/check_inacbg_codes.php
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

try {
    // Get input data
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid JSON input');
    }
    
    $codes = $input['codes'] ?? [];
    
    if (empty($codes) || !is_array($codes)) {
        throw new Exception('Codes array is required');
    }
    
    // Get connection
    $pdo = getConnection();
    
    // Create placeholders for IN clause
    $placeholders = str_repeat('?,', count($codes) - 1) . '?';
    
    // Query untuk mendapatkan kode yang ada di database
    $sql = "SELECT id, CODE FROM inacbg_codes WHERE CODE IN ($placeholders)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($codes);
    $existingCodes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Create array of existing codes
    $existingCodeList = array_column($existingCodes, 'CODE');
    
    // Determine which codes are valid and which are not
    $result = [];
    foreach ($codes as $code) {
        $result[$code] = in_array($code, $existingCodeList);
    }
    
    echo json_encode([
        'success' => true,
        'data' => $result,
        'message' => 'INACBG codes checked successfully'
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
