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
abstract class we_glossary_frameEditorType extends we_glossary_frameEditor{

	public static function Header(we_glossary_frames $weGlossaryFrames){
		$we_tabs = new we_tabs();

		$we_tabs->addTab(g_l('modules_glossary', '[overview]'), true, 1);

		return self::buildHeader($weGlossaryFrames, $we_tabs, g_l('modules_glossary', '[type]'), g_l('modules_glossary', '[' . array_pop(explode('_', we_base_request::_(we_base_request::STRING, 'cmdid'))) . ']'));
	}

	public static function Body(we_glossary_frames $weGlossaryFrames){
		$Temp = explode("_", we_base_request::_(we_base_request::STRING, 'cmdid'));
		$Language = $Temp[0] . "_" . $Temp[1];
		$Type = $Temp[2];
		$Cache = new we_glossary_cache($Language);
		$id = we_base_request::_(we_base_request::INT, 'ID', 0);
		if($id){
			switch(we_base_request::_(we_base_request::STRING, 'do')){
				case 'delete':
					if($GLOBALS['DB_WE']->query('DELETE FROM ' . GLOSSARY_TABLE . ' WHERE ID IN (' . implode(',', $id) . ')')){
						$weGlossaryFrames->jsCmd->addCmd('delItems', $id);
					}
					$Cache->write();
					break;

				case 'publish':
					$GLOBALS['DB_WE']->query('UPDATE ' . GLOSSARY_TABLE . ' SET Published=UNIX_TIMESTAMP() WHERE ID IN (' . implode(',', $id) . ')');
					$Cache->write();
					break;

				case 'unpublish':
					$GLOBALS['DB_WE']->query('UPDATE ' . GLOSSARY_TABLE . ' SET Published=0 WHERE ID IN (' . implode(',', $id) . ')');
					$Cache->write();
					break;

				default:
					break;
			}
		}
		unset($Cache);

		// ---> Search Start

		$Rows = we_base_request::_(we_base_request::INT, 'Rows', 10);
		$Offset = we_base_request::_(we_base_request::INT, 'Offset', 0);
		$Order = we_base_request::_(we_base_request::STRING, 'Order', 'Text');
		$Sort = we_base_request::_(we_base_request::STRING, 'Sort', 'ASC');
		$Where = 'Language="' . $Language . '" AND Type="' . $Type . '"' .
			(($kw = escape_sql_query(strtolower(we_base_request::_(we_base_request::STRING, 'Keyword')))) ?
			' AND (
lcase(Text) LIKE "%' . $kw . '%" OR
lcase(Title) LIKE "%' . $kw . '%" OR
lcase(Description) LIKE "%' . $kw . '%"
)' : '') .
			(we_base_request::_(we_base_request::BOOL, 'GreenOnly') ?
			' AND Published>0' : '');

		$Search = new we_glossary_search(GLOSSARY_TABLE);
		$Search->setFields(["*"]);
		$Search->setLimit($Offset, $Rows);
		$Search->setOrder($Order, $Sort);
		$Search->setWhere($Where);

		// ---> Search End

		$content = self::getHTMLPreferences($Search, $Type, $Language) .
			($Search->countItems() ?
			self::getHTMLPrevNext($Search) .
			self::getHTMLSearchResult($weGlossaryFrames, $Search, $Type) .
			self::getHTMLPrevNext($Search, true) :
			we_html_element::htmlDiv(['style' => "margin:12px 5px;"], g_l('modules_glossary', '[no_entries_found]'))
			);

		// ---> end of uilding content

		$out = we_html_element::htmlDiv(['id' => 'tab1'], we_html_multiIconBox::getHTML('', [
					['headline' => '',
						'html' => $content,
					],
					], 30));

		$weGlossaryFrames->jsCmd->addCmd('loadHeaderFooter', 'type', we_base_request::_(we_base_request::STRING, 'cmdid'));
		$weGlossaryFrames->jsCmd->addCmd('setRows', $Rows);
		return self::buildBody($weGlossaryFrames, $out, we_html_element::jsScript(WE_JS_MODULES_DIR . 'glossary/we_glossary_frameEditorType.js'));
	}

	public static function Footer(we_glossary_frames $weGlossaryFrames){
		return self::buildFooter($weGlossaryFrames, "");
	}

