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
	' . $this->lcmdFrame . '.location.replace("' . WEBEDITION_DIR . 'we_lcmd.php?we_cmd[0]="+cmd);
}');
	}

	public function getHTML(){
		$out = '<span class="preload1"></span><span class="preload2"></span><span class="preload3"></span><span class="preload4"></span>' .
			'<ul id="nav">';
		$menus = array();
		foreach($this->entries as $id => $e){
			if(empty($e['parent'])){
				if(isset($e['perm']) ? self::isEnabled($e['perm']) : 1){
					$mtext = (is_array($e['text']) ?
							($e['text'][$GLOBALS['WE_LANGUAGE']] ? : '') :
							($e['text'] ? : ''));

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
			if(isset($e['parent']) && $e['parent'] == $p){
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
		if(empty($nf)){
			return;
		}

		foreach($nf as $id => $e){
			$newAst = $zweig;
			$mtext = (isset($e['text']) && is_array($e['text']) ?
					($e['text'][$GLOBALS['WE_LANGUAGE']] ? : '') :
					(isset($e['text']) ? $e['text'] : ''));

			if(isset($e['hide']) && $e['hide']){

			} elseif((isset($e['perm']) ? self::isEnabled($e['perm']) : 1)){
				if((!(isset($e['cmd']) && $e['cmd'])) && $mtext){
					$opt .= '<li><a class="fly" href="#void">' . $mtext . '</a><ul>' . "\n";
					$this->h_pCODE($men, $opt, $id, $newAst);
					$opt .= '</ul></li>' . "\n";
				} else if($mtext){
					$opt .= '<li><a href="#void" onclick="' . $this->menuaction . 'menuaction(\'' . $e["cmd"] . '\')">' . $mtext . '</a></li>';
				} else {//separator
					$opt .= '<li class="disabled"></li>';
				}
			}
		}
	}

}
