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
class we_import_site{
	var $step = 0;
	var $cmd = '';
	var $from = '/';
	var $to = '';
	var $depth = -1;
	var $images = 1;
	var $htmlPages = 1;
	var $createWePages = 1;
	var $flashmovies = 1;
	var $quicktime = 1;
	var $js = 1;
	var $css = 1;
	var $text = 1;
	var $other = 1;
	var $maxSize = 1; // in Mb
	var $sameName = 'overwrite';
	var $importMetadata = true;
	var $_files;
	var $_depth = 0;
	var $_slash = DIRECTORY_SEPARATOR;
	var $thumbs = '';
	var $width = '';
	var $height = '';
	var $widthSelect = 'pixel';
	var $heightSelect = 'pixel';
	var $keepRatio = 1;
	var $quality = 8;
	var $degrees = 0;
	var $_postProcess;
	var $excludeddirs = array(WEBEDITION_DIR, WE_THUMBNAIL_DIRECTORY);

	/**
	 * Constructor of Class
	 *
	 *
	 * @return we_import_site
	 */
	public function __construct(){
		$wsa = makeArrayFromCSV(get_def_ws());
		$ws = ($wsa ? $wsa[0] : 0);
		$this->from = we_base_request::_(we_base_request::FILE, 'from', (!empty($_SESSION['prefs']['import_from']) ? $_SESSION['prefs']['import_from'] : $this->from));
		$_SESSION['prefs']['import_from'] = $this->from;
		$this->to = we_base_request::_(we_base_request::FILE, 'to', (strlen($this->to) ? $this->to : $ws));
		$this->depth = we_base_request::_(we_base_request::INT, 'depth', $this->depth);
		$this->images = we_base_request::_(we_base_request::BOOL, 'images', $this->images);
		$this->htmlPages = we_base_request::_(we_base_request::BOOL, 'htmlPages', $this->htmlPages);
		$this->createWePages = we_base_request::_(we_base_request::BOOL, 'createWePages', $this->createWePages);
		$this->flashmovies = we_base_request::_(we_base_request::BOOL, 'flashmovies', $this->flashmovies);
		$this->quicktime = we_base_request::_(we_base_request::BOOL, 'quicktime', $this->quicktime);
		$this->js = we_base_request::_(we_base_request::BOOL, 'js', $this->js);
		$this->css = we_base_request::_(we_base_request::BOOL, 'css', $this->css);
		$this->text = we_base_request::_(we_base_request::BOOL, 'text', $this->text);
		$this->other = we_base_request::_(we_base_request::BOOL, 'other', $this->other);
		$this->maxSize = we_base_request::_(we_base_request::INT, 'maxSize', $this->maxSize);
		$this->step = we_base_request::_(we_base_request::INT, 'step', $this->step);
		$this->sameName = we_base_request::_(we_base_request::RAW, 'sameName', $this->sameName);
		$this->importMetadata = we_base_request::_(we_base_request::BOOL, 'importMetadata', $this->importMetadata);
		$this->thumbs = ($thumbs = we_base_request::_(we_base_request::RAW, 'thumbs')) !== false ? implode(',', $thumbs) : $this->thumbs;
		$this->width = we_base_request::_(we_base_request::INT, 'width', $this->width);
		$this->height = we_base_request::_(we_base_request::INT, 'height', $this->height);
		$this->widthSelect = we_base_request::_(we_base_request::BOOL, 'widthSelect', $this->widthSelect);
		$this->heightSelect = we_base_request::_(we_base_request::BOOL, 'heightSelect', $this->heightSelect);
		$this->keepRatio = we_base_request::_(we_base_request::BOOL, 'keepRatio', $this->keepRatio);
		$this->quality = we_base_request::_(we_base_request::INT, 'quality', $this->quality);
		$this->degrees = we_base_request::_(we_base_request::INT, 'degrees', $this->degrees);

		$this->_files = array();

		switch(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0)){
			case 'siteImportSaveWePageSettings' :
				$this->cmd = 'saveWePageSettings';
				break;
			case 'siteImportCreateWePageSettings' :
				$this->cmd = 'createWePageSettings';
				break;
			case 'updateSiteImportTable' :
				$this->cmd = 'updateSiteImportTable';
				break;
			default:
				$this->cmd = we_base_request::_(we_base_request::STRING, 'cmd', $this->cmd);
		}
	}

	/**
	 * returns the right HTML for siteimport depending on $this->cmd
	 *
	 * @return         string
	 */
	public function getHTML(){
		switch($this->cmd){
			case 'updateSiteImportTable' :
				return $this->_updateSiteImportTable();
			case 'createWePageSettings' :
				return $this->_getCreateWePageSettingsHTML();
			case 'saveWePageSettings' :
				return $this->_getSaveWePageSettingsHTML();
			case 'content' :
				return $this->_getContentHTML();
			case 'buttons' :
				return $this->_getButtonsHTML();
			default :
				return $this->_getFrameset();
		}
	}

	/**
	 * returns the javascript needed in the main content frame
	 *
	 *  @return         string
	 */
	private static function _getJS(){
		return we_html_element::jsElement('
function we_cmd() {
	var args = "";
	var url = "' . WEBEDITION_DIR . 'we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+encodeURI(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}

	switch (arguments[0]) {
			case "we_selector_image":
			case "we_selector_document":
			new jsWindow(url,"we_docselector",-1,-1,' . we_selector_file::WINDOW_DOCSELECTOR_WIDTH . ',' . we_selector_file::WINDOW_DOCSELECTOR_HEIGHT . ',true,true,true,true);
			break;

			case "we_selector_directory":
			new jsWindow(url,"we_dirselector",-1,-1,' . we_selector_file::WINDOW_DIRSELECTOR_WIDTH . ',' . we_selector_file::WINDOW_DIRSELECTOR_HEIGHT . ',true,true,true,true);
			break;

		case "browse_server":
			new jsWindow(url,"browse_server",-1,-1,800,400,true,false,true);
			break;

		case "siteImportCreateWePageSettings":
			new jsWindow(url,"siteImportCreateWePageSettings",-1,-1,520,600,true,false,true);
			break;
	}
}

function hideTable() {
	document.getElementById("specifyParam").style.display="none";
}

function displayTable() {
	if (document.we_form.templateID.value > 0) {
		document.getElementById("specifyParam").style.display="block";
		var iframeObj = document.getElementById("iloadframe");
		iframeObj.src = "' . WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=updateSiteImportTable&tid="+document.we_form.templateID.value;
	}
}') .
			we_html_element::jsScript(JS_DIR . 'windows.js') .
			we_html_element::jsElement('
function doUnload() {
	if (jsWindow_count) {
		for (i = 0; i < jsWindow_count; i++) {
			eval("jsWindow" + i + "Object.close()");
		}
	}
}');
	}

	/**
	 * returns the fields of the template with given $tid (ID of template)
	 *
	 * @param	int	$tid ID of template
	 *
	 * @return	array
	 */
	private static function _getFieldsFromTemplate($tid){
		$templateCode = f('SELECT c.Dat FROM ' . CONTENT_TABLE . ' c JOIN ' . LINK_TABLE . ' l ON l.CID=c.ID WHERE l.DocumentTable="' . stripTblPrefix(TEMPLATES_TABLE) . '" AND l.DID=' . intval($tid) . ' AND l.Name="completeData"');
		$tp = new we_tag_tagParser($templateCode);
		$tags = $tp->getAllTags();
		$records = $regs = array();
		foreach($tags as $tag){
			if(preg_match('|<we:([^> /]+)|i', $tag, $regs)){
				$tagname = $regs[1];
				if(preg_match('|name="([^"]+)"|i', $tag, $regs) && ($tagname != 'var') && ($tagname != 'field')){
					$name = $regs[1];
					switch($tagname){
						// tags with text content, images, links and hrefs
						case 'img' :
							$records[$name] = 'img';
							break;
						case 'href' :
							$records[$name] = 'href';
							break;
						case 'link' :
							$records[$name] = 'link';
							break;
						case 'textarea' :
							$records[$name] = 'text';
							break;
						case 'input' :
							$attribs = we_tag_tagParser::makeArrayFromAttribs(preg_replace('/^<we:[^ ]+ /i', '', $tag));
							$type = isset($attribs['type']) ? $attribs['type'] : 'text';
							switch($type){
								case 'text' :
								case 'choice' :
								case 'select' :
									$records[$name] = 'text';
									break;

								case 'date' :
									$records[$name] = 'date';
									break;
							}
							break;
					}
				}
			}
		}
		$records['Title'] = 'text';
		$records['Description'] = 'text';
		$records['Keywords'] = 'text';
		$records['Charset'] = 'text';
		return $records;
	}

	/**
	 * returns the HTML with JavasScript which updates the HTML of the site import table (its a view function called from getHTML()) via DOM (kind of AJAX)
	 *
	 * @return	string
	 */
	private function _updateSiteImportTable(){

		$_templateFields = self::_getFieldsFromTemplate(we_base_request::_(we_base_request::INT, "tid"));
		$hasDateFields = false;

		$values = array();

		foreach($_templateFields as $name => $type){
			if($type === 'date'){
				$hasDateFields = true;
			}
			switch($name){
				case 'Title' :
					$values[] = array(
						'name' => $name,
						'pre' => '<title>',
						'post' => '</title>'
					);
					break;

				case 'Keywords' :
					$values[] = array(
						'name' => $name,
						'pre' => '<meta name="keywords" content="',
						'post' => '">'
					);
					break;

				case 'Description' :
					$values[] = array(
						'name' => $name,
						'pre' => '<meta name="description" content="',
						'post' => '">'
					);
					break;

				case 'Charset' :
					$values[] = array(
						'name' => $name,
						'pre' => '<meta http-equiv="content-type" content="text/html;charset=',
						'post' => '">'
					);
					break;

				default :
					$values[] = array(
						'name' => $name,
						'pre' => '',
						'post' => ''
					);
			}
		}

		return $this->_getHtmlPage('', we_html_element::jsElement('
		var tableDivObj = parent.document.getElementById("tablediv");
		tableDivObj.innerHTML = "' .
					str_replace(array("\r", "\n"), array('\r', '\n'), addslashes($this->_getSiteImportTableHTML($_templateFields, $values))) . '"
		parent.document.getElementById("dateFormatDiv").style.display="' . ($hasDateFields ? "block" : "none") . '";'
		));
	}

	/**
	 * saves the request data in database and session and returns the HTML which closes the window
	 *
	 * @return	string
	 */
	private function _getSaveWePageSettingsHTML(){
		$ct = we_base_request::_(we_base_request::STRING, 'createType');

		$data = ($ct === 'specify' ?
				array(
				'valueCreateType' => $ct,
				'valueTemplateId' => we_base_request::_(we_base_request::INT, 'templateID', 0),
				'valueUseRegex' => we_base_request::_(we_base_request::BOOL, 'useRegEx'),
				'valueFieldValues' => serialize(we_base_request::_(we_base_request::RAW, 'fields', array())),
				'valueDateFormat' => we_base_request::_(we_base_request::STRING, 'dateFormat', 'unix'),
				'valueDateFormatField' => we_base_request::_(we_base_request::RAW, 'dateformatField', ''),
				'valueTemplateName' => g_l('siteimport', '[newTemplate]'),
				'valueTemplateParentID' => 0,
				) :
				array(
				'valueCreateType' => $ct,
				'valueTemplateId' => 0,
				'valueUseRegex' => false,
				'valueFieldValues' => serialize(array()),
				'valueDateFormat' => 'unix',
				'valueDateFormatField' => '',
				'valueTemplateName' => we_base_request::_(we_base_request::STRING, 'templateName', g_l('siteimport', '[newTemplate]')),
				'valueTemplateParentID' => we_base_request::_(we_base_request::INT, 'templateParentID', 0),
		));
		// update session
		$_SESSION['prefs']['siteImportPrefs'] = serialize($data);
		// update DB
		$GLOBALS['DB_WE']->query('REPLACE INTO ' . PREFS_TABLE . ' SET userID=' . intval($_SESSION["user"]["ID"]) . ',`key`="siteImportPrefs",`value`="' . $GLOBALS['DB_WE']->escape($_SESSION["prefs"]["siteImportPrefs"]) . '"');
		return $this->_getHtmlPage('', we_html_element::jsElement('parent.close();'));
	}

	/**
	 * returns HTML of Table with fields and start and end mark
	 *
	 * @param	array	$fields array with fields
	 * @param	array	$values array with values like it comes from REQUEST
	 *
	 * @return	string
	 */
	private function _getSiteImportTableHTML($fields, $values = array()){

		$headlines = array(
			array('dat' => g_l('siteimport', '[fieldName]')),
			array('dat' => g_l('siteimport', '[startMark]')),
			array('dat' => g_l('siteimport', '[endMark]'))
		);

		$content = array();
		if(count($fields) > 0){
			$i = 0;
			foreach(array_keys($fields) as $name){
				list($valpre, $valpost) = $this->_getIndexOfValues($values, $name);
				$content[] = array(
					array('dat' => oldHtmlspecialchars($name) . we_html_element::htmlHidden('fields[' . $i . '][name]', $name)),
					array('dat' => '<textarea name="fields[' . $i . '][pre]" style="width:160px;height:80px" wrap="off">' . oldHtmlspecialchars($valpre) . '</textarea>'),
					array('dat' => '<textarea name="fields[' . $i . '][post]" style="width:160px;height:80px" wrap="off">' . oldHtmlspecialchars($valpost) . '</textarea>'),
				);
				$i++;
			}
		}
		return we_html_tools::htmlDialogBorder3(420, 270, $content, $headlines, "middlefont", "", "", "fields", "margin-top:5px;");
	}

	/**
	 * returns index of array which name is the same as $name
	 *
	 * @param	array	$values array with values
	 * @param	string	$name name to compare
	 *
	 * @return	array
	 */
	private function _getIndexOfValues($values, $name){
		foreach($values as $cur){
			if($cur["name"] === $name){
				return array($cur['pre'], $cur['post']);
			}
		}
		return array('', '');
	}

	/**
	 * returns HTML of the "create webEdition page" settings Dialog
	 *
	 * @return	string
	 */
	private function _getCreateWePageSettingsHTML(){
		$data = (isset($_SESSION["prefs"]["siteImportPrefs"])) ? we_unserialize($_SESSION["prefs"]["siteImportPrefs"]) : array();

		$_valueCreateType = isset($data["valueCreateType"]) ? $data["valueCreateType"] : "auto";
		$_valueTemplateId = isset($data["valueTemplateId"]) ? $data["valueTemplateId"] : 0;
		$_valueUseRegex = isset($data["valueUseRegex"]) ? $data["valueUseRegex"] : 0;
		$_valueFieldValues = isset($data["valueFieldValues"]) ? we_unserialize($data["valueFieldValues"]) : array();
		$_valueDateFormat = isset($data["valueDateFormat"]) ? $data["valueDateFormat"] : "unix";
		$_valueDateFormatField = isset($data["valueDateFormatField"]) ? $data["valueDateFormatField"] : g_l('siteimport', '[dateFormatString]');
		$_valueTemplateName = isset($data["valueTemplateName"]) ? $data["valueTemplateName"] : str_replace(' ', '', g_l('siteimport', '[newTemplate]'));
		$_valueTemplateParentID = isset($data["valueTemplateParentID"]) ? $data["valueTemplateParentID"] : "0";

		$_templateFields = self::_getFieldsFromTemplate($_valueTemplateId);
		$hasDateFields = false;
		foreach($_templateFields as $type){
			if($type === "date"){
				$hasDateFields = true;
				break;
			}
		}
		$date_help_button = we_html_button::create_button("fa:btn_help,fa-question", "javascript:showDateHelp();", true, 0, 0);
		$dateformatvals = array(
			"unix" => g_l('import', '[uts]'),
			"gmt" => g_l('import', '[gts]'),
			"own" => g_l('import', '[fts]')
		);
		$_dateFormatHTML = '<div id="dateFormatDiv" style="display:' . ($hasDateFields ? 'block' : 'none') . ';margin-bottom:10px;"><table style="margin:10px 0 10px 0" class="default"><tr><td style="padding-right:10px" class="defaultfont">' . oldHtmlspecialchars(
				g_l('siteimport', '[dateFormat]'), ENT_QUOTES) . ':</td><td>' . we_html_tools::htmlSelect(
				"dateFormat", $dateformatvals, 1, $_valueDateFormat, false, array('onchange' => "dateFormatChanged(this);")) . '</td><td id="ownValueInput" style="padding-left:10px;display:' . (($_valueDateFormat === "own") ? 'block' : 'none') . '">' . we_html_tools::htmlTextInput(
				"dateformatField", 20, $_valueDateFormatField) . '</td><td id="ownValueInputHelp" style="padding-bottom:1px;padding-left:10px;display:' . (($_valueDateFormat === "own") ? 'block' : 'none') . '">' . $date_help_button . '</td></tr></table></div>';

		$table = '<div style="overflow:auto;height:330px; margin-top:5px;"><div style="width:450px;" id="tablediv">' . $this->_getSiteImportTableHTML($_templateFields, $_valueFieldValues) . '</div></div>';

		$_regExCheckbox = we_html_forms::checkboxWithHidden($_valueUseRegex, "useRegEx", oldHtmlspecialchars(g_l('siteimport', '[useRegEx]'), ENT_QUOTES));
		$specifyHTML = $this->_getTemplateSelectHTML($_valueTemplateId) . '<div id="specifyParam" style="padding-top:10px;display:' . ($_valueTemplateId ? 'block' : 'none') . '">' . $_dateFormatHTML . $_regExCheckbox . $table . '</div>';

		$vals = array(
			"auto" => oldHtmlspecialchars(g_l('siteimport', '[cresteAutoTemplate]'), ENT_QUOTES),
			"specify" => oldHtmlspecialchars(g_l('siteimport', '[useSpecifiedTemplate]'), ENT_QUOTES)
		);

		$_html = '<table style="margin-bottom:10px" class="default"><tr><td style="padding-right:10px" class="defaultfont">' . oldHtmlspecialchars(
				g_l('siteimport', '[importKind]'), ENT_QUOTES) . ':</td><td>' . we_html_tools::htmlSelect(
				"createType", $vals, 1, $_valueCreateType, false, array('onchange' => "createTypeChanged(this);")) . '</td></tr></table><div id="ctauto" style="display:' . (($_valueCreateType === "auto") ? 'block' : 'none') . '">' . we_html_tools::htmlAlertAttentionBox(
				g_l('siteimport', '[autoExpl]'), we_html_tools::TYPE_INFO, 450) . self::_formPathHTML($_valueTemplateName, $_valueTemplateParentID) . '</div><div id="ctspecify" style="display:' . (($_valueCreateType === "specify") ? 'block' : 'none') . '"><div style="height:4px;"></div>' . $specifyHTML . '</div>';

		$_html = '<div style="height:480px">' . $_html . '</div>';

		$parts = array(
			array(
				"headline" => "", "html" => $_html, "space" => 0
		));

		$bodyhtml = '<body class="weDialogBody">
					<iframe style="position:absolute;top:-2000px;" src="about:blank" id="iloadframe" name="iloadframe" width="400" height="200"></iframe>
					<form onsubmit="return false;" name="we_form" method="post" action="' . $_SERVER['SCRIPT_NAME'] . '" target="iloadframe">' .
			we_html_element::htmlHiddens(array(
				"we_cmd[0]" => "siteImportSaveWePageSettings",
				"ok" => 1)) . we_html_multiIconBox::getJS();

		$okbutton = we_html_button::create_button(we_html_button::OK, "javascript:if(checkForm()){document.we_form.submit();}");
		$cancelbutton = we_html_button::create_button(we_html_button::CANCEL, "javascript:self.close()");
		$buttons = we_html_button::position_yes_no_cancel($okbutton, null, $cancelbutton);
		$bodyhtml .= we_html_multiIconBox::getHTML(
				"", "100%", $parts, 30, $buttons, -1, "", "", false, g_l('siteimport', '[importSettingsWePages]'));
		$bodyhtml .= '</form></body>';

		$js = we_html_element::jsElement('
	function checkForm(){
		var f = document.forms[0];
		var createType = f.createType.options[f.createType.selectedIndex].value;
		if (createType == "specify") {
			// check if template is selected
			if (f.templateID.value == "0" || f.templateID.value=="") {
				' . we_message_reporting::getShowMessageCall(
					g_l('siteimport', '[pleaseSelectTemplateAlert]'), we_message_reporting::WE_MESSAGE_ERROR) . '
				return false;
			}
			// check value of fields
			var fields = [];
			var inputElements = f.getElementsByTagName("input");
			for (var i=0; i<inputElements.length; i++) {
				if (inputElements[i].name.indexOf("fields[") == 0) {
					var search = /^fields\[([^\]]+)\]\[([^\]]+)\]$/;
					var result = search.exec(inputElements[i].name);
					var index = parseInt(result[1]);
					var key = result[2];
					if (fields[index] == null) {
						fields[index] = {};
					}
					fields[index][key] = inputElements[i].value;
				}
			}
			var textareaElements = f.getElementsByTagName("textarea");
			for (var i=0; i<textareaElements.length; i++) {
				if (textareaElements[i].name.indexOf("fields[") == 0) {
					var search = /^fields\[([^\]]+)\]\[([^\]]+)\]$/;
					var result = search.exec(textareaElements[i].name);
					var index = parseInt(result[1]);
					var key = result[2];
					if (fields[index] == null) {
						fields[index] = {};
					}
					fields[index][key] = textareaElements[i].value;
				}
			}
			filled = 0;
			for (var i=0; i<fields.length; i++) {
				if (fields[i]["pre"].length > 0 && fields[i]["post"].length > 0) {
					filled = 1;
					break;
				}
			}
			if (filled == 0) {
				' . we_message_reporting::getShowMessageCall(
					g_l('siteimport', '[startEndMarkAlert]'), we_message_reporting::WE_MESSAGE_ERROR) . '
				return false;
			}
			if (document.getElementById("ownValueInput").style.display != "none") {
				if (f.dateformatField.value.length == 0) {
					' . we_message_reporting::getShowMessageCall(
					str_replace('"', '\"', g_l('siteimport', '[errorEmptyDateFormat]')), we_message_reporting::WE_MESSAGE_ERROR) . '
					return false;
				}
			}
		} else {
			if (f.templateName.value.length==0) {
				' . we_message_reporting::getShowMessageCall(
					g_l('siteimport', '[nameOfTemplateAlert]'), we_message_reporting::WE_MESSAGE_ERROR) . '
				f.templateName.focus();
				f.templateName.select();
				return false;
			}
			var reg = /[^a-z0-9\._+\-]/gi;
			if (reg.test(f.templateName.value)) {' .
				we_message_reporting::getShowMessageCall(g_l('alert', '[we_filename_notValid]'), we_message_reporting::WE_MESSAGE_ERROR) . '
				f.templateName.focus();
				f.templateName.select();
				return false;
			}
		}
		return true;
	}

	function createTypeChanged(s) {
		var val = s.options[s.selectedIndex].value;
		document.getElementById("ctauto").style.display = (val == "auto") ? "block" : "none";
		document.getElementById("ctspecify").style.display = (val == "specify") ? "block" : "none";
	}

	function dateFormatChanged(s) {
		var val = s.options[s.selectedIndex].value;
		document.getElementById("ownValueInput").style.display = (val == "own") ? "block" : "none";
		document.getElementById("ownValueInputHelp").style.display = (val == "own") ? "block" : "none";
	}

	function showDateHelp() {
		// this is a real alert, dont use showMessage yet
		' . we_message_reporting::getShowMessageCall(
					g_l('import', '[format_timestamp]'), we_message_reporting::WE_MESSAGE_INFO) . '
	}');

		return $this->_getHtmlPage($bodyhtml, self::_getJS() . $js);
	}

	/**
	 * returns HTML of the template selector
	 *
	 * @param int $tid  ID of template
	 *
	 * @return	string
	 */
	private function _getTemplateSelectHTML($tid){
		$path = f('SELECT Path FROM ' . TEMPLATES_TABLE . ' WHERE ID=' . intval($tid));
		$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['templateID'].value");
		$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['templateDummy'].value");
		$wecmdenc3 = we_base_request::encCmd("opener.displayTable();");

		$button = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_document',document.we_form.elements['templateID'].value,'" . TEMPLATES_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','','','" . we_base_ContentTypes::TEMPLATE . "',1)");

		$foo = we_html_tools::htmlTextInput('templateDummy', 30, $path, "", ' readonly', "text", 320, 0);
		return we_html_tools::htmlFormElementTable(
				$foo, oldHtmlspecialchars(g_l('siteimport', '[template]'), ENT_QUOTES), "left", "defaultfont", we_html_tools::hidden('templateID', intval($tid)), we_html_tools::getPixel(20, 4), $button);
	}

	/**
	 * returns HTML of the main dialog (contemt)
	 *
	 * @return	string
	 */
	private function _getContentHTML(){
		// Suorce Directory
		$wecmdenc1 = we_base_request::encCmd("document.we_form.elements.from.value");
		$_from_button = permissionhandler::hasPerm("CAN_SELECT_EXTERNAL_FILES") ?
			we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('browse_server', '" . $wecmdenc1 . "','" . we_base_ContentTypes::FOLDER . "',document.we_form.elements.from.value)") :
			"";

		$_input = we_html_tools::htmlTextInput("from", 30, $this->from, "", "readonly", "text", 300);

		$_importFrom = we_html_tools::htmlFormElementTable(
				$_input, g_l('siteimport', '[importFrom]'), "left", "defaultfont", we_html_tools::getPixel(10, 1), $_from_button, "", "", "", 0);

		// Destination Directory
		$wecmdenc1 = we_base_request::encCmd("document.we_form.elements.to.value");
		$wecmdenc2 = we_base_request::encCmd("document.we_form.elements.toPath.value");
		$_to_button = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_directory',document.we_form.elements.to.value,'" . FILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','','','0')");

		//$_hidden = we_html_tools::hidden("to",$this->to);
		//$_input = we_html_tools::htmlTextInput("toPath",30,id_to_path($this->to),"",'readonly="readonly"',"text",300);
		//$_importTo = we_html_tools::htmlFormElementTable($_input, g_l('siteimport',"[importTo]"), "left", "defaultfont", we_html_tools::getPixel(10, 1), $_to_button, $_hidden, "", "", 0);


		$yuiSuggest = & weSuggest::getInstance();
		$yuiSuggest->setAcId("DirPath");
		$yuiSuggest->setContentType("folder");
		$yuiSuggest->setInput("toPath", id_to_path($this->to));
		$yuiSuggest->setLabel(g_l('siteimport', '[importTo]'));
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(0);
		$yuiSuggest->setResult("to", $this->to);
		$yuiSuggest->setSelector(weSuggest::DirSelector);
		$yuiSuggest->setWidth(300);
		$yuiSuggest->setSelectButton($_to_button, 10);

		$_importTo = weSuggest::getYuiFiles() . $yuiSuggest->getHTML() . $yuiSuggest->getYuiJs();

		// Checkboxes
		$weoncklick = "if(this.checked && (!this.form.elements.htmlPages.checked)){this.form.elements.htmlPages.checked = true;}";
		$weoncklick .= ((!permissionhandler::hasPerm("NEW_HTML")) && permissionhandler::hasPerm("NEW_WEBEDITIONSITE")) ? "if((!this.checked) && this.form.elements.htmlPages.checked){this.form.elements.htmlPages.checked = false;}" : "";

		$_images = we_html_forms::checkboxWithHidden(permissionhandler::hasPerm("NEW_GRAFIK") ? $this->images : false, "images", g_l('siteimport', '[importImages]'), false, "defaultfont", "", !permissionhandler::hasPerm("NEW_GRAFIK"));

		$_htmlPages = we_html_forms::checkboxWithHidden(permissionhandler::hasPerm("NEW_HTML") ? $this->htmlPages : ((permissionhandler::hasPerm("NEW_WEBEDITIONSITE") && $this->createWePages) ? true : false), "htmlPages", g_l('siteimport', '[importHtmlPages]'), false, "defaultfont", "if(this.checked){this.form.elements.check_createWePages.disabled=false;document.getElementById('label__createWePages').style.color='black';}else{this.form.elements.check_createWePages.disabled=true;document.getElementById('label__createWePages').style.color='grey';}", !permissionhandler::hasPerm("NEW_HTML"));
		$_createWePages = we_html_forms::checkboxWithHidden(permissionhandler::hasPerm("NEW_WEBEDITIONSITE") ? $this->createWePages : false, "createWePages", g_l('siteimport', '[createWePages]') . "&nbsp;&nbsp;", false, "defaultfont", $weoncklick, !permissionhandler::hasPerm("NEW_WEBEDITIONSITE"));
		$_flashmovies = we_html_forms::checkboxWithHidden(permissionhandler::hasPerm("NEW_FLASH") ? $this->flashmovies : false, "flashmovies", g_l('siteimport', '[importFlashmovies]'), false, "defaultfont", "", !permissionhandler::hasPerm("NEW_FLASH"));
		$_quicktime = we_html_forms::checkboxWithHidden(permissionhandler::hasPerm("NEW_QUICKTIME") ? $this->quicktime : false, "quicktime", g_l('siteimport', '[importQuicktime]'), false, "defaultfont", "", !permissionhandler::hasPerm("NEW_QUICKTIME"));
		$_jss = we_html_forms::checkboxWithHidden(permissionhandler::hasPerm("NEW_JS") ? $this->js : false, "j", g_l('siteimport', '[importJS]'), false, "defaultfont", "", !permissionhandler::hasPerm("NEW_JS"));
		$_css = we_html_forms::checkboxWithHidden(permissionhandler::hasPerm("NEW_CSS") ? $this->css : false, "css", g_l('siteimport', '[importCSS]'), false, "defaultfont", "", !permissionhandler::hasPerm("NEW_CSS"));
		$_text = we_html_forms::checkboxWithHidden(permissionhandler::hasPerm("NEW_TEXT") ? $this->text : false, "text", g_l('siteimport', '[importText]'), false, "defaultfont", "", !permissionhandler::hasPerm("NEW_TEXT"));
		$_htaccess = we_html_forms::checkboxWithHidden(permissionhandler::hasPerm("NEW_HTACCESS") ? $this->text : false, "htacsess", g_l('siteimport', '[importHTACCESS]'), false, "defaultfont", "", !permissionhandler::hasPerm("NEW_HTACCESS"));
		$_others = we_html_forms::checkboxWithHidden(permissionhandler::hasPerm("NEW_SONSTIGE") ? $this->other : false, "other", g_l('siteimport', '[importOther]'), false, "defaultfont", "", !permissionhandler::hasPerm("NEW_SONSTIGE"));

		$_wePagesOptionButton = we_html_button::create_button("preferences", "javascript:we_cmd('siteImportCreateWePageSettings')", true, 150, 22, "", "", false, true, "", true);
		// Depth
		$_select = we_html_tools::htmlSelect(
				"depth", array(
				"-1" => g_l('siteimport', '[nolimit]'),
				0,
				1,
				2,
				3,
				4,
				5,
				6,
				7,
				8,
				9,
				10,
				11,
				12,
				13,
				14,
				15,
				16,
				17,
				18,
				19,
				20,
				21,
				22,
				23,
				24,
				25,
				26,
				27,
				28,
				29,
				30
				), 1, $this->depth, false, array(), "value", 150);

		$_depth = we_html_tools::htmlFormElementTable($_select, g_l('siteimport', '[depth]'));
		$maxallowed = round($GLOBALS['DB_WE']->getMaxAllowedPacket() / (1024 * 1024)) ? : 20;
		$maxarray = array(
			"0" => g_l('siteimport', '[nolimit]'), "0.5" => "0.5"
		);
		for($i = 1; $i <= $maxallowed; $i++){
			$maxarray[$i] = $i;
		}

		// maxSize
		$_select = we_html_tools::htmlSelect("maxSize", $maxarray, 1, $this->maxSize, false, array(), "value", 150);
		$_maxSize = we_html_tools::htmlFormElementTable($_select, g_l('siteimport', '[maxSize]'));

		$GLOBALS['DB_WE']->query('SELECT ID,Name FROM ' . THUMBNAILS_TABLE . ' ORDER BY Name');
		$thumbsarray = $GLOBALS['DB_WE']->getAllFirst(false);
		$_select = we_html_tools::htmlSelect("thumbs[]", $thumbsarray, 5, $this->thumbs, true, array(), "value", 150);
		$_thumbs = we_html_tools::htmlFormElementTable($_select, g_l('importFiles', '[thumbnails]'));

		/* Create Main Table */
		$_attr = array('class' => 'default');
		$_tableObj = new we_html_table($_attr, 6, 3);
		$_tableObj->setCol(0, 0, array("colspan" => 2), $_images);
		$_tableObj->setCol(1, 0, array("colspan" => 2), $_flashmovies);
		$_tableObj->setCol(2, 0, array("colspan" => 2), $_htmlPages);
		$_tableObj->setCol(3, 0, null, "");
		$_tableObj->setCol(3, 1, null, $_createWePages);
		$_tableObj->setCol(4, 1, null, $_wePagesOptionButton);
		$_tableObj->setCol(5, 0, null, we_html_tools::getPixel(20, 1));
		$_tableObj->setCol(5, 1, null, we_html_tools::getPixel(200, 1));
		$_tableObj->setCol(5, 2, null, we_html_tools::getPixel(180, 1));
		$_tableObj->setCol(0, 2, null, $_jss);
		$_tableObj->setCol(1, 2, null, $_css);
		$_tableObj->setCol(2, 2, null, $_text);
		$_tableObj->setCol(3, 2, null, $_others);
		$_tableObj->setCol(4, 2, array("valign" => "top"), $_quicktime);


		$parts = array(
			array(
				"headline" => g_l('siteimport', '[dirs_headline]'),
				"html" => $_importFrom . we_html_tools::getPixel(20, 5) . $_importTo,
				"space" => 120
			),
			array(
				"headline" => g_l('siteimport', '[import]'),
				"html" => $_tableObj->getHtml(),
				"space" => 120
			),
		);

		$_tableObj = new we_html_table($_attr, 2, 2);
		$_tableObj->setCol(0, 0, null, $_depth);
		$_tableObj->setCol(0, 1, null, $_maxSize);
		$_tableObj->setCol(1, 0, null, we_html_tools::getPixel(220, 1));
		$_tableObj->setCol(1, 1, null, we_html_tools::getPixel(180, 1));

		$parts[] = array(
			"headline" => g_l('siteimport', '[limits]'),
			"html" => $_tableObj->getHtml(),
			"space" => 120
		);

		$content = we_html_tools::htmlAlertAttentionBox(g_l('importFiles', '[sameName_expl]'), we_html_tools::TYPE_INFO, 410) .
			we_html_tools::getPixel(200, 10) .
			we_html_forms::radiobutton("overwrite", ($this->sameName === "overwrite"), "sameName", g_l('importFiles', '[sameName_overwrite]')) .
			we_html_forms::radiobutton("rename", ($this->sameName === "rename"), "sameName", g_l('importFiles', '[sameName_rename]')) .
			we_html_forms::radiobutton("nothing", ($this->sameName === "nothing"), "sameName", g_l('importFiles', '[sameName_nothing]'));

		$parts[] = array(
			"headline" => g_l('importFiles', '[sameName_headline]'),
			"html" => $content,
			"space" => 120
		);

		if(permissionhandler::hasPerm("NEW_GRAFIK")){
			$parts[] = array(
				'headline' => g_l('importFiles', '[metadata]'),
				'html' => we_html_forms::checkboxWithHidden($this->importMetadata == true, 'importMetadata', g_l('importFiles', '[import_metadata]')),
				'space' => 120
			);

			if(we_base_imageEdit::gd_version() > 0){
				$parts[] = array(
					"headline" => g_l('importFiles', '[make_thumbs]'),
					"html" => $_thumbs,
					"space" => 120
				);

				$widthInput = we_html_tools::htmlTextInput("width", 10, $this->width, "", '', "text", 60);
				$heightInput = we_html_tools::htmlTextInput("height", 10, $this->height, "", '', "text", 60);

				$widthSelect = '<select size="1" class="weSelect" name="widthSelect"><option value="pixel"' . (($this->widthSelect === "pixel") ? ' selected="selected"' : '') . '>' . g_l('weClass', '[pixel]') . '</option><option value="percent"' . (($this->widthSelect === "percent") ? ' selected="selected"' : '') . '>' . g_l('weClass', '[percent]') . '</option></select>';
				$heightSelect = '<select size="1" class="weSelect" name="heightSelect"><option value="pixel"' . (($this->heightSelect === "pixel") ? ' selected="selected"' : '') . '>' . g_l('weClass', '[pixel]') . '</option><option value="percent"' . (($this->heightSelect === "percent") ? ' selected="selected"' : '') . '>' . g_l('weClass', '[percent]') . '</option></select>';

				$ratio_checkbox = we_html_forms::checkbox(
						1, $this->keepRatio, "keepRatio", g_l('thumbnails', '[ratio]'));

				$_resize = '<table>
				<tr>
					<td class="defaultfont">' . g_l('weClass', '[width]') . ':</td>
					<td>' . $widthInput . '</td>
					<td>' . $widthSelect . '</td>
				</tr>
				<tr>
					<td class="defaultfont">' . g_l('weClass', '[height]') . ':</td>
					<td>' . $heightInput . '</td>
					<td>' . $heightSelect . '</td>
				</tr>
				<tr>
					<td colspan="3">' . $ratio_checkbox . '</td>
				</tr>
			</table>';

				$parts[] = array(
					"headline" => g_l('weClass', '[resize]'), "html" => $_resize, "space" => 120
				);

				$_radio0 = we_html_forms::radiobutton(0, $this->degrees == 0, "degrees", g_l('weClass', '[rotate0]'));
				$_radio180 = we_html_forms::radiobutton(180, $this->degrees == 180, "degrees", g_l('weClass', '[rotate180]'));
				$_radio90l = we_html_forms::radiobutton(90, $this->degrees == 90, "degrees", g_l('weClass', '[rotate90l]'));
				$_radio90r = we_html_forms::radiobutton(270, $this->degrees == 270, "degrees", g_l('weClass', '[rotate90r]'));

				$parts[] = array(
					"headline" => g_l('weClass', '[rotate]'),
					"html" => $_radio0 . $_radio180 . $_radio90l . $_radio90r,
					"space" => 120
				);

				$parts[] = array(
					"headline" => g_l('weClass', '[quality]'),
					"html" => we_base_imageEdit::qualitySelect("quality", $this->quality),
					"space" => 120
				);
			} else {
				$parts[] = array(
					"headline" => "",
					"html" => we_html_tools::htmlAlertAttentionBox(
						g_l('importFiles', '[add_description_nogdlib]'), we_html_tools::TYPE_INFO, ""),
					"space" => 0
				);
			}
			$foldAT = 4;
		} else {
			$foldAT = -1;
		}

		$wepos = weGetCookieVariable("but_wesiteimport");
		$content = we_html_multiIconBox::getJS() .
			we_html_multiIconBox::getHTML(
				"wesiteimport", "100%", $parts, 30, "", $foldAT, g_l('importFiles', '[image_options_open]'), g_l('importFiles', '[image_options_close]'), ($wepos === "down"), g_l('siteimport', '[siteimport]')) . $this->_getHiddensHTML();

		$content = we_html_element::htmlForm(
				array(
				"action" => WEBEDITION_DIR . "we_cmd.php",
				"name" => "we_form",
				"method" => "post",
				"target" => "siteimportcmd"
				), $content);

		$body = we_html_element::htmlBody(array(
				"class" => "weDialogBody", "onunload" => "doUnload();"
				), $content);

		$js = self::_getJS();

		return $this->_getHtmlPage($body, $js);
	}

	/**
	 * returns HTML of the buttons frame
	 *
	 * @return	string
	 */
	private function _getButtonsHTML(){

		if($this->step == 1){
			$this->_fillFiles();
			if(count($this->_files) == 0){
				$importDirectory = rtrim(rtrim($_SERVER['DOCUMENT_ROOT'], '/') . $this->from, '/');
				if(count(scandir($importDirectory)) <= 2){
					return we_html_element::jsElement('alert(\'' . addslashes(g_l('importFiles', '[emptyDir]')) . '\');top.close()');
				}
				return we_html_element::jsElement('alert(\'' . addslashes(g_l('importFiles', '[noFiles]')) . '\');top.close();');
			}
			new we_import_siteFrag($this);
			return '';
		}

		$bodyAttribs = array(
			"class" => "weDialogButtonsBody",
			'style' => 'overflow:hidden;'
		);

		$cancelButton = we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close()", true, 100, 22, "", "", false, false);

		$js = we_html_element::jsElement("
		function back() {
			top.location.href='" . WEBEDITION_DIR . "we_cmd.php?we_cmd[0]=import&we_cmd[1]=siteImport';
		}
		function next() {
			var testvalue = 0;
			if(!top.siteimportcontent.document.we_form.from.value  || top.siteimportcontent.document.we_form.from.value=='/'){
				testvalue += 1;
			}
			if(top.siteimportcontent.document.we_form.to.value == 0 || top.siteimportcontent.document.we_form.to.value == ''){
				testvalue += 2;
			}
			switch(testvalue){
			case 0:
				top.siteimportcontent.document.we_form.submit();
				break;
			case 1:
				if(confirm('" . g_l('importFiles', '[root_dir_1]') . "')){
					top.siteimportcontent.document.we_form.submit();
				}
				break;
			case 2:
				if(confirm('" . g_l('importFiles', '[root_dir_2]') . "')){
					top.siteimportcontent.document.we_form.submit();
				}
				break;
			case 3:
				if(confirm('" . g_l('importFiles', '[root_dir_3]') . "')){
					top.siteimportcontent.document.we_form.submit();
				}
				break;
			default:
			}
		}");


		$prevNextButtons = we_html_button::create_button_table(array(
				we_html_button::create_button(we_html_button::BACK, "javascript:back();", true, 100, 22, "", "", false, false),
				we_html_button::create_button(we_html_button::NEXT, "javascript:next();", true, 100, 22, "", "", false, false)
		));

		$pb = new we_progressBar(0);
		$pb->setStudLen(200);
		$pb->addText("&nbsp;", 0, "progressTxt");
		$js.=$pb->getJS('', true);

		$table = new we_html_table(array('class' => 'default', "width" => "100%"), 1, 2);
		$table->setCol(0, 0, null, '<div id="progressBarDiv" style="display:none;">' . $pb->getHTML() . '</div>');
		$table->setCol(0, 1, array(
			"align" => "right"
			), we_html_button::position_yes_no_cancel($prevNextButtons, null, $cancelButton, 10, '', array(), 10));


		return $this->_getHtmlPage(we_html_element::htmlBody($bodyAttribs, $table->getHtml()), $js);
	}

	/**
	 * used by importFile() internal Function, dont call directly!
	 *
	 * @param $content string
	 * @param &$we_doc we_webEditionDocument
	 * @param $sourcePath string
	 * @static
	 */
	private static function _importWebEditionPage($content, &$we_doc, $sourcePath){
		$data = (isset($_SESSION["prefs"]["siteImportPrefs"])) ? we_unserialize($_SESSION["prefs"]["siteImportPrefs"]) : array();

		$_valueCreateType = isset($data["valueCreateType"]) ? $data["valueCreateType"] : "auto";
		$_valueTemplateId = isset($data["valueTemplateId"]) ? $data["valueTemplateId"] : 0;
		$_valueUseRegex = isset($data["valueUseRegex"]) ? $data["valueUseRegex"] : 0;
		$_valueFieldValues = isset($data["valueFieldValues"]) ? we_unserialize($data["valueFieldValues"]) : array();
		$_valueDateFormat = isset($data["valueDateFormat"]) ? $data["valueDateFormat"] : "unix";
		$_valueDateFormatField = isset($data["valueDateFormatField"]) ? $data["valueDateFormatField"] : "d.m.Y";
		$_valueTemplateName = isset($data["valueTemplateName"]) ? $data["valueTemplateName"] : g_l('siteimport', '[newTemplate]');
		$_valueTemplateParentID = isset($data["valueTemplateParentID"]) ? $data["valueTemplateParentID"] : "";

		$content = self::_makeAbsolutPathOfContent($content, $sourcePath, $we_doc->ParentPath);

		if($_valueCreateType === "auto"){
			self::_importAuto($content, $we_doc, $_valueTemplateName, $_valueTemplateParentID);
		} else {
			self::_importSpecify($content, $we_doc, $_valueTemplateId, $_valueUseRegex, $_valueFieldValues, $_valueDateFormat, $_valueDateFormatField);
		}
	}

	/**
	 * Makes a relative path from an absolute path
	 *
	 * @param	string	$docpath Absolute Path of document
	 * @param	string	$linkpath Absolute Path of link (href or src)
	 *
	 * @return         string
	 */
	public static function makeRelativePath($docpath, $linkpath){
		$parentPath = $docpath;
		$newLinkPath = '';

		while($parentPath != substr($linkpath, 0, strlen($parentPath))){
			$parentPath = dirname($parentPath);
			$newLinkPath .= '../';
		}
		$rest = substr($linkpath, strlen($parentPath));
		if(substr($rest, 0, 1) === '/'){
			$rest = substr($rest, 1);
		}
		return $newLinkPath . $rest;
	}

	/**
	 * converts a relative path to an absolute path and returns it
	 *
	 * @param $path string path to convert
	 * @param $sourcePath string path of source file
	 * @param $parentPath string parent path
	 * @return string
	 * @static
	 */
	private static function _makeAbsolutePath($path, $sourcePath, $parentPath){
		if(!preg_match('|^[a-z]+://|i', $path)){
			if(substr($path, 0, 1) === '/'){
				// if href is an absolute URL convert it into a relative URL
				$path = self::makeRelativePath($sourcePath, $path);
			} elseif(substr($path, 0, 2) === './'){
				// if href is a relative URL starting with "./" remove the "./"
				$path = substr($path, 2);
			}
			// Make absolute Path out of it
			while(substr($path, 0, 3) === '../' && strlen($parentPath) > 0 && $parentPath != '/'){
				$parentPath = dirname($parentPath);
				$path = substr($path, 3);
			}
			if(substr($parentPath, -1) != '/'){
				$parentPath = $parentPath . '/';
			}
			return $parentPath . $path;
		}
		return $path;
	}

	/**
	 * returns HTML for path information (in webEdition page settings dialog)
	 *
	 * @param $templateName string name of template
	 * @param $myid int id of template dir
	 * @return string
	 * @static
	 */
	private static function _formPathHTML($templateName, $myid){
		$path = id_to_path($myid, TEMPLATES_TABLE);
		$wecmdenc1 = we_base_request::encCmd("document.we_form.elements.templateParentID.value");
		$wecmdenc2 = we_base_request::encCmd("document.we_form.elements.templateDirName.value");
		$button = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_directory',document.we_form.elements.templateParentID.value,'" . TEMPLATES_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','','')");

		$yuiSuggest = & weSuggest::getInstance();
		$yuiSuggest->setAcId("TplPath");
		$yuiSuggest->setContentType("folder");
		$yuiSuggest->setInput('templateDirName', $path);
		$yuiSuggest->setResult('templateParentID', 0);
		$yuiSuggest->setLabel(g_l('weClass', '[dir]'));
		$yuiSuggest->setMaxResults(20);
		$yuiSuggest->setMayBeEmpty(1);
		$yuiSuggest->setWidth(320);
		$yuiSuggest->setTable(TEMPLATES_TABLE);
		$yuiSuggest->setSelector(weSuggest::DirSelector);
		$yuiSuggest->setSelectButton($button);
		$dirChooser = weSuggest::getYuiFiles() . $yuiSuggest->getHTML() . $yuiSuggest->getYuiJs();

		/*

		  $dirChooser = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($textname,30,$path,"",' readonly',"text",320,0),
		  g_l('weClass',"[dir]"),
		  "left",
		  "defaultfont",
		  we_html_tools::hidden($idname,0),
		  we_html_tools::getPixel(20,4),
		  $button);
		 */

		return '
<table class="default" style="margin-top:10px;">
	<tr>
		<td>' . we_html_tools::htmlFormElementTable(
				we_html_tools::htmlTextInput("templateName", 30, $templateName, 255, "", "text", 320), g_l('siteimport', '[nameOfTemplate]')) . '</td>
		<td></td>
		<td>' . we_html_tools::htmlFormElementTable(
				'<span class="defaultfont"><b>.tmpl</b></span>', g_l('weClass', '[extension]')) . '</td>
	</tr>
	<tr>
		<td>' . we_html_tools::getPixel(20, 4) . '</td>
		<td>' . we_html_tools::getPixel(20, 2) . '</td>
		<td>' . we_html_tools::getPixel(100, 2) . '</td>
	</tr>
	<tr>
		<td colspan="3">' . $dirChooser . '</td>
	</tr>
