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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
class weNavigationRuleFrames{

	var $Frameset = '/webEdition/we/include/we_tools/navigation/edit_navigation_rules_frameset.php';
	var $Controller;
	var $db;

	function __construct(){
		$this->Controller = new weNavigationRuleControl();
		$this->db = new DB_WE();
		$yuiSuggest = & weSuggest::getInstance();
	}

	function getHTML($what){
		switch($what){
			case 'frameset' :
				print $this->getHTMLFrameset();
				break;
			case 'content' :
				print $this->getHTMLContent();
				break;
			default :
				t_e(__FILE__ . ": unknown reference $what");
		}
	}

	function getHTMLFrameset(){
		return we_html_tools::htmlTop(g_l('navigation', '[menu_highlight_rules]')) . '
   <frameset rows="*,0" framespacing="0" border="1" frameborder="Yes">
   <frame src="' . $this->Frameset . '?pnt=content" name="content" scrolling=no>
   <frame src="' . HTML_DIR . 'white.html" name="cmdFrame" scrolling=no noresize>
  </frameset>
</head>
 <body background="' . IMAGE_DIR . 'backgrounds/aquaBackground.gif" style="background-color:#bfbfbf; background-repeat:repeat;margin:0px 0px 0px 0px">
 </body>
</html>';
	}

	function getHTMLContent(){
		// content contains textarea with all so far existing rules
		$yuiSuggest = & weSuggest::getInstance();

		$allRules = weNavigationRuleControl::getAllNavigationRules();

		$_rules = array();

		foreach($allRules as $_navigationRule){
			$_rules["$_navigationRule->ID"] = $_navigationRule->NavigationName;
		}
		asort($_rules);
		$yuiSuggest = & weSuggest::getInstance();
		$parts = array(
			array(
				'headline' => g_l('navigation', '[rules][available_rules]'),
				'space' => 200,
				'html' => $yuiSuggest->getYuiJsFiles() . '<table border="0" cellpadding="0" cellspacing="0">
										<tr><td>' . we_html_tools::htmlSelect(
					'navigationRules', $_rules, 8, '', false, ' style="width: 275px;" onclick="we_cmd(\'edit_navigation_rule\', this.value)"') . '</td>
											<td>' . we_html_tools::getPixel(10, 1) . '</td>
											<td valign="top">
												' . we_button::create_button(
					'new_entry', 'javascript:we_cmd("new_navigation_rule")') . '<div style="height:10px;"></div>
												' . we_button::create_button(
					'delete', 'javascript:we_cmd("delete_navigation_rule")') . '
											</td>
										</tr>
										</table>'
			),
			// build the formular



			array(
				'headline' => g_l('navigation', '[rules][rule_name]'),
				'space' => 200,
				'html' => we_html_tools::htmlTextInput('NavigationName', 24, '', '', 'style="width: 275px;"'),
				'noline' => 1
			)
		);

		$yuiSuggest->setAcId("NavigationIDPath");
		$yuiSuggest->setContentType("folder,weNavigation");
		$yuiSuggest->setInput('NavigationIDPath');
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setTable(NAVIGATION_TABLE);
		$yuiSuggest->setResult('NavigationID');
		$yuiSuggest->setSelector("Docselector");
		$yuiSuggest->setWidth(275);
		$yuiSuggest->setSelectButton(
			we_button::create_button(
				'select', "javascript:we_cmd('openSelector', document.we_form.elements['NavigationID'].value, '" . NAVIGATION_TABLE . "', 'document.we_form.elements[\\'NavigationID\\'].value', 'document.we_form.elements[\\'NavigationIDPath\\'].value')"), 10);

		$weAcSelector = $yuiSuggest->getHTML();

		array_push(
			$parts, array(
			'headline' => g_l('navigation', '[rules][rule_navigation_link]'),
			'space' => 200,
			'html' => $weAcSelector,
			'noline' => 1
		));

		$selectionTypes = array(
			weNavigation::STPYE_DOCTYPE => g_l('global', "[documents]")
		);
		if(defined('OBJECT_TABLE')){
			$selectionTypes[weNavigation::STPYE_CLASS] = g_l('global', "[objects]");
		}


		$parts[] = array(
			'headline' => g_l('navigation', '[rules][rule_applies_for]'),
			'space' => 200,
			'html' => we_html_tools::htmlSelect(
				'SelectionType', $selectionTypes, 1, 0, false, "style=\"width: 275px;\" onchange=\"switchType(this.value);\"")
		);

		// getDoctypes
		$docTypes = array(
			0 => g_l('navigation', '[no_entry]')
		);
		$q = getDoctypeQuery($this->db);
		$this->db->query("SELECT ID,DocType FROM " . DOC_TYPES_TABLE . " $q");
		while($this->db->next_record()) {
			$docTypes[$this->db->f("ID")] = $this->db->f('DocType');
		}

		$yuiSuggest->setAcId("FolderIDPath");
		$yuiSuggest->setContentType("folder");
		$yuiSuggest->setInput('FolderIDPath');
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setResult('FolderID');
		$yuiSuggest->setSelector("Dirselector");
		$yuiSuggest->setWidth(275);
		//javascript:we_cmd('openDirselector', document.we_form.elements['FolderID'].value, '" . FILE_TABLE . "', 'document.we_form.elements[\\'FolderID\\'].value', 'document.we_form.elements[\\'FolderIDPath\\'].value')
		$wecmdenc1 = we_cmd_enc("document.we_form.elements['FolderID'].value");
		$wecmdenc2 = we_cmd_enc("document.we_form.elements['FolderIDPath'].value");
		$wecmdenc3 = '';
		$yuiSuggest->setSelectButton(
			we_button::create_button(
				'select', "javascript:we_cmd('openDirselector', document.we_form.elements['FolderID'].value, '" . FILE_TABLE . "', '" . $wecmdenc1 . "', '" . $wecmdenc2 . "')"), 10);
		$yuiSuggest->setTrashButton(
			we_button::create_button(
				"image:btn_function_trash", "javascript:document.we_form.elements['FolderID'].value = '';document.we_form.elements['FolderIDPath'].value = '';"), 10);

		$weAcSelector = $yuiSuggest->getHTML();

		$formTable = '<table border="0" cellspacing="0" cellpadding="0">
