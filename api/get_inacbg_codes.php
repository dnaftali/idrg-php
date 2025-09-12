<?php
/**
 * API Endpoint untuk mengambil data INACBG codes
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

// Include database connection
require_once '../config/database.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get search term from query parameters
    $searchTerm = $_GET['q'] ?? '';
    $type = $_GET['type'] ?? 'all'; // 'diagnosis' or 'procedure' or 'all'
    
    // Log parameters for debugging
    error_log("INACBG API called with searchTerm: '$searchTerm', type: '$type'");
    
    // First, let's check what systems are available in the database
    $checkSql = "SELECT DISTINCT system FROM inacbg_codes ORDER BY system";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute();
    $availableSystems = $checkStmt->fetchAll(PDO::FETCH_COLUMN);
    error_log("INACBG API available systems: " . json_encode($availableSystems));
    
    // Build query
    $sql = "SELECT id, code, code2, description, system, validcode FROM inacbg_codes WHERE 1=1";
    $params = [];
    
    // Add search filter
    if (!empty($searchTerm)) {
        $sql .= " AND (code LIKE :search OR code2 LIKE :search OR description LIKE :search)";
        $params[':search'] = '%' . $searchTerm . '%';
    }
    
    // Add type filter if specified
    if ($type === 'diagnosis') {
        // Try ICD_10_2010 first, then fallback to ICD_9CM_2010 if no results
        $sql .= " AND system = 'ICD_10_2010'";
    } elseif ($type === 'procedure') {
        $sql .= " AND system = 'ICD_9CM_2010'";
    }
    
    // Log final query for debugging
    error_log("INACBG API final query: $sql");
    error_log("INACBG API params: " . json_encode($params));
    
    // Add ordering
    $sql .= " ORDER BY code ASC";
    
    // Add limit for performance
    $sql .= " LIMIT 100";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Log results for debugging
    error_log("INACBG API found " . count($results) . " results");
    if (count($results) > 0) {
        error_log("INACBG API first result: " . json_encode($results[0]));
    }
    
    // If no results for diagnosis and we're looking for diagnosis, try fallback
    if (count($results) === 0 && $type === 'diagnosis') {
        error_log("INACBG API no results for ICD_10_2010, trying fallback to ICD_9CM_2010");
        
        // Build fallback query
        $fallbackSql = "SELECT id, code, code2, description, system, validcode FROM inacbg_codes WHERE system = 'ICD_9CM_2010'";
        $fallbackParams = [];
        
        if (!empty($searchTerm)) {
            $fallbackSql .= " AND (code LIKE :search OR code2 LIKE :search OR description LIKE :search)";
            $fallbackParams[':search'] = '%' . $searchTerm . '%';
        }
        
        $fallbackSql .= " ORDER BY code ASC LIMIT 100";
        
        error_log("INACBG API fallback query: $fallbackSql");
        
        $fallbackStmt = $pdo->prepare($fallbackSql);
        $fallbackStmt->execute($fallbackParams);
        $results = $fallbackStmt->fetchAll(PDO::FETCH_ASSOC);
        
        error_log("INACBG API fallback found " . count($results) . " results");
    }
    
    // Format results for Select2
    $formattedResults = [];
    foreach ($results as $row) {
        $formattedResults[] = [
            'id' => $row['id'],
            'text' => $row['code'] . ' - ' . $row['description'],
            'code' => $row['code'],
            'code2' => $row['code2'],
            'description' => $row['description'],
            'system' => $row['system'],
            'validcode' => $row['validcode']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'results' => $formattedResults,
        'total' => count($formattedResults)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
