<?php

namespace App\Http\Controllers\Helper;

use App\Http\Controllers\Controller;
use DB, Input, Hash;


class MCryptController extends Controller {
	private $iv = 'fedcba9876543210'; #Same as in JAVA
    private $key = '0123456789abcdef'; #Same as in JAVA
    private $phpVersion = '';

    function __construct() {
    	$x = explode(".", phpversion());
    	$this->phpVersion = $x[0];
    }

    function encrypt($str) {
    	if($this->phpVersion == "5") {
	      	//$key = $this->hex2bin($key);    
	      	$iv = $this->iv;

	      	$td = mcrypt_module_open('rijndael-128', '', 'cbc', $iv);

	      	mcrypt_generic_init($td, $this->key, $iv);
	      	$encrypted = mcrypt_generic($td, $str);

	      	mcrypt_generic_deinit($td);
	      	mcrypt_module_close($td);
      	}
      	else { //should be 7
      		$encrypted = openssl_encrypt($str, 'aes-128-cbc', $this->key, OPENSSL_RAW_DATA, $this->iv);
      	}
  
		return composeReply("SUCCESS", "Data", array(
			"VERSION" => $this->phpVersion,
			"RESULT" => bin2hex($encrypted)
		));
    }

    function decrypt($code) {
    	if($this->phpVersion == "5") {
	      	//$key = $this->hex2bin($key);
	      	$code = $this->hex2bin($code);
	      	$iv = $this->iv;

	      	$td = mcrypt_module_open('rijndael-128', '', 'cbc', $iv);

	      	mcrypt_generic_init($td, $this->key, $iv);
	      	$decrypted = mdecrypt_generic($td, $code);

	      	mcrypt_generic_deinit($td);
	      	mcrypt_module_close($td);

	      	return composeReply("SUCCESS", "Data", array(
	      		"VERSION" => $this->phpVersion,
	      		"RESULT" => utf8_encode(trim($decrypted))
	      	));
		}
		else { //should be 7
			$code = $this->hex2bin($code);
			$decrypted = openssl_decrypt($code, 'aes-128-cbc', $this->key, OPENSSL_RAW_DATA |  OPENSSL_ZERO_PADDING, $this->iv);
  
			return composeReply("SUCCESS", "Data", array(
				"VERSION" => $this->phpVersion,
				"RESULT" => trim($decrypted)
			));
		}
    }

    private function hex2bin($hexdata) {
      	$bindata = '';

      	for ($i = 0; $i < strlen($hexdata); $i += 2) {
            $bindata .= chr(hexdec(substr($hexdata, $i, 2)));
      	}

      	return $bindata;
    }
}