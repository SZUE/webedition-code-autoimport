<?php

/**
 * webEdition CMS
 *
 * $Rev: 13703 $
 * $Author: lukasimhof $
 * $Date: 2017-04-06 16:44:34 +0200 (Do, 06. Apr 2017) $
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
class we_exim_ExportWE extends we_exim_Export{

	function __construct(){
		parent::__construct();
		$this->exportType = we_exim_ExIm::TYPE_WE;
	}

	protected function writeExportItem($doc, $fh, $attribute = [], $isBin = false, $setBackupMarker = false){
		if($isBin){
			we_exim_contentProvider::binary2file($doc, $fh);
		} else {
			we_exim_contentProvider::object2xml($doc, $fh, $attribute);
		}

		if($setBackupMarker){
			fwrite($fh, we_backup_util::backupMarker . "\n");
		}
		
	}

	public function prepareExport(){ // check access level of parents
		$this->savePreserves();

		$preparer = new we_export_preparer(/*$this->options, $this->RefTable*/);
		$preparer->loadPreserves();
		$preparer->prepareExport();
		$preparer->savePreserves();

		$this->loadPreserves();
		parent::prepareExport();
	}

	protected function fileCreate(){
		if(parent::fileCreate()){
			we_base_file::save($this->exportProperties['file'], we_exim_ExIm::getHeader(), we_exim_ExIm, 'wb');

			return true;
		}

		return false;
	}

	protected function fileComplete(){
		we_base_file::save($this->exportProperties['file'], we_exim_ExIm::getFooter(), "ab");
	}
}
