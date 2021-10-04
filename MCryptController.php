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

	function hariIndo($aDay) {
		$arrHari["MONDAY"] = "Senin";
		$arrHari["TUESDAY"] = "Selasa";
		$arrHari["WEDNESDAY"] = "Rabu";
		$arrHari["THURSDAY"] = "Kamis";
		$arrHari["FRIDAY"] = "Jumat";
		$arrHari["SATURDAY"] = "Sabtu";
		$arrHari["SUNDAY"] = "Minggu";
	
		return (isset($arrHari[$aDay]) ? $arrHari[$aDay] : "");
	}
	
	function tglIndo($tgl,$mode) {
		if($tgl != "" && $mode != "" && $tgl!= "0000-00-00" && $tgl != "0000-00-00 00:00:00") {
			$t = explode("-",$tgl);
			$bln = array();
			$bln["01"]["LONG"] = "Januari";
			$bln["01"]["SHORT"] = "Jan";
			$bln["1"]["LONG"] = "Januari";
			$bln["1"]["SHORT"] = "Jan";
			$bln["02"]["LONG"] = "Februari";
			$bln["02"]["SHORT"] = "Feb";
			$bln["2"]["LONG"] = "Februari";
			$bln["2"]["SHORT"] = "Feb";
			$bln["03"]["LONG"] = "Maret";
			$bln["03"]["SHORT"] = "Mar";
			$bln["3"]["LONG"] = "Maret";
			$bln["3"]["SHORT"] = "Mar";
			$bln["04"]["LONG"] = "April";
			$bln["04"]["SHORT"] = "Apr";
			$bln["4"]["LONG"] = "April";
			$bln["4"]["SHORT"] = "Apr";
			$bln["05"]["LONG"] = "Mei";
			$bln["05"]["SHORT"] = "Mei";
			$bln["5"]["LONG"] = "Mei";
			$bln["5"]["SHORT"] = "Mei";
			$bln["06"]["LONG"] = "Juni";
			$bln["06"]["SHORT"] = "Jun";
			$bln["6"]["LONG"] = "Juni";
			$bln["6"]["SHORT"] = "Jun";
			$bln["07"]["LONG"] = "Juli";
			$bln["07"]["SHORT"] = "Jul";
			$bln["7"]["LONG"] = "Juli";
			$bln["7"]["SHORT"] = "Jul";
			$bln["08"]["LONG"] = "Agustus";
			$bln["08"]["SHORT"] = "Ags";
			$bln["8"]["LONG"] = "Agustus";
			$bln["8"]["SHORT"] = "Ags";
			$bln["09"]["LONG"] = "September";
			$bln["09"]["SHORT"] = "Sep";
			$bln["9"]["LONG"] = "September";
			$bln["9"]["SHORT"] = "Sep";
			$bln["10"]["LONG"] = "Oktober";
			$bln["10"]["SHORT"] = "Okt";
			$bln["11"]["LONG"] = "November";
			$bln["11"]["SHORT"] = "Nov";
			$bln["12"]["LONG"] = "Desember";
			$bln["12"]["SHORT"] = "Des";
	
			$b = $t[1];
	
			if (strpos($t[2], ":") === false) { //tdk ada format waktu
				$jam = "";
			}
			else {
				$j = explode(" ",$t[2]);
				$t[2] = $j[0];
				$jam = $j[1];
			}
	
			return $t[2]." ".$bln[$b][$mode]." ".$t[0]." ".$jam;
		}
		else {
			return "-";
		}
	}
	
}