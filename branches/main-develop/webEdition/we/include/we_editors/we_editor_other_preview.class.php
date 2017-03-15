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
// force the download of this document

class we_editor_other_preview extends we_editor_base{

	private function download(){
		$file = (file_exists($_SERVER['DOCUMENT_ROOT'] . $this->we_doc->Path) ? $_SERVER['DOCUMENT_ROOT'] . $this->we_doc->Path : $_SERVER['DOCUMENT_ROOT'] . SITE_DIR . $this->we_doc->Path);
		$filename = $this->we_doc->Filename . $this->we_doc->Extension;
		if(file_exists($file)){
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-control: private, max-age=0, must-revalidate");

			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="' . trim(htmlentities($filename)) . '"');
			header('Content-Description: ' . trim(htmlentities($filename)));
			header('Content-Length: ' . filesize($file));
			readfile($file);
			return '';
		}
	}

	public function show(){
		if(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 3) === 'download'){
			return $this->download();
		}
		switch(strtolower($this->we_doc->Extension)){
			case '.pdf':
				$previewAvailable = true;
				break;
			default:
				$previewAvailable = false;
				break;
		}


		if($previewAvailable && $this->we_doc->ID){
			$form = we_html_element::htmlIFrame('preview', $this->we_doc->Path);
		} else {
			$parts = [
				[
					"headline" => g_l('weClass', '[preview]'), "html" => we_html_tools::htmlAlertAttentionBox(g_l('weClass', '[no_preview_available]'), we_html_tools::TYPE_ALERT), 'space' => we_html_multiIconBox::SPACE_MED]
			];

			if($this->we_doc->ID){
				$we_transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_transaction', 0);
				$link = '<a href="' . WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=' . we_base_request::_(we_base_request::CMD, 'we_cmd', '', 0) . '&we_cmd[1]=' . we_base_request::_(we_base_request::INT, 'we_cmd', '', 1) . '&we_cmd[2]=' . we_base_request::_(we_base_request::CMD, 'we_cmd', '', 2) . '&we_cmd[3]=download&we_transaction=' . $we_transaction . '" download="' . $this->we_doc->Filename . '">' . $this->we_doc->getHttpPath() . "</a>";
			} else {
				$link = g_l('weClass', '[file_not_saved]');
			}
			$parts[] = ["headline" => g_l('weClass', '[download]'), "html" => $link, 'space' => we_html_multiIconBox::SPACE_MED];

			$form = we_html_multiIconBox::getHTML('weOtherDocPrev', $parts, 20);
		}

		return $this->getPage($form, '', [
					'class' => 'weEditorBody previewOther',
					'onload' => (substr(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0), 0, 15) === 'doImage_convert' ?
					'WE().layout.we_setPath(_EditorFrame,\'' . $this->we_doc->Path . '\',\'' . $this->we_doc->Text . '\', ' . intval($this->we_doc->ID) . ',\'published\');"' : '')
		]);
	}

}
