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
function orderBy($a, $b){
	static $ob = false;
	if($ob === false){
		$ob = we_base_request::_(we_base_request::STRING, 'orderBy');
	}
	return ($a[$ob] >= $b[$ob] ? !we_base_request::_(we_base_request::BOOL, 'orderDesc') : we_base_request::_(we_base_request::BOOL, 'orderDesc'));
}

abstract class we_shop_articleExtend{

	private static $typeObj;
	private static $typeDoc;
	private static $orderBy;
	private static $classid;
	private static $actPage;

	/*	 * ************ fuction for orders  ************** */

	private static function getTitleLinkObj($text, $orderKey){
		$href = WEBEDITION_DIR . 'we_showMod.php?mod=shop&pnt=edit_shop_article_extend' .
				'&typ=' . self::$typeObj .
				'&orderBy=' . $orderKey .
				'&ViewClass=' . self::$classid .
				'&actPage=' . self::$actPage .
				( ($GLOBALS['orderBy'] == $orderKey && !we_base_request::_(we_base_request::BOOL, 'orderDesc')) ? '&orderDesc=1' : '' );

		return '<a href="' . $href . '">' . $text . '</a>' . ($GLOBALS['orderBy'] == $orderKey ? ' <i class="fa fa-sort-' . (we_base_request::_(we_base_request::BOOL, 'orderDesc') ? 'desc' : 'asc') . ' fa-lg"></i>' : '<i class="fa fa-sort fa-lg"></i>');
	}

	private static function getPagerLinkObj(){

		return WEBEDITION_DIR . 'we_showMod.php?mod=shop&pnt=edit_shop_article_extend' .
				'&typ=' . self::$typeObj .
				'&orderBy=' . self::$orderBy .
				'&ViewClass=' . self::$classid .
				'&actPage=' . self::$actPage .
				(we_base_request::_(we_base_request::BOOL, 'orderdesc') ? '&orderDesc=1' : '' );
	}

	private static function getTitleLinkDoc($text, $orderKey){

		$href = WEBEDITION_DIR . 'we_showMod.php?mod=shop&pnt=edit_shop_article_extend' .
				'&typ=' . self::$typeDoc .
				'&orderBy=' . $orderKey .
				'&actPage=' . self::$actPage .
				( (self::$orderBy == $orderKey && !we_base_request::_(we_base_request::BOOL, 'orderDesc')) ? '&orderDesc=1' : '' );

		$arrow = '<i class="fa fa-sort fa-lg"></i>';

		if(self::$orderBy == $orderKey){
			$arrow = ' <i class="fa fa-lg fa-sort-' . (we_base_request::_(we_base_request::BOOL, 'orderDesc') ?
					'desc' :
					'asc') .
					'"></i>';
		}

		return '<a href="' . $href . '">' . $text . '</a>' . $arrow;
	}

	private static function getPagerLinkDoc(){
		return WEBEDITION_DIR . 'we_showMod.php?mod=shop&pnt=edit_shop_article_extend' .
				'&typ=' . self::$typeDoc .
				'&orderBy=' . self::$orderBy .
				'&actPage=' . self::$actPage .
				(we_base_request::_(we_base_request::BOOL, 'orderdesc') ? '&orderDesc=1' : '' );
	}

