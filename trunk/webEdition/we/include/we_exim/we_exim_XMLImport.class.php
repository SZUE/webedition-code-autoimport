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
class we_exim_XMLImport extends we_exim_XMLExIm{

	var $nodehierarchy = array();

	function __construct(){
		parent::__construct();
	}

	function import($chunk_file){
		$db = new DB_WE();
		update_time_limit(0);

		$objects = array();

		$data = we_base_file::load($chunk_file);
		$this->xmlBrowser = new we_backup_XMLParser();
		$this->xmlBrowser->parse($data, $this->options['xml_encoding']);
		unset($data);
		$this->xmlBrowser->normalize();
		$node_set = array();
		if($this->xmlBrowser->getChildren(0, $node_set)){

			foreach($node_set as $node){
				$this->xmlBrowser->seek($node);

				if($this->handleTag($this->xmlBrowser->getNodeName($node))){
					$tmp = $this->importNodeSet($node);
					if(is_object($tmp)){
						$objects[] = $tmp;
					}
				}
			}
		}

		$save = false;
		foreach($objects as $object){
			$save = true;
			$extra = array(
				'OldID' => isset($object->ID) ? $object->ID : 0,
				'OldParentID' => isset($object->ParentID) ? $object->ParentID : 0,
				'OldPath' => isset($object->Path) ? $object->Path : '',
				'OldTemplatePath' => isset($object->TemplatePath) ? $object->TemplatePath : '',
				'OldDocTypeName' => isset($object->DocTypeName) ? $object->DocTypeName : '',
				'Examined' => 1,
			);

			if(isset($object->elements)){
				$extra['elements'] = $object->elements;
			}

			$object->ID = 0;
			$object->Table = $this->getTable($object->ClassName);

			switch($object->ClassName){
				case 'we_base_model':
					$extra['ContentType'] = 'category';
					break;
				case 'we_docTypes':
					$extra['ContentType'] = 'doctype';
					$dtid = f('SELECT ID FROM ' . DOC_TYPES_TABLE . ' WHERE DocType="' . $db->escape($object->DocType) . '"', '', $db);
					if($dtid){
						switch($this->options["handle_collision"]){
							case 'replace':
								$object->ID = $dtid;
								break;
							case 'rename':
								$this->getNewName($object, $dtid, "DocType");
								break;
							default:
								$save = false;
								continue;
						}
					}
					break;
				case 'we_navigation_rule':
				case 'weNavigationRule':
					$nid = f('SELECT ID FROM ' . NAVIGATION_RULE_TABLE . ' WHERE NavigationName="' . $db->escape($object->NavigationName) . '"', '', $db);
					if($nid){
						switch($this->options["handle_collision"]){
							case "replace":
								$object->ID = $nid;
								break;
							case "rename":
								$this->getNewName($object, $nid, "NavigationName");
								break;
							default:
								$save = false;
								continue;
						}
					}
					break;
				case 'we_thumbnail':
				case 'we_thumbnailEx':
					$nid = f('SELECT ID FROM ' . THUMBNAILS_TABLE . ' WHERE Name="' . $db->escape($object->Name) . '"', "", $db);
					if($nid){
						switch($this->options['handle_collision']){
							case 'replace':
								$object->ID = $nid;
								break;
							case 'rename':
								$this->getNewName($object, $nid, 'Name');
								break;
							default:
								$save = false;
								continue;
						}
					}
					break;
			}

			if(isset($object->Path)){
				if(isset($object->Table) && !empty($object->Table)){
					$prefix = '/';
					switch($object->Table){
						case FILE_TABLE:
							if($this->options["document_path"]){
								$prefix = id_to_path($this->options["document_path"], FILE_TABLE, $db);
							}
							$object->Path = $prefix . ($this->options["restore_doc_path"] ?
											$object->Path :
											'/' . $object->Text);
							break;
						case TEMPLATES_TABLE:
							if($this->options["template_path"]){
								$prefix = id_to_path($this->options["template_path"], TEMPLATES_TABLE, $db);
							}
							$object->Path = $prefix . ($this->options["restore_tpl_path"] ?
									$object->Path :
									'/' . $object->Text);
							break;
						case NAVIGATION_TABLE:
							if($this->options["navigation_path"]){
								$prefix = id_to_path($this->options["navigation_path"], NAVIGATION_TABLE, $db);
							}
							$object->Path = $prefix . $object->Path;
							break;
					}

					$object->Path = we_base_file::clearPath($object->Path);

					//fix Path if there is a conflict
					$id = path_to_id($object->Path, $object->Table, $GLOBALS['DB_WE']);

					if($id){
						if($this->options["handle_collision"] === "replace" ||
								($object->ClassName == "we_folder" && $this->RefTable->exists(array("OldID" => $object->ID, "Table" => $object->Table)))
						){
							$object->ID = $id;
							if(isset($object->isnew)){
								$object->isnew = 0;
							}
						} else if($this->options["handle_collision"] === "rename"){
							$this->getNewName($object, $id, "Path");
						} else {
							$save = false;
							continue;
						}
					}
				}
				//fix Path ends
				// set OldPath
				if(isset($object->OldPath)){
					$object->OldPath = $object->Path;
				}

				// assign ParentID and ParentPath based on Path
				if(isset($object->Table)){
					$pathids = array();
					$old_pid = $object->ParentID;
					$owner = ($this->options['owners_overwrite'] && $this->options['owners_overwrite_id']) ? $this->options['owners_overwrite_id'] : 0;
					if(defined('OBJECT_TABLE') && $object->ClassName === 'we_objectFile'){
						//dont create Path in objects if the class doesn't exist
						$match = array();
						preg_match('|(/+[a-zA-Z0-9_+-\.]*)|', $object->Path, $match);
						if(isset($match[0]) && !f('SELECT 1 FROM ' . OBJECT_TABLE . ' WHERE Path="' . $db->escape($match[0]) . '" LIMIT 1', '', $db)){
							return false;
						}
					}
					$object->ParentID = self::makePath(dirname($object->Path), $object->Table, $pathids, $owner);
					if(isset($object->ParentPath)){
						$object->ParentPath = id_to_path($object->ParentID, $object->Table);
					}
					// insert new created folders in ref table
					foreach($pathids as $pid){

						$h = getHash('SELECT ParentID,Path FROM ' . $db->escape($object->Table) . ' WHERE ID=' . intval($pid), $db);
						if(!$this->RefTable->exists(array('ID' => $pid, 'ContentType' => 'folder'))){
							$this->RefTable->add2(
								array(
									'ID' => $pid,
									'ParentID' => $h['ParentID'],
									'Path' => $h['Path'],
									'Table' => $object->Table,
									'ContentType' => 'folder',
									'OldID' => ($pid == $object->ParentID) ? $old_pid : null,
									'OldParentID' => null,
									'OldPath' => null,
									'OldTemplatePath' => null,
									'Examined' => 0,
								)
							);
						}
					}
				}

				if($object->ClassName === 'we_backup_binary'){
					if(is_file($_SERVER['DOCUMENT_ROOT'] . $object->Path)){
						switch($this->options['handle_collision']){
							case 'replace':
								$save = true;
								break;
							case 'rename':
								$c = 1;
								do{
									$path = $object->Path . '_' . $c;
									$c++;
								} while(is_file($_SERVER['DOCUMENT_ROOT'] . $path));
								$object->Path = $path;
								unset($path);
								unset($c);
								break;
							default:
								$save = false;
						}
					}

					if($save && !$this->RefTable->exists(array('ID' => $object->ID, 'Path' => $object->Path, 'ContentType' => 'weBinary'))){
						$this->RefTable->add2(array(
							'ID' => $object->ID,
							'ParentID' => 0,
							'Path' => $object->Path,
							'Table' => $object->Table,
							'ContentType' => 'weBinary'
						));
					}
				}
			}

			if(defined('OBJECT_TABLE') && ($object->ClassName === 'we_objectFile' || $object->ClassName === 'we_class_folder')){
				$ref = $this->RefTable->getRef(array(
					'OldID' => $object->TableID,
					'ContentType' => "object"
				));
				if($ref){
					// assign TableID and ParentID from reference
					$object->TableID = $ref->ID;
				} else {
					//assign TableID based on Path
					// evaluate root dir for object
					$match = array();
					preg_match('|(/+[a-zA-Z0-9_+-\.]*)|', $object->Path, $match);
					if(isset($match[0])){
						$object->TableID = f('SELECT ID FROM ' . OBJECT_TABLE . ' WHERE Path="' . $db->escape($match[0]) . '"', '', $db);
					}
				}
			}

			// update owners data
			$this->refreshOwners($object);
			if(!defined('IMPORT_RUNNING')){
				define('IMPORT_RUNNING', 1);
			}

			if($save){
				$save = $this->saveObject($object);
			}
			$this->RefTable->add($object, $extra);
		}
		return $save;
	}

