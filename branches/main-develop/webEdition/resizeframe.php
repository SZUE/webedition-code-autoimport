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

protect();
htmlTop();


//	Here begins the code for showing the correct frameset.
//	To improve readability the different cases are outsourced
//	in several functions, for SEEM, normal or edit_include-Mode.

/**
 * function startNormalMode()
 * @desc	This function writes the frameset in the resizeframe for the webedition-start
 			in the normal mode.
 */

function startNormalMode() {

	$_treewidth = isset($_COOKIE["treewidth_main"]) ? $_COOKIE["treewidth_main"]  : WE_TREE_DEFAULT_WIDTH;

	// Get the width of the sidebar
	$_sidebarwidth = 0;
	if(defined("SIDEBAR_DISABLED") && SIDEBAR_DISABLED == 1) {
		$_sidebarwidth = 0;

	} else if(!defined("SIDEBAR_SHOW_ON_STARTUP") || SIDEBAR_SHOW_ON_STARTUP == 1) {
		if(defined("SIDEBAR_DEFAULT_WIDTH")) {
			$_sidebarwidth = SIDEBAR_DEFAULT_WIDTH;

		} else {
			$_sidebarwidth = 300;

		}

	}

?>
		<div style="position:absolute;top:0;bottom:0;left:0;right:0;border: 0;">
       <div style="position:absolute;top:0;bottom:0;left:0;width:<?php print $_treewidth;?>px;border-right:1px solid black;" id="bframeDiv">
				<iframe src="<?php print WEBEDITION_DIR; ?>baumFrame.php" style="border:0;width:100%;height:100%;overflow: hidden;" name="bframe"></iframe>
			</div>
			<div style="position:absolute;top:0;bottom:0;right:<?php echo $_sidebarwidth; ?>px;left:<?php print $_treewidth;?>px;border-left:1px solid black;overflow: hidden;" id="bm_content_frameDiv">
				<iframe src="<?php print WEBEDITION_DIR; ?>multiContentFrame.php" name="bm_content_frame" style="border:0;width:100%;height:100%;overflow: hidden;"></iframe>
       </div>
			<?php if($_sidebarwidth>0){ ?>
       <div style="position:absolute;top:0;bottom:0;right:0;width:<?php echo $_sidebarwidth; ?>px;" id="sidebarDiv">
				<iframe src="<?php print WEBEDITION_DIR; ?>sideBarFrame.php" name="sidebar" style="border:0;width:100%;height:100%;overflow: hidden;"></iframe>
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

    for($i=1; $i<sizeof($_REQUEST["we_cmd"]); $i++){
    	$we_cmds .= "we_cmd[" . $i . "]=" . $_REQUEST["we_cmd"][$i] . "&";
	}

	if($GLOBALS["BROWSER"]== "NN"){
?>
  <FRAMESET cols="0,*,0" border="0" frameborder="NO">
		<frame src="baumFrame.php" name="bframe" scrolling="no" noresize>
		<frame src="<?php print WEBEDITION_DIR; ?>multiContentFrame.php?<?php print $we_cmds ?>SEEM_edit_include=true" name="bm_content_frame" noresize>
		<frame src="<?php print WEBEDITION_DIR; ?>sideBarFrame.php" name="sidebar">
	</FRAMESET>
<?php
	} else {
?>
	<FRAMESET cols="0,*,0" border="1" frameborder="YES">
		<frame src="baumFrame.php" name="bframe" scrolling="no" noresize>
		<frame src="<?php print WEBEDITION_DIR; ?>multiContentFrame.php?<?php print $we_cmds ?>SEEM_edit_include=true" name="bm_content_frame" noresize>
		<frame src="<?php print WEBEDITION_DIR; ?>sideBarFrame.php" name="sidebar">
	</FRAMESET>
<?php
	}
}


/**
 * function startSEEMMode()
 * @desc	This function writes the frameset in the resizeframe for the webedition-start
 			in the SEEM-mode.
 */
function startSEEMMode(){
	if (($GLOBALS["BROWSER"] == "NN6") || ($GLOBALS["BROWSER"] == "OPERA")){
?>
  <FRAMESET cols="0,*,0" border="1">
		<frame src="<?php print HTML_DIR; ?>white.html" name="bframe" scrolling="no" noresize>
		<frame src="<?php print WEBEDITION_DIR; ?>multiContentFrame.php" name="bm_content_frame">
		<frame src="<?php print WEBEDITION_DIR; ?>sideBarFrame.php" name="sidebar">
	</FRAMESET>
<?php
	} else {
?>
	<frameset cols="0,*,0" border="0" frameborder="0">
		<frame src="<?php print HTML_DIR; ?>white.html" name="bframe" frameborder="0" scrolling="no" noresize>
		<frame src="<?php print WEBEDITION_DIR; ?>multiContentFrame.php" name="bm_content_frame">
		<frame src="<?php print WEBEDITION_DIR; ?>sideBarFrame.php" name="sidebar">
	</frameset>
<?php
	}
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
} else {
	echo '<body style="margin:0;">';
	startNormalMode();
	echo '</body>';
}
?>
</html>
