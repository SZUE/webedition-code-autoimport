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
abstract class we_rebuild_base{

	public static function rebuild($data, $printIt = false){
		if($printIt){
			$_newLine = count($_SERVER['argv']) ? "\n" : "<br/>\n";
		}

		switch($data['type']){
			case 'navigation':
				if($printIt){
					echo ('Rebuilding Navigation Item with Id: ' . $data['id']);
					flush();
				}

				if($data['id']){ //don't save id=0
					$nav = new we_navigation_navigation($data['id']);
					$nav->save(false, true);
				}
				//clean AFTER rebuild to make sure all data is acurate afterwards!
				we_navigation_cache::clean(true);
				if($printIt){
					echo ("   done$_newLine");
					flush();
				}
				break;
			case 'thumbnail':
				$imgdoc = new we_imageDocument();
				$imgdoc->initByID($data['id']);
				if($printIt){
					echo ('Rebuilding thumb for image: ' . $imgdoc->Path);
					flush();
				}

				$imgdoc->Thumbs = $data['thumbs'] ? : -1;
				$imgdoc->DocChanged = true;
				$imgdoc->we_save(true);
				unset($imgdoc);
				if($printIt){
					echo ("   done$_newLine");
					flush();
				}
				break;
			case 'metadata':
				$imgdoc = new we_imageDocument();
				$imgdoc->initByID($data['id']);
				if($printIt){
					echo ('Rebulding meta data for image: ' . $imgdoc->Path);
					flush();
				}

				$imgdoc->importMetaData($data['metaFields'], $data['onlyEmpty']);

				$imgdoc->we_save(true);
				unset($imgdoc);
				if($printIt){
					echo ("   done$_newLine");
					flush();
				}
				break;
			case 'medialink':
				switch($data['cn']){
					case 'we_banner_banner':
					case 'we_category':
					case 'we_customer_customer':
					case 'we_glossary_glossary':
					case 'we_navigation_navigation':
					case 'we_newsletter_newsletter':
						$model = new $data['cn'](intval($data['id']));
						if($printIt){
							echo ('Rebulding Media-Links for: ' . $model->Text);
							flush();
						}
						$model->registerMediaLinks();
						break;
					case 'we_temporaryDocument':
						$content = we_temporaryDocument::load($data['id'], $data['tbl'], $GLOBALS['DB_WE']);
						if($data['tbl'] === 'tblFile'){
							$doc = new we_webEditionDocument();
							$doc->Table = FILE_TABLE;
						} else {
							$doc = new we_objectFile();
							$doc->Table = OBJECT_FILES_TABLE;
							$doc->TableID = $content[0]['TableID'];
						}
						$doc->elements = $content[0]['elements'];
						$doc->ID = $data['id'];
						if($printIt){
							echo ('Rebulding Media-Links for: ' . $doc->Path);
							flush();
						}
						$doc->parseTextareaFields('temp');
						$doc->registerMediaLinks(true);
						unset($doc);
						break;
					default:
						$doc = new $data['cn'];
						$doc->initByID($data['id'], $doc->Table);
						if($printIt){
							echo ('Rebulding Media-Links for: ' . $doc->Path);
							flush();
						}
						$doc->correctFields();
						$doc->parseTextareaFields('main');
						$doc->registerMediaLinks();
						unset($doc);
				}
				if($printIt){
					echo ("   done$_newLine");
					flush();
				}
				break;
			default:
				switch($data['type']){
					case 'document':
						$table = FILE_TABLE;
						break;
					case 'template':
						$table = TEMPLATES_TABLE;
						break;
					case 'object':
						$table = OBJECT_FILES_TABLE;
						break;
					default:
						return false;
				}
				$tmp = $data['cn'];
				if(!$tmp){
					t_e('Element ' . $data['id'] . ' in ' . $table . ' has no documentclass');
					return;
				}
				$GLOBALS['we_doc'] = new $tmp();
				$GLOBALS['we_doc']->initByID($data['id'], $table, (defined('OBJECT_FILES_TABLE') && $table == OBJECT_FILES_TABLE ? we_class::LOAD_TEMP_DB : we_class::LOAD_MAID_DB));
				if($printIt){
					echo ('Rebuilding: ' . $GLOBALS['we_doc']->Path);
					flush();
				}

				/* removed 30.12.2011
				 * if($data['mt'] || $data['tt']){
				  $GLOBALS['we_doc']->correctFields();
				  } */


				/* removed: can cause data loss
				 * if($data['tt']){
				  $GLOBALS['we_doc']->we_resaveTemporaryTable();
				  } */
				if($data['mt'] || ($table == TEMPLATES_TABLE)){
					$tmpPath = $GLOBALS['we_doc']->constructPath();
					if($tmpPath){
						$GLOBALS['we_doc']->Path = $tmpPath;
					}

					$ret = ($table == TEMPLATES_TABLE ?
							// templates has to be treated differently
							$GLOBALS['we_doc']->we_save(true) :
							$GLOBALS['we_doc']->we_resaveMainTable());
					if(!$ret){
						t_e('error writing ' . $GLOBALS['we_doc']->Path);
					}
				}
				if($data['it']){
					$GLOBALS['we_doc']->insertAtIndex();
				} else {
					if(!$GLOBALS['we_doc']->we_rewrite()){
						t_e('error in writing document', $GLOBALS['we_doc']->Path);
					}
					$GLOBALS['we_doc']->we_republish($data['mt']);
				}
				if($printIt){
					echo ("   done$_newLine");
					flush();
				}
		}
	}