	function getNewName(&$object, $id, $prop){
		$c = 0;
		$newid = $id;
		do{
			$c++;

			switch($object->ClassName){
				case "we_docTypes" :
				case 'we_navigation_rule':
				case "weNavigationRule":
				case "we_thumbnail":
				case "we_thumbnailEx":
					$newname = $object->$prop;
					break;
				default:
					$newname = basename($object->$prop);
			}

			if($newid){
				$newname = $c . "_" . $newname;
			}
			switch($object->ClassName){
				case "we_docTypes":
					$newid = f('SELECT ID FROM ' . DOC_TYPES_TABLE . ' dt WHERE dt.DocType="' . escape_sql_query($newname) . '"', '', new DB_WE());
					break;
				case 'we_navigation_rule':
				case 'weNavigationRule':
					$newid = f('SELECT ID FROM ' . NAVIGATION_RULE_TABLE . ' nr WHERE nr.NavigationName="' . escape_sql_query($newname) . '"', '', new DB_WE());
					break;
				case 'we_thumbnail':
				case 'we_thumbnailEx':
					$newid = f('SELECT ID FROM ' . THUMBNAILS_TABLE . ' t WHERE t.Name="' . escape_sql_query($newname) . '"', '', new DB_WE());
					break;
				default:
					$newid = path_to_id(we_base_file::clearPath(dirname($object->Path) . '/' . $newname), $object->Table, FILE_TABLE, $GLOBALS['DB_WE']);
			}
		}while($newid);
		$this->renameObject($object, $newname);
	}

