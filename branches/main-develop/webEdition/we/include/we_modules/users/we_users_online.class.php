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
 * Class we_usersOnline
 *
 * This class handles the users online for the personalized desktop (Cockpit).
 */
class we_users_online{
	var $num_uo = 0;
	var $users = '';

	public function __construct(){
		global $DB_WE;
		$_row = '';
		$colors = array(
			'red',
			'blue',
			'green',
			'orange',
			'darkgreen',
			'darkblue',
			'darkslategray',
			'navy',
			'tomato',
			'orchid',
			'darkorange',
			'fuchsia',
			'seagreen'
		); //FIXME:add usefull colors
		$i = -1;
		$DB_WE->query('SELECT ID,username,TRIM(CONCAT(First," ",Second)) AS User FROM ' . USER_TABLE . ' WHERE Ping>((NOW()-INTERVAL ' . (we_base_constants::PING_TIME + we_base_constants::PING_TOLERANZ) . ' SECOND )) ORDER BY Ping DESC');
		$colorCount = count($colors);
		while($DB_WE->next_record()){
			$this->num_uo++;
			$_row .= '<tr><td width="30" style="margin-top:8px;color:' . $colors[( ++$i) % $colorCount] . '"><i class="fa fa-user fa-2x"></i></td>' .
				'<td class="middlefont we-user">' . ($DB_WE->f('User')? : $DB_WE->f('username')) . '</td>' .
				(defined('MESSAGES_TABLE') ?
					'<td><a href="javascript:newMessage(\'' . $DB_WE->f('username') . '\');">' .
					'<i style="color:#9fbcd5;" class="fa fa-2x fa-envelope"></i></a><td>' :
					''
				) . '</tr>';
		}

		$this->users = '<div style="height:187px;overflow:auto;"><table width="100%" class="default">' . $_row . '</table></div>';
	}

	function getNumUsers(){
		return $this->num_uo;
	}

	function getUsers(){
		return $this->users;
	}

}
