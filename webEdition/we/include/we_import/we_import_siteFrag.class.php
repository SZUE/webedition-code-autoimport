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
class we_import_siteFrag extends we_fragment_base{
	private $obj = null;

	function __construct($obj){
		$this->obj = $obj;
		parent::__construct("siteImport", 10, ['style' => 'margin:10px 15px;']);
	}

	protected function init(){
		$this->alldata = $this->obj->files;
	}

	protected function doTask(){
		switch($this->data["contentType"]){
			case "post/process":
				we_import_site::postprocessFile($this->data["path"], $this->data["sourceDir"], $this->data["destDirID"]);
				return;
			default:
				$ret = we_import_site::importFile($this->data["path"], $this->data["contentType"], $this->data["sourceDir"], $this->data["destDirID"], $this->data["sameName"], $this->data["thumbs"], $this->data["width"], $this->data["height"], $this->data["widthSelect"], $this->data["heightSelect"], $this->data["keepRatio"], $this->data["quality"], $this->data["degrees"], $this->data["importMetadata"], $this->data["isSearchable"]);
				if(!empty($ret)){
					t_e('import error:', $ret);
				}
		}
	}

	protected function updateProgressBar(we_base_jsCmd $jsCmd){
		$path = substr($this->data["path"], strlen($_SERVER['DOCUMENT_ROOT']));
		$jsCmd->addCmd('setProgress', [
			'progress' => ((int) ((100 / count($this->alldata)) * (1 + $this->currentTask))),
			'name' => 'progressTxt',
			'text' => we_base_util::shortenPath($path, 30),
			'win' => 'siteimportbuttons'
		]);
		$jsCmd->addCmd('disableBackNext', 'siteimportbuttons');
	}

	protected function finish(we_base_jsCmd $jsCmd){
		$jsCmd->addMsg(g_l('siteimport', '[importFinished]'), we_message_reporting::WE_MESSAGE_NOTICE);
		$jsCmd->addCmd('we_cmd', ['load', FILE_TABLE]);
		$jsCmd->addCmd('close');
	}

}
