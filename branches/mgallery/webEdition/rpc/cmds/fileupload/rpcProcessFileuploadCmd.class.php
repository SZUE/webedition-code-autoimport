<?php

/**
 * webEdition CMS
 *
 * $Rev: 10084 $
 * $Author: lukasimhof $
 * $Date: 2015-07-01 12:23:38 +0200 (Wed, 01 Jul 2015) $
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
class rpcProcessFileuploadCmd extends rpcCmd{

	function execute(){
		$resp = new rpcResponse();
		$fileinputName = we_base_request::_(we_base_request::STRING, 'fileinputName', '');
		$fileuploadRespClass = we_base_request::_(we_base_request::STRING, 'weResponseClass', 'we_fileupload_resp_base');

		$FILES = $controlVars = $fileVars = $docVars = array();

		/*
		$controlVars = array(
			'partNum' => we_base_request::_(we_base_request::INT, 'wePartNum', we_base_request::NOT_VALID),
			'partCount' => we_base_request::_(we_base_request::INT, 'wePartCount', we_base_request::NOT_VALID),
			'formnum' => we_base_request::_(we_base_request::INT, "weFormNum", we_base_request::NOT_VALID),
			'formcount' => we_base_request::_(we_base_request::INT, "weFormCount", we_base_request::NOT_VALID),
			'weIsUploadComplete' => false,//FIXME: do we really need so much vars for execution control?
			'weIsUploading' => we_base_request::_(we_base_request::BOOL, 'weIsUploading', we_base_request::NOT_VALID),
		);
		$fileVars = array(
			'genericFileNameTemp' => we_base_request::_(we_base_request::STRING, 'genericFilename', we_base_request::NOT_VALID),
			'fileNameTemp' => we_base_request::_(we_base_request::STRING, 'weFileNameTemp', we_base_request::NOT_VALID),
			'weFileName' => we_base_request::_(we_base_request::STRING, 'weFileName', we_base_request::NOT_VALID),
			'weFileSize' => we_base_request::_(we_base_request::INT, 'weFileSize', we_base_request::NOT_VALID),
			'weFileCt' => we_base_request::_(we_base_request::STRING, 'weFileCt', we_base_request::NOT_VALID)
		);

		$docVars = array(
			'transaction' => we_base_request::_(we_base_request::TRANSACTION, 'we_transaction', we_base_request::NOT_VALID),
			'sameName' => we_base_request::_(we_base_request::STRING, "sameName", we_base_request::NOT_VALID),
			'importToID' => we_base_request::_(we_base_request::INT, "importToID", we_base_request::NOT_VALID),
			'importMetadata' => we_base_request::_(we_base_request::INT, "importMetadata", we_base_request::NOT_VALID),
			'imgsSearchable' => we_base_request::_(we_base_request::INT, "imgsSearchable", we_base_request::NOT_VALID),
			'thumbs' => we_base_request::_(we_base_request::INTLIST, 'thumbs', we_base_request::NOT_VALID),
			'width' => we_base_request::_(we_base_request::INT, "width", we_base_request::NOT_VALID),
			'widthSelect' => we_base_request::_(we_base_request::STRING, "widthSelect", we_base_request::NOT_VALID),
			'height' => we_base_request::_(we_base_request::INT, "height", we_base_request::NOT_VALID),
			'heihgtSelect' => we_base_request::_(we_base_request::STRING, "heightSelect", we_base_request::NOT_VALID),
			'quality' => we_base_request::_(we_base_request::INT, "quality", we_base_request::NOT_VALID),
			'keepRatio' => we_base_request::_(we_base_request::BOOL, "keepRatio", we_base_request::NOT_VALID),
			'degrees' => we_base_request::_(we_base_request::INT, "degrees", we_base_request::NOT_VALID),
		);
		 * 
		 */
		$fileupload_resp = new $fileuploadRespClass($fileinputName, '', $FILES, $fileVars, $controlVars, $docVars);
		$resp->setData("data", $fileupload_resp->processRequest());//, JSON_F

		return $resp;
	}

}