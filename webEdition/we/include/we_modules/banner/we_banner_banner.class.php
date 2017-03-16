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
 * General Definition of WebEdition Banner
 *
 */
class we_banner_banner extends we_banner_base{
	const PAGE_PROPERTY = 0;
	const PAGE_PLACEMENT = 1;
	const PAGE_STATISTICS = 2;

//properties
	var $ID = 0;
	var $Text;
	var $ParentID = 0;
	var $bannerID = 0;
	var $bannerUrl = '';
	var $bannerIntID = 0;
	var $maxShow = 10000;
	var $maxClicks = 1000;
	var $IsDefault = 0;
	var $clickPrice = 0;
	var $showPrice = 0;
	var $IsFolder = 0;
	var $Path = "";
	var $IntHref = 0;
	var $FileIDs;
	var $FolderIDs;
	var $CategoryIDs;
	var $DoctypeIDs;
	var $IsActive = 1;
	var $StartDate = 0;
	var $EndDate = 0;
	var $StartOk = 0;
	var $EndOk = 0;
	var $clicks = 0;
	var $views = 0;
	var $Customers = '';
	var $TagName = '';
	var $weight = 4;
	protected $MediaLinks = [];

	/**
	 * steps for WorkFlow Definition
	 * this is array of weBannerStep objects
	 */
	var $steps = [];
	// default document object
	var $documentDef;
	// documents array; format document[documentID]=document_name
	// don't create array of objects 'cos whant to save some memory
	var $documents = [];

	/**
	 * Default Constructor
	 * Can load or create new Banner Definition depends of parameter
	 */
	public function __construct($bannerID = 0, $IsFolder = 0){
		parent::__construct();
		$this->table = BANNER_TABLE;

		$this->persistents = ['ID' => we_base_request::INT,
			'Text' => we_base_request::STRING,
			'ParentID' => we_base_request::INT,
			'bannerID' => we_base_request::INT,
			'bannerUrl' => we_base_request::URL,
			'bannerIntID' => we_base_request::INT,
			'maxShow' => we_base_request::INT,
			'maxClicks' => we_base_request::INT,
			'IsDefault' => we_base_request::BOOL,
			'clickPrice' => we_base_request::FLOAT,
			'showPrice' => we_base_request::FLOAT,
			'IsFolder' => we_base_request::BOOL,
			'Path' => we_base_request::STRING,
			'IntHref' => we_base_request::URL,
			'FileIDs' => we_base_request::INTLIST,
			'FolderIDs' => we_base_request::INTLIST,
			'CategoryIDs' => we_base_request::INTLIST,
			'DoctypeIDs' => we_base_request::INTLIST,
			'StartDate' => we_base_request::INT,
			'EndDate' => we_base_request::INT,
			'StartOk' => we_base_request::BOOL,
			'EndOk' => we_base_request::BOOL,
			'IsActive' => we_base_request::BOOL,
			'clicks' => we_base_request::INT,
			'views' => we_base_request::INT,
			'Customers' => we_base_request::INTLIST,
			'TagName' => we_base_request::RAW,
			'weight' => we_base_request::INT,
			];

		$this->IsFolder = $IsFolder;
		$this->Text = g_l('modules_banner', ($this->IsFolder ? '[newbannergroup]' : '[newbanner]'));
		$this->Path = '/' . g_l('modules_banner', ($this->IsFolder ? '[newbannergroup]' : '[newbanner]'));

		if($bannerID){
			$this->ID = $bannerID;
			$this->load($bannerID);
		}
	}

	/**
	 * Load banner definition from database
	 */
	public function load($id = 0){
		if($id){
			$this->ID = $id;
		}
		if(!$this->ID){
			return false;
		}
		parent::load();
		$ppath = id_to_path($this->ParentID, BANNER_TABLE);
		$this->Path = ($ppath === '/') ? $ppath . $this->Text : $ppath . '/' . $this->Text;
		return true;
	}

	/**
	 * get all banners from database (STATIC)
	 */
	function getAllBanners(){
		//FIXME: check for e.g. group by, having, ..
		$this->db->query('SELECT ID FROM ' . $this->table . ' ORDER BY (text REGEXP "^[0-9]") DESC,abs(text),Text');

		$out = [];
		while($this->db->next_record()){
			$out[] = new we_banner_banner($this->db->f("ID"));
		}
		return $out;
	}

