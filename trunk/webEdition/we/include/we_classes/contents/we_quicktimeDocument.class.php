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
/*  a class for handling quicktimeDocuments. */

class we_quicktimeDocument extends we_document_deprecatedVideo{
	/* Parameternames which are placed within the object-Tag */
	var $ObjectParamNames = array("width", "height", "name", "vspace", "hspace", "style");

	public function __construct(){
		parent::__construct();
		if(isWE()){
			$this->EditPageNrs[] = we_base_constants::WE_EDITPAGE_PREVIEW;
		}
		$this->ContentType = we_base_ContentTypes::QUICKTIME;
	}

	// is not written yet
	function initByAttribs($attribs){
		if(($sizingrel = weTag_getAttribute('sizingrel', $attribs, 0, we_base_request::INT))){
			$orig_w = weTag_getAttribute('width', $attribs, $this->getElement('width'), we_base_request::UNIT);
			$orig_h = weTag_getAttribute('height', $attribs, $this->getElement('height'), we_base_request::UNIT);
			$attribs['width'] = round($orig_w * $sizingrel);
			$attribs['height'] = round($orig_h * $sizingrel);
		}
		$sizingbase = weTag_getAttribute('sizingbase', $attribs, 16, we_base_request::UNIT);
		$sizingstyle = weTag_getAttribute('sizingstyle', $attribs, false, we_base_request::STRING);
		if($sizingstyle === 'none'){
			$sizingstyle = false;
		}

		$removeAttribs = array('sizingrel', 'sizingbase', 'sizingstyle', 'xml', '_name_orig');
		if($sizingstyle){
			$style_width = round($attribs['width'] / $sizingbase, 6);
			$style_height = round($attribs['height'] / $sizingbase, 6);
			$attribs['style'] = (isset($attribs['style']) ? $attribs['style'] : '') . ';width:' . $style_width . $sizingstyle . ';height:' . $style_height . $sizingstyle . ';';
			$removeAttribs[] = 'width';
			$removeAttribs[] = 'height';
		}
		$this->setElement('xml', weTag_getAttribute('xml', $attribs, XHTML_DEFAULT, we_base_request::BOOL));
		$attribs = removeAttribs($attribs, $removeAttribs);

		foreach($attribs as $a => $b){
			if($b != ''){
				$this->setElement($a, $b, ($a === 'Pluginspage' || $a === 'Codebase' ? 'txt' : 'attrib'));
			}
		}
	}

	/* gets the HTML for including in HTML-Docs */

	function getHtml($dyn = false, $preload = false){
		//    At the moment it is not possible to make this tag xhtml valid, so the output is only posible
		//    non xhtml valid

		$data = $this->getElement('data');
		if($this->ID || ($data && !is_dir($data) && is_readable($data))){
			$pluginspage = $this->getElement('Pluginspage') ? : 'http://www.apple.com/quicktime/download/';
			$codebase = $this->getElement('Codebase') ? : 'http://www.apple.com/qtactivex/qtplugin.cab';

			$src = $dyn ?
				WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=show_binaryDoc&we_cmd[1]=' . $this->ContentType . '&we_cmd[2]=' . $GLOBALS['we_transaction'] . '&rand=' . we_base_file::getUniqueId() :
				$this->Path;

			$filter = array("filesize", "type", "xml");
			$noAtts = array("scale", "volume"); //  no atts for xml

			$this->resetElements();
			while(list($k, $v) = $this->nextElement("attrib")){
				if(in_array($k, $this->ObjectParamNames)){
					$objectAtts[$k] = $v["dat"];
				}
			}

			//  $xml = $this->getElement("xml");
			//  xhtml output is not possible to work for IE and Mozilla
			//  therefore it is deactivated
			$xml = 'false';
			$objectAtts['xml'] = $xml;

			//  <embed> for none xhtml
			$embed = '';

			//  <params>
			$params = "\n" . getHtmlTag('param', array('name' => 'src', 'value' => $src, 'xml' => $xml)) . "\n";

			if($xml === 'true'){ //  only object tag
				$objectAtts['type'] = 'video/quicktime';
				$objectAtts['data'] = $src;

				$this->resetElements();
				while(list($k, $v) = $this->nextElement("attrib")){
					if(!in_array($k, $filter) && !in_array($k, $this->ObjectParamNames)){

						if($v["dat"] != ""){ //  dont use empty params
							if(!in_array($k, $noAtts)){
								$objectAtts[$k] = $v["dat"];
							}
							$params .= getHtmlTag('param', array('name' => $k, 'value' => $v["dat"], 'xml' => $xml)) . "\n";
						}
					}
				}
			} else { //  object tag and embed
				$objectAtts['classid'] = 'clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B';
				$objectAtts['codebase'] = $codebase;
				//   we need embed as well

				$embedAtts['type'] = 'video/quicktime';
				$embedAtts['pluginspace'] = $pluginspage;
				$embedAtts['xml'] = $xml;
				$embedAtts['src'] = $src;

				$this->resetElements();
				while(list($k, $v) = $this->nextElement("attrib")){
					if(!in_array($k, $filter) && $v["dat"] != ""){

						if($v["dat"] != ""){ //  dont use empty params
							$params .= getHtmlTag('param', array('name' => $k, 'value' => $v["dat"], 'xml' => $xml)) . "\n";
						}

						$embedAtts[$k] = $v["dat"];
					}
				}
				$embed = "\n" . getHtmlTag('embed', $embedAtts, '', true);
			}
			$objectAtts = removeEmptyAttribs($objectAtts);
			$this->html = getHtmlTag('object', $objectAtts, $params . $embed);
		} else {
			$this->html = '';
		}
		return $this->html;
	}

