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
function we_tag_url(array $attribs){
	if(($foo = attributFehltError($attribs, 'id', __FUNCTION__))){
		return $foo;
	}
	static $urls = array();
	$type = weTag_getAttribute('type', $attribs, 'document', we_base_request::STRING);
	$id = weTag_getAttribute('id', $attribs, 0, we_base_request::STRING);
	$triggerid = weTag_getAttribute('triggerid', $attribs, 0, we_base_request::INT);
	$hidedirindex = weTag_getAttribute('hidedirindex', $attribs, TAGLINKS_DIRECTORYINDEX_HIDE, we_base_request::BOOL);
	$objectseourls = weTag_getAttribute('objectseourls', $attribs, TAGLINKS_OBJECTSEOURLS, we_base_request::BOOL);
	
	if(is_numeric($id) && (isset($urls[$type . $id]))){ // do only work you have never done before
		return $urls[$type . $id];
	}
	
	if($id !== 'self' && $id !== 'top' && intval($id) === 0){
		$url = '/';
	} else {
		$url = '';
		switch($type){
			case 'object' :
				$objectID = ($id === 'self' || $id === 'top') ? $GLOBALS['we_obj']->ID : intval($id);
				if(($getObject = getHash('SELECT Url,TriggerID FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . intval($objectID)))){
					$triggerDocPath = $triggerid ? id_to_path($triggerid) : 
						($getObject['TriggerID'] ? id_to_path($getObject['TriggerID']) : 
							(defined('WE_REDIRECTED_SEO') ? //webEdition object uses SEO-URL
								we_objectFile::getNextDynDoc(($path = rtrim(substr(WE_REDIRECTED_SEO, 0, strripos(WE_REDIRECTED_SEO, $getObject['Url'])), '/') . DEFAULT_DYNAMIC_EXT), path_to_id(rtrim(substr(WE_REDIRECTED_SEO, 0, strripos(WE_REDIRECTED_SEO, $getObject['Url'])), '/')), $GLOBALS['WE_MAIN_DOC']->Workspaces, $GLOBALS['WE_MAIN_DOC']->ExtraWorkspacesSelected, $GLOBALS['DB_WE']) :
								parse_url(urldecode($_SERVER['REQUEST_URI']), PHP_URL_PATH)
							)
						);
											
					$path_parts = pathinfo($triggerDocPath);

					if($objectseourls && $getObject['Url'] != '' && show_SeoLinks()){
						$url = ($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/' .
							($hidedirindex && seoIndexHide($path_parts['basename']) ?
								'' : $path_parts['filename'] . '/' ) . $getObject['Url'];
					} else {
						$url = ($hidedirindex && seoIndexHide($path_parts['basename']) ?
								($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/?we_objectID=' . $objectID :
								$triggerDocPath . '?we_objectID=' . $objectID
							);
					}
				}
				break;
			case 'document':
			default:
				switch($id){
					case 'self':
					case 'top':
						$doc = we_getDocForTag($id, true);
						$docID = $doc->ID;
						break;
					default:
						$docID = $id;
						break;
				}
				
				$getDocument = getHash('SELECT Path,IsFolder FROM ' . FILE_TABLE . ' WHERE ID=' . intval($docID));
				if(isset($getDocument['Path'])){
					$path = ($getDocument['Path'] . ($getDocument['IsFolder'] ? '/' : ''));
					$path_parts = pathinfo($path);
					$url = ($hidedirindex && TAGLINKS_DIRECTORYINDEX_HIDE && seoIndexHide($path_parts['basename'])) ?
						($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/' :
						$path;
				}
				
				break;
		}
	}
	
	$urls[$type . $id] = $url;
	return $url;
}
