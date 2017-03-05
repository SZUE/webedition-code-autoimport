<?php
/**
 * webEdition CMS
 *
 * $Rev$
 * $Author$
 * $Date$
 *
 * This source is part of webEdition CMS. webEdition CMS is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionCMS/License.txt
 *
 * @category   webEdition
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

/**
 * Implements JWT encoding and decoding as per http://tools.ietf.org/html/draft-ietf-oauth-json-web-token-06
 * Encoding algorithm based on http://code.google.com/p/google-api-php-client
 * Decoding algorithm based on https://github.com/luciferous/jwt
 * Src: https://github.com/F21/jwt/blob/master/JWT/JWT.php
 * @author Francis Chuang <francis.chuang@gmail.com>
 */
abstract class we_helpers_jwt{

	public static function encode($payload, $key, $algo = 'HS256'){
		$header = ['typ' => 'JWT', 'alg' => $algo];

		$segments = [self::urlsafeB64Encode(json_encode($header)),
			self::urlsafeB64Encode(json_encode($payload))
		];

		$signing_input = implode('.', $segments);

		$signature = self::sign($signing_input, $key, $algo);
		$segments[] = self::urlsafeB64Encode($signature);

		return implode('.', $segments);
	}

	public static function decode($jwt, $key = '', $algo = 'HS256'){
		$tks = explode('.', $jwt);

		if(count($tks) != 3){
			throw new Exception('Wrong number of segments');
		}

		list($headb64, $payloadb64, $cryptob64) = $tks;

		if(null === ($header = json_decode(self::urlsafeB64Decode($headb64)))){
			throw new Exception('Invalid segment encoding');
		}

		if(null === $payload = json_decode(self::urlsafeB64Decode($payloadb64))){
			throw new Exception('Invalid segment encoding');
		}

		$sig = self::urlsafeB64Decode($cryptob64);

		if($key){

			if(empty($header->alg)){
				throw new Exception('Empty algorithm');
			}

			if(!self::verifySignature($sig, "$headb64.$payloadb64", $key, $algo)){
				throw new UnexpectedValueException('Signature verification failed');
			}
		}

		return $payload;
	}

	private static function verifySignature($signature, $input, $key, $algo){
		switch($algo){
			case'HS256':
			case'HS384':
			case'HS512':
				return self::sign($input, $key, $algo) === $signature;

			case 'RS256':
				return (boolean) openssl_verify($input, $signature, $key, OPENSSL_ALGO_SHA256);

			case 'RS384':
				return (boolean) openssl_verify($input, $signature, $key, OPENSSL_ALGO_SHA384);

			case 'RS512':
				return (boolean) openssl_verify($input, $signature, $key, OPENSSL_ALGO_SHA512);

			default:
				throw new Exception("Unsupported or invalid signing algorithm.");
		}
	}

	private static function sign($input, $key, $algo){
		switch($algo){

			case 'HS256':
				return hash_hmac('sha256', $input, $key, true);

			case 'HS384':
				return hash_hmac('sha384', $input, $key, true);

			case 'HS512':
				return hash_hmac('sha512', $input, $key, true);

			case 'RS256':
				return self::generateRSASignature($input, $key, OPENSSL_ALGO_SHA256);

			case 'RS384':
				return self::generateRSASignature($input, $key, OPENSSL_ALGO_SHA384);

			case 'RS512':
				return self::generateRSASignature($input, $key, OPENSSL_ALGO_SHA512);

			default:
				throw new Exception("Unsupported or invalid signing algorithm.");
		}
	}

	private static function generateRSASignature($input, $key, $algo){
		$signature = '';
		if(!openssl_sign($input, $signature, $key, $algo)){
			throw new Exception("Unable to sign data.");
		}

		return $signature;
	}

	private static function urlSafeB64Encode($data){
		return str_replace(['+', '/', '\r', '\n', '='], ['-', '_'], base64_encode($data));
	}

	private static function urlSafeB64Decode($b64){
		return base64_decode(strtr($b64, ['-' => '+', '_' => '/']));
	}

}
