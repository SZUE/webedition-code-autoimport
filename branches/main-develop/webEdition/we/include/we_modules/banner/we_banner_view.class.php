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
/* the parent class of storagable webEdition classes */

class we_banner_view extends we_modules_view{
	// settings array; format settings[setting_name]=settings_value
	var $settings = [];
	//default banner
	var $banner;
	//wat page is currentlly displed 0-properties(default);1-stat;
	var $page = 0;
	var $UseFilter = 0;
	var $FilterDate = -1;
	var $FilterDateEnd = -1;
	var $Order = "views";
	var $pageFields = [];
	var $uid;

	public function __construct($frameset){
		parent::__construct($frameset);
		$this->banner = new we_banner_banner();
		$this->page = 0;
		$this->settings = $this->getSettings();
		$this->pageFields[we_banner_banner::PAGE_PROPERTY] = ["Text", "ParentID", "bannerID", "bannerUrl", "bannerIntID", "IntHref", "IsDefault", "IsActive", "StartOk",
			"EndOk", "StartDate", "EndDate"];
		$this->pageFields[we_banner_banner::PAGE_PLACEMENT] = ["DoctypeIDs", "TagName"];
		$this->pageFields[we_banner_banner::PAGE_STATISTICS] = [];
		$this->uid = "ba_" . md5(uniqid(__FILE__, true));
	}

	function getHiddens(){
		$out = we_html_element::htmlHiddens(['home' => 0,
				'ncmd' => 'new_banner',
				'ncmdvalue' => '',
				'bid' => $this->banner->ID,
				'pnt' => we_base_request::_(we_base_request::STRING, 'pnt'),
				'page' => $this->page,
				'bname' => $this->uid,
				'order' => $this->Order,
				$this->uid . '_IsFolder' => $this->banner->IsFolder
		]);
		foreach(array_keys($this->banner->persistents) as $p){
			if(!in_array($p, $this->pageFields[$this->page])){
				$v = $this->banner->{$p};
				$out .= we_html_element::htmlHidden($this->uid . '_' . $p, $v);
			}
		}
		return $out;
	}

	public function getHomeScreen(){
		$content = we_html_button::create_button('new_banner', "javascript:top.we_cmd('new_banner');", '', 0, 0, "", "", !we_base_permission::hasPerm("NEW_BANNER")) .
			'<br/>' .
			we_html_button::create_button('new_bannergroup', "javascript:top.we_cmd('new_bannergroup');", '', 0, 0, "", "", !we_base_permission::hasPerm("NEW_BANNER"));

		return parent::getActualHomeScreen('banner', "banner.gif", $content, '<form name="we_form">' . $this->getHiddens() . '</form>');
	}

	function getProperties(){
		$weSuggest = & weSuggest::getInstance();
		$out = '
				<body class="weEditorBody" onload="loaded=1;" onunload="doUnload()">' .
			$this->getJSProperty() . '
				<form name="we_form" onsubmit="return false;">' .
			$this->getHiddens();

		$znr = -1;
		$headline = $openText = $closeText = $wepos = $itsname = '';
		switch($this->page){
			case we_banner_banner::PAGE_PROPERTY:
				$out .= we_html_element::htmlHiddens(['UseFilter' => $this->UseFilter,
						'FilterDate' => $this->FilterDate,
						'FilterDateEnd' => $this->FilterDateEnd
				]);
				$parts = [['headline' => g_l('modules_banner', '[path]'),
					'html' => $this->formPath(),
					'space' => we_html_multiIconBox::SPACE_MED
				]];
				$znr = -1;
				if(!$this->banner->IsFolder){
					$parts[] = ['headline' => g_l('modules_banner', '[banner]'),
						'html' => $this->formBanner(),
						'space' => we_html_multiIconBox::SPACE_MED
					];
					$parts[] = ['headline' => g_l('modules_banner', '[period]'),
						'html' => $this->formPeriod(),
						'space' => we_html_multiIconBox::SPACE_MED
					];
					$znr = 2;
				}
				if(defined('CUSTOMER_TABLE')){
					$parts[] = ['headline' => g_l('modules_banner', '[customers]'),
						'html' => $this->formCustomer(),
						'space' => we_html_multiIconBox::SPACE_MED
					];
				}
				$headline = g_l('tabs', '[module][properties]');
				$itsname = 'weBannerProp';
				$openText = g_l('weClass', '[moreProps]');
				$closeText = g_l('weClass', '[lessProps]');
				$wepos = weGetCookieVariable('but_weBannerProp');
				break;
			case we_banner_banner::PAGE_PLACEMENT:
				$out .= we_html_element::htmlHiddens(['UseFilter' => $this->UseFilter,
						'FilterDate' => $this->FilterDate,
						'FilterDateEnd' => $this->FilterDateEnd
				]);
				$parts = [['headline' => g_l('modules_banner', '[tagname]'),
					'html' => $this->formTagName(),
					'space' => we_html_multiIconBox::SPACE_MED
					],
						['headline' => g_l('modules_banner', '[pages]'),
						'html' => $this->formFiles(),
						'space' => we_html_multiIconBox::SPACE_MED
					],
						['headline' => g_l('modules_banner', '[dirs]'),
						'html' => $this->formFolders(),
						'space' => we_html_multiIconBox::SPACE_MED
					],
						['headline' => g_l('modules_banner', '[categories]'),
						'html' => $this->formCategories(),
						'space' => we_html_multiIconBox::SPACE_MED
					],
						['headline' => g_l('modules_banner', '[doctypes]'),
						'html' => $this->formDoctypes(),
						'space' => we_html_multiIconBox::SPACE_MED]
				];
				$headline = g_l('tabs', '[module][placement]');
				$znr = 3;
				$itsname = 'weBannerPlace';
				$openText = g_l('weClass', '[moreProps]');
				$closeText = g_l('weClass', '[lessProps]');
				$wepos = weGetCookieVariable('but_' . $itsname);
				break;
			case we_banner_banner::PAGE_STATISTICS:
				$headline = g_l('tabs', '[module][statistics]');
				$parts = [['headline' => '',
					'html' => $this->formStat(),
					]
				];
				break;
			default:
				$parts = [];
		}

		$out .= we_html_multiIconBox::getJS() .
			we_html_multiIconBox::getHTML($itsname, $parts, 30, '', $znr, $openText, $closeText, ($wepos === 'down')) .
			'</form></body></html>';

		return $out;
	}