	/**
	 * Create and returns data Array with IDs and other information for the fragmment class for rebuilding documents
	 *
	 * @return array
	 * @param string $btype "rebuild_all" or "rebuild_filter"
	 * @param string $categories csv value of category IDs
	 * @param boolean $catAnd true if AND should be used for more than one categories (default=OR)
	 * @param string $doctypes csv value of doctypeIDs
	 * @param string $folders csv value of directory IDs
	 * @param boolean $maintable if the main table should be rebuilded
	 * @param boolean $tmptable if the tmp table should be rebuilded
	 * @param int $templateID ID of a template (All documents of this template should be rebuilded)
	 */
	public static function getDocuments($btype = 'rebuild_all', $categories = '', $catAnd = false, $doctypes = '', $folders = '', $maintable = false, $tmptable = false, $templateID = 0){
		switch($btype){
			case 'rebuild_all':
				return self::getAllDocuments($maintable, $tmptable);
			case 'rebuild_templates':
				return self::getTemplates();
			case 'rebuild_filter':
				return self::getFilteredDocuments($categories, $catAnd, $doctypes, $folders, $templateID);
		}
	}

	/**
	 * Create and returns data Array with IDs and other information for the fragmment class for rebuilding all documents and templates (Called from getDocuments())
	 *
	 * @return array
	 * @param boolean $maintable if the main table should be rebuilded
	 * @param boolean $tmptable if the tmp table should be rebuilded
	 */
	private static function getAllDocuments($maintable, $tmptable){
		if(!permissionhandler::hasPerm('REBUILD_ALL')){
			return array();
		}
		$data = self::getTemplates(true, $maintable, $tmptable);

		$GLOBALS['DB_WE']->query('SELECT ID,ClassName,Path FROM ' . FILE_TABLE . ' WHERE IsFolder=1 OR Published>0 ORDER BY IsFolder DESC, LENGTH(Path)');
		while($GLOBALS['DB_WE']->next_record()){
			$data[] = array(
				'id' => $GLOBALS['DB_WE']->f('ID'),
				'type' => 'document',
				'cn' => $GLOBALS['DB_WE']->f('ClassName'),
				'mt' => $maintable,
				'tt' => $tmptable,
				'path' => $GLOBALS['DB_WE']->f('Path'),
				'it' => 0);
		}
		$GLOBALS['DB_WE']->query('SELECT ID,ClassName,Path FROM ' . FILE_TABLE . ' WHERE IsDynamic=0 AND Published>0 AND ContentType="' . we_base_ContentTypes::WEDOCUMENT . '" ORDER BY ID');
		while($GLOBALS['DB_WE']->next_record()){
			$data[] = array(
				'id' => $GLOBALS['DB_WE']->f('ID'),
				'type' => 'document',
				'cn' => $GLOBALS['DB_WE']->f('ClassName'),
				'mt' => $maintable,
				'tt' => $tmptable,
				'path' => $GLOBALS['DB_WE']->f('Path'),
				'it' => 0);
		}
		/* why do we make an rebuild of navi table?
		  $GLOBALS['DB_WE']->query('SELECT ID,Path FROM ' . NAVIGATION_TABLE . ' WHERE IsFolder=0 ORDER BY ID');
		  while($GLOBALS['DB_WE']->next_record()){
		  $data[] = array(
		  'id' => $GLOBALS['DB_WE']->f('ID'),
		  'type' => 'navigation',
		  'cn' => 'weNavigation',
		  'mt' => $maintable,
		  'tt' => $tmptable,
		  'path' => $GLOBALS['DB_WE']->f('Path'),
		  'it' => 0);
		  }
		  $data[] = array(
		  'id' => 0,
		  'type' => 'navigation',
		  'cn' => 'weNavigation',
		  'mt' => $maintable,
		  'tt' => $tmptable,
		  'path' => $GLOBALS['DB_WE']->f('Path'),
		  'it' => 0
		  ); */

		return $data;
	}

