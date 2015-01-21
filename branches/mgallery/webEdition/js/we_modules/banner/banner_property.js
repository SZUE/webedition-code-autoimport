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

var loaded;

			function doUnload() {
				if (!!jsWindow_count) {
					for (i = 0; i < jsWindow_count; i++) {
						eval("jsWindow" + i + "Object.close()");
					}
				}
			}

			function we_cmd() {
				var args = "";
				var url = dirs.WEBEDITION_DIR + "we_cmd.php?";
				for (var i = 0; i < arguments.length; i++) {
					url += "we_cmd[" + i + "]=" + encodeURIComponent(arguments[i]);
					if (i < (arguments.length - 1)) {
						url += "&";
					}
				}
				switch (arguments[0]) {
					case "openSelector":
						new jsWindow(url, "we_selector", -1, -1, size.windowSelect.width, size.windowSelect.height, true, true, true, true);
						break;
					case "openCatselector":
						new jsWindow(url, "we_catselector", -1, -1, size.catSelect.width, size.catSelect.height, true, true, true, true);
						break;
					case "openImgselector":
					case "openDocselector":
						new jsWindow(url, "we_docselector", -1, -1, size.docSelect.width, size.docSelect.height, true, true, true, true);
						break;
					case "openDirselector":
						new jsWindow(url, "we_dirselector", -1, -1, size.windowDirSelect.width, size.windowDirSelect.height, true, true, true, true);
						break;
					case "banner_openDirselector":
						new jsWindow(url, "we_bannerselector", -1, -1, 600, 350, true, true, true);
						break;
					case "switchPage":
						document.we_form.ncmd.value = arguments[0];
						document.we_form.page.value = arguments[1];
						submitForm();
						break;
					case "add_cat":
					case "del_cat":
					case "del_all_cats":
					case "add_file":
					case "del_file":
					case "del_all_files":
					case "add_folder":
					case "del_folder":
					case "del_customer":
					case "del_all_customers":
					case "del_all_folders":
					case "add_customer":
						document.we_form.ncmd.value = arguments[0];
						document.we_form.ncmdvalue.value = arguments[1];
						submitForm();
						break;
					case "delete_stat":
						if (confirm(g_l.deleteStatConfirm)) {
							document.we_form.ncmd.value = arguments[0];
							submitForm();
						}
						break;
					default:
						for (var i = 0; i < arguments.length; i++) {
							args += 'arguments[' + i + ']' + ((i < (arguments.length - 1)) ? ',' : '');
						}
						eval('top.content.we_cmd(' + args + ')');
				}
			}

			function submitForm() {
				var f = self.document.we_form;
				f.target = (arguments[0] ? arguments[0] : "edbody");
				f.action = (arguments[1] ? arguments[1] : "");
				f.method = (arguments[2] ? arguments[2] : "post");
				f.submit();
			}
			function checkData() {

				return true;
			}

			self.focus();