<tr><td width="200">' . we_html_tools::getPixel(200, 1) . '</td></tr>
<tr id="trFolderID">
	<td class="weMultiIconBoxHeadline" valign="top">' . g_l('navigation', '[rules][rule_folder]') . '</td>
	<td colspan="5">' . $weAcSelector . '</td>
</tr>
<tr id="trDoctypeID">
	<td style="height: 40px;" class="weMultiIconBoxHeadline">' . g_l('navigation', '[rules][rule_doctype]') . '</td>
	<td>' . we_html_tools::htmlSelect(
				'DoctypeID', $docTypes, 1, 0, false, "style=\"width: 275px;\"") . '</td>
</tr>';

		if(defined('OBJECT_TABLE')){

			$yuiSuggest->setAcId("ClassIDPath");
			$yuiSuggest->setContentType("folder,object");
			$yuiSuggest->setInput("ClassIDPath");
			$yuiSuggest->setMaxResults(10);
			$yuiSuggest->setMayBeEmpty(true);
			$yuiSuggest->setResult('ClassID');
			$yuiSuggest->setSelector("Docselector");
			$yuiSuggest->setTable(OBJECT_TABLE);
			$yuiSuggest->setWidth(275);
			//javascript:we_cmd('openDocselector', document.we_form.elements['ClassID'].value, '" . OBJECT_TABLE . "', 'document.we_form.elements[\\'ClassID\\'].value', 'document.we_form.elements[\\'ClassIDPath\\'].value', 'top.opener.we_cmd(\"get_workspaces\");')
			$wecmdenc1 = we_cmd_enc("document.we_form.elements['ClassID'].value");
			$wecmdenc2 = we_cmd_enc("document.we_form.elements['ClassIDPath'].value");
			$wecmdenc3 = we_cmd_enc("top.opener.we_cmd('get_workspaces');");
			$yuiSuggest->setSelectButton(we_button::create_button('select', "javascript:we_cmd('openDocselector', document.we_form.elements['ClassID'].value, '" . OBJECT_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "')"), 10);

			$weAcSelector = $yuiSuggest->getHTML();

			$formTable .=
				'<tr id="trClassID">
	<td class="weMultiIconBoxHeadline">' . g_l('navigation', '[rules][rule_class]') . '</td>
	<td colspan="3">' . $weAcSelector . '</td>
</tr>
<tr id="trWorkspaceID">
	<td style="height: 40px;" class="weMultiIconBoxHeadline">' . g_l('navigation', '[rules][rule_workspace]') . '</td>
	<td>' . we_html_tools::htmlSelect(
					'WorkspaceID', array(), 1, '', false, "style=\"width: 275px;\"") . '</td>
