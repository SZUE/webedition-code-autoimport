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
class we_banner_frames extends we_modules_frame{

	var $edit_cmd = "banner_edit";
	protected $useMainTree = false;
	protected $treeDefaultWidth = 224;

	function __construct($frameset){
		parent::__construct($frameset);
		$this->View = new we_banner_view();
		$this->module = 'banner';
	}

	function getHTML($what = '', $mode = ''){
		switch($what){
			case "edheader":
				return $this->getHTMLEditorHeader($mode);
			case "edfooter":
				return $this->getHTMLEditorFooter($mode);
			default:
				return parent::getHTML($what);
		}
	}

	function getHTMLFrameset(){
		$extraHead = $this->getJSTreeCode();
		return parent::getHTMLFrameset($extraHead);
	}

	protected function getHTMLEditor(){
		return parent::getHTMLEditor('&home=1');
	}

	function getJSTreeCode(){//TODO: move (as in all modules...) to some future moduleTree class
		//start of code from ex class weModuleBannerFrames
		echo we_html_element::jsScript(JS_DIR . 'images.js') .
		we_html_element::jsScript(JS_DIR . 'windows.js');
		?>
		<script type="text/javascript"><!--
			var loaded = 0;
			var hot = 0;
			var hloaded = 0;

			function setHot() {
				hot = 1;
			}

			function usetHot() {
				hot = 0;
			}

			var menuDaten = new container();
			var count = 0;
			var folder = 0;
			var table = "<?php echo BANNER_TABLE; ?>";

			function drawEintraege() {
				fr = top.content.tree.document;
				fr.open();
				fr.writeln("<html><head>");
				fr.writeln("<?php echo str_replace(array('script', '"'), array('scr"+"ipt', '\''), we_html_tools::getJSErrorHandler());?>");
				fr.writeln("<script type=\"text/javascript\">");
				fr.writeln("var clickCount=0;");
				fr.writeln("var wasdblclick=0;");
				fr.writeln("var tout=null;");
				fr.writeln("function doClick(id,ct,table){");
				//fr.writeln("if(ct=='folder') top.content.we_cmd('newsletter_edit',id,ct,table); else if(ct=='file') top.content.we_cmd('show_document',id,ct,table);");
				fr.writeln("top.content.we_cmd('<?php echo $this->edit_cmd; ?>',id,ct,table);");
				fr.writeln("}");
				fr.writeln("top.content.loaded=1;");
				fr.writeln("</" + "script>");
				fr.writeln('<?php echo STYLESHEET_SCRIPT; ?>');
				fr.write("</head>\n");
				fr.write("<body bgcolor=\"#F3F7FF\" link=\"#000000\" alink=\"#000000\" vlink=\"#000000\" leftmargin=5 topmargin=5 marginheight=5 marginwidth=5>\n");
				fr.write("<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\"><tr><td class=\"tree\">\n<nobr>\n");
				zeichne(top.content.startloc, "");
				fr.write("</nobr>\n</td></tr></table>\n");
				fr.write("</body>\n</html>");
				fr.close();
			}


