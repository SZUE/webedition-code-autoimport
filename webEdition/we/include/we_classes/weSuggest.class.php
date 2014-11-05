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

/**
 * Klasse f�r Autocomleter
 *
 * $yuiSuggest =& weSuggest::getInstance();																											// Die Kalsse instanzieren.
 * echo $yuiSuggest->getYuiFiles																										// Die notwendigen YUI-JS-Dateien werden an einer passenden Stelle eingebunden
 * echo $yuiSuggest->createAutocompleter(																								// GUI-Element mit Input-Feld und Auswahl-Button
 * 			"Doc", 																														// AC-Id
 * 			we_button::create_button("select", "javascript:select_seem_start()", true, 100, 22, "", "", false, false),					// Auswahl-Button
 * 			we_html_tools::htmlTextInput("seem_start_document_name", 11, $_document_path, "", " id='yuiAcInputDoc'", "text", 190, 0, "", false),		// Input-Feld
 * 			'yuiAcInputDoc',																											// Input-Feld-Id. Die Id besteht aus 'yuiAcInput' und AC-Id
 * 			we_html_element::htmlHidden(array("name" => "seem_start_document", "value" => $_document_id, "id"=>"yuiAcResultDoc")),		// Result-Field (hidden) für die Document-, Folder-, Object-,...ID
 * 			'yuiAcResultDoc', 																											// Result-Feld-Id. Die Id besteht aus 'yuiAcResult' und AC-Id
 * 			'',																															// Label: steht über dem Inputfeld
 * 			FILE_TABLE, 																												// Name der Tabele in für die Query
 * 			"folder,text/webedition,image/*,text/js,text/css,text/html,application/*,video/quicktime", 													// ContentTypen für die Query: sie entsprechende Tabele
 * 			"docSelector", 																												// docSelector | dirSelector : ob nach folder oder doc gesucht wird
 * 			20, 																														// Anzahl der Vorschläge
 * 			0, 																															// Verzögerung für das auslösen des AutoCompletion
 * 			true, 																														// Soll eine Ergebnisüberprüfung stattfinden
 * 			"190", 																														// Container-Breite
 * 			"true",																														// Feld darf leer bleiben
 * 			10																															// Abstand zwischen Input-Feld und Button
 * 		);
 * echo $yuiSuggest->getYuiCode																											// Generieter CSS- und JS-Code
 */
class weSuggest{
	const DocSelector = 'docSelector';
	const DirSelector = 'dirSelector';

	var $inputfields = array();
	var $containerfields = array();
	var $containerwidth = array();
	var $tables = array();
	var $rootDirs = array();
	var $contentTypes = array();
	var $weMaxResults = array();
	var $queryDelay = array();
	var $layer = array();
	var $setOnSelectFields = array();
	var $checkFieldsValues = array();
	var $selectors = array();
	var $ct = array();
	var $inputMayBeEmpty = array();
	var $_doOnItemSelect = array();
	var $_doOnTextfieldBlur = array();
	var $preCheck = "";
	/*	 * ************************************* */
	var $acId = '';
	var $checkFieldValue = true;
	var $containerWidth = '';
	var $containerWidthForAll = 0;
	var $contentType = "folder";
	var $inputAttribs = 0;
	var $inputDisabled = 0;
	var $inputId = '';
	var $inputName = '';
	var $inputValue = '';
	var $label = '';
	var $maxResults = 20;
	var $mayBeEmpty = 1;
	var $resultName = '';
	var $resultValue = '';
	var $resultId = '';
	var $rootDir = '';
	var $selectButton = '';
	var $selectButtonSpace = '';
	var $selector = "Dir"; //FIXME: self::DirSelector???
	var $trashButton = '';
	var $trashButtonSpace = '';
	var $openButton = '';
	var $openButtonSpace = '';
	var $createButton = '';
	var $createButtonSpace = '';
	var $table = FILE_TABLE;
	var $width = 280;
	/*	 * ************************************* */
	var $addJS = '';
	var $doOnItemSelect = '';
	var $doOnTextfieldBlur = '';
	private static $giveStatic = true;

	static function &getInstance(){
		static $inst = null;
		if(!is_object($inst)){
			$inst = new self();
		}
		if(self::$giveStatic){
			return $inst;
		}
		$void = new self();
		return $void;
	}

	function getErrorMarkPlaceHolder($id = "errormark", $space = 3, $w = 4, $h = 20){
		$s = $w + $space;
		return '<img id="' . $id . '" src="' . ICON_DIR . 'errormark.gif" width="' . $w . '" height="' . $h . '" border="0" style="position:relative; left:-' . $s . 'px; visibility: hidden;' . (we_base_browserDetect::isIE() ? 'top:4px; z-index:1000000' : '') . '" />';
	}

	static function getYuiFiles(){
		return self::getYuiCssFiles() . self::getYuiJsFiles();
	}

	/**
	 * This function returns the required js files from the YUI library
	 *
	 * @return String
	 */
	static function getYuiJsFiles(){
		return we_html_element::jsScript(JS_DIR . 'libs/yui/yahoo-min.js') .
			we_html_element::jsScript(JS_DIR . 'libs/yui/dom-min.js') .
			we_html_element::jsScript(JS_DIR . 'libs/yui/event-min.js') .
			we_html_element::jsScript(JS_DIR . 'libs/yui/datasource-min.js') .
			we_html_element::jsScript(JS_DIR . 'libs/yui/connection-min.js') .
			we_html_element::jsScript(JS_DIR . 'libs/yui/animation-min.js') .
			we_html_element::jsScript(JS_DIR . 'libs/yui/json-min.js') .
			we_html_element::jsScript(JS_DIR . 'libs/yui/autocomplete-min.js') .
			we_html_element::jsScript(JS_DIR . 'utils/we_cmd_encode.js');
	}

	function getYuiCode(){
		return self::getYuiCss() . self::getYuiJs();
	}

	/**
	 * This function returns the required css files from the YUI library
	 *
	 * @return String
	 */
	static function getYuiCssFiles(){
		return '';
	}

