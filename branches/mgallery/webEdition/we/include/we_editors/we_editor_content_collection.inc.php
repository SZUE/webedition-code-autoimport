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
echo we_html_tools::getHtmlTop();
require_once(WE_INCLUDES_PATH . 'we_editors/we_editor_script.inc.php');
echo STYLESHEET .
we_html_element::jsScript(JS_DIR . 'windows.js') . 
we_html_element::jsScript(JS_DIR . 'we_editor_collectionContent.js');
?>

</head>
<body class="weEditorBody">
	<form name="we_form"><?php echo we_class::hiddenTrans(); ?>
		<?php echo $GLOBALS['we_doc']->formInputField('', 'Collection', 'Collection', 40, 410); ?>
		<input type="hidden" name="we_complete_request" value="1"/>
	</form>

	<br/><br/>
ATTENTION: this is a dummy to develop editor's JS
<div id="content_table">
	<div class="drop_reference" draggable="true" ondragstart="drag(event)" id="drag1" ondrop="drop(event)" ondragover="allowDrop(event)" ondragenter="enterDrag(event)">
		<table cellspacing="0" draggable="false">
		<tr style="background-color: lightgray;">
			<td width="60px" style="border-top:2px solid white">elem 1</td>
			<td width="200px" style="border-top:2px solid white"><input class="testclass gugus" type="text" value="hier rein der dateiname"></td>
			<td width="" style="border-top:2px solid white">
				<table style="border-style:none;padding:0px;border-spacing:0px;">
					<tr>
						<td style="padding-right:5px;" class="weEditmodeStyle"><button type="button"  title="Listenelement hinzufügen" id="webtn_add_listelement_d9b5fc928184865c329077335803424b" class="weBtn"  style="" onclick="_EditorFrame.setEditorIsHot(true);"><img src="/webEdition/images/button/icons/add_listelement.gif" class="weBtnImage" alt="-"/></button></td>
						<td style="padding-right:5px;" class="weEditmodeStyle"><select name="a164ca58a312dbde4c75ba7b89fabe7a_1"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option></select></td>
						<td style="padding-right:5px;" class="weEditmodeStyle"><button type="button"  title="Nach oben" id="webtn_direction_up_886bcb21fb283632cacfd27f126f20ef" class="weBtn"  style="" onclick="moveUp(this);"><img src="/webEdition/images/button/icons/direction_up.gif" class="weBtnImage" alt="-"/></button></td>
						<td style="padding-right:5px;" class="weEditmodeStyle"><button type="button"  title="Nach unten" id="webtn_direction_down_c981d94793884ae5c9e8fe8ef96cc6ff" class="weBtn"  style="" onclick="moveDown(this); "><img src="/webEdition/images/button/icons/direction_down.gif" class="weBtnImage" alt="-"/></button></td>
						<td class="weEditmodeStyle"><button type="button"  title="Löschen" id="webtn_function_trash_fff8e7fdeebb71068524e734f8330adf" class="weBtn"  style="" onclick="setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('delete_list','liste','1','blk_liste__2',1)"><img src="/webEdition/images/button/icons/function_trash.gif" class="weBtnImage" alt="-"/></button></td>
					</tr>
				</table>
			</td>
		</tr>
		</table>
	</div>

	<div class="drop_reference" draggable="true" ondragstart="drag(event)" id="drag2" ondrop="drop(event)" ondragover="allowDrop(event)" ondragenter="enterDrag(event)">
		<table cellspacing="0" draggable="false">
		<tr style="background-color: lightgray;">
			<td width="60px" style="border-top:2px solid white">elem 2</td>
			<td width="200px" style="border-top:2px solid white"><input type="text" value="hier rein der dateiname"></td>
			<td width="" style="border-top:2px solid white">
				<table style="border-style:none;padding:0px;border-spacing:0px;">
					<tr>
						<td style="padding-right:5px;" class="weEditmodeStyle"><button type="button"  title="Listenelement hinzufügen" id="webtn_add_listelement_d9b5fc928184865c329077335803424b" class="weBtn"  style="" onclick="setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('insert_entry_at_list','liste',1,document.we_form.elements['a164ca58a312dbde4c75ba7b89fabe7a_1'].options[document.we_form.elements['a164ca58a312dbde4c75ba7b89fabe7a_1'].selectedIndex].text)"><img src="/webEdition/images/button/icons/add_listelement.gif" class="weBtnImage" alt="-"/></button></td>
						<td style="padding-right:5px;" class="weEditmodeStyle"><select name="a164ca58a312dbde4c75ba7b89fabe7a_1"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option></select></td>
						<td style="padding-right:5px;" class="weEditmodeStyle"><button type="button"  title="Nach oben" id="webtn_direction_up_886bcb21fb283632cacfd27f126f20ef" class="weBtn"  style="" onclick="moveUp(this);"><img src="/webEdition/images/button/icons/direction_up.gif" class="weBtnImage" alt="-"/></button></td>
						<td style="padding-right:5px;" class="weEditmodeStyle"><button type="button"  title="Nach unten" id="webtn_direction_down_c981d94793884ae5c9e8fe8ef96cc6ff" class="weBtn"  style="" onclick="moveDown(this); "><img src="/webEdition/images/button/icons/direction_down.gif" class="weBtnImage" alt="-"/></button></td>
						<td class="weEditmodeStyle"><button type="button"  title="Löschen" id="webtn_function_trash_fff8e7fdeebb71068524e734f8330adf" class="weBtn"  style="" onclick="setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('delete_list','liste','1','blk_liste__2',1)"><img src="/webEdition/images/button/icons/function_trash.gif" class="weBtnImage" alt="-"/></button></td>
					</tr>
				</table>
			</td>
		</tr>
		</table>
	</div>

	<div class="drop_reference" draggable="true" ondragstart="drag(event)" id="drag3" ondrop="drop(event)" ondragover="allowDrop(event)" ondragenter="enterDrag(event)">
		<table cellspacing="0" draggable="false">
		<tr style="background-color: lightgray;">
			<td width="60px" style="border-top:2px solid white">elem 3</td>
			<td width="200px" style="border-top:2px solid white"><input type="text" value="hier rein der dateiname"></td>
			<td width="" style="border-top:2px solid white">
				<table style="border-style:none;padding:0px;border-spacing:0px;">
					<tr>
						<td style="padding-right:5px;" class="weEditmodeStyle"><button type="button"  title="Listenelement hinzufügen" id="webtn_add_listelement_d9b5fc928184865c329077335803424b" class="weBtn"  style="" onclick="setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('insert_entry_at_list','liste',1,document.we_form.elements['a164ca58a312dbde4c75ba7b89fabe7a_1'].options[document.we_form.elements['a164ca58a312dbde4c75ba7b89fabe7a_1'].selectedIndex].text)"><img src="/webEdition/images/button/icons/add_listelement.gif" class="weBtnImage" alt="-"/></button></td>
						<td style="padding-right:5px;" class="weEditmodeStyle"><select name="a164ca58a312dbde4c75ba7b89fabe7a_1"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option></select></td>
						<td style="padding-right:5px;" class="weEditmodeStyle"><button type="button"  title="Nach oben" id="webtn_direction_up_886bcb21fb283632cacfd27f126f20ef" class="weBtn"  style="" onclick="moveUp(this);"><img src="/webEdition/images/button/icons/direction_up.gif" class="weBtnImage" alt="-"/></button></td>
						<td style="padding-right:5px;" class="weEditmodeStyle"><button type="button"  title="Nach unten" id="webtn_direction_down_c981d94793884ae5c9e8fe8ef96cc6ff" class="weBtn"  style="" onclick="moveDown(this); "><img src="/webEdition/images/button/icons/direction_down.gif" class="weBtnImage" alt="-"/></button></td>
						<td class="weEditmodeStyle"><button type="button"  title="Löschen" id="webtn_function_trash_fff8e7fdeebb71068524e734f8330adf" class="weBtn"  style="" onclick="setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('delete_list','liste','1','blk_liste__2',1)"><img src="/webEdition/images/button/icons/function_trash.gif" class="weBtnImage" alt="-"/></button></td>
					</tr>
				</table>
			</td>
		</tr>
		</table>
	</div>

	<div class="drop_reference" draggable="true" ondragstart="drag(event)" id="drag4" ondrop="drop(event)" ondragover="allowDrop(event)" ondragenter="enterDrag(event)">
		<table cellspacing="0" draggable="false">
		<tr style="background-color: lightgray;">
			<td width="60px" style="border-top:2px solid white">elem 4</td>
			<td width="200px" style="border-top:2px solid white"><input type="text" value="hier rein der dateiname"></td>
			<td width="" style="border-top:2px solid white">
				<table style="border-style:none;padding:0px;border-spacing:0px;">
					<tr>
						<td style="padding-right:5px;" class="weEditmodeStyle"><button type="button"  title="Listenelement hinzufügen" id="webtn_add_listelement_d9b5fc928184865c329077335803424b" class="weBtn"  style="" onclick="setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('insert_entry_at_list','liste',1,document.we_form.elements['a164ca58a312dbde4c75ba7b89fabe7a_1'].options[document.we_form.elements['a164ca58a312dbde4c75ba7b89fabe7a_1'].selectedIndex].text)"><img src="/webEdition/images/button/icons/add_listelement.gif" class="weBtnImage" alt="-"/></button></td>
						<td style="padding-right:5px;" class="weEditmodeStyle"><select name="a164ca58a312dbde4c75ba7b89fabe7a_1"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option></select></td>
						<td style="padding-right:5px;" class="weEditmodeStyle"><button type="button"  title="Nach oben" id="webtn_direction_up_886bcb21fb283632cacfd27f126f20ef" class="weBtn"  style="" onclick="moveUp(this);"><img src="/webEdition/images/button/icons/direction_up.gif" class="weBtnImage" alt="-"/></button></td>
						<td style="padding-right:5px;" class="weEditmodeStyle"><button type="button"  title="Nach unten" id="webtn_direction_down_c981d94793884ae5c9e8fe8ef96cc6ff" class="weBtn"  style="" onclick="moveDown(this); "><img src="/webEdition/images/button/icons/direction_down.gif" class="weBtnImage" alt="-"/></button></td>
						<td class="weEditmodeStyle"><button type="button"  title="Löschen" id="webtn_function_trash_fff8e7fdeebb71068524e734f8330adf" class="weBtn"  style="" onclick="setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('delete_list','liste','1','blk_liste__2',1)"><img src="/webEdition/images/button/icons/function_trash.gif" class="weBtnImage" alt="-"/></button></td>
					</tr>
				</table>
			</td>
		</tr>
		</table>
	</div>
</div>
	
	
	
</body>
</html>