	function renameObject(&$object, $new_name){
		switch($object->ClassName){
			case "we_docTypes":
				$object->DocType = $new_name;
				return;
			case 'we_navigation_rule':
			case "weNavigationRule":
				$object->NavigationName = $new_name;
				return;
			case 'we_thumbnail':
			case "we_thumbnailEx":
				$object->Name = $new_name;
				return;
		}
		if(isset($object->Path)){
			$path = dirname($object->Path);
			$ref = $this->RefTable->getRef(array(
				'OldID' => $object->ParentID,
				'ContentType' => 'weNavigation'
					));
			if($ref){
				$object->ParentID = $ref->ID;
				$object->Path = $ref->Path . '/' . $new_name;
			} else {
				$object->Path = we_base_file::clearPath(dirname($object->Path) . '/' . $new_name);
			}
		}
		if(isset($object->Text)){
			$object->Text = $new_name;
		}
		if(isset($object->Filename)){
			$object->Filename = str_replace($object->Extension, '', $new_name);
		}
	}

	function importNodeSet($node_id){
		$i = 0;
		$object = '';
		$node_props = $node_data = $node_coding = array();

		if($this->xmlBrowser->getChildren($node_id, $node_props)){

			foreach($node_props as $node){
				$this->xmlBrowser->seek($node);
				$nodname = $this->xmlBrowser->getNodeName();
				$noddata = $this->xmlBrowser->getNodeData();
				$attributes = $this->xmlBrowser->getNodeAttributes();


				switch($nodname){
					case 'we:info':
						$this->importNodeSet($node);
						break;
					case 'we:map':
						$this->RefTable->Users[$attributes['user']] = $attributes;
						break;
					case 'we:content':
						$i++;
						$this->xmlBrowser->addMark('we:content');
						$content = $this->importNodeSet($node);
						$this->xmlBrowser->gotoMark('we:content');
						if(!is_object($object)){
							t_e($this->xmlBrowser, $nodname, $noddata, $attributes);
						}
						if($object){
							$object->elements = array_merge($object->elements, $content->getElement());
						}
						break;
					case 'ClassName':
						$this->nodehierarchy[] = $noddata;
						switch($noddata){
							case "we_object":
								$object = (defined('OBJECT_TABLE') ? new we_backup_object() : '');

								break;
							case "we_objectFile":
								$object = (defined('OBJECT_FILES_TABLE') ? new we_objectFile() : '');

								break;
							case 'we_class_folder': //Bug 3857 sonderbehandlung hinzugef�gt, da es sonst hier beim letzten else zum Absturz kommt, es wird nichts geladen, da eigentlich alles geladen ist
								$object = (defined('OBJECT_FILES_TABLE') ? new we_class_folder() : '');
								break;
							case 'we_navigation_navigation':
							case 'weNavigation':
								$object = new we_navigation_navigation();
								break;
							case 'we_navigation_rule':
							case 'weNavigationRule':
								$object = new we_navigation_rule();
								break;
							case 'we_thumbnail':
							case 'we_thumbnailEx':
								$object = new we_exim_thumbnailExport();
								break;
							case 'we_backup_binary':
							case 'weBinary'://FIXME: remove
							default:
								$object = new $noddata();
								break;
						}
					//no break!
					default:
						$node_data[$nodname] = $noddata;
						$node_coding[$nodname] = (isset($attributes[we_exim_contentProvider::CODING_ATTRIBUTE]) ? $attributes[we_exim_contentProvider::CODING_ATTRIBUTE] : we_exim_contentProvider::CODING_NONE);
				}
			}
		}

		if($object){
			$reflect = new ReflectionClass($object);
			$props = $reflect->getProperties(ReflectionProperty::IS_PRIVATE | ReflectionProperty::IS_PROTECTED);
			foreach($props as $prop){
				unset($node_data[$prop->getName()]);
			}

			we_exim_contentProvider::populateInstance($object, $node_data);

			foreach($node_data as $k => $v){
				$v = we_exim_contentProvider::getDecodedData($node_coding[$k], $v);

				if($v != $object->$k){
					$object->$k = $v;
				}
			}
		}

		return $object;
	}

