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
class we_newsletter_group extends we_newsletter_base{

	// properties start
	var $ID = 0;
	var $NewsletterID = 0;
	var $Emails = '';
	var $Extern;
	var $Customers = '';
	var $SendAll = 1;
	public $Filter = ''; //due to processVariables
	var $settings;
	// properties end

	private $aFilter = array();

	/*	 * *****************************************************
	 * Default Constructor
	 * Can load or create new Newsletter Group depends of parameter
	 * ****************************************************** */

	function __construct($groupID = 0){

		parent::__construct();
		$this->table = NEWSLETTER_GROUP_TABLE;

		array_push($this->persistents, 'NewsletterID', 'Emails', 'Extern', 'Customers', 'SendAll', 'Filter');

		$this->settings = self::getSettings();
		$this->Extern = isset($this->settings['global_mailing_list']) ? $this->settings['global_mailing_list'] : '';

		if($groupID){
			$this->ID = $groupID;
			$this->load($groupID);
		}
	}

	/*	 * **************************************
	 * load mailing list from database
	 *
	 * *************************************** */

	function load($groupID){
		parent::load($groupID);
		$this->aFilter = unserialize($this->Filter);
		return true;
	}

	/*	 * **************************************
	 * save mailing list to database
	 *
	 * *************************************** */

	public function preserveFilter(){
		$this->Filter = serialize($this->aFilter);
	}

	function save(){
		$this->preserveFilter();
		parent::save();
		return true;
	}

	public function getFilter(){
		if($this->aFilter){
			return $this->aFilter;
		} else {
			$this->aFilter = unserialize($this->Filter);
			return $this->aFilter;
		}
	}

	/*	 * **************************************
	 * delete mailing list from database
	 *
	 * *************************************** */

	function delete(){
		parent::delete();
		return true;
	}

	/*	 * **********************************
	 * check email syntax
	 *
	 * ********************************** */

	function checkEmails($group, &$malformed){

		if(defined("CUSTOMER_TABLE")){
			if(empty($this->settings["customer_email_field"])){
				t_e('empty setting for customer email field');
			} else {
				$customers = makeArrayFromCSV($this->Customers);
				foreach($customers as $customer){
					$customer_mail = f('SELECT ' . $this->settings["customer_email_field"] . ' FROM ' . CUSTOMER_TABLE . ' WHERE ID=' . intval($customer), $this->settings["customer_email_field"], $this->db);
					if(!$this->check_email($customer_mail)){
						$malformed = $customer_mail;
						return $group;
					}
				}
			}
		}

		$emails = $this->getEmailsFromList($this->Emails, 1);
		$extern = $this->getEmailsFromExtern($this->Extern, 1);
		$emails = array_merge($extern, $emails);


		foreach($emails as $email){
			if(!$this->check_email($email)){
				$malformed = $email;
				return $group;
			}
		}
		return 0;
	}

	function addFilter($name = ''){
		$this->aFilter[] = array("fieldname" => $name, "operator" => 0, "fieldvalue" => '', "logic" => '', "hours" => '', "minutes" => '');
	}

	function appendFilter(array $filter){
		$this->aFilter[] = $filter;
	}

	function delFilter(){
		array_pop($this->aFilter);
	}

	function delallFilter(){
		$this->Filter = '';
		$this->aFilter = array();
	}

	//---------------------------------- STATIC FUNCTIONS -------------------------------

	/*	 * ****************************************************
	 * return all newsletter blocks for given newsletter id
	 *
	 * ***************************************************** */
	static function __getAllGroups($newsletterID, we_database_base $db){
		$db->query('SELECT ID FROM ' . NEWSLETTER_GROUP_TABLE . ' WHERE NewsletterID=' . intval($newsletterID) . ' ORDER BY ID');
		$ret = array();
		while($db->next_record()){
			$ret[] = new self($db->f('ID'));
		}
		return $ret;
	}

	static function getSettings(){
		$db = new DB_WE();
		$db->query('SELECT pref_name,pref_value FROM ' . NEWSLETTER_PREFS_TABLE);
		return $db->getAllFirst(false);
	}

}
