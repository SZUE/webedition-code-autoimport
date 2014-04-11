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
 * @package    webEdition_javamenu
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
class we_base_menu{

	private $entries;
	private $lcmdFrame = '';
	private $menuaction = '';

	public function __construct($entries, $lcmdFrame = "top.load", $menuaction = 'parent.'){
		$this->menuaction = $menuaction;
		if($entries){
			$this->entries = $entries;
		}
		$this->lcmdFrame = $lcmdFrame;
	}

	public function getCode(){
		return $this->getJS() . $this->getHTML();
	}

	public function getJS(){
		return we_html_element::jsScript(JS_DIR . 'attachKeyListener.js') .
			we_html_element::jsElement('
function menuaction(cmd) {
	' . $this->lcmdFrame . '.location.replace("' . getServerUrl() . WEBEDITION_DIR . 'we_lcmd.php?we_cmd[0]="+cmd);
}

//WEEXT: menu integration
function menuactionExt(cmd) {
	top.WE.app.getController("Bridge").menuactionExt(cmd);
}');
	}

	public function getHTML(){
		$showAltMenu = (isset($_SESSION['weS']['weShowAltMenu']) && $_SESSION['weS']['weShowAltMenu']) || weRequest('bool', 'showAltMenu');
		$_SESSION['weS']['weShowAltMenu'] = $showAltMenu;

		$out = '<span class="preload1"></span><span class="preload2"></span><span class="preload3"></span><span class="preload4"></span>' .
			'<ul id="nav">';
		$menus = array();
		foreach($this->entries as $id => $e){
			if($e['parent'] == 0){
				if(isset($e['perm']) ? self::isEnabled($e['perm']) : 1){
					$mtext = (is_array($e['text']) ?
							($e['text'][$GLOBALS['WE_LANGUAGE']] ? $e['text'][$GLOBALS['WE_LANGUAGE']] : '') :
							($e['text'] ? $e['text'] : ''));

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

	private static function h_search($men, $p){
		$container = array();
		foreach($men as $id => $e){
			if($e['parent'] == $p){
				$container[$id] = $e;
			}
		}
		return $container;
	}

	public static function isEnabled($perm){
		if(!$perm){
			return true;
		}
		$enabled = 0;
		$or = explode('||', $perm);
		foreach($or as $v){
			$and = explode('&&', $v);
			$eand = 1;
			foreach($and as $val){
				$eand&=permissionhandler::hasPerm(trim($val));
			}
			$enabled|=$eand;
			if($enabled){
				return true;
			}
		}
		return $enabled;
	}

	private function h_pCODE($men, &$opt, $p, $zweig){
		$nf = self::h_search($men, $p);
		if(!empty($nf)){
			foreach($nf as $id => $e){
				$newAst = $zweig;
				$e['enabled'] = isset($e['perm']) ? self::isEnabled($e['perm']) : 1;
				$mtext = (isset($e['text']) && is_array($e['text']) ?
						($e['text'][$GLOBALS['WE_LANGUAGE']] ? $e['text'][$GLOBALS['WE_LANGUAGE']] : '') :
						(isset($e['text']) ? $e['text'] : ''));

				if(isset($e['hide']) && $e['hide']){

				} else {
					if((!(isset($e['cmd']) && $e['cmd'])) && $mtext){
						if($e['enabled'] == 1){
							$opt .= '<li><a class="fly" href="#void">' . $mtext . '</a><ul>' . "\n";
							$this->h_pCODE($men, $opt, $id, $newAst);
							$opt .= '</ul></li>' . "\n";
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

	//WEEXT: get menu as JS object
	//Important: menu items for ext menu are listed linear: ext menu builds the nestetd structure using parent-id's
	function getJsonData(){
		$menu = array();
		foreach($this->entries as $id => $e){
				$e['enabled'] = isset($e['perm']) ? self::isEnabled($e['perm']) : 1;
				$mtext = isset($e['text']) ? $e['text'] : '';
				$handler = isset($e['function']) ? $e['function'] : 'standardHandler';

				if(!(isset($e['hide']) && $e['hide'])){
					if((!(isset($e['cmd']) && $e['cmd'])) && $mtext){//no leaf
						$menu[] = array('menuId' => 'm_' . $id, 'name' => $mtext, 'parent' => 'm_' . $e['parent'], 'cmd' => '', 'itemHandler' => '');
					} else if($mtext){
						if($e['enabled'] != 0){
							$menu[] = array('menuId' => 'm_' . $id, 'name' => $mtext, 'parent' => 'm_' . $e['parent'], 'cmd' => $e["cmd"], 'itemHandler' => $handler);
						}
					} elseif($e['enabled'] != 0){//separator
						$menu[] = array('menuId' => 'm_' . $id, 'parent' => 'm_' . $e['parent'], 'name' => '');
					}
				}
			
		}

		return $menu;
	}

}
