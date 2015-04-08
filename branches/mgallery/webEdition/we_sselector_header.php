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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

we_html_tools::protect();

echo we_html_tools::getHtmlTop() .
 STYLESHEET .
 we_html_element::jsScript(JS_DIR . 'windows.js') .
 we_html_element::jsScript(JS_DIR . 'selectors/we_sselector_header.js') .
 we_html_element::cssLink(CSS_DIR . 'selectors.css');
?>
</head>
<body class="selectorHeader" onload="setLookin();
		self.focus()">
	<form name="we_form" method="post">
		<table class="selectorHeaderTable">
			<tr valign="middle">
				<td class="defaultfont lookinText"><?php echo g_l('fileselector', '[lookin]') ?></td>
				<td class="lookin"><select name="lookin" id="lookin" size="1" onchange="top.fscmd.setDir(lookin.options[lookin.selectedIndex].value);" class="defaultfont" style="width:100%">
						<option value="/">/</option>
					</select></td>
				<td><?php echo we_html_button::create_button("root_dir", "javascript:top.fscmd.setDir('/');"); ?></td>
				<td><?php echo we_html_button::create_button("image:btn_fs_back", "javascript:top.fscmd.goUp();"); ?></td>
				<?php if(!we_base_request::_(we_base_request::BOOL, "ret")){ ?>
					<td><?php echo we_html_button::create_button("image:btn_new_dir", "javascript:top.fscmd.drawNewFolder();", true, 100, 22, "", "", false, false, "_ss"); ?></td>
					<td><?php echo we_html_button::create_button("image:btn_add_file", "javascript:javascript:openFile();", true, 100, 22, "", "", false, false, "_ss"); ?></td>
					<td class="trash">
						<?php echo we_html_button::create_button("image:btn_function_trash", "javascript:top.fscmd.delFile();", true, 100, 22, "", "", false, false, "_ss"); ?></td>
				<?php } ?>
			</tr>
		</table>
		<table class="headerLines">
			<tr>
				<th class="selector treeIcon"></th>
				<th class="selector filename"><a href="#" onclick="reorder('name');"><?php echo g_l('fileselector', '[filename]') ?></a></th>
				<th class="selector filetype"><a href="#" onclick="reorder('type');"><?php echo g_l('fileselector', '[type]') ?></a></th>
				<th class="selector moddate"><a href="#" onclick="reorder('date');"><?php echo g_l('fileselector', '[modified]') ?></a></th>
				<th class="selector filesize"><a href="#" onclick="reorder('size');"><?php echo g_l('fileselector', '[filesize]') ?></a></th>
				<th class="selector remain"></th>
			</tr>
		</table>
	</form>
</body>

</html>
