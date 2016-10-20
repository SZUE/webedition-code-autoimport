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
/* messaging email send class */

class we_messaging_email extends we_messaging_proto{
	const TYPE_SEND_RECEIVE = 0;
	const TYPE_SEND_ONLY = 1;

	var $msgclass_type = self::TYPE_SEND_ONLY;

	function __construct(){
		parent::__construct();
		$this->Name = 'msg_email_' . md5(uniqid(__FILE__, true));
	}

	function get_email_addr($userid){
		return f('SELECT Email FROM ' . USER_TABLE . ' WHERE ID=' . intval($userid), '', new DB_WE());
	}

	function send(&$rcpts, &$data){
		$results = ['err' => [],
			'ok' => [],
			'failed' => [],
			];

		$from = we_messaging_format::get_nameline($this->userid, 'email');
		$to = array_shift($rcpts);
		//$cc = join(',', $rcpts);
		//FIXME: more receipients not supported

		if(we_mail($to, $data['subject'], $data['body'], '', $from)){
			$results['err'] = g_l('modules_messaging', '[error_occured]') . ': ' . g_l('modules_messaging', '[mail_not_sent]');
			$results['failed'] = $rcpts;
		} else {
			array_unshift($rcpts, $to);
			$results['ok'] = $rcpts;
		}

		return $results;
	}

}
