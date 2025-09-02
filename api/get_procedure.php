<?php
/**
 * API Endpoint untuk mengambil data prosedur
 * Method: GET
 * URL: /api/get_procedure.php?kunjungan_id=1
 */

// Set header untuk JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Hanya terima GET request
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Method not allowed. Only GET method is supported.'
    ]);
    exit();
}

// Include database configuration
require_once '../config/database.php';

try {
    // Get kunjungan_id from query parameter
    $kunjungan_id = isset($_GET['kunjungan_id']) ? (int)$_GET['kunjungan_id'] : null;
    
    if (!$kunjungan_id) {
        throw new Exception('Missing required parameter: kunjungan_id');
    }
    
    // Validate kunjungan_id exists
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare("SELECT id FROM kunjungan_pasien WHERE id = ?");
    $stmt->execute([$kunjungan_id]);
    
    if (!$stmt->fetch()) {
        throw new Exception('Kunjungan ID tidak ditemukan');
    }
    
    // Get procedure data using view
    $stmt = $pdo->prepare("
        SELECT 
            id,
            kunjungan_id,
            procedure_order,
            procedure_type,
            icd_code,
            icd_description,
            quantity,
            validcode,
            accpdx,
            asterisk,
            im,
            system,
            created_at,
            updated_at
        FROM v_procedure_complete 
        WHERE kunjungan_id = ? 
        ORDER BY procedure_order ASC
    ");
    
    $stmt->execute([$kunjungan_id]);
    $procedure_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Data prosedur berhasil diambil',
        'data' => [
            'kunjungan_id' => $kunjungan_id,
            'procedure_count' => count($procedure_data),
            'procedure_list' => $procedure_data
        ]
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in get_procedure.php: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
