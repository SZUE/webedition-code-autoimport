<?php

/**
 * webEdition CMS
 *
 * $Rev: 6072 $
 * $Author: lukasimhof $
 * $Date: 2013-04-30 15:32:40 +0200 (Di, 30 Apr 2013) $
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
class weShopFrames extends weModuleFrames {

	var $db;
	var $View;
	var $frameset;
	var $edit_cmd = "edit_newsletter";

	function __construct($frameset){

		parent::__construct(WE_SHOP_MODULE_DIR . "edit_shop_frameset.php");//FIXME: tmp!

		//$this->Tree = new weGlossaryTree();
		$this->View = new weShopView(WE_GLOSSARY_MODULE_DIR . "edit_shop_frameset.php", "top.content");
		
		$this->module = "shop";
		$this->treeDefaultWidth = 204;
	}
	
	function getHTML($what){

		switch($what){
			case "frameset": 
				print $this->getHTMLFrameset();
				break;
			/*
			case "header": print $bannerFrame->getHTMLHeader();
				break;
			 * 
			 */
			case "resize":
				print $this->getHTMLResize();
				break;
			/*
			case "left": 
			 *	print $this->getHTMLLeft();
				break;
			case "right": 
			 *	print $this->getHTMLRight();
				break;
			case "editor": 
			 *	print $this->getHTMLEditor();
				break;
			case "edheader": 
			 *	print $this->getHTMLEditorHeader($mode);
				break;
			case "edbody": 
			 *	print $this->getHTMLEditorBody();
				break;
			case "edfooter": 
			 *	print $this->getHTMLEditorFooter($mode);
				break;
			case "cmd": 
			 *	print $this->getHTMLCmd();
				break;
			 */
			default:
		}
	}

	function getJSCmdCode(){

		return $this->View->getJSTop_tmp();
		//. we_html_element::jsElement($this->Tree->getJSMakeNewEntry());
	}
	
	function getJSTreeCode(){ //TODO: use we_html_element::jsElement and move to new class weShopTree
		?>
		<script type="text/javascript"><!--
			var menuDaten = new container();var count = 0;var folder=0;
			var table="<?php print SHOP_TABLE; ?>";

			function drawEintraege(){
				fr = top.content.resize.shop_tree.window.document;//imi new adress
				fr.open();
				fr.writeln("<html><head>");
				fr.writeln("<script type=\"text/javascript\">");
				fr.writeln("clickCount=0;");
				fr.writeln("wasdblclick=0;");
				fr.writeln("tout=null");
				fr.writeln("function doClick(id,ct,table){");
				fr.writeln("top.content.resize.shop_properties.location='<?php print WE_SHOP_MODULE_DIR ?>edit_shop_editorFrameset.php?bid='+id;");
				fr.writeln("}");
				fr.writeln("function doFolderClick(id,ct,table){");
				fr.writeln("top.content.resize.shop_properties.location='<?php print WE_SHOP_MODULE_DIR; ?>edit_shop_editorFrameset.php?mid='+id;");
				fr.writeln("}");

				fr.writeln("function doYearClick(yearView){");
				fr.writeln("top.content.resize.shop_properties.location='<?php print WE_SHOP_MODULE_DIR; ?>edit_shop_editorFrameset.php?ViewYear='+yearView;");
				fr.writeln("}");

				fr.writeln("</"+"SCRIPT>");
				fr.writeln('<?php print STYLESHEET_SCRIPT; ?>');
				fr.write("</head>");
				fr.write("<BODY BGCOLOR=\"#F3F7FF\" LINK=\"#000000\" ALINK=\"#000000\" VLINK=\"#000000\" leftmargin=\"5\" topmargin=\"0\" marginheight=\"0\" marginwidth=\"5\">");
				fr.write("<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\"><tr><td class=\"tree\">\n<NOBR>\n");
				fr.write("<tr><td class=\"tree\"><NOBR><a href=javascript:// onClick=\"doYearClick("+ top.yearshop +");return true;\" title=\"Ums�tze des Gesch�ftsjahres\" ><?php print g_l('modules_shop', '[treeYear]'); ?>: <strong>" + top.yearshop + " </strong></a> <br/>");

				zeichne("0","");
				fr.write("</NOBR></td></tr></table>");
				fr.write("</BODY></html>");
				fr.close();
			}

			function zeichne(startEntry,zweigEintrag){
				var nf = search(startEntry);
				var ai = 1;
				while (ai <= nf.laenge) {
					fr.write(zweigEintrag);
					if (nf[ai].typ == 'shop') {
						if(ai == nf.laenge) fr.write("&nbsp;&nbsp;<IMG SRC=<?php print TREE_IMAGE_DIR; ?>kreuzungend.gif WIDTH=19 HEIGHT=18 align=absmiddle BORDER=0>");
						else fr.write("&nbsp;&nbsp;<IMG SRC=<?php print TREE_IMAGE_DIR; ?>kreuzung.gif WIDTH=19 HEIGHT=18 align=absmiddle BORDER=0>");
		<?php if(we_hasPerm("EDIT_SHOP_ORDER")){ ?> // make  in tree clickable
						if(nf[ai].name != -1){
							fr.write("<a href=\"javascript://\" onClick=\"doClick("+nf[ai].name+",'"+nf[ai].contentType+"','"+nf[ai].table+"');return true;\" BORDER=0>");
						}
		<?php } ?>
						fr.write("<IMG SRC=<?php print TREE_IMAGE_DIR; ?>icons/"+nf[ai].icon+" WIDTH=16 HEIGHT=18 align=absmiddle BORDER=0 title=\"<?php print g_l('tree', "[edit_statustext]"); ?>\">");
		<?php if(we_hasPerm("EDIT_SHOP_ORDER")){ ?>
						fr.write("</a>");
		<?php } ?>
						fr.write("&nbsp;");
		<?php if(we_hasPerm("EDIT_SHOP_ORDER")){ ?> // make orders in tree clickable
						fr.write("<a href=\"javascript://\" onClick=\"doClick("+nf[ai].name+",'"+nf[ai].contentType+"','"+nf[ai].table+"');return true;\">");

		<?php } ?>
						//changed for #6786
						fr.write("<span style='"+nf[ai].st+"'>"+ nf[ai].text+"</span>");
		<?php if(we_hasPerm("EDIT_SHOP_ORDER")){ ?>
							fr.write("</A>");
		<?php } ?>
						fr.write("&nbsp;&nbsp;<BR>\n");
					}else{
						var newAst = zweigEintrag;

						var zusatz = (ai == nf.laenge) ? "end" : "";

						if (nf[ai].offen == 0){
							fr.write("&nbsp;&nbsp;<A HREF=\"javascript:top.content.openClose('" + nf[ai].name + "',1)\" BORDER=0><IMG SRC=<?php print TREE_IMAGE_DIR; ?>auf"+zusatz+".gif WIDTH=19 HEIGHT=18 align=absmiddle BORDER=0 title=\"<?php print g_l('tree', "[open_statustext]") ?>\"></A>");
							var zusatz2 = "";
						}else{
							fr.write("&nbsp;&nbsp;<A HREF=\"javascript:top.content.openClose('" + nf[ai].name + "',0)\" BORDER=0><IMG SRC=<?php print TREE_IMAGE_DIR; ?>zu"+zusatz+".gif WIDTH=19 HEIGHT=18 align=absmiddle BORDER=0 title=\"<?php print g_l('tree', "[close_statustext]") ?>\"></A>");
							var zusatz2 = "open";
						}
		<?php if(we_hasPerm("EDIT_SHOP_ORDER")){ ?>
						fr.write("<a href=\"javascript://\" onClick=\"doFolderClick("+nf[ai].name+",'"+nf[ai].contentType+"','"+nf[ai].table+"');return true;\" BORDER=0>");
		<?php } ?>
						fr.write("<IMG SRC=<?php print TREE_IMAGE_DIR; ?>icons/folder"+zusatz2+".gif WIDTH=16 HEIGHT=18 align=absmiddle BORDER=0 title=\"<?php print g_l('tree', "[edit_statustext]"); ?>\">");
		<?php if(we_hasPerm("EDIT_SHOP_ORDER")){ ?>
						fr.write("</a>");
		<?php }
		if(we_hasPerm("EDIT_SHOP_ORDER")){
		?> // make the month in tree clickable
						fr.write("<A HREF=\"javascript://\" onClick=\"doFolderClick("+nf[ai].name+",'"+nf[ai].contentType+"','"+nf[ai].table+"');return true;\">");
		<?php } ?>
						fr.write("&nbsp;"+(parseInt(nf[ai].published) ? " <b>" : "") + nf[ai].text +(parseInt(nf[ai].published) ? " </b>" : ""));
		<?php if(we_hasPerm("EDIT_SHOP_ORDER")){ ?>
						fr.write("</a>");
		<?php } ?>
						fr.write("&nbsp;&nbsp;<BR>\n");
						if (nf[ai].offen){
							if(ai == nf.laenge) newAst = newAst + "<IMG SRC=<?php print TREE_IMAGE_DIR; ?>leer.gif WIDTH=19 HEIGHT=18 align=absmiddle BORDER=0>";
							else newAst = newAst + "<IMG SRC=<?php print TREE_IMAGE_DIR; ?>strich2.gif WIDTH=19 HEIGHT=18 align=absmiddle BORDER=0>";
							zeichne(nf[ai].name,newAst);
						}
					}
					ai++;
				}
			}

			function makeNewEntry(icon,id,pid,txt,offen,ct,tab,pub){
				if(table == tab){
					if(menuDaten[indexOfEntry(pid)]){
						if(ct=="folder")
							menuDaten.addSort(new dirEntry(icon,id,pid,txt,offen,ct,tab));
						else
							menuDaten.addSort(new urlEntry(icon,id,pid,txt,ct,tab,pub));
						drawEintraege();
					}
				}
			}


			function updateEntry(id,text,pub){
				var ai = 1;
				while (ai <= menuDaten.laenge) {
					if ((menuDaten[ai].typ=='folder') || (menuDaten[ai].typ=='shop'))
						if (menuDaten[ai].name==id) {
							menuDaten[ai].text=text;
							menuDaten[ai].published=pub;
						}
					ai++;
				}
				drawEintraege();
			}

			function deleteEntry(id){
				var ai = 1;
				var ind=0;
				while (ai <= menuDaten.laenge) {
					if ((menuDaten[ai].typ=='folder') || (menuDaten[ai].typ=='shop'))
						if (menuDaten[ai].name==id) {
							ind=ai;
							break;
						}
					ai++;
				}
				if(ind!=0){
					ai = ind;
					while (ai <= menuDaten.laenge-1) {
						menuDaten[ai]=menuDaten[ai+1];
						ai++;
					}
					menuDaten.laenge[menuDaten.laenge]=null;
					menuDaten.laenge--;
					drawEintraege();
				}
			}

			function openClose(name,status){
				var eintragsIndex = indexOfEntry(name);
				menuDaten[eintragsIndex].offen = status;
				if(status){
					if(!menuDaten[eintragsIndex].loaded){
						drawEintraege();
					}else{
						drawEintraege();
					}
				}else{
					drawEintraege();
				}
			}

			function indexOfEntry(name){var ai = 1;while (ai <= menuDaten.laenge) {if ((menuDaten[ai].typ == 'root') || (menuDaten[ai].typ == 'folder'))if (menuDaten[ai].name == name) return ai;ai++;}return -1;}

			function search(eintrag){var nf = new container();var ai = 1;while (ai <= menuDaten.laenge) {if ((menuDaten[ai].typ == 'folder') || (menuDaten[ai].typ == 'shop'))if (menuDaten[ai].vorfahr == eintrag) nf.add(menuDaten[ai]);ai++;}return nf;}

			function container(){this.laenge = 0;this.clear=containerClear;this.add = add;this.addSort = addSort;return this;}

			function add(object){this.laenge++;this[this.laenge] = object;}

			function containerClear(){this.laenge =0;}

			function addSort(object){this.laenge++;for(var i=this.laenge; i>0; i--){if(i > 1 && this[i-1].text.toLowerCase() > object.text.toLowerCase() ){this[i] = this[i-1];}else{this[i] = object;break;}}}

			function rootEntry(name,text,rootstat){this.name = name;this.text = text;this.loaded=true;this.typ = 'root';this.rootstat = rootstat;return this;}

			function dirEntry(icon,name,vorfahr,text,offen,contentType,table,published){this.icon=icon;this.name = name;this.vorfahr = vorfahr;this.text = text;this.typ = 'folder';this.offen = (offen ? 1 : 0);this.contentType = contentType;this.table = table;this.loaded = (offen ? 1 : 0);this.checked = false;this.published = published;return this;}

			//changed for #6786
			function urlEntry(icon,name,vorfahr,text,contentType,table,published,style){this.icon=icon;this.name = name;this.vorfahr = vorfahr; this.text = text;this.typ = 'shop';this.checked = false;this.contentType = contentType;this.table = table;this.published = published;this.st = style;return this;}

			function loadData(){

				menuDaten.clear();
				menuDaten.add(new self.rootEntry('0','root','root'));


		<?php
		// echo "menuDaten.add(new dirEntry('folder.gif','aaaa',0, 'Article',0,'','',".(($k>0)?1:0)."));";

		$this->db->query("SELECT IntOrderID,DateShipping,DateConfirmation,DateCustomA,DateCustomB,DateCustomC,DateCustomD,DateCustomE,DatePayment,DateCustomF,DateCustomG,DateCancellation,DateCustomH,DateCustomI,DatecustomJ,DateFinished, DATE_FORMAT(DateOrder,'" . g_l('date', '[format][mysqlDate]') . "') as orddate, DATE_FORMAT(DateOrder,'%c%Y') as mdate FROM " . SHOP_TABLE . " GROUP BY IntOrderID ORDER BY IntID DESC");
		while($this->db->next_record()) {
			//added for #6786
			$style = "color:black;font-weight:bold;";

			if($this->db->f("DateCustomA") != '' || $this->db->f("DateCustomB") != '' || $this->db->f("DateCustomC") != '' || $this->db->f("DateCustomD") != '' || $this->db->f("DateCustomE") != '' || $this->db->f("DateCustomF") != '' || $this->db->f("DateCustomG") != '' || $this->db->f("DateCustomH") != '' || $this->db->f("DateCustomI") != '' || $this->db->f("DateCustomJ") != '' || $this->db->f("DateConfirmation") != '' || $this->db->f("DateShipping") != '0000-00-00 00:00:00'){
				$style = "color:red;";
			}

			if($this->db->f("DatePayment") != '0000-00-00 00:00:00'){
				$style = "color:#006699;";
			}

			if($this->db->f("DateCancellation") != '' || $this->db->f("DateFinished") != ''){
				$style = "color:black;";
			}


			print "  menuDaten.add(new urlEntry('" . we_base_ContentTypes::LINK_ICON . "','" . $this->db->f("IntOrderID") . "'," . $this->db->f("mdate") . ",'" . $this->db->f("IntOrderID") . ". " . g_l('modules_shop', '[bestellung]') . " " . $this->db->f("orddate") . "','shop','" . SHOP_TABLE . "','" . (($this->db->f("DateShipping") > 0) ? 0 : 1) . "','" . $style . "'));\n";
			if($this->db->f("DateShipping") <= 0){
				//FIXME: remove eval
				eval('if(isset($l' . $this->db->f("mdate") . ')) {$l' . $this->db->f("mdate") . '++;} else { $l' . $this->db->f("mdate") . ' = 1;}');
			}

			//FIXME: remove eval
			eval('if(isset($v' . $this->db->f("mdate") . ')) {$v' . $this->db->f("mdate") . '++;} else { $v' . $this->db->f("mdate") . ' = 1;}');
		}

		$year = (empty($_REQUEST["year"])) ? date("Y") : $_REQUEST["year"];
		//unset($_SESSION["year"]);
		for($f = 12; $f > 0; $f--){
			$r = (isset(${'v' . $f . $year}) ? ${'v' . $f . $year} : '');
			$k = (isset(${'l' . $f . $year}) ? ${'l' . $f . $year} : '');
			echo "menuDaten.add(new dirEntry('" . we_base_ContentTypes::FOLDER_ICON . "',$f+''+$year,0, '" . (($f < 10) ? "0" . $f : $f) . ' ' . g_l('modules_shop', '[sl]') . " " . g_l('date', '[month][long][' . ($f - 1) . ']') . " (" . (($k > 0) ? "<b>" . $k . "</b>" : 0) . "/" . (($r > 0) ? $r : 0) . ")',0,'',''," . (($k > 0) ? 1 : 0) . "));";
		} //'".$this->db->f("mdate")."'
		echo "top.yearshop = '$year';";
		?>

			}

			function start(){loadData();drawEintraege();}
			self.focus();
			//-->
		</script>
		<?php
	}

	function getHTMLFrameset(){
		print $this->getJSCmdCode() . 
			self::getJSToggleTreeCode($this->module, $this->treeDefaultWidth) . 
			$this->getJSTreeCode();

		print we_html_element::htmlBody(array('style' => 'background-color:grey;margin: 0px;position:fixed;top:0px;left:0px;right:0px;bottom:0px;border:0px none;', "onload" => "start();")
			, we_html_element::htmlDiv(array('style' => 'position:absolute;top:0px;bottom:0px;left:0px;right:0px;')
				, we_html_element::htmlExIFrame('shop_header', parent::getHTMLHeader(WE_INCLUDES_PATH .'java_menu/modules/module_menu_' . $this->module . '.inc.php', $this->module), 'position:absolute;top:0px;height:28px;left:0px;right:0px;') .
				we_html_element::htmlIFrame('shop_header_icons', WE_SHOP_MODULE_DIR . 'edit_shop_iconbarHeader.php', 'position:absolute;top:28px;height:38px;left:0px;right:0px;') .
				we_html_element::htmlIFrame('resize', $this->frameset . '?pnt=resize', 'position:absolute;top:66px;bottom:1px;left:0px;right:0px;overflow: hidden;') .
				we_html_element::htmlIFrame('cmd', WE_SHOP_MODULE_DIR . 'edit_shop_cmd.php', 'position:absolute;bottom:0px;height:1px;left:0px;right:0px;overflow: hidden;')
			));
		
		//'header', self::getHTMLHeader(WE_INCLUDES_PATH .'java_menu/modules/module_menu_' . $this->module . '.inc.php', $this->module
	}

	function getHTMLHeader(){


		?>
		</head>
		<body style="background-color:#bfbfbf; background-repeat:repeat;margin:0px 0px 0px 0px">
			HEADER
		</body>
		</html>
		<?php
		

	}

	function getHTMLResize(){//in use
		$_treewidth = 204;
		$incDecTree = '
			<img id="incBaum" src="' . BUTTONS_DIR . 'icons/function_plus.gif" width="9" height="12" style="position:absolute;bottom:53px;left:5px;border:1px solid grey;padding:0 1px;cursor: pointer; ' . ($_treewidth <= 100 ? 'bgcolor:grey;' : '') . '" onClick="incTree();">
			<img id="decBaum" src="' . BUTTONS_DIR . 'icons/function_minus.gif" width="9" height="12" style="position:absolute;bottom:33px;left:5px;border:1px solid grey;padding:0 1px;cursor: pointer; ' . ($_treewidth <= 100 ? 'bgcolor:grey;' : '') . '" onClick="decTree();">
			<img id="arrowImg" src="' . BUTTONS_DIR . 'icons/direction_' . ($_treewidth <= 100 ? 'right' : 'left') . '.gif" width="9" height="12" style="position:absolute;bottom:13px;left:5px;border:1px solid grey;padding:0 1px;cursor: pointer;" onClick="top.content.toggleTree();">
		';
	
		$editorPath = isset($_REQUEST['bid']) ? WE_SHOP_MODULE_DIR . 'edit_shop_editorFrameset.php?bid=' . $_REQUEST['bid'] : 
			WE_SHOP_MODULE_DIR . 'edit_shop_editorFramesetTop.php?home=1';

		print we_html_element::htmlBody(array('style' => 'background-color:#bfbfbf; background-repeat:repeat;margin:0px 0px 0px 0px'),
			we_html_element::htmlDiv(array('style' => 'position:absolute;top:0px;bottom:0px;left:0px;right:0px;'),
				we_html_element::htmlDiv(array('id' => 'lframeDiv','style' => 'position: absolute; top: 0px; bottom: 0px; left: 0px; right: 0px;'),
					we_html_element::htmlDiv(array('style' => 'position: absolute; top: 0px; bottom: 0px; left: 0px; right: 0px; width: 24px; background-image: url(/webEdition/images/v-tabs/background.gif); background-repeat: repeat-y; border-top: 1px solid black;'), $incDecTree) .
					we_html_element::htmlIFrame('shop_tree', HTML_DIR . 'white.html', 'position: absolute; top: 0px; bottom: 0px; left: 24px; right: 0px; verflow: hidden; border-top: 1px solid white')
				) .
				we_html_element::htmlIFrame('shop_properties', $editorPath, 'position: absolute; top: 0px; bottom: 0px; left: 204px; right: 0px; width:auto; border-left: 1px solid black; overflow: hidden;')
			)
		);
	}

	function getHTMLLeft(){
		?>
		</head>
		<frameset rows="1,*" framespacing="0" border="0" frameborder="NO">
			<frame src="<?php print HTML_DIR ?>whiteWithTopLine.html" scrolling="no" noresize>
			<frame src="<?php print HTML_DIR ?>white.html" name="tree" scrolling="auto" noresize>
		</frameset>
		<noframes>
			<body style="background-color:#bfbfbf; background-repeat:repeat;margin:0px 0px 0px 0px">
			</body>
		</noframes>
		</html>
		<?php
	}

	function getHTMLRight(){
		?>
		</head>
		<frameset cols="*" framespacing="0" border="0" frameborder="NO">
			<frame src="<?php
		print $this->frameset
		?>?pnt=editor" scrolling="no" noresize name="editor">
		</frameset>
		<noframes>
			<body bgcolor="#ffffff">
				<p></p>
			</body>
		</noframes>
		</html>

		<?php
	}

	function getHTMLEditor(){
		?>
		</head>
		<frameset rows="40,*,40" framespacing="0" border="0" frameborder="no">
			<frame src="<?php
		print $this->frameset
		?>?pnt=edheader&home=1" name="edheader" noresize scrolling=no>
			<frame src="<?php
				 print $this->frameset
		?>?pnt=edbody&home=1" name="edbody" scrolling=auto>
			<frame src="<?php
				 print $this->frameset
		?>?pnt=edfooter&home=1" name="edfooter" scrolling=no>

		</frameset>
		<noframes>
			<body style="background-color:#bfbfbf; background-repeat:repeat;margin:0px 0px 0px 0px">
			</body>
		</noframes>
		</html>
		<?php
	}

}
