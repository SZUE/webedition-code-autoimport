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
abstract class we_import_wizardBase{
	var $path = '';
	public $fileUploader = null;

	protected function __construct(){
		$this->path = WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=import';
	}

	public function getHTML($what, $type, $step, $mode){
		switch($what){
			case "wizframeset":
				return $this->getWizFrameset();
			case "wizbody":
				return $this->getWizBody($type, $step, $mode);
			case "wizbusy":
				return $this->getWizBusy();
			case "wizcmd":
				return $this->getWizCmd();
		}
	}

	private function getWizFrameset(){
		$args = 'pnt=wizbody' .
			(($cmd1 = we_base_request::_(we_base_request::STRING, 'we_cmd', false, 1)) ? '&we_cmd[1]=' . $cmd1 : '');

		$body = we_html_element::htmlBody(array('id' => 'weMainBody', "onload" => "wiz_next('wizbody', '" . $this->path . '&' . $args . "');")
				, we_html_element::htmlDiv(array('style' => 'position:absolute;top:0px;bottom:0px;left:0px;right:0px;')
					, we_html_element::htmlIFrame('wizbody', "about:blank", 'position:absolute;top:0px;bottom:40px;left:0px;right:0px;') .
					we_html_element::htmlIFrame('wizbusy', "about:blank", 'position:absolute;height:40px;bottom:0px;left:0px;right:0px;overflow: hidden;', '', '', false) .
					we_html_element::htmlIFrame('wizcmd', $this->path . "&pnt=wizcmd", 'position:absolute;bottom:0px;height:0px;left:0px;right:0px;overflow: hidden;')
		));

		return we_html_tools::getHtmlTop(g_l('import', '[title]'), '', '', we_html_element::jsScript(LIB_DIR . 'additional/yui/yahoo-min.js') .
				we_html_element::jsScript(LIB_DIR . 'additional/yui/event-min.js') .
				we_html_element::jsScript(LIB_DIR . 'additional/yui/json-min.js') .
				we_html_element::jsScript(LIB_DIR . 'additional/yui/connection-min.js') .
				we_html_element::jsElement("
var tables = {
	OBJECT_TABLE: '" . (defined('OBJECT_TABLE') ? OBJECT_TABLE : 'OBJECT_TABLE') . "'
};
var path='" . $this->path . "';") .
				we_html_element::jsScript(JS_DIR . 'import_wizardBase.js') .
				STYLESHEET, $body
		);
	}

	private function getWizBody($type = '', $step = 0, $mode = 0){
		// FIXME: probably obsolete
		$continue = true;
		if($this->fileUploader){
//				$continue = $this->fileUploader->processFileRequest();
		}

		if($continue){
			$a = array(
				'name' => 'we_form'
			);
			if($type == we_import_functions::TYPE_GENERIC_XML && $step == 1){
				$a["onsubmit"] = 'return false;';
			}
			if($step == 1){
				$a["enctype"] = 'multipart/form-data';
			}
			$_step = 'get' . $type . 'Step' . $step;
			list($js, $content) = $this->$_step();
			$doOnLoad = !we_base_request::_(we_base_request::BOOL, 'noload');
			return we_html_tools::getHtmlTop('', '', '',
						STYLESHEET .
						($this->fileUploader ? $this->fileUploader->getCss() . $this->fileUploader->getJs() : '') .
						we_html_element::jsElement($js), we_html_element::htmlBody(array(
						"class" => "weDialogBody",
						"onload" => $doOnLoad ? "parent.wiz_next('wizbusy', '" . $this->path . "&pnt=wizbusy&mode=" . $mode . "&type=" . (we_base_request::_(we_base_request::RAW, 'type', '')) . "'); self.focus();" : "if(set_button_state) set_button_state();"
						), we_html_element::htmlForm($a, we_html_element::htmlHiddens(array(
								"pnt" => "wizbody",
								"type" => $type,
								"v[type]" => $type,
								"step" => $step,
								"mode" => $mode,
								"button_state" => 0)) .
							$content
						)
					)
			);
		}
	}

	private function getWizBusy(){
		if(we_base_request::_(we_base_request::INT, "mode") == 1){
			$WE_PB = new we_progressBar(0, true);
			$WE_PB->setStudLen(200);
			$WE_PB->addText($text = g_l('import', '[import_progress]'), 0, "pb1");
			$pb = $WE_PB->getJSCode() .
				we_html_element::htmlDiv(array('id' => 'progress'), $WE_PB->getHTML());
			$js = we_html_element::jsElement('
function finish(rebuild) {
	var std = top.wizbusy.document.getElementById("standardDiv");
	if(std!==undefined){
		std.style.display = "none";
	}
	var cls = top.wizbusy.document.getElementById("closeDiv");
	if(cls!==undefined){
		 cls.style.display = "block";
	}
	if(rebuild) {
		top.opener.top.openWindow(WE().consts.dirs.WEBEDITION_DIR+"we_cmd.php?we_cmd[0]=rebuild&step=2&btype=rebuild_all&responseText=' . g_l('import', '[finished_success]') . '","rebuildwin",-1,-1,600,130,0,true);
	}
}

top.wizcmd.cycle();
top.wizcmd.we_import(1,-2' . ((we_base_request::_(we_base_request::STRING, 'type') == we_import_functions::TYPE_WE_XML) ? ',1' : '') . ');'
			);
		} else {
			$pb = $js = '';
		}

		$cancelButton = we_html_button::create_button(we_html_button::CANCEL, "javascript:parent.wizbody.handle_event('cancel');", false, 0, 0, '', '', false, false);
		$prevButton = we_html_button::create_button(we_html_button::BACK, "javascript:parent.wizbody.handle_event('previous');", true, 0, 0, "", "", true, false);
		$nextButton = we_html_button::create_button(we_html_button::NEXT, "javascript:parent.wizbody.handle_event('next');", true, 0, 0, "", "", false, false, '_btn');
		$closeButton = we_html_button::create_button(we_html_button::CLOSE, "javascript:parent.wizbody.handle_event('cancel');", true, 0, 0, "", "", false, false);

		$prevNextButtons = $prevButton ? $prevButton . $nextButton : null;

		$content = new we_html_table(array('class' => 'default', "width" => "100%"), 1, 2);
		$content->setCol(0, 0, null, $pb);
		$content->setCol(0, 1, array("style" => "text-align:right"), '
<div id="standardDiv">' . we_html_button::position_yes_no_cancel($prevNextButtons, null, $cancelButton, 10, "", array(), 10) . '</div>
<div id="closeDiv" style="display:none;">' . $closeButton . '</div>'
		);

		echo we_html_tools::getHtmlTop('', '', '', STYLESHEET, we_html_element::htmlBody(array(
				"class" => "weDialogButtonsBody",
				"onload" => "top.wizbody.set_button_state();",
				'style' => 'overflow:hidden;'
				), $content->getHtml() . $js
			)
		);
	}

	private function getWizCmd($type = 'normal'){
		$out = '';
		$mode = we_base_request::_(we_base_request::INT, 'mode', 0);
		$v = we_base_request::_(we_base_request::STRING, 'v');
		$v["import_ChangeEncoding"] = isset($v["import_ChangeEncoding"]) ? $v["import_ChangeEncoding"] : 0;
		$v["import_XMLencoding"] = isset($v["import_XMLencoding"]) ? $v["import_XMLencoding"] : '';
		$v["import_TARGETencoding"] = isset($v["import_TARGETencoding"]) ? $v["import_TARGETencoding"] : '';

		if(isset($v["mode"]) && $v["mode"] == 1){
			$records = we_base_request::_(we_base_request::RAW, "records", array());
			$we_flds = we_base_request::_(we_base_request::RAW, "we_flds", array());
			$attrs = we_base_request::_(we_base_request::RAW, 'attrs', array());
			$attributes = we_base_request::_(we_base_request::RAW, 'attributes', array());

			switch($v['cid']){
				case -2:
					$h = $this->getHdns('v', $v);
					if($v["type"] != "" && $v["type"] != we_import_functions::TYPE_WE_XML){
						$h.=$this->getHdns("records", $records) .
							$this->getHdns("we_flds", $we_flds);
					}
					if($v["type"] == we_import_functions::TYPE_GENERIC_XML){
						$h.=$this->getHdns("attributes", $attributes) .
							$this->getHdns("attrs", $attrs);
					}

					$JScript = 'top.wizbusy.setProgressText("pb1","' . g_l('import', '[prepare_progress]') . '");';


					$out .= we_html_element::htmlForm(array("name" => "we_form"), $h) .
						we_html_element::jsElement($JScript . 'setTimeout(function(){we_import(1,-1);},15);');
					break;

				case -1:
					switch($v["type"]){
						case we_import_functions::TYPE_WE_XML:

							echo we_html_element::jsElement('
if (top.wizbody && top.wizbody.addLog){
	top.wizbody.addLog("");
	top.wizbody.addLog("' . we_html_element::htmlB(g_l('import', '[start_import]') . ' - ' . date("d.m.Y H:i:s")) . '");
	top.wizbody.addLog("' . we_html_element::htmlB(g_l('import', '[prepare]')) . '");
	top.wizbody.addLog("' . we_html_element::htmlB(g_l('import', '[import]')) . '");
}');
							flush();

							$path = TEMP_PATH . we_base_file::getUniqueId() . '/';
							we_base_file::createLocalFolder($path);

							if(is_dir($path)){
								$num_files = we_exim_XMLImport::splitFile($_SERVER['DOCUMENT_ROOT'] . $v['import_from'], $path, 1);
								++$num_files;
							}
							break;
						case we_import_functions::TYPE_GENERIC_XML:
							$parse = new we_xml_splitFile($_SERVER['DOCUMENT_ROOT'] . $v["import_from"]);
							$parse->splitFile("*/" . $v["rcd"], (isset($v["from_elem"])) ? $v["from_elem"] : false, (isset($v["to_elem"])) ? $v["to_elem"] : false, 1);
							break;
						case we_import_functions::TYPE_CSV:
							switch($v['csv_enclosed']){
								case 'double_quote':
									$encl = '"';
									break;
								case 'single_quote':
									$encl = "'";
									break;
								case 'none':
									$encl = '';
									break;
							}
							$cp = new we_import_CSV;
							$cp->setFile($_SERVER['DOCUMENT_ROOT'] . $v['import_from']);
							$del = ($v['csv_seperator'] != "\\t") ? (($v['csv_seperator'] != '') ? $v['csv_seperator'] : ' ') : '	';
							$cp->setDelim($del);
							$cp->setEnclosure($encl);
							$cp->parseCSV();
							$num_files = 0;
							$unique_id = we_base_file::getUniqueId(); // #6590, changed from: uniqid(microtime())

							$path = TEMP_PATH . $unique_id;
							we_base_file::createLocalFolder($path);

							if($cp->isOK()){
								$fieldnames = ($v['csv_fieldnames']) ? 0 : 1;
								$num_rows = $cp->CSVNumRows();
								$num_fields = $cp->CSVNumFields();

								for($i = 0; $i < $num_rows + $fieldnames; $i++){
									$d[0] = $d[1] = '';
									for($j = 0; $j < $num_fields; $j++){
										$d[1] .= (!$fieldnames ?
												(($cp->CSVFieldName($j) != "") ?
													$encl . str_replace($encl, "\\" . $encl, $cp->CSVFieldName($j)) . $encl :
													'') :
												$encl . 'f_' . $j . $encl);
										$d[0] .= ($fieldnames && $i == 0) ?
											(($cp->CSVFieldName($j) != '') ? $encl . str_replace($encl, "\\" . $encl, $cp->CSVFieldName($j)) . $encl : "") :
											(($cp->Fields[(!$fieldnames) ? $i : ($i - 1)][$j] != "") ?
												$encl . str_replace($encl, "\\" . $encl, $cp->Fields[(!$fieldnames) ? $i : ($i - 1)][$j]) . $encl : "");
										if($j + 1 < $num_fields){
											$d[1] .= $del;
											$d[0] .= $del;
										}
									}
									we_base_file::save($path . '/temp_' . $i . '.csv', implode("\n", $d), 'wb');
									$num_files++;
								}
							}
							break;
					}

					$h = $this->getHdns("v", $v);
					if($v["type"] != we_import_functions::TYPE_WE_XML){
						$h.=$this->getHdns("records", $records) . $this->getHdns("we_flds", $we_flds);
					}
					if($v["type"] == we_import_functions::TYPE_GENERIC_XML){
						$h.=$this->getHdns("attributes", $attributes) . $this->getHdns("attrs", $attrs);
					}
					$h .= we_html_element::htmlHiddens(array(
							"v[numFiles]" => ($v["type"] != we_import_functions::TYPE_GENERIC_XML) ? $num_files : $parse->fileId,
							"v[uniquePath]" => ($v["type"] != we_import_functions::TYPE_GENERIC_XML) ? $path : $parse->path));

					$out .= we_html_element::htmlForm(array("name" => "we_form"), $h) . we_html_element::jsElement(
							"setTimeout(function(){we_import(1,0);},15);");
					break;

				case $v['numFiles']:
					$out.=self::importFinished($v, $type);
					break;
				default:
					$fields = array();
					switch($v["type"]){
						case we_import_functions::TYPE_WE_XML:
							$hiddens = $this->getHdns("v", $v);

							if(intval($v['cid']) == 0){
								// clear session data
								we_exim_XMLExIm::unsetPerserves();
							}

							$ref = false;
							if($v["cid"] >= $v["numFiles"] - 1){ // finish import
								$xmlExIm = new we_import_updater();
								$xmlExIm->loadPerserves();
								$xmlExIm->setOptions(array(
									'handle_documents' => $v['import_docs'],
									'handle_templates' => $v['import_templ'],
									'handle_objects' => isset($v['import_objs']) ? $v['import_objs'] : 0,
									'handle_classes' => isset($v['import_classes']) ? $v['import_classes'] : 0,
									'handle_doctypes' => $v['import_dt'],
									'handle_categorys' => $v['import_ct'],
									'handle_binarys' => $v['import_binarys'],
									'document_path' => $v['doc_dir_id'],
									'template_path' => $v['tpl_dir_id'],
									'handle_collision' => $v['collision'],
									'restore_doc_path' => $v['restore_doc_path'],
									'restore_tpl_path' => $v['restore_tpl_path'],
									'handle_owners' => $v['import_owners'],
									'owners_overwrite' => $v['owners_overwrite'],
									'owners_overwrite_id' => $v['owners_overwrite_id'],
									'handle_navigation' => $v['import_navigation'],
									'navigation_path' => $v['navigation_dir_id'],
									'handle_thumbnails' => $v['import_thumbnails'],
									'change_encoding' => $v['import_ChangeEncoding'],
									'xml_encoding' => $v['import_XMLencoding'],
									'target_encoding' => $v['import_TARGETencoding'],
									'rebuild' => $v['rebuild']
								));

								if($xmlExIm->RefTable->current == 0){
									echo we_html_element::jsElement('
if (top.wizbody.addLog){
	top.wizbody.addLog("' . we_html_element::htmlB(g_l('import', '[update_links]')) . '");
}');
									flush();
								}

								$ref = null;

								while(($ref = $xmlExIm->RefTable->getNext()) !== null){
									if(isset($ref->ContentType) && isset($ref->ID)){
										$doc = we_exim_contentProvider::getInstance($ref->ContentType, $ref->ID, $ref->Table);
										$xmlExIm->updateObject($doc);
									}
								}

								if($ref){
									$xmlExIm->savePerserves();

									$JScript = "top.wizbusy.setProgressText('pb1','" . g_l('import', '[update_links]') . $xmlExIm->RefTable->current . '/' . count($xmlExIm->RefTable->Storage) . "');
										top.wizbusy.setProgress(Math.floor(((" . (int) ($v['cid'] + $xmlExIm->RefTable->current) . "+1)/" . (int) ($xmlExIm->RefTable->getLastCount() + $v["numFiles"]) . ")*100));";


									$out .= we_html_element::htmlForm(array("name" => "we_form"), $hiddens .
											we_html_element::jsElement($JScript . "setTimeout(function(){we_import(1," . $v['cid'] . ");},15);"));
								} else {

									$JScript = "
top.wizbusy.finish(" . $xmlExIm->options['rebuild'] . ");
setTimeout(function(){we_import(1," . $v['numFiles'] . ");},15);";
								}
								$out .= we_html_element::htmlForm(array("name" => "we_form"), $hiddens . we_html_element::jsElement($JScript));

								$xmlExIm->unsetPerserves();
							} else { // do import
								$xmlExIm = new we_exim_XMLImport();
								$chunk = $v["uniquePath"] . basename($v["import_from"]) . "_" . $v["cid"];
								if(file_exists($chunk)){
									$xmlExIm->loadPerserves();
									$xmlExIm->setOptions(array(
										'handle_documents' => $v['import_docs'],
										'handle_templates' => $v['import_templ'],
										'handle_objects' => isset($v['import_objs']) ? $v['import_objs'] : 0,
										'handle_classes' => isset($v['import_classes']) ? $v['import_classes'] : 0,
										'handle_doctypes' => $v['import_dt'],
										'handle_categorys' => $v['import_ct'],
										'handle_binarys' => $v['import_binarys'],
										'document_path' => $v['doc_dir_id'],
										'template_path' => $v['tpl_dir_id'],
										'handle_collision' => $v['collision'],
										'restore_doc_path' => $v['restore_doc_path'],
										'restore_tpl_path' => $v['restore_tpl_path'],
										'handle_owners' => $v['import_owners'],
										'owners_overwrite' => $v['owners_overwrite'],
										'owners_overwrite_id' => $v['owners_overwrite_id'],
										'handle_navigation' => $v['import_navigation'],
										'navigation_path' => $v['navigation_dir_id'],
										'handle_thumbnails' => $v['import_thumbnails'],
										'change_encoding' => $v['import_ChangeEncoding'],
										'xml_encoding' => $v['import_XMLencoding'],
										'target_encoding' => $v['import_TARGETencoding'],
										'rebuild' => $v['rebuild']
									));
									$imported = $xmlExIm->import($chunk);
									$xmlExIm->savePerserves();
									$ref = $xmlExIm->RefTable->getLast();
									if($imported){
										$_status = g_l('import', '[import]');

										switch($ref->ContentType){
											case 'weBinary':
											case 'category':
											case 'objectFile':
												$_path_info = $ref->Path;
												break;
											case 'doctype':
												$_path_info = f('SELECT DocType FROM ' . escape_sql_query($ref->Table) . ' WHERE ID=' . intval($ref->ID), '', new DB_WE());
												break;
											case 'weNavigationRule':
												$_path_info = f('SELECT NavigationName FROM ' . escape_sql_query($ref->Table) . ' WHERE ID=' . intval($ref->ID), '', new DB_WE());
												break;
											case 'weThumbnail':
												$_path_info = f('SELECT Name FROM ' . escape_sql_query($ref->Table) . ' WHERE ID=' . intval($ref->ID), '', new DB_WE());
												break;
											default:
												$_path_info = id_to_path($ref->ID, $ref->Table);
												break;
										}
										$_progress_text = we_html_element::htmlB(
												g_l('contentTypes', '[' . $ref->ContentType . ']', true) ?
													g_l('contentTypes', '[' . $ref->ContentType . ']') :
													(g_l('import', '[' . $ref->ContentType . ']', true) ?
														g_l('import', '[' . $ref->ContentType . ']') : ''
													)
											) . '  ' . $_path_info;

										echo we_html_element::jsElement(
											'if (top.wizbody.addLog){
												top.wizbody.addLog("' . addslashes($_progress_text) . '");
											}');
										flush();
									} else {
										$_status = g_l('import', '[skip]');
										echo we_html_element::jsElement(
											'if (top.wizbody.addLog){
												top.wizbody.addLog("' . addslashes(g_l('import', '[skip]') . we_html_tools::getPixel(50, 5) . $ref->Path) . '<br/>");
											}');
									}

									$_counter_text = g_l('import', '[item]') . ' ' . $v['cid'] . '/' . ($v['numFiles'] - 2);

									$JScript = "
top.wizbusy.setProgressText('pb1','" . $_status . " - " . $_counter_text . "');
top.wizbusy.setProgress(Math.floor(((" . $v['cid'] . "+1)/" . (int) (2 * $v["numFiles"]) . ")*100));";


									$out .= we_html_element::htmlForm(array("name" => "we_form"), $hiddens .
											we_html_element::jsElement($JScript . "setTimeout(function(){we_import(1," . ($v["cid"] + 1) . ");},15);"));
								}
							}
							break 2;
						case we_import_functions::TYPE_GENERIC_XML:
							$hiddens = $this->getHdns('v', $v) . $this->getHdns('records', $records) . $this->getHdns("we_flds", $we_flds) . $this->getHdns("attributes", $attributes);
							$xp = new we_xml_parser($v['uniquePath'] . '/temp_' . $v['cid'] . '.xml');
							foreach($records as $record){
								$nodeSet = $xp->evaluate($xp->root . '/' . $we_flds[$record]);
								$xPath = '';
								$loop = 0;
								$firstNode = '';
								foreach($nodeSet as $node){
									if($loop == 0){
										$firstNode = $node;
										$loop++;
									}
									$list = $xp->getAttributes($node);
									$flag = true;
									$decAttrs = we_tag_tagParser::makeArrayFromAttribs(base64_decode($attributes[$record]));
									foreach($decAttrs as $key => $value){
										if(!isset($list[$key]) || $list[$key] != $value){
											$flag = false;
										}
									}
									if($flag){
										$xPath = $node;
										break;
									}
								}
								if($xPath == ''){
									$xPath = $firstNode;
								}
								$fields = $fields + array($record => $xp->getData($xPath));
							}
							if($v['pfx_fn'] == 1){
								$v['rcd_pfx'] = $xp->getData($xp->root . '/' . $v["rcd_pfx"] . "[1]");
								if($v['rcd_pfx'] == ''){
									$v['rcd_pfx'] = g_l('import', ($v['import_type'] === 'documents' ? '[pfx_doc]' : '[pfx_obj]'));
								}
							}
							break;
						case we_import_functions::TYPE_CSV:
							$hiddens = $this->getHdns("v", $v) . $this->getHdns("records", $records) . $this->getHdns("we_flds", $we_flds);
							switch($v["csv_enclosed"]){
								case 'double_quote':
									$encl = '"';
									break;
								case 'single_quote':
									$encl = "'";
									break;
								case 'none':
									$encl = '';
									break;
							}
							list($v["classID"]) = explode('_', $v['classID']);
							$cp = new we_import_CSV;
							$cp->setFile($v['uniquePath'] . '/temp_' . $v["cid"] . ".csv");
							$cp->setDelim($v['csv_seperator']);
							$cp->setEnclosure($encl);
							$cp->setFromCharset($v['encoding']);
							$cp->parseCSV();
							$recs = array();
							$names = array();
							for($i = 0; $i < $cp->CSVNumFields(); $i++){
								$names[$i] = $cp->CSVFieldName($i);
								$recs[$names[$i]] = $cp->Fields[0][$i];
							}
							foreach($we_flds as $name => $value){
								$fields[$name] = (isset($recs[$value]) ? $recs[$value] : '');
							}
							if($v['pfx_fn'] == 1){
								$v['rcd_pfx'] = $recs[$v['rcd_pfx']];

								if($v['rcd_pfx'] == ''){
									$v['rcd_pfx'] = g_l('import', ($v['import_type'] === 'documents' ? '[pfx_doc]' : '[pfx_obj]'));
								}
							}
					}

					if($v['type'] != we_import_functions::TYPE_WE_XML){
						if(isset($v["dateFields"])){
							$dateFields = makeArrayFromCSV($v["dateFields"]);
							if(($v["sTimeStamp"] === "Format" && $v["timestamp"] != "") || ($v["sTimeStamp"] === "GMT")){
								foreach($dateFields as $dateField){
									$fields[$dateField] = we_import_functions::date2Timestamp($fields[$dateField], ($v["sTimeStamp"] != "GMT") ? $v["timestamp"] : "");
								}
							}
						}

						$rcd_name = ($v['pfx_fn'] == 1) ? $v['rcd_pfx'] : $v['asoc_prefix'];
						switch($v['import_type']){
							case 'documents':
								$IsSearchable = $v["docType"] > 0 ? (!empty($v['doc_search'])) || f('SELECT IsSearchable FROM ' . DOC_TYPES_TABLE . ' WHERE ID=' . intval($v["docType"]), '', new DB_WE()) : false;
								if(!we_import_functions::importDocument($v["store_to_id"], $v["we_TemplateID"], $fields, $v["docType"], $v["docCategories"], $rcd_name, $v["is_dynamic"], $v["we_Extension"], isset($v['doc_publish']) ? $v['doc_publish'] : true, $IsSearchable, isset($v['encoding']) ? DEFAULT_CHARSET : '' //if charset is set, we know csv was converted to defaultcharset
										, $v['collision'])){
									t_e('warning', 'import of entry failed', $fields);
								}
								break;
							case 'objects':
								if(!we_import_functions::importObject($v["classID"], $fields, $v["objCategories"], $rcd_name, isset($v['obj_publish']) ? $v['obj_publish'] : true, isset($v['obj_search']) ? $v['obj_search'] : true, isset($v['obj_path_id']) ? $v['obj_path_id'] : 0, isset($v['encoding']) ? DEFAULT_CHARSET : '' //if charset is set, we know csv was converted to defaultcharset
										, $v['collision'])){
									t_e('warning', 'import of entry failed', $fields);
								}
								break;
						}
					}


					$JScript = "
top.wizbusy.setProgressText('pb1','" . g_l('import', '[import]') . "');
top.wizbusy.setProgress(Math.floor(((" . $v["cid"] . "+1)/" . $v["numFiles"] . ")*100));";


					$out .= we_html_element::htmlForm(array("name" => "we_form"), $hiddens .
							we_html_element::jsElement($JScript . "setTimeout(function(){we_import(1," . ($v["cid"] + 1) . ");},15);"));
					break;
			} // end switch
		} else if($mode != 1){
			$out .= we_html_element::htmlForm(array('id' => 'wizardBaseForm', "name" => "we_form"), we_html_element::htmlHiddens(array(
						"v[mode]" => "",
						"v[cid]" => "",
						"mode" => "",
						"type" => "",
						"cid" => "")));
		}

		return we_html_tools::getHtmlTop('', '', '', we_html_element::jsElement("
function addField(form, fieldType, fieldName, fieldValue) {
	if (document.getElementById) {
		var input = document.createElement('INPUT');
		if (document.all) {
			input.type = fieldType;
			input.name = fieldName;
			input.value = fieldValue;
		}else if (document.getElementById) {
			input.setAttribute('type', fieldType);
			input.setAttribute('name', fieldName);
			input.setAttribute('value', fieldValue);
		}
		form.appendChild(input);
	}
}
function getField(form, fieldName) {
	if (!document.all){
		return form[fieldName];
	}else{
		for (var e = 0; e < form.elements.length; e++){
			if (form.elements[e].name == fieldName){
				return form.elements[e];
			}
		}
	}
	return null;
}
function removeField(form, fieldName) {
	var field = getField (form, fieldName);
	if (field && !field.length){
		field.parentNode.removeChild(field);
	}
}
function toggleField (form, fieldName, value) {
	var field = getField (form, fieldName);
	if (field){
		removeField (form, fieldName);
	}else{
		addField (form, 'hidden', fieldName, value);
	}
}
function cycle() {
	var test = '';
	var cf = self.document.we_form;
	var bf = top.wizbody.document.we_form;
	for (var i = 0; i < bf.elements.length; i++) {
		if ((bf.elements[i].name.indexOf('v') > -1) || (bf.elements[i].name.indexOf('records') > -1) ||
			(bf.elements[i].name.indexOf('we_flds') > -1) || (bf.elements[i].name.indexOf('attributes') > -1)) {
			addField(cf, 'hidden', bf.elements[i].name, bf.elements[i].value);
		}
	}
}
function we_import(mode, cid,reload) {
	if(reload==1){
		top.wizbody.location = '" . $this->path . "&pnt=wizbody&step=3&type=" . we_import_functions::TYPE_WE_XML . "&noload=1';
	};
	var we_form = self.document.we_form;
	we_form.elements['v[mode]'].value = mode;
	we_form.elements['v[cid]'].value = cid;
	we_form.target = 'wizcmd';
	we_form.action = '" . $this->path . "&pnt=wizcmd';
	we_form.method = 'post';
	we_form.submit();
}"
				), we_html_element::htmlBody(array('style' => 'overflow:hidden;'), $out));
	}

	private function importFinished($v, $type){
		switch($type){

			default:
				$JScript = "top.wizbusy.setProgressText('pb1','" . g_l('import', '[finish_progress]') . "');
top.wizbusy.setProgress(100);
top.opener.top.we_cmd('load', top.opener.top.treeData.table ,0);
if(WE().layout.weEditorFrameController.getActiveDocumentReference().quickstart && WE().layout.weEditorFrameController.getActiveDocumentReference().quickstart != undefined) WE().layout.weEditorFrameController.getActiveDocumentReference().location.reload();
if(top.wizbusy && top.wizbusy.document.getElementById('progress')) {
progress = top.wizbusy.document.getElementById('progress');
if(progress!==undefined){
		progress.style.display = 'none';
	}
}" .
					($v['type'] == we_import_functions::TYPE_WE_XML ?						"
if (top.wizbody && top.wizbody.addLog) {
	top.wizbody.addLog(\"" . addslashes(we_html_element::htmlB(g_l('import', '[end_import]') . " - " . date("d.m.Y H:i:s"))) . "\");
}" :
						we_message_reporting::getShowMessageCall(g_l('import', '[finish_import]'), we_message_reporting::WE_MESSAGE_NOTICE) . 'setTimeout(top.close,100);'
					);
		}

		return we_html_element::jsElement($JScript);
	}

}
