<?php
/**
 * API Endpoint untuk menyimpan data diagnosa
 * Method: POST
 * URL: /api/save_diagnosis.php
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
    if (!isset($input['kunjungan_id']) || !isset($input['diagnosis_data'])) {
        throw new Exception('Missing required fields: kunjungan_id and diagnosis_data');
    }
    
    $kunjungan_id = (int)$input['kunjungan_id'];
    $diagnosis_data = $input['diagnosis_data'];
    
    if (!is_array($diagnosis_data)) {
        throw new Exception('diagnosis_data must be an array');
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
    
    // Delete existing diagnosis data for this kunjungan
    $stmt = $pdo->prepare("DELETE FROM diagnosis_details WHERE kunjungan_id = ?");
    $stmt->execute([$kunjungan_id]);
    
    // Insert new diagnosis data
    $insert_stmt = $pdo->prepare("
        INSERT INTO diagnosis_details (
            kunjungan_id, icd_code_id, diagnosis_order, diagnosis_type,
            icd_code, icd_description, validcode, accpdx, asterisk, im
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $inserted_count = 0;
    
    foreach ($diagnosis_data as $diagnosis) {
        // Validate diagnosis data
        if (!isset($diagnosis['icd_code_id']) || !isset($diagnosis['diagnosis_order']) || 
            !isset($diagnosis['diagnosis_type']) || !isset($diagnosis['icd_code']) || 
            !isset($diagnosis['icd_description'])) {
            throw new Exception('Missing required fields in diagnosis data');
        }
        
        // Validate icd_code_id exists
        $stmt = $pdo->prepare("SELECT id FROM idr_codes WHERE id = ?");
        $stmt->execute([$diagnosis['icd_code_id']]);
        
        if (!$stmt->fetch()) {
            throw new Exception('ICD Code ID tidak ditemukan: ' . $diagnosis['icd_code_id']);
        }
        
        // Insert diagnosis
        $insert_stmt->execute([
            $kunjungan_id,
            $diagnosis['icd_code_id'],
            $diagnosis['diagnosis_order'],
            $diagnosis['diagnosis_type'],
            $diagnosis['icd_code'],
            $diagnosis['icd_description'],
            $diagnosis['validcode'] ?? 1,
            $diagnosis['accpdx'] ?? 'Y',
            $diagnosis['asterisk'] ?? 0,
            $diagnosis['im'] ?? 0
        ]);
        
        $inserted_count++;
    }
    
    // Update diagnosa field in kunjungan_pasien table
    $diagnosis_codes = [];
    foreach ($diagnosis_data as $diagnosis) {
        $diagnosis_codes[] = $diagnosis['icd_code'];
    }
    
    $diagnosis_string = implode('#', $diagnosis_codes);
    
    $stmt = $pdo->prepare("UPDATE kunjungan_pasien SET diagnosa = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
    $stmt->execute([$diagnosis_string, $kunjungan_id]);
    
    // Commit transaction
    $pdo->commit();
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Diagnosa berhasil disimpan',
        'data' => [
            'kunjungan_id' => $kunjungan_id,
            'inserted_count' => $inserted_count,
            'diagnosis_codes' => $diagnosis_codes
        ]
    ]);
    
} catch (PDOException $e) {
    // Rollback transaction if started
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    error_log("Database error in save_diagnosis.php: " . $e->getMessage());
    
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
