<?php
/**
 * API Endpoint untuk menyimpan data prosedur
 * Method: POST
 * URL: /api/save_procedure.php
 */

// Set header untuk JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Hanya terima POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Method not allowed. Only POST method is supported.'
    ]);
    exit();
}

// Include database configuration
require_once '../config/database.php';

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid JSON input');
    }
    
    // Validate required fields
    if (!isset($input['kunjungan_id']) || !isset($input['procedure_data'])) {
        throw new Exception('Missing required fields: kunjungan_id and procedure_data');
    }
    
    $kunjungan_id = (int)$input['kunjungan_id'];
    $procedure_data = $input['procedure_data'];
    
    if (!is_array($procedure_data)) {
        throw new Exception('procedure_data must be an array');
    }
    
    // Validate kunjungan_id exists
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare("SELECT id FROM kunjungan_pasien WHERE id = ?");
    $stmt->execute([$kunjungan_id]);
    
    if (!$stmt->fetch()) {
        throw new Exception('Kunjungan ID tidak ditemukan');
    }
    
    // Start transaction
    $pdo->beginTransaction();
    
    // Delete existing procedure data for this kunjungan
    $stmt = $pdo->prepare("DELETE FROM procedure_details WHERE kunjungan_id = ?");
    $stmt->execute([$kunjungan_id]);
    
    // Insert new procedure data
    $insert_stmt = $pdo->prepare("
        INSERT INTO procedure_details (
            kunjungan_id, icd_code_id, procedure_order, procedure_type,
            icd_code, icd_description, quantity, validcode, accpdx, asterisk, im
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $inserted_count = 0;
    
    foreach ($procedure_data as $procedure) {
        // Validate procedure data
        if (!isset($procedure['icd_code_id']) || !isset($procedure['procedure_order']) || 
            !isset($procedure['procedure_type']) || !isset($procedure['icd_code']) || 
            !isset($procedure['icd_description']) || !isset($procedure['quantity'])) {
            throw new Exception('Missing required fields in procedure data');
        }
        
        // Validate icd_code_id exists
        $stmt = $pdo->prepare("SELECT id FROM idr_codes WHERE id = ?");
        $stmt->execute([$procedure['icd_code_id']]);
        
        if (!$stmt->fetch()) {
            throw new Exception('ICD Code ID tidak ditemukan: ' . $procedure['icd_code_id']);
        }
        
        // Insert procedure
        $insert_stmt->execute([
            $kunjungan_id,
            $procedure['icd_code_id'],
            $procedure['procedure_order'],
            $procedure['procedure_type'],
            $procedure['icd_code'],
            $procedure['icd_description'],
            $procedure['quantity'],
            $procedure['validcode'] ?? 1,
            $procedure['accpdx'] ?? 'Y',
            $procedure['asterisk'] ?? 0,
            $procedure['im'] ?? 0
        ]);
        
        $inserted_count++;
    }
    
    // Update procedures field in kunjungan_pasien table
    $procedure_codes = [];
    foreach ($procedure_data as $procedure) {
        $procedure_codes[] = $procedure['icd_code'];
    }
    
    $procedure_string = implode('#', $procedure_codes);
    
    $stmt = $pdo->prepare("UPDATE kunjungan_pasien SET procedures = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
    $stmt->execute([$procedure_string, $kunjungan_id]);
    
    // Commit transaction
    $pdo->commit();
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Prosedur berhasil disimpan',
        'data' => [
            'kunjungan_id' => $kunjungan_id,
            'inserted_count' => $inserted_count,
            'procedure_codes' => $procedure_codes
        ]
    ]);
    
} catch (PDOException $e) {
    // Rollback transaction if started
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    error_log("Database error in save_procedure.php: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
    
} catch (Exception $e) {
    // Rollback transaction if started
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
