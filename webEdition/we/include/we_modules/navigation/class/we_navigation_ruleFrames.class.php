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
class we_navigation_ruleFrames{
	public $Frameset;
	public $Controller;
	public $db;

	function __construct(){
		$this->Frameset = WEBEDITION_DIR . 'we_showMod.php?mod=navigation';
		$this->Controller = new we_navigation_ruleControl();
		$this->db = new DB_WE();
		$yuiSuggest = &weSuggest::getInstance();
	}

	function getHTML($what){
		switch($what){
			case 'ruleFrameset' :
				echo $this->getHTMLFrameset();
				break;
			case 'ruleContent' :
				echo $this->getHTMLContent();
				break;
			default :
				t_e(__FILE__ . ": unknown reference $what");
		}
	}

	function getHTMLFrameset(){
		return we_html_tools::getHtmlTop(g_l('navigation', '[menu_highlight_rules]')) . STYLESHEET . '</head>' .
			we_html_element::htmlBody(array('class ' => 'weDialogBody')
				, we_html_element::htmlDiv(array('style' => 'position:absolute;top:0px;bottom:0px;left:0px;right:0px;')
					, we_html_element::htmlIFrame('content', $this->Frameset . '&pnt=ruleContent', 'position:absolute;top:0px;bottom:0px;left:0px;right:0px;overflow: hidden') .
					we_html_element::htmlIFrame('cmdFrame', "about:blank", 'position:absolute;bottom:0px;height:0px;left:0px;right:0px;overflow: hidden;')
			)) .
			'</html>';
	}

	function getHTMLContent(){
		// content contains textarea with all so far existing rules
		$yuiSuggest = & weSuggest::getInstance();

		$allRules = we_navigation_ruleControl::getAllNavigationRules();

		$_rules = array();

		foreach($allRules as $_navigationRule){
			$_rules[$_navigationRule->ID] = $_navigationRule->NavigationName;
		}
		asort($_rules);

		$parts = array(
			array(
				'headline' => g_l('navigation', '[rules][available_rules]'),
				'space' => 200,
				'html' => weSuggest::getYuiFiles() . '<table class="default">
	<tr><td>' . we_html_tools::htmlSelect('navigationRules', $_rules, 8, '', false, array('style' => "width: 275px;", 'onclick' => 'we_cmd(\'navigation_edit_rule\', this.value)')) . '</td>
		<td style="vertical-align:top">' . we_html_button::create_button('new_entry', 'javascript:we_cmd("new_navigation_rule")') . '<div style="height:10px;"></div>' . we_html_button::create_button('delete', 'javascript:we_cmd("delete_navigation_rule")') . '
		</td>
	</tr>
</table>'
			),
			array(
				'headline' => g_l('navigation', '[rules][rule_name]'),
				'space' => 200,
				'html' => we_html_tools::htmlTextInput('NavigationName', 24, '', '', 'style="width: 275px;"'),
				'noline' => 1
			),
		);

		$yuiSuggest->setAcId("NavigationIDPath");
		$yuiSuggest->setContentType("folder,weNavigation");
		$yuiSuggest->setInput('NavigationIDPath');
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setTable(NAVIGATION_TABLE);
		$yuiSuggest->setResult('NavigationID');
		$yuiSuggest->setSelector(weSuggest::DocSelector);
		$yuiSuggest->setWidth(275);
		$yuiSuggest->setSelectButton(
			we_html_button::create_button(
				'select', "javascript:we_cmd('we_selector_file', document.we_form.elements.NavigationID.value, '" . NAVIGATION_TABLE . "', 'document.we_form.elements.NavigationID.value', 'document.we_form.elements.NavigationIDPath.value')"), 10);

		$weAcSelector = $yuiSuggest->getHTML();

		$parts[] = array(
			'headline' => g_l('navigation', '[rules][rule_navigation_link]'),
			'space' => 200,
			'html' => $weAcSelector,
			'noline' => 1
		);

