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
 * @abstract class for reading and writing metadata from/to media files (i.e. audio, video or image files)
 * 			The implementations are to be found in its subclasses (i.e. 'weMetaData_IPTC')
 */
class we_metadata_metaData{
	const ALL_BUT_STANDARD_FIELDS = 0;
	const ONLY_STANDARD_FIELDS = 1;
	const ALL_FIELDS = 2;
	const STANDARD_FIELDS = 'Title,Description,Keywords';

	/**
	 * @var array specifies possible access methods to metadata handled by this implementation class (i.e. exif: readonly)
	 */
	protected $accesstypes = array('read,write');

	/**
	 * @var array mapping of datatypes and their metadata models
	 */
	private $dataTypeMapping = array(
		'jpe' => array('Exif', 'IPTC'), // iptc support is currently broken, will be fixed later
		'jpg' => array('Exif', 'IPTC'),
		'jpeg' => array('Exif', 'IPTC'),
		'wbmp' => array('Exif'),
		'pdf' => array('PDF'),
	);
	private $imageTypeMap = array(
		'', // image type 0 not defined
		'gif', // IMAGETYPE_GIF
		'jpg', // IMAGETYPE_JPEG
		'png', // IMAGETYPE_PNG
		'swf', // IMAGETYPE_SWF
		'psd', // IMAGETYPE_PSD
		'bmp', // IMAGETYPE_BMP
		'tif', // IMAGETYPE_TIFF_II intel-Bytefolge
		'tif', // IMAGETYPE_TIFF_MM motorola-Bytefolge
		'jpc', // IMAGETYPE_JPC
		'jp2', // IMAGETYPE_JP2
		'jpx', // IMAGETYPE_JPX
		'jb2', // IMAGETYPE_JB2
		'swc', // IMAGETYPE_SWC
		'iff', // IMAGETYPE_IFF
		'wbmp', // IMAGETYPE_WBMP
		'xbm', // IMAGETYPE_XBM
	);

	/**
	 * @var string name and path of the file for read/write operations
	 */
	var $datasource = '';

	/**
	 * @var array access permissions to datasource (read and/or write). Other permissions can
	 * 			be implemented by subclasses (i.e. 'modify')
	 */
	var $datasourcePerms = array();

	/**
	 * @var string filetype of the file that has to be read/written (i.e. 'jpg')
	 */
	var $filetype = array();

	/**
	 * @var string array of metadata types that have to be read/written from/to the file
	 */
	var $datatype = array();

	/**
	 * @var array containing all metadata fetched from datasource
	 */
	var $metadata = array();

	/**
	 * @var array of objects instance of the implementation class for reading/writing metadata
	 * 			has to be an array because multiple metadata readers/writers can be specified for a fileformat.
	 */
	var $_instance = array();

	/**
	 * @var bool flag for validity checks within these classes
	 */
	var $_valid = true;

	/**
	 * @abstract constructor for PHP4
	 * @param string filetype filetype of the file whose metadata has to be read  (i.e. 'mp3')
	 * @return bool returns false if no spezialisation for the given filetype is available
	 */
	public function __construct($source = ''){
		if(empty($source)){
			$this->_valid = false;
			return false;
		}

		if($this->_setDatasource($source)){
			if($this->_setDatatype()){
				foreach($this->datatype as $_type){
					$this->_getInstance($_type);
				}
			}
		}
	}

	/*
	 * public methods for usage from outside
	 * their only purpose is to redirect calls to protected methods that can be overridden by subclasses
	 * to implement other behaviours so that all calls are being transparently redirected to an appropriate
	 * implementation class
	 */

	/**
	 * @abstract method for identifying all valid implementations for a given file.
	 * @return array of all valid metadata types or false if there are none
	 */
	function getImplementations(){
		return (!$this->_valid || !$this->datatype ?
						false :
						$this->datatype);
	}

	function getMetaData($selection = ''){
		if(!$this->_valid){
			return false;
		}
		foreach($this->datatype as $_type){
			if(!in_array('read', $this->_instance[$_type]->accesstypes)){
				return false;
			}
			$this->metadata[strToLower($_type)] = $this->_instance[$_type]->_getMetaData();
		}
		return $this->metadata;
	}

