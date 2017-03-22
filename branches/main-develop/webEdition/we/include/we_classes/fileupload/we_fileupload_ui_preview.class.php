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
class we_fileupload_ui_preview extends we_fileupload_ui_base{
	protected $formElements = ['uploader' => ['set' => false, 'multiIconBox' => true, 'rightHeadline' => true, 'noline' => true],
		'importMeta' => ['set' => false, 'multiIconBox' => true, 'rightHeadline' => true, 'noline' => true],
		'isSearchable' => ['set' => false, 'multiIconBox' => true, 'rightHeadline' => true, 'noline' => true],
		'categories' => ['set' => false, 'multiIconBox' => true, 'rightHeadline' => true, 'noline' => true],
		'parentId' => ['set' => false, 'multiIconBox' => true, 'rightHeadline' => true, 'noline' => true],
		'sameName' => ['set' => false, 'multiIconBox' => true, 'rightHeadline' => true, 'noline' => true],
		'attributes' => ['set' => false, 'multiIconBox' => true, 'rightHeadline' => true, 'noline' => true],
		'thumbnails' => ['set' => false, 'multiIconBox' => true, 'rightHeadline' => true, 'noline' => true],
		'imageResize' => ['set' => false, 'multiIconBox' => true, 'rightHeadline' => true, 'noline' => true],
		'imageRotate' => ['set' => false, 'multiIconBox' => true, 'rightHeadline' => true, 'noline' => true],
		'imageQuality' => ['set' => false, 'multiIconBox' => true, 'rightHeadline' => true, 'noline' => true],
		'tableProperties' => ['foldAtNr' => -1, 'foldAtOpen' => '', 'foldAtClose' => '']
	];
	protected $isExternalBtnUpload = false;
	protected $parentID = ['setField' => false,
		'preset' => IMAGESTARTID_DEFAULT,
		'setFixed' => false,
	];
	protected $transaction;
	protected $contentType;
	protected $extension;
	//array is used in settings as well
	public static $scaleProps = ['' => '', 320 => 320, 640 => 640, 1280 => 1280, 1440 => 1440, 1600 => 1600, 1920 => 1920, 2560 => 2560];

	public function __construct($contentType = '', $extension = ''){
		parent::__construct('we_File');

		$this->doCommitFile = true;
		$this->contentType = $contentType;
		$this->responseClass = 'we_fileupload_resp_import';
		//$type = 'binDoc';
		$this->callback = '';
		$this->type = 'preview';
		$this->extension = $extension;
		$this->setInternalProgress(['isInternalProgress' => true]);
		$this->internalProgress['width'] = 170;
		$this->setTypeCondition('accepted', [$contentType]);
		$this->setDimensions(['width' => 200, 'dragHeight' => 116, 'alertBoxWidth' => 507]);
		//$this->binDocProperties = $this->getDocProperties();
		$this->moreFieldsToAppend = array_merge($this->moreFieldsToAppend, [['fu_doc_importMetadata', 'text'],
			['fu_file_sameName', 'text'],
			['fu_doc_focusX', 'text'],
			['fu_doc_focusY', 'text'],
		]);
		$this->cliensideImageEditing = true;
	}

