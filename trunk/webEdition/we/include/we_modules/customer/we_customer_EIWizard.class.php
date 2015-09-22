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
define("CSV_DELIMITER", ";");
define("CSV_ENCLOSE", "");
define("CSV_LINEEND", "windows");
define("THE_CHARSET", "UTF-8");

class we_customer_EIWizard{
	var $frameset;
	var $db;
	var $topFrame = "top";
	var $headerFrame = "top.header";
	var $loadFrame = "top.load";
	var $bodyFrame = "top.body";
	var $footerFrame = "top.footer";
	var $exim_number = 5;

	const SELECTION_MANUAL = 'manual';
	const SELECTION_FILTER = 'filter';
	const TYPE_CSV = 'csv'; //fixme: MOVE TO we_import_functions (name it TYPE_CSV)
	const EXPORT_SERVER = 'server';
	const EXPORT_LOCAL = 'local';
	const ART_IMPORT = 'import';
	const ART_EXPORT = 'export';

	function __construct(){
		$this->setFrameset(WE_CUSTOMER_MODULE_DIR . "edit_customer_frameset.php");
		$this->db = new DB_WE();
	}

	function setFrameset($frameset){
		$this->frameset = $frameset;
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

		$head = we_html_tools::getHtmlInnerHead(g_l('modules_customer', '[export_title]')) .
			we_html_element::jsElement('
			var table="' . FILE_TABLE . '";
			self.focus();
		') . STYLESHEET;

		$body = we_html_element::htmlBody(array('style' => 'background-color:grey;margin: 0px;position:fixed;top:0px;left:0px;right:0px;bottom:0px;border:0px none;')
				, we_html_element::htmlDiv(array('style' => 'position:absolute;top:0px;bottom:0px;left:0px;right:0px;')
					, we_html_element::htmlIFrame('body', $this->frameset . "?pnt=eibody&art=" . $mode . "&step=1", 'position:absolute;top:0px;bottom:45px;left:0px;right:0px;overflow: auto', 'border:0px;width:100%;height:100%;overflow: auto;') .
					we_html_element::htmlIFrame('footer', $this->frameset . "?pnt=eifooter&art=" . $mode . "&step=1", 'position:absolute;height:45px;bottom:0px;left:0px;right:0px;overflow: hidden') .
					we_html_element::htmlIFrame('load', $this->frameset . "?pnt=eiload&step=1", 'position:absolute;bottom:0px;height:0px;left:0px;right:0px;overflow: hidden;')
		));


		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
				we_html_element::htmlHead($head) .
				$body
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
				return we_html_element::htmlDocType() . we_html_element::htmlHtml(
						we_html_element::htmlHead(we_html_tools::getHtmlInnerHead(g_l('modules_customer', '[export_title]'))) .
						we_html_element::htmlBody(array("bgcolor" => "white", "marginwidth" => 10, "marginheight" => 10, "leftmargin" => 10, "topmargin" => 10), "aba")
				);
		}
	}