			function zeichne(startEntry, zweigEintrag) {
				var nf = search(startEntry);
				var ai = 1;
				while (ai <= nf.laenge) {
					fr.write(zweigEintrag);

					if (nf[ai].typ == 'file') {
						if (ai == nf.laenge) {
							fr.write("&nbsp;&nbsp;<img src=<?php echo TREE_IMAGE_DIR; ?>kreuzungend.gif width=19 height=18 align=absmiddle border=0>");
						} else {
							fr.write("&nbsp;&nbsp;<img src=<?php echo TREE_IMAGE_DIR; ?>kreuzung.gif width=19 height=18 align=absmiddle border=0>");
						}
						if (nf[ai].name != -1) {
							fr.write("<a name='_" + nf[ai].name + "' href=\"javascript://\" onclick=\"doClick(" + nf[ai].name + ",'" + nf[ai].contentType + "','" + nf[ai].table + "');return true;\" BORDER=0>");
						}
						fr.write("<IMG SRC=<?php echo TREE_IMAGE_DIR; ?>icons/" + nf[ai].icon + " WIDTH=16 HEIGHT=18 align=absmiddle BORDER=0 alt=\"<?php #print g_l('tree',"[edit_statustext]");                         ?>\">");
						fr.write("</a>");
						fr.write("&nbsp;<a name='_" + nf[ai].name + "' href=\"javascript://\" onclick=\"doClick(" + nf[ai].name + ",'" + nf[ai].contentType + "','" + nf[ai].table + "');return true;\">" + (parseInt(nf[ai].published) ? "" : "") + nf[ai].text + (parseInt(nf[ai].published) ? "" : "") + "</a>&nbsp;&nbsp;<br/>\n");
					} else {
						var newAst = zweigEintrag;

						var zusatz = (ai == nf.laenge) ? "end" : "";

						if (nf[ai].offen == 0) {
							fr.write("&nbsp;&nbsp;<A href=\"javascript:top.content.openClose('" + nf[ai].name + "',1)\" BORDER=0><IMG SRC=<?php echo TREE_IMAGE_DIR; ?>auf" + zusatz + ".gif WIDTH=19 HEIGHT=18 align=absmiddle BORDER=0 Alt=\"<?php #print g_l('tree',"[open_statustext]")                         ?>\"></A>");
							var zusatz2 = "";
						} else {
							fr.write("&nbsp;&nbsp;<A href=\"javascript:top.content.openClose('" + nf[ai].name + "',0)\" BORDER=0><IMG SRC=<?php echo TREE_IMAGE_DIR; ?>zu" + zusatz + ".gif WIDTH=19 HEIGHT=18 align=absmiddle BORDER=0 Alt=\"<?php #print g_l('tree',"[close_statustext]")                         ?>\"></A>");
							var zusatz2 = "open";
						}
						fr.write("<a name='_" + nf[ai].name + "' href=\"javascript://\" onclick=\"doClick(" + nf[ai].name + ",'" + nf[ai].contentType + "','" + nf[ai].table + "');return true;\" border='0'>");
						fr.write("<img src=<?php echo TREE_IMAGE_DIR; ?>icons/" + nf[ai].icon.replace(/\.gif/, "") + zusatz2 + ".gif WIDTH=16 HEIGHT=18 align=absmiddle BORDER=0 Alt=\"<?php #print g_l('tree',"[edit_statustext]");                         ?>\">");
						fr.write("</a>");
						fr.write("<a name='_" + nf[ai].name + "' href=\"javascript://\" onclick=\"doClick(" + nf[ai].name + ",'" + nf[ai].contentType + "','" + nf[ai].table + "');return true;\">");
						fr.write("&nbsp;<b>" + nf[ai].text + "</b>");
						fr.write("</a>");
						fr.write("&nbsp;&nbsp;<br/>\n");
						if (nf[ai].offen) {
							if (ai == nf.laenge) {
								newAst = newAst + "<IMG SRC=<?php echo TREE_IMAGE_DIR; ?>leer.gif WIDTH=19 HEIGHT=18 align=absmiddle BORDER=0>";
							} else {
								newAst = newAst + "<IMG SRC=<?php echo TREE_IMAGE_DIR; ?>strich2.gif WIDTH=19 HEIGHT=18 align=absmiddle BORDER=0>";
							}
							zeichne(nf[ai].name, newAst);
						}
					}
					ai++;
				}
			}


			function makeNewEntry(icon, id, pid, txt, offen, ct, tab, pub) {
				if (ct === "folder") {
					menuDaten.addSort(new dirEntry(icon, id, pid, txt, offen, ct, tab, pub));
				} else {
					menuDaten.addSort(new urlEntry(icon, id, pid, txt, ct, tab, pub));
				}
				drawEintraege();
			}

