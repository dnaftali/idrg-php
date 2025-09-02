<?php
/**
 * Encryption Functions untuk Web Service E-Klaim INA-CBG
 * Berdasarkan Manual Web Service 5.9
 */

/**
 * Ambil encryption key dari database
 */
function getEncryptionKey($keyName = 'simrs_default') {
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT key_value FROM encryption_keys WHERE key_name = ? AND is_active = 1");
    $stmt->execute([$keyName]);
    $result = $stmt->fetch();
    return $result ? $result['key_value'] : null;
}

/**
 * Enkripsi data untuk web service
 */
function encryptForWebService($data, $keyName = 'simrs_default') {
    $key = getEncryptionKey($keyName);
    if (!$key) {
        throw new Exception("Encryption key tidak ditemukan");
    }
    
    // Gunakan fungsi enkripsi dari manual
    return inacbg_encrypt(json_encode($data), $key);
}

/**
 * Dekripsi response dari web service
 */
function decryptWebServiceResponse($encryptedData, $keyName = 'simrs_default') {
    $key = getEncryptionKey($keyName);
    if (!$key) {
        throw new Exception("Encryption key tidak ditemukan");
    }
    
    // Hapus header dan footer encrypted data
    $data = preg_replace('/----BEGIN ENCRYPTED DATA----\r?\n/', '', $encryptedData);
    $data = preg_replace('/----END ENCRYPTED DATA----\r?\n/', '', $data);
    
    // Gunakan fungsi dekripsi dari manual
    $decrypted = inacbg_decrypt($data, $key);
    return json_decode($decrypted, true);
}

/**
 * Dekripsi request dari web service
 */
function decryptWebServiceRequest($encryptedData, $keyName = 'simrs_default') {
    $key = getEncryptionKey($keyName);
    if (!$key) {
        throw new Exception("Encryption key tidak ditemukan");
    }
    
    // Hapus header dan footer encrypted data
    $data = preg_replace('/----BEGIN ENCRYPTED DATA----\r?\n/', '', $encryptedData);
    $data = preg_replace('/----END ENCRYPTED DATA----\r?\n/', '', $data);
    
    // Gunakan fungsi dekripsi dari manual
    $decrypted = inacbg_decrypt($data, $key);
    return json_decode($decrypted, true);
}

/**
 * Enkripsi response untuk web service
 */
function encryptWebServiceResponse($data, $keyName = 'simrs_default') {
    $key = getEncryptionKey($keyName);
    if (!$key) {
        throw new Exception("Encryption key tidak ditemukan");
    }
    
    // Gunakan fungsi enkripsi dari manual
    return inacbg_encrypt(json_encode($data), $key);
}

/**
 * Encryption Function berdasarkan manual
 */
function inacbg_encrypt($data, $key) {
    // make binary representation of $key
    $key = hex2bin($key);
    
    // check key length, must be 256 bit or 32 bytes
    if (mb_strlen($key, "8bit") !== 32) {
        throw new Exception("Needs a 256-bit key!");
    }
    
    // create initialization vector
    $iv_size = openssl_cipher_iv_length("aes-256-cbc");
    $iv = openssl_random_pseudo_bytes($iv_size);
    
    // encrypt
    $encrypted = openssl_encrypt($data, "aes-256-cbc", $key, OPENSSL_RAW_DATA, $iv);
    
    // create signature, against padding oracle attacks
    $signature = mb_substr(hash_hmac("sha256", $encrypted, $key, true), 0, 10, "8bit");
    
    // combine all, encode, and format
    $encoded = chunk_split(base64_encode($signature . $iv . $encrypted));
    
    return $encoded;
}

/**
 * Decryption Function berdasarkan manual
 */
function inacbg_decrypt($str, $strkey) {
    // make binary representation of $key
    $key = hex2bin($strkey);
    
    // check key length, must be 256 bit or 32 bytes
    if (mb_strlen($key, "8bit") !== 32) {
        throw new Exception("Needs a 256-bit key!");
    }
    
    // calculate iv size
    $iv_size = openssl_cipher_iv_length("aes-256-cbc");
    
    // breakdown parts
    $decoded = base64_decode($str);
    $signature = mb_substr($decoded, 0, 10, "8bit");
    $iv = mb_substr($decoded, 10, $iv_size, "8bit");
    $encrypted = mb_substr($decoded, $iv_size + 10, NULL, "8bit");
    
    // check signature, against padding oracle attack
    $calc_signature = mb_substr(hash_hmac("sha256", $encrypted, $key, true), 0, 10, "8bit");
    if (!inacbg_compare($signature, $calc_signature)) {
        return "SIGNATURE_NOT_MATCH"; // signature doesn't match
    }
    
    $decrypted = openssl_decrypt($encrypted, "aes-256-cbc", $key, OPENSSL_RAW_DATA, $iv);
    
    return $decrypted;
}

/**
 * Compare Function berdasarkan manual
 */
function inacbg_compare($a, $b) {
    // compare individually to prevent timing attacks
    
    // compare length
    if (strlen($a) !== strlen($b)) return false;
    
    // compare individual
    $result = 0;
    for ($i = 0; $i < strlen($a); $i++) {
        $result |= ord($a[$i]) ^ ord($b[$i]);
    }
    
    return $result == 0;
}

/**
 * Generate encryption key 256-bit
 */
function generateEncryptionKey() {
    return bin2hex(random_bytes(32));
}

/**
 * Test encryption/decryption
 */
function testEncryption($testData = "Test data untuk enkripsi") {
    try {
        $key = generateEncryptionKey();
        $encrypted = inacbg_encrypt($testData, $key);
        $decrypted = inacbg_decrypt($encrypted, $key);
        
        return [
            'success' => $decrypted === $testData,
            'key' => $key,
            'original' => $testData,
            'encrypted' => $encrypted,
            'decrypted' => $decrypted
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}
?>
