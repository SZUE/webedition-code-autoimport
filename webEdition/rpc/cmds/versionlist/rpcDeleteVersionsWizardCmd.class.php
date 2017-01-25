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
class rpcDeleteVersionsWizardCmd extends we_rpc_cmd{

	function execute(){

		$resp = new we_rpc_response();

		we_html_tools::protect();

		$db = $GLOBALS['DB_WE'];

		$db->query('DELETE FROM `' . VERSIONS_TABLE . '` WHERE ' . $_SESSION['weS']['versions']['deleteWizardWhere']);

		unset($_SESSION['weS']['versions']['deleteWizardWhere']);

//		while($db->next_record()) {
//			weVersions::deleteVersion($db->f("ID"));
//		}
//		foreach($_SESSION['weS']['versions']['IDs'] as $k=>$v) {
//			weVersions::deleteVersion($v);
//		}
		if(!empty($_SESSION['weS']['versions']['deleteWizardbinaryPath']) && is_array($_SESSION['weS']['versions']['deleteWizardbinaryPath'])){
			foreach($_SESSION['weS']['versions']['deleteWizardbinaryPath'] as $v){
				$binaryPath = $_SERVER['DOCUMENT_ROOT'] .VERSION_DIR . $v;
				$binaryPathUsed = f('SELECT 1 FROM ' . VERSIONS_TABLE . ' WHERE binaryPath="' . $db->escape($v) . '" LIMIT 1', '', $db);

				if(file_exists($binaryPath) && !$binaryPathUsed){
					@unlink($binaryPath);
				}
			}
			unset($_SESSION['weS']['versions']['deleteWizardbinaryPath']);
		}

		if($_SESSION['weS']['versions']['logDeleteIds']){
			$versionslog = new we_versions_log();
			$versionslog->saveVersionsLog($_SESSION['weS']['versions']['logDeleteIds'], we_versions_log::VERSIONS_DELETE);
		}
		unset($_SESSION['weS']['versions']['logDeleteIds']);


		$WE_PB = new we_progressBar(100, 200);

		$WE_PB->addText(g_l('versions', '[deleteDateVersionsOK]'), we_progressBar::TOP, "pb1");
		//$js = $WE_PB->getJSCode();
		$pb = $WE_PB->getHTML();


		$resp->setData("data", $pb);

		return $resp;
	}

}
