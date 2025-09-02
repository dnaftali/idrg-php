<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

$pdo = getConnection();

try {
    // Total kode valid
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM idr_codes WHERE validcode = 1");
    $totalValid = $stmt->fetch()['total'];
    
    // Total per sistem
    $stmt = $pdo->query("SELECT system, COUNT(*) as count FROM idr_codes WHERE validcode = 1 GROUP BY system");
    $systemStats = $stmt->fetchAll();
    
    // Total kode tidak valid
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM idr_codes WHERE validcode = 0");
    $totalInvalid = $stmt->fetch()['total'];
    
    echo json_encode([
        'success' => true,
        'data' => [
            'total_valid' => $totalValid,
            'total_invalid' => $totalInvalid,
            'system_stats' => $systemStats
        ]
    ]);
    
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
