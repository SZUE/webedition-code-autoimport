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
 * Class which contains all functions for the
 * rebuild dialog and the rebuild function
 * @static
 */
abstract class we_versions_wizard{
	const DELETE_VERSIONS = 'delete_versions';
	const RESET_VERSIONS = 'reset_versions';

	/**
	 * returns HTML for the Body Frame
	 *
	 * @return string
	 */
	static function getBody(){
		$step = 'getStep' . we_base_request::_(we_base_request::INT, "step", "0");
		return self::getPage(self::$step());
	}

	/**
	 * returns HTML for the Frame with the progress bar
	 *
	 * @return string
	 */
	static function getBusy(){
		$dc = we_base_request::_(we_base_request::BOOL, "dc");

		$WE_PB = new we_progressBar(0, ($dc ? 490 : 200));
		$WE_PB->addText("", 0, "pb1");
		$pb = $WE_PB->getHTML();

		$cancelButton = we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close();");
		$refreshButton = we_html_button::create_button(we_html_button::REFRESH, "javascript:parent.wizcmd.location.reload();", '', 0, 0, "", "", false, false);

		$nextbutdisabled = !(permissionhandler::hasPerm(["REBUILD_ALL", "REBUILD_FILTERD", "REBUILD_OBJECTS", "REBUILD_INDEX", "REBUILD_THUMBS", "REBUILD_META"]));

		if($dc){
			$buttons = $refreshButton . $cancelButton;
			$pb = we_html_tools::htmlDialogLayout($pb, g_l('rebuild', '[rebuild]'), $buttons);
		} else {
			$prevButton = we_html_button::create_button(we_html_button::BACK, "javascript:parent.wizbody.handle_event('previous');", '', 0, 0, "", "", true, false);
			$nextButton = we_html_button::create_button(we_html_button::NEXT, "javascript:parent.wizbody.handle_event('next');", '', 0, 0, "", "", $nextbutdisabled, false);

			$content2 = we_html_element::htmlSpan(["id" => "prev", 'style' => "padding-left:10px;text-align:right"], $prevButton) .
				we_html_element::htmlSpan(["id" => "next", 'style' => "padding-left:10px;text-align:right"], $nextButton) .
				we_html_element::htmlSpan(["id" => "refresh", 'style' => "display:none; padding-left:10px;text-align:right"], $refreshButton) .
				we_html_element::htmlSpan(["id" => "cancel", 'style' => "padding-left:10px;text-align:right"], $cancelButton);

			$content = new we_html_table(['class' => 'default', "width" => "100%"], 1, 2);
			$content->setCol(0, 0, ["id" => "progr", 'style' => "display:none;text-align:left"], $pb);
			$content->setCol(0, 1, ['style' => "text-align:right"], $content2);
		}

		return we_html_tools::getHtmlTop('', '', '', we_progressBar::getJSCode() .
				we_html_element::jsScript(JS_DIR . 'nextButtons.js'), we_html_element::htmlBody(
					['class' => ($dc ? "weDialogBody" : "weDialogButtonsBody"),					], ($dc ? $pb : $content->getHtml()))
		);
	}

	/**
	 * returns HTML for the Cmd Frame
	 *
	 * @return string for now it is an empty page
	 */
	static function getCmd(){
		return self::getPage(['', '']);
	}

	/**
	 * returns the HTML for the First Step (0) of the wizard
	 *
	 * @return string
	 */
	static function getStep0(){
		$version = new we_versions_version();
		$type = we_base_request::_(we_base_request::STRING, "type", self::DELETE_VERSIONS);

		$version_delete = ['delete_date' => we_base_request::_(we_base_request::RAW, 'delete_date', ''),
			'delete_hours' => we_base_request::_(we_base_request::INT, 'delete_hours', 0),
			'delete_minutes' => we_base_request::_(we_base_request::INT, 'delete_minutes', 0),
			'delete_seconds' => we_base_request::_(we_base_request::INT, 'delete_seconds', 0),
		];
		$version_reset = ['reset_date' => we_base_request::_(we_base_request::RAW, 'reset_date', ''),
			'reset_hours' => we_base_request::_(we_base_request::INT, 'reset_hours', 0),
			'reset_minutes' => we_base_request::_(we_base_request::INT, 'reset_minutes', 0),
			'reset_seconds' => we_base_request::_(we_base_request::INT, 'reset_seconds', 0),
		];

		foreach($version->contentTypes as $k){
			$version_delete[$k] = we_base_request::_(we_base_request::BOOL, 'version_delete_' . $k);
			$version_reset[$k] = we_base_request::_(we_base_request::BOOL, 'version_reset_' . $k);
		}

		$def = (we_base_request::_(we_base_request::STRING, 'type') === 'reset_versions');
		$version_reset['reset_doPublish'] = we_base_request::_(we_base_request::BOOL, 'reset_doPublish', $def);

		$parts = [["headline" => "",
			"html" => we_html_forms::radiobutton("delete_versions", ($type == self::DELETE_VERSIONS), "type", g_l('versions', '[delete_versions]'), true, "defaultfont", "", false, g_l('versions', '[txt_delete_versions]'), 0, 495),
			],
				["headline" => "",
				"html" => we_html_forms::radiobutton("reset_versions", ($type == self::RESET_VERSIONS), "type", g_l('versions', '[reset_versions]'), true, "defaultfont", "", false, g_l('versions', '[txt_reset_versions]'), 0, 495),
		]];


		$js = '
window.onload = function(){
	top.focus();
}
function handle_event(what){
	f = document.we_form;
	switch(what){
	case "previous":
		break;
	case "next":
		selectedValue="";
		for(var i=0;i<f.type.length;i++){
			if(f.type[i].checked){
				selectedValue = f.type[i].value;
			}
		}
		goTo(selectedValue);
		break;
	}
}
function goTo(where){
	f = document.we_form;
	switch(where){
	case "rebuild_thumbnails":
	case "delete_versions":
		f.target="wizbody";
		break;
	case "rebuild_objects":
	case "rebuild_index":
	case "rebuild_navigation":
		set_button_state(1);
		f.target="wizcmd";
		f.step.value="2";
		break;
	}
	f.submit();
}
function set_button_state(alldis) {
	if(top.wizbusy){
		top.wizbusy.back_enabled = WE().layout.button.switch_button_state(top.wizbusy.document, "back", "disabled");
		if(alldis){
			top.wizbusy.next_enabled = WE().layout.button.switch_button_state(top.wizbusy.document, "next", "disabled");
			top.wizbusy.showRefreshButton();
		}else{
			top.wizbusy.next_enabled = WE().layout.button.switch_button_state(top.wizbusy.document, "next", "enabled");
		}
	}else{
		setTimeout(set_button_state,300,(alldis ? 1 : 0));
	}
}
set_button_state(false);';

		$hiddenFields = we_html_element::htmlHiddens($version_delete) .
			we_html_element::htmlHiddens($version_reset);

		return [$js,
			we_html_multiIconBox::getHTML("", $parts, 40, "", -1, "", "", false, g_l('versions', '[versioning]')) .
			$hiddenFields .
			we_html_element::htmlHiddens(["fr" => "body",
				"we_cmd[0]" => "versions_wizard",
				"step" => 1])
		];
	}