	/**
	 * Create and returns data Array with IDs and other information for the fragmment class for rebuilding metadata
	 *
	 * @return array
	 * @param array $metaFields array which meta fields should be rebuilt
	 * @param boolean $onlyEmpty if this is true, only empty fields will be imported
	 * @param array $metaFolders array with folder Ids
	 */
	public static function getMetadata($metaFields, $onlyEmpty, $metaFolders){

		if(!is_array($metaFolders)){
			$metaFolders = makeArrayFromCSV($metaFolders);
		}
		$data = array();
		if(permissionhandler::hasPerm('REBUILD_META')){
			$foldersQuery = count($metaFolders) ? ' AND ParentId IN(' . implode(',', $metaFolders) . ') ' : '';
			$GLOBALS['DB_WE']->query('SELECT ID,path FROM ' . FILE_TABLE . ' WHERE ContentType="' . we_base_ContentTypes::IMAGE . '" AND Extension IN (".jpg","jpeg","wbmp") ' . $foldersQuery);
			while($GLOBALS['DB_WE']->next_record()){
				$data[] = array(
					'id' => $GLOBALS['DB_WE']->f('ID'),
					'type' => 'metadata',
					'onlyEmpty' => $onlyEmpty,
					'path' => $GLOBALS['DB_WE']->f('path'),
					'metaFields' => $metaFields
				);
			}
		}
		return $data;
	}

