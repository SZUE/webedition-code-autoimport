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
class weShopFrames extends weModuleFrames{

	var $db;
	var $View;
	var $frameset;

	//var $edit_cmd = "edit_newsletter";

	function __construct($frameset){

		parent::__construct(WE_SHOP_MODULE_DIR . "edit_shop_frameset.php"); //FIXME: tmp!
		$this->View = new weShopView(WE_SHOP_MODULE_DIR . "edit_shop_frameset.php", "top.content");

		$this->module = "shop";
		$this->treeDefaultWidth = 204;
	}

	function getHTML($what){
		parent::getHTML($what);
	}

	function getJSCmdCode(){
		return $this->View->getJSTop_tmp();
	}

	function getJSTreeCode(){ //TODO: use we_html_element::jsElement and move to new class weShopTree
		?>
		<script type="text/javascript"><!--
			var menuDaten = new container();
			var count = 0;
			var folder = 0;
			var table = "<?php print SHOP_TABLE; ?>";

			function drawEintraege() {
				fr = top.content.resize.left.window.document;//imi new adress
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

				fr.writeln("</" + "SCRIPT>");
				fr.writeln('<?php print STYLESHEET_SCRIPT; ?>');
				fr.write("</head>");
				fr.write("<BODY BGCOLOR=\"#F3F7FF\" LINK=\"#000000\" ALINK=\"#000000\" VLINK=\"#000000\" leftmargin=\"5\" topmargin=\"0\" marginheight=\"0\" marginwidth=\"5\">");
				fr.write("<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\"><tr><td class=\"tree\">\n<NOBR>\n");
				fr.write("<tr><td class=\"tree\"><NOBR><a href=javascript:// onClick=\"doYearClick(" + top.yearshop + ");return true;\" title=\"Umsätze des Geschäftsjahres\" ><?php print g_l('modules_shop', '[treeYear]'); ?>: <strong>" + top.yearshop + " </strong></a> <br/>");

				zeichne("0", "");
				fr.write("</NOBR></td></tr></table>");
				fr.write("</BODY></html>");
				fr.close();
			}

			function zeichne(startEntry, zweigEintrag) {
				var nf = search(startEntry);
				var ai = 1;
				while (ai <= nf.laenge) {
					fr.write(zweigEintrag);
					if (nf[ai].typ == 'shop') {
						if (ai == nf.laenge)
							fr.write("&nbsp;&nbsp;<IMG SRC=<?php print TREE_IMAGE_DIR; ?>kreuzungend.gif WIDTH=19 HEIGHT=18 align=absmiddle BORDER=0>");
						else
							fr.write("&nbsp;&nbsp;<IMG SRC=<?php print TREE_IMAGE_DIR; ?>kreuzung.gif WIDTH=19 HEIGHT=18 align=absmiddle BORDER=0>");
		<?php if(we_hasPerm("EDIT_SHOP_ORDER")){ ?> // make  in tree clickable
							if (nf[ai].name != -1) {
								fr.write("<a href=\"javascript://\" onClick=\"doClick(" + nf[ai].name + ",'" + nf[ai].contentType + "','" + nf[ai].table + "');return true;\" BORDER=0>");
							}
		<?php } ?>
						fr.write("<IMG SRC=<?php print TREE_IMAGE_DIR; ?>icons/" + nf[ai].icon + " WIDTH=16 HEIGHT=18 align=absmiddle BORDER=0 title=\"<?php print g_l('tree', "[edit_statustext]"); ?>\">");
		<?php if(we_hasPerm("EDIT_SHOP_ORDER")){ ?>
							fr.write("</a>");
		<?php } ?>
						fr.write("&nbsp;");
		<?php if(we_hasPerm("EDIT_SHOP_ORDER")){ ?> // make orders in tree clickable
							fr.write("<a href=\"javascript://\" onClick=\"doClick(" + nf[ai].name + ",'" + nf[ai].contentType + "','" + nf[ai].table + "');return true;\">");

		<?php } ?>
						//changed for #6786
						fr.write("<span style='" + nf[ai].st + "'>" + nf[ai].text + "</span>");
		<?php if(we_hasPerm("EDIT_SHOP_ORDER")){ ?>
							fr.write("</A>");
		<?php } ?>
						fr.write("&nbsp;&nbsp;<BR>\n");
					} else {
						var newAst = zweigEintrag;

						var zusatz = (ai == nf.laenge) ? "end" : "";

						if (nf[ai].offen == 0) {
							fr.write("&nbsp;&nbsp;<A HREF=\"javascript:top.content.openClose('" + nf[ai].name + "',1)\" BORDER=0><IMG SRC=<?php print TREE_IMAGE_DIR; ?>auf" + zusatz + ".gif WIDTH=19 HEIGHT=18 align=absmiddle BORDER=0 title=\"<?php print g_l('tree', "[open_statustext]") ?>\"></A>");
							var zusatz2 = "";
						} else {
							fr.write("&nbsp;&nbsp;<A HREF=\"javascript:top.content.openClose('" + nf[ai].name + "',0)\" BORDER=0><IMG SRC=<?php print TREE_IMAGE_DIR; ?>zu" + zusatz + ".gif WIDTH=19 HEIGHT=18 align=absmiddle BORDER=0 title=\"<?php print g_l('tree', "[close_statustext]") ?>\"></A>");
							var zusatz2 = "open";
						}
		<?php if(we_hasPerm("EDIT_SHOP_ORDER")){ ?>
							fr.write("<a href=\"javascript://\" onClick=\"doFolderClick(" + nf[ai].name + ",'" + nf[ai].contentType + "','" + nf[ai].table + "');return true;\" BORDER=0>");
		<?php } ?>
						fr.write("<IMG SRC=<?php print TREE_IMAGE_DIR; ?>icons/folder" + zusatz2 + ".gif WIDTH=16 HEIGHT=18 align=absmiddle BORDER=0 title=\"<?php print g_l('tree', "[edit_statustext]"); ?>\">");
		<?php if(we_hasPerm("EDIT_SHOP_ORDER")){ ?>
							fr.write("</a>");
			<?php
		}
		if(we_hasPerm("EDIT_SHOP_ORDER")){
			?> // make the month in tree clickable
							fr.write("<A HREF=\"javascript://\" onClick=\"doFolderClick(" + nf[ai].name + ",'" + nf[ai].contentType + "','" + nf[ai].table + "');return true;\">");
		<?php } ?>
						fr.write("&nbsp;" + (parseInt(nf[ai].published) ? " <b>" : "") + nf[ai].text + (parseInt(nf[ai].published) ? " </b>" : ""));
		<?php if(we_hasPerm("EDIT_SHOP_ORDER")){ ?>
							fr.write("</a>");
		<?php } ?>
						fr.write("&nbsp;&nbsp;<BR>\n");
						if (nf[ai].offen) {
							if (ai == nf.laenge)
								newAst = newAst + "<IMG SRC=<?php print TREE_IMAGE_DIR; ?>leer.gif WIDTH=19 HEIGHT=18 align=absmiddle BORDER=0>";
							else
								newAst = newAst + "<IMG SRC=<?php print TREE_IMAGE_DIR; ?>strich2.gif WIDTH=19 HEIGHT=18 align=absmiddle BORDER=0>";
							zeichne(nf[ai].name, newAst);
						}
					}
					ai++;
				}
			}

			function makeNewEntry(icon, id, pid, txt, offen, ct, tab, pub) {
				if (table == tab) {
					if (menuDaten[indexOfEntry(pid)]) {
						if (ct == "folder")
							menuDaten.addSort(new dirEntry(icon, id, pid, txt, offen, ct, tab));
						else
							menuDaten.addSort(new urlEntry(icon, id, pid, txt, ct, tab, pub));
						drawEintraege();
					}
				}
			}


			function updateEntry(id, text, pub) {
				var ai = 1;
				while (ai <= menuDaten.laenge) {
					if ((menuDaten[ai].typ == 'folder') || (menuDaten[ai].typ == 'shop'))
						if (menuDaten[ai].name == id) {
							menuDaten[ai].text = text;
							menuDaten[ai].published = pub;
						}
					ai++;
				}
				drawEintraege();
			}

			function deleteEntry(id) {
				var ai = 1;
				var ind = 0;
				while (ai <= menuDaten.laenge) {
					if ((menuDaten[ai].typ == 'folder') || (menuDaten[ai].typ == 'shop'))
						if (menuDaten[ai].name == id) {
							ind = ai;
							break;
						}
					ai++;
				}
				if (ind != 0) {
					ai = ind;
					while (ai <= menuDaten.laenge - 1) {
						menuDaten[ai] = menuDaten[ai + 1];
						ai++;
					}
					menuDaten.laenge[menuDaten.laenge] = null;
					menuDaten.laenge--;
					drawEintraege();
				}
			}

			function openClose(name, status) {
				var eintragsIndex = indexOfEntry(name);
				menuDaten[eintragsIndex].offen = status;
				if (status) {
					if (!menuDaten[eintragsIndex].loaded) {
						drawEintraege();
					} else {
						drawEintraege();
					}
				} else {
					drawEintraege();
				}
			}

			function indexOfEntry(name) {
				var ai = 1;
				while (ai <= menuDaten.laenge) {
					if ((menuDaten[ai].typ == 'root') || (menuDaten[ai].typ == 'folder'))
						if (menuDaten[ai].name == name)
							return ai;
					ai++;
				}
				return -1;
			}

			function search(eintrag) {
				var nf = new container();
				var ai = 1;
				while (ai <= menuDaten.laenge) {
					if ((menuDaten[ai].typ == 'folder') || (menuDaten[ai].typ == 'shop'))
						if (menuDaten[ai].vorfahr == eintrag)
							nf.add(menuDaten[ai]);
					ai++;
				}
				return nf;
			}

			function container() {
				this.laenge = 0;
				this.clear = containerClear;
				this.add = add;
				this.addSort = addSort;
				return this;
			}

			function add(object) {
				this.laenge++;
				this[this.laenge] = object;
			}

			function containerClear() {
				this.laenge = 0;
			}

			function addSort(object) {
				this.laenge++;
				for (var i = this.laenge; i > 0; i--) {
					if (i > 1 && this[i - 1].text.toLowerCase() > object.text.toLowerCase()) {
						this[i] = this[i - 1];
					} else {
						this[i] = object;
						break;
					}
				}
			}

			function rootEntry(name, text, rootstat) {
				this.name = name;
				this.text = text;
				this.loaded = true;
				this.typ = 'root';
				this.rootstat = rootstat;
				return this;
			}

			function dirEntry(icon, name, vorfahr, text, offen, contentType, table, published) {
				this.icon = icon;
				this.name = name;
				this.vorfahr = vorfahr;
				this.text = text;
				this.typ = 'folder';
				this.offen = (offen ? 1 : 0);
				this.contentType = contentType;
				this.table = table;
				this.loaded = (offen ? 1 : 0);
				this.checked = false;
				this.published = published;
				return this;
			}

			//changed for #6786
			function urlEntry(icon, name, vorfahr, text, contentType, table, published, style) {
				this.icon = icon;
				this.name = name;
				this.vorfahr = vorfahr;
				this.text = text;
				this.typ = 'shop';
				this.checked = false;
				this.contentType = contentType;
				this.table = table;
				this.published = published;
				this.st = style;
				return this;
			}

			function loadData() {

				menuDaten.clear();
				menuDaten.add(new self.rootEntry('0', 'root', 'root'));


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
				if(isset(${'l' . $this->db->f("mdate")})){
					${'l' . $this->db->f("mdate")}++;
				} else{
					${'l' . $this->db->f("mdate")} = 1;
				}
			}


			//FIXME: remove eval
			if(isset(${'v' . $this->db->f("mdate")})){
				${'v' . $this->db->f("mdate")}++;
			} else{
				${'v' . $this->db->f("mdate")} = 1;
			}
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

			function start() {
				loadData();
				drawEintraege();
			}
			self.focus();
			//-->
		</script>
		<?php
	}

