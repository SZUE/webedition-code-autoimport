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
class we_shop_frames extends we_modules_frame{
	var $db;
	var $View;
	var $frameset;
	public $module = 'shop';
	protected $hasIconbar = true;
	protected $useMainTree = false;
	protected $treeDefaultWidth = 204;

	function __construct($frameset){
		parent::__construct(WE_SHOP_MODULE_DIR . 'edit_shop_frameset.php');
		$this->View = new we_shop_view(WE_SHOP_MODULE_DIR . 'edit_shop_frameset.php', 'top.content');
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
			var table = "<?php echo SHOP_TABLE; ?>";

			function drawEintraege() {
				fr = top.content.tree.window.document;//TODO: when frame tree is eliminated change adress to ...getElementById('tree')!!!
				fr.open();
				fr.writeln("<html><head>");
				fr.writeln("<?php echo str_replace(array('script', '"'), array('scr"+"ipt', '\''), we_html_tools::getJSErrorHandler()); ?>");
				fr.writeln("<script type=\"text/javascript\">");
				fr.writeln("var clickCount=0;");
				fr.writeln("var wasdblclick=0;");
				fr.writeln("var tout=null;");
				fr.writeln("function doClick(id,ct,table){");
				fr.writeln("top.content.editor.location='<?php echo WE_SHOP_MODULE_DIR ?>edit_shop_frameset.php?pnt=editor&bid='+id;");
				fr.writeln("}");
				fr.writeln("function doFolderClick(id,ct,table){");
				fr.writeln("top.content.editor.location='<?php echo WE_SHOP_MODULE_DIR; ?>edit_shop_frameset.php?pnt=editor&mid='+id;");
				fr.writeln("}");

				fr.writeln("function doYearClick(yearView){");
				fr.writeln("top.content.editor.location='<?php echo WE_SHOP_MODULE_DIR; ?>edit_shop_frameset.php?pnt=editor&ViewYear='+yearView;");
				fr.writeln("}");

				fr.writeln("</" + "script>");
				fr.writeln('<?php echo STYLESHEET_SCRIPT; ?>');
				fr.write("</head>");
				fr.write("<body bgcolor=\"#F3F7FF\" link=\"#000000\" alink=\"#000000\" vlink=\"#000000\" leftmargin=\"5\" topmargin=\"0\" marginheight=\"0\" marginwidth=\"5\">");
				fr.write("<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\"><tr><td class=\"tree\"><nobr>");
				fr.write("<tr><td class=\"tree\"><nobr><a href=javascript:// onclick=\"doYearClick(" + top.yearshop + ");return true;\" title=\"<?php echo g_l('modules_shop', '[treeYearClick]'); ?>\" ><?php echo g_l('modules_shop', '[treeYear]'); ?>: <strong>" + top.yearshop + " </strong></a> <br/>");

				zeichne(0, "");
				fr.write("</nobr></td></tr></table>");
				fr.write("</body></html>");
				fr.close();
			}

