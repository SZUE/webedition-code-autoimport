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

/**
 * Class which contains all functions for the
 * rebuild dialog and the rebuild function
 * @static
 */
abstract class we_rebuild_wizard{

	/**
	 * returns HTML for the Body Frame
	 *
	 * @return string
	 */
	static function getBody(){
		switch(we_base_request::_(we_base_request::INT, 'step', 0)){
			default:
			case 0:
				return self::getPage(self::getStep0());
			case 1:
				return self::getPage(self::getStep1());
			case 2:
				return self::getPage(self::getStep2());
		}
	}

	/**
	 * returns HTML for the Frame with the progress bar
	 *
	 * @return string
	 */
	static function getBusy(){
		$dc = we_base_request::_(we_base_request::INT, 'dc', 0);

		$WE_PB = new we_progressBar(0, ($dc ? 490 : 200));
		$WE_PB->addText(g_l('rebuild', '[savingDocument]'), we_progressBar::TOP, 'pb1');

		$cancelButton = we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close();");
		$refreshButton = we_html_button::create_button(we_html_button::REFRESH, "javascript:parent.wizcmd.location.reload();", '', 0, 0, "", "", false, false);

		$nextbutdisabled = !(we_base_permission::hasPerm(["REBUILD_ALL", "REBUILD_FILTERD", "REBUILD_OBJECTS", "REBUILD_INDEX", "REBUILD_THUMBS", "REBUILD_META"]));

		if($dc){
			$pb = we_html_tools::htmlDialogLayout($WE_PB->getHTML(), g_l('rebuild', '[rebuild]'), $refreshButton . $cancelButton);
		} else {
			$prevButton = we_html_button::create_button(we_html_button::BACK, "javascript:parent.wizbody.handle_event('previous');", '', 0, 0, "", "", true, false);
			$nextButton = we_html_button::create_button(we_html_button::NEXT, "javascript:parent.wizbody.handle_event('next');", '', 0, 0, "", "", $nextbutdisabled, false);

			$content2 = we_html_element::htmlSpan(["id" => "prev", 'style' => "padding-left:10px;text-align:right"], $prevButton) .
				we_html_element::htmlSpan(["id" => "next", 'style' => "padding-left:10px;text-align:right"], $nextButton) .
				we_html_element::htmlSpan(["id" => "refresh", 'style' => "display:none; padding-left:10px;text-align:right"], $refreshButton) .
				we_html_element::htmlSpan(["id" => "cancel", 'style' => "padding-left:10px;text-align:right"], $cancelButton);

			$content = new we_html_table(["width" => "100%"], 1, 2);
			$content->setCol(0, 0, ["id" => "progr", 'style' => "padding-left:1em;text-align:left"], $WE_PB->getHTML('', 'display:none;'));
			$content->setCol(0, 1, ['style' => "text-align:right"], $content2);
		}

		return we_html_tools::getHtmlTop(g_l('rebuild', '[rebuild]'), '', '', we_progressBar::getJSCode() . we_html_element::jsScript(JS_DIR . 'nextButtons.js'), we_html_element::htmlBody([
					'style' => 'overflow:hidden', "class" => ($dc ? "weDialogBody" : "weDialogButtonsBody")], ($dc ? $pb : $content->getHtml())
				)
		);
	}

	/**
	 * returns HTML for the Cmd Frame
	 *
	 * @return string for now it is an empty page
	 */
	static function getCmd(){
		return self::getPage(['', '', '']);
	}