	/**
	 * returns the HTML for the Second Step (1) of the wizard
	 *
	 * @return string
	 */
	static function getStep1(){
		switch(we_base_request::_(we_base_request::STRING, "type", self::DELETE_VERSIONS)){
			case self::DELETE_VERSIONS:
				return self::getDelete1();
			case self::RESET_VERSIONS:
				return self::getReset1();
		}
	}

	static function getDelete1(){
		$version = new we_versions_version();
		$type = we_base_request::_(we_base_request::STRING, "type", self::DELETE_VERSIONS);

		//	$versions_delete_all = we_base_request::_(we_base_request::BOOL, "version_delete_all");
		$version_delete_date = we_base_request::_(we_base_request::RAW, "delete_date", "");
		$version_delete_hours = we_base_request::_(we_base_request::INT, "delete_hours", 0);
		$version_delete_minutes = we_base_request::_(we_base_request::INT, "delete_minutes", 0);
		$version_delete_seconds = we_base_request::_(we_base_request::INT, "delete_seconds", 0);

		$content = "";
		foreach($version->contentTypes as $k){
			$txt = $k;
			$name = "version_delete_" . $k;
			$val = "version_delete_" . $k;
			$checked = we_base_request::_(we_base_request::RAW, $k, 0);
			if($k === "all"){
				$jvs = "checkAll(this);";
				$content .= we_html_forms::checkbox($val, $checked, $name, g_l('versions', '[versions_all]'), false, "defaultfont", $jvs) . "<br/>";
			} else {
				$jvs = "checkAllRevert(this);";
				$content .= we_html_forms::checkbox($val, $checked, $name, g_l('contentTypes', '[' . $txt . ']'), false, "defaultfont", $jvs) . "<br/>";
			}
		}
		$parts = [['html' => we_html_tools::htmlAlertAttentionBox(g_l('versions', '[ct_delete_text]'), we_html_tools::TYPE_INFO, 520),
			'noline' => 1,
			],
				['headline' => g_l('versions', '[ContentType]'),
				'space' => we_html_multiIconBox::SPACE_BIG,
				'html' => $content,
				'noline' => 1
			]
		];


		$versions_delete_date = we_html_tools::getDateSelector('delete_date', '_1', $version_delete_date);

		$reset_hours = new we_html_select(["id" => "delete_hours",
			"name" => "delete_hours",
			'style' => "",
			"class" => "weSelect",
			"onchange" => ""
		]);

		for($x = 0; $x <= 23; $x++){
			$txt = $x;
			if($x <= 9){
				$txt = "0" . $x;
			}
			$reset_hours->addOption($x, $txt);
		}

		$reset_hours->selectOption($version_delete_hours);

		$reset_minutes = new we_html_select(["id" => "delete_minutes",
			"name" => "delete_minutes",
			'style' => "",
			"class" => "weSelect",
			"onchange" => ""
		]);

		for($x = 0; $x <= 59; $x++){
			$txt = $x;
			if($x <= 9){
				$txt = "0" . $x;
			}
			$reset_minutes->addOption($x, $txt);
		}

		$reset_minutes->selectOption($version_delete_minutes);

		$reset_seconds = new we_html_select(["id" => "delete_seconds",
			"name" => "delete_seconds",
			'style' => "",
			"class" => "weSelect",
			"onchange" => ""
			]);

		for($x = 0; $x <= 59; $x++){
			$txt = $x;
			if($x <= 9){
				$txt = "0" . $x;
			}
			$reset_seconds->addOption($x, $txt);
		}

		$reset_seconds->selectOption($version_delete_seconds);

		$parts[] = ['html' => we_html_tools::htmlAlertAttentionBox(g_l('versions', '[date_delete_text]'), we_html_tools::TYPE_INFO, 520),
			'noline' => 1,
		];

		$clearDate = we_html_button::create_button('reset', "javascript:document.getElementById('delete_date').value='';", '', 0, 0, "", "", "", false);


		$parts[] = ['headline' => g_l('versions', '[time]'),
			'html' => "<div style='padding-bottom:3px;'>" . g_l('versions', '[day]') . ":</div><div style='float:left;'>" . $versions_delete_date . "</div><div style='float:left;margin: 0px 0px 10px 10px;'>" . $clearDate . "</div><br style='clear:left;' /><div style='padding-bottom:3px;'>" . g_l('versions', '[clocktime]') . ":</div>" . $reset_hours->getHtml() . " h : " . $reset_minutes->getHtml() . " m: " . $reset_seconds->getHtml() . " s",
			'noline' => 1,
			'space' => we_html_multiIconBox::SPACE_BIG
		];

		//js
		$jsCheckboxCheckAll = '';
		$jsCheckboxCtIf = '';

		$jsCheckboxArgs = '';
		foreach($version->contentTypes as $k){
			if($k != "all"){
				$jsCheckboxCheckAll .= 'document.getElementById("version_delete_' . $k . '").checked = checked;';
			}
			$jsCheckboxCtIf .= (empty($jsCheckboxCtIf) ? '' : ' && ') . 'document.getElementById("version_delete_' . $k . '").checked==0';
			$jsCheckboxArgs .= 'args += "&ct[' . $k . ']="+encodeURI(document.getElementById("version_delete_' . $k . '").checked);';
		}

		$nextButton = we_html_button::create_button(we_html_button::NEXT, "javascript:parent.wizbody.handle_event(\"next\");", '', 0, 0, "", "", "", false);

		$js = '
window.onload = function(){
	top.focus();
}
function handle_event(what){
	f = document.we_form;
	switch(what){
		case "previous":
			f.step.value=0
			f.target="wizbody";
			f.submit();
			break;
		case "next":
			var date = document.getElementById("delete_date").value;
			var hour = document.getElementById("delete_hours").value;
			var minutes = document.getElementById("delete_minutes").value;
			var seconds = document.getElementById("delete_seconds").value;
			if(' . $jsCheckboxCtIf . ') {
				' . we_message_reporting::getShowMessageCall(g_l('versions', '[notCheckedContentType]'), we_message_reporting::WE_MESSAGE_NOTICE) . '
			}else {
				selectedValue="";
				for(var i=0;i<f.type.length;i++){
					if(f.type[i].checked){;
						selectedValue = f.type[i].value;
					}
				}
				goTo(selectedValue);
			}
		break;
	}
}

function checkAll(val) {
	if(val.checked) {
		checked = 1;
	}else {
		checked = 0;
	}' . $jsCheckboxCheckAll . ';

}

function checkAllRevert() {//FIXME:unused (box doesnt exist?)
	var checkbox = document.getElementById("version_delete_all");
	checkbox.checked = false;
}

function goTo(where){
	f = document.we_form;
	switch(where){
		case "delete_versions":
			f.target="wizbody";
			break;
	}
	f.submit();
}



function set_button_state(alldis) {
					if(top.wizbusy){
						top.wizbusy.back_enabled = WE().layout.button.switch_button_state(top.wizbusy.document, "back", "enabled");
		if(alldis){
							top.wizbusy.next_enabled = WE().layout.button.switch_button_state(top.wizbusy.document, "next", "enabled");
							top.wizbusy.showRefreshButton();
		}else{
							top.wizbusy.next_enabled = WE().layout.button.switch_button_state(top.wizbusy.document, "next", "enabled");
							var nextBut = top.wizbusy.document.getElementById(\'next\');
				nextBut.innerHTML = \'' . $nextButton . '\';
		}
	}else{
		setTimeout(set_button_state,300,(alldis ? 1 : 0));
	}
}
set_button_state(false);';


		return [$js,
			we_html_multiIconBox::getHTML("", $parts, 40, "", -1, "", "", false, g_l('versions', '[delete_versions]') . " - " . g_l('versions', '[step]') . " 1 " . g_l('versions', '[of]') . " 2") .
			we_html_element::htmlHiddens(["fr" => "body",
				"type" => $type,
				"we_cmd[0]" => "versions_wizard",
				"step" => 2])
		];
	}

	static function getReset1(){
		$version = new we_versions_version();
		$type = we_base_request::_(we_base_request::RAW, "type", self::RESET_VERSIONS);

		$version_reset_date = we_base_request::_(we_base_request::RAW, "reset_date", "");
		$version_reset_hours = we_base_request::_(we_base_request::INT, "reset_hours", 0);
		$version_reset_minutes = we_base_request::_(we_base_request::INT, "reset_minutes", 0);
		$version_reset_seconds = we_base_request::_(we_base_request::INT, "reset_seconds", 0);
		$version_reset_doPublish = we_base_request::_(we_base_request::BOOL, "reset_doPublish");


		$content = "";
		foreach($version->contentTypes as $k){
			$txt = $k;
			$name = "version_reset_" . $k;
			$val = "version_reset_" . $k;
			$checked = we_base_request::_(we_base_request::RAW, $k, 0);
			if($k === "all"){
				$jvs = "checkAll(this);";
				$content .= we_html_forms::checkbox($val, $checked, $name, g_l('versions', '[versions_all]'), false, "defaultfont", $jvs) . "<br/>";
			} else {
				$jvs = "checkAllRevert(this);";
				$content .= we_html_forms::checkbox($val, $checked, $name, g_l('contentTypes', '[' . $txt . ']'), false, "defaultfont", $jvs) . "<br/>";
			}
		}

		$versions_reset_date = we_html_tools::getDateSelector("reset_date", "_1", $version_reset_date);
		$doPublish = we_html_forms::checkbox($version_reset_doPublish, $version_reset_doPublish, "reset_doPublish", g_l('versions', '[publishIfReset]'), false, "defaultfont", "");


		$parts = [['html' => we_html_tools::htmlAlertAttentionBox(g_l('versions', '[ct_reset_text]'), we_html_tools::TYPE_INFO, 520),
			'noline' => 1,
			],
				['headline' => g_l('versions', '[ContentType]'),
				'space' => we_html_multiIconBox::SPACE_BIG,
				'html' => $content,
				'noline' => 1
			],
				['html' => we_html_tools::htmlAlertAttentionBox(g_l('versions', '[doPublish_text]'), we_html_tools::TYPE_INFO, 520),
				'noline' => 1,
			],
				['headline' => "", 'html' => $doPublish, 'noline' => 1
			],
		];

		$reset_hours = new we_html_select(["id" => "reset_hours",
			"name" => "reset_hours",
			'style' => "",
			"class" => "weSelect",
			"onchange" => ""
		]);

		for($x = 0; $x <= 23; $x++){
			$txt = $x;
			if($x <= 9){
				$txt = "0" . $x;
			}
			$reset_hours->addOption($x, $txt);
		}

		$reset_hours->selectOption($version_reset_hours);

		$reset_minutes = new we_html_select(["id" => "reset_minutes",
			"name" => "reset_minutes",
			'style' => "",
			"class" => "weSelect",
			"onchange" => ""
		]);

		for($x = 0; $x <= 59; $x++){
			$txt = $x;
			if($x <= 9){
				$txt = "0" . $x;
			}
			$reset_minutes->addOption($x, $txt);
		}

		$reset_minutes->selectOption($version_reset_minutes);

		$reset_seconds = new we_html_select(["id" => "reset_seconds",
			"name" => "reset_seconds",
			'style' => "",
			"class" => "weSelect",
			"onchange" => ""
		]);

		for($x = 0; $x <= 59; $x++){
			$txt = $x;
			if($x <= 9){
				$txt = "0" . $x;
			}
			$reset_seconds->addOption($x, $txt);
		}

		$reset_seconds->selectOption($version_reset_seconds);

		$parts[] = ['html' => we_html_tools::htmlAlertAttentionBox(g_l('versions', '[date_reset_text]'), we_html_tools::TYPE_INFO, 520),
			'noline' => 1,
		];

		$clearDate = we_html_button::create_button('reset', "javascript:document.getElementById('reset_date').value='';", '', 0, 0, "", "", "", false);

		$parts[] = ['headline' => g_l('versions', '[time]'),
			'html' => "<div style='padding-bottom:3px;'>Tag:</div><div style='float:left;'>" . $versions_reset_date . "</div><div style='float:left;margin:0px 0px 10px 10px;'>" . $clearDate . "</div><br style='clear:left;' /><div style='padding-bottom:3px;'>Uhrzeit:</div>" . $reset_hours->getHtml() . " h : " . $reset_minutes->getHtml() . " m: " . $reset_seconds->getHtml() . " s ",
			'noline' => 1,
			'space' => we_html_multiIconBox::SPACE_BIG
		];

		//js
		$jsCheckboxCheckAll = '';
		$jsCheckboxCtIf = '';

		$jsCheckboxArgs = '';
		foreach($version->contentTypes as $k){
			if($k != "all"){
				$jsCheckboxCheckAll .= 'document.getElementById("version_reset_' . $k . '").checked = checked;';
			}
			if($jsCheckboxCtIf != ""){
				$jsCheckboxCtIf .= " && ";
			}
			$jsCheckboxCtIf .= 'document.getElementById("version_reset_' . $k . '").checked==0';
			$jsCheckboxArgs .= 'args += "&ct[' . $k . ']="+encodeURI(document.getElementById("version_reset_' . $k . '").checked);';
		}

		$nextButton = we_html_button::create_button(we_html_button::NEXT, "javascript:parent.wizbody.handle_event(\"next\");", '', 0, 0, "", "", "", false);

		$js = 'window.onload = function(){
					top.focus();
				}
				function handle_event(what){
					f = document.we_form;
					switch(what){
						case "previous":
							f.step.value=0
							f.target="wizbody";
							f.submit();
							break;
						case "next":
							var date = document.getElementById("reset_date").value;
							var hour = document.getElementById("reset_hours").value;
							var minutes = document.getElementById("reset_minutes").value;
							var seconds = document.getElementById("reset_seconds").value;
							if(' . $jsCheckboxCtIf . ') {
								' . we_message_reporting::getShowMessageCall(g_l('versions', '[notCheckedContentType]'), we_message_reporting::WE_MESSAGE_NOTICE) . '
							}
							else if(date=="") {
								' . we_message_reporting::getShowMessageCall(g_l('versions', '[notCheckedDate]'), we_message_reporting::WE_MESSAGE_NOTICE) . '
							}
							else {
								selectedValue="";
								for(var i=0;i<f.type.length;i++){
									if(f.type[i].checked){;
										selectedValue = f.type[i].value;
									}
								}
								goTo(selectedValue);
							}
						break;
					}
				}

				function checkAll(val) {
		            	if(val.checked) {
		            		checked = 1;
		            	}
		            	else {
		            		checked = 0;
		            	}
						' . $jsCheckboxCheckAll . ';

					}

	            	function checkAllRevert() {//FIXME:unused (box doesnt exist?)
	            		var checkbox = document.getElementById("version_reset_all");
						checkbox.checked = false;
	            	}


				function goTo(where){
					f = document.we_form;
					switch(where){
						case "reset_versions":
							f.target="wizbody";
							break;
					}
					f.submit();
				}

				function set_button_state(alldis) {
					if(top.wizbusy){
						top.wizbusy.back_enabled = WE().layout.button.switch_button_state(top.wizbusy.document, "back", "enabled");
						if(alldis){
							top.wizbusy.next_enabled = WE().layout.button.switch_button_state(top.wizbusy.document, "next", "enabled");
							top.wizbusy.showRefreshButton();
						}else{
							top.wizbusy.next_enabled = WE().layout.button.switch_button_state(top.wizbusy.document, "next", "enabled");
							var nextBut = top.wizbusy.document.getElementById("next");
				  			nextBut.innerHTML = \'' . $nextButton . '\';
						}
					}else{
						setTimeout(set_button_state,300,(alldis ? 1 : 0));
					}
				}
				set_button_state(false);';

		return [$js,
			we_html_multiIconBox::getHTML("", $parts, 40, "", -1, "", "", false, g_l('versions', '[reset_versions]') . " - " . g_l('versions', '[step]') . " 1 " . g_l('versions', '[of]') . " 2") .
			we_html_element::htmlHiddens(["fr" => "body",
				"type" => $type,
				"we_cmd[0]" => "versions_wizard",
				"step" => 2])
		];
	}

