<?php

class languagesBase{

	/**
	 * returns array with all existing languages
	 *
	 * @return array
	 */
	static function getExistingLanguages($skipUTF = false){
		$GLOBALS['DB_WE']->query('SELECT DISTINCT(language) AS language FROM ' . SOFTWARE_LANGUAGE_TABLE . ($skipUTF ? "WHERE language NOT LIKE '%_UTF-8%'" : ''));
		return $GLOBALS['DB_WE']->getAll(true);
	}

}