	function getHTMLFrameset(){
		$extraHead = $this->getJSTreeCode();
		return weModuleFrames::getHTMLFrameset($extraHead, true);
	}

	function getHTMLIconbar(){
		print STYLESHEET;

		print we_html_element::jsElement('

			function doUnload() {
				if (!!jsWindow_count) {
					for (i = 0; i < jsWindow_count; i++) {
						eval("jsWindow" + i + "Object.close()");
					}
				}
			}

			function we_cmd() {

				switch (arguments[0]) {

					case "openOrder":
						if(top.content.resize.left.window.doClick) {
							top.content.resize.left.window.doClick(arguments[1], arguments[2], arguments[3]);
						}
					break;

					default:
						// not needed yet
					break;
				}
			}

		');

		$bid = isset($_REQUEST["bid"]) ? intval($_REQUEST["bid"]) : 0;

		$cid = f("SELECT IntCustomerID FROM " . SHOP_TABLE . " WHERE IntOrderID = " . $bid, "IntCustomerID", $this->db);
		$this->db->query("SELECT IntOrderID,DATE_FORMAT(DateOrder,'" . g_l('date', '[format][mysqlDate]') . "') as orddate FROM " . SHOP_TABLE . " GROUP BY IntOrderID ORDER BY IntID DESC");

		if($this->db->next_record()){
			$headline = '<a style="text-decoration: none;" href="javascript:we_cmd(\'openOrder\', ' . $this->db->f("IntOrderID") . ',\'shop\',\'' . SHOP_TABLE . '\');">' . sprintf(g_l('modules_shop', '[lastOrder]'), $this->db->f("IntOrderID"), $this->db->f("orddate")) . '</a>';
		} else{
			$headline = "";
		}


		// grep the last element from the year-set, wich is the current year
		$this->db->query("SELECT DATE_FORMAT(DateOrder,'%Y') AS DateOrd FROM " . SHOP_TABLE . " ORDER BY DateOrd");
		while($this->db->next_record()) {
			$strs = array($this->db->f("DateOrd"));
			$yearTrans = end($strs);
		}
		// print $yearTrans;
		/// config
		$this->db->query("SELECT strFelder from " . ANZEIGE_PREFS_TABLE . " WHERE strDateiname = 'shop_pref'");
		$this->db->next_record();
		$feldnamen = explode("|", $this->db->f("strFelder"));
		for($i = 0; $i <= 3; $i++){
			$feldnamen[$i] = isset($feldnamen[$i]) ? $feldnamen[$i] : '';
		}
		$fe = explode(",", $feldnamen[3]);
		if(empty($classid)){
			$classid = $fe[0];
		}

		//$resultO = count($fe);
		$resultO = array_shift($fe);

		// wether the resultset ist empty?
		$resultD = f("SELECT count(Name) as Anzahl FROM " . LINK_TABLE . ' WHERE Name ="' . WE_SHOP_TITLE_FIELD_NAME . '"', 'Anzahl', $this->db);
		?>

		<body background="<?php print IMAGE_DIR ?>backgrounds/iconbarBack.gif" marginwidth="0" topmargin="5" marginheight="5" leftmargin="0">
			<table border="0" cellpadding="6" cellspacing="0" style="margin-left:8px">
				<tr>
					<?php echo "<td>" . we_button::create_button("image:btn_shop_extArt", "javascript:top.opener.top.we_cmd('new_article')", true, -1, -1, "", "", !we_hasPerm("NEW_USER")); ?></td>

					<td>
						<?php echo we_button::create_button("image:btn_shop_delOrd", "javascript:top.opener.top.we_cmd('delete_shop')", true, -1, -1, "", "", !we_hasPerm("NEW_USER")); ?></td>
					<?php
					if(($resultD > 0) && (!empty($resultO))){ //docs and objects
						echo "<td>" . we_button::create_button("image:btn_shop_sum", "javascript:top.content.resize.shop_properties.location=' edit_shop_editorFramesetTop.php?typ=document '", true) . "</td>";
					} elseif(($resultD < 1) && (!empty($resultO))){ // no docs but objects
						echo "<td>" . we_button::create_button("image:btn_shop_sum", "javascript:top.content.resize.shop_properties.location=' edit_shop_editorFramesetTop.php?typ=object&ViewClass=$classid '", true) . "</td>";
					} elseif(($resultD > 0) && (empty($resultO))){ // docs but no objects
						echo "<td>" . we_button::create_button("image:btn_shop_sum", "javascript:top.content.resize.shop_properties.location=' edit_shop_editorFramesetTop.php?typ=document '", true) . "</td>";
					} else{
						echo " ";
					}
					?>
					<td>
						<?php echo we_button::create_button("image:btn_shop_pref", "javascript:top.opener.top.we_cmd('pref_shop')", true, -1, -1, "", "", !we_hasPerm("NEW_USER")); ?></td>
					<td>
						<?php echo we_button::create_button("image:btn_payment_val", "javascript:top.opener.top.we_cmd('payment_val')", true, -1, -1, "", "", !we_hasPerm("NEW_USER")); ?></td>
					<?php
					if($headline){
						?>
						<td align="right" class="header_shop"><span style="margin-left:15px"><?php print @$headline; ?></span></td>
							<?php
						}
						?>
				</tr>
			</table>
		</body></html>
		<?php
	}

	function getHTMLCmd(){
		$body = we_html_element::htmlBody();

		return $this->getHTMLDocument($body);
	}

	function getHTMLResize(){
		$_treewidth = 204;
		$incDecTree = '
			<img id="incBaum" src="' . BUTTONS_DIR . 'icons/function_plus.gif" width="9" height="12" style="position:absolute;bottom:53px;left:5px;border:1px solid grey;padding:0 1px;cursor: pointer; ' . ($_treewidth <= 100 ? 'bgcolor:grey;' : '') . '" onClick="incTree();">
			<img id="decBaum" src="' . BUTTONS_DIR . 'icons/function_minus.gif" width="9" height="12" style="position:absolute;bottom:33px;left:5px;border:1px solid grey;padding:0 1px;cursor: pointer; ' . ($_treewidth <= 100 ? 'bgcolor:grey;' : '') . '" onClick="decTree();">
			<img id="arrowImg" src="' . BUTTONS_DIR . 'icons/direction_' . ($_treewidth <= 100 ? 'right' : 'left') . '.gif" width="9" height="12" style="position:absolute;bottom:13px;left:5px;border:1px solid grey;padding:0 1px;cursor: pointer;" onClick="top.content.toggleTree();">
		';

		$editorPath = isset($_REQUEST['bid']) ? WE_SHOP_MODULE_DIR . 'edit_shop_editorFrameset.php?bid=' . $_REQUEST['bid'] :
			WE_SHOP_MODULE_DIR . 'edit_shop_editorFramesetTop.php?home=1';

		print we_html_element::htmlBody(array('style' => 'background-color: #bfbfbf; background-repeat: repeat; margin: 0px 0px 0px 0px'), 
			we_html_element::htmlDiv(array('style' => 'position: absolute; top: 0px; bottom: 0px; left: 0px; right: 0px;'), 
				we_html_element::htmlDiv(array('id' => 'lframeDiv', 'style' => 'position: absolute; top: 0px; bottom: 0px; left: 0px; right: 0px;'), 
					we_html_element::htmlDiv(array('style' => 'position: absolute; top: 0px; bottom: 0px; left: 0px; right: 0px; width: 24px; background-image: url(/webEdition/images/v-tabs/background.gif); background-repeat: repeat-y; border-top: 1px solid black;'), $incDecTree) .
					we_html_element::htmlIFrame('left', HTML_DIR . 'white.html', 'position: absolute; top: 0px; bottom: 0px; left: 24px; right: 0px; verflow: hidden; border-top: 1px solid white')
				) .
				//we_html_element::htmlIFrame('shop_properties', $this->frameset . '?pnt=right', 'position: absolute; top: 0px; bottom: 0px; left: 204px; right: 0px; width:auto; border-left: 1px solid black; overflow: hidden;')
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
		//print $this->View->getHTMLProperties(); 
	}

	function getHTMLEditorBody(){
		print $this->View->getHTMLProperties(); 
	}
}