	/**
	 * returns the HTML for the Third Step (2) of the wizard. - Here the real work (loop) is done - it should be displayed in the cmd frame
	 *
	 * @return string
	 */
	static function getStep2(){
		$type = we_base_request::_(we_base_request::STRING, "type", self::DELETE_VERSIONS);

		switch($type){
			case self::DELETE_VERSIONS:
				return self::getDelete2();
			case self::RESET_VERSIONS:
				return self::getReset2();
		}
	}

	static function getStep3(){
		$version = new we_versions_version();

		$type = we_base_request::_(we_base_request::STRING, "type", self::DELETE_VERSIONS);

		$version_delete = [];
		$version_reset = [];

		foreach($version->contentTypes as $k){
			$version_delete[$k] = we_base_request::_(we_base_request::BOOL, "version_delete_" . $k);
			$version_reset[$k] = we_base_request::_(we_base_request::BOOL, "version_reset_" . $k);
		}

		$version_delete['delete_date'] = we_base_request::_(we_base_request::RAW, "delete_date", "");
		$version_delete['delete_hours'] = we_base_request::_(we_base_request::INT, "delete_hours", 0);
		$version_delete['delete_minutes'] = we_base_request::_(we_base_request::INT, "delete_minutes", 0);
		$version_delete['delete_seconds'] = we_base_request::_(we_base_request::INT, "delete_seconds", 0);

		$version_reset['reset_date'] = we_base_request::_(we_base_request::RAW, "reset_date", "");
		$version_reset['reset_hours'] = we_base_request::_(we_base_request::INT, "reset_hours", 0);
		$version_reset['reset_minutes'] = we_base_request::_(we_base_request::INT, "reset_minutes", 0);
		$version_reset['reset_seconds'] = we_base_request::_(we_base_request::INT, "reset_seconds", 0);

		$def = (we_base_request::_(we_base_request::STRING, 'type') === 'reset_versions');
		$version_reset['reset_doPublish'] = we_base_request::_(we_base_request::BOOL, 'reset_doPublish', $def);

		$taskname = md5(session_id() . '_version_wizard');
		$currentTask = we_base_request::_(we_base_request::RAW, 'fr_' . $taskname . '_ct', 0);
		$taskFilename = FRAGMENT_LOCATION . $taskname;

		$js = "";
		if(!(file_exists($taskFilename) && $currentTask)){
			switch($type){
				case self::DELETE_VERSIONS:
					$data = we_versions_fragment::getDocuments($type, $version_delete);
					break;
				case self::RESET_VERSIONS:
					$data = we_versions_fragment::getDocuments($type, $version_reset);
					break;
			}
			if(count($data)){
				$fr = new we_versions_fragment($taskname, $data);

				return [];
			}
			return [$js . we_message_reporting::getShowMessageCall(g_l('versions', '[deleteNothingFound]'), 1) . 'top.wizbusy.showPrevNextButton();',
				""
			];
		}
		$fr = new we_versions_fragment($taskname);
		return [];
	}