	function setMetaData($data = '', $datatype = ''){
		foreach($this->datatype as $_type){
			if(!$this->_instance[$_type]->_valid){
				return false;
			}
			if(!in_array('write', $this->_instance[$_type]->accesstypes)){
				return false;
			}
			$this->_instance[$_type]->_setMetaData($data = '');
		}
		return true;
	}

	/**
	 * @abstract saves fetched metadata to database, currently in table tblContent
	 * @return bool false if fails, else true
	 */
	function saveToDatabase($id = ''){
		if(!$this->_valid){
			return false;
		}
		// table name: CONTENT_TABLE
		// currently all metadata is saved via we_root::setElement()
		return true;
	}

	/*
	 * protected methods, only for internal usage:
	 */

	/**
	 * @abstract method for setting the datasource that has to be used
	 * 			takes a webEdition document id and fetches document path and name from database (tblFile).
	 * 			this method has to be overridden if the used datasource is not a webEdition Document with
	 * 			tblFile database entry
	 * 			sets $this->datasourcePerms according to read/write rights
	 * @param string datasource id of webEdition document
	 * @return bool returns false if datasource is not valid
	 */
	protected function _setDatasource($datasource = ''){
		// determines if given datasource is valid. will be assignet to instances later:
		if(!$this->_valid){
			return false;
		}
		if(empty($datasource)){
			$this->_valid = false;
			return false;
		}
		if(is_numeric($datasource)){
			// TODO: get path to file from database (tblFile)
			$datasource = $this->_getDatasourceFromDatabase($datasource);
		} else if(is_file($datasource)){
			$this->_valid = true;
			if(is_readable($datasource)){
				$this->datasourcePerms[] = 'read';
			} else {
				$this->_valid = false;
			}
			if(is_writable($datasource)){
				$this->datasourcePerms[] = 'write';
			}
		} else {
			// check if it is a temporary file (i.e. an uploaded image that has not been saved yet):
			if(!is_readable(TEMP_PATH, $datasource)){
				$this->_valid = false;
				return false;
			}
		}
		$this->datasource = $datasource;
		return true;
	}

	/**
	 * @abstract internal (private) function for obtaining path/name of the media file from database (tblFile)
	 */
	protected function _getDatasourceFromDatabase(){
		$this->_valid = false;
		return false;
	}

	/**
	 * @abstract method for detecting type of current file needed, for identifying correct metadata implementation class
	 */
	protected function _setDatatype(){
		/*
		 * detecting filetype in this order:
		 * 1. exif_imagetype()
		 * 2. file extension
		 */
		if(!$this->_valid){
			return false;
		}
		$_filetype = (is_callable('exif_imagetype') ? @exif_imagetype($this->datasource) : '');
		// if $_filetype is a numeric value, filetype should first be identified by
		// Get fype for image-type returned by getimagesize, exif_read_data, exif_thumbnail, exif_imagetype
		if(!empty($_filetype) && is_numeric($_filetype)){
			if(isset($this->imageTypeMap[$_filetype]) && !empty($this->imageTypeMap[$_filetype])){
				$this->filetype = $this->imageTypeMap[$_filetype];
			} else {
				$this->_valid = false;
				$_filetype = '';
			}
		}
		// if first check fails try to identify file extension:
		if(empty($_filetype)){
			// try to identify type of file by its extension by checking substring after last point in $this->datasource
			$_extension = strrchr($this->datasource, '.');
			if($_extension && $_extension != '.'){
				$this->filetype = substr($_extension, 1);
			} else {
				$this->_valid = false;
				return false;
			}
		}

		if(array_key_exists(strtolower($this->filetype), $this->dataTypeMapping)){
			$this->datatype = $this->dataTypeMapping[strtolower($this->filetype)];
			$this->_valid = true;
			return true;
		}
		$this->_valid = false;
		return false;
	}

	/**
	 * @return object instance of the metadata implementation class
	 * @return bool returns false if no or invalid datatype specified
	 */
	protected function _getInstance($value = ''){
		if(!$this->_valid){
			return false;
		}
		$className = 'we_metadata_' . $value;
		if(class_exists($className, true)){
			$this->_instance[$value] = new $className($this->filetype);
			if(!$this->_instance[$value]->_checkDependencies()){
				$this->_instance[$value]->_valid = false;
			} else {
				$this->_instance[$value]->_valid = true;
				$this->_instance[$value]->datasource = $this->datasource;
			}
			return true;
		}
		$this->_instance[$value]->_valid = false;
		return false;
	}

