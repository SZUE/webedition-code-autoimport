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
class we_editor_info extends we_editor_base{

	public function show(){

		$GLOBALS['we_transaction'] = we_base_request::_(we_base_request::TRANSACTION, 'we_cmd', we_base_request::_(we_base_request::TRANSACTION, 'we_transaction'), 2);
		if($this->we_doc->ContentType !== we_base_ContentTypes::FOLDER && $this->we_doc->ContentType !== we_base_ContentTypes::COLLECTION){
			$fs = $this->we_doc->getFilesize();
		}

		$parts = [
			['headline' => '',
				'html' => '
<div class="weMultiIconBoxHeadline" style="margin-bottom:5px;">ID</div>
<div style="margin-bottom:10px;">' . ($this->we_doc->ID ?: '-') . '</div>
<div class="weMultiIconBoxHeadline" style="padding-bottom:5px;">' . g_l('weEditorInfo', '[content_type]') . '</div>
<div style="margin-bottom:10px;">' . ($this->we_doc->ContentType ? g_l('weEditorInfo', '[' . $this->we_doc->ContentType . ']') : '') . '</div>' .
				(isset($fs) ?
				'<div class="weMultiIconBoxHeadline" style="padding-bottom:5px;">' . g_l('weEditorInfo', '[file_size]') . '</div>
<div style="margin-bottom:10px;">' . round(($fs / 1024), 2) . "&nbsp;KB&nbsp;(" . $fs . "&nbsp;Byte)" . '</div>' :
				''),
				'space' => we_html_multiIconBox::SPACE_MED2,
				'icon' => '<span class="docIcon" data-contenttype="' . $this->we_doc->ContentType . '" data-extension="' . (isset($this->we_doc->Extension) ? $this->we_doc->Extension : '') . '"></span>'
			],
			['headline' => '',
				'html' => '
<div class="weMultiIconBoxHeadline" style="padding-bottom:5px;">' . g_l('weEditorInfo', '[creation_date]') . '</div>
<div style="margin-bottom:10px;">' . date(g_l('weEditorInfo', '[date_format]'), $this->we_doc->CreationDate) . '</div>' .
				($this->we_doc->CreatorID && ($name = f('SELECT CONCAT(First," ",Second," (",username,")") FROM ' . USER_TABLE . ' WHERE ID=' . intval($this->we_doc->CreatorID))) ?
				'
<div class="weMultiIconBoxHeadline" style="padding-bottom:5px;">' . g_l('modules_users', '[created_by]') . '</div>
<div style="margin-bottom:10px;">' . $name . '</div>' :
				'') .
				'<div class="weMultiIconBoxHeadline" style="padding-bottom:5px;">' . g_l('weEditorInfo', '[changed_date]') . '</div>
<div style="margin-bottom:10px;">' . date(g_l('weEditorInfo', '[date_format]'), $this->we_doc->ModDate) . '</div>' .
				($this->we_doc->ModifierID && ($name = f('SELECT CONCAT(First," ",Second," (",username,")") FROM ' . USER_TABLE . ' WHERE ID=' . intval($this->we_doc->ModifierID))) ?
				'<div class="weMultiIconBoxHeadline" style="padding-bottom:5px;">' . g_l('modules_users', '[changed_by]') . '</div>
<div style="margin-bottom:10px;">' . $name . '</div>' .
				(in_array($this->we_doc->ContentType, [we_base_ContentTypes::HTML, we_base_ContentTypes::WEDOCUMENT]) ?
				'<div class="weMultiIconBoxHeadline" style="padding-bottom:5px;">' . g_l('weEditorInfo', '[lastLive]') . '</div>' .
				'<div style="margin-bottom:10px;">' . ($this->we_doc->Published ? date(g_l('weEditorInfo', '[date_format]'), $this->we_doc->Published) : "-") . '</div>' :
				'') .
				(!in_array($this->we_doc->Table, [TEMPLATES_TABLE, VFILE_TABLE]) && $this->we_doc->ContentType !== we_base_ContentTypes::FOLDER && $this->we_doc->Published && $this->we_doc->ModDate > $this->we_doc->Published ?
				'<div style="margin-bottom:10px;">' . we_html_button::create_button('revert_published', "javascript:top.we_cmd('revert_published_question');") . '</div>' :
				'') :
				''),
				'space' => we_html_multiIconBox::SPACE_MED2,
				'icon' => we_html_multiIconBox::INFO_CALENDAR
			]
		];

		$this->jsCmd->addCmd('setIconOfDocClass', 'docIcon');

		if($this->we_doc->ContentType !== we_base_ContentTypes::FOLDER){
			switch($this->we_doc->Table){
				case TEMPLATES_TABLE:
				case VFILE_TABLE:
					break;
				default:
					$rp = realpath($this->we_doc->getRealPath());
					$http = $this->we_doc->getHttpPath();

					switch($this->we_doc->ContentType){
						default:
							$showlink = false;
							break;
						case we_base_ContentTypes::WEDOCUMENT:
							$showlink = true;
							if(defined('WORKFLOW_TABLE')){
								$anzeige = (we_workflow_utility::inWorkflow($this->we_doc->ID, $this->we_doc->Table) ?
										we_workflow_utility::getDocumentStatusInfo($this->we_doc->ID, $this->we_doc->Table) :
										we_workflow_utility::getLogButton($this->we_doc->ID, $this->we_doc->Table));
							}
							break;

						case we_base_ContentTypes::HTML:
						case we_base_ContentTypes::IMAGE:
						case we_base_ContentTypes::FLASH:
						case we_base_ContentTypes::VIDEO:
						case we_base_ContentTypes::AUDIO:
							$showlink = true;
					}

					$published = !(($this->we_doc->ContentType == we_base_ContentTypes::HTML || $this->we_doc->ContentType == we_base_ContentTypes::WEDOCUMENT) && $this->we_doc->Published == 0);

					$html = '
<div class="weMultiIconBoxHeadline" style="padding-bottom:5px;">' . g_l('weEditorInfo', '[local_path]') . '</div>
<div style="margin-bottom:10px;">' . ($this->we_doc->ID == 0 || !$published ? '-' : '<span title="' . oldHtmlspecialchars($rp) . '">' . oldHtmlspecialchars(we_base_util::shortenPath($rp, 74)) . '</span>') . '</div>
<div class="weMultiIconBoxHeadline" style="padding-bottom:5px;">' . g_l('weEditorInfo', '[http_path]') . '</div>
<div style="margin-bottom:10px;">' . ($this->we_doc->ID == 0 || !$published ? '-' : ($showlink ? '<a href="' . $http . '" target="_blank" title="' . oldHtmlspecialchars($http) . '">' : '') . we_base_util::shortenPath($http, 74) . ($showlink ? '</a>' : '')) . '</div>';

					$parts[] = ['headline' => '',
						'html' => $html,
						'space' => we_html_multiIconBox::SPACE_MED2,
						'icon' => we_html_multiIconBox::PROP_PATH
					];
			}

			if(isset($anzeige)){
				$parts[] = ['headline' => g_l('modules_workflow', '[workflow]'),
					'html' => $anzeige,
					'space' => we_html_multiIconBox::SPACE_MED2,
					'forceRightHeadline' => 1,
					'icon' => we_html_multiIconBox::INFO_WORKFLOW
				];
			}

			switch($this->we_doc->ContentType){
				case we_base_ContentTypes::TEMPLATE:
					list($cnt, $select) = $this->we_doc->formTemplateDocuments();
					$parts[] = ['icon' => we_html_multiIconBox::PROP_DOC,
						'headline' => g_l('weClass', '[documents]') . ($cnt ? ' (' . $cnt . ')' : ''),
						'html' => $select,
						'space' => we_html_multiIconBox::SPACE_MED2
					];
					list($cnt, $select) = $this->we_doc->formTemplatesUsed();
					$parts[] = ['icon' => we_html_multiIconBox::PROP_DOC,
						'headline' => g_l('weClass', '[usedTemplates]') . ($cnt ? ' (' . $cnt . ')' : ''),
						'html' => $select,
						'space' => we_html_multiIconBox::SPACE_MED2
					];
					list($cnt, $select) = $this->we_doc->formTemplateUsedByTemplate();
					$parts[] = ['icon' =>we_html_multiIconBox::PROP_DOC,
						'headline' => g_l('weClass', '[usedByTemplates]') . ($cnt ? ' (' . $cnt . ')' : ''),
						'html' => $select,
						'space' => we_html_multiIconBox::SPACE_MED2
					];
					break;
				case we_base_ContentTypes::IMAGE:
					$metaData = $this->we_doc->getMetaData();
					$metaDataTable = '
<table class="default">
	<tr><td style="padding-bottom: 5px;" class="weMultiIconBoxHeadline" colspan="2">' . g_l('metadata', '[info_exif_data]') . '</td></tr>';
					if(empty($metaData['exif'])){
						$metaDataTable .= '<tr><td style="padding:0px 5px 5px 0px;" class="defaultfont" colspan="2">' . g_l('metadata', (is_callable("exif_read_data") ? '[no_exif_data]' : '[no_exif_installed]')) . '</td></tr>';
					} else {
						foreach($metaData['exif'] as $key => $val){
							$metaDataTable .= '<tr><td style="padding:0px 5px 5px 0px;" class="defaultfont">' . oldHtmlspecialchars($key) . ':</td><td style="padding:0px 5px 5px 0px;" class="defaultfont">' . oldHtmlspecialchars($val) . '</td></tr>';
						}
					}

					$metaDataTable .= '<tr><td style="padding:10px 0 5px 0;" class="weMultiIconBoxHeadline" colspan="2">' . g_l('metadata', '[info_iptc_data]') . '</td></tr>';
					if(empty($metaData['iptc'])){
						$metaDataTable .= '<tr><td style="padding:0px 5px 5px 0px;" class="defaultfont" colspan="2">' . g_l('metadata', '[no_iptc_data]') . '</td></tr>';
					} else {
						foreach($metaData['iptc'] as $key => $val){
							$metaDataTable .= '<tr><td style="padding:0px 5px 5px 0px;" class="defaultfont">' . oldHtmlspecialchars($key) . ':</td><td style="padding:0px 5px 5px 0px;" class="defaultfont">' . oldHtmlspecialchars($val) . '</td></tr>';
						}
					}
					$metaDataTable .= '</table>';
					break;
				case we_base_ContentTypes::APPLICATION:
					if($this->we_doc->Extension === '.pdf'){
						$metaData = $this->we_doc->getMetaData();
						$metaDataTable = '
<table class="default">
	<tr><td style="padding-bottom: 5px;" class="weMultiIconBoxHeadline" colspan="2">' . g_l('metadata', '[info_pdf_data]') . '</td></tr>';
						if(!empty($metaData['pdf'])){
							foreach($metaData['pdf'] as $key => $val){
								$metaDataTable .= '<tr><td style="padding:0px 5px 5px 0px;" class="defaultfont">' . oldHtmlspecialchars($key) . ':</td><td style="padding:0px 5px 5px 0px;" class="defaultfont">' . oldHtmlspecialchars($val) . '</td></tr>';
							}
						}
						$metaDataTable .= '</table>';
						break;
					}
				//no break;
				default:
					if($this->we_doc->isBinary()){
						$metaDataTable = g_l('metadata', '[no_metadata_supported]');
					}
			}

			if($this->we_doc->isBinary()){
				$formReference = $this->we_doc->formReferences($this->jsCmd);
				$parts[] = [
					'headline' => g_l('weClass', '[isUsed]') . ' (' . $formReference['num'] . ')',
					'html' => $formReference['form'],
					'space' => we_html_multiIconBox::SPACE_MED2,
					'forceRightHeadline' => 1,
					'icon' => we_html_multiIconBox::INFO_REFERENCES
				];
			}

			if(isset($metaDataTable)){
				$parts[] = [
					'headline' => '',
					'html' => $metaDataTable,
					'space' => we_html_multiIconBox::SPACE_MED2,
					'forceRightHeadline' => 1,
					'icon' => we_html_multiIconBox::PROP_META
				];
			}
		}
		return $this->getPage(we_html_multiIconBox::getHTML('', $parts, 20));
	}

}
