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
$_treewidth = isset($_COOKIE["treewidth_main"]) && ($_COOKIE["treewidth_main"] >= weTree::MinWidth) ? $_COOKIE["treewidth_main"] : weTree::DefaultWidth;

$svgPre = '<defs>
    <linearGradient id="gradAct#ID#" x1="0%" y1="0%" x2="50%" y2="0%">
      <stop style="stop-color:white;stop-opacity:1;" offset="0%" />
      <stop style="stop-color:#cacaca;stop-opacity:1;" offset="100%" />
    </linearGradient>
    <linearGradient id="gradDis#ID#" x1="0%" y1="0%" x2="50%" y2="0%">
      <stop style="stop-color:white;stop-opacity:1;" offset="0%" />
      <stop style="stop-color:#eaeaea;stop-opacity:1;" offset="100%" />
    </linearGradient>
  </defs>';

$svg = array(
	'normal' => $svgPre . '<rect fill="url(#gradAct#ID#)" width="17px" height="83px"/>
	<text class="defaultfont" style="text-anchor: middle;font-style:normal;font-weight:normal;letter-spacing:0px;word-spacing:0px;" x="-41px" y="13px" transform="matrix(0,-1,1,0,0,0)">REPLACE</text>
	<rect width="18px" fill="#909090" height="1px" />',
	'active' => '<rect fill="#f3f7ff" width="19px" height="83px"/>
	<text class="defaultfont" style="text-anchor: middle;font-style:normal;font-weight:normal;letter-spacing:0px;word-spacing:0px;" x="-41px" y="13px" transform="matrix(0,-1,1,0,0,0)">REPLACE</text>
	<rect width="18px" fill="#909090" height="1px" />',
	'disabled' => $svgPre . '<rect fill="url(#gradDis#ID#)" width="17px" height="83px"/>
	<text class="defaultfont" style="text-anchor: middle;font-style:normal;font-weight:normal;letter-spacing:0px;word-spacing:0px;" x="-41px" y="13px" transform="matrix(0,-1,1,0,0,0)">REPLACE</text>
	<rect width="18px" fill="#909090" height="1px" />',
);

$vtab = array(
	'FILE_TABLE' => array(
		'file' => 'we_language/' . $GLOBALS["WE_LANGUAGE"] . '/v-tabs/documents',
		'show' => permissionhandler::hasPerm('CAN_SEE_DOCUMENTS') || permissionhandler::hasPerm('ADMINISTRATOR'),
		'size' => array(19, 83),
		'desc' => g_l('global', '[documents]'),
	),
	'TEMPLATES_TABLE' => array(
		'file' => 'we_language/' . $GLOBALS['WE_LANGUAGE'] . '/v-tabs/templates',
		'show' => permissionhandler::hasPerm('CAN_SEE_TEMPLATES'),
		'size' => array(19, 83),
		'desc' => g_l('global', '[templates]'),
	),
	'OBJECT_FILES_TABLE' => array(
		'file' => 'we_language/' . $GLOBALS["WE_LANGUAGE"] . '/v-tabs/objects',
		'show' => defined('OBJECT_TABLE') && permissionhandler::hasPerm('CAN_SEE_OBJECTFILES'),
		'size' => array(19, 83),
		'desc' => g_l('global', '[objects]'),
	),
	'OBJECT_TABLE' => array(
		'file' => 'we_language/' . $GLOBALS["WE_LANGUAGE"] . '/v-tabs/classes',
		'show' => defined('OBJECT_TABLE') && permissionhandler::hasPerm("CAN_SEE_OBJECTS"),
		'size' => array(19, 83),
		'desc' => g_l('javaMenu_object', '[classes]'),
	)
);
foreach($vtab as &$val){
	if(file_exists(WE_INCLUDES_PATH . $val['file'] . '_normal.gif')){
		$val['size'] = getimagesize(WE_INCLUDES_PATH . $val['file'] . '_normal.gif');
	}
}
unset($val);

echo we_html_element::jsScript(JS_DIR . 'images.js') .
 we_html_element::jsScript(JS_DIR . 'we_vtabs.js');
