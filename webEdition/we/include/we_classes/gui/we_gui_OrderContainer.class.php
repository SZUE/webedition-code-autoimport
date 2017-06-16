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
abstract class we_gui_OrderContainer{

	private static function getCmd($mode, $uniqueid = false, $afterid = false){
		// FIXME: call generic we_cmd 'objectField_action, action=mode, uniqueid, afterid'
		$afterid = ($afterid ? "'" . $afterid . "'" : "null");

		switch(strtolower($mode)){
			case 'add':
				return "container.add(document, '" . $uniqueid . "', $afterid);";
			case 'reload':
				return "container.reload(document, '" . $uniqueid . "');";
			case 'delete':
			case 'del':
				return "container.del('" . $uniqueid . "');";
			case 'moveup':
			case 'up':
				return "container.up('" . $uniqueid . "');";
			case 'movedown':
			case 'down':
				return "container.down('" . $uniqueid . "');";
			default:
				return "";
		}
	}

	public static function getResponse($mode, $uniqueid, $content = '', $afterid = false){
		if(!($cmd = self::getCmd($mode, $uniqueid, $afterid))){
			return '';
		}

		return (!$content ? '' : '<div id="orderContainer" style="display: none;">' . $content . '</div>') .
			we_html_element::jsElement('var container=_EditorFrame.getContentEditor().orderContainer;' .
				$cmd .
				self::getDisableButtonJS()
			);
	}

// end: getResponse

	private static function getDisableButtonJS(){
		return '';
//FIXME: this doesn't work
		/* return '
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
		  }'; */
	}

}