	/**
	 * save complete banner definition in database
	 */
	public function save(){
		$ppath = id_to_path($this->ParentID, BANNER_TABLE);
		$this->Path = ($ppath === '/') ? $ppath . $this->Text : $ppath . '/' . $this->Text;
		$retVal = parent::save();

		//if($retVal){
		$this->registerMediaLinks();
		//}
	}

	function registerMediaLinks(){// FIXME: base banner on we_ModelBase to us registerFileLink()
		$this->unregisterMediaLinks();

		if($this->bannerID){
			$this->MediaLinks[] = $this->bannerID;
		}
		if(!intval($this->IsFolder) && $this->IntHref && $this->bannerIntID){
			$this->MediaLinks[] = $this->bannerIntID;
		}

		$c = count($this->MediaLinks);
		for($i = 0; $i < $c; $i++){
			if(!$this->MediaLinks[$i] || !is_numeric($this->MediaLinks[$i])){
				unset($this->MediaLinks[$i]);
			}
		}

		// the following would be obsolete, when class was based on we_modelBase
		if(!empty($this->MediaLinks)){
			$whereType = 'AND ContentType IN ("' . we_base_ContentTypes::APPLICATION . '","' . we_base_ContentTypes::FLASH . '","' . we_base_ContentTypes::IMAGE . '","' . we_base_ContentTypes::VIDEO . '")';
			$this->db->query('SELECT ID FROM ' . FILE_TABLE . ' WHERE ID IN (' . implode(',', array_unique($this->MediaLinks)) . ') ' . $whereType);
			$this->MediaLinks = [];
			while($this->db->next_record()){
				$this->MediaLinks[] = $this->db->f('ID');
			}
		}

		foreach(array_unique($this->MediaLinks) as $remObj){
			$this->db->query('REPLACE INTO ' . FILELINK_TABLE . ' SET ' . we_database_base::arraySetter(['ID' => $this->ID,
					'DocumentTable' => stripTblPrefix($this->table),
					'type' => 'media',
					'remObj' => $remObj,
					'remTable' => stripTblPrefix(FILE_TABLE),
					'position' => 0,
					'isTemp' => 0
					]));
		}
	}

	function unregisterMediaLinks(){
		$this->db->query('DELETE FROM ' . FILELINK_TABLE . ' WHERE ID=' . intval($this->ID) . ' AND DocumentTable="' . $this->db->escape(stripTblPrefix($this->table)) . '"  AND type="media"');
	}

	/**
	 * delete banner from database
	 */
	public function delete(){
		if(!$this->ID){
			return false;
		}

		parent::delete();
		$this->db->query('DELETE FROM ' . BANNER_VIEWS_TABLE . ' WHERE ID=' . intval($this->ID));
		$this->db->query('DELETE FROM ' . BANNER_CLICKS_TABLE . ' WHERE ID=' . intval($this->ID));
		if($this->IsFolder){
			$path = (substr($this->Path, -1) === "/") ? $this->Path : $this->Path . "/";
			$this->db->query('SELECT ID FROM ' . BANNER_TABLE . ' WHERE Path LIKE "' . $this->db->escape($path) . '%"');
			$ids = [];
			while($this->db->next_record()){
				$ids[] = $this->db->f("ID");
			}
			foreach($ids as $id){
				if($id){
					$this->db->query('DELETE FROM ' . BANNER_VIEWS_TABLE . ' WHERE ID=' . intval($id));
					$this->db->query('DELETE FROM ' . BANNER_CLICKS_TABLE . ' WHERE ID=' . intval($id));
					$this->db->query('DELETE FROM ' . BANNER_TABLE . ' WHERE ID=' . intval($id));
				}
			}
		}

		return true;
	}

