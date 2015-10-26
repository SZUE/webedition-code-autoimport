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
abstract class we_captcha_captcha{

	/**
	 * display the image
	 *
	 * @return void
	 */
	static function display($image, $type = "gif"){

		$code = '';
		$im = $image->get($code);
		// save the code to the memory
		self::save($code);

		header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: no-cache, must-revalidate, max-age=0');
		header('Pragma: no-cache');

		switch($type){
			case 'jpg':
				header("Content-type: image/jpeg");
				imagejpeg($im);
				imagedestroy($im);
				return;
			case 'png':
				header("Content-type: image/png");
				imagepng($im);
				imagedestroy($im);
				return;
			case 'gif':
			default:
				header('Content-type: image/gif');
				imagegif($im);
				imagedestroy($im);
				return;
		}
	}

	static function check($captcha){
		static $valid = array();
		if(isset($valid[$captcha])){
			return true;
		}
		$db = new DB_WE();
		self::cleanup($db);
		$db->query('DELETE FROM ' . CAPTCHA_TABLE . ' WHERE IP=x\'' . bin2hex(inet_pton(strstr($_SERVER['REMOTE_ADDR'], ':') ? $_SERVER['REMOTE_ADDR'] : '::ffff:' . $_SERVER['REMOTE_ADDR'])) . '\' AND BINARY code="' . $db->escape($captcha) . '" AND agent="' . md5($_SERVER['HTTP_USER_AGENT']) . '"', '', $db);

		if($db->affected_rows()){
			$valid[$captcha] = true;
			return true;
		}
		return false;
	}

	static function cleanup(we_database_base $db){
		$db->query('DELETE FROM ' . CAPTCHA_TABLE . ' WHERE created<NOW()-INTERVAL 30 MINUTE');
	}

	/**
	 * Save the Captcha Code
	 *
	 * @param string $captcha
	 * @return void
	 */
	static function save($captcha){
		$db = new DB_WE();
		self::cleanup($db);
//FIMXE: make IP bin save
		$db->query('REPLACE INTO ' . CAPTCHA_TABLE . ' SET ' . we_database_base::arraySetter(array(
				'IP' => inet_pton(strstr($_SERVER['REMOTE_ADDR'], ':') ? $_SERVER['REMOTE_ADDR'] : '::ffff:' . $_SERVER['REMOTE_ADDR']),
				'agent' => md5($_SERVER['HTTP_USER_AGENT']),
				'code' => $captcha,
		)));
	}

}
