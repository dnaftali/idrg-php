<?php
/**
 * Mapping Cara Masuk untuk E-Klaim Web Service
 * File helper untuk konversi cara masuk sesuai standar E-Klaim
 */

// Mapping cara masuk E-Klaim
define('CARA_MASUK_MAPPING', [
    'gp' => [
        'code' => 'gp',
        'description' => 'Rujukan FKTP',
        'display_name' => 'Rujukan FKTP'
    ],
    'hosp-trans' => [
        'code' => 'hosp-trans',
        'description' => 'Rujukan FKRTL',
        'display_name' => 'Rujukan FKRTL'
    ],
    'mp' => [
        'code' => 'mp',
        'description' => 'Rujukan Spesialis',
        'display_name' => 'Rujukan Spesialis'
    ],
    'outp' => [
        'code' => 'outp',
        'description' => 'Dari Rawat Jalan',
        'display_name' => 'Dari Rawat Jalan'
    ],
    'inp' => [
        'code' => 'inp',
        'description' => 'Dari Rawat Inap',
        'display_name' => 'Dari Rawat Inap'
    ],
    'emd' => [
        'code' => 'emd',
        'description' => 'Dari Rawat Darurat',
        'display_name' => 'Dari Rawat Darurat'
    ],
    'born' => [
        'code' => 'born',
        'description' => 'Lahir di RS',
        'display_name' => 'Lahir di RS'
    ],
    'nursing' => [
        'code' => 'nursing',
        'description' => 'Rujukan Panti Jompo',
        'display_name' => 'Rujukan Panti Jompo'
    ],
    'psych' => [
        'code' => 'psych',
        'description' => 'Rujukan dari RS Jiwa',
        'display_name' => 'Rujukan dari RS Jiwa'
    ],
    'rehab' => [
        'code' => 'rehab',
        'description' => 'Rujukan Fasilitas Rehab',
        'display_name' => 'Rujukan Fasilitas Rehab'
    ],
    'other' => [
        'code' => 'other',
        'description' => 'Lain-lain',
        'display_name' => 'Lain-lain'
    ]
]);

/**
 * Fungsi untuk mendapatkan kode cara masuk dari display name
 * @param string $displayName Nama tampilan cara masuk
 * @return string Kode cara masuk
 */
function getCaraMasukCode($displayName) {
    foreach (CARA_MASUK_MAPPING as $code => $mapping) {
        if ($mapping['display_name'] === $displayName) {
            return $code;
        }
    }
    return 'gp'; // Default ke Rujukan FKTP
}

/**
 * Fungsi untuk mendapatkan display name dari kode cara masuk
 * @param string $code Kode cara masuk
 * @return string Nama tampilan cara masuk
 */
function getCaraMasukDisplayName($code) {
    return CARA_MASUK_MAPPING[$code]['display_name'] ?? 'Rujukan FKTP';
}

/**
 * Fungsi untuk mendapatkan deskripsi cara masuk dari kode
 * @param string $code Kode cara masuk
 * @return string Deskripsi cara masuk
 */
function getCaraMasukDescription($code) {
    return CARA_MASUK_MAPPING[$code]['description'] ?? 'Rujukan FKTP';
}

/**
 * Fungsi untuk mendapatkan semua opsi cara masuk untuk dropdown
 * @return array Array opsi cara masuk
 */
function getCaraMasukOptions() {
    $options = [];
    foreach (CARA_MASUK_MAPPING as $code => $mapping) {
        $options[] = [
            'value' => $code,
            'label' => $mapping['display_name'],
            'description' => $mapping['description']
        ];
    }
    return $options;
}

/**
 * Fungsi untuk validasi kode cara masuk
 * @param string $code Kode cara masuk
 * @return bool True jika valid, false jika tidak
 */
function isValidCaraMasukCode($code) {
    return array_key_exists($code, CARA_MASUK_MAPPING);
}

/**
 * Fungsi untuk mendapatkan mapping lengkap cara masuk
 * @return array Mapping lengkap cara masuk
 */
function getCaraMasukMapping() {
    return CARA_MASUK_MAPPING;
}
