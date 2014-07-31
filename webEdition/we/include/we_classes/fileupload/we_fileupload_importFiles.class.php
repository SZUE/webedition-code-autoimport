<?php

/**
 * webEdition CMS
 *
 * $Rev: 7705 $
 * $Author: mokraemer $
 * $Date: 2014-06-10 21:46:56 +0200 (Di, 10 Jun 2014) $
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

class we_fileupload_importFiles extends we_fileupload_base {

	private $jsRequirementsOk = false;

	public function __construct($name) {
		parent::__construct($name);
		$this->jsRequirementsOk = we_base_request::_(we_base_request::BOOL, "jsRequirementsOk", false);
		$this->setDimensions(array('width' => 400, 'dragHeight' => 44));
	}

	protected function _getInitJS(){
		return we_html_element::jsElement('
function weFU(){
	var preparedFiles,
		mapFiles,
		totalFiles = 0,
		totalWeight = 0,
		currentWeight,
		currentWeightTag,
		currentFile = null,
		elems,
		chunkSize,
		action;
};
weFU();

		') . we_html_element::jsElement("

//FIXME: move this vars into namespace weFU and get rid of v1 code + adapter to v2
var weUploadFilesClean = new Array(),
	weMapFiles = new Array(),
	weUploadFilesClean = new Array(),
	weTotalFiles = 0,
	weActualFile = -1,
	weTotalWeight = 0,
	weActualWeight = 0;

function upload(){
	var cf = top.imgimportcontent;

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

		weFU.repaintGUI({'what' : 'startUpload'});

		//adapt uploader's state to send logic of weFU allready insertet here
		weFU.preparedFiles = Array();
		for(var i = 0, fileSizeOk = false; i < weUploadFilesClean.length; i++){console.log(weUploadFilesClean[i].size + ' | ' + " . $this->maxUploadSizeBytes . ");
			fileSizeOk = weUploadFilesClean[i].size <= " . $this->maxUploadSizeBytes . ";
			weFU.preparedFiles.push({
				file: weUploadFilesClean[i],
				fileNum: i,
				uploadConditionsOk: fileSizeOk, //we only need to check filesize, not mime type or extension
				error: '',
				dataArray: null,
				currentPos: 0,
				partNum: 0,
				totalParts: 0,
				lastChunkSize: 0,
				currentWeightFile: 0,
				mimePHP: 'none',
				fileNameTemp: ''
			})
		}
		weFU.mapFiles = weMapFiles;
		weFU.chunkSize = " . self::CHUNK_SIZE . " * 1024;
		weFU.totalFiles = weTotalFiles;
		weFU.totalWeight = weTotalWeight;
		weFU.totalChunks = weTotalWeight / weFU.chunkSize;
		weFU.currentWeight = 0;
		weFU.currentWeightTag = 0;
		weFU.elems = [];
		weFU.action = '" . WEBEDITION_DIR . "we_cmd.php';
		setTimeout(function(){weFU.sendNextFile()},100);

		return;
	}
}
		");
	}

	protected function _getSelectorJS(){
		/* move JS from we_import_file.class */
		return '';
	}

	protected function _getSenderJS_core(){
		return parent::_getSenderJS_core();
	}

	protected function _getSenderJS_additional(){
		return we_html_element::jsElement('
weFU.appendMoreData = function(fd){
	var cf = top.imgimportcontent,
		sf = cf.document.we_startform,
		cur = weFU.currentFile;

	fd.append("weFormNum", cur.fileNum+1);
	fd.append("weFormCount", weFU.totalFiles);

	fd.append("we_cmd[0]", "import_files");
	fd.append("cmd", "buttons");
	fd.append("jsRequirementsOk", 1);
	fd.append("step", 1);
	fd.append("importToID", sf.importToID.value);

	' . ((we_base_imageEdit::gd_version() > 0) ? '
	if(cur.partNum === cur.totalParts){
		fd.append("thumbs", sf.thumbs.value);
		fd.append("width", sf.width.value);
		fd.append("height", sf.height.value);
		fd.append("widthSelect", sf.widthSelect.value);
		fd.append("heightSelect", sf.heightSelect.value);
		fd.append("keepRatio", sf.keepRatio.value);
		fd.append("quality", sf.quality.value);
		fd.append("sameName", sf.sameName.value);
		fd.append("degrees", sf.degrees.value);
	}
	' : '') . '

	return fd;
}

weFU.postProcess = function(resp){
	setProgress(100);
	setProgressText("progress_title", "");
	top.opener.top.we_cmd("load","' . FILE_TABLE . '");
	eval(resp.completed);

	//reinitialize some vars to add and upload more files
	weFU.reset();
	weFU.setCancelButtonText("close");
};

weFU.repaintGUI = function(arg){
	var cf = top.imgimportcontent,
		totalDigits = weFU.totalChunks > 1000 ? 2 : (weFU.totalChunks > 100 ? 1 : 0);

	switch(arg.what){
		case "chunkOK":
			var cur = weFU.currentFile,
				i = weFU.mapFiles[cur.fileNum],
				digits = cur.totalParts > 1000 ? 2 : (cur.totalParts > 100 ? 1 : 0);
				fileProg = (100/cur.file.size) * cur.currentWeightFile;
				totalProg = (100/weFU.totalWeight) * weFU.currentWeight;
			//per file progress
			cf._setProgress_uploader(i, fileProg.toFixed(digits));
			//cummulated progress
			if(cur.partNum == 1){
				setProgressText("progress_title", "' . g_l('importFiles', "[do_import]") . " " . g_l('importFiles', "[file]") . ' " + (i+1));
			}
			setProgress(totalProg.toFixed(totalDigits));
			break;
		case "fileOK":
			var i = weFU.mapFiles[weFU.currentFile.fileNum];console.log(weFU.mapFiles);console.log(i);
			cf.document.getElementById("div_upload_files").scrollTop = cf.document.getElementById("div_uploadFiles_" + i).offsetTop - 200;
			cf._setProgressCompleted_uploader(true, i, "");
			break;
		case "chunkNOK":
			var cur = weFU.currentFile,
				i = weFU.mapFiles[cur.fileNum];
				totalProg = (100/weFU.totalWeight) * weFU.currentWeight;
			cf.document.getElementById("div_upload_files").scrollTop = cf.document.getElementById("div_uploadFiles_" + i).offsetTop - 200;
			cf._setProgressCompleted_uploader(false, i, arg.message);
			if(cur.partNum == 1){
				setProgressText("progress_title", "' . g_l('importFiles', "[do_import]") . " " . g_l('importFiles', "[file]") . ' " + (i+1));
			}
			setProgress(totalProg.toFixed(totalDigits));
			break;
		case "startUpload":
			//set buttons state and show initial progress bar
			back_enabled = switch_button_state("back", "back_enabled", "disabled");
			next_enabled = switch_button_state("next", "next_enabled", "disabled");
			document.getElementById("progressbar").style.display = "";
			setProgressText("progress_title", "' . g_l('importFiles', "[do_import]") . " " . g_l('importFiles', "[file]") . ' 1");

			//scroll to top of files list
			cf.document.getElementById("div_upload_files").scrollTop = 0;
			break;
	}
}

weFU.processError = function(arg){
	switch(arg.from){
		case "gui":
			top.we_showMessage(arg.msg, 4, window);
		case "request":
			//weFU.repaintGUI({"what" : "fileNOK"});
			//weFU.reset();
	}
};

weFU.reset = function(){
	var cf = top.imgimportcontent,
		l = cf.weUploadFiles.length;


	for(var i = 0; i < l; i++){
		cf.weUploadFiles[i] = null;
		weFU.preparedFiles[i] = null;
	}

	weFU.mapFiles = new Array();
	weFU.totalFiles = 0;
	weFU.totalWeight = 0;
	weFU.currentWeight = 0;
	weFU.currentWeightTag = 0;
	weFU.currentFile = -1;

	weMapFiles = new Array();
	weUploadFilesClean = new Array();
	weTotalFiles = 0;
	weActualFile = -1;
	weTotalWeight = 0;
	weActualWeight = 0;

	setProgress(0);
	document.getElementById("progressbar").style.display = "none";
}

weFU.cleanAll = function(){
	weFU.reset();
	top.imgimportcontent.weUploadFiles = new Array();
	weFU.preparedFiles = new Array();
}

weFU.setCancelButtonText = function(text){
	var close = "' . g_l('button', '[close][value]') . '",
		cancel = "' . g_l('button', '[cancel][value]') . '",
		replace = (text === "close" ? close : (text === "cancel" ? cancel : ""));

	if(replace){
		document.getElementById("div_cancelButton").getElementsByTagName("td")[1].innerHTML = replace;
	}
}
		');
	}
}