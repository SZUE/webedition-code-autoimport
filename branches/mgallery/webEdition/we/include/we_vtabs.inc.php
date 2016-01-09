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
		'desc' => '<i class="fa fa-file-o"></i> ' . g_l('global', '[documents]'),
	),
	'TEMPLATES_TABLE' => array(
		'show' => permissionhandler::hasPerm('CAN_SEE_TEMPLATES'),
		'desc' => '<i class="fa fa-file-code-o"></i> ' . g_l('global', '[templates]'),
	),
	'OBJECT_FILES_TABLE' => array(
		'show' => defined('OBJECT_TABLE') && permissionhandler::hasPerm('CAN_SEE_OBJECTFILES'),
		'desc' => '<i class="fa fa-file"></i> ' . g_l('global', '[objects]'),
	),
	'OBJECT_TABLE' => array(
		'show' => defined('OBJECT_TABLE') && permissionhandler::hasPerm("CAN_SEE_OBJECTS"),
		'desc' => '<i class="fa fa-chevron-left"></i><i class="fa fa-chevron-right"></i> ' . g_l('javaMenu_object', '[classes]'),
	),
	'VFILE_TABLE' => array(
		'show' => we_base_moduleInfo::isActive(we_base_moduleInfo::COLLECTION) && permissionhandler::hasPerm("CAN_SEE_COLLECTIONS"),
		'desc' => '<i class="fa fa-archive"></i> ' . g_l('global', '[vfile]'),
	)
);
$i = 0;
$jsTabs = array();
$defTab = we_base_request::_(we_base_request::STRING, "table", '');
foreach($vtab as $tab => $val){
	if(defined($tab)){
		$jsTabs[] = 'case WE().consts.tables.' . $tab . ':
		setActiveVTab(' . $i . ');
		break;';
	}
	if($val['show']){
		echo '<div class="tab tabNorm" onclick="clickVTab(this,' . $i . ',\'' . constant($tab) . '\');"><span class="middlefont">' . $val['desc'] . '</span></div>';
	++$i;
	}
}
?>
<script><!--
	function setTab(table) {
		switch (table) {
<?php
echo implode("\n", $jsTabs);
?>
		}
	}

//	setTab('<?php echo $defTab; ?>');
//-->
</script>
<div id="baumArrows">
	<div class="baumArrow" id="incBaum" title="<?php echo g_l('global', '[tree][grow]'); ?>" <?php echo ($_treewidth <= 100) ? 'style="background-color: grey"' : ''; ?> onclick="incTree();"><i class="fa fa-plus"></i></div>
	<div class="baumArrow" id="decBaum" title="<?php echo g_l('global', '[tree][reduce]'); ?>" <?php echo ($_treewidth <= 100) ? 'style="background-color: grey"' : ''; ?> onclick="decTree();"><i class="fa fa-minus"></i></div>
</div>