	/**
	 * returns Array with javascript (array[0]) and HTML Content (array[1]) for the rebuild document page
	 *
	 * @return array
	 */
	static function getDelete2(){
		$version = new we_versions_version();

		$type = we_base_request::_(we_base_request::STRING, "type", self::DELETE_VERSIONS);

		$version_delete = ['delete_date' => we_base_request::_(we_base_request::RAW, "delete_date", ""),
			'delete_hours' => we_base_request::_(we_base_request::INT, "delete_hours", 0),
			'delete_minutes' => we_base_request::_(we_base_request::INT, "delete_minutes", 0),
			'delete_seconds' => we_base_request::_(we_base_request::INT, "delete_seconds", 0),
		];

		foreach($version->contentTypes as $k){
			$version_delete[$k] = we_base_request::_(we_base_request::BOOL, "version_delete_" . $k);
		}

		$timestamp = "";
		$timestampWhere = 1;
		if($version_delete['delete_date'] != ""){
			$date = explode(".", we_base_request::_(we_base_request::STRING, "delete_date"));
			$day = intval($date[0]);
			$month = intval($date[1]);
			$year = intval($date[2]);
			$hour = $version_delete['delete_hours'];
			$minutes = $version_delete['delete_minutes'];
			$seconds = $version_delete['delete_seconds'];
			$timestamp = mktime($hour, $minutes, $seconds, $month, $day, $year);

			$timestampWhere = ' timestamp<' . $timestamp . ' ';
		}

		$parts = [];

		$whereCtA = [];
		foreach($version_delete as $k => $v){
			switch($k){
				case 'all':
				case 'delete_date':
				case 'delete_hours':
				case 'delete_minutes':
				case 'delete_seconds':
					break;
				default:
					if($v){
						$whereCtA[] = '"' . $k . '"';
					}
			}
		}
		$whereCt = ($whereCt ? ' ContentType IN (' . implode(',', $whereCtA) . ')' : '1');

		$cont = $docIds = [];
		$_SESSION['weS']['versions']['deleteWizardWhere'] = $whereCt . ' AND ' . $timestampWhere;
		$GLOBALS['DB_WE']->query('SELECT ID,documentID,documentTable,Text AS text,Path AS path,ContentType,binaryPath,timestamp,version FROM ' . VERSIONS_TABLE . ' WHERE ' . $whereCt . ' AND ' . $timestampWhere . ' ORDER BY ID');
		$_SESSION['weS']['versions']['logDeleteIds'] = [];
		while($GLOBALS['DB_WE']->next_record(MYSQL_ASSOC)){
			if(!in_array($GLOBALS['DB_WE']->f('documentID'), $docIds)){
				$docIds[$GLOBALS['DB_WE']->f('documentID')] = ['Path' => $GLOBALS['DB_WE']->f('path'),
					'ContentType' => $GLOBALS['DB_WE']->f('ContentType')
				];
			}

			$cont[] = $GLOBALS['DB_WE']->getRecord();
			$_SESSION['weS']['versions']['logDeleteIds'][$GLOBALS['DB_WE']->f('ID')] = ['Text' => $GLOBALS['DB_WE']->f('text'),
				'ContentType' => $GLOBALS['DB_WE']->f('ContentType'),
				'Path' => $GLOBALS['DB_WE']->f('path'),
				'Version' => $GLOBALS['DB_WE']->f('version'),
				'documentID' => $GLOBALS['DB_WE']->f('documentID'),
			];
			if($GLOBALS['DB_WE']->f('binaryPath')){
				$_SESSION['weS']['versions']['deleteWizardbinaryPath'][] = $GLOBALS['DB_WE']->f('binaryPath');
			}
		}

		$out = '<div style="width:520px;">' .
			g_l('versions', '[step2_txt1]');

		if($timestamp){
			$date = date('d.m.y - H:i:s', $timestamp);
			$out .= sprintf(g_l('versions', '[step2_txt2_delete]'), $date);
		}
		$out .= g_l('versions', '[step2_txt3]') .
			'</div>
<div style="background-color:#fff;width:520px;margin-top:20px;">
	<table style="width:100%">
		<tr class="defaultfont" style="height:30px;">
		<th style="border-bottom:1px solid #B7B5B6;">' . g_l('versions', '[_id]') . '</th>
		<th style="border-bottom:1px solid #B7B5B6;">' . g_l('versions', '[path]') . '</th>
		<th style="border-bottom:1px solid #B7B5B6;">' . g_l('versions', '[ContentType]') . '</th>
		</tr>';

		foreach($docIds as $k => $v){
			$out .= '
<tr class="defaultfont">
	<td style="text-align:center">' . $k . '</td>
	<td style="text-align:center">' . we_base_util::shortenPath($v['Path'], 55) . '</td>
	<td style="text-align:center">' . $v['ContentType'] . '</td>
</tr>';
		}
		$out .= '</table>
			</div>';

		$parts[] = ["headline" => "",
			"html" => $out,
		];

		$hiddenFields = we_html_element::htmlHiddens($version_delete);

		return [self::getPage2Js(!empty($cont), 'delete'),
			we_html_multiIconBox::getHTML("", $parts, 40, "", -1, "", "", false, g_l('versions', '[delete_versions]') . " - " . g_l('versions', '[step]') . " 2 " . g_l('versions', '[of]') . " 2") .
			$hiddenFields .
			we_html_element::htmlHiddens(["fr" => "body",
				"type" => $type,
				"we_cmd[0]" => "versions_wizard",
				"step" => 3]
		)];
	}

