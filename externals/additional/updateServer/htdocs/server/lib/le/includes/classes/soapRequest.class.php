<?php
/*
 * deactivate php wsdl-caching for development:
 */
//@ini_set("display_errors","Off");
@ini_set("soap.wsdl_cache",0);
@ini_set("soap.wsdl_cache_enabled",0);
@ini_set("soap.wsdl_cache_ttl","0");

/*
 * usage example:
 * $soapresult = soapRequest::GetData("remoteFunction",array("data"=> $data));
 * 
 */
class soapRequest {
	
	private static $soapUser = "SAdfdfbhdfDFUdf7df";
	private static $soapPass = "SHfnbfhdfz365342";
	private static $ConnectionString = "http://213.144.11.105/updateserver/server.php";
	private static $CryptVector = "bAQjLY1ga9pV03l50kVo1mNgNJnlsPIG";
	private static $CryptKey = "SLeiLRcQzApvKbeypfKrBabgURixB6N7";

	public function GetData($Method,$Paramter = array()) {
		
		crypt::$_iv = self::$CryptVector;
		crypt::$_key = self::$CryptKey;

		$User = base64_encode(crypt::encrypt(self::$soapUser));
		$Password = base64_encode(crypt::encrypt(self::$soapPass));
		$Paramter = base64_encode(crypt::encrypt(serialize($Paramter)));
		$client = new SoapClient(NULL, array("location" => self::$ConnectionString, "uri" => "urn:xmethodsTestServer","style" => SOAP_RPC, "use" => SOAP_ENCODED ));

        $parameters = array(
	        new SoapParam($_SERVER['REMOTE_ADDR'], 'Requester'),
	        new SoapParam($User, 'Username'),
	        new SoapParam($Password, 'Password'),
	        new SoapParam($Paramter, 'Paramters')
	    );
	    
        if(!$result = $client->__call(  
        $Method, 
        $parameters,
        array(
	        "uri" => "urn:xmethodsTestServer",             
	        "soapaction" => "urn:xmethodsTestServer#addiere"     //irgendein Platzhalter
        ))) {
        	//throw new Exception('Server not available');
        	return false;
        }

        if($result) {
        	return unserialize(crypt::decrypt(base64_decode($result)));
        } else {
			return false;
        }
	}
}

class crypt {
	/**
	 * specifies which encryption algorithm has to be used for encryption / decryption
	 * 
	 * @uses mcrypt php extension
	 */

	const CRYPT_METHOD = "crypt";
	
	/**
	 * specifies which hash algorithm has to be used for creating checksums
	 *
	 */
	const HASH_METHOD = "sha1"; // can be "md5", "crc32" or "sha1", 

	/**
	 * mcrypt initialization vector
	 *
	 * @var string
	 */
	public static $_iv = 'bAQjLY1ga9pV03l50kVo1mNgNJnlsPIG';
	
	/**
	 * mcrypt mcrypt key
	 *
	 * @var string
	 */
	public static $_key = 'SLeiLRcQzApvKbeypfKrBabgURixB6N7';
	
	public static function encrypt($input = "") {
	    /* Open the cipher */
	    $td = mcrypt_module_open('rijndael-256', '', 'ofb', '');
	    $ks = mcrypt_enc_get_key_size($td);
		/* Intialize encryption */
	    mcrypt_generic_init($td, self::$_key, self::$_iv);
	    /* Encrypt data */
	    $encrypted = @mcrypt_generic($td, $input);
	    /* Terminate encryption handler */
	    mcrypt_generic_deinit($td);
		/* return string */
	    return $encrypted;
	}
	
	public static function decrypt($string = "") {
	    /* Open the cipher */
	    $td = mcrypt_module_open('rijndael-256', '', 'ofb', '');
	    mcrypt_generic_init($td, self::$_key, self::$_iv);
		/* Decrypt encrypted string */
	    $decrypted = mdecrypt_generic($td, $string);
		/* Terminate decryption handle and close module */
	    mcrypt_generic_deinit($td);
	    mcrypt_module_close($td);
		/* return string */
	    return trim($decrypted);
	}

	/**
	 * creates a hash / checksum from specified string according to crypt::HASH_METHOD
	 *
	 * @param string $string
	 * @return hash value
	 */
	public static function hash($string = "", $algo="") {
		if(empty($string)) {
			return false;
		}
		if(empty($algo)) {
			$algo = 'sha512';
		}
		switch(HASH_METHOD) {
			case "md5":
				return md5($string);
			case "sha1":
				return sha1($string);
			case "crc32":
				return crc32($string);
			case "hash":
				return hash($algo,$string);
			case "hash_hmac":
				return hash_hmac($algo,$string,self::$_key);
			default:
				return $string;
		}
	}
}

?>