	public static function isSerialized($str){
		return ($str === serialize(false) || @unserialize($str) !== false);
	}

	function changeEncoding($value){
		if($this->options['change_encoding']){
			if($this->options['target_encoding'] != '' && $this->options['xml_encoding'] != '' && $this->options['target_encoding'] != $this->options['xml_encoding']){

				if($value == $this->options['xml_encoding']){
					return $this->options['target_encoding'];
				}
				if(self::isSerialized($value)){
					$usv = we_unserialize($value);
					if(is_array($usv)){
						foreach($usv as &$av){
							if($this->options['xml_encoding'] === 'ISO-8859-1'){
								$av = utf8_encode($av);
							} else {
								$av = utf8_decode($av);
							}
						}
						$sv = we_serialize($usv);
						return $sv;
					}
					return $value;
				}
				return ($this->options['xml_encoding'] === 'ISO-8859-1' ?
								utf8_encode($value) :
								utf8_decode($value));
			}
		}
		return $value;
	}

	function refreshOwners(&$object){
		if(isset($object->CreatorID) && ($this->options['handle_owners'] || $this->options['owners_overwrite'])){
			$userid = $object->CreatorID;
			if($this->options['handle_owners']){
				$userid = $this->RefTable->getNewOwnerID($userid);
				if($userid == 0 && $this->options['owners_overwrite'] && $this->options['owners_overwrite_id']){
					$userid = $this->options['owners_overwrite_id'];
				}
			} else if($this->options['owners_overwrite'] && $this->options['owners_overwrite_id']){
				$userid = $this->options['owners_overwrite_id'];
			} else {
				$userid = 0;
			}
			$object->CreatorID = $userid;
			if(isset($object->ModifierID)){
				$object->ModifierID = $userid;
			}
		} else {
			if(isset($object->CreatorID)){
				$object->CreatorID = 0;
			}
			if(isset($object->ModifierID)){
				$object->ModifierID = 0;
			}
		}

		if(isset($object->Owners) && ($this->options['handle_owners'] || $this->options['owners_overwrite'])){
			$owners = makeArrayFromCSV($object->Owners);
			$newowners = array();
			foreach($owners as $owner){
				if($this->options['handle_owners']){
					$own = $this->RefTable->getNewOwnerID($owner);
					if($own == 0 && $this->options['owners_overwrite'] && $this->options['owners_overwrite_id']){
						$own = $this->options['owners_overwrite_id'];
					}
				} else if($this->options['owners_overwrite'] && $this->options['owners_overwrite_id']){
					$own = $this->options['owners_overwrite_id'];
				}
				if(!empty($own) && !in_array($own, $newowners)){
					if(!$object->CreatorID){
						$object->CreatorID = $own;
					}
					if(!$object->ModifierID){
						$object->ModifierID = $own;
					}
					$newowners[] = $own;
				}
			}
			$object->Owners = implode(',', $newowners);
			if(isset($object->OwnersReadOnly)){
				$readonly = we_unserialize($object->OwnersReadOnly);
				$readonly_new = array();
				if(is_array($readonly)){
					foreach($readonly as $key => $value){
						if($this->options['handle_owners']){
							$newkey = $this->RefTable->getNewOwnerID($key);
							if($newkey == 0 && $this->options['owners_overwrite'] && $this->options['owners_overwrite_id']){
								$newkey = $this->options['owners_overwrite_id'];
							}
						} else if($this->options['owners_overwrite'] && $this->options['owners_overwrite_id']){
							$newkey = $this->options['owners_overwrite_id'];
						}
						if($newkey){
							$readonly_new[$newkey] = $value;
						}
					}
					$object->OwnersReadOnly = we_serialize($readonly_new);
				}
			}
		} else {
			if(isset($object->Owners)){
				$object->Owners = '';
			}
			if(isset($object->RestrictOwners)){
				$object->RestrictOwners = 0;
			}
			if(isset($object->OwnersReadOnly)){
				$object->OwnersReadOnly = serialize(array());
			}
		}
	}

