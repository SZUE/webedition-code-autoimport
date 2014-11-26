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
 we_html_element::jsScript(JS_DIR . 'windows.js');
?>
<script type="text/javascript"><!--
  var name_ord = 0;
	var type_ord = 0;
	var date_ord = 0;
	var size_ord = 0;

	function addOption(txt, id) {
		var a = document.forms["we_form"].elements["lookin"];
		a.options[a.options.length] = new Option(txt, id);
		a.selectedIndex = (a.options.length > 0 ? a.options.length - 1 : 0);
	}

	function openFile() {
		url = "we_sselector_uploadFile.php?pat=" + top.currentDir;
		new jsWindow(url, "we_fsuploadImage", -1, -1, 450, 360, true, false, true);
	}

	function reorder(name) {
		var order = 0;
		switch (name) {
			case "name":
				if (name_ord == 1) {
					order = 10;
					name_ord = 0;
				} else {
					order = 11;
					name_ord = 1;
				}
				break;
			case "type":
				if (type_ord == 1) {
					order = 20;
					type_ord = 0;
				} else {
					order = 21;
					type_ord = 1;
				}
				break;
			case "date":
				if (date_ord == 1) {
					order = 30;
					date_ord = 0;
				} else {
					order = 31;
					date_ord = 1;
				}
				break;
			case "size":
				if (size_ord == 1) {
					order = 40;
					size_ord = 0;
				} else {
					order = 41;
					size_ord = 1;
				}
				break;
		}
		top.fscmd.reorderDir(top.currentDir, order);
	}

	function setLookin() {
		var dirs = new Array();
		var foo = new Array();
		var a = document.forms["we_form"].elements["lookin"];
		var c = 0;

		a.options.length = 0;
		foo = top.currentDir.split("/");
		for (j = 0; j < foo.length; j++) {
			if (foo[j] != "") {
				dirs[c] = foo[j];
				c++;
			}
		}
		foo = top.rootDir.split("/");
		root = "/";
		for (j = 0; j < foo.length; j++) {
			if (foo[j] != "") {
				root = foo[j];
			}
		}

		addOption(root, "/");
		for (i = 0; i < dirs.length; i++) {
			if (a.options[i].value == "/") {
				addOption(dirs[i], a.options[i].value + dirs[i]);
			} else {
				addOption(dirs[i], a.options[i].value + "/" + dirs[i]);
			}
		}

	}


//-->
</script>
</head>
<body background="<?php echo IMAGE_DIR ?>backgrounds/radient.gif" LINK="#000000" ALINK="#000000" VLINK="#000000" style="background-color:#bfbfbf; background-repeat:repeat;margin:0px 0px 0px 0px" onload="setLookin();
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