	/**
	 * @abstract public method for fetching metadata from datasource
	 * @param mixed selection empty or "all" returns all metadata values available in given Datasource,
	 * 			a selection is specified as an array of metadata tags/fields
	 * @return array metadata according to $selection
	 */
	protected function _getMetaData($selection = ''){
		// override!
		return $this->metadata;
	}

	/**
	 * @abstract public method for fetching metadata from datasource
	 * @param mixed selection empty or "all" returns all metadata values available in given Datasource,
	 * 			a selection is specified as an array of metadata tags/fields
	 * @return array metadata according to $selection
	 */
	protected function _setMetaData($data = '', $datatype = ''){
		return true;
		// override!
	}

	/**
	 * @abstract checks if all dependencies of this class are met
	 * 			(i.e. if needed libraries, php extensions or classes are available)
	 * @return bool returns true if all dependencies are met and false if not
	 */
	protected function _checkDependencies(){
		// override!
		return true;
	}

	public static function getDefinedMetaDataFields($filter = self::ALL_BUT_STANDARD_FIELDS, $assoc = false){
		static $fields = false;

		if($fields === false){
			$fields = $GLOBALS['DB_WE']->getAllFirstq('SELECT * FROM ' . METADATA_TABLE . ' ORDER BY tag', true, MYSQL_ASSOC);
			foreach($fields as $k => $v){
				$fields[$k]['tag'] = $k;
			}
		}

		switch($filter){
			case self::ONLY_STANDARD_FIELDS:
				$ret = array_filter($fields, function($v){return in_array($v['tag'], explode(',', self::STANDARD_FIELDS));});
				break;
			case self::ALL_FIELDS:
				$ret = $fields;
				break;
			case self::ALL_BUT_STANDARD_FIELDS:
				$ret = array_filter($fields, function($v){return !in_array($v['tag'], explode(',', self::STANDARD_FIELDS));});
		}

		return $assoc ? $ret : array_values($ret);
	}

	public static function getMetaDataField($field = ''){
		$fields = self::getDefinedMetaDataFields(self::ALL_FIELDS, true);

		return isset($fields[$field]) ? $fields[$field] : array();
	}

	public static function getDefinedMetaValues($getAssoc = false, $leading = false, $field = '', $getDelete = false, $getDeleteLast = false){
		// defined_values change more often than defined_fields, so we do not cache them!
		$_defined_values = array();
		$GLOBALS['DB_WE']->query('SELECT * FROM ' . METAVALUES_TABLE . ($field ? ' WHERE tag = "' . $GLOBALS['DB_WE']->escape($field) . '"' : '') . ' ORDER BY value');
		$isDel = false;

		while($GLOBALS['DB_WE']->next_record()){
			if(!isset($_defined_values[$GLOBALS['DB_WE']->f('tag')])){
				if($leading){
					$_defined_values[$GLOBALS['DB_WE']->f('tag')][] = $leading;
				}
				if($getAssoc){
					if($getDelete){
						$_defined_values[$GLOBALS['DB_WE']->f('tag')]['__del__'] = '-- ' . g_l('metadata', '[deleteEntry]') . ' --';
						$isDel = true;
					}
					if($getDeleteLast){
						$_defined_values[$GLOBALS['DB_WE']->f('tag')]['__del_last__'] = '-- ' . g_l('metadata', '[deleteLastEntry]') . ' --';
						$isDel = true;
					}
					if($isDel){
						$_defined_values[$GLOBALS['DB_WE']->f('tag')]['__empty__'] = '';
					}
				}
			}
			if($getAssoc){
				$_defined_values[$GLOBALS['DB_WE']->f('tag')][$GLOBALS['DB_WE']->f('value')] = $GLOBALS['DB_WE']->f('value');
			} else {
				$_defined_values[$GLOBALS['DB_WE']->f('tag')][] = $GLOBALS['DB_WE']->f('value');
			}
		}

		return $field ? (isset($_defined_values[$field]) ? $_defined_values[$field] : array()) : $_defined_values;
	}

}
