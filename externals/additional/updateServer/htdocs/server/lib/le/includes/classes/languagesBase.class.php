<?php

class languagesBase {

	/**
	 * returns array with all existing languages
	 *
	 * @return array
	 */
	function getExistingLanguages() {

		global $DB_Versioning;

		$allLanguages = array();

		$query = "
			SELECT DISTINCT(language) AS language
			FROM " . VERSION_TABLE . "
		";

		$res =& $DB_Versioning->query($query);
		while ( $row = $res->fetchRow() ) {

			$allLanguages[] = $row['language'];
		}
		return $allLanguages;
	}

}

?>