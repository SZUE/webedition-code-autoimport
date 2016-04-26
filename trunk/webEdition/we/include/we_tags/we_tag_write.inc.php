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
function we_tag_write($attribs){
	$type = weTag_getAttribute('type', $attribs, 'document', we_base_request::STRING);

	switch($type){
		case 'object':
			if(($foo = attributFehltError($attribs, 'classid', __FUNCTION__))){
				return $foo;
			}
			break;
		default:
			$type = 'document'; //make sure type is known!
			if(($foo = attributFehltError($attribs, 'doctype', __FUNCTION__))){
				return $foo;
			}
			break;
	}

	$name = weTag_getAttribute('formname', $attribs, (empty($GLOBALS['WE_FORM']) ? 'we_global_form' : $GLOBALS['WE_FORM']), we_base_request::STRING);

	$publish = weTag_getAttribute('publish', $attribs, false, we_base_request::BOOL);
	$triggerid = weTag_getAttribute('triggerid', $attribs, 0, we_base_request::INT);
	$charset = weTag_getAttribute('charset', $attribs, 'iso-8859-1', we_base_request::STRING);
	$categories = weTag_getAttribute('categories', $attribs, '', we_base_request::STRING);
	$classid = weTag_getAttribute('classid', $attribs, 0, we_base_request::INT);
	$userid = weTag_getAttribute('userid', $attribs, '', we_base_request::STRING); // deprecated  use protected=true instead
	$protected = weTag_getAttribute('protected', $attribs, false, we_base_request::BOOL);
	$admin = weTag_getAttribute('admin', $attribs, '', we_base_request::STRING);
	$mail = weTag_getAttribute('mail', $attribs, '', we_base_request::STRING); //FIXME: email_list
	$mailfrom = weTag_getAttribute('mailfrom', $attribs, '', we_base_request::EMAIL);
	$forceedit = weTag_getAttribute('forceedit', $attribs, false, we_base_request::BOOL) && !empty($_SESSION['webuser']['registered']);
	$workspaces = weTag_getAttribute('workspaces', $attribs, array(), we_base_request::INTLISTA);
	$objname = preg_replace('/[^a-z0-9_-]/i', '', weTag_getAttribute('name', $attribs, '', we_base_request::STRING));
	$onduplicate = ($objname === '' ? 'overwrite' : weTag_getAttribute('onduplicate', $attribs, 'increment', we_base_request::STRING));
	$onpredefinedname = weTag_getAttribute('onpredefinedname', $attribs, 'appendto', we_base_request::STRING);
	$workflowname = weTag_getAttribute('workflowname', $attribs, '', we_base_request::STRING);
	$workflowuserid = weTag_getAttribute('workflowuserid', $attribs, 0, we_base_request::INT);
	$doworkflow = ($workflowname != '' && $workflowuserid != 0);
	$searchable = weTag_getAttribute('searchable', $attribs, true, we_base_request::BOOL);
	$language = weTag_getAttribute('language', $attribs, '', we_base_request::STRING);

	if(we_base_request::_(we_base_request::BOOL, 'edit_' . $type)){

		switch($type){
			case 'document':
				$tid = weTag_getAttribute('tid', $attribs, 0, we_base_request::INT);
				$doctype = weTag_getAttribute('doctype', $attribs, '', we_base_request::STRING);
				$id = we_base_request::_(we_base_request::INT, 'we_editDocument_ID', 0);
				$ok = we_webEditionDocument::initDocument($name, $tid, $doctype, $categories, $id, true);
				break;
			case 'object':
				$parentid = weTag_getAttribute('parentid', $attribs, 0, we_base_request::INT);
				$id = we_base_request::_(we_base_request::INT, 'we_editObject_ID', 0);

				if(f('SELECT 1 FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($classid))){
					if(!$id || f('SELECT 1 FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . $id . ' AND TableID=' . intval($classid))){
						$ok = we_objectFile::initObject(intval($classid), $name, $categories, intval($parentid), $id, true);
					} else {
						$GLOBALS['ERROR']['write']['object'][$name] = true;
						t_e('Object ' . $id . ' is no element of class ' . intval($classid) . '!');
						return;
					}
				} else {
					$GLOBALS['ERROR']['write']['object'][$name] = true;
					t_e('Table ' . intval($classid) . ' does not exist!');
					return;
				}
				break;
		}

		if(!$ok){
			$GLOBALS['ERROR']['write']['object'][$name] = true;
			return;
		}
		$isOwner = !empty($_SESSION['webuser']['registered']) && isset($_SESSION['webuser']['ID']) && (
			($protected && ($_SESSION['webuser']['ID'] == $GLOBALS['we_' . $type][$name]->WebUserID)) ||
			($userid && ($_SESSION['webuser']['ID'] == $GLOBALS['we_' . $type][$name]->getElement($userid)))
			);

		$isAdmin = !empty($_SESSION['webuser']['registered']) && $admin && !empty($_SESSION['webuser'][$admin]);

		$isNew = (($GLOBALS['we_' . $type][$name]->ID == 0) ? ($admin/* only if this field is used */ ? $isAdmin : true) : false); //FR #8411

		if($isAdmin || $isNew || $isOwner || $forceedit){
			$doWrite = true;
			//$newObject = ($GLOBALS['we_'.$type][$name]->ID) ? false : true;
			if($protected){
				if(!isset($_SESSION['webuser']['ID']) || !isset($_SESSION['webuser']['registered']) || !$_SESSION['webuser']['registered']){
					$GLOBALS['ERROR']['write'][$type][$name] = true;
					return;
				}
				if(!$GLOBALS['we_' . $type][$name]->WebUserID){
					$GLOBALS['we_' . $type][$name]->WebUserID = $_SESSION['webuser']['ID'];
				}
			} elseif($userid){
				if(!isset($_SESSION['webuser']['ID']) || !isset($_SESSION['webuser']['registered']) || !$_SESSION['webuser']['registered']){
					$GLOBALS['ERROR']['write'][$type][$name] = true;
					return;
				}
				if(!$GLOBALS['we_' . $type][$name]->getElement($userid)){
					$GLOBALS['we_' . $type][$name]->setElement($userid, $_SESSION['webuser']['ID']);
				}
			}
			$GLOBALS['ERROR']['write'][$type][$name] = false;
			checkAndCreateBinary($name, ($type === 'document' ? 'we_document' : 'we_object'));

			//FIXME: we should probably use checkFieldsOnSave?!
			$GLOBALS['we_' . $type][$name]->i_checkPathDiffAndCreate();
			if(!$objname){
				$GLOBALS['we_' . $type][$name]->i_correctDoublePath();
			}
			if(isset($GLOBALS['we_doc'])){
				$_WE_DOC_SAVE = $GLOBALS['we_doc'];
			}
			$GLOBALS['we_doc'] = &$GLOBALS['we_' . $type][$name];
			$GLOBALS['we_doc']->IsSearchable = $searchable;
			switch($language){
				case 'self':
				case 'top':
					$docLanguage = we_getDocForTag($language);
					$language = $docLanguage->Language;
					unset($docLanguage);
			}
			$GLOBALS['we_doc']->Language = $language;
			if($workspaces && $type === 'object'){
				$tmplArray = array();
				foreach($workspaces as $wsId){
					$tmplArray[] = $GLOBALS['we_' . $type][$name]->getTemplateFromWs($wsId);
				}
				$GLOBALS['we_' . $type][$name]->Workspaces = implode(',', $workspaces);
				$GLOBALS['we_' . $type][$name]->Templates = implode(',', $tmplArray);
			}

			$GLOBALS['we_' . $type][$name]->Path = $GLOBALS['we_' . $type][$name]->getPath();

			if(defined('OBJECT_FILES_TABLE') && $type === 'object'){
				if($GLOBALS['we_' . $type][$name]->Text === ''){
					if($objname === ''){
						$objname = 1 + intval(f('SELECT MAX(ID) AS ID FROM ' . OBJECT_FILES_TABLE));
					}
				} else {
					switch($onpredefinedname){
						case 'appendto':
							$objname = ($objname ? $GLOBALS['we_' . $type][$name]->Text . '_' . $objname : $GLOBALS['we_' . $type][$name]->Text);
							break;
						case 'infrontof':
							$objname .= ($objname ? '_' . $GLOBALS['we_' . $type][$name]->Text : $GLOBALS['we_' . $type][$name]->Text);
							break;
						case 'overwrite':
							if($objname === ''){
								$objname = $GLOBALS['we_' . $type][$name]->Text;
							}
							break;
					}
				}
				$objexists = f('SELECT ID FROM ' . OBJECT_FILES_TABLE . ' WHERE Path="' . $GLOBALS['DB_WE']->escape(str_replace('//', '/', $GLOBALS['we_' . $type][$name]->Path . '/' . $objname)) . '"');
				if(!$objexists){
					$GLOBALS['we_' . $type][$name]->Text = $objname;
					$GLOBALS['we_' . $type][$name]->Path = str_replace('//', '/', $GLOBALS['we_' . $type][$name]->Path . '/' . $objname);
				} else {
					switch($onduplicate){
						case 'abort':
							$GLOBALS['ERROR']['write'][$type][$name] = true;
							$doWrite = false;
							break;
						case 'overwrite':
							$GLOBALS['we_' . $type][$name]->ID = $objexists;
							$GLOBALS['we_' . $type][$name]->Path = str_replace('//', '/', $GLOBALS['we_' . $type][$name]->Path . '/' . $objname);
							$GLOBALS['we_' . $type][$name]->Text = $objname;
							break;
						case 'increment':
							$z = 1;
							$footext = $objname . '_' . $z;
							while(f('SELECT ID FROM ' . OBJECT_FILES_TABLE . ' WHERE Path="' . $GLOBALS['DB_WE']->escape(str_replace('//', '/', $GLOBALS['we_' . $type][$name]->Path . '/' . $footext)) . '"')){
								$z++;
								$footext = $objname . '_' . $z;
							}
							$GLOBALS['we_' . $type][$name]->Path = str_replace('//', '/', $GLOBALS["we_$type"][$name]->Path . '/' . $footext);
							$GLOBALS['we_' . $type][$name]->Text = $footext;
							break;
					}
				}
			}
			if($doWrite){
				$ret = $GLOBALS['we_' . $type][$name]->we_save();
				if($publish && !$doworkflow){
					$ret1 = ($type === 'document' && (!$GLOBALS['we_' . $type][$name]->IsDynamic) && isset($GLOBALS['we_doc']) ? // on static HTML Documents we have to do it different
							$GLOBALS['we_doc']->we_publish() :
							$GLOBALS['we_' . $type][$name]->we_publish());
				}

				if($doworkflow){
					$wf_text = $workflowname . '  ';
					switch($type){
						default:
						case 'document':
							$wf_text .= 'Document ID: ' . $GLOBALS['we_doc']->ID;
							$tab = FILE_TABLE;
							break;
						case 'object':
							$wf_text .= 'Object ID: ' . $GLOBALS['we_doc']->ID;
							$tab = OBJECT_FILES_TABLE;
							break;
					}
					$workflowID = we_workflow_utility::getWorkflowID($workflowname, $tab);

					if(!we_workflow_utility::insertDocInWorkflow($GLOBALS['we_doc']->ID, $GLOBALS['we_doc']->Table, $workflowID, $workflowuserid, $wf_text)){
						t_e('error inserting document to workflow. Additional data:', $GLOBALS['we_doc']->Table, $workflowID, $workflowuserid);
					}
				}
				$GLOBALS['we_object_write_ID'] = $GLOBALS['we_doc']->ID;

				/**
				 * Fix #9818
				 * now we have to set the new document/object ID as request value to avoid
				 * createing more documents/objects by reload the webform (<we:form type="object | document">) by an user
				 */
				$requestVarName = 'we_edit' . ucfirst($type) . '_ID';
				$_REQUEST[$requestVarName] = $GLOBALS['we_doc']->ID;
			}

			unset($GLOBALS['we_doc']);
			if(isset($_WE_DOC_SAVE)){
				$GLOBALS['we_doc'] = $_WE_DOC_SAVE;
				unset($_WE_DOC_SAVE);
			}
			$_REQUEST['we_returnpage'] = $GLOBALS['we_' . $type][$name]->getElement('we_returnpage');

			if($doWrite && $mail){
				if(!$mailfrom){
					$mailfrom = 'dontReply@' . $_SERVER['SERVER_NAME'];
				}
				$path = $GLOBALS['we_' . $type][$name]->Path;
				switch($type){
					case 'object':
						$classname = f('SELECT Text FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($classid));
						$mailtext = sprintf(g_l('global', '[std_mailtext_newObj]'), $path, $classname) . "\n" .
							($triggerid ? id_to_path($triggerid) . '?we_objectID=' : 'ObjectID: ') .
							$GLOBALS['we_object'][$name]->ID;
						$subject = g_l('global', '[std_subject_newObj]');
						break;
					default:
					case 'document':
						$mailtext = sprintf(g_l('global', '[std_mailtext_newDoc]'), $path) . "\n" . $GLOBALS['we_' . $type][$name]->getHttpPath();
						$subject = g_l('global', '[std_subject_newDoc]');
						break;
				}
				$phpmail = new we_helpers_mail($mail, $subject, $mailfrom);
				$phpmail->setCharSet($charset);
				$phpmail->addTextPart($mailtext);
				$phpmail->buildMessage();
				$phpmail->Send();
			}
		} else {
			$GLOBALS['ERROR']['write'][$type][$name] = 1;
		}
	}
	if(!empty($GLOBALS['WE_SESSION_START'])){
		unset($_SESSION['weS']['we_' . $type . '_session_' . $name]); //fix #8051
	}
}

function checkAndCreateBinary($formname, $type = 'we_document'){
	$webuserId = !empty($_SESSION['webuser']['registered']) && !empty($_SESSION['webuser']['ID']) ? $_SESSION['webuser']['ID'] : 0;
	$regs = array();

	$checks = array(
		'BINARY' => array(
			'we_otherDocument',
			'application/',
			we_base_ContentTypes::APPLICATION,
			'application',
			array()
		),
		'FLASHMOVIE' => array(
			'we_flashDocument',
			we_base_ContentTypes::FLASH,
			we_base_ContentTypes::FLASH,
			'image',
			array(
				'width' => 'imgwidth',
				'height' => 'imgheight',
				'origwidth' => 'imgwidth',
				'origheight' => 'imgheight'
			)
		),
		'QUICKTIME' => array(
			'we_quicktimeDocument',
			we_base_ContentTypes::QUICKTIME,
			we_base_ContentTypes::QUICKTIME,
			'image',
			array()
		),
		'IMG' => array(
			'we_imageDocument',
			'image/',
			we_base_ContentTypes::IMAGE,
			'image',
			array(
				'width' => 'imgwidth',
				'height' => 'imgheight',
				'origwidth' => 'imgwidth',
				'origheight' => 'imgheight'
			)
		),
	);
	foreach($_REQUEST as $key => $dataID){
		$doc = '';
		foreach($checks as $check => $checkData){
			if(preg_match('|^WE_UI_' . $check . '_DATA_ID_(.*)$|', $key, $regs)){
				list($doc, $matchType, $contentType, $dataType, $moreAttribs) = $checkData;
				break;
			}
		}
		if(!$doc){
			continue;
		}
		$name = $regs[1];
		$id = isset($_SESSION[$dataID]['id']) ? $_SESSION[$dataID]['id'] : 0;
		if(isset($_SESSION[$dataID]['doDelete']) && $_SESSION[$dataID]['doDelete'] == 1){
			if($id){
				$document = new $doc();
				$document->initByID($id);
				if($document->WebUserID == $webuserId){
					//everything ok, now delete
					we_base_delete::deleteEntry($id, FILE_TABLE);
					$GLOBALS[$type][$formname]->setElement($name, 0);
				}
			}
			unset($_SESSION[$dataID]);
			continue;
		}
		if(isset($_SESSION[$dataID]['serverPath'])){
			if(strpos($_SESSION[$dataID]['type'], $matchType) === 0){
				$document = new $doc();

				if($id){
					// document has already an image
					// so change binary data
					$document->initByID($id);
				} else {
					$document->setParentID($_SESSION[$dataID]['parentid']);
				}

				$document->Table = FILE_TABLE;
				$document->Published = time();
				$document->WebUserID = $webuserId;
				$document->Filename = $_SESSION[$dataID]['fileName'];
				$document->Extension = $_SESSION[$dataID]['extension'];
				$document->Text = $_SESSION[$dataID]['text'];
				$document->Path = rtrim($document->getParentPath(), '/') . '/' . $document->Text;
				$document->setElement('type', $contentType, 'attrib');
				$document->setElement('data', $_SESSION[$dataID]['serverPath'], $dataType);
				$document->setElement('filesize', $_SESSION[$dataID]['size'], 'attrib');
				foreach($moreAttribs as $eName => $sKey){
					$document->setElement($eName, $_SESSION[$dataID][$sKey], 'attrib');
				}

				$document->we_save();

				$newId = $document->ID;

				$t = explode('_', $document->Filename);
				$t[1] = $newId;
				$fn = implode('_', $t);
				$document->Filename = $fn;
				$document->Path = rtrim($document->getParentPath(), '/') . '/' . $document->Filename . $document->Extension;
				$document->we_save();

				$GLOBALS[$type][$formname]->setElement($name, $newId);
			}
		}
		if(isset($_SESSION[$dataID])){
			unset($_SESSION[$dataID]);
		}
	}
}
