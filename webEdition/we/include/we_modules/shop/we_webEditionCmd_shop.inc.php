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
?>
<script type="text/javascript"><!--
	switch (WE_REMOVE) {
		case "shop_edit_ifthere":
		case "shop_edit":
			new jsWindow(url, "edit_module", -1, -1, 970, 760, true, true, true, true);
			break;
		case "pref_shop":
			var fo = false;
			if (jsWindow_count) {
				for (var k = jsWindow_count - 1; k > -1; k--) {
					eval("if(jsWindow" + k + "Object.ref=='edit_module'){ jsWindow" + k + "Object.wind.content.we_cmd('" + arguments[0] + "');fo=true;wind=jsWindow" + k + "Object.wind}");
					if (fo)
						break;
				}
				wind.focus();
			}
			url = "<?php echo WE_SHOP_MODULE_DIR ?>edit_shop_pref.php";
			new jsWindow(url, "shoppref", -1, -1, 470, 600, true, true, true, false);
			break;
		case "edit_shop_status":
			var fo = false;
			if (jsWindow_count) {
				for (var k = jsWindow_count - 1; k > -1; k--) {
					eval("if(jsWindow" + k + "Object.ref=='edit_module'){ jsWindow" + k + "Object.wind.content.we_cmd('" + arguments[0] + "');fo=true;wind=jsWindow" + k + "Object.wind}");
					if (fo)
						break;
				}
				wind.focus();
			}
			url = "<?php echo WE_SHOP_MODULE_DIR ?>edit_shop_status.php";
			new jsWindow(url, "edit_shop_status", -1, -1, 700, 580, true, true, true, false);
			break;
		case "edit_shop_vat_country":
			var fo = false;
			if (jsWindow_count) {
				for (var k = jsWindow_count - 1; k > -1; k--) {
					eval("if(jsWindow" + k + "Object.ref=='edit_module'){ jsWindow" + k + "Object.wind.content.we_cmd('" + arguments[0] + "');fo=true;wind=jsWindow" + k + "Object.wind}");
					if (fo)
						break;
				}
				wind.focus();
			}
			url = "<?php echo WE_SHOP_MODULE_DIR ?>edit_shop_vat_country.php";
			new jsWindow(url, "edit_shop_vat_country", -1, -1, 700, 780, true, true, true, false);
			break;
		case "edit_shop_categories":
			var fo = false;
			if (jsWindow_count) {
				for (var k = jsWindow_count - 1; k > -1; k--) {
					eval("if(jsWindow" + k + "Object.ref=='edit_module'){ jsWindow" + k + "Object.wind.content.we_cmd('" + arguments[0] + "');fo=true;wind=jsWindow" + k + "Object.wind}");
					if (fo)
						break;
				}
				wind.focus();
			}
			url = "<?php echo WE_SHOP_MODULE_DIR ?>edit_shop_categories.php";
			new jsWindow(url, "edit_shop_categories", -1, -1, 740, 650, true, false, true, false);
			break;
		case "edit_shop_vats":
			var fo = false;
			if (jsWindow_count) {
				for (var k = jsWindow_count - 1; k > -1; k--) {
					eval("if(jsWindow" + k + "Object.ref=='edit_module'){ jsWindow" + k + "Object.wind.content.we_cmd('" + arguments[0] + "');fo=true;wind=jsWindow" + k + "Object.wind}");
					if (fo)
						break;
				}
				wind.focus();
			}
			url = "<?php echo WE_SHOP_MODULE_DIR ?>edit_shop_vats.php";
			new jsWindow(url, "edit_shop_vats", -1, -1, 650, 650, true, false, true, false);
			break;
		case "edit_shop_shipping":
			var fo = false;
			if (jsWindow_count) {
				for (var k = jsWindow_count - 1; k > -1; k--) {
					eval("if(jsWindow" + k + "Object.ref=='edit_module'){ jsWindow" + k + "Object.wind.content.we_cmd('" + arguments[0] + "');fo=true;wind=jsWindow" + k + "Object.wind}");
					if (fo)
						break;
				}
				wind.focus();
			}
			url = "<?php echo WE_SHOP_MODULE_DIR ?>edit_shop_shipping.php";
			new jsWindow(url, "edit_shop_shipping", -1, -1, 700, 600, true, false, true, false);
			break;
		case "payment_val":
			var fo = false;
			if (jsWindow_count) {
				for (var k = jsWindow_count - 1; k > -1; k--) {
					eval("if(jsWindow" + k + "Object.ref=='edit_module'){ jsWindow" + k + "Object.wind.content.we_cmd('" + arguments[0] + "');fo=true;wind=jsWindow" + k + "Object.wind}");
					if (fo)
						break;
				}
				wind.focus();
			}
			url = "<?php echo WE_SHOP_MODULE_DIR ?>edit_shop_payment.php";
			new jsWindow(url, "edit_shop_payment", -1, -1, 520, 720, true, false, true, false);
			break;
<?php
$years = we_shop_shop::getAllOrderYears();
foreach($years as $cur){
	echo 'case "year' . $cur . '":' . "\n";
}
?>

		case "revenue_view":
		case "new_article":

		case "delete_shop":
			var fo = false;
			if (jsWindow_count) {
				for (var k = jsWindow_count - 1; k > -1; k--) {
					eval("if(jsWindow" + k + "Object.ref=='edit_module'){fo=true;wind=jsWindow" + k + "Object.wind}");
					if (fo)
						break;
				}
				if (fo) {
					wind.content.we_cmd(arguments[0]);
					wind.focus();
				}
			}
			break;
		case "exit_shop":
			if (jsWindow_count) {
				for (i = 0; i < jsWindow_count; i++) {
					eval("if(jsWindow" + i + "Object.ref=='edit_module') jsWindow" + i + "Object.close()");
				}
			}
			break;
		case "shop_insert_variant":
		case "shop_move_variant_up":
		case "shop_move_variant_down":
		case "shop_remove_variant":
			url += "#f" + (parseInt(arguments[1]) - 1);
			we_sbmtFrm(top.weEditorFrameController.getActiveDocumentReference().frames["1"], url);
			break;
		case 'shop_preview_variant':
			url += "#f" + (parseInt(arguments[1]) - 1);
			var prevWin = new jsWindow(url, "previewVariation", -1, -1, 1600, 1200, true, true, true, true);
			we_sbmtFrm(prevWin.wind, url);
			break;
	}//WE_REMOVE
//-->
</script>