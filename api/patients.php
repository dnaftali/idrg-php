<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Include database connection
require_once '../config/database.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if specific patient ID is requested
    $patientId = isset($_GET['id']) ? $_GET['id'] : null;
    
    if ($patientId) {
        // Get specific patient by ID with detail_tarif data
        $query = "SELECT 
                        kp.id,
                        kp.nomor_kartu,
                        kp.nomor_sep,
                        kp.nomor_rm,
                        kp.nama_pasien,
                        kp.tgl_lahir,
                        kp.gender,
                        kp.tgl_masuk,
                        kp.tgl_pulang,
                        kp.jenis_rawat,
                        kp.kelas_rawat,
                        kp.discharge_status,
                        kp.jaminan_cara_bayar,
                        kp.payor_id,
                        kp.payor_cd,
                        kp.diagnosa,
                        kp.procedures,
                        kp.los_hari,
                        kp.adl_sub_acute,
                        kp.adl_chronic,
                        kp.icu_los,
                        kp.ventilator_hour,
                        kp.nama_dokter,
                        kp.kode_tarif,
                        kp.coder_nik,
                        kp.klaim_status,
                        kp.bpjs_klaim_status_cd,
                        kp.bpjs_klaim_status_nm,
                        kp.kemenkes_dc_status_cd,
                        kp.bpjs_dc_status_cd,
                        kp.cara_masuk,
                        kp.kode_tarif,
                        kp.covid19_status_cd,
                        kp.created_at,
                        -- Detail tarif fields
                        dt.prosedur_non_bedah,
                        dt.prosedur_bedah,
                        dt.konsultasi,
                        dt.tenaga_ahli,
                        dt.keperawatan,
                        dt.penunjang,
                        dt.radiologi,
                        dt.laboratorium,
                        dt.pelayanan_darah,
                        dt.rehabilitasi,
                        dt.kamar,
                        dt.rawat_intensif,
                        dt.obat,
                        dt.obat_kronis,
                        dt.obat_kemoterapi,
                        dt.alkes,
                        dt.bmhp,
                        dt.sewa_alat,
                        dt.total_tarif,
                        dt.kategori_tarif,
                        dt.nama_layanan
                    FROM kunjungan_pasien kp
                    LEFT JOIN detail_tarif dt ON kp.id = dt.kunjungan_id
                    WHERE kp.id = :id";
        
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $patientId, PDO::PARAM_INT);
        $stmt->execute();
        $patient = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($patient) {
            // Format the data
            $formattedPatient = [
                'id' => (int)$patient['id'],
                'nomor_kartu' => $patient['nomor_kartu'] ?: null,
                'nomor_sep' => $patient['nomor_sep'] ?: null,
                'nomor_rm' => $patient['nomor_rm'] ?: null,
                'nama_pasien' => $patient['nama_pasien'] ?: 'Nama Tidak Diketahui',
                'tgl_lahir' => $patient['tgl_lahir'] ?: null,
                'gender' => $patient['gender'] ?: null,
                'tgl_masuk' => $patient['tgl_masuk'] ?: null,
                'tgl_pulang' => $patient['tgl_pulang'] ?: null,
                'jenis_rawat' => $patient['jenis_rawat'] ?: '2',
                'kelas_rawat' => $patient['kelas_rawat'] ?: '3',
                'discharge_status' => $patient['discharge_status'] ?: '1',
                'jaminan_cara_bayar' => $patient['jaminan_cara_bayar'] ?: 'JKN',
                'payor_id' => $patient['payor_id'] ?: null,
                'payor_cd' => $patient['payor_cd'] ?: null,
                'diagnosa' => $patient['diagnosa'] ?: null,
                'procedures' => $patient['procedures'] ?: null,
                'los_hari' => (int)($patient['los_hari'] ?: 0),
                'adl_sub_acute' => (int)($patient['adl_sub_acute'] ?: 0),
                'adl_chronic' => (int)($patient['adl_chronic'] ?: 0),
                'icu_los' => (int)($patient['icu_los'] ?: 0),
                'ventilator_hour' => (int)($patient['ventilator_hour'] ?: 0),
                'nama_dokter' => $patient['nama_dokter'] ?: 'DPJP Tidak Diketahui',
                'kode_tarif' => $patient['kode_tarif'] ?: null,
                'coder_nik' => $patient['coder_nik'] ?: null,
                'klaim_status' => $patient['klaim_status'] ?: 'draft',
                'bpjs_klaim_status_cd' => $patient['bpjs_klaim_status_cd'] ?: null,
                'bpjs_klaim_status_nm' => $patient['bpjs_klaim_status_nm'] ?: null,
                'kemenkes_dc_status_cd' => $patient['kemenkes_dc_status_cd'] ?: null,
                'bpjs_dc_status_cd' => $patient['bpjs_dc_status_cd'] ?: null,
                'cara_masuk' => $patient['cara_masuk'] ?: 'gp',
                'kode_tarif' => $patient['kode_tarif'] ?: 'CP',
                'covid19_status_cd' => $patient['covid19_status_cd'] ?: '0',
                'created_at' => $patient['created_at'] ?: null,
                // Detail tarif data
                'detail_tarif' => [
                    'prosedur_non_bedah' => (float)($patient['prosedur_non_bedah'] ?: 0),
                    'prosedur_bedah' => (float)($patient['prosedur_bedah'] ?: 0),
                    'konsultasi' => (float)($patient['konsultasi'] ?: 0),
                    'tenaga_ahli' => (float)($patient['tenaga_ahli'] ?: 0),
                    'keperawatan' => (float)($patient['keperawatan'] ?: 0),
                    'penunjang' => (float)($patient['penunjang'] ?: 0),
                    'radiologi' => (float)($patient['radiologi'] ?: 0),
                    'laboratorium' => (float)($patient['laboratorium'] ?: 0),
                    'pelayanan_darah' => (float)($patient['pelayanan_darah'] ?: 0),
                    'rehabilitasi' => (float)($patient['rehabilitasi'] ?: 0),
                    'kamar' => (float)($patient['kamar'] ?: 0),
                    'rawat_intensif' => (float)($patient['rawat_intensif'] ?: 0),
                    'obat' => (float)($patient['obat'] ?: 0),
                    'obat_kronis' => (float)($patient['obat_kronis'] ?: 0),
                    'obat_kemoterapi' => (float)($patient['obat_kemoterapi'] ?: 0),
                    'alkes' => (float)($patient['alkes'] ?: 0),
                    'bmhp' => (float)($patient['bmhp'] ?: 0),
                    'sewa_alat' => (float)($patient['sewa_alat'] ?: 0),
                    'total_tarif' => (float)($patient['total_tarif'] ?: 0),
                    'kategori_tarif' => $patient['kategori_tarif'] ?: null,
                    'nama_layanan' => $patient['nama_layanan'] ?: null
                ]
            ];
            
            echo json_encode([
                'success' => true,
                'data' => $formattedPatient
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'Patient not found'
            ]);
        }
    } else {
        // Get all patients from kunjungan_pasien table with new structure
        $query = "SELECT 
                        kp.id,
                        kp.nomor_kartu,
                        kp.nomor_sep,
                        kp.nomor_rm,
                        kp.nama_pasien,
                        kp.tgl_lahir,
                        kp.gender,
                        kp.tgl_masuk,
                        kp.tgl_pulang,
                        kp.jenis_rawat,
                        kp.kelas_rawat,
                        kp.discharge_status,
                        kp.jaminan_cara_bayar,
                        kp.payor_id,
                        kp.payor_cd,
                        kp.diagnosa,
                        kp.procedures,
                        kp.los_hari,
                        kp.adl_sub_acute,
                        kp.adl_chronic,
                        kp.icu_los,
                        kp.ventilator_hour,
                        kp.nama_dokter,
                        kp.kode_tarif,
                        kp.coder_nik,
                        kp.klaim_status,
                        kp.bpjs_klaim_status_cd,
                        kp.bpjs_klaim_status_nm,
                        kp.kemenkes_dc_status_cd,
                        kp.bpjs_dc_status_cd,
                        kp.kode_tarif,
                        kp.covid19_status_cd,
                        kp.created_at
                    FROM kunjungan_pasien kp
                    ORDER BY kp.tgl_masuk DESC";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format the data to match the expected structure
        $formattedPatients = [];
        foreach ($patients as $patient) {
            $formattedPatients[] = [
                'id' => (int)$patient['id'],
                'nomor_kartu' => $patient['nomor_kartu'] ?: null,
                'nomor_sep' => $patient['nomor_sep'] ?: null,
                'nomor_rm' => $patient['nomor_rm'] ?: null,
                'nama_pasien' => $patient['nama_pasien'] ?: 'Nama Tidak Diketahui',
                'tgl_lahir' => $patient['tgl_lahir'] ?: null,
                'gender' => $patient['gender'] ?: null,
                'tgl_masuk' => $patient['tgl_masuk'] ?: null,
                'tgl_pulang' => $patient['tgl_pulang'] ?: null,
                'jenis_rawat' => $patient['jenis_rawat'] ?: null, // Jangan set default, biarkan null jika kosong
                'kelas_rawat' => $patient['kelas_rawat'] ?: '3', // Default to kelas 3
                'discharge_status' => $patient['discharge_status'] ?: '1', // Default to atas persetujuan dokter
                'jaminan_cara_bayar' => $patient['jaminan_cara_bayar'] ?: 'JKN',
                'payor_id' => $patient['payor_id'] ?: null,
                'payor_cd' => $patient['payor_cd'] ?: null,
                'diagnosa' => $patient['diagnosa'] ?: null,
                'procedures' => $patient['procedures'] ?: null,
                'los_hari' => (int)($patient['los_hari'] ?: 0),
                'adl_sub_acute' => (int)($patient['adl_sub_acute'] ?: 0),
                'adl_chronic' => (int)($patient['adl_chronic'] ?: 0),
                'icu_los' => (int)($patient['icu_los'] ?: 0),
                'ventilator_hour' => (int)($patient['ventilator_hour'] ?: 0),
                'nama_dokter' => $patient['nama_dokter'] ?: 'DPJP Tidak Diketahui',
                'kode_tarif' => $patient['kode_tarif'] ?: null,
                'coder_nik' => $patient['coder_nik'] ?: null,
                'klaim_status' => $patient['klaim_status'] ?: 'draft',
                'bpjs_klaim_status_cd' => $patient['bpjs_klaim_status_cd'] ?: null,
                'bpjs_klaim_status_nm' => $patient['bpjs_klaim_status_nm'] ?: null,
                'kemenkes_dc_status_cd' => $patient['kemenkes_dc_status_cd'] ?: null,
                'bpjs_dc_status_cd' => $patient['bpjs_dc_status_cd'] ?: null,
                'kode_tarif' => $patient['kode_tarif'] ?: 'CP',
                'covid19_status_cd' => $patient['covid19_status_cd'] ?: '0',
                'created_at' => $patient['created_at'] ?: null
            ];
        }
        
        echo json_encode([
            'success' => true,
            'data' => $formattedPatients,
            'total' => count($formattedPatients)
        ]);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Server error: ' . $e->getMessage()
    ]);
}
?>
