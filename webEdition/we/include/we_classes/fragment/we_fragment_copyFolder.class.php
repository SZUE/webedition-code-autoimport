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
class we_fragment_copyFolder extends we_fragment_base{
	var $copyToPath = "";
	private $text = '';

	protected function init(){
		$fromID = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1);
		$toID = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 2);
		$table = we_base_request::_(we_base_request::TABLE, 'we_cmd', '', 4);
		$OverwriteObjects = we_base_request::_(we_base_request::STRING, 'OverwriteObjects', 'nothing');
		$ObjectCopyNoFolders = we_base_request::_(we_base_request::BOOL, 'DoNotCopyFolders');
		$CreateTemplate = we_base_request::_(we_base_request::BOOL, 'CreateTemplate');
		$CreateDoctypes = we_base_request::_(we_base_request::BOOL, 'CreateDoctypes');

		$CreateTemplateInFolderID = we_base_request::_(we_base_request::INT, 'CreateTemplateInFolderID', 0);
		$OverwriteCategories = we_base_request::_(we_base_request::BOOL, 'OverwriteCategories', false);
		$vals = [];
		foreach($_REQUEST as $name => $val){
			if(!is_array($val)){
				if(preg_match('%^me(.*)variant0_me(.*)_item%i', $name)){
					$vals[] = $val;
				}
			}
		}
		$newCategories = path_to_id($vals, CATEGORY_TABLE, $GLOBALS['DB_WE']);

		if(isset($_SESSION['weS']['WE_CREATE_DOCTYPE'])){
			unset($_SESSION['weS']['WE_CREATE_DOCTYPE']);
		}
		if(isset($_SESSION['weS']['WE_CREATE_TEMPLATE'])){
			unset($_SESSION['weS']['WE_CREATE_TEMPLATE']);
		}
		$checkTable = (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 1);
		if($fromID && $toID && $table != $checkTable){
			//  "fromID"  cannot be a parent  of "toID"


			$fromPath = id_to_path($fromID);
			$db = new DB_WE();
			$this->alldata = [];
			$merge = ["CopyToId" => $toID,
				"CopyFromId" => $fromID,
				"CopyFromPath" => $fromPath,
				"IsWeFile" => 0,
				"CreateTemplate" => $CreateTemplate ? 1 : 0,
				"CreateDoctypes" => $CreateDoctypes ? 1 : 0,
				"CreateTemplateInFolderID" => $CreateTemplateInFolderID,
				"OverwriteCategories" => $OverwriteCategories,
				"newCategories" => $newCategories,
			];

			// make it twice to be sure that all linked IDs are correct
			$db->query('SELECT ID,ParentID,Text,Path,IsFolder,ClassName,ContentType,Category FROM ' . FILE_TABLE . " WHERE (Path LIKE'" . $db->escape($fromPath) . "/%') AND ContentType != '" . we_base_ContentTypes::WEDOCUMENT . "' ORDER BY IsFolder DESC,Path");
			while($db->next_record()){
				$this->alldata[] = array_merge($db->getRecord(), $merge);
			}

			$merge['IsWeFile'] = 1;
			for($num = 0; $num < 2; $num++){
				$merge['num'] = $num;
				$db->query('SELECT ID,ParentID,Text,TemplateID,Path,IsFolder,ClassName,ContentType,Category FROM ' . FILE_TABLE . " WHERE (Path LIKE'" . $db->escape($fromPath) . "/%') AND ContentType = '" . we_base_ContentTypes::WEDOCUMENT . "' ORDER BY IsFolder DESC,Path");
				while($db->next_record()){
					$merge["CreateTemplate"] = $CreateTemplate ? (id_to_path($db->f('TemplateID'), TEMPLATES_TABLE) ? 1 : 0) : 0;
					$this->alldata[] = array_merge($db->getRecord(), $merge);
				}
			}
		} else {
			if(defined('OBJECT_FILES_TABLE') && $table == OBJECT_FILES_TABLE){
				$_SESSION['weS']['WE_COPY_OBJECTS'] = true;
				$fromPath = id_to_path($fromID, OBJECT_FILES_TABLE);

				$qfolders = ($ObjectCopyNoFolders ? ' ParentID=' . $fromID . ' AND IsFolder=0 AND ' : '');

				$db = new DB_WE();
				$this->alldata = [];
				$merge = ["CopyToId" => $toID,
					"CopyFromId" => $fromID,
					"CopyFromPath" => $fromPath,
					"IsWeFile" => 1,
					"TheTable" => OBJECT_FILES_TABLE,
					"OverwriteObjects" => $OverwriteObjects,
					"ObjectCopyNoFolders" => $ObjectCopyNoFolders,
					"CreateTemplate" => 0,
					"CreateDoctypes" => 0,
					"CreateTemplateInFolderID" => 0,
					"OverwriteCategories" => 0,
					"newCategories" => '',
				];
				$db->query('SELECT ID,ParentID,Text,Path,IsFolder,ClassName,ContentType,Published FROM ' . OBJECT_FILES_TABLE . ' WHERE ' . $qfolders . " (Path LIKE'" . $db->escape($fromPath) . "/%') ORDER BY IsFolder DESC,Path");
				while($db->next_record(MYSQL_ASSOC)){
					$this->alldata[] = array_merge($db->getRecord(), $merge);
				}
			}
		}
	}

	protected function doTask(){
		if(is_array($this->data)){
			if(!isset($this->data['TheTable'])){
				if(!$this->copyFile()){
					t_e('Error importing File: ' . $this->data['Path']);
					exit('Error importing File: ' . $this->data['Path']);
				}
				$this->text = addslashes(($this->data['IsWeFile'] && $this->data['num'] ?
					sprintf(g_l('copyFolder', '[rewrite]'), basename($this->data['Path'])) :
					sprintf(g_l('copyFolder', $this->data['IsFolder'] ? '[copyFolder]' : '[copyFile]'), basename($this->data['Path'])))
				);

				return;
			}
			if(!$this->copyObjects()){
				t_e('Error importing Object: ' . $this->data['Path']);
				exit('Error importing Object: ' . $this->data['Path']);
			}
			$this->text = addslashes(sprintf(g_l('copyFolder', $this->data['IsFolder'] ? '[copyFolder]' : '[copyFile]'), basename($this->data["Path"])));
		}
	}

	protected function updateProgressBar(){
		return we_html_element::jsElement('parent.setProgress("",' . ((int) ((100 / count($this->alldata)) * (1 + $this->currentTask))) . ');parent.setProgressText("pbar1","' . $this->text . '");');
	}

	private function getObjectPid($path, we_database_base $db){
		$path = dirname($path);
		if($path === '/'){
			return 0;
		}
		return f('SELECT ID FROM ' . OBJECT_FILES_TABLE . ' WHERE Path="' . $db->escape($path) . '"', '', $db);
	}

	private function copyObjects(){
		$GLOBALS['we_doc'] = $this->getObjectFile();
		$this->copyToPath = id_to_path($this->data['CopyToId'], OBJECT_FILES_TABLE);
		$path = preg_replace('|^' . $this->data['CopyFromPath'] . '/|', $this->copyToPath . '/', $this->data['Path']);
		if($this->data['IsFolder']){
			if(!$GLOBALS['we_doc']->initByPath($path, OBJECT_FILES_TABLE) || !$GLOBALS['we_doc']->we_save()){
				return false;
			}
		} else {
			$GLOBALS['we_doc']->copyDoc($this->data['ID']);
			$GLOBALS['we_doc']->Text = $this->data['Text'];
			$GLOBALS['we_doc']->Path = $path;
			$GLOBALS['we_doc']->OldPath = '';
			$pid = $this->getObjectPid($path, $GLOBALS['DB_WE']);
			$GLOBALS['we_doc']->setParentID($pid);
			$ObjectExists = $this->CheckForSameObjectName($GLOBALS['we_doc']->Path);


			if($ObjectExists && $this->data['OverwriteObjects'] == 'nothing'){
				return true;
			}
			if($ObjectExists && $this->data['OverwriteObjects'] === 'rename'){
				$GLOBALS['we_doc']->Text = $GLOBALS['we_doc']->Text . '_copy';
				$GLOBALS['we_doc']->Path = $GLOBALS['we_doc']->Path . '_copy';
				while($this->CheckForSameObjectName($GLOBALS['we_doc']->Path)){
					$GLOBALS['we_doc']->Text = $GLOBALS['we_doc']->Text . '_copy';
					$GLOBALS['we_doc']->Path = $GLOBALS['we_doc']->Path . '_copy';
				}
			}
			if($ObjectExists && $this->data['OverwriteObjects'] === 'overwrite'){
				$GLOBALS['we_doc']->ID = $ObjectExists;
			}
			if(!$GLOBALS['we_doc']->we_save()){
				return false;
			}
			if($this->data['Published']){
				$GLOBALS['we_doc']->we_publish();
			}
		}
		return true;
	}

	private function CheckForSameObjectName($path){
		return f('SELECT ID FROM ' . OBJECT_FILES_TABLE . ' WHERE Path="' . $GLOBALS['DB_WE']->escape($path) . '"');
	}

	private function getObjectFile(){
		switch($this->data['ContentType']){
			case we_base_ContentTypes::FOLDER:
				$we_ContentType = $this->data['ContentType'];
				return new we_class_folder();
			case we_base_ContentTypes::OBJECT_FILE:
				$we_ContentType = $this->data['ContentType'];
				return new we_objectFile();
		}
	}

	private function copyFile(){
		$GLOBALS['we_doc'] = $this->getDocument();
		$this->copyToPath = id_to_path($this->data['CopyToId']);
		$path = preg_replace('|^' . $this->data['CopyFromPath'] . '/|', $this->copyToPath . '/', $this->data['Path']);
		$GLOBALS['we_doc'] = new $this->data['ClassName']();
		if($this->data['IsFolder']){
			$GLOBALS['we_doc']->initByPath($path);
			if(!$GLOBALS['we_doc']->we_save()){
				return false;
			}
		} else {
			$GLOBALS['we_doc']->initByID($this->data['ID']);
			// if file  exists the file will overwritten, if not a new one (with no id) will be created
			$GLOBALS['we_doc']->ID = f('SELECT ID FROM ' . FILE_TABLE . ' WHERE Path="' . $GLOBALS['DB_WE']->escape($path) . '"');
			$GLOBALS['we_doc']->Path = $path;
			$GLOBALS['we_doc']->OldPath = '';
			$pid = $this->getPid($path, $GLOBALS['DB_WE']);
			$GLOBALS['we_doc']->setParentID($pid);
			switch($GLOBALS['we_doc']->ContentType){
				case we_base_ContentTypes::WEDOCUMENT:
					$oldTemplateID = $GLOBALS['we_doc']->TemplateID;
					$this->parseWeDocument($GLOBALS['we_doc']);

					// check if we need to create a template
					if($this->data['CreateTemplate']){
						$CreateMasterTemplate = we_base_request::_(we_base_request::BOOL, 'CreateMasterTemplate');
						$CreateIncludedTemplate = we_base_request::_(we_base_request::BOOL, 'CreateIncludedTemplate');
						// check if a template was created from prior doc
						if(!(isset($_SESSION['weS']['WE_CREATE_TEMPLATE']) && isset($_SESSION['weS']['WE_CREATE_TEMPLATE'][$GLOBALS['we_doc']->TemplateID]))){
							$createdTemplate = $this->copyTemplate($GLOBALS['we_doc']->TemplateID, $this->data['CreateTemplateInFolderID'], $CreateMasterTemplate, $CreateIncludedTemplate);
						}

						$GLOBALS['we_doc']->setTemplateID($_SESSION['weS']['WE_CREATE_TEMPLATE'][$GLOBALS['we_doc']->TemplateID]);
					}

					if($this->data['OverwriteCategories']){
						$GLOBALS['we_doc']->Category = $this->data['newCategories'];
					} else {
						// remove duplicates
						$old = explode(',', $GLOBALS['we_doc']->Category);
						$tmp = explode(',', $this->data['newCategories']);
						$new = array_unique(array_merge($old, $tmp));
						$GLOBALS['we_doc']->Category = implode(',', $new);
					}

					if($GLOBALS['we_doc']->DocType && $this->data['CreateDoctypes']){
						// check if a doctype was created from prior doc
						if(!(isset($_SESSION['weS']['WE_CREATE_DOCTYPE']) &&
							isset($_SESSION['weS']['WE_CREATE_DOCTYPE'][$GLOBALS['we_doc']->DocType]))){

							$dt = new we_docTypes();

							$dt->initByID($GLOBALS['we_doc']->DocType, DOC_TYPES_TABLE);
							$dt->ID = 0;
							$dt->DocType = $dt->DocType . '_copy';
							// if file exists we need  to create a new one!
							if(($file_id = f('SELECT ID FROM ' . DOC_TYPES_TABLE . ' WHERE DocType="' . $GLOBALS['DB_WE']->escape($dt->DocType) . '"'))){
								$z = 0;
								$footext = $dt->DocType . '_' . $z;
								while(f('SELECT ID FROM ' . DOC_TYPES_TABLE . ' WHERE DocType="' . $GLOBALS['DB_WE']->escape($footext) . '"')){
									$z++;
									$footext = $dt->DocType . '_' . $z;
								}
								$dt->DocType = $footext;
							}
							$path = id_to_path($dt->ParentID);
							if($this->mustChange($path)){
								$dt->ParentPath = $this->getNewPath($path);
								$dt->ParentID = path_to_id($dt->ParentPath, FILE_TABLE, $GLOBALS['DB_WE']);
							}

							if($dt->Templates){
								$templArray = makeArrayFromCSV($dt->Templates);
								$newTemplateIDs = [];
								foreach($templArray as $id){
									if($id == $oldTemplateID){
										$newTemplateIDs[] = $GLOBALS['we_doc']->TemplateID;
									} else {
										$newTemplateIDs[] = $id;
									}
								}
								$dt->Templates = implode(',', $newTemplateIDs);
								$dt->TemplateID = $GLOBALS['we_doc']->TemplateID;
							}

							$dt->we_save();
							$newID = $dt->ID;

							if(!isset($_SESSION['weS']['WE_CREATE_DOCTYPE'])){
								$_SESSION['weS']['WE_CREATE_DOCTYPE'] = [];
							}
							$_SESSION['weS']['WE_CREATE_DOCTYPE'][$GLOBALS['we_doc']->DocType] = $newID;
						}

						$GLOBALS['we_doc']->DocType = $_SESSION['weS']['WE_CREATE_DOCTYPE'][$GLOBALS['we_doc']->DocType];
					}

					// bugfix 0001582
					$GLOBALS['we_doc']->OldPath = $GLOBALS['we_doc']->Path;
					break;
				case we_base_ContentTypes::HTML:
				case we_base_ContentTypes::TEXT:
				case we_base_ContentTypes::CSS:
				case we_base_ContentTypes::HTACCESS:
				case we_base_ContentTypes::JS:
					$this->parseTextDocument($GLOBALS['we_doc']);
					break;
			}

			if(!$GLOBALS['we_doc']->we_save()){
				return false;
			}

			if($GLOBALS['we_doc']->Published){
				if(!$GLOBALS['we_doc']->we_publish()){
					return false;
				}
			}
		}
		return true;
	}

	private function copyTemplate($templateID, $parentID, $CreateMasterTemplate = false, $CreateIncludedTemplate = false, $counter = 0){
		$counter++;
		$templVars = [];
		if(!isset($_SESSION['weS']['WE_CREATE_TEMPLATE'])){
			$_SESSION['weS']['WE_CREATE_TEMPLATE'] = [];
		}
		if(!isset($_SESSION['weS']['WE_CREATE_TEMPLATE'][$templateID])){

			$templ = new we_template();
			$templ->initByID($templateID, TEMPLATES_TABLE);
			$templ->ID = 0;
			$templ->OldPath = '';
			$templ->setParentID($parentID);
			$templ->Path = $templ->getParentPath() . (($templ->getParentPath() != '/') ? '/' : '') . $templ->Text;
			// if file exists we need  to create a new one!
			if(($file_id = f('SELECT ID FROM ' . TEMPLATES_TABLE . ' WHERE Path="' . $GLOBALS['DB_WE']->escape($templ->Path) . '"'))){
				$z = 0;
				$footext = $templ->Filename . '_' . $z . $templ->Extension;
				while(f('SELECT ID FROM ' . TEMPLATES_TABLE . ' WHERE Text="' . $GLOBALS['DB_WE']->escape($footext) . '" AND ParentID=' . intval($templ->ParentID))){
					$z++;
					$footext = $templ->Filename . '_' . $z . $templ->Extension;
				}
				$templ->Text = $footext;
				$templ->Filename = $templ->Filename . '_' . $z;
				$templ->Path = $templ->getParentPath() . (($templ->getParentPath() != '/') ? '/' : '') . $templ->Text;
			}
			$this->ParseTemplate($templ);
			$templ->we_save();
			$newID = $templ->ID;
			$templVars['newID'] = $newID;

			$_SESSION['weS']['WE_CREATE_TEMPLATE'][$templateID] = $newID;
			if($counter < 10){
				if($CreateMasterTemplate && $templ->MasterTemplateID > 0){
					if(isset($_SESSION['weS']['WE_CREATE_TEMPLATE'][$templ->MasterTemplateID])){
						$templ->MasterTemplateID = $_SESSION['weS']['WE_CREATE_TEMPLATE'][$templ->MasterTemplateID];
					} else {
						$createdMasterVars = $this->copyTemplate($templ->MasterTemplateID, $parentID, $CreateMasterTemplate, $CreateIncludedTemplate, $counter);
						$templ->MasterTemplateID = $createdMasterVars['newID'];
					}
					$templ->we_save();
				}
				if($CreateIncludedTemplate && !empty($templ->IncludedTemplates)){
					$includedTemplates = explode(',', $templ->IncludedTemplates);
					$code = $templ->getElement('data');
					foreach($includedTemplates as $incTempl){
						if(!empty($incTempl) && $incTempl > 0){
							if(isset($_SESSION['weS']['WE_CREATE_TEMPLATE'][trim($incTempl)])){
								$templID = str_replace($incTempl, $_SESSION['weS']['WE_CREATE_TEMPLATE'][trim($incTempl)], $templ->IncludedTemplates);
								$newTemplId = $_SESSION['weS']['WE_CREATE_TEMPLATE'][trim($incTempl)];
							} else {
								$createdIncVars = $this->copyTemplate(trim($incTempl), $parentID, $CreateMasterTemplate, $CreateIncludedTemplate, $counter);
								$templID = str_replace($incTempl, $createdIncVars['newID'], $templ->IncludedTemplates);
								$newTemplId = $createdIncVars['newID'];
							}
							$tp = new we_tag_tagParser($code);
							$tags = $tp->getAllTags();
							foreach($tags as $tag){
								$regs = [];
								$xid = 0;
								if(preg_match('|^<we:include ([^>]+)>$|i', $tag, $regs)){
									if(preg_match('|type *= *" *template *"|i', $regs[1])){
										$foo = [];

										$attributes = $regs[1];
										preg_match_all('/([^=]+)= *("[^"]*")/', $attributes, $foo, PREG_PATTERN_ORDER);
										foreach($foo[1] as $k => $v){
											if(trim($v) === 'id'){
												$xid = abs(str_replace('"', '', $foo[2][$k]));
												break;
											}
										}
										if($xid == $incTempl){
											$newtag = preg_replace('|id *= *" *' . $xid . ' *"|i', 'id="' . $newTemplId . '"', $tag);
											$code = str_replace($tag, $newtag, $code);
										}
									}
								}
							}
						}
					}
					$templ->elements['data']['dat'] = $code;
					$templ->we_save();
				}
			}
		}
		return $templVars;
	}

	private function ParseTemplate(we_template &$we_doc){
		// parse hard  coded  links in template  :TODO: check for ="/Path ='Path and =Path
		$we_doc->elements['data']['dat'] = str_replace($this->data['CopyFromPath'] . '/', $this->copyToPath . '/', $we_doc->getElement('data'));

		$ChangeTags = ['a' => ['id'],
			'url' => ['id'],
			'img' => ['id'],
			'listview' => ['triggerid', 'workspaceID'],
			'ifSelf' => ['id'],
			'ifNotSelf' => ['id'],
			'form' => ['id', 'onsuccess', 'onerror', 'onmailerror'],
			'include' => ['id'],
			'addDelNewsletterEmail' => ['mailid', 'id'],
			'css' => ['id'],
			'icon' => ['id'],
			'js' => ['id'],
			'linkToSEEM' => ['id'],
			'linkToSeeMode' => ['id'],
			'listdir' => ['id'],
			'printVersion' => ['triggerid'],
			'sendMail' => ['id'],
			'write' => ['triggerid'],
			'flashmovie' => ['id'],
			'delete' => ['pid'],
		];

		$changed = false;
		$regs = [];
		$tp = new we_tag_tagParser($we_doc->getElement('data'));
		$tags = $tp->getAllTags();
		foreach($tags as $tag){
			$destTag = $tag;
			if(preg_match('|<we:([^> /]+)|i', $tag, $regs)){ // starttag found
				$tagname = $regs[1];
				if(isset($ChangeTags[$tagname])){
					foreach($ChangeTags[$tagname] as $attribname){
						if(preg_match('|' . $attribname . '="([0-9]+)"|', $tag, $regs)){
							$id = $regs[1];
							$path = id_to_path($id, FILE_TABLE, $GLOBALS['DB_WE']);
							if($this->mustChange($path)){
								$changed = true;
								$pathTo = $this->getNewPath($path);
								$idTo = path_to_id($pathTo, FILE_TABLE, $GLOBALS['DB_WE']) ?: '##WEPATH##' . $pathTo . ' ###WEPATH###';
								$destTag = preg_replace('/' .
									$attribname . '="[0-9]+"/', $attribname . '="' . $idTo . '"', $destTag);
							}
						}
					}
				}
			}
			if($changed){
				$changed = false;
				$we_doc->elements['data']['dat'] = str_replace($tag, $destTag, $we_doc->getElement('data'));
			}
		}
	}

	private function parseWeDocument(we_document &$we_doc){
		$DB_WE = new DB_WE();

		$hrefs = [];
		foreach($we_doc->elements as $k => $v){

			switch(isset($v['type']) ? $v['type'] : ''){
				case 'txt' :
				case 'link'://since old fields are txt we have to handle it here
					$regs = [];
					$elem = $we_doc->getElement($k);
					if(preg_match('|(.+)' . we_base_link::MAGIC_INFIX . '(.+)|', $k, $regs)){ // is a we:href field
						$v['type'] = 'href';
					} elseif(substr($elem, 0, 2) === 'a:' && is_array($link = we_unserialize($elem))){ // is a we:link field
						if(isset($link['type']) && ($link['type'] == we_base_link::TYPE_INT)){
							$intID = $link['id'];
							$path = id_to_path($intID, FILE_TABLE, $DB_WE);
							if($this->mustChange($path)){
								$pathTo = $this->getNewPath($path);
								$link['id'] = path_to_id($pathTo, FILE_TABLE, $DB_WE);
								$we_doc->setElement($k, we_serialize($link), 'link');
							}
						}
					} else { // its a normal  text field
						$this->parseInternalLinks($we_doc->getElement($k), $DB_WE);
						// :TODO: check for ="/Path ='Path and =Path
						$we_doc->elements[$k]['dat'] = str_replace($this->data['CopyFromPath'] . '/', $this->copyToPath . '/', $we_doc->getElement($k));
					}
					if($v['type'] != 'href'){
						break;
					}//fall through to correct field
				case 'href':
					$regs = [];
					if(preg_match('|(.+)' . we_base_link::MAGIC_INFIX . '(.+)|', $k, $regs) && // is a we:href field
						!in_array($regs[1], $hrefs)){//already scanned?
						$hrefs[] = $regs[1];
						$int = intval($we_doc->getElement($regs[1] . we_base_link::MAGIC_INT_LINK));
						if($int){
							if(($intID = $we_doc->getElement($regs[1] . we_base_link::MAGIC_INT_LINK_ID, 'bdid'))){
								$path = id_to_path($intID, FILE_TABLE, $DB_WE);
								if($this->mustChange($path)){
									$pathTo = $this->getNewPath($path);
									$idTo = path_to_id($pathTo, FILE_TABLE, $DB_WE);
									$we_doc->setElement($regs[1] . we_base_link::MAGIC_INT_LINK_ID, ( $idTo ?: '##WEPATH##' . $pathTo . ' ###WEPATH###'), 'href', 'bdid');
								}
							}
						} elseif(($path = $we_doc->getElement($regs[1]))){
							if($this->mustChange($path)){
								$we_doc->setElement($regs[1], $this->getNewPath($path));
							}
						}
					}
					break;

				case 'img' :
					$path = id_to_path($we_doc->getElement($k, 'bdid', 0), FILE_TABLE, $DB_WE);
					if($this->mustChange($path)){
						$pathTo = $this->getNewPath($path);
						$idTo = path_to_id($pathTo, FILE_TABLE, $DB_WE);
						$we_doc->setElement($k, ($idTo ?: '##WEPATH##' . $pathTo . ' ###WEPATH###'), 'img', 'bdid');
					}
					break;
				case 'linklist' :
					$ll = new we_base_linklist(we_unserialize($we_doc->getElement($k)));
					$changed = false;
					$cnt = $ll->length();
					for($i = 0; $i < $cnt; $i++){
						$id = $ll->getID($i);
						$path = id_to_path($id, FILE_TABLE, $DB_WE);
						if($this->mustChange($path)){
							$pathTo = $this->getNewPath($path);
							$idTo = path_to_id($pathTo, FILE_TABLE, $DB_WE);
							$ll->setID($i, $idTo);
							$changed = true;
						}
					}

					if($changed){
						$we_doc->setElement($k, $ll->getString(), 'linklist');
					}

					break;
			}
		}
	}

	private function getNewPath($oldPath){
		if($oldPath == $this->data['CopyFromPath']){
			return $this->copyToPath;
		}
		// :TODO: check for ='/Path ='Path and =Path
		return preg_replace('|^' . $this->data['CopyFromPath'] . '/|', $this->copyToPath . '/', $oldPath);
	}

	private function mustChange($path){
		return substr($path, 0, strlen($this->data['CopyFromPath'])) == $this->data['CopyFromPath'];
	}

	private function parseTextDocument(we_document &$we_doc){
		//:TODO: check for ='/Path ='Path and =Path
		$doc = str_replace($this->data['CopyFromPath'] . '/', $this->copyToPath . '/', $we_doc->i_getDocument());
		$we_doc->i_setDocument($doc);
	}

	//FIXME: check why this is different to we_document::parseInternalLinks
	private function parseInternalLinks(&$text, we_database_base $DB_WE){
		$regs = [];
		if(preg_match_all('/(href|src)="' . we_base_link::TYPE_INT_PREFIX . '([^" ]+)/i', $text, $regs, PREG_SET_ORDER)){
			foreach($regs as $reg){
				$id = intval($reg[2]);

				$path = id_to_path($id, FILE_TABLE, $DB_WE);
				if($this->mustChange($path)){
					$pathTo = $this->getNewPath($path);
					$idTo = path_to_id($pathTo, FILE_TABLE, $DB_WE) ?: '##WEPATH##' . $pathTo . ' ###WEPATH###';
					$text = preg_replace('#(href|src)="' . we_base_link::TYPE_INT_PREFIX . $id . '#i', $reg[1] . '="' . we_base_link::TYPE_INT_PREFIX . $idTo, $text);
				}
			}
		}
	}

	private function getPid($path, we_database_base $db){
		$path = dirname($path);
		if($path === "/"){
			return 0;
		}
		return f('SELECT ID FROM ' . FILE_TABLE . ' WHERE Path="' . $db->escape($path) . '"', '', $db);
	}

	private function getDocument(){
		$we_ContentType = $this->data['ContentType'];
		return we_document::initDoc([], $we_ContentType);
	}

	protected function finish(){
		//$cancelButton = we_html_button::create_button(we_html_button::CANCEL, 'javascript:top.close()');

		if(isset($_SESSION['weS']['WE_CREATE_DOCTYPE'])){
			unset($_SESSION['weS']['WE_CREATE_DOCTYPE']);
		}
		$cmd = new we_base_jsCmd();

		if(isset($_SESSION['weS']['WE_CREATE_TEMPLATE'])){
			$pbText = g_l('copyFolder', '[prepareTemplates]');

			echo we_html_element::jsElement('parent.document.getElementById("pbTd").style.display="block";parent.setProgress("",0);parent.setProgressText("pbar1","' . addslashes($pbText) . '");');
			flush();
			$cmd->addCmd('location', ['doc' => 'document', 'loc' => WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=copyFolder&finish=1']);
			#unset($_SESSION['weS']['WE_CREATE_TEMPLATE']);
		} elseif(!isset($_SESSION['weS']['WE_COPY_OBJECTS'])){
			$cmd->addCmd('we_cmd', ['load', FILE_TABLE]);
			$cmd->addMsg(g_l('copyFolder', '[copy_success]'), we_message_reporting::WE_MESSAGE_NOTICE);
			$cmd->addCmd('close');
		} else {
			unset($_SESSION['weS']['WE_COPY_OBJECTS']);
			$cmd->addCmd('we_cmd', ['load', OBJECT_FILES_TABLE]);
			$cmd->addMsg(g_l('copyFolder', '[copy_success]'), we_message_reporting::WE_MESSAGE_NOTICE);
			$cmd->addCmd('close');
		}
		echo $cmd->getCmds();
	}

	static function formCreateTemplateDirChooser(){
		$path = '/';
		$myid = 0;

		$weSuggest = & weSuggest::getInstance();
		$weSuggest->setAcId('Template');
		$weSuggest->setContentType(we_base_ContentTypes::FOLDER);
		$weSuggest->setInput('foo', $path, [], true);
		$weSuggest->setLabel(g_l('copyFolder', '[destdir]'));
		$weSuggest->setMaxResults(10);
		$weSuggest->setRequired(true);
		$weSuggest->setResult('CreateTemplateInFolderID', $myid);
		$weSuggest->setSelector(weSuggest::DirSelector);
		$weSuggest->setTable(TEMPLATES_TABLE);
		$weSuggest->setWidth(370);
		$weSuggest->setSelectButton(we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_directory',document.we_form.elements.CreateTemplateInFolderID.value,'" . TEMPLATES_TABLE . "','CreateTemplateInFolderID','foo','setCreateTemplate')", '', 0, 0, "", "", true, false));

		return $weSuggest->getHTML();
	}

	static function formCreateCategoryChooser(){
		// IMI: replace inline js
		$addbut = we_html_button::create_button(we_html_button::ADD, "javascript:we_cmd('we_selector_category',-1,'" . CATEGORY_TABLE . "','','','opener.addCat(top.fileSelect.data.allPaths);')");
		$del_but = addslashes(we_html_button::create_button(we_html_button::TRASH, 'javascript:#####placeHolder#####;'));

		$js = we_html_element::jsElement('
var categories_edit = new (WE().util.multi_edit)("categories",window,0,"' . $del_but . '",478,false);
categories_edit.addVariant();
categories_edit.showVariant(0);
');

		$table = new we_html_table([
			'id' => 'CategoriesBlock',
			'style' => 'display: block;',
			'class' => 'default',
			], 5, 2);

		$table->setCol(1, 0, ['class' => 'defaultfont', 'width' => 100, 'style' => 'padding-top:5px;'], g_l('copyFolder', '[categories]'));
		$table->setCol(1, 1, ['class' => 'defaultfont'], we_html_forms::checkbox(1, 0, 'OverwriteCategories', g_l('copyFolder', '[overwrite_categories]'), false, "defaultfont", "toggleButton();"));
		$table->setCol(2, 0, ['colspan' => 2], we_html_element::htmlDiv(['id' => 'categories',
				'class' => 'blockWrapper',
				'style' => 'width: 488px; height: 60px; border: #AAAAAA solid 1px;'
		]));

		$table->setCol(4, 0, ['colspan' => 2, 'style' => 'text-align:right;padding-top:5px;'], we_html_button::create_button(we_html_button::DELETE_ALL, "javascript:removeAllCats()") . $addbut);

		return $table->getHtml() . $js;
	}

}
