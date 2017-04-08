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
class we_fileupload_resp_multiimport extends we_fileupload_resp_import{

	public function processRequest(){
		// manage filenumber
		if($this->controlVars['formcount']){
			return parent::processRequest();
		}
	}

	protected function postprocess(){
		$response = parent::postprocess();

		if($response['status'] === 'failure'){
			if(!isset($_SESSION['weS']['WE_IMPORT_FILES_ERRORs'])){
				$_SESSION['weS']['WE_IMPORT_FILES_ERRORs'] = [];
			}
			$_SESSION['weS']['WE_IMPORT_FILES_ERRORs'][] = ['filename' => $this->fileVars['weFileName'], 'error' => $response['message']];
		} else {
			$_SESSION['weS']['WE_IMPORT_FILES_SUCCESS_IDS'] = isset($_SESSION['weS']['WE_IMPORT_FILES_SUCCESS_IDS']) ? $_SESSION['weS']['WE_IMPORT_FILES_SUCCESS_IDS'] : [];
			$_SESSION['weS']['WE_IMPORT_FILES_DOCUMENTS'] = isset($_SESSION['weS']['WE_IMPORT_FILES_DOCUMENTS']) ? $_SESSION['weS']['WE_IMPORT_FILES_DOCUMENTS'] : [];

			$_SESSION['weS']['WE_IMPORT_FILES_SUCCESS_IDS'][] = $response['weDoc']['id'];
			$_SESSION['weS']['WE_IMPORT_FILES_DOCUMENTS'][] = $response['weDoc'];
		}

		if($this->controlVars['formnum'] === $this->controlVars['formcount']){
			$response['success'] = empty($_SESSION['weS']['WE_IMPORT_FILES_SUCCESS_IDS']) ? [] : $_SESSION['weS']['WE_IMPORT_FILES_SUCCESS_IDS'];
			$response['imported_files'] = empty($_SESSION['weS']['WE_IMPORT_FILES_DOCUMENTS']) ? [] : $_SESSION['weS']['WE_IMPORT_FILES_DOCUMENTS'];

			if(isset($_SESSION['weS']['WE_IMPORT_FILES_ERRORs']) && $this->controlVars['formnum'] !== 0){
				$filelist = '';
				foreach($_SESSION['weS']['WE_IMPORT_FILES_ERRORs'] as $err){
					$filelist .= '- ' . $err["filename"] . ' => ' . $err["error"] . '\n';
				}
				unset($_SESSION['weS']['WE_IMPORT_FILES_ERRORs']);
				$response['completed'] = ['message' => sprintf(g_l('importFiles', '[error]'), $filelist), 'type' => we_base_util::WE_MESSAGE_ERROR];
			} else {
				$completed = g_l('importFiles', '[finished]');
				if($response['imported_files']){
					$completed .= '</br>';
					foreach($response['imported_files'] as $file){
						$completed .= '</br>- ' . $file['text'];
					}
				}
				$response['completed'] = ['message' => $completed, 'type' => we_base_util::WE_MESSAGE_NOTICE];
			}

			unset($_SESSION['weS']['WE_IMPORT_FILES_SUCCESS_IDS']);
			unset($_SESSION['weS']['WE_IMPORT_FILES_DOCUMENTS']);
		}

		return $response;
	}

}
