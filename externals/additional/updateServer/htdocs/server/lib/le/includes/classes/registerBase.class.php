<?php

class registerBase {

	/**
	 * returns uniqueId used for updateing
	 *
	 * @param integer $pass_len
	 * @return string
	 */
	function generateUniqueId($pass_len=16) {
		/*
		global $DB_Register;

		$allchars ='abcdefghijklnmopqrstuvwxyz0123456789ABCDEFGHIJKLNMOPQRSTUVWXYZ0123456789';
		$string = '';

		for ($i = 0; $i < $pass_len; $i++) {
			$string .= $allchars{ mt_rand(0,strlen($allchars)-1) };
		}

		// check if the generated unique-id already exists
		$res =& $DB_Register->query("SELECT id FROM " . INSTALLATION_TABLE . " WHERE lifeUpdate='$string'");

		if ($res->fetchRow()) {
			return register::generateUniqueId($pass_len);
		} else {
			return $string;
		}
		*/
		register::generateUniqueId(16);
	}

}

?>