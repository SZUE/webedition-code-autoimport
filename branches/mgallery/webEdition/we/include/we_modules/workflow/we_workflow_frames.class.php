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
class we_workflow_frames extends we_modules_frame{

	public $module = "workflow";
	protected $useMainTree = false;

	function __construct(){
		parent::__construct(WE_WORKFLOW_MODULE_DIR . "edit_workflow_frameset.php");
		$this->View = new we_workflow_view();
	}

	function getHTML($what = '', $mode = 0, $type = 0){
		switch($what){
			case "edheader":
				return $this->getHTMLEditorHeader($mode);
			case "edfooter":
				return $this->getHTMLEditorFooter($mode);
			case "qlog":
				return $this->getHTMLLogQuestion();
			case "log":
				return $this->getHTMLLog($mode, $type);
			case 'edit':
				return $this->getHTMLEditorBody();
			default:
				return parent::getHTML($what);
		}
	}

	function getHTMLFrameset(){
		$extraHead = $this->getJSTreeCode() . $this->getJSCmdCode();
		return parent::getHTMLFrameset($extraHead);
	}

	function getJSTreeCode(){

		//start ex we_workflow_moduleFrames::getJSTreeCode()
		echo we_html_element::jsScript(JS_DIR . 'images.js') .
		we_html_element::jsScript(JS_DIR . 'windows.js');

		// TODO: move shared code for (some of the) modules-tree (not based on weTree!!) to new weModulesTree.class
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
			var table = "<?php echo USER_TABLE; ?>";

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
				fr.writeln("if(ct=='folder') top.content.we_cmd('workflow_edit',id,ct,table); else if(ct=='file') top.content.we_cmd('show_document',id,ct,table);");
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
					nf[ai].text = nf[ai].text.replace(/</g, "&lt;");
					nf[ai].text = nf[ai].text.replace(/>/g, "&gt;");
					if (nf[ai].typ == 'file') {
						if (ai == nf.laenge) {
							fr.write("&nbsp;&nbsp;<IMG SRC=<?php echo TREE_IMAGE_DIR; ?>kreuzungend.gif WIDTH=19 HEIGHT=18 align=absmiddle BORDER=0>");
						} else {
							fr.write("&nbsp;&nbsp;<IMG SRC=<?php echo TREE_IMAGE_DIR; ?>kreuzung.gif WIDTH=19 HEIGHT=18 align=absmiddle BORDER=0>");
						}
						if (nf[ai].name != -1) {
							fr.write("<a name='_" + nf[ai].name + "' href=\"javascript://\" onclick=\"doClick(" + nf[ai].name + ",'" + nf[ai].contentType + "','" + nf[ai].table + "');return true;\" BORDER=0>");
						}
						fr.write("<IMG SRC=<?php echo TREE_IMAGE_DIR; ?>icons/" + nf[ai].icon + " WIDTH=16 HEIGHT=18 align=absmiddle BORDER=0 alt=\"<?php #print g_l('tree',"[edit_statustext]");             ?>\">");
						fr.write("</a>");
						fr.write("&nbsp;<a name='_" + nf[ai].name + "' href=\"javascript://\" onclick=\"doClick(" + nf[ai].name + ",'" + nf[ai].contentType + "','" + nf[ai].table + "');return true;\">" + (parseInt(nf[ai].published) ? "" : "") + nf[ai].text + (parseInt(nf[ai].published) ? "" : "") + "</A>&nbsp;&nbsp;<br/>\n");
					} else {
						var newAst = zweigEintrag;

						var zusatz = (ai == nf.laenge ? "end" : "");

						if (nf[ai].offen == 0) {
							fr.write("&nbsp;&nbsp;<A href=\"javascript:top.content.openClose('" + nf[ai].name + "',1)\" BORDER=0><IMG SRC=<?php echo TREE_IMAGE_DIR; ?>auf" + zusatz + ".gif WIDTH=19 HEIGHT=18 align=absmiddle BORDER=0 Alt=\"<?php #print g_l('tree',"[open_statustext]")             ?>\"></A>");
							var zusatz2 = "";
						} else {
							fr.write("&nbsp;&nbsp;<A href=\"javascript:top.content.openClose('" + nf[ai].name + "',0)\" BORDER=0><IMG SRC=<?php echo TREE_IMAGE_DIR; ?>zu" + zusatz + ".gif WIDTH=19 HEIGHT=18 align=absmiddle BORDER=0 Alt=\"<?php #print g_l('tree',"[close_statustext]")             ?>\"></A>");
							var zusatz2 = "open";
						}
						fr.write("<a name='_" + nf[ai].name + "' href=\"javascript://\" onclick=\"doClick(" + nf[ai].name + ",'" + nf[ai].contentType + "','" + nf[ai].table + "');return true;\" BORDER=0>");
						fr.write("<IMG SRC=<?php echo TREE_IMAGE_DIR; ?>icons/workflow_folder" + zusatz2 + ".gif WIDTH=16 HEIGHT=18 align=absmiddle BORDER=0 Alt=\"<?php #print g_l('tree',"[edit_statustext]");             ?>\">");
						fr.write("</a>");
						fr.write("<A name='_" + nf[ai].name + "' HREF=\"javascript://\" onclick=\"doClick(" + nf[ai].name + ",'" + nf[ai].contentType + "','" + nf[ai].table + "');return true;\">");
						fr.write("&nbsp;<b>" + (!parseInt(nf[ai].published) ? "<font color=\"red\">" : "") + nf[ai].text + (parseInt(nf[ai].published) ? "</font>" : "") + "</b>");
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
				if (ct == "folder") {
					menuDaten.addSort(new dirEntry(icon, id, pid, txt, offen, ct, tab, pub));
				} else {
					menuDaten.addSort(new urlEntry(icon, id, pid, txt, ct, tab, pub));
				}
				drawEintraege();
			}

			function updateEntry(id, pid, text, pub) {
				var ai = 1;
				while (ai <= menuDaten.laenge) {
					if ((menuDaten[ai].typ == 'folder')) {
						if (menuDaten[ai].name == id) {
							menuDaten[ai].vorfahr = pid;
							menuDaten[ai].text = text;
							menuDaten[ai].published = pub;
						}
					}
					ai++;
				}
				drawEintraege();
			}

			function deleteEntry(id, type) {
				var ai = 1;
				var ind = 0;
				while (ai <= menuDaten.laenge) {
					if ((menuDaten[ai].typ == type)) {
						if (menuDaten[ai].name == id) {
							ind = ai;
							break;
						}
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
				/*if (status) {
				 if (!menuDaten[eintragsIndex].loaded) {
				 drawEintraege();
				 } else {
				 drawEintraege();
				 }
				 } else {*/
				drawEintraege();
				//}
			}

			function indexOfEntry(name) {
				var ai = 1;
				while (ai <= menuDaten.laenge) {
					if ((menuDaten[ai].typ == 'root') || (menuDaten[ai].typ == 'folder')) {
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
					if ((menuDaten[ai].typ == 'folder') || (menuDaten[ai].typ == 'file')) {
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
		//end ex we_workflow_moduleFrames::getJSTreeCode()

		$out = '
		function loadData(){
			menuDaten.clear();';

		$startloc = 0;

		$out.="startloc=" . $startloc . ";";
		$this->db->query('SELECT * FROM ' . WORKFLOW_TABLE . ' ORDER BY Text ASC');
		while($this->db->next_record()){
			$this->View->workflowDef = new we_workflow_workflow();
			$this->View->workflowDef->load($this->db->f('ID'));
			$out.="  menuDaten.add(new dirEntry('folder','" . $this->View->workflowDef->ID . "','0','" . oldHtmlspecialchars(addslashes($this->View->workflowDef->Text)) . "',false,'folder','workflowDef','" . $this->View->workflowDef->Status . "'));";

			foreach($this->View->workflowDef->documents as $v){
				$out.="  menuDaten.add(new urlEntry('" . $v["Icon"] . "','" . $v["ID"] . "','" . $this->View->workflowDef->ID . "','" . oldHtmlspecialchars(addslashes($v["Text"])) . "','file','" . FILE_TABLE . "',1));";
			}
		}

		$out.='}';
		echo we_html_element::jsElement($out);
	}

	function getJSCmdCode(){
		echo $this->View->getJSTopCode();
	}

	protected function getHTMLEditorHeader($mode = 0){
		if(we_base_request::_(we_base_request::BOOL, "home")){
			return $this->getHTMLDocument(we_html_element::htmlBody(array("bgcolor" => "F0EFF0"), ""));
		}

		$page = we_base_request::_(we_base_request::INT, "page", 0);
		$text = we_base_request::_(we_base_request::RAW, 'txt', g_l('modules_workflow', '[new_workflow]'));

		$we_tabs = new we_tabs();

		if($mode == 0){
			$we_tabs->addTab(new we_tab("#", g_l('tabs', '[module][properties]'), we_tab::NORMAL, "setTab(0);", array("id" => "tab_0")));
			$we_tabs->addTab(new we_tab("#", g_l('tabs', '[module][overview]'), we_tab::NORMAL, "setTab(1);", array("id" => "tab_1")));
		} else {
			$we_tabs->addTab(new we_tab("#", g_l('tabs', '[editor][information]'), we_tab::ACTIVE, "//", array("id" => "tab_0")));
		}

		$we_tabs->onResize();
		$tab_header = $we_tabs->getHeader('', 22);
		$textPre = g_l('modules_workflow', ($mode == 1 ? '[document]' : '[workflow]'));
		$textPost = '/' . $text;

		$extraHead = we_html_element::jsElement('
function setTab(tab){
	switch(tab){
		case 0:
			top.content.editor.edbody.we_cmd("switchPage",0);
			break;
		case 1:
			top.content.editor.edbody.we_cmd("switchPage",1);
			break;
	}
}

top.content.hloaded=1;
		') . $tab_header;

		$mainDiv = we_html_element::htmlDiv(array('id' => 'main'), we_html_tools::getPixel(100, 3) .
						we_html_element::htmlDiv(array('style' => 'margin:0px;padding-left:10px;', 'id' => 'headrow'), we_html_element::htmlNobr(
										we_html_element::htmlB(oldHtmlspecialchars($textPre) . ':&nbsp;') .
										we_html_element::htmlSpan(array('id' => 'h_path', 'class' => 'header_small'), '<b id="titlePath">' . oldHtmlspecialchars($textPost) . '</b>')
						)) .
						we_html_tools::getPixel(100, 3) .
						$we_tabs->getHTML()
		);

		$body = we_html_element::htmlBody(array(
					'onresize' => 'setFrameSize()',
					'onload' => 'setFrameSize()',
					'bgcolor' => 'white',
					'background' => IMAGE_DIR . 'backgrounds/header_with_black_line.gif',
					'marginwidth' => 0,
					'marginheight' => 0,
					'leftmargin' => 0,
					'topmargin' => 0,
						), $mainDiv . we_html_element::jsElement('document.getElementById("tab_' . $page . '").className="tabActive";')
		);

		return $this->getHTMLDocument($body, $extraHead);
	}

	protected function getHTMLEditorFooter($mode = 0){
		if(we_base_request::_(we_base_request::BOOL, "home")){
			return $this->getHTMLDocument(we_html_element::htmlBody(array("bgcolor" => "#EFF0EF"), ""));
		}

		$extraHead = we_html_element::jsElement('
			function setStatusCheck(){
				var a=document.we_form.status_workflow;
				var b;
				if(top.content.editor.edbody.loaded) b=top.content.editor.edbody.getStatusContol();
				else setTimeout("setStatusCheck()",100);

				if(b==1) a.checked=true;
				else a.checked=false;
			}
			function we_save() {
				top.content.we_cmd("save_workflow");
			}
		');

		$table1 = new we_html_table(array("border" => 0, "cellpadding" => 0, "cellspacing" => 0, "width" => 300), 1, 1);
		$table1->setCol(0, 0, array("nowrap" => null, "valign" => "top"), we_html_tools::getPixel(1, 10));

		$table2 = new we_html_table(array('border' => 0, 'cellpadding' => 0, 'cellspacing' => 0, 'width' => 300), 1, 3);
		//$table2->setRow(0, array('valign' => 'middle'));
		$table2->setCol(0, 0, array('nowrap' => null), we_html_tools::getPixel(15, 5));
		$table2->setCol(0, 1, array('nowrap' => null), we_html_button::create_button('save', 'javascript:we_save()'));
		$table2->setCol(0, 2, array('nowrap' => null, 'class' => 'defaultfont'), $this->View->getStatusHTML());

		$body = we_html_element::htmlBody(array(
					'bgcolor' => 'white',
					'background' => IMAGE_DIR . 'edit/editfooterback.gif',
					'style' => 'margin: 0px 0px 0px 0px;',
					'onload' => ($mode == 0 ? 'setStatusCheck()' : '')
						), we_html_element::htmlForm($attribs = array(), $table1->getHtml() . $table2->getHtml())
		);

		return $this->getHTMLDocument($body, $extraHead);
	}

	function getHTMLLog($docID, $type = 0){
		$extraHead = we_html_element::jsElement('self.focus();');
		$body = we_html_element::htmlBody(array('class' => 'weDialogBody'), we_workflow_view::getLogForDocument($docID, $type));

		return $this->getHTMLDocument($body, $extraHead);
	}

	function getHTMLCmd(){
		$form = we_html_element::htmlForm(array('name' => 'we_form'), $this->View->htmlHidden("wcmd", "") . $this->View->htmlHidden("wopt", ""));
		$body = we_html_element::htmlBody(array(), $form);

		return $this->getHTMLDocument($body, $this->View->getCmdJS());
	}

	function getHTMLLogQuestion(){
		$form = we_html_element::htmlForm(array('name' => 'we_form'), $this->View->getLogQuestion());
		$body = we_html_element::htmlBody(array(), $form);

		return $this->getHTMLDocument($body);
	}

}
