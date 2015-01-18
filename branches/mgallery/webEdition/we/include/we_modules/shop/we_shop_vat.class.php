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
class we_shop_vat{

	var $id;
	var $text;
	var $vat;
	var $standard;

	//TODO: make getters and set private
	public $territory;
	public $country;
	public $province;
	public $textProvince;
	public $categories;
	public $textTerritory;
	public $textTerritorySortable;

	private static $predefinedNames = array(
		'exempt',
		'zero',
		'superreduced',
		'reduced',
		'standard',
		'parking'
	);

	function __construct($id = -1, $text = '', $vat = 0, $standard = false, $territory = '', $textProvince = '', $categories = ''){
		$this->id = $id;
		$this->setText($text);
		$this->vat = $vat;
		$this->standard = $standard;
		$this->territory = $territory;
		$this->textProvince = $textProvince;
		$this->categories = $categories;

		$this->country = substr($territory, 0, 2);
		$this->province = (strlen($territory) > 2 ? substr($territory, 3) : '');
		$this->textTerritory = $textProvince ?: Zend_Locale::getTranslation($this->country, 'territory', array_search($GLOBALS['WE_LANGUAGE'], getWELangs()));
		$this->textTerritorySortable = str_replace(array('Ä', 'Ö', 'Ü', 'ä', 'ö', 'ü'), array('Ae', 'Oe', 'Ue', 'ae', 'oe', 'ue'), $this->textTerritory);

	}

	public function getNaturalizedText(){
		if(!in_array($this->text, self::$predefinedNames)){
			return $this->text;
		}

		return g_l('modules_shop', '[vat][name_' . $this->text . ']');
	}

	public function setText($text){
		if(in_array($text, ($translations = self::getPredefinedNames(true)))){
			foreach($translations as $k => $v){
				if($v === $text){
					$this->text = $k;
					return;
				}
			}
		}
		$this->text = $text;
	}

	public function initById($id){
		$hash = getHash('SELECT id,text,vat,standard, territory, textProvince FROM ' . WE_SHOP_VAT_TABLE . ' WHERE id=' . intval($id));
		if($hash){
			$this->id = $hash['id'];
			$this->text = $hash['text'];
			$this->vat = $hash['vat'];
			$this->standard = ($hash['standard'] ? true : false);
			$this->territory = $hash['territory'];
			$this->textProvince = $hash['textProvince'];
			return true;
		}

		return false;
	}

	public function save(){
		$this->territory = $this->province ? $this->country . '-' . $this->province : $this->country;
		$this->textProvince = $this->textProvince ?: '';

		$set = we_database_base::arraySetter(array(
				'text' => $this->text,
				'vat' => $this->vat,
				'standard' => $this->standard,
				'territory' => $this->territory,
				'textProvince' => $this->textProvince
		));

		if($this->id == 0){ // insert a new vat
			if($GLOBALS['DB_WE']->query('INSERT INTO ' . WE_SHOP_VAT_TABLE . ' SET ' . $set)){
				return $GLOBALS['DB_WE']->getInsertId();
			}
		} else { // update existing vat
			if($GLOBALS['DB_WE']->query('UPDATE ' . WE_SHOP_VAT_TABLE . ' SET ' . $set . '	WHERE id=' . intval($this->id))){
				return $this->id;
			}
		}

		return false;
	}

	function delete(){
		return $GLOBALS['DB_WE']->query('DELETE FROM ' . WE_SHOP_VAT_TABLE . ' WHERE id=' . intval($this->id));
	}

	public static function getVatById($id){
		$vat = new self();

		return $vat->initById($id) ? $vat : false;
	}

	public static function getPredefinedNames($translated = true){
		if(!$translated){
			return self::$predefinedNames;
		} else {
			$ret = array();
			foreach(self::$predefinedNames as $name){
				$ret[$name] = g_l('modules_shop', '[vat][name_' . $name . ']');
			}

			return $ret;
		}
	}

}
