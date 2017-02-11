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
class we_main_headermenu{

	/**
	 * creates a new messageConsole
	 *
	 * @param string $consoleName
	 * @return string
	 */
	public static function createMessageConsole($consoleName = 'NoName'){
		return 			'
<div id="messageConsole" onclick="_console_.openMessageConsole();">
<table><tr>
	<td style="vertical-align:middle"><div class="small messageConsoleMessage" id="messageConsoleMessage' . $consoleName . '">--</div></td>
	<td><div class="navigation" id="messageConsoleImageDiv"><i id="messageConsoleImage' . $consoleName . '" class="fa fa-lg fa-bell"></i></div></td>
	</tr></table>
</div>';
	}

	static function css(){
		return
			we_html_element::cssLink(WEBEDITION_DIR . 'css/menu.css') .
			we_html_element::jsScript(JS_DIR . 'menu/clickMenu.js');
	}

	static function getMenuReloadCode($location = 'top.opener.', $asCmd = false){
		$menu = self::getMenu();
		return $asCmd ? ['updateMenu', $menu->getHTML()] :
			$location . 'document.getElementById("nav").parentNode.innerHTML="' . str_replace("\n", '"+"', addslashes($menu->getHTML())) . '";';
	}

	private static function getMenu(){
		if(we_base_request::_(we_base_request::BOOL, 'SEEM_edit_include')){ // there is only a menu when not in seem_edit_include!
			return null;
		}
		$we_menu = include(WE_INCLUDES_PATH . 'menu/we_menu.inc.php');

		if(// menu for normalmode
			isset($_SESSION['weS']['we_mode']) && $_SESSION['weS']['we_mode'] == we_base_constants::MODE_NORMAL){
			$jmenu = new we_base_menu($we_menu, "top.load");
		} else { // menu for seemode
			if(!permissionhandler::isUserAllowedForAction("header", "with_java")){
				return null;
			}
			$jmenu = new we_base_menu($we_menu, "top.load");
		}

		return $jmenu;
	}

	static function pbody($msg){

// all available elements
		$jmenu = self::getMenu();
		$navigationButtons = [];

		if(!we_base_request::_(we_base_request::BOOL, 'SEEM_edit_include')){ // there is only a menu when not in seem_edit_include!
			if(isset($_SESSION['weS']['we_mode']) && $_SESSION['weS']['we_mode'] == we_base_constants::MODE_NORMAL){
// menu for normalmode
			} else if(permissionhandler::isUserAllowedForAction('header', 'with_java')){
// menu for seemode
			} else {
//  no menu in this case !
				$navigationButtons[] = [
					"onclick" => "top.we_cmd('dologout');",
					"i" => "close",
					"text" => g_l('javaMenu_global', '[close]')
				];
			}
			$navigationButtons = array_merge($navigationButtons, [
				["onclick" => "WE().layout.weNavigationHistory.navigateReload();", "i" => "refresh", "text" => g_l('javaMenu_global', '[reload]')],
				["onclick" => "WE().layout.weNavigationHistory.navigateBack();", "i" => "caret-left", "text" => g_l('javaMenu_global', '[back]')],
				["onclick" => "WE().layout.weNavigationHistory.navigateNext();", "i" => "caret-right", "text" => g_l('javaMenu_global', '[next]')],
				]
			);
		}
		?>
		<div>
			<div id="home" class="navigation" onclick="top.we_cmd('start_multi_editor');"><i class="fa fa-home" title="<?= g_l('javaMenu_global', '[home]'); ?>"></i></div>
			<div id="weMainMenu">
				<?php
				if($jmenu){
					echo $jmenu->getHTML();
				}
				?>
			</div>
			<div id="navigationHistory">
				<?php
				if($navigationButtons){
					foreach($navigationButtons as $button){
						echo '<div class="navigation" onclick="' . $button['onclick'] . '"><i class="fa fa-' . $button['i'] . '" title="' . $button['text'] . '"></i></div>';
					}
				}
				?></div>
			<div id="weHeaderRight"><?php
				if(($versionInfo = updateAvailable())){
					?>
					<div id="newUpdate" class="navigation">
						<i class="fa fa-lg fa-exclamation-circle" title="<?php printf(g_l('sysinfo', '[newWEAvailable]'), $versionInfo['dotted'] . ' (svn ' . $versionInfo['svnrevision'] . ')', $versionInfo['date']); ?>"></i>
					</div>
					<?php
				}
				if($msg){
					?>
					<div id="msgheadertable"><?php we_messaging_headerMsg::pbody(); ?></div><?php
				}

				echo self::createMessageConsole('mainWindow', false);
//				<img src="<php echo IMAGE_DIR >/webedition.svg" alt="" id="weHeaderLogo"/>
				?>
				<div id="logout" class="navigation" onclick="top.we_cmd('dologout');"><i class="fa fa-power-off fa-lg"></i></div>
			</div>
		</div>
		<?php
	}

}