?>
<script type="text/javascript"><!--

	function setTab(table) {
		if (we_tabs === null) {
			setTimeout("setTab('" + table + "')", 500);
			return;
		}
		switch (table) {
			default:
				break;
<?php
$i = 0;
foreach($vtab as $tab => $val){
	if(defined($tab)){
		echo 'case "' . constant($tab) . '":
		we_tabs[' . $i . '].setState(TAB_ACTIVE,false,we_tabs);
		break;
		';
	}
	++$i;
}
?>

		}
	}

	var we_tabs = new Array(
<?php
$tmp = array();
$id = 0;
$pos = 0;
foreach($vtab as $tab => $val){
	$file = WE_INCLUDES_DIR . $val['file'];
	$tmp[] = ($val['show'] ?
			'new we_tab("#",\'' . str_replace(array('REPLACE', '#ID#', "\n"), array($val['desc'], ++$id, ''), $svg['normal']) . '\', \'' . str_replace(array('REPLACE', '#ID#', "\n"), array($val['desc'], ++$id, ''), $svg['active']) . '\', \'' . str_replace(array('REPLACE', '#ID#', "\n"), array($val['desc'], ++$id, ''), $svg['disabled']) . '\', ' . $val['size'][0] . ',' . $val['size'][1] . ' ,' . ($val['show'] ? we_tab::NORMAL : we_tab::DISABLED) . ', "if(top.deleteMode){we_cmd(\'exit_delete\', \'' . constant($tab) . '\');};treeOut();we_cmd(\'loadVTab\', \'' . constant($tab) . '\' ,0);",true,' . $pos . ')' :
			'null');
	$pos++;
}
echo implode(',', $tmp);
?>
	);

	var oldWidth = <?php echo weTree::DefaultWidth; ?>;

	function toggleTree() {
		top.toggleTree();
	}

	function incTree() {
		var w = parseInt(top.getTreeWidth());
		if ((w ><?php echo weTree::MinWidth; ?>) && (w <<?php echo weTree::MaxWidth; ?>)) {
			w +=<?php echo weTree::StepWidth; ?>;
			top.setTreeWidth(w);
		}
		if (w >=<?php echo weTree::MaxWidth; ?>) {
			w =<?php echo weTree::MaxWidth; ?>;
			self.document.getElementById("incBaum").style.backgroundColor = "grey";
		}
	}

	function decTree() {
		var w = parseInt(top.getTreeWidth());
		w -=<?php echo weTree::StepWidth; ?>;
		if (w ><?php echo weTree::MinWidth; ?>) {
			top.setTreeWidth(w);
			self.document.getElementById("incBaum").style.backgroundColor = "";
		}
		if (w <=<?php echo weTree::MinWidth; ?> && ((w +<?php echo weTree::StepWidth; ?>) >=<?php echo weTree::MinWidth; ?>)) {
			toggleTree();
		}
	}


	function treeOut() {
		if (top.getTreeWidth() <= <?php echo weTree::MinWidth; ?>) {
			toggleTree();
		}
	}
//-->
</script>
<div style="position:absolute;top:8px;left:5px;z-index:10;border-left:1px solid #909090;border-bottom:1px solid #909090;text-decoration:none ">
	<script type="text/javascript"><!--
	for (var i = 0; i < we_tabs.length; i++) {
			if (we_tabs[i] !== null) {
				we_tabs[i].write();
			}
		}
<?php
if(($tab = we_base_request::_(we_base_request::STRING, "table"))){
	echo "var defTab = '" . $tab . "';";
} else {
	$ok = false;
	foreach($vtab as $tab => $val){
		if($val['show']){
			echo "var defTab = '" . constant($tab) . "';";
			$ok = true;
			break;
		}
	}
	if(!$ok){
		echo "var defTab = '';";
	}
}
?>
		setTab(defTab);
//-->
	</script>
</div>
<img id="incBaum" src="<?php echo BUTTONS_DIR ?>icons/function_plus.gif" width="9" height="12" style="position:absolute;bottom:53px;left:5px;border:1px solid grey;padding:0 1px;cursor: pointer;<?php echo ($_treewidth <= 100) ? 'bgcolor:grey;' : ''; ?>" onclick="incTree();">
<img id="decBaum" src="<?php echo BUTTONS_DIR ?>icons/function_minus.gif" width="9" height="12" style="position:absolute;bottom:33px;left:5px;border:1px solid grey;padding:0 1px;cursor: pointer;<?php echo ($_treewidth <= 100) ? 'bgcolor:grey;' : ''; ?>" onclick="decTree();">
<img id="arrowImg" src="<?php echo BUTTONS_DIR ?>icons/direction_<?php echo ($_treewidth <= 100) ? "right" : "left"; ?>.gif" width="9" height="12" style="position:absolute;bottom:13px;left:5px;border:1px solid grey;padding:0 1px;cursor: pointer;" onclick="toggleTree();">