	private static function array_select($select_name, $label){ // function for a selectbox for the purpose of selecting a class
		$shopConfig = !empty($GLOBALS['feldnamen']) ?
				$GLOBALS['feldnamen'] :
				explode('|', f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="shop" AND pref_name="shop_pref"'));

		$fe = (isset($shopConfig[3]) ?
				array_map('intval', array_filter(explode(',', $shopConfig[3]))) : //determine more than just one class-ID
				[]);

		$selVal = we_base_request::_(we_base_request::STRING, $select_name);

		$menu = '<label for="' . $select_name . '">' . $label . '</label><select name="' . $select_name . '" onchange="document.location.href=\'' . WEBEDITION_DIR . 'we_showMod.php?mod=shop&pnt=edit_shop_article_extend&typ=object&ViewClass=\' + this.options[this.selectedIndex].value ">';

		if($fe){
			$GLOBALS['DB_WE']->query('SELECT ID,Text FROM ' . OBJECT_TABLE . ' WHERE ID IN (' . implode(',', $fe) . ')');
			foreach($GLOBALS['DB_WE']->getAllFirst(false) as $id => $val){
				$menu .= '<option value="' . $id . '"' . (($id == $selVal) ? ' selected="selected"' : '') . '>' . $val . '</option>';
			}
		}
		$menu .= '</select><input type="hidden" name="typ" value="object" />';

		return $menu;
	}

	/*	 * ************ fuction for orders  ************** */

	/*	 * ************ some config  ************** */

	public static function getDialog(){
		self::$typeObj = we_base_request::_(we_base_request::STRING, 'type', 'object');
		self::$typeDoc = we_base_request::_(we_base_request::STRING, 'type', 'document');
		$DB_WE = $GLOBALS['DB_WE'];
		self::$actPage = we_base_request::_(we_base_request::INT, 'actPage', 0);

		$feldnamen = explode('|', f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="shop" AND pref_name="shop_pref"'));
		/* $waehr = '&nbsp;' . oldHtmlspecialchars($feldnamen[0]);
		  $numberformat = $feldnamen[2];
		 */
//determine more than just one class-ID
		$fe = isset($feldnamen[3]) ? explode(',', $feldnamen[3]) : [0];

		self::$classid = $fe[0];
		$nrOfPage = (isset($feldnamen[4]) && $feldnamen[4] !== 'default') ? $feldnamen[4] : 20;
		//$varies = "variant_" . we_base_constants::WE_VARIANTS_ELEMENT_NAME;

		/*		 * ************ some config  ************** */
		$parts = [];

		/*		 * ************ some initialisation  ************** */
		/* $mwst = (!empty($feldnamen[1])) ? (($feldnamen[1] / 100) + 1) : "";
		  $da = ( $GLOBALS["WE_LANGUAGE"] === "Deutsch" ) ? "%d.%m.%y" : "%m/%d/%y";
		  $dateform = ( $GLOBALS["WE_LANGUAGE"] === "Deutsch" ) ? "00.00.00" : "00/00/00";
		  $datereg = ( $GLOBALS["WE_LANGUAGE"] === "Deutsch" ) ? "/\d\d\.\d\d\.\d\d/" : "/\d\d\\/\d\d\\/\d\d/";
		 */
		if(!isset($_REQUEST['sort'])){
			$_REQUEST['sort'] = "";
		}

		switch(we_base_request::_(we_base_request::STRING, 'typ')){
			case 'object': //start output object
				self::$orderBy = $DB_WE->escape(we_base_request::_(we_base_request::STRING, 'orderBy', 'obTitle'));
				$entries = 0;
				$count_expression = $from_expression = $where_expression = [];
				if(!empty($fe)){
					self::$classid = intval(self::$classid);
					if(self::$classid){
						continue;
					}
					$count_expression[] = 'COUNT(obx.OF_ID)';
					$from_expression[] = OBJECT_X_TABLE . self::$classid . ' obx';
					$where_expression[] = 'obx.OF_ID!=0';
				} else {
					foreach($fe as $clId){
						$clId = intval($clId);
						if(!$clId){
							continue;
						}
						$count_expression[] = 'COUNT(DISTINCT ob' . $clId . '.OF_ID)';
						$from_expression[] = OBJECT_X_TABLE . $clId . ' ob' . $clId;
						$where_expression[] = 'ob' . $clId . '.OF_ID!=0';
					}
				}
				$DB_WE->query('SELECT ' . implode('+', $count_expression) . ' FROM ' . implode(',', $from_expression) . ' WHERE ' . implode(' AND ', $where_expression));
				$entries += array_sum($DB_WE->getAll(true)); // Pager: determine the number of records;
				$active_page = we_base_request::_(we_base_request::RAW, 'page', 0); // Pager: determine the current page
				$docType2 = we_base_ContentTypes::OBJECT_FILE; // Pager: determine the current page
				$typeAlias = we_base_ContentTypes::OBJECT; // Pager: determine the current page
				$classSelectTable = (isset($classSelectTable) ? $classSelectTable : '');
				if($entries){ // Pager: Number of records not empty?
					$topInfo = ($entries > 0 ? $entries : g_l('modules_shop', '[noRecord]'));
					$self::$classid = abs(we_base_request::_(we_base_request::INT, 'ViewClass')); // gets the value from the selectbox;

					$classSelectTable .= '<table style="width:600px;">
    <tr>
        <td colspan="2" class="defaultfont">' .
							// displays a selectbox for the purpose of selecting a class..
							self::array_select('ViewClass', g_l('modules_shop', '[classSel]')) . '</td>
    </tr>
</table>';
					$parts[] = ['html' => $classSelectTable,
					];

					// :: then do the query for objects
					$DB_WE->query('SELECT obx.input_' . WE_SHOP_TITLE_FIELD_NAME . ' AS obTitle,obx.OF_ID AS obID,of.CreationDate AS cDate,of.Published AS cPub,of.ModDate AS cMob
FROM ' . OBJECT_X_TABLE . self::$classid . ' obx JOIN ' . OBJECT_FILES_TABLE . ' of ON obx.OF_ID=of.ID
WHERE of.IsFolder=0
ORDER BY obx.OF_ID'); // get the shop-objects from DB;
					// build the table
					$orderRows = $DB_WE->getAll();
					// we need functionalitty to order these
					if(we_base_request::_(we_base_request::BOOL, 'orderBy')){
						usort($orderRows, 'orderBy');
					}

					// build the headline
					$headline = [
						['dat' => self::getTitleLinkObj(g_l('modules_shop', '[ArtName]'), 'obTitle')],
						['dat' => self::getTitleLinkObj(g_l('modules_shop', '[ArtID]'), 'obID')],
						['dat' => self::getTitleLinkObj(g_l('modules_shop', '[artCreate]'), 'cDate')],
						['dat' => self::getTitleLinkObj(g_l('modules_shop', '[artPub]'), 'cPub')],
						['dat' => self::getTitleLinkObj(g_l('modules_shop', '[artMod]'), 'cMob')],
					];

					$content = [];

					for($nr = 0, $i = (self::$actPage * $nrOfPage); $i < count($orderRows) && $i < (self::$actPage * $nrOfPage + $nrOfPage); $i++, $nr++){
						$isPublished = $orderRows[$i]['cPub'] > 0 ? true : false;
						$publishedStylePre = $isPublished ? '<span>' : '<span style="color: red">';

						$content[] = [
							['dat' => '<a href="javascript:WE().layout.weEditorFrameController.openDocument(\'' . OBJECT_FILES_TABLE . '\' ,\'' . $orderRows[$i]['obID'] . '\',\'' . $docType2 . '\');");">' . $publishedStylePre . substr($orderRows[$i]['obTitle'], 0, 25) . '..</span></a>'],
							['dat' => $publishedStylePre . $orderRows[$i]['obID'] . '</span>'],
							//$content[$nr][2]['dat'] = $orderRows[$i]['type'];
							['dat' => $publishedStylePre . ($orderRows[$i]['cDate'] > 0 ? date('d.m.Y - H:m:s', $orderRows[$i]['cDate']) : "") . '</span>'],
							['dat' => $orderRows[$i]['cPub'] > 0 ? date('d.m.Y - H:m:s', $orderRows[$i]['cPub']) : ''],
							['dat' => $publishedStylePre . ($orderRows[$i]['cMob'] > 0 ? date('d.m.Y - H:m:s', $orderRows[$i]['cMob']) : "") . '</span>']
						];
					}

					$parts[] = [
						'html' => we_html_tools::htmlDialogBorder3(670, $content, $headline),
						'noline' => true
					];

					// now the pager class at last:
					// Pager: Zweite Linkliste zeigen
					$parts[] = ['html' => we_shop_pager::getStandardPagerHTML(self::getPagerLinkObj(), self::$actPage, $nrOfPage, count($orderRows)),
					];
				}
				break;
			case 'document': //start output doc
				self::$orderBy = we_base_request::_(we_base_request::RAW, 'orderBy', 'sql');
				$entries = f('SELECT COUNT(Name) FROM ' . CONTENT_TABLE . ' WHERE nHash=x\'' . md5(WE_SHOP_TITLE_FIELD_NAME) . '\''); // Pager: determine the number of records;
				$active_page = we_base_request::_(we_base_request::RAW, 'page', 0); // Pager: determine the number of records;
				$docType = we_base_ContentTypes::WEDOCUMENT; // Pager: determine the current page
				$typeAlias = isset($typeAlias) ? "document" : "document"; // Pager: determine the current page

				if($entries){ // Pager: Number of records not empty?
					$topInfo = ($entries ?: g_l('modules_shop', '[noRecord]'));
					// :: then do the query for documents
					$DB_WE->query('SELECT c.dat AS `sql`,c.DID AS dd,f.CreationDate AS dDate,f.Published AS dPub,f.ModDate AS dMod FROM ' . CONTENT_TABLE . ' c JOIN ' . FILE_TABLE . ' f ON f.ID=c.DID WHERE c.DocumentTable="' . stripTblPrefix(FILE_TABLE) . '" AND c.nHash=x\'' . md5(WE_SHOP_TITLE_FIELD_NAME) . '\' ORDER BY dd'); // get the shop-documents from DB;
					// for the articlelist, we need also all these article, so save them in array
					$orderRows = $DB_WE->getAll();
					// we need functionalitty to order these
					if(we_base_request::_(we_base_request::BOOL, 'orderBy')){
						usort($orderRows, 'orderBy');
					}
					$typeAlias = "document";
					// build the headline
					$headline = [
						['dat' => self::getTitleLinkDoc(g_l('modules_shop', '[ArtName]'), 'sql')],
						['dat' => self::getTitleLinkDoc(g_l('modules_shop', '[ArtID]'), 'dd')],
						//$headline[2]['dat'] = self::getTitleLinkDoc(g_l('modules_shop','[docType]'), $typeAlias);
						['dat' => self::getTitleLinkDoc(g_l('modules_shop', '[artCreate]'), 'dDate')],
						['dat' => self::getTitleLinkDoc(g_l('modules_shop', '[artPub]'), 'dPub')],
						['dat' => self::getTitleLinkDoc(g_l('modules_shop', '[artMod]'), 'dMod')],
					];

					$content = [];
					for($nr = 0, $i = (self::$actPage * $nrOfPage); $i < count($orderRows) && $i < (self::$actPage * $nrOfPage + $nrOfPage); $i++, $nr++){
						$isPublished = $orderRows[$i]['dPub'] > 0 ? true : false;
						$publishedStylePre = $isPublished ? '<span>' : '<span style="color: red">';
						$content[$nr] = [
							['dat' => '<a href="javascript:WE().layout.weEditorFrameController.openDocument(\'' . FILE_TABLE . '\' ,\'' . $orderRows[$i]['dd'] . '\',\'' . $docType . '\');");">' . $publishedStylePre . substr($orderRows[$i]['sql'], 0, 25) . '..</span></a>'],
							['dat' => $publishedStylePre . ($orderRows[$i]['dd']) . '</span>'],
							//$content[$nr][2]['dat'] = $orderRows[$i]['type'];
							['dat' => $publishedStylePre . ($orderRows[$i]['dDate'] > 0 ? date('d.m.Y - H:m:s', $orderRows[$i]['dDate']) : '') . '</span>'],
							['dat' => $orderRows[$i]['dPub'] > 0 ? date("d.m.Y - H:m:s", $orderRows[$i]['dPub']) : ""],
							['dat' => $publishedStylePre . ($orderRows[$i]['dMod'] > 0 ? date('d.m.Y - H:m:s', $orderRows[$i]['dMod']) : '') . '</span>'],
						];
					}
					$parts[] = ['html' => we_html_tools::htmlDialogBorder3(670, $content, $headline),
						'noline' => true
					];

					$parts[] = ['html' => we_shop_pager::getStandardPagerHTML(self::getPagerLinkDoc(), self::$actPage, $nrOfPage, count($orderRows)),
					];
				}

				/*				 * ******** END PROCESS THE OUTPUT IF OPTED FOR A DOCUMENT *********** */
				break;
			default:
				return 'Die von Ihnen gewünschte Seite kann nicht angezeigt werden!';
		}


		if(!$parts){
			$parts = [['html' => '<table style="width:100%">' .
			'<tr><td class="defaultfont">' . g_l('modules_shop', '[noRecordAlert]') . '</td></tr>' .
			'<tr><td class="defaultfont">' . we_html_button::create_button('fa:btn_shop_pref,fa-lg fa-pencil,fa-lg fa-list-alt', "javascript:top.opener.top.we_cmd('pref_shop')", '', 0, 0, "", "", !we_base_permission::hasPerm("NEW_USER")) . '</td></tr>' .
			'</table>',
				]
			];
			$topInfo = g_l('modules_shop', '[noRecord]');
		}

		return we_html_tools::getHtmlTop('', '', '', '', '
<body class="weEditorBody" onload="self.focus();top.content.editor.edheader.weTabs.setFrameSize();" onresize="top.content.editor.edheader.weTabs.setFrameSize();" onunload="">
<form>' .
						we_html_multiIconBox::getHTML("revenues", $parts, 30, "", -1, "", "", false, sprintf(g_l('tabs', '[module][artList]'), $topInfo)) . '
		</form></body>
</html>');
	}

}
