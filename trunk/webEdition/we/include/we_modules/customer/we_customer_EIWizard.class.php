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
class we_customer_EIWizard{
	var $frameset;
	var $db;
	var $exim_number = 5;

	const CSV_DELIMITER = ';';
	const CSV_ENCLOSE = '';
	const CSV_LINEEND = 'windows';
	const THE_CHARSET = 'UTF-8';
	const SELECTION_MANUAL = 'manual';
	const SELECTION_FILTER = 'filter';
	const TYPE_CSV = 'csv'; //fixme: MOVE TO we_import_functions (name it TYPE_CSV)
	const EXPORT_SERVER = 'server';
	const EXPORT_LOCAL = 'local';
	const ART_IMPORT = 'import';
	const ART_EXPORT = 'export';

	function __construct($frameset){
		$this->frameset = $frameset;
		$this->db = new DB_WE();
	}

	function getHTML($what = '', $mode = '', $step = ''){
		switch($what){
			case self::ART_EXPORT:
			case self::ART_IMPORT:
				echo $this->getHTMLFrameset($what);
				break;
			case "eibody":
				echo $this->getHTMLStep($mode, $step);
				break;
			case "eifooter":
				echo $this->getHTMLFooter($mode, $step);
				break;
			case "eiload":
				echo $this->getHTMLLoad();
				break;
			default:
				error_log(__FILE__ . " unknown reference: $what");
		}
	}

