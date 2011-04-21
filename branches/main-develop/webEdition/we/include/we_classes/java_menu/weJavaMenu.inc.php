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


include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_browser_check.inc.php");

class weJavaMenu {
	var $entries;
	var $lcmdFrame = "";
	var $protocol = "http";
	var $SERVER_NAME = "";
	var $width = 200;
	var $height = 30;
	var $port = "";
	var $prename = "";

	function weJavaMenu($entries, $SERVER_NAME, $lcmdFrame="top.load", $protocol="http", $port="", $width=200, $height=30, $prename="") {
		$this->prename = $prename;
		if($entries){
			$this->entries = $entries;
			if($GLOBALS["BROWSER"] == "NN6"){
				$_SESSION[$prename."menuentries"] = $this->entries;
			}
		}else if(isset($_SESSION[$prename."menuentries"])){
			$this->entries = $_SESSION[$prename."menuentries"];
			unset($_SESSION[$prename."menuentries"]);
		}
		$this->SERVER_NAME = $SERVER_NAME;
		$this->lcmdFrame = $lcmdFrame;
		$this->protocol = $protocol;
		$this->width = $width;
		$this->height = $height;
		$this->port = $port;
	}

	function printMenu() {
		print $this->getCode();
	}

	function getCode() {
		return $this->getJS() .$this->getHTML();
	}

	function getJS() {
		$portVar = (
			($this->port==80 && $this->protocol=="http") ||
			($this->port==443 && $this->protocol=="https") ||
			(!$this->port)
		) ? "" : ":".$this->port;
		return '
			<script type="text/javascript" src="' . JS_DIR . 'attachKeyListener.js"></script>
			<script  type="text/javascript"><!--
				function menuaction(cmd) {
					'.$this->lcmdFrame.'.location.replace("'.$this->protocol.'://'.$this->SERVER_NAME.$portVar.'/webEdition/we_lcmd.php?we_cmd[0]="+cmd);
				}
			//-->
			</script>';
	}


