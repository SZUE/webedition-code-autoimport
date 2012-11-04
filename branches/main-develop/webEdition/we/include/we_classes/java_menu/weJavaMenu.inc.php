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
class weJavaMenu{

	var $entries;
	var $lcmdFrame = "";
	var $width = 350;
	var $height = 30;
	var $menuaction = "";

	function __construct($entries, $lcmdFrame = "top.load", $menuaction = 'parent.'){
		$this->menuaction = $menuaction;
		if($entries){
			$this->entries = $entries;
			if(we_base_browserDetect::isGecko()){
				$_SESSION["menuentries"] = $this->entries;
			}
		} else if(isset($_SESSION["menuentries"])){
			$this->entries = $_SESSION["menuentries"];
			unset($_SESSION["menuentries"]);
		}
		$this->lcmdFrame = $lcmdFrame;
	}

	function printMenu(){

		print $this->getCode();
	}

	function getCode($old = true){
		return ($old ? $this->getJS() : '') . $this->getHTML($old);
	}

	function getJS(){
		return we_html_element::jsScript(JS_DIR . 'attachKeyListener.js') .
			we_html_element::jsElement('
				function menuaction(cmd) {
					' . $this->lcmdFrame . '.location.replace("' . getServerUrl() . '/webEdition/we_lcmd.php?we_cmd[0]="+cmd);
				}');
	}

	function getHTML($old = true){
		$showAltMenu = (isset($_SESSION['weS']['weShowAltMenu']) && $_SESSION['weS']['weShowAltMenu']) || (isset($_REQUEST["showAltMenu"]) && $_REQUEST["showAltMenu"]);
		$_SESSION['weS']['weShowAltMenu'] = $showAltMenu;
		// On Mozilla OSX, when the Java Menu is loaded, it is not possible to make any text input (java steels focus from input fields or e.g) so we dont show the applet.
		if(!$old){
			$out = '<span class="preload1"></span><span class="preload2"></span><span class="preload3"></span><span class="preload4"></span>' .
				'<ul id="nav">';
			$menus = array();

			foreach($this->entries as $id => $e){

				if($e["parent"] == "000000"){
					if(is_array($e["text"])){
						$mtext = ($e["text"][$GLOBALS["WE_LANGUAGE"]] ? $e["text"][$GLOBALS["WE_LANGUAGE"]] : "");
					} else{
						$mtext = ($e["text"] ? $e["text"] : "");
					}
					$menus[] = array('id' => $id,
						'code' => '<li class="top" onmouseover="topMenuHover(this)"><div class="top_div" onclick="topMenuClick(this)"><a href="#void" class="top_link"><span class="down">' . $mtext . '</span></a><ul class="sub">',
					);
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
		$out = '';
		if(!$showAltMenu){
			$out .= '
				<div id="divForSelectMenu"></div>
				<applet name="weJavaMenuApplet" code="menuapplet"  archive="JavaMenu.jar"  codebase="' . we_util_Sys_Server::getHostUri(LIB_DIR . 'we/ui/controls') . '" align="baseline" width="' . $this->width . '" height="' . $this->height . '" mayscript scriptable>
					<param name="phpext" value=".php"/>';
			$i = 0;
			foreach($this->entries as $id => $m){
				if(we_hasPerm('ADMINISTRATOR')){
					$m['enabled'] = 1;
				}
				if(!we_hasPerm('ADMINISTRATOR') && (isset($m["perm"]) && $m["perm"]) != ""){
					$set = array();
					$or = explode("||", $m["perm"]);
					foreach($or as $k => $v){
						$and = explode("&&", $v);
						$one = true;
						foreach($and as $key => $val){
							array_push($set, 'isset($_SESSION["perms"]["' . trim($val) . '"])');
							//$and[$key]='$_SESSION["perms"]["'.trim($val).'"]';
							$and[$key] = '(isset($_SESSION["perms"]["' . trim($val) . '"]) && $_SESSION["perms"]["' . trim($val) . '"])';
							$one = false;
						}
						$or[$k] = implode(" && ", $and);
						if($one && !in_array('isset($_SESSION["perms"]["' . trim($v) . '"])', $set))
							array_push($set, 'isset($_SESSION["perms"]["' . trim($v) . '"])');
					}
					$set_str = implode(" || ", $set);
					$condition_str = implode(" || ", $or);
					eval('if(' . $set_str . '){ if(' . $condition_str . ') $m["enabled"]=1; else $m["enabled"]=0;}');
				}
				if(isset($m["text"]) && is_array($m["text"])){
					$mtext = ($m["text"][$GLOBALS["WE_LANGUAGE"]] ? $m["text"][$GLOBALS["WE_LANGUAGE"]] : "#");
				} else{
					$mtext = (isset($m["text"]) ? $m["text"] : "#");
				}
				if(!isset($m["cmd"])){
					$m["cmd"] = "#";
				}
				if(isset($m["enabled"]) && $m["enabled"]){
					$out .= '<param name="entry' . $i . '" value="' . $id . ',' . $m["parent"] . ',' . $m["cmd"] . ',' . $mtext . ',' . ( (isset($m["enabled"]) && $m["enabled"] ) ? $m["enabled"] : "0") . '">';
				} else{
					$out .= '<param name="entry' . $i . '" value="' . $id . ',' . $m["parent"] . ',0,' . $mtext . ',0"/>';
				}
				$i++;
			}
		}

		$menus = array();

		$onCh = 'var si=this.selectedIndex;
			if(this.options[si].value) {
				menuaction(this.options[si].value);
			}
			this.selectedIndex=0;';
		$i = 0;
		foreach($this->entries as $id => $e){
			if($e["parent"] == "000000"){
				if(is_array($e["text"])){
					$mtext = ($e["text"][$GLOBALS["WE_LANGUAGE"]] ? $e["text"][$GLOBALS["WE_LANGUAGE"]] : "");
				} else{
					$mtext = ($e["text"] ? $e["text"] : "");
				}
				$menus[$i]["id"] = $id;
				$menus[$i]["code"] = '<select class="defaultfont" style="font-size: 9px;font-family:arial;" onChange="' . $onCh . '" size="1"><option value="">' . $mtext . "\n";
				$i++;
			}
		}

		$out .= '
			<div id="divWithSelectMenu">
			<table cellpadding="2" cellspacing="0" border="0">
				<tr>
					<td><form></td>';
		for($i = 0; $i < sizeof($menus); $i++){
			$foo = $menus[$i]["code"];
			$this->h_pOption($this->entries, $foo, $menus[$i]["id"], "");
			$foo .= "</select>\n";
			$out .= '<td>' . ((we_html_tools::getPixel(2, 3) . '<br>')) . $foo . '</td>' . (($i < (sizeof($menus) - 1)) ? '<td>&nbsp;&nbsp;</td>' : '');
		}
		$out .= '
					</tr>
				</table>
			</div>
			' . (we_base_browserDetect::isGecko() ? '
			<script type="text/javascript">

			// BUGFIX #1831,
			// Alternate txt does not work in firefox. Therefore, the select-menu is copied to another visible div ONLY in firefox
			// Only script elements work: look at https://bugzilla.mozilla.org/show_bug.cgi?id=60724 for details

			if ( !navigator.javaEnabled() ) {
				//document.getElementById("divForSelectMenu").innerHTML = document.getElementById("divWithSelectMenu").innerHTML;
			}
			</script>' : '' ) . '
			</form>';

		if(!$showAltMenu){
			$out .= '</applet>' . "\n";
		}
		return $out;
	}

	function h_search($men, $p){
		$container = array();
		foreach($men as $id => $e){
			if($e["parent"] == $p){
				$container[$id] = $e;
			}
		}
		return $container;
	}

	function h_pOption($men, &$opt, $p, $zweig){
		$nf = $this->h_search($men, $p);
		if(sizeof($nf)){
			foreach($nf as $id => $e){
				$newAst = $zweig;
				$e["enabled"] = 1;
				if(isset($e["perm"])){
					$set = array();
					$or = explode("||", $e["perm"]);
					foreach($or as $k => $v){
						$and = explode("&&", $v);
						$one = true;
						foreach($and as $key => $val){
							array_push($set, 'isset($_SESSION["perms"]["' . trim($val) . '"])');
							//$and[$key]='$_SESSION["perms"]["'.trim($val).'"]';
							$and[$key] = '(isset($_SESSION["perms"]["' . trim($val) . '"]) && $_SESSION["perms"]["' . trim($val) . '"])';
							$one = false;
						}
						$or[$k] = implode(" && ", $and);
						if($one && !in_array('isset($_SESSION["perms"]["' . trim($v) . '"])', $set))
							array_push($set, 'isset($_SESSION["perms"]["' . trim($v) . '"])');
					}
					$set_str = implode(" || ", $set);
					$condition_str = implode(" || ", $or);
					eval('if(' . $set_str . '){ if(' . $condition_str . ') $e["enabled"]=1; else $e["enabled"]=0;}');
				}
				if(isset($e["text"]) && is_array($e["text"])){
					$mtext = ($e["text"][$GLOBALS["WE_LANGUAGE"]] ? $e["text"][$GLOBALS["WE_LANGUAGE"]] : "");
				} else{
					$mtext = ( isset($e["text"]) ? $e["text"] : "");
				}
				if((!isset($e["cmd"])) && $mtext){
					$opt .= '<option value="" disabled>&nbsp;&nbsp;' . $newAst . $mtext . "&nbsp;&gt;\n";
					$newAst = $newAst . "&nbsp;&nbsp;";
					$this->h_pOption($men, $opt, $id, $newAst);
				} else if($mtext){
					$opt .= '<option' . (($e["enabled"] == 0) ? (' value="" style="{color:\'grey\'}" disabled') : (' value="' . $e["cmd"] . '"')) . '>&nbsp;&nbsp;' . $newAst . $mtext;
				} else{
					$opt .= '<option value="" disabled>&nbsp;&nbsp;' . $newAst . "--------\n";
				}
			}
		}
	}

	function h_pCODE($men, &$opt, $p, $zweig){
		$nf = $this->h_search($men, $p);
		if(sizeof($nf)){
			foreach($nf as $id => $e){
				$newAst = $zweig;
				$e["enabled"] = 1;
				if(isset($e["perm"])){
					$set = array();
					$or = explode("||", $e["perm"]);
					foreach($or as $k => $v){
						$and = explode("&&", $v);
						$one = true;
						foreach($and as $key => $val){
							array_push($set, 'isset($_SESSION["perms"]["' . trim($val) . '"])');
							//$and[$key]='$_SESSION["perms"]["'.trim($val).'"]';
							$and[$key] = '(isset($_SESSION["perms"]["' . trim($val) . '"]) && $_SESSION["perms"]["' . trim($val) . '"])';
							$one = false;
						}
						$or[$k] = implode(" && ", $and);
						if($one && !in_array('isset($_SESSION["perms"]["' . trim($v) . '"])', $set))
							array_push($set, 'isset($_SESSION["perms"]["' . trim($v) . '"])');
					}
					$set_str = implode(" || ", $set);
					$condition_str = implode(" || ", $or);
					eval('if(' . $set_str . '){ if(' . $condition_str . ') $e["enabled"]=1; else $e["enabled"]=0;}');
				}
				if(isset($e["text"]) && is_array($e["text"])){
					$mtext = ($e["text"][$GLOBALS["WE_LANGUAGE"]] ? $e["text"][$GLOBALS["WE_LANGUAGE"]] : "");
				} else{
					$mtext = ( isset($e["text"]) ? $e["text"] : "");
				}
				if((!isset($e["cmd"])) && $mtext){
					$opt .= '<li><a class="fly" href="#void">' . $mtext . '</a><ul>' . "\n";
					$this->h_pCODE($men, $opt, $id, $newAst);
					$opt .= '</ul></li>' . "\n";
				} else if($mtext){
					if(!(isset($e["enabled"]) && $e["enabled"] == 0)){
						$opt .= '<li><a href="#void" onclick="' . $this->menuaction . 'menuaction(\'' . $e["cmd"] . '\')">' . $mtext . '</a></li>';
					}
				} else{
					$opt .= '<li class="disabled"></li>';
				}
			}
		}
	}

}
