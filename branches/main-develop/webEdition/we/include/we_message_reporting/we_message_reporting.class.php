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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

/**
 * class forerror_reporting, uses the javascript function showmessage in
 * webEdition.php
 *
 */
class we_message_reporting{
// contansts for messaging!
// these are binray checked like permissions in unix, DON'T change indexes

	const WE_MESSAGE_INFO = -1;
	const WE_MESSAGE_FRONTEND = -2;
	const WE_MESSAGE_NOTICE = 1;
	const WE_MESSAGE_WARNING = 2;
	const WE_MESSAGE_ERROR = 4;

	/**
	 * returns js-call for the showMessage function
	 *
	 * @param string $message
	 * @param integer $priority
	 * @param boolean $isJsMsg
	 * @return string
	 */
	static function getShowMessageCall($message, $priority, $isJsMsg = false, $isOpener = false){

		if($priority == self::WE_MESSAGE_INFO || $priority == self::WE_MESSAGE_FRONTEND){

			if($isJsMsg){ // message is build from scripts, just print it!
				return "alert( $message );";
			} else{
				return 'alert("' . str_replace(array('\n','\\', '"','##NL##','`'), array('##NL##','\\\\', '\\"','\n','\"'), $message) . '");';
			}
		} else{
			if($isJsMsg){ // message is build from scripts, just print it!
				return ($isOpener ? 'top.opener.' : '') . 'top.we_showMessage('.$message.', '.$priority.', window);';
			} else{
				return ($isOpener ? 'top.opener.' : '') . 'top.we_showMessage("' . str_replace(array('\n','\\', '"','##NL##','`'), array('##NL##','\\\\', '\\"','\n','\"'), $message) . '", '.$priority.', window);';
			}
		}
	}

}
