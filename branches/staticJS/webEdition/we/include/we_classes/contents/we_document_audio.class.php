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
class we_document_audio extends we_binaryDocument{

	public function __construct(){
		parent::__construct();
		if(isWE()){
			$this->EditPageNrs[] = we_base_constants::WE_EDITPAGE_PREVIEW;
		}
		$this->ContentType = we_base_ContentTypes::AUDIO;
	}

	function editor(){
		switch($this->EditPageNr){
			case we_base_constants::WE_EDITPAGE_PREVIEW:
				return 'we_editors/we_editor_document_preview.inc.php';
			default:
				return parent::editor();
		}
	}

	public function getHtml($dyn = false){
		$_data = $this->getElement('data');
		if($this->ID || ($_data && !is_dir($_data) && is_readable($_data))){

			return '<audio controls preload="none" style="margin-left:2em;">
							<source src="' . ( $dyn ?
					WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=show_binaryDoc&we_cmd[1]=' . $this->ContentType . '&we_cmd[2]=' . $GLOBALS['we_transaction'] . '&rand=' . we_base_file::getUniqueId() :
					$this->Path) . '" type="audio/' . str_replace('.', '', $this->Extension) . '">
						</video>';
		}
		return '';
	}

}
