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
class rpcShowPreparedPreviewCmd extends rpcCmd{

	function execute(){

		$resp = new rpcResponse();

		$we_dt = "";

		$_REQUEST['we_cmd'][0] = "";

		if(($dt=weRequest('string', 'we_dt', ''))){
			$we_dt = isset($_SESSION['weS']['we_data'][$dt]) ? $_SESSION['weS']['we_data'][$dt] : '';
		}

		$GLOBALS['we_doc'] = new we_template();
		$GLOBALS['we_doc']->we_initSessDat($we_dt);

		$GLOBALS['we_doc']->setElement("data", stripslashes($_SESSION['weS']['rpc_previewCode']));
		unset($_SESSION['weS']['rpc_previewCode']);

		$GLOBALS['we_doc']->EditPageNr = (weRequest('string', 'mode', '')== "preview" ? WE_EDITPAGE_PREVIEW_TEMPLATE : WE_EDITPAGE_PREVIEW);

		$we_doc = $GLOBALS['we_doc'];

		ob_start();
		include( $GLOBALS['we_doc']->editor() );
		$data = ob_get_contents();
		ob_end_clean();

		$resp->addData("data", $data);

		return $resp;
	}

}