			function zeichne(startEntry, zweigEintrag) {
				var nf = search(startEntry);
				var ai = 1;
				while (ai <= nf.laenge) {
					fr.write(zweigEintrag);
					if (nf[ai].typ === 'shop') {
						if (ai === nf.laenge) {
							fr.write("&nbsp;&nbsp;<IMG SRC=<?php echo TREE_IMAGE_DIR; ?>kreuzungend.gif WIDTH=19 HEIGHT=18 align=absmiddle BORDER=0>");
						} else {
							fr.write("&nbsp;&nbsp;<IMG SRC=<?php echo TREE_IMAGE_DIR; ?>kreuzung.gif WIDTH=19 HEIGHT=18 align=absmiddle BORDER=0>");
						}
		<?php if(permissionhandler::hasPerm("EDIT_SHOP_ORDER")){ ?> // make  in tree clickable
							if (nf[ai].name !== -1) {
								fr.write("<a href=\"javascript://\" onclick=\"doClick(" + nf[ai].name + ",'" + nf[ai].contentType + "','" + nf[ai].table + "');return true;\" BORDER=0>");
							}
		<?php } ?>
						fr.write("<IMG SRC=<?php echo TREE_IMAGE_DIR; ?>icons/" + nf[ai].icon + " WIDTH=16 HEIGHT=18 align=absmiddle BORDER=0 title=\"<?php echo g_l('tree', '[edit_statustext]'); ?>\">");
		<?php if(permissionhandler::hasPerm("EDIT_SHOP_ORDER")){ ?>
							fr.write("</a>");
		<?php } ?>
						fr.write("&nbsp;");
		<?php if(permissionhandler::hasPerm("EDIT_SHOP_ORDER")){ ?> // make orders in tree clickable
							fr.write("<a href=\"javascript://\" onclick=\"doClick(" + nf[ai].name + ",'" + nf[ai].contentType + "','" + nf[ai].table + "');return true;\">");

		<?php } ?>
						//changed for #6786
						fr.write("<span style='" + nf[ai].st + "'>" + nf[ai].text + "</span>");
		<?php if(permissionhandler::hasPerm('EDIT_SHOP_ORDER')){ ?>
							fr.write("</a>");
		<?php } ?>
						fr.write("&nbsp;&nbsp;<br/>\n");
					} else {
						var newAst = zweigEintrag;

						var zusatz = (ai === nf.laenge) ? "end" : "";

						if (nf[ai].offen === 0) {
							fr.write("&nbsp;&nbsp;<a href=\"javascript:top.content.openClose('" + nf[ai].name + "',1)\" border=0><img src=<?php echo TREE_IMAGE_DIR; ?>auf" + zusatz + ".gif WIDTH=19 HEIGHT=18 align=absmiddle BORDER=0 title=\"<?php echo g_l('tree', '[open_statustext]') ?>\"></a>");
							var zusatz2 = "";
						} else {
							fr.write("&nbsp;&nbsp;<a href=\"javascript:top.content.openClose('" + nf[ai].name + "',0)\" border=0><img src=<?php echo TREE_IMAGE_DIR; ?>zu" + zusatz + ".gif WIDTH=19 HEIGHT=18 align=absmiddle BORDER=0 title=\"<?php echo g_l('tree', '[close_statustext]') ?>\"></a>");
							var zusatz2 = "open";
						}
		<?php if(permissionhandler::hasPerm("EDIT_SHOP_ORDER")){ ?>
							fr.write("<a href=\"javascript://\" onclick=\"doFolderClick(" + nf[ai].name + ",'" + nf[ai].contentType + "','" + nf[ai].table + "');return true;\" BORDER=0>");
		<?php } ?>
						fr.write("<img src=<?php echo TREE_IMAGE_DIR; ?>icons/folder" + zusatz2 + ".gif WIDTH=16 HEIGHT=18 align=absmiddle BORDER=0 title=\"<?php echo g_l('tree', '[edit_statustext]'); ?>\">");
		<?php if(permissionhandler::hasPerm('EDIT_SHOP_ORDER')){ ?>
							fr.write("</a>");
			<?php
		}
		if(permissionhandler::hasPerm("EDIT_SHOP_ORDER")){
			?> // make the month in tree clickable
							fr.write("<a href=\"javascript://\" onclick=\"doFolderClick(" + nf[ai].name + ",'" + nf[ai].contentType + "','" + nf[ai].table + "');return true;\">");
		<?php } ?>
						fr.write("&nbsp;" + (parseInt(nf[ai].published) ? " <b>" : "") + nf[ai].text + (parseInt(nf[ai].published) ? " </b>" : ""));
		<?php if(permissionhandler::hasPerm('EDIT_SHOP_ORDER')){ ?>
							fr.write("</a>");
		<?php } ?>
						fr.write("&nbsp;&nbsp;<br/>");
						if (nf[ai].offen) {
							newAst = newAst + "<img src=<?php echo TREE_IMAGE_DIR; ?>" + (ai === nf.laenge ? "leer.gif" : "strich2.gif") + " width=19 height=18 align=absmiddle border=0>";
							zeichne(nf[ai].name, newAst);
						}
					}
					ai++;
				}
			}

			function makeNewEntry(icon, id, pid, txt, offen, ct, tab, pub) {
				if (table === tab && menuDaten[indexOfEntry(pid)]) {
					if (ct === "folder") {
						menuDaten.addSort(new dirEntry(icon, id, pid, txt, offen, ct, tab));
					} else {
						menuDaten.addSort(new urlEntry(icon, id, pid, txt, ct, tab, pub));
					}
					drawEintraege();
				}
			}