	/**
	 * returns the HTML for the First Step (0) of the wizard
	 *
	 * @return string
	 */
	private static function getStep0(){
		$dws = get_def_ws();
		$btype = we_base_request::_(we_base_request::STRING, 'btype', 'rebuild_all');
		$categories = we_base_request::_(we_base_request::STRING, 'categories', '');
		$doctypes = implode(',', we_base_request::_(we_base_request::STRING, 'doctypes', []));
		$folders = we_base_request::_(we_base_request::INTLIST, 'folders', ($dws ?: ''));
		$maintable = we_base_request::_(we_base_request::BOOL, 'maintable');
		$tmptable = false; //we_base_request::_(we_base_request::INT, 'tmptable', 0);
		$thumbsFolders = we_base_request::_(we_base_request::INTLIST, 'thumbsFolders', ($dws ?: ''));
		$thumbs = implode(',', we_base_request::_(we_base_request::INT, 'thumbs', []));
		$catAnd = we_base_request::_(we_base_request::BOOL, 'catAnd');
		$metaFolders = we_base_request::_(we_base_request::STRING, 'metaFolders', ($dws ?: ''));
		$metaFields = we_base_request::_(we_base_request::RAW, '_field', []);
		$onlyEmpty = we_base_request::_(we_base_request::BOOL, 'onlyEmpty');

		if(($type = we_base_request::_(we_base_request::STRING, 'type'))){

		} elseif(we_base_permission::hasPerm(['REBUILD_ALL', 'REBUILD_FILTERD'])){
			$type = 'rebuild_documents';
		} else if(defined('OBJECT_FILES_TABLE') && we_base_permission::hasPerm('REBUILD_OBJECTS')){
			$type = 'rebuild_objects';
		} else if(we_base_permission::hasPerm('REBUILD_INDEX')){
			$type = 'rebuild_index';
		} else if(we_base_permission::hasPerm('REBUILD_THUMBS')){
			$type = 'rebuild_thumbnails';
		} else if(we_base_permission::hasPerm('REBUILD_NAVIGATION')){
			$type = 'rebuild_navigation';
		} else if(we_base_permission::hasPerm('REBUILD_META')){
			$type = 'rebuild_metadata';
		} else {
			$type = '';
		}

		$parts = [['headline' => '',
			'html' => we_html_forms::radiobutton('rebuild_documents', ($type === 'rebuild_documents' && (we_base_permission::hasPerm(['REBUILD_ALL', 'REBUILD_FILTERD']))), 'type', g_l('rebuild', '[documents]') . ' ' . we_html_tools::htmlAlertAttentionBox(g_l('rebuild', '[txt_rebuild_documents]'), we_html_tools::TYPE_HELP, false), true, 'defaultfont', 'setNavStatDocDisabled()', (!(we_base_permission::hasPerm([
					'REBUILD_ALL', 'REBUILD_FILTERD'])))),
			]
		];

		if(defined('OBJECT_FILES_TABLE')){
			$parts[] = ['headline' => '',
				'html' => we_html_forms::radiobutton('rebuild_objects', ($type === 'rebuild_objects' && we_base_permission::hasPerm('REBUILD_OBJECTS')), 'type', g_l('rebuild', '[rebuild_objects]') . ' ' . we_html_tools::htmlAlertAttentionBox(g_l('rebuild', '[txt_rebuild_objects]'), we_html_tools::TYPE_HELP, false), true, 'defaultfont', 'setNavStatDocDisabled()', (!we_base_permission::hasPerm('REBUILD_OBJECTS'))),
			];
		}

		$parts[] = ['headline' => '',
			'html' => we_html_forms::radiobutton('rebuild_index', ($type === 'rebuild_index' && we_base_permission::hasPerm('REBUILD_INDEX')), 'type', g_l('rebuild', '[rebuild_index]') . ' ' . we_html_tools::htmlAlertAttentionBox(g_l('rebuild', '[txt_rebuild_index]'), we_html_tools::TYPE_HELP, false), true, 'defaultfont', 'setNavStatDocDisabled()', (!we_base_permission::hasPerm('REBUILD_INDEX'))),
		];

		$parts[] = ['headline' => '',
			'html' => we_html_forms::radiobutton('rebuild_thumbnails', ($type === 'rebuild_thumbnails' && we_base_permission::hasPerm('REBUILD_THUMBS')), 'type', g_l('rebuild', '[thumbnails]') . ' ' . we_html_tools::htmlAlertAttentionBox(g_l('rebuild', '[txt_rebuild_thumbnails]'), we_html_tools::TYPE_HELP, false), true, 'defaultfont', 'setNavStatDocDisabled()', (we_base_imageEdit::gd_version() == 0 || (!we_base_permission::hasPerm('REBUILD_THUMBS')))),
		];

		$navRebuildHTML = '<div>' .
			we_html_forms::radiobutton('rebuild_navigation', ($type === 'rebuild_navigation' && we_base_permission::hasPerm('REBUILD_NAVIGATION')), 'type', g_l('rebuild', '[navigation]') . ' ' . we_html_tools::htmlAlertAttentionBox(g_l('rebuild', '[txt_rebuild_navigation]'), we_html_tools::TYPE_HELP, false), false, 'defaultfont', 'setNavStatDocDisabled()', !we_base_permission::hasPerm('REBUILD_NAVIGATION')) .
			'</div><div style="padding:10px 20px;">' .
			we_html_forms::checkbox(1, false, 'rebuildStaticAfterNavi', g_l('rebuild', '[rebuildStaticAfterNaviCheck]') . ' ' . we_html_tools::htmlAlertAttentionBox(g_l('rebuild', '[rebuildStaticAfterNaviHint]'), we_html_tools::TYPE_HELP, false), false, 'defaultfont', '', true) .
			'</div>';

		$parts[] = ['headline' => '',
			'html' => $navRebuildHTML,
		];

		$metaDataFields = we_metadata_metaData::getDefinedMetaDataFields();

		$rebuildMetaDisabled = true;
		foreach($metaDataFields as $md){
			if($md['importFrom'] !== ''){
				$rebuildMetaDisabled = false;
				break;
			}
		}

		$parts[] = ['headline' => '',
			'html' => we_html_forms::radiobutton('rebuild_metadata', ($type === 'rebuild_metadata' && we_base_permission::hasPerm('REBUILD_META')), 'type', g_l('rebuild', '[metadata]') . ' ' . we_html_tools::htmlAlertAttentionBox(g_l('rebuild', '[txt_rebuild_metadata]'), we_html_tools::TYPE_HELP, false), true, 'defaultfont', 'setNavStatDocDisabled()', (!we_base_permission::hasPerm('REBUILD_META')) || $rebuildMetaDisabled),
		];

		$parts[] = ['headline' => '',
			'html' => we_html_forms::radiobutton('rebuild_medialinks', ($type === 'rebuild_medialinks' && true), 'type', g_l('rebuild', '[media_links]') . ' ' . we_html_tools::htmlAlertAttentionBox(g_l('rebuild', '[txt_media_links]'), we_html_tools::TYPE_HELP, false), true, 'defaultfont', '', false),
		];

		$allbutdisabled = !(we_base_permission::hasPerm(['REBUILD_ALL', 'REBUILD_FILTERD', 'REBUILD_OBJECTS', 'REBUILD_INDEX', 'REBUILD_THUMBS', 'REBUILD_META']));


		$js = '
			WE().util.loadConsts(document, "g_l.rebuild");
WE().session.rebuild={};
window.onload = function(){top.focus();}
set_button_state(' . ($allbutdisabled ? 1 : 0) . ');
';

		$dthidden = '';
		$doctypesArray = makeArrayFromCSV($doctypes);
		foreach($doctypesArray as $k => $v){
			$dthidden .= we_html_element::htmlHidden("doctypes[$k]", $v);
		}

		$thumbsHidden = "";
		$thumbsArray = makeArrayFromCSV($thumbs);
		foreach($thumbsArray as $k => $v){
			$thumbsHidden .= we_html_element::htmlHidden("thumbs[$k]", $v);
		}

		$metaFieldsHidden = "";
		foreach($metaFields as $key => $val){
			$metaFieldsHidden .= we_html_element::htmlHidden('_field[' . $key . ']', $val);
		}

		return [we_html_element::jsScript(JS_DIR . 'rebuild0.js'),
			$js,
			we_html_multiIconBox::getHTML("", $parts, 40, "", -1, "", "", false, g_l('rebuild', '[rebuild]')) .
			$dthidden .
			$thumbsHidden .
			$metaFieldsHidden .
			we_html_element::htmlHiddens([
				"catAnd" => $catAnd,
				"thumbsFolders" => $thumbsFolders,
				"metaFolders" => $metaFolders,
				"maintable" => $maintable,
				"tmptable" => $tmptable,
				"categories" => $categories,
				"folders" => $folders,
				"fr" => "body",
				"btype" => $btype,
				"onlyEmpty" => $onlyEmpty,
				"we_cmd[0]" => "rebuild",
				"step" => 1])];
	}

