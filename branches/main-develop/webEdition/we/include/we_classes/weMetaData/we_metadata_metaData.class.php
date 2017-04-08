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
	protected $accesstypes = ['read,write'];

	/**
	 * @var array mapping of datatypes and their metadata models
	 */
	private $dataTypeMapping = ['jpe' => ['Exif', 'IPTC'], // iptc support is currently broken, will be fixed later
		'jpg' => ['Exif', 'IPTC'],
		'jpeg' => ['Exif', 'IPTC'],
		'wbmp' => ['Exif'],
		'pdf' => ['PDF'],
	];
	private $imageTypeMap = ['', // image type 0 not defined
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
	];

	/**
	 * @var string name and path of the file for read/write operations
	 */
	var $datasource = '';

	/**
	 * @var array access permissions to datasource (read and/or write). Other permissions can
	 * 			be implemented by subclasses (i.e. 'modify')
	 */
	var $datasourcePerms = [];

	/**
	 * @var string filetype of the file that has to be read/written (i.e. 'jpg')
	 */
	var $filetype = [];

	/**
	 * @var string array of metadata types that have to be read/written from/to the file
	 */
	var $datatype = [];

	/**
	 * @var array containing all metadata fetched from datasource
	 */
	var $metadata = [];

	/**
	 * @var array of objects instance of the implementation class for reading/writing metadata
	 * 			has to be an array because multiple metadata readers/writers can be specified for a fileformat.
	 */
	protected $instance = [];

	/**
	 * @var bool flag for validity checks within these classes
	 */
	public $valid = true;

	/**
	 * @abstract constructor for PHP4
	 * @param string filetype filetype of the file whose metadata has to be read  (i.e. 'mp3')
	 * @return bool returns false if no spezialisation for the given filetype is available
	 */
	public function __construct($source = ''){
		if(empty($source)){
			$this->valid = false;
			return false;
		}

		if($this->setDatasource($source)){
			if($this->setDatatype()){
				foreach($this->datatype as $type){
					$this->getInstance($type);
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
		return (!$this->valid || !$this->datatype ?
			false :
			$this->datatype);
	}

	function getMetaData($selection = ''){
		if(!$this->valid){
			return false;
		}
		foreach($this->datatype as $type){
			if(!in_array('read', $this->instance[$type]->accesstypes)){
				return false;
			}
			$this->metadata[strToLower($type)] = $this->instance[$type]->getInstMetaData();
		}
		return $this->metadata;
	}

	function setMetaData($data = '', $datatype = ''){
		foreach($this->datatype as $type){
			if(!$this->instance[$type]->valid){
				return false;
			}
			if(!in_array('write', $this->instance[$type]->accesstypes)){
				return false;
			}
			$this->instance[$type]->setInstMetaData($data = '');
		}
		return true;
	}

	/**
	 * @abstract saves fetched metadata to database, currently in table tblContent
	 * @return bool false if fails, else true
	 */
	function saveToDatabase($id = ''){
		if(!$this->valid){
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
	protected function setDatasource($datasource = ''){
		// determines if given datasource is valid. will be assignet to instances later:
		if(!$this->valid){
			return false;
		}
		if(empty($datasource)){
			$this->valid = false;
			return false;
		}
		if(is_numeric($datasource)){
			// TODO: get path to file from database (tblFile)
			$datasource = $this->getDatasourceFromDatabase($datasource);
		} else if(is_file($datasource)){
			$this->valid = true;
			if(is_readable($datasource)){
				$this->datasourcePerms[] = 'read';
			} else {
				$this->valid = false;
			}
			if(is_writable($datasource)){
				$this->datasourcePerms[] = 'write';
			}
		} else {
			// check if it is a temporary file (i.e. an uploaded image that has not been saved yet):
			if(!is_readable(TEMP_PATH, $datasource)){
				$this->valid = false;
				return false;
			}
		}
		$this->datasource = $datasource;
		return true;
	}

	/**
	 * @abstract internal (private) function for obtaining path/name of the media file from database (tblFile)
	 */
	protected function getDatasourceFromDatabase(){
		$this->valid = false;
		return false;
	}

	/**
	 * @abstract method for detecting type of current file needed, for identifying correct metadata implementation class
	 */
	protected function setDatatype(){
		/*
		 * detecting filetype in this order:
		 * 1. exif_imagetype()
		 * 2. file extension
		 */
		if(!$this->valid){
			return false;
		}
		$filetype = (is_callable('exif_imagetype') ? @exif_imagetype($this->datasource) : '');
		// if $filetype is a numeric value, filetype should first be identified by
		// Get fype for image-type returned by getimagesize, exif_read_data, exif_thumbnail, exif_imagetype
		if(!empty($filetype) && is_numeric($filetype)){
			if(isset($this->imageTypeMap[$filetype]) && !empty($this->imageTypeMap[$filetype])){
				$this->filetype = $this->imageTypeMap[$filetype];
			} else {
				$this->valid = false;
				$filetype = '';
			}
		}
		// if first check fails try to identify file extension:
		if(empty($filetype)){
			// try to identify type of file by its extension by checking substring after last point in $this->datasource
			$extension = strrchr($this->datasource, '.');
			if($extension && $extension != '.'){
				$this->filetype = substr($extension, 1);
			} else {
				$this->valid = false;
				return false;
			}
		}

		if(array_key_exists(strtolower($this->filetype), $this->dataTypeMapping)){
			$this->datatype = $this->dataTypeMapping[strtolower($this->filetype)];
			$this->valid = true;
			return true;
		}
		$this->valid = false;
		return false;
	}

	/**
	 * @return object instance of the metadata implementation class
	 * @return bool returns false if no or invalid datatype specified
	 */
	protected function getInstance($value = ''){
		if(!$this->valid){
			return false;
		}
		$className = 'we_metadata_' . $value;
		if(class_exists($className, true)){
			$this->instance[$value] = new $className($this->filetype);
			if(!$this->instance[$value]->checkDependencies()){
				$this->instance[$value]->valid = false;
			} else {
				$this->instance[$value]->valid = true;
				$this->instance[$value]->datasource = $this->datasource;
			}
			return true;
		}
		$this->instance[$value]->valid = false;
		return false;
	}

	/**
	 * @abstract public method for fetching metadata from datasource
	 * @param mixed selection empty or "all" returns all metadata values available in given Datasource,
	 * 			a selection is specified as an array of metadata tags/fields
	 * @return array metadata according to $selection
	 */
	protected function getInstMetaData($selection = ''){
		// override!
		return $this->metadata;
	}

	/**
	 * @abstract public method for fetching metadata from datasource
	 * @param mixed selection empty or "all" returns all metadata values available in given Datasource,
	 * 			a selection is specified as an array of metadata tags/fields
	 * @return array metadata according to $selection
	 */
	protected function setInstMetaData($data = '', $datatype = ''){
		return true;
		// override!
	}

	/**
	 * @abstract checks if all dependencies of this class are met
	 * 			(i.e. if needed libraries, php extensions or classes are available)
	 * @return bool returns true if all dependencies are met and false if not
	 */
	protected function checkDependencies(){
		// override!
		return true;
	}

	public static function getDefinedMetaDataFields($filter = we_metadata_metaData::ALL_BUT_STANDARD_FIELDS, $assoc = false){
		static $fields = false;

		if($fields === false){
			$fields = $GLOBALS['DB_WE']->getAllFirstq('SELECT * FROM ' . METADATA_TABLE . ' ORDER BY tag', true, MYSQL_ASSOC);
			foreach($fields as $k => $v){
				$fields[$k]['tag'] = $k;
			}
		}

		switch($filter){
			case we_metadata_metaData::ONLY_STANDARD_FIELDS:
				$ret = array_filter($fields, function($v){
					return in_array($v['tag'], explode(',', we_metadata_metaData::STANDARD_FIELDS));
				});
				break;
			case we_metadata_metaData::ALL_FIELDS:
				$ret = $fields;
				break;
			case we_metadata_metaData::ALL_BUT_STANDARD_FIELDS:
				$ret = array_filter($fields, function($v){
					return !in_array($v['tag'], explode(',', we_metadata_metaData::STANDARD_FIELDS));
				});
		}

		return $assoc ? $ret : array_values($ret);
	}

	public static function getMetaDataField($field = ''){
		$fields = self::getDefinedMetaDataFields(self::ALL_FIELDS, true);

		return isset($fields[$field]) ? $fields[$field] : [];
	}

	public static function getDefinedMetaValues($getAssoc = false, $leading = false, $field = '', $getDelete = false, $getDeleteLast = false){
		// defined_values change more often than defined_fields, so we do not cache them!
		$defined_values = [];
		$GLOBALS['DB_WE']->query('SELECT * FROM ' . METAVALUES_TABLE . ($field ? ' WHERE tag = "' . $GLOBALS['DB_WE']->escape($field) . '"' : '') . ' ORDER BY value');
		$isDel = false;

		while($GLOBALS['DB_WE']->next_record()){
			if(!isset($defined_values[$GLOBALS['DB_WE']->f('tag')])){
				if($leading){
					$defined_values[$GLOBALS['DB_WE']->f('tag')][] = $leading;
				}
				if($getAssoc){
					if($getDelete){
						$defined_values[$GLOBALS['DB_WE']->f('tag')]['__del__'] = '-- ' . g_l('metadata', '[deleteEntry]') . ' --';
						$isDel = true;
					}
					if($getDeleteLast){
						$defined_values[$GLOBALS['DB_WE']->f('tag')]['__del_last__'] = '-- ' . g_l('metadata', '[deleteLastEntry]') . ' --';
						$isDel = true;
					}
					if($isDel){
						$defined_values[$GLOBALS['DB_WE']->f('tag')]['__empty__'] = '';
					}
				}
			}
			if($getAssoc){
				$defined_values[$GLOBALS['DB_WE']->f('tag')][$GLOBALS['DB_WE']->f('value')] = $GLOBALS['DB_WE']->f('value');
			} else {
				$defined_values[$GLOBALS['DB_WE']->f('tag')][] = $GLOBALS['DB_WE']->f('value');
			}
		}

		return $field ? (isset($defined_values[$field]) ? $defined_values[$field] : []) : $defined_values;
	}

	public static function getJSLangConsts(){
		return 'WE().consts.g_l.metadatafields=JSON.parse("' . we_base_util::setLangString([
				'error_meta_field_empty_msg' => (g_l('metadata', '[error_meta_field_empty_msg]')),
				'fields' => (g_l('metadata', '[fields]')),
				'import_from' => (g_l('metadata', '[import_from]')),
				'meta_field_wrong_chars_messsage' => (g_l('metadata', '[meta_field_wrong_chars_messsage]')),
				'meta_field_wrong_name_messsage' => (g_l('metadata', '[meta_field_wrong_name_messsage]')),
				'proposals' => g_l('metadata', '[proposals]'),
				'tagname' => g_l('metadata', '[tagname]'),
				'type' => g_l('metadata', '[type]'),
				]) . '");';
	}

}