	function previewBanner(){
		$ID = $this->banner->bannerID;
		if($ID){
			switch(f('SELECT ContentType FROM ' . FILE_TABLE . ' WHERE ID=' . intval($ID), '', $this->db)){
				case we_base_ContentTypes::IMAGE;
					$img = new we_imageDocument();
					$img->initByID($ID, FILE_TABLE);
					return $img->getHTML();
			}
		}

		return '';
	}

	function getJSTop(){
		$mod = we_base_request::_(we_base_request::STRING, 'mod', '');
		$modData = we_base_moduleInfo::getModuleData($mod);
		$title = isset($modData['text']) ? 'webEdition ' . g_l('global', '[modules]') . ' - ' . $modData['text'] : '';
		return we_html_element::jsScript(WE_JS_MODULES_DIR . 'banner/banner_top.js', "parent.document.title='" . $title . "';");
	}

	function getJSProperty(){
		return we_html_element::jsScript(WE_JS_MODULES_DIR . 'banner/banner_property.js', 'self.focus();');
	}

	private function saveBanner(we_base_jsCmd $jscmd){
		if(we_base_request::_(we_base_request::INT, "bid") !== false){
			$newone = ($this->banner->ID == 0);
			$acQuery = new we_selector_query();
			if((!we_base_permission::hasPerm("EDIT_BANNER") && !we_base_permission::hasPerm("NEW_BANNER")) ||
				($newone && !we_base_permission::hasPerm("NEW_BANNER"))){
				$jscmd->addMsg(g_l('modules_banner', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR);
				return;
			}
			if(!$this->banner->Text){
				$jscmd->addMsg(g_l('modules_banner', '[no_text]'), we_message_reporting::WE_MESSAGE_ERROR);
				return;
			}
			if(preg_match('|[%/\\\"\']|', $this->banner->Text)){
				$jscmd->addMsg(g_l('modules_banner', '[wrongtext]'), we_message_reporting::WE_MESSAGE_ERROR);
				return;
			}
			if(!$this->banner->bannerID && !$this->banner->IsFolder){
				$jscmd->addMsg(g_l('modules_banner', '[no_bannerid]'), we_message_reporting::WE_MESSAGE_ERROR);
				return;
			}
			if($this->banner->ID && ($this->banner->ID == $this->banner->ParentID)){
				$jscmd->addMsg(g_l('modules_banner', '[no_group_in_group]'), we_message_reporting::WE_MESSAGE_ERROR);
				return;
			}
			if(f('SELECT 1 FROM ' . BANNER_TABLE . ' WHERE Text="' . $this->db->escape($this->banner->Text) . '" AND ParentID=' . intval($this->banner->ParentID) .
					($newone ? '' : ' AND ID!=' . intval($this->banner->ID)), '', $this->db)){
				$jscmd->addMsg(g_l('modules_banner', '[double_name]'), we_message_reporting::WE_MESSAGE_ERROR);
				return;
			}

			if($this->banner->ParentID > 0){
				$acResult = $acQuery->getItemById($this->banner->ParentID, BANNER_TABLE, "IsFolder");
				if(!$acResult || (isset($acResult[0]['IsFolder']) && $acResult[0]['IsFolder'] == 0)){
					$jscmd->addMsg(g_l('modules_banner', '[error_ac_field]'), we_message_reporting::WE_MESSAGE_ERROR);
					return;
				}
			}
			if($this->banner->IntHref){
				$acResult = $acQuery->getItemById($this->banner->bannerIntID, FILE_TABLE, ["IsFolder"]);
				if(!$acResult || $acResult[0]['IsFolder'] == 1){
					$jscmd->addMsg(g_l('modules_banner', '[error_ac_field]'), we_message_reporting::WE_MESSAGE_ERROR);
					return;
				}
			}
			if($this->banner->bannerID > 0){
				$acResult = $acQuery->getItemById($this->banner->bannerID, FILE_TABLE, ["ContentType"]);
				if(!$acResult || $acResult[0]['ContentType'] != we_base_ContentTypes::IMAGE){
					$jscmd->addMsg(g_l('modules_banner', '[error_ac_field]'), we_message_reporting::WE_MESSAGE_ERROR);
					return;
				}
			}

			$message = "";
			$this->banner->save($message);
			if($newone){
				$jscmd->addCmd('makeTreeEntry', [
					'id' => $this->banner->ID,
					'parentid' => $this->banner->ParentID,
					'text' => $this->banner->Text,
					'open' => true,
					'contenttype' => ($this->banner->IsFolder ? we_base_ContentTypes::FOLDER : 'file'),
					'table' => "weBanner"]);
			} else {
				$jscmd->addCmd('updateTreeEntry', [
					'id' => $this->banner->ID,
					'parentid' => $this->banner->ParentID,
					'text' => $this->banner->Text,
				]);
			}
			$jscmd->addMsg(g_l('modules_banner', ($this->banner->IsFolder ? '[save_group_ok]' : '[save_ok]')), we_message_reporting::WE_MESSAGE_NOTICE);
		}
	}

	function processCommands(we_base_jsCmd $jscmd){
		switch(we_base_request::_(we_base_request::STRING, "ncmd")){
			case "delete_stat":
				$this->banner->views = 0;
				$this->banner->clicks = 0;
				$this->db->query('UPDATE ' . BANNER_TABLE . ' SET views=0,clicks=0 WHERE ID=' . intval($this->banner->ID));
				$this->db->query('DELETE FROM ' . BANNER_CLICKS_TABLE . ' WHERE ID=' . intval($this->banner->ID));
				$this->db->query('DELETE FROM ' . BANNER_VIEWS_TABLE . ' WHERE ID=' . intval($this->banner->ID));
				break;
			case "new_banner":
				$this->page = 0;
				$this->banner = new we_banner_banner();
				$jscmd->addCmd('banner_load', [$this->page, $this->banner->Path, $this->banner->IsFolder]);
				break;
			case "new_bannergroup":
				$this->page = 0;
				$this->banner = new we_banner_banner(0, 1);
				$jscmd->addCmd('banner_load', [$this->page, $this->banner->Path, $this->banner->IsFolder]);
				break;
			case "reload":
				$jscmd->addCmd('banner_load', [$this->page, $this->banner->Path, $this->banner->IsFolder]);
				break;
			case "banner_edit":
				if(($id = we_base_request::_(we_base_request::INT, "bid"))){
					$this->banner = new we_banner_banner($id);
				}
				if($this->banner->IsFolder){
					$this->page = 0;
				}
				$_REQUEST["ncmd"] = "reload";
				$this->processCommands($jscmd);

				break;
			case "add_cat":
				$arr = makeArrayFromCSV($this->banner->CategoryIDs);
				if(($ids = we_base_request::_(we_base_request::INTLISTA, "ncmdvalue", []))){
					foreach($ids as $id){
						if($id && (!in_array($id, $arr))){
							$arr[] = $id;
						}
					}
					$this->banner->CategoryIDs = implode(',', $arr);
				}
				break;
			case "del_cat":
				$arr = makeArrayFromCSV($this->banner->CategoryIDs);
				if(($id = we_base_request::_(we_base_request::INT, "ncmdvalue"))){
					foreach($arr as $k => $v){
						if($v == $id){
							unset($arr[$k]);
						}
					}
					$this->banner->CategoryIDs = implode(',', $arr);
				}
				break;
			case "del_all_cats":
				$this->banner->CategoryIDs = "";
				break;
			case "add_file":
				$arr = makeArrayFromCSV($this->banner->FileIDs);
				if(($ids = we_base_request::_(we_base_request::INTLISTA, "ncmdvalue", []))){
					foreach($ids as $id){
						if($id && (!in_array($id, $arr))){
							$arr[] = $id;
						}
					}
					$this->banner->FileIDs = implode(',', $arr);
				}
				break;
			case "del_file":
				$arr = makeArrayFromCSV($this->banner->FileIDs);
				if(($id = we_base_request::_(we_base_request::INT, "ncmdvalue")) !== false){
					if(($k = array_search($id, $arr, false)) !== false){
						unset($arr[$k]);
					}
					$this->banner->FileIDs = implode(',', $arr);
				}
				break;
			case "del_all_files":
				$this->banner->FileIDs = '';
				break;
			case "add_folder":
				$arr = makeArrayFromCSV($this->banner->FolderIDs);
				if(($ids = we_base_request::_(we_base_request::INTLISTA, "ncmdvalue", []))){
					foreach($ids as $id){
						if(strlen($id) && (!in_array($id, $arr))){
							$arr[] = $id;
						}
					}
					$this->banner->FolderIDs = implode(',', $arr);
				}
				break;
			case "add_customer":
				$arr = makeArrayFromCSV($this->banner->Customers);
				if(($ids = we_base_request::_(we_base_request::INTLISTA, "ncmdvalue", []))){
					foreach($ids as $id){
						if($id && (!in_array($id, $arr))){
							$arr[] = $id;
						}
					}
					$this->banner->Customers = implode(',', $arr);
				}
				break;
			case "del_customer":
				$arr = makeArrayFromCSV($this->banner->Customers);
				if(($id = we_base_request::_(we_base_request::INT, "ncmdvalue"))){
					foreach($arr as $k => $v){
						if($v == $id){
							unset($arr[$k]);
						}
					}
					$this->banner->Customers = implode(',', $arr);
				}
				break;
			case "del_all_customers":
				$this->banner->Customers = "";
				break;
			case "del_folder":
				$arr = makeArrayFromCSV($this->banner->FolderIDs);
				if(($id = we_base_request::_(we_base_request::INT, "ncmdvalue"))){
					foreach($arr as $k => $v){
						if($v == $id){
							unset($arr[$k]);
						}
					}
					$this->banner->FolderIDs = implode(',', $arr);
				}
				break;
			case "del_all_folders":
				$this->banner->FolderIDs = "";
				break;
			case "switchPage":
				$this->page = we_base_request::_(we_base_request::INT, "page", $this->page);
				break;
			case "save_banner":
				return $this->saveBanner($jscmd);
			case "delete_banner":
				$bid = we_base_request::_(we_base_request::INT, 'bid');
				if($bid){
					if(!we_base_permission::hasPerm("DELETE_BANNER")){
						$jscmd->addMsg(g_l('modules_banner', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR);
						return;
					}

					$this->banner = new we_banner_banner($bid);
					if($this->banner->delete()){
						$this->banner = new we_banner_banner(0, $this->banner->IsFolder);
						$jscmd->addCmd('deleteTreeEntry', [$bid, ($this->banner->IsFolder ? we_base_ContentTypes::FOLDER : 'file')]);
						$jscmd->addMsg(g_l('modules_banner', ($this->banner->IsFolder ? '[delete_group_ok]' : '[delete_ok]')), we_message_reporting::WE_MESSAGE_NOTICE);
						$jscmd->addCmd('new_banner');
					} else {
						$jscmd->addMsg(g_l('modules_banner', ($this->banner->IsFolder ? '[delete_group_nok]' : '[delete_nok]')), we_message_reporting::WE_MESSAGE_ERROR);
					}
				}
				break;
			case "reload_table":
				$this->page = 1;
				break;
			default:
		}
	}

	function processVariables(){
		$this->uid = we_base_request::_(we_base_request::STRING, "bname", $this->uid);
		$this->banner->ID = we_base_request::_(we_base_request::INT, "bid", $this->banner->ID);
		$this->page = we_base_request::_(we_base_request::INT, "page", $this->page);
		$this->Order = we_base_request::_(we_base_request::STRING, "order", $this->Order);
		$this->UseFilter = we_base_request::_(we_base_request::BOOL, "UseFilter", $this->UseFilter);
		$this->banner->Customers = we_base_request::_(we_base_request::RAW, "Customers", $this->banner->Customers);

		if(($ids = we_base_request::_(we_base_request::INT, "DoctypeIDs")) !== false){
			$this->banner->DoctypeIDs = implode(',', $ids);
		}

		foreach($this->banner->persistents as $val => $type){
			$varname = $this->uid . "_" . $val;
			if(($value = we_base_request::_($type, $varname, '_no_val')) !== '_no_val'){
				$this->banner->$val = $value;
			}
		}

		if(($day = we_base_request::_(we_base_request::INT, "dateFilter_day"))){
			$this->FilterDate = mktime(0, 0, 0, we_base_request::_(we_base_request::INT, "dateFilter_month"), $day, we_base_request::_(we_base_request::INT, "dateFilter_year"));
		} else if(($date = we_base_request::_(we_base_request::INT, "FilterDate"))){
			$this->FilterDate = $date;
		}
		if(($day = we_base_request::_(we_base_request::INT, "dateFilter2_day"))){
			$this->FilterDateEnd = mktime(0, 0, 0, we_base_request::_(we_base_request::INT, "dateFilter2_month"), $day, we_base_request::_(we_base_request::INT, "dateFilter2_year"));
		} else if(($date = we_base_request::_(we_base_request::INT, "FilterDateEnd"))){
			$this->FilterDateEnd = $date;
		}

		if(($day = we_base_request::_(we_base_request::INT, "we__From_day"))){
			$this->banner->StartDate = mktime(we_base_request::_(we_base_request::INT, "we__From_hour"), we_base_request::_(we_base_request::INT, "we__From_minute"), 0, we_base_request::_(we_base_request::INT, "we__From_month"), $day, we_base_request::_(we_base_request::INT, "we__From_year"));
			$this->banner->EndDate = mktime(we_base_request::_(we_base_request::INT, "we__To_hour"), we_base_request::_(we_base_request::INT, "we__To_minute"), 0, we_base_request::_(we_base_request::INT, "we__To_month"), we_base_request::_(we_base_request::INT, "we__To_day"), we_base_request::_(we_base_request::INT, "we__To_year"));
		}
	}

	// Static function - Settings

	function getSettings(){
		$db = new DB_WE();
		$db->query('SELECT pref_name,pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="banner"');
		return $db->getAllFirst(false);
	}

	function saveSettings($settings){
		$db = new DB_WE();
		foreach($settings as $key => $value){
			$db->query('REPLACE INTO ' . SETTINGS_TABLE . ' SET ' . we_database_base::arraySetter(['tool' => 'banner',
					'pref_name' => $key,
					'pref_value' => $value
			]));
		}
	}

	############### form functions #################

	function formTagName(){

		$tagnames = [];
		$this->db->query('SELECT c.Dat AS templateCode, l.DID AS DID FROM ' . CONTENT_TABLE . ' c JOIN ' . LINK_TABLE . ' l ON l.CID=c.ID WHERE l.DocumentTable="' . stripTblPrefix(TEMPLATES_TABLE) . '" AND c.Dat LIKE "%<we:banner %"');
		$foo = [];
		while($this->db->next_record()){
			preg_match_all('|(<we:banner [^>]+>)|U', $this->db->f('templateCode'), $foo, PREG_SET_ORDER);
			foreach($foo as $cur){
				$wholeTag = $cur[1];
				$name = preg_replace('|.+name="([^"]+)".*|i', '${1}', $wholeTag);
				if($name && (!in_array($name, $tagnames))){
					$tagnames[] = $name;
				}
			}
		}
		sort($tagnames);

		$code = '<table class="default"><tr><td class="defaultfont">' .
			we_html_tools::htmlTextInput($this->uid . "_TagName", 50, $this->banner->TagName, "", 'style="width:250px" onchange="we_cmd(\'setHot\');"') .
			'</td>
<td class="defaultfont" style="padding-left:10px;"><select style="width:240px" class="weSelect" name="' . $this->uid . '_TagName_tmp" onchange="we_cmd(\'setHot\'); this.form.elements[\'' . $this->uid . '_TagName\'].value=this.options[this.selectedIndex].value;this.selectedIndex=0">' .
			'<option value=""></option>';
		foreach($tagnames as $tagname){
			$code .= '<option value="' . $tagname . '">' . $tagname . '</option>' . "\n";
		}
		$code .= '</select></td></tr></table>';
		return $code;
	}

	function formFiles(){
		$delallbut = we_html_button::create_button(we_html_button::DELETE_ALL, "javascript:we_cmd('del_all_files')");
		$addbut = we_html_button::create_button(we_html_button::ADD, "javascript:we_cmd('we_selector_document',0,'" . FILE_TABLE . "','','','add_file','','','" . we_base_ContentTypes::WEDOCUMENT . "','',1)");

		$dirs = new we_chooser_multiDir(495, $this->banner->FileIDs, "del_file", $delallbut . $addbut, "", 'ContentType', FILE_TABLE);

		return $dirs->get();
	}

	function formFolders(){
		$delallbut = we_html_button::create_button(we_html_button::DELETE_ALL, "javascript:we_cmd('del_all_folders')");
		$addbut = we_html_button::create_button(we_html_button::ADD, "javascript:we_cmd('we_selector_directory','','" . FILE_TABLE . "','','','add_folder','','','',1)");

		$dirs = new we_chooser_multiDir(495, $this->banner->FolderIDs, "del_folder", $delallbut . $addbut, "", "ContentType", FILE_TABLE);

		return $dirs->get();
	}

	function formCategories(){
		$delallbut = we_html_button::create_button(we_html_button::DELETE_ALL, "javascript:we_cmd('del_all_cats')");
		$addbut = we_html_button::create_button(we_html_button::ADD, "javascript:we_cmd('we_selector_category',-1,'" . CATEGORY_TABLE . "','','','add_cat')");
		$cats = new we_chooser_multiDir(495, $this->banner->CategoryIDs, "del_cat", $delallbut . $addbut, "", '"we/category"', CATEGORY_TABLE);

		return $cats->get();
	}

	function formDoctypes(){
		$dt = '<select name="DoctypeIDs[]" size="10" multiple="multiple" style="width:495" onchange="we_cmd(\'setHot\');">';
		$this->db->query('SELECT dt.DocType,dt.ID FROM ' . DOC_TYPES_TABLE . ' dt ORDER BY dt.DocType');

		$doctypesArr = makeArrayFromCSV($this->banner->DoctypeIDs);
		while($this->db->next_record()){
			$dt .= '<option value="' . $this->db->f("ID") . '"' . (in_array($this->db->f("ID"), $doctypesArr) ? ' selected' : '') . '>' . $this->db->f("DocType") . '</option>';
		}
		$dt .= '</select>';

		return $dt;
	}

	function formStat($class = "middlefont"){
		$datefilterCheck = we_html_forms::checkboxWithHidden($this->UseFilter, "UseFilter", g_l('modules_banner', '[datefilter]'), false, "defaultfont", "we_cmd('switchPage','" . $this->page . "')");
		$datefilter = we_html_tools::getDateInput("dateFilter%s", ($this->FilterDate == -1 ? time() : $this->FilterDate), false, "dmy", "we_cmd('switchPage','" . $this->page . "');", $class);
		$datefilter2 = we_html_tools::getDateInput("dateFilter2%s", ($this->FilterDateEnd == -1 ? time() : $this->FilterDateEnd), false, "dmy", "we_cmd('switchPage','" . $this->page . "');", $class);

		$content = '
<table class="default" style="padding-bottom:10px;">
	<tr><td colspan="2" style="padding-bottom:5px;">' . $datefilterCheck . '</td></tr>
	<tr><td colspan="2">
	<table class="default">
	<tr>
		<td class="defaultfont">' . g_l('global', '[from]') . ':&nbsp;</td>
		<td>' . $datefilter . '</td>
		<td class="defaultfont">' . g_l('global', '[to]') . ':&nbsp;</td>
		<td>' . $datefilter2 . '</td>
	</tr>
</table></td>
	</tr>
</table>';

		$GLOBALS["lv"] = new we_listview_banner(0, 99999999, $this->Order, $this->banner->ID, $this->UseFilter, $this->FilterDate, $this->FilterDateEnd + 86399);
		$pathlink = '<a href="javascript:if(this.document.we_form.elements.order.value==\'path\'){this.document.we_form.elements.order.value=\'path desc\';}else{this.document.we_form.elements.order.value=\'path\';}we_cmd(\'switchPage\',\'' . $this->page . '\');">' . g_l('modules_banner', '[page]') . '</a>';
		$viewslink = '<a href="javascript:if(this.document.we_form.elements.order.value==\'views desc\'){this.document.we_form.elements.order.value=\'views\';}else{this.document.we_form.elements.order.value=\'views desc\';}we_cmd(\'switchPage\',\'' . $this->page . '\');">' . g_l('modules_banner', '[views]') . '</a>';
		$clickslink = '<a href="javascript:if(this.document.we_form.elements.order.value==\'clicks desc\'){this.document.we_form.elements.order.value=\'clicks\';}else{this.document.we_form.elements.order.value=\'clicks desc\';}we_cmd(\'switchPage\',\'' . $this->page . '\');">' . g_l('modules_banner', '[clicks]') . '</a>';
		$ratelink = '<a href="javascript:if(this.document.we_form.elements.order.value==\'rate desc\'){this.document.we_form.elements.order.value=\'rate\';}else{this.document.we_form.elements.order.value=\'rate desc\';}we_cmd(\'switchPage\',\'' . $this->page . '\');">' . g_l('modules_banner', '[rate]') . '</a>';
		$headline = [['dat' => $pathlink],
				['dat' => $viewslink],
				['dat' => $clickslink],
				['dat' => $ratelink]
		];
		$rows = [[['dat' => g_l('modules_banner', '[all]')],
				['dat' => $GLOBALS["lv"]->getAllviews()],
				['dat' => $GLOBALS["lv"]->getAllclicks()],
				['dat' => $GLOBALS["lv"]->getAllrate() . "%", 'style' => "text-align:right"]
			]
		];
		while($GLOBALS["lv"]->next_record()){
			$rows[] = [['dat' => ($GLOBALS["lv"]->f("page") ? '' : '<a href="' . $GLOBALS["lv"]->f(we_listview_base::PROPPREFIX . 'PATH') . '" target="_blank">') . $GLOBALS["lv"]->f(we_listview_base::PROPPREFIX . 'PATH') . ($GLOBALS["lv"]->f("page") ? '' : '</a>'),
				FILE_TABLE],
					['dat' => $GLOBALS["lv"]->f("views")],
					['dat' => $GLOBALS["lv"]->f("clicks")],
					['dat' => $GLOBALS["lv"]->f("rate") . "%", 'style' => "text-align:right"]
			];
		}

		$table = we_html_tools::htmlDialogBorder3(650, $rows, $headline, $class);
		$delbut = we_html_button::create_button(we_html_button::DELETE, "javascript:we_cmd('delete_stat')");

		return $content . $table . "<br/>" . $delbut;
	}

	function formBanner($leftsize = 120){
		return '
<table class="default">
	<tr><td>' . $this->formBannerChooser(388, $this->uid . "_bannerID", $this->banner->bannerID, g_l('modules_banner', '[imagepath]'), 'switchPage,' . $this->page) . '</td></tr>
' . ($this->banner->bannerID ?
			'<tr><td style="padding-top:10px;">' . $this->previewBanner() . '</td></tr>' : ''
			) .
			'<tr><td style="padding:10px 0px;">' . $this->formBannerHref() . '</td></tr>
	<tr><td>' . $this->formBannerNumbers() . '</td></tr>
</table>';
	}

	function formPeriod(){
		$now = time();
		$from = $this->banner->StartOk ? $this->banner->StartDate : $now;
		$to = $this->banner->EndOk ? $this->banner->EndDate : $now + 3600;

		$checkStart = we_html_forms::checkboxWithHidden($this->banner->StartOk, $this->uid . '_StartOk', g_l('modules_banner', '[from]'), false, "defaultfont", "we_cmd('setHot');");
		$checkEnd = we_html_forms::checkboxWithHidden($this->banner->EndOk, $this->uid . '_EndOk', g_l('modules_banner', '[to]'), false, "defaultfont", "we_cmd('setHot');");

		return '<table class="default">
	<tr>
		<td style="padding-right:20px;">' . $checkStart . '</td>
		<td>' . $checkEnd . '</td>
	</tr>
	<tr>
		<td>' . we_html_tools::getDateInput("we__From%s", $from, false, "", "we_cmd('setHot');") . '</td>
		<td>' . we_html_tools::getDateInput("we__To%s", $to, false, "", "we_cmd('setHot');") . '</td>
	</tr>
</table>';
	}

	function formCustomer(){
		$delallbut = we_html_button::create_button(we_html_button::DELETE_ALL, "javascript:we_cmd('del_all_customers')");
		$addbut = we_html_button::create_button(we_html_button::ADD, "javascript:we_cmd('we_customer_selector','','" . CUSTOMER_TABLE . "','','','add_customer','','','',1)");
		$obj = new we_chooser_multiDir(508, $this->banner->Customers, "setHot", $delallbut . $addbut, "", '"we/customer"', CUSTOMER_TABLE);
		return $obj->get();
	}

	function formPath($leftsize = 120){
		return '<table class="default">
	<tr><td style="padding-bottom:10px;">' . we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($this->uid . "_Text", 37, $this->banner->Text, "", 'style="width:388px" id="yuiAcInputPathName" onchange="we_cmd(\'setHot\');" onblur="parent.edheader.weTabs.setTitlePath(this.value);"'), g_l('modules_banner', '[name]')) . '</td></tr>
	<tr><td>' . $this->formDirChooser($this->banner->ParentID, $this->uid . "_ParentID", g_l('modules_banner', '[group]'), "PathGroup") . '</td></tr>
</table>';
	}

	function getHTMLParentPath(){
		$IDName = "ParentID";
		$Pathname = "ParentPath";

		return we_html_element::htmlHiddens([$IDName => 0,
				$Pathname => ""]) .
			we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_file',document.we_form.elements['" . $IDName . "'].value,'" . BANNER_TABLE . "','" . $IDName . "','" . $Pathname . "','copy_banner','','" . $rootDirID . "')");
	}

	/* creates the DocumentChoooser field with the "browse"-Button. Clicking on the Button opens the fileselector */

	private function formBannerChooser($width = "", $IDName = "bannerID", $IDValue = 0, $title = "", $cmd = ""){
		$weSuggest = & weSuggest::getInstance();
		$Pathvalue = $IDValue ? id_to_path($IDValue, FILE_TABLE, $this->db) : '';
		$Pathname = md5(uniqid(__FUNCTION__, true));

		$weSuggest->setAcId('Image');
		$weSuggest->setLabel($title);
		$weSuggest->setContentType([we_base_ContentTypes::FOLDER, we_base_ContentTypes::IMAGE, we_base_ContentTypes::APPLICATION, we_base_ContentTypes::FLASH]);
		$weSuggest->setInput($Pathname, $Pathvalue, [], true, true);
		$weSuggest->setMaxResults(10);
		$weSuggest->setResult($IDName, $IDValue);
		$weSuggest->setSelector(weSuggest::DocSelector);
		$weSuggest->setWidth($width);
		$weSuggest->setSelectButton(we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_image',((document.we_form.elements['" . $IDName . "'].value != 0) ? document.we_form.elements['" . $IDName . "'].value : ''),'" . FILE_TABLE . "','" . $IDName . "','" . $Pathname . "','" . $cmd . "','',0,'" . we_base_ContentTypes::IMAGE . "')"));

		return $weSuggest->getHTML();
	}

	private function formDirChooser($idvalue = 0, $idname = '', $title = "", $acID = ""){
		$weSuggest = & weSuggest::getInstance();
		$path = id_to_path($idvalue, BANNER_TABLE, $this->db);
		$textname = md5(uniqid(__FUNCTION__, true));

		$weSuggest->setAcId($acID);
		$weSuggest->setLabel($title);
		$weSuggest->setContentType(we_base_ContentTypes::FOLDER);
		$weSuggest->setInput($textname, $path, [], true, true);
		$weSuggest->setMaxResults(10);
		$weSuggest->setRequired(true);
		$weSuggest->setResult($idname, $idvalue);
		$weSuggest->setSelector(weSuggest::DirSelector);
		$weSuggest->setTable(BANNER_TABLE);
		//$weSuggest->setWidth($width);
		$weSuggest->setSelectButton(we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_banner_dirSelector',document.we_form.elements['" . $idname . "'].value,'" . $idname . "','" . $textname . "','setHot')"));

		return $weSuggest->getHTML();
	}

	function formBannerNumbers(){
		$cn = md5(uniqid(__FUNCTION__, true));
		$activeCheckbox = we_html_forms::checkboxWithHidden($this->banner->IsActive, $this->uid . '_IsActive', g_l('modules_banner', '[active]'), false, "defaultfont", "we_cmd('setHot');");
		$maxShow = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($this->uid . "_maxShow", 10, $this->banner->maxShow, "", 'onchange="we_cmd(\'setHot\');"', "text", 100, 0), g_l('modules_banner', '[max_show]'), "left", "defaultfont");
		$maxClicks = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($this->uid . "_maxClicks", 10, $this->banner->maxClicks, "", 'onchange="we_cmd(\'setHot\');"', "text", 100, 0), g_l('modules_banner', '[max_clicks]'), "left", "defaultfont");
		$weight = we_html_tools::htmlFormElementTable(we_html_tools::htmlSelect($this->uid . "_weight", [8 => '1 (' . g_l('modules_banner', '[infrequent]') . ")",
					7 => 2,
					6 => 3,
					5 => 4,
					4 => '5 (' . g_l('modules_banner', '[normal]') . ")",
					3 => 6,
					2 => 7,
					1 => 8,
					0 => '9 (' . g_l('modules_banner', '[frequent]') . ")"], 1, $this->banner->weight), g_l('modules_banner', '[weight]'), "left", "defaultfont");

		return '<table class="default">
	<tr>
		<td>' . $activeCheckbox . '</td>
		<td style="padding-left:40px;">' . $maxShow . '</td>
		<td style="padding-left:40px;">' . $maxClicks . '</td>
		<td style="padding-left:40px;">' . $weight . '</td>
	</tr>
</table>';
	}

	function formBannerHref(){
		$idvalue = $this->banner->bannerIntID;
		$idname = $this->uid . "_bannerIntID";

		$Pathvalue = $idvalue ? id_to_path($idvalue, FILE_TABLE, $this->db) : "";
		$Pathname = md5(uniqid(__FUNCTION__, true));

		$onkeydown = "self.document.we_form.elements['" . $this->uid . "_IntHref'][0].checked=true; WE().layout.weSuggest.checkRequired(window,'yuiAcInputInternalURL'); document.getElementById('yuiAcInputInternalURL').value=''; document.getElementById('yuiAcResultInternalURL').value=''";

		$title1 = '<table class="default">
	<tr>
		<td><input type="radio" name="' . $this->uid . '_IntHref" id="' . $this->uid . '_IntHref0" value="0"' . ($this->banner->IntHref ? "" : " checked") . ' /></td>
		<td class="defaultfont">&nbsp;<label for="' . $this->uid . '_IntHref0">' . g_l('modules_banner', '[ext_url]') . '</label></td>
	</tr>
</table>';

		$title2 = '<table class="default">
	<tr>
		<td><input type="radio" name="' . $this->uid . '_IntHref" id="' . $this->uid . '_IntHref1" value="1"' . ($this->banner->IntHref ? " checked" : "") . ' /></td>
		<td class="defaultfont">&nbsp;<label for="' . $this->uid . '_IntHref1">' . g_l('modules_banner', '[int_url]') . '</label></td>
	</tr>
</table>';

		$weSuggest = & weSuggest::getInstance();
		$weSuggest->setAcId("InternalURL");
		$weSuggest->setContentType([we_base_ContentTypes::FOLDER, we_base_ContentTypes::XML, we_base_ContentTypes::WEDOCUMENT, we_base_ContentTypes::IMAGE, we_base_ContentTypes::HTML,
			we_base_ContentTypes::APPLICATION, we_base_ContentTypes::FLASH]);
		$weSuggest->setInput($Pathname, $Pathvalue, [], true, true);
		$weSuggest->setLabel($title2);
		$weSuggest->setMaxResults(10);
		$weSuggest->setResult($idname, $idvalue);
		$weSuggest->setSelector(weSuggest::DocSelector);
		$weSuggest->setWidth(388);
		$weSuggest->setSelectButton(we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_document',document.we_form.elements['" . $idname . "'].value,'" . FILE_TABLE . "','" . $idname . "','" . $Pathname . "','selector_intHrefCallback," . $this->uid . "','',0,'')"));

		return we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($this->uid . "_bannerUrl", 30, $this->banner->bannerUrl, "", 'id="' . $this->uid . '_bannerUrl" onkeydown="' . $onkeydown . '"', "text", 388, 0), $title1, "left", "defaultfont", "", "", "", "", "", 0) . $weSuggest->getHTML();
	}

}