	function formProperties(){
		return '<table class="default propertydualtable">
	<tr>
		<td>' . $this->formInputInfo2(155, "width", 10, "attrib", 'onchange="_EditorFrame.setEditorIsHot(true);"', "origwidth") . '</td>
		<td>' . $this->formInputInfo2(155, "height", 10, "attrib", 'onchange="_EditorFrame.setEditorIsHot(true);"', "origheight") . '</td>
		<td>' . $this->formSelectElement(155, "scale", array(
				"" => "",
				"tofit" => "tofit",
				"aspect" => "aspect",
				"0.5" => "0,5x",
				"2" => "2x",
				"4" => "4x"
				), "attrib", 1, array('onchange' => '_EditorFrame.setEditorIsHot(true);')
			) . '</td>
	</tr>
	<tr>
		<td>' . $this->formInput2(155, "hspace", 10, "attrib", 'onchange="_EditorFrame.setEditorIsHot(true);"') . '</td>
		<td>' . $this->formInput2(155, "vspace", 10, "attrib", 'onchange="_EditorFrame.setEditorIsHot(true);"') . '</td>
		<td>' . $this->formInput2(155, "name", 10, "attrib", 'onchange="_EditorFrame.setEditorIsHot(true);"') . '</td>
	</tr>
	<tr>
		<td>' . $this->formSelectElement(155, "autoplay", array("" => g_l('global', '[true]'), "false" => g_l('global', '[false]')), "attrib", 1, array('onchange' => '_EditorFrame.setEditorIsHot(true);')) . '</td>
		<td>' . $this->formSelectElement(155, "controller", array("" => g_l('global', '[true]'), "false" => g_l('global', '[false]')), "attrib", 1, array('onchange' => '_EditorFrame.setEditorIsHot(true);')) . '</td>
		<td>' . $this->formColor(155, "bgcolor", "attrib") . '</td>
	</tr>
	<tr>
		<td>' . $this->formSelectElement(155, "volume", array("100" => "", 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 100), "attrib", 1, array('onchange' => '_EditorFrame.setEditorIsHot(true);')) . '</td>
		<td>' . $this->formSelectElement(155, "hidden", array("true" => g_l('global', '[true]'), "" => g_l('global', '[false]')), "attrib", 1, array('onchange' => "_EditorFrame.setEditorIsHot(true);")) . '</td>
		<td>' . $this->formSelectElement(155, "loop", array("" => g_l('global', '[true]'), "false" => g_l('global', '[false]')), "attrib", 1, array('onchange' => "_EditorFrame.setEditorIsHot(true);")) . '</td>
	</tr>