	static function getBannerData($did, $paths, array $dt, array $cats, $bannername, we_database_base $db){
		$parents = [];

		we_readParents($did, $parents, FILE_TABLE, 'ContentType', we_base_ContentTypes::FOLDER, $db);

		$foo = '';
		foreach($parents as $p){
			$foo .= ' FIND_IN_SET(' . intval($p) . ',FolderIDs) OR ';
		}
		$where = 'IsActive=1 AND IsFolder=0 AND ( FIND_IN_SET(' . intval($did) . ',FileIDs) OR FileIDs="" OR FileIDs="0" ) AND (' . $foo . ' FolderIDs="" OR FolderIDs="0") ';

		$foo = '';
		foreach($dt as $d){
			$foo .= ' FIND_IN_SET(' . intval($d) . ',DoctypeIDs) OR ';
		}
		$where .= ' AND (' . $foo . ' DoctypeIDs="" OR DoctypeIDs="0") ';

		$foo = '';
		foreach($cats as $c){
			$foo .= ' FIND_IN_SET(' . intval($c) . ',CategoryIDs) OR ';
		}
		$where .= ' AND (' . $foo . ' CategoryIDs="" OR CategoryIDs="0") ';

		if($paths){
			$foo = [];
			$pathsArray = makeArrayFromCsv($paths);
			foreach($pathsArray as $p){
				$foo[] = 'Path LIKE "' . $db->escape($p) . '/%" OR Path="' . $db->escape($p) . '"';
			}
			$where .= ' AND (' . implode(' OR ', $foo) . ') ';
		}

		$where .= ' AND ( (StartOk=0 OR StartDate <= UNIX_TIMESTAMP() ) AND (EndOk=0 OR EndDate > UNIX_TIMESTAMP()) ) AND (maxShow=0 OR views<maxShow) AND (maxClicks=0 OR clicks<=maxClicks) ';

		$maxweight = f('SELECT MAX(weight) FROM ' . BANNER_TABLE, '', $db);

		srand((double) microtime() * 1000000);
		$weight = rand(0, intval($maxweight));
		$anz = 0;
		while($anz == 0 && $weight <= $maxweight){
			$db->query('SELECT ID, bannerID FROM ' . BANNER_TABLE . ' WHERE ' . $where . ' AND weight<=' . $weight . ' AND (TagName="" OR TagName="' . $db->escape($bannername) . '")');
			$anz = $db->num_rows();
			if($anz == 0){
				++$weight;
			}
		}

		if($anz > 0){
			if($anz > 1){
				srand((double) microtime() * 1000000);
				$offset = rand(0, $anz - 1);
				$db->seek($offset);
			}
			if($db->next_record(MYSQL_ASSOC)){
				return $db->getRecord();
			}
		}

		return ["ID" => 0, "bannerID" => 0];
	}

	private static function getImageInfos($fileID, we_database_base $db){
		$db->query('SELECT c.Name, c.Dat FROM ' . CONTENT_TABLE . ' c WHERE c.Type="attrib" AND c.DID=' . intval($fileID));
		return $db->getAllFirst(false);
	}

