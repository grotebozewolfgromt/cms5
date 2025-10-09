<?php
/**
 * In this library security related functions like encryption
 *
 * IMPORTANT:
 * This library is language independant, so don't use language specific elements
 *
 * 30 juni 2021: created
 * 
 * @author Dennis Renirie
 */


//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_date.php');
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_img.php'); 
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_inet.php');
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_math.php');
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_misc.php');
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_string.php');

// Save The Keys In Your Configuration File

/**
 * 2 way encrypt data.
 * 
 * NEVER USE THESE FUNCTIONS TO ENCRYPT/DECRYPT PASSWORDS, BECAUSE IT'S UNSAFE !!!!!
 *
 * @param string $sUncryptedData data to encrypt
 * @param string $sPassphrase the passphrase or encryption key
 * @param string $sCipherMethod the cypher method
 * @param string $sDigestHashAlgorithm the digest/hash algo
 * @return string
 */
function encrypt($sUncryptedData, $sPassphrase, $sCipherMethod = ENCRYPTION_CYPHERMETHOD_DEFAULT, $sDigestHashAlgorithm = ENCRYPTION_DIGESTALGORITHM_DEFAULT)
{       
    if (($sUncryptedData === '') || ($sUncryptedData === null))
        return '';

    $enc_key = openssl_digest($sPassphrase, $sDigestHashAlgorithm, TRUE);
    $sEnc_iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($sCipherMethod));
    return openssl_encrypt($sUncryptedData, $sCipherMethod, $enc_key, 0, $sEnc_iv) . "::" . bin2hex($sEnc_iv);
}

/**
 * 2 way decrypt data.
 * 
 * NEVER USE THESE FUNCTIONS TO ENCRYPT/DECRYPT PASSWORDS, BECAUSE IT'S UNSAFE !!!!!
 * 
 * @param string $sEncryptedData
 * @param string $sPassphrase the passphrase
 * @param string $sCipherMethod the cypher method
 * @param string $sDigestHashAlgorithm the digest/hash algo
 * @return string
 */
function decrypt($sEncryptedData, $sPassphrase, $sCipherMethod = ENCRYPTION_CYPHERMETHOD_DEFAULT, $sDigestHashAlgorithm = ENCRYPTION_DIGESTALGORITHM_DEFAULT)
{
    $arrInput = array();

    if (($sEncryptedData === '') || ($sEncryptedData === null))
        return '';

    $arrInput = explode("::", $sEncryptedData);
    if (count($arrInput) == 2)
    {
        $enc_key = openssl_digest($sPassphrase, $sDigestHashAlgorithm, TRUE);
        list($crypted_token, $enc_iv) = $arrInput;
        return openssl_decrypt($crypted_token, $sCipherMethod, $enc_key, 0, hex2bin($enc_iv));
    }
    else
    {
        return '';
    }
}


?>