	/**
	 * returns the HTML for the Second Step (1) of the wizard
	 *
	 * @return string
	 */
	private static function getStep1(){
		switch(we_base_request::_(we_base_request::STRING, "type", "rebuild_documents")){
			case "rebuild_documents":
				return we_rebuild_wizard::getRebuildDocuments();
			case "rebuild_thumbnails":
				return we_rebuild_wizard::getRebuildThumbnails();
			case "rebuild_metadata":
				return we_rebuild_wizard::getRebuildMetadata();
		}
	}

	/**
	 * returns the HTML for the Third Step (2) of the wizard. - Here the real work (loop) is done - it should be displayed in the cmd frame
	 *
	 * @return string
	 */
	private static function getStep2(){
		$btype = we_base_request::_(we_base_request::STRING, "btype", "rebuild_all");
		$categories = we_base_request::_(we_base_request::INTLIST, "categories", "");
		$doctypes = implode(',', we_base_request::_(we_base_request::INT, "doctypes", []));
		$folders = we_base_request::_(we_base_request::INTLIST, "folders", "");
		$maintable = we_base_request::_(we_base_request::BOOL, "maintable", 0);
		$thumbsFolders = we_base_request::_(we_base_request::INTLIST, "thumbsFolders", "");
		$thumbs = implode(',', we_base_request::_(we_base_request::STRING, "thumbs", []));
		$catAnd = we_base_request::_(we_base_request::BOOL, "catAnd");
		$templateID = we_base_request::_(we_base_request::INT, "templateID", 0);
		$metaFolders = we_base_request::_(we_base_request::INTLIST, "metaFolders", (get_def_ws() ?: ""));
		$metaFields = we_base_request::_(we_base_request::INT, "_field", []);
		$onlyEmpty = we_base_request::_(we_base_request::BOOL, 'onlyEmpty');

		$taskname = md5(session_id() . '_rebuild');
		$currentTask = we_base_request::_(we_base_request::INT, 'fr_' . $taskname . '_ct', 0);
		$taskFilename = WE_FRAGMENT_PATH . $taskname;

		$js = 'function set_button_state() {
				if(top.wizbusy){
					top.wizbusy.back_enabled = WE().layout.button.switch_button_state(top.wizbusy.document, "back", "enabled");
					top.wizbusy.next_enabled = WE().layout.button.switch_button_state(top.wizbusy.document, "next", "enabled");
				}else{
					window.setTimeout(set_button_state,300);
				}
			}
			set_button_state();';
		if(!(file_exists($taskFilename) && $currentTask)){
			switch(we_base_request::_(we_base_request::STRING, 'type', 'rebuild_documents')){
				case 'rebuild_documents':
					$data = we_rebuild_base::getDocuments($btype, $categories, $catAnd, $doctypes, $folders, $maintable, false, $templateID);
					break;
				case 'rebuild_thumbnails':
					if(!$thumbs){
						return ['',
							$js . ';top.wizbusy.showPrevNextButton();' . we_message_reporting::getShowMessageCall(g_l('rebuild', '[no_thumbs_selected]'), we_message_reporting::WE_MESSAGE_ERROR),
							''];
					}
					$data = we_rebuild_base::getThumbnails($thumbs, $thumbsFolders);
					break;
				case 'rebuild_index':
					$data = we_rebuild_base::getIndex();
					break;
				case 'rebuild_objects':
					$data = we_rebuild_base::getObjects();
					break;
				case 'rebuild_navigation':
					$data = we_rebuild_base::getNavigation();
					break;
				case 'rebuild_metadata':
					$data = we_rebuild_base::getMetadata($metaFields, $onlyEmpty, $metaFolders);
					break;
				case 'rebuild_medialinks':
					$data = we_rebuild_base::getMedialinks();
					break;
			}
			if($data){
				$fr = new we_rebuild_fragment($taskname, 5, [], $data);

				return [];
			}
			return ['',
				$js . we_message_reporting::getShowMessageCall(g_l('rebuild', '[nothing_to_rebuild]'), we_message_reporting::WE_MESSAGE_ERROR) . 'top.wizbusy.showPrevNextButton();',
				''];
		}
		switch(we_base_request::_(we_base_request::STRING, "type", "rebuild_documents")){
			case 'rebuild_documents':
				$count = 1; //FIXME: we need a solution for static documents with e.g. <we:ifSelf> in navigation
				break;
			case 'rebuild_thumbnails':
				$count = 4;
				break;
			case 'rebuild_index':
				$count = 8;
				break;
			case 'rebuild_objects':
				$count = 10;
				break;
			case 'rebuild_navigation':
				if(we_base_request::_(we_base_request::BOOL, 'rebuildStaticAfterNavi')){//static documents consume more time, we have to be patient
					$count = 4;
					break;
				}
			case 'rebuild_metadata':
				$count = 15;
				break;
			case 'rebuild_medialinks':
				$count = 8;
				break;
		}

