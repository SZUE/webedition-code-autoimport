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
class rpcDeleteVersionCmd extends we_rpc_cmd{

	function execute(){
		we_html_tools::protect();

		$ids = we_base_request::_(we_base_request::INTLISTA, 'we_cmd', [], 'deleteVersion');
		$version=new we_versions_version();
		if($ids){
			$_SESSION['weS']['versions']['logDeleteIds'] = [];
			foreach($ids as $v){
				$version->deleteVersion($v);
			}
			if($_SESSION['weS']['versions']['logDeleteIds']){
				$versionslog = new we_versions_log();
				$versionslog->saveVersionsLog($_SESSION['weS']['versions']['logDeleteIds'], we_versions_log::VERSIONS_DELETE);
			}
			unset($_SESSION['weS']['versions']['logDeleteIds']);
		}
	}

}