		$selectionTypes = array(
			we_navigation_navigation::STYPE_DOCTYPE => g_l('global', '[documents]')
		);
		if(defined('OBJECT_TABLE')){
			$selectionTypes[we_navigation_navigation::STYPE_CLASS] = g_l('global', '[objects]');
		}

		$parts[] = array(
			'headline' => g_l('navigation', '[rules][rule_applies_for]'),
			'space' => 200,
			'html' => we_html_tools::htmlSelect(
				'SelectionType', $selectionTypes, 1, 0, false, array('style' => "width: 275px;", 'onchange' => "switchType(this.value);"))
		);

// getDoctypes
		$docTypes = array(
			0 => g_l('navigation', '[no_entry]')
		);
		$dtq = we_docTypes::getDoctypeQuery($this->db);
		$this->db->query('SELECT dt.ID,dt.DocType FROM ' . DOC_TYPES_TABLE . ' dt LEFT JOIN ' . FILE_TABLE . ' dtf ON dt.ParentID=dtf.ID ' . $dtq['join'] . ' WHERE ' . $dtq['where']);
		while($this->db->next_record()){
			$docTypes[$this->db->f('ID')] = $this->db->f('DocType');
		}

		$yuiSuggest->setAcId("FolderIDPath");
		$yuiSuggest->setContentType("folder");
		$yuiSuggest->setInput('FolderIDPath');
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setResult('FolderID');
		$yuiSuggest->setSelector(weSuggest::DirSelector);
		$yuiSuggest->setWidth(275);
		$wecmdenc1 = we_base_request::encCmd("document.we_form.elements.FolderID.value");
		$wecmdenc2 = we_base_request::encCmd("document.we_form.elements.FolderIDPath.value");
		$yuiSuggest->setSelectButton(we_html_button::create_button('select', "javascript:we_cmd('we_selector_directory', document.we_form.elements.FolderID.value, '" . FILE_TABLE . "', '" . $wecmdenc1 . "', '" . $wecmdenc2 . "')"), 10);
		$yuiSuggest->setTrashButton(we_html_button::create_button(we_html_button::TRASH, "javascript:document.we_form.elements.FolderID.value = '';document.we_form.elements.FolderIDPath.value = '';"));

		$weAcSelector = $yuiSuggest->getHTML();

		$formTable = '<table class="default">
<tr id="trFolderID">
	<td class="weMultiIconBoxHeadline" style="vertical-align:top">' . g_l('navigation', '[rules][rule_folder]') . '</td>
	<td colspan="5">' . $weAcSelector . '</td>
</tr>
<tr id="trDoctypeID">
	<td style="height: 40px;" class="weMultiIconBoxHeadline">' . g_l('navigation', '[rules][rule_doctype]') . '</td>
	<td>' . we_html_tools::htmlSelect('DoctypeID', $docTypes, 1, 0, false, array("style" => "width: 275px;")) . '</td>
</tr>';

		if(defined('OBJECT_TABLE')){

			$yuiSuggest->setAcId("ClassIDPath");
			$yuiSuggest->setContentType("folder,object");
			$yuiSuggest->setInput("ClassIDPath");
			$yuiSuggest->setMaxResults(10);
			$yuiSuggest->setMayBeEmpty(true);
			$yuiSuggest->setResult('ClassID');
			$yuiSuggest->setSelector(weSuggest::DocSelector);
			$yuiSuggest->setTable(OBJECT_TABLE);
			$yuiSuggest->setWidth(275);
			$wecmdenc1 = we_base_request::encCmd("document.we_form.elements.ClassID.value");
			$wecmdenc2 = we_base_request::encCmd("document.we_form.elements.ClassIDPath.value");
			$wecmdenc3 = we_base_request::encCmd("top.opener.we_cmd('get_workspaces');");
			$yuiSuggest->setSelectButton(we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_document', document.we_form.elements.ClassID.value, '" . OBJECT_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "')"), 10);

			$weAcSelector = $yuiSuggest->getHTML();

			$formTable .=
				'<tr id="trClassID">
	<td class="weMultiIconBoxHeadline">' . g_l('navigation', '[rules][rule_class]') . '</td>
	<td colspan="3">' . $weAcSelector . '</td>
</tr>
<tr id="trWorkspaceID">
	<td style="height: 40px;" class="weMultiIconBoxHeadline">' . g_l('navigation', '[rules][rule_workspace]') . '</td>
	<td>' . we_html_tools::htmlSelect(
					'WorkspaceID', array(), 1, '', false, array('style' => "width: 275px;")) . '</td>
</tr>';
		}
		$formTable .= '
<tr id="trCategories">
	<td style="width: 200px;vertical-align:top" class="weMultiIconBoxHeadline">' . g_l('navigation', '[rules][rule_categories]') . '</td>
	<td colspan="4">
		' . $this->getHTMLCategory() . '
	</td>
</tr>
</table>';