	function getHTMLExportStep1(){
		$type = we_base_request::_(we_base_request::STRING, "type", we_import_functions::TYPE_GENERIC_XML);

		$generic = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0), 3, 1);
		$generic->setCol(0, 0, array(), we_html_forms::radiobutton(we_import_functions::TYPE_GENERIC_XML, ($type == we_import_functions::TYPE_GENERIC_XML), "type", g_l('modules_customer', '[gxml_export]'), true, "defaultfont", "if(document.we_form.type[0].checked) " . $this->topFrame . ".type='" . we_import_functions::TYPE_GENERIC_XML . "';", false, g_l('modules_customer', '[txt_gxml_export]'), 0, 430));
		$generic->setCol(1, 0, array(), we_html_tools::getPixel(0, 4));
		$generic->setCol(2, 0, array(), we_html_forms::radiobutton(self::TYPE_CSV, ($type == self::TYPE_CSV), "type", g_l('modules_customer', '[csv_export]'), true, "defaultfont", "if(document.we_form.type[1].checked) " . $this->topFrame . ".type='" . self::TYPE_CSV . "';", false, g_l('modules_customer', '[txt_csv_export]'), 0, 430));

		$parts = array(
			array(
				"headline" => g_l('modules_customer', '[generic_export]'),
				"html" => $generic->getHTML(),
				"space" => 120,
				"noline" => 1)
		);

		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
				we_html_element::htmlHead(we_html_tools::getHtmlInnerHead(g_l('modules_customer', '[export_title]')) . STYLESHEET) .
				we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_element::htmlCenter(
						we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "target" => "body"),
							//we_html_element::htmlHidden(array("name"=>"pnt","value"=>"eibody")).
							//we_html_element::htmlHidden(array("name"=>"step","value"=>"1")).
							$this->getHiddens(array("art" => self::ART_EXPORT, "step" => 1)) .
							we_html_multiIconBox::getHTML("", "100%", $parts, 30, "", -1, "", "", false, g_l('modules_customer', '[export_step1]'))
						)
					)
				)
		);
	}

	function getHTMLExportStep2(){
		$selection = we_base_request::_(we_base_request::STRING, "selection", self::SELECTION_FILTER);

		$table = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0), 1, 2);
		$table->setColContent(0, 0, we_html_tools::getPixel(25, 5));
		$table->setColContent(0, 1, $this->getHTMLCustomerFilter()
		);

		$generic = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0), 8, 1);
		$generic->setColContent(0, 0, we_html_tools::getPixel(5, 10));
		$generic->setColContent(1, 0, we_html_forms::radiobutton(self::SELECTION_FILTER, ($selection == self::SELECTION_FILTER), "selection", g_l('modules_customer', '[filter_selection]'), true, "defaultfont", "if(document.we_form.selection[0].checked) " . $this->topFrame . ".selection='" . self::SELECTION_FILTER . "';"));
		$generic->setColContent(2, 0, we_html_tools::getPixel(5, 10));
		$generic->setColContent(3, 0, $table->getHtml());
		$generic->setColContent(4, 0, we_html_tools::getPixel(5, 10));

		$table->setColContent(0, 1, we_html_tools::htmlFormElementTable(
				$this->getHTMLCustomer(), g_l('modules_customer', '[customer]')
			)
		);
		$generic->setColContent(5, 0, we_html_forms::radiobutton(self::SELECTION_MANUAL, ($selection == self::SELECTION_MANUAL), "selection", g_l('modules_customer', '[manual_selection]'), true, "defaultfont", "if(document.we_form.selection[1].checked) " . $this->topFrame . ".selection='" . self::SELECTION_MANUAL . "';"));
		$generic->setColContent(6, 0, we_html_tools::getPixel(5, 10));
		$generic->setColContent(7, 0, $table->getHtml());

		$parts = array(array(
				"headline" => "",
				"html" => $generic->getHTML(),
				"space" => 30,
				"noline" => 1)
		);

		$js = we_html_element::jsElement('

			function doUnload() {
				if (!!jsWindow_count) {
					for (i = 0; i < jsWindow_count; i++) {
						eval("jsWindow" + i + "Object.close()");
					}
				}
			}

			function we_cmd(){
				var args = "";
				var url = "' . WEBEDITION_DIR . 'we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+encodeURI(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}
				switch (arguments[0]){
					case "del_customer":
						selector_cmd(arguments[0],arguments[1],arguments[2]);
					break;
				}
			}

			//' . $this->footerFrame . '.location="' . $this->frameset . '?pnt=eifooter&step="+' . $this->topFrame . '.step;

		');
		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
				we_html_element::htmlHead(we_html_tools::getHtmlInnerHead(g_l('modules_customer', '[export_title]')) . STYLESHEET . $js) .
				we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_element::htmlCenter(
						we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "target" => "body"), $this->getHiddens(array("art" => self::ART_EXPORT, "step" => 2)) .
							we_html_multiIconBox::getHTML("", "100%", $parts, 30, "", -1, "", "", false, g_l('modules_customer', '[export_step2]'))
						)
					)
				)
		);
	}

	function getHTMLExportStep3(){
		//	define different parts of the export wizard
		$_space = 150;
		$_input_size = 42;


		//set defaults
		$type = we_base_request::_(we_base_request::STRING, "type", we_import_functions::TYPE_GENERIC_XML);
		$filename = we_base_request::_(we_base_request::FILE, "filename", "weExport_" . time() . ($type == self::TYPE_CSV ? ".csv" : ".xml"));
		$export_to = we_base_request::_(we_base_request::STRING, "export_to", self::EXPORT_SERVER);
		$path = we_base_request::_(we_base_request::FILE, "path", "/");
		$cdata = we_base_request::_(we_base_request::INT, "cdata", true);

		$csv_delimiter = we_base_request::_(we_base_request::RAW, "csv_delimiter", CSV_DELIMITER);
		$csv_enclose = we_base_request::_(we_base_request::RAW, "csv_enclose", CSV_ENCLOSE);
		$csv_lineend = we_base_request::_(we_base_request::RAW, "csv_lineend", CSV_LINEEND);
		$csv_fieldnames = we_base_request::_(we_base_request::BOOL, "csv_fieldnames", true);

		switch(we_base_request::_(we_base_request::STRING, "selection")){
			case self::SELECTION_MANUAL:
				$customers = we_base_request::_(we_base_request::INTLISTA, "customers", array());
				break;
			default:
				$filter_count = we_base_request::_(we_base_request::INT, "filter_count", 0);
				$filter_fieldname = $filter_operator = $filter_fieldvalue = $filter_logic = array();

				$fields_names = array("fieldname", "operator", "fieldvalue", "logic");
				for($i = 0; $i < $filter_count; $i++){
					//$new = array("fieldname" => "", "operator" => "", "fieldvalue" => "", "logic" => "");
					foreach($fields_names as $field){
						$var = "filter_" . $field;
						$varname = $var . "_" . $i;
						if(($val = we_base_request::_(we_base_request::STRING, $varname))){
							${$var}[] = $val;
						}
					}
				}

				$filterarr = array();
				foreach($filter_fieldname as $k => $v){
					$filterarr[] = ($k ? (' ' . $filter_logic[$k-1] . ' ') : '') . $filter_fieldname[$k] . ' ' . $this->getOperator($filter_operator[$k]) . " '" . (is_numeric($filter_fieldvalue[$k]) ? $filter_fieldvalue[$k] : $this->db->escape($filter_fieldvalue[$k])) . "'";
				}

				$this->db->query('SELECT ID FROM ' . CUSTOMER_TABLE . ($filterarr ? ' WHERE (' . implode(' ', $filterarr) . ')' : ''));
				$customers = $this->db->getAll(true);
		}
		if($customers){
			//set variables in top frame
			$js = '';
			$parts = array(
				array(
					'headline' => g_l('modules_customer', '[filename]'),
					'html' => we_html_tools::htmlTextInput('filename', 						$_input_size, $filename),
					'space' => $_space
				),
			);

			switch($type){
				case we_import_functions::TYPE_GENERIC_XML:

					$table = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0), 3, 1);

					$table->setColContent(1, 0, we_html_tools::getPixel(1, 10));
					$table->setColContent(0, 0, we_html_forms::radiobutton(1, $cdata, "cdata", g_l('modules_customer', '[export_xml_cdata]'), true, "defaultfont", ""));
					$table->setColContent(2, 0, we_html_forms::radiobutton(0, !$cdata, "cdata", g_l('modules_customer', '[export_xml_entities]'), true, "defaultfont", ""));

					$parts[] = array("headline" => g_l('modules_customer', '[cdata]'), "html" => $table->getHtml(), "space" => $_space);

					break;
				case self::TYPE_CSV:
					$fileformattable = new we_html_table(array("cellpadding" => 2, "cellspacing" => 2, "border" => 0), 5, 1);

					$_file_encoding = new we_html_select(array("name" => "csv_lineend", "size" => 1, "class" => "defaultfont", "style" => "width: 254px;"));
					$_file_encoding->addOption("windows", g_l('modules_customer', '[windows]'));
					$_file_encoding->addOption("unix", g_l('modules_customer', '[unix]'));
					$_file_encoding->addOption("mac", g_l('modules_customer', '[mac]'));
					$_file_encoding->selectOption($csv_lineend);

					$fileformattable->setCol(0, 0, array("class" => "defaultfont"), we_html_tools::getPixel(10, 10));
					$fileformattable->setCol(1, 0, array("class" => "defaultfont"), g_l('modules_customer', '[csv_lineend]') . "<br/>" . $_file_encoding->getHtml());
					$fileformattable->setColContent(2, 0, $this->getHTMLChooser("csv_delimiter", $csv_delimiter, array("," => g_l('modules_customer', '[comma]'), ";" => g_l('modules_customer', '[semicolon]'), ":" => g_l('modules_customer', '[colon]'), "\\t" => g_l('modules_customer', '[tab]'), " " => g_l('modules_customer', '[space]')), g_l('modules_customer', '[csv_delimiter]')));
					$fileformattable->setColContent(3, 0, $this->getHTMLChooser("csv_enclose", $csv_enclose, array("\"" => g_l('modules_customer', '[double_quote]'), "'" => g_l('modules_customer', '[single_quote]')), g_l('modules_customer', '[csv_enclose]')));

					$fileformattable->setColContent(4, 0, we_html_forms::checkbox(1, $csv_fieldnames, "csv_fieldnames", g_l('modules_customer', '[csv_fieldnames]')));

					$parts[] = array("headline" => g_l('modules_customer', '[csv_params]'), "html" => $fileformattable->getHtml(), "space" => $_space);
			}

			$parts[] = array("headline" => g_l('modules_customer', '[export_to]'), "html" => "", "space" => 0, "noline" => 1);

			$table = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0), 1, 2);
			$table->setColContent(0, 0, we_html_tools::getPixel(20, 2));
			$table->setColContent(0, 1, we_html_forms::radiobutton(self::EXPORT_SERVER, ($export_to == self::EXPORT_SERVER), "export_to", g_l('modules_customer', '[export_to_server]'), true, "defaultfont", $this->topFrame . ".export_to='" . self::EXPORT_SERVER . "'"));
			$parts[] = array("space" => $_space, "noline" => 1,
				"headline" => $table->getHtml(),
				"html" =>
				we_html_element::htmlBr() .
				we_html_tools::htmlFormElementTable($this->formFileChooser(200, "path", $path, "", we_base_ContentTypes::FOLDER), g_l('modules_customer', '[path]'))
			);

			$table->setColContent(0, 1, we_html_forms::radiobutton(self::EXPORT_LOCAL, ($export_to == self::EXPORT_LOCAL), "export_to", g_l('modules_customer', '[export_to_local]'), true, "defaultfont", $this->topFrame . ".export_to='" . self::EXPORT_LOCAL . "'"));
			$parts[] = array("headline" => $table->getHtml(), "space" => $_space, "noline" => 1, "html" => "");
		} else {
			$parts = array(
				array("headline" => 'Fehler', "html" => '<b>Die Auswahl ist leer</b>', "space" => $_space)
			);
			$js = we_html_element::jsElement(
					$this->bodyFrame . '.document.we_form.step.value--;
	' . $this->footerFrame . '.location="' . $this->frameset . '?pnt=eifooter&art=' . self::ART_EXPORT . '&step="+' . $this->bodyFrame . '.document.we_form.step.value;
	' . $this->bodyFrame . '.document.we_form.submit();'
			); //FIXME: disable next button
		}
		$body = we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_element::htmlCenter(
					we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "target" => "body"),
						//we_html_element::htmlHidden(array("name"=>"step",""=>"4")).
						$this->getHiddens(array("art" => self::ART_EXPORT, "step" => 3)) .
						we_html_multiIconBox::getHTML("weExportWizard", "100%", $parts, 30, "", -1, "", "", false, g_l('modules_customer', '[export_step3]'))
					)
				)
		);

		return we_html_element::htmlDocType() . we_html_element::htmlHtml(we_html_element::htmlHead(STYLESHEET . $js) . $body);
	}

	function getHTMLExportStep4(){
		$export_to = we_base_request::_(we_base_request::STRING, 'export_to', self::EXPORT_SERVER);
		$path = urldecode(we_base_request::_(we_base_request::FILE, "path", ''));
		$filename = urldecode(we_base_request::_(we_base_request::FILE, "filename", ''));
		$js = we_html_element::jsElement($this->footerFrame . '.location="' . $this->frameset . '?pnt=eifooter&art=' . self::ART_EXPORT . '&step=5";');

		if($export_to == self::EXPORT_LOCAL){
			$message = we_html_element::htmlSpan(array("class" => "defaultfont"), g_l('modules_customer', '[export_finished]') . "<br/><br/>" .
					g_l('modules_customer', '[download_starting]') .
					we_html_element::htmlA(array("href" => $this->frameset . "?pnt=eibody&art=" . self::ART_EXPORT . "&step=5&exportfile=" . $filename), g_l('modules_customer', '[download]'))
			);
			return we_html_element::htmlDocType() . we_html_element::htmlHtml(
					we_html_element::htmlHead(we_html_tools::getHtmlInnerHead(g_l('modules_customer', '[export_title]')) . STYLESHEET . $js .
						we_html_element::htmlMeta(array("http-equiv" => "refresh", "content" => "2; URL=" . $this->frameset . "?pnt=eibody&art=" . self::ART_EXPORT . "&step=5&exportfile=" . $filename))
					) .
					we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_element::htmlCenter(
							we_html_tools::htmlDialogLayout($message, g_l('modules_customer', '[export_step4]'))
						)
					)
			);
		} else {
			$message = we_html_element::htmlSpan(array("class" => "defaultfont"), g_l('modules_customer', '[export_finished]') . "<br/><br/>" .
					g_l('modules_customer', '[server_finished]') . "<br/>" .
					rtrim($path, '/') . '/' . $filename
			);

			return we_html_element::htmlDocType() . we_html_element::htmlHtml(
					we_html_element::htmlHead(we_html_tools::getHtmlInnerHead(g_l('modules_customer', '[export_title]')) . STYLESHEET) . $js .
					we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_element::htmlCenter(
							we_html_tools::htmlDialogLayout($message, g_l('modules_customer', '[export_step4]'))
						)
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
		header("Location: " . $this->frameset . "?pnt=cmd&step=99&error=download_failed");
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
				$csv_delimiter = we_base_request::_(we_base_request::RAW, 'csv_delimiter', CSV_DELIMITER);
				$csv_enclose = we_base_request::_(we_base_request::RAW, 'csv_enclose', CSV_ENCLOSE);
				$csv_lineend = we_base_request::_(we_base_request::RAW, 'csv_lineend', CSV_LINEEND);
				$the_charset = we_base_request::_(we_base_request::RAW, 'the_charset', THE_CHARSET);

				$csv_fieldnames = we_base_request::_(we_base_request::BOOL, "csv_fieldnames");

				$source = we_base_request::_(we_base_request::FILE, 'source', '/');

				switch($options["step"]){
					case 1:
						return we_html_element::htmlHidden(array("name" => "pnt", "value" => "eibody")) .
							we_html_element::htmlHidden(array("name" => "step", "value" => $options["step"])) .
							we_html_element::htmlHidden(array("name" => "art", "value" => self::ART_IMPORT)) .
							($filename ? we_html_element::htmlHidden(array("name" => "filename", "value" => $filename)) : '') .
							we_html_element::htmlHidden(array("name" => "source", "value" => $source)) .
							we_html_element::htmlHidden(array("name" => "import_from", "value" => $import_from)) .
							we_html_element::htmlHidden(array("name" => "xml_from", "value" => $xml_from)) .
							we_html_element::htmlHidden(array("name" => "xml_to", "value" => $xml_to)) .
							we_html_element::htmlHidden(array("name" => "dataset", "value" => $dataset)) .
							we_html_element::htmlHidden(array("name" => "csv_delimiter", "value" => $csv_delimiter)) .
							we_html_element::htmlHidden(array("name" => "csv_enclose", "value" => $csv_enclose)) .
							we_html_element::htmlHidden(array("name" => "csv_lineend", "value" => $csv_lineend)) .
							we_html_element::htmlHidden(array("name" => "the_charset", "value" => $the_charset)) .
							we_html_element::htmlHidden(array("name" => "csv_fieldnames", "value" => $csv_fieldnames));

					case 2:
						return we_html_element::htmlHidden(array("name" => "pnt", "value" => "eibody")) .
							we_html_element::htmlHidden(array("name" => "step", "value" => $options["step"])) .
							we_html_element::htmlHidden(array("name" => "art", "value" => self::ART_IMPORT)) .
							we_html_element::htmlHidden(array("name" => "type", "value" => $type)) .
							($filename ? we_html_element::htmlHidden(array("name" => "filename", "value" => $filename)) : '') .
							we_html_element::htmlHidden(array("name" => "xml_from", "value" => $xml_from)) .
							we_html_element::htmlHidden(array("name" => "xml_to", "value" => $xml_to)) .
							we_html_element::htmlHidden(array("name" => "dataset", "value" => $dataset)) .
							we_html_element::htmlHidden(array("name" => "csv_delimiter", "value" => $csv_delimiter)) .
							we_html_element::htmlHidden(array("name" => "csv_enclose", "value" => $csv_enclose)) .
							we_html_element::htmlHidden(array("name" => "csv_lineend", "value" => $csv_lineend)) .
							we_html_element::htmlHidden(array("name" => "the_charset", "value" => $the_charset)) .
							we_html_element::htmlHidden(array("name" => "csv_fieldnames", "value" => $csv_fieldnames));

					case 3:
						return we_html_element::htmlHidden(array("name" => "pnt", "value" => "eibody")) .
							we_html_element::htmlHidden(array("name" => "step", "value" => $options["step"])) .
							we_html_element::htmlHidden(array("name" => "art", "value" => self::ART_IMPORT)) .
							we_html_element::htmlHidden(array("name" => "type", "value" => $type)) .
							we_html_element::htmlHidden(array("name" => "source", "value" => $source)) .
							($filename ? we_html_element::htmlHidden(array("name" => "filename", "value" => $filename)) : '') .
							we_html_element::htmlHidden(array("name" => "import_from", "value" => $import_from)) .
							we_html_element::htmlHidden(array("name" => "dataset", "value" => $dataset));

					case 4:
						return we_html_element::htmlHidden(array("name" => "pnt", "value" => "eibody")) .
							we_html_element::htmlHidden(array("name" => "step", "value" => $options["step"])) .
							we_html_element::htmlHidden(array("name" => "art", "value" => self::ART_IMPORT)) .
							we_html_element::htmlHidden(array("name" => "type", "value" => $type)) .
							($filename ? we_html_element::htmlHidden(array("name" => "filename", "value" => $filename)) : '') .
							we_html_element::htmlHidden(array("name" => "source", "value" => $source)) .
							we_html_element::htmlHidden(array("name" => "import_from", "value" => $import_from)) .
							we_html_element::htmlHidden(array("name" => "dataset", "value" => $dataset)) .
							we_html_element::htmlHidden(array("name" => "xml_from", "value" => $xml_from)) .
							we_html_element::htmlHidden(array("name" => "xml_to", "value" => $xml_to)) .
							we_html_element::htmlHidden(array("name" => "csv_delimiter", "value" => $csv_delimiter)) .
							'<input type="hidden" name="csv_enclose" value=' . ($csv_enclose === '"' ? "'\"'" : "\"$csv_enclose\"") . ' />' .
							we_html_element::htmlHidden(array("name" => "csv_lineend", "value" => $csv_lineend)) .
							we_html_element::htmlHidden(array("name" => "the_charset", "value" => $the_charset)) .
							we_html_element::htmlHidden(array("name" => "csv_fieldnames", "value" => $csv_fieldnames)) .
							we_html_element::htmlHidden(array("name" => "cmd", "value" => self::ART_IMPORT));
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

				$csv_delimiter = we_base_request::_(we_base_request::RAW, "csv_delimiter", CSV_DELIMITER);
				$csv_enclose = we_base_request::_(we_base_request::RAW, "csv_enclose", CSV_ENCLOSE);
				$csv_lineend = we_base_request::_(we_base_request::RAW, "csv_lineend", CSV_LINEEND);
				$csv_fieldnames = we_base_request::_(we_base_request::BOOL, "csv_fieldnames", true);

				$filter_count = we_base_request::_(we_base_request::INT, "filter_count", 0);
				$filter = "";
				$fields_names = array("fieldname", "operator", "fieldvalue", "logic");
				for($i = 0; $i < $filter_count; $i++){
					//$new = array("fieldname" => "", "operator" => "", "fieldvalue" => "", "logic" => "");
					foreach($fields_names as $field){
						$varname = "filter_" . $field . "_" . $i;
						if(($f = we_base_request::_(we_base_request::STRING, $varname)) !== false){
							$filter.=we_html_element::htmlHidden(array("name" => $varname, "value" => $f));
						}
					}
				}

				switch($options["step"]){
					case 1:
						return we_html_element::htmlHidden(array("name" => "pnt", "value" => "eibody")) .
							we_html_element::htmlHidden(array("name" => "step", "value" => $options["step"])) .
							we_html_element::htmlHidden(array("name" => "art", "value" => self::ART_EXPORT)) .
							we_html_element::htmlHidden(array("name" => "type", "value" => $type)) .
							we_html_element::htmlHidden(array("name" => "selection", "value" => $selection)) .
							we_html_element::htmlHidden(array("name" => "export_to", "value" => $export_to)) .
							we_html_element::htmlHidden(array("name" => "path", "value" => $path)) .
							we_html_element::htmlHidden(array("name" => "cdata", "value" => $cdata)) .
							we_html_element::htmlHidden(array("name" => "customers", "value" => $customers)) .
							($filename ? we_html_element::htmlHidden(array("name" => "filename", "value" => $filename)) : '') .
							we_html_element::htmlHidden(array("name" => "csv_delimiter", "value" => $csv_delimiter)) .
							we_html_element::htmlHidden(array("name" => "csv_enclose", "value" => $csv_enclose)) .
							we_html_element::htmlHidden(array("name" => "csv_lineend", "value" => $csv_lineend)) .
							we_html_element::htmlHidden(array("name" => "csv_fieldnames", "value" => $csv_fieldnames)) .
							we_html_element::htmlHidden(array("name" => "filter_count", "value" => $filter_count)) .
							$filter;

					case 2:
						return we_html_element::htmlHidden(array("name" => "pnt", "value" => "eibody")) .
							we_html_element::htmlHidden(array("name" => "step", "value" => $options["step"])) .
							we_html_element::htmlHidden(array("name" => "art", "value" => self::ART_EXPORT)) .
							we_html_element::htmlHidden(array("name" => "type", "value" => $type)) .
							we_html_element::htmlHidden(array("name" => "selection", "value" => $selection)) .
							we_html_element::htmlHidden(array("name" => "export_to", "value" => $export_to)) .
							we_html_element::htmlHidden(array("name" => "path", "value" => $path)) .
							we_html_element::htmlHidden(array("name" => "cdata", "value" => $cdata)) .
							we_html_element::htmlHidden(array("name" => "customers", "value" => $customers)) .
							($filename ? we_html_element::htmlHidden(array("name" => "filename", "value" => $filename)) : '') .
							we_html_element::htmlHidden(array("name" => "csv_delimiter", "value" => $csv_delimiter)) .
							we_html_element::htmlHidden(array("name" => "csv_enclose", "value" => $csv_enclose)) .
							we_html_element::htmlHidden(array("name" => "csv_lineend", "value" => $csv_lineend)) .
							we_html_element::htmlHidden(array("name" => "csv_fieldnames", "value" => $csv_fieldnames)) .
							we_html_element::htmlHidden(array("name" => "filter_count", "value" => $filter_count));

					case 3:
						return we_html_element::htmlHidden(array("name" => "pnt", "value" => "eibody")) .
							we_html_element::htmlHidden(array("name" => "step", "value" => $options["step"])) .
							we_html_element::htmlHidden(array("name" => "art", "value" => self::ART_EXPORT)) .
							we_html_element::htmlHidden(array("name" => "type", "value" => $type)) .
							we_html_element::htmlHidden(array("name" => "selection", "value" => $selection)) .
							we_html_element::htmlHidden(array("name" => "customers", "value" => $customers)) .
							we_html_element::htmlHidden(array("name" => "filter_count", "value" => $filter_count)) .
							we_html_element::htmlHidden(array("name" => "cmd", "value" => self::ART_EXPORT)) .
							$filter;

					case 4:
						return we_html_element::htmlHidden(array("name" => "pnt", "value" => "eibody")) .
							we_html_element::htmlHidden(array("name" => "step", "value" => $options["step"])) .
							we_html_element::htmlHidden(array("name" => "art", "value" => self::ART_EXPORT)) .
							we_html_element::htmlHidden(array("name" => "type", "value" => $type)) .
							we_html_element::htmlHidden(array("name" => "selection", "value" => $selection)) .
							we_html_element::htmlHidden(array("name" => "export_to", "value" => $export_to)) .
							we_html_element::htmlHidden(array("name" => "path", "value" => $path)) .
							we_html_element::htmlHidden(array("name" => "cdata", "value" => $cdata)) .
							we_html_element::htmlHidden(array("name" => "customers", "value" => $customers)) .
							($filename ? we_html_element::htmlHidden(array("name" => "filename", "value" => $filename)) : '') .
							we_html_element::htmlHidden(array("name" => "csv_delimiter", "value" => $csv_delimiter)) .
							we_html_element::htmlHidden(array("name" => "csv_enclose", "value" => $csv_enclose)) .
							we_html_element::htmlHidden(array("name" => "csv_lineend", "value" => $csv_lineend)) .
							we_html_element::htmlHidden(array("name" => "csv_fieldnames", "value" => $csv_fieldnames)) .
							we_html_element::htmlHidden(array("name" => "filter_count", "value" => $filter_count)) .
							$filter;
				}
				return '';
		}

		return '';
	}

	function getHTMLImportStep1(){
		$type = we_base_request::_(we_base_request::STRING, "type", we_import_functions::TYPE_GENERIC_XML);

		$generic = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0), 3, 1);
		$generic->setCol(0, 0, array(), we_html_forms::radiobutton(we_import_functions::TYPE_GENERIC_XML, ($type == we_import_functions::TYPE_GENERIC_XML), "type", g_l('modules_customer', '[gxml_import]'), true, "defaultfont", "if(document.we_form.type[0].checked) " . $this->topFrame . ".type='" . we_import_functions::TYPE_GENERIC_XML . "';", false, g_l('modules_customer', '[txt_gxml_import]'), 0, 430));
		$generic->setCol(1, 0, array(), we_html_tools::getPixel(0, 4));
		$generic->setCol(2, 0, array(), we_html_forms::radiobutton(self::TYPE_CSV, ($type == self::TYPE_CSV), "type", g_l('modules_customer', '[csv_import]'), true, "defaultfont", "if(document.we_form.type[1].checked) " . $this->topFrame . ".type='" . self::TYPE_CSV . "';", false, g_l('modules_customer', '[txt_csv_import]'), 0, 430));

		$parts = array(
			array(
				"headline" => g_l('modules_customer', '[generic_import]'),
				"html" => $generic->getHTML(),
				"space" => 120,
				"noline" => 1)
		);

		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
				we_html_element::htmlHead(we_html_tools::getHtmlInnerHead(g_l('modules_customer', '[import_title]')) . STYLESHEET) .
				we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_element::htmlCenter(
						we_html_element::htmlForm(array("name" => "we_form", "method" => "post"), $this->getHiddens(array("art" => self::ART_IMPORT, "step" => 1)) .
							we_html_multiIconBox::getHTML("", "100%", $parts, 30, "", -1, "", "", false, g_l('modules_customer', '[import_step1]'))
						)
					)
				)
		);
	}

	function getHTMLImportStep2(){
		$import_from = we_base_request::_(we_base_request::STRING, "import_from", self::EXPORT_SERVER);
		$source = we_base_request::_(we_base_request::FILE, "source", "/");
		$type = we_base_request::_(we_base_request::STRING, "type", "");

		$fileUploader = new we_fileupload_include('upload', 'body', 'footer', 'we_form', 'next_footer', false, 'top.load.doNextAction()', 'document.we_form.import_from[1].checked = true;', 369, true, true, 200);
		$fileUploader->setAction($this->frameset . '?pnt=eibody&art=import&step=3&import_from=' . self::EXPORT_LOCAL . '&type=' . $type);
		$fileUploader->setDimensions(array('alertBoxWidth' => 430, 'marginTop' => 10));

		$parts = array();
		$js = we_html_element::jsScript(JS_DIR . "windows.js") .
			we_html_element::jsElement('
					function switchImportFrom(obj){
						if(obj.value === "local"){
							top.footer.weButton.disable("next_footer");
						} else {
							top.footer.weButton.enable("next_footer");
						}
					}

					function callBack(){
						document.we_form.import_from[1].checked=true;
					}
				') . $fileUploader->getJs();
		$css = STYLESHEET . $fileUploader->getCss();

		$tmptable = new we_html_table(array("border" => 0, "cellpadding" => 0, "cellspacing" => 0), 4, 1);
		$tmptable->setCol(0, 0, array("valign" => "middle"), $this->formFileChooser(250, "source", $source, "opener." . $this->bodyFrame . ".document.we_form.import_from[0].checked=true;", ($type == we_import_functions::TYPE_GENERIC_XML ? we_base_ContentTypes::XML : "")));
		$tmptable->setCol(1, 0, array(), we_html_tools::getPixel(2, 5));

		$table = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0), 4, 2);
		$table->setCol(0, 0, array("colspan" => 2), we_html_forms::radiobutton(self::EXPORT_SERVER, ($import_from == self::EXPORT_SERVER), "import_from", g_l('modules_customer', '[server_import]'), true, "defaultfont", "switchImportFrom(this);"));
		$table->setColContent(1, 0, we_html_tools::getPixel(25, 5));
		$table->setColContent(2, 1, $tmptable->getHtml());

		$parts[] = array(
			"headline" => g_l('modules_customer', '[source_file]'),
			"html" => $table->getHtml(),
			"space" => 120,
			"noline" => 1
		);

		//upload table
		//$tmptable->setCol(0, 0, array(), we_html_tools::htmlAlertAttentionBox(sprintf(g_l('newFile', '[max_possible_size]'), we_base_file::getHumanFileSize($maxsize, we_base_file::SZ_MB)), we_html_tools::TYPE_ALERT, 430));
		$tmptable->setCol(0, 0, array(), $fileUploader->getHtmlAlertBoxes());
		$tmptable->setCol(1, 0, array(), we_html_tools::getPixel(2, 5));

		//$tmptable->setCol(2, 0, array("valign" => "middle"), we_html_tools::htmlTextInput("upload", 35, "", 255, "onclick=\"document.we_form.import_from[1].checked=true;\"", "file"));
		$tmptable->setCol(2, 0, array("valign" => "middle"), $fileUploader->getHTML());
		$tmptable->setCol(3, 0, array(), we_html_tools::getPixel(2, 5));

		$table = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0), 3, 2);
		$table->setCol(0, 0, array("colspan" => 2), we_html_forms::radiobutton(self::EXPORT_LOCAL, ($import_from == self::EXPORT_LOCAL), "import_from", g_l('modules_customer', '[upload_import]'), true, "defaultfont", "switchImportFrom(this)"));
		$table->setColContent(1, 0, we_html_tools::getPixel(25, 5));
		$table->setColContent(2, 1, $tmptable->getHtml());

		$parts[] = array(
			"headline" => "",
			"html" => $table->getHTML(),
			"space" => 120,
			"noline" => 1
		);

		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
				we_html_element::htmlHead(we_html_tools::getHtmlInnerHead(g_l('modules_customer', '[import_title]')) . $css . $js) .
				we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_element::htmlCenter(
						we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "target" => "body", "enctype" => "multipart/form-data"), $this->getHiddens(array("art" => self::ART_IMPORT, "step" => 2)) .
							we_html_multiIconBox::getHTML("", "100%", $parts, 30, "", -1, "", "", false, g_l('modules_customer', '[import_step2]'))
						)
					)
				)
		);
	}

	function getHTMLImportStep3(){
		$import_from = we_base_request::_(we_base_request::STRING, "import_from", self::EXPORT_SERVER);
		$source = we_base_request::_(we_base_request::FILE, "source", "/");
		$type = we_base_request::_(we_base_request::STRING, "type", "");
		$ext = $type == self::TYPE_CSV ? ".csv" : ".xml";
		$filename = "";
		$filesource = "";

		if($import_from == self::EXPORT_LOCAL){
			$fileUploader = new we_fileupload_include('upload');
			$fileUploader->setFileNameTemp(array('postfix' => $ext));

			if($fileUploader->processFileRequest()){
				//we have finished upload or we are in fallback mode
				$filesource = $fileUploader->getFileNameTemp();
				$filename = str_replace($_SERVER['DOCUMENT_ROOT'], '', $filesource);

				if(!$filename && isset($_FILES['upload']) && $_FILES["upload"]["size"]){
					//fallback mode
					$filename = TEMP_DIR . we_base_file::getUniqueId() . $ext;
					$filesource = $_SERVER['DOCUMENT_ROOT'] . $filename;
					move_uploaded_file($_FILES['upload']["tmp_name"], $filesource);
				}
			} else {
				//ajax response allready written: return here to send response only
				return;
			}
		} else {
			$filename = $source;
			$filesource = $_SERVER['DOCUMENT_ROOT'] . $filename;
		}

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

					$fileformattable = new we_html_table(array("cellpadding" => 2, "cellspacing" => 2, "border" => 0), 6, 1);

					$_file_encoding = new we_html_select(array("name" => "csv_lineend", "size" => 1, "class" => "defaultfont", "style" => "width: 254px;"));
					$_file_encoding->addOption('windows', g_l('modules_customer', '[windows]'));
					$_file_encoding->addOption('unix', g_l('modules_customer', '[unix]'));
					$_file_encoding->addOption('mac', g_l('modules_customer', '[mac]'));
					$_file_encoding->selectOption($csv_lineend);

					$_charsetHandler = new we_base_charsetHandler();
					$_charsets = $_charsetHandler->getCharsetsForTagWizzard();
					//$charset = $GLOBALS['WE_BACKENDCHARSET'];
					//$GLOBALS['weDefaultCharset'] = get_value("default_charset");
					$_importCharset = we_html_tools::htmlTextInput('the_charset', 8, ($charset === 'ASCII' ? 'ISO8859-1' : $charset), 255, '', 'text', 100);
					$_importCharsetChooser = we_html_tools::htmlSelect("ImportCharsetSelect", $_charsets, 1, ($charset === 'ASCII' ? 'ISO8859-1' : $charset), false, array("onchange" => "document.forms[0].elements['the_charset'].value=this.options[this.selectedIndex].value;this.selectedIndex=-1;"), "value", 160, "defaultfont", false);
					$import_Charset = '<table border="0" cellpadding="0" cellspacing="0"><tr><td>' . $_importCharset . '</td><td>' . $_importCharsetChooser . '</td></tr></table>';


					$fileformattable->setCol(0, 0, array("class" => "defaultfont"), we_html_tools::getPixel(10, 10));
					$fileformattable->setCol(1, 0, array("class" => "defaultfont"), g_l('modules_customer', '[csv_lineend]') . we_html_element::htmlBr() . $_file_encoding->getHtml());
					$fileformattable->setCol(2, 0, array("class" => "defaultfont"), g_l('modules_customer', '[import_charset]') . we_html_element::htmlBr() . $import_Charset);
					//$fileformattable->setCol(2, 0, array("class" => "defaultfont"), "abc");

					$fileformattable->setColContent(3, 0, $this->getHTMLChooser("csv_delimiter", $csv_delimiter, $csv_delimiters, g_l('modules_customer', '[csv_delimiter]')));
					$fileformattable->setColContent(4, 0, $this->getHTMLChooser("csv_enclose", $csv_enclose, $csv_encloses, g_l('modules_customer', '[csv_enclose]')));

					$fileformattable->setColContent(5, 0, we_html_forms::checkbox(1, $csv_fieldnames, "csv_fieldnames", g_l('modules_customer', '[csv_fieldnames]')));

					$parts = array(array("headline" => g_l('modules_customer', '[csv_params]'), "html" => $fileformattable->getHtml(), "space" => 150));
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
							'size' => 1,
							'class' => 'defaultfont',
							(($isSingleNode) ? "disabled" : "style") => "",
							'onchange' => "this.form.elements['xml_to'].value=this.options[this.selectedIndex].value; this.form.elements['xml_from'].value=1;this.form.elements['dataset'].value=this.options[this.selectedIndex].text;" .
							"if(this.options[this.selectedIndex].value==1) {this.form.elements['xml_from'].disabled=true;this.form.elements['xml_to'].disabled=true;} else {this.form.elements['xml_from'].disabled=false;this.form.elements['xml_to'].disabled=false;}")
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
						$parts = array(array("html" => $tblFrame->getHtml(), "space" => 0, "noline" => 1));
					}else {
						$parts = array(array("html" => we_html_tools::htmlAlertAttentionBox(g_l('modules_customer', (!$xmlWellFormed) ? '[not_well_formed]' : '[missing_child_node]'), we_html_tools::TYPE_ALERT, 570), "space" => 0, "noline" => 1));
						$js = we_html_element::jsElement($this->footerFrame . '.location="' . $this->frameset . '?pnt=eifooter&art=' . self::ART_IMPORT . '&step=99";');
					}
					break;
			}
		} else {
			$js = we_html_element::jsElement($this->footerFrame . '.location="' . $this->frameset . '?pnt=eifooter&art=' . self::ART_IMPORT . '&step=99";');
			$parts[] = array("html" => we_html_tools::htmlAlertAttentionBox(g_l('modules_customer', '[missing_filesource]'), we_html_tools::TYPE_ALERT, 570), "space" => 0, "noline" => 1);
		}

		$_REQUEST["filename"] = $filename;
		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
				we_html_element::htmlHead(we_html_tools::getHtmlInnerHead(g_l('modules_customer', '[import_title]')) . STYLESHEET . $js) .
				we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_element::htmlCenter(
						we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "target" => "body"), $this->getHiddens(array("art" => self::ART_IMPORT, "step" => 3)) .
							we_html_multiIconBox::getHTML("", "100%", $parts, 30, "", -1, "", "", false, g_l('modules_customer', '[import_step3]'))
						)
					)
				)
		);
	}

	function getHTMLImportStep4(){
		$filename = we_base_request::_(we_base_request::FILE, "filename", "");
		$type = we_base_request::_(we_base_request::STRING, "type", "");
		$dataset = we_base_request::_(we_base_request::RAW, "dataset", "");
		$csv_delimiter = we_base_request::_(we_base_request::RAW, "csv_delimiter", CSV_DELIMITER);
		$csv_enclose = we_base_request::_(we_base_request::RAW, "csv_enclose", CSV_ENCLOSE);
		$csv_lineend = we_base_request::_(we_base_request::RAW, "csv_lineend", CSV_LINEEND);
		$the_charset = we_base_request::_(we_base_request::RAW, "the_charset", THE_CHARSET);
		$csv_fieldnames = we_base_request::_(we_base_request::BOOL, "csv_fieldnames");
		$same = we_base_request::_(we_base_request::STRING, "same", "rename");

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
				"size" => 1,
				"class" => "defaultfont",
				"onclick" => "",
				"style" => "")
			);

			$we_fields->addOption("", g_l('modules_customer', '[any]'));

			foreach(array_keys($nodes) as $node){
				$we_fields->addOption(oldHtmlspecialchars(str_replace(" ", "", $node)), oldHtmlspecialchars($node));
				if(isset($field_mappings[$record])){
					if($node == $field_mappings[$record])
						$we_fields->selectOption($node);
				}
				else {
					if($node == $record)
						$we_fields->selectOption($node);
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

		$table = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0), 4, 1);
		$table->setColContent(0, 0, we_html_forms::radiobutton("rename", ($same === "rename"), "same", g_l('modules_customer', '[same_rename]'), true, "defaultfont", ""));
		$table->setColContent(1, 0, we_html_forms::radiobutton("overwrite", ($same === "overwrite"), "same", g_l('modules_customer', '[same_overwrite]'), true, "defaultfont", ""));
		$table->setColContent(2, 0, we_html_forms::radiobutton("skip", ($same === "skip"), "same", g_l('modules_customer', '[same_skip]'), true, "defaultfont", ""));

		$parts = array(
			array(
				"headline" => g_l('modules_customer', '[same_names]'),
				"html" => $table->getHtml(),
				"space" => 0
			),
			array(
				"headline" => g_l('modules_customer', '[import_step4]'),
				"html" => we_html_tools::getPixel(1, 8) . "<br/>" . we_html_tools::htmlDialogBorder3(510, 255, $rows, $tableheader, "defaultfont"),
				"space" => 150),
		);


		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
				we_html_element::htmlHead(we_html_tools::getHtmlInnerHead(g_l('modules_customer', '[import_title]')) . STYLESHEET . we_html_multiIconBox::getJS()) .
				we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_element::htmlCenter(
						we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "target" => "body"), $this->getHiddens(array("art" => self::ART_IMPORT, "step" => 4)) .
							we_html_multiIconBox::getHTML("xml", "100%", $parts, 30, "", -1, "", "", false, g_l('modules_customer', '[import_step4]'))
						)
					)
				)
		);
	}

	function getHTMLImportStep5(){
		$tmpdir = we_base_request::_(we_base_request::FILE, "tmpdir");
		$impno = we_base_request::_(we_base_request::INT, "impno", 0);

		$table = new we_html_table(array("cellpadding" => 2, "cellspacing" => 2, "border" => 0), 3, 1);
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
				"space" => 20
			)
		);

		if(is_dir(TEMP_PATH . $tmpdir)){
			rmdir(TEMP_PATH . $tmpdir);
		}

		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
				we_html_element::htmlHead(we_html_tools::getHtmlInnerHead(g_l('modules_customer', '[import_title]')) . STYLESHEET . we_html_multiIconBox::getJS()) .
				we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_element::htmlCenter(
						we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "target" => "load"), we_html_multiIconBox::getHTML("", "100%", $parts, 30, "", -1, "", "", false, g_l('modules_customer', '[import_step5]'))
						)
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
		$content = new we_html_table(array("border" => 0, "cellpadding" => 0, "cellspacing" => 0, "width" => 575, "align" => "right"), 1, 2);

		if($step == 1){
			$buttons = we_html_button::position_yes_no_cancel(
					we_html_button::create_button_table(array(
						we_html_button::create_button("back", "", false, 100, 22, "", "", true),
						we_html_button::create_button("next", "javascript:" . $this->loadFrame . ".location='" . $this->frameset . "?pnt=eiload&cmd=export_next&step=" . $step . "';"))
					), we_html_button::create_button("cancel", "javascript:top.close();")
			);
		} else if($step == 4){
			$buttons = we_html_button::position_yes_no_cancel(
					we_html_button::create_button_table(array(
						we_html_button::create_button("back", "", false, 100, 22, "", "", true),
						we_html_button::create_button("next", "", false, 100, 22, "", "", true))
					), we_html_button::create_button("cancel", "javascript:top.close();")
			);
			$text = g_l('modules_customer', '[exporting]');
			$progress = 0;
			$progressbar = new we_progressBar($progress);
			$progressbar->setStudLen(200);
			$progressbar->addText($text, 0, "current_description");

			$content->setCol(0, 0, null, (isset($progressbar) ? $progressbar->getHtml() : ""));
		} else if($step == 5){
			$buttons = we_html_button::position_yes_no_cancel(
					we_html_button::create_button_table(array(
						we_html_button::create_button("back", "", false, 100, 22, "", "", true),
						we_html_button::create_button("next", "", false, 100, 22, "", "", true))
					), we_html_button::create_button("cancel", "javascript:top.close();")
			);
		} else {
			$buttons = we_html_button::position_yes_no_cancel(
					we_html_button::create_button_table(array(
						we_html_button::create_button("back", "javascript:" . $this->loadFrame . ".location='" . $this->frameset . "?pnt=eiload&cmd=export_back&step=" . $step . "';"),
						we_html_button::create_button("next", "javascript:" . $this->loadFrame . ".location='" . $this->frameset . "?pnt=eiload&cmd=export_next&step=" . $step . "';"))
					), we_html_button::create_button("cancel", "javascript:top.close();")
			);
		}
		$content->setCol(0, 1, array("align" => "right"), $buttons);

		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
				we_html_element::htmlHead(//FIXME: missing title
					we_html_tools::getHtmlInnerHead() . STYLESHEET . "\n" . (isset($progressbar) ? $progressbar->getJSCode() . "\n" : "")
				) .
				we_html_element::htmlBody(array("class" => "weDialogButtonsBody"), we_html_element::htmlForm(array(
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
		$content = new we_html_table(array("border" => 0, "cellpadding" => 0, "cellspacing" => 0, "width" => 575, "align" => "right"), 1, 2);

		switch($step){
			case "1":
				$buttons = we_html_button::position_yes_no_cancel(
						we_html_button::create_button_table(array(
							we_html_button::create_button("back", "", false, 100, 22, "", "", true),
							we_html_button::create_button("next", "javascript:" . $this->loadFrame . ".location='" . $this->frameset . "?pnt=eiload&cmd=import_next&step=" . $step . "';"))
						), we_html_button::create_button("cancel", "javascript:top.close();")
				);
				break;
			case "2":
				$buttons = we_html_button::position_yes_no_cancel(
						we_html_button::create_button_table(array(
							we_html_button::create_button("back", "javascript:" . $this->loadFrame . ".location='" . $this->frameset . "?pnt=eiload&cmd=import_back&step=" . $step . "';"),
							we_html_button::create_button("next", "javascript:" . $this->loadFrame . ".location='" . $this->frameset . "?pnt=eiload&cmd=import_next&step=" . $step . "';", true, we_html_button::WIDTH, we_html_button::HEIGHT, '', '', false, false, '_footer'))
						), we_html_button::create_button("cancel", "javascript:" . we_fileupload_include::getJsBtnCmdStatic('cancel', 'body'))
				);
				break;
			case "5":
				$buttons = we_html_button::position_yes_no_cancel(
						we_html_button::create_button_table(array(
							we_html_button::create_button("back", "", false, 100, 22, "", "", true),
							we_html_button::create_button("next", "", false, 100, 22, "", "", true))
						), we_html_button::create_button("cancel", "javascript:top.close();")
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
						we_html_button::create_button("close", "javascript:top.close();")
				);
				break;
			case "99":
				$buttons = we_html_button::position_yes_no_cancel(
						we_html_button::create_button_table(array(
							we_html_button::create_button("back", "javascript:" . $this->loadFrame . ".location='" . $this->frameset . "?pnt=eiload&cmd=import_back&step=2';"),
							we_html_button::create_button("next", "", false, 100, 22, "", "", true))
						), we_html_button::create_button("cancel", "javascript:top.close();")
				);
				break;
			default:
				$buttons = we_html_button::position_yes_no_cancel(
						we_html_button::create_button_table(array(
							we_html_button::create_button("back", "javascript:" . $this->loadFrame . ".location='" . $this->frameset . "?pnt=eiload&cmd=import_back&step=" . $step . "';"),
							we_html_button::create_button("next", "javascript:" . $this->loadFrame . ".location='" . $this->frameset . "?pnt=eiload&cmd=import_next&step=" . $step . "';"))
						), we_html_button::create_button("cancel", "javascript:top.close();")
				);
		}
		$content->setCol(0, 1, array("align" => "right"), $buttons);

		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
				we_html_element::htmlHead(//FIXME: missing title
					we_html_tools::getHtmlInnerHead() . STYLESHEET . (isset($progressbar) ? $progressbar->getJSCode() : "")
				) .
				we_html_element::htmlBody(array("class" => "weDialogButtonsBody"), we_html_element::htmlForm(array(
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
			return we_html_element::jsElement("self.location='" . WE_EXPORT_MODULE_DIR . "exportLoadTree.php?we_cmd[1]=" . we_base_request::_(we_base_request::TABLE, "tab") . "&we_cmd[2]=" . $pid . "&we_cmd[3]=" . we_base_request::_(we_base_request::STRING, "openFolders") . "'");
		}
		return '';
	}

	private function getExportNextCode(){
		switch(we_base_request::_(we_base_request::INT, "step")){
			case 1:
			case 2:
			case 3:
			case 4:
				$head = we_html_tools::getHtmlInnerHead(g_l('modules_customer', '[export_title]')) .
					we_html_element::jsElement('
function doNext(){
	' . $this->bodyFrame . '.document.we_form.step.value++;
	' . $this->footerFrame . '.location="' . $this->frameset . '?pnt=eifooter&art=' . self::ART_EXPORT . '&step="+' . $this->bodyFrame . '.document.we_form.step.value;
	if(' . $this->bodyFrame . '.document.we_form.step.value>3){
		' . $this->bodyFrame . '.document.we_form.target="load";
		' . $this->bodyFrame . '.document.we_form.pnt.value="eiload";
		' . $this->bodyFrame . '.document.we_form.cmd.value="' . self::ART_EXPORT . '";
	}
	' . $this->bodyFrame . '.document.we_form.submit();
}');

				return we_html_element::htmlDocType() . we_html_element::htmlHtml(
						we_html_element::htmlHead($head) .
						we_html_element::htmlBody(array("bgcolor" => "#ffffff", "marginwidth" => 5, "marginheight" => 5, "leftmargin" => 5, "topmargin" => 5, "onload" => "doNext()"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "target" => "body", "action" => $this->frameset), "")
						)
				);

			default:
				return '';
		}
	}

	private function getExportBackCode(){
		$head = we_html_tools::getHtmlInnerHead(g_l('modules_customer', '[export_title]')) .
			we_html_element::jsElement('
function doNext(){
	' . $this->bodyFrame . '.document.we_form.step.value--;
	' . $this->footerFrame . '.location="' . $this->frameset . '?pnt=eifooter&art=' . self::ART_EXPORT . '&step="+' . $this->bodyFrame . '.document.we_form.step.value;
	' . $this->bodyFrame . '.document.we_form.submit();
}');

		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
				we_html_element::htmlHead($head) .
				we_html_element::htmlBody(array("bgcolor" => "#ffffff", "marginwidth" => 5, "marginheight" => 5, "leftmargin" => 5, "topmargin" => 5, "onload" => "doNext()"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "target" => "body", "action" => $this->frameset), "")
				)
		);
	}

	private function getExportCode(){
		$file_format = we_base_request::_(we_base_request::STRING, "type", we_import_functions::TYPE_GENERIC_XML);
		$file_name = we_base_request::_(we_base_request::FILE, "filename", date('Y-m-d'));
		$export_to = we_base_request::_(we_base_request::RAW, "export_to", self::EXPORT_SERVER);
		$path = ($export_to == self::EXPORT_SERVER ? we_base_request::_(we_base_request::FILE, "path", "") : rtrim(TEMP_DIR, '/'));
		$cdata = we_base_request::_(we_base_request::INT, "cdata", 0);
		$csv_delimiter = we_base_request::_(we_base_request::RAW, "csv_delimiter", "");
		$csv_enclose = we_base_request::_(we_base_request::RAW, "csv_enclose", "");
		$csv_lineend = we_base_request::_(we_base_request::RAW, "csv_lineend", "");
		$csv_fieldnames = we_base_request::_(we_base_request::BOOL, "csv_fieldnames");

		switch(we_base_request::_(we_base_request::STRING, "selection")){
			case self::SELECTION_MANUAL:
				$customers = we_base_request::_(we_base_request::INTLISTA, "customers", array());
				break;
			default:
				$filter_count = we_base_request::_(we_base_request::INT, "filter_count", 0);
				$filter_fieldname = $filter_operator = $filter_fieldvalue = $filter_logic = array();

				$fields_names = array("fieldname", "operator", "fieldvalue", "logic");
				for($i = 0; $i < $filter_count; $i++){
					$new = array("fieldname" => "", "operator" => "", "fieldvalue" => "", "logic" => "");
					foreach($fields_names as $field){
						$var = "filter_" . $field;
						$varname = $var . "_" . $i;
						if(($val = we_base_request::_(we_base_request::STRING, $varname))){
							${$var}[] = $val;
						}
					}
				}

				$filterarr = array();
				foreach($filter_fieldname as $k => $v){
					$op = $this->getOperator($filter_operator[$k]);
					$filterarr[] = ($k ? (" " . $filter_logic[$k] . " ") : "") . $filter_fieldname[$k] . " " . $op . " '" . (is_numeric($filter_fieldvalue[$k]) ? $filter_fieldvalue[$k] : $this->db->escape($filter_fieldvalue[$k])) . "'";
				}

				$this->db->query('SELECT ID FROM ' . CUSTOMER_TABLE . ($filterarr ? ' WHERE (' . implode(' ', $filterarr) . ')' : ""));
				$customers = $this->db->getAll(true);
		}

		if(!$customers){
//FIXME: add code to switch to previous page
			t_e('noting to export', $customers, $_REQUEST);
		}

		$hiddens = we_html_element::htmlHidden(array("name" => "pnt", "value" => "eiload")) .
			we_html_element::htmlHidden(array("name" => "art", "value" => self::ART_EXPORT)) .
			we_html_element::htmlHidden(array("name" => "customers", "value" => implode(',', $customers))) .
			we_html_element::htmlHidden(array("name" => "file_format", "value" => $file_format)) .
			we_html_element::htmlHidden(array("name" => "filename", "value" => $file_name)) .
			we_html_element::htmlHidden(array("name" => "export_to", "value" => $export_to)) .
			we_html_element::htmlHidden(array("name" => "path", "value" => $path)) .
			we_html_element::htmlHidden(array("name" => "all", "value" => count($customers))) .
			we_html_element::htmlHidden(array("name" => "cmd", "value" => "do_export")) .
			we_html_element::htmlHidden(array("name" => "step", "value" => 4));

		if($file_format == we_import_functions::TYPE_GENERIC_XML){
			$hiddens.=we_html_element::htmlHidden(array("name" => "cdata", "value" => $cdata));
		}

		if($file_format == self::TYPE_CSV){
			$hiddens.=we_html_element::htmlHidden(array("name" => "csv_delimiter", "value" => $csv_delimiter)) .
				($csv_enclose === '"' ?
					"<input type='hidden' name='csv_enclose' value='" . $csv_enclose . "' />" :
					we_html_element::htmlHidden(array("name" => "csv_enclose", "value" => $csv_enclose))
				) .
				we_html_element::htmlHidden(array("name" => "csv_lineend", "value" => $csv_lineend)) .
				we_html_element::htmlHidden(array("name" => "csv_fieldnames", "value" => $csv_fieldnames ? 1 : 0));
		}

		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
				we_html_element::htmlHead(we_html_tools::getHtmlInnerHead(g_l('modules_customer', '[export_title]')) . STYLESHEET) .
				we_html_element::htmlBody(array("onload" => "document.we_form.submit()"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "target" => "load", "action" => $this->frameset), $hiddens)
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

		$hiddens = we_html_element::htmlHidden(array("name" => "file_format", "value" => $file_format)) .
			we_html_element::htmlHidden(array("name" => "filename", "value" => $filename)) .
			we_html_element::htmlHidden(array("name" => "export_to", "value" => $export_to)) .
			we_html_element::htmlHidden(array("name" => "path", "value" => $path));

		if($file_format == we_import_functions::TYPE_GENERIC_XML){
			$hiddens.=we_html_element::htmlHidden(array("name" => "cdata", "value" => $cdata));
		}
		if($file_format == self::TYPE_CSV){
			$csv_delimiter = we_base_request::_(we_base_request::RAW, 'csv_delimiter', '');
			$csv_enclose = we_base_request::_(we_base_request::RAW, 'csv_enclose', '');
			$csv_lineend = we_base_request::_(we_base_request::RAW, 'csv_lineend', '');
			$csv_fieldnames = we_base_request::_(we_base_request::BOOL, 'csv_fieldnames', true);

			$hiddens.=we_html_element::htmlHidden(array("name" => "csv_delimiter", "value" => $csv_delimiter)) .
				($csv_enclose === '"' ?
					"<input type='hidden' name='csv_enclose' value='" . $csv_enclose . "' />" :
					we_html_element::htmlHidden(array("name" => "csv_enclose", "value" => $csv_enclose))
				) .
				we_html_element::htmlHidden(array("name" => "csv_lineend", "value" => $csv_lineend));
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
				$options["csv_fieldnames"] = $csv_fieldnames;
			}
			$success = we_customer_EI::exportCustomers($options);
		}

		$hiddens.=we_html_element::htmlHidden(array("name" => "art", "value" => self::ART_EXPORT)) .
			($customers ?
				(
				we_html_element::htmlHidden(array("name" => "pnt", "value" => "eiload")) .
				we_html_element::htmlHidden(array("name" => "cmd", "value" => "do_export")) .
				we_html_element::htmlHidden(array("name" => "firstexec", "value" => 0)) .
				we_html_element::htmlHidden(array("name" => "all", "value" => $all)) .
				we_html_element::htmlHidden(array("name" => "customers", "value" => implode(',', $customers)))
				) :
				(
				we_html_element::htmlHidden(array("name" => "pnt", "value" => "eiload")) .
				we_html_element::htmlHidden(array("name" => "cmd", "value" => "end_export"))
				//we_html_element::htmlHidden(array("name" => "success", "value" => $success))
				)
			);


		$exports = count($customers);
		$percent = max(min(($all ? (int) ((($all - $exports + 2) / $all) * 100) : 0), 100), 0);


		$progressjs = we_html_element::jsElement('if (top.footer.setProgress) top.footer.setProgress(' . $percent . ');');

		$head = we_html_tools::getHtmlInnerHead(g_l('modules_customer', '[export_title]')) . STYLESHEET . $progressjs;
		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
				we_html_element::htmlHead($head) .
				we_html_element::htmlBody(array("onload" => "document.we_form.submit()"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "target" => "load", "action" => $this->frameset), $hiddens)
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
			we_customer_EI::save2File($file_name, we_backup_backup::weXmlExImFooter);
		}

		$head = we_html_tools::getHtmlInnerHead(g_l('modules_customer', '[export_title]')) . STYLESHEET;
		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
				we_html_element::htmlHead($head) .
				we_html_element::htmlBody(array("onload" => "document.we_form.submit()"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "target" => "body", "action" => $this->frameset), we_html_element::htmlHidden(array("name" => "pnt", "value" => "eibody")) .
						we_html_element::htmlHidden(array("name" => "step", "value" => 4)) .
						we_html_element::htmlHidden(array("name" => "art", "value" => self::ART_EXPORT)) .
						we_html_element::htmlHidden(array("name" => "export_to", "value" => $export_to)) .
						we_html_element::htmlHidden(array("name" => "filename", "value" => $filename)) .
						we_html_element::htmlHidden(array("name" => "path", "value" => $path))
					)
				)
		);
	}

	private function getImportNextCode(){
		if(we_base_request::_(we_base_request::INT, "step") !== false){
			$head = we_html_tools::getHtmlInnerHead(g_l('modules_customer', '[import_title]')) .
				we_html_element::jsElement('
function doNext(){
	if(' . $this->bodyFrame . '.document.we_form.step.value === "2" &&
			typeof ' . $this->bodyFrame . '.we_FileUpload !== "undefined" &&
			' . $this->bodyFrame . '.document.we_form.import_from[1].checked){
		' . we_fileupload_include::getJsBtnCmdStatic('upload', 'body', 'doNextAction();') . '
		return;
	}
	doNextAction();
}

function doNextAction(){
	' . $this->bodyFrame . '.document.we_form.step.value++;
	' . $this->footerFrame . '.location="' . $this->frameset . '?pnt=eifooter&art=' . self::ART_IMPORT . '&step="+' . $this->bodyFrame . '.document.we_form.step.value;
	if(' . $this->bodyFrame . '.document.we_form.step.value>4){
		' . $this->bodyFrame . '.document.we_form.target="load";
		' . $this->bodyFrame . '.document.we_form.pnt.value="eiload";
	}
	' . $this->bodyFrame . '.document.we_form.submit();
}');

			return we_html_element::htmlDocType() . we_html_element::htmlHtml(
					we_html_element::htmlHead($head) .
					we_html_element::htmlBody(array("bgcolor" => "#ffffff", "marginwidth" => 5, "marginheight" => 5, "leftmargin" => 5, "topmargin" => 5, "onload" => "doNext()"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "target" => "body", "action" => $this->frameset), "")
					)
			);
		}
		return '';
	}

	private function getImportBackCode(){
		$head = we_html_tools::getHtmlInnerHead(g_l('modules_customer', '[import_title]')) .
			we_html_element::jsElement('
function doNext(){
	' . $this->bodyFrame . '.document.we_form.step.value--;
	' . $this->footerFrame . '.location="' . $this->frameset . '?pnt=eifooter&art=' . self::ART_IMPORT . '&step="+' . $this->bodyFrame . '.document.we_form.step.value;
	' . $this->bodyFrame . '.document.we_form.submit();
}');

		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
				we_html_element::htmlHead($head) .
				we_html_element::htmlBody(array("bgcolor" => "#ffffff", "marginwidth" => 5, "marginheight" => 5, "leftmargin" => 5, "topmargin" => 5, "onload" => "doNext()"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "target" => "body", "action" => $this->frameset), "")
				)
		);
	}

	private function getImportCode(){
		$filename = we_base_request::_(we_base_request::FILE, "filename", "");
		$import_from = we_base_request::_(we_base_request::RAW, "import_from", "");
		$type = we_base_request::_(we_base_request::RAW, "type", "");
		$xml_from = we_base_request::_(we_base_request::RAW, "xml_from", "");
		$xml_to = we_base_request::_(we_base_request::RAW, "xml_to", "");
		$dataset = we_base_request::_(we_base_request::RAW, "dataset", "");
		$csv_delimiter = we_base_request::_(we_base_request::RAW, "csv_delimiter", CSV_DELIMITER);
		$csv_enclose = we_base_request::_(we_base_request::RAW, "csv_enclose", CSV_ENCLOSE);
		$csv_lineend = we_base_request::_(we_base_request::RAW, "csv_lineend", CSV_LINEEND);
		$the_charset = we_base_request::_(we_base_request::RAW, "the_charset", THE_CHARSET);
		$csv_fieldnames = we_base_request::_(we_base_request::BOOL, "csv_fieldnames", true);

		$same = we_base_request::_(we_base_request::RAW, "same", "rename");

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

		$hiddens = we_html_element::htmlHidden(array("name" => "pnt", "value" => "eiload")) .
			we_html_element::htmlHidden(array("name" => "art", "value" => self::ART_IMPORT)) .
			we_html_element::htmlHidden(array("name" => "cmd", "value" => "do_import")) .
			we_html_element::htmlHidden(array("name" => "step", "value" => 5)) .
			we_html_element::htmlHidden(array("name" => "tmpdir", "value" => $filesnum["tmp_dir"])) .
			we_html_element::htmlHidden(array("name" => "fstart", "value" => 0)) .
			we_html_element::htmlHidden(array("name" => "fcount", "value" => $filesnum["file_count"])) .
			we_html_element::htmlHidden(array("name" => "same", "value" => $same));

		foreach($field_mappings as $key => $field){
			$hiddens.=we_html_element::htmlHidden(array("name" => "field_mappings[$key]", "value" => "$field"));
		}
		foreach($att_mappings as $key => $field){
			$hiddens.=we_html_element::htmlHidden(array("name" => "att_mappings[$key]", "value" => "$field"));
		}

		$head = we_html_tools::getHtmlInnerHead(g_l('modules_customer', '[import_title]')) .
			we_html_element::jsElement('
							function doNext(){
								' . $this->topFrame . '.step++;
								document.we_form.submit();
							}
					');

		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
				we_html_element::htmlHead($head) .
				we_html_element::htmlBody(array("bgcolor" => "#ffffff", "marginwidth" => 5, "marginheight" => 5, "leftmargin" => 5, "topmargin" => 5, "onload" => "doNext()"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "target" => "load", "action" => $this->frameset), $hiddens)
				)
		);
	}

	private function getDoImportCode(){
		$tmpdir = we_base_request::_(we_base_request::FILE, "tmpdir", "");
		$fstart = we_base_request::_(we_base_request::INT, "fstart", 0);
		$fcount = we_base_request::_(we_base_request::INT, "fcount", "");
		$field_mappings = we_base_request::_(we_base_request::RAW, "field_mappings", array());
		$att_mappings = we_base_request::_(we_base_request::RAW, "att_mappings", array());
		$same = we_base_request::_(we_base_request::RAW, "same", "rename");
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

		$hiddens = we_html_element::htmlHidden(array("name" => "pnt", "value" => "eiload")) .
			we_html_element::htmlHidden(array("name" => "art", "value" => self::ART_IMPORT)) .
			we_html_element::htmlHidden(array("name" => "cmd", "value" => "do_import")) .
			we_html_element::htmlHidden(array("name" => "tmpdir", "value" => $tmpdir)) .
			we_html_element::htmlHidden(array("name" => "fstart", "value" => $fstart)) .
			we_html_element::htmlHidden(array("name" => "fcount", "value" => $fcount)) .
			we_html_element::htmlHidden(array("name" => "impno", "value" => $impno)) .
			we_html_element::htmlHidden(array("name" => "same", "value" => $same));

		foreach($field_mappings as $key => $field){
			$hiddens.=we_html_element::htmlHidden(array("name" => "field_mappings[$key]", "value" => "$field"));
		}
		foreach($att_mappings as $key => $field){
			$hiddens.=we_html_element::htmlHidden(array("name" => "att_mappings[$key]", "value" => "$field"));
		}

		$percent = ($fcount == 0 || $fcount == '0' ? 0 : min(100, max(0, (int) (($fstart / $fcount) * 100))) );

		$head = we_html_tools::getHtmlInnerHead(g_l('modules_customer', '[import_title]')) .
			we_html_element::jsElement('
function doNext(){
	' . (!($fstart < $fcount) ? 'document.we_form.cmd.value="import_end";' : 'document.we_form.cmd.value="do_import";') . '
	if (' . $this->footerFrame . '.setProgress) ' . $this->footerFrame . '.setProgress(' . $percent . ');
	document.we_form.submit();
}');

		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
				we_html_element::htmlHead($head) .
				we_html_element::htmlBody(array("bgcolor" => "#ffffff", "marginwidth" => 5, "marginheight" => 5, "leftmargin" => 5, "topmargin" => 5, "onload" => "doNext()"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "target" => "load", "action" => $this->frameset), $hiddens
					)
				)
		);
	}

	private function getEndImportCode(){
		$tmpdir = we_base_request::_(we_base_request::FILE, "tmpdir", "");
		$impno = we_base_request::_(we_base_request::INT, "impno", 0);

		$head = we_html_tools::getHtmlInnerHead(g_l('modules_customer', '[import_title]')) .
			we_html_element::jsElement('
function doNext(){
		top.opener.top.content.treeheader.applySort();//TODO: check this adress
		' . $this->footerFrame . '.location="' . $this->frameset . '?pnt=eifooter&art=' . self::ART_IMPORT . '&step=6";
		document.we_form.submit();
}');

		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
				we_html_element::htmlHead($head) .
				we_html_element::htmlBody(array("bgcolor" => "#ffffff", "marginwidth" => 5, "marginheight" => 5, "leftmargin" => 5, "topmargin" => 5, "onload" => "doNext()"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "target" => "body", "action" => $this->frameset), we_html_element::htmlHidden(array("name" => "tmpdir", "value" => $tmpdir)) .
						we_html_element::htmlHidden(array("name" => "impno", "value" => $impno)) .
						we_html_element::htmlHidden(array("name" => "pnt", "value" => "eibody")) .
						we_html_element::htmlHidden(array("name" => "art", "value" => self::ART_IMPORT)) .
						we_html_element::htmlHidden(array("name" => "step", "value" => 5))
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
			case self::ART_IMPORT:
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
		$ret = array();
		$tmp = array();
		foreach($selIDs as $v){
			if($v){
				$isfolder = f("SELECT IsFolder FROM " . $this->db->escape($table) . " WHERE ID=" . intval($v), "IsFolder", $this->db);
				if($isfolder){
					we_readChilds($v, $tmp, $table, false);
				} else {
					$tmp[] = $v;
				}
			}
		}
		foreach($tmp as $v){
			$isfolder = f("SELECT IsFolder FROM " . $table . " WHERE ID=" . intval($v), "IsFolder", $this->db);
			if(!$isfolder)
				$ret[] = $v;
		}
		return $ret;
	}

	/* creates the FileChoooser field with the "browse"-Button. Clicking on the Button opens the fileselector */

	function formFileChooser($width = "", $IDName = "ParentID", $IDValue = "/", $cmd = "", $filter = ""){

		$js = we_html_element::jsScript(JS_DIR . "windows.js") .
			we_html_element::jsElement('
function formFileChooser() {
	var args = "";
	var url = "' . WEBEDITION_DIR . 'we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+encodeURI(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}
	switch (arguments[0]) {
		case "browse_server":
			new jsWindow(url,"server_selector",-1,-1,700,400,true,false,true);
		break;
	}
}');

		$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['" . $IDName . "'].value");
		$wecmdenc4 = we_base_request::encCmd(str_replace('\\', '', $cmd));
		$button = we_html_button::create_button("select", "javascript:formFileChooser('browse_server','" . $wecmdenc1 . "','" . $filter . "',document.we_form.elements['" . $IDName . "'].value,'" . $wecmdenc4 . "');");

		return $js . we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($IDName, 30, $IDValue, "", ' readonly', "text", $width, 0), "", "left", "defaultfont", "", we_html_tools::getPixel(20, 4), permissionhandler::hasPerm("CAN_SELECT_EXTERNAL_FILES") ? $button : "");
	}

	/* creates the DirectoryChoooser field with the "browse"-Button. Clicking on the Button opens the fileselector */

	function formDirChooser($width = "", $rootDirID = 0, $table = FILE_TABLE, $Pathname = "ParentPath", $Pathvalue = "", $IDName = "ParentID", $IDValue = "", $cmd = ""){

		$js = we_html_element::jsScript(JS_DIR . "windows.js") .
			we_html_element::jsElement('
function formDirChooser() {
	var args = "";
	var url = "' . WEBEDITION_DIR . 'we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+encodeURI(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}
	switch (arguments[0]) {
		case "openDirselector":
			new jsWindow(url,"dir_selector",-1,-1,' . we_selector_file::WINDOW_DIRSELECTOR_WIDTH . ',' . we_selector_file::WINDOW_DIRSELECTOR_HEIGHT . ',true,false,true,true);
		break;
	}
}');

		$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['" . $IDName . "'].value");
		$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['" . $Pathname . "'].value");
		$wecmdenc3 = we_base_request::encCmd(str_replace('\\', '', $cmd));
		$button = we_html_button::create_button("select", "javascript:formDirChooser('openDirselector',document.we_form.elements['" . $IDName . "'].value,'" . FILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','','" . $rootDirID . "')");
		return $js . we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($Pathname, 30, $Pathvalue, "", ' readonly', "text", $width, 0), "", "left", "defaultfont", we_html_element::htmlHidden(array("name" => $IDName, "value" => $IDValue)), we_html_tools::getPixel(20, 4), $button);
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
		$js = we_html_element::jsScript(JS_DIR . "windows.js") .
			we_html_element::jsElement('
function selector_cmd(){
	var args = "";
	var url = "' . WEBEDITION_DIR . 'we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+encodeURI(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}
	switch (arguments[0]){
		case "openSelector":
			new jsWindow(url,"we_selector",-1,-1,' . we_selector_file::WINDOW_SELECTOR_WIDTH . ',' . we_selector_file::WINDOW_SELECTOR_HEIGHT . ',true,true,true,true);
		break;
		case "add_customer":
		case "del_customer":
		case "del_all_customers":
			document.we_form.wcmd.value=arguments[0];
			document.we_form.cus.value=arguments[1];
			document.we_form.submit();
		break;
	}
}
' . $this->topFrame . '.customers="' . implode(',', $customers) . '";');

		$hiddens = we_html_element::htmlHidden(array("name" => "wcmd", "value" => "")) .
			we_html_element::htmlHidden(array("name" => "cus", "value" => we_base_request::_(we_base_request::INTLIST, "cus", "")));


		$delallbut = we_html_button::create_button("delete_all", "javascript:selector_cmd('del_all_customers')", true, 0, 0, "", "", ($customers ? false : true));
		$addbut = we_html_button::create_button("add", "javascript:selector_cmd('openSelector','','" . CUSTOMER_TABLE . "','','','fillIDs();opener." . $this->bodyFrame . ".selector_cmd(\\'add_customer\\',top.allIDs);')");
		$custs = new we_chooser_multiDir(400, ($customers ? : array()), "del_customer", we_html_button::create_button_table(array($delallbut, $addbut)), "", "Icon,Path", CUSTOMER_TABLE);

		$custs->isEditable = permissionhandler::hasPerm("EDIT_CUSTOMER");
		return $js . $hiddens . $custs->get();
	}

	function formWeChooser($table = FILE_TABLE, $width = "", $rootDirID = 0, $IDName = "ID", $IDValue = 0, $Pathname = "Path", $Pathvalue = "/", $cmd = ""){
		if(!$Pathvalue){
			$Pathvalue = f('SELECT Path FROM ' . $this->db->escape($table) . ' WHERE ID=' . intval($IDValue), '', $this->db);
		}

		$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['" . $IDName . "'].value");
		$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['" . $Pathname . "'].value");
		$wecmdenc3 = we_base_request::encCmd(str_replace('\\', '', $cmd));
		$button = we_html_button::create_button("select", "javascript:selector_cmd('openSelector',document.we_form.elements['" . $IDName . "'].value,'" . $table . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','','" . $rootDirID . "')");

		return we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($Pathname, 30, $Pathvalue, "", 'readonly', "text", $width, 0), "", "left", "defaultfont", we_html_element::htmlHidden(array("name" => $IDName, "value" => $IDValue)), we_html_tools::getPixel(20, 4), $button);
	}

	private function getHTMLChooser($name, $value, $values, $title){

		$input_size = 5;

		$select = new we_html_select(array("name" => $name . "_select", 'onchange' => "document.we_form." . $name . ".value=this.options[this.selectedIndex].value;this.selectedIndex=0", "style" => "width:200px;"));
		$select->addOption("", "");
		foreach($values as $k => $v)
			$select->addOption(oldHtmlspecialchars($k), oldHtmlspecialchars($v));

		$table = new we_html_table(array("border" => 0, "cellpadding" => 0, "cellspacing" => 0, "width" => 250), 1, 3);

		$table->setColContent(0, 0, we_html_tools::htmlTextInput($name, $input_size, $value));
		$table->setColContent(0, 1, we_html_tools::getPixel(10, 10));
		$table->setColContent(0, 2, $select->getHtml());

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
function filter_cmd(){
	switch (arguments[0]){
		case "add_filter":
		case "del_filter":
		case "del_all_filters":
			document.we_form.fcmd.value=arguments[0];
			document.we_form.submit();
			break;
	}
}
document.we_form.filter_count.value="' . $count . '";');

		$custfields = array();
		$customers_fields = array();
		$this->db->query("SHOW FIELDS FROM " . CUSTOMER_TABLE);
		while($this->db->next_record()){
			$customers_fields[] = $this->db->f("Field");
		}
		foreach($customers_fields as $fk => $fv){
			if($fv != "ParentID" && $fv != "IsFolder" && $fv != "Path" && $fv != "Text" && $fv != "Icon"){
				$custfields[$fv] = $fv;
			}
		}

		$operators = array("=", "<>", "<", "<=", ">", ">=", "LIKE");
		$logic = array("AND" => "AND", "OR" => "OR");

		$table = new we_html_table(array("border" => 0, "cellpadding" => 0, "cellspacing" => 0), 1, 3);
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
				$table->setCol($c, 0, array("colspan" => $colspan), we_html_element::htmlHidden(array("name" => "filter_logic_0", "value" => "")));
				$c++;
			}

			$table->addRow();
			$table->setCol($c, 0, array(), we_html_tools::htmlSelect("filter_fieldname_" . $i, $custfields, 1, $new["fieldname"], false, array(), "value", 200));
			$table->setCol($c, 1, array(), we_html_tools::htmlSelect("filter_operator_" . $i, $operators, 1, $new["operator"], false, array(), "value", 70));
			$table->setCol($c, 2, array(), we_html_tools::htmlTextInput("filter_fieldvalue_" . $i, 16, $new["fieldvalue"]));
			$c++;
		}

		$table->addRow();
		$table->setCol($c, 0, array("colspan" => $colspan), we_html_tools::getPixel(5, 5));

		$plus = we_html_button::create_button("image:btn_function_plus", "javascript:filter_cmd('add_filter')");
		$trash = we_html_button::create_button("image:btn_function_trash", "javascript:filter_cmd('del_filter')");

		$c++;
		$table->addRow();
		$table->setCol($c, 0, array("colspan" => $colspan), we_html_button::create_button_table(array($plus, $trash)));

		return $js .
			//we_html_element::htmlHidden(array("name"=>"filter_count","value"=>$count)).
			we_html_element::htmlHidden(array("name" => "fcmd", "value" => "")) .
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

}
