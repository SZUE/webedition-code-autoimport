<?php
/**
 * webEdition SDK
 *
 * $Rev$
 * $Author$
 * $Date$
 *
 * This source is part of the webEdition SDK. The webEdition SDK is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU Lesser General Public License can be found at
 * http://www.gnu.org/licenses/lgpl-3.0.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionSDK/License.txt
 *
 *
 * @category   we
 * @package none
 * @subpackage we_ui_controls
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */

/**
 * Class to display a JavaMenu
 *
 * @category   we
 * @package none
 * @subpackage we_ui_controls
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
class we_ui_controls_CssMenu extends we_ui_abstract_AbstractElement{
	var $entries;
	var $lcmdFrame = "";
	var $width = 350;
	var $height = 31;
	var $menuaction = "";

	function __construct($entries, $lcmdFrame = "top.load", $menuaction = 'parent.'){
		parent::__construct();
		$this->menuaction = $menuaction;
		if($entries){
			$this->entries = $entries;
			if(we_base_browserDetect::isGecko()){
				$_SESSION['weS']['menuentries'] = $this->entries;
			}
		} else if(isset($_SESSION['weS']['menuentries'])){
			$this->entries = $_SESSION['weS']['menuentries'];
			unset($_SESSION['weS']['menuentries']);
		}
		$this->lcmdFrame = $lcmdFrame;
	}

	protected function _renderHTML(){
		return $this->getJS() . $this->getHTMLMenu(false);
	}

	function getJS(){
		return we_html_element::jsElement('
function menuaction(cmd) {
	weCmdController.fire({cmdName: cmd})
}');
	}

	function getHTMLMenu($old = true){
		$out = '<ul id="nav">';
		$menus = [];

		foreach($this->entries as $id => $e){
			if($e['parent'] == 0){
				if(isset($e['perm']) ? we_base_menu::isEnabled($e['perm']) : 1){
					if(is_array($e["text"])){
						$mtext = ($e["text"][$GLOBALS["WE_LANGUAGE"]] ? : '');
					} else {
						$mtext = ($e["text"] ? : "");
					}
					$menus[] = array(
						'id' => $id,
						'code' => '<li class="top" onmouseover="topMenuHover(this)"><div class="top_div" onclick="topMenuClick(this)"><a href="#void" class="top_link"><span class="down">' . $mtext . '</span></a><ul class="sub">',
					);
				}
			}
		}
		foreach($menus as $menu){
			$foo = $menu['code'];
			$this->h_pCODE($this->entries, $foo, $menu['id'], '');
			$foo .= '</ul></div></li>';
			$out .= $foo;
		}

		$out .= '</ul>';
		return $out;
	}

	function h_search($men, $p){
		$container = [];
		foreach($men as $id => $e){
			if($e["parent"] == $p){
				$container[$id] = $e;
			}
		}
		return $container;
	}

	private function h_pCODE($men, &$opt, $p, $zweig){
		$nf = self::h_search($men, $p);
		if(!empty($nf)){
			foreach($nf as $id => $e){
				$newAst = $zweig;
				$e['enabled'] = isset($e['perm']) ? we_base_menu::isEnabled($e['perm']) : 1;
				$mtext = (isset($e['text']) && is_array($e['text']) ?
						($e['text'][$GLOBALS['WE_LANGUAGE']] ? : '') :
						(isset($e['text']) ? $e['text'] : ''));

				if(!empty($e['hide'])){

				} else {
					if((!(isset($e['cmd']) && $e['cmd'])) && $mtext){
						if($e['enabled'] == 1){
							$opt .= '<li><a class="fly" href="#void">' . $mtext . '<i class="fa fa-caret-right"></i></a><ul>';
							$this->h_pCODE($men, $opt, $id, $newAst);
							$opt .= '</ul></li>';
						}
					} else if($mtext){
						if($e['enabled'] == 1){
							$opt .= '<li><a href="#void" onclick="' . $this->menuaction . 'menuaction(\'' . $e["cmd"] . '\')">' . $mtext . '</a></li>';
						}
					} elseif($e['enabled'] == 1){//separator
						$opt .= '<li class="disabled"></li>';
					}
				}
			}
		}
	}

}
