<?php
/**
 * API Endpoint untuk menyimpan data klinis
 * Method: POST
 * URL: /api/save_clinical_data.php
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
    if (!isset($input['kunjungan_id'])) {
        throw new Exception('Missing required field: kunjungan_id');
    }
    
    $kunjungan_id = (int)$input['kunjungan_id'];
    
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
    
    // Check if clinical data already exists
    $stmt = $pdo->prepare("SELECT id FROM clinical_data WHERE kunjungan_id = ?");
    $stmt->execute([$kunjungan_id]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        // Update existing clinical data
        $update_stmt = $pdo->prepare("
            UPDATE clinical_data SET
                sistole = ?,
                diastole = ?,
                heart_rate = ?,
                temperature = ?,
                oxygen_saturation = ?,
                respiratory_rate = ?,
                blood_glucose = ?,
                notes = ?,
                updated_at = CURRENT_TIMESTAMP
            WHERE kunjungan_id = ?
        ");
        
        $update_stmt->execute([
            $input['sistole'] ?? null,
            $input['diastole'] ?? null,
            $input['heart_rate'] ?? null,
            $input['temperature'] ?? null,
            $input['oxygen_saturation'] ?? null,
            $input['respiratory_rate'] ?? null,
            $input['blood_glucose'] ?? null,
            $input['notes'] ?? null,
            $kunjungan_id
        ]);
        
        $action = 'updated';
    } else {
        // Insert new clinical data
        $insert_stmt = $pdo->prepare("
            INSERT INTO clinical_data (
                kunjungan_id, sistole, diastole, heart_rate, temperature,
                oxygen_saturation, respiratory_rate, blood_glucose, notes
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $insert_stmt->execute([
            $kunjungan_id,
            $input['sistole'] ?? null,
            $input['diastole'] ?? null,
            $input['heart_rate'] ?? null,
            $input['temperature'] ?? null,
            $input['oxygen_saturation'] ?? null,
            $input['respiratory_rate'] ?? null,
            $input['blood_glucose'] ?? null,
            $input['notes'] ?? null
        ]);
        
        $action = 'inserted';
    }
    
    // Update sistole and diastole in kunjungan_pasien table
    $stmt = $pdo->prepare("
        UPDATE kunjungan_pasien SET 
            sistole = ?, 
            diastole = ?, 
            updated_at = CURRENT_TIMESTAMP 
        WHERE id = ?
    ");
    $stmt->execute([
        $input['sistole'] ?? null,
        $input['diastole'] ?? null,
        $kunjungan_id
    ]);
    
    // Commit transaction
    $pdo->commit();
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Data klinis berhasil disimpan',
        'data' => [
            'kunjungan_id' => $kunjungan_id,
            'action' => $action,
            'clinical_data' => [
                'sistole' => $input['sistole'] ?? null,
                'diastole' => $input['diastole'] ?? null,
                'heart_rate' => $input['heart_rate'] ?? null,
                'temperature' => $input['temperature'] ?? null,
                'oxygen_saturation' => $input['oxygen_saturation'] ?? null,
                'respiratory_rate' => $input['respiratory_rate'] ?? null,
                'blood_glucose' => $input['blood_glucose'] ?? null,
                'notes' => $input['notes'] ?? null
            ]
        ]
    ]);
    
} catch (PDOException $e) {
    // Rollback transaction if started
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    error_log("Database error in save_clinical_data.php: " . $e->getMessage());
    
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