			function updateEntry(id, text, pub) {
				var ai = 1;
				while (ai <= menuDaten.laenge) {
					if ((menuDaten[ai].typ === 'folder') || (menuDaten[ai].typ === 'shop')) {
						if (menuDaten[ai].name == id) {
							menuDaten[ai].text = text;
							menuDaten[ai].published = pub;
						}
					}
					ai++;
				}
				drawEintraege();
			}

			function deleteEntry(id) {
				var ai = 1;
				var ind = 0;
				while (ai <= menuDaten.laenge) {
					if ((menuDaten[ai].typ === 'folder') || (menuDaten[ai].typ === 'shop')) {
						if (menuDaten[ai].name == id) {
							ind = ai;
							break;
						}
					}
					ai++;
				}
				if (ind !== 0) {
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
					if ((menuDaten[ai].typ === 'root') || (menuDaten[ai].typ === 'folder')) {
						if (menuDaten[ai].name == name) {
							return ai;
						}
					}
					ai++;
				}
				return -1;
			}

			function search(eintrag) {
				var nf = new container();
				var ai = 1;
				while (ai <= menuDaten.laenge) {
					if ((menuDaten[ai].typ === 'folder') || (menuDaten[ai].typ === 'shop')) {
						if (menuDaten[ai].vorfahr == eintrag) {
							nf.add(menuDaten[ai]);
						}
					}
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

		$this->db->query("SELECT IntOrderID,DateShipping,DateConfirmation,DateCustomA,DateCustomB,DateCustomC,DateCustomD,DateCustomE,DatePayment,DateCustomF,DateCustomG,DateCancellation,DateCustomH,DateCustomI,DatecustomJ,DateFinished, DATE_FORMAT(DateOrder,'" . g_l('date', '[format][mysqlDate]') . "') AS orddate, DATE_FORMAT(DateOrder,'%c%Y') as mdate FROM " . SHOP_TABLE . ' GROUP BY IntOrderID ORDER BY IntID DESC');
		while($this->db->next_record()){
			//added for #6786
			$style = 'color:black;font-weight:bold;';

			if($this->db->f('DateCustomA') || $this->db->f('DateCustomB') || $this->db->f('DateCustomC') || $this->db->f('DateCustomD') || $this->db->f('DateCustomE') || $this->db->f('DateCustomF') || $this->db->f('DateCustomG') || $this->db->f('DateCustomH') || $this->db->f('DateCustomI') || $this->db->f('DateCustomJ') || $this->db->f('DateConfirmation') || ($this->db->f('DateShipping') != '0000-00-00 00:00:00' && $this->db->f('DateShipping'))){
				$style = 'color:red;';
			}

			if($this->db->f('DatePayment') != '0000-00-00 00:00:00' && $this->db->f('DatePayment')){
				$style = 'color:#006699;';
			}

			if($this->db->f('DateCancellation') || $this->db->f('DateFinished')){
				$style = 'color:black;';
			}


			echo "  menuDaten.add(new urlEntry('" . we_base_ContentTypes::FILE_ICON . "'," . $this->db->f("IntOrderID") . "," . $this->db->f("mdate") . ",'" . $this->db->f("IntOrderID") . ". " . g_l('modules_shop', '[bestellung]') . " " . $this->db->f("orddate") . "','shop','" . SHOP_TABLE . "','" . (($this->db->f("DateShipping") > 0) ? 0 : 1) . "','" . $style . "'));\n";
			if($this->db->f('DateShipping') <= 0){
				if(isset(${'l' . $this->db->f('mdate')})){
					${'l' . $this->db->f('mdate')} ++;
				} else {
					${'l' . $this->db->f('mdate')} = 1;
				}
			}


			//FIXME: remove eval
			if(isset(${'v' . $this->db->f('mdate')})){
				${'v' . $this->db->f('mdate')} ++;
			} else {
				${'v' . $this->db->f('mdate')} = 1;
			}
		}

		$year = we_base_request::_(we_base_request::INT, 'year', date('Y'));
//unset($_SESSION['year']);
		for($f = 12; $f > 0; $f--){
			$r = (isset(${'v' . $f . $year}) ? ${'v' . $f . $year} : '');
			$k = (isset(${'l' . $f . $year}) ? ${'l' . $f . $year} : '');
			echo "menuDaten.add(new dirEntry('" . we_base_ContentTypes::FOLDER_ICON . "',$f+''+$year,0, '" . (($f < 10) ? "0" . $f : $f) . ' ' . g_l('modules_shop', '[sl]') . " " . g_l('date', '[month][long][' . ($f - 1) . ']') . " (" . (($k > 0) ? "<b>" . $k . "</b>" : 0) . "/" . (($r > 0) ? $r : 0) . ")',0,'',''," . (($k > 0) ? 1 : 0) . "));";
		} //'".$this->db->f("mdate")."'
		echo 'top.yearshop = ' . $year . ';';
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

		if(($bid = we_base_request::_(we_base_request::INT, 'bid')) === -1){
			$this->db->query("SELECT IntOrderID FROM " . SHOP_TABLE . " ORDER BY IntID DESC");
			$bid = $this->db->next_record() ? $this->db->f("IntOrderID") : 0;
		}

		$extraUrlParams = $bid > 0 ? '&bid=' . $bid : '&top=1&home=1';

		return parent::getHTMLFrameset($extraHead, $extraUrlParams);
	}

	function getHTMLIconbar(){ //TODO: move this to weShopView::getHTMLIconbar();
		$extraHead = we_html_element::jsElement('
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
			//TODO: check this adress: mit oder ohne tree? Bisher: left
			if(top.content.tree.window.doClick) {
				top.content.tree.window.doClick(arguments[1], arguments[2], arguments[3]);//TODO: check this adress
			}
		break;

		default:
			// not needed yet
		break;
	}
}

		');

		//	$bid = we_base_request::_(we_base_request::INT, 'bid', 0);
		//	$cid = f('SELECT IntCustomerID FROM ' . SHOP_TABLE . ' WHERE IntOrderID=' . $bid, '', $this->db);
		$data = getHash("SELECT IntOrderID,DATE_FORMAT(DateOrder,'" . g_l('date', '[format][mysqlDate]') . "') AS orddate FROM " . SHOP_TABLE . ' GROUP BY IntOrderID ORDER BY IntID DESC LIMIT 1', $this->db);

		$headline = $data ? '<a style="text-decoration: none;" href="javascript:we_cmd(\'openOrder\', ' . $data["IntOrderID"] . ',\'shop\',\'' . SHOP_TABLE . '\');">' . sprintf(g_l('modules_shop', '[lastOrder]'), $data["IntOrderID"], $data["orddate"]) . '</a>' : '';

		/// config
		$feldnamen = explode('|', f('SELECT strFelder FROM ' . WE_SHOP_PREFS_TABLE . ' WHERE strDateiname="shop_pref"', '', $this->db));
		for($i = 0; $i <= 3; $i++){
			$feldnamen[$i] = isset($feldnamen[$i]) ? $feldnamen[$i] : '';
		}
		$fe = explode(',', $feldnamen[3]);

		$classid = $fe[0];


		/* TODO: we have this or similar code at least four times!! */

		$resultO = array_shift($fe);

		// wether the resultset ist empty?
		$resultD = f('SELECT 1 FROM ' . LINK_TABLE . ' WHERE Name="' . WE_SHOP_TITLE_FIELD_NAME . '" LIMIT 1', '', $this->db);

		$c = 0;
		$iconBarTable = new we_html_table(array("border" => 0, "cellpadding" => 6, "cellspacing" => 0, "style" => "margin-left:8px"), 1, 4);

		$iconBarTable->setCol(0, $c++, null, we_html_button::create_button("image:btn_shop_extArt", "javascript:top.opener.top.we_cmd('new_article')", true, 0, 0, "", "", !permissionhandler::hasPerm("NEW_USER")));
		$iconBarTable->setCol(0, $c++, null, we_html_button::create_button("image:btn_shop_delOrd", "javascript:top.opener.top.we_cmd('delete_shop')", true, 0, 0, "", "", !permissionhandler::hasPerm("NEW_USER")));

		if($resultD){
			$iconBarTable->addCol();
			$iconBarTable->setCol(0, $c++, null, we_html_button::create_button("image:btn_shop_sum", "javascript:top.content.editor.location=' edit_shop_frameset.php?pnt=editor&top=1&typ=document '", true));
		} elseif($resultO){
			$iconBarTable->addCol();
			$iconBarTable->setCol(0, $c++, null, we_html_button::create_button("image:btn_shop_sum", "javascript:top.content.editor.location=' edit_shop_frameset.php?pnt=editor&top=1&typ=object&ViewClass=$classid '", true));
		}

		$iconBarTable->setCol(0, $c++, null, we_html_button::create_button("image:btn_shop_pref", "javascript:top.opener.top.we_cmd('pref_shop')", true, 0, 0, "", "", !permissionhandler::hasPerm("NEW_USER")));
		$iconBarTable->setCol(0, $c++, null, we_html_button::create_button("image:btn_payment_val", "javascript:top.opener.top.we_cmd('payment_val')", true, 0, 0, "", "", !permissionhandler::hasPerm("NEW_USER")));

		if($headline){
			$iconBarTable->addCol();
			$iconBarTable->setCol(0, $c++, array('align' => 'right', 'class' => 'header_shop'), '<span style="margin-left:15px">' . $headline . '</span>');
		}

		$body = we_html_element::htmlBody(array('background' => IMAGE_DIR . 'backgrounds/iconbarBack.gif', 'marginwidth' => 0, 'topmargin' => 5, 'marginheight' => 5, 'leftmargin' => 0), $iconBarTable->getHTML());

		return $this->getHTMLDocument($body, $extraHead);
	}

