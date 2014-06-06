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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
class we_import_files{

	var $importToID = 0;
	var $step = 0;
	var $sameName = "overwrite";
	var $importMetadata = true;
	var $cmd = "";
	var $thumbs = "";
	var $width = "";
	var $height = "";
	var $widthSelect = "pixel";
	var $heightSelect = "pixel";
	var $keepRatio = 1;
	var $quality = 8;
	var $degrees = 0;
	var $categories = '';
	private $jsRequirementsOk = false;
	private $useLegacyUpload = false; //TODO: replace by FILE_UPLOADER_USE_LEGACY
	private $useJsUpload = false;
	private $maxUploadSizeMB = 8; //TODO: replace by FILE_UPLOADER_MAX_UPLOAD_SIZE
	private $maxUploadSizeB = 0;
	private $fileNameTmp = '';
	private $partNum = 0;
	private $partCount = 0;

	private $showErrorAtChunkNr = -1; //Trigger an Error at n-th chunk of 100KB to demonstrate error response

	function __construct(){
		if(isset($_REQUEST['categories'])){
			$_catarray = makeArrayFromCSV($_REQUEST['categories']);
			$_cats = array();
			foreach($_catarray as $_cat){
				// bugfix Workarround #700
				$_cats[] = (is_numeric($_cat) ?
						$_cat :
						path_to_id($_cat, CATEGORY_TABLE));
			}
			$_REQUEST['categories'] = makeCSVFromArray($_cats);
		}

		$this->categories = weRequest('raw', "categories", $this->categories);
		$this->importToID = weRequest('int', "importToID", $this->importToID);
		$this->sameName = weRequest('raw', "sameName", $this->sameName);
		$this->importMetadata = weRequest('raw', "importMetadata", $this->importMetadata);
		$this->step = weRequest('int', "step", $this->step);
		$this->cmd = weRequest('raw', "cmd", $this->cmd);
		$this->thumbs = weRequest('raw', "thumbs", $this->thumbs);
		$this->width = weRequest('int', "width", $this->width);
		$this->height = weRequest('int', "height", $this->height);
		$this->widthSelect = weRequest('string', "widthSelect", $this->widthSelect);
		$this->heightSelect = weRequest('string', "heightSelect", $this->heightSelect);
		$this->keepRatio = weRequest('bool', "keepRatio", $this->keepRatio);
		$this->quality = weRequest('int', "quality", $this->quality);
		$this->degrees = weRequest('int', "degrees", $this->degrees);
		$this->jsRequirementsOk = weRequest('bool', "jsRequirementsOk", false);
		$this->partNum = weRequest('int', "wePartNum", 0);
		$this->partCount = weRequest('int', "wePartCount", 0);
		$this->fileNameTmp = weRequest('raw', "weFileNameTmp", '');
		$this->maxUploadSizeB = $this->maxUploadSizeMB * 1048576;
		$this->useJsUpload = !getPref('use_jupload') && $this->jsRequirementsOk && !$this->useLegacyUpload;
	}

	function getHTML(){
		switch($this->cmd){
			case "content" :
				return $this->_getContent();
			case "buttons" :
				return $this->_getButtons();
			default :
				return $this->_getFrameset();
		}
	}