	private static function getHTMLSearchResult(we_glossary_frames $weGlossaryFrames, &$Search, $Type){

		$Search->execute();

		$retVal = "";

		$headline = [['dat' => '',],
			['dat' => g_l('modules_glossary', '[show]'),],
			['dat' => g_l('modules_glossary', '[' . $Type . ']'),]
		];

		switch($Type){

			case we_glossary_glossary::TYPE_ABBREVATION:
			case we_glossary_glossary::TYPE_ACRONYM:
				$headline[3] = ['dat' => g_l('modules_glossary', '[announced_word]'),];
				break;

			case we_glossary_glossary::TYPE_FOREIGNWORD:
			case we_glossary_glossary::TYPE_TEXTREPLACE:
				break;

			case we_glossary_glossary::TYPE_LINK:
				$headline[3] = ['dat' => g_l('modules_glossary', '[link_mode]'),];
				$headline[4] = ['dat' => g_l('modules_glossary', '[link_url]'),];
				break;
		}

		$headline[] = ['dat' => g_l('modules_glossary', '[date_published]'),];
		$headline[] = ['dat' => g_l('modules_glossary', '[date_modified]'),];

		$content = [];
		while($Search->next()){
			$show = '<i class="fa fa-lg fa-circle" style="color:#006DB8;"></i>';
			/* if($Search->getField('Published')) {
			  $show = '<i class="fa fa-lg fa-circle" style="color:#E7E7E7;"></i>';
			  } */

			$temp = [['dat' => '<input type="checkbox" name="ID[]" value="' . $Search->getField('ID') . '" />',
				'height' => 25,
				'style' => 'text-align:center',
				'bgcolor' => '#ffffff',
				],
				['dat' => $show,
					'height' => 25,
					'style' => 'text-align:center',
					'bgcolor' => '#ffffff',
				],
				['dat' => '<a href="javascript://" onclick="top.content.editor.edbody.location=\'' . $weGlossaryFrames->frameset . '&pnt=edbody&cmd=edit_glossary_' . $Type . '&cmdid=' . $Search->getField('ID') . '&tabnr=\'+top.content.activ_tab;">' . oldHtmlspecialchars($Search->getField('Text')) . '</a>',
					'height' => 25,
					'style' => 'text-align:left',
					'bgcolor' => '#ffffff',
				]
			];

			$values = we_unserialize($Search->getField('Attributes'));
			switch($Type){

				case we_glossary_glossary::TYPE_ABBREVATION:
				case we_glossary_glossary::TYPE_ACRONYM:
					$temp[3] = ['dat' => ($Search->getField('Title') ? oldHtmlspecialchars($Search->getField('Title')) : "-"),
						'height' => 25,
						'style' => 'text-align:left',
						'bgcolor' => '#ffffff',
					];
					break;

				case we_glossary_glossary::TYPE_FOREIGNWORD:
				case we_glossary_glossary::TYPE_TEXTREPLACE:
					break;

				case we_glossary_glossary::TYPE_LINK:
					$url = "";
					switch($values['mode']){
						case 'intern':
							$url = $values['InternLinkPath'];
							$mode = g_l('modules_glossary', '[link_intern]');
							break;
						case 'extern':
							$url = $values['ExternUrl'];
							$mode = g_l('modules_glossary', '[link_extern]');
							break;
						case 'object':
							$url = $values['ObjectLinkPath'];
							$mode = g_l('modules_glossary', '[link_object]');
							break;
						case 'category':
							if($values['modeCategory'] === "extern"){
								$url = $values['CategoryUrl'];
							} else {
								$url = $values['CategoryInternLinkPath'];
							}
							$mode = g_l('modules_glossary', '[link_category]');
							break;
					}
					$temp[3] = ['dat' => $mode,
						'height' => 25,
						'style' => 'text-align:left',
						'bgcolor' => '#ffffff',
					];
					$temp[4] = ['dat' => $url,
						'height' => 25,
						'style' => 'text-align:left',
						'bgcolor' => '#ffffff',
					];
					break;
			}
			$temp[] = ['dat' => $Search->getField('Published') > 0 ? str_replace(" - ", "<br/>", date(g_l('date', '[format][default]'), $Search->getField('Published'))) : "-",
				'height' => 25,
				'style' => 'text-align:center',
				'bgcolor' => '#ffffff',
			];
			$temp[] = ['dat' => $Search->getField('ModDate') > 0 ? str_replace(" - ", "<br />", date(g_l('date', '[format][default]'), $Search->getField('ModDate'))) : "-",
				'height' => 25,
				'style' => 'text-align:center',
				'bgcolor' => '#ffffff',
			];
			$content[] = $temp;
		}

		$retVal .= we_html_tools::htmlDialogBorder3(636, $content, $headline);

		return $retVal;
	}

