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
define('NO_SESS', 1);
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
if(!defined('BANNER_TABLE')){
	//do nothing if deactivated
	return;
}

$id = we_base_request::_(we_base_request::INT, 'id', 0);
$bid = we_base_request::_(we_base_request::INT, 'bid', 0);
$did = we_base_request::_(we_base_request::INT, 'did', 0);
$paths = we_base_request::_(we_base_request::WEFILELIST, 'paths', '');
$target = we_base_request::_(we_base_request::STRING, 'target', '');
$height = we_base_request::_(we_base_request::INT, 'height', 0);
$width = we_base_request::_(we_base_request::INT, 'width', 0);
$bannerclick = we_base_request::_(we_base_request::URL, 'bannerclick', WEBEDITION_DIR . 'bannerclick.php');
$referer = we_base_request::_(we_base_request::RAW, 'referer', '');
$type = we_base_request::_(we_base_request::STRING, 'type', '');
$cats = we_base_request::_(we_base_request::INTLISTA, 'cats', '');
$dt = we_base_request::_(we_base_request::INTLISTA, 'dt', '');
$link = we_base_request::_(we_base_request::BOOL, 'link', 1);
$bannername = we_base_request::_(we_base_request::STRING, 'bannername', '');
$page = we_base_request::_(we_base_request::STRING, 'page', '');
$nocount = we_base_request::_(we_base_request::BOOL, 'nocount');
$xml = we_base_request::_(we_base_request::BOOL, 'xml');
$c = we_base_request::_(we_base_request::BOOL, 'c', 0);

if($type && $type != 'pixel'){
	$code = we_banner_banner::getBannerCode($did, $paths, $target, $width, $height, $dt, $cats, $bannername, $link, $referer, $bannerclick, WEBEDITION_DIR . basename(__FILE__), $type, $page, $nocount, $xml);
}
switch($type){
	case 'js':
		$jsarr = explode("\n", strtr($code, ["\r" => "\n", "'" => "\\'"]));
		header("Content-type: application/x-javascript");

		foreach($jsarr as $line){
			echo "document.writeln('" . $line . "');";
		}
		break;
	case 'iframe':
		echo $code;
		break;
	default:

		if(!$id){
			$bannerData = we_banner_banner::getBannerData($did, $paths, $dt, $cats, $bannername, $GLOBALS['DB_WE']);
			$id = $bannerData['ID'];
			$bid = $bannerData['bannerID'];
		}
		if(!$bid){
			$id = f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="banner" AND pref_name="DefaultBannerID"');
			$bid = f('SELECT bannerID FROM ' . BANNER_TABLE . ' WHERE ID=' . intval($id));
		}

		$bannerpath = f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . intval($bid));

		if(($type === 'pixel' || (!$nocount) && $id && $c)){
			$GLOBALS['DB_WE']->query('INSERT INTO ' . BANNER_VIEWS_TABLE . ' SET ' . we_database_base::arraySetter([
					'ID' => intval($id),
					'Timestamp' => sql_function('UNIX_TIMESTAMP()'),
					'IP' => $_SERVER['REMOTE_ADDR'],
					'Referer' => ($referer ? : (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '')),
					'DID' => intval($did),
					'Page' => $page]));

			$GLOBALS['DB_WE']->query('UPDATE ' . BANNER_TABLE . ' SET views=views+1 WHERE ID=' . intval($id));
			setcookie("webid_$bannername", intval($id));
		}

		if($bannerpath){
			if(!$c){
				header("Location: $bannerpath");
				exit();
			}
			$ext = preg_replace('/.*\.(.+)$/', '${1}', $bannerpath);
			switch($ext){
				case "jpg":
				case "jpeg":
					$contenttype = "image/jpeg";
					break;
				case "png":
					$contenttype = "image/png";
					break;
				default:
					$contenttype = "image/gif";
			}


			header("Cache-Control: no-cache");
			header("Pragma: no-cache");
			header("Expires: -1");
			header("Content-disposition: filename=" . basename($bannerpath));
			header("Content-Type: $contenttype");

			readfile($_SERVER['DOCUMENT_ROOT'] . $bannerpath);
		} else {
			header("Content-type: image/gif");
			echo chr(0x47) . chr(0x49) . chr(0x46) . chr(0x38) . chr(0x39) . chr(0x61) . chr(0x01) . chr(0x00) .
			chr(0x01) . chr(0x00) . chr(0x80) . chr(0x00) . chr(0x00) . chr(0x04) . chr(0x02) . chr(0x04) .
			chr(0x00) . chr(0x00) . chr(0x00) . chr(0x21) . chr(0xF9) . chr(0x04) . chr(0x01) . chr(0x00) .
			chr(0x00) . chr(0x00) . chr(0x00) . chr(0x2C) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) .
			chr(0x01) . chr(0x00) . chr(0x01) . chr(0x00) . chr(0x00) . chr(0x02) . chr(0x02) . chr(0x44) .
			chr(0x01) . chr(0x00) . chr(0x3B);
		}
}