		$parts[] = array(
			'html' => $formTable,
			'space' => 0
		);

		$saveButton = we_html_button::create_button(we_html_button::SAVE, 'javascript:we_cmd("save_navigation_rule");');
		$closeButton = we_html_button::create_button(we_html_button::CLOSE, 'javascript:top.window.close();');
		return we_html_tools::getHtmlTop() . STYLESHEET .
			we_html_element::jsScript(JS_DIR . 'formFunctions.js') .
			we_html_element::jsElement('
var dependencies = {;
	' . we_navigation_navigation::STYPE_CLASS . ':["ClassID", "WorkspaceID", "Categories"],
	' . we_navigation_navigation::STYPE_DOCTYPE . ': ["FolderID", "DoctypeID", "Categories"]
};
') .
			we_html_element::jsScript(WE_JS_MODULES_DIR . 'navigation/navigationRule.js') . '
</head>
<body onload="switchType(document.we_form[\'SelectionType\'].value)" class="weDialogBody">
	<form name="we_form" target="cmdFrame" action="' . $this->Frameset . '&pnt=ruleCmd">' .
			we_html_tools::hidden('cmd', '') .
			we_html_tools::hidden('ID', '0') .
			we_html_multiIconBox::getHTML('navigationRules', $parts, 30, we_html_button::position_yes_no_cancel($saveButton, null, $closeButton), -1, '', '', false, g_l('navigation', '[rules][navigation_rules]')) . '
	</form>' .
			$yuiSuggest->getYuiJs() .
			'</body></html>';
	}

	function getHTMLCategory(){
		$addbut = we_html_button::create_button("add", "javascript:we_cmd('we_selector_category',0,'" . CATEGORY_TABLE . "','','','fillIDs();opener.addCat(top.allPaths, top.allIDs);')");
		$del_but = addslashes(we_html_button::create_button(we_html_button::TRASH, 'javascript:#####placeHolder#####;'));


		$table = new we_html_table(
			array(
			'id' => 'CategoriesBlock',
			'style' => 'display: block;',
			'class' => 'default withSpace'
			), 2, 1);

		$table->setColContent(0, 0, we_html_element::htmlDiv(
				array(
					'id' => 'categories',
					'class' => 'blockWrapper',
					'style' => 'width: 380px; height: 80px; border: #AAAAAA solid 1px;'
		)));

		$table->setCol(1, 0, array('colspan' => 2, 'style' => 'text-align:right'), we_html_button::create_button(we_html_button::DELETE_ALL, "javascript:removeAllCats()") . $addbut);

		return $table->getHtml() . we_html_tools::hidden('CategoriesControl', 0) . we_html_tools::hidden('CategoriesCount', 0) .
			we_html_element::jsScript(JS_DIR . 'utils/multi_edit.js') .
			we_html_element::jsElement('
			var categories_edit = new multi_edit("categories",document.we_form,0,"' . $del_but . '",400,false);
			categories_edit.addVariant();
			document.we_form.CategoriesControl.value = categories_edit.name;
			categories_edit.showVariant(0);
		');
	}

}
