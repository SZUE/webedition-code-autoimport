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
class we_editor_thumbnails extends we_editor_base{

	public function show(){
		if(we_base_imageEdit::gd_version() > 0){
			$doc = $this->we_doc->getDocument();
			$imgType = we_base_imageEdit::detect_image_type('', $doc);

			if(!$doc){
				$parts = [[
				"headline" => "",
				"html" => we_html_tools::htmlAlertAttentionBox(g_l('thumbnails', '[no_image_uploaded]'), we_html_tools::TYPE_INFO, 700),
				]];
			} else if(we_base_imageEdit::is_imagetype_read_supported($imgType)){
				$parts = [];

				// look if the fields origwidth & origheight exixts. If not get and set the values
				if((!$this->we_doc->issetElement('origwidth')) || (!$this->we_doc->issetElement('origheight'))){
					$arr = $this->we_doc->getOrigSize();
					$this->we_doc->setElement('origwidth', $arr[0], 'attrib', 'bdid');
					$this->we_doc->setElement('origheight', $arr[1], 'attrib', 'bdid');
					unset($arr);
				}

				$thumbs = $this->we_doc->getThumbs();
				foreach($thumbs as $thumbid){

					$thumbObj = new we_thumbnail();
					$thumbObj->initByThumbID($thumbid, $this->we_doc->ID, $this->we_doc->Filename, $this->we_doc->Path, $this->we_doc->Extension, $this->we_doc->getElement('origwidth', 'bdid'), $this->we_doc->getElement('origheight', 'bdid'), $doc);

					srand((double) microtime() * 1000000);
					$randval = rand();


					$useOrig = $thumbObj->isOriginal();

					if((!$useOrig) && $this->we_doc->ID && ($this->we_doc->DocChanged == false) && file_exists($thumbObj->getOutputPath(true))){
						$src = $thumbObj->getOutputPath(false, true);
					} else {
						$src = WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=show_binaryDoc&amp;we_cmd[1]=' .
								$this->we_doc->ContentType . '&amp;we_cmd[2]=' .
								$GLOBALS['we_transaction'] . '&amp;we_cmd[3]=' . ($useOrig ? "" : $thumbid) . '&amp;rand=' . $randval;
					}


					$delbut = we_html_button::create_button(we_html_button::TRASH, "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('del_thumb','" . $thumbid . "');");

					$thumbnail = '<table class="default" style="width:570px;"><tr><td style="width:538px;"><img src="' . $src . '" style="width:' . $thumbObj->getOutputWidth() . 'px;height:' . $thumbObj->getOutputHeight() . 'px;" /></td><td>' . $delbut . '</td></tr></table>';

					$parts[] = [
						'headline' => $thumbObj->getThumbName(),
						'space' => we_html_multiIconBox::SPACE_BIG,
						'noline' => true
					];
					$parts[] = [
						'html' => $thumbnail,
					];
				}
				$parts[] = [
					"headline" => "",
					"html" => we_html_tools::htmlAlertAttentionBox(g_l('thumbnails', '[add_descriptiontext]'), we_html_tools::TYPE_INFO, 700) . '<br/><br/>' . we_html_button::create_button('fa:btn_add_thumbnail,fa-plus,fa-lg fa-picture-o', "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('add_thumbnail','" . $GLOBALS['we_transaction'] . "');"),
				];
			} else {
				$parts = [
					[
						"headline" => "",
						"html" => we_html_tools::htmlAlertAttentionBox(g_l('thumbnails', '[format_not_supported]'), we_html_tools::TYPE_INFO, 700),
				]];
			}
		} else {
			$parts = [
				[
					"headline" => "",
					"html" => we_html_tools::htmlAlertAttentionBox(g_l('thumbnails', '[add_description_nogdlib]'), we_html_tools::TYPE_INFO, 700),
			]];
		}

		return $this->getPage(we_html_multiIconBox::getHTML('', $parts, 20), we_editor_script::get() . we_html_element::jsScript(JS_DIR . 'multiIconBox.js'), ['onload' => "doScrollTo();"]);
	}

}
