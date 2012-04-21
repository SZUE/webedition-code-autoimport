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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

	class weGlossaryFrameEditorDictionary extends weGlossaryFrameEditor {


		function Header(&$weGlossaryFrames) {


			$we_tabs = new we_tabs();t_e('notice','cmdid',$_REQUEST['cmdid']);
			$we_tabs->addTab(new we_tab("#",g_l('modules_glossary','[dictionary]'),'TAB_ACTIVE',"setTab('1');"));

			$frontendL=getWeFrontendLanguagesForBackend();
			$title = g_l('modules_glossary','[dictionary]') . ":&nbsp;".$frontendL[substr($_REQUEST['cmdid'], 0, 5)];

			return weGlossaryFrameEditorDictionary::buildHeader($weGlossaryFrames, $we_tabs, g_l('modules_glossary','[dictionary]'),$frontendL[substr($_REQUEST['cmdid'], 0, 5)]);

		}


		function Body(&$weGlossaryFrames) {

			$tabNr = isset($_REQUEST["tabnr"]) ? (($weGlossaryFrames->View->Glossary->IsFolder && $_REQUEST["tabnr"]!=1) ? 1 : $_REQUEST["tabnr"]) : 1;


			$_js =		$weGlossaryFrames->topFrame.'.resize.right.editor.edheader.location="'.$weGlossaryFrames->frameset.'?pnt=edheader&cmd=view_dictionary&cmdid=' . $_REQUEST['cmdid'] . '";'
					.	$weGlossaryFrames->topFrame.'.resize.right.editor.edfooter.location="'.$weGlossaryFrames->frameset.'?pnt=edfooter&cmd=view_dictionary&cmdid=' . $_REQUEST['cmdid'] . '"';

	        $js = we_html_element::jsElement($_js);

	        $out = $js . we_html_element::htmlDiv(array('id' => 'tab1','style'=>($tabNr==1 ? '' : 'display: none')), we_multiIconBox::getHTML('weMultibox',"100%",weGlossaryFrameEditorDictionary::getHTMLTabProperties($weGlossaryFrames),30,'',-1,'','',false));

	        return weGlossaryFrameEditorDictionary::buildBody($weGlossaryFrames, $out);

		}


		function Footer(&$weGlossaryFrames) {


			$_table = array(
				'border'		=> '0',
				'cellpadding'	=> '0',
				'cellspacing'	=> '0',
				'width'			=> '3000',
			);

			$table1 = new we_html_table($_table, 1, 1);
			$table1->setCol(0, 0, array("nowrap"=>null,"valign"=>"top"), we_html_tools::getPixel(1600, 10));


			$_table = array(
				'border'		=> '0',
				'cellpadding'	=> '0',
				'cellspacing'	=> '0',
			);

			$_we_button = we_button::create_button("save", "javascript:top.opener.top.we_cmd('save_dictionary')",true,100,22,'','',(!we_hasPerm('NEW_GLOSSARY') && !we_hasPerm('EDIT_GLOSSARY')));

			$table2 = new we_html_table($_table, 1, 2);
			$table2->setRow(0, array("valign"=>"middle"));
			$table2->setCol(0, 0, array("nowrap"=>null), we_html_tools::getPixel(10, 20));
			$table2->setCol(0, 1, array("nowrap"=>null), $_we_button);

			$form = we_html_element::htmlForm(array(),$table1->getHtml().$table2->getHtml());

	        return weGlossaryFrameEditorDictionary::buildFooter($weGlossaryFrames, $form);

		}





		function getHTMLTabProperties(&$weGlossaryFrames) {

			$parts = array();

			$language = substr($_REQUEST['cmdid'], 0, 5);

			$content = '<table border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td>
							' . we_html_tools::htmlAlertAttentionBox(g_l('modules_glossary','[hint_dictionary]'), 2, 520, true, 0) . '</td>
					</tr>
					<tr>
						<td>
							'.we_html_tools::getPixel(2,4).'</td>
					</tr>
					<tr>
						<td>
							' . we_html_element::htmlTextarea(array('name'=>'Dictionary', 'cols'=>60, 'rows'=>20, 'style'=>'width:520px;'),implode("\n", weGlossary::getDictionary($language))) . '</td>
					</tr>
				</table>';

			$item = array(
				"headline" => g_l('modules_glossary','[dictionary]'),
				"html" => $content,
				"space" => 120
			);
			array_push($parts, $item);

			return $parts;

		}


	}

