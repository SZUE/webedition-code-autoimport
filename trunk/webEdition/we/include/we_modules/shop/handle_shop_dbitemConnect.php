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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

we_html_tools::protect();

// grep the last element from the year-set, wich is the current year
$DB_WE->query("SELECT DATE_FORMAT(DateOrder,'%Y') AS DateOrd FROM " . SHOP_TABLE . ' ORDER BY DateOrd');
while($DB_WE->next_record()){
	if(isset($strs)){
		$strs = array($DB_WE->f("DateOrd"));
		$yearTrans = end($strs);
	}
}
// print $yearTrans;
/// config
$feldnamen = explode('|', f('SELECT strFelder FROM ' . ANZEIGE_PREFS_TABLE . " WHERE strDateiname='shop_pref'"));
for($i = 0; $i <= 3; $i++){
	$feldnamen[$i] = isset($feldnamen[$i]) ? $feldnamen[$i] : '';
}
$fe = explode(',', $feldnamen[3]);
$classid = $fe[0];


//$resultO = count($fe);
$resultO = array_shift($fe);

//FIXME: this is really nonsense.
// wether the resultset ist empty?
$resultD = f('SELECT 1 FROM ' . LINK_TABLE . ' WHERE Name="' . WE_SHOP_TITLE_FIELD_NAME . '" LIMIT 1');
