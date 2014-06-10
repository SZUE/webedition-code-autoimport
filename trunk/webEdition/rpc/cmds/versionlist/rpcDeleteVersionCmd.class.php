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
class rpcDeleteVersionCmd extends rpcCmd{

	function execute(){
		we_html_tools::protect();

		$ids = weRequest('intList', 'we_cmd', '', 'deleteVersion');

		if($ids){
			$_SESSION['weS']['versions']['logDeleteIds'] = array();
			foreach($ids as $v){
				weVersions::deleteVersion($v);
			}
			if($_SESSION['weS']['versions']['logDeleteIds']){
				$versionslog = new versionsLog();
				$versionslog->saveVersionsLog($_SESSION['weS']['versions']['logDeleteIds'], versionsLog::VERSIONS_DELETE);
			}
			unset($_SESSION['weS']['versions']['logDeleteIds']);
		}
	}

}
