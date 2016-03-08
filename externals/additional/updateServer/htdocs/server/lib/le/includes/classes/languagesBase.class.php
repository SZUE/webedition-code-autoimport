<?php

class languagesBase{

	/**
	 * returns array with all existing languages
	 *
	 * @return array
	 */
	function getExistingLanguages(){//FIXME check version
		$GLOBALS['DB_WE']->query("SELECT DISTINCT(language) AS language FROM " . SOFTWARE_LANGUAGE_TABLE);
		return $GLOBALS['DB_WE']->getAll(true);
	}

}
