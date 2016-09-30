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
class we_fileupload_ui_wedoc extends we_fileupload_ui_preview{

	public function __construct($contentType = []){
		parent::__construct($contentType);

		//Fix #10418
		if($contentType === we_base_ContentTypes::APPLICATION){
			$this->setTypeCondition('accepted', []);
			$this->setTypeCondition('forbidden', [], we_base_ContentTypes::inst()->getExtension(we_base_ContentTypes::IMAGE));
		}

		$this->formElements = array_merge($this->formElements, ['importMeta' => ['set' => true, 'multiIconBox' => false, 'rightHeadline' => true, 'noline' => true],
		]);
		$this->type = 'wedoc';
		$this->dimensions['dragWidth'] = 300;
		$this->moreFieldsToAppend = array_merge($this->moreFieldsToAppend, [['we_transaction', 'text'],
				['we_doc_ct', 'text'],
				['we_doc_ext', 'text']
		]);
		$this->cliensideImageEditing = ($this->contentType === we_base_ContentTypes::IMAGE);
	}

	public function getHTML($fs = '', $ft = '', $md = '', $thumbnailSmall = '', $thumbnailBig = ''){
		$isIE10 = we_base_browserDetect::isIE() && we_base_browserDetect::getIEVersion() < 11;

		$progress = new we_progressBar(0, 170, '_fileupload');
		$progress->setProgressTextPlace(0);
		$divProgressbar = we_html_element::htmlDiv(['id' => 'div_fileupload_progressBar', 'style' => 'margin: 13px 0 10px 0;display:none;'], $progress->getHTML('', 'font-size:11px;'));
		$divFileInfo = we_html_element::htmlDiv([], $fs . '<br />' . $ft . '<br />' . $md);
		$divButtons = we_html_element::htmlDiv(['id' => 'div_fileupload_buttons', 'style' => 'width:180px'], $this->getDivBtnInputReset($isIE10 ? 84 : 170) .
				$divProgressbar .
				$this->getDivBtnUploadCancel($isIE10 ? 84 : 170)
		);

		return $this->getJs() . $this->getCss() . $this->getHiddens() . '
			<table id="table_form_upload" class="default" style="width:500px;">
				<tr style="vertical-align:top;">
					<td class="defaultfont" style="width:200px">' .
			$divFileInfo . $divButtons . '
					</td>
					<td style="width:300px">' .
			we_html_element::htmlDiv(['id' => 'div_fileupload_right', 'style' => "position:relative;"], $this->getHtmlDropZone('preview', $thumbnailSmall) .
				($this->contentType === we_base_ContentTypes::IMAGE && we_fileupload::EDIT_IMAGES_CLIENTSIDE ? $this->getFormImageEditClientside() : '') . ($this->contentType === we_base_ContentTypes::IMAGE ? $this->getFormImportMeta() : '')
			) . '
					</td>
				</tr>
				<tr><td colspan="2" class="defaultfont" style="padding-top:20px;">' . $this->getHtmlAlertBoxes() . '</td></tr>
				<tr>
					<td colspan="2" class="defaultfont" style="padding-top:20px;">' .
			we_html_tools::htmlAlertAttentionBox(g_l('weClass', (isset($GLOBALS['we_doc']) && $GLOBALS['we_doc']->getFilesize() ? "[upload_will_replace]" : "[upload_single_files]")), we_html_tools::TYPE_ALERT, 508) . '
					</td>
				</tr>
			</table>';
	}

}