	function splitFile($filename, $tmppath, $count){
		if(!$filename){
			return -1;
		}

		$path = $tmppath;
		$marker = we_backup_util::backupMarker;
		$marker2 = "<!--webackup -->"; //Bug 5089
		$pattern = basename($filename) . "_%s";

		$compress = (we_base_file::isCompressed($filename) ? we_backup_util::COMPRESSION : we_backup_util::NO_COMPRESSION);
		$head = we_base_file::loadPart($filename, 0, 256, $compress === 'gzip');

		$encoding = we_xml_parser::getEncoding('', $head);
		$_SESSION['weS']['weXMLimportCharset'] = $encoding;
		$header = ''; //weXMLExIm::getHeader($encoding);
		$footer = we_exim_XMLExIm::getFooter();

		$buff = $filename_tmp = "";
		$fh = ($compress != we_backup_util::NO_COMPRESSION ? gzopen($filename, "rb") : @fopen($filename, "rb"));

		$num = -1;
		$fsize = $elnum = 0;
		$fh_temp = 0;

		$marker_size = strlen($marker);
		$marker2_size = strlen($marker2); //Backup 5089

		if($fh){
			while(!feof($fh)){
				$line = "";
				$findline = false;

				while($findline == false && !@feof($fh)){
					$line .= ($compress != we_backup_util::NO_COMPRESSION ? @gzgets($fh, 4096) : @fgets($fh, 4096));
					if(substr($line, -1) === "\n"){
						$findline = true;
					}
				}

				if(!$fh_temp && $line && trim($line) != we_backup_util::weXmlExImFooter){
					$num++;
					$filename_tmp = sprintf($path . $pattern, $num);
					$fh_temp = fopen($filename_tmp, "wb");
					if(!$fh_temp){
						return -1;
					}
					if($header){
						fwrite($fh_temp, $header);
					}
					/* if($num == 0){
					  $header = "";
					  } */
				}

				if($fh_temp){
					if((substr($line, 0, 2) != "<?") && (substr($line, 0, 11) != we_backup_util::weXmlExImHead) && (substr($line, 0, 12) != we_backup_util::weXmlExImFooter)){

						$buff.=$line;
						$write = false;
						if($marker_size){
							$write = ((substr($buff, (0 - ($marker_size + 1))) == $marker . "\n") || (substr($buff, (0 - ($marker_size + 2))) == $marker . "\r\n") || (substr($buff, (0 - ($marker2_size + 1))) == $marker2 . "\n") || (substr($buff, (0 - ($marker2_size + 2))) == $marker2 . "\r\n" ));
						} else {
							$write = true;
						}

						if($write){
							$fsize+=strlen($buff);
							fwrite($fh_temp, $buff);
							if($marker_size){
								$elnum++;
								if($elnum >= $count){
									$elnum = 0;
									fwrite($fh_temp, $footer);
									fclose($fh_temp);
									$fh_temp = 0;
								}
								$fsize = 0;
							}
							$buff = "";
						}
					} else {
						if(((substr($line, 0, 2) === "<?") || (substr($line, 0, 11) == we_backup_util::weXmlExImHead)) && $num == 0){
							$header.=$line;
							fwrite($fh_temp, $line);
						}
					}
				}
			}
		} else {
			return -1;
		}
		if($fh_temp && trim($line) != we_backup_util::weXmlExImFooter){
			if($buff){
				fwrite($fh_temp, $buff);
			}
			fwrite($fh_temp, $footer);
			fclose($fh_temp);
			$fh_temp = 0;
		}
		if($compress != we_backup_util::NO_COMPRESSION){
			gzclose($fh);
		} else {
			fclose($fh);
			$fh_temp = 0;
		}

		return $num + 1;
	}

