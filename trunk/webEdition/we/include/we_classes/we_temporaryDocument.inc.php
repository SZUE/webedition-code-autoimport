<?php

/**
 * webEdition CMS
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
/**
 * include connection with webEdition
 */
include_once($_SERVER["DOCUMENT_ROOT"] . "/webEdition/we/include/we.inc.php");



/**
 * name of table in database where will be stored all temporary documents
 *
 *  sturcture of the table :
 *
 * CREATE TABLE TEMPRARY_DOC_TABLE (
 *   ID bigint(20) NOT NULL auto_increment,
 *   DocumentID bigint(20) NOT NULL default '0',
 *   DocumentObject longtext NOT NULL,
 *   Table varchar(64) NOT NULL,
 *   UnixTimestamp bigint(20) NOT NULL default '0',
 *   Active tinyint(1) NOT NULL default '0',
 *   PRIMARY KEY  (ID)
 * ) ENGINE=MyISAM;
 *
 */

/**
 * Temporary document
 *
 * all functions on this class is static, and please use it in static form :
 *    we_temporaryDocument::function_name();
 *
 *
 * @static
 * @package WebEdition.Classes
 * @version 1.1
 * @date 06.07.2002
 */
abstract class we_temporaryDocument{

	/**
	 * Save document in temporary table
	 *
	 * @static
	 * @access public
	 *
	 * @param int documentID ID for document which will be stored in database
	 * @param object mixed document object
	 */
	static function save($documentID, $table="", $document="", $db=""){
		$table = ($table ? $table : FILE_TABLE);

		$db = $db ? $db : new DB_WE();

		$docSer = addslashes(serialize($document));
		$documentID = intval($documentID);
		$db->query('UPDATE ' . TEMPORARY_DOC_TABLE . ' SET Active=0 WHERE DocumentID=' . $documentID . ' AND Active=1 AND  DocTable="' . $db->escape(stripTblPrefix($table)) . '"');
		$ret = $db->query('INSERT INTO ' . TEMPORARY_DOC_TABLE . ' SET DocumentID=' . $documentID . ',DocumentObject="' . $docSer . '",Active=1,UnixTimestamp=UNTIX_TIMESTAMP(),DocTable="' . $db->escape(stripTblPrefix($table)) . '"');
		if($ret){
			$db->query('DELETE FROM ' . TEMPORARY_DOC_TABLE . ' WHERE DocumentID=' . $documentID . ' AND Active=0 AND  DocTable="' . $db->escape(stripTblPrefix($table)) . '"');
		} else{
			//reset to current version
			$db->query('UPDATE ' . TEMPORARY_DOC_TABLE . ' SET Active=1 WHERE DocumentID=' . $documentID . ' AND Active=0 AND  DocTable="' . $db->escape(stripTblPrefix($table)) . '"');
		}
		return $ret;
	}

	static function resave($documentID, $table="", $document="", $db=""){
		$table = ($table ? $table : FILE_TABLE);

		$db = $db ? $db : new DB_WE();
		$docSer = $db->escape(serialize($document));
		return $db->query('UPDATE ' . TEMPORARY_DOC_TABLE . ' SET DocumentObject="' . $docSer . '",UnixTimestamp=UNIX_TIMESTAMP() WHERE DocumentID=' . intval($documentID) . ' AND Active=1 AND  DocTable="' . $db->escape(stripTblPrefix($table)) . '"');
	}

	/**
	 * Load document from temporary table
	 *
	 * @static
	 * @access public
	 *
	 * @param int documentID Document ID
	 * @return object mixed document object. if return value is flase, document doesn't exists in temporary table
	 */
	static function load($documentID, $table="", $db=""){
		$table = ($table ? $table : FILE_TABLE);
		$db = $db ? $db : new DB_WE();

		return f('SELECT DocumentObject FROM ' . TEMPORARY_DOC_TABLE . ' WHERE DocumentID=' . intval($documentID) . ' AND Active=1 AND  DocTable="' . $db->escape(stripTblPrefix($table)) . '"', 'DocumentObject', $db);
	}

	/**
	 * Delete document from temporary table
	 *
	 * @static
	 * @access public
	 *
	 * @param int documentID Document ID
	 */
	static function delete($documentID, $table='', $db=''){
		$table = ($table ? $table : FILE_TABLE);
		$db = $db ? $db : new DB_WE();
		return $db->query('DELETE FROM ' . TEMPORARY_DOC_TABLE . ' WHERE DocumentID=' . intval($documentID) . ' AND  DocTable="' . $db->escape(stripTblPrefix($table)) . '"');
	}

	static function isInTempDB($id, $table="", $db=""){
		$table = ($table ? $table : FILE_TABLE);

		if(isset($id)){
			$db = $db ? $db : new DB_WE();
			$ret = f('SELECT 1 AS a FROM ' . TEMPORARY_DOC_TABLE . ' WHERE DocumentID=' . intval($id) . ' AND Active=1 AND  DocTable="' . $db->escape(stripTblPrefix($table)) . '"', 'a', $db);
			return ($ret == '1');
		} else{
			return false;
		}
	}

}