		$fr = new we_rebuild_fragment($taskname, $count, []);

		return [];
	}

	/**
	 * returns HTML for the category form
	 *
	 * @return string
	 * @param string $categories csv value with category IDs
	 * @param boolean $catAnd if the categories should be connected with AND
	 */
	static function formCategory($categories, $catAnd){
		$catAndCheck = we_html_forms::checkbox(1, $catAnd, "catAnd", g_l('rebuild', '[catAnd]'), false, "defaultfont", "document.we_form.btype[2].checked=true;");
		$delallbut = we_html_button::create_button(we_html_button::DELETE_ALL, "javascript:document.we_form.btype[2].checked=true;we_cmd('del_all_cats')");
		$addbut = we_html_button::create_button(we_html_button::ADD, "javascript:document.we_form.btype[2].checked=true;we_cmd('we_selector_category',-1,'" . CATEGORY_TABLE . "','','','add_cat')");
		$upperTable = '<table class="default" style="width:495px;"><tr><td style="text-align:left">' . $catAndCheck . '</td><td style="text-align:right">' . $delallbut . $addbut . '</td></tr></table>';

		$cats = new we_chooser_multiDir(495, $categories, "del_cat", $upperTable, '', '"we/category"', CATEGORY_TABLE);
		return g_l('global', '[categorys]') . '<br/><br/>' . $cats->get();
	}

	/**
	 * returns HTML for the doctypes form
	 *
	 * @return string
	 * @param string $doctypes csv value with doctype IDs
	 */
	static function formDoctypes($doctypes){

		$GLOBALS['DB_WE']->query('SELECT dt.ID,dt.DocType FROM ' . DOC_TYPES_TABLE . ' dt ORDER BY dt.DocType');
		$DTselect = g_l('global', '[doctypes]') . "<br/><br/>" . '<select class="defaultfont" name="doctypes[]" size="5" multiple style="width: 495px" onchange="document.we_form.btype[2].checked=true;">';

		$doctypesArray = makeArrayFromCSV($doctypes);
		while($GLOBALS['DB_WE']->next_record()){
			$DTselect .= '<option value="' . $GLOBALS['DB_WE']->f("ID") . '"' . (in_array($GLOBALS['DB_WE']->f("ID"), $doctypesArray) ? " selected" : "") . '>' . $GLOBALS['DB_WE']->f("DocType") . "</option>";
		}
		$DTselect .= '</select>';
		return $DTselect;
	}

	/**
	 * returns HTML for the directories form
	 *
	 * @return string
	 * @param string $folders csv value with directory IDs
	 * @param boolean $thumnailpage if it should displayed in the thumbnails page or on an other page
	 */
	static function formFolders($folders, $thumnailpage = false, $width = 495){
		$delallbut = we_html_button::create_button(we_html_button::DELETE_ALL, "javascript:" . ($thumnailpage ? "" : "document.we_form.btype[2].checked=true;") . "we_cmd('del_all_folders')");
		$addbut = we_html_button::create_button(we_html_button::ADD, "javascript:" . ($thumnailpage ? "" : "document.we_form.btype[2].checked=true;") . "we_cmd('we_selector_directory','','" . FILE_TABLE . "','','','add_folder','','','',1)");

		$dirs = new we_chooser_multiDir($width, $folders, "del_folder", $delallbut . $addbut, '', 'ContentType', FILE_TABLE);

		return g_l('rebuild', ($thumnailpage ? '[thumbdirs]' : '[dirs]')) . '<br/><br/>' . $dirs->get();
	}

	/**
	 * returns HTML for the thumbnails form
	 *
	 * @return string
	 * @param string $thumbs csv value with thumb IDs
	 */
	private static function formThumbs($thumbs){
		$GLOBALS['DB_WE']->query('SELECT ID,Name,description FROM ' . THUMBNAILS_TABLE . ' ORDER BY Name');
		$Thselect = g_l('rebuild', '[thumbnails]') . '<br/><br/>' .
			'<select class="defaultfont" name="thumbs[]" size="10" multiple style="width: 520px">';

		$thumbsArray = makeArrayFromCSV($thumbs);
		while($GLOBALS['DB_WE']->next_record()){
			$Thselect .= '<option title="' . $GLOBALS['DB_WE']->f('description') . '" value="' . $GLOBALS['DB_WE']->f("ID") . '"' . (in_array($GLOBALS['DB_WE']->f("ID"), $thumbsArray) ? ' selected' : '') . '>' . $GLOBALS['DB_WE']->f("Name") . "</option>";
		}
		$Thselect .= '</select>';
		return $Thselect;
	}

	static function formMetadata($metaFields, $onlyEmpty){
		$metaDataFields = we_metadata_metaData::getDefinedMetaDataFields();

		$html = we_html_element::jsElement('document._errorMessage=' . (empty($metaFields) ? '"' . addslashes(g_l('rebuild', '[noFieldsChecked]')) . '"' : '""' )) .
			we_html_tools::htmlAlertAttentionBox(g_l('rebuild', '[expl_rebuild_metadata]'), we_html_tools::TYPE_INFO, 520) .
			'<div class="defaultfont" style="margin:10px 0 5px 0;">' . g_l('rebuild', '[metadata]') . ':</div>';

		$selAllBut = we_html_button::create_button(we_html_button::TOGGLE, "javascript:we_cmd('toggle_all_fields');");

		foreach($metaDataFields as $md){
			if($md['importFrom']){
				$html .= we_html_forms::checkbox(1, (!empty($metaFields[$md['tag']])), "_field[" . $md['tag'] . "]", $md['tag'], false, "defaultfont", "checkForError()");
			}
		}

		$html .= we_html_element::htmlSpan(['style' => 'margin:10px 0 20px 0;'], $selAllBut) .
			we_html_forms::checkbox(1, $onlyEmpty, 'onlyEmpty', g_l('rebuild', '[onlyEmpty]'));

		return $html;
	}

	/**
	 * returns Array with javascript (array[0]) and HTML Content (array[1]) for the rebuild document page
	 *
	 * @return array
	 */
	static function getRebuildDocuments(){
		$thumbsFolders = we_base_request::_(we_base_request::INTLIST, 'thumbsFolders', '');
		$metaFolders = we_base_request::_(we_base_request::INTLIST, 'metaFolders', '');
		$metaFields = we_base_request::_(we_base_request::INT, '_field', '');
		$thumbs = implode(',', we_base_request::_(we_base_request::INT, 'thumbs', []));
		$type = we_base_request::_(we_base_request::STRING, 'type', 'rebuild_documents');
		$btype = we_base_request::_(we_base_request::STRING, 'btype', 'rebuild_all');
		$categories = we_base_request::_(we_base_request::INTLIST, 'categories', '');
		$doctypes = implode(',', we_base_request::_(we_base_request::INT, 'doctypes', []));
		$folders = we_base_request::_(we_base_request::INTLIST, 'folders', '');
		$maintable = we_base_request::_(we_base_request::BOOL, 'maintable');
		$catAnd = we_base_request::_(we_base_request::BOOL, 'catAnd');
		$onlyEmpty = we_base_request::_(we_base_request::BOOL, 'onlyEmpty', 0);


		$ws = get_ws(FILE_TABLE, true);
		if($ws && !in_array(0, $ws) && (!$folders)){
			$folders = get_def_ws(FILE_TABLE);
		}

		$all_content = (we_base_permission::hasPerm('ADMINISTRATOR') ?
			we_html_forms::checkbox(1, $maintable, 'maintable', g_l('rebuild', '[rebuild_maintable]'), false, 'defaultfont', 'document.we_form.btype[0].checked=true;') :
			'');

		$filter_content = we_rebuild_wizard::formCategory($categories, $catAnd) . '<br/><br/>' .
			we_rebuild_wizard::formDoctypes($doctypes) . '<br/><br/>' .
			we_rebuild_wizard::formFolders($folders);

		$filter_content = we_html_forms::radiobutton('rebuild_filter', ($btype === 'rebuild_filter' && we_base_permission::hasPerm('REBUILD_FILTERD') || ($btype === 'rebuild_all' && (!we_base_permission::hasPerm('REBUILD_ALL')) && we_base_permission::hasPerm('REBUILD_FILTERD'))), 'btype', g_l('rebuild', '[rebuild_filter]'), true, 'defaultfont', '', (!we_base_permission::hasPerm('REBUILD_FILTERD')), g_l('rebuild', '[txt_rebuild_filter]'), 0, 495, '', $filter_content);


		$parts = [['headline' => '',
			'html' => we_html_forms::radiobutton('rebuild_all', ($btype === 'rebuild_all' && we_base_permission::hasPerm('REBUILD_ALL')), 'btype', g_l('rebuild', '[rebuild_all]'), true, 'defaultfont', '', (!we_base_permission::hasPerm('REBUILD_ALL')), g_l('rebuild', '[txt_rebuild_all]'), 0, 495, '', $all_content),
			],
			['headline' => '',
				'html' => we_html_forms::radiobutton('rebuild_templates', ($btype === 'rebuild_templates' && we_base_permission::hasPerm('REBUILD_TEMPLATES')), 'btype', g_l('rebuild', '[rebuild_templates]'), true, 'defaultfont', '', (!we_base_permission::hasPerm('REBUILD_TEMPLATES')), g_l('rebuild', '[txt_rebuild_templates]'), 0, 495),
			],
			['headline' => '',
				'html' => $filter_content,
			]
		];

		$thumbsHidden = '';
		$thumbsArray = makeArrayFromCSV($thumbs);
		foreach($thumbsArray as $i => $cur){
			$thumbsHidden .= we_html_element::htmlHidden('thumbs[' . $i . ']', $cur);
		}

		$metaFieldsHidden = '';
		if($metaFields){
			foreach($metaFields as $key => $val){
				$metaFieldsHidden .= we_html_element::htmlHidden('_field[' . $key . ']', $val);
			}
		}
		return [we_html_element::jsScript(JS_DIR . 'rebuild2.js'), 'WE().session.rebuild.folders="folders";',
			we_html_multiIconBox::getHTML('', $parts, 40, '', -1, '', '', false, g_l('rebuild', '[rebuild_documents]')) .
			$thumbsHidden .
			$metaFieldsHidden .
			we_html_element::htmlHiddens(['thumbsFolders' => $thumbsFolders,
				'metaFolders' => $metaFolders,
				'metaFields' => $metaFields,
				'onlyEmpty' => $onlyEmpty,
				'folders' => $folders,
				'categories' => $categories,
				'fr' => 'body',
				'type' => $type,
				'we_cmd[0]' => 'rebuild',
				'step' => 2])];
	}

	/**
	 * returns Array with javascript (array[0]) and HTML Content (array[1]) for the rebuild metadata page
	 *
	 * @return array
	 */
	static function getRebuildThumbnails(){

		$thumbsFolders = we_base_request::_(we_base_request::INTLIST, 'thumbsFolders', '');
		$metaFolders = we_base_request::_(we_base_request::INTLIST, 'metaFolders', '');
		$metaFields = we_base_request::_(we_base_request::INT, '_field', []);
		$thumbs = implode(',', we_base_request::_(we_base_request::INT, 'thumbs', []));
		$type = we_base_request::_(we_base_request::STRING, 'type', 'rebuild_documents');
		$categories = we_base_request::_(we_base_request::INTLIST, 'categories', '');
		$doctypes = implode(',', we_base_request::_(we_base_request::INT, 'doctypes', []));
		$folders = we_base_request::_(we_base_request::INTLIST, 'folders', '');
		$catAnd = we_base_request::_(we_base_request::BOOL, 'catAnd', 0);
		$onlyEmpty = we_base_request::_(we_base_request::BOOL, 'onlyEmpty', 0);

		$ws = get_ws(FILE_TABLE, true);

		// check if folers are in Workspace of User

		if($ws && $folders){
			$newFolders = [];
			$foldersArray = makeArrayFromCSV($folders);
			foreach($foldersArray as $folder){
				if(we_users_util::in_workspace($folder, $ws)){
					$newFolders[] = $folder;
				}
			}
			$folders = implode(',', $newFolders);
		}

		if($ws && !in_array(0, $ws) && ($thumbsFolders == '' || $thumbsFolders == '0')){
			$thumbsFolders = get_def_ws(FILE_TABLE);
		}
		$parts = [];

		$content = we_rebuild_wizard::formThumbs($thumbs) .
			'<br/><br/>' .
			we_rebuild_wizard::formFolders($thumbsFolders, true, 520);

		$parts[] = ['headline' => '',
			'html' => $content,
		];

		$dthidden = '';
		$doctypesArray = makeArrayFromCSV($doctypes);
		foreach($doctypesArray as $key => $val){
			$dthidden .= we_html_element::htmlHidden('doctypes[' . $key . ']', $val);
		}
		$metaFieldsHidden = '';
		foreach($metaFields as $key => $val){
			$metaFieldsHidden .= we_html_element::htmlHidden("_field[$key]", $val);
		}
		return [we_html_element::jsScript(JS_DIR . 'rebuild2.js'), 'WE().session.rebuild.folders="thumbsFolders";',
			we_html_multiIconBox::getHTML('', $parts, 40, '', -1, '', '', false, g_l('rebuild', '[rebuild_thumbnails]')) .
			$dthidden .
			$metaFieldsHidden .
			we_html_element::htmlHiddens(['catAnd' => $catAnd,
				'thumbsFolders' => $thumbsFolders,
				'metaFolders' => $metaFolders,
				'onlyEmpty' => $onlyEmpty,
				'folders' => $folders,
				'categories' => $categories,
				'fr' => 'body',
				'type' => $type,
				'we_cmd[0]' => 'rebuild',
				'step' => 2])];
	}

	static function getRebuildMetadata(){
		$thumbsFolders = we_base_request::_(we_base_request::INTLIST, 'thumbsFolders', '');
		$metaFolders = we_base_request::_(we_base_request::INTLIST, 'metaFolders', '');
		$onlyEmpty = we_base_request::_(we_base_request::BOOL, 'onlyEmpty');
		$metaFields = we_base_request::_(we_base_request::RAW, '_field', []);
		$thumbs = we_base_request::_(we_base_request::INT, 'thumbs', []);
		$type = we_base_request::_(we_base_request::STRING, 'type', 'rebuild_documents');
		$categories = we_base_request::_(we_base_request::INTLIST, 'categories', '');
		$doctypes = we_base_request::_(we_base_request::INT, 'doctypes', []);
		$folders = we_base_request::_(we_base_request::INTLIST, 'folders', '');
		$catAnd = we_base_request::_(we_base_request::BOOL, 'catAnd');

		$ws = get_ws(FILE_TABLE, true);

		// check if folers are in Workspace of User

		if($ws && $folders){
			$newFolders = [];
			//$wsArray = makeArrayFromCSV($ws);
			$foldersArray = makeArrayFromCSV($folders);
			for($i = 0; $i < count($foldersArray); $i++){
				if(we_users_util::in_workspace($foldersArray[$i], $ws)){
					$newFolders[] = $foldersArray[$i];
				}
			}
			$folders = implode(',', $newFolders);
		}

		if($ws && strpos($ws, (',0,')) !== true && ($metaFolders == '' || $metaFolders == '0')){
			$metaFolders = get_def_ws(FILE_TABLE);
		}

		$content = we_rebuild_wizard::formMetadata($metaFields, $onlyEmpty) .
			we_html_element::htmlBr() . we_html_element::htmlBr() .
			we_rebuild_wizard::formFolders($metaFolders, true, 520);

		$parts = [
			['headline' => '',
				'html' => $content,
			]
		];

		$dthidden = '';
		foreach($doctypes as $doctype){
			$dthidden .= we_html_element::htmlHidden('doctypes[]', $doctype);
		}
		$thumbsHidden = '';
		foreach($thumbs as $thumb){
			$thumbsHidden .= we_html_element::htmlHidden('thumbs[]', $thumb);
		}
		return [we_html_element::jsScript(JS_DIR . 'rebuild2.js'), 'WE().session.rebuild.folders="metaFolders";',
			we_html_multiIconBox::getHTML('', $parts, 40, '', -1, '', '', false, g_l('rebuild', '[rebuild_metadata]')) .
			$dthidden .
			$thumbsHidden .
			we_html_element::htmlHiddens(['catAnd' => $catAnd,
				'metaFolders' => $metaFolders,
				'thumbsFolders' => $thumbsFolders,
				'folders' => $folders,
				'categories' => $categories,
				'fr' => 'body',
				'type' => $type,
				'we_cmd[0]' => 'rebuild',
				'step' => 2])];
	}

	/**
	 * returns HTML for the frameset
	 *
	 * @return string
	 */
	static function getFrameset(){
		$btype = we_base_request::_(we_base_request::STRING, 'btype');
		$type = we_base_request::_(we_base_request::STRING, 'type');
		$tid = we_base_request::_(we_base_request::INT, 'templateID');
		$step = we_base_request::_(we_base_request::INT, 'step');
		$resp = we_base_request::_(we_base_request::STRING, 'responseText');
		$tail = ($btype ? '&amp;btype=' . rawurlencode($btype) : '') .
			($type ? '&amp;type=' . rawurlencode($type) : '') .
			($tid ? '&amp;templateID=' . $tid : '') .
			($step ? '&amp;step=' . $step : '') .
			($resp ? '&amp;responseText=' . rawurlencode($resp) : '');

		$taskname = md5(session_id() . '_rebuild');
		$taskFilename = WE_FRAGMENT_PATH . $taskname;
		if(file_exists($taskFilename)){
			we_base_file::delete($taskFilename);
		}

		if($tail){
			$body = we_html_element::htmlBody(['id' => 'weMainBody', "onload" => "wizcmd.location=WE().consts.dirs.WEBEDITION_DIR+'we_cmd.php?we_cmd[0]=rebuild&amp;fr=body" . $tail . "';"]
					, we_html_element::htmlIFrame('wizbusy', WEBEDITION_DIR . "we_cmd.php?we_cmd[0]=rebuild&amp;fr=busy&amp;dc=1", 'position:absolute;top:0px;bottom:0px;left:0px;right:0px;overflow: hidden', '', '', false) .
					we_html_element::htmlIFrame('wizcmd', "about:blank", 'position:absolute;bottom:0px;height:0px;left:0px;right:0px;overflow: hidden;')
			);
		} else {
			$height = (we_base_browserDetect::isFF() ? 60 : 40);
			$body = we_html_element::htmlBody(['id' => 'weMainBody']
					, we_html_element::htmlIFrame('wizbody', WEBEDITION_DIR . "we_cmd.php?we_cmd[0]=rebuild&amp;fr=body", 'position:absolute;top:0px;bottom:' . $height . 'px;left:0px;right:0px;overflow: hidden') .
					we_html_element::htmlIFrame('wizbusy', WEBEDITION_DIR . "we_cmd.php?we_cmd[0]=rebuild&amp;fr=busy", 'position:absolute;height:' . $height . 'px;bottom:0px;left:0px;right:0px;overflow: hidden', '', '', false) .
					we_html_element::htmlIFrame('wizcmd', WEBEDITION_DIR . "we_cmd.php?we_cmd[0]=rebuild&amp;fr=cmd", 'position:absolute;bottom:0px;height:0px;left:0px;right:0px;overflow: hidden;')
			);
		}

		return we_html_tools::getHtmlTop(g_l('rebuild', '[rebuild]'), '', '', '', $body);
	}

	/**
	 * returns Javascript for step 2 (1)
	 *
	 * @return string
	 * @param array first element (array[0]) must be a javascript, second element (array[1]) must be the Body HTML
	 */
	static function getPage(array $contents){
		if(!$contents){
			return '';
		}
		return we_html_tools::getHtmlTop(g_l('rebuild', '[rebuild]'), '', '', $contents[0] .
				($contents[1] ?
				we_html_element::jsElement($contents[1]) :
				''), we_html_element::htmlBody(["class" => "weDialogBody"], we_html_element::htmlForm(['name' => 'we_form', "method" => "post", "action" => WEBEDITION_DIR . 'we_cmd.php'], $contents[2])
				)
		);
	}

	public static function showFrameset(){
		switch(we_base_request::_(we_base_request::STRING, 'fr')){
			case "body":
				echo self::getBody();
				break;
			case "busy":
				echo self::getBusy();
				break;
			case "cmd":
				echo self::getCmd();
				break;
			default:
				echo self::getFrameset();
		}
	}

	public static function getJSLangConsts(){
		return '
WE().consts.g_l.rebuild={
	noFieldsChecked:"' . we_message_reporting::prepareMsgForJS(g_l('rebuild', '[noFieldsChecked]')) . '",
};';
	}

}