	static function getReset2(){
		$type = we_base_request::_(we_base_request::RAW, "type", self::RESET_VERSIONS);

		$_SESSION['weS']['versions']['logResetIds'] = [];

		$version_reset = ['reset_date' => we_base_request::_(we_base_request::STRING, "reset_date", ""),
			'reset_hours' => we_base_request::_(we_base_request::INT, "reset_hours", 0),
			'reset_minutes' => we_base_request::_(we_base_request::INT, "reset_minutes", 0),
			'reset_seconds' => we_base_request::_(we_base_request::INT, "reset_seconds", 0),
		];

		foreach(we_versions_version::getContentTypesVersioning() as $k){
			$version_reset[$k] = we_base_request::_(we_base_request::BOOL, "version_reset_" . $k);
		}

		$def = (we_base_request::_(we_base_request::STRING, 'type') === 'reset_versions');
		$version_reset['reset_doPublish'] = we_base_request::_(we_base_request::BOOL, 'reset_doPublish', $def);

		if($version_reset['reset_date']){
			$date = explode('.', $version_reset['reset_date']);
			$day = intval($date[0]);
			$month = intval($date[1]);
			$year = intval($date[2]);
			$hour = $version_reset['reset_hours'];
			$minutes = $version_reset['reset_minutes'];
			$seconds = $version_reset['reset_seconds'];
			$timestamp = mktime($hour, $minutes, $seconds, $month, $day, $year);
			$timestampWhere = ' timestamp<' . $timestamp . ' ';
		} else {
			$timestamp = 0;
			$timestampWhere = 1;
		}


		$w = [];
		foreach($version_reset as $k => $v){
			switch($k){
				case "all":
				case "reset_date":
				case "reset_hours":
				case "reset_minutes":
				case "reset_seconds":
				case "reset_doPublish":
					break;
				case we_base_ContentTypes::WEDOCUMENT:
				case we_base_ContentTypes::HTML:
				case we_base_ContentTypes::OBJECT_FILE:
					if($v){
						$w[] = '(ContentType="' . $k . '" AND status="published" )';
					}
					break;
				default:
					if($v){
						$w[] = ' ContentType="' . $k . '" ';
					}
					break;
			}
		}

		$cont = $docIds = [];

		$_SESSION['weS']['versions']['query'] = 'SELECT ID,documentID,documentTable,Text,Path,ContentType,timestamp,MAX(version) as version FROM ' . VERSIONS_TABLE . ' WHERE timestamp<=' . $timestamp . ($w ? ' AND (' . implode(' OR ', $w) . ') ' : '') . ' GROUP BY documentTable,documentID ORDER BY version DESC';
		$GLOBALS['DB_WE']->query($_SESSION['weS']['versions']['query']);
		while($GLOBALS['DB_WE']->next_record()){
			if(!in_array($GLOBALS['DB_WE']->f("documentID"), $docIds)){
				$docIds[$GLOBALS['DB_WE']->f("documentID")]["Path"] = $GLOBALS['DB_WE']->f("Path");
				$docIds[$GLOBALS['DB_WE']->f("documentID")]["ContentType"] = $GLOBALS['DB_WE']->f("ContentType");
			}

			$cont[] = ["ID" => $GLOBALS['DB_WE']->f("ID"),
				"documentID" => $GLOBALS['DB_WE']->f("documentID"),
				"version" => $GLOBALS['DB_WE']->f("version"),
				"text" => $GLOBALS['DB_WE']->f("Text"),
				"path" => $GLOBALS['DB_WE']->f("Path"),
				"table" => $GLOBALS['DB_WE']->f("documentTable"),
				"contentType" => $GLOBALS['DB_WE']->f("ContentType"),
				"timestamp" => $GLOBALS['DB_WE']->f("timestamp")
			];
		}

//FIXME: date format should be obtained from g_l
		$out = '
<div style="width:520px;">' . sprintf(g_l('versions', '[step2_txt_reset]'), date("d.m.y - H:i:s", $timestamp)) . '</div>
<div style="background-color:#fff;width:520px;margin-top:20px;">
	<table style="width:100%">
	<tr class="defaultfont" style="height:30px;">
		<th style="border-bottom:1px solid #B7B5B6;">' . g_l('versions', '[_id]') . '</th>
		<th style="border-bottom:1px solid #B7B5B6;">' . g_l('versions', '[path]') . '</th>
		<th style="border-bottom:1px solid #B7B5B6;">' . g_l('versions', '[ContentType]') . '</th>
	</tr>';

		foreach($docIds as $k => $v){
			$out .= '
<tr class="defaultfont">
	<td style="text-align:center">' . $k . '</td>
	<td style="text-align:center">' . we_base_util::shortenPath($v['Path'], 55) . '</td>
	<td style="text-align:center">' . $v['ContentType'] . '</td>
</tr>';
		}
		$out .= '</table>
			</div>';

		$parts[] = ["headline" => "",
			"html" => $out,
		];

		$hiddenFields = we_html_element::htmlHiddens($version_reset);

		return [self::getPage2Js(!empty($cont), "reset"),
			we_html_multiIconBox::getHTML("", $parts, 40, "", -1, "", "", false, g_l('versions', '[reset_versions]') . " - " . g_l('versions', '[step]') . " 2 " . g_l('versions', '[of]') . " 2") .
			$hiddenFields .
			we_html_element::htmlHiddens(["fr" => "body",
				"type" => $type,
				"we_cmd[0]" => "versions_wizard",
				"step" => 3])
		];
	}

