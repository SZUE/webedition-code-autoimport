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
 * Definition of WebEdition Newsletter Block
 *
 */
class we_newsletter_block extends we_newsletter_base{
// Document based Newsletter Block

	const DOCUMENT = 0;
// Document field based Newsletter Block
	const DOCUMENT_FIELD = 1;
// Object based Newsletter Block
	const OBJECT = 2;
// Object field based Newsletter Block
	const OBJECT_FIELD = 3;
// File based Newsletter Block
	const FILE = 4;
//  Text based Newsletter Block
	const TEXT = 5;
//  Newsletter attachment
	const ATTACHMENT = 6;
//  URL based newsletter
	const URL = 7;

	//properties
	var $ID = 0;
	var $NewsletterID = 0;
	var $Groups = '';
	var $Type = self::DOCUMENT;
	var $LinkID = 0;
	var $Field = '';
	var $Source = '';
	var $Html = '';
	public $GroupsA = [];
	public $Pack = '';

	/*	 * *****************************************************
	 * Default Constructor
	 * Can load or create new Newsletter depends of parameter
	 * ****************************************************** */

	function __construct($newsletterID = 0){
		parent::__construct();
		$this->table = NEWSLETTER_BLOCK_TABLE;
		$this->persistents = ['NewsletterID' => we_base_request::INT,
			'Groups' => we_base_request::INTLIST,
			'Type' => we_base_request::INT,
			'LinkID' => we_base_request::INT,
			'Field' => we_base_request::STRING, //can be numeric or not, seems that paths can be transfered this way.
			'Source' => we_base_request::RAW,
			'Html' => we_base_request::RAW,
			'Pack' => we_base_request::RAW,
			];

		if($newsletterID){
			$this->ID = $newsletterID;
			$this->load($newsletterID);
			$this->GroupsA = explode(',', $this->Groups);
		}
	}

	/*	 * **************************************
	 * saves newsletter blocks in database
	 *
	 * *************************************** */

	function save(){

		$this->Groups = implode(',', array_filter(explode(',', $this->Groups)));
		parent::save();
		return true;
	}

	/*	 * **************************************
	 * deletes newsletter blocks from database
	 *
	 * *************************************** */

	function delete(){

		parent::delete();
		return true;
	}

	//---------------------------------- STATIC FUNCTIONS -------------------------------

	/*	 * ****************************************************
	 * return all newsletter blocks for given newsletter id
	 *
	 * ***************************************************** */
	static function __getAllBlocks($newsletterID, we_database_base $db){
		$db->query('SELECT ID FROM ' . NEWSLETTER_BLOCK_TABLE . ' WHERE NewsletterID=' . intval($newsletterID) . ' ORDER BY ID');
		$ret = [];
		while($db->next_record()){
			$ret[] = new we_newsletter_block($db->f("ID"));
		}
		return $ret;
	}

}
