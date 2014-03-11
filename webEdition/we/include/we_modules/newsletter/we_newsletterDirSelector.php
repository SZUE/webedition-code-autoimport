<?php
/**
 * webEdition CMS
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

	include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_global.inc.php");
	include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_modules/newsletter/weNewsletterDirSelector.inc.php");
	protect();

	$_SERVER["SCRIPT_NAME"] = "/webEdition/we/include/we_modules/newsletter/we_newsletterDirSelector.php";
		if (isset($_REQUEST["JSIDName"]) && strpos($_REQUEST["JSIDName"],'WECMDENC_')!==false){$_REQUEST["JSIDName"]=base64_decode( urldecode(substr($_REQUEST["JSIDName"],9)));}
		if (isset($_REQUEST["JSTextName"]) && strpos($_REQUEST["JSTextName"],'WECMDENC_')!==false){$_REQUEST["JSTextName"]=base64_decode( urldecode(substr($_REQUEST["JSTextName"],9)));}
		if (isset($_REQUEST["JSCommand"]) && strpos($_REQUEST["JSCommand"],'WECMDENC_')!==false){$_REQUEST["JSCommand"]=base64_decode( urldecode(substr($_REQUEST["JSCommand"],9)));}

	$fs = new weNewsletterDirSelector(
		isset( $id ) ? $id : ( isset( $_REQUEST["id"] ) ? $_REQUEST["id"] : '' ),
		isset( $JSIDName ) ? $JSIDName : ( isset( $_REQUEST["JSIDName"] ) ? $_REQUEST["JSIDName"] : '' ),
		isset( $JSTextName ) ? $JSTextName : ( isset( $_REQUEST["JSTextName"] ) ? $_REQUEST["JSTextName"] : '' ),
		isset( $JSCommand ) ? $JSCommand : ( isset( $_REQUEST["JSCommand"] ) ? $_REQUEST["JSCommand"] : '' ),
		isset( $order ) ? $order : ( isset( $_REQUEST["order"] ) ? $_REQUEST["order"] : '' ),
		0,
		isset( $we_editDirID ) ? $we_editDirID : ( isset( $_REQUEST["we_editDirID"] ) ? $_REQUEST["we_editDirID"] : '' ),
		isset( $we_FolderText ) ? $we_FolderText : ( isset( $_REQUEST["we_FolderText"] ) ? $_REQUEST["we_FolderText"] : '' ),
		isset( $rootDirID ) ? $rootDirID : ( isset( $_REQUEST["rootDirID"] ) ? $_REQUEST["rootDirID"] : '' ),
		isset( $multiple ) ? $multiple : ( isset( $_REQUEST["multiple"] ) ? $_REQUEST["multiple"] : '' )
	);

	$fs->printHTML(isset($_REQUEST["what"]) ? $_REQUEST["what"] : FS_FRAMESET);
