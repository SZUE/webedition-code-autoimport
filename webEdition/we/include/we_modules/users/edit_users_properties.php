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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

we_html_tools::protect();

$yuiSuggest = & weSuggest::getInstance();
we_html_tools::htmlTop('', DEFAULT_CHARSET);

print STYLESHEET;

$user_object = new we_user();
if(isset($_SESSION["user_session_data"])){
	$user_object->setState($_SESSION["user_session_data"]);
}
echo $yuiSuggest->getYuiCssFiles() .
 $yuiSuggest->getYuiJsFiles() .
 we_html_element::jsScript(JS_DIR . 'images.js') .
 we_html_element::jsScript(JS_DIR . 'windows.js') .
 we_html_element::jsScript(JS_DIR . 'md5.js');
?>
<script type="text/javascript"><!--

	var loaded = 0;
	function we_submitForm(target,url){
		var f = self.document.we_form;

		ok=true;

		if (f.input_pass) {
			if (f.oldtab.value == 0) {
				if (f.input_pass.value.length < 4 && f.input_pass.value.length != 0) {
<?php print we_message_reporting::getShowMessageCall(g_l('modules_users', "[password_alert]"), we_message_reporting::WE_MESSAGE_ERROR); ?>
					return false;
				} else {
					if (f.input_pass.value != "") {
						var clearPass = f.input_pass.value;
						f.input_pass.value = "";
						eval('f.' + f.obj_name.value + '_clearpasswd.value = clearPass;');
					}
				}
			}
		}

		if (ok) {
			f.target = target;
			f.action = url;
			f.method = "post";
			f.submit();
		}
		return true;
	}

	function switchPage(page) {
		document.we_form.tab.value = page;
		return we_submitForm(self.name, "<?php print WE_USERS_MODULE_DIR; ?>edit_users_properties.php");
	}


	function doUnload() {
		if (!!jsWindow_count) {
			for (i = 0; i < jsWindow_count; i++) {
				eval("jsWindow" + i + "Object.close()");
			}
		}
	}

	function we_cmd(){
		var args = "";
		var url = "<?php print WEBEDITION_DIR; ?>we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+escape(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}

		switch (arguments[0]) {
			case "browse_users":
				new jsWindow(url,"browse_users",-1,-1,500,300,true,false,true);
				break;

			case "openDirselector":
				new jsWindow(url,"we_fileselector",-1,-1,<?php echo WINDOW_DIRSELECTOR_WIDTH . ',' . WINDOW_DIRSELECTOR_HEIGHT; ?>,true,true,true,true);
				break;

			case "select_seem_start":
				myWind = false;

				for(k=top.opener.top.jsWindow_count;k>-1;k--){

					eval("if(top.opener.top.jsWindow" + k + "Object){" +
						"	if(top.opener.top.jsWindow" + k + "Object.ref == 'edit_module'){" +
						"		myWind = top.opener.top.jsWindow" + k + "Object.wind.content.user_resize.user_right.user_editor.user_properties;" +
						"		myWindStr = 'top.jsWindow" + k + "Object.wind.content.user_resize.user_right.user_editor.user_properties';" +
						"	}" +
						"}");
					if(myWind){
						break;
					}
				}

				top.opener.top.we_cmd('openDocselector', myWind.document.forms[0].elements['seem_start_file'].value,'<?php print FILE_TABLE; ?>', myWindStr + '.document.forms[0].elements[\'seem_start_file\'].value', myWindStr + '.document.forms[0].elements[\'seem_start_file_name\'].value','','<?php print session_id(); ?>','','text/webedition',1);

				break;
			case "openNavigationDirselector":
			case "openNewsletterDirselector":
				if(arguments[0] == "openNewsletterDirselector") {
					url = "<?php echo WE_MODULES_DIR; ?>newsletter/we_dirfs.php?";
				}
				else {
					url = "<?php echo WE_INCLUDES_DIR; ?>we_tools/navigation/we_navigationDirSelect.php?";
				}
				for(var i = 0; i < arguments.length; i++){
					url += "we_cmd["+i+"]="+escape(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }
				}
				new jsWindow(url,"we_navigation_dirselector",-1,-1,600,400,true,true,true);
				break;
			default:
				for (var i = 0; i < arguments.length; i++) {
					args += 'arguments['+i+']' + ((i < (arguments.length-1)) ? ',' : '');
				}
				eval('top.content.we_cmd('+args+')');
				break;
			}
		}
		//-->
</script>
</head>
<body class="weEditorBody" onUnload="doUnload()" onLoad="loaded=1;">
	<form name="we_form" method="post" onSubmit="return false">
		<input type="hidden" name="ucmd" value="" />
		<input type="hidden" name="tab" value="<?php print (isset($_REQUEST["tab"]) ? intval($_REQUEST["tab"]) : ""); ?>" />
		<input type="hidden" name="oldtab" value="<?php print (isset($_REQUEST["tab"]) ? intval($_REQUEST["tab"]) : 0); ?>" />
		<input type="hidden" name="perm_branch" value="<?php print ( (isset($_REQUEST["perm_branch"]) && $_REQUEST["perm_branch"]) ? htmlspecialchars($_REQUEST["perm_branch"]) : 0); ?>" />
		<input type="hidden" name="old_perm_branch" value="<?php print ( (isset($_REQUEST["perm_branch"]) && $_REQUEST["perm_branch"]) ? htmlspecialchars($_REQUEST["perm_branch"]) : 0); ?>" />
		<input type="hidden" name="obj_name" value="<?php print $user_object->Name ?>" />
		<input type="hidden" name="uid" value="<?php print $user_object->ID ?>" />
		<input type="hidden" name="ctype" value="<?php print (isset($_REQUEST["ctype"]) ? htmlspecialchars($_REQUEST["ctype"]) : ""); ?>" />
		<input type="hidden" name="ctable" value="<?php print (isset($_REQUEST["ctable"]) ? htmlspecialchars($_REQUEST["ctable"]) : ""); ?>" />
		<input type="hidden" name="sd" value="0" />
		<?php
		if($user_object){
			if(isset($_REQUEST["oldtab"]) && isset($_REQUEST["old_perm_branch"])){ // && isset($_REQUEST["old_perm_branch"]) added for 4705
				$user_object->preserveState($_REQUEST["oldtab"], $_REQUEST["old_perm_branch"]);
				$_SESSION["user_session_data"] = $user_object->getState();
			}
			if(isset($_REQUEST["seem_start_file"])){
				$_SESSION["save_user_seem_start_file"][$_REQUEST["uid"]] = $_REQUEST["seem_start_file"];
			}
			print $user_object->formDefinition(isset($_REQUEST["tab"]) ? $_REQUEST["tab"] : "", isset($_REQUEST["perm_branch"]) ? $_REQUEST["perm_branch"] : 0);
		}
		print $yuiSuggest->getYuiCss();
		print $yuiSuggest->getYuiJs();
		?>
	</form>
</body>
</html>