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
 * class forerror_reporting, uses the javascript function showmessage in
 * webEdition.php
 *
 */
abstract class we_message_reporting{
// constants for messaging!
// these are binray checked like permissions in unix, DON'T change indexes

	const WE_MESSAGE_NOTICE = 1;
	const WE_MESSAGE_WARNING = 2;
	const WE_MESSAGE_ERROR = 4;
	const WE_MESSAGE_INFO = 8;
	const WE_MESSAGE_FRONTEND = 16;

	/**
	 * returns js-call for the showMessage function
	 *
	 * @param string $message
	 * @param integer $priority
	 * @param boolean $isJsMsg
	 * @return string
	 */
	public static function getShowMessageCall($message, $priority, $isJsMsg = false, $isOpener = false){
		$message = $isJsMsg ? $message : '"' . self::prepareMsgForJS($message) . '"';
		switch($priority){
			case self::WE_MESSAGE_INFO:
			case self::WE_MESSAGE_FRONTEND:
				return 'alert(' . $message . ');';
			default:
				return ($isOpener ? 'top.opener.' : '') . 'WE().util.showMessage(' . $message . ', ' . $priority . ', window);';
		}
	}

	private static function prepareMsgForJS($message){
		return str_replace(["\n",
			'\n',
			'\\',
			'"',
			'###NL###'
			], ['###NL###',
			'###NL###',
			'\\\\',
			'\\"',
			'\n'
			], $message
		);
	}

	public static function jsString(){
		return '
var message_reporting=JSON.parse("' . setLangString([
				'notice' => g_l('alert', '[notice]'),
				'warning' => g_l('alert', '[warning]'),
				'error' => g_l('alert', '[error]'),
			]) . '");';
	}

}