	public static function getMediaLinks(){
		// delete all media links
		$GLOBALS['DB_WE']->query('DELETE FROM ' . FILELINK_TABLE . ' WHERE type="media"');


		//FIXME: add permission
		$data = array();
		//FIXME: add classes and templates
		//FIXME: pack this queries into the loop too
		$GLOBALS['DB_WE']->query('SELECT ID,ClassName,Path,ModDate,Published FROM ' . FILE_TABLE . ' WHERE
			ContentType IN ("' . we_base_ContentTypes::WEDOCUMENT . '","' . we_base_ContentTypes::IMAGE . '","' . we_base_ContentTypes::CSS . '","' . we_base_ContentTypes::JS . '")
			AND IsFolder = 0 ORDER BY ID');
		while($GLOBALS['DB_WE']->next_record()){
			// beware of a very special case: when a document is unpublished && mofified we must not have any main-table entries!
			if($GLOBALS['DB_WE']->f('Published') > 0 || $GLOBALS['DB_WE']->f('Published') == $GLOBALS['DB_WE']->f('ModDate')){
				$data[] = array(
					'id' => $GLOBALS['DB_WE']->f('ID'),
					'type' => 'medialink',
					'cn' => $GLOBALS['DB_WE']->f('ClassName'),
					'mt' => 1,
					'tt' => 0,
					'path' => $GLOBALS['DB_WE']->f('Path'),
					'it' => 0);
			}
		}

		$GLOBALS['DB_WE']->query('SELECT ID,ClassName,Path FROM ' . OBJECT_FILES_TABLE . ' WHERE IsFolder = 0 ORDER BY ID');
		while($GLOBALS['DB_WE']->next_record()){
			// beware of a very special case: when an object is unpublished && mofified we must not have any main-table entries!
			if($GLOBALS['DB_WE']->f('Published') > 0 || $GLOBALS['DB_WE']->f('Published') == $GLOBALS['DB_WE']->f('ModDate')){
				$data[] = array(
					'id' => $GLOBALS['DB_WE']->f('ID'),
					'type' => 'medialink',
					'cn' => $GLOBALS['DB_WE']->f('ClassName'),
					'mt' => 1,
					'tt' => 0,
					'path' => $GLOBALS['DB_WE']->f('Path'),
					'it' => 0);
			}
		}

		$GLOBALS['DB_WE']->query('SELECT DocumentID,DocTable FROM ' . TEMPORARY_DOC_TABLE . ' ORDER BY DocumentID');
		while($GLOBALS['DB_WE']->next_record()){
			$data[] = array(
				'id' => $GLOBALS['DB_WE']->f('DocumentID'),
				'type' => 'medialink',
				'cn' => 'we_temporaryDocument',
				'tbl' => $GLOBALS['DB_WE']->f('DocTable'),
				'mt' => 0,
				'tt' => 1,
				'path' => '',
				'it' => 0);
		}

		$tables = array(
			array(TEMPLATES_TABLE, 'WHERE IsFolder = 0', 'we_template'),
			array(OBJECT_TABLE, 'WHERE IsFolder = 0', 'we_object'),
			array(VFILE_TABLE, 'WHERE IsFolder = 0', 'we_collection'),
			array(BANNER_TABLE, '', 'we_banner_banner'),
			array(CATEGORY_TABLE, 'WHERE Description != ""', 'we_category'),
			array(GLOSSARY_TABLE, 'WHERE IsFolder = 0 AND type = "link"', 'we_glossary_glossary'),
			array(NAVIGATION_TABLE, 'WHERE IconID != 0 OR (SelectionType = "docLink" AND LinkID != 0)', 'we_navigation_navigation'),
			array(NEWSLETTER_TABLE, '', 'we_newsletter_newsletter'),
			array(CUSTOMER_TABLE, 'WHERE IsFolder = 0', 'we_customer_customer'),
		);

		foreach($tables as $table){
			$GLOBALS['DB_WE']->query('SELECT ID,Path FROM ' . $table[0] . ' ' . $table[1] . ' ORDER BY ID');
			while($GLOBALS['DB_WE']->next_record()){
				$data[] = array(
					'id' => $GLOBALS['DB_WE']->f('ID'),
					'type' => 'medialink',
					'cn' => $table[2],
					'mt' => 1,
					'tt' => 0,
					'path' => $GLOBALS['DB_WE']->f('Path'),
					'it' => 0);
			}
		}

		return $data;
	}

	private static function insertTemplatesInArray(array $all, array &$data, $mt, $tt){
		foreach($all as $cur){
			$data[] = array(
				'id' => $cur['ID'],
				'type' => 'template',
				'cn' => $cur['ClassName'],
				'mt' => $mt,
				'tt' => $tt,
				'path' => $cur['Path'],
				'it' => 0
			);
		}
	}

	private static function getDependendTemplates(we_database_base $db, array $done, array &$data, $mt, $tt){
		//get other, these have to be processed in php
		$db->query('SELECT ID,ClassName,Path,MasterTemplateID,IncludedTemplates FROM ' . TEMPLATES_TABLE . ' WHERE IsFolder=0 AND ID NOT IN (' . ($done ? implode(',', $done) : 0) . ') ORDER BY (`IncludedTemplates` = "") DESC');

		$todo = array();
		while($db->next_record(MYSQL_ASSOC)){
			$rec = $db->getRecord();
			$tmp = trim($rec['IncludedTemplates'], ',');
			$rec['IncludedTemplates'] = (empty($tmp) ? array() : array_diff(explode(',', $tmp), $done));
			$todo[] = $rec;
		}

		$round = 0;
		while(!empty($todo) && ++$round < 100){
			$preDone = array();
			foreach($todo as $key => &$rec){
				$rec['IncludedTemplates'] = empty($rec['IncludedTemplates']) ? $rec['IncludedTemplates'] : array_diff($rec['IncludedTemplates'], $done);
				if(empty($rec['IncludedTemplates']) && ($rec['MasterTemplateID'] == 0 || (array_search($rec['MasterTemplateID'], $done) !== FALSE))){
					$preDone[] = $rec;
					$done[] = $rec['ID'];
					unset($todo[$key]);
				}
			}
			self::insertTemplatesInArray($preDone, $data, $mt, $tt);
		}
		if($todo){
			//we have conflicts
			//t_e('conflicting templates in rebuild', $todo, $done);
			//add them even if rebuild will not succeed
			self::insertTemplatesInArray($todo, $data, $mt, $tt);
		}
	}

	/**
	 * Create and returns data Array with IDs and other information for the fragmment class for rebuilding all documents and templates (Called from getDocuments())
	 *
	 * @return array
	 */
	private static function getTemplates($all = false, $mt = 0, $tt = 0){
		if(!($all || permissionhandler::hasPerm('REBUILD_TEMPLATES'))){
			return array();
		}
		$data = array();
		$db = $GLOBALS['DB_WE'];
		//get all folders + easy templates
		$db->query('(SELECT ID,ClassName,Path FROM ' . TEMPLATES_TABLE . ' WHERE IsFolder=1 ORDER BY ID) UNION
			(SELECT ID,ClassName,Path FROM ' . TEMPLATES_TABLE . ' WHERE IsFolder=0 AND MasterTemplateID=0 AND IncludedTemplates="" ORDER BY LENGTH(Path)) UNION
			(SELECT ID,ClassName,Path FROM ' . TEMPLATES_TABLE . ' WHERE IsFolder=0 AND MasterTemplateID IN (SELECT ID FROM ' . TEMPLATES_TABLE . ' WHERE IsFolder=0 AND MasterTemplateID=0 AND IncludedTemplates="") AND IncludedTemplates="" ORDER BY LENGTH(Path))', true);
		self::insertTemplatesInArray($db->getAll(), $data, $mt, $tt);

		//make a done list with id's
		$db->query('SELECT ID FROM ' . TEMPLATES_TABLE . ' WHERE IsFolder=0 AND MasterTemplateID=0 AND IncludedTemplates="" UNION
		(SELECT ID FROM ' . TEMPLATES_TABLE . ' WHERE IsFolder=0 AND MasterTemplateID IN (SELECT ID FROM ' . TEMPLATES_TABLE . ' WHERE IsFolder=0 AND MasterTemplateID=0 AND IncludedTemplates="") AND IncludedTemplates="" ORDER BY LENGTH(Path))', true);
		$done = $db->getAll(true);

		self::getDependendTemplates($db, $done, $data, $mt, $tt);
		return $data;
	}

	/**
	 * Create and returns data Array with IDs and other information for the fragmment class for rebuilding filtered documents (Called from getDocuments())
	 *
	 * @return array
	 * @param string $categories csv value of category IDs
	 * @param boolean $catAnd true if AND should be used for more than one categories (default=OR)
	 * @param string $doctypes csv value of doctypeIDs
	 * @param string $folders csv value of directory IDs
	 * @param int $templateID ID of a template (All documents of this template should be rebuilded)
	 */
	private static function getFilteredDocuments($categories, $catAnd, $doctypes, $folders, $templateID){
		if(!permissionhandler::hasPerm('REBUILD_FILTERD')){
			return array();
		}
		$data = array();
		$_cat_query = $_doctype_query = $_folders_query = $_template_query = '';

		if($categories){
			$_foo = makeArrayFromCSV($categories);
			$tmp = array();
			foreach($_foo as $catID){
				$tmp[] = ' FIND_IN_SET(' . intval($catID) . ',Category)';
			}
			$_cat_query = '(' . implode(' ' . ($catAnd ? ' AND ' : ' OR ') . ' ', $tmp) . ')';
		}
		if($doctypes){
			$_foo = makeArrayFromCSV($doctypes);
			$_doctype_query = 'Doctype IN (' . implode(',', $_foo) . ')';
		}
		if($folders){
			$_foo = makeArrayFromCSV($folders);
			$_foldersList = array();
			foreach($_foo as $folderID){
				$_foldersList = array_merge($_foldersList, we_base_file::getFoldersInFolder($folderID));
			}
			$_folders_query = '( ParentID IN(' . implode(',', $_foldersList) . '))';
		}

		if($templateID){
			$arr = self::getTemplAndDocIDsOfTemplate($templateID);

			if($arr['templateIDs']){

				//$GLOBALS['DB_WE']->query('SELECT ID,ClassName,Path FROM ' . TEMPLATES_TABLE . ' WHERE ' . $where . ' ORDER BY ID');
				//get other, these have to be processed in php

				$GLOBALS['DB_WE']->query('SELECT ID FROM ' . TEMPLATES_TABLE . ' WHERE IsFolder=0 AND ID NOT IN (' . implode(',', $arr['templateIDs']) . ')');
				$done = $GLOBALS['DB_WE']->getAll(true);

				self::getDependendTemplates($GLOBALS['DB_WE'], $done, $data, 0, 0);

				$tmp = $arr['templateIDs'];
				$tmp[] = $templateID;
				$_template_query = '(TemplateID IN (' . implode(',', $tmp) . '))';
			} else {
				$_template_query = '(TemplateID=' . intval($templateID) . ')';
			}
		}

		$query = ($_cat_query ? ' AND ' . $_cat_query . ' ' : '') .
			($_doctype_query ? ' AND ' . $_doctype_query . ' ' : '') .
			($_folders_query ? ' AND ' . $_folders_query . ' ' : '') .
			($_template_query ? ' AND ' . $_template_query . ' ' : '');

		if(!$categories && !$catAnd && !$doctypes && !$folders && !$templateID){
			$GLOBALS['DB_WE']->query('SELECT ID,ClassName,Path FROM ' . FILE_TABLE . ' WHERE IsFolder=1 ' . ($folders ? 'AND ' . $_folders_query : '') . 'ORDER BY IsFolder DESC, LENGTH(Path)');
			while($GLOBALS['DB_WE']->next_record()){
				$data[] = array(
					'id' => $GLOBALS['DB_WE']->f('ID'),
					'type' => 'document',
					'cn' => $GLOBALS['DB_WE']->f('ClassName'),
					'mt' => 0,
					'tt' => 0,
					'path' => $GLOBALS['DB_WE']->f('Path'),
					'it' => 0);
			}
		}

		$GLOBALS['DB_WE']->query('SELECT ID,ClassName,Path FROM ' . FILE_TABLE . ' WHERE IsDynamic=0 AND Published>0 AND ContentType IN("' . we_base_ContentTypes::WEDOCUMENT . '","' . we_base_ContentTypes::CSS . '","' . we_base_ContentTypes::JS . '") ' . $query . ' ORDER BY LENGTH(Path)');

		while($GLOBALS['DB_WE']->next_record()){
			$data[] = array(
				'id' => $GLOBALS['DB_WE']->f('ID'),
				'type' => 'document',
				'cn' => $GLOBALS['DB_WE']->f('ClassName'),
				'mt' => 0,
				'tt' => 0,
				'path' => $GLOBALS['DB_WE']->f('Path'),
				'it' => 0);
		}
		return $data;
	}

	/**
	 * Create and returns data Array with IDs and other information for the fragmment class for rebuilding the OBJECTFILES_TABLE
	 *
	 * @return array
	 */
	public static function getObjects(){
		we_updater::doUpdate();
		$data = array();
		if(permissionhandler::hasPerm('REBUILD_OBJECTS')){
			$GLOBALS['DB_WE']->query('SELECT ID,ClassName,Path FROM ' . OBJECT_FILES_TABLE . ' WHERE Published > 0 ORDER BY ID');
			while($GLOBALS['DB_WE']->next_record()){
				$data[] = array(
					'id' => $GLOBALS['DB_WE']->f('ID'),
					'type' => 'object',
					'cn' => $GLOBALS['DB_WE']->f('ClassName'),
					'mt' => 0,
					'tt' => 0,
					'path' => $GLOBALS['DB_WE']->f('Path'),
					'it' => 0);
			}
			$GLOBALS['DB_WE']->query('SELECT ID,ClassName,Path FROM ' . OBJECT_FILES_TABLE . ' WHERE IsFolder=1 ORDER BY ID');
			while($GLOBALS['DB_WE']->next_record()){
				$data[] = array(
					'id' => $GLOBALS['DB_WE']->f('ID'),
					'type' => 'object',
					'cn' => 'we_class_folder',
					'mt' => 0,
					'tt' => 0,
					'path' => $GLOBALS['DB_WE']->f('Path'),
					'it' => 0);
			}
		}
		return $data;
	}

	/**
	 * Create and returns data Array with IDs and other information for the fragmment class for rebuilding the INDEX_TABLE
	 *
	 * @return array
	 */
	public static function getNavigation(){
		$data = array();
		if(permissionhandler::hasPerm('REBUILD_NAVIGATION')){
			$GLOBALS['DB_WE']->query('SELECT ID,Path FROM ' . NAVIGATION_TABLE . ' WHERE IsFolder=0 ORDER BY ID');
			while($GLOBALS['DB_WE']->next_record()){
				$data[] = array(
					'id' => $GLOBALS['DB_WE']->f('ID'),
					'type' => 'navigation',
					'cn' => 'weNavigation',
					'mt' => 0,
					'tt' => 0,
					'path' => $GLOBALS['DB_WE']->f('Path'),
					'it' => 0);
			}
			$data[] = array(
				'id' => 0,
				'type' => 'navigation',
				'cn' => 'weNavigation',
				'mt' => 0,
				'tt' => 0,
				'path' => $GLOBALS['DB_WE']->f('Path'),
				'it' => 0
			);
		}

		if(we_base_request::_(we_base_request::BOOL, 'rebuildStaticAfterNavi')){
			$data2 = self::getFilteredDocuments('', '', '', '', '');
			$data = array_merge($data, $data2);
		}

		return $data;
	}

	/**
	 * Create and returns data Array with IDs and other information for the fragmment class for rebuilding the INDEX_TABLE
	 *
	 * @return array
	 */
	public static function getIndex(){
		if(!permissionhandler::hasPerm('REBUILD_INDEX')){
			return array();
		}
		$data = array();
		$GLOBALS['DB_WE']->query('SELECT ID,ClassName,Path FROM ' . FILE_TABLE . ' WHERE Published > 0 AND IsSearchable=1 ORDER BY ID');
		while($GLOBALS['DB_WE']->next_record()){
			$data[] = array(
				'id' => $GLOBALS['DB_WE']->f('ID'),
				'type' => 'document',
				'cn' => $GLOBALS['DB_WE']->f('ClassName'),
				'mt' => 0,
				'tt' => 0,
				'path' => $GLOBALS['DB_WE']->f('Path'),
				'it' => 1);
		}
		if(defined('OBJECT_FILES_TABLE')){
			$GLOBALS['DB_WE']->query('SELECT ID,ClassName,Path FROM ' . OBJECT_FILES_TABLE . ' WHERE Published > 0 ORDER BY ID');
			while($GLOBALS['DB_WE']->next_record()){
				$data[] = array(
					'id' => $GLOBALS['DB_WE']->f('ID'),
					'type' => 'object',
					'cn' => $GLOBALS['DB_WE']->f('ClassName'),
					'mt' => 0,
					'tt' => 0,
					'path' => $GLOBALS['DB_WE']->f('Path'),
					'it' => 1);
			}
		}
		$GLOBALS['DB_WE']->query('TRUNCATE ' . INDEX_TABLE);
		return $data;
	}

	/**
	 * Create and returns data Array with IDs and other information for the fragmment class for rebuilding thumbnails
	 *
	 * @return array
	 * @param string $thumbs csv value of IDs which thumbs to create
	 * @param string $thumbsFolders csv value of directory IDs => Create Thumbs for images in these directories.
	 */
	public static function getThumbnails($thumbs = '', $thumbsFolders = ''){
		if(!permissionhandler::hasPerm('REBUILD_THUMBS')){
			return array();
		}
		$data = array();
		if($thumbsFolders){
			$_foo = makeArrayFromCSV($thumbsFolders);
			$_foldersList = array();
			foreach($_foo as $folderID){
				$_foldersList[] = implode(',', we_base_file::getFoldersInFolder($folderID));
			}
			$_folders_query = '( ParentID IN(' . implode(',', $_foldersList) . ') )';
		} else {
			$_folders_query = '';
		}
		$GLOBALS['DB_WE']->query('SELECT ID,ClassName,Path,Extension FROM ' . FILE_TABLE . ' WHERE ContentType="' . we_base_ContentTypes::IMAGE . '"' . ($_folders_query ? ' AND ' . $_folders_query : '') . ' ORDER BY ID');
		while($GLOBALS['DB_WE']->next_record()){
			$data[] = array(
				'id' => $GLOBALS['DB_WE']->f('ID'),
				'type' => 'thumbnail',
				'cn' => $GLOBALS['DB_WE']->f('ClassName'),
				'thumbs' => $thumbs,
				'extension' => $GLOBALS['DB_WE']->f('Extension'),
				'mt' => 0,
				'tt' => 0,
				'path' => $GLOBALS['DB_WE']->f('Path'),
				'it' => 0);
		}
		return $data;
	}

	private static function getTemplatesOfTemplate($id, &$arr){
		$GLOBALS['DB_WE']->query('SELECT ID FROM ' . TEMPLATES_TABLE . ' WHERE MasterTemplateID=' . intval($id) . ' OR FIND_IN_SET(' . intval($id) . ',IncludedTemplates) ' . ($arr ? 'AND ID NOT IN (' . implode($arr) . ')' : ''));
		$foo = $GLOBALS['DB_WE']->getAll(true);

		if(!$foo){
			return;
		}

		$arr = array_merge($arr, $foo);
		if(in_array($id, $arr)){
			return;
		}

		foreach($foo as $check){
			self::getTemplatesOfTemplate($check, $arr);
		}
	}

	static function getTemplAndDocIDsOfTemplate($id, $staticOnly = true, $publishedOnly = false, $PublishedAndTemp = false, $useLockTbl = false){
		if(!$id){
			return 0;
		}

		$returnIDs = array(
			'templateIDs' => array(),
			'documentIDs' => array(),
		);

		self::getTemplatesOfTemplate($id, $returnIDs['templateIDs']);

		$id = intval($id);

// Bug Fix 6615
		$tmpArray = $returnIDs['templateIDs'];
		$tmpArray[] = $id;
		$tmp = implode(',', array_filter($tmpArray));
		unset($tmpArray);
		if($useLockTbl){
			$returnIDs['templateIDs'] = $GLOBALS['DB_WE']->getAllq('SELECT ID FROM ' . LOCK_TABLE . ' WHERE tbl="' . stripTblPrefix(TEMPLATES_TABLE) . '" AND ID IN (' . $tmp . ')', true);
		}
		$where = ' (' .
			($PublishedAndTemp ? 'temp_template_id IN (' . $tmp . ') OR ' : '') .
			' TemplateID IN (' . $tmp . ')' .
			')' .
			($staticOnly ? ' AND IsDynamic=0' : '') .
			($publishedOnly ? ' AND Published>0' : '');

		$GLOBALS['DB_WE']->query('SELECT f.ID FROM ' . FILE_TABLE . ' f ' . ($useLockTbl ? 'INNER JOIN ' . LOCK_TABLE . ' l ON f.ID=l.ID AND l.tbl="' . stripTblPrefix(FILE_TABLE) . '"' : '') . ' WHERE ' . $where);

		$returnIDs['documentIDs'] = $GLOBALS['DB_WE']->getAll(true);
		return $returnIDs;
	}

}
