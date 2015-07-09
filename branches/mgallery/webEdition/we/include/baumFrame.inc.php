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
	<div id="bm_treeheaderDiv">
		<iframe frameBorder="0" src="about:blank" name="treeheader" style="border:0px;width:100%;height:100%;overflow: hidden;"></iframe>
	</div>
	<div id="bm_mainDiv">
		<?php
		$Tree = new weMainTree('webEdition.php', 'top', 'top.resize.left.tree', 'top.load');
		echo $Tree->getHTMLContructX('if(top.treeResized){top.treeResized();}');
		?>
	</div>
	<div id="bm_searchField">
		<div id="infoField" style="margin:5px; display: none;" class="defaultfont"></div>
		<form name="we_form" onsubmit="top.we_cmd('tool_weSearch_edit', document.we_form.keyword.value, top.treeData.table);
				return false;">
			<div id="search" style="margin: 10px 0 0 10px;">
				<?php
				echo we_html_tools::htmlTextInput('keyword', 10, we_base_request::_(we_base_request::STRING, 'keyword', ''), '', '', 'search', '120px') .
				we_html_button::create_button(we_html_button::SEARCH, "javascript:top.we_cmd('tool_weSearch_edit',document.we_form.keyword.value, top.treeData.table);", true);
				?>
			</div>
		</form>
	</div>
</div>