	function getHTMLFrameset($mode){
		$body = we_html_element::htmlBody(array('id' => 'weMainBody')
				, we_html_element::htmlIFrame('body', WEBEDITION_DIR . 'we_showMod.php?mod=customer&pnt=eibody&art=' . $mode . "&step=1", 'position:absolute;top:0px;bottom:45px;left:0px;right:0px;', 'border:0px;width:100%;height:100%;') .
				we_html_element::htmlIFrame('footer', WEBEDITION_DIR . 'we_showMod.php?mod=customer&pnt=eifooter&art=' . $mode . "&step=1", 'position:absolute;height:45px;bottom:0px;left:0px;right:0px;overflow: hidden', '', '', false) .
				we_html_element::htmlIFrame('load', WEBEDITION_DIR . 'we_showMod.php?mod=customer&pnt=eiload&step=1', 'position:absolute;bottom:0px;height:0px;left:0px;right:0px;overflow: hidden;')
		);


		return we_html_tools::getHtmlTop(g_l('modules_customer', '[export_title]'), '', '', we_html_element::jsElement('
			var table="' . FILE_TABLE . '";
			self.focus();
		') . STYLESHEET, $body
		);
	}

	function getHTMLStep($mode, $step = 0){
		switch($mode){
			case self::ART_EXPORT:
				$function = 'getHTMLExportStep' . intval($step);
				return (method_exists($this, $function) ?
						$this->$function() :
						$this->getHTMLStep0());
			case self::ART_IMPORT:
				$function = 'getHTMLImportStep' . intval($step);
				return (method_exists($this, $function) ?
						$this->$function() :
						$this->getHTMLStep0());
			default:
				return we_html_tools::getHtmlTop(g_l('modules_customer', '[export_title]'), '', '', '', we_html_element::htmlBody(array("bgcolor" => "white", "style" => 'margin:10px'), "aba")
				);
		}
	}

	function getHTMLExportStep1(){
		$type = we_base_request::_(we_base_request::STRING, "type", we_import_functions::TYPE_GENERIC_XML);

		$generic = new we_html_table(array('class' => 'default withSpace'), 2, 1);
		$generic->setCol(0, 0, array(), we_html_forms::radiobutton(we_import_functions::TYPE_GENERIC_XML, ($type == we_import_functions::TYPE_GENERIC_XML), "type", g_l('modules_customer', '[gxml_export]'), true, "defaultfont", "if(document.we_form.type[0].checked){top.type='" . we_import_functions::TYPE_GENERIC_XML . "';}", false, g_l('modules_customer', '[txt_gxml_export]'), 0, 430));
		$generic->setCol(1, 0, array(), we_html_forms::radiobutton(self::TYPE_CSV, ($type == self::TYPE_CSV), "type", g_l('modules_customer', '[csv_export]'), true, "defaultfont", "if(document.we_form.type[1].checked) top.type='" . self::TYPE_CSV . "';", false, g_l('modules_customer', '[txt_csv_export]'), 0, 430));

		$parts = array(
			array(
				"headline" => g_l('modules_customer', '[generic_export]'),
				"html" => $generic->getHTML(),
				'space' => we_html_multiIconBox::SPACE_MED,
				'noline' => 1)
		);

		return we_html_tools::getHtmlTop(g_l('modules_customer', '[export_title]'), '', '', STYLESHEET, we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "target" => "body"),
						//("name"=>"pnt","value"=>"eibody")).
						//array("name"=>"step","value"=>"1")).
						$this->getHiddens(array("art" => self::ART_EXPORT, "step" => 1)) .
						we_html_multiIconBox::getHTML("", $parts, 30, "", -1, "", "", false, g_l('modules_customer', '[export_step1]'))
					)
				)
		);
	}

	function getHTMLExportStep2(){
		$selection = we_base_request::_(we_base_request::STRING, "selection", self::SELECTION_FILTER);

		$table = new we_html_table(array('class' => 'default', 'style' => 'margin:5px 25px 0 0;'), 1, 2);
		$table->setColContent(0, 1, $this->getHTMLCustomerFilter());

		$generic = new we_html_table(array('class' => 'default withSpace'), 4, 1);
		$generic->setColContent(0, 0, we_html_forms::radiobutton(self::SELECTION_FILTER, ($selection == self::SELECTION_FILTER), "selection", g_l('modules_customer', '[filter_selection]'), true, "defaultfont", "if(document.we_form.selection[0].checked) top.selection='" . self::SELECTION_FILTER . "';"));
		$generic->setColContent(1, 0, $table->getHtml());

		$table->setColContent(0, 1, we_html_tools::htmlFormElementTable($this->getHTMLCustomer(), g_l('modules_customer', '[customer]')));
		$generic->setColContent(2, 0, we_html_forms::radiobutton(self::SELECTION_MANUAL, ($selection == self::SELECTION_MANUAL), "selection", g_l('modules_customer', '[manual_selection]'), true, "defaultfont", "if(document.we_form.selection[1].checked) top.selection='" . self::SELECTION_MANUAL . "';"));
		$generic->setColContent(3, 0, $table->getHtml());

		$parts = array(array(
				"headline" => "",
				"html" => $generic->getHTML(),
				'space' => we_html_multiIconBox::SPACE_SMALL,
				'noline' => 1)
		);

		$js = we_html_element::jsElement('

			function doUnload() {
				WE().util.jsWindow.prototype.closeAll(window);
			}

			function we_cmd(){
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

switch (args[0]){
					case "del_customer":
						selector_cmd(args[0],args[1],args[2]);
						break;
					default:
						top.opener.top.we_cmd.apply(this, Array.prototype.slice.call(arguments));
				}
			}

			//top.footer.location=WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=customer&pnt=eifooter&step="+top.step;

		');
		return we_html_tools::getHtmlTop(g_l('modules_customer', '[export_title]'), '', '', STYLESHEET . $js, we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "target" => "body"), $this->getHiddens(array("art" => self::ART_EXPORT, "step" => 2)) .
						we_html_multiIconBox::getHTML("", $parts, 30, "", -1, "", "", false, g_l('modules_customer', '[export_step2]'))
					)
				)
		);
	}

	function getHTMLExportStep3(){
		//set defaults
		$type = we_base_request::_(we_base_request::STRING, "type", we_import_functions::TYPE_GENERIC_XML);
		$filename = we_base_request::_(we_base_request::FILE, "filename", "weExport_" . time() . ($type == self::TYPE_CSV ? ".csv" : ".xml"));
		$export_to = we_base_request::_(we_base_request::STRING, "export_to", self::EXPORT_SERVER);
		$path = we_base_request::_(we_base_request::FILE, "path", "/");
		$cdata = we_base_request::_(we_base_request::INT, "cdata", true);

		$csv_delimiter = we_base_request::_(we_base_request::RAW_CHECKED, "csv_delimiter", self::CSV_DELIMITER);
		$csv_enclose = we_base_request::_(we_base_request::RAW_CHECKED, "csv_enclose", self::CSV_ENCLOSE);
		$csv_lineend = we_base_request::_(we_base_request::STRING, "csv_lineend", self::CSV_LINEEND);
		$csv_fieldnames = we_base_request::_(we_base_request::BOOL, "csv_fieldnames", true);

		$customers = $this->getExportCustomers();
		if($customers){
			//set variables in top frame
			$js = '';
			$parts = array(
				array(
					'headline' => g_l('modules_customer', '[filename]'),
					'html' => we_html_tools::htmlTextInput('filename', 42, $filename),
					'space' => we_html_multiIconBox::SPACE_MED2
				),
			);

			switch($type){
				case we_import_functions::TYPE_GENERIC_XML:

					$table = new we_html_table(array('class' => 'default withSpace'), 2, 1);

					$table->setColContent(0, 0, we_html_forms::radiobutton(1, $cdata, "cdata", g_l('modules_customer', '[export_xml_cdata]'), true, "defaultfont", ""));
					$table->setColContent(1, 0, we_html_forms::radiobutton(0, !$cdata, "cdata", g_l('modules_customer', '[export_xml_entities]'), true, "defaultfont", ""));

					$parts[] = array("headline" => g_l('modules_customer', '[cdata]'), "html" => $table->getHtml(), 'space' => we_html_multiIconBox::SPACE_MED2);

					break;
				case self::TYPE_CSV:
					$fileformattable = new we_html_table(array('style' => 'margin-top:10px;'), 4, 1);

					$_file_encoding = new we_html_select(array("name" => "csv_lineend", "class" => "defaultfont", "style" => "width: 254px;"));
					$_file_encoding->addOption("windows", g_l('modules_customer', '[windows]'));
					$_file_encoding->addOption("unix", g_l('modules_customer', '[unix]'));
					$_file_encoding->addOption("mac", g_l('modules_customer', '[mac]'));
					$_file_encoding->selectOption($csv_lineend);

					$fileformattable->setCol(0, 0, array("class" => "defaultfont"), g_l('modules_customer', '[csv_lineend]') . "<br/>" . $_file_encoding->getHtml());
					$fileformattable->setColContent(1, 0, $this->getHTMLChooser("csv_delimiter", $csv_delimiter, array("," => g_l('modules_customer', '[comma]'), ";" => g_l('modules_customer', '[semicolon]'), ":" => g_l('modules_customer', '[colon]'), "\\t" => g_l('modules_customer', '[tab]'), " " => g_l('modules_customer', '[space]')), g_l('modules_customer', '[csv_delimiter]')));
					$fileformattable->setColContent(2, 0, $this->getHTMLChooser("csv_enclose", $csv_enclose, array('"' => g_l('modules_customer', '[double_quote]'), "'" => g_l('modules_customer', '[single_quote]')), g_l('modules_customer', '[csv_enclose]')));

					$fileformattable->setColContent(3, 0, we_html_forms::checkbox(1, $csv_fieldnames, "csv_fieldnames", g_l('modules_customer', '[csv_fieldnames]')));

					$parts[] = array("headline" => g_l('modules_customer', '[csv_params]'), "html" => $fileformattable->getHtml(), 'space' => we_html_multiIconBox::SPACE_MED2);
			}

			$parts[] = array("headline" => g_l('modules_customer', '[export_to]'), "html" => "", 'noline' => 1);

			$parts[] = array('space' => we_html_multiIconBox::SPACE_MED2, 'noline' => 1,
				"headline" => we_html_element::htmlDiv(array('class' => 'default'), we_html_forms::radiobutton(self::EXPORT_SERVER, ($export_to == self::EXPORT_SERVER), "export_to", g_l('modules_customer', '[export_to_server]'), true, "defaultfont", "top.export_to='" . self::EXPORT_SERVER . "'")),
				"html" =>
				we_html_element::htmlBr() .
				we_html_tools::htmlFormElementTable($this->formFileChooser(200, "path", $path, "", we_base_ContentTypes::FOLDER), g_l('modules_customer', '[path]'))
			);

			$parts[] = array(
				"headline" => we_html_forms::radiobutton(self::EXPORT_LOCAL, ($export_to == self::EXPORT_LOCAL), "export_to", g_l('modules_customer', '[export_to_local]'), true, "defaultfont", "top.export_to='" . self::EXPORT_LOCAL . "'"),
				'space' => we_html_multiIconBox::SPACE_MED2,
				'noline' => 1,
				"html" => ""
			);
		} else {
			$parts = array(
				array('headline' => 'Fehler', "html" => '<b>Die Auswahl ist leer</b>', 'space' => we_html_multiIconBox::SPACE_MED2)
			);
			$js = we_html_element::jsElement(
					 'top.body.document.we_form.step.value--;
	top.footer.location=WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=customer&pnt=eifooter&art=' . self::ART_EXPORT . '&step="+top.body.document.we_form.step.value;
	top.body.document.we_form.submit();'
			); //FIXME: disable next button
		}
		return we_html_tools::getHtmlTop('', '', '', STYLESHEET . $js, we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "target" => "body"),
						//we_html_element::htmlHidden(array("name"=>"step",""=>"4")).
						$this->getHiddens(array('art' => self::ART_EXPORT, 'step' => 3)) .
						we_html_multiIconBox::getHTML("weExportWizard", $parts, 30, "", -1, "", "", false, g_l('modules_customer', '[export_step3]'))
					)
				)
		);
	}

	function getHTMLExportStep4(){
		$export_to = we_base_request::_(we_base_request::STRING, 'export_to', self::EXPORT_SERVER);
		$path = urldecode(we_base_request::_(we_base_request::FILE, "path", ''));
		$filename = urldecode(we_base_request::_(we_base_request::FILE, "filename", ''));
		$js = we_html_element::jsElement('top.footer.location=WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=customer&pnt=eifooter&art=' . self::ART_EXPORT . '&step=5";');

		if($export_to == self::EXPORT_LOCAL){
			$message = we_html_element::htmlSpan(array("class" => "defaultfont"), g_l('modules_customer', '[export_finished]') . "<br/><br/>" .
					g_l('modules_customer', '[download_starting]') .
					we_html_element::htmlA(array("href" => WEBEDITION_DIR . 'we_showMod.php?mod=customer&pnt=eibody&art=' . self::ART_EXPORT . '&step=5&exportfile=' . $filename, 'download' => $filename), g_l('modules_customer', '[download]'))
			);
			return we_html_tools::getHtmlTop(g_l('modules_customer', '[export_title]'), '', '', STYLESHEET . $js .
					we_html_element::htmlMeta(array("http-equiv" => "refresh", "content" => "2; url=" . WEBEDITION_DIR . 'we_showMod.php?mod=customer&pnt=eibody&art=' . self::ART_EXPORT . '&step=5&exportfile=' . $filename)), we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_tools::htmlDialogLayout($message, g_l('modules_customer', '[export_step4]'))
					)
			);
		} else {
			$message = we_html_element::htmlSpan(array("class" => "defaultfont"), g_l('modules_customer', '[export_finished]') . "<br/><br/>" .
					g_l('modules_customer', '[server_finished]') . "<br/>" .
					rtrim($path, '/') . '/' . $filename
			);

			return we_html_tools::getHtmlTop(g_l('modules_customer', '[export_title]'), '', '', STYLESHEET . $js, we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_tools::htmlDialogLayout($message, g_l('modules_customer', '[export_step4]'))
					)
			);
		}
	}

	function getHTMLExportStep5(){
		if(($_filename = we_base_request::_(we_base_request::FILE, "exportfile"))){

			if(file_exists(TEMP_PATH . $_filename) // Does file exist?
				&& !preg_match('%p?html?%i', $_filename) && stripos($_filename, "inc") === false && !preg_match('%php3?%i', $_filename)){ // Security check
				session_write_close();
				$_size = filesize(TEMP_PATH . $_filename);

				header("Pragma: public");
				header("Expires: 0");
				header("Cache-control: private, max-age=0, must-revalidate");

				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename="' . trim(htmlentities($_filename)) . '"');
				header('Content-Description: Customer-Export');
				header('Content-Length: ' . $_size);

				readfile(TEMP_PATH . $_filename);

				exit;
			}
		}
		header("Location: " . WEBEDITION_DIR . 'we_showMod.php?mod=customer&pnt=cmd&step=99&error=download_failed');
		exit;
	}

	function getHiddens($options = array()){
		switch($options["art"]){
			case self::ART_IMPORT:
				$filename = we_base_request::_(we_base_request::FILE, 'filename');
				$import_from = we_base_request::_(we_base_request::STRING, 'import_from', self::EXPORT_SERVER);
				$type = we_base_request::_(we_base_request::STRING, 'type', we_import_functions::TYPE_GENERIC_XML);
				$xml_from = we_base_request::_(we_base_request::RAW, 'xml_from', 0);
				$xml_to = we_base_request::_(we_base_request::RAW, 'xml_to', 1);
				$dataset = we_base_request::_(we_base_request::RAW, 'dataset', '');
				$csv_delimiter = we_base_request::_(we_base_request::RAW_CHECKED, 'csv_delimiter', self::CSV_DELIMITER);
				$csv_enclose = we_base_request::_(we_base_request::RAW_CHECKED, 'csv_enclose', self::CSV_ENCLOSE);
				$csv_lineend = we_base_request::_(we_base_request::STRING, 'csv_lineend', self::CSV_LINEEND);
				$the_charset = we_base_request::_(we_base_request::STRING, 'the_charset', self::THE_CHARSET);

				$csv_fieldnames = we_base_request::_(we_base_request::BOOL, "csv_fieldnames");

				$source = we_base_request::_(we_base_request::FILE, 'source', '/');

				switch($options["step"]){
					case 1:
						return we_html_element::htmlHiddens(array(
								"pnt" => "eibody",
								"step" => $options["step"],
								"art" => self::ART_IMPORT,
								($filename ? "filename" : '') => $filename,
								"source" => $source,
								"import_from" => $import_from,
								"xml_from" => $xml_from,
								"xml_to" => $xml_to,
								"dataset" => $dataset,
								"csv_delimiter" => $csv_delimiter,
								"csv_enclose" => $csv_enclose,
								"csv_lineend" => $csv_lineend,
								"the_charset" => $the_charset,
								"csv_fieldnames" => $csv_fieldnames));

					case 2:
						return we_html_element::htmlHiddens(array(
								"pnt" => "eibody",
								"step" => $options["step"],
								"art" => self::ART_IMPORT,
								"type" => $type,
								($filename ? "filename" : '') => $filename,
								"xml_from" => $xml_from,
								"xml_to" => $xml_to,
								"dataset" => $dataset,
								"csv_delimiter" => $csv_delimiter,
								"csv_enclose" => $csv_enclose,
								"csv_lineend" => $csv_lineend,
								"the_charset" => $the_charset,
								"csv_fieldnames" => $csv_fieldnames
						));

					case 3:
						return we_html_element::htmlHiddens(array(
								"pnt" => "eibody",
								"step" => $options["step"],
								"art" => self::ART_IMPORT,
								"type" => $type,
								"source" => $source,
								($filename ? "filename" : '') => $filename,
								"import_from" => $import_from,
								"dataset" => $dataset));

					case 4:
						return we_html_element::htmlHiddens(array(
								"pnt" => "eibody",
								"step" => $options["step"],
								"art" => self::ART_IMPORT,
								"type" => $type,
								"source" => $source,
								"import_from" => $import_from,
								"dataset" => $dataset,
								"xml_from" => $xml_from,
								"xml_to" => $xml_to,
								"csv_delimiter" => $csv_delimiter,
								"csv_lineend", "value" => $csv_lineend,
								"the_charset" => $the_charset,
								"csv_fieldnames" => $csv_fieldnames,
								"cmd" => self::ART_IMPORT,
								($filename ? "filename" : '') => $filename,
								"csv_enclose" => ($csv_enclose === '"' ? '"' : $csv_enclose)
						));
				}
				return '';

			case self::ART_EXPORT:

				$type = we_base_request::_(we_base_request::STRING, "type", we_import_functions::TYPE_GENERIC_XML);
				$selection = we_base_request::_(we_base_request::STRING, "selection", self::SELECTION_FILTER);
				$export_to = we_base_request::_(we_base_request::STRING, "export_to", self::EXPORT_SERVER);
				$path = urldecode(we_base_request::_(we_base_request::FILE, "path", "/"));
				$filename = we_base_request::_(we_base_request::FILE, "filename");
				$cdata = we_base_request::_(we_base_request::INT, "cdata", 1);

				$customers = we_base_request::_(we_base_request::INTLIST, "customers", "");

				$csv_delimiter = we_base_request::_(we_base_request::RAW_CHECKED, "csv_delimiter", self::CSV_DELIMITER);
				$csv_enclose = we_base_request::_(we_base_request::RAW_CHECKED, "csv_enclose", self::CSV_ENCLOSE);
				$csv_lineend = we_base_request::_(we_base_request::STRING, "csv_lineend", self::CSV_LINEEND);
				$csv_fieldnames = we_base_request::_(we_base_request::BOOL, "csv_fieldnames", true);

				$filter_count = we_base_request::_(we_base_request::INT, "filter_count", 0);
				$filter = "";
				$fields_names = array("fieldname", "operator", "fieldvalue", "logic");
				for($i = 0; $i < $filter_count; $i++){
					//$new = array("fieldname" => "", "operator" => "", "fieldvalue" => "", "logic" => "");
					foreach($fields_names as $field){
						$varname = "filter_" . $field . "_" . $i;
						if(($f = we_base_request::_(we_base_request::STRING, $varname)) !== false){
							$filter.=we_html_element::htmlHidden($varname, $f);
						}
					}
				}

				switch($options['step']){
					case 1:
						return we_html_element::htmlHiddens(array(
								"pnt" => "eibody",
								"step" => $options["step"],
								"art" => self::ART_EXPORT,
								"type" => $type,
								"selection" => $selection,
								"export_to" => $export_to,
								"path" => $path,
								"cdata" => $cdata,
								"customers" => $customers,
								($filename ? "filename" : '') => $filename,
								"csv_delimiter" => $csv_delimiter,
								"csv_enclose" => $csv_enclose,
								"csv_lineend" => $csv_lineend,
								"csv_fieldnames" => $csv_fieldnames,
								"filter_count" => $filter_count)) .
							$filter;

					case 2:
						return we_html_element::htmlHiddens(array(
								"pnt" => "eibody",
								"step" => $options["step"],
								"art" => self::ART_EXPORT,
								"type" => $type,
								"selection" => $selection,
								"export_to" => $export_to,
								"path" => $path,
								"cdata" => $cdata,
								"customers" => $customers,
								($filename ? "filename" : '') => $filename,
								"csv_delimiter" => $csv_delimiter,
								"csv_enclose" => $csv_enclose,
								"csv_lineend" => $csv_lineend,
								"csv_fieldnames" => $csv_fieldnames,
								"filter_count" => $filter_count));

					case 3:
						return we_html_element::htmlHiddens(array(
								"pnt" => "eibody",
								"step" => $options["step"],
								"art" => self::ART_EXPORT,
								"type" => $type,
								"selection" => $selection,
								"customers" => $customers,
								"filter_count" => $filter_count,
								"cmd" => self::ART_EXPORT
							)) .
							$filter;

					case 4:
						return we_html_element::htmlHiddens(array(
								"pnt" => "eibody",
								"step" => $options["step"],
								"art" => self::ART_EXPORT,
								"type" => $type,
								"selection" => $selection,
								"export_to" => $export_to,
								"path" => $path,
								"cdata" => $cdata,
								"customers" => $customers,
								($filename ? "filename" : '') => $filename,
								"csv_delimiter" => $csv_delimiter,
								"csv_enclose" => $csv_enclose,
								"csv_lineend" => $csv_lineend,
								"csv_fieldnames" => $csv_fieldnames,
								"filter_count" => $filter_count,
								"cmd" => self::ART_EXPORT
							)) .
							$filter;
				}
				return '';
		}

		return '';
	}

	function getHTMLImportStep1(){
		$type = we_base_request::_(we_base_request::STRING, "type", we_import_functions::TYPE_GENERIC_XML);

		$generic = new we_html_table(array('class' => 'default withSpace'), 2, 1);
		$generic->setCol(0, 0, array(), we_html_forms::radiobutton(we_import_functions::TYPE_GENERIC_XML, ($type == we_import_functions::TYPE_GENERIC_XML), "type", g_l('modules_customer', '[gxml_import]'), true, "defaultfont", "if(document.we_form.type[0].checked) top.type='" . we_import_functions::TYPE_GENERIC_XML . "';", false, g_l('modules_customer', '[txt_gxml_import]'), 0, 430));
		$generic->setCol(1, 0, array(), we_html_forms::radiobutton(self::TYPE_CSV, ($type == self::TYPE_CSV), "type", g_l('modules_customer', '[csv_import]'), true, "defaultfont", "if(document.we_form.type[1].checked) top.type='" . self::TYPE_CSV . "';", false, g_l('modules_customer', '[txt_csv_import]'), 0, 430));

		$parts = array(
			array(
				"headline" => g_l('modules_customer', '[generic_import]'),
				"html" => $generic->getHTML(),
				'space' => we_html_multiIconBox::SPACE_MED,
				'noline' => 1)
		);

		return we_html_tools::getHtmlTop(g_l('modules_customer', '[import_title]'), '', '', STYLESHEET, we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post"), $this->getHiddens(array("art" => self::ART_IMPORT, "step" => 1)) .
						we_html_multiIconBox::getHTML("", $parts, 30, "", -1, "", "", false, g_l('modules_customer', '[import_step1]'))
					)
				)
		);
	}

	function getHTMLImportStep2(){
		$import_from = we_base_request::_(we_base_request::STRING, "import_from", self::EXPORT_SERVER);
		$source = we_base_request::_(we_base_request::FILE, "source", "/");
		$type = we_base_request::_(we_base_request::STRING, "type", "");

		$fileUploader = new we_fileupload_ui_base('upload');
		$fileUploader->setExternalUiElements(array('btnUploadName' => 'next_footer'));
		$fileUploader->setCallback('top.load.doNextAction()');
		//$fileUploader->setForm(array('action' => WEBEDITION_DIR.'we_showMod.php?mod=customer&pnt=eibody&art=import&step=3&import_from=' . self::EXPORT_LOCAL . '&type=' . $type));
		$fileUploader->setInternalProgress(array('isInternalProgress' => true, 'width' => 200));
		$fileUploader->setFileSelectOnclick('document.we_form.import_from[1].checked = true;');
		$fileUploader->setGenericFileName(TEMP_DIR . we_fileupload::REPLACE_BY_UNIQUEID . ($type == self::TYPE_CSV ? ".csv" : ".xml"));
		$fileUploader->setDisableUploadBtnOnInit(false);
		$fileUploader->setDimensions(array('width' => 369, 'alertBoxWidth' => 430, 'marginTop' => 10));

		$parts = array();
		$js = we_html_element::jsElement('

function callBack(){
	document.we_form.import_from[1].checked=true;
}') . $fileUploader->getJs();
		$css = STYLESHEET . $fileUploader->getCss();

		$table = new we_html_table(array('class' => 'default withSpace'), 2, 2);
		$table->setCol(0, 0, array("colspan" => 2), we_html_forms::radiobutton(self::EXPORT_SERVER, ($import_from == self::EXPORT_SERVER), "import_from", g_l('modules_customer', '[server_import]'), true, "defaultfont"));

		$table->setCol(1, 1, array('style' => 'padding-bottom:5px;'), $this->formFileChooser(250, "source", $source, "opener.top.body.document.we_form.import_from[0].checked=true;", ($type == we_import_functions::TYPE_GENERIC_XML ? we_base_ContentTypes::XML : "")));

		$parts[] = array(
			"headline" => g_l('modules_customer', '[source_file]'),
			"html" => $table->getHtml(),
			'space' => we_html_multiIconBox::SPACE_MED,
			'noline' => 1
		);

		//upload table
		$tmptable = new we_html_table(array('class' => 'default withSpace'), 2, 1);
		//$tmptable->setCol(0, 0, array(), we_html_tools::htmlAlertAttentionBox(sprintf(g_l('newFile', '[max_possible_size]'), we_base_file::getHumanFileSize($maxsize, we_base_file::SZ_MB)), we_html_tools::TYPE_ALERT, 430));
		$tmptable->setCol(0, 0, array(), $fileUploader->getHtmlAlertBoxes());

		//$tmptable->setCol(2, 0, array('style'=>'vertical-align:middle;'), we_html_tools::htmlTextInput("upload", 35, "", 255, "onclick=\"document.we_form.import_from[1].checked=true;\"", "file"));
		$tmptable->setCol(1, 0, array('style' => 'vertical-align:middle;'), $fileUploader->getHTML());

		$table = new we_html_table(array('class' => 'default withSpace'), 2, 2);
		$table->setCol(0, 0, array("colspan" => 2), we_html_forms::radiobutton(self::EXPORT_LOCAL, ($import_from == self::EXPORT_LOCAL), "import_from", g_l('modules_customer', '[upload_import]'), true, "defaultfont"));
		$table->setColContent(1, 1, $tmptable->getHtml());

		$parts[] = array(
			"headline" => "",
			"html" => $table->getHTML(),
			'space' => we_html_multiIconBox::SPACE_MED,
			'noline' => 1
		);

		return we_html_tools::getHtmlTop(g_l('modules_customer', '[import_title]'), '', '', $css . $js, we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "target" => "body", "enctype" => "multipart/form-data"), $this->getHiddens(array("art" => self::ART_IMPORT, "step" => 2)) .
						we_html_multiIconBox::getHTML("", $parts, 30, "", -1, "", "", false, g_l('modules_customer', '[import_step2]'))
					)
				)
		);
	}

	function getHTMLImportStep3(){
		$import_from = we_base_request::_(we_base_request::STRING, "import_from", self::EXPORT_SERVER);
		$source = we_base_request::_(we_base_request::FILE, "source", "/");
		$type = we_base_request::_(we_base_request::STRING, "type", "");

		if($import_from == self::EXPORT_LOCAL){
			$fileUploader = new we_fileupload_resp_base();
			//$fileUploader->setTypeCondition();
			$filename = $fileUploader->commitUploadedFile();
		} else {
			$filename = $source;
		}
		$filesource = $filename ? $_SERVER['DOCUMENT_ROOT'] . $filename : '';

		$parts = array();
		if(is_file($filesource) && is_readable($filesource)){
			$js = "";
			switch($type){

				case self::TYPE_CSV:
					$line = we_base_file::loadLine($filesource, 0, 80960);
					$charsets = array('UTF-8', 'ISO-8859-15', 'ISO-8859-1'); //charsetHandler::getAvailCharsets();
					$charset = mb_detect_encoding($line, $charsets, true);
					$charCount = count_chars($line, 0);

					$csv_delimiters = array(';' => g_l('modules_customer', '[semicolon]'), ',' => g_l('modules_customer', '[comma]'), ':' => g_l('modules_customer', '[colon]'), '\t' => g_l('modules_customer', '[tab]'), ' ' => g_l('modules_customer', '[space]'));
					$csv_encloses = array('"' => g_l('modules_customer', '[double_quote]'), '\'' => g_l('modules_customer', '[single_quote]'));
					$max = 0;
					$csv_delimiter = '';
					foreach(array_keys($csv_delimiters) as $char){
						$ord = ord($char);
						if($charCount[$ord] > $max){
							$csv_delimiter = $char;
							$max = $charCount[$ord];
						}
					}
					//leave max
					$csv_enclose = '';
					foreach(array_keys($csv_encloses) as $char){
						$ord = ord($char);
						if($charCount[$ord] > $max){
							$csv_enclose = $char;
							$max = $charCount[$ord];
						}
					}
					$r = $charCount[ord("\r")];
					$n = $charCount[ord("\n")];
					$csv_lineend = ($r > 0 && $r == $n ? 'windows' : $r > 0 ? 'mac' : 'unix');
					$csv_fieldnames = (strpos($line, 'Username') !== false);

					//t_e($csv_delimiter, $csv_enclose, $max, $charCount, $r, $n, $csv_lineend, $csv_fieldnames,$line);

					$fileformattable = new we_html_table(array('style' => 'margin-top:10px;'), 5, 1);

					$_file_encoding = new we_html_select(array("name" => "csv_lineend", "class" => "defaultfont", "style" => "width: 254px;"));
					$_file_encoding->addOption('windows', g_l('modules_customer', '[windows]'));
					$_file_encoding->addOption('unix', g_l('modules_customer', '[unix]'));
					$_file_encoding->addOption('mac', g_l('modules_customer', '[mac]'));
					$_file_encoding->selectOption($csv_lineend);

					$_charsetHandler = new we_base_charsetHandler();
					$_charsets = $_charsetHandler->getCharsetsForTagWizzard();
					//$charset = $GLOBALS['WE_BACKENDCHARSET'];
					//$GLOBALS['weDefaultCharset'] = get_value("default_charset");
					$_importCharset = we_html_tools::htmlTextInput('the_charset', 8, ($charset === 'ASCII' ? 'ISO8859-1' : $charset), 255, '', 'text', 100);
					$_importCharsetChooser = we_html_tools::htmlSelect("ImportCharsetSelect", $_charsets, 1, ($charset === 'ASCII' ? 'ISO8859-1' : $charset), false, array("onchange" => "document.forms[0].elements.the_charset.value=this.options[this.selectedIndex].value;this.selectedIndex=-1;"), "value", 160, "defaultfont", false);
					$import_Charset = '<table class="default"><tr><td>' . $_importCharset . '</td><td>' . $_importCharsetChooser . '</td></tr></table>';


					$fileformattable->setCol(0, 0, array("class" => "defaultfont"), g_l('modules_customer', '[csv_lineend]') . we_html_element::htmlBr() . $_file_encoding->getHtml());
					$fileformattable->setCol(1, 0, array("class" => "defaultfont"), g_l('modules_customer', '[import_charset]') . we_html_element::htmlBr() . $import_Charset);
					$fileformattable->setColContent(2, 0, $this->getHTMLChooser("csv_delimiter", $csv_delimiter, $csv_delimiters, g_l('modules_customer', '[csv_delimiter]')));
					$fileformattable->setColContent(3, 0, $this->getHTMLChooser("csv_enclose", $csv_enclose, $csv_encloses, g_l('modules_customer', '[csv_enclose]')));

					$fileformattable->setColContent(4, 0, we_html_forms::checkbox(1, $csv_fieldnames, "csv_fieldnames", g_l('modules_customer', '[csv_fieldnames]')));

					$parts = array(array("headline" => g_l('modules_customer', '[csv_params]'), "html" => $fileformattable->getHtml(), 'space' => we_html_multiIconBox::SPACE_MED2));
					break;
				case we_import_functions::TYPE_GENERIC_XML:
					//invoke parser
					$xp = new we_xml_parser($filesource);
					$xmlWellFormed = ($xp->parseError === "");

					if($xmlWellFormed){
						// Node-set with paths to the child nodes.
						$node_set = $xp->evaluate("*/child::*");
						$children = $xp->nodes[$xp->root]["children"];

						$recs = array();
						foreach($children as $key => $value){
							$flag = true;
							for($k = 1; $k < ($value + 1); $k++){
								if(!$xp->hasChildNodes($xp->root . "/" . $key . "[" . $k . "]")){
									$flag = false;
								}
							}
							if($flag){
								$recs[$key] = $value;
							}
						}
						$isSingleNode = (count($recs) == 1);
						$hasChildNode = (!empty($recs));
					}
					if($xmlWellFormed && $hasChildNode){
						$rcdSelect = new we_html_select(array(
							'name' => "we_select",
							'class' => 'defaultfont',
							(($isSingleNode) ? "disabled" : "style") => "",
							'onchange' => "this.form.elements.xml_to.value=this.options[this.selectedIndex].value; this.form.elements.xml_from.value=1;this.form.elements.dataset.value=this.options[this.selectedIndex].text;" .
							"if(this.options[this.selectedIndex].value==1) {this.form.elements.xml_from.disabled=true;this.form.elements.xml_to.disabled=true;} else {this.form.elements.xml_from.disabled=false;this.form.elements.xml_to.disabled=false;}")
						);
						$optid = 0;
						foreach($recs as $value => $text){
							if($optid == 0){
								$firstItem = $value;
								$firstOptVal = $text;
							}
							$rcdSelect->addOption($text, $value);
							if(isset($v["rcd"]))
								if($text == $v["rcd"])
									$rcdSelect->selectOption($value);
							$optid++;
						}

						$tblSelect = new we_html_table(array(), 1, 7);
						$tblSelect->setCol(0, 1, array(), $rcdSelect->getHtml());
						$tblSelect->setCol(0, 2, array("width" => 20));
						$tblSelect->setCol(0, 3, array("class" => "defaultfont"), g_l('modules_customer', '[num_data_sets]'));
						$tblSelect->setCol(0, 4, array(), we_html_tools::htmlTextInput("xml_from", 4, 1, 5, "align=right", "text", 30, "", "", ($isSingleNode && ($firstOptVal == 1)) ? 1 : 0));
						$tblSelect->setCol(0, 5, array("class" => "defaultfont"), g_l('modules_customer', '[to]'));
						$tblSelect->setCol(0, 6, array(), we_html_tools::htmlTextInput("xml_to", 4, $firstOptVal, 5, "align=right", "text", 30, "", "", ($isSingleNode && ($firstOptVal == 1)) ? 1 : 0));

						$tblFrame = new we_html_table(array(), 3, 2);
						$tblFrame->setCol(0, 0, array("colspan" => 2, "class" => "defaultfont"), ($isSingleNode) ? we_html_tools::htmlAlertAttentionBox(g_l('modules_customer', '[well_formed]') . " " . g_l('modules_customer', '[select_elements]'), we_html_tools::TYPE_INFO, 570) :
								we_html_tools::htmlAlertAttentionBox(g_l('modules_customer', '[xml_valid_1]') . " $optid " . g_l('modules_customer', '[xml_valid_m2]'), we_html_tools::TYPE_INFO, 570));
						$tblFrame->setCol(1, 0, array("colspan" => 2));
						$tblFrame->setCol(2, 1, array(), $tblSelect->getHtml());

						$_REQUEST["dataset"] = $firstItem;
						$parts = array(array("html" => $tblFrame->getHtml(), 'noline' => 1));
					}else {
						$parts = array(array("html" => we_html_tools::htmlAlertAttentionBox(g_l('modules_customer', (!$xmlWellFormed) ? '[not_well_formed]' : '[missing_child_node]'), we_html_tools::TYPE_ALERT, 570), 'noline' => 1));
						$js = we_html_element::jsElement('top.footer.location=WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=customer&pnt=eifooter&art=' . self::ART_IMPORT . '&step=99";');
					}
					break;
			}
		} else {
			$js = we_html_element::jsElement('top.footer.location=WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=customer&pnt=eifooter&art=' . self::ART_IMPORT . '&step=99";');
			$parts[] = array("html" => we_html_tools::htmlAlertAttentionBox(g_l('modules_customer', '[missing_filesource]'), we_html_tools::TYPE_ALERT, 570), 'noline' => 1);
		}

		$_REQUEST["filename"] = $filename;
		return we_html_tools::getHtmlTop(g_l('modules_customer', '[import_title]'), '', '', STYLESHEET . $js, we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "target" => "body"), $this->getHiddens(array("art" => self::ART_IMPORT, "step" => 3)) .
						we_html_multiIconBox::getHTML("", $parts, 30, "", -1, "", "", false, g_l('modules_customer', '[import_step3]'))
					)
				)
		);
	}

	function getHTMLImportStep4(){
		$filename = we_base_request::_(we_base_request::FILE, "filename", "");
		$type = we_base_request::_(we_base_request::STRING, "type", "");
		$dataset = we_base_request::_(we_base_request::RAW, "dataset", "");
		$csv_delimiter = we_base_request::_(we_base_request::RAW_CHECKED, "csv_delimiter", self::CSV_DELIMITER);
		$csv_enclose = we_base_request::_(we_base_request::RAW_CHECKED, "csv_enclose", self::CSV_ENCLOSE);
		$csv_lineend = we_base_request::_(we_base_request::STRING, "csv_lineend", self::CSV_LINEEND);
		$the_charset = we_base_request::_(we_base_request::STRING, "the_charset", self::THE_CHARSET);
		$csv_fieldnames = we_base_request::_(we_base_request::BOOL, "csv_fieldnames");
		$same = we_base_request::_(we_base_request::STRING, 'same', 'rename');

		$field_mappings = we_base_request::_(we_base_request::RAW, "field_mappings", "");
		$att_mappings = we_base_request::_(we_base_request::RAW, "att_mappings", "");

		if($type == self::TYPE_CSV){
			$arrgs = array(
				"delimiter" => $csv_delimiter,
				"enclose" => $csv_enclose,
				"lineend" => $csv_lineend,
				"fieldnames" => $csv_fieldnames,
				"charset" => $the_charset,
			);
		} else {
			$arrgs = array("dataset" => $dataset);
		}

		$nodes = we_customer_EI::getDataset($type, $filename, $arrgs);
		$records = we_customer_EI::getCustomersFieldset();

		if($type == we_import_functions::TYPE_GENERIC_XML){
			$tableheader = array(array("dat" => g_l('modules_customer', '[we_flds]')), array("dat" => g_l('modules_customer', '[rcd_flds]')), array("dat" => g_l('import', '[attributes]')));
		} else {
			$tableheader = array(array("dat" => g_l('modules_customer', '[we_flds]')), array("dat" => g_l('modules_customer', '[rcd_flds]')));
		}

		$rows = array();
		$i = 0;

		foreach($records as $record){
			$we_fields = new we_html_select(array(
				"name" => "field_mappings[$record]",
				"class" => "defaultfont",
				"onclick" => "",
				"style" => "")
			);

			$we_fields->addOption("", g_l('modules_customer', '[any]'));

			foreach(array_keys($nodes) as $node){
				$we_fields->addOption(oldHtmlspecialchars(str_replace(" ", "", $node)), oldHtmlspecialchars($node));
				if(isset($field_mappings[$record])){
					if($node == $field_mappings[$record]){
						$we_fields->selectOption($node);
					}
				} else {
					if($node == $record){
						$we_fields->selectOption($node);
					}
				}
			}
			if($type == we_import_functions::TYPE_GENERIC_XML){
				$rows[] = array(
					array("dat" => $record),
					array("dat" => $we_fields->getHTML()),
					array("dat" => we_html_tools::htmlTextInput("att_mappings[$record]", 30, (isset($att_mappings[$record]) ? $att_mappings[$record] : ""), 255, "", "text", 100))
				);
			} else {
				$rows[] = array(
					array("dat" => $record),
					array("dat" => $we_fields->getHTML())
				);
			}
			$i++;
		}

		$table = new we_html_table(array('class' => 'default'), 4, 1);
		$table->setColContent(0, 0, we_html_forms::radiobutton("rename", ($same === "rename"), "same", g_l('modules_customer', '[same_rename]'), true, "defaultfont", ""));
		$table->setColContent(1, 0, we_html_forms::radiobutton("overwrite", ($same === "overwrite"), "same", g_l('modules_customer', '[same_overwrite]'), true, "defaultfont", ""));
		$table->setColContent(2, 0, we_html_forms::radiobutton("skip", ($same === "skip"), "same", g_l('modules_customer', '[same_skip]'), true, "defaultfont", ""));

		$parts = array(
			array(
				"headline" => g_l('modules_customer', '[same_names]'),
				"html" => $table->getHtml(),
			),
			array(
				"headline" => g_l('modules_customer', '[import_step4]'),
				"html" => "<br/>" . we_html_tools::htmlDialogBorder3(510, $rows, $tableheader, "defaultfont"),
				'space' => we_html_multiIconBox::SPACE_MED2),
		);


		return we_html_tools::getHtmlTop(g_l('modules_customer', '[import_title]'), '', '', STYLESHEET . we_html_multiIconBox::getJS(), we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "target" => "body"), $this->getHiddens(array("art" => self::ART_IMPORT, "step" => 4)) .
						we_html_multiIconBox::getHTML("xml", $parts, 30, "", -1, "", "", false, g_l('modules_customer', '[import_step4]'))
					)
				)
		);
	}

	function getHTMLImportStep5(){
		$tmpdir = we_base_request::_(we_base_request::FILE, "tmpdir");
		$impno = we_base_request::_(we_base_request::INT, "impno", 0);

		$table = new we_html_table(array(), 3, 1);
		$table->setCol(0, 0, array("class" => "defaultfont"), sprintf(g_l('modules_customer', '[import_finished_desc]'), $impno));

		if($tmpdir && is_file(TEMP_PATH . $tmpdir . '/' . $tmpdir . '.log') && is_readable(TEMP_PATH . "$tmpdir/$tmpdir.log")){
			$log = we_base_file::load(TEMP_PATH . $tmpdir . '/' . $tmpdir . '.log', 'rb');
			if($log){

				$table->setColContent(1, 0, we_html_tools::htmlAlertAttentionBox(g_l('modules_customer', '[show_log]'), we_html_tools::TYPE_ALERT, 550));
				$table->setColContent(2, 0, we_html_element::htmlTextArea(array("name" => "log", "rows" => 15, "cols" => 15, "style" => "width: 550px; height: 200px;"), oldHtmlspecialchars($log)));
				unlink(TEMP_PATH . "$tmpdir/$tmpdir.log");
			}
		}
		$parts = array(
			array(
				"headline" => "",
				"html" => $table->getHtml(),
				'space' => we_html_multiIconBox::SPACE_SMALL
			)
		);

		if(is_dir(TEMP_PATH . $tmpdir)){
			rmdir(TEMP_PATH . $tmpdir);
		}

		return we_html_tools::getHtmlTop(g_l('modules_customer', '[import_title]'), '', '', STYLESHEET . we_html_multiIconBox::getJS(), we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "target" => "load"), we_html_multiIconBox::getHTML("", $parts, 30, "", -1, "", "", false, g_l('modules_customer', '[import_step5]'))
					)
				)
		);
	}

	function getHTMLFooter($mode, $step){

		return ($mode == self::ART_EXPORT ?
				$this->getHTMLExportFooter($step) :
				$this->getHTMLImportFooter($step));
	}

	function getHTMLExportFooter($step = 1){
		$content = new we_html_table(array('class' => 'default', "width" => 575, "style" => "text-align:right"), 1, 2);

		if($step == 1){
			$buttons = we_html_button::position_yes_no_cancel(
					we_html_button::create_button(we_html_button::BACK, "", false, 100, 22, "", "", true) . we_html_button::create_button(we_html_button::NEXT, "javascript:top.load.location=WE().consts.dirs.WEBEDITION_DIR+'we_showMod.php?mod=customer&pnt=eiload&cmd=export_next&step=" . $step . "';"), we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close();")
			);
		} else if($step == 4){
			$buttons = we_html_button::position_yes_no_cancel(
					we_html_button::create_button(we_html_button::BACK, "", false, 100, 22, "", "", true) .
					we_html_button::create_button(we_html_button::NEXT, "", false, 100, 22, "", "", true), we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close();")
			);
			$text = g_l('modules_customer', '[exporting]');
			$progress = 0;
			$progressbar = new we_progressBar($progress);
			$progressbar->setStudLen(200);
			$progressbar->addText($text, 0, "current_description");

			$content->setCol(0, 0, null, (isset($progressbar) ? $progressbar->getHtml() : ""));
		} else if($step == 5){
			$buttons = we_html_button::position_yes_no_cancel(
					we_html_button::create_button(we_html_button::BACK, "", false, 100, 22, "", "", true) .
					we_html_button::create_button(we_html_button::NEXT, "", false, 100, 22, "", "", true), we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close();")
			);
		} else {
			$buttons = we_html_button::position_yes_no_cancel(
					we_html_button::create_button(we_html_button::BACK, "javascript:top.load.location=WE().consts.dirs.WEBEDITION_DIR+'we_showMod.php?mod=customer&pnt=eiload&cmd=export_back&step=" . $step . "';") .
					we_html_button::create_button(we_html_button::NEXT, "javascript:top.load.location=WE().consts.dirs.WEBEDITION_DIR+'we_showMod.php?mod=customer&pnt=eiload&cmd=export_next&step=" . $step . "';"), we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close();")
			);
		}
		$content->setCol(0, 1, array("style" => "text-align:right"), $buttons);

		return we_html_tools::getHtmlTop(''/* FIXME: missing title */, '', '', STYLESHEET . (isset($progressbar) ? $progressbar->getJSCode() : ''), we_html_element::htmlBody(array("class" => "weDialogButtonsBody"), we_html_element::htmlForm(array(
						"name" => "we_form",
						"method" => "post",
						"target" => "load",
						"action" => $this->frameset
						), $content->getHtml()
					)
				)
		);
	}

	function getHTMLImportFooter($step = 1){
		$content = new we_html_table(array('class' => 'default', "width" => 575, "style" => "text-align:right"), 1, 2);

		switch($step){
			case "1":
				$buttons = we_html_button::position_yes_no_cancel(
						we_html_button::create_button(we_html_button::BACK, "", false, 100, 22, "", "", true) .
						we_html_button::create_button(we_html_button::NEXT, "javascript:top.load.location=WE().consts.dirs.WEBEDITION_DIR+'we_showMod.php?mod=customer&pnt=eiload&cmd=import_next&step=" . $step . "';"), we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close();")
				);
				break;
			case "2":
				$buttons = we_html_button::position_yes_no_cancel(
						we_html_button::create_button(we_html_button::BACK, "javascript:top.load.location=WE().consts.dirs.WEBEDITION_DIR+'we_showMod.php?mod=customer&pnt=eiload&cmd=import_back&step=" . $step . "';") .
						we_html_button::create_button(we_html_button::NEXT, "javascript:top.load.location=WE().consts.dirs.WEBEDITION_DIR+'we_showMod.php?mod=customer&pnt=eiload&cmd=import_next&step=" . $step . "';", true, we_html_button::WIDTH, we_html_button::HEIGHT, '', '', false, false, '_footer'), we_html_button::create_button(we_html_button::CANCEL, "javascript:" . we_fileupload_ui_base::getJsBtnCmdStatic('cancel', 'body'))
				);
				break;
			case "5":
				$buttons = we_html_button::position_yes_no_cancel(
						we_html_button::create_button(we_html_button::BACK, "", false, 100, 22, "", "", true) .
						we_html_button::create_button(we_html_button::NEXT, "", false, 100, 22, "", "", true), we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close();")
				);
				$text = g_l('modules_customer', '[importing]');
				$progress = 0;
				$progressbar = new we_progressBar($progress);
				$progressbar->setStudLen(200);
				$progressbar->addText($text, 0, "current_description");

				$content->setCol(0, 0, null, (isset($progressbar) ? $progressbar->getHtml() : ""));
				break;
			case "6":
				$buttons = we_html_button::position_yes_no_cancel(
						we_html_button::create_button(we_html_button::CLOSE, "javascript:top.close();")
				);
				break;
			case "99":
				$buttons = we_html_button::position_yes_no_cancel(
						we_html_button::create_button(we_html_button::BACK, "javascript:top.load.location='" . $this->frameset . "&pnt=eiload&cmd=import_back&step=2';") .
						we_html_button::create_button(we_html_button::NEXT, "", false, 100, 22, "", "", true), we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close();")
				);
				break;
			default:
				$buttons = we_html_button::position_yes_no_cancel(
						we_html_button::create_button(we_html_button::BACK, "javascript:top.load.location='" . $this->frameset . "&pnt=eiload&cmd=import_back&step=" . $step . "';") .
						we_html_button::create_button(we_html_button::NEXT, "javascript:top.load.location='" . $this->frameset . "&pnt=eiload&cmd=import_next&step=" . $step . "';"), we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close();")
				);
		}
		$content->setCol(0, 1, array("style" => "text-align:right"), $buttons);

		return we_html_tools::getHtmlTop(''/* FIXME: missing title */, '', '', STYLESHEET . (isset($progressbar) ? $progressbar->getJSCode() : ""), we_html_element::htmlBody(array("class" => "weDialogButtonsBody"), we_html_element::htmlForm(array(
						"name" => "we_form",
						"method" => "post",
						"target" => "load",
						"action" => $this->frameset
						), $content->getHtml()
					)
				)
		);
	}

	private function getLoadCode(){
		if(($pid = we_base_request::_(we_base_request::INT, "pid"))){
			return we_html_element::jsElement("self.location='" . WEBEDITION_DIR . "we_cmd.php?we_cmd[0]=loadTree&we_cmd[1]=" . we_base_request::_(we_base_request::TABLE, "tab") . "&we_cmd[2]=" . $pid . "&we_cmd[3]=" . we_base_request::_(we_base_request::STRING, "openFolders") . "'");
		}
		return '';
	}

	private function getExportNextCode(){
		switch(we_base_request::_(we_base_request::INT, "step")){
			case 1:
			case 2:
			case 3:
			case 4:
				$head = we_html_element::jsElement('
function doNext(){
	top.body.document.we_form.step.value++;
	top.footer.location=WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=customer&pnt=eifooter&art=' . self::ART_EXPORT . '&step="+top.body.document.we_form.step.value;
	if(top.body.document.we_form.step.value>3){
		top.body.document.we_form.target="load";
		top.body.document.we_form.pnt.value="eiload";
		top.body.document.we_form.cmd.value="' . self::ART_EXPORT . '";
	}
	top.body.document.we_form.submit();
}');

				return we_html_tools::getHtmlTop(g_l('modules_customer', '[export_title]'), '', '', $head, we_html_element::htmlBody(array("bgcolor" => "#ffffff", "style" => 'margin:5px;', "onload" => "doNext()"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "target" => "body", "action" => $this->frameset), "")
						)
				);

			default:
				return '';
		}
	}

	private function getExportBackCode(){
		$head = we_html_element::jsElement('
function doNext(){
	top.body.document.we_form.step.value--;
	top.footer.location=WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=customer&pnt=eifooter&art=' . self::ART_EXPORT . '&step="+top.body.document.we_form.step.value;
	top.body.document.we_form.submit();
}');

		return we_html_tools::getHtmlTop(g_l('modules_customer', '[export_title]'), '', '', $head, we_html_element::htmlBody(array("bgcolor" => "#ffffff", "style" => 'margin:5px', "onload" => "doNext()"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "target" => "body", "action" => $this->frameset), "")
				)
		);
	}

	private function getExportCode(){
		$file_format = we_base_request::_(we_base_request::STRING, "type", we_import_functions::TYPE_GENERIC_XML);
		$file_name = we_base_request::_(we_base_request::FILE, "filename", date('Y-m-d'));
		$export_to = we_base_request::_(we_base_request::STRING, "export_to", self::EXPORT_SERVER);
		$path = ($export_to == self::EXPORT_SERVER ? we_base_request::_(we_base_request::FILE, "path", "") : rtrim(TEMP_DIR, '/'));
		$cdata = we_base_request::_(we_base_request::INT, "cdata", 0);
		$csv_delimiter = we_base_request::_(we_base_request::RAW_CHECKED, "csv_delimiter", "");
		$csv_enclose = we_base_request::_(we_base_request::RAW_CHECKED, "csv_enclose", "");
		$csv_lineend = we_base_request::_(we_base_request::STRING, "csv_lineend", "");
		$csv_fieldnames = we_base_request::_(we_base_request::BOOL, "csv_fieldnames");

		$customers = $this->getExportCustomers();
		if(!$customers){
//FIXME: add code to switch to previous page
			t_e('noting to export', $customers);
		}

		$hiddens = we_html_element::htmlHiddens(array(
				"pnt" => "eiload",
				"art" => self::ART_EXPORT,
				"customers" => implode(',', $customers),
				"file_format" => $file_format,
				"filename" => $file_name,
				"export_to" => $export_to,
				"path" => $path,
				"all" => count($customers),
				"cmd" => "do_export",
				"step" => 4));

		if($file_format == we_import_functions::TYPE_GENERIC_XML){
			$hiddens.=we_html_element::htmlHidden("cdata", $cdata);
		}

		if($file_format == self::TYPE_CSV){
			$hiddens.=
				($csv_enclose === '"' ?
					"<input type='hidden' name='csv_enclose' value='" . $csv_enclose . "' />" :
					we_html_element::htmlHidden("csv_enclose", $csv_enclose)
				) .
				we_html_element::htmlHiddens(array(
					"csv_delimiter" => $csv_delimiter,
					"csv_lineend" => $csv_lineend,
					"csv_fieldnames" => $csv_fieldnames ? 1 : 0));
		}

		return we_html_tools::getHtmlTop(g_l('modules_customer', '[export_title]'), '', '', STYLESHEET, we_html_element::htmlBody(array("onload" => "document.we_form.submit()"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "target" => "load", "action" => $this->frameset), $hiddens)
				)
		);
	}

	private function getDoExportCode(){
		$customers = we_base_request::_(we_base_request::INTLISTA, "customers", array());
		$file_format = we_base_request::_(we_base_request::STRING, "file_format", '');
		$export_to = we_base_request::_(we_base_request::STRING, "export_to", self::EXPORT_SERVER);
		$path = ($export_to == self::EXPORT_SERVER ? we_base_request::_(we_base_request::FILE, 'path', '') : TEMP_DIR);
		$filename = we_base_request::_(we_base_request::FILE, "filename", '');
		$firstexec = we_base_request::_(we_base_request::INT, "firstexec", -999);
		$all = we_base_request::_(we_base_request::INT, "all", 0);
		$cdata = we_base_request::_(we_base_request::INT, "cdata", 0);

		$hiddens = we_html_element::htmlHiddens(array(
				"file_format" => $file_format,
				"filename" => $filename,
				"export_to" => $export_to,
				"path" => $path));

		if($file_format == we_import_functions::TYPE_GENERIC_XML){
			$hiddens.=we_html_element::htmlHidden("cdata", $cdata);
		}
		if($file_format == self::TYPE_CSV){
			$csv_delimiter = we_base_request::_(we_base_request::RAW_CHECKED, 'csv_delimiter', '');
			$csv_enclose = we_base_request::_(we_base_request::RAW_CHECKED, 'csv_enclose', '');
			$csv_lineend = we_base_request::_(we_base_request::STRING, 'csv_lineend', '');
			$csv_fieldnames = we_base_request::_(we_base_request::BOOL, 'csv_fieldnames', true);

			$hiddens.=
				($csv_enclose === '"' ?
					"<input type='hidden' name='csv_enclose' value='" . $csv_enclose . "' />" :
					we_html_element::htmlHidden("csv_enclose", $csv_enclose)
				) .
				we_html_element::htmlHiddens(array(
					"csv_delimiter" => $csv_delimiter,
					"csv_lineend" => $csv_lineend));
		}
		if($customers){
			$options = array(
				"customers" => array(),
				"filename" => $_SERVER['DOCUMENT_ROOT'] . $path . "/" . $filename,
				"format" => $file_format,
				"firstexec" => $firstexec,
				"customers" => array_splice($customers, 0, $this->exim_number),
			);

			if($file_format == we_import_functions::TYPE_GENERIC_XML){
				$options['cdata'] = $cdata;
			}
			if($file_format == self::TYPE_CSV){
				$options["csv_delimiter"] = $csv_delimiter;
				$options["csv_enclose"] = $csv_enclose;
				$options["csv_lineend"] = $csv_lineend;
				$options["csv_fieldnames"] = ($firstexec == -999 ? $csv_fieldnames : false);
			}
			$success = we_customer_EI::exportCustomers($options);
		}

		$hiddens.=we_html_element::htmlHidden("art", self::ART_EXPORT) .
			($customers ?
				(
				we_html_element::htmlHiddens(array(
					"pnt" => "eiload",
					"cmd" => "do_export",
					"firstexec" => 0,
					"all" => $all,
					"customers" => implode(',', $customers)))
				) :
				(
				we_html_element::htmlHiddens(array(
					"pnt" => "eiload",
					"cmd" => "end_export"))
				//rray("name" => "success", "value" => $success))
				)
			);


		$exports = count($customers);
		$percent = max(min(($all ? (int) ((($all - $exports + 2) / $all) * 100) : 0), 100), 0);


		$progressjs = we_html_element::jsElement('if (top.footer.setProgress) top.footer.setProgress(' . $percent . ');');

		return we_html_tools::getHtmlTop(g_l('modules_customer', '[export_title]'), '', '', STYLESHEET . $progressjs, we_html_element::htmlBody(array("onload" => "document.we_form.submit()"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "target" => "load", "action" => $this->frameset), $hiddens)
				)
		);
	}

	private function getEndExportCode(){
		$export_to = we_base_request::_(we_base_request::FILE, "export_to", self::EXPORT_SERVER);
		$file_format = we_base_request::_(we_base_request::STRING, "file_format", '');
		$filename = we_base_request::_(we_base_request::FILE, "filename", '');
		$path = ($export_to == self::EXPORT_SERVER ? we_base_request::_(we_base_request::FILE, 'path', '') : TEMP_DIR);

		if($file_format == we_import_functions::TYPE_GENERIC_XML){

			$file_name = $_SERVER['DOCUMENT_ROOT'] . $path . '/' . $filename;
			we_customer_EI::save2File($file_name, we_backup_util::weXmlExImFooter);
		}

		return we_html_tools::getHtmlTop(g_l('modules_customer', '[export_title]'), '', '', STYLESHEET, we_html_element::htmlBody(array("onload" => "document.we_form.submit()"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "target" => "body", "action" => $this->frameset), we_html_element::htmlHiddens(array(
							"pnt" => "eibody",
							"step" => 4,
							"art" => self::ART_EXPORT,
							"export_to" => $export_to,
							"filename" => $filename,
							"path" => $path))
					)
				)
		);
	}

	private function getImportNextCode(){
		if(we_base_request::_(we_base_request::INT, "step") !== false){
			$head = we_html_element::jsElement('
function doNext(){
	if(top.body.document.we_form.step.value === "2" &&
			top.body.we_FileUpload !== undefined &&
			top.body.document.we_form.import_from[1].checked){
		' . we_fileupload_ui_base::getJsBtnCmdStatic('upload', 'body', 'doNextAction();') . '
		return;
	}
	doNextAction();
}

function doNextAction(){
	top.body.document.we_form.step.value++;
	top.footer.location=WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=customer&pnt=eifooter&art=' . self::ART_IMPORT . '&step="+top.body.document.we_form.step.value;
	if(top.body.document.we_form.step.value>4){
		top.body.document.we_form.target="load";
		top.body.document.we_form.pnt.value="eiload";
	}
	top.body.document.we_form.submit();
}');

			return we_html_tools::getHtmlTop(g_l('modules_customer', '[import_title]'), '', '', $head, we_html_element::htmlBody(array("bgcolor" => "#ffffff", "style" => 'margin: 5px', "onload" => "doNext()"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "target" => "body", "action" => $this->frameset), "")
					)
			);
		}
		return '';
	}

	private function getImportBackCode(){
		$head = we_html_element::jsElement('
function doNext(){
	top.body.document.we_form.step.value--;
	top.footer.location=WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=customer&pnt=eifooter&art=' . self::ART_IMPORT . '&step="+top.body.document.we_form.step.value;
	top.body.document.we_form.submit();
}');

		return we_html_tools::getHtmlTop(g_l('modules_customer', '[import_title]'), '', '', $head, we_html_element::htmlBody(array("bgcolor" => "#ffffff", "style" => 'margin: 5px', "onload" => "doNext()"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "target" => "body", "action" => $this->frameset), "")
				)
		);
	}

	private function getImportCode(){
		$filename = we_base_request::_(we_base_request::FILE, "filename", "");
//		$import_from = we_base_request::_(we_base_request::STRING, "import_from", "");
		$type = we_base_request::_(we_base_request::RAW, "type", "");
		$xml_from = we_base_request::_(we_base_request::RAW, "xml_from", "");
		$xml_to = we_base_request::_(we_base_request::RAW, "xml_to", "");
		$dataset = we_base_request::_(we_base_request::RAW, "dataset", "");
		$csv_delimiter = we_base_request::_(we_base_request::RAW_CHECKED, "csv_delimiter", self::CSV_DELIMITER);
		$csv_enclose = we_base_request::_(we_base_request::RAW_CHECKED, "csv_enclose", self::CSV_ENCLOSE);
		$csv_lineend = we_base_request::_(we_base_request::STRING, "csv_lineend", self::CSV_LINEEND);
		$the_charset = we_base_request::_(we_base_request::STRING, "the_charset", self::THE_CHARSET);
		$csv_fieldnames = we_base_request::_(we_base_request::BOOL, "csv_fieldnames", true);

		$same = we_base_request::_(we_base_request::STRING, "same", "rename");

		$field_mappings = we_base_request::_(we_base_request::RAW, "field_mappings", array());
		$att_mappings = we_base_request::_(we_base_request::RAW, "att_mappings", array());

		$options = array();
		$options["type"] = $type;
		$options["filename"] = $filename;
		$options["exim"] = $this->exim_number;
		if($type == self::TYPE_CSV){
			$options["csv_delimiter"] = $csv_delimiter;
			$options["csv_enclose"] = $csv_enclose;
			$options["csv_lineend"] = $csv_lineend;
			$options["the_charset"] = $the_charset;
			$options["csv_fieldnames"] = $csv_fieldnames;
		} else {
			$options["dataset"] = $dataset;
			$options["xml_from"] = $xml_from;
			$options["xml_to"] = $xml_to;
		}

		$filesnum = we_customer_EI::prepareImport($options);

		$hiddens = we_html_element::htmlHiddens(array(
				"pnt" => "eiload",
				"art" => self::ART_IMPORT,
				"cmd" => "do_import",
				"step" => 5,
				"tmpdir" => $filesnum["tmp_dir"],
				"fstart" => 0,
				"fcount" => $filesnum["file_count"],
				"same" => $same));

		foreach($field_mappings as $key => $field){
			$hiddens.=we_html_element::htmlHidden("field_mappings[$key]", "$field");
		}
		foreach($att_mappings as $key => $field){
			$hiddens.=we_html_element::htmlHidden("att_mappings[$key]", "$field");
		}

		$head = we_html_element::jsElement('
							function doNext(){
								top.step++;
								document.we_form.submit();
							}
					');

		return we_html_tools::getHtmlTop(g_l('modules_customer', '[import_title]'), '', '', $head, we_html_element::htmlBody(array("bgcolor" => "#ffffff", "style" => 'margin:5px', "onload" => "doNext()"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "target" => "load", "action" => $this->frameset), $hiddens)
				)
		);
	}

	private function getDoImportCode(){
		$tmpdir = we_base_request::_(we_base_request::FILE, "tmpdir", "");
		$fstart = we_base_request::_(we_base_request::INT, "fstart", 0);
		$fcount = we_base_request::_(we_base_request::INT, "fcount", "");
		$field_mappings = we_base_request::_(we_base_request::RAW, "field_mappings", array());
		$att_mappings = we_base_request::_(we_base_request::RAW, "att_mappings", array());
		$same = we_base_request::_(we_base_request::STRING, "same", "rename");
		$impno = we_base_request::_(we_base_request::INT, "impno", 0);

		if(we_customer_EI::importCustomers(array(
				"xmlfile" => TEMP_PATH . $tmpdir . '/temp_' . $fstart . '.xml',
				"field_mappings" => $field_mappings,
				"att_mappings" => $att_mappings,
				"same" => $same,
				"logfile" => TEMP_PATH . $tmpdir . '/' . $tmpdir . '.log'
				)
			)){
			$impno++;
		}
		$fstart++;

		$hiddens = we_html_element::htmlHiddens(array(
				"pnt" => "eiload",
				"art" => self::ART_IMPORT,
				"cmd" => "do_import",
				"tmpdir" => $tmpdir,
				"fstart" => $fstart,
				"fcount" => $fcount,
				"impno" => $impno,
				"same" => $same));

		foreach($field_mappings as $key => $field){
			$hiddens.=we_html_element::htmlHidden("field_mappings[" . $key . ']', $field);
		}
		foreach($att_mappings as $key => $field){
			$hiddens.=we_html_element::htmlHidden("att_mappings[" . $key . "]", $field);
		}

		$percent = ($fcount == 0 || $fcount == '0' ? 0 : min(100, max(0, (int) (($fstart / $fcount) * 100))) );

		$head = we_html_element::jsElement('
function doNext(){
	' . (!($fstart < $fcount) ? 'document.we_form.cmd.value="import_end";' : 'document.we_form.cmd.value="do_import";') . '
	if (top.footer.setProgress){
		top.footer.setProgress(' . $percent . ');
	}
	document.we_form.submit();
}');

		return we_html_tools::getHtmlTop(g_l('modules_customer', '[import_title]'), '', '', $head, we_html_element::htmlBody(array("bgcolor" => "#ffffff", "style" => 'margin:5px', "onload" => "doNext()"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "target" => "load", "action" => $this->frameset), $hiddens
					)
				)
		);
	}

	private function getEndImportCode(){
		$tmpdir = we_base_request::_(we_base_request::FILE, "tmpdir", "");
		$impno = we_base_request::_(we_base_request::INT, "impno", 0);

		$head = we_html_element::jsElement('
function doNext(){
		top.opener.top.content.applySort();//TODO: check this adress
		top.footer.location=WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=customer&pnt=eifooter&art=' . self::ART_IMPORT . '&step=6";
		document.we_form.submit();
}');

		return we_html_tools::getHtmlTop(g_l('modules_customer', '[import_title]'), '', '', $head, we_html_element::htmlBody(array("bgcolor" => "#ffffff", "style" => 'margin:5px', "onload" => "doNext()"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "target" => "body", "action" => $this->frameset), we_html_element::htmlHiddens(array(
							"tmpdir" => $tmpdir,
							"impno" => $impno,
							"pnt" => "eibody",
							"art" => self::ART_IMPORT,
							"step" => 5))
					)
				)
		);
	}

	function getHTMLLoad(){
		switch(we_base_request::_(we_base_request::STRING, 'cmd')){
			//------------------------ Export commands --------------------------------------------------------------
			case 'load':
				return $this->getLoadCode();
			case 'export_next':
				return $this->getExportNextCode();
			case 'export_back':
				return $this->getExportBackCode();
			case self::ART_EXPORT:
				return $this->getExportCode();
			case 'do_export':
				return $this->getDoExportCode();
			case 'end_export':
				return $this->getEndExportCode();
			//------------------------ Import commands --------------------------------------------------------------
			case 'import_next':
				return $this->getImportNextCode();
			case 'import_back':
				return $this->getImportBackCode();
			case 'import':
				return $this->getImportCode();
			case 'do_import':
				return $this->getDoImportCode();
			case 'import_end':
				return $this->getEndImportCode();
			default:
				return '';
		}
	}

	function getIDs($selIDs, $table){
		return array_filter($selIDs);
	}

	/* creates the FileChoooser field with the "browse"-Button. Clicking on the Button opens the fileselector */

	private function formFileChooser($width = "", $IDName = "ParentID", $IDValue = "/", $cmd = "", $filter = ""){

		$js = we_html_element::jsElement('
function formFileChooser() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);
	switch (args[0]) {
		case "browse_server":
			new (WE().util.jsWindow)(window, url,"server_selector",-1,-1,700,400,true,false,true);
		break;
	}
}');

		$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['" . $IDName . "'].value");
		$wecmdenc4 = we_base_request::encCmd(str_replace('\\', '', $cmd));
		$button = we_html_button::create_button(we_html_button::SELECT, "javascript:formFileChooser('browse_server','" . $wecmdenc1 . "','" . $filter . "',document.we_form.elements['" . $IDName . "'].value,'" . $wecmdenc4 . "');");

		return $js . we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($IDName, 30, $IDValue, "", ' readonly', "text", $width, 0), "", "left", "defaultfont", "", permissionhandler::hasPerm("CAN_SELECT_EXTERNAL_FILES") ? $button : "");
	}

	/* creates the DirectoryChoooser field with the "browse"-Button. Clicking on the Button opens the fileselector */

	function formDirChooser($width = "", $rootDirID = 0, $table = FILE_TABLE, $Pathname = "ParentPath", $Pathvalue = "", $IDName = "ParentID", $IDValue = "", $cmd = ""){

		$js = we_html_element::jsElement('
function formDirChooser() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);
	switch (args[0]) {
		case "we_selector_directory":
			new (WE().util.jsWindow)(window, url,"dir_selector",-1,-1,WE().consts.size.windowDirSelect.width,WE().consts.size.windowDirSelect.height,true,false,true,true);
		break;
	}
}');

		$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['" . $IDName . "'].value");
		$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['" . $Pathname . "'].value");
		$wecmdenc3 = we_base_request::encCmd(str_replace('\\', '', $cmd));
		$button = we_html_button::create_button(we_html_button::SELECT, "javascript:formDirChooser('we_selector_directory',document.we_form.elements['" . $IDName . "'].value,'" . FILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','','" . $rootDirID . "')");
		return $js . we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($Pathname, 30, $Pathvalue, "", ' readonly', "text", $width, 0), "", "left", "defaultfont", we_html_element::htmlHidden($IDName, $IDValue), $button);
	}

	function getHTMLCustomer(){

		switch(we_base_request::_(we_base_request::STRING, "wcmd")){
			case "add_customer":
				$customers = we_base_request::_(we_base_request::INTLISTA, "customers", array());
				$customers = array_unique(array_merge($customers, we_base_request::_(we_base_request::INTLISTA, "cus", array())));
				break;
			case "del_customer":
				$customers = we_base_request::_(we_base_request::INTLISTA, "customers", array());
				if(($id = we_base_request::_(we_base_request::INT, "cus"))){
					foreach($customers as $k => $v){
						if($v == $id){
							unset($customers[$k]);
						}
					}
				}
				break;
			case "del_all_customers":
				$customers = array();
				break;
			default:
				$customers = we_base_request::_(we_base_request::INTLISTA, "customers", array());
		}
		$customers = array_filter($customers);
		$js = we_html_element::jsElement('
function selector_cmd(){
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);
	switch (args[0]){
		case "we_selector_file":
			new (WE().util.jsWindow)(window, url,"we_selector",-1,-1,' . we_selector_file::WINDOW_SELECTOR_WIDTH . ',' . we_selector_file::WINDOW_SELECTOR_HEIGHT . ',true,true,true,true);
		break;
		case "add_customer":
		case "del_customer":
		case "del_all_customers":
			document.we_form.wcmd.value=args[0];
			document.we_form.cus.value=args[1];
			document.we_form.submit();
		break;
	}
}
top.customers="' . implode(',', $customers) . '";');

		$hiddens = we_html_element::htmlHiddens(array("wcmd" => "",
				"cus" => we_base_request::_(we_base_request::INTLIST, "cus", "")));


		$delallbut = we_html_button::create_button(we_html_button::DELETE_ALL, "javascript:selector_cmd('del_all_customers')", true, 0, 0, "", "", ($customers ? false : true));
		$addbut = we_html_button::create_button(we_html_button::ADD, "javascript:we_cmd('we_customer_selector','','" . CUSTOMER_TABLE . "','','','fillIDs();opener.top.body.selector_cmd(\'add_customer\',top.allIDs);')");
		$custs = new we_chooser_multiDir(400, ($customers ? : array()), "del_customer", $delallbut . $addbut, "", '"we/customer"', CUSTOMER_TABLE);

		$custs->isEditable = permissionhandler::hasPerm("EDIT_CUSTOMER");
		return $js . $hiddens . $custs->get();
	}

	private function getHTMLChooser($name, $value, $values, $title){

		$input_size = 5;

		$select = new we_html_select(array("name" => $name . "_select", 'onchange' => "document.we_form." . $name . ".value=this.options[this.selectedIndex].value;this.selectedIndex=0", "style" => "width:200px;"));
		$select->addOption("", "");
		foreach($values as $k => $v){
			$select->addOption(oldHtmlspecialchars($k), oldHtmlspecialchars($v));
		}
		$table = new we_html_table(array('class' => 'default', "width" => 250), 1, 3);

		$table->setColContent(0, 0, we_html_tools::htmlTextInput($name, $input_size, $value));
		$table->setCol(0, 1, array('style' => 'padding-left:10px;'), $select->getHtml());

		return we_html_tools::htmlFormElementTable($table->getHtml(), $title);
	}

	function getHTMLCustomerFilter(){
		$count = we_base_request::_(we_base_request::INT, "filter_count", 0);

		switch(we_base_request::_(we_base_request::STRING, "fcmd")){
			case "add_filter":
				$count++;
				break;
			case "del_filter":
				if($count){
					$count--;
				} else {
					$count = 0;
				}
				break;
			default:
		}

		$js = we_html_element::jsElement('
function filter_cmd(what){
	switch (what){
		case "add_filter":
		case "del_filter":
		case "del_all_filters":
			document.we_form.fcmd.value=what;
			document.we_form.submit();
			break;
	}
}
document.we_form.filter_count.value="' . $count . '";');

		$custfields = array();
		$this->db->query('SHOW FIELDS FROM ' . CUSTOMER_TABLE);
		while($this->db->next_record()){
			$fv = $this->db->f("Field");
			switch($fv){
				case 'ParentID':
				case 'IsFolder':
				case 'Path':
				case 'Text':
					break;
				default:
					$custfields[$fv] = $fv;
			}
		}

		$operators = array("=", "<>", "<", "<=", ">", ">=", "LIKE");
		$logic = array("AND" => "AND", "OR" => "OR");

		$table = new we_html_table(array('class' => 'default'), 1, 3);
		$colspan = "3";

		$c = 0;
		$fields_names = array("fieldname", "operator", "fieldvalue", "logic");

		for($i = 0; $i < $count; $i++){
			$new = array("fieldname" => "", "operator" => "", "fieldvalue" => "", "logic" => "");
			foreach($fields_names as $field){
				if(($val = we_base_request::_(we_base_request::STRING, "filter_" . $field . "_" . $i))){
					$new[$field] = $val;
				}
			}
			if($i != 0){
				$table->addRow();
				$table->setCol($c, 0, array("colspan" => $colspan), we_html_tools::htmlSelect("filter_logic_" . $i, $logic, 1, $new["logic"], false, array(), "value", 70));
				$c++;
			} else {
				$table->addRow();
				$table->setCol($c, 0, array("colspan" => $colspan), we_html_element::htmlHidden("filter_logic_0", ""));
				$c++;
			}

			$table->addRow();
			$table->setCol($c, 0, array(), we_html_tools::htmlSelect("filter_fieldname_" . $i, $custfields, 1, $new["fieldname"], false, array(), "value", 200));
			$table->setCol($c, 1, array(), we_html_tools::htmlSelect("filter_operator_" . $i, $operators, 1, $new["operator"], false, array(), "value", 70));
			$table->setCol($c, 2, array(), we_html_tools::htmlTextInput("filter_fieldvalue_" . $i, 16, $new["fieldvalue"]));
			$c++;
		}

		$plus = we_html_button::create_button(we_html_button::PLUS, "javascript:filter_cmd('add_filter')");
		$trash = we_html_button::create_button(we_html_button::TRASH, "javascript:filter_cmd('del_filter')");

		$c++;
		$table->addRow();
		$table->setCol($c, 0, array("colspan" => $colspan, 'style' => 'padding-top:5px;'), $plus . $trash);

		return $js .
			//(array("name"=>"filter_count","value"=>$count)).
			we_html_element::htmlHidden("fcmd", "") .
			$table->getHtml();
	}

	function getOperator($num){
		switch($num){
			case 0:
				return "=";
			case 1:
				return "<>";
			case 2:
				return "<";
			case 3:
				return "<=";
			case 4:
				return ">";
			case 5:
				return ">=";
			case 6:
				return "LIKE";
		}
	}

	private function getExportCustomers(){
		switch(we_base_request::_(we_base_request::STRING, 'selection')){
			case self::SELECTION_MANUAL:
				return we_base_request::_(we_base_request::INTLISTA, 'customers', array());
			default:
				$filter_count = we_base_request::_(we_base_request::INT, 'filter_count', 0);
				$filter_fieldname = $filter_operator = $filter_fieldvalue = $filter_logic = array();

				$fields_names = array('fieldname', 'operator', 'fieldvalue', 'logic');
				for($i = 0; $i < $filter_count; $i++){
					foreach($fields_names as $field){
						$var = "filter_" . $field;
						${$var}[] = we_base_request::_(we_base_request::STRING, $var . '_' . $i, 0);
					}
				}
				$filterarr = array();
				foreach($filter_fieldname as $k => $v){
					$filterarr[] = ($k ? (' ' . $filter_logic[$k] . ' ') : '') . $v . ' ' . $this->getOperator($filter_operator[$k]) . " '" . (is_numeric($filter_fieldvalue[$k]) ? $filter_fieldvalue[$k] : $this->db->escape($filter_fieldvalue[$k])) . "'";
				}

				$this->db->query('SELECT ID FROM ' . CUSTOMER_TABLE . ($filterarr ? ' WHERE (' . implode(' ', $filterarr) . ')' : ''));
				return $this->db->getAll(true);
		}
	}

}
