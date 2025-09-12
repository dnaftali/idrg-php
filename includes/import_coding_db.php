<?php
/**
 * Import Coding Database Functions
 * Fungsi untuk menyimpan data import coding dari IDRG ke INACBG
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/logging_functions.php';

class ImportCodingDB {
    private $conn;
    
    public function __construct() {
        $this->conn = getConnection();
    }
    
    /**
     * Simpan log import coding
     */
    public function saveImportLog($nomorSep, $status, $totalDiagnosis, $totalProcedure, $responseMessage, $metadata) {
        try {
            $sql = "INSERT INTO import_coding_log (nomor_sep, status, total_diagnosis, total_procedure, response_message, metadata) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->conn->prepare($sql);
            $metadataJson = json_encode($metadata);
            
            if ($stmt->execute([$nomorSep, $status, $totalDiagnosis, $totalProcedure, $responseMessage, $metadataJson])) {
                $importLogId = $this->conn->lastInsertId();
                logWebServiceRequest("Import Coding Log", [
                    'nomor_sep' => $nomorSep,
                    'status' => $status,
                    'import_log_id' => $importLogId
                ]);
                return $importLogId;
            } else {
                throw new Exception("Failed to save import log");
            }
        } catch (Exception $e) {
            logWebServiceError("Import Coding Log Error", [
                'nomor_sep' => $nomorSep,
                'status' => $status
            ], $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Ambil deskripsi dari tabel inacbg_codes berdasarkan icd_code
     * Jika tidak ditemukan, cari di idr_codes
     */
    public function getInacbgDescription($icdCode) {
        try {
            // Cari di inacbg_codes terlebih dahulu
            $sql = "SELECT description FROM inacbg_codes WHERE code = ? LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$icdCode]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                return $result['description'];
            }
            
            // Jika tidak ditemukan di inacbg_codes, cari di idr_codes
            $sql = "SELECT description FROM idr_codes WHERE code = ? LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$icdCode]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                return $result['description'] . ' <span class="text-danger">(IM tidak berlaku)</span>';
            }
            
            // Jika tidak ditemukan di kedua tabel, gunakan kode sebagai fallback
            return $icdCode . ' <span class="text-danger">(IM tidak berlaku)</span>';
            
        } catch (Exception $e) {
            logWebServiceError("Get INACBG Description Error", [
                'icd_code' => $icdCode
            ], $e->getMessage());
            return $icdCode; // Fallback ke icd_code jika error
        }
    }
    
    /**
     * Ambil deskripsi untuk multiple kode sekaligus
     * Jika tidak ditemukan di inacbg_codes, cari di idr_codes
     */
    public function getInacbgDescriptions($icdCodes) {
        try {
            if (empty($icdCodes)) {
                return [];
            }
            
            $placeholders = str_repeat('?,', count($icdCodes) - 1) . '?';
            $sql = "SELECT code, description FROM inacbg_codes WHERE code IN ($placeholders)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($icdCodes);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Convert to associative array
            $descriptions = [];
            foreach ($results as $row) {
                $descriptions[$row['code']] = $row['description'];
            }
            
            // Cari kode yang tidak ditemukan di inacbg_codes
            $missingCodes = [];
            foreach ($icdCodes as $code) {
                if (!isset($descriptions[$code])) {
                    $missingCodes[] = $code;
                }
            }
            
            // Jika ada kode yang tidak ditemukan, cari di idr_codes
            if (!empty($missingCodes)) {
                $missingPlaceholders = str_repeat('?,', count($missingCodes) - 1) . '?';
                $sql = "SELECT code, description FROM idr_codes WHERE code IN ($missingPlaceholders)";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute($missingCodes);
                $idrResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Tambahkan deskripsi dari idr_codes dengan teks "(IM tidak berlaku)"
                foreach ($idrResults as $row) {
                    $descriptions[$row['code']] = $row['description'] . ' <span class="text-danger">(IM tidak berlaku)</span>';
                }
                
                // Untuk kode yang masih tidak ditemukan di kedua tabel, gunakan kode sebagai fallback
                foreach ($missingCodes as $code) {
                    if (!isset($descriptions[$code])) {
                        $descriptions[$code] = $code . ' <span class="text-danger">(IM tidak berlaku)</span>';
                    }
                }
            }
            
            // Fill missing codes dengan kode sendiri sebagai fallback (jika belum diisi)
            foreach ($icdCodes as $code) {
                if (!isset($descriptions[$code])) {
                    $descriptions[$code] = $code;
                }
            }
            
            return $descriptions;
            
        } catch (Exception $e) {
            logWebServiceError("Get INACBG Descriptions Error", [
                'icd_codes' => $icdCodes
            ], $e->getMessage());
            
            // Fallback: return codes as descriptions
            $descriptions = [];
            foreach ($icdCodes as $code) {
                $descriptions[$code] = $code;
            }
            return $descriptions;
        }
    }
    
    /**
     * Simpan data diagnosa import
     */
    public function saveImportDiagnosis($importLogId, $nomorSep, $diagnosisData) {
        try {
            // Ambil semua kode ICD untuk mendapatkan deskripsi sekaligus
            $icdCodes = array_column($diagnosisData, 'icd_code');
            $descriptions = $this->getInacbgDescriptions($icdCodes);
            
            $sql = "INSERT INTO import_coding_diagnosis (import_log_id, nomor_sep, diagnosis_type, icd_code, icd_description, is_primary) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->conn->prepare($sql);
            $savedCount = 0;
            
            foreach ($diagnosisData as $index => $diagnosis) {
                $diagnosisType = $diagnosis['diagnosis_type'] ?? '1';
                $icdCode = $diagnosis['icd_code'] ?? '';
                $icdDescription = $descriptions[$icdCode] ?? $icdCode; // Gunakan deskripsi dari inacbg_codes
                $isPrimary = ($index === 0) ? 1 : 0; // Row pertama adalah primary
                
                if ($stmt->execute([$importLogId, $nomorSep, $diagnosisType, $icdCode, $icdDescription, $isPrimary])) {
                    $savedCount++;
                } else {
                    logWebServiceError("Import Diagnosis Error", [
                        'import_log_id' => $importLogId,
                        'icd_code' => $icdCode
                    ], "Failed to execute query");
                }
            }
            
            logWebServiceRequest("Import Diagnosis", [
                'import_log_id' => $importLogId,
                'saved_count' => $savedCount,
                'total_count' => count($diagnosisData)
            ]);
            
            return $savedCount;
        } catch (Exception $e) {
            logWebServiceError("Import Diagnosis Error", [
                'import_log_id' => $importLogId,
                'nomor_sep' => $nomorSep
            ], $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Simpan data prosedur import
     */
    public function saveImportProcedure($importLogId, $nomorSep, $procedureData) {
        try {
            // Ambil semua kode ICD untuk mendapatkan deskripsi sekaligus
            $icdCodes = array_column($procedureData, 'icd_code');
            $descriptions = $this->getInacbgDescriptions($icdCodes);
            
            $sql = "INSERT INTO import_coding_procedure (import_log_id, nomor_sep, procedure_type, icd_code, icd_description, quantity, is_primary) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->conn->prepare($sql);
            $savedCount = 0;
            
            foreach ($procedureData as $index => $procedure) {
                $procedureType = $procedure['procedure_type'] ?? '1';
                $icdCode = $procedure['icd_code'] ?? '';
                $icdDescription = $descriptions[$icdCode] ?? $icdCode; // Gunakan deskripsi dari inacbg_codes
                $quantity = $procedure['quantity'] ?? 1;
                $isPrimary = ($index === 0) ? 1 : 0; // Row pertama adalah primary
                
                if ($stmt->execute([$importLogId, $nomorSep, $procedureType, $icdCode, $icdDescription, $quantity, $isPrimary])) {
                    $savedCount++;
                } else {
                    logWebServiceError("Import Procedure Error", [
                        'import_log_id' => $importLogId,
                        'icd_code' => $icdCode
                    ], "Failed to execute query");
                }
            }
            
            logWebServiceRequest("Import Procedure", [
                'import_log_id' => $importLogId,
                'saved_count' => $savedCount,
                'total_count' => count($procedureData)
            ]);
            
            return $savedCount;
        } catch (Exception $e) {
            logWebServiceError("Import Procedure Error", [
                'import_log_id' => $importLogId,
                'nomor_sep' => $nomorSep
            ], $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Simpan semua data import (log + diagnosis + procedure)
     */
    public function saveImportData($nomorSep, $responseData) {
        try {
            // Mulai transaksi
            $this->conn->beginTransaction();
            
            $diagnosisData = $responseData['diagnosis'] ?? [];
            $procedureData = $responseData['procedure'] ?? [];
            $metadata = $responseData['metadata'] ?? [];
            $message = $responseData['message'] ?? 'Import coding berhasil';
            
            // Tentukan status berdasarkan data yang tersedia
            $status = 'success';
            if (empty($diagnosisData) && empty($procedureData)) {
                $status = 'failed';
            } elseif (empty($diagnosisData) || empty($procedureData)) {
                $status = 'partial';
            }
            
            // Simpan log import
            $importLogId = $this->saveImportLog(
                $nomorSep, 
                $status, 
                count($diagnosisData), 
                count($procedureData), 
                $message, 
                $metadata
            );
            
            $savedDiagnosisCount = 0;
            $savedProcedureCount = 0;
            
            // Simpan data diagnosa
            if (!empty($diagnosisData)) {
                $savedDiagnosisCount = $this->saveImportDiagnosis($importLogId, $nomorSep, $diagnosisData);
            }
            
            // Simpan data prosedur
            if (!empty($procedureData)) {
                $savedProcedureCount = $this->saveImportProcedure($importLogId, $nomorSep, $procedureData);
            }
            
            // Commit transaksi
            $this->conn->commit();
            
            logWebServiceResponse("Import Coding Save", [
                'nomor_sep' => $nomorSep,
                'status' => $status
            ], [
                'import_log_id' => $importLogId,
                'saved_diagnosis' => $savedDiagnosisCount,
                'saved_procedure' => $savedProcedureCount
            ]);
            
            return [
                'success' => true,
                'import_log_id' => $importLogId,
                'saved_diagnosis_count' => $savedDiagnosisCount,
                'saved_procedure_count' => $savedProcedureCount,
                'status' => $status
            ];
            
        } catch (Exception $e) {
            // Rollback transaksi jika terjadi error
            $this->conn->rollBack();
            
            logWebServiceError("Import Coding Save Error", [
                'nomor_sep' => $nomorSep
            ], $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Ambil data import berdasarkan nomor SEP
     */
    public function getImportDataBySep($nomorSep) {
        try {
            $sql = "SELECT 
                        il.*,
                        COUNT(DISTINCT id.id) as diagnosis_count,
                        COUNT(DISTINCT ip.id) as procedure_count
                    FROM import_coding_log il
                    LEFT JOIN import_coding_diagnosis id ON il.id = id.import_log_id
                    LEFT JOIN import_coding_procedure ip ON il.id = ip.import_log_id
                    WHERE il.nomor_sep = ?
                    GROUP BY il.id
                    ORDER BY il.import_date DESC";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$nomorSep]);
            
            $imports = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $imports;
        } catch (Exception $e) {
            logWebServiceError("Get Import Data Error", [
                'nomor_sep' => $nomorSep
            ], $e->getMessage());
            return [];
        }
    }
    
    /**
     * Ambil detail diagnosa import
     */
    public function getImportDiagnosis($importLogId) {
        try {
            $sql = "SELECT * FROM import_coding_diagnosis WHERE import_log_id = ? ORDER BY is_primary DESC, id ASC";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$importLogId]);
            
            $diagnosis = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $diagnosis;
        } catch (Exception $e) {
            logWebServiceError("Get Import Diagnosis Error", [
                'import_log_id' => $importLogId
            ], $e->getMessage());
            return [];
        }
    }
    
    /**
     * Hapus data import berdasarkan nomor SEP
     */
    public function deleteImportDataBySep($nomorSep) {
        try {
            // Mulai transaksi
            $this->conn->beginTransaction();
            
            // Ambil semua import_log_id untuk nomor SEP ini
            $sql = "SELECT id FROM import_coding_log WHERE nomor_sep = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$nomorSep]);
            $importLogIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            $deletedDiagnosisCount = 0;
            $deletedProcedureCount = 0;
            $deletedLogCount = 0;
            
            if (!empty($importLogIds)) {
                // Hapus data diagnosa
                $sql = "DELETE FROM import_coding_diagnosis WHERE import_log_id IN (" . implode(',', array_fill(0, count($importLogIds), '?')) . ")";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute($importLogIds);
                $deletedDiagnosisCount = $stmt->rowCount();
                
                // Hapus data prosedur
                $sql = "DELETE FROM import_coding_procedure WHERE import_log_id IN (" . implode(',', array_fill(0, count($importLogIds), '?')) . ")";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute($importLogIds);
                $deletedProcedureCount = $stmt->rowCount();
                
                // Hapus log import
                $sql = "DELETE FROM import_coding_log WHERE nomor_sep = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([$nomorSep]);
                $deletedLogCount = $stmt->rowCount();
            }
            
            // Commit transaksi
            $this->conn->commit();
            
            logWebServiceRequest("Import Coding Delete", [
                'nomor_sep' => $nomorSep,
                'deleted_logs' => $deletedLogCount,
                'deleted_diagnosis' => $deletedDiagnosisCount,
                'deleted_procedure' => $deletedProcedureCount
            ]);
            
            return [
                'success' => true,
                'deleted_log_count' => $deletedLogCount,
                'deleted_diagnosis_count' => $deletedDiagnosisCount,
                'deleted_procedure_count' => $deletedProcedureCount
            ];
            
        } catch (Exception $e) {
            // Rollback transaksi jika terjadi error
            $this->conn->rollBack();
            
            logWebServiceError("Import Coding Delete Error", [
                'nomor_sep' => $nomorSep
            ], $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Ambil data import terbaru berdasarkan nomor SEP untuk ditampilkan di INACBG
     */
    public function getLatestImportDataForInacbg($nomorSep) {
        try {
            // Ambil import log terbaru untuk nomor SEP ini
            $sql = "SELECT * FROM import_coding_log WHERE nomor_sep = ? ORDER BY import_date DESC LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$nomorSep]);
            $importLog = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$importLog) {
                return [
                    'success' => true,
                    'data' => null,
                    'message' => 'Tidak ada data import ditemukan'
                ];
            }
            
            // Ambil data diagnosa
            $diagnosisData = $this->getImportDiagnosis($importLog['id']);
            
            // Ambil data prosedur
            $procedureData = $this->getImportProcedure($importLog['id']);
            
            // Format data untuk INACBG
            $formattedDiagnosis = [];
            foreach ($diagnosisData as $diagnosis) {
                $formattedDiagnosis[] = [
                    'diagnosis_type' => $diagnosis['diagnosis_type'],
                    'icd_code' => $diagnosis['icd_code'],
                    'icd_description' => $diagnosis['icd_description'],
                    'is_primary' => $diagnosis['is_primary']
                ];
            }
            
            $formattedProcedure = [];
            foreach ($procedureData as $procedure) {
                $formattedProcedure[] = [
                    'procedure_type' => $procedure['procedure_type'],
                    'icd_code' => $procedure['icd_code'],
                    'icd_description' => $procedure['icd_description'],
                    'quantity' => $procedure['quantity'],
                    'is_primary' => $procedure['is_primary']
                ];
            }
            
            logWebServiceRequest("Get Latest Import Data for INACBG", [
                'nomor_sep' => $nomorSep,
                'import_log_id' => $importLog['id'],
                'diagnosis_count' => count($formattedDiagnosis),
                'procedure_count' => count($formattedProcedure)
            ]);
            
            return [
                'success' => true,
                'data' => [
                    'import_log' => $importLog,
                    'diagnosis' => $formattedDiagnosis,
                    'procedure' => $formattedProcedure
                ],
                'message' => 'Data import ditemukan'
            ];
            
        } catch (Exception $e) {
            logWebServiceError("Get Latest Import Data Error", [
                'nomor_sep' => $nomorSep
            ], $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Ambil detail prosedur import
     */
    public function getImportProcedure($importLogId) {
        try {
            $sql = "SELECT * FROM import_coding_procedure WHERE import_log_id = ? ORDER BY is_primary DESC, id ASC";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$importLogId]);
            
            $procedure = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $procedure;
        } catch (Exception $e) {
            logWebServiceError("Get Import Procedure Error", [
                'import_log_id' => $importLogId
            ], $e->getMessage());
            return [];
        }
    }
}
?>