	function _getJS($fileinput){
		return we_html_element::jsElement("
function makeArrayFromCSV(csv) {
	if(csv.length && csv.substring(0,1)==\",\"){csv=csv.substring(1,csv.length);}
	if(csv.length && csv.substring(csv.length-1,csv.length)==\",\"){csv=csv.substring(0,csv.length-1);}
	if(csv.length==0){return new Array();}else{return csv.split(/,/);};
}

function inArray(needle,haystack){
	for(var i=0;i<haystack.length;i++){
		if(haystack[i] == needle){return true;}
	}
	return false;
}

function makeCSVFromArray(arr) {
	if(arr.length == 0){return \"\";};
	return \",\"+arr.join(\",\")+\",\";
}
function we_cmd(){
	var args = '';
	var url = '" . WEBEDITION_DIR . "we_cmd.php?'; for(var i = 0; i < arguments.length; i++){ url += 'we_cmd['+i+']='+escape(arguments[i]); if(i < (arguments.length - 1)){ url += '&'; }}

	switch (arguments[0]){
		case 'openDirselector':
			new jsWindow(url,'we_fileselector',-1,-1," . we_selector_file::WINDOW_DIRSELECTOR_WIDTH . "," . we_selector_file::WINDOW_DIRSELECTOR_HEIGHT . ",true,true,true,true);
			break;
		case 'openCatselector':
			new jsWindow(url,'we_catselector',-1,-1," . we_selector_file::WINDOW_CATSELECTOR_WIDTH . "," . we_selector_file::WINDOW_CATSELECTOR_HEIGHT . ",true,true,true,true);
		break;
	}
}" . 'var we_fileinput = \'<form name="we_upload_form_WEFORMNUM" method="post" action="' . WEBEDITION_DIR . 'we_cmd.php" enctype="multipart/form-data" target="imgimportbuttons">' . str_replace("\n", " ", str_replace("\r", " ", $this->_getHiddens("buttons", $this->step + 1))) . $fileinput . '</form>\';

function refreshTree() {
	//FIXME: this won\'t work in current version
	top.opener.top.we_cmd("load","' . FILE_TABLE . '");
}

function uploadFinished() {
	refreshTree();
	' . we_message_reporting::getShowMessageCall(
					g_l('importFiles', "[finished]"), we_message_reporting::WE_MESSAGE_NOTICE) . '
}') .

we_html_element::jsElement($this->useJsUpload ? '
//JS for new HTML5-d&d-Uploader
var weUploadFiles = new Array(),
	weNextTitleNr = 1,
	weFileinput = \'' . str_replace("\n", " ", str_replace("\r", " ", $fileinput)) . '\';

function initFileUpload() {
	var fileselect = document.getElementById("fileselect"),
		filedrag = document.getElementById("filedrag"),
		c = 0,
		bf = top.imgimportbuttons;

	if ((!filedrag || !fileselect) && c < 100){
		if(c < 100){
			setTimeout(function(){initFileUpload()},200);
		} else {
			alert("FileUploader failed beeing initialized.");
		}
		c++;
	}

	fileselect.addEventListener("change", FileSelectHandler, false);
	filedrag.addEventListener("dragover", FileDragHover, false);
	filedrag.addEventListener("dragleave", FileDragHover, false);
	filedrag.addEventListener("drop", FileSelectHandler, false);
	filedrag.style.display = "block";

	bf.next_enabled = bf.switch_button_state("next", "next_enabled", "disabled");
}

function FileDragHover(e) {
	e.stopPropagation();
	e.preventDefault();
	e.target.className = (e.type == "dragover" ? "hover" : "");
}

function FileSelectHandler(e) {
	var added = false,
		bf = top.imgimportbuttons,
		files,
		lastIndex,
		maxSize = ' . $this->maxUploadSizeB . ';

	if(typeof e.dataTransfer !== "undefined"){
			FileDragHover(e);
	}
	files = e.target.files || e.dataTransfer.files;
	lastIndex = weUploadFiles.length;

	for (var i = 0, f; f = files[i]; i++) {

		/*
		//this dows not work, because FileReader and Image work asynchronously
		//we need a call back construction!
		if (f.type.indexOf("image") == 0) {
			var reader = new FileReader();
			reader.onload = function(e) {
				var image = new Image();
				image.onload = function() {
					// access image size here
					console.log(this.naturalWidth + "x" + this.naturalHeight + " | ");
				}
				image.src = e.target.result;
			}
			reader.readAsDataURL(f);
		}
		*/
		if(!_weContains(weUploadFiles, f)){
			if(maxSize === 0 || f.size < maxSize){
				weUploadFiles.push(f);
				added = true;
			} else {
				weUploadFiles.push(null);
			}
			_weAppendRow(f, lastIndex);
			lastIndex++;
		}
	}
	if(added){
		bf.next_enabled = bf.switch_button_state("next", "next_enabled", "enabled");
	}
}

function ReplaceSelectHandler(e){
	//FIXME: the code of this function is redundant: make function of parts of FileSelectHandler()
	var files = e.target.files,
		bf = top.imgimportbuttons,
		added = false,
		maxSize = ' . $this->maxUploadSizeB . ';

	if(files[0] instanceof File && !_weContains(weUploadFiles, files[0])){
		var f = files[0],
			inputId = "fileInput_uploadFiles_",
			index = e.target.id.substring(inputId.length),
			nameField = document.getElementById("name_uploadFiles_" + index),
			sizeField = document.getElementById("size_uploadFiles_" + index);

		weUploadFiles[index] = f
		nameField.value = f.name;
		sizeField.innerHTML = maxSize === 0 || f.size < maxSize ? _weComputeSize(f.size) : \'<span style="color:red">> ' . $this->maxUploadSizeMB . ' MB</span>\';
		added = maxSize === 0 || f.size < maxSize ? true : false;
	}
	if(added){
		bf.next_enabled = bf.switch_button_state("next", "next_enabled", "enabled");
	}
}

function _weContainsFiles(arr){
	for (var i = 0; i < arr.length; i++){
		if(typeof arr[i] === "object" && arr[i] !== null){
			return true;
		}
	}
	return false;
}

function _weContains(a, obj) {
	var i = a.length;
	while (i--) {
		if (a[i] !== null && a[i].name === obj.name) {
			return true;
		}
	}
	return false;
}

function _weComputeSize(size){
	return size = size/1024 > 1023 ? ((size/1024)/1024).toFixed(1) + \' MB\' :
			(size/1024).toFixed(1) + \' KB\';
}

function _weDeleteRow(index,but){
	var prefix = "div_uploadFiles_",
		num = 0,
		z = 1,
		divs = document.getElementsByTagName("DIV"),
		bf = top.imgimportbuttons;

	weUploadFiles[index] = null;
	weDelMultiboxRow(index);

	for(var i = 0; i<divs.length; i++){
		if(divs[i].id.length > prefix.length && divs[i].id.substring(0,prefix.length) == prefix){
			num = divs[i].id.substring(prefix.length,divs[i].id.length);
			var sp = document.getElementById("headline_uploadFiles_"+(num));
			if(sp){
				sp.innerHTML = z;
			}
			z++;
		}
	}
	weNextTitleNr = z;
	if(!_weContainsFiles(weUploadFiles)){
		bf.next_enabled = bf.switch_button_state("next", "next_enabled", "disabled");
	}
}

function _weAppendRow(file, index){
	var fi = weFileinput.replace(/WEFORMNUM/g,index),
		div,
		bf = top.imgimportbuttons,
		maxSize = ' . $this->maxUploadSizeB . ';
		//size = _weComputeSize(file.size);

	fi = fi.replace(/WE_FORM_NUM/g,(weNextTitleNr++));
	fi = fi.replace(/FILENAME/g,(file.name));
	fi = fi.replace(/FILESIZE/g,(maxSize === 0 || file.size < maxSize ? _weComputeSize(file.size) : \'<span style="color:red">> ' . $this->maxUploadSizeMB . ' MB</span>\'));
	weAppendMultiboxRow(fi,"",0,0,0,-1);
	div = document.getElementById("div_upload_files")
	div.scrollTop = div.scrollHeight;
	document.getElementById("fileInput_uploadFiles_" + index).addEventListener("change", ReplaceSelectHandler, false);
	bf.document.getElementById("progressbar").style.display = "none";
	bf._weSetCancelButtonText("cancel");
}

function weClearFileList(){
	var bf = top.imgimportbuttons;

	weUploadFiles = new Array();
	bf._weResetUploader();
	document.getElementById("td_uploadFiles").innerHTML = "";
	bf.next_enabled = bf.switch_button_state("next", "next_enabled", "disabled");
	//switch_button_state("reset", "reset_enabled", "disabled");
}

function _setProgressText_uploader(index,name,text){
	var div = document.getElementById(name + "_" + index);
	div.innerHTML = text;
}

function _setProgress_uploader(index,progress){
	koef = 90 / 100;
	document.images["progress_image_" + index].width=koef*progress;
	document.images["progress_image_bg_" + index].width=(koef*100)-(koef*progress);
	_setProgressText_uploader(index, "progress_text", parseInt(progress) + "%");
}

function _setProgressCompleted_uploader(success, index, message){
	if(success){
		_setProgress_uploader(index, 100);
		document.images["progress_image_" + index].src = "/webEdition/images/fileUpload/balken_gr.gif";
	} else {
		//_setProgressText_uploader(index, "progress_text", "Abbruch");
		document.images["alert_img_" + index].style.visibility  = "visible ";
		document.images["alert_img_" + index].title = message;
		document.images["progress_image_" + index].src = "/webEdition/images/fileUpload/balken_red.gif";
	}
}
': '
function checkFileinput(){
	var prefix =  "trash_";
	var imgs = document.getElementsByTagName("IMG");
	if(document.forms[document.forms.length-1].name.substring(0,14) == "we_upload_form" && document.forms[document.forms.length-1].elements["we_File"].value){
		for(var i = 0; i<imgs.length; i++){
			if(imgs[i].id.length > prefix.length && imgs[i].id.substring(0,prefix.length) == prefix){
					imgs[i].style.display="";
			}
		}
		//weAppendMultiboxRow(we_fileinput.replace(/WEFORMNUM/g,weGetLastMultiboxNr()),\'' . g_l('importFiles', "[file]") . '\' + \' \' + (parseInt(weGetMultiboxLength())),80,1);
		var fi = we_fileinput.replace(/WEFORMNUM/g,weGetLastMultiboxNr());
		fi = fi.replace(/WE_FORM_NUM/g,(document.forms.length));
		weAppendMultiboxRow(fi,"",0,1);
		window.scrollTo(0,1000000);
	}
}

function we_trashButDown(but){
	if(but.src.indexOf("disabled") == -1){
		but.src = "' . BUTTONS_DIR . 'btn_function_trash_down.gif";
	}
}

function we_trashButUp(but){
	if(but.src.indexOf("disabled") == -1){
		but.src = "' . BUTTONS_DIR . 'btn_function_trash.gif";
	}
}

function wedelRow(nr,but){
	if(typeof but.src === "undefined" || but.src.indexOf("disabled") == -1){
		var prefix =  "div_uploadFiles_";
		var num = -1;
		var z = 0;
		weDelMultiboxRow(nr);
		var divs = document.getElementsByTagName("DIV");
		for(var i = 0; i<divs.length; i++){
			if(divs[i].id.length > prefix.length && divs[i].id.substring(0,prefix.length) == prefix){
				num = divs[i].id.substring(prefix.length,divs[i].id.length);
				if(parseInt(num)){
					var sp = document.getElementById("headline_uploadFiles_"+(num-1));
					if(sp){
						sp.innerHTML = z;
					}
				}
				z++;
			}
		}
	}
}

function checkButtons(){
	try{
		if(typeof(document.JUpload)=="undefined"||(typeof(document.JUpload.isActive)!="function")||document.JUpload.isActive()==false){
			checkFileinput();
			window.setTimeout(function(){checkButtons()},1000);
			//recheck
		}else{
			setApplet();
		}
	}catch(e){
		checkFileinput();
		window.setTimeout(function(){checkButtons()},1000);
	}
}

function setApplet() {
	var descDiv = document.getElementById("desc");
	if(descDiv.style.display!="none"){
		var descJUDiv = document.getElementById("descJupload");
		var buttDiv = top.imgimportbuttons.document.getElementById("normButton");
		var buttJUDiv = top.imgimportbuttons.document.getElementById("juButton");

		descDiv.style.display="none";
		buttDiv.style.display="none";
		descJUDiv.style.display="block";
		buttJUDiv.style.display="block";
	}

	//setTimeout("document.JUpload.jsRegisterUploaded(\"refreshTree\");",3000);
}
') .

we_html_element::jsScript(JS_DIR . "windows.js");
	}

	function _getContent(){
		$_funct = 'getStep' . weRequest('int', 'step', 1);

		return $this->$_funct();
	}

	function getStep1(){
		$yuiSuggest = & weSuggest::getInstance();
		$this->loadPropsFromSession();
		unset($_SESSION['weS']['WE_IMPORT_FILES_ERRORs']);

		// create Start Screen ##############################################################################

		$wsA = makeArrayFromCSV(get_def_ws());
		$ws = $wsA ? $wsA[0] : 0;
		$store_id = $this->importToID ? $this->importToID : $ws;

		$path = id_to_path($store_id);
		$wecmdenc1 = we_cmd_enc('document.we_startform.importToID.value');
		$wecmdenc2 = we_cmd_enc('document.we_startform.egal.value');
		$button = we_html_button::create_button('select', "javascript:we_cmd('openDirselector',document.we_startform.importToID.value,'" . FILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','','','0')");

		$yuiSuggest->setAcId('Dir');
		$yuiSuggest->setContentType('folder');
		$yuiSuggest->setInput('egal', $path);
		$yuiSuggest->setLabel(g_l('weClass', '[path]'));
		$yuiSuggest->setMaxResults(20);
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setResult('importToID', $store_id);
		$yuiSuggest->setSelector(weSuggest::DirSelector);
		$yuiSuggest->setWidth(260);
		$yuiSuggest->setSelectButton($button);


		$parts = array(
			array(
				'headline' => g_l('importFiles', "[destination_dir]"),
				'html' =>
				we_html_tools::hidden('we_cmd[0]', 'import_files') . we_html_tools::hidden('cmd', 'content') . we_html_tools::hidden('step', '2') . we_html_tools::hidden('jsRequirementsOk', 0) . // fix for categories require reload!
				we_html_element::htmlHidden(array('name' => 'categories', 'value' => '')) .
				$yuiSuggest->getHTML(),
				'space' => 150
			),
			array(
				'headline' => g_l('importFiles', '[sameName_headline]'),
				'html' =>
				we_html_tools::htmlAlertAttentionBox(g_l('importFiles', "[sameName_expl]"), we_html_tools::TYPE_INFO, 380) .
				we_html_tools::getPixel(200, 10) .
				we_html_forms::radiobutton('overwrite', ($this->sameName == "overwrite"), "sameName", g_l('importFiles', "[sameName_overwrite]")) .
				we_html_forms::radiobutton('rename', ($this->sameName == "rename"), "sameName", g_l('importFiles', "[sameName_rename]")) .
				we_html_forms::radiobutton('nothing', ($this->sameName == "nothing"), "sameName", g_l('importFiles', "[sameName_nothing]")),
				'space' => 150
			),
		);

		// categoryselector


		if(permissionhandler::hasPerm("EDIT_KATEGORIE")){

			$parts[] = array(
				'headline' => g_l('global', "[categorys]") . '',
				'html' => $this->getHTMLCategory(),
				'space' => 150
			);
		}

		if(permissionhandler::hasPerm("NEW_GRAFIK")){
			$parts[] = array(
				'headline' => g_l('importFiles', "[metadata]") . '',
				'html' => we_html_forms::checkboxWithHidden(
					$this->importMetadata == true, 'importMetadata', g_l('importFiles', "[import_metadata]")),
				'space' => 150
			);

			if(we_base_imageEdit::gd_version() > 0){
				$GLOBALS['DB_WE']->query("SELECT ID,Name FROM " . THUMBNAILS_TABLE . " Order By Name");
				$Thselect = g_l('importFiles', "[thumbnails]") . "<br>" . we_html_tools::getPixel(1, 3) . "<br>" . '<select class="defaultfont" name="thumbs_tmp" size="5" multiple style="width: 260px" onchange="this.form.thumbs.value=\'\';for(var i=0;i<this.options.length;i++){if(this.options[i].selected){this.form.thumbs.value +=(this.options[i].value+\',\');}};this.form.thumbs.value=this.form.thumbs.value.replace(/^(.+),$/,\'$1\');">' . "\n";

				$thumbsArray = makeArrayFromCSV($this->thumbs);
				while($GLOBALS['DB_WE']->next_record()){
					$Thselect .= '<option value="' . $GLOBALS['DB_WE']->f("ID") . '"' . (in_array(
							$GLOBALS['DB_WE']->f("ID"), $thumbsArray) ? " selected" : "") . '>' . $GLOBALS['DB_WE']->f("Name") . "</option>\n";
				}
				$Thselect .= '</select><input type="hidden" name="thumbs" value="' . $this->thumbs . '" />' . "\n";

				$parts[] = array(
					"headline" => g_l('importFiles', "[make_thumbs]"),
					"html" => $Thselect,
					"space" => 150
				);

				$widthInput = we_html_tools::htmlTextInput("width", 10, $this->width, "", '', "text", 60);
				$heightInput = we_html_tools::htmlTextInput("height", 10, $this->height, "", '', "text", 60);

				$widthSelect = '<select size="1" class="weSelect" name="widthSelect"><option value="pixel"' . (($this->widthSelect == "pixel") ? ' selected="selected"' : '') . '>' . g_l('weClass', "[pixel]") . '</option><option value="percent"' . (($this->widthSelect == "percent") ? ' selected="selected"' : '') . '>' . g_l('weClass', "[percent]") . '</option></select>';
				$heightSelect = '<select size="1" class="weSelect" name="heightSelect"><option value="pixel"' . (($this->heightSelect == "pixel") ? ' selected="selected"' : '') . '>' . g_l('weClass', "[pixel]") . '</option><option value="percent"' . (($this->heightSelect == "percent") ? ' selected="selected"' : '') . '>' . g_l('weClass', "[percent]") . '</option></select>';

				$ratio_checkbox = we_html_forms::checkbox(1, $this->keepRatio, "keepRatio", g_l('thumbnails', "[ratio]"));

				$_resize = '<table border="0" cellpadding="2" cellspacing="0">
<tr>
	<td class="defaultfont">' . g_l('weClass', "[width]") . ':</td>
	<td>' . $widthInput . '</td>
	<td>' . $widthSelect . '</td>
</tr>
<tr>
	<td class="defaultfont">' . g_l('weClass', "[height]") . ':</td>
	<td>' . $heightInput . '</td>
	<td>' . $heightSelect . '</td>
</tr>
<tr>
	<td colspan="3">' . $ratio_checkbox . '</td>
</tr>
</table>';

				$parts[] = array(
					"headline" => g_l('weClass', "[resize]"), "html" => $_resize, "space" => 150
				);

				$_radio0 = we_html_forms::radiobutton(0, $this->degrees == 0, "degrees", g_l('weClass', "[rotate0]"));
				$_radio180 = we_html_forms::radiobutton(180, $this->degrees == 180, "degrees", g_l('weClass', "[rotate180]"));
				$_radio90l = we_html_forms::radiobutton(90, $this->degrees == 90, "degrees", g_l('weClass', "[rotate90l]"));
				$_radio90r = we_html_forms::radiobutton(270, $this->degrees == 270, "degrees", g_l('weClass', "[rotate90r]"));

				$parts[] = array(
					"headline" => g_l('weClass', "[rotate]"),
					"html" => $_radio0 . $_radio180 . $_radio90l . $_radio90r,
					"space" => 150
				);

				$parts[] = array(
					"headline" => g_l('weClass', "[quality]"),
					"html" => we_base_imageEdit::qualitySelect("quality", $this->quality),
					"space" => 150
				);
			} else {
				$parts[] = array(
					"headline" => "",
					"html" => we_html_tools::htmlAlertAttentionBox(
						g_l('importFiles', "[add_description_nogdlib]"), we_html_tools::TYPE_INFO, ""),
					"space" => 0
				);
			}
			$foldAt = 3;
		} else {
			$foldAt = -1;
		}
		$wepos = weGetCookieVariable("but_weimportfiles");
		$content = we_html_multiIconBox::getJS() .
			we_html_multiIconBox::getHTML(
				"weimportfiles", "99%", $parts, 30, "", $foldAt, g_l('importFiles', "[image_options_open]"), g_l('importFiles', "[image_options_close]"), ($wepos == "down"), g_l('importFiles', "[step1]"));
		$startsrceen = we_html_element::htmlDiv(
				array(
				"id" => "start"
				), we_html_element::htmlForm(
					array(
					"action" => WEBEDITION_DIR . "we_cmd.php",
					"name" => "we_startform",
					"method" => "post"
					), $content));

		$body = we_html_element::htmlBody(array(
				"class" => "weDialogBody"
				), $startsrceen . $yuiSuggest->getYuiCss() . $yuiSuggest->getYuiJs());

		return $this->_getHtmlPage($body, $this->_getJS(''));
	}

	function getStep2(){
		$this->savePropsInSession();

		if(!$this->useJsUpload){
			return $this->getStep2Legacy();
		}

		$css = we_html_element::cssElement('
			#filedrag{
				display: none;
				font-weight: bold;
				text-align: center;
				padding-top: 18px;
				margin: 1em 0;
				color: #555;
				border: 2px dashed #555;
				border-radius: 7px;
				cursor: default;
				height: 44px;
				background-color: white;
			}
			#filedrag.hover{
				color: #00cc00;
				border-color: #00cc00;
				border-style: solid;
				box-shadow: inset 0 3px 4px #888;
				background-color: rgb(243, 247, 255);
			}
			.fileInputWrapper {
				overflow: hidden;
				position: relative;
				cursor: pointer;
				/*Using a background color, but you can use a background image to represent a button*/
				background-color: #DDF;
			}
			.fileInput {
				cursor: pointer;
				height: 100%;
				position:absolute;
				top: 0;
				right: 0;
				width: 286px;
				/*This makes the button huge so that it can be clicked on*/
				/*font-size:50px;*/
			}
			.fileInputHidden {
				/*Opacity settings for all browsers*/
				opacity: 0;
				-moz-opacity: 0;
				filter:progid:DXImageTransform.Microsoft.Alpha(opacity=0)
			}
		');

		// create Second Screen ##############################################################################
		$maxUploadSizeB = $this->maxUploadSizeMB * 1048576;

		$content = we_html_tools::hidden('we_cmd[0]', 'import_files') .
			we_html_tools::hidden('cmd', 'content') . we_html_tools::hidden('step', 2) .
			we_html_element::htmlDiv(array('id' => 'desc'), we_html_tools::htmlAlertAttentionBox(g_l('importFiles', "[import_expl_js]") . '<br/><br/>' . ($this->maxUploadSizeMB == 0 ? g_l('importFiles', "[import_expl_js_no_limit]") : sprintf(g_l('importFiles', "[import_expl_js_limit]"), $this->maxUploadSizeMB)), we_html_tools::TYPE_INFO, 520, false, 20));

		$topParts = array(
			array("headline" => "", "html" => $content, "space" => 0)
		);

		$butBrowse = we_html_button::create_button('browse_harddisk', 'javascript:void(0)', true, 286, 22);
		$butBrowse = str_replace("\n", " ", str_replace("\r", " ", $butBrowse));

		$butReset = we_html_button::create_button('reset', 'javascript:weClearFileList()', true, 100, 22, '', '', false);
		$butReset = str_replace("\n", " ", str_replace("\r", " ", $butReset));

		$fileselect = '
		<div style="float:left;">
		<form id="filechooser" action="" method="" enctype="multipart/form-data">
			<div style="">
				<div class="fileInputWrapper" style="vertical-align: top; display: inline-block; height: 22px;">
					<input class="fileInput fileInputHidden" type="file" id="fileselect" name="fileselect[]" multiple="multiple" />
					' . $butBrowse . '
				</div>
				<div style="vertical-align: top; display: inline-block; height: 22px">
					' . $butReset . '
				</div>
				<div id="filedrag">' . g_l('importFiles', "[dragdrop_text]") . '</div>
			</div>
		</form>
		</div>
		';

		$topParts[] = array("headline" => g_l('importFiles', "[select_files]"), "html" => $fileselect, "space" => 130);

		$butEdit = we_html_button::create_button(
				we_html_button::WE_IMAGE_BUTTON_IDENTIFY . 'edit_edit',
				'javascript:void(0)'
		);
		$butEdit = str_replace("\n", " ", str_replace("\r", " ", $butEdit));

		$butTrash = we_html_button::create_button(
				we_html_button::WE_IMAGE_BUTTON_IDENTIFY . 'btn_function_trash',
				"javascript:_weDeleteRow(WEFORMNUM,this);"
		);
		$butTrash = str_replace("\n", " ", str_replace("\r", " ", $butTrash));

		$fileinput = '
			<table cellspacing="0" cellpadding="0" border="0" width="520"><tbody><tr height="28" width="520">
				<td width="20" valign="bottom"></td>
				<td class="weMultiIconBoxHeadline" width="80" valign="bottom">' . g_l('importFiles', "[file]") . '&nbsp;<span id="headline_uploadFiles_WEFORMNUM">WE_FORM_NUM</span><span style="display:inline-block;width:20px;height:5px;"></span></td>
				<td valign="bottom" width="270"><input id="name_uploadFiles_WEFORMNUM" display:inline-block; type="text" size="' . (we_base_browserDetect::isOpera() ? 34 : 38) . '" readonly="readonly" value="FILENAME" /></td>
				<td width valign="bottom" width="150">
					<div style="display: block" id="div_rowButtons_WEFORMNUM">
						<table cellspacing="0" cellpadding="0" border="0"><tbody><tr width="150">
								<td valign="bottom" width="2"></td>
								<td valign="bottom" width="76"><span id="size_uploadFiles_WEFORMNUM">FILESIZE<span></td>
								<td width="20" valign="bottom" align="middle"><img style="visibility:hidden;" width="14" height="18" src="/webEdition/images/fileUpload/alert.gif" id="notice_img_WEFORMNUM" title=""></td>
								<td valign="bottom" width="27" height="22">
									<div class="fileInputWrapper" style="vertical-align: bottom; display: inline-block; height: 22px; width: 27px;">
										<input class="fileInput fileInputHidden" type="file" id="fileInput_uploadFiles_WEFORMNUM" name="" />
										' . $butEdit . '
									</div>
								</td>
								<td valign="bottom" width="27" align="right" height="22">
									' . $butTrash . '
								</td>
						</tr></tbody></table>
					</div>
					<div style="display: none" id="div_rowProgress_WEFORMNUM">
						<table cellpadding="0" style="border-spacing: 0px;border-style:none;"><tbody><tr>
							<td valign="bottom" width="2"></td>
							<td valign="middle"><img width="0" height="10" src="/webEdition/images/balken.gif" name="progress_image_WEFORMNUM" valign="top"></td>
							<td valign="middle"><img width="90" height="10" src="/webEdition/images/balken_bg.gif" name="progress_image_bg_WEFORMNUM" valign="top"></td>
							<td valign="bottom" width="8"></td>
							<td width="34" class="small" style="color:#006699;font-weight:bold"><div id="progress_text_WEFORMNUM">0%</div></td>
							<td width="14" valign="bottom"><img style="visibility:hidden;" width="14" height="18" src="/webEdition/images/fileUpload/alert.gif" id="alert_img_WEFORMNUM" title=""></td>
						</tr></tbody></table>
					</div>
				<td>
			</tr></tbody></table>
		';
		$contentParts = array();

		$content = we_html_element::htmlDiv(
				array("id" => "forms", "style" => "display:block"),
				(getPref('use_jupload') ? we_html_element::htmlForm(array(
						"name" => "JUploadForm"
						), '') : '') .
					we_html_element::htmlForm(
						array(
						"action" => WEBEDITION_DIR . "we_cmd.php",
						"name" => "we_startform",
						"method" => "post"
						), $this->_getHiddens()) .
					'<div style="overflow:hidden; padding-bottom: 10px">' . we_html_multiIconBox::getHTML("selectFiles", "100%", $topParts, 30, "", -1, "", "", "", g_l('importFiles', "[step2]"), "", 0, "hidden") . '</div>' .
					'<div id="div_upload_files" style="height:310px; width: 100%; overflow:auto">' . we_html_multiIconBox::getHTML("uploadFiles", "100%", $contentParts, 30, "", -1, "", "", "", "") . '</div>'
		);

		$body = we_html_element::htmlBody(
				array(
				"class" => "weDialogBody",
				"onload" => "initFileUpload();"
				), $content);

		$js = $this->_getJS($fileinput, $this->maxUploadSizeMB, $maxUploadSizeB) . we_html_multiIconBox::getDynJS("uploadFiles", 30);
		$head = we_html_tools::getHtmlInnerHead(g_l('import', '[title]')) . STYLESHEET . $css . $js;

		return we_html_element::htmlDocType() . we_html_element::htmlHtml(we_html_element::htmlHead($head) . $body);
	}

	function getStep2Legacy(){
		// create Second Screen ##############################################################################
		$but = we_html_tools::getPixel(10, 22) .
			we_html_element::htmlImg(
				array(
					"src" => IMAGE_DIR . 'button/btn_function_trash.gif',
					"width" => 27,
					"height" => 22,
					"border" => 0,
					"align" => "absmiddle",
					"onMouseDown" => "we_trashButDown(this)",
					"onMouseUp" => "we_trashButUp(this)",
					"onMouseOut" => "we_trashButUp(this)",
					"style" => "display: none;cursor:pointer;",
					"id" => "trash_WEFORMNUM",
					"onclick" => "wedelRow(WEFORMNUM + 1,this)"
		));
		$but = str_replace("\n", " ", str_replace("\r", " ", $but));

		$maxsize = getUploadMaxFilesize(false, $GLOBALS['DB_WE']);
		$maxsize = we_base_file::getHumanFileSize($maxsize, we_base_file::SZ_MB);

		$content = we_html_tools::hidden('we_cmd[0]', 'import_files') .
			we_html_tools::hidden('cmd', 'content') . we_html_tools::hidden('step', 2) .
			we_html_element::htmlDiv(array('id' => 'desc'), we_html_tools::htmlAlertAttentionBox(sprintf(g_l('importFiles', "[import_expl]"), $maxsize), we_html_tools::TYPE_INFO, 520, false)) .
			we_html_element::htmlDiv(array('id' => 'descJupload', 'style' => 'display:none;'), we_html_tools::htmlAlertAttentionBox(sprintf(g_l('importFiles', "[import_expl_jupload]"), $maxsize), we_html_tools::TYPE_INFO, 520, false));

		$parts = array(
			array("headline" => "", "html" => $content, "space" => 0)
		);

		$fileinput = we_html_element::htmlInput(
				array(
					'name' => "we_File",
					'type' => "file",
					'size' => 40,
					'onclick' => "checkFileinput();",
					'onchange' => "checkFileinput();"
			)) . $but;

		$fileinput = '<table><tr><td valign="top" class="weMultiIconBoxHeadline">' . g_l('importFiles', "[file]") . '&nbsp;<span id="headline_uploadFiles_WEFORMNUM">WE_FORM_NUM</span></td><td>' . we_html_tools::getPixel(
				35, 5) . '</td><td>' . $fileinput . '</td></tr></table>';

		$form_content = str_replace("WEFORMNUM", 0, $this->_getHiddens("buttons", $this->step) . str_replace("WE_FORM_NUM", 1, $fileinput));
		$formhtml = we_html_element::htmlForm(
				array(
				"action" => WEBEDITION_DIR . "we_cmd.php",
				"name" => "we_upload_form_0",
				"method" => "post",
				"enctype" => "multipart/form-data",
				"target" => "imgimportbuttons"
				), $form_content);

		// JUpload part0
		if(getPref('use_jupload')){
			$_weju = new we_import_jUpload();
			$formhtml = $_weju->getAppletTag($formhtml, 530, 300);
		}

		$parts[] = array(
			"headline" => '', "html" => $formhtml, "space" => 0
		);

		$content = we_html_element::htmlDiv(
				array("id" => "forms", "style" => "display:block"), (getPref('use_jupload') ? we_html_element::htmlForm(array(
						"name" => "JUploadForm"
						), '') : '') .
				we_html_element::htmlForm(
					array(
					"action" => WEBEDITION_DIR . "we_cmd.php",
					"name" => "we_startform",
					"method" => "post"
					), $this->_getHiddens()) .
				we_html_multiIconBox::getHTML("uploadFiles", "100%", $parts, 30, "", -1, "", "", "", g_l('importFiles', "[step2]"))
		);

		$body = we_html_element::htmlBody(
				array(
				"class" => "weDialogBody",
				//"onMouseMove" => "checkButtons();",
				"onload" => "checkButtons();"
				), $content);

		$js = $this->_getJS($fileinput) . we_html_multiIconBox::getDynJS("uploadFiles", 30);

		return $this->_getHtmlPage($body, $js);
	}

	function getStep3(){

		// create Second Screen ##############################################################################
		$parts = array();

		if(isset($_SESSION['weS']['WE_IMPORT_FILES_ERRORs'])){

			$filelist = "";
			foreach($_SESSION['weS']['WE_IMPORT_FILES_ERRORs'] as $err){
				$filelist .= '- ' . $err["filename"] . ' => ' . $err['error'] . we_html_element::htmlBr();
			}
			unset($_SESSION['weS']['WE_IMPORT_FILES_ERRORs']);

			$parts[] = array(
				'html' => we_html_tools::htmlAlertAttentionBox(sprintf(str_replace('\n', '<br>', g_l('importFiles', '[error]')), $filelist), we_html_tools::TYPE_ALERT, 520, false));
		} else {

			$parts[] = array(
				'html' => we_html_tools::htmlAlertAttentionBox(g_l('importFiles', '[finished]'), we_html_tools::TYPE_INFO, 520, false)
			);
		}

		$content = we_html_element::htmlForm(
				array(
				"action" => WEBEDITION_DIR . "we_cmd.php", "name" => "we_startform", "method" => "post"
				), we_html_element::htmlHidden(array(
					'name' => 'step', 'value' => 3
				)) . we_html_multiIconBox::getHTML(
					"uploadFiles", "100%", $parts, 30, "", -1, "", "", "", g_l('importFiles', "[step3]")))// bugfix 1001
		;

		$body = we_html_element::htmlBody(array(
				"class" => "weDialogBody"
				), $content);
		return $this->_getHtmlPage($body);
	}

	function _getButtons(){
		$formnum = weRequest('int', "weFormNum", 0);
		$formcount = weRequest('int', "weFormCount", 0);

		$bodyAttribs = array("class" => "weDialogButtonsBody", 'style' => 'overflow:hidden;');
		if($this->step == 1){
			$bodyAttribs["onload"] = "next();";
			$error = $this->importFile();
			if(!empty($error)){
				if(!isset($_SESSION['weS']['WE_IMPORT_FILES_ERRORs'])){
					$_SESSION['weS']['WE_IMPORT_FILES_ERRORs'] = array();
				}
				$_SESSION['weS']['WE_IMPORT_FILES_ERRORs'][] = $error;
			}
		}

		if($formcount && $this->useJsUpload){
			$response = array('status' => '', 'tmpName' => '', 'message' => '', 'completed' => '');

			if($this->partNum == $this->partCount){
				//actual file completed
				$response['status'] = empty($error) ? 'success' : 'failure';
				$response['message'] = empty($error) ? '' : g_l('importFiles', "[" . $error['error'] . "]");

				//all files done
				if($formnum == $formcount){
					if(isset($_SESSION['weS']['WE_IMPORT_FILES_ERRORs']) && $formnum != 0){
						$filelist = '';
						foreach($_SESSION['weS']['WE_IMPORT_FILES_ERRORs'] as $err){
							$filelist .= '- ' . $err["filename"] . ' => ' . $err["error"] . '\n';
						}
						unset($_SESSION['weS']['WE_IMPORT_FILES_ERRORs']);
						$response['completed'] = we_message_reporting::getShowMessageCall(sprintf(g_l('importFiles', "[error]"), $filelist), we_message_reporting::WE_MESSAGE_ERROR);
					} else {
						$response['completed'] = we_message_reporting::getShowMessageCall(g_l('importFiles', "[finished]"), we_message_reporting::WE_MESSAGE_NOTICE);
					}
				}
			} else {
				$response['tmpName'] = $this->fileNameTmp;
				//TODO: if we have a message here, we can stop uloading chunks of this file!
				$response['status'] = empty($error) ? 'continue' : 'failure';
				$response['message'] = empty($error) ? '' : g_l('importFiles', "[" . $error['error'] . "]");
			}
			echo json_encode($response);
			exit();
		}

		$cancelButton = we_html_button::create_button("cancel", "javascript:top.close()");
		$closeButton = we_html_button::create_button("close", "javascript:top.close()");
		$progressbar = '';

		$js = we_html_button::create_state_changer(false) . '
var weFormNum = ' . $formnum . ';
var weFormCount = ' . $formcount . ';

var weUploadFilesClean = new Array();
var weMapFiles = new Array();

var weUploadFilesClean = new Array();
var weTotalFiles = 0;
var weActualFile = -1;
var weTotalWeight = 0;
var weActualWeight = 0;

function back() {
	if(top.imgimportcontent.document.we_startform.step.value=="2") {
		top.location.href="' . WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=import&we_cmd[1]=' . we_import_functions::TYPE_LOCAL_FILES . '";
	} else {
		top.location.href="' . WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=import_files&jsRequirementsOk=' . ($this->jsRequirementsOk ? 1 : 0) . '";
	}
}

function weCheckAC(j){
	if(top.imgimportcontent.YAHOO.autocoml){
		feld = top.imgimportcontent.YAHOO.autocoml.checkACFields();
		if(j<30){
			if(feld.running) {
				setTimeout(function(){weCheckAC(j++)},100);
			} else {
				return feld.valid;
			}
		} else {
			return false;
		}
	} else {
		return true;
	}
}
		';

		$js .= (getPref('use_jupload') || !$this->jsRequirementsOk) ? $this->_getJsFnNextLegacy($formcount, $formcount) : "
function next() {
	var cf = top.imgimportcontent;

	if (cf.document.getElementById('start') && top.imgimportcontent.document.getElementById('start').style.display != 'none') {
		" . (permissionhandler::hasPerm('EDIT_KATEGORIE') ? "top.imgimportcontent.selectCategories();" : "") . "
		cf.document.we_startform.jsRequirementsOk.value = " . ($this->jsRequirementsOk ? 1 : 0) . ";
		cf.document.we_startform.submit();
	} else {
		//prepare editor for upload
		if(weActualFile === -1){

			//clean out deleted files from weUploadFiles
			var filesTmp = cf.weUploadFiles;

			for(var i=0; i < filesTmp.length; i++){
				if(typeof filesTmp[i] === 'object' && filesTmp[i] !== null){
					weUploadFilesClean.push(filesTmp[i]);
					weMapFiles.push(i);
					weTotalWeight += filesTmp[i].size;
					cf.document.getElementById('div_rowButtons_' + i).style.display = 'none';
					cf.document.getElementById('div_rowProgress_' + i).style.display = '';
				}
			}
			weTotalFiles = weUploadFilesClean.length;

			//set buttons' state and show initial progress bar
			back_enabled = switch_button_state('back', 'back_enabled', 'disabled');
			next_enabled = switch_button_state('next', 'next_enabled', 'disabled');
			document.getElementById('progressbar').style.display = '';
			setProgressText('progress_title', '" . g_l('importFiles', "[do_import]") . " " . g_l('importFiles', "[file]") . " 1');

			//scroll to top of files' list
			cf.document.getElementById('div_upload_files').scrollTop = 0;

			//let's go
			setTimeout(function(){_weSendNextFile()},100);

			return;
		}
	}
}

function _weSendNextFile(resp){
	var f;

	weActualFile++;
	if(f = weUploadFilesClean.shift()){
		if(f.size <= 100*1024){
			resp = _weSplitFileAndSend(f, false);
		} else {
			var fr = new FileReader();
			fr.onload = function(e){
				resp = _weSplitFileAndSend(f, true, new Uint8Array(e.target.result));
			};
			fr.readAsArrayBuffer(f);
		}
	} else {
		setProgress(100);
		setProgressText('progress_title', '');
		top.opener.top.we_cmd('load','" . FILE_TABLE . "');

		eval(JSON.parse(resp).completed);

		//reinitialize some vars to add and upload more files
		_weResetUploader();
		_weSetCancelButtonText('close');
	}
}

function _weSplitFileAndSend(file, split, dataArray){
	var resp = '',
		cf = top.imgimportcontent;

	if(split){
		var blob,
			p = 0,
			blobSize = 100*1024,
			numBlobs = Math.ceil(dataArray.length / blobSize),
			lastSize = dataArray.length % blobSize;

		for (var i = 0; i < dataArray.length; i += blobSize) {
			blob = new Blob([dataArray.subarray(i, i + blobSize)]);
			p++;
			resp = _weSendPart(blob, file.name, file.size, p === numBlobs ? lastSize : blobSize, p, numBlobs, p === 1 ? '' : JSON.parse(resp).tmpName);

			//if one chunk failed we stop uploading this file
			if(JSON.parse(resp).status === 'failure'){
				break;
			}
		}
	} else {
		resp = _weSendPart(file, file.name, file.size, file.size, 1, 1, '');
	}

	cf.document.getElementById('div_upload_files').scrollTop = cf.document.getElementById('div_uploadFiles_' + weMapFiles[weActualFile]).offsetTop - 200;
	cf._setProgressCompleted_uploader((JSON.parse(resp).status === 'failure' ? false : true), weMapFiles[weActualFile], JSON.parse(resp).message);
	setTimeout(function(){_weSendNextFile(resp)}, 100);
}

function _weSendPart(part, fileName, fileSize, partSize, partNum, totalParts, fileNameTemp){
	var xhr = new XMLHttpRequest(),
		fd = new FormData(),
		cf = top.imgimportcontent,
		sf = cf.document.we_startform;

	fd.append('we_cmd[0]', 'import_files');
	fd.append('cmd', 'buttons');
	fd.append('jsRequirementsOk', 1);
	fd.append('step', 1);
	fd.append('importToID', sf.importToID.value);

	fd.append('weFormNum', weActualFile+1);
	fd.append('weFormCount', weTotalFiles);
	fd.append('wePartNum', partNum);
	fd.append('wePartCount', totalParts);

	fd.append('weFileName', fileName);
	fd.append('weFileSize', fileSize);
	fd.append('weFileNameTmp', fileNameTemp);
	fd.append('we_File', part, fileName);

	" . ((we_base_imageEdit::gd_version() > 0) ? "
	if(partNum === totalParts){
		fd.append('thumbs', sf.thumbs.value);
		fd.append('width', sf.width.value);
		fd.append('height', sf.height.value);
		fd.append('widthSelect', sf.widthSelect.value);
		fd.append('heightSelect', sf.heightSelect.value);
		fd.append('keepRatio', sf.keepRatio.value);
		fd.append('quality', sf.quality.value);
		fd.append('sameName', sf.sameName.value);
		fd.append('degrees', sf.degrees.value);
	}
	" : "") . "

	xhr.open('POST', '" . WEBEDITION_DIR . "we_cmd.php', false);
	xhr.send(fd);

	//code executed when response arrived: manage per file progress bars here!
	weActualWeight += partSize;
	if(partNum == 1){
		setProgressText('progress_title', '" . g_l('importFiles', "[do_import]") . " " . g_l('importFiles', "[file]") ." ' + (weActualFile+1));
	}
	setProgress(parseInt((100 / weTotalWeight) * weActualWeight));
	cf._setProgress_uploader(weMapFiles[weActualFile], (100 / totalParts)*partNum);

	//return response to caller: _weSplitFileAndSend()
	return xhr.responseText;
}

function _weResetUploader(){
	var cf = top.imgimportcontent,
		l = cf.weUploadFiles.length;

	for(var i = 0; i < l; i++){
		cf.weUploadFiles[i] = null;
	}

	weMapFiles = new Array();
	weUploadFilesClean = new Array();
	weTotalFiles = 0;
	weActualFile = -1;
	weTotalWeight = 0;
	weActualWeight = 0;

	document.getElementById('progressbar').style.display = 'none';
}

function _weSetCancelButtonText(text){
	var close = '" . g_l('button', '[close][value]') . "',
		cancel = '" . g_l('button', '[cancel][value]') . "',
		replace = (text === 'close' ? close : (text === 'cancel' ? cancel : ''));

	if(replace){
		document.getElementById('normButton').getElementsByTagName('table')[4].getElementsByTagName('td')[1].innerHTML = replace;
	}
}
";

		$js = we_html_element::jsElement($js);

		$prevButton = we_html_button::create_button("back", "javascript:back();", true, 0, 0, "", "", false);
		$prevButton2 = we_html_button::create_button("back", "javascript:back();", true, 0, 0, "", "", false, false);
		$nextButton = we_html_button::create_button("next", "javascript:next();", true, 0, 0, "", "", $this->step > 0, false);

		$prog = ($formcount == 0) ? 0 : (($this->step == 0) ? 0 : ((int) ((100 / $formcount) * ($formnum + 1))));
		$pb = new we_progressBar($prog);
		$pb->setStudLen(200);
		$pb->addText(sprintf(g_l('importFiles', "[import_file]"), $formnum + 1), 0, "progress_title");
		$progressbar = '<span id="progressbar"' . (($this->step == 0) ? 'style="display:none' : '') . '">' . $pb->getHTML() . '</span>';
		$js .= $pb->getJSCode();

		$prevNextButtons = $prevButton ? we_html_button::create_button_table(array($prevButton, $nextButton)) : null;

		$table = new we_html_table(array(
			"border" => 0, "cellpadding" => 0, "cellspacing" => 0, "width" => "100%"
			), 1, 2);
		$table->setCol(0, 0, null, $progressbar);
		$table->setCol(0, 1, array(
			"align" => "right"
			), we_html_element::htmlDiv(array(
				'id' => 'normButton'
				), we_html_button::position_yes_no_cancel($prevNextButtons, null, $cancelButton, 10, '', array(), 10)) .
			we_html_element::htmlDiv(
				array(
				'id' => 'juButton', 'style' => 'display:none;'
				), we_html_button::position_yes_no_cancel($prevButton2, null, $closeButton, 10, '', array(), 10)));

		if($this->step == 3){
			$table->setCol(0, 0, null, '');
			$table->setCol(0, 1, array("align" => "right"), we_html_element::htmlDiv(array(
					'id' => 'normButton'
					), we_html_button::position_yes_no_cancel($prevButton2, null, $closeButton, 10, '', array(), 10)));
		}

		$content = $table->getHtml();
		$body = we_html_element::htmlBody($bodyAttribs, $content);

		return $this->_getHtmlPage($body, $js);
	}

	function _getJsFnNextLegacy($formnum = 0, $formcount = 0){
		$js = '
function next() {
	if(!weCheckAC(1)){
		return false;
	}
	if (top.imgimportcontent.document.getElementById("start") && top.imgimportcontent.document.getElementById("start").style.display != "none") {
		' . (permissionhandler::hasPerm('EDIT_KATEGORIE') ? 'top.imgimportcontent.selectCategories();' : '') . '
		top.imgimportcontent.document.we_startform.submit();
	} else {
		if(weFormNum == weFormCount && weFormNum != 0){
			document.getElementById("progressbar").style.display = "none";
		';

		if(isset($_SESSION['weS']['WE_IMPORT_FILES_ERRORs']) && $formnum == $formcount && $formnum != 0){
			$filelist = '';
			foreach($_SESSION['weS']['WE_IMPORT_FILES_ERRORs'] as $err){
				$filelist .= '- ' . $err["filename"] . ' => ' . $err["error"] . '\n';
			}
			unset($_SESSION['weS']['WE_IMPORT_FILES_ERRORs']);
			$js .= we_message_reporting::getShowMessageCall(sprintf(g_l('importFiles', "[error]"), $filelist), we_message_reporting::WE_MESSAGE_ERROR);
		} else {
			$js .= we_message_reporting::getShowMessageCall(g_l('importFiles', "[finished]"), we_message_reporting::WE_MESSAGE_NOTICE);
		}

		$js .= "
			top.opener.top.we_cmd('load','" . FILE_TABLE . "');
			top.close();
			return;
		}
		forms = top.imgimportcontent.document.forms;
		var z=0;
		var sameName=top.imgimportcontent.document.we_startform.sameName.value;
		var prefix =  'trash_';
		var imgs = top.imgimportcontent.document.getElementsByTagName('IMG');
		for(var i = 0; i<imgs.length; i++){
			if(imgs[i].id.length > prefix.length && imgs[i].id.substring(0,prefix.length) == prefix){
				imgs[i].src='" . BUTTONS_DIR . "btn_function_trash_dis.gif';
				imgs[i].style.cursor='default';
			}
		}
		for(var i=0; i<forms.length;i++){
			if(forms[i].name.substring(0,14) == 'we_upload_form') {
				if(z == weFormNum && forms[i].we_File.value != ''){
					forms[i].importToID.value = top.imgimportcontent.document.we_startform.importToID.value;" . ((we_base_imageEdit::gd_version() > 0) ? ("
					forms[i].thumbs.value = top.imgimportcontent.document.we_startform.thumbs.value;
					forms[i].width.value = top.imgimportcontent.document.we_startform.width.value;
					forms[i].height.value = top.imgimportcontent.document.we_startform.height.value;
					forms[i].widthSelect.value = top.imgimportcontent.document.we_startform.widthSelect.value;
					forms[i].heightSelect.value = top.imgimportcontent.document.we_startform.heightSelect.value;
					forms[i].keepRatio.value = top.imgimportcontent.document.we_startform.keepRatio.checked ? 1 : 0;
					forms[i].quality.value = top.imgimportcontent.document.we_startform.quality.value;
					for(var n=0;n<top.imgimportcontent.document.we_startform.degrees.length;n++){
						if(top.imgimportcontent.document.we_startform.degrees[n].checked){
							forms[i].degrees.value = top.imgimportcontent.document.we_startform.degrees[n].value;
							break;
						}
					}") : "") . "
					forms[i].sameName.value = sameName;
					forms[i].weFormNum.value = weFormNum + 1;
					forms[i].weFormCount.value = forms.length - 2;
					back_enabled = switch_button_state('back', 'back_enabled', 'disabled');
					next_enabled = switch_button_state('next', 'next_enabled', 'disabled');
					document.getElementById('progressbar').style.display = '';
					forms[i].submit();
					return;
				}
				z++;
			}
		}
	}
}
		";

		return $js;
	}

	function importFile(){
		if(isset($_FILES['we_File']) && strlen($_FILES['we_File']["tmp_name"])){
			$we_ContentType = getContentTypeFromFile($_FILES['we_File']["name"]);
			if(!permissionhandler::hasPerm(we_base_ContentTypes::inst()->getPermission($we_ContentType)) || $this->partNum == $this->showErrorAtChunkNr){
			//if(!permissionhandler::hasPerm(we_base_ContentTypes::inst()->getPermission($we_ContentType))){

				return array(
					'filename' => $_FILES['we_File']['name'], 'error' => 'no_perms'
				);
			}

			// move file
			include (WE_INCLUDES_PATH . 'we_editors/we_init_doc.inc.php');
			$tm = we_base_file::getUniqueId();
			$tempName = TEMP_PATH . $tm;

			if(!@move_uploaded_file($_FILES['we_File']["tmp_name"], $tempName)){
				return array(
					'filename' => $_FILES['we_File']['name'], 'error' => 'move_file_error'
				);
			}

			if($this->partCount > 1){
				if($this->partNum == 1){
					$this->fileNameTmp = $tm;
				} else {
					file_put_contents(TEMP_PATH . $this->fileNameTmp, file_get_contents($tempName), FILE_APPEND);
					unlink($tempName);
				}
			}

			if($this->partCount == 1 || ($this->partCount == $this->partNum)){
				$tempName = $this->partCount > 1 ? TEMP_PATH . $this->fileNameTmp : $tempName;
				$this->fileNameTmp = '';
				$fileSize = weRequest('int', "weFileSize", $_FILES['we_File']['size']);

				// setting Filename, Path ...
				$_fn = we_import_functions::correctFilename($_FILES['we_File']["name"]);
				$matches = array();
				preg_match('#^(.*)(\..+)$#', $_fn, $matches);


				$we_doc->Filename = $matches[1];
				$we_doc->Extension = strtolower($matches[2]);
				if(empty($we_doc->Filename)){
					$we_doc->Filename = $matches[2];
					$we_doc->Extension = '';
				}
				$we_doc->Text = $we_doc->Filename . $we_doc->Extension;
				$we_doc->setParentID($this->importToID);
				$we_doc->Path = $we_doc->getParentPath() . (($we_doc->getParentPath() != '/') ? '/' : '') . $we_doc->Text;

				// if file exists we have to see if we should create a new one or overwrite it!
				if(($file_id = f('SELECT ID FROM ' . FILE_TABLE . ' WHERE Path="' . $GLOBALS['DB_WE']->escape($we_doc->Path) . '"', 'ID', $GLOBALS['DB_WE']))){
					if($this->sameName == 'overwrite'){
						$tmp = $we_doc->ClassName;
						$we_doc = new $tmp();
						$we_doc->initByID($file_id, FILE_TABLE);
					} elseif($this->sameName == "rename"){
						$z = 0;
						$footext = $we_doc->Filename . '_' . $z . $we_doc->Extension;
						while(f('SELECT ID FROM ' . FILE_TABLE . " WHERE Text='" . $GLOBALS['DB_WE']->escape($footext) . "' AND ParentID=" . intval($this->importToID), "ID", $GLOBALS['DB_WE'])){
							$z++;
							$footext = $we_doc->Filename . '_' . $z . $we_doc->Extension;
						}
						$we_doc->Text = $footext;
						$we_doc->Filename = $we_doc->Filename . "_" . $z;
						$we_doc->Path = $we_doc->getParentPath() . (($we_doc->getParentPath() != '/') ? '/' : '') . $we_doc->Text;
					} else {
						return array("filename" => $_FILES['we_File']["name"], 'error' => g_l('importFiles', '[same_name]'));
					}
				}
				// now change the category
				$we_doc->Category = $this->categories;
				if($we_ContentType == we_base_ContentTypes::IMAGE || $we_ContentType == we_base_ContentTypes::FLASH){
					$we_size = $we_doc->getimagesize($tempName);
					if(is_array($we_size) && count($we_size) >= 2){
						$we_doc->setElement("width", $we_size[0], "attrib");
						$we_doc->setElement("height", $we_size[1], "attrib");
						$we_doc->setElement("origwidth", $we_size[0]);
						$we_doc->setElement("origheight", $we_size[1]);
					}
				}
				if($we_doc->Extension == '.pdf'){
					$we_doc->setMetaDataFromFile($tempName);
				}

				$we_doc->setElement('type', $we_ContentType, "attrib");
				$fh = @fopen($tempName, 'rb');
				$fileSize = $fileSize < 1 ? 1 : $fileSize;

				if($fh){
					if($we_doc->isBinary()){
						$we_doc->setElement("data", $tempName);
					} else {
						$foo = explode('/', $_FILES['we_File']["type"]);
						$we_doc->setElement("data", fread($fh, $fileSize), $foo[0]);
					}
					fclose($fh);
				} else {
					//FIXME: fopen uses less memory then gd: gd can fail (and returns 500) even if $fh = true!
					return array('filename' => $_FILES['we_File']['name'], 'error' => g_l('importFiles', '[read_file_error]'));
				}

				$we_doc->setElement('filesize', $fileSize, 'attrib');
				$we_doc->Table = FILE_TABLE;
				$we_doc->Published = time();
				if($we_ContentType == we_base_ContentTypes::IMAGE){
					$we_doc->Thumbs = $this->thumbs;

					$newWidth = 0;
					$newHeight = 0;
					if($this->width){
						$newWidth = ($this->widthSelect == 'percent' ?
								round(($we_doc->getElement("origwidth") / 100) * $this->width) :
								$this->width);
					}
					if($this->height){
						$newHeight = ($this->widthSelect == 'percent' ?
								round(($we_doc->getElement("origheight") / 100) * $this->height) :
								$this->height);
					}
					if(($newWidth && ($newWidth != $we_doc->getElement("origwidth"))) || ($newHeight && ($newHeight != $we_doc->getElement("origheight")))){

						if($we_doc->resizeImage($newWidth, $newHeight, $this->quality, $this->keepRatio)){
							$this->width = $newWidth;
							$this->height = $newHeight;
						}
					}

					if($this->degrees){
						$we_doc->rotateImage(
							($this->degrees % 180 == 0) ? $we_doc->getElement('origwidth') : $we_doc->getElement(
									"origheight"), ($this->degrees % 180 == 0) ? $we_doc->getElement("origheight") : $we_doc->getElement(
									"origwidth"), $this->degrees, $this->quality);
					}
					$we_doc->DocChanged = true;
				}
				if(!$we_doc->we_save()){
					return array('filename' => $_FILES['we_File']["name"], "error" => g_l('importFiles', '[save_error]'));
				}
				if($we_ContentType == we_base_ContentTypes::IMAGE && $this->importMetadata){
					$we_doc->importMetaData();
					$we_doc->we_save();
				}
				if(!$we_doc->we_publish()){
					return array("filename" => $_FILES['we_File']["name"], "error" => "publish_error"
					);
				}
				if($we_ContentType == we_base_ContentTypes::IMAGE && $this->importMetadata){
					$we_doc->importMetaData();
				}
			}
			return array();
		} else {
			return array("filename" => $_FILES['we_File']["name"], "error" => g_l('importFiles', '[php_error]'));
		}
	}

	function _getHiddens(){
		return we_html_element::htmlHidden(array("name" => "we_cmd[0]", "value" => "import_files")) .
			we_html_element::htmlHidden(array("name" => "cmd", "value" => "buttons")) .
			we_html_element::htmlHidden(array("name" => "step", "value" => 1)) .
			we_html_element::htmlHidden(array("name" => "weFormNum", "value" => 0)) .
			we_html_element::htmlHidden(array("name" => "weFormCount", "value" => 0)) .
			we_html_element::htmlHidden(array("name" => "importToID", "value" => $this->importToID)) .
			we_html_element::htmlHidden(array("name" => "sameName", "value" => $this->sameName)) .
			we_html_element::htmlHidden(array("name" => "thumbs", "value" => $this->thumbs)) .
			we_html_element::htmlHidden(array("name" => "width", "value" => $this->width)) .
			we_html_element::htmlHidden(array("name" => "height", "value" => $this->height)) .
			we_html_element::htmlHidden(array("name" => "widthSelect", "value" => $this->widthSelect)) .
			we_html_element::htmlHidden(array("name" => "heightSelect", "value" => $this->heightSelect)) .
			we_html_element::htmlHidden(array("name" => "keepRatio", "value" => $this->keepRatio)) .
			we_html_element::htmlHidden(array("name" => "degrees", "value" => $this->degrees)) .
			we_html_element::htmlHidden(array("name" => "quality", "value" => $this->quality)) .
			we_html_element::htmlHidden(array("name" => "categories", "value" => $this->categories));
	}

	function _getFrameset(){
		$_step = weRequest('int','step',-1);

		// set and return html code
		$body = we_html_element::htmlBody(array('style' => 'background-color:grey;margin: 0px;position:fixed;top:0px;left:0px;right:0px;bottom:0px;border:0px none;')
				, we_html_element::htmlDiv(array('style' => 'position:absolute;top:0px;bottom:0px;left:0px;right:0px;')
					, we_html_element::htmlIFrame('imgimportcontent', WEBEDITION_DIR . "we_cmd.php?we_cmd[0]=import_files&cmd=content&jsRequirementsOk=" . ($this->jsRequirementsOk ? 1 : 0) . ($_step > -1 ? '&step=' . $_step : ''), 'position:absolute;top:0px;bottom:40px;left:0px;right:0px;overflow: auto') .
					we_html_element::htmlIFrame('imgimportbuttons', WEBEDITION_DIR . "we_cmd.php?we_cmd[0]=import_files&cmd=buttons&jsRequirementsOk=" . ($this->jsRequirementsOk ? 1 : 0) . ($_step > -1 ? '&step=' . $_step : ''), 'position:absolute;bottom:0px;height:40px;left:0px;right:0px;overflow: hidden;')
		));

		return $this->_getHtmlPage($body);
	}

	function _getHtmlPage($body, $js = ""){
		//$yuiSuggest = & weSuggest::getInstance();
		$head = we_html_tools::getHtmlInnerHead(g_l('import', '[title]')) . STYLESHEET . $js .
			weSuggest::getYuiFiles();
		return we_html_element::htmlDocType() . we_html_element::htmlHtml(we_html_element::htmlHead($head) . $body);
	}

	function getHTMLCategory(){
		$_width_size = 300;

		$addbut = we_html_button::create_button(
				"add", "javascript:we_cmd('openCatselector',0,'" . CATEGORY_TABLE . "','','','fillIDs();opener.addCat(top.allPaths);')");
		$del_but = addslashes(
			we_html_element::htmlImg(
				array(
					'src' => BUTTONS_DIR . 'btn_function_trash.gif',
					'onclick' => 'javascript:#####placeHolder#####;',
					'style' => 'cursor: pointer; width: 27px;'
		)));

		$js = we_html_element::jsScript(JS_DIR . 'utils/multi_edit.js');

		$variant_js = '
			var categories_edit = new multi_edit("categoriesDiv",document.we_startform,0,"' . $del_but . '",' . ($_width_size - 10) . ',false);
			categories_edit.addVariant();';

		$_cats = makeArrayFromCSV($this->categories);
		if(is_array($_cats)){
			foreach($_cats as $cat){
				$variant_js .='
categories_edit.addItem();
categories_edit.setItem(0,(categories_edit.itemCount-1),"' . id_to_path($cat, CATEGORY_TABLE) . '");';
			}
		}

		$variant_js .= 'categories_edit.showVariant(0);';

		$js .= we_html_element::jsElement($variant_js);

		$table = new we_html_table(
			array(
			'id' => 'CategoriesBlock',
			'style' => 'display: block;',
			'cellpadding' => 0,
			'cellspacing' => 0,
			'border' => 0
			), 4, 1);

		$table->setColContent(0, 0, we_html_tools::getPixel(5, 5));
		$table->setColContent(
			1, 0, we_html_element::htmlDiv(
				array(
					'id' => 'categoriesDiv',
					'class' => 'blockWrapper',
					'style' => 'width: ' . ($_width_size) . 'px; height: 60px; border: #AAAAAA solid 1px;'
		)));
		$table->setColContent(2, 0, we_html_tools::getPixel(5, 5));
		$table->setCol(
			3, 0, array(
			'colspan' => 2, 'align' => 'right'
			), we_html_button::create_button_table(
				array(
					we_html_button::create_button("delete_all", "javascript:removeAllCats()"), $addbut
		)));

		return $table->getHtml() . $js . we_html_element::jsElement('
function removeAllCats(){
	if(categories_edit.itemCount>0){
		while(categories_edit.itemCount>0){
			categories_edit.delItem(categories_edit.itemCount);
		}
		categories_edit.showVariant(0);
	}
}

function addCat(paths){
	var path = paths.split(",");
	for (var i = 0; i < path.length; i++) {
		if(path[i]!="") {
			categories_edit.addItem();
			categories_edit.setItem(0,(categories_edit.itemCount-1),path[i]);
		}
	}
	categories_edit.showVariant(0);
}

function selectCategories() {
	var cats = new Array();
	for(var i=0;i<categories_edit.itemCount;i++){
		cats.push(categories_edit.form.elements[categories_edit.name+"_variant0_"+categories_edit.name+"_item"+i].value);
	}
	categories_edit.form.categories.value=makeCSVFromArray(cats);
}');
	}

	function savePropsInSession(){
		$_SESSION['weS']['_we_import_files'] = array();
		$_vars = get_object_vars($this);
		foreach($_vars as $_name => $_value){
			$_SESSION['weS']['_we_import_files'][$_name] = $_value;
		}
	}

	function loadPropsFromSession(){
		if(isset($_SESSION['weS']['_we_import_files'])){
			foreach($_SESSION['weS']['_we_import_files'] as $_name => $_var){
				$this->$_name = $_var;
			}
		}
	}

}
