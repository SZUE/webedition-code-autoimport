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


protect();
		$id = $_REQUEST["we_cmd"][1];
		if (isset($_REQUEST["we_cmd"][2]) && strpos($_REQUEST["we_cmd"][2],'WECMDENC_')!==false){$_REQUEST["we_cmd"][2]=base64_decode( substr($_REQUEST["we_cmd"][2],9));}
		if (isset($_REQUEST["we_cmd"][3]) && strpos($_REQUEST["we_cmd"][3],'WECMDENC_')!==false){$_REQUEST["we_cmd"][3]=base64_decode( substr($_REQUEST["we_cmd"][3],9));}
		if (isset($_REQUEST["we_cmd"][4]) && strpos($_REQUEST["we_cmd"][4],'WECMDENC_')!==false){$_REQUEST["we_cmd"][4]=base64_decode( substr($_REQUEST["we_cmd"][4],9));}

		$JSIDName = $_REQUEST["we_cmd"][2];
		$JSTextName = $_REQUEST["we_cmd"][3];
		$JSCommand = $_REQUEST["we_cmd"][4];

		include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_modules/banner/we_bannerSelect.php");
