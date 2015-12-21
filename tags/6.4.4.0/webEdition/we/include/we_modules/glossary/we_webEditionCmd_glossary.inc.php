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
		case "glossary_check":

			var _EditorFrame = top.weEditorFrameController.getActiveEditorFrame();
			if (_EditorFrame != false
							&& _EditorFrame.getEditorType() == "model"
							&& (
											_EditorFrame.getEditorContentType() == "<?php echo we_base_ContentTypes::WEDOCUMENT; ?>"
											|| _EditorFrame.getEditorContentType() == "objectFile"
											)
							) {

				var transaction = _EditorFrame.getEditorTransaction();
				url = "<?php echo WEBEDITION_DIR; ?>we_cmd.php?we_cmd[0]=glossary_check&we_cmd[2]=" + transaction + "&we_cmd[3]=checkOnly";
				new jsWindow(url, "glossary_check", -1, -1, 730, 400, true, false, true);

			} else {
<?php echo we_message_reporting::getShowMessageCall(g_l('modules_glossary', '[glossary_check_not_avalaible]'), we_message_reporting::WE_MESSAGE_ERROR); ?>

			}
			break;
		case "glossary_edit_acronym":
		case "glossary_edit_abbreviation":
		case "glossary_edit_foreignword":
		case "glossary_edit_link":
		case "glossary_edit_textreplacement":
		case "glossary_edit_ifthere":
			new jsWindow(url, "edit_module", -1, -1, 970, 760, true, true, true, true);
			break;
		case "glossary_settings":
			var fo = false;
			if (jsWindow_count) {
				for (var k = jsWindow_count - 1; k > -1; k--) {
					eval("if(jsWindow" + k + "Object.ref=='edit_module'){ fo=true;wind=jsWindow" + k + "Object.wind}");
					if (fo)
						break;
				}
				if (typeof (wind) != "undefined") {
					wind.focus();
				}
			}
			new jsWindow(url, "edit_glossary_settings", -1, -1, 490, 250, true, true, true, true);
			break;
		case "glossary_dictionaries":
			new jsWindow(url, "edit_glossary_dictionaries", -1, -1, 490, 250, true, true, true, true);
			break;
<?php
if($GLOBALS['weFrontendLanguages']){

	echo '	case ((arguments[0].substr(0, 15) == "GlossaryXYZnew_") ? arguments[0] : false):' .
	"		tempargs = arguments[0].split(\"\XYZ\");
				var fo=false;
				for(var k=jsWindow_count-1;k>-1;k--) {
					eval(\"if(jsWindow\"+k+\"Object.ref=='edit_module'){ jsWindow\"+k+\"Object.wind.content.we_cmd('\"+tempargs[1]+\"','\"+tempargs[2]+\"');fo=true;wind=jsWindow\"+k+\"Object.wind}\");
					if(fo) {
						break;
					}
				}
				wind.focus();
				break;";
}
?>
		case "new_glossary_acronym":
		case "new_glossary_abbreviation":
		case "new_glossary_foreignword":
		case "new_glossary_link":
		case "new_glossary_textreplacement":
		case "exit_glossary":

		case "save_exception":
		case "save_glossary":
		case "delete_glossary":
			var fo = false;
			if (jsWindow_count) {
				for (var k = jsWindow_count - 1; k > -1; k--) {
					if (arguments[1] != undefined) {
						eval("if(jsWindow" + k + "Object.ref=='edit_module'){ jsWindow" + k + "Object.wind.content.we_cmd('" + arguments[0] + "','" + arguments[1] + "');fo=true;wind=jsWindow" + k + "Object.wind}");
					} else {
						eval("if(jsWindow" + k + "Object.ref=='edit_module'){ jsWindow" + k + "Object.wind.content.we_cmd('" + arguments[0] + "');fo=true;wind=jsWindow" + k + "Object.wind}");
					}
					if (fo) {
						break;
					}
				}
				wind.focus();
			}
			break;
		case "unlock"://FIXME:???
			we_repl(self.load, url, arguments[0]);
			break;
	}//WE_REMOVE

//-->
</script>