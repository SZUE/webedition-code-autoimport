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
// Activate the webEdition error handler
include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_error_handler.inc.php');
we_error_handler(false);

if(!isset($_SESSION)){
//	session_name(SESSION_NAME);
	@session_start();
}

//FIXME: this should be removed if all variables are located inside weS
while((list($name, $val) = each($_SESSION))) {
	if($name != "webuser"){
		unset($_SESSION[$name]);
	}
}

if(isset($_POST["username"]) && isset($_POST["id"]) && isset($_POST["type"])){

	$_SESSION['weS']['we_set_registered'] = true;

	$_POST["password"] = $_SESSION["webuser"]["Password"];

	//	Login
	require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

	if(isset($_SESSION["user"]["Username"])){ //	login ok!
		//	we must give some information, that we start in Super-Easy-Edit-Mode
		$_SESSION['weS']['we_mode'] = "seem";
		$_SESSION["SEEM"]["startId"] = $_POST["id"];
		$_SESSION["SEEM"]["startType"] = $_POST["type"];
		$_SESSION["SEEM"]["startPath"] = $_POST["path"];

		$_SESSION["SEEM"]["open_selected"] = true; //	This var is only temporary
		//	now start webEdition
		we_html_tools::htmlTop();
		print '
</head>
<body>
<form name="startSuperEasyEditMode" method="post" action="/webEdition/webEdition.php">
</form>' . we_html_element::jsElement('document.forms[\'startSuperEasyEditMode\'].submit();') .
			'</body>
</html>';
	} else{

		print "Ein Fehler trat auf. - 1";
	}
} else{

	print "Es trat ein Fehler auf. - 2";
}
