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
		return we_html_element::jsScript(JS_DIR . 'messageConsoleView.js') .
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
<div id="messageConsole">
<table>
	<tr>
		<td valign="middle">
	<div class="small messageConsoleMessage" id="messageConsoleMessage' . $consoleName . '">--</div>
		</td>
		<td>
	<div onclick="_console_' . $consoleName . '.openMessageConsole();" class="navigation" id="messageConsoleImageDiv"><i id="messageConsoleImage' . $consoleName . '" class="fa fa-lg fa-info"></i></div>
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

	public static function getJsForCssMenu(){
		return JS_DIR . 'menu/clickMenu.js';
	}

	static function pJS(){
		$jmenu = self::getMenu();

		echo we_html_element::jsScript(JS_DIR . 'images.js') .
		($jmenu ? $jmenu->getJS() : '');
		we_html_element::jsElement('
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
		ksort($we_menu);
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
					"i" => "close",
					"text" => g_l('javaMenu_global', '[close]')
				);
			}
			$navigationButtons = array_merge($navigationButtons, array(
				array("onclick" => "top.we_cmd('start_multi_editor');", 'i' => 'home', "text" => g_l('javaMenu_global', '[home]')),
				array("onclick" => "top.weNavigationHistory.navigateReload();", "i" => "refresh", "text" => g_l('javaMenu_global', '[reload]')),
				array("onclick" => "top.weNavigationHistory.navigateBack();", "i" => "caret-left", "text" => g_l('javaMenu_global', '[back]')),
				array("onclick" => "top.weNavigationHistory.navigateNext();", "i" => "caret-right", "text" => g_l('javaMenu_global', '[next]')),
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
			<div id="navigationHistory">
				<?php
				if($navigationButtons){
					foreach($navigationButtons as $button){
						echo '<div class="navigation" onclick="' . $button['onclick'] . '"><i class="fa fa-' . $button['i'] . '" title="' . $button['text'] . '"></i></div>';
					}
				}
				?></div>
			<div style="position:absolute;top:0px;bottom:0px;right:10px;border:0px;" ><?php
				echo self::createMessageConsole('mainWindow');
				?>
				<img src="<?php echo IMAGE_DIR ?>pixel.gif" alt="" name="busy" width="20" height="19"/>
				<img src="<?php echo IMAGE_DIR ?>/webedition.svg" alt="" id="weHeaderLogo"/>
			</div>
		</div>
		<?php
	}

}
