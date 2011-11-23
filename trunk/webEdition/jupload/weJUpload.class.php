<?php

/**
 * webEdition CMS
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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
class weJUpload{

	var $Params = array();
	var $Buttons = array();

	/* avail langs
	 * ar, 	  ar_SA, 	  bg, 	  cs, 	  da, 	  de, 	  en, 	  eo, 	  es,
	 * fi, 	  fr, 	  hr, 	  hu, 	  il, 	  it, 	  ja, 	  nl, 	  no, 	  pl,
	 * pt_BR, 	  pt, 	  ro, 	  ru, 	  sk, 	  sl, 	  sv, 	  tr, 	  zh, zh_TW
	 */
	function weJUpload($params, $language=''){

		$this->Params = $params;

		if(!empty($language)){
			switch($language){
				default:
				case 'Deutsch':
					$this->Params['land'] = 'de';
					break;
				case 'Dutch':
					$this->Params['lang'] = 'nl';
					break;
				case 'English':
					$this->Params['lang'] = 'en';
					break;
				case 'Finnish':
					$this->Params['lang'] = 'fi';
					break;
				case 'French':
					$this->Params['lang'] = 'fr';
					break;
				case 'Polish':
					$this->Params['lang'] = 'pl';
					break;
				case 'Russian':
					$this->Params['lang'] = 'ru';
					break;
				case 'Spanish':
					$this->Params['lang'] = 'es';
					break;
			}
		}
	}

	function addParam($name, $value){
		$this->Params[$name] = $value;
	}

	function getAppletTag($content='', $w=300, $h=300){

		$_params = '';

		foreach($this->Params as $name => $value){
			$_params .= '<param name="' . $name . '" value="' . $value . '">
				';
		}

		return '
			<applet	name="JUpload" code="wjhk.jupload2.JUploadApplet" archive="/webEdition/jupload/jupload.jar" width="' . $w . '" height="' . $h . '" mayscript scriptable>
				' . $_params . '
				' . $content . '
			</applet>
			';
	}

	function getJS(){

		return '';
	}

	function getButtons($buttons, $order='h', $space=5){

		include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_classes/html/we_button.inc.php');
		$we_button = new we_button();

		$_buttons = array();

		foreach($buttons as $button){
			switch($button){
				case 'add':
					$_buttons[] = $we_button->create_button("add", "javascript:if(document.JUpload.jsIsReady()) document.JUpload.jsClickAdd();");
					break;
				case 'remove':
					$_buttons[] = $we_button->create_button("delete", "javascript:if(document.JUpload.jsIsReady()) document.JUpload.jsClickRemove();");
					break;
				case 'upload':
					$_buttons[] = $we_button->create_button("upload", "javascript:if(document.JUpload.jsIsReady()) document.JUpload.jsClickUpload();");
					break;
			}
		}

		if($order == 'h'){
			return $we_button->create_button_table($_buttons, $space);
		} else{
			return '<div style="margin-bottom: ' . $space . 'px;">' . implode('</div><div style="margin-bottom: ' . $space . 'px;">', $_buttons) . '</div>';
		}
	}

}
