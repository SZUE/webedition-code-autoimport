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

if(empty($_SESSION["user"]["Username"]) && isset($_REQUEST['csid'])) {
	session_id($_REQUEST['csid']);
}

include_once($_SERVER['DOCUMENT_ROOT'].'/webEdition/we/include/we.inc.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/webEdition/we/include/we_global.inc.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/webEdition/we/include/we_import_files.inc.php');

we_html_tools::protect();

$import_files = new we_import_files();

if(isset($_SESSION['_we_import_files'])){
	$import_files->loadPropsFromSession();
}

$_counter = 0;
foreach($_FILES as $_index=>$_file) {
	if(strpos($_index,'File')===0 && $_file['error']==0) {
		$_FILES['we_File'] = $_file;

		$error = $import_files->importFile();

		if(sizeof($error)){
			if(!isset($_SESSION["WE_IMPORT_FILES_ERRORs"])){
				$_SESSION["WE_IMPORT_FILES_ERRORs"] = array();
			}
			array_push($_SESSION["WE_IMPORT_FILES_ERRORs"],$error);
		}

		flush();
		unset($_FILES['we_File']);
		$_counter++;
	} else {
		break;
	}
}

if(isset($_SESSION["WE_IMPORT_FILES_ERRORs"])){
	print_r($_SESSION["WE_IMPORT_FILES_ERRORs"]);
	echo "\n";
}else{
	echo "SUCCESS\n";
}

flush();