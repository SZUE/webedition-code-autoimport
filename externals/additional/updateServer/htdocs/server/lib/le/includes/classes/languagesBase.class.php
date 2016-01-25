<?php

class languagesBase{

	/**
	 * returns array with all existing languages
	 *
	 * @return array
	 */
	function getExistingLanguages(){

		$query = "SELECT DISTINCT(language) AS language			FROM " . VERSION_TABLE;
		$GLOBALS['DB_WE']->query($query);
		return $GLOBALS['DB_WE']->getAll(true);
	}

}
