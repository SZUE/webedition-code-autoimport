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

$vtab = array(
	'FILE_TABLE' => array(
		'show' => permissionhandler::hasPerm('CAN_SEE_DOCUMENTS') || permissionhandler::hasPerm('ADMINISTRATOR'),
		'desc' => g_l('global', '[documents]'),
	),
	'TEMPLATES_TABLE' => array(
		'show' => permissionhandler::hasPerm('CAN_SEE_TEMPLATES'),
		'desc' => g_l('global', '[templates]'),
	),
	'OBJECT_FILES_TABLE' => array(
		'show' => defined('OBJECT_TABLE') && permissionhandler::hasPerm('CAN_SEE_OBJECTFILES'),
		'desc' => g_l('global', '[objects]'),
	),
	'OBJECT_TABLE' => array(
		'show' => defined('OBJECT_TABLE') && permissionhandler::hasPerm("CAN_SEE_OBJECTS"),
		'desc' => g_l('javaMenu_object', '[classes]'),
	),
	'VFILE_TABLE' => array(
		'show' => we_base_moduleInfo::isActive(we_base_moduleInfo::COLLECTION) && permissionhandler::hasPerm("CAN_SEE_COLLECTIONS"),
		'desc' => g_l('global', '[vfile]'),
	)
);
?>
<div id="vtab">
	<?php
	$i = 0;
	$jsTabs = array();
	$defTab = we_base_request::_(we_base_request::STRING, "table", '');
	foreach($vtab as $tab => $val){
		if(defined($tab)){
			$jsTabs[] = 'case "' . constant($tab) . '":
		setActiveTab(' . $i . ');
		break;';
		}
		if($val['show']){
			echo '<div class="tabNorm" onclick="setActiveTab(' . $i . ');if(top.deleteMode){we_cmd(\'exit_delete\', \'' . constant($tab) . '\');};treeOut();we_cmd(\'loadVTab\', \'' . constant($tab) . '\' ,0);"><span class="middlefont">' . $val['desc'] . '</span></div>';
		}
		if(!$defTab && $val['show']){
			$defTab = constant($tab);
		}
		++$i;
	}
	?>
	<script><!--
		function setActiveTab(no) {
			var allTabs = document.getElementById("vtab").getElementsByTagName("div");
			for (var i = 0; i < allTabs.length; i++) {
				allTabs[i].className = (i == no ? "tabActive" : "tabNorm");
			}
		}
		function setTab(table) {
			switch (table) {
				default:
					break;
<?php
echo implode("\n", $jsTabs);
?>
			}
		}

		setTab('<?php echo $defTab; ?>');
//-->
	</script>
</div>
<div id="baumArrows">
	<div class="baumArrow" id="incBaum" <?php echo ($_treewidth <= 100) ? 'style="background-color: grey"' : ''; ?> onclick="incTree();"><i class="fa fa-plus"></i></div>
	<div class="baumArrow" id="decBaum" <?php echo ($_treewidth <= 100) ? 'style="background-color: grey"' : ''; ?> onclick="decTree();"><i class="fa fa-minus"></i></div>
	<div class="baumArrow" onclick="toggleTree();"><i id="arrowImg" class="fa fa-lg fa-caret-<?php echo ($_treewidth <= 100) ? "right" : "left"; ?>" ></i></div>
</div>
