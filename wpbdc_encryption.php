<?php
function wpbdc_encryption_list()
{
	if(!function_exists('mcrypt_encrypt') || !function_exists('mcrypt_module_open') || !function_exists('mcrypt_enc_get_iv_size'))
	{
		return false;
	}
	return true;
}

function wpbdc_max_password() { 
   $td = mcrypt_module_open('rijndael-256', '', 'ofb', '');

   /* Create the IV and determine the keysize length, used MCRYPT_RAND
     * on Windows instead */
   //$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
   return mcrypt_enc_get_key_size($td);

}
function wpbdc_min_iv()
{
	$td = mcrypt_module_open('rijndael-256', '', 'ofb', '');
	$ivs=mcrypt_enc_get_iv_size($td);
	mcrypt_module_close($td);
	return $ivs;
}

function wpbdc_encryption_ok()
{
	if(strlen(get_option('wpbdc_encryption_password'))>wpbdc_max_password()) { return false; }
	if(strlen(get_option('wpbdc_encryption_iv'))!=wpbdc_min_iv()) { return false; }
	return true;
}

function wpbdc_encrypt($pass,$iv,$text)
{
   /* Open the cipher */
   $td = mcrypt_module_open('rijndael-256', '', 'ofb', '');

   /* Create the IV and determine the keysize length, used MCRYPT_RAND
     * on Windows instead */
   //$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
   $ks = mcrypt_enc_get_key_size($td);
   
   /* Create key */
   $key = substr(md5($pass), 0, $ks);
   
  /* Initialize encryption module for decryption */
   mcrypt_generic_init($td, $key, $iv);
   
   /* Decrypt encrypted string */
   $decrypted = mdecrypt_generic($td, $text);

   /* Terminate decryption handle and close module */
   mcrypt_generic_deinit($td);
   mcrypt_module_close($td);
   
   return $decrypted;
}

function wpbdc_decrypt($pass,$iv,$text)
{
   /* Open the cipher */
   $td = mcrypt_module_open('rijndael-256', '', 'ofb', '');

   /* Create the IV and determine the keysize length, used MCRYPT_RAND
     * on Windows instead */
   //$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
   $ks = mcrypt_enc_get_key_size($td);

   /* Create key */
   $key = substr(md5($pass), 0, $ks);

   /* Intialize encryption */
   mcrypt_generic_init($td, $key, $iv);

   /* Encrypt data */
   $decrypted=mcrypt_generic($td, $text);
   
   /* Terminate decryption handle and close module */
   mcrypt_generic_deinit($td);
   mcrypt_module_close($td);
      
   return $decrypted;
}
?>