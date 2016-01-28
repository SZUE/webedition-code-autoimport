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
		return we_html_element::jsScript(JS_DIR . 'messageConsoleImages.js') .
				we_html_element::jsScript(JS_DIR . 'messageConsoleView.js') .
				we_html_element::jsElement('
var _msgNotice  = "' . g_l('messageConsole', '[iconBar][notice]') . '";
var _msgWarning = "' . g_l('messageConsole', '[iconBar][warning]') . '";
var _msgError   = "' . g_l('messageConsole', '[iconBar][error]') . '";


var _console_' . $consoleName . ' = new messageConsoleView("' . $consoleName . '", this.window );
_console_' . $consoleName . '.register();

onunload=function() {
	_console_' . $consoleName . '.unregister();
}
') . '
<div style="position:relative;float:left;">
	<table>
	<tr>
		<td valign="middle">
		<span class="small" id="messageConsoleMessage' . $consoleName . '" style="display: none; background-color: white; border: 1px solid #cdcdcd; padding: 2px 4px 2px 4px; margin: 3px 10px 0 0;">
			--
		</span>
		</td>
		<td>
			<div onclick="_console_' . $consoleName . '.openMessageConsole();" class="navigation_normal" onmouseover="this.className=\'navigation_hover\'" onmouseout="this.className=\'navigation_normal\'"><img id="messageConsoleImage' . $consoleName . '" src="' . IMAGE_DIR . 'messageConsole/notice.gif" style="border: none; padding: 1px;" /></div>
		</td>
	</tr>
	</table>
</div>';
	}

	static function pCSS(){
		echo self::css();
	}

	static function css(){
		$ret = '';
		foreach(self::getCssForCssMenu() as $link){
			$ret .= we_html_element::cssLink($link);
		}
		$ret .= we_html_element::jsScript(self::getJsForCssMenu());

		return $ret;
	}

	static function getCssForCssMenu(){
		$arr = array(WEBEDITION_DIR . 'css/menu/pro_drop_1.css');
		switch(we_base_browserDetect::inst()->getBrowser()){
			case we_base_browserDetect::CHROME:
			case we_base_browserDetect::SAFARI:
				$arr[] = WEBEDITION_DIR . 'css/menu/pro_drop_safari.css';
				break;
		}
		if(we_base_browserDetect::inst()->isMAC()){
			$arr[] = WEBEDITION_DIR . 'css/menu/pro_drop_mac.css';
		}

		return $arr;
	}

	static function getJsForCssMenu(){
		if(we_base_browserDetect::isIE() && intval(we_base_browserDetect::inst()->getBrowserVersion()) < 9){
			return WEBEDITION_DIR . 'css/menu/clickMenu_IE8.js';
		}
		return WEBEDITION_DIR . 'css/menu/clickMenu.js';
	}

	static function pJS(){
		$jmenu = self::getMenu();

		echo we_html_element::jsScript(JS_DIR . 'images.js') .
		we_html_element::jsScript(JS_DIR . 'weSidebar.php') .
		($jmenu ? $jmenu->getJS() : '');
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

	static function getMenuReloadCode($location = 'top.opener.'){
		$menu = self::getMenu();
		$menu = str_replace("\n", '"+"', addslashes($menu->getHTML()));
		return $location . 'document.getElementById("nav").parentNode.innerHTML="' . $menu . '";';
	}

	static function getMenu(){
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

	static function pbody(){

// all available elements
		$jmenu = self::getMenu();
		$navigationButtons = array();

		if(!we_base_request::_(we_base_request::BOOL, 'SEEM_edit_include')){ // there is only a menu when not in seem_edit_include!
			if(isset($_SESSION['weS']['we_mode']) && $_SESSION['weS']['we_mode'] == we_base_constants::MODE_NORMAL){
// menu for normalmode
			} else if(permissionhandler::isUserAllowedForAction('header', 'with_java')){
// menu for seemode
			} else {
//  no menu in this case !
				$navigationButtons[] = array(
					"onclick" => "top.we_cmd('dologout');",
					"imagepath" => "/navigation/close.gif",
					"text" => g_l('javaMenu_global', '[close]')
				);
			}
			$navigationButtons = array_merge($navigationButtons, array(
				array("onclick" => "top.we_cmd('start_multi_editor');", "imagepath" => "/navigation/home.gif", "text" => g_l('javaMenu_global', '[home]')),
				array("onclick" => "top.weNavigationHistory.navigateReload();", "imagepath" => "/navigation/reload.gif", "text" => g_l('javaMenu_global', '[reload]')),
				array("onclick" => "top.weNavigationHistory.navigateBack();", "imagepath" => "/navigation/back.gif", "text" => g_l('javaMenu_global', '[back]')),
				array("onclick" => "top.weNavigationHistory.navigateNext();", "imagepath" => "/navigation/next.gif", "text" => g_l('javaMenu_global', '[next]')),
					)
			);
		}
		?>
		<div style="position:absolute;top:0px;left:0px;right:0px;bottom:0px;border:0px;">
			<div style="position:relative;border:0px;float:left;" >
				<?php
				if($jmenu){
					echo $jmenu->getCode();
				}
				?>
			</div>
			<div style="position:relative;bottom:0px;border:0px;padding-left: 10px;float:left;" >
				<?php
				if(!empty($navigationButtons)){
					foreach($navigationButtons as $button){
						echo '<div style = "float:left;margin-top:5px;" class = "navigation_normal" onclick = "' . $button['onclick'] . '" onmouseover = "this.className=\'navigation_hover\'" onmouseout = "this.className=\'navigation_normal\'"><img border = "0" hspace = "2" src = "' . IMAGE_DIR . $button['imagepath'] . '" width = "17" height = "18" alt = "' . $button['text'] . '" title = "' . $button['text'] . '"></div>';
					}
				}
				?></div>
			<div style="position:absolute;top:0px;bottom:0px;right:10px;border:0px;" >


				<?php
				echo self::createMessageConsole('mainWindow');
				?>
				<img src="<?php echo IMAGE_DIR ?>pixel.gif" alt="" name="busy" width="20" height="19">
				<img src="<?php echo IMAGE_DIR ?>webedition.gif" alt="" style="width:78px;height:25px;padding-left: 10px;padding-right: 5px;padding-top:3px;">
			</div>
		</div>
		<?php
	}

}
