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
class we_editor_info_objectFile extends we_editor_base{

	public function show(){
		$html = '<div class="weMultiIconBoxHeadline" style="margin-bottom:5px;">ID</div>' .
				'<div style="margin-bottom:10px;">' . ($this->we_doc->ID ?: "-") . '</div>
	<div class="weMultiIconBoxHeadline" style="padding-bottom:5px;">' . g_l('weEditorInfo', '[content_type]') . '</div>' .
				'<div style="margin-bottom:10px;">' . g_l('weEditorInfo', '[' . $this->we_doc->ContentType . ']') . '</div>';

		$parts = [["headline" => "",
		"html" => $html,
		'space' => we_html_multiIconBox::SPACE_MED2,
		'icon' => "meta.gif"
			]
		];

		$html = '<div class="weMultiIconBoxHeadline" style="padding-bottom:5px;">' . g_l('weEditorInfo', '[creation_date]') . '</div>' .
				'<div style="margin-bottom:10px;">' . date(g_l('weEditorInfo', '[date_format]'), $this->we_doc->CreationDate) . '</div>';


		if($this->we_doc->CreatorID){
			$GLOBALS['DB_WE']->query('SELECT First,Second,username FROM ' . USER_TABLE . ' WHERE ID=' . $this->we_doc->CreatorID);
			if($GLOBALS['DB_WE']->next_record()){
				$html .= '<div class="weMultiIconBoxHeadline" style="padding-bottom:5px;">' . g_l('modules_users', '[created_by]') . '</div>' .
						'<div style="margin-bottom:10px;">' . $GLOBALS['DB_WE']->f("First") . ' ' . $GLOBALS['DB_WE']->f("Second") . ' (' . $GLOBALS['DB_WE']->f("username") . ')</div>';
			}
		}

		$html .= '<div class="weMultiIconBoxHeadline" style="padding-bottom:5px;">' . g_l('weEditorInfo', '[changed_date]') . '</div>' .
				'<div style="margin-bottom:10px;">' . date(g_l('weEditorInfo', '[date_format]'), $this->we_doc->ModDate) . '</div>';


		if($this->we_doc->ModifierID){
			$GLOBALS['DB_WE']->query('SELECT First,Second,username FROM ' . USER_TABLE . ' WHERE ID=' . $this->we_doc->ModifierID);
			if($GLOBALS['DB_WE']->next_record()){
				$html .= '<div class="weMultiIconBoxHeadline" style="padding-bottom:5px;">' . g_l('modules_users', '[changed_by]') . '</div>' .
						'<div style="margin-bottom:10px;">' . $GLOBALS['DB_WE']->f("First") . ' ' . $GLOBALS['DB_WE']->f("Second") . ' (' . $GLOBALS['DB_WE']->f("username") . ')</div>';
			}
		}

		$html .= '<div class="weMultiIconBoxHeadline" style="padding-bottom:5px;">' . g_l('weEditorInfo', '[lastLive]') . '</div>' .
				'<div style="margin-bottom:10px;">' . ($this->we_doc->Published ? date(g_l('weEditorInfo', '[date_format]'), $this->we_doc->Published) : "-") . '</div>';


		$parts[] = ["headline" => "",
			"html" => $html,
			'space' => we_html_multiIconBox::SPACE_MED2,
			'icon' => "cal.gif"
		];


		if(defined('WORKFLOW_TABLE')){
			$anzeige = (we_workflow_utility::inWorkflow($this->we_doc->ID, $this->we_doc->Table) ?
					we_workflow_utility::getDocumentStatusInfo($this->we_doc->ID, $this->we_doc->Table) :
					we_workflow_utility::getLogButton($this->we_doc->ID, $this->we_doc->Table));

			$parts[] = ["headline" => g_l('modules_workflow', '[workflow]'),
				"html" => $anzeige,
				'space' => we_html_multiIconBox::SPACE_MED2,
				"forceRightHeadline" => 1,
				'icon' => "workflow.gif"
			];

			return $this->getPage(we_html_multiIconBox::getHTML("", $parts, 30));
		}
	}

}
