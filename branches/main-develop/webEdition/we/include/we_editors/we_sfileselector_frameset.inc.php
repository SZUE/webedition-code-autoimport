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
we_html_tools::protect(['BROWSE_SERVER', 'SITE_IMPORT', 'ADMINISTRATOR']);

function printHeaderHTML($ret){
	return '
<table class="selectorHeaderTable">
	<tr style="vertical-align:middle">
		<td class="defaultfont lookinText">' . g_l('fileselector', '[lookin]') . '</td>
		<td class="lookin"><select name="lookin" id="lookin" onchange="top.fscmd.setDir(lookin.options[lookin.selectedIndex].value);" class="defaultfont" style="width:100%">
				<option value="/">/</option>
			</select></td>
		<td>' . we_html_button::create_button('root_dir', "javascript:top.fscmd.setDir('/');") . '</td>
		<td>' . we_html_button::create_button('fa:btn_fs_back,fa-lg fa-level-up,fa-lg fa-folder', "javascript:top.fscmd.goUp();") . '</td>
' . ($ret ? '' : '
			<td>' . we_html_button::create_button('fa:btn_new_dir,fa-plus,fa-lg fa-folder', "javascript:top.fscmd.drawNewFolder();", '', 0, 0, "", "", false, false, "_ss") . '</td>
			<td>' . we_html_button::create_button('fa:btn_add_file,fa-plus,fa-lg fa-file-o', "javascript:javascript:openFile();", '', 0, 0, "", "", false, false, "_ss") . '</td>
			<td class="trash">' . we_html_button::create_button(we_html_button::TRASH, "javascript:top.fscmd.delFile();", '', 0, 0, "", "", false, false, "_ss") . '</td>') .
		'</tr>
</table>
<table class="headerLines">
	<tr>
		<th class="selector treeIcon"></th>
		<th class="selector filename"><a href="#" onclick="reorder(\'name\');">' . g_l('fileselector', '[filename]') . '</a></th>
		<th class="selector filetype"><a href="#" onclick="reorder(\'type\');">' . g_l('fileselector', '[type]') . '</a></th>
		<th class="selector moddate"><a href="#" onclick="reorder(\'date\');">' . g_l('fileselector', '[modified]') . '</a></th>
		<th class="selector filesize"><a href="#" onclick="reorder(\'size\');">' . g_l('fileselector', '[filesize]') . '</a></th>
		<th class="selector remain"></th>
	</tr>
</table>';
}

function printFooterTable($ret, $filter, $currentName){
	if($ret){
		$cancel_button = we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close();");
		$yes_button = we_html_button::create_button(we_html_button::OK, "javascript:top.exit_close();");
	} else {
		$cancel_button = we_html_button::create_button(we_html_button::CLOSE, "javascript:top.exit_close();");
		$yes_button = we_html_button::create_button(we_html_button::EDIT, "javascript:editFile();");
	}
	if($filter === "all_Types"){
		$options = '<option value="' . str_replace(' ', '%20', g_l('contentTypes', '[all_Types]')) . '">' . g_l('contentTypes', '[all_Types]') . '</option>';
		$ct = we_base_ContentTypes::inst();
		foreach($ct->getFiles() as $key){
			$options.= '<option value="' . rawurlencode(g_l('contentTypes', '[' . $key . ']')) . '">' . g_l('contentTypes', '[' . $key . ']') . '</option>';
		}
	}
	return '
<table id="footer">' .
		($filter === "all_Types" ? '
	<tr>
		<td class="defaultfont description">' . g_l('fileselector', '[type]') . '</td>
		<td class="defaultfont">
			<select name="filter" class="weSelect" onchange="top.fscmd.setFilter(this.options[this.selectedIndex].value)" style="width:100%">
				' . $options . '</select></td>
	</tr>' : '') . '
	<tr>
		<td class="defaultfont description">' . g_l('fileselector', '[name]') . '</td>
		<td class="defaultfont" style="text-align:left">' . we_html_tools::htmlTextInput("fname", 24, $currentName, "", "style=\"width:100%\" readonly=\"readonly\"") . '</td>
	</tr>
</table>
<div id="footerButtons">' . we_html_button::position_yes_no_cancel($yes_button, null, $cancel_button) . '</div>
';
}

function printFrameSet(){
	$docroot = rtrim($_SERVER['DOCUMENT_ROOT'], '/');
	$cmd1 = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 1);


	$filter = we_base_request::_(we_base_request::STRING, 'we_cmd', 'all_Types', 2);
	$url = we_base_request::_(we_base_request::URL, 'we_cmd', '', 3);
	$currentDir = str_replace('\\', '/', ( $url ?
			($url === '/' ? '' :
				( parse_url($url) === FALSE && is_dir($docroot . $url) ?
					$url :
					dirname($url))) :
			''));
	$currentName = basename($url);
	if(!file_exists($docroot . $currentDir . '/' . $currentName)){
		$currentDir = '';
		$currentName = '';
	}
	$currentID = $docroot . $currentDir . ($filter == we_base_ContentTypes::FOLDER || $filter === 'filefolder' ? '' : (($currentDir != '') ? '/' : '') . $currentName);

	$currentID = str_replace('\\', '/', $currentID);

	$rootDir = we_base_request::_(we_base_request::FILE, 'we_cmd', '', 5);
	$selectOwn = we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 6);

	echo we_html_tools::getHtmlTop('', '', 'frameset', we_html_element::cssLink(CSS_DIR . 'selectors.css') .
		we_html_element::cssElement('
#fsfooter{
	 bottom:0px;
}
') .
		we_html_element::jsScript(JS_DIR . 'selectors/we_sselector_header.js', '', ['id' => 'loadVarSelectors', 'data-selector' => setDynamicVar([
				'options' => [
				],
				'data' => [
					'currentType' => ($filter == we_base_ContentTypes::FOLDER) ? we_base_ContentTypes::FOLDER : '',
					'currentDir' => str_replace($rootDir, '', $currentDir),
					'currentID' => $currentID,
					'rootDir' => $rootDir,
					'currentName' => $currentName,
					'currentFilter' => str_replace(' ', '%20', g_l('contentTypes', '[' . $filter . ']', true) !== false ? g_l('contentTypes', '[' . $filter . ']') : ''),
					'browseServer' => $cmd1 ? false : true,
					'cmd1' => $cmd1,
					'cmd4' => we_base_request::_(we_base_request::CMD, 'we_cmd', '', 4),
					'sitepath' => $docroot,
					'filter' => $filter
				],
				'click' => [
					'oldID ' => 0,
				]
		])]), we_html_element::htmlBody([
			'onload' => "init()",
			'onunload' => "doUnload();"
			], we_html_element::htmlDiv(['id' => 'fsheader'], printHeaderHTML(($cmd1 ? 1 : 0))) .
			we_html_element::htmlIFrame('fsbody', 'about:blank', '', '', '', true) .
			we_html_element::htmlDiv(['id' => 'fsfooter'], printFooterTable(($cmd1 ? 1 : 0), $filter, $currentName)) .
			we_html_element::htmlIFrame('fscmd', WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=selectorBrowseCmd&ret=' . ($cmd1 ? 1 : 0) . '&filter=' . $filter . '&currentName=' . $currentName . '&selectOwn=' . $selectOwn, 'display:none;', '', '', false))
	);
}

printFrameSet();