	function getHTML() {
		$showAltMenu = 'JS';//(isset($_SESSION['weShowAltMenu']) && $_SESSION['weShowAltMenu']) || (isset($_REQUEST["showAltMenu"]) && $_REQUEST["showAltMenu"]);
		$_SESSION['weShowAltMenu'] = $showAltMenu;
		// On Mozilla OSX, when the Java Menu is loaded, it is not possible to make any text input (java steels focus from input fields or e.g) so we dont show the applet.

		$out = '<ul id="nav">';
		$menus = array();

		$i=0;
		foreach ($this->entries as $id=>$e) {

			if ($e["parent"] == "000000") {
				if(is_array($e["text"])) {
					$mtext = ($e["text"][$GLOBALS["WE_LANGUAGE"]] ? $e["text"][$GLOBALS["WE_LANGUAGE"]] : "");
				}
				else {
					$mtext = ($e["text"] ? $e["text"] : "");
				}
				$menus[$i]["id"] = $id;
				$menus[$i]["code"] = '<li class="top"><a href="#void" class="top_link"><span class="down">'.$mtext.'</span></a><ul class="sub">'."\n";
				$i++;
			}
		}

		for ($i=0;$i<sizeof($menus);$i++) {
			$foo = $menus[$i]["code"];
			$this->h_pCODE($this->entries,$foo,$menus[$i]["id"],"");
			$foo .= "</ul></li>\n";
			$out .= $foo;
		}

  $out .=    '</ul>';
		return $out;




		return '<ul id="nav">
	<li class="top"><a href="#nogo1" class="top_link"><span>Home</span></a></li>
	<li class="top"><a href="#nogo2" id="products" class="top_link"><span class="down">Products</span></a>
		<ul class="sub">
			<li><a href="#nogo3" class="fly">Cameras</a>
					<ul>
						<li><a href="#nogo4" onclick="menuaction(\'new_text_xml\');">Nikon</a></li>
						<li><a href="#nogo5">Minolta</a></li>
						<li><a href="#nogo6">Pentax</a></li>
					</ul>
			</li>
			<li class="mid"><a href="#nogo7" class="fly">Lenses</a>
					<ul>
						<li><a href="#nogo8">Wide Angle</a></li>
						<li><a href="#nogo9">Standard</a></li>
						<li><a href="#nogo10">Telephoto</a></li>
						<li><a href="#nogo11" class="fly">Zoom</a>
							<ul>
								<li><a href="#nogo12">35mm to 125mm</a></li>
								<li><a href="#nogo13">50mm to 250mm</a></li>
								<li><a href="#nogo14">125mm to 500mm</a></li>
							</ul>
						</li>
						<li><a href="#nogo15">Mirror</a></li>
						<li><a href="#nogo16" class="fly">Non standard</a>
							<ul>
								<li><a href="#nogo17">Bayonet mount</a></li>
								<li><a href="#nogo18">Screw mount</a></li>
							</ul>
						</li>
					</ul>
			</li>
			<li><a href="#nogo19">Flash Guns</a></li>
			<li><a href="#nogo20">Tripods</a></li>
			<li><a href="#nogo21">Filters</a></li>
		</ul>
	</li>
	<li class="top"><a href="#nogo22" id="services" class="top_link"><span class="down">Services</span></a>
		<ul class="sub">
			<li><a href="#nogo23">Printing</a></li>
			<li><a href="#nogo24">Photo Framing</a></li>
			<li><a href="#nogo25">Retouching</a></li>
			<li><a href="#nogo26">Archiving</a></li>
		</ul>
	</li>
	<li class="top"><a href="#nogo27" id="contacts" class="top_link"><span class="down">Contacts</span></a>
		<ul class="sub">
			<li><a href="#nogo28">Support</a></li>
			<li><a href="#nogo29" class="fly">Sales</a>
				<ul>
					<li><a href="#nogo30">USA</a></li>
					<li><a href="#nogo31">Canadian</a></li>
					<li><a href="#nogo32">South American</a></li>
					<li><a href="#nogo33" class="fly">European</a>
						<ul>
							<li><a href="#nogo34" class="fly">British</a>
								<ul>
									<li><a href="#nogo35">London</a></li>
									<li><a href="#nogo36">Liverpool</a></li>
									<li><a href="#nogo37">Glasgow</a></li>
									<li><a href="#nogo38" class="fly">Bristol</a>
										<ul>
											<li><a href="#nogo39">Redland</a></li>
											<li><a href="#nogo40">Hanham</a></li>
											<li><a href="#nogo41">Eastville</a></li>
										</ul>
									</li>
									<li><a href="#nogo42">Cardiff</a></li>
									<li><a href="#nogo43">Belfast</a></li>
								</ul>
							</li>
							<li><a href="#nogo44">French</a></li>
							<li><a href="#nogo45">German</a></li>
							<li><a href="#nogo46">Spanish</a></li>
						</ul>
					</li>
					<li><a href="#nogo47">Australian</a></li>
					<li><a href="#nogo48">Asian</a></li>
				</ul>
			</li>
			<li><a href="#nogo49">Buying</a></li>
			<li><a href="#nogo50">Photographers</a></li>
			<li><a href="#nogo51">Stockist</a></li>
			<li><a href="#nogo52">General</a></li>
		</ul>
	</li>
	<li class="top"><a href="#nogo53" id="shop" class="top_link"><span class="down">Shop</span></a>
		<ul class="sub">
			<li><a href="#nogo54">Online</a></li>
			<li><a href="#nogo55">Catalogue</a></li>
			<li><a href="#nogo56">Mail Order</a></li>
		</ul>
	</li>
	<li class="top"><a href="#nogo57" id="privacy" class="top_link"><span>Privacy Policy</span></a></li>
</ul>

	';

		$out = '';
		if(!$showAltMenu) {
			$out .= '
				<div id="divForSelectMenu"></div>
				<applet name="weJavaMenuApplet" code="menuapplet"  archive="JavaMenu.jar"  codebase="' . we_util_Sys_Server::getHostUri('/webEdition/lib/we/ui/controls') . '" align="baseline" width="' . $this->width . '" height="' . $this->height . '" mayscript scriptable>
					<param name="phpext" value=".php"/>';
			$i=0;
			foreach ($this->entries as $id=>$m) {
				if(we_hasPerm('ADMINISTRATOR')) {
					$m['enabled'] = 1;
				}
				if (!we_hasPerm('ADMINISTRATOR') && (isset($m["perm"]) && $m["perm"]) != "") {
					$set=array();
					$or=explode("||",$m["perm"]);
					foreach ($or as $k=>$v) {
						$and=explode("&&",$v);
						$one=true;
						foreach($and as $key=>$val) {
							array_push($set,'isset($_SESSION["perms"]["'.trim($val).'"])');
							//$and[$key]='$_SESSION["perms"]["'.trim($val).'"]';
							$and[$key]='(isset($_SESSION["perms"]["'.trim($val).'"]) && $_SESSION["perms"]["'.trim($val).'"])';
							$one=false;
						}
						$or[$k]=implode(" && ",$and);
						if($one && !in_array('isset($_SESSION["perms"]["'.trim($v).'"])',$set))
							array_push($set,'isset($_SESSION["perms"]["'.trim($v).'"])');
					}
					$set_str=implode(" || ",$set);
					$condition_str=implode(" || ",$or);
					eval('if('.$set_str.'){ if('.$condition_str.') $m["enabled"]=1; else $m["enabled"]=0;}');
				}
				if (isset($m["text"]) && is_array($m["text"])) {
					$mtext = ($m["text"][$GLOBALS["WE_LANGUAGE"]] ? $m["text"][$GLOBALS["WE_LANGUAGE"]] : "#");
				}else{
					$mtext = (isset($m["text"]) ? $m["text"] : "#");
				}
				if (!isset($m["cmd"])) {
					$m["cmd"] = "#";
				}
				$out .= "\n" . '				<param name="entry'.$i.'" value="'.$id.','.$m["parent"].','.$m["cmd"].','.$mtext.','.( (isset($m["enabled"]) && $m["enabled"] ) ? $m["enabled"] : "0").'">'."\n";
				$i++;
			}
		}

		$menus = array();

		$onCh = '
			var si=this.selectedIndex;
			if(this.options[si].value) {
				menuaction(this.options[si].value);
			}
			this.selectedIndex=0;';
		$i=0;
		foreach ($this->entries as $id=>$e) {
			if ($e["parent"] == "000000") {
				if(is_array($e["text"])) {
					$mtext = ($e["text"][$GLOBALS["WE_LANGUAGE"]] ? $e["text"][$GLOBALS["WE_LANGUAGE"]] : "");
				}
				else {
					$mtext = ($e["text"] ? $e["text"] : "");
				}
				$menus[$i]["id"] = $id;
				$menus[$i]["code"] = '<select class="defaultfont" style="font-size: 9px;font-family:arial;" onChange="'.$onCh.'" size="1"><option value="">'.$mtext."\n";
				$i++;
			}
		}

		$out .= '
			<div id="divWithSelectMenu">
			<table cellpadding="2" cellspacing="0" border="0">
				<tr>
					<td><form></td>';
		for ($i=0;$i<sizeof($menus);$i++) {
			$foo = $menus[$i]["code"];
			$this->h_pOption($this->entries,$foo,$menus[$i]["id"],"");
			$foo .= "</select>\n";
			$out .= '<td>'.(($GLOBALS["BROWSER"]!="NN") ? (getPixel(2,3).'<br>') : '').$foo.'</td>'.(($i<(sizeof($menus)-1)) ? '<td>&nbsp;&nbsp;</td>' : '');
		}
		$out .= '
					</tr>
				</table>
			</div>
			' . ($GLOBALS["BROWSER"] == "NN6"
					? '
			<script type="text/javascript">

			// BUGFIX #1831,
			// Alternate txt does not work in firefox. Therefore, the select-menu is copied to another visible div ONLY in firefox
			// Only script elements work: look at https://bugzilla.mozilla.org/show_bug.cgi?id=60724 for details

			if ( !navigator.javaEnabled() ) {
				//document.getElementById("divForSelectMenu").innerHTML = document.getElementById("divWithSelectMenu").innerHTML;
			}
			</script>'
					: '' ) . '
			</form>';