</table>';
	}

	function formOther(){
		return '<table class="default">
	<tr><td>' . $this->formInputField("txt", "Pluginspage", "Pluginspage", 24, 388, "", 'onchange="_EditorFrame.setEditorIsHot(true);"') . '</td><tr>
	<tr><td>' . $this->formInputField("txt", "Codebase", "Codebase", 24, 388, "", 'onchange="_EditorFrame.setEditorIsHot(true);"') . '</td></tr>
</table>';
	}

	function getThumbnail($width = 150, $height = 100){
		$elemWidth = $this->getElement('width');
		$elemHeight = $this->getElement('height');
		$scale = $this->getElement('scale');
		$hspace = $this->getElement('hspace');
		$vspace = $this->getElement('vspace');
		$name = $this->getElement('name');
		$autoplay = $this->getElement('autoplay');
		$controller = $this->getElement('controller');
		$bgcolor = $this->getElement('bgcolor');
		$volume = $this->getElement('volume');
		$hidden = $this->getElement('hidden');
		$loop = $this->getElement('loop');

		$this->setElement('width', $width, 'attrib');
		$this->setElement('height', $height, 'attrib');
		$this->setElement('scale', 'aspect', 'attrib');
		$this->setElement('hspace', '', 'attrib');
		$this->setElement('vspace', '', 'attrib');
		$this->setElement('name', '', 'attrib');
		$this->setElement('autoplay', 'true', 'attrib');
		$this->setElement('controller', 'false', 'attrib');
		$this->setElement('bgcolor', '', 'attrib');
		$this->setElement('volume', '', 'attrib');
		$this->setElement('hidden', '', 'attrib');
		$this->setElement('loop', '', 'attrib');
		$html = $this->getHtml(true);
		$this->setElement('width', $elemWidth, 'attrib');
		$this->setElement('height', $elemHeight, 'attrib');
		$this->setElement('scale', $scale, 'attrib');
		$this->setElement('hspace', $hspace, 'attrib');
		$this->setElement('vspace', $vspace, 'attrib');
		$this->setElement('name', $name, 'attrib');
		$this->setElement('autoplay', $autoplay, 'attrib');
		$this->setElement('controller', $controller, 'attrib');
		$this->setElement('bgcolor', $bgcolor, 'attrib');
		$this->setElement('volume', $volume, 'attrib');
		$this->setElement('hidden', $hidden, 'attrib');
		$this->setElement('loop', $loop, 'attrib');
		return $html;
	}

	static function checkAndPrepare($formname, $key = 'we_document'){
		// check to see if there is an image to create or to change
		if(!(isset($_FILES['we_ui_' . $formname]) && is_array($_FILES['we_ui_' . $formname]) && isset($_FILES['we_ui_' . $formname]['name']) && is_array($_FILES['we_ui_' . $formname]['name']) )){
			return;
		}
		//$webuserId = isset($_SESSION['webuser']['ID']) ? $_SESSION['webuser']['ID'] : 0;

		foreach($_FILES['we_ui_' . $formname]['name'] as $videoName => $filename){

			$videoDataId = we_base_request::_(we_base_request::STRING, 'WE_UI_QUICKTIME_DATA_ID_' . $videoName);

			if($videoDataId !== false && isset($_SESSION[$videoDataId])){

				$_SESSION[$videoDataId]['doDelete'] = false;

				if(we_base_request::_(we_base_request::BOOL, 'WE_UI_DEL_CHECKBOX_' . $videoName)){
					$_SESSION[$videoDataId]['doDelete'] = true;
				} elseif($filename){
					// file is selected, check to see if it is an image
					$ct = getContentTypeFromFile($filename);
					if($ct == $this->ContentType){
						$videoid = intval($GLOBALS[$key][$formname]->getElement($videoName));

						// move document from upload location to tmp dir
						$_SESSION[$videoDataId]['serverPath'] = TEMP_PATH . we_base_file::getUniqueId();
						move_uploaded_file($_FILES['we_ui_' . $formname]['tmp_name'][$videoName], $_SESSION[$videoDataId]['serverPath']);

						$unique = we_base_file::getUniqueId();
						$tmp_Filename = $videoName . '_' . $unique . '_' . preg_replace('[^A-Za-z0-9._-]', '', $_FILES['we_ui_' . $formname]['name'][$videoName]);

						if($videoid){
							$_SESSION[$videoDataId]['id'] = $videoid;
						}

						$_SESSION[$videoDataId]['fileName'] = preg_replace('#^(.+)\..+$#', '${1}', $tmp_Filename);
						$_SESSION[$videoDataId]['extension'] = (strpos($tmp_Filename, '.') > 0) ? preg_replace('#^.+(\..+)$#', '${1}', $tmp_Filename) : '';
						$_SESSION[$videoDataId]['text'] = $_SESSION[$videoDataId]['fileName'] . $_SESSION[$videoDataId]['extension'];
						$_SESSION[$videoDataId]['unique'] = $unique;

						//$_SESSION[$videoDataId]["imgwidth"] = $we_size[0];
						//$_SESSION[$videoDataId]["imgheight"] = $we_size[1];
						$_SESSION[$videoDataId]['type'] = $_FILES['we_ui_' . $formname]['type'][$videoName];
						$_SESSION[$videoDataId]['size'] = $_FILES['we_ui_' . $formname]['size'][$videoName];
					}
				}
			}
		}
	}

}
