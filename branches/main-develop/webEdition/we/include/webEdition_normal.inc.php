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

include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_language/".$GLOBALS["WE_LANGUAGE"]."/cache.inc.php");

   /**
	* @return void
	* @desc prints the functions needed for the tree.
	*/
	function pWebEdition_Tree(){
		$Tree = new weMainTree("webEdition.php","top","self.Tree","top.load");
		print $Tree->getJSTreeCode();
	}

   /**
	* @return void
	* @desc prints JavaScript functions only needed in normal mode
	*/
	function pWebEdition_JSFunctions(){?>
function toggleBusy(w) {
	if(w == busy || firstLoad==false)
		return;
	if(self.header) {
		if(self.header.toggleBusy) {
			busy=w;
			self.header.toggleBusy(w);
			return;
		}
	}
	setTimeout("toggleBusy("+w+");",300);
}

function doUnload(whichWindow) {

	// unlock all open documents
	var _usedEditors = top.weEditorFrameController.getEditorsInUse();

	var docIds = "";
	var docTables = "";

	for (frameId in _usedEditors) {

		if (_usedEditors[frameId].EditorType != "cockpit") {

			docIds += _usedEditors[frameId].getEditorDocumentId() + ",";
			docTables += _usedEditors[frameId].getEditorEditorTable() + ",";
		}
	}

	if (docIds) {

		top.we_cmd('unlock',docIds,'<?php print $_SESSION["user"]["ID"]; ?>',docTables);
		if(top.opener){
			top.opener.focus();

		}
	}

	try{
        if(jsWindow_count) {
            for(i = 0;i < jsWindow_count;i++){
        	   eval("jsWindow"+i+"Object.close()");
        	}
        }
		if(browserwind){
			browserwind.close();
		}
    } catch(e){

    }
    //  only when no SEEM-edit-include window is closed
    if(whichWindow != "include"){
        if(opener) {
            opener.location.replace('<?php print WEBEDITION_DIR; ?>we_loggingOut.php');
        }
    }
}

var widthBeforeDeleteMode = 0;
var widthBeforeDeleteModeSidebar = 0;

	<?php
	}

   /**
	* @return void
	* @desc prints the different cases for the function we_cmd
	*/
	function pWebEdition_JSwe_cmds(){?>
		case "new":
			treeData.unselectnode();
			if(typeof(arguments[5])!="undefined") {
				top.weEditorFrameController.openDocument(arguments[1],arguments[2],arguments[3],"",arguments[4],"",arguments[5]);
			} else if(typeof(arguments[4])!="undefined" && arguments[5]=="undefined") {
				top.weEditorFrameController.openDocument(arguments[1],arguments[2],arguments[3],"","","",arguments[5]);
			} else {
				top.weEditorFrameController.openDocument(arguments[1],arguments[2],arguments[3],"",arguments[4]);
			}
			break;

		case "load":
			if(self.Tree)
				if(self.Tree.setScrollY)
					self.Tree.setScrollY();
			we_cmd("setTab",arguments[1]);
			//toggleBusy(1);
			we_repl(self.load,url,arguments[0]);
			break;
		case "exit_delete":
		case "exit_move":
			deleteMode = false;
			treeData.setstate(treeData.tree_states["edit"]);
			drawTree();

			self.rframe.bframe.document.getElementById("bm_vtabsDiv").style.height = "1px";
			self.rframe.bframe.document.getElementById("bm_mainDiv").style.top = "1px";
			top.setTreeWidth(widthBeforeDeleteMode);
			top.setSidebarWidth(widthBeforeDeleteModeSidebar);
			break;
		case "delete":
			if(top.deleteMode != arguments[1]){
				top.deleteMode=arguments[1];
			}
			if(!top.deleteMode &&  treeData.state==treeData.tree_states["select"]){
				treeData.setstate(treeData.tree_states["edit"]);
				drawTree();
			}
			self.rframe.bframe.document.getElementById("bm_vtabsDiv").style.height = "150px";
			self.rframe.bframe.document.getElementById("bm_mainDiv").style.top = "150px";

			var width = top.getTreeWidth();

			widthBeforeDeleteMode = width;

			if (width < 420) {
				top.setTreeWidth(420);
				top.storeTreeWidth(420);
			}

			var widthSidebar = top.getSidebarWidth();

			widthBeforeDeleteModeSidebar = widthSidebar;

			if(arguments[2] != 1) we_repl(self.rframe.bframe.treeheader,url,arguments[0]);
			break;
		case "move":
			if(top.deleteMode != arguments[1]){
				top.deleteMode=arguments[1];
			}
			if(!top.deleteMode && treeData.state==treeData.tree_states["selectitem"]){
				treeData.setstate(treeData.tree_states["edit"]);
				drawTree();
			}
			self.rframe.bframe.document.getElementById("bm_vtabsDiv").style.height = "160px";
			self.rframe.bframe.document.getElementById("bm_mainDiv").style.top = "160px";

			var width = top.getTreeWidth();

			widthBeforeDeleteMode = width;

			if (width < 500) {
				top.setTreeWidth(500);
				top.storeTreeWidth(500);
			}

			var widthSidebar = top.getSidebarWidth();

			widthBeforeDeleteModeSidebar = widthSidebar;

			if(arguments[2] != 1) {
				we_repl(self.rframe.bframe.treeheader,url,arguments[0]);
			}
			break;

		<?php
	}


   /**
	* @return void
	* @desc the frameset for the SeeMode
	*/
	function pWebEdition_Frameset(){?>
	<div style="position:fixed;top:0px;left:0px;right:0px;bottom:0px;border:0px;">
		<div style="position:absolute;top:0px;left:0px;right:0px;height:32px;border-bottom: 1px solid black;">
			<?php include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/header.php");?>
		</div>
		<div style="position:absolute;top:32px;left:0px;right:0px;bottom:<?php print ( (isset($_SESSION["prefs"]["debug_normal"]) && $_SESSION["prefs"]["debug_normal"] != 0)) ? 100 : 0; ?>px;border: 0;">
			<iframe src="<?php print WEBEDITION_DIR; ?>resizeframe.php" style="border:0;width:100%;height:100%;overflow: hidden;" id="rframe" name="rframe"></iframe>
		</div>
		<div style="position:absolute;left:0px;right:0px;bottom:0px;height:<?php print ( (isset($_SESSION["prefs"]["debug_normal"]) && $_SESSION["prefs"]["debug_normal"] != 0)) ? 100 : 0; ?>px;border: 1px solid;">
			<div style="position:absolute;top:0px;bottom:0px;width:25%;border:0px;">"
			<iframe src="<?php print HTML_DIR ?>white.html" style="border-right:1px solid black;width:100%;height:100%;overflow: hidden;" name="load"></iframe>
			</div>
			<div style="position:absolute;top:0px;bottom:0px;width:25%;border:0px;">"
			<iframe src="<?php print HTML_DIR ?>white.html" style="border-right:1px solid black;width:100%;height:100%;overflow: hidden;" name="load2"></iframe>
			</div>
			<!-- Bugfix Opera >=10.5  target name is always "ad" -->
			<div style="position:absolute;top:0px;bottom:0px;width:10%;border:0px;">"
			<iframe src="<?php print HTML_DIR ?>white.html" style="border-right:1px solid black;width:100%;height:100%;overflow: hidden;" name="ad"></iframe>
			</div>
			<div style="position:absolute;top:0;bottom:0;width:10%;border:0;">"
			<iframe src="<?php print WE_USERS_MODULE_PATH; ?>we_users_ping.php" style="border-right:1px solid black;width:100%;height:100%;overflow: hidden;" name="ping"></iframe>
			</div>
			<div style="position:absolute;top:0;bottom:0;width:10%;border:0;">"
			<iframe src="<?php print HTML_DIR ?>white.html" style="border-right:1px solid black;width:100%;height:100%;overflow: hidden;" name="postframe"></iframe>
			</div>
			<div style="position:absolute;top:0;bottom:0;width:10%;border:0;">"
			<iframe src="<?php print HTML_DIR ?>white.html" style="border-right:1px solid black;width:100%;height:100%;overflow: hidden;" name="plugin"></iframe>
			</div>
		</div>
	</div>
	<?php
	}