	public function getCss(){
		return we_html_element::cssLink(CSS_DIR . 'we_fileupload.css') . we_html_element::cssElement('
			div.we_file_drag{
				width: ' . $this->dimensions['dragWidth'] . 'px;
			}
			div.filedrag_content_left{
				padding-left: ' . ($this->dimensions['dragWidth'] * 0.04) . 'px;
				width: ' . ($this->dimensions['dragWidth'] * 0.56) . 'px;
			}
			div.filedrag_content_right{
				/*width: ' . ($this->dimensions['dragWidth'] * 0.4) . 'px;*/
			}
			div.filedrag_preview_left{
				padding-left: ' . ($this->dimensions['dragWidth'] * 0.04) . 'px;
				width: ' . ($this->dimensions['dragWidth'] * 0.56) . 'px;
			}');
	}

	public function getDivBtnUploadCancel($width = 170){
		return we_html_element::htmlDiv([], $this->getButtonWrapped('upload', true, $width) . $this->getButtonWrapped('cancel'), false, $width);
	}

	protected function getDivBtnInputReset($width){
		return we_html_element::htmlDiv(['style' => 'margin-top:18px;'], $this->getButtonWrapped('reset', false, $width) . $this->getButtonWrapped('browse', false, $width));
	}

	protected function getHtmlDropZone($type = 'preview', $thumbnailSmall = ''){
		$dropText = g_l('newFile', $this->isDragAndDrop ? '[drop_text_ok]' : '[drop_text_nok]');

		$content = we_html_element::htmlDiv(['id' => 'div_fileupload_fileDrag_state_0', 'class' => 'we_file_drag_content', 'style' => (!$this->isDragAndDrop ? 'border-color:white;' : '')/* , 'ondragenter' => "alert('wrong div')" */], we_html_element::htmlDiv([
					'id' => 'div_filedrag_content_left', 'class' => 'filedrag_content_left', 'style' => (!$this->isDragAndDrop ? 'font-size:14px' : '')], $dropText) .
				we_html_element::htmlDiv(['id' => 'div_filedrag_content_right', 'class' => 'filedrag_content_right'], ($thumbnailSmall ?: we_html_element::jsElement('document.write(WE().util.getTreeIcon("' . $this->contentType . '"));')))
			) .
			we_html_element::htmlDiv(['id' => 'div_fileupload_fileDrag_state_1', 'class' => 'we_file_drag_preview', 'style' => (!$this->isDragAndDrop ? 'border-color:rgb(243, 247, 255);' : 'display:none;')], we_html_element::htmlDiv([
					'id' => 'div_upload_fileDrag_innerLeft', 'class' => 'filedrag_preview_left'], we_html_element::htmlDiv(['id' => 'span_fileDrag_inner_filename']) .
					we_html_element::htmlDiv(['id' => 'span_fileDrag_inner_size', 'style' => 'padding-top: 4px;']) .
					we_html_element::htmlDiv(['id' => 'span_fileDrag_inner_type']) .
					we_html_element::htmlDiv(['id' => 'span_fileDrag_inner_edit', 'style' => 'display:none;padding-top: 4px;'])
				) .
				we_html_element::htmlDiv(['id' => 'div_upload_fileDrag_innerRight', 'class' => 'filedrag_preview_right'], '')
			) .
			we_html_element::htmlDiv(['id' => 'div_fileupload_fileDrag_mask', 'class' => 'we_file_drag_mask'], we_html_element::htmlDiv(['class' => 'we_file_drag_maskSpinner'], '<i class="fa fa-2x fa-spinner fa-pulse"></i></span>') .
				we_html_element::htmlDiv(['id' => 'image_edit_mask_text', 'class' => 'we_file_drag_maskBusyText'])
		);

		return self::getHtmlLoup() . ($this->isDragAndDrop ? we_html_element::htmlDiv(['id' => 'div_we_File_fileDrag', 'class' => 'we_file_drag'], $content) : $content);
	}

	protected function getHiddens(){
		return $hiddens = parent::getHiddens() . we_html_element::htmlHiddens([
				'we_doc_ct' => $this->contentType,
				'we_doc_ext' => $this->extension,
				'fu_doc_focusX' => 0,
				'fu_doc_focusY' => 0,
		]);
	}

	public static function getFormImageEditClientside($type = '', $hide = false){
		$importer = $type === 'importer';
		$attribs = ['name' => 'fuOpts_scale', 'type' => 'text', 'class' => 'wetextinput optsScaleInput' . ($importer ? ' multiimport' : ''), 'autocomplete' => 'off',
			'value' => (FILE_UPLOAD_IMG_MAX_SIZE !== -1 ? FILE_UPLOAD_IMG_MAX_SIZE : '')];
		$scaleValue = we_html_element::htmlInput($attribs);
		$scaleProps = FILE_UPLOAD_IMG_MAX_SIZE === -1 ? self::$scaleProps : array_filter(self::$scaleProps, function($val){
				return $val <= FILE_UPLOAD_IMG_MAX_SIZE;
			});
		$scalePropositions = we_html_tools::htmlSelect('fuOpts_scaleProps', $scaleProps, 1, 0, false, [], '', '', 'weSelect optsScalePropositions' . ($importer ? ' multiimport' : ''));
		$scaleWhatSelect = we_html_tools::htmlSelect('fuOpts_scaleWhat', [
				'pixel_l' => g_l('importFiles', '[edit_pixel_longest]'),
				'pixel_w' => g_l('importFiles', '[edit_pixel_width]'),
				'pixel_h' => g_l('importFiles', '[edit_pixel_height]')
				], 1, 0, false, [], '', $importer ? 0 : 155, 'weSelect optsUnitSelect' . ($importer ? ' multiimport' : ''));
		$scaleHelp = we_html_element::htmlDiv(['data-index' => '0', 'class' => 'optsRowScaleHelp'], '<span class="fa-stack alertIcon" style="color:black;"><i class="fa fa-question-circle" ></i></span>' . we_html_element::htmlDiv([
					'class' => 'optsRowScaleHelpText']));
		if(!$importer){
			$rotateSelect = we_html_tools::htmlSelect('fuOpts_rotate', [
					0 => g_l('weClass', '[rotate0]'),
					180 => g_l('weClass', '[rotate180]'),
					270 => g_l('weClass', '[rotate90l]'),
					90 => g_l('weClass', '[rotate90r]'),
					], 1, 0, false, [], '28', 0, 'weSelect optsRotateSelect');
		}
		$quality = '<i class="fa fa-image qualityIconLeft"></i>' . we_html_element::htmlInput(['type' => 'range', 'value' => 90, 'min' => 10, 'max' => 90, 'step' => 5, 'name' => 'fuOpts_quality', 'class' => 'optsQuality']) . '<i class="fa fa-image qualityIconRight"></i>';
		$btnProcess = we_html_button::create_button(we_html_button::MAKE_PREVIEW, "javascript:", '', 0, 0, '', '', true, false, '_weFileupload', false, $title = 'Bearbeitungsvorschau erstellen', 'weFileupload_btnImgEditRefresh');

		return we_html_element::htmlDiv([], we_html_element::htmlHidden(fuOpts_doEdit, 1)) .
			we_html_element::htmlDiv(['class' => 'imgEditOpts' . ($hide ? ' hidden' : ''), 'id' => 'editImage'], we_html_element::htmlDiv([
					'class' => 'scaleDiv'], we_html_element::htmlDiv(['class' => 'labelContainer'], g_l('importFiles', '[scale_label]') . ':') .
					we_html_element::htmlDiv(['class' => 'inputContainer'], $scaleWhatSelect . ' ' . $scalePropositions . $scaleValue) . $scaleHelp
				) .
				($importer ? we_html_element::htmlHidden('fuOpts_rotate', 0) : we_html_element::htmlDiv(['class' => 'rotationDiv'], we_html_element::htmlDiv(['class' => 'labelContainer'], g_l('importFiles', '[rotate_label]') . ':') .
					we_html_element::htmlDiv(['class' => 'inputContainer'], $rotateSelect)
				)) .
				we_html_element::htmlDiv(['class' => 'qualityDiv'], we_html_element::htmlDiv(['class' => 'labelContainer'], g_l('weClass', '[quality]') . ':') .
					we_html_element::htmlDiv(['class' => 'inputContainer'], $quality) .
					(!$importer ? we_html_element::htmlDiv(['class' => 'btnContainer'], $btnProcess) : '')
				) .
				($importer ? we_html_element::htmlDiv(['class' => 'btnDiv'], we_html_element::htmlDiv(['class' => 'btnContainer' . ($importer ? ' multiimport' : '')], $btnProcess)
				) : '')
		);
	}

	public function getFormImportMeta($hide = false){
		$name = 'importMeta';
		if(!isset($this->formElements[$name]) || !$this->formElements[$name]['set']){
			return false;
		}

		$html = we_html_element::htmlDiv(['id' => 'div_importMeta', 'class' => 'importMeta' . ($hide ? ' hidden' : '')], we_html_forms::checkboxWithHidden(($this->imageEditProps['importMetadata'] ? true : false), 'fu_doc_importMetadata', g_l('importFiles', '[import_metadata]'), false, 'defaultfont', ''));
		$headline = g_l('importFiles', '[metadata]');

		return $this->formElements[$name]['multiIconBox'] ? $this->makeMultiIconRow($name, $headline, $html) : $html;
	}

	function getFormCategories(){
		$name = 'categories';
		if(!isset($this->formElements[$name]) || !$this->formElements[$name]['set'] || !we_base_permission::hasPerm("NEW_GRAFIK")){
			return;
		}

		$width_size = 378;
		$addbut = we_html_button::create_button(we_html_button::ADD, "javascript:we_cmd('we_selector_category',-1,'" . CATEGORY_TABLE . "','','','opener.addCat(top.fileSelect.data.allPaths);opener.selectCategories();')");
		$del_but = addslashes(we_html_button::create_button(we_html_button::TRASH, "javascript:#####placeHolder#####;if(typeof 'selectCategories' !== 'undefined'){selectCategories()};"));

		$cats = makeArrayFromCSV($this->imageEditProps['categories']) ?
			id_to_path($cats, CATEGORY_TABLE) : [];

		$table = new we_html_table([
			'id' => 'CategoriesBlock',
			'style' => 'display: block;',
			'class' => 'default withSpace'
			], 2, 1);

		$table->setColContent(0, 0, we_html_element::htmlDiv([
				'id' => 'categoriesDiv',
				'class' => 'blockWrapper',
				'style' => 'width: ' . ($width_size) . 'px; height: 60px; border: #AAAAAA solid 1px;'
		]));
		$table->setCol(1, 0, ['colspan' => 2, 'style' => 'text-align:right'], we_html_button::create_button(we_html_button::DELETE_ALL, "javascript:removeAllCats()") . $addbut
		);

		$js = we_html_element::jsScript(JS_DIR . 'fileupload/we_fileupload_formCategories.js', 'init();', ['id' => 'loadVarFileupload_ui_preview', 'data-preview' => setDynamicVar([
					'delButton' => $del_but,
					'categoriesDivSize' => ($width_size - 10),
					'variantCats' => $cats
		])]);
		$html = $table->getHtml() . $js . we_html_element::htmlHidden('fu_doc_categories', '');
		$headline = g_l('global', '[categorys]');

		return $this->formElements[$name]['multiIconBox'] ? $this->makeMultiIconRow($name, $headline, $html) : $html;
	}

	public function getFormIsSearchable($isMultiIconBox = true){
		$name = 'isSearchable';
		if(!isset($this->formElements[$name]) || !$this->formElements[$name]['set']){
			return;
		}

		$html = we_html_element::htmlDiv([], we_html_forms::checkboxWithHidden(($this->imageEditProps['isSearchable'] ? true : false), 'fu_doc_isSearchable', g_l('importFiles', '[imgsSearchable_label]'), false, 'defaultfont', ''));
		$headline = g_l('importFiles', '[imgsSearchable]');

		return $this->formElements[$name]['multiIconBox'] ? $this->makeMultiIconRow($name, $headline, $html) : $html;
	}

	public function getFormSameName(){
		$name = 'sameName';
		if(!isset($this->formElements[$name]) || !$this->formElements[$name]['set']){
			return;
		}

		$html = we_html_element::htmlDiv(['style' => 'margin:10px 0 0 0;'],
				//we_html_tools::htmlAlertAttentionBox(g_l('importFiles', '[sameName_expl]'), we_html_tools::TYPE_INFO, 380) .
				we_html_element::htmlDiv(['style' => 'margin-top:10px'], //g_l('newFile', '[caseFileExists]') . '<br/>' .
					we_html_forms::radiobutton('overwrite', false, "sameName", g_l('importFiles', '[sameName_overwrite]'), false, "defaultfont", 'document.we_form.fu_file_sameName.value=this.value;') .
					we_html_forms::radiobutton('rename', true, "sameName", g_l('importFiles', '[sameName_rename]'), false, "defaultfont", 'document.we_form.fu_file_sameName.value=this.value;') .
					we_html_forms::radiobutton('nothing', false, "sameName", g_l('importFiles', '[sameName_nothing]'), false, "defaultfont", 'document.we_form.fu_file_sameName.value=this.value;')
				) .
				we_html_element::htmlHidden('fu_file_sameName', 'rename')
		);

		$headline = g_l('importFiles', '[sameName_headline]');

		return $this->formElements[$name]['multiIconBox'] ? $this->makeMultiIconRow($name, $headline, $html) : $html;
	}

	public function getFormParentID($formName = 'we_form'){// TODO: set formName as class prop
		$name = 'parentId';
		if(!isset($this->formElements[$name]) || !$this->formElements[$name]['set']){
			return;
		}

		$parentDir = '';
		if(!$this->doImport && $this->parentID['preset'] && !is_numeric($this->parentID['preset']) && !path_to_id($this->parentID['preset'])){
			// in sselect we when uploading to ext dir we have parentDir in $this->parentID['preset' with no id to path!
			$parentDir = $this->parentID['preset'];
			$parentID = -1;
		} else {
			$parentID = $this->parentID['preset'] ? (is_numeric($this->parentID['preset']) ? $this->parentID['preset'] : path_to_id($this->parentID['preset'])) : ($this->imageEditProps['parentID'] ?: (IMAGESTARTID_DEFAULT ?: 0));
		}

		$DB_WE = new DB_WE();
		$parentID = !$parentID ? 0 : (!f('SELECT 1 FROM ' . FILE_TABLE . ' WHERE ID=' . intval($parentID) . ' AND IsFolder=1', '', $DB_WE) ? 0 : $parentID);

		if(($ws = get_ws(FILE_TABLE, true))){
			if(!(we_users_util::in_workspace($parentID, $ws, FILE_TABLE))){
				$parentID = intval(reset($ws));

				if($this->parentID['setFixed']){
					t_e('wrong uploader configuration: fixed parentID not in user\'s ws', $ws, $this->parentID['preset']);
				}
			}
		}

		if(!$this->parentID['setFixed']){
			$weSuggest = &weSuggest::getInstance();
			$weSuggest->setAcId("fu_file_parentID");
			$weSuggest->setContentType(we_base_ContentTypes::FOLDER);
			$weSuggest->setInput("parentPath", $parentID ? id_to_path($parentID, FILE_TABLE) : '/', [], false);
			$weSuggest->setMaxResults(10);
			$weSuggest->setResult("fu_file_parentID", $parentID);
			$weSuggest->setSelector(weSuggest::DirSelector);
			$weSuggest->setWidth(326);
			$weSuggest->setSelectButton(we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_directory',$parentID,'" . FILE_TABLE . "','fu_file_parentID','parentPath','','',0,'" . we_base_ContentTypes::FOLDER . "'," . (we_base_permission::hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ");"));

			$html = $weSuggest->getHTML();
		} else {
			$parentPath = $parentDir ?: id_to_path($parentID);
			$html = we_html_element::htmlInput(['value' => $parentPath, 'disabled' => 'disabled']) .
				we_html_button::create_button(we_html_button::SELECT, '', '', '', '', '', '', true) .
				we_html_element::htmlHiddens(['fu_file_parentID' => $parentID,
					'fu_file_parentDir' => $parentDir,
			]);
		}

		$headline = g_l('importFiles', '[destination_dir]');

		return $this->formElements[$name]['multiIconBox'] ? $this->makeMultiIconRow($name, $headline, $html) : $html;
	}

	public function getFormThumbnails($thumbs = ''){
		$name = 'thumbnails';
		if(!isset($this->formElements[$name]) || !$this->formElements[$name]['set'] || !we_base_permission::hasPerm("NEW_GRAFIK")){
			return;
		}

		$thumbnails = new we_html_select([
			'multiple' => 'multiple',
			'name' => 'thumbnails_tmp',
			'id' => 'thumbnails_tmp',
			'class' => 'defaultfont',
			'size' => 6,
			'style' => 'width: 378px;',
			'onchange' => "this.form.fu_doc_thumbs.value='';for(var i=0;i<this.options.length;i++){if(this.options[i].selected){this.form.fu_doc_thumbs.value +=(this.options[i].value + ',');}};this.form.fu_doc_thumbs.value=this.form.fu_doc_thumbs.value.replace(/^(.+),$/,'$1');"
		]);
		$DB_WE = new DB_WE();
		$DB_WE->query('SELECT ID,Name,description FROM ' . THUMBNAILS_TABLE . ' ORDER BY Name');

		$thumbsArr = explode(',', trim($this->imageEditProps['thumbnails'], ' ,'));
		while($DB_WE->next_record()){
			$attribs = ['title' => $DB_WE->f('description'),];
			$attribs = in_array($DB_WE->f('ID'), $thumbsArr) ? array_merge($attribs, ['selected' => 'selected']) : $attribs;
			$thumbnails->addOption($DB_WE->f('ID'), $DB_WE->f('Name'), $attribs);
		}

		$html = g_l('importFiles', '[thumbnails]') . "<br/><br/>" . $thumbnails->getHtml() . we_html_element::htmlHidden('fu_doc_thumbs', $this->imageEditProps['thumbnails']);
		$headline = g_l('thumbnails', '[create_thumbnails]');

		return $this->formElements[$name]['multiIconBox'] ? $this->makeMultiIconRow($name, $headline, $html) : $html;
	}

	public function getFormImageAttributes(){
		$name = 'attributes';
		if(!isset($this->formElements[$name]) || !$this->formElements[$name]['set'] || !we_base_permission::hasPerm("NEW_GRAFIK")){
			return;
		}

		$html = we_html_element::htmlDiv([], we_html_element::htmlLabel([], 'Alternativ Text') . '<br>' . we_html_tools::htmlTextInput('fu_doc_alt', 24, '', '', '', 'text', 378)) .
			we_html_element::htmlDiv([], we_html_element::htmlLabel([], 'Titel') . '<div>' . we_html_tools::htmlTextInput('fu_doc_title', 24, '', '', '', 'text', 378) . '</div>');
		$headline = 'Attribute';

		return $this->formElements[$name]['multiIconBox'] ? $this->makeMultiIconRow($name, $headline, $html) : $html;
	}

	public function getFormImageResize(){
		$name = 'imageResize';
		if(!isset($this->formElements[$name]) || !$this->formElements[$name]['set'] || !we_base_permission::hasPerm("NEW_GRAFIK")){
			return;
		}

		$widthInput = we_html_tools::htmlTextInput("fu_doc_width", 10, intval($this->imageEditProps['imageWidth']), '', '', "text", 60);
		$heightInput = we_html_tools::htmlTextInput("fu_doc_height", 10, intval($this->imageEditProps['imageHeight']), '', '', "text", 60);
		$widthSelect = '<select class="weSelect" name="fu_doc_widthSelect"><option value="pixel"' . ($this->imageEditProps['widthSelect'] === 'pixel' ? ' selected="selected"' : '') . '>' . g_l('weClass', '[pixel]') . '</option><option value="percent"' . ($this->imageEditProps['widthSelect'] !== 'pixel' ? ' selected="selected"' : '') . '>' . g_l('weClass', '[percent]') . '</option></select>';
		$heightSelect = '<select class="weSelect" name="fu_doc_heightSelect"><option value="pixel"' . ($this->imageEditProps['heightSelect'] === 'pixel' ? ' selected="selected"' : '') . '>' . g_l('weClass', '[pixel]') . '</option><option value="percent"' . ($this->imageEditProps['heightSelect'] !== 'pixel' ? ' selected="selected"' : '') . '>' . g_l('weClass', '[percent]') . '</option></select>';
		$ratio_checkbox = we_html_forms::checkboxWithHidden(($this->imageEditProps['keepRatio'] ? true : false), 'fu_doc_keepRatio', g_l('thumbnails', '[ratio]'), false, 'defaultfont', '');

		$html = '<table>
			<tr>
			<td class="defaultfont">' . g_l('weClass', '[width]') . ':</td>
			<td>' . $widthInput . '</td>
			<td>' . $widthSelect . '</td>
			</tr>
			<tr>
			<td class="defaultfont">' . g_l('weClass', '[height]') . ':</td>
			<td>' . $heightInput . '</td>
			<td>' . $heightSelect . '</td>
			</tr>
			<tr>
			<td colspan="3">' . $ratio_checkbox . '</td>
			</tr>
			</table>';

		$headline = g_l('weClass', '[resize]');

		return $this->formElements[$name]['multiIconBox'] ? $this->makeMultiIconRow($name, $headline, $html) : $html;
	}

	public function getFormImageRotate(){
		$name = 'imageRotate';
		if(!isset($this->formElements[$name]) || !$this->formElements[$name]['set'] || !we_base_permission::hasPerm("NEW_GRAFIK")){
			return;
		}

		$degrees = intval($this->imageEditProps['degrees']);
		$radio0 = we_html_forms::radiobutton(0, $degrees === 0, "fu_doc_degrees", g_l('weClass', '[rotate0]'));
		$radio180 = we_html_forms::radiobutton(180, $degrees === 180, "fu_doc_degrees", g_l('weClass', '[rotate180]'));
		$radio90l = we_html_forms::radiobutton(90, $degrees === 90, "fu_doc_degrees", g_l('weClass', '[rotate90l]'));
		$radio90r = we_html_forms::radiobutton(270, $degrees === 270, "fu_doc_degrees", g_l('weClass', '[rotate90r]'));

		$html = $radio0 . $radio180 . $radio90l . $radio90r;
		$headline = g_l('weClass', '[rotate]');

		return $this->formElements[$name]['multiIconBox'] ? $this->makeMultiIconRow($name, $headline, $html) : $html;
	}

	public function getFormImageQuality(){ // obsolete
		$name = 'imageQuality';
		if(!isset($this->formElements[$name]) || !$this->formElements[$name]['set'] || !we_base_permission::hasPerm("NEW_GRAFIK")){
			return;
		}

		$html = we_base_imageEdit::qualitySelect("fu_doc_quality", $this->imageEditProps['quality']);
		$headline = g_l('weClass', '[quality]');

		return $this->formElements[$name]['multiIconBox'] ? $this->makeMultiIconRow($name, $headline, $html) : $html;
	}

	public static function getHtmlLoup(){
		$divLoupe = we_html_element::htmlDiv(['id' => 'we_fileUpload_loupe', 'class' => 'editorLoupe'], we_html_element::htmlDiv(['id' => 'we_fileUpload_loupeInner', 'class' => 'editorLoupeInner']) .
				we_html_element::htmlDiv(['id' => 'we_fileUpload_loupeInfo', 'class' => 'editorLoupeInfo']) .
				we_html_element::htmlDiv(['id' => 'we_fileUpload_focusPoint', 'class' => 'editorFocusPoint'])
		);
		$divLoupeFallback = we_html_element::htmlDiv(['id' => 'we_fileUpload_loupeFallback', 'class' => 'editorLoupeFallback']); //editorLoupeFallback
		$divLoupeCrosshairH = we_html_element::htmlDiv(['class' => 'editorCrosshairH']);
		$divLoupeCrosshairV = we_html_element::htmlDiv(['class' => 'editorCrosshairV']);
		$divFixedFocusPoint = we_html_element::htmlDiv(['id' => 'editorFocuspointFixed', 'class' => 'editorFocusPoint focusPointOnSet']);
		$divLoupeSpinner = we_html_element::htmlDiv(['id' => 'we_fileUpload_spinner', 'class' => 'editorLoupeSpinner'], we_html_element::htmlSpan([], '<i class="fa fa-2x fa-spinner fa-pulse"></i>'));

		return $divLoupe . $divLoupeFallback . $divLoupeCrosshairH . $divLoupeCrosshairV . $divFixedFocusPoint . $divLoupeSpinner . $divLoupeMessage;
	}

	protected function makeMultiIconRow($formname, $headline, $html){
		$row = [
			'headline' => $headline,
			'html' => $html,
			'class' => 'weFileUploadEditorElem' . (!empty($this->formElements[$formname]['class']) ? ' ' . $this->formElements[$formname]['class'] : ''),
			//'noline' => ,//$this->formElements[$formname]['noline'],
			'forceRightHeadline' => $this->formElements[$formname]['rightHeadline'],
			'space' => !empty($this->formElements[$formname]['space']) ? $this->formElements[$formname]['space'] : 0
		];

		return !empty($this->formElements[$formname]['noline']) ? array_merge($row, ['noline' => true]) : $row;
	}

	public function setIsExternalBtnUpload($isExternal){
		$this->isExternalBtnUpload = $isExternal;
	}

	public function setFieldParentID($parentID = []){
		$this->parentID = array_merge($this->parentID, $parentID);
	}

	public function setFormElements($formElements = []){
		$this->formElements = array_merge($this->formElements, $formElements);
	}

}