	private function handleTag($tag){
		switch($tag){
			case 'we:document':
				return $this->options['handle_documents'];
			case 'we:template':
				return $this->options['handle_templates'];
			case 'we:class':
				return $this->options['handle_classes'];
			case 'we:object':
				return $this->options['handle_objects'];
			case 'we:doctype':
				return $this->options['handle_doctypes'];
			case 'we:category':
				return $this->options['handle_categorys'];
			case 'we:content':
				return $this->options['handle_content'];
			case 'we:table':
				return $this->options['handle_table'];
			case 'we:tableitem':
				return $this->options['handle_tableitems'];
			case 'we:binary':
				return $this->options['handle_binarys'];
			case 'we:navigation':
				return $this->options['handle_navigation'];
			case 'we:navigationrule':
				return $this->options['handle_navigation'];
			case 'we:thumbnail':
				return $this->options['handle_thumbnails'];
			case 'we:map'://internal
			case 'we:info':
				return false;
			default:
				return true;
		}
	}

	/**
	 * This function creates the given path in the repository and returns the id of the last created folder
	 *
	 * @param          string				$path
	 * @param          string				$table
	 * @param          array				$pathids
	 *
	 * @return         string
	 */
	private static function makePath($path, $table, &$pathids, $owner = 0){
		$path = str_replace('\\', '/', $path);
		$patharr = explode('/', $path);
		$mkpath = '';
		$pid = 0;
		foreach($patharr as $elem){
			if($elem != '' && $elem != '/'){
				$mkpath .= '/' . $elem;
				$id = path_to_id($mkpath, $table, $GLOBALS['DB_WE']);
				if(!$id){
					$new = new we_folder();
					$new->Text = $elem;
					$new->Filename = $elem;
					$new->ParentID = $pid;
					$new->Path = $mkpath;
					$new->Table = $table;
					$new->CreatorID = $owner;
					$new->ModifierID = $owner;
					$new->Owners = ',' . $owner . ',';
					$new->OwnersReadOnly = serialize(array(
						$owner => 0
					));
					$new->we_save();
					$id = $new->ID;
					$pathids[] = $id;
				}
				$pid = $id;
			}
		}

		return $pid;
	}

}
