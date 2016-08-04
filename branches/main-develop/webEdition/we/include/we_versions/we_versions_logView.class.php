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
class we_versions_logView{
	public $actionView;

	const versionPerPage = 10;

	private $Model;

	public function __construct(){
		$this->Model = new we_versions_log();
	}

	public function getJS(){
		return we_html_element::jsElement('
var ajaxURL = WE().consts.dirs.WEBEDITION_DIR+"rpc.php";

var currentId = 0;

var ajaxCallbackDetails = {
	success: function(o) {
	if(o.responseText !== undefined && o.responseText != "") {
		document.getElementById("dataContent_"+currentId+"").innerHTML = o.responseText;
	}
},
	failure: function(o) {
	}
}

function openDetails(id) {
	currentId = id;
	var dataContent = document.getElementById("dataContent_"+id+"");
	dataContent.innerHTML = "<table border=\'0\' width=\'100%\' height=\'100%\'><tr><td style=\'text-align:center\'><i class=\"fa fa-2x fa-spinner fa-pulse\"></i></td></tr></table>";
	var otherdataContents = document.getElementsByName("dataContent");
	for(var i=0;i<otherdataContents.length;i++) {
		if(otherdataContents[i].id != "dataContent_"+id+""){
			otherdataContents[i].innerHTML = "";
		}
	}


	YAHOO.util.Connect.asyncRequest("POST", ajaxURL, ajaxCallbackDetails, "protocol=json&cns=logging/versions&cmd=GetLogVersionDetails&id="+id+"");

}

function showAll(id) {
	var Elements = document.getElementsByName(id+"_list");
	for(var i=0;i<Elements.length;i++) {
		Elements[i].style.display = "";
	}

	var newstartNumber = 1;
	document.getElementById("startNumber_"+id).innerHTML = newstartNumber;

	var newshowNumber = Elements.length;
	document.getElementById("showNumber_"+id).innerHTML = newshowNumber;

	document.getElementById("showAll_"+id).innerHTML = "' . g_l('logging', '[defaultView]') . '";
	document.getElementById("showAll_"+id).onclick = function(){
		showDefault(id);
	};
	document.getElementById("back_"+id).style.display = "none";
	document.getElementById("next_"+id).style.display = "none";

}

function showDefault(id) {
	var Elements = document.getElementsByName(id+"_list");
	for(var i=0;i<Elements.length;i++) {
		if(i>=' . self::versionPerPage . ') {
			Elements[i].style.display = "none";
		}
		else {
			Elements[i].style.display = "";
		}
	}

	var newstartNumber = 1;
	document.getElementById("startNumber_"+id).innerHTML = newstartNumber;

	var newshowNumber = ' . self::versionPerPage . ';
	document.getElementById("showNumber_"+id).innerHTML = newshowNumber;

	document.getElementById("back_"+id).style.display = "none";
	document.getElementById("next_"+id).style.display = "inline";

	document.getElementById("showAll_"+id).innerHTML = "' . g_l('logging', '[all]') . '";
	document.getElementById("showAll_"+id).onclick = function(){
		showAll(id);
	};

	document.getElementsByName("start_"+id)[0].value = 0;

}

function next(id) {
	var start = document.getElementsByName("start_"+id)[0].value;
	var newStart = parseInt(start) + ' . self::versionPerPage . ';

	var Elements = document.getElementsByName(id+"_list");
	for(var i=0;i<Elements.length;i++) {
		if(i>=newStart && i<(newStart + ' . self::versionPerPage . ')) {
			Elements[i].style.display = "";
		}
		else {
			Elements[i].style.display = "none";
		}

	}

	if(newStart>(Elements.length-' . self::versionPerPage . ')) {
		document.getElementById("next_"+id).style.display = "none";
	}
	else {
		document.getElementById("next_"+id).style.display = "inline";
	}
	document.getElementById("back_"+id).style.display = "inline";

	var newstartNumber = newStart+1;
	document.getElementById("startNumber_"+id).innerHTML = newstartNumber;

	var newshowNumber = Elements.length;
	if(Elements.length>(newStart+' . self::versionPerPage . ')) {
		newshowNumber = (newStart+' . self::versionPerPage . ');
	}

	document.getElementById("showNumber_"+id).innerHTML = newshowNumber;

	document.getElementsByName("start_"+id)[0].value = parseInt(newStart);


}

function back(id) {
	var start = document.getElementsByName("start_"+id)[0].value;
	var newStart = parseInt(start) - ' . self::versionPerPage . ';

	var Elements = document.getElementsByName(id+"_list");
	for(var i=0;i<Elements.length;i++) {
		if(i>=newStart && i<(newStart + ' . self::versionPerPage . ')) {
			Elements[i].style.display = "";
		}
		else {
			Elements[i].style.display = "none";
		}

	}


	if(newStart==0) {
		document.getElementById("back_"+id).style.display = "none";
	}
	else {
		document.getElementById("back_"+id).style.display = "inline";
	}
	document.getElementById("next_"+id).style.display = "inline";

	var newstartNumber = newStart+1;
	document.getElementById("startNumber_"+id).innerHTML = newstartNumber;


	newshowNumber = (newstartNumber+' . self::versionPerPage . ');
	document.getElementById("showNumber_"+id).innerHTML = newshowNumber;

	document.getElementsByName("start_"+id)[0].value = parseInt(newStart);

}');
	}

	private function getContent(){
		return $this->Model->load();
	}

	public function printContent(){
		$content = $this->getContent();
		$out = '';
		$anz = count($content);
		if($anz){
			$out .= '<div style="text-align:center" width="100%"><table width="100%" class="default middlefont">';
//		$out .= '<thead>';
//		$out .= '<tr>';
//		$out .= '<th style="width:150px;">';
//		$out .= g_l('logging','[date]');
//		$out .= '</th>';
//		$out .= '<th style="width:100px;">';
//		$out .= g_l('logging','[user]');
//		$out .= '</th>';
//		$out .= '<th style="width:350px;">';
//		$out .= g_l('logging','[logEntry]');
//		$out .= '</th>';
//		$out .= '</tr>';
//		$out .= '</thead>';


			for($i = 0; $i < $anz; $i++){
				$out .= '<tr><td class="bold" style="width:100px;padding:5px 15px 5px 15px;">' .
					g_l('logging', '[date]') . ':</td><td width="200">' .
					date("d.m.y - H:i:s", $content[$i]['timestamp']) . '</td>' .
					'<td width="auto"></td></tr>' .
					'<tr><td class="bold" style="width:100px;padding:5px 15px 5px 15px;">' .
					g_l('logging', '[user]') . ':</td><td width="auto">' .
					f('SELECT Text FROM `' . USER_TABLE . "` WHERE ID=" . intval($content[$i]['userID']), "Text", new DB_WE()) .
					'</td></tr>' .
					'<tr><td class="bold" style="width:100px;padding:5px 15px 5px 15px;">' .
					g_l('logging', '[logEntry]') . ':</td><td width="auto">' .
					$this->showLog($content[$i]['typ'], $content[$i]['ID']) . '</td></tr>' .
					'<tr><td colspan="3" style="padding:5px 15px 5px 15px;"><div id="dataContent_' . $content[$i]['ID'] . '" name="dataContent">' .
					$this->handleData($content[$i]['ID'], 0, self::versionPerPage) . '</div>' .
					'<div style="border-top:1px solid #000;margin-top:20px;margin-bottom:20px;"></div></td></tr>';
			}

			$out .= '</table></div>';
		} else {
			$out = '<div style="text-align:center" width="100%">' . g_l('logging', '[notfound]') . '</div>';
		}

		return $out;
	}

	private function showLog($action, $logID){
		switch($action){
			case we_versions_log::VERSIONS_DELETE:
				$title = g_l('logging', '[versions]') . " " . g_l('logging', '[deleted]');
				break;
			case we_versions_log::VERSIONS_RESET:
				$title = g_l('logging', '[versions]') . " " . g_l('logging', '[reset]');
				break;
			case we_versions_log::VERSIONS_PREFS:
				$title = g_l('logging', '[prefsVersionChanged]');
				break;
		}

		return $title . '.';
	}

	public static function handleData($logId, $start, $anzahl){
		$hash = getHash('SELECT data,typ FROM `' . VERSIONSLOG_TABLE . '` WHERE ID=' . intval($logId), new DB_WE());
		$action = $hash['typ'];
		$data = we_unserialize($hash['data']);

		$out = '';

		switch($action){
			case we_versions_log::VERSIONS_DELETE:
			case we_versions_log::VERSIONS_RESET:

				$out = '<table style="width:100%;border:1px solid #BBBAB9;" class="middlefont">' .
					'<thead><tr class="bold" style="background-color:#dddddd;"><td></td><td>' .
					g_l('logging', '[ID]') . '</td><td>' .
					g_l('logging', '[name]') . '</td><td>' .
					g_l('logging', '[path]') . '</td><td>' .
					g_l('logging', '[version]') . '</td><td>' .
					g_l('logging', '[contenttype]') .
					'</td></tr></thead>';

				$anzGesamt = count($data);

				$orderedArray = [];
				foreach($data as $k => $v){
					$orderedArray[] = $v;
				}

				$showNumber = 0;
				//for($i=$start;$i<$anzahl;$i++) {
				foreach($orderedArray as $k => $v){
					$display = "none";
					$m = $k + 1;
					$name = $logId . '_list';
					if($k >= $start && $k < $anzahl){
						$display = "";
						$showNumber++;
					}
					$out .= '<tr id="' . $name . '" name="' . $name . '" style="display:' . $display . ';">
					<td style="text-align:left">' . $m . '.</td><td style="text-align:left">' .
						$v['documentID'] . '</td><td style="text-align:left">' .
						we_base_util::shortenPath($v['Text'], 18) . '</td><td style="text-align:left">' .
						we_base_util::shortenPath($v['Path'], 40) . '</td><td style="text-align:left">' .
						$v['Version'] . '</td><td style="text-align:left">' .
						$v['ContentType'] . '</td></tr>';
				}
				$out .= '<tr style="background-color:#dddddd;">
				<td style="border-top:1px solid #BBBAB9;padding:3px 5px 3px 3px;text-align:right" colspan="6">
				<span id="startNumber_' . $logId . '">' . ($start + 1) . '</span> - <span id="showNumber_' . $logId . '">' . $showNumber . '</span> <span>' . g_l('logging', '[of]') . '</span> <span style="margin-right:20px;">' . $anzGesamt . '</span>' .
					(($anzGesamt > self::versionPerPage) ? '<span style="margin-right:20px;"><a id="showAll_' . $logId . '" href="#" onclick="showAll(' . $logId . ');">' . g_l('logging', '[all]') . '</a></span>' : '') .
					'<span style="margin-right:5px;"><a title="' . g_l('logging', '[back]') . '" href="#" onclick="back(' . $logId . ');"><i class="fa fa-caret-left" id="back_' . $logId . '" style="display:none;border:2px solid #DDD;"/></a></span>' .
					(($anzGesamt > self::versionPerPage) ? '<span style="margin-right:5px;"><a title="' . g_l('logging', '[next]') . '" href="#" onclick="next(' . $logId . ');"><i class="fa fa-caret-right" id="next_' . $logId . '" style="border:2px solid #DDD;"/></a></span>' : '') .
					we_html_element::htmlHidden("start_" . $logId, $start) . '</td></tr>
				</table>';
				break;
			case we_versions_log::VERSIONS_PREFS:
				$secondsDay = 86400;
				$secondsWeek = 604800;
				$secondsYear = 31449600;

				foreach($data as $k => $v){
					list(, $k) = explode('_', $k, 2);
					switch($k){
						case "image/*":
						case "text/html":
						case "text/webedition":
						case "text/js":
						case "text/css":
						case "text/plain":
						case "text/htaccess":
						case "text/weTmpl"://#4120
						case "application/x-shockwave-flash":
						case "video/quicktime":
						case "application/*":
						case "text/xml":
						case "objectFile":
							$val = g_l('logging', (!empty($v)) ? '[activated]' : '[deactivated]');
							$out .= '-> ' . g_l('logging', '[contenttype]') . " " . g_l('contentTypes', '[' . $k . ']') . ": " . $val;
							break;
						case "time_days":
							$val = (!empty($v) && $v != -1) ? ($v / $secondsDay) : "";
							$out .= '-> ' . g_l('logging', '[zeitraum]') . " " . g_l('logging', '[days]') . ": " . $val;
							break;
						case "time_weeks":
							$val = (!empty($v) && $v != -1) ? ($v / $secondsWeek) : "";
							$out .= '-> ' . g_l('logging', '[zeitraum]') . " " .
								g_l('logging', '[weeks]') . ": " . $val;
							break;
						case "time_years":
							$val = (!empty($v) && $v != -1) ? ($v / $secondsYear) : "";
							$out .= '-> ' . g_l('logging', '[zeitraum]') . " " .
								g_l('logging', '[years]') . ": " . $val;
							break;
						case "anzahl":
							$val = (!empty($v)) ? $v : "";
							$out .= '-> ' . g_l('logging', '[anzahlVersions]') . ": " . $val;
							break;
					}
					$out .= we_html_element::htmlBr();
				}

				$out .= we_html_element::htmlBr();
				break;
		}

		return $out;
	}

	public static function showFrameset(){
		$versionsLogView = new self();

		echo we_html_tools::getHtmlTop(g_l('versions', '[versions_log]'), '', '', YAHOO_FILES .
			$versionsLogView->getJS() .
			we_html_element::cssLink(CSS_DIR . 'messageConsole.css'), we_html_element::htmlBody(['class' => 'weDialogBody messageConsoleWindow'], '
	<div id="headlineDiv">
		<div class="weDialogHeadline">' . g_l('versions', '[versions_log]') . '
		</div>
	</div>
	<div id="versionsDiv">' . $versionsLogView->printContent() . '</div>
	<div class="dialogButtonDiv">
		<div style="position:absolute;top:10px;right:20px;">' . we_html_button::create_button(we_html_button::CLOSE, "javascript:window.close();") . '</div>
	</div>
	')
		);
	}

}
