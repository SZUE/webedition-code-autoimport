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

class rpcGetRssCmd extends we_rpc_cmd{

	function execute(){
		//close session, we don't need it anymore
		session_write_close();
		$sRssUri = we_base_request::_(we_base_request::URL, 'we_cmd', '', 0);
		$sCfgBinary = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1); //note binary content
		$bCfgTitle = (bool) $sCfgBinary{0};
		$bCfgLink = (bool) $sCfgBinary{1};
		$bCfgDesc = (bool) $sCfgBinary{2};
		$bCfgContEnc = (bool) $sCfgBinary{3};
		$bCfgPubDate = (bool) $sCfgBinary{4};
		$bCfgCategory = (bool) $sCfgBinary{5};
		$iNumItems = we_base_request::_(we_base_request::INT, 'we_cmd', '', 2);
		switch($iNumItems){
			case 11:
				$iNumItems = 15;
				break;
			case 12:
				$iNumItems = 20;
				break;
			case 13:
				$iNumItems = 25;
				break;
			case 14:
				$iNumItems = 50;
				break;
		}
		$sTbBinary = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 3); //binary
		$bTbLabel = (bool) $sTbBinary{0};
		$bTbTitel = (bool) $sTbBinary{1};
		$bTbDesc = (bool) $sTbBinary{2};
		$bTbLink = (bool) $sTbBinary{3};
		$bTbPubDate = (bool) $sTbBinary{4};
		$bTbCopyright = (bool) $sTbBinary{5};

		//Bug 6119: Keine Unterstützung für curl in der XML_RSS Klasse
		//daher Umstellung den Inhalt des Feeds selbst zu holen
		$parsedurl = parse_url($sRssUri);
		$http_request = new we_http_request($parsedurl['path'], $parsedurl['host'], 'GET');
		$http_request->executeHttpRequest();
		$http_response = new we_http_response($http_request->getHttpResponseStr());
		while(isset($http_response->http_headers['Location'])){//eine Weiterleitung ist aktiv
			$parsedurl = parse_url($http_response->http_headers['Location']);
			$http_request = new we_http_request($parsedurl['path'], $parsedurl['host'], 'GET');
			$http_request->executeHttpRequest();
			$http_response = new we_http_response($http_request->getHttpResponseStr());
		}
		$feeddata = $http_response->http_body;

		$oRssParser = new we_xml_rss($feeddata, null, $GLOBALS['WE_BACKENDCHARSET']); // Umstellung in der XML_RSS-Klasse: den string, und nicht die url weiterzugeben
		$tmp = $oRssParser->parse();
		$sRssOut = "";

		$iCurrItem = 0;
		foreach($oRssParser->getItems() as $item){
			$bShowTitle = ($bCfgTitle && isset($item['title']));
			$bShowLink = ($bCfgLink && isset($item['link']));
			$bShowDesc = ($bCfgDesc && isset($item['description']));
			$bShowContEnc = ($bCfgContEnc && isset($item['content:encoded']));
			$bShowPubdate = ($bCfgPubDate && isset($item['pubdate']));
			$bShowCategory = ($bCfgCategory && isset($item['category']));

			$sLink = (($bCfgLink && isset($item['link'])) && !$bShowTitle) ? " &nbsp;" .
				we_html_element::htmlA(["href" => $item['link'], "target" => "_blank", "style" => "text-decoration:underline;"], g_l('cockpit', '[more]')) : "";
			if($bShowContEnc){
				$contEnc = new we_html_table(["class" => 'default'], 1, 1);
				$contEnc->setCol(0, 0, null, $item['content:encoded'] . ((!$bCfgDesc) ? $sLink : ""));
			}

			$sRssOut .= ($bShowTitle ?
					($bShowLink ? we_html_element::htmlA(["href" => $item['link'], "target" => "_blank"], we_html_element::htmlB($item['title'])) :
						we_html_element::htmlB($item['title'])) .
					($bShowPubdate ? ' ' : we_html_element::htmlBr()) :
					'') .
				($bShowPubdate ?
					g_l('cockpit', '[published]') . ': ' . date(g_l('date', '[format][default]'), strtotime($item['pubdate'])) :
					'') .
				($bShowCategory ?
					($bShowPubdate ? we_html_element::htmlBr() : "") .
					g_l('cockpit', '[category]') . ": " . $item['category'] :
					'') .
				($bShowPubdate || $bShowCategory ?
					we_html_element::htmlBr() :
					'') .
				($bShowDesc ?
					$item['description'] . $sLink . we_html_element::htmlBr() :
					"") .
				($bShowContEnc ?
					$contEnc->getHTML() :
					(!$bShowDesc ?
						$sLink . we_html_element::htmlBr() :
						'')
				) .
				($bShowDesc || $bShowContEnc ?
					we_html_element::htmlBr() :
					"");
			if($iNumItems){
				$iCurrItem++;
				if($iCurrItem == $iNumItems){
					break;
				}
			}
		}

		$aTb = [];
		if($bTbLabel){
			$aTb[] = g_l('cockpit', '[rss_feed]');
		}
		if($bTbTitel){
			$aTb[] = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 4) ?  :
				((isset($oRssParser->channel["title"])) ? $oRssParser->channel["title"] : "");
		}
		if($bTbDesc){
			$aTb[] = (isset($oRssParser->channel["description"])) ? str_replace(["\n", "\r"], '', $oRssParser->channel["description"]) : '';
		}
		if($bTbLink){
			$aTb[] = (isset($oRssParser->channel["link"])) ? $oRssParser->channel["link"] : '';
		}
		if($bTbPubDate){
			$aTb[] = (isset($oRssParser->channel["pubdate"])) ? (date(g_l('date', '[format][default]'), strtotime($oRssParser->channel["pubdate"]))) : "";
		}
		if($bTbCopyright){
			$aTb[] = (isset($oRssParser->channel["copyright"])) ? $oRssParser->channel["copyright"] : "";
		}
		$resp = new we_rpc_response();
		$resp->setData('data', $sRssOut);

		// title
		$title = implode(' - ', $aTb);
		if(strlen($title) > 50){
			$title = substr($title, 0, 50) . '&hellip;';
		}
		$resp->setData('titel', $title);
		$resp->setData('widgetType', "rss");
		$resp->setData('widgetId', we_base_request::_(we_base_request::STRING, 'we_cmd', 0, 5));

		return $resp;
	}

}