	private static function getHTMLPreferences($Search, $Type, $Language){

		$button = we_html_button::create_button(we_html_button::SEARCH, "javascript:submitForm();");
		$newButton = we_html_button::create_button('new_entry', "javascript:we_cmd('new_glossary_" . $Type . "','" . $Language . "');", '', 0, 0, "", "", !we_base_permission::hasPerm("NEW_GLOSSARY"));

		$rows = [10 => 10, 25 => 25, 50 => 50, 100 => 100];

		return we_html_element::htmlHiddens(["we_transaction" => $GLOBALS['we_transaction'],
				"Order" => $Search->Order,
				"Offset" => $Search->Offset,
				"Sort" => $Search->Sort,
				"selectAll" => 0,
				"do" => ""
			]) . '
<table class="default" style="width:637px;margin-bottom:12px;">
	<tr>
		<td style="width:80px;"></td>
		<td style="width:157px;"></td>
		<td style="width:280px;"></td>
		<td style="width:20px;"></td>
		<td style="width:100px"></td>
	</tr>
	<tr>
		<td class="defaultfont lowContrast">' . g_l('modules_glossary', '[search]') . '</td>
		<td colspan="2">' . we_html_tools::htmlTextInput('Keyword', 24, we_base_request::_(we_base_request::STRING, 'Keyword', ''), "", "style=\"width: 430px\"") . '</td>
		<td></td>
		<td>' . $button . '</td>
	</tr>
	<tr>
		<td class="defaultfont lowContrast" style="padding-top:12px;">' . g_l('modules_glossary', '[view]') . '</td>
		<td>' . we_html_tools::htmlSelect("Rows", $rows, 1, $Search->Rows, "", ['onchange' => "submitForm();"]) . '</td>
		<td>' . we_html_forms::checkboxWithHidden(we_base_request::_(we_base_request::BOOL, 'GreenOnly'), "GreenOnly", g_l('modules_glossary', '[show_only_visible_items]'), false, "defaultfont", "jump(0);") . '</td>
		<td></td>
		<td>' . $newButton . '</td>
	</tr>
	</table>';
	}

	private static function getHTMLPrevNext($Search, $extended = false){

		$sum = $Search->countItems();
		$min = ($Search->Offset) + 1;
		$max = min($Search->Offset + $Search->Rows, $sum);

		$prev = ($Search->Offset > 0 ?
			we_html_button::create_button(we_html_button::BACK, "javascript:prev();") : //bt_back
			we_html_button::create_button(we_html_button::BACK, "", '', 0, 0, "", "", true));

		$next = ($Search->Offset + $Search->Rows >= $sum ?
			we_html_button::create_button(we_html_button::NEXT, "", '', 0, 0, "", "", true) :
			we_html_button::create_button(we_html_button::NEXT, "javascript:next();")); //bt_next


		$pages = $Search->getPages();

		$select = we_html_tools::htmlSelect("TmpOffset", $pages, 1, $Search->Offset, false, ['onchange' => "jump(this.value);"]);

		return '
	<table class="default withBigSpace" style="margin:12px 0px 12px 5px;">
	<tr>
		<td>' . ($extended && (we_base_permission::hasPerm(["DELETE_GLOSSARY", "NEW_GLOSSARY"])) ? we_html_button::create_button(we_html_button::TOGGLE, "javascript: AllItems();") : "") . '</td>
		<td style="text-align:right"><table class="default">
			<tr>
				<td>' . $prev . '</td>
				<td class="defaultfont" style="padding-left:10px;"><b>' . ($Search->Rows == 1 ? $min : $min . '-' . $max) . ' ' . g_l('global', '[from]') . ' ' . $sum . '</b></td>
				<td style="padding-left:10px;">' . $next . '</td>
				<td style="padding-left:10px;">' . $select . '</td>
			</tr>
			</table></td>
	</tr>
	' .
			($extended ?
			'<tr>
		<td colspan="3">
			<table class="default">
			<tr>
				<td class="small">' . (we_base_permission::hasPerm("DELETE_GLOSSARY") ? we_html_button::create_button(we_html_button::TRASH, "javascript: if(window.confirm('" . g_l('modules_glossary', '[confirm_delete]') . "')) { document.we_form.elements.do.value='delete'; submitForm(); }") . '</td>
				<td class="small" style="padding-left:1em;">' . g_l('modules_glossary', '[delete_selected_items]') : "") . '</td>
			</tr>
			</table>
		</td>
	<tr>
	<tr>
		<td colspan="3">
			<table class="default">
			<tr>
				<td class="small">' . (we_base_permission::hasPerm("NEW_GLOSSARY") ? we_html_button::create_button('fa:btn_function_publish,fa-lg fa-globe', "javascript: if(window.confirm('" . g_l('modules_glossary', '[confirm_publish]') . "')) { document.we_form.elements.do.value='publish'; submitForm(); }") . '</td>
				<td class="small" style="padding-left:1em;">' . g_l('modules_glossary', '[publish_selected_items]') : "") . '</td>
			</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="3">
			<table class="default">
			<tr>
				<td class="small">' . (we_base_permission::hasPerm("NEW_GLOSSARY") ? we_html_button::create_button('fa:btn_function_unpublish,fa-lg fa-moon-o', "javascript: if(window.confirm('" . g_l('modules_glossary', '[confirm_unpublish]') . "')) { document.we_form.elements.do.value='unpublish'; submitForm(); }") . '</td>
				<td class="small" style="padding-left:1em;">' . g_l('modules_glossary', '[unpublish_selected_items]') : "") . '</td>
			</tr>
			</table>
		</td>
	</tr>' :
			'') .
			'</table>';
	}

}
