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


//	Here begins the code for showing the correct frameset.
//	To improve readability the different cases are outsourced
//	in several functions, for SEEM, normal or edit_include-Mode.

/**
 * function startNormalMode()
 * @desc	This function writes the frameset in the resizeframe for the webedition-start
  in the normal mode.
 */
function getSidebarWidth(){
// Get the width of the sidebar
	$_sidebarwidth = 0;
	if(defined("SIDEBAR_DISABLED") && SIDEBAR_DISABLED == 1){
		$_sidebarwidth = 0;
	} else if(!defined("SIDEBAR_SHOW_ON_STARTUP") || SIDEBAR_SHOW_ON_STARTUP == 1){
		if(defined("SIDEBAR_DEFAULT_WIDTH")){
			$_sidebarwidth = SIDEBAR_DEFAULT_WIDTH;
		} else{
			$_sidebarwidth = 300;
		}
	}
	return $_sidebarwidth;
}

function startNormalMode(){
	$_sidebarwidth = getSidebarWidth();
	$_treewidth = isset($_COOKIE["treewidth_main"]) ? $_COOKIE["treewidth_main"] : WE_TREE_DEFAULT_WIDTH;
	?>
	<div style="position:absolute;top:0px;bottom:0px;left:0px;right:0px;border: 0px;">
		<div style="position:absolute;top:0px;bottom:0px;left:0px;width:<?php print $_treewidth; ?>px;border-right:1px solid black;" id="bframeDiv">
			<iframe frameBorder="0" src="<?php print WEBEDITION_DIR; ?>baumFrame.php" style="border:0px;width:100%;height:100%;overflow: hidden;" name="bframe"></iframe>
		</div>
		<div style="position:absolute;top:0px;bottom:0px;right:<?php echo $_sidebarwidth; ?>px;left:<?php print $_treewidth; ?>px;border-left:1px solid black;overflow: hidden;" id="bm_content_frameDiv">
			<iframe frameBorder="0" src="<?php print WEBEDITION_DIR; ?>multiContentFrame.php" name="bm_content_frame" style="border:0px;width:100%;height:100%;overflow: hidden;"></iframe>
		</div>
		<?php if($_sidebarwidth > 0){ ?>
			<div style="position:absolute;top:0px;bottom:0px;right:0px;width:<?php echo $_sidebarwidth; ?>px;" id="sidebarDiv">
				<iframe frameBorder="0" src="<?php print WEBEDITION_DIR; ?>sideBarFrame.php" name="sidebar" style="border:0px;width:100%;height:100%;overflow: hidden;"></iframe>
			</div>
		<?php } ?>
	</div>
	<?php
}

/**
 * function startEditInclude()
 * @desc	This function writes the frameset in the resizeframe for an edit-include-window
 */
function startEditIncludeMode(){

	$we_cmds = "we_cmd[0]=edit_document&";

	for($i = 1; $i < sizeof($_REQUEST['we_cmd']); $i++){
		$we_cmds .= "we_cmd[" . $i . "]=" . $_REQUEST['we_cmd'][$i] . "&";
	}
}

/**
 * function startSEEMMode()
 * @desc	This function writes the frameset in the resizeframe for the webedition-start
  in the SEEM-mode.
 */
function startSEEMMode(){
	$_sidebarwidth = getSidebarWidth();
	?>
	<div style="position:absolute;top:0px;bottom:0px;left:0px;right:0px;border: 0px;">
		<div style="position:absolute;top:0px;bottom:0px;left:0px;width:0px;border-right:1px solid black;" id="bframeDiv">
			<iframe frameBorder="0" src="<?php print HTML_DIR; ?>white.html" style="border:0px;width:100%;height:100%;overflow: hidden;" name="bframe"></iframe>
		</div>
		<div style="position:absolute;top:0px;bottom:0px;right:<?php echo $_sidebarwidth; ?>px;left:0px;border-left:1px solid black;overflow: hidden;" id="bm_content_frameDiv">
			<iframe frameBorder="0" src="<?php print WEBEDITION_DIR; ?>multiContentFrame.php" name="bm_content_frame" style="border:0px;width:100%;height:100%;overflow: hidden;"></iframe>
		</div>
		<?php if($_sidebarwidth > 0){ ?>
			<div style="position:absolute;top:0px;bottom:0px;right:0px;width:<?php echo $_sidebarwidth; ?>px;" id="sidebarDiv">
				<iframe frameBorder="0" src="<?php print WEBEDITION_DIR; ?>sideBarFrame.php" name="sidebar" style="border:0px;width:100%;height:100%;overflow: hidden;"></iframe>
			</div>
		<?php } ?>
	</div>
	<?php
}
?>

<script type="text/javascript"><!--
	function we_cmd(){
		var args = "";
		for(var i = 0; i < arguments.length; i++){
			args += 'arguments['+i+']' + ( (i < (arguments.length-1)) ? ',' : '');
		}
		eval('parent.we_cmd('+args+')');
	}



	//-->
</script>
</head>
<?php
//	Here begins the controller of the page
//  Edit an included file with SEEM.
if(isset($_REQUEST["SEEM_edit_include"]) && $_REQUEST["SEEM_edit_include"]){
	startEditIncludeMode();

//  We are in SEEM-Mode
} else if($_SESSION["we_mode"] == "seem"){
	startSEEMMode();

//  Open webEdition normally
} else{
	echo '<body style="margin:0px;border:0px none;overflow:hidden;">';
	startNormalMode();
	echo '</body>';
}
?>
</html>
