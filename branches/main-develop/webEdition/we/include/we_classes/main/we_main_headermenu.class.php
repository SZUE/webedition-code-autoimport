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
class we_main_headermenu{

	static function pCSS(){
		print we_html_element::cssLink(WEBEDITION_DIR . 'css/menu/pro_drop_1.css');
		//needed, to correct IE Bug
		$_BROWSER = new we_base_browserDetect();
		if($_BROWSER->getBrowser() == we_base_browserDetect::IE && $_BROWSER->getBrowserVersion() < 7){
			echo we_html_element::jsScript(WEBEDITION_DIR . 'css/menu/stuHover.js');
		}
	}
	static function pJS(){
		echo we_html_element::jsScript(JS_DIR . 'images.js') .
		we_html_element::jsScript(JS_DIR . 'weSidebar.php');
		we_html_element::jsElement('
top.weSidebar = weSidebar;

	preload("busy_icon","' . IMAGE_DIR . 'logo-busy.gif");
	preload("empty_icon","' . IMAGE_DIR . 'pixel.gif");
	function toggleBusy(foo){
		if(!document.images["busy"]){
			setTimeout("toggleBusy("+foo+")",200);
		}else{
			changeImage(null,"busy",(foo ? "busy_icon" : "empty_icon"));
		}
	}
');
	}

	static function pbody(){
//	width of java-/XUL-Menu
		$_menu_width = 360;
		$port = defined("HTTP_PORT") ? HTTP_PORT : "";

// all available elements
		$jmenu = null;
		$navigationButtons = array();

		if(!isset($_REQUEST["SEEM_edit_include"])){ // there is only a menu when not in seem_edit_include!
			include_once($_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/java_menu/we_menu.inc.php");
			ksort($we_menu);
			if(// menu for normalmode
				isset($_SESSION["we_mode"]) && $_SESSION["we_mode"] == "normal"){

				$jmenu = new weJavaMenu($we_menu, SERVER_NAME, "top.load", getServerProtocol(), $port, $_menu_width, 30);
			} else{ // menu for seemode
				if(permissionhandler::isUserAllowedForAction("header", "with_java")){
					$jmenu = new weJavaMenu($we_menu, SERVER_NAME, "top.load", getServerProtocol(), $port, $_menu_width, 30);
				} else{
//  no menu in this case !
					$navigationButtons[] = array(
						"onclick" => "top.we_cmd('dologout');",
						"imagepath" => "/navigation/close.gif",
						"text" => g_l('javaMenu_global', "[close]")
					);
				}
			}
			$navigationButtons = array_merge($navigationButtons, array(
				array("onclick" => "top.we_cmd('start_multi_editor');", "imagepath" => "/navigation/home.gif", "text" => g_l('javaMenu_global', "[home]")),
				array("onclick" => "top.weNavigationHistory.navigateReload();", "imagepath" => "/navigation/reload.gif", "text" => g_l('javaMenu_global', "[reload]")),
				array("onclick" => "top.weNavigationHistory.navigateBack();", "imagepath" => "/navigation/back.gif", "text" => g_l('javaMenu_global', "[back]")),
				array("onclick" => "top.weNavigationHistory.navigateNext();", "imagepath" => "/navigation/next.gif", "text" => g_l('javaMenu_global', "[next]")),
				)
			);
		}
		?>
		<div style="position:absolute;top:0px;left:0px;right:0px;bottom:0px;border:0px;">
			<div style="position:relative;border:0px;float:left;" >
				<?php
				if($jmenu){
					print $jmenu->getCode(false);
				}
				?>
			</div>
			<div style="position:relative;bottom:0px;border:0px;padding-left: 10px;float:left;" >
				<?php
				if(count($navigationButtons)){
					foreach($navigationButtons as $button){
						print '<div style="float:left;margin-top:5px;" class="navigation_normal" onclick="' . $button['onclick'] . '" onmouseover="this.className=\'navigation_hover\'" onmouseout="this.className=\'navigation_normal\'"><img border="0" hspace="2" src="' . IMAGE_DIR . $button['imagepath'] . '" width="17" height="18" alt="' . $button['text'] . '" title="' . $button['text'] . '"></div>';
					}
				}
				?></div>
			<div style="position:absolute;top:0px;bottom:0px;right:10px;border:0px;" >


				<?php
				include_once( $_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/jsMessageConsole/messageConsole.inc.php" );
				print createMessageConsole("mainWindow");
				?>
				<img src="<?php print IMAGE_DIR ?>pixel.gif" alt="" name="busy" width="20" height="19">
				<img src="<?php print IMAGE_DIR ?>pixel.gif" alt="" width="10" height="19">
				<img src="<?php print IMAGE_DIR ?>webedition.gif" alt="" width="78" height="25">
				<img src="<?php print IMAGE_DIR ?>pixel.gif" alt="" width="5" height="19">
			</div>
		</div>
		<?php
	}
}