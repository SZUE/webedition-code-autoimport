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
 we_html_element::jsScript(JS_DIR . 'selectors/we_sselector_header.js');
?>
</head>
<body class="selectorHeader" onload="setLookin();
		self.focus()">
	<form name="we_form" method="post">
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr valign="middle">
				<td width="10"><?php echo we_html_tools::getPixel(10, 49); ?></td>
				<td width="70" class="defaultfont"><b><?php echo g_l('fileselector', '[lookin]') ?></b></td>
				<td width="10"><?php echo we_html_tools::getPixel(10, 29); ?></td>
				<td><select name="lookin" size="1" onchange="top.fscmd.setDir(lookin.options[lookin.selectedIndex].value);" class="defaultfont" style="width:100%">
						<option value="/">/</option>
					</select><?php echo we_html_tools::getPixel(1, 1); ?></td>
				<td width="10"><?php echo we_html_tools::getPixel(10, 29); ?></td>
				<td width="40">
					<?php echo we_html_button::create_button("root_dir", "javascript:top.fscmd.setDir('/');"); ?>
				</td>
				<td width="10"><?php echo we_html_tools::getPixel(10, 29); ?></td>
				<td width="40">
					<?php echo we_html_button::create_button("image:btn_fs_back", "javascript:top.fscmd.goUp();"); ?>
				</td>
				<?php if(!we_base_request::_(we_base_request::BOOL, "ret")){ ?>
					<td width="10"><?php echo we_html_tools::getPixel(10, 29); ?></td>
					<td width="40">
						<?php echo we_html_button::create_button("image:btn_new_dir", "javascript:top.fscmd.drawNewFolder();", true, 100, 22, "", "", false, false, "_ss"); ?>
					</td>
					<td width="10"><?php echo we_html_tools::getPixel(10, 29); ?></td>
					<td width="40">
						<?php echo we_html_button::create_button("image:btn_add_file", "javascript:javascript:openFile();", true, 100, 22, "", "", false, false, "_ss"); ?>
					</td>
					<td width="10"><?php echo we_html_tools::getPixel(10, 29); ?></td>
					<td width="25">
						<?php echo we_html_button::create_button("image:btn_function_trash", "javascript:top.fscmd.delFile();", true, 100, 22, "", "", false, false, "_ss"); ?>
					</td>
				<?php } ?>
				<td width="10"><?php echo we_html_tools::getPixel(10, 29); ?></td>
			</tr>
		</table>
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td><img src="<?php echo IMAGE_DIR ?>umr_h_small.gif" width="100%" height="2" border="0"></td>
			</tr>
		</table>
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td><?php echo we_html_tools::getPixel(25, 20) ?></td>
				<td class="selector"><b><a href="#" onclick="reorder('name');"><?php echo g_l('fileselector', '[filename]') ?></a></b></td>
				<td class="selector"><b><a href="#" onclick="reorder('type');"><?php echo g_l('fileselector', '[type]') ?></b></a></td>
				<td class="selector"><b><a href="#" onclick="reorder('date');"><?php echo g_l('fileselector', '[modified]') ?></b></a></td>
				<td class="selector"><b><a href="#" onclick="reorder('size');"><?php echo g_l('fileselector', '[filesize]') ?></b></a></td>
			</tr>
			<tr>
				<td width="25"><?php echo we_html_tools::getPixel(25, 1) ?></td>
				<td width="200"><?php echo we_html_tools::getPixel(200, 1) ?></td>
				<td width="150"><?php echo we_html_tools::getPixel(150, 1) ?></td>
				<td width="200"><?php echo we_html_tools::getPixel(200, 1) ?></td>
				<td><?php echo we_html_tools::getPixel(15, 1) ?></td>
			</tr>
		</table>

		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td><img src="<?php echo IMAGE_DIR ?>umr_h_small.gif" width="100%" height="2" border="0"></td>
			</tr>
		</table>

	</form>
</body>

</html>
