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
$table = isset($table) ? $table : FILE_TABLE;
?>
<div id="vtabs">
	<?php include(WE_INCLUDES_PATH . 'we_vtabs.inc.php'); ?>
</div>
<div id="treeFrameDiv">
	<div id="treeControl">
		<span id="treeName" class="middlefont"></span>
		<span id="reloadTree" onclick="we_cmd('loadVTab', top.treeData.table, 0);"><i class="fa fa-refresh"></i></span>
		<span id="toggleTree" onclick="WE().layout.tree.toggle();" title="<?= g_l('global', '[tree][minimize]'); ?>"><i id="arrowImg" class="fa fa-lg fa-caret-<?= ($treewidth <= 100) ? "right" : "left"; ?>" ></i></span>
	</div>
	<div id="treeContent">
		<div id="bm_treeheaderDiv">
			<iframe src="about:blank" name="treeheader"></iframe>
		</div>
		<?php
		$Tree = new we_tree_main('webEdition.php', 'top', 'top', 'top.load');
		echo $Tree->getHTMLConstruct();
		?>
		<div id="bm_searchField">
			<div id="infoField" class="defaultfont"></div>
			<form name="we_form" onsubmit="top.we_cmd('tool_weSearch_edit', document.we_form.keyword.value, top.treeData.table);
					return false;">
				<div id="search">
					<?php
					echo we_html_tools::htmlTextInput('keyword', 10, we_base_request::_(we_base_request::STRING, 'keyword', ''), '', 'placeholder="' . g_l('buttons_modules_message', '[search][alt]') . '"', 'search') .
					we_html_button::create_button(we_html_button::SEARCH, "javascript:top.we_cmd('tool_weSearch_edit',document.we_form.keyword.value, top.treeData.table);");
					?>
				</div>
			</form>
		</div>
	</div>
</div>