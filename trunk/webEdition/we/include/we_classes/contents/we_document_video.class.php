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
class we_document_video extends we_binaryDocument{

	public function __construct(){
		parent::__construct();
		if(isWE()){
			$this->EditPageNrs[] = we_base_constants::WE_EDITPAGE_PREVIEW;
		}
		$this->ContentType = we_base_ContentTypes::VIDEO;
	}

	function editor(){
		switch($this->EditPageNr){
			case we_base_constants::WE_EDITPAGE_PREVIEW:
				return 'we_editors/we_editor_document_preview.inc.php';
			default:
				return parent::editor();
		}
	}

	function formProperties(){
		$yuiSuggest = & weSuggest::getInstance();
		return '<table class="default propertydualtable">
	<tr>
		<td>' . $this->formInputInfo2(155, 'width', 10, 'attrib', 'onchange="_EditorFrame.setEditorIsHot(true);"', 'origwidth') . '</td>
		<td>' . $this->formInputInfo2(155, 'height', 10, "attrib", 'onchange="_EditorFrame.setEditorIsHot(true);"', 'origheight') . '</td>
		<td>' . $this->formDocChooser(155, 'poster', 'attrib') . '</td>
	</tr>
	<tr>
		<td>' . $this->formSelectElement(155, 'autoplay', array(0 => g_l('global', '[false]'), 1 => g_l('global', '[true]')), "attrib", 1, array('onchange' => '_EditorFrame.setEditorIsHot(true);')) . '</td>
		<td>' . $this->formSelectElement(155, 'controller', array(1 => g_l('global', '[true]'), 0 => g_l('global', '[false]')), "attrib", 1, array('onchange' => '_EditorFrame.setEditorIsHot(true);')) . '</td>
		<td>' . $this->formColor(155, 'bgcolor', "attrib") . '</td>
	</tr>
	<tr>
		<td>' . $this->formSelectElement(155, 'mute', array(0 => g_l('global', '[false]'), 1 => g_l('global', '[true]')), "attrib", 1, array('onchange' => '_EditorFrame.setEditorIsHot(true);')) . '</td>
		<td>' . $this->formSelectElement(155, 'loop', array(0 => g_l('global', '[false]'), 1 => g_l('global', '[true]')), "attrib", 1, array('onchange' => '_EditorFrame.setEditorIsHot(true);')) . '</td>
		<td>' . $this->formInput2(155, 'name', 10, 'attrib', 'onchange="_EditorFrame.setEditorIsHot(true);"') . '</td>
	</tr>
</table>' .
			$yuiSuggest->getYuiJs()
		;
	}

	public function getHtml($dyn = false, $preload = false){
		$data = $this->getElement('data');
		if($this->ID || ($data && !is_dir($data) && is_readable($data))){

			if(($bdid = $this->getElement('poster', 'bdid'))){
				$poster = id_to_path($bdid);
				$poster = ($bdid && $poster && file_exists($_SERVER['DOCUMENT_ROOT'] . $poster) ?
						$poster :
						'');
			} else {
				$poster = '';
			}
			$width = $preload ? 100 : $this->getElement('width');
			$height = $preload ? 100 : $this->getElement('height');
			$play = !$preload && $this->getElement('autoplay');
			$control = !$preload && $this->getElement('controller');
			$bgcolor = $preload ? '' : $this->getElement('bgcolor');
			$mute = $this->getElement('mute');
			$loop = $this->getElement('loop');
			$name = $this->getElement('name');


			return
				getHtmlTag('video', array_filter(array(
				'style' => 'width:' . ($width? : 400) . 'px;height:' . ($height? : 400) . 'px;' . ($preload ? 'margin-left:2em;' : '') . ($bgcolor ? 'background-color:' . $bgcolor . ';' : ''),
				($play ? 'autoplay' : '') => 'autoplay',
				(!$preload && $control !== '0' ? 'controls' : '') => 'controls',
				($mute ? 'muted' : '') => 'muted',
				($loop ? 'loop' : '') => 'loop',
				($name ? 'name' : '') => $name,
				($poster ? 'poster' : '') => $poster,
				'preload' => ($preload || !$poster ? 'metadata' : 'none')
				)), getHtmlTag('source', array(
				'src' => ( $dyn ?
					WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=show_binaryDoc&we_cmd[1]=' . $this->ContentType . '&we_cmd[2]=' . $GLOBALS['we_transaction'] . '&rand=' . we_base_file::getUniqueId() :
					$this->Path),
				'type' => 'video/' . str_replace('.', '', $this->Extension)
				))
				, true);
		}
		return '';
	}

	protected function getThumbnail($width = 150, $height = 100){
		if(($bdid = $this->getElement('poster', 'bdid'))){
			$path = id_to_path($bdid);
		}

		return ($bdid && $path && file_exists($_SERVER['DOCUMENT_ROOT'] . $path) ?
				we_html_element::htmlImg(array('src' => $path, 'maxwidth' => '100px', 'maxheight' => '100px')) :
				$this->getHtml(true, true));
	}

	/**
	 * sets extra attributes for the image
	 *
	 * @return void
	 * @param array $attribs
	 */
	function initByAttribs($attribs){
		foreach($attribs as $a => $b){
			switch($a){
				case 'id':
				case 'name':
					break;
				default:
					$this->setElement($a, $b, 'attrib');
			}
		}
		$this->checkDisableEditpages();
	}

}
