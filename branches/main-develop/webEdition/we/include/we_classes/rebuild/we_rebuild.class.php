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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
class we_rebuild{

	function rebuild($data, $printIt = false){
		if($printIt){
			$_newLine = count($_SERVER['argv']) ? "\n" : "<br>\n";
		}

		switch($data['type']){
			case 'navigation':
				if($printIt){
					print ('Rebuilding Navigation Item with Id: ' . $data['id']);
					flush();
				}

//				if(isset($_SESSION["prefs"]["debug_normal"]) && $_SESSION["prefs"]["debug_normal"] != 0){
					print 'Rebuilding Navigation Item with Id: ' . $data['id'];
//				}
				weNavigationCache::delCacheNavigationEntry($data['id']);
				if($data['id']){ //don't save id=0
					$nav = new weNavigation($data['id']);
					$nav->save(false, true);
				}
				if($printIt){
					print ("   done$_newLine");
					flush();
				}
				break;
			case 'thumbnail':
				$imgdoc = new we_imageDocument();
				$imgdoc->initByID($data['id']);
				if($printIt){
					print ('Rebuilding thumb for image: ' . $imgdoc->Path);
					flush();
				}

				//if(isset($_SESSION["prefs"]["debug_normal"]) && $_SESSION["prefs"]["debug_normal"] != 0){
					print "Debug: Rebuilding thumb for image: " . $imgdoc->Path;
				//}
				$imgdoc->Thumbs = $data['thumbs'] ? $data['thumbs'] : -1;
				$imgdoc->DocChanged = true;
				$imgdoc->we_save(true);
				unset($imgdoc);
				if($printIt){
					print ("   done$_newLine");
					flush();
				}
				break;
			case 'metadata':
				$imgdoc = new we_imageDocument();
				$imgdoc->initByID($data['id']);
				if($printIt){
					print ('Rebulding meta data for image: ' . $imgdoc->Path);
					flush();
				}

				//if(isset($_SESSION["prefs"]["debug_normal"]) && $_SESSION["prefs"]["debug_normal"] != 0){
					print "Rebulding meta data for image: " . $imgdoc->Path;
				//}

				$imgdoc->importMetaData($data['metaFields'], $data['onlyEmpty']);

				$imgdoc->we_save(true);
				unset($imgdoc);
				if($printIt){
					print ("   done$_newLine");
					flush();
				}
				break;
			default:
				switch($data["type"]){
					case 'document':
						if(file_exists(WE_INCLUDES_PATH.'we_classes/' . $data['cn'] . '.inc.php')){

						} else{ // it has to be an object
							return false;
						}
						$table = FILE_TABLE;
						break;
					case 'template':
						if(file_exists(WE_INCLUDES_PATH.'we_classes/' . $data['cn'] . '.inc.php')){

						} else{ // it has to be an object
							return false;
						}
						$table = TEMPLATES_TABLE;
						break;
					case 'object':
						if(file_exists(WE_INCLUDES_PATH.'we_modules/object/' . $data['cn'] . '.inc.php')){

						} else{ // it has to be an object
							return false;
						}
						$table = OBJECT_FILES_TABLE;
						break;
					default:
						return false;
				}
				$tmp = $data['cn'];
				$GLOBALS['we_doc'] = new $tmp();
				$GLOBALS['we_doc']->initByID($data['id'], $table, we_class::LOAD_MAID_DB);
				if($printIt){
					print ('Rebuilding: ' . $GLOBALS['we_doc']->Path);
					flush();
				}

				//if(isset($_SESSION["prefs"]["debug_normal"]) && $_SESSION["prefs"]["debug_normal"] != 0){
					print "Debug: Rebuilding: " . $GLOBALS['we_doc']->Path;
				//}

				/* removed 30.12.2011
				 * if($data['mt'] || $data['tt']){
				  $GLOBALS['we_doc']->correctFields();
				  } */


				if($data['tt']){
					$GLOBALS['we_doc']->we_resaveTemporaryTable();
				}
				if($data['mt'] || ($table == TEMPLATES_TABLE)){
					$tmpPath = $GLOBALS['we_doc']->constructPath();
					if($tmpPath){
						$GLOBALS['we_doc']->Path = $tmpPath;
					}

					if($table == TEMPLATES_TABLE){
						// templates has to be treated differently
						$GLOBALS['we_doc']->we_save(1);
					} else{
						$GLOBALS['we_doc']->we_resaveMainTable();
					}
				}
				if($data['it']){
					$GLOBALS['we_doc']->insertAtIndex();
				} else{
					$GLOBALS['we_doc']->we_rewrite();
					$GLOBALS['we_doc']->we_republish($data['mt']);
				}
				if($printIt){
					print ("   done$_newLine");
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
	function getDocuments($btype = 'rebuild_all', $categories = '', $catAnd = false, $doctypes = '', $folders = '', $maintable = false, $tmptable = false, $templateID = 0){
		switch($btype){
			case 'rebuild_all':
				return we_rebuild::getAllDocuments($maintable, $tmptable);
			case 'rebuild_templates':
				return we_rebuild::getTemplates();
			case 'rebuild_filter':
				return we_rebuild::getFilteredDocuments($categories, $catAnd, $doctypes, $folders, $templateID);
		}
	}

	/**
	 * Create and returns data Array with IDs and other information for the fragmment class for rebuilding all documents and templates (Called from getDocuments())
	 *
	 * @return array
	 * @param boolean $maintable if the main table should be rebuilded
	 * @param boolean $tmptable if the tmp table should be rebuilded
	 */
	function getAllDocuments($maintable, $tmptable){
		$data = array();
		if(we_hasPerm('REBUILD_ALL')){
			$GLOBALS['DB_WE']->query('SELECT ID,ClassName,Path FROM ' . TEMPLATES_TABLE . ' ORDER BY ID');
			while($GLOBALS['DB_WE']->next_record()) {
				$data[] = array(
					'id' => $GLOBALS['DB_WE']->f('ID'),
					'type' => 'template',
					'cn' => $GLOBALS['DB_WE']->f('ClassName'),
					'mt' => $maintable,
					'tt' => $tmptable,
					'path' => $GLOBALS['DB_WE']->f('Path'),
					'it' => 0);
			}
			$GLOBALS['DB_WE']->query('SELECT ID,ClassName,Path FROM ' . FILE_TABLE . ' WHERE IsFolder=1 OR Published > 0 ORDER BY ID');
			while($GLOBALS['DB_WE']->next_record()) {
				$data[] = array(
					'id' => $GLOBALS['DB_WE']->f('ID'),
					'type' => 'document',
					'cn' => $GLOBALS['DB_WE']->f('ClassName'),
					'mt' => $maintable,
					'tt' => $tmptable,
					'path' => $GLOBALS['DB_WE']->f('Path'),
					'it' => 0);
			}
			$GLOBALS['DB_WE']->query('SELECT ID,ClassName,Path FROM ' . FILE_TABLE . ' WHERE IsDynamic=0 AND Published > 0 AND ContentType="text/webedition" ORDER BY ID');
			while($GLOBALS['DB_WE']->next_record()) {
				$data[] = array(
					'id' => $GLOBALS['DB_WE']->f('ID'),
					'type' => 'document',
					'cn' => $GLOBALS['DB_WE']->f('ClassName'),
					'mt' => $maintable,
					'tt' => $tmptable,
					'path' => $GLOBALS['DB_WE']->f('Path'),
					'it' => 0);
			}
			$GLOBALS['DB_WE']->query('SELECT ID,Path FROM ' . NAVIGATION_TABLE . ' WHERE IsFolder=0 ORDER BY ID');
			while($GLOBALS['DB_WE']->next_record()) {
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
			);
		}
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
	function getMetadata($metaFields, $onlyEmpty, $metaFolders){

		if(!is_array($metaFolders)){
			$metaFolders = makeArrayFromCSV($metaFolders);
		}
		$data = array();
		if(we_hasPerm('REBUILD_META')){
			$foldersQuery = count($metaFolders) ? ' AND ParentId IN(' . implode(',', $metaFolders) . ') ' : '';
			$GLOBALS['DB_WE']->query('SELECT ID,path FROM ' . FILE_TABLE . " WHERE ContentType='image/*' AND (Extension='.jpg' OR Extension='jpeg' OR Extension='wbmp') $foldersQuery");
			while($GLOBALS['DB_WE']->next_record()) {
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

	/**
	 * Create and returns data Array with IDs and other information for the fragmment class for rebuilding all documents and templates (Called from getDocuments())
	 *
	 * @return array
	 */
	function getTemplates(){
		$data = array();
		if(we_hasPerm('REBUILD_TEMPLATES')){
			$GLOBALS['DB_WE']->query('SELECT ID,ClassName,Path FROM ' . TEMPLATES_TABLE . ' ORDER BY ID');
			while($GLOBALS['DB_WE']->next_record()) {
				$data[] = array('id' => $GLOBALS['DB_WE']->f('ID'),
					'type' => 'template',
					'cn' => $GLOBALS['DB_WE']->f('ClassName'),
					'mt' => 0,
					'tt' => 0,
					'path' => $GLOBALS['DB_WE']->f('Path'),
					'it' => 0);
			}
		}
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
	function getFilteredDocuments($categories, $catAnd, $doctypes, $folders, $templateID){
		if(!we_hasPerm('REBUILD_FILTERD')){
			return array();
		}
		$data = array();
		$_cat_query = '';
		$_doctype_query = '';
		$_folders_query = '';
		$_template_query = '';

		if($categories){
			$_foo = makeArrayFromCSV($categories);
			$tmp = array();
			foreach($_foo as $catID){
				$tmp [] = ' Category like "%,' . intval($catID) . ',%"';
			}
			$_cat_query = '(' . implode(' ' . ($catAnd ? 'AND' : 'OR') . ' ', $tmp) . ')';
		}
		if($doctypes){
			$_foo = makeArrayFromCSV($doctypes);
			$tmp = array();
			foreach($_foo as $doctypeID){
				$tmp [] = ' Doctype = "' . escape_sql_query($doctypeID) . '"';
			}
			$_doctype_query = '(' . implode(' OR ', $tmp) . ')';
		}
		if($folders){
			$_foo = makeArrayFromCSV($folders);
			$_foldersList = array();
			foreach($_foo as $folderID){
				$_foldersList[] = makeCSVFromArray(we_util::getFoldersInFolder($folderID));
			}
			$_folders_query = '( ParentID IN(' . implode(',', $_foldersList) . ') )';
		}

		if($templateID){

			$arr = getTemplAndDocIDsOfTemplate($templateID);

			if(count($arr['templateIDs'])){
				$where = array();
				foreach($arr['templateIDs'] as $tid){
					$where[] = ' ID=' . intval($tid);
				}
				$where = '(' . implode(' OR ', $where) . ')';

				$GLOBALS['DB_WE']->query('SELECT ID,ClassName,Path FROM ' . TEMPLATES_TABLE . ' WHERE ' . $where . ' ORDER BY ID');
				while($GLOBALS['DB_WE']->next_record()) {
					$data[] = array(
						'id' => $GLOBALS['DB_WE']->f('ID'),
						'type' => 'template',
						'cn' => $GLOBALS['DB_WE']->f('ClassName'),
						'mt' => 0,
						'tt' => 0,
						'path' => $GLOBALS['DB_WE']->f('Path'),
						'it' => 0);
				}

				$_template_query = array(' TemplateID=' . $templateID );
				foreach($arr['templateIDs'] as $tid){
					$_template_query[] = ' TemplateID=' . intval($tid);
				}
				// remove last OR
				$_template_query = '(' . implode(' OR ', $_template_query) . ')';
			} else{
				$_template_query = '( TemplateID=' . intval($templateID) . ')';
			}
		}

		$query = ($_cat_query ? ' AND ' . $_cat_query . ' ' : '') .
			($_doctype_query ? ' AND ' . $_doctype_query . ' ' : '') .
			($_folders_query ? ' AND ' . $_folders_query . ' ' : '') .
			($_template_query ? ' AND ' . $_template_query . ' ' : '');

		$GLOBALS['DB_WE']->query('SELECT ID,ClassName,Path FROM ' . FILE_TABLE . ' WHERE IsDynamic=0 AND Published > 0 AND ContentType="text/webedition" ' . $query . ' ORDER BY ID');
		while($GLOBALS['DB_WE']->next_record()) {
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
	function getObjects(){
		$updater = new we_updater();
		$updater->updateObjectFilesX();
		$data = array();
		if(we_hasPerm('REBUILD_OBJECTS')){
			$GLOBALS['DB_WE']->query('SELECT ID,ClassName,Path FROM ' . OBJECT_FILES_TABLE . ' WHERE Published > 0 ORDER BY ID');
			while($GLOBALS['DB_WE']->next_record()) {
				$data[] = array(
					'id' => $GLOBALS['DB_WE']->f('ID'),
					'type' => 'object',
					'cn' => $GLOBALS['DB_WE']->f('ClassName'),
					'mt' => 0,
					'tt' => 0,
					'path' => $GLOBALS['DB_WE']->f('Path'),
					'it' => 0);
			}
			$GLOBALS['DB_WE']->query('SELECT ID,ClassName,Path FROM ' . OBJECT_FILES_TABLE . ' WHERE ClassName = "we_folder" ORDER BY ID');
			while($GLOBALS['DB_WE']->next_record()) {
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
	function getNavigation(){
		$data = array();
		if(we_hasPerm('REBUILD_NAVIGATION')){
			$GLOBALS['DB_WE']->query('SELECT ID,Path FROM ' . NAVIGATION_TABLE . ' WHERE IsFolder=0 ORDER BY ID');
			while($GLOBALS['DB_WE']->next_record()) {
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

		if(isset($_REQUEST['rebuildStaticAfterNavi']) && $_REQUEST['rebuildStaticAfterNavi'] == 1){
			$data2 = we_rebuild::getFilteredDocuments('', '', '', '', '');
			$data = array_merge($data, $data2);
		}

		return $data;
	}

	/**
	 * Create and returns data Array with IDs and other information for the fragmment class for rebuilding the INDEX_TABLE
	 *
	 * @return array
	 */
	function getIndex(){
		if(!we_hasPerm('REBUILD_INDEX')){
			return array();
		}
		$data = array();
		$GLOBALS['DB_WE']->query('SELECT ID,ClassName,Path FROM ' . FILE_TABLE . ' WHERE Published > 0 AND IsSearchable=1 ORDER BY ID');
		while($GLOBALS['DB_WE']->next_record()) {
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
			while($GLOBALS['DB_WE']->next_record()) {
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
		$GLOBALS['DB_WE']->query('DELETE FROM ' . INDEX_TABLE);
		return $data;
	}

	/**
	 * Create and returns data Array with IDs and other information for the fragmment class for rebuilding thumbnails
	 *
	 * @return array
	 * @param string $thumbs csv value of IDs which thumbs to create
	 * @param string $thumbsFolders csv value of directory IDs => Create Thumbs for images in these directories.
	 */
	function getThumbnails($thumbs = '', $thumbsFolders = ''){
		if(we_hasPerm('REBUILD_THUMBS')){
			return array();
		}
		$data = array();
		if($thumbsFolders){
			$_foo = makeArrayFromCSV($thumbsFolders);
			$_foldersList = array();
			foreach($_foo as $folderID){
				$_foldersList[] = makeCSVFromArray(we_util::getFoldersInFolder($folderID));
			}
			$_folders_query = '( ParentID IN(' . implode(',', $_foldersList) . ') )';
		} else{
			$_folders_query = '';
		}
		$GLOBALS['DB_WE']->query('SELECT ID,ClassName,Path,Extension FROM ' . FILE_TABLE . ' WHERE ContentType="image/*"' . ($_folders_query ? " AND $_folders_query " : '') . ' ORDER BY ID');
		while($GLOBALS['DB_WE']->next_record()) {
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

}