<?php
/**
 * Mapping Kode Tarif INA-CBG
 * Berdasarkan kelas rumah sakit dan kepemilikannya
 */

// Mapping kode tarif INA-CBG
define('KODE_TARIF_MAPPING', [
    'AP' => 'TARIF RS KELAS A PEMERINTAH',
    'AS' => 'TARIF RS KELAS A SWASTA',
    'BP' => 'TARIF RS KELAS B PEMERINTAH',
    'BS' => 'TARIF RS KELAS B SWASTA',
    'CP' => 'TARIF RS KELAS C PEMERINTAH',
    'CS' => 'TARIF RS KELAS C SWASTA',
    'DP' => 'TARIF RS KELAS D PEMERINTAH',
    'DS' => 'TARIF RS KELAS D SWASTA',
    'RSCM' => 'TARIF RSUPN CIPTO MANGUNKUSUMO',
    'RSJP' => 'TARIF RSJPD HARAPAN KITA',
    'RSD' => 'TARIF RS KANKER DHARMAIS',
    'RSAB' => 'TARIF RSAB HARAPAN KITA'
]);

/**
 * Get kode tarif description by code
 * @param string $code
 * @return string
 */
function getKodeTarifDescription($code) {
    return KODE_TARIF_MAPPING[$code] ?? 'TARIF RS KELAS D SWASTA';
}

/**
 * Get kode tarif code by description
 * @param string $description
 * @return string
 */
function getKodeTarifCode($description) {
    $codeMap = array_flip(KODE_TARIF_MAPPING);
    return $codeMap[$description] ?? 'DS';
}

/**
 * Get all kode tarif options for dropdown
 * @return array
 */
function getKodeTarifOptions() {
    $options = [];
    foreach (KODE_TARIF_MAPPING as $code => $description) {
        $options[] = [
            'value' => $code,
            'text' => $code . ' - ' . $description
        ];
    }
    return $options;
}

/**
 * Check if kode tarif is valid
 * @param string $code
 * @return bool
 */
function isValidKodeTarif($code) {
    return array_key_exists($code, KODE_TARIF_MAPPING);
}

/**
 * Get default kode tarif
 * @return string
 */
function getDefaultKodeTarif() {
    return 'DS'; // Default to TARIF RS KELAS D SWASTA
}

/**
 * Get kode tarif mapping array
 * @return array
 */
function getKodeTarifMapping() {
    return KODE_TARIF_MAPPING;
}

/**
 * Get kode tarif by kelas and kepemilikan
 * @param string $kelas (A, B, C, D)
 * @param string $kepemilikan (PEMERINTAH, SWASTA)
 * @return string
 */
function getKodeTarifByKelasKepemilikan($kelas, $kepemilikan) {
    $kelas = strtoupper($kelas);
    $kepemilikan = strtoupper($kepemilikan);
    
    if ($kepemilikan === 'PEMERINTAH') {
        $suffix = 'P';
    } elseif ($kepemilikan === 'SWASTA') {
        $suffix = 'S';
    } else {
        return getDefaultKodeTarif();
    }
    
    $code = $kelas . $suffix;
    
    return isValidKodeTarif($code) ? $code : getDefaultKodeTarif();
}

/**
 * Get kelas and kepemilikan from kode tarif
 * @param string $code
 * @return array
 */
function getKelasKepemilikanFromKodeTarif($code) {
    if (!isValidKodeTarif($code)) {
        return ['kelas' => 'D', 'kepemilikan' => 'SWASTA'];
    }
    
    $kelas = substr($code, 0, 1);
    $suffix = substr($code, 1, 1);
    
    $kepemilikan = ($suffix === 'P') ? 'PEMERINTAH' : 'SWASTA';
    
    return [
        'kelas' => $kelas,
        'kepemilikan' => $kepemilikan
    ];
}
?>