		if(!$showAltMenu) {
			$out .= '</applet>'."\n";
		}
		return $out;
	}

	function h_search($men,$p) {
		$container = array();
		foreach($men as $id=>$e) {
			if($e["parent"] == $p) {
				$container[$id] =$e;
			}
		}
		return $container;
	}

	function h_pOption($men,&$opt,$p,$zweig) {
		$nf = $this->h_search($men,$p);
		if(sizeof($nf)) {
			foreach($nf as $id=>$e) {
				$newAst = $zweig;
				$e["enabled"]=1;
				if(isset($e["perm"])) {
					$set=array();
					$or=explode("||",$e["perm"]);
					foreach($or as $k=>$v) {
						$and=explode("&&",$v);
						$one=true;
						foreach($and as $key=>$val) {
							array_push($set,'isset($_SESSION["perms"]["'.trim($val).'"])');
							//$and[$key]='$_SESSION["perms"]["'.trim($val).'"]';
							$and[$key]='(isset($_SESSION["perms"]["'.trim($val).'"]) && $_SESSION["perms"]["'.trim($val).'"])';
							$one=false;
						}
						$or[$k]=implode(" && ",$and);
						if($one && !in_array('isset($_SESSION["perms"]["'.trim($v).'"])',$set))
							array_push($set,'isset($_SESSION["perms"]["'.trim($v).'"])');
					}
					$set_str=implode(" || ",$set);
					$condition_str=implode(" || ",$or);
					eval('if('.$set_str.'){ if('.$condition_str.') $e["enabled"]=1; else $e["enabled"]=0;}');
				}
				if( isset($e["text"]) && is_array($e["text"]) ) {
					$mtext = ($e["text"][$GLOBALS["WE_LANGUAGE"]] ? $e["text"][$GLOBALS["WE_LANGUAGE"]] : "");
				}
				else {
					$mtext = ( isset($e["text"]) ? $e["text"] : "");
				}
				if((!isset($e["cmd"])) && $mtext) {
					$opt .=  '<option value="" disabled>&nbsp;&nbsp;'.$newAst."".$mtext."&nbsp;&gt;\n";
					$newAst = $newAst . "&nbsp;&nbsp;";
					$this->h_pOption($men,$opt,$id,$newAst);
				}
				else if($mtext) {
					$opt .=  '<option'.(($e["enabled"]==0) ? (' value="" style="{color:\'gray\'}" disabled') : (' value="'.$e["cmd"].'"')).'>&nbsp;&nbsp;'.$newAst.(($GLOBALS['BROWSER']=="NN" && $e["enabled"]==0) ? "(" : "").$mtext.(($GLOBALS['BROWSER']=="NN" && $e["enabled"]==0) ? ")" : "")."\n";
				}
				else {
					$opt .=  '<option value="" disabled>&nbsp;&nbsp;'.$newAst."--------\n";
				}
			}
		}
	}


		function h_pCODE($men,&$opt,$p,$zweig) {
		$nf = $this->h_search($men,$p);
		if(sizeof($nf)) {
			foreach($nf as $id=>$e) {
				$newAst = $zweig;
				$e["enabled"]=1;
				if(isset($e["perm"])) {
					$set=array();
					$or=explode("||",$e["perm"]);
					foreach($or as $k=>$v) {
						$and=explode("&&",$v);
						$one=true;
						foreach($and as $key=>$val) {
							array_push($set,'isset($_SESSION["perms"]["'.trim($val).'"])');
							//$and[$key]='$_SESSION["perms"]["'.trim($val).'"]';
							$and[$key]='(isset($_SESSION["perms"]["'.trim($val).'"]) && $_SESSION["perms"]["'.trim($val).'"])';
							$one=false;
						}
						$or[$k]=implode(" && ",$and);
						if($one && !in_array('isset($_SESSION["perms"]["'.trim($v).'"])',$set))
							array_push($set,'isset($_SESSION["perms"]["'.trim($v).'"])');
					}
					$set_str=implode(" || ",$set);
					$condition_str=implode(" || ",$or);
					eval('if('.$set_str.'){ if('.$condition_str.') $e["enabled"]=1; else $e["enabled"]=0;}');
				}
				if( isset($e["text"]) && is_array($e["text"]) ) {
					$mtext = ($e["text"][$GLOBALS["WE_LANGUAGE"]] ? $e["text"][$GLOBALS["WE_LANGUAGE"]] : "");
				}
				else {
					$mtext = ( isset($e["text"]) ? $e["text"] : "");
				}
				if((!isset($e["cmd"])) && $mtext) {
					$opt .= '<li><a class="fly" href="#void">'.$mtext.'</a><ul>'."\n";
					$this->h_pCODE($men,$opt,$id,$newAst);
					$opt .= '</ul></li>'."\n";
				}
				else if($mtext) {
					$opt .= '<li'.((isset($e["enabled"]) && $e["enabled"]==0) ? ' class="disabled" disabled="true"' : '').'><a href="#void" onclick="parent.menuaction(\''.$e["cmd"].'\')">'.$mtext.'</a></li>';
				}
				else {
					$opt .=  '<li class="disabled">----------------</li>';
				}
			}
		}
	}

}