	function getHTMLCmd(){
		return $this->getHTMLDocument(we_html_element::htmlBody());
	}

	protected function getHTMLEditor(){//TODO: maybe abandon the split between former Top- and other editor files
		if(we_base_request::_(we_base_request::BOOL, 'top')){//doing what have been done in edit_shop_editorFramesetTop before
			return $this->getHTMLEditorTop();
		}

		//do what have been done in edit_shop_editorFrameset before

		$bid = we_base_request::_(we_base_request::INT, 'bid', 0);
		$mid = we_base_request::_(we_base_request::STRING, 'mid', 0);
		$yearView = we_base_request::_(we_base_request::INT, 'ViewYear', 0);
		$home = we_base_request::_(we_base_request::BOOL, 'home');

		if($home){
			$bodyURL = WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=mod_home&mod=shop';
		} elseif($mid){
			$year = substr($mid, (strlen($mid) - 4));
			$month = str_replace($year, '', $mid);
			$bodyURL = WE_SHOP_MODULE_DIR . 'edit_shop_revenueTop.php?ViewYear=' . $year . '&ViewMonth=' . $month;
		} elseif($yearView){
			$bodyURL = WE_SHOP_MODULE_DIR . 'edit_shop_revenueTop.php?ViewYear=' . $yearView;
		} else {
			$bodyURL = WE_SHOP_MODULE_DIR . 'edit_shop_frameset.php?bid=' . $bid;
		}

		return $this->getHTMLDocument(
				we_html_element::htmlBody(array('style' => 'position: fixed; top: 0px; left: 0px; right: 0px; bottom: 0px; border: 0px none;'), we_html_element::htmlIFrame('edheader', $this->frameset . '?pnt=edheader&home=' . $home . '&mid=' . $mid . $yearView . '&bid=' . $bid, 'position: absolute; top: 0px; left: 0px; right: 0px; height: 40px; overflow: hidden;') .
					we_html_element::htmlIFrame('edbody', $bodyURL . '&pnt=edbody', 'position: absolute; top: 40px; bottom: 0px; left: 0px; right: 0px; overflow: auto;', 'border:0px;width:100%;height:100%;overflow: auto;')
				)
		);
	}

