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
 * Temporary document
 *
 * @static
 * @package none
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
	static function save($documentID, $table, $document, we_database_base $db){
		$documentID = intval($documentID);
		$db->query('UPDATE ' . TEMPORARY_DOC_TABLE . ' SET Active=0 WHERE DocumentID=' . $documentID . ' AND Active=1 AND DocTable="' . $db->escape(stripTblPrefix($table)) . '"');
		$ret = $db->query('INSERT INTO ' . TEMPORARY_DOC_TABLE . ' SET ' .
			we_database_base::arraySetter(array(
				'DocumentID' => $documentID,
				'DocumentObject' => serialize($document),
				'Active' => 1,
				'UnixTimestamp' => sql_function('UNIX_TIMESTAMP()'),
				'DocTable' => stripTblPrefix($table))));
		if($ret){
			$db->query('DELETE FROM ' . TEMPORARY_DOC_TABLE . ' WHERE DocumentID=' . $documentID . ' AND Active=0 AND DocTable="' . $db->escape(stripTblPrefix($table)) . '"');
		} else {
			//reset to current version
			$db->query('UPDATE ' . TEMPORARY_DOC_TABLE . ' SET Active=1 WHERE DocumentID=' . $documentID . ' AND Active=0 AND DocTable="' . $db->escape(stripTblPrefix($table)) . '"');
		}
		return $ret;
	}

	static function resave($documentID, $table, $document, we_database_base $db){
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
	static function load($documentID, $table, we_database_base $db){
		return f('SELECT DocumentObject FROM ' . TEMPORARY_DOC_TABLE . ' WHERE DocumentID=' . intval($documentID) . ' AND Active=1 AND DocTable="' . $db->escape(stripTblPrefix($table)) . '"', '', $db);
	}

	/**
	 * Delete document from temporary table
	 *
	 * @static
	 * @access public
	 *
	 * @param int documentID Document ID
	 */
	static function delete($documentID, $table, we_database_base $db){
		return $db->query('DELETE FROM ' . TEMPORARY_DOC_TABLE . ' WHERE DocumentID=' . intval($documentID) . ' AND  DocTable="' . $db->escape(stripTblPrefix($table)) . '"');
	}

	static function isInTempDB($id, $table, we_database_base $db){
		return (intval($id) > 0 ?
				(f('SELECT 1 FROM ' . TEMPORARY_DOC_TABLE . ' WHERE DocumentID=' . intval($id) . ' AND Active=1 AND  DocTable="' . $db->escape(stripTblPrefix($table)) . '"  LIMIT 1', '', $db)) :
				false);
	}

}