</tr>';
		}
		$formTable .= '
<tr id="trCategories">
	<td style="width: 200px;" valign="top" class="weMultiIconBoxHeadline">' . g_l('navigation', '[rules][rule_categories]') . '</td>
	<td colspan="4">
		' . $this->getHTMLCategory() . '
	</td>
</tr>
</table>';

		array_push($parts, array(
			'html' => $formTable, 'space' => 0
		));

		$saveButton = we_button::create_button('save', 'javascript:we_cmd("save_navigation_rule");');
		$closeButton = we_button::create_button('close', 'javascript:top.window.close();');
		$acErrorMsg = we_message_reporting::getShowMessageCall(
				g_l('alert', '[save_error_fields_value_not_valid]'), we_message_reporting::WE_MESSAGE_ERROR);
		return we_html_tools::htmlTop() . STYLESHEET .
			we_html_element::jsScript(JS_DIR . 'formFunctions.js') .
			we_html_element::jsScript(JS_DIR . 'windows.js') . we_html_element::jsElement('

var allFields = new Array("FolderID", "DoctypeID", "ClassID", "WorkspaceID");
var resetFields = new Array("NavigationName", "NavigationID", "NavigationIDPath", "FolderID", "FolderIDPath", "DoctypeID", "ClassID", "ClassIDPath", "WorkspaceID");

var dependencies = new Array();
dependencies["' . weNavigation::STPYE_CLASS . '"] = new Array("ClassID", "WorkspaceID", "Categories");
dependencies["' . weNavigation::STPYE_DOCTYPE . '"] = new Array("FolderID", "DoctypeID", "Categories");


function switchType(value) {

	// 1st hide all
	for (i=0; i<allFields.length;i++) {
		if (elem = document.getElementById("tr" + allFields[i])) {
			elem.style.display = "none";
		}
	}

	// show needed
	if (dependencies[value]) {

		for (j=0;j<dependencies[value].length;j++) {
			if (elem = document.getElementById("tr" + dependencies[value][j])) {
				elem.style.display = "";
			}
		}
	}
}

function clearNavigationForm() {

	for (i=0;i<resetFields.length;i++) {
		if (document.we_form[resetFields[i]]) {
			document.we_form[resetFields[i]].value = "";
		}
	}

	document.we_form["ID"].value="0";
	weSelect.removeOptions("WorkspaceID");

	removeAllCats();
}

function we_cmd(){

	var args = "";
	var url = "' . WEBEDITION_DIR . 'we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+escape(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}

	switch (arguments[0]){

		case "switchType":
			switchType(arguments[1]);
		break;

		case "new_navigation_rule":
			clearNavigationForm();
		break;

		case "save_navigation_rule":
			var isValid=1;
			if(document.we_form.SelectionType.options[0].selected==true){
				isValid=YAHOO.autocoml.isValidById("yuiAcInputFolderIDPath");
			} else if(!!document.we_form.SelectionType.options[1] && document.we_form.SelectionType.options[1].selected==true){
				isValid=YAHOO.autocoml.isValidById("yuiAcInputClassIDPath");
			}
			if(isValid && YAHOO.autocoml.isValidById("yuiAcInputNavigationIDPath")) {
				weInput.setValue("cmd", "save_navigation_rule");
				document.we_form.submit();
			} else {
				' . $acErrorMsg . '
				return false;
			}
		break;

		case "delete_navigation_rule":
			if (navId = document.we_form["navigationRules"].value) {
			    document.we_form["NavigationName"].value = "";
				weInput.setValue("cmd", "delete_navigation_rule");
				weInput.setValue("ID", navId);
				document.we_form.submit();
			}
		break;

		case "edit_navigation_rule":
			weInput.setValue("cmd", "edit_navigation_rule");
			weInput.setValue("ID", arguments[1]);
			document.we_form.submit();
		break;

		case "get_workspaces":
			weInput.setValue("cmd", "get_workspaces");
			document.we_form.submit();
		break;

		case "openDirselector":
			new jsWindow(url,arguments[0],-1,-1,' . WINDOW_DIRSELECTOR_WIDTH . ',' . WINDOW_DIRSELECTOR_HEIGHT . ',true,true,true,true);
		break;
		case "openCatselector":
			new jsWindow(url,arguments[0],-1,-1,' . WINDOW_CATSELECTOR_WIDTH . ',' . WINDOW_CATSELECTOR_HEIGHT . ',true,true,true,true);
		break;
		case "openSelector":
			new jsWindow(url,arguments[0],-1,-1,' . WINDOW_SELECTOR_WIDTH . ',' . WINDOW_SELECTOR_HEIGHT . ',true,true,true,true);
		break;
		case "openDocselector":
			new jsWindow(url,arguments[0],-1,-1,' . WINDOW_DOCSELECTOR_WIDTH . ',' . WINDOW_DOCSELECTOR_HEIGHT . ',true,true,true,true);
		break;
	}
}') . '
</head>
<body onload="switchType(document.we_form[\'SelectionType\'].value)" class="weDialogBody">
	<form name="we_form" target="cmdFrame" action="' . $this->Frameset . '">' .
			we_html_tools::hidden('cmd', '') .
			we_html_tools::hidden('ID', '0') .
			we_multiIconBox::getHTML(
				'navigationRules', "100%", $parts, 30, we_button::position_yes_no_cancel($saveButton, null, $closeButton), -1, '', '', false, g_l('navigation', '[rules][navigation_rules]')) . '
	</form>' .
			$yuiSuggest->getYuiCss() . $yuiSuggest->getYuiJs() .
			'</body></html>';
	}

	function getHTMLCategory(){

		$addbut = we_button::create_button(
				"add", "javascript:we_cmd('openCatselector','','" . CATEGORY_TABLE . "','','','fillIDs();opener.addCat(top.allPaths, top.allIDs);')");
		$del_but = addslashes(
			we_html_element::htmlImg(
				array(
					'src' => BUTTONS_DIR . 'btn_function_trash.gif',
					'onclick' => 'javascript:#####placeHolder#####;',
					'style' => 'cursor: pointer; width: 27px;'
			)));

		$js = we_html_element::jsScript(JS_DIR . 'utils/multi_edit.js');

		$variant_js = '
			var categories_edit = new multi_edit("categories",document.we_form,0,"' . $del_but . '",400,false);
			categories_edit.addVariant();
			document.we_form.CategoriesControl.value = categories_edit.name;

		';

		$variant_js .= '
			categories_edit.showVariant(0);
		';

		$js .= we_html_element::jsElement($variant_js);

		$table = new we_html_table(
				array(
					'id' => 'CategoriesBlock',
					'style' => 'display: block;',
					'cellpadding' => 0,
					'cellspacing' => 0,
					'border' => 0
				),
				3,
				1);

		$table->setColContent(
			0, 0, we_html_element::htmlDiv(
				array(
					'id' => 'categories',
					'class' => 'blockWrapper',
					'style' => 'width: 380px; height: 80px; border: #AAAAAA solid 1px;'
			)));

		$table->setColContent(1, 0, we_html_tools::getPixel(5, 5));

		$table->setCol(
			2, 0, array(
			'colspan' => '2', 'align' => 'right'
			), we_button::create_button_table(
				array(
					we_button::create_button("delete_all", "javascript:removeAllCats()"), $addbut
			)));

		return $table->getHtml() . we_html_tools::hidden('CategoriesControl', 0) . we_html_tools::hidden('CategoriesCount', 0) . $js . we_html_element::jsElement(
				'

							function removeAllCats(){

								if(categories_edit.itemCount>0){
									while(categories_edit.itemCount>0){
										categories_edit.delItem(categories_edit.itemCount);
									}
								}
								document.we_form.CategoriesCount.value = categories_edit.itemCount;
							}

							function addCat(paths, ids){

								var path = paths.split(",");
								var id = ids.split(",");
								for (var i = 0; i < path.length; i++) {
									if(path[i]!="") {
										categories_edit.addItem();
										categories_edit.setItem(0,(categories_edit.itemCount-1),path[i], id[i]);
									}
								}
								categories_edit.showVariant(0);
								document.we_form.CategoriesCount.value = categories_edit.itemCount;
							}
					');
	}

}