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
abstract class we_users_online{

	public static function getUsers(){
		global $DB_WE;
		$row = '';
		$num = 0;
		$colors = ['red',
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
		];
		$i = -1;
		$DB_WE->query('SELECT ID,username,TRIM(CONCAT(First," ",Second)) AS User,Email FROM ' . USER_TABLE . ' WHERE Ping>((NOW()-INTERVAL ' . (we_base_constants::PING_TIME + we_base_constants::PING_TOLERANZ) . ' SECOND )) ORDER BY Ping DESC');
		$colorCount = count($colors);
		while($DB_WE->next_record()){
			$num++;
			$row .= '<tr><td style="width:30px;margin-top:8px;color:' . $colors[( ++$i) % $colorCount] . '"><i class="fa fa-user fa-2x"></i></td>' .
				'<td class="middlefont we-user">' . htmlentities(($DB_WE->f('User')? : $DB_WE->f('username')), ENT_COMPAT, $GLOBALS['WE_BACKENDCHARSET']) . '</td>' .
				($DB_WE->f('Email') ?
					'<td><a href="mailto:' . rawurlencode($DB_WE->f('User') . '<' . $DB_WE->f('Email') . '>') . ');">' .
					'<i style="color:#9fbcd5;" class="fa fa-2x fa-envelope"></i></a><td>' :
					''
				) . '</tr>';
		}

		return [$num, '<div style="height:187px;overflow:auto;"><table style="width:100%" class="default">' . $row . '</table></div>'];
	}

}
