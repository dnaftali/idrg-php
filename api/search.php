<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

try {
    require_once '../config/database.php';
    
    $pdo = getConnection();
    
    $system = $_GET['system'] ?? '';
    $search = $_GET['search'] ?? '';
    $limit = (int)($_GET['limit'] ?? 20);
    
    // Validate limit
    if ($limit <= 0) $limit = 20;
    if ($limit > 100) $limit = 100;
    
    // Debug log
    error_log("Search API called with: system='" . $system . "', search='" . $search . "', limit=$limit");
    
    $sql = "SELECT id, code, code2, description, system, validcode, accpdx, asterisk, im 
            FROM idr_codes 
            WHERE 1 = 1";
    
    $params = [];
    
    if (!empty($system)) {
        $sql .= " AND system = ?";
        $params[] = trim($system);
    }
    
    if (!empty($search)) {
        $sql .= " AND (code LIKE ? OR code2 LIKE ? OR description LIKE ?)";
        $searchTerm = "%" . trim($search) . "%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    $sql .= " ORDER BY code ASC LIMIT " . $limit;
    
    // Debug log
    error_log("SQL Query: $sql");
    error_log("Parameters: " . json_encode($params));
    error_log("Limit value: $limit (type: " . gettype($limit) . ")");
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll();
    
    error_log("Query executed successfully. Found " . count($results) . " results");
    error_log("Final SQL: $sql");
    
    $response = [
        'success' => true,
        'data' => $results,
        'count' => count($results)
    ];
    
    echo json_encode($response);
    
} catch(PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
} catch(Exception $e) {
    error_log("General error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'General error: ' . $e->getMessage()
    ]);
}
?>
