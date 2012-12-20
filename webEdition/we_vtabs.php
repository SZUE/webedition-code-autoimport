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
we_html_tools::htmlTop();

$_treewidth = isset($_COOKIE["treewidth_main"]) && ($_COOKIE["treewidth_main"] >= weTree::MinWidth) ? $_COOKIE["treewidth_main"] : weTree::DefaultWidth;

/**
 * GET WIDTH AND HEIGHT OF VERTICAL TABS
 */
$vtab = array(
	'FILE_TABLE' => array(
		'file' => 'we_language/' . $GLOBALS["WE_LANGUAGE"] . "/v-tabs/documents",
		'show' => we_hasPerm("CAN_SEE_DOCUMENTS") || we_hasPerm("ADMINISTRATOR"),
		'size' => array(19, 83),
	),
	'TEMPLATES_TABLE' => array(
		'file' => 'we_language/' . $GLOBALS["WE_LANGUAGE"] . "/v-tabs/templates",
		'show' => we_hasPerm("CAN_SEE_TEMPLATES") || we_hasPerm("ADMINISTRATOR"),
		'size' => array(19, 83),
	),
	'OBJECT_FILES_TABLE' => array(
		'file' => 'we_language/' . $GLOBALS["WE_LANGUAGE"] . "/v-tabs/objects",
		'show' => defined("OBJECT_TABLE") && (we_hasPerm("CAN_SEE_OBJECTFILES") || we_hasPerm("ADMINISTRATOR")),
		'size' => array(19, 83),
	),
	'OBJECT_TABLE' => array(
		'file' => 'we_language/' . $GLOBALS["WE_LANGUAGE"] . "/v-tabs/classes",
		'show' => defined("OBJECT_TABLE") && (we_hasPerm("CAN_SEE_OBJECTS") || we_hasPerm("ADMINISTRATOR")),
		'size' => array(19, 83),
	)
);
foreach($vtab as $key => &$val){
	if(defined($key)){
		if(file_exists(WE_INCLUDES_PATH . $val['file'] . '_normal.gif')){
			$val['size'] = getimagesize(WE_INCLUDES_PATH . $val['file'] . '_normal.gif');
		}
	} else{
		unset($vtab[$key]);
	}
}
unset($val);

echo we_html_element::jsScript(JS_DIR . 'images.js') .
 we_html_element::jsScript(JS_DIR . 'we_tabs.js');
?>
<script type="text/javascript"><!--
	function we_cmd(){
		var args = "";
		var url = "<?php print WEBEDITION_DIR; ?>we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+escape(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}
		switch(arguments[0]){
			case "load":
				var op = top.makeFoldersOpenString();
				parent.we_cmd("load",arguments[1],0,op,top.treeData.table);
				break;
			default:
				for(var i = 0; i < arguments.length; i++){
					args += 'arguments['+i+']' + ( (i < (arguments.length-1)) ? ',' : '');
				}
				eval('parent.we_cmd('+args+')');
		}
	}

	function setTab(table){
		if(we_tabs == null){
			setTimeout("setTab('"+table+"')",500);
			return;
		}
		switch(table){
<?php
$i = 0;
foreach($vtab as $tab => $val){
	echo 'case "' . constant($tab) . '":
		we_tabs[' . $i++ . '].setState(TAB_ACTIVE,false,we_tabs);
		break;
		';
}
?>

		}
	}

	var we_tabs = new Array(
<?php
$tmp = array();
foreach($vtab as $tab => $val){
	$file = WE_INCLUDES_DIR . $val['file'];
	$tmp[] = 'new we_tab("#","' . $file . '_normal.gif", "' . $file . '_active.gif", "' . $file . '_disabled.gif", ' . $val['size'][0] . ',' . $val['size'][1] . ' ,' . ($val['show'] ? 'TAB_ACTIVE' : 'TAB_DISABLED') . ', "if(top.deleteMode){we_cmd(\'exit_delete\', \'' . constant($tab) . '\');};treeOut();we_cmd(\'load\', \'' . constant($tab) . '\' ,0);")';
}
print implode(',', $tmp);
?>
);

	var oldWidth = <?php print weTree::DefaultWidth; ?>;

	function toggleTree() {
		top.toggleTree();
	}

	function incTree(){
		var w = parseInt(top.getTreeWidth());
		if((100<w) && (w<1000)){
			w+=20;
			top.setTreeWidth(w);
		}
		if(w>=1000){
			w=1000;
			self.document.getElementById("incBaum").style.backgroundColor="grey";
		}
	}

	function decTree(){
		var w = parseInt(top.getTreeWidth());
		w-=20;
		if(w>200){
			top.setTreeWidth(w);
			self.document.getElementById("incBaum").style.backgroundColor="";
		}
		if(w<=200 && ((w+20)>=200)){
			toggleTree();
		}
	}


	function treeOut() {
		if (top.getTreeWidth() <= 30) {
			toggleTree();
		}
	}
	//-->
</script>
</head>
<body bgcolor="#ffffff" style="background-image: url(<?php print IMAGE_DIR; ?>v-tabs/background.gif);background-repeat:repeat-y;border-top:1px solid black;margin-top:0px;margin-bottom:0px;margin-left:0px;margin-right:0px;">
	<div style="position:absolute;top:8px;left:5px;z-index:10;border-top:1px solid black;">
		<script type="text/javascript"><!--
			for (var i=0; i<we_tabs.length;i++) {
				we_tabs[i].write();
				document.writeln('<br/>');
			}
<?php
if(isset($_REQUEST["table"]) && $_REQUEST["table"]){
	print "var defTab = '" . $_REQUEST["table"] . "';";
} else{
	$ok = false;
	foreach($vtab as $tab => $val){
		if($val['show']){
			print "var defTab = '" . constant($tab) . "';";
			$ok = true;
			break;
		}
	}
	if(!$ok){
		print "var defTab = '';";
	}
}
?>
	setTab(defTab);
	//-->
		</script>
	</div>
	<img id="incBaum" src="<?php print BUTTONS_DIR ?>icons/function_plus.gif" width="9" height="12" style="position:absolute;bottom:53px;left:5px;border:1px solid grey;padding:0 1px;cursor: pointer;<?php print ($_treewidth <= 100) ? 'bgcolor:grey;' : ''; ?>" onClick="incTree();">
	<img id="decBaum" src="<?php print BUTTONS_DIR ?>icons/function_minus.gif" width="9" height="12" style="position:absolute;bottom:33px;left:5px;border:1px solid grey;padding:0 1px;cursor: pointer;<?php print ($_treewidth <= 100) ? 'bgcolor:grey;' : ''; ?>" onClick="decTree();">
	<img id="arrowImg" src="<?php print BUTTONS_DIR ?>icons/direction_<?php print ($_treewidth <= 100) ? "right" : "left"; ?>.gif" width="9" height="12" style="position:absolute;bottom:13px;left:5px;border:1px solid grey;padding:0 1px;cursor: pointer;" onClick="toggleTree();">
</body>
</html>