			function updateEntry(id, pid, text, pub) {
				var ai = 1;
				while (ai <= menuDaten.laenge) {
					if (menuDaten[ai].name == id) {
						menuDaten[ai].vorfahr = pid;
						menuDaten[ai].text = text;
						menuDaten[ai].published = 1;
					}
					ai++;
				}
				drawEintraege();
			}

			function deleteEntry(id, type) {
				var ai = 1;
				var ind = 0;
				while (ai <= menuDaten.laenge) {
					if ((menuDaten[ai].typ == type))
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
					if ((menuDaten[ai].typ == 'folder') || (menuDaten[ai].typ == 'file'))
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

			function urlEntry(icon, name, vorfahr, text, contentType, table, published) {
				this.icon = icon;
				this.name = name;
				this.vorfahr = vorfahr;
				this.text = text;
				this.typ = 'file';
				this.checked = false;
				this.contentType = contentType;
				this.table = table;
				this.published = published;
				return this;
			}

			function start() {
				loadData();
				drawEintraege();
			}

			var startloc = 0;

			self.focus();
			//-->
		</script>
		<?php
		//end of code from ex class weModuleBannerFrames

		$startloc = 0;

		$out = '
		function loadData(){
			menuDaten.clear();
			startloc=' . $startloc . ';';

		$this->db->query('SELECT ID,ParentID,Path,Text,Icon,IsFolder,ABS(text) as Nr, (text REGEXP "^[0-9]") as isNr FROM ' . BANNER_TABLE . ' ORDER BY isNr DESC,Nr,Text');
		while($this->db->next_record()){
			$ID = $this->db->f("ID");
			$ParentID = $this->db->f("ParentID");
			$Path = $this->db->f("Path");
			$Text = addslashes($this->db->f("Text"));
			$Icon = $this->db->f("Icon");
			$IsFolder = $this->db->f("IsFolder");

			$out.=($IsFolder ?
							"  menuDaten.add(new dirEntry('" . $Icon . "'," . $ID . "," . $ParentID . ",'" . $Text . "',0,'folder','" . BANNER_TABLE . "',1));" :
							"  menuDaten.add(new urlEntry('" . $Icon . "'," . $ID . "," . $ParentID . ",'" . $Text . "','file','" . BANNER_TABLE . "',1));");
		}

		$out.='}';
		echo we_html_element::jsElement($out);
	}

	function getJSCmdCode(){
		echo $this->View->getJSTopCode();
	}

	protected function getHTMLEditorHeader($mode = 0){
		if(we_base_request::_(we_base_request::BOOL, "home")){
			return $this->getHTMLDocument(we_html_element::htmlBody(array('bgcolor' => '#F0EFF0'), ''));
		}

		$isFolder = we_base_request::_(we_base_request::BOOL, "isFolder");

		$page = we_base_request::_(we_base_request::INT, "page", 0);

		$headline1 = g_l('modules_banner', $isFolder ? '[group]' : '[banner]');
		$text = we_base_request::_(we_base_request::STRINGC, "txt", g_l('modules_banner', ($isFolder ? '[newbannergroup]' : '[newbanner]')));

		$we_tabs = new we_tabs();

		if($isFolder){
			$we_tabs->addTab(new we_tab("#", g_l('tabs', '[module][properties]'), we_tab::ACTIVE, "setTab(0);"));
		} else {
			$we_tabs->addTab(new we_tab("#", g_l('tabs', '[module][properties]'), ($page == 0 ? we_tab::ACTIVE : we_tab::NORMAL), "setTab(0);"));
			$we_tabs->addTab(new we_tab("#", g_l('tabs', '[module][placement]'), ($page == 1 ? we_tab::ACTIVE : we_tab::NORMAL), "setTab(1);"));
			$we_tabs->addTab(new we_tab("#", g_l('tabs', '[module][statistics]'), ($page == 2 ? we_tab::ACTIVE : we_tab::NORMAL), "setTab(2);"));
		}

		$we_tabs->onResize('header');
		$tab_head = $we_tabs->getHeader();

		$extraHead = $tab_head .
				we_html_element::jsElement('
				function setTab(tab){
					switch(tab){
						case ' . we_banner_banner::PAGE_PROPERTY . ':
						case ' . we_banner_banner::PAGE_PLACEMENT . ':
						case ' . we_banner_banner::PAGE_STATISTICS . ':
							top.content.editor.edbody.we_cmd("switchPage",tab);
							break;
					}
				}
				top.content.hloaded=1;
			');

		//TODO: we have the following body in several modules!
		$body = we_html_element::htmlBody(array('onresize' => 'setFrameSize()', 'onload' => 'setFrameSize()', 'bgcolor' => 'white', 'background' => IMAGE_DIR . 'backgrounds/header_with_black_line.gif', 'marginwidth' => 0, 'marginheight' => 0, 'leftmargin' => 0, 'topmargin' => 0), we_html_element::htmlDiv(array('id' => 'main'), we_html_tools::getPixel(100, 3) .
								we_html_element::htmlDiv(array('style' => 'margin:0px;padding-left:10px;', 'id' => 'headrow'), we_html_element::htmlNobr(
												we_html_element::htmlB(str_replace(" ", "&nbsp;", $headline1) . ':&nbsp;') .
												we_html_element::htmlSpan(array('id' => 'h_path', 'class' => 'header_small'), '<b id="titlePath">' . str_replace(" ", "&nbsp;", $text) . '</b>'
												)
										)
								) .
								we_html_tools::getPixel(100, 3) .
								$we_tabs->getHTML()
						)
		);

		return $this->getHTMLDocument($body, $extraHead);
	}

	protected function getHTMLEditorFooter($mode = 0){
		if(we_base_request::_(we_base_request::BOOL, 'home')){
			return $this->getHTMLDocument(we_html_element::htmlBody(array('bgcolor' => '#F0EFF0'), ''));
		}

		echo we_html_tools::getHtmlTop() .
		STYLESHEET;

		$this->View->getJSFooterCode();

		$extraHead = $this->View->getJSFooterCode() . we_html_element::jsElement('
			function sprintf(){
				if (!arguments || arguments.length < 1) return;

				var argum = arguments[0];
				var regex = /([^%]*)%(%|d|s)(.*)/;
				var arr = new Array();
				var iterator = 0;
				var matches = 0;

				while (arr=regex.exec(argum)){
					var left = arr[1];
					var type = arr[2];
					var right = arr[3];

					matches++;
					iterator++;

					var replace = arguments[iterator];

					if (type=="d") replace = parseInt(param) ? parseInt(param) : 0;
					else if (type=="s") replace = arguments[iterator];
					argum = left + replace + right;
				}
				return argum;
			}

			function we_save() {
				var acLoopCount=0;
				var acIsRunning = false;
				if(!!top.content.editor.edbody.YAHOO && !!top.content.editor.edbody.YAHOO.autocoml){
					while(acLoopCount<20 && top.content.editor.edbody.YAHOO.autocoml.isRunnigProcess()){
						acLoopCount++;
						acIsRunning = true;
						setTimeout("we_save()",100);
					}
					if(!acIsRunning) {
						if(top.content.editor.edbody.YAHOO.autocoml.isValid()) {
							_we_save();
						} else {
							' . we_message_reporting::getShowMessageCall(g_l('alert', '[save_error_fields_value_not_valid]'), we_message_reporting::WE_MESSAGE_ERROR) . '
						}
					}
				} else {
					_we_save();
				}
			}
		');

		return parent::getHTMLEditorFooter('save_banner', $extraHead);
	}

	function getHTMLCmd(){
		return $this->getHTMLDocument(we_html_element::htmlBody(array(), we_html_element::htmlForm(array(), $this->View->htmlHidden("ncmd", "") .
										$this->View->htmlHidden("nopt", "")
								)
						), $this->View->getJSCmd());
	}

	function getHTMLDCheck(){
		return $this->getHTMLDocument(we_html_element::htmlBody(array(), $this->View->getHTMLDCheck()), we_html_element::jsElement('self.focus();'));
	}

}
