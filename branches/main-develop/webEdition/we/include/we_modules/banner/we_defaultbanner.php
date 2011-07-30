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


include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we.inc.php");
include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_html_tools.inc.php");
include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_classes/html/we_button.inc.php");
include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_classes/weSuggest.class.inc.php');

protect();
htmlTop(g_l('modules_banner','[defaultbanner]'));
print STYLESHEET;
$DefaultBannerID = 0;

if(isset($_REQUEST["ok"]) && $_REQUEST["ok"]){
	$DefaultBannerID = isset($_REQUEST["DefaultBannerID"]) ? $_REQUEST["DefaultBannerID"] : 0;
	$DB_WE->query("SELECT * FROM ".BANNER_PREFS_TABLE." WHERE pref_name='DefaultBannerID'");
	if($DB_WE->num_rows()){
		$DB_WE->query("UPDATE ".BANNER_PREFS_TABLE." SET pref_value='".$DB_WE->escape($DefaultBannerID)."' WHERE pref_name='DefaultBannerID'");
	}else{
		$DB_WE->query("INSERT INTO ".BANNER_PREFS_TABLE." (pref_name,pref_value) VALUES('DefaultBannerID','".$DB_WE->escape($DefaultBannerID)."')");
	}

	print '<script type="text/javascript">

top.close();

</script>
</head><body></body></html>';
	exit();
}
	$yuiSuggest =& weSuggest::getInstance();

	function formBannerChooser($width="",$table=BANNER_TABLE,$idvalue,$idname,$title="",$cmd=""){
		$yuiSuggest =& weSuggest::getInstance();
		$path=id_to_path($idvalue,$table);
		$textname = md5(uniqid(rand()));
		$we_button = new we_button();
		//javascript:we_cmd('openBannerSelector',document.we_form.elements['$idname'].value,'document.we_form.elements[\\'$idname\\'].value','document.we_form.elements[\\'$textname\\'].value','".$cmd."')
		$wecmdenc1= we_cmd_enc("document.we_form.elements['$idname'].value");
		$wecmdenc2= we_cmd_enc("document.we_form.elements['$textname'].value");
		$wecmdenc3= we_cmd_enc(str_replace('\\','',$cmd));
		$button = $we_button->create_button("select","javascript:we_cmd('openBannerSelector',document.we_form.elements['$idname'].value,'".$wecmdenc1."',".$wecmdenc2."','".$wecmdenc3."')");

		$yuiSuggest->setAcId("Path");
		$yuiSuggest->setContentType("folder");
		$yuiSuggest->setInput($textname,$path);
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(false);
		$yuiSuggest->setResult($idname,$idvalue);
		$yuiSuggest->setSelector("Dirselector");
		$yuiSuggest->setTable($table);
		$yuiSuggest->setWidth($width);
		$yuiSuggest->setSelectButton($button);

		return $yuiSuggest->getHTML();
	}
?>
<script  type="text/javascript" src="<?php print JS_DIR ?>windows.js"></script>

		<script type="text/javascript">
			var loaded;
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
				switch (arguments[0]){
					case "openBannerSelector":
						new jsWindow(url,"we_bannerselector",-1,-1,650,400,true,true,true);
						break;
               default:
					for(var i = 0; i < arguments.length; i++){
						args += 'arguments['+i+']' + ((i < (arguments.length-1)) ? ',' : '');
					}
					eval('top.content.we_cmd('+args+')');
				}
			}

			function we_save() {
				var acLoopCount=0;
				var acIsRunning = false;
				while(acLoopCount<20 && YAHOO.autocoml.isRunnigProcess()){
					acLoopCount++;
					acIsRunning = true;
					setTimeout('we_save()',100);
				}
				if(!acIsRunning) {
					if(YAHOO.autocoml.isValid()) {
						document.we_form.submit();;
					} else {
						<?php echo we_message_reporting::getShowMessageCall(g_l('alert','[save_error_fields_value_not_valid]'),WE_MESSAGE_ERROR); ?>
					}
				}
			}

			self.focus();
		</script>
		<?php echo $yuiSuggest->getYuiJsFiles(); ?>

	</head>
	<body class="weDialogBody" onUnload="doUnload()">
	<form name="we_form" action="<?php print $_SERVER["SCRIPT_NAME"]; ?>" method="post"><input type="hidden" name="ok" value="1" /><input type="hidden" name="we_cmd[0]" value="<?php print $_REQUEST["we_cmd"][0]; ?>" />
		<?php
		$DefaultBannerID = f("SELECT pref_value FROM ".BANNER_PREFS_TABLE." WHERE pref_name='DefaultBannerID'","pref_value",$DB_WE);
		$content = formBannerChooser(300,BANNER_TABLE,$DefaultBannerID,"DefaultBannerID","");
		$we_button = new we_button();
		$yes_button = $we_button->create_button("save","javascript:we_save();");
		$cancel_button = $we_button->create_button("cancel","javascript:top.close();");
		$buttons = $we_button->position_yes_no_cancel($yes_button, null, $cancel_button);

		print  htmlDialogLayout($content,g_l('modules_banner','[defaultbanner]'),$buttons,"100%","30","175");
		?>
	</form>
	<?php echo $yuiSuggest->getYuiCss().$yuiSuggest->getYuiJs(); ?>
	</body>
</html>