</table>';
	}

	/**
	 * converts all relative paths of a document to absolute paths and returns the converted document
	 *
	 * @param $content string document to convert
	 * @param $sourcePath string path of source file
	 * @param $parentPath string parent path
	 * @return string
	 * @static
	 */
	private static function _makeAbsolutPathOfContent($content, $sourcePath, $parentPath){
		$sourcePath = substr($sourcePath, strlen($_SERVER['DOCUMENT_ROOT']));
		if(substr($sourcePath, 0, 1) != "/"){
			$sourcePath = "/" . $sourcePath;
		}
		$regs = $regs2 = array();
		// replace hrefs
		preg_match_all('/(<[^>]+href=["\']?)([^"\' >]+)([^"\'>]?[^>]*>)/i', $content, $regs, PREG_PATTERN_ORDER);
		if($regs != null){
			for($i = 0; $i < count($regs[2]); $i++){
				$orig_href = $regs[2][$i];
				$new_href = self::_makeAbsolutePath($orig_href, $sourcePath, $parentPath);
				if($new_href != $orig_href){
					$newTag = $regs[1][$i] . $new_href . $regs[3][$i];
					$content = str_replace($regs[0][$i], $newTag, $content);
				}
			}
		}
		// replace src (same as href!!)
		preg_match_all('/(<[^>]+src=["\']?)([^"\' >]+)([^"\'>]?[^>]*>)/i', $content, $regs, PREG_PATTERN_ORDER);
		if($regs != null){
			for($i = 0; $i < count($regs[2]); $i++){
				$orig_href = $regs[2][$i];
				$new_href = self::_makeAbsolutePath($orig_href, $sourcePath, $parentPath);
				if($new_href != $orig_href){
					$newTag = $regs[1][$i] . $new_href . $regs[3][$i];
					$content = str_replace($regs[0][$i], $newTag, $content);
				}
			}
		}
		// url() in styles with style=""
		preg_match_all('/(<[^>]+style=")([^"]+)("[^>]*>)/i', $content, $regs, PREG_PATTERN_ORDER);
		if($regs != null){
			for($i = 0; $i < count($regs[2]); $i++){
				$style = $regs[2][$i];
				$newStyle = $style;
				preg_match_all('/(url\(\'?)([^\'\)]+)(\'?\))/i', $style, $regs2, PREG_PATTERN_ORDER);
				if($regs2 != null){
					for($z = 0; $z < count($regs2[2]); $z++){
						$orig_url = $regs2[2][$z];
						$new_url = self::_makeAbsolutePath($orig_url, $sourcePath, $parentPath);
						if($orig_url != $new_url){
							$newStyle = str_replace($orig_url, $new_url, $newStyle);
						}
					}
				}
				if($newStyle != $style){
					$newTag = $regs[1][$i] . $newStyle . $regs[3][$i];
					$content = str_replace($regs[0][$i], $newTag, $content);
				}
			}
		}

		// url() in styles with style=''
		preg_match_all('/(<[^>]+style=\')([^\']+)(\'[^>]*>)/i', $content, $regs, PREG_PATTERN_ORDER);
		if($regs != null){
			for($i = 0; $i < count($regs[2]); $i++){
				$style = $regs[2][$i];
				$newStyle = $style;
				preg_match_all('/(url\("?)([^"\)]+)("?\))/i', $style, $regs2, PREG_PATTERN_ORDER);
				if($regs2 != null){
					for($z = 0; $z < count($regs2[2]); $z++){
						$orig_url = $regs2[2][$z];
						$new_url = self::_makeAbsolutePath($orig_url, $sourcePath, $parentPath);
						if($orig_url != $new_url){
							$newStyle = str_replace($orig_url, $new_url, $newStyle);
						}
					}
				}
				if($newStyle != $style){
					$newTag = $regs[1][$i] . $newStyle . $regs[3][$i];
					$content = str_replace($regs[0][$i], $newTag, $content);
				}
			}
		}

		// url() in style tags
		preg_match_all('/(<style[^>]*>)(.*)(<\/style>)/isU', $content, $regs, PREG_PATTERN_ORDER);
		if($regs != null){
			for($i = 0; $i < count($regs[2]); $i++){
				$style = $regs[2][$i];
				$newStyle = $style;
				// url() in styles with style=''
				preg_match_all(
					'/(url\([\'"]?)([^\'"\)]+)([\'"]?\))/iU', $style, $regs2, PREG_PATTERN_ORDER);
				if($regs2 != null){
					for($z = 0; $z < count($regs2[2]); $z++){
						$orig_url = $regs2[2][$z];
						$new_url = self::_makeAbsolutePath($orig_url, $sourcePath, $parentPath);
						if($orig_url != $new_url){
							$newStyle = str_replace(
								$regs2[0][$z], $regs2[1][$z] . $new_url . $regs2[3][$z], $newStyle);
						}
					}
				}
				if($newStyle != $style){
					$newTag = $regs[1][$i] . $newStyle . $regs[3][$i];
					$content = str_replace($regs[0][$i], $newTag, $content);
				}
			}
		}
		return $content;
	}

	/**
	 * internal function, used by _importWebEditionPage() => don't call directly!
	 * @param $content string
	 * @param &$we_doc we_webEditionDocument
	 * @param $templateFilename string
	 * @param $templateParentID int
	 * @static
	 */
	private static function _importAuto($content, &$we_doc, $templateFilename, $templateParentID){

		$textareaCode = '<we:textarea name="content" wysiwyg="true" width="800" height="600" xml="true" inlineedit="true"/>';
		$titleCode = "<we:title />";
		$descriptionCode = "<we:description />";
		$keywordsCode = "<we:keywords />";

		$title = $description = $keywords = $charset = '';
		$attr = $regs = array();

		// check if we have a body start and end tag
		if(preg_match('/<body[^>]*>(.*)<\/body>/is', $content, $regs)){
			$bodyhtml = $regs[1];
			$templateCode = preg_replace('/(.*<body[^>]*>).*(<\/body>.*)/is', "$1$textareaCode$2", $content);
		} else {
			$bodyhtml = $content;
			$templateCode = $textareaCode;
		}

		// try to get title, description, keywords and charset
		if(preg_match('/<title[^>]*>(.*)<\/title>/is', $content, $regs)){
			$title = $regs[1];
			$templateCode = preg_replace('/<title[^>]*>.*<\/title>/is', $titleCode, $templateCode);
		}
		if(preg_match('/<meta ([^>]*)name="description"([^>]*)>/is', $content, $regs)){
			if(preg_match('/content="([^"]+)"/is', $regs[1], $attr)){
				$description = $attr[1];
			} elseif(preg_match('/content="([^"]+)"/is', $regs[2], $attr)){
				$description = $attr[1];
			}
			$templateCode = preg_replace('/<meta [^>]*name="description"[^>]*>/is', $descriptionCode, $templateCode);
		}
		if(preg_match('/<meta ([^>]*)name="keywords"([^>]*)>/is', $content, $regs)){
			if(preg_match('/content="([^"]+)"/is', $regs[1], $attr)){
				$keywords = $attr[1];
			} elseif(preg_match('/content="([^"]+)"/is', $regs[2], $attr)){
				$keywords = $attr[1];
			}
			$templateCode = preg_replace('/<meta [^>]*name="keywords"[^>]*>/is', $keywordsCode, $templateCode);
		}
		if(preg_match('/<meta ([^>]*)http-equiv="content-type"([^>]*)>/is', $content, $regs)){
			if(preg_match('/content="([^"]+)"/is', $regs[1], $attr)){
				$cs = array();
				if(preg_match('/charset=([^ "\']+)/is', $attr[1], $cs)){
					$charset = $cs[1];
				}
			} elseif(preg_match('/content="([^"]+)"/is', $regs[2], $attr)){
				if(preg_match('/charset=([^ "\']+)/is', $attr[1], $cs)){
					$charset = $cs[1];
				}
			}
			$templateCode = preg_replace('/<meta [^>]*http-equiv="content-type"[^>]*>/is', '<we:charset defined="' . $charset . '">' . $charset . '</we:charset>', $templateCode);
		}

		// replace external css (link rel=stylesheet)
		preg_match_all('/<link ([^>]+)>/i', $templateCode, $regs, PREG_PATTERN_ORDER);
		if($regs != null){
			$regs2 = array();
			for($i = 0; $i < count($regs[1]); $i++){
				preg_match_all('/([^= ]+)=[\'"]?([^\'" ]+)[\'"]?/is', $regs[1][$i], $regs2, PREG_PATTERN_ORDER);
				if($regs2 != null){
					for($z = 0; $z < count($regs2[1]); $z++){
						$attribs[$regs2[1][$z]] = $regs2[2][$z];
					}
					if(isset($attribs["rel"]) && $attribs["rel"] === 'stylesheet'){
						if(!empty($attribs["href"])){
							$id = path_to_id($attribs["href"]);
							$tag = '<we:css id="' . $id . '" xml="true" ' . ((!empty($attribs["media"])) ? ' pass_media="' . $attribs["media"] . '"' : '') . '/>';
							$templateCode = str_replace($regs[0][$i], $tag, $templateCode);
						}
					}
				}
			}
		}

		// replace external js scripts
		preg_match_all('/<script ([^>]+)>.*<\/script>/isU', $templateCode, $regs, PREG_PATTERN_ORDER);
		if($regs != null){
			for($i = 0; $i < count($regs[1]); $i++){
				preg_match('/src=["\']?([^"\']+)["\']?/is', $regs[1][$i], $regs2);
				if($regs2 != null){
					$id = path_to_id($regs2[1]);
					$tag = '<we:js id="' . $id . '" xml="true" />';
					$templateCode = str_replace($regs[0][$i], $tag, $templateCode);
				}
			}
		}

		// check if there is allready a template with the same content


		$newTemplateID = f('SELECT l.DID FROM ' . LINK_TABLE . ' l JOIN ' . CONTENT_TABLE . ' c ON l.CID=c.ID WHERE c.Dat="' . $GLOBALS['DB_WE']->escape(
				$templateCode) . '" AND l.DocumentTable="' . stripTblPrefix(TEMPLATES_TABLE) . '"');

		if(!$newTemplateID){
			// create Template


			$newTemplateFilename = $templateFilename;
			$GLOBALS['DB_WE']->query("SELECT Filename FROM " . TEMPLATES_TABLE . " WHERE ParentID=" . abs($templateParentID) . " AND Filename LIKE '" . $GLOBALS['DB_WE']->escape($templateFilename) . "%'");
			$result = array();
			if($GLOBALS['DB_WE']->num_rows()){
				while($GLOBALS['DB_WE']->next_record()){
					$result[] = $GLOBALS['DB_WE']->f("Filename");
				}
			}
			$z = 1;
			while(in_array($newTemplateFilename, $result)){
				$newTemplateFilename = $templateFilename . $z;
				$z++;
			}
			$templateObject = new we_template();
			$templateObject->we_new();
			$templateObject->CreationDate = time();
			$templateObject->ID = 0;
			$templateObject->OldPath = "";
			$templateObject->Extension = ".tmpl";
			$templateObject->Filename = $newTemplateFilename;
			$templateObject->Text = $templateObject->Filename . $templateObject->Extension;
			$templateObject->setParentID($templateParentID);
			$templateObject->Path = $templateObject->ParentPath . ($templateParentID ? "/" : "") . $templateObject->Text;
			$templateObject->OldPath = $templateObject->Path;
			$templateObject->setElement('data', $templateCode, "txt");
			$templateObject->we_save();
			$templateObject->we_publish();
			$templateObject->setElement('Charset', $charset);
			$newTemplateID = $templateObject->ID;
		}

		$we_doc->setTemplateID($newTemplateID);
		$we_doc->setElement("content", $bodyhtml);
		$we_doc->setElement("Title", $title);
		$we_doc->setElement("Keywords", $keywords);
		$we_doc->setElement("Description", $description);
		$we_doc->setElement("Charset", $charset);
	}

	/**
	 * internal function, used by _importWebEditionPage() => don't call directly!
	 * @param $content string
	 * @param &$we_doc we_webEditionDocument
	 * @param $templateId int
	 * @param $useRegex boolean
	 * @param $fieldValues array
	 * @param $dateFormat string
	 * @param $dateFormatValue string
	 * @static
	 */
	private static function _importSpecify($content, &$we_doc, $templateId, $useRegex, $fieldValues, $dateFormat, $dateFormatValue){

		// TODO width & height of image
		// get field infos of template
		$_templateFields = self::_getFieldsFromTemplate($templateId);

		foreach($fieldValues as $field){
			if(!empty($field["pre"]) && !empty($field["post"]) && !empty($field["name"])){
				$fieldval = '';
				$field['pre'] = str_replace(array("\r\n", "\r"), "\n", $field['pre']);
				$field['post'] = str_replace(array("\r\n", "\n"), "\n", $field['post']);
				if(!$useRegex){
					$prepos = strpos($content, $field["pre"]);
					$postpos = strpos($content, $field["post"], abs($prepos));
					if($prepos !== false && $postpos !== false && $prepos < $postpos){
						$prepos += strlen($field["pre"]);
						$fieldval = substr($content, $prepos, $postpos - $prepos);
					}
				} else {
					$regs = array();
					if(preg_match('/' . preg_quote($field["pre"], '/') . '(.+)' . preg_quote($field["post"], '/') . '/isU', $content, $regs)){
						$fieldval = $regs[1];
					}
				}
				// only set field if field exists in template
				if(isset($_templateFields[$field["name"]])){

					if($_templateFields[$field["name"]] === "date"){ // import date fields
						switch($dateFormat){
							case "unix" :
								$fieldval = abs($fieldval);
								break;

							case "gmt" :
								$fieldval = we_import_functions::date2Timestamp(trim($fieldval), "");
								break;

							case "own" :
								$fieldval = we_import_functions::date2Timestamp(trim($fieldval), $dateFormatValue);
								break;
						}
						$we_doc->setElement($field["name"], abs($fieldval), "date");
					} elseif($_templateFields[$field["name"]] === "img"){ // import image fields
						if(preg_match('/<[^>]+src=["\']?([^"\' >]+)[^"\'>]?[^>]*>/i', $fieldval, $regs)){ // only if image tag has a src attribute
							$src = $regs[1];
							$imgId = path_to_id($src);
							$we_doc->elements[$field["name"]] = array(
								"type" => "img",
								"bdid" => $imgId
							);
						}
					} else {
						$we_doc->setElement($field["name"], trim($fieldval));
					}
				}
			}
		}
		$we_doc->setTemplateID($templateId);
	}

	/**
	 * converts an external  link (src or href) into an internal
	 * @param $href string
	 * @return string
	 * @static
	 */
	private static function _makeInternalLink($href){
		$ct = '';
		$id = path_to_id_ct($href, FILE_TABLE, $ct);
		if(substr($ct, 0, 5) === "text/"){
			$href = we_base_link::TYPE_INT_PREFIX . $id;
		} elseif($ct == we_base_ContentTypes::IMAGE){
			if(strpos($href, "?") === false){
				$href .= '?id=' . $id;
			}
		}
		return $href;
	}

	/**
	 * converts all external links in a HTML page to internal links
	 * @param $content string
	 * @return string
	 * @static
	 */
	private static function _external_to_internal($content){
		// replace hrefs
		$regs = $regs2 = array();
		preg_match_all('/(<[^>]+href=["\']?)([^"\' >]+)([^"\'>]?[^>]*>)/i', $content, $regs, PREG_PATTERN_ORDER);
		if($regs != null){
			for($i = 0; $i < count($regs[2]); $i++){
				$orig_href = $regs[2][$i];
				$new_href = self::_makeInternalLink($orig_href);
				if($new_href != $orig_href){
					$newTag = $regs[1][$i] . $new_href . $regs[3][$i];
					$content = str_replace($regs[0][$i], $newTag, $content);
				}
			}
		}
		// replace src (same as href!!)
		preg_match_all('/(<[^>]+src=["\']?)([^"\' >]+)([^"\'>]?[^>]*>)/i', $content, $regs, PREG_PATTERN_ORDER);
		if($regs != null){
			for($i = 0; $i < count($regs[2]); $i++){
				$orig_href = $regs[2][$i];
				$new_href = self::_makeInternalLink($orig_href);
				if($new_href != $orig_href){
					$newTag = $regs[1][$i] . $new_href . $regs[3][$i];
					$content = str_replace($regs[0][$i], $newTag, $content);
				}
			}
		}
		// url() in styles with style=""
		preg_match_all('/(<[^>]+style=")([^"]+)("[^>]*>)/i', $content, $regs, PREG_PATTERN_ORDER);
		if($regs != null){
			for($i = 0; $i < count($regs[2]); $i++){
				$style = $regs[2][$i];
				$newStyle = $style;
				preg_match_all('/(url\(\'?)([^\'\)]+)(\'?\))/i', $style, $regs2, PREG_PATTERN_ORDER);
				if($regs2 != null){
					for($z = 0; $z < count($regs2[2]); $z++){
						$orig_url = $regs2[2][$z];
						$new_url = self::_makeInternalLink($orig_url);
						if($orig_url != $new_url){
							$newStyle = str_replace($orig_url, $new_url, $newStyle);
						}
					}
				}
				if($newStyle != $style){
					$newTag = $regs[1][$i] . $newStyle . $regs[3][$i];
					$content = str_replace($regs[0][$i], $newTag, $content);
				}
			}
		}

		// url() in styles with style=''
		preg_match_all('/(<[^>]+style=\')([^\']+)(\'[^>]*>)/i', $content, $regs, PREG_PATTERN_ORDER);
		if($regs != null){
			for($i = 0; $i < count($regs[2]); $i++){
				$style = $regs[2][$i];
				$newStyle = $style;
				preg_match_all('/(url\("?)([^"\)]+)("?\))/i', $style, $regs2, PREG_PATTERN_ORDER);
				if($regs2 != null){
					for($z = 0; $z < count($regs2[2]); $z++){
						$orig_url = $regs2[2][$z];
						$new_url = self::_makeInternalLink($orig_url);
						if($orig_url != $new_url){
							$newStyle = str_replace($orig_url, $new_url, $newStyle);
						}
					}
				}
				if($newStyle != $style){
					$newTag = $regs[1][$i] . $newStyle . $regs[3][$i];
					$content = str_replace($regs[0][$i], $newTag, $content);
				}
			}
		}

		// url() in style tags
		preg_match_all('/(<style[^>]*>)(.*)(<\/style>)/isU', $content, $regs, PREG_PATTERN_ORDER);
		if($regs != null){
			for($i = 0; $i < count($regs[2]); $i++){
				$style = $regs[2][$i];
				$newStyle = $style;
				// url() in styles with style=''
				preg_match_all('/(url\([\'"]?)([^\'"\)]+)([\'"]?\))/iU', $style, $regs2, PREG_PATTERN_ORDER);
				if($regs2 != null){
					for($z = 0; $z < count($regs2[2]); $z++){
						$orig_url = $regs2[2][$z];
						$new_url = self::_makeInternalLink($orig_url);
						if($orig_url != $new_url){
							$newStyle = str_replace(
								$regs2[0][$z], $regs2[1][$z] . $new_url . $regs2[3][$z], $newStyle);
						}
					}
				}
				if($newStyle != $style){
					$newTag = $regs[1][$i] . $newStyle . $regs[3][$i];
					$content = str_replace($regs[0][$i], $newTag, $content);
				}
			}
		}

		return $content;
	}

	/**
	 * this routine is called after normal import for each webEdition file. it is e.g. responsible for converting relative links to absolute links
	 *
	 * @param $path string
	 * @param $sourcePath string
	 * @param $destinationDirID int
	 * @return array
	 * @static
	 */
	public static function postprocessFile($path, $sourcePath, $destinationDirID){
		$we_docSave = isset($GLOBALS["we_doc"]) ? $GLOBALS["we_doc"] : null;

		// preparing Paths
		$path = str_replace("\\", "/", $path); // change windoof backslashes to slashes
		$sourcePath = str_replace("\\", "/", $sourcePath); // change windoof backslashes to slashes
		$sizeofdocroot = strlen(rtrim($_SERVER['DOCUMENT_ROOT'], '/')); // make sure that no ending slash is there
		$sizeofsourcePath = strlen(rtrim($sourcePath, '/')); // make sure that no ending slash is there
		$destinationDir = id_to_path($destinationDirID);
		if($destinationDir === "/"){
			$destinationDir = "";
		}
		$destinationPath = $destinationDir . substr($path, $sizeofdocroot + $sizeofsourcePath);
		$id = path_to_id($destinationPath);
		$GLOBALS["we_doc"] = new we_webEditionDocument();
		$GLOBALS["we_doc"]->initByID($id);

		// we need to get the name of the fields which needs to processed
		foreach($GLOBALS['we_doc']->elements as $fieldname => $element){
			if($fieldname != "Title" && $fieldname != "Description" && $fieldname != "Keywords" && $fieldname != "Charset"){
				switch($element["type"]){
					case "txt" :
						$GLOBALS['we_doc']->elements[$fieldname]["dat"] = self::_external_to_internal($element["dat"]);
						break;
				}
			}
		}
		//save and publish
		if(!$GLOBALS["we_doc"]->we_save()){
			$GLOBALS["we_doc"] = $we_docSave;
			return array(
				"filename" => $_FILES['we_File']["name"],
				"error" => "save_error"
			);
		}
		if(!$GLOBALS["we_doc"]->we_publish()){
			$GLOBALS["we_doc"] = $we_docSave;
			return array(
				"filename" => $_FILES['we_File']["name"],
				"error" => "publish_error"
			);
		}

		$GLOBALS["we_doc"] = $we_docSave;
		return array();
	}

	/**
	 * this routine is called from task fragment class each time a document/filder is imported
	 *
	 * @param $path string
	 * @param $contentType string
	 * @param $sourcePath string
	 * @param $destinationDirID int
	 * @param $sameName boolean
	 * @param $thumbs boolean
	 * @param $width int
	 * @param $height int
	 * @param $widthSelect string
	 * @param $heightSelect string
	 * @param $keepRatio bool
	 * @param $quality int
	 * @param $degrees int
	 * @return array
	 * @static
	 */
	public function importFile($path, $contentType, $sourcePath, $destinationDirID, $sameName, $thumbs, $width, $height, $widthSelect, $heightSelect, $keepRatio, $quality, $degrees, $importMetadata = true){
		$we_docSave = isset($GLOBALS["we_doc"]) ? $GLOBALS["we_doc"] : null;

		// preparing Paths
		$path = str_replace("\\", "/", $path); // change windoof backslashes to slashes
		$sourcePath = str_replace("\\", "/", $sourcePath); // change windoof backslashes to slashes
		$sizeofdocroot = strlen(rtrim($_SERVER['DOCUMENT_ROOT'], '/')); // make sure that no ending slash is there
		$sizeofsourcePath = strlen(rtrim($sourcePath, '/')); // make sure that no ending slash is there
		$destinationDir = id_to_path($destinationDirID);
		if($destinationDir === '/'){
			$destinationDir = '';
		}
		$destinationPath = $destinationDir . '/' . we_import_functions::correctFilename(substr($path, $sizeofdocroot + $sizeofsourcePath), true);
		$parentDirPath = dirname($destinationPath);

		$parentID = path_to_id($parentDirPath);
		$data = "";

		$we_ContentType = $contentType;

		// initializing $we_doc
		include (WE_INCLUDES_PATH . 'we_editors/we_init_doc.inc.php');

		// initialize Path Information
		$GLOBALS["we_doc"]->we_new();
		$GLOBALS["we_doc"]->ContentType = $contentType;
		$GLOBALS["we_doc"]->Text = we_import_functions::correctFilename(basename($path));
		$GLOBALS["we_doc"]->Path = $destinationPath;
		// get Data of File
		switch($contentType){
			case we_base_ContentTypes::IMAGE:
			case we_base_ContentTypes::APPLICATION:
			case we_base_ContentTypes::FLASH:
			case we_base_ContentTypes::QUICKTIME:
			case we_base_ContentTypes::VIDEO:
			case we_base_ContentTypes::AUDIO:
				break;
			default:
				if(!is_dir($path) && filesize($path)){
					$data = we_base_file::load($path);
				}
		}
		$regs = array();
		if($contentType === "folder"){
			$GLOBALS["we_doc"]->Filename = $GLOBALS["we_doc"]->Text;
		} elseif(preg_match('|^(.+)(\.[^\.]+)$|', $GLOBALS["we_doc"]->Text, $regs)){
			$GLOBALS["we_doc"]->Extension = $regs[2];
			$GLOBALS["we_doc"]->Filename = $regs[1];
		} else {
			$GLOBALS["we_doc"]->Extension = '';
			$GLOBALS["we_doc"]->Filename = $GLOBALS["we_doc"]->Text;
		}

		$GLOBALS["we_doc"]->ParentID = $parentID;
		$GLOBALS["we_doc"]->ParentPath = $GLOBALS["we_doc"]->getParentPath();
		$id = path_to_id($GLOBALS["we_doc"]->Path);

		if($id){
			if($sameName === "overwrite" || $contentType === "folder"){ // folders we dont have to rename => we can use the existing folder
				$GLOBALS["we_doc"]->initByID($id, FILE_TABLE);
			} elseif($sameName === "rename"){
				$z = 0;
				$footext = $GLOBALS["we_doc"]->Filename . "_" . $z . $GLOBALS["we_doc"]->Extension;
				while(f("SELECT ID FROM " . FILE_TABLE . " WHERE Text='" . $GLOBALS['DB_WE']->escape($footext) . "' AND ParentID='" . intval($parentID) . "'", "ID", $GLOBALS['DB_WE'])){
					$z++;
					$footext = $GLOBALS["we_doc"]->Filename . "_" . $z . $GLOBALS["we_doc"]->Extension;
				}
				$GLOBALS["we_doc"]->Text = $footext;
				$GLOBALS["we_doc"]->Filename = $GLOBALS["we_doc"]->Filename . "_" . $z;
				$GLOBALS["we_doc"]->Path = $GLOBALS["we_doc"]->getParentPath() . (($GLOBALS["we_doc"]->getParentPath() != "/") ? "/" : "") . $GLOBALS["we_doc"]->Text;
			} else {
				return array(
					"filename" => $GLOBALS["we_doc"]->Path,
					"error" => "same_name"
				);
			}
		}

		$GLOBALS["we_doc"]->IsSearchable = 0;

		// initialize Content
		switch($contentType){
			case we_base_ContentTypes::WEDOCUMENT :
				self::_importWebEditionPage($data, $GLOBALS["we_doc"], $sourcePath);
				$GLOBALS["we_doc"]->IsSearchable = 1;
				break;
			case "folder" :
				break;
			case we_base_ContentTypes::IMAGE :
				// getting attributes of image
				$foo = $GLOBALS["we_doc"]->getimagesize($path);
				$GLOBALS["we_doc"]->setElement("width", $foo[0], "attrib");
				$GLOBALS["we_doc"]->setElement("height", $foo[1], "attrib");
				$GLOBALS["we_doc"]->setElement("origwidth", $foo[0], 'attrib');
				$GLOBALS["we_doc"]->setElement("origheight", $foo[1], 'attrib');
			// no break!! because we need to do the same after the following case
			case we_base_ContentTypes::APPLICATION:
			case we_base_ContentTypes::FLASH:
			case we_base_ContentTypes::QUICKTIME:
			case we_base_ContentTypes::VIDEO:
				$GLOBALS["we_doc"]->setElement('data', $path, 'image');
				break;
			case we_base_ContentTypes::AUDIO:
				$GLOBALS["we_doc"]->setElement('data', $path, 'audio');
				break;
			case we_base_ContentTypes::HTML :
				$GLOBALS["we_doc"]->IsSearchable = 1;
			case we_base_ContentTypes::TEXT:
			case we_base_ContentTypes::JS:
			case we_base_ContentTypes::CSS:
			default :
				// set Data of File
				$GLOBALS["we_doc"]->setElement("data", $data, "txt");
		}

		if($contentType == we_base_ContentTypes::IMAGE){
			$GLOBALS["we_doc"]->Thumbs = $thumbs;
			$newWidth = ($width && $widthSelect === "percent" ?
					round(($GLOBALS["we_doc"]->getElement("origwidth") / 100) * $width) :
					$width);

			$newHeight = ($height && $widthSelect === "percent" ?
					round(($GLOBALS["we_doc"]->getElement("origheight") / 100) * $height) :
					$height);

			if(($newWidth && ($newWidth != $GLOBALS["we_doc"]->getElement("origwidth"))) || ($newHeight && ($newHeight != $GLOBALS["we_doc"]->getElement("origheight")))){
				$GLOBALS["we_doc"]->resizeImage($newWidth, $newHeight, $quality, $keepRatio);
				$width = $newWidth;
				$height = $newHeight;
			}

			if($degrees){
				$GLOBALS["we_doc"]->rotateImage(
					($degrees % 180 == 0) ? $GLOBALS["we_doc"]->getElement("origwidth") : $GLOBALS["we_doc"]->getElement(
							"origheight"), ($degrees % 180 == 0) ? $GLOBALS["we_doc"]->getElement("origheight") : $GLOBALS["we_doc"]->getElement(
							"origwidth"), $degrees, $quality);
			}
			$GLOBALS["we_doc"]->DocChanged = true;
		}
		//save and publish
		if(!$GLOBALS["we_doc"]->we_save()){
			$GLOBALS["we_doc"] = $we_docSave;
			return array(
				"filename" => $path,
				"error" => "save_error"
			);
		}
		if($contentType == we_base_ContentTypes::IMAGE && $importMetadata){
			$GLOBALS["we_doc"]->importMetaData();
			$GLOBALS["we_doc"]->we_save();
		}
		if(!$GLOBALS["we_doc"]->we_publish()){
			$GLOBALS["we_doc"] = $we_docSave;
			return array(
				"filename" => $path,
				"error" => "publish_error"
			);
		}
		$GLOBALS["we_doc"] = $we_docSave;
		return array();
	}

	/**
	 * this function is called right before starting to import the files
	 *
	 */
	private function _fillFiles(){
		// directory from which we import (real path)
		// when running on windows we have to change slashes to backslashes
		$importDirectory = str_replace('/', DIRECTORY_SEPARATOR, rtrim(rtrim($_SERVER['DOCUMENT_ROOT'], '/') . $this->from, '/'));
		$this->_files = array();
		$this->_depth = 0;
		$this->_postProcess = array();
		$this->_fillDirectories($importDirectory);
		// sort it so that webEdition files are at the end (that templates know about css and js files)


		$tmp = array();
		foreach($this->_files as $e){
			if($e["contentType"] === "folder"){
				$tmp[] = $e;
			}
		}
		foreach($this->_files as $e){
			if($e["contentType"] != "folder" && $e["contentType"] != we_base_ContentTypes::WEDOCUMENT){
				$tmp[] = $e;
			}
		}
		foreach($this->_files as $e){
			if($e["contentType"] == we_base_ContentTypes::WEDOCUMENT){
				$tmp[] = $e;
			}
		}

		$this->_files = $tmp;

		foreach($this->_postProcess as $e){
			$this->_files[] = $e;
		}
	}

	/**
	 * this function fills the $this->files and $this->_postProcess arrays
	 * @param $importDirectory string
	 */
	private function _fillDirectories($importDirectory){
		update_time_limit(60);

		$weDirectory = rtrim(WEBEDITION_PATH, '/');

		if($importDirectory == $weDirectory){ // we do not import stuff from the webEdition home dir
			return;
		}

		// go throuh all files of the directory
		$d = dir($importDirectory);
		while(false !== ($entry = $d->read())){
			switch($entry){
				default:
					if(!((strlen($entry) >= 2) && substr($entry, 0, 2) === "._")){
						break;
					}
				case '.':
				case '..':
					continue 2;
			}
			// now we have to check if the file should be imported
			$PathOfEntry = $importDirectory . $this->_slash . $entry;

			if((strpos($PathOfEntry, $weDirectory) !== false) ||
				(!is_dir($PathOfEntry) && ($this->maxSize && (filesize($PathOfEntry) > (abs($this->maxSize) * 1024 * 1024))))){
				continue;
			}
			$contentType = getContentTypeFromFile($PathOfEntry);
			$importIt = false;

			switch($contentType){
				case we_base_ContentTypes::IMAGE:
					if($this->images){
						$importIt = true;
					}
					break;
				case we_base_ContentTypes::HTML:
					if($this->htmlPages){
						if($this->createWePages){
							$contentType = we_base_ContentTypes::WEDOCUMENT;
							// webEdition files needs to be post processed (external links => internal links)
							$this->_postProcess[] = array(
								"path" => $PathOfEntry,
								"contentType" => "post/process",
								"sourceDir" => $this->from,
								"destDirID" => $this->to
							);
						}
						$importIt = true;
					}
					break;
				case we_base_ContentTypes::VIDEO:
					if($this->flashmovies){//FIXME: has to be video!
						$importIt = true;
					}
					break;
				case we_base_ContentTypes::AUDIO:
					if($this->other){//FIXME: has to be audio!
						$importIt = true;
					}
					break;
				case we_base_ContentTypes::FLASH:
					if($this->flashmovies){
						$importIt = true;
					}
					break;
				case we_base_ContentTypes::QUICKTIME:
					if($this->quicktime){
						$importIt = true;
					}
					break;
				case we_base_ContentTypes::JS:
					if($this->js){
						$importIt = true;
					}
					break;
				case we_base_ContentTypes::TEXT:
					if($this->text){
						$importIt = true;
					}
					break;
				case we_base_ContentTypes::CSS:
					if($this->css){
						$importIt = true;
					}
					break;
				case "folder" :
					$importIt = false;
					break;
				default :
					if($this->other){
						$importIt = true;
					}
					break;
			}

			if($importIt){
				$this->_files[] = array(
					"path" => $PathOfEntry,
					"contentType" => $contentType,
					"sourceDir" => $this->from,
					"destDirID" => $this->to,
					"sameName" => $this->sameName,
					"thumbs" => $this->thumbs,
					"width" => $this->width,
					"height" => $this->height,
					"widthSelect" => $this->widthSelect,
					"heightSelect" => $this->heightSelect,
					"keepRatio" => $this->keepRatio,
					"quality" => $this->quality,
					"degrees" => $this->degrees,
					"importMetadata" => $this->importMetadata
				);
			}
			if($contentType === "folder"){
				if(($this->depth == -1) || (abs($this->depth) > $this->_depth)){
					$this->_files[] = array(
						"path" => $PathOfEntry,
						"contentType" => $contentType,
						"sourceDir" => $this->from,
						"destDirID" => $this->to,
						"sameName" => $this->sameName,
						"thumbs" => "",
						"width" => "",
						"height" => "",
						"widthSelect" => "",
						"heightSelect" => "",
						"keepRatio" => "",
						"quality" => "",
						"degrees" => "",
						"importMetadata" => 0
					);
					$this->_depth++;
					$this->_fillDirectories($PathOfEntry);
					$this->_depth--;
				}
			}
		}
		$d->close();
	}

	/**
	 * returns hidden fields
	 * @return string
	 */
	private function _getHiddensHTML(){
		return
			we_html_element::htmlHiddens(array(
				"we_cmd[0]" => "siteImport",
				"cmd" => "buttons",
				"step" => 1));
	}

	private function _getFrameset(){
		$body = we_html_element::htmlBody(array('id' => 'weMainBody')
				, we_html_element::htmlDiv(array('style' => 'position:absolute;top:0px;bottom:0px;left:0px;right:0px;')
					, we_html_element::htmlIFrame('siteimportcontent', WEBEDITION_DIR . "we_cmd.php?we_cmd[0]=siteImport&cmd=content", 'position:absolute;top:0px;bottom:40px;left:0px;right:0px;') .
					we_html_element::htmlIFrame('siteimportbuttons', "we_cmd.php?we_cmd[0]=siteImport&cmd=buttons", 'position:absolute;height:40px;bottom:0px;left:0px;right:0px;overflow: hidden', '', '', false) .
					we_html_element::htmlIFrame('siteimportcmd', "about:blank", 'position:absolute;bottom:0px;height:0px;left:0px;right:0px;overflow: hidden;')
		));

		return $this->_getHtmlPage($body);
	}

	private function _getHtmlPage($body, $js = ""){
		return we_html_tools::getHtmlTop(''/* FIXME: missing title */, '', '', STYLESHEET . $js, $body);
	}

}
