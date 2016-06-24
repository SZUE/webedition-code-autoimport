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
class we_gui_OrderContainer{
	// private Target Frame
	var $targetFrame = "";
	// private containerId
	var $containerId = "";
	// private containerType
	var $containerType = "";

	public function __construct($targetFrame, $id, $type = "div"){
		$this->targetFrame = $targetFrame;
		$this->containerId = $id;
		$this->containerType = $type;
	}

	function getJS(){
		return (!defined('weOrderContainer_JS_loaded') && define('weOrderContainer_JS_loaded', true) ?
				we_html_element::jsScript(JS_DIR . '/weOrderContainer.js') :
				''
			) .
			we_html_element::jsElement('var ' . $this->containerId . ' = new weOrderContainer("' . $this->containerId . '");');
	}

	function getContainer($attribs = []){

		$attrib = '';
		foreach($attribs as $name => $value){
			$attrib .= ' ' . $name . "=\"" . $value . "\"";
		}

		$src = '<' . $this->containerType . ' id="' . $this->containerId . '"' . $attrib . '>'
			. '</' . $this->containerType . '>';

		return $src;
	}

// end: getContainer

	function getCmd($mode, $uniqueid = false, $afterid = false){
		$prefix = 'container';
		$afterid = ($afterid ? "'" . $afterid . "'" : "null");

		switch(strtolower($mode)){
			case 'add':
				$cmd = $prefix . ".add(document, '" . $uniqueid . "', $afterid);";
				break;
			case 'reload':
				$cmd = $prefix . ".reload(document, '" . $uniqueid . "');";
				break;
			case 'delete':
			case 'del':
				$cmd = $prefix . ".del('" . $uniqueid . "');";
				break;
			case 'moveup':
			case 'up':
				$cmd = $prefix . ".up('" . $uniqueid . "');";
				break;
			case 'movedown':
			case 'down':
				$cmd = $prefix . ".down('" . $uniqueid . "');";
				break;
			default:
				$cmd = "";
				break;
		}

		return $cmd;
	}

// end: getCmd

	function getResponse($mode, $uniqueid, $string = "", $afterid = false, $js = ''){
		if(!($cmd = $this->getCmd($mode, $uniqueid, $afterid))){
			return "";
		}

		return ($string != "" ?
				'<' . $this->containerType . ' id="' . $this->containerId . '" style="display: none;">'
				. $string
				. '</' . $this->containerType . '>' : '') .
			we_html_element::jsElement('
var targetF=' . $this->targetFrame . ';
var container=targetF.' . $this->containerId . ';' .
				$cmd .
				$this->getDisableButtonJS() .
				$js);
	}

// end: getResponse

	function getDisableButtonJS(){
		return '';
//FIXME: this doesn't work
		/*return '
for(i=0; i < top.container.position.length; i++) {
	id = top.container.position[i];
	id = id.replace(/entry_/, "");
	WE().layout.button.enable(targetF.document, "btn_direction_up_" + id);
	WE().layout.button.enable(targetF.document, "btn_direction_down_" + id);
	if(i == 0) {
		WE().layout.button.disable(targetF.document, "btn_direction_up_" + id);
	}
	if(i+1 == top.container.position.length) {
		WE().layout.button.disable(targetF.document, "btn_direction_down_" + id);
	}
}';*/
	}

}