	function getHTMLEditorTop(){// TODO: merge getHTMLRight and getHTMLRightTop
		$DB_WE = $this->db;

		$home = we_base_request::_(we_base_request::BOOL, "home");
		$mid = we_base_request::_(we_base_request::INT, "mid", 0);
		$bid = we_base_request::_(we_base_request::INT, "bid", 0);

		// config
		$feldnamen = explode('|', f('SELECT strFelder FROM ' . WE_SHOP_PREFS_TABLE . ' WHERE strDateiname="shop_pref"', '', $DB_WE));
		for($i = 0; $i <= 3; $i++){
			$feldnamen[$i] = isset($feldnamen[$i]) ? $feldnamen[$i] : '';
		}
		$fe = explode(',', $feldnamen[3]);

		$classid = $fe[0];


		$resultO = array_shift($fe);

		// wether the resultset ist empty?
		$resultD = f('SELECT 1 FROM ' . LINK_TABLE . ' WHERE Name="' . $DB_WE->escape(WE_SHOP_TITLE_FIELD_NAME) . '" LIMIT 1', '', $DB_WE);

		if($home){
			$bodyURL = WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=mod_home&mod=shop'; //same as in getHTMLRight()
		} elseif($mid){
			// TODO::WANN UND VON WEM WIRD DAS AUFGERUFEN ????
			$bodyURL = WE_SHOP_MODULE_DIR . 'edit_shop_overviewTop.php?mid=' . $mid;
		} elseif($resultD && !$resultO){ // docs but no objects
			$bodyURL = 'edit_shop_article_extend.php?typ=document';
		} elseif(!$resultD && $resultO){ // no docs but objects
			$bodyURL = 'edit_shop_article_extend.php?typ=object&ViewClass=' . $classid;
		} elseif($resultD && $resultO){
			$bodyURL = 'edit_shop_article_extend.php?typ=document';
		}

		$frameset = new we_html_frameset(array("framespacing" => 0, "border" => 0, "frameborder" => "no"));
		$frameset->setAttributes(array("rows" => "40,*"));
		$frameset->addFrame(array('src' => 'edit_shop_frameset.php?pnt=edheader&top=1&home=' . $home . '&mid=' . $mid . '&bid=' . $bid . '&typ=object&ViewClass=' . $classid, 'name' => 'edheader', 'noresize' => null, 'scrolling' => 'no'));
		$frameset->addFrame(array('src' => $bodyURL, 'name' => 'edbody', 'scrolling' => 'auto'));

		$body = $frameset->getHtml();

		return $this->getHTMLDocument($body);
	}