	/**
	 * This function generates the individual js code for the autocomletion
	 *
	 * @return String
	 */
	function getYuiJs(){
		/**
		 * @todo 	1. value
		 * 			2. table
		 * 			3. contenttype
		 * 			4. ?
		 * 			5. id
		 */
		$weSelfContentType = (isset($GLOBALS['we_doc']) && isset($GLOBALS['we_doc']->ContentType)) ? $GLOBALS['we_doc']->ContentType : '';
		$weSelfID = (isset($GLOBALS['we_doc']) && isset($GLOBALS['we_doc']->ID)) ? $GLOBALS['we_doc']->ID : '';

		if(is_array($this->inputfields) && empty($this->inputfields)){
			return;
		}

		$safariEventListener = '';
		$initVars = '	var ajaxMaxResponseTime = 1500;
			var ajaxResponseStep = 100;
			var ajaxResponseCT = 0;
			var countMark = 0;
			var ajaxURL = "' . WEBEDITION_DIR . 'rpc/rpc.php";';
		// WORKSPACES
		$weFieldWS = 'var weWorkspacePathArray = new Array();';
		// AC-FIEDS BY ID
		$fildsById = 'var yuiAcFieldsById = new Array();';
		// AC-FIEDS
		$fildsObj = 'var yuiAcFields = {';
		$invalidFields = <<<HTS

			if(parent && parent.weAutoCompetionFields && parent.weAutoCompetionFields.length>0) {
				for(i=0; i< parent.weAutoCompetionFields.length; i++) {
					if(parent.weAutoCompetionFields[i] && parent.weAutoCompetionFields[i].id && !parent.weAutoCompetionFields[i].valid) {
						YAHOO.autocoml.markNotValid(i);
					}
				}
			}
HTS;

		$oACDSInit = $oAutoCompInit = $oAutoCompRes = $declare = $onSelect = $onBlur = $onFocus = $doAjax = $weAcFields = '';
		$postData = 'protocol=text&cmd=SelectorGetSelectedId';
		// loop fields
		for($i = 0; $i < count($this->inputfields); $i++){
			$safariEventListener .= "YAHOO.util.Event.addListener('" . $this->inputfields[$i] . "','blur',YAHOO.autocoml.doSafariOnTextfieldBlur_$i);";
			$weErrorMarkId = str_replace("Input", "ErrorMark", $this->inputfields[$i]);
			$weWorkspacePathArray = makeArrayFromCSV(id_to_path(get_ws($this->tables[$i]), $this->tables[$i]));
			$weWorkspacePathArrayJS = '';
			if(is_array($weWorkspacePathArray)){
				$ix = 0;
				foreach($weWorkspacePathArray as $val){
					if($ix > 0){
						$weWorkspacePathArrayJS .= ',';
					}
					$weWorkspacePathArrayJS .= '"' . $val . '"';
					$ix++;
				}
			}
			$weFieldWS .= 'weWorkspacePathArray[' . $i . '] = new Array(' . $weWorkspacePathArrayJS . ');';

			$weAcFields .= <<<HTS

			if((inst == -1 || inst == 'set_$i') && parent && parent.weAutoCompetionFields && !parent.weAutoCompetionFields[$i]) {
				parent.weAutoCompetionFields[$i] = {
					'id' : yuiAcFields.set_$i.id,
					'valid' : true,
					'cType' : yuiAcFields.set_$i.cType
				}
			}
HTS;

			$fildsById .= "	yuiAcFieldsById['" . $this->inputfields[$i] . "']={'index':'" . $i . "','set':'set_$i'};";
			$fildsObj .=
				($i > 0 ? ',' : '') . "'set_$i': {
			'id' : '" . $this->inputfields[$i] . "',
			'old': document.getElementById('" . $this->inputfields[$i] . "').value,
			'selector': '" . $this->selectors[$i] . "',
			'sel': '',
			'newval': null,
			'run': false,
			'found': 0,
			'cType': '',
			'valid': true,
			'countMark': 0,
			'changed': false,
			'table': '" . $this->tables[$i] . "',
			'rootDir': '" . $this->rootDirs[$i] . "',
			'cTypes': '" . $this->contentTypes[$i] . "',
			'workspace': new Array(" . $weWorkspacePathArrayJS . "),
			'mayBeEmpty': " . ($this->inputMayBeEmpty[$i] ? "true" : "false");
			$oACDSInit .= ($i > 0 ? ", " : "") . 'oACDS_' . $i;
			$oAutoCompInit .= ($i > 0 ? ", " : "") . 'oAutoComp_' . $i;
			$oAutoCompRes .= "	var oAutoCompRes_$i = new Array();";

			if(isset($this->setOnSelectFields[$i]) && is_array($this->setOnSelectFields[$i])){
				$initVars .= "	var selInputVal_" . $i . ";";
				$onSelectInit = "";
				$onSelectDecl = "";
				if(!empty($this->setOnSelectFields[$i])){
					$fildsObjId = ",			'fields_id': new Array(";
					$fildsObjVal = ",			'fields_val': new Array(";
					for($j = 0; $j < count($this->setOnSelectFields[$i]); $j++){
						if($j > 0){
							$fildsObjId .= ",";
							$fildsObjVal .= ",";
						}
						$fildsObjId .= "'" . $this->setOnSelectFields[$i][$j] . "'";
						$fildsObjVal .= "document.getElementById('" . $this->setOnSelectFields[$i][$j] . "').value";
						$onSelectInit .= "var yuiAcOnSelectField_" . $j . ";";
						$onSelectDecl .=
							"if ((yuiAcOnSelectField_" . $j . " = document.getElementById('" . $this->setOnSelectFields[$i][$j] . "')) && (typeof(params[" . (3) . "])!=undefined)) {
										yuiAcOnSelectField_" . $j . ".value = params[" . (3) . "];
							}";
					}
					$fildsObj .= $fildsObjId . ")" . $fildsObjVal . ")";
				}
				$onSelect .= <<<HTS

		doOnItemSelect_$i: function(param1,param2) {
			param=param2.toString();
			params=param.split(',');
			$onSelectInit
			if((yuiAcFields.set_$i.selector=='docSelector'||yuiAcFields.set_$i.selector =='Docselector')&&params[4]=='folder') {
				yuiAcFields.set_$i.valid = false;
				yuiAcFields.set_$i.cType = params[4];
			} else {
				yuiAcFields.set_$i.valid=true;
				yuiAcFields.set_$i.sel = params[3];
				yuiAcFields.set_$i.cType = params[4];
				YAHOO.autocoml.unmarkNotValid($i);
				$onSelectDecl
			}
			yuiAcFields.set_$i.found = 1;
			yuiAcFields.set_$i.run = false;
			selInputVal_$i = document.getElementById(yuiAcFields.set_$i.id).value;
			yuiAcFields.set_$i.newval = document.getElementById(yuiAcFields.set_$i.id).value;
			inputID = yuiAcFields.set_$i.id;
			resultID = yuiAcFields.set_$i.fields_id[0];
			{$this->_doOnItemSelect[$i]}
		},


		doOnDataRequestEvent_$i: function() {
			yuiAcFields.set_$i.found = 0;
			yuiAcFields.set_$i.run = true;
			yuiAcFields.set_$i.changed = true;
		},


		doOnDataReturnEvent_$i: function(param1,param2) {
			param=param2.toString();
			params=param.split(',');
			if(params.length<4) {
				if(document.getElementById('{$this->inputfields[$i]}').value == "/" && (yuiAcFields.set_$i.selector == "dirSelector" || yuiAcFields.set_$i.selector == "Dirselector" || yuiAcFields.set_$i.selector == "selector")) {
					document.getElementById(yuiAcFields.set_$i.fields_id[0]).value = '0';
					YAHOO.autocoml.unmarkNotValid($i);
					if(parent && parent.weAutoCompetionFields) parent.weAutoCompetionFields[$i].valid = true;
				} else if (document.getElementById('{$this->inputfields[$i]}').value =="" && (yuiAcFields.set_$i.selector == "docSelector" || yuiAcFields.set_$i.selector == "Docselector") && yuiAcFields.set_{$i}.mayBeEmpty) {
					document.getElementById(yuiAcFields.set_$i.fields_id[0]).value = "";
					YAHOO.autocoml.unmarkNotValid($i);
					if(parent && parent.weAutoCompetionFields) parent.weAutoCompetionFields[$i].valid = true;
				} else {
					YAHOO.autocoml.markNotValid($i);
					if(parent && parent.weAutoCompetionFields) parent.weAutoCompetionFields[$i].valid = false;

				}
			} else {
				document.getElementById('" . $weErrorMarkId . "').style.visibility = 'hidden';
				if(parent && parent.weAutoCompetionFields) parent.weAutoCompetionFields[$i].valid = true;
			}
			yuiAcFields.set_$i.run = false;
		},


		doOnDataErrorEvent_$i: function() {
			yuiAcFields.set_$i.run = false;
			yuiAcFields.set_$i.valid = false;
		},


		doOnDataReturnEvent_$i: function() {
			yuiAcFields.set_$i.run = false;
		},


		doOnUnmatchedItemSelectEvent_$i: function() {
			yuiAcFields.set_$i.run = false;
		},


HTS;
			}
			if(isset($this->checkFieldsValues[$i]) && $this->checkFieldsValues[$i]){
				$initVars .= "	var oldInputVal_" . $i . ";
					var newInputVal_" . $i . ";";
				$additionalFields = "";
				if(isset($this->setOnSelectFields[$i]) && is_array($this->setOnSelectFields[$i])){
					for($j = 0; $j < count($this->setOnSelectFields[$i]); $j++){
						$initVars .= "	var old_" . str_replace('-', '_', $this->setOnSelectFields[$i][$j]) . ";";
						$additionalFields .= ($j > 0 ? "," : "") . str_replace('-', '_', $this->setOnSelectFields[$i][$j]);
					}
				}
				$onBlur .= <<<HTS
		doSafariOnTextfieldBlur_$i: function(e) {
			YAHOO.autocoml.doOnTextfieldBlur_$i();
		},


		doOnTextfieldBlur_$i: function() {
			//document.getElementById(yuiAcFields.set_$i.id).blur();
			wsValid_$i = true;
			if(yuiAcFields.set_{$i}.workspace.length > 0) {
				wsValid_$i = false;
				var wsPathInput_$i = document.getElementById(yuiAcFields.set_$i.id).value;
				for(i=0; i<yuiAcFields.set_{$i}.workspace.length; i++) {
					if(wsPathInput_$i.length >= yuiAcFields.set_{$i}.workspace[i].length) {
						if(wsPathInput_$i.substr(0,yuiAcFields.set_{$i}.workspace[i].length) == yuiAcFields.set_{$i}.workspace[i]){
							wsValid_$i = true;
						}
					}
				}
			}
			var rootDirValid_$i = (yuiAcFields.set_$i.rootDir !== '' && document.getElementById(yuiAcFields.set_$i.id).value.indexOf(yuiAcFields.set_$i.rootDir) !== 0) ? false : true;
			if(document.getElementById(yuiAcFields.set_$i.id).value =="/" && (yuiAcFields.set_$i.selector == "dirSelector"|| yuiAcFields.set_$i.selector == "Dirselector"|| yuiAcFields.set_$i.selector == "selector") && wsValid_$i && rootDirValid_$i) {
				document.getElementById(yuiAcFields.set_{$i}.fields_id[0]).value = '0';
				yuiAcFields.set_$i.newval = '/';
				yuiAcFields.set_$i.run = false;
				YAHOO.autocoml.unmarkNotValid($i);
			} else if (document.getElementById(yuiAcFields.set_$i.id).value =="" && (yuiAcFields.set_{$i}.selector == "docSelector" || yuiAcFields.set_{$i}.selector == "Docselector" || yuiAcFields.set_$i.selector == "dirSelector" || yuiAcFields.set_$i.selector == "Dirselector" || yuiAcFields.set_$i.selector == "selector") && yuiAcFields.set_$i.mayBeEmpty) {
				document.getElementById(yuiAcFields.set_$i.fields_id[0]).value = "";
				yuiAcFields.set_$i.run = false;
				YAHOO.autocoml.unmarkNotValid($i);
			} else {
				switch(true) {
					case (!rootDirValid_$i):                              // ERROR: Not valid rootDir
						debug('Not valid rootDir');
						YAHOO.autocoml.markNotValid($i);
						break;
					case (!wsValid_$i):                                   // ERROR: Not valid workspace
						debug('Not valid workspace');
						YAHOO.autocoml.markNotValid($i);
						break;
					case (ajaxResponseCT > ajaxMaxResponseTime):          // ERROR: No respone - timeout
						debug('Timeout');
						YAHOO.autocoml.markNotValid($i);
						break;
					case (yuiAcFields.set_$i.run):                        // ERROR: Request is running
						debug('Running');
						ajaxResponseCT +=ajaxResponseStep;
						setTimeout('YAHOO.autocoml.doOnTextfieldBlur_$i()',ajaxResponseStep);
						break;
					case (yuiAcFields.set_$i.found == 2):                 // ERROR: No result found
						debug('No reault');
						YAHOO.autocoml.markNotValid($i);
						break;
					case (yuiAcFields.set_$i.found == 0):                 // ERROR: Nothing found
						debug('found=0');
						newInputVal_$i = document.getElementById(yuiAcFields.set_$i.id).value;
						if(newInputVal_$i != selInputVal_$i || newInputVal_$i != oldInputVal_$i) {
							yuiAcFields.set_$i.run = true;
							YAHOO.autocoml.doAjax(ajaxCallback_$i, '$postData&we_cmd[1]='+newInputVal_$i+'&we_cmd[2]='+yuiAcFields.set_{$i}.table+'&we_cmd[3]={$this->contentTypes[$i]}&we_cmd[4]={$additionalFields}&we_cmd[5]=$i');
							setTimeout('YAHOO.autocoml.doOnTextfieldBlur_$i()',ajaxResponseStep);
						}
						break;
					case ((yuiAcFields.set_$i.selector == "docSelector" || yuiAcFields.set_$i.selector == "Docselector") && yuiAcFields.set_$i.cType=="folder") :   // ERROR: Wrong type
						debug('folder');
						YAHOO.autocoml.markNotValid($i);
						break;
					default:
						debug('checkfields');
						YAHOO.autocoml.checkFields();
				}
			}
			if (typeof _EditorFrame != 'undefined' && yuiAcFields.set_$i.old != yuiAcFields.set_$i.newval && yuiAcFields.set_$i.newval!=null) {
				_EditorFrame.setEditorIsHot(true);
				//don't match again, since on save frame is not reloaded
				yuiAcFields.set_$i.old=yuiAcFields.set_$i.newval;
			}
			inputID = yuiAcFields.set_$i.id;
			resultID = yuiAcFields.set_$i.fields_id[0];
			{$this->_doOnTextfieldBlur[$i]}
			yuiAcFields.set_$i.changed=false;
		},


		doOnContainerCollapse_$i: function(){
			//setTimeout('YAHOO.autocoml.doOnTextfieldBlur_$i()',100);
		},
HTS;

				$onFocus .= "		doOnTextfieldFocus_" . $i . ": function() {
							ajaxResponseCT=0;
							oldInputVal_" . $i . " = document.getElementById('" . $this->inputfields[$i] . "').value;";
				if(isset($this->setOnSelectFields[$i]) && is_array($this->setOnSelectFields[$i])){
					for($j = 0; $j < count($this->setOnSelectFields[$i]); $j++){
						$onFocus .= "			old_" . $this->setOnSelectFields[$i][$j] . " = document.getElementById('" . $this->setOnSelectFields[$i][$j] . "').value;";
					}
				}

				//$onFocus .= "			YAHOO.autocoml.unmarkNotValid($i);";
				$onFocus .= "			if(parent && parent.weAutoCompetionFields) parent.weAutoCompetionFields[yuiAcFields.set_{$i}.id] = false;" .
					"			yuiAcFields.set_$i.set = '';" .
					"		},";
				$doAjax .= <<<HTS

		doAjax: function(callback, postdata) {
			var request = YAHOO.util.Connect.asyncRequest('POST', ajaxURL, callback, postdata);
		},


HTS;

				$initVars .= <<<HTS
	var ajaxCallback_$i = {
		success: function(o) {
			if(o.responseText != undefined && o.responseText != ''){
				eval(o.responseText);
				if(weResponse.type=='error') {
					//for (i=0; i < yuiAcFields.set_$i.fields_id.length; i++) {
						document.getElementById(yuiAcFields.set_$i.fields_id[0]).value = yuiAcFields.set_$i.fields_val[0];
					//}
					yuiAcFields.set_$i.found = 2;
					yuiAcFields.set_$i.newval='';
					YAHOO.autocoml.markNotValid($i);
					yuiAcFields.set_$i.newval = '';
				} else {
					if(weResponse.data.contentType=='folder' && (yuiAcFields.set_{$i}.selector == 'docSelector' || yuiAcFields.set_{$i}.selector == 'Docselector')) {
						document.getElementById(weResponse.data.id).value = "";
						YAHOO.autocoml.markNotValid($i);
						yuiAcFields.set_$i.newval = '';
					} else {
						document.getElementById(weResponse.data.id).value = weResponse.data.value;
						YAHOO.autocoml.unmarkNotValid($i);
						yuiAcFields.set_$i.newval = document.getElementById(yuiAcFields.set_$i.id).value;
					}
					yuiAcFields.set_$i.found = 1;
				}
			}
			yuiAcFields.set_$i.run = false;
		},
		failure: function(o) {
			for (i=1; i < yuiAcFields.set_$i.fields_id.length; i++) {
				document.getElementById(yuiAcFields.set_$i.fields_id[i]).value = yuiAcFields.set_$i.fields_val;
			}
			yuiAcFields.set_$i.run = false;
			yuiAcFields.set_{$i}.valid=false;
			YAHOO.autocoml.markNotValid($i);
			yuiAcFields.set_$i.newval = '';
		}
	};


HTS;
			}

			// EOF loop fields

			$fildsObj .= "		}";
			$declare .=

				'if(inst == -1 || inst == "set_' . $i . '"){
				oACDS_' . $i . ' = null;
				oACDS_' . $i . ' = new YAHOO.widget.DS_XHR(ajaxURL, ["\n", "\t"]);
				oACDS_' . $i . '.responseType = YAHOO.widget.DS_XHR.TYPE_FLAT;
				oACDS_' . $i . '.maxCacheEntries = 60;
				oACDS_' . $i . '.queryMatchSubset = false;
				oACDS_' . $i . '.scriptQueryAppend  = "protocol=text&cmd=SelectorSuggest&we_cmd[2]="+yuiAcFields.set_' . $i . '.table+"&we_cmd[3]="+yuiAcFields.set_' . $i . '.cTypes+"&we_cmd[4]=' . $weSelfContentType . '&we_cmd[5]=' . $weSelfID . '&we_cmd[6]="+yuiAcFields.set_' . $i . '.rootDir;
				oACDS_' . $i . '.scriptQueryParam  = "we_cmd[1]";
				var myInput = document.getElementById("' . $this->inputfields[$i] . '");
				var myContainer = document.getElementById("' . $this->containerfields[$i] . '");
				if(oAutoComp_' . $i . ' !== undefined){
					oAutoComp_' . $i . '.destroy();
				}
				oAutoComp_' . $i . ' = new YAHOO.widget.AutoComplete(myInput,myContainer,oACDS_' . $i . ');
				oAutoComp_' . $i . '.maxResultsDisplayed = ' . $this->weMaxResults[$i] . ';
				oAutoComp_' . $i . '.queryDelay = 0;';
			if(isset($this->setOnSelectFields[$i]) && is_array($this->setOnSelectFields[$i])){
				$declare .= 'oAutoComp_' . $i . '.itemSelectEvent.subscribe(YAHOO.autocoml.doOnItemSelect_' . $i . ');' .
					'oAutoComp_' . $i . '.dataRequestEvent.subscribe(YAHOO.autocoml.doOnDataRequestEvent_' . $i . ');' .
					'oAutoComp_' . $i . '.dataReturnEvent.subscribe(YAHOO.autocoml.doOnDataReturnEvent_' . $i . ');' .
					'oAutoComp_' . $i . '.unmatchedItemSelectEvent.subscribe(YAHOO.autocoml.doOnUnmatchedItemSelectEvent_' . $i . ');' .
					'oAutoComp_' . $i . '.dataErrorEvent.subscribe(YAHOO.autocoml.doOnDataErrorEvent_' . $i . ');' .
					'oAutoComp_' . $i . '.dataReturnEvent.subscribe(YAHOO.autocoml.doOnDataReturnEvent_' . $i . ');';
			}
			if(isset($this->checkFieldsValues[$i]) && $this->checkFieldsValues[$i]){
				$declare .= 'oAutoComp_' . $i . '.textboxBlurEvent.subscribe(YAHOO.autocoml.doOnTextfieldBlur_' . $i . ');' .
					'oAutoComp_' . $i . '.textboxFocusEvent.subscribe(YAHOO.autocoml.doOnTextfieldFocus_' . $i . ');' .
					'oAutoComp_' . $i . '.containerCollapseEvent.subscribe(YAHOO.autocoml.doOnContainerCollapse_' . $i . ');';
			}
			$declare .= '
				oAutoComp_' . $i . '.formatResult = function(oResultItem, sQuery) {
					var sKey = oResultItem[0];
					var nQuantity = oResultItem[1];
					var sKeyQuery = sKey.substring(0, sQuery.length);
					if(sQuery.length>10){
						var path=sKeyQuery.split("/");
						var pPart="/"+path[path.length-1];
						if(pPart.length>' . intval($this->width / 15) . '){
							pPart=pPart.substring(pPart.length-10,pPart.length);
						}
						sKeyQuery ="&hellip;"+pPart;
					}
					var sKeyRemainder = sKey.substr(sQuery.length);
					oAutoCompRes_' . $i . '[sKeyQuery] = oResultItem[2];
					var aMarkup = ["<div id=\'ysearchresult\'><div class=\'ysearchquery\'>",
					//nQuantity,
					"</div><span style=\'font-weight:bold\'>",
					sKeyQuery,
					"</span>",
					sKeyRemainder,
					"</div>"];
					return (aMarkup.join(""));
				};
			}
			';
		}

		/* $declare .= <<<HTS
		  if(parent && parent.weAutoCompetionFields && parent.weAutoCompetionFields.length>0) {
		  for(arrayIndex in parent.weAutoCompetionFields) {
		  if(parent.weAutoCompetionFields[arrayIndex] ) YAHOO.autocoml.markNotValid(i);
		  }
		  }
		  HTS; */

		$fildsObj .= "	};";
		return we_html_element::jsElement("
YAHOO.autocoml = function(){
$weFieldWS
$fildsById
$fildsObj
$initVars
	var $oACDSInit;
	var $oAutoCompInit;
	$oAutoCompRes

	return {
$onSelect
$onBlur
$onFocus
$doAjax
		init: function(param,inst) {
			inst = inst === undefined ? -1 : inst;
			$declare
			$weAcFields
			$invalidFields
		},
		validateForm: function() {
			// Validate form inputs here
			return false;
		},
		checkFields: function() {
			for(i=0; i<" . count($this->inputfields) . "; i++) {
				set = eval('yuiAcFields.set_'+i);
				if(!set.valid) {
					document.getElementById(set.fields_id[0]='');
					YAHOO.autocoml.markNotValid(i);
				}
			}
			return true;
		},
		checkRunnigProcess: function() {
			for(i=0; i<" . count($this->inputfields) . "; i++) {
				set = eval('yuiAcFields.set_'+i);
				if(set.run) {
					return true;
				}
			}
			return false;
		},
		markNotValid: function(setNr) {
			set = eval('yuiAcFields.set_'+setNr);
			set.valid = false;
			set.run = false;
			var _elem = document.getElementById(set.id);
			if (_elem != null) {
				weInputAppendClass(_elem, 'weMarkInputError');
			}
			if(parent && parent.weAutoCompetionFields) parent.weAutoCompetionFields[setNr].valid = false;
		},
		unmarkNotValid: function(setNr) {
			set = eval('yuiAcFields.set_'+setNr);
			set.valid = true;
			set.run = false;
			set.found = 1;
			var _elem = document.getElementById(set.id);
			if (_elem != null) {
				weInputRemoveClass(_elem, 'weMarkInputError');
			}
			if(parent && parent.weAutoCompetionFields) parent.weAutoCompetionFields[setNr].valid = true;
		},
		checkACFields: function() {
			if(YAHOO.autocoml.checkRunnigProcess()) return {'running':true};
			for(i=0; i<" . count($this->inputfields) . "; i++) {
				set = eval('yuiAcFields.set_'+i);
				if(!set.valid) {
					return {'running':false, 'valid':false};
				}
			}
			return {'running':false, 'valid':true};
		},

		selectorSetValid: function(setFieldId) {
			for(i=0; i<" . count($this->inputfields) . "; i++) {
				set = eval('yuiAcFields.set_'+i);
				if(set.id==setFieldId) {
					YAHOO.autocoml.unmarkNotValid(i);
				}
			}
		},

		checkOnContainerCollapse: function(setNr) {
			set = eval('yuiAcFields.set_'+setNr);
			if(set.set=='') {
			}
		},

		modifySetById: function(fId,param,value){
			set = yuiAcFieldsById[fId].set;
			if(typeof param === 'object'){
				for(var name in param){
					yuiAcFields[set][name] = typeof yuiAcFields[set][name] !== 'undefined' ? param[name] : yuiAcFields[set][name];
				}
			} else{
				yuiAcFields[set][param] = typeof yuiAcFields[set][param] !== 'undefined' ? value : yuiAcFields[set][param];
			}
			YAHOO.autocoml.init(undefined, set);
		},

		getParamById: function(fId,param){
			set = yuiAcFieldsById[fId].set;
			return yuiAcFields[set][param];
		},
		getYuiAcFields: function(){
			return yuiAcFields;
		},
		isValidById: function(fId){
			if(fId) {
				if(YAHOO.autocoml.counter < 10 && yuiAcFields[yuiAcFieldsById[fId].set]['run']) {
					YAHOO.autocoml.counter++;
					setTimeout('YAHOO.autocoml.isValidById(\"'+fId+'\")',100);
				} else {
					YAHOO.autocoml.counter=0;
					return yuiAcFields[yuiAcFieldsById[fId].set]['valid'];
				}
			} else {
				return false;
			}
		},
		counter: 0,

		isValid: function(){
			var isValid = true;
			for(fId in yuiAcFieldsById){
				if( document.getElementById(fId).style.display != 'none' && !yuiAcFields[yuiAcFieldsById[fId].set]['valid']) {
					isValid = false;
				}
			}
			return isValid;
		},
		isRunnigProcess: function(){
			var isRunning = false;
			for(fId in yuiAcFieldsById){
				if( document.getElementById(fId).style.display != 'none' && yuiAcFields[yuiAcFieldsById[fId].set]['run']) {
					isRunning = true;
				}
			}
			return isRunning;
		},
		setValidById: function(fId){
			YAHOO.autocoml.unmarkNotValid(yuiAcFieldsById[fId].index);
			yuiAcFields[yuiAcFieldsById[fId].set]['valid']=true;
		},
		setNotValidById: function(fId){
			YAHOO.autocoml.markNotValid(yuiAcFieldsById[fId].index);
			yuiAcFields[yuiAcFieldsById[fId].set]['valid']=false;
		},

		restoreById: function(fId){
			set = yuiAcFieldsById[fId].set;
			YAHOO.autocoml.markValid(yuiAcFieldsById[fId].index);
			document.getElementById(fId).value  = yuiAcFields[yuiAcFieldsById[fId].set]['old'];
			document.getElementById(yuiAcFields[yuiAcFieldsById[fId].set]['fields_id'][0]).value  = yuiAcFields[yuiAcFieldsById[fId].set]['fields_val'][0];
		},


		setOldVal: function(set) {
		}
	}
}();

YAHOO.util.Event.addListener(this,'load',YAHOO.autocoml.init);
{$this->preCheck}
" . (we_base_browserDetect::isSafari() ? $safariEventListener : "") . "

function weInputAppendClass(inp, cls) {
	if (inp.className) {
		var _class = inp.className;
		var _arr = _class.split(/ /);
		if (!weInputInArray(_arr, cls)) {
			_arr.push(cls);
		}
		var _newCls = _arr.join(' ');
		inp.className =  _newCls;
	} else {
		inp.setAttribute('class', cls);
	}
}

function weInputRemoveClass(inp, cls) {
	if (inp.className) {
		var _class = inp.className;
		var _arr = _class.split(/ /);
		if (weInputInArray(_arr, cls)) {
			var _newArr = new Array();
			var _l = _arr.length;
			for (var i=0; i< _l; i++) {
				if (_arr[i] !== cls) {
					_newArr.push(_arr[i]);
				}
			}

			if (_newArr.length > 0) {
				var _newCls = _newArr.join(' ');
				inp.className = _newCls;
			} else {
				inp.className = null;
			}
		}
	}
}

function weInputInArray(arr, val) {
	var _l = arr.length;
	for (var i=0; i<_l; i++) {
		if (arr[i] === val) {
			return true;
		}
	}
	return false;
}

{$this->addJS}
");
		/*		 * *****************************************************
		  var weShowDebug = true;
		  var debugsizeW=145;
		  var debugsizeH='100%';
		  function debug(text){
		  }
		  function doDebugResizeW(){
		  if(debugsizeW<600) {
		  debugsize=600;
		  document.getElementById('DebugResizeW').innerHTML='&lt;';
		  } else {
		  debugsize=145;
		  document.getElementById('DebugResizeW').innerHTML='&gt;';
		  }
		  document.getElementById('damd').style.width=debugsize;
		  }
		  function doDebugResizeH(){
		  if(debugsizeH=='30px') {
		  debugsizeH='100%';
		  document.getElementById('DebugResizeH').innerHTML='A';
		  } else {
		  debugsizeH='30px';
		  document.getElementById('DebugResizeH').innerHTML='V';
		  }
		  document.getElementById('damd').style.height=debugsizeH;
		  }") . "
		  <div style='display:none; position:absolute; top:0px; width:145px; height:100%; background:yellow; border: 1px solid red; color:red; z-index:10000' id='damd'>
		  <div align='center'><button onclick='document.getElementById(\"debug\").innerHTML=\"\"'>clear</button><button id='DebugResizeW' onclick='doDebugResizeW()'>&gt;</button><button id='DebugResizeH' onclick='doDebugResizeH()'>A</button></div><hr>
		  <div id='debug'></div>
		  </div>
		  "; */
	}

	/**
	 * This function generates the individual css code for the autocomletion
	 *
	 * @return unknown
	 */
	function getYuiCss(){
		$inputfields = "";
		$containerfields = "";
		$yuiAcContent = "";
		$ysearchquery = "";
		$yuiAcShadow = "";
		$ul = "";
		$li = "";
		$yuAcHighlight = "";
		$layer = "";
		$layerZ = "";
		for($i = 0; $i < count($this->inputfields); $i++){
			$inputfields .= ($i > 0 ? ", " : "") . "#" . $this->inputfields[$i];
			$containerfields .= ($i > 0 ? ", " : "") . "#" . $this->containerfields[$i];
			$yuiAcContent .= "#" . $this->containerfields[$i] . " .yui-ac-content {position:absolute;left:0px;width:" . (we_base_browserDetect::isIE() ? $this->containerwidth[$i] : ($this->containerwidth[$i] + 4)) . "px;border:1px solid #404040;background:#fff;overflow:hidden;z-index:9050; margin-top:-10px}";
			$ysearchquery .= ($i > 0 ? ", " : "") . "#" . $this->containerfields[$i] . " .ysearchquery";
			$yuiAcShadow .= ($i > 0 ? ", " : "") . "#" . $this->containerfields[$i] . " .yui-ac-shadow";
			$ul .= ($i > 0 ? ", " : "") . "#" . $this->containerfields[$i] . " ul";
			$li .= ($i > 0 ? ", " : "") . "#" . $this->containerfields[$i] . " li";
			$yuAcHighlight .= ($i > 0 ? ", " : "") . "#" . $this->containerfields[$i] . " li.yui-ac-highlight";
		}
		foreach($this->layer as $i => $cur){
			$layer .= ($i > 0 ? ", " : "") . "#" . $cur;
			$layerZ .= "#" . $cur . " {z-index:" . (9010 - $i) . ";}";
		}
		if(we_base_browserDetect::isIE()){
			$out = (!empty($layer) ? "	$layerZ" : '') . "
	$inputfields { width:100%; }
	$containerfields {position:relative; top:0px !important;margin-top:8px; width:100%; z-index:10000 }
	$yuiAcContent
	$ysearchquery {position:absolute;right:10px;color:#808080;z-index:10;}
	$yuiAcShadow {position:absolute;margin:.3em;width:100%;background:#a0a0a0;z-index:9049;}
	$ul { padding:5px 0;margin-left:0px; background-color:#ffffff}
	$li { padding:0 5px;cursor:default;white-space:nowrap; }
	$yuAcHighlight {background:#B5D5FF;}
	div.yui-ac-bd ul, div.yui-ac-bd li{ margin:0px; padding:0px; list-style:none; font-family: Verdana, Arial, sans-serif; font-size: 10px; line-height:11px}
	div.yui-ac-bd ul, div.yui-ac-bd ui{ margin:-5px; margin-top:-5px; padding:0px; list-style:none; font-family: Verdana, Arial, sans-serif; font-size: 10px;}
	div.yuiAcLayer { margin:0px; padding:0px;}
";
		} else {
			$out = (!empty($layer) ?
					"	$layer {position:relative;margin-bottom:1.5em;width:100%;}/* set width of widget here*/" .
					$layerZ :
					'') . "
	$inputfields {position:absolute;width:100%; margin-top:-2px} /* abs for ie quirks */
	$containerfields {position:absolute;top:30px !important;}
	$yuiAcContent
	$ysearchquery {position:absolute;right:10px;color:#808080;z-index:10;}
	$yuiAcShadow {position:absolute;margin:.3em;width:100%;background:#a0a0a0;z-index:9049;}
	$ul { padding:5px 0;margin-left:0px; background-color:#ffffff}
	$li { padding:0 5px;cursor:default;white-space:nowrap; }
	$yuAcHighlight {background:#B5D5FF;}
	div.yui-ac-bd ul, div.yui-ac-bd li{ margin:0px; padding:0px; list-style:none; font-family: Verdana, Arial, sans-serif; font-size: 10px; line-height:11px}
	div.yui-ac-bd ul, div.yui-ac-bd ui{ margin:-7px; margin-top:-5px; padding:0px; list-style:none; font-family: Verdana, Arial, sans-serif; font-size: 10px;}
	div.yuiAcLayer { margin:0px; padding:0px;}";
		}
		return $inputfields ? we_html_element::cssElement($out, array('scoped' => 'scoped')) : '';
	}

	function getHTML(){
		$selectButtonSpace = $this->selectButtonSpace + $this->width - 1;
		$inputId = $this->inputId ? : 'yuiAcInput' . $this->acId;
		$resultId = $this->resultId ? : 'yuiAcResult' . $this->acId;
		$containerWidth = $this->containerWidth ? : $this->width;

		$this->setAutocompleteField($inputId, "yuiAcContainer" . $this->acId, $this->table, $this->contentType, $this->selector, $this->maxResults, 0, "yuiAcLayer" . $this->acId, array($resultId), $this->checkFieldValue, (we_base_browserDetect::isIE() ? $containerWidth : ($containerWidth - 8)), $this->mayBeEmpty, $this->rootDir);
		$inputField = $this->_htmlTextInput($this->inputName, 30, $this->inputValue, "", 'id="' . $inputId . '" ' . $this->inputAttribs, "text", $this->width, 0, "", $this->inputDisabled);
		$resultField = we_html_tools::hidden($this->resultName, $this->resultValue, array('id' => $resultId));
		$autoSuggest = '<div id="yuiAcLayer' . $this->acId . '" class="yuiAcLayer">' . $inputField . '<div id="yuiAcContainer' . $this->acId . '"></div></div>';


		$html = we_html_tools::htmlFormElementTable(
				array(
				"text" => $resultField . $autoSuggest . ($this->selectButton ? we_html_tools::getPixel(intval($selectButtonSpace), 4) : ''),
				"valign" => "top",
				"style" => "height:10px"), $this->label, 'left', 'defaultfont', (
				$this->selectButton ?
					array("text" => '<div style="">' . $this->selectButton . '</div>', "valign" => "top") :
					''
				), we_html_tools::getPixel(intval($this->trashButtonSpace), 4), (
				$this->trashButton ?
					array("text" => '<div style="margin-right:' . $this->trashButtonSpace . 'px">' . $this->trashButton . '</div>', "valign" => "top") :
					''
				), (
				$this->openButton ?
					array("text" => '<div style="margin-right:' . $this->openButtonSpace . 'px">' . $this->openButton . '</div>', "valign" => "top") :
					''
				), (
				$this->createButton ?
					array("text" => '<div style="margin-right:' . $this->createButtonSpace . 'px">' . $this->createButton . '</div>', "valign" => "top") :
					'')
		);

		$this->acId = '';
		$this->containerWidth = '';
		$this->containerWidthForAll = 0;
		$this->contentType = we_base_ContentTypes::FOLDER;
		$this->label = '';
		$this->maxResults = 20;
		$this->mayBeEmpty = 1;
		$this->resultName = '';
		$this->resultValue = '';
		$this->resultId = '';
		$this->selectButton = '';
		$this->selectButtonSpace = '';
		$this->selector = 'Dir'; //FIXME:self::Dirselector??
		$this->trashButton = '';
		$this->trashButtonSpace = '';
		$this->openButton = '';
		$this->openButtonSpace = '';
		$this->createButton = '';
		$this->createButtonSpace = '';
		$this->table = FILE_TABLE;
		$this->width = 280;
		$this->doOnItemSelect = '';
		$this->doOnTextfieldBlur = '';
		return $html;
	}

	function getInputId(){
		return $this->inputId;
	}

	function _htmlTextInput($name, $size = 20, $value = "", $maxlength = "", $attribs = "", $type = "text", $width = 0, $height = 0, $markHot = "", $disabled = false){
		$style = ($width || $height) ? (' style="' . ($width ? ('width: ' . $width . ((strpos($width, "px") || strpos($width, "%")) ? "" : "px") . ';') : '') . ($height ? ('height: ' . $height . ((strpos($height, "px") || strpos($height, "%")) ? "" : "px") . ';') : '') . '"') : '';
		return '<input type="' . trim($type) . '" name="' . trim($name) . '" size="' . abs($size) . '" value="' . oldHtmlspecialchars($value) . '" ' . ($maxlength ? (' maxlength="' . abs($maxlength) . '"') : '') . $attribs . $style . ' />';
	}

	//setter

	function setAcId($val, $rootDir = ""){
		$this->acId = str_replace('-', '_', $val);
		$this->rootDir = $rootDir;
	}

	/**
	 * Additional javascript code
	 *
	 * @param unknown_type $val
	 */
	function setAddJS($val){
		$this->addJS = $val;
	}

	/**
	 * Setts the width of the suggest container. Default is input field width
	 *
	 * @param Int $containerWidth
	 * @param Boolean $containerWidthforAll
	 */
	function setContainerWidth($containerWidth){
		$this->containerWidth = $containerWidth;
	}

	/**
	 * Set the content tye to filter result
	 *
	 * @param unknown_type $val
	 */
	function setContentType($val){
		$this->contentType = $val;
	}

	function setDoOnItemSelect($val){
		$this->doOnItemSelect = $val;
	}

	function setDoOnTextfieldBlur($val){
		$this->doOnTextfieldBlur = $val;
	}

	/**
	 * Set id and value for the input field
	 *
	 * @param String $name
	 * @param String $value
	 * @param Array $attribs
	 * @param Boolean $disabled
	 */
	function setInput($name, $value = "", $attribs = "", $disabled = false, $markHot = ""){
		$this->inputId = '';
		$this->inputName = $name;
		$this->inputValue = $value;
		$this->inputAttribs = "";
		if(isset($attribs) && is_array($attribs)){
			foreach($attribs as $key => $val){
				$key = strtolower($key);
				switch($key){
					case "id":
						$this->inputId = $key;
						break;
					case "onchange":
						$_onchange = 1;
						$this->inputAttribs .= $key . '="' . ($markHot ? 'if(_EditorFrame){_EditorFrame.setEditorIsHot(true);hot=1}' : '') . $val . '" ';
						break;
					case "class":
						$_class = 1;
						$val.=' wetextinput';
					case "onblur":
					case "onfocus":
					default:
						$this->inputAttribs .= $key . '="' . $val . '" ';
				}
			}
			if(!isset($_class)){
				$this->inputAttribs .= 'class="wetextinput" ';
			}
			if(!isset($_onchange)){
				$this->inputAttribs .= ' onchange="' . ($markHot ? 'if(_EditorFrame){_EditorFrame.setEditorIsHot(true);hot=1}; ' : '') . '" ';
			}
		} else {
			$this->inputAttribs = 'class="wetextinput" onchange="' . ($markHot ? 'if(_EditorFrame){_EditorFrame.setEditorIsHot(true);hot=1;}' : '') . '" ';
		}
		if(!$this->inputId){
			$this->setInputId();
		}
		$this->inputDisabled = $disabled;
	}

	function setInputId($val = ''){
		$this->inputId = ($val ? : "yuiAcInput" . $this->acId);
	}

	function setInputName($val){
		$this->inputName = $val;
	}

	function setInputValue($val){
		$this->inputValue = $val;
	}

	function setMaxResults($val){
		$this->maxResults = $val;
	}

	function setCheckFieldValue($val){
		$this->checkFieldValue = $val;
	}

	/**
	 * Flag if the autocompleter my be empty
	 *
	 * @param unknown_type $val
	 */
	function setMayBeEmpty($val){
		$this->mayBeEmpty = $val;
	}

	function setLabel($val){
		$this->label = $val;
	}

	/**
	 * Set name, value and id for the result field
	 *
	 * @param unknown_type $resultID
	 * @param unknown_type $resultValue
	 */
	function setResult($resultName, $resultValue = "", $resultID = ""){
		$this->resultName = $resultName;
		$this->resultId = $resultID;
		$this->resultValue = $resultValue;
	}

	function setResultId($val){
		$this->resultId = $val;
	}

	function setResultName($val){
		$this->resultValue = $val;
	}

	function setResultValue($val){
		$this->resultValue = $val;
	}

	function setSelectButton($val, $space = 20){
		$this->selectButton = $val;
		$this->selectButtonSpace = $space;
	}

	/**
	 * Set the selector
	 *
	 * @param String $val
	 */
	function setSelector($val){
		$this->selector = $val;
	}

	/**
	 * Set the table for query result
	 *
	 * @param unknown_type $val
	 */
	function setTable($val){
		$this->table = $val;
	}

	function setTrashButton($val, $space = 10){
		$this->trashButton = $val;
		$this->trashButtonSpace = $space;
	}

	function setOpenButton($val, $space = 10){
		$this->openButton = $val;
		$this->openButtonSpace = $space;
	}

	function setCreateButton($val, $space = 10){
		$this->createButton = $val;
		$this->createButtonSpace = $space;
	}

	function setWidth($var){
		$this->width = $var;
	}

	/**
	 * This function sets the values for the autocompletion fields
	 *
	 * @param unknown_type $inputFieldId
	 * @param unknown_type $containerFieldId
	 * @param unknown_type $table
	 * @param unknown_type $contentType
	 * @param unknown_type $maxResults
	 * @param unknown_type $queryDelay
	 * @param unknown_type $layerId
	 * @param unknown_type $setOnSelectFields
	 * @param unknown_type $checkFieldsValue
	 * @param unknown_type $containerwidth
	 */
	function setAutocompleteField($inputFieldId, $containerFieldId, $table, $contentType = '', $selector = '', $maxResults = 10, $queryDelay = 0, $layerId = null, $setOnSelectFields = null, $checkFieldsValue = true, $containerwidth = "100%", $inputMayBeEmpty = 'true', $rootDir = ''){
		$this->inputfields[] = $inputFieldId;
		$this->containerfields[] = $containerFieldId;
		$this->tables[] = $table;
		$this->rootDirs[] = $rootDir;
		$this->contentTypes[] = $contentType;
		$this->selectors[] = $selector;
		$this->weMaxResults[] = $maxResults;
		$this->queryDelay[] = $queryDelay;
		$layerId ? array_push($this->layer, $layerId) : "";
		$this->setOnSelectFields[] = $setOnSelectFields;
		$this->checkFieldsValues[] = $checkFieldsValue;
		$this->containerwidth[] = $containerwidth;
		$this->inputMayBeEmpty[] = $inputMayBeEmpty;
		switch($contentType){
			case self::DirSelector:
				array($this->ct, "folder");
				break;
			case self::DocSelector:
				array($this->ct, "doc");
				break;
		}
		$this->_doOnItemSelect[] = $this->doOnItemSelect;
		$this->doOnItemSelect = '';
		$this->_doOnTextfieldBlur[] = $this->doOnTextfieldBlur;
		$this->doOnTextfieldBlur = '';
	}

	/**
	 * needed to suppress giving the same instance
	 * If sth. is included & the main instance should not be modified, set this to false
	 * @param bool $staticInstance false, if the results should be omitted; don't forget to reset
	 */
	public static function setStaticInstance($staticInstance){
		self::$giveStatic = $staticInstance;
	}

}