	/**
	 * returns HTML for the frameset
	 *
	 * @return string
	 */
	static function getFrameset(){
		$query = [];
		if(($btype = we_base_request::_(we_base_request::STRING, "btype")) !== false){
			$query['btype'] = $btype;
		}
		if(($type = we_base_request::_(we_base_request::STRING, "type"))){
			$query['type'] = $type;
		}
		if(($tid = we_base_request::_(we_base_request::INT, "templateID")) !== false){
			$query['templateID'] = $tid;
		}
		if(($step = we_base_request::_(we_base_request::INT, "step")) !== false){
			$query['step'] = $step;
		}
		if(($text = we_base_request::_(we_base_request::STRING, 'responseText')) !== false){
			$query['responseText'] = $text;
		}

		$taskname = md5(session_id() . "_version_wizard");
		$taskFilename = WE_FRAGMENT_PATH . $taskname;
		if(file_exists($taskFilename)){
			we_base_file::delete($taskFilename);
		}

		if($query){
			$query['we_cmd'][0] = 'versions_wizard';
			$query['fr'] = 'body';
			//maybe restore of a given version?
			$body = we_html_element::htmlBody(['class' => 'weDialogBody', 'onload' => "wizcmd.location='" . WEBEDITION_DIR . 'we_cmd.php?' . http_build_query(query) . "';"]
					, we_html_element::htmlIFrame('wizbusy', WEBEDITION_DIR . 'we_cmd.php?' . http_build_query(['we_cmd' => [0 => 'versions_wizard'], 'fr' => 'busy',
							'dc' => 1]), 'position:absolute;top:0px;bottom:0px;left:0px;right:0px;overflow: hidden;', '', '', false) .
					we_html_element::htmlIFrame('wizcmd', "about:blank", 'position:absolute;height:0px;bottom:0px;left:0px;right:0px;overflow: hidden;')
			);
		} else {
			$body = we_html_element::htmlBody(['class' => 'weDialogBody']
					, we_html_element::htmlIFrame('wizbody', WEBEDITION_DIR . 'we_cmd.php?' . http_build_query(['we_cmd' => [0 => 'versions_wizard'], 'fr' => 'body']), 'position:absolute;top:0px;bottom:40px;left:0px;right:0px;') .
					we_html_element::htmlIFrame('wizbusy', WEBEDITION_DIR . 'we_cmd.php?' . http_build_query(['we_cmd' => [0 => 'versions_wizard'], 'fr' => 'busy']), 'position:absolute;bottom:0px;height:40px;left:0px;right:0px;overflow: hidden;', '', '', false) .
					we_html_element::htmlIFrame('wizcmd', WEBEDITION_DIR . 'we_cmd.php?' . http_build_query(['we_cmd' => [0 => 'versions_wizard'], 'fr' => 'cmd']), 'position:absolute;height:0px;bottom:0px;left:0px;right:0px;overflow: hidden;')
			);
		}

		return we_html_tools::getHtmlTop(g_l('versions', '[versions_wizard]'), '', '', '', $body);
	}