	public static function getBannerCode($did, $paths, $target, $width, $height, array $dt, array $cats, $bannername, $link = true, $referer = "", $bannerclick = '/webEdition/bannerclick.php', $getbanner = "/webEdition/getBanner.php", $type = "", $page = "", $nocount = false, $xml = false){
		$db = new DB_WE();
		$bannerData = self::getBannerData($did, $paths, $dt, $cats, $bannername, $db);
		$uniq = md5(uniqid(__FUNCTION__, true));
		$showlink = true;
		$attsImage['border'] = 0;
		$attsImage['alt'] = '';

		if(($id = $bannerData['ID'])){
			if($bannerData['bannerID']){
				$bannersrc = id_to_path($bannerData['bannerID'], FILE_TABLE, $db);
				$attsImage = array_merge($attsImage, self::getImageInfos($bannerData['bannerID'], $db));
				if(isset($attsImage['longdescid'])){
					unset($attsImage['longdescid']);
				}
			} else {
				$bannersrc = $getbanner . '?' . http_build_query([($nocount ? 'nocount' : 'n') => $nocount,
						'u' => $uniq,
						'bannername' => $bannername,
						'id' => $bannerData["ID"],
						'bid' => $bannerData["bannerID"],
						'did' => $did,
						'page' => $page
						]);
			}
			$bannerlink = $bannerclick . '?' . http_build_query([($nocount ? 'nocount' : 'n') => $nocount,
					'u' => $uniq,
					'bannername' => $bannername,
					'id' => $bannerData["ID"],
					'did' => $did,
					'page' => $page
					]);
		} else {
			$id = f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="banner" AND pref_name="DefaultBannerID"', '', $db);

			$bannerID = f('SELECT bannerID FROM ' . BANNER_TABLE . ' WHERE ID=' . intval($id), '', $db);
			if($bannerID){
				$bannersrc = id_to_path($bannerID, FILE_TABLE, $db);
				$attsImage = array_merge($attsImage, self::getImageInfos($bannerID), $db);
				if(isset($attsImage['longdescid'])){
					unset($attsImage['longdescid']);
				}
			} else {
				$bannersrc = $getbanner . '?' . http_build_query([($nocount ? 'nocount' : 'n') => $nocount,
						'u' => $uniq,
						'bannername' => $bannername,
						'id' => $id,
						'bid' => $bannerID,
						'did' => $did
						]);
				$showlink = false;
			}
			$bannerlink = $bannerclick . '?' . http_build_query([($nocount ? 'nocount' : 'n') => $nocount,
					'u' => $uniq,
					'bannername' => $bannername,
					'id' => $id,
					'did' => $did,
					'page' => $page
					]);
		}
		if(!$nocount){
			$db->query('INSERT INTO ' . BANNER_VIEWS_TABLE . ' SET ' . we_database_base::arraySetter(['ID' => intval($id),
					'Timestamp' => sql_function('UNIX_TIMESTAMP()'),
					'IP' => $_SERVER['REMOTE_ADDR'],
					'Referer' => $referer ? : (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : ""),
					'DID' => intval($did),
					'Page' => $page
					]));
			$db->query('UPDATE ' . BANNER_TABLE . ' SET views=views+1 WHERE ID=' . intval($id));
		}

		$attsImage['xml'] = $xml ? 'true' : 'false';
		$attsImage['src'] = $bannersrc;

		if($width){
			$attsImage['width'] = $width;
		}
		if($height){
			$attsImage['height'] = $height;
		}
		if(isset($attsImage['type'])){
			unset($attsImage['type']);
		}
		if(isset($attsImage['filesize'])){
			unset($attsImage['filesize']);
		}
		$img = getHtmlTag('img', $attsImage);

		if($showlink){
			$linkAtts['href'] = $bannerlink;
			if($target){
				$linkAtts['target'] = $target;
			} else if($type === 'iframe'){
				$linkAtts['target'] = '_parent';
			}

			return getHtmlTag('a', $linkAtts, $img);
		}

		return $img;
	}

	public static function getBannerURL($bid){
		$h = getHash('SELECT IntHref,bannerIntID,bannerURL FROM ' . BANNER_TABLE . ' WHERE ID=' . intval($bid));
		return $h['IntHref'] ? id_to_path($h['bannerIntID'], FILE_TABLE, $GLOBALS['DB_WE']) : $h['bannerURL'];
	}

	public static function customerOwnsBanner($customerID, $bannerID, we_database_base $db){
		$res = getHash('SELECT FIND_IN_SET(' . $customerID . ',Customers) AS found,ParentID FROM ' . BANNER_TABLE . ' WHERE ID=' . intval($bannerID), $db);
		return ($res['found'] ?
				true :
				($res['ParentID'] ?
					self::customerOwnsBanner($customerID, $res["ParentID"], $db) :
					false)
			);
	}

	public static function getJSLangConsts(){
		return 'WE().consts.g_l.banner={
	view:{
		deleteStatConfirm: "' . g_l('modules_banner', '[deleteStatConfirm]') . '",
		delete_question:"' . g_l('modules_banner', '[delete_question]') . '",
		nothing_to_delete: "' . we_message_reporting::prepareMsgForJS(g_l('modules_banner', '[nothing_to_delete]')) . '",
		nothing_to_save: "' . we_message_reporting::prepareMsgForJS(g_l('modules_banner', '[nothing_to_save]')) . '",
		save_changed_banner:"' . g_l('modules_banner', '[save_changed_banner]') . '",
	}
};
';
	}

}