	protected function getHTMLEditorHeader(){
		$DB_WE = $this->db;
		if(we_base_request::_(we_base_request::BOOL, 'home')){
			return $this->getHTMLDocument('<body bgcolor="#F0EFF0"></body></html>');
		}

		if(we_base_request::_(we_base_request::BOOL, 'top')){
			return $this->getHTMLEditorHeaderTop();
		}

		$bid = we_base_request::_(we_base_request::INT, 'bid', 0);

		$hash = getHash('SELECT IntCustomerID,DATE_FORMAT(DateOrder,"' . g_l('date', '[format][mysqlDate]') . '") AS d FROM ' . SHOP_TABLE . ' WHERE IntOrderID=' . $bid, $DB_WE);
		$cid = $hash['IntCustomerID'];
		$cdat = $hash['d'];
		$we_tabs = new we_tabs();

		if(isset($_REQUEST["mid"]) && $_REQUEST["mid"] && $_REQUEST["mid"] != '00'){
			$we_tabs->addTab(new we_tab('#', g_l('tabs', '[module][overview]'), we_tab::ACTIVE, 0));
		} else {
			$we_tabs->addTab(new we_tab('#', g_l('tabs', '[module][orderdata]'), we_tab::ACTIVE, "setTab(0);"));
			$we_tabs->addTab(new we_tab("#", g_l('tabs', '[module][orderlist]'), we_tab::NORMAL, "setTab(1);"));
		}

		$textPre = g_l('modules_shop', $bid > 0 ? '[orderList][order]' : '[order_view]');
		$textPost = isset($_REQUEST['mid']) && $_REQUEST['mid'] > 0 ? (strlen($_REQUEST['mid']) > 5 ? g_l('modules_shop', '[month][' . substr($_REQUEST['mid'], 0, -5) . ']') . " " . substr($_REQUEST['mid'], -5, 4) : substr($_REQUEST['mid'], 1)) : ($bid ? sprintf(g_l('modules_shop', '[orderNo]'), $bid, $cdat) : '');
		$we_tabs->onResize();

		$tab_head = $we_tabs->getHeader() . we_html_element::jsElement('
function setTab(tab) {
	switch (tab) {
		case 0:
			parent.edbody.document.location = "edit_shop_frameset.php?pnt=edbody&bid=' . $bid . '";
			break;
		case 1:
			parent.edbody.document.location = "edit_shop_orderlist.php?cid=' . $cid . '";
			break;
	}
}

top.content.hloaded = 1;
		');

		$tab_body_content = '<div id="main" >' . we_html_tools::getPixel(100, 3) . '<div style="margin:0px;padding-left:10px;" id="headrow"><nobr><b>' . str_replace(" ", "&nbsp;", $textPre) . ':&nbsp;</b><span id="h_path" class="header_small"><b id="titlePath">' . str_replace(" ", "&nbsp;", $textPost) . '</b></span></nobr></div>' . we_html_tools::getPixel(100, 3) .
			$we_tabs->getHTML() .
			'</div>';
		$tab_body = we_html_element::htmlBody(array("onresize" => "setFrameSize()", "onload" => "setFrameSize()", "bgcolor" => "#FFFFFF", "background" => IMAGE_DIR . "backgrounds/header_with_black_line.gif"), $tab_body_content);

		return $this->getHTMLDocument($tab_body, $tab_head);
	}

	function getHTMLEditorHeaderTop(){
		//$yid = we_base_request::_(we_base_request::INT, "ViewYear", date("Y"));
		//$bid = we_base_request::_(we_base_request::INT, "bid", 0);
		//$cid = f('SELECT IntCustomerID FROM ' . SHOP_TABLE . ' WHERE IntOrderID=' . intval($bid), "IntCustomerID", $this->db);
		$data = getHash("SELECT IntOrderID,DATE_FORMAT(DateOrder,'" . g_l('date', '[format][mysqlDate]') . "') AS orddate FROM " . SHOP_TABLE . ' GROUP BY IntOrderID ORDER BY IntID DESC LIMIT 1', $this->db);
		$headline = ($data ? sprintf(g_l('modules_shop', '[lastOrder]'), $data["IntOrderID"], $data["orddate"]) : '');

		/// config
		$feldnamen = explode('|', f('SELECT strFelder FROM ' . WE_SHOP_PREFS_TABLE . ' WHERE strDateiname="shop_pref"', '', $this->db));
		$fe = isset($feldnamen[3]) ? explode(",", $feldnamen[3]) : array(0);

		$classid = $fe[0];
		$resultO = array_shift($fe);

		// wether the resultset ist empty?
		$resultD = f('SELECT 1 FROM ' . LINK_TABLE . ' WHERE Name="' . WE_SHOP_TITLE_FIELD_NAME . '" LIMIT 1', '', $this->db);

		// grep the last element from the year-set, wich is the current year
		$yearTrans = f('SELECT DATE_FORMAT(DateOrder,"%Y") AS DateOrd FROM ' . SHOP_TABLE . ' ORDER BY DateOrd DESC LIMIT 1', 'DateOrd', $this->db);


		$we_tabs = new we_tabs();
		if(isset($_REQUEST["mid"]) && $_REQUEST["mid"]){
			$we_tabs->addTab(new we_tab("#", g_l('tabs', '[module][overview]'), we_tab::ACTIVE, "//"));
		} else {
			switch(true){
				default:
				case ($resultD):
					$we_tabs->addTab(new we_tab("#", g_l('tabs', '[module][admin_1]'), we_tab::ACTIVE, "setTab(0);"));
				case ($resultO):
					$we_tabs->addTab(new we_tab("#", g_l('tabs', '[module][admin_2]'), ($resultD ? we_tab::NORMAL : we_tab::ACTIVE), "setTab(1);"));
				case (isset($yearTrans) && $yearTrans != 0):
					$we_tabs->addTab(new we_tab("#", g_l('tabs', '[module][admin_3]'), we_tab::NORMAL, "setTab(2);"));
					break;
			}
		}
		$we_tabs->onResize();

		$tab_head = $we_tabs->getHeader() . we_html_element::jsElement('
function setTab(tab) {
	switch (tab) {
		case 0:
			parent.edbody.document.location = "edit_shop_article_extend.php?typ=document";
			break;
		case 1:
			parent.edbody.document.location = "edit_shop_article_extend.php?typ=object&ViewClass=' . $classid . '";
			break;
		' . (isset($yearTrans) ? '
		case 2:
			parent.edbody.document.location = "edit_shop_revenueTop.php?ViewYear=' . $yearTrans . '" // " + top.yearshop
			break;
		' : '') . '
	}
}
top.content.hloaded = 1;
		');

		$tab_body_content = '<div id="main" >' . we_html_tools::getPixel(100, 3) . '<div style="margin:0px;" id="headrow">&nbsp;' . we_html_element::htmlB($headline) . '</div>' . we_html_tools::getPixel(100, 3) .
			$we_tabs->getHTML() .
			'</div>';
		$tab_body = we_html_element::htmlBody(array('bgcolor' => '#FFFFFF', 'background' => IMAGE_DIR . 'backgrounds/header_with_black_line.gif'), $tab_body_content);

		return $this->getHTMLDocument($tab_body, $tab_head);
	}

	public function getHTML($what = ''){
		switch($what){
			case 'iconbar':
				return $this->getHTMLIconbar();
			default:
				return parent::getHTML($what);
		}
	}

}