	/**
	 * returns Javascript for step 2 (1)
	 *
	 * @return string
	 * @param string $folders csv value with directory IDs
	 */
	static function getPage2Js($cont, $action){
		$disabled = !$cont;
		//reset
		$act = ($action === "delete" ? 0 : 1);

		$nextButton = we_html_button::create_button('go', 'javascript:parent.wizbody.handle_event("next");', '', 0, 0, "", "", $disabled, false);
		$publish = we_base_request::_(we_base_request::BOOL, 'reset_doPublish');
		$we_transaction = $GLOBALS['we_transaction'];
		return '
window.onload = function(){
	top.focus();
}
function handle_event(what){
	f = document.we_form;
	switch(what){
		case "previous":
			f.step.value=1
			f.target="wizbody";
			f.submit();
			break;
		case "next":
				selectedValue="";
				for(var i=0;i<f.type.length;i++){
					if(f.type[i].checked){;
						selectedValue = f.type[i].value;
					}
				}
				goTo(selectedValue);

		break;
	}
}

function ajaxCallbackDeleteVersionsWizard (responseText){
	if(responseText != "") {
		parent.wizbusy.document.getElementById("progr").innerHTML = responseText;
		' . we_message_reporting::getShowMessageCall(addslashes(g_l('versions', '[deleteDateVersionsOK]') ?: ""), we_message_reporting::WE_MESSAGE_NOTICE) . '
		// reload current document => reload all open Editors on demand

		var _usedEditors =  WE().layout.weEditorFrameController.getEditorsInUse();
		for (frameId in _usedEditors) {

			if ( _usedEditors[frameId].getEditorIsActive() ) { // reload active editor
				_usedEditors[frameId].setEditorReloadAllNeeded(true);
				_usedEditors[frameId].setEditorIsActive(true);

			} else {
				_usedEditors[frameId].setEditorReloadAllNeeded(true);
			}
		}
		_multiEditorreload = true;

		//reload tree
		top.opener.we_cmd("load", top.opener.top.treeData.table ,0);
		top.close();
	}
}

function ajaxCallbackResetVersionsWizard(responseText) {
	if(responseText != "") {
		parent.wizbusy.document.getElementById("progr").innerHTML = responseText;
		' . we_message_reporting::getShowMessageCall(addslashes(g_l('versions', '[resetAllVersionsOK]') ?: ""), we_message_reporting::WE_MESSAGE_NOTICE) . '
		top.close();
	}
}

function goTo(where){

	if(' . $act . ') {
	var f = document.we_form;
		switch(where){
			case "delete_versions":
				f.target="wizbody";
				break;
		}
		f.submit();

//						parent.wizbusy.document.getElementById("progr").style.display = "block";
/*	WE().util.rpc(WE().consts.dirs.WEBEDITION_DIR + "rpc.php?cmd=ResetVersionsWizard","protocol=json&publish=' . $publish . '&we_transaction=' . $we_transaction . '&cns=versionlist", ajaxCallbackResetVersionsWizard,"html");
*/
//
	}	else {
		parent.wizbusy.document.getElementById("progr").style.display = "block";
		WE().util.rpc(WE().consts.dirs.WEBEDITION_DIR + "rpc.php?cmd=DeleteVersionsWizard", "protocol=json&cns=versionlist",ajaxCallbackDeleteVersionsWizard,"html");
	}
}


function set_button_state(alldis) {
					if(top.wizbusy){
						top.wizbusy.back_enabled = WE().layout.button.switch_button_state(top.wizbusy.document, "back", "enabled");
		if(alldis){
							top.wizbusy.next_enabled = WE().layout.button.switch_button_state(top.wizbusy.document, "next", "enabled");
							top.wizbusy.showRefreshButton();
		}else{
							top.wizbusy.next_enabled = WE().layout.button.switch_button_state(top.wizbusy.document, "next", "enabled");
							var nextBut = top.wizbusy.document.getElementById(\'next\');
				nextBut.innerHTML = \'' . $nextButton . '\';
		}
	}else{
		setTimeout(set_button_state,300,(alldis ? 1 : 0));
	}
}
set_button_state(false);';
	}

	/**
	 * returns Javascript for step 2 (1)
	 *
	 * @return string
	 * @param array first element (array[0]) must be a javascript, second element (array[1]) must be the Body HTML
	 */
	static function getPage($contents){
		if(!count($contents)){
			return '';
		}

		return we_html_tools::getHtmlTop('', '', '',
				($contents[0] ? we_html_element::jsElement($contents[0]) : ""), we_html_element::htmlBody(
					['class' => "weDialogBody"]
					, we_html_element::htmlForm(["name" => "we_form",
						"method" => "post",
						"action" => WEBEDITION_DIR . "we_cmd.php"
						], $contents[1]))
		);
	}

	public static function showFrameset(){
		switch(we_base_request::_(we_base_request::STRING, "fr", '')){
			case "body" :
				echo self::getBody();
				break;
			case "busy" :
				echo self::getBusy();
				break;
			case "cmd" :
				echo self::getCmd();
				break;
			default :
				echo self::getFrameset();
		}
	}

}
