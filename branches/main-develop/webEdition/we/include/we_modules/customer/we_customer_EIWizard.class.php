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
			case 'eibody':
				echo $this->getHTMLStep($mode, $step);
				break;
			case 'eifooter':
				echo $this->getHTMLFooter($mode, $step);
				break;
			case 'eiload':
				echo $this->getHTMLLoad();
				break;
			default:
				error_log(__FILE__ . ' unknown reference: ' . $what);
		}
	}

	function getHTMLFrameset($mode){
		$body = we_html_element::htmlBody(['id' => 'weMainBody', 'onload' => 'self.focus();']
				, we_html_element::htmlIFrame('body', WEBEDITION_DIR . 'we_showMod.php?mod=customer&pnt=eibody&art=' . $mode . '&step=1', 'position:absolute;top:0px;bottom:45px;left:0px;right:0px;', 'border:0px;width:100%;height:100%;') .
				we_html_element::htmlIFrame('footer', WEBEDITION_DIR . 'we_showMod.php?mod=customer&pnt=eifooter&art=' . $mode . '&step=1', 'position:absolute;height:45px;bottom:0px;left:0px;right:0px;overflow: hidden', '', '', false) .
				we_html_element::htmlIFrame('load', WEBEDITION_DIR . 'we_showMod.php?mod=customer&pnt=eiload&step=1', 'position:absolute;bottom:0px;height:0px;left:0px;right:0px;overflow: hidden;')
		);


		return we_html_tools::getHtmlTop(g_l('modules_customer', '[export_title]'), '', '', we_html_element::jsScript(WE_JS_MODULES_DIR . 'customer/EIWizard.js', '', [
					'id' => 'loadVarEIWizard',
					'data-wizzard' => setDynamicVar([
						'art' => $mode,
						'type' => ''
				])]), $body
		);
	}

	function getHTMLStep($mode, $step = 0){
		switch($mode){
			case self::ART_EXPORT:
				switch($step){
					case 1:
						return $this->getHTMLExportStep1();
					case 2:
						return $this->getHTMLExportStep2();
					case 3:
						return $this->getHTMLExportStep3();
					case 4:
						return $this->getHTMLExportStep4();
					case 5:
						return $this->getHTMLExportStep5();
					default:
						return $this->getHTMLStep0();
				}
			case self::ART_IMPORT:
				switch($step){
					case 1:
						return $this->getHTMLImportStep1();
					case 2:
						return $this->getHTMLImportStep2();
					case 3:
						return $this->getHTMLImportStep3();
					case 4:
						return $this->getHTMLImportStep4();
					case 5:
						return $this->getHTMLImportStep5();
					default:
						return $this->getHTMLStep0();
				}
			default:
				return we_html_tools::getHtmlTop(g_l('modules_customer', '[export_title]'), '', '', '', we_html_element::htmlBody(['style' => 'margin:10px'])
				);
		}
	}

	private static function getJSFrame(){
		return we_html_element::jsScript(WEBEDITION_DIR . 'js/weCmd_apply.js');
	}

	function getHTMLExportStep1(){
		$type = we_base_request::_(we_base_request::STRING, 'type', we_import_functions::TYPE_GENERIC_XML);

		$generic = new we_html_table(['class' => 'default withSpace'], 2, 1);
		$generic->setCol(0, 0, [], we_html_forms::radiobutton(we_import_functions::TYPE_GENERIC_XML, ($type == we_import_functions::TYPE_GENERIC_XML), 'type', g_l('modules_customer', '[gxml_export]'), true, 'defaultfont', "we_cmd('set_topVar', {name: 'type', value: '" . we_import_functions::TYPE_GENERIC_XML . "'});", false, g_l('modules_customer', '[txt_gxml_export]'), 0, 430));
		$generic->setCol(1, 0, [], we_html_forms::radiobutton(self::TYPE_CSV, ($type == self::TYPE_CSV), 'type', g_l('modules_customer', '[csv_export]'), true, 'defaultfont', "we_cmd('set_topVar', {name: 'type', value: '" . self::TYPE_CSV . "'});", false, g_l('modules_customer', '[txt_csv_export]'), 0, 430));

		$parts = [['headline' => g_l('modules_customer', '[generic_export]'),
			'html' => $generic->getHTML(),
			'space' => we_html_multiIconBox::SPACE_MED,
			'noline' => 1]
		];

		return we_html_tools::getHtmlTop(g_l('modules_customer', '[export_title]'), '', '', self::getJSFrame(), we_html_element::htmlBody(['class' => 'weDialogBody'], we_html_element::htmlForm([
						'name' => 'we_form', 'method' => 'post', 'target' => 'body'], $this->getHiddens(['art' => self::ART_EXPORT, 'step' => 1]) .
						we_html_multiIconBox::getHTML('', $parts, 30, '', -1, '', '', false, g_l('modules_customer', '[export_step1]'))
					)
				)
		);
	}

	function getHTMLExportStep2(){
		$jsCmd = new we_base_jsCmd();
		$selection = we_base_request::_(we_base_request::STRING, 'selection', self::SELECTION_FILTER);

		$table = new we_html_table(['class' => 'default', 'style' => 'margin:5px 25px 0 0;'], 1, 2);
		$table->setColContent(0, 1, $this->getHTMLCustomerFilter($jsCmd));

		$generic = new we_html_table(['class' => 'default withSpace'], 4, 1);
		$generic->setColContent(0, 0, we_html_forms::radiobutton(self::SELECTION_FILTER, ($selection == self::SELECTION_FILTER), 'selection', g_l('modules_customer', '[filter_selection]'), true, 'defaultfont', "we_cmd('set_topVar', {name: 'type', value: '" . self::SELECTION_FILTER . "'});"));
		$generic->setColContent(1, 0, $table->getHtml());

		$table->setColContent(0, 1, we_html_tools::htmlFormElementTable($this->getHTMLCustomer($jsCmd), g_l('modules_customer', '[customer]')));
		$generic->setColContent(2, 0, we_html_forms::radiobutton(self::SELECTION_MANUAL, ($selection == self::SELECTION_MANUAL), 'selection', g_l('modules_customer', '[manual_selection]'), true, 'defaultfont', "we_cmd('set_topVar', {name: 'type', value: '" . self::SELECTION_MANUAL . "'});"));
		$generic->setColContent(3, 0, $table->getHtml());

		$parts = [['headline' => '',
			'html' => $generic->getHTML(),
			'space' => we_html_multiIconBox::SPACE_SMALL,
			'noline' => 1]
		];

		return we_html_tools::getHtmlTop(g_l('modules_customer', '[export_title]'), '', '', self::getJSFrame() . $jsCmd->getCmds(), we_html_element::htmlBody(['class' => 'weDialogBody'], we_html_element::htmlForm([
						'name' => 'we_form', 'method' => 'post', 'target' => 'body'], $this->getHiddens(["art" => self::ART_EXPORT, 'step' => 2]) .
						we_html_multiIconBox::getHTML("", $parts, 30, '', -1, '', '', false, g_l('modules_customer', '[export_step2]'))
					)
				)
		);
	}

	function getHTMLExportStep3(){
		//set defaults
		$type = we_base_request::_(we_base_request::STRING, 'type', we_import_functions::TYPE_GENERIC_XML);
		$filename = we_base_request::_(we_base_request::FILE, 'filename', 'weExport_' . time() . ($type == self::TYPE_CSV ? '.csv' : '.xml'));
		$export_to = we_base_request::_(we_base_request::STRING, 'export_to', self::EXPORT_SERVER);
		$path = we_base_request::_(we_base_request::FILE, 'path', '/');
		$cdata = we_base_request::_(we_base_request::INT, 'cdata', true);

		$csv_delimiter = we_base_request::_(we_base_request::RAW_CHECKED, 'csv_delimiter', self::CSV_DELIMITER);
		$csv_enclose = we_base_request::_(we_base_request::RAW_CHECKED, 'csv_enclose', self::CSV_ENCLOSE);
		$csv_lineend = we_base_request::_(we_base_request::STRING, 'csv_lineend', self::CSV_LINEEND);
		$csv_fieldnames = we_base_request::_(we_base_request::BOOL, 'csv_fieldnames', true);

		$jsCmd = new we_base_jsCmd();

		$customers = $this->getExportCustomers();
		if($customers){
			//set variables in top frame
			$parts = [['headline' => g_l('modules_customer', '[filename]'),
				'html' => we_html_tools::htmlTextInput('filename', 42, $filename),
				'space' => we_html_multiIconBox::SPACE_MED2
				],
			];

			switch($type){
				case we_import_functions::TYPE_GENERIC_XML:
					$table = new we_html_table(['class' => 'default withSpace'], 2, 1);
					$table->setColContent(0, 0, we_html_forms::radiobutton(1, $cdata, 'cdata', g_l('modules_customer', '[export_xml_cdata]'), true, 'defaultfont', ''));
					$table->setColContent(1, 0, we_html_forms::radiobutton(0, !$cdata, 'cdata', g_l('modules_customer', '[export_xml_entities]'), true, 'defaultfont', ''));
					$parts[] = ['headline' => g_l('modules_customer', '[cdata]'), 'html' => $table->getHtml(), 'space' => we_html_multiIconBox::SPACE_MED2];
					break;
				case self::TYPE_CSV:
					$fileformattable = new we_html_table(['style' => 'margin-top:10px;'], 4, 1);
					$file_encoding = new we_html_select(['name' => 'csv_lineend', 'class' => 'defaultfont', 'style' => 'width: 254px;']);
					$file_encoding->addOption('windows', g_l('modules_customer', '[windows]'));
					$file_encoding->addOption('unix', g_l('modules_customer', '[unix]'));
					$file_encoding->addOption('mac', g_l('modules_customer', '[mac]'));
					$file_encoding->selectOption($csv_lineend);

					$fileformattable->setCol(0, 0, ['class' => 'defaultfont'], g_l('modules_customer', '[csv_lineend]') . '<br/>' . $file_encoding->getHtml());
					$fileformattable->setColContent(1, 0, $this->getHTMLChooser('csv_delimiter', $csv_delimiter, [',' => g_l('modules_customer', '[comma]'), ';' => g_l('modules_customer', '[semicolon]'),
							':' => g_l('modules_customer', '[colon]'), "\\t" => g_l('modules_customer', '[tab]'), ' ' => g_l('modules_customer', '[space]')], g_l('modules_customer', '[csv_delimiter]')));
					$fileformattable->setColContent(2, 0, $this->getHTMLChooser('csv_enclose', $csv_enclose, ['"' => g_l('modules_customer', '[double_quote]'), "'" => g_l('modules_customer', '[single_quote]')], g_l('modules_customer', '[csv_enclose]')));
					$fileformattable->setColContent(3, 0, we_html_forms::checkbox(1, $csv_fieldnames, 'csv_fieldnames', g_l('modules_customer', '[csv_fieldnames]')));
					$parts[] = ["headline" => g_l('modules_customer', '[csv_params]'), 'html' => $fileformattable->getHtml(), 'space' => we_html_multiIconBox::SPACE_MED2];
			}

			$parts[] = ['headline' => g_l('modules_customer', '[export_to]'), 'html' => '', 'noline' => 1];

			$parts[] = ['space' => we_html_multiIconBox::SPACE_MED2, 'noline' => 1,
				'headline' => we_html_element::htmlDiv(['class' => 'default'], we_html_forms::radiobutton(self::EXPORT_SERVER, ($export_to == self::EXPORT_SERVER), 'export_to', g_l('modules_customer', '[export_to_server]'), true, 'defaultfont', "we_cmd('set_topVar', {name: 'export_to', value: '" . self::EXPORT_SERVER . "'});")),
				'html' =>
				we_html_element::htmlBr() .
				we_html_tools::htmlFormElementTable($this->formFileChooser(200, 'path', $path, '', we_base_ContentTypes::FOLDER), g_l('modules_customer', '[path]'))
			];

			$parts[] = ['headline' => we_html_forms::radiobutton(self::EXPORT_LOCAL, ($export_to == self::EXPORT_LOCAL), 'export_to', g_l('modules_customer', '[export_to_local]'), true, 'defaultfont', "we_cmd('set_topVar', {name: 'export_to', value: '" . self::EXPORT_LOCAL . "'});"),
				'space' => we_html_multiIconBox::SPACE_MED2,
				'noline' => 1,
				'html' => ''
			];
		} else {
			$parts = [['headline' => 'Fehler', 'html' => '<b>Die Auswahl ist leer</b>', 'space' => we_html_multiIconBox::SPACE_MED2]
			];

			$jsCmd->addCmd('do_back', self::ART_EXPORT);
		}

		return we_html_tools::getHtmlTop('', '', '', self::getJSFrame() . $jsCmd->getCmds(), we_html_element::htmlBody(['class' => "weDialogBody"], we_html_element::htmlForm([
						'name' => 'we_form', "method" => "post",
						"target" => "body"],
						//we_html_element::htmlHidden(array('name'=>"step",""=>"4")).
																																																																																				$this->getHiddens(['art' => self::ART_EXPORT, 'step' => 3]) .
						we_html_multiIconBox::getHTML("weExportWizard", $parts, 30, "", -1, "", "", false, g_l('modules_customer', '[export_step3]'))
					)
				)
		);
	}

	function getHTMLExportStep4(){
		$path = urldecode(we_base_request::_(we_base_request::FILE, 'path', ''));
		$filename = urldecode(we_base_request::_(we_base_request::FILE, 'filename', ''));

		switch(we_base_request::_(we_base_request::STRING, 'export_to', self::EXPORT_SERVER)){
			case self::EXPORT_LOCAL:
				$message = we_html_element::htmlSpan(['class' => 'defaultfont'], g_l('modules_customer', '[export_finished]') . '<br/><br/>' .
						g_l('modules_customer', '[download_starting]') .
						we_html_element::htmlA(['href' => WEBEDITION_DIR . 'we_showMod.php?mod=customer&pnt=eibody&art=' . self::ART_EXPORT . '&step=5&exportfile=' . $filename, 'download' => $filename], g_l('modules_customer', '[download]'))
				);

				return we_html_tools::getHtmlTop(g_l('modules_customer', '[export_title]'), '', '', we_base_jsCmd::singleCmd('reload_frame', ['frame' => 'footer', 'pnt' => 'eifooter',
							'art' => self::ART_EXPORT, 'cmd' => '', 'step' => 5]) .
						we_html_element::htmlMeta(['http-equiv' => 'refresh', 'content' => '2; url=' . WEBEDITION_DIR . 'we_showMod.php?mod=customer&pnt=eibody&art=' . self::ART_EXPORT . '&step=5&exportfile=' . $filename]), we_html_element::htmlBody([
							'class' => 'weDialogBody'], we_html_tools::htmlDialogLayout($message, g_l('modules_customer', '[export_step4]'))
						)
				);
			default:
			case self::EXPORT_SERVER:
				$message = we_html_element::htmlSpan(['class' => 'defaultfont'], g_l('modules_customer', '[export_finished]') . '<br/><br/>' .
						g_l('modules_customer', '[server_finished]') . '<br/>' .
						rtrim($path, '/') . '/' . $filename
				);

				return we_html_tools::getHtmlTop(g_l('modules_customer', '[export_title]'), '', '', self::getJSFrame() . we_base_jsCmd::singleCmd('reload_frame', ['frame' => 'footer',
							'pnt' => 'eifooter', 'art' => self::ART_EXPORT, 'cmd' => '', 'step' => 5]), we_html_element::htmlBody(['class' => 'weDialogBody'], we_html_tools::htmlDialogLayout($message, g_l('modules_customer', '[export_step4]'))
						)
				);
		}
	}

	function getHTMLExportStep5(){
		if(($filename = we_base_request::_(we_base_request::FILE, 'exportfile'))){
			if(file_exists(TEMP_PATH . $filename) // Does file exist?
				&& !preg_match('%p?html?%i', $filename) && stripos($filename, 'inc') === false && !preg_match('%php3?%i', $filename)){ // Security check
				session_write_close();
				$size = filesize(TEMP_PATH . $filename);

				header('Pragma: public');
				header('Expires: 0');
				header('Cache-control: private, max-age=0, must-revalidate');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename="' . trim(htmlentities($filename)) . '"');
				header('Content-Description: Customer-Export');
				header('Content-Length: ' . $size);

				readfile(TEMP_PATH . $filename);

				exit;
			}
		}
		header('Location: ' . WEBEDITION_DIR . 'we_showMod.php?mod=customer&pnt=cmd&step=99&error=download_failed');
		exit;
	}

	function getHiddens($options = []){
		switch($options['art']){
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

				$csv_fieldnames = we_base_request::_(we_base_request::BOOL, 'csv_fieldnames');

				$source = we_base_request::_(we_base_request::FILE, 'source', '/');

				switch($options['step']){
					case 1:
						return we_html_element::htmlHiddens(['pnt' => 'eibody',
								'step' => $options['step'],
								'art' => self::ART_IMPORT,
								($filename ? 'filename' : '') => $filename,
								'source' => $source,
								'import_from' => $import_from,
								'xml_from' => $xml_from,
								'xml_to' => $xml_to,
								'dataset' => $dataset,
								'csv_delimiter' => $csv_delimiter,
								'csv_enclose' => $csv_enclose,
								'csv_lineend' => $csv_lineend,
								'the_charset' => $the_charset,
								'csv_fieldnames' => $csv_fieldnames]);

					case 2:
						return we_html_element::htmlHiddens(['pnt' => 'eibody',
								'step' => $options['step'],
								'art' => self::ART_IMPORT,
								'type' => $type,
								($filename ? 'filename' : '') => $filename,
								'xml_from' => $xml_from,
								'xml_to' => $xml_to,
								'dataset' => $dataset,
								'csv_delimiter' => $csv_delimiter,
								'csv_enclose' => $csv_enclose,
								'csv_lineend' => $csv_lineend,
								'the_charset' => $the_charset,
								'csv_fieldnames' => $csv_fieldnames
						]);

					case 3:
						return we_html_element::htmlHiddens(['pnt' => 'eibody',
								'step' => $options['step'],
								'art' => self::ART_IMPORT,
								'type' => $type,
								'source' => $source,
								($filename ? 'filename' : '') => $filename,
								'import_from' => $import_from,
								'dataset' => $dataset]);

					case 4:
						return we_html_element::htmlHiddens(['pnt' => 'eibody',
								'step' => $options['step'],
								'art' => self::ART_IMPORT,
								'type' => $type,
								'source' => $source,
								'import_from' => $import_from,
								'dataset' => $dataset,
								'xml_from' => $xml_from,
								'xml_to' => $xml_to,
								'csv_delimiter' => $csv_delimiter,
								'csv_lineend' => $csv_lineend,
								'the_charset' => $the_charset,
								'csv_fieldnames' => $csv_fieldnames,
								'cmd' => self::ART_IMPORT,
								($filename ? 'filename' : '') => $filename,
								'csv_enclose' => ($csv_enclose === '"' ? '"' : $csv_enclose)
						]);
				}
				return '';

			case self::ART_EXPORT:
				$type = we_base_request::_(we_base_request::STRING, 'type', we_import_functions::TYPE_GENERIC_XML);
				$selection = we_base_request::_(we_base_request::STRING, 'selection', self::SELECTION_FILTER);
				$export_to = we_base_request::_(we_base_request::STRING, 'export_to', self::EXPORT_SERVER);
				$path = urldecode(we_base_request::_(we_base_request::FILE, 'path', '/'));
				$filename = we_base_request::_(we_base_request::FILE, 'filename');
				$cdata = we_base_request::_(we_base_request::INT, 'cdata', 1);

				$customers = we_base_request::_(we_base_request::INTLIST, 'customers', '');

				$csv_delimiter = we_base_request::_(we_base_request::RAW_CHECKED, 'csv_delimiter', self::CSV_DELIMITER);
				$csv_enclose = we_base_request::_(we_base_request::RAW_CHECKED, 'csv_enclose', self::CSV_ENCLOSE);
				$csv_lineend = we_base_request::_(we_base_request::STRING, 'csv_lineend', self::CSV_LINEEND);
				$csv_fieldnames = we_base_request::_(we_base_request::BOOL, 'csv_fieldnames', true);

				$filter_count = we_base_request::_(we_base_request::INT, 'filter_count', 0);
				$filter = '';
				$fields_names = ['fieldname', 'operator', 'fieldvalue', 'logic'];
				for($i = 0; $i < $filter_count; $i++){
					foreach($fields_names as $field){
						$varname = 'filter_' . $field . '_' . $i;
						if(($f = we_base_request::_(we_base_request::STRING, $varname)) !== false){
							$filter .= we_html_element::htmlHidden($varname, $f);
						}
					}
				}

				switch($options['step']){
					case 1:
						return we_html_element::htmlHiddens(['pnt' => 'eibody',
								'step' => $options['step'],
								'art' => self::ART_EXPORT,
								'type' => $type,
								'selection' => $selection,
								'export_to' => $export_to,
								'path' => $path,
								'cdata' => $cdata,
								'customers' => $customers,
								($filename ? 'filename' : '') => $filename,
								'csv_delimiter' => $csv_delimiter,
								'csv_enclose' => $csv_enclose,
								'csv_lineend' => $csv_lineend,
								'csv_fieldnames' => $csv_fieldnames,
								'filter_count' => $filter_count]) .
							$filter;

					case 2:
						return we_html_element::htmlHiddens(['pnt' => 'eibody',
								'step' => $options['step'],
								'art' => self::ART_EXPORT,
								'type' => $type,
								'selection' => $selection,
								'export_to' => $export_to,
								'path' => $path,
								'cdata' => $cdata,
								'customers' => $customers,
								($filename ? 'filename' : '') => $filename,
								'csv_delimiter' => $csv_delimiter,
								'csv_enclose' => $csv_enclose,
								'csv_lineend' => $csv_lineend,
								'csv_fieldnames' => $csv_fieldnames,
								'filter_count' => $filter_count]);

					case 3:
						return we_html_element::htmlHiddens(['pnt' => 'eibody',
								'step' => $options['step'],
								'art' => self::ART_EXPORT,
								'type' => $type,
								'selection' => $selection,
								'customers' => $customers,
								'filter_count' => $filter_count,
								'cmd' => self::ART_EXPORT
							]) .
							$filter;

					case 4:
						return we_html_element::htmlHiddens(['pnt' => 'eibody',
								'step' => $options['step'],
								'art' => self::ART_EXPORT,
								'type' => $type,
								'selection' => $selection,
								'export_to' => $export_to,
								'path' => $path,
								'cdata' => $cdata,
								'customers' => $customers,
								($filename ? 'filename' : '') => $filename,
								'csv_delimiter' => $csv_delimiter,
								'csv_enclose' => $csv_enclose,
								'csv_lineend' => $csv_lineend,
								'csv_fieldnames' => $csv_fieldnames,
								'filter_count' => $filter_count,
								'cmd' => self::ART_EXPORT
							]) .
							$filter;
				}
				return '';
		}

		return '';
	}

	function getHTMLImportStep1(){
		$type = we_base_request::_(we_base_request::STRING, 'type', we_import_functions::TYPE_GENERIC_XML);

		$generic = new we_html_table(['class' => 'default withSpace'], 2, 1);
		$generic->setCol(0, 0, [], we_html_forms::radiobutton(we_import_functions::TYPE_GENERIC_XML, ($type == we_import_functions::TYPE_GENERIC_XML), "type", g_l('modules_customer', '[gxml_import]'), true, "defaultfont", "we_cmd('set_topVar', {name: 'type', value: '" . we_import_functions::TYPE_GENERIC_XML . "'});", false, g_l('modules_customer', '[txt_gxml_import]'), 0, 430));
		$generic->setCol(1, 0, [], we_html_forms::radiobutton(self::TYPE_CSV, ($type == self::TYPE_CSV), 'type', g_l('modules_customer', '[csv_import]'), true, 'defaultfont', "we_cmd('set_topVar', {name: 'type', value: '" . self::TYPE_CSV . "'});", false, g_l('modules_customer', '[txt_csv_import]'), 0, 430));

		$parts = [
			['headline' => g_l('modules_customer', '[generic_import]'),
				'html' => $generic->getHTML(),
				'space' => we_html_multiIconBox::SPACE_MED,
				'noline' => 1]
		];

		return we_html_tools::getHtmlTop(g_l('modules_customer', '[import_title]'), '', '', self::getJSFrame(), we_html_element::htmlBody(['class' => 'weDialogBody'], we_html_element::htmlForm([
						'name' => 'we_form', 'method' => 'post'], $this->getHiddens(['art' => self::ART_IMPORT, 'step' => 1]) .
						we_html_multiIconBox::getHTML('', $parts, 30, '', -1, '', '', false, g_l('modules_customer', '[import_step1]'))
					)
				)
		);
	}

	function getHTMLImportStep2(){
		$import_from = we_base_request::_(we_base_request::STRING, 'import_from', self::EXPORT_SERVER);
		$source = we_base_request::_(we_base_request::FILE, 'source', '/');
		$type = we_base_request::_(we_base_request::STRING, 'type', '');

		$fileUploader = new we_fileupload_ui_base('upload');
		$fileUploader->setExternalUiElements(['btnUploadName' => 'next_footer']);
		$fileUploader->setNextCmd('uploader_callback');
		$fileUploader->setCmdFileSelectOnclick('set_radio_importFrom,,1');
		$fileUploader->setInternalProgress(['isInternalProgress' => true, 'width' => 200]);
		$fileUploader->setGenericFileName(TEMP_DIR . we_fileupload::REPLACE_BY_UNIQUEID . ($type == self::TYPE_CSV ? '.csv' : '.xml'));
		$fileUploader->setDisableUploadBtnOnInit(false);
		$fileUploader->setDimensions(['width' => 369, 'alertBoxWidth' => 430, 'marginTop' => 10]);

		$parts = [];
		$js = $fileUploader->getJs();
		$css = $fileUploader->getCss();

		$table = new we_html_table(['class' => 'default withSpace'], 2, 2);
		$table->setCol(0, 0, ['colspan' => 2], we_html_forms::radiobutton(self::EXPORT_SERVER, ($import_from == self::EXPORT_SERVER), 'import_from', g_l('modules_customer', '[server_import]'), true, 'defaultfont'));
		$table->setCol(1, 1, ['style' => 'padding-bottom:5px;'], $this->formFileChooser(250, 'source', $source, 'set_radio_importFrom,0', ($type == we_import_functions::TYPE_GENERIC_XML ? we_base_ContentTypes::XML : '')));

		$parts[] = ['headline' => g_l('modules_customer', '[source_file]'),
			'html' => $table->getHtml(),
			'space' => we_html_multiIconBox::SPACE_MED,
			'noline' => 1
		];

		//upload table
		$tmptable = new we_html_table(['class' => 'default withSpace'], 2, 1);
		//$tmptable->setCol(0, 0, [], we_html_tools::htmlAlertAttentionBox(sprintf(g_l('newFile', '[max_possible_size]'), we_base_file::getHumanFileSize($maxsize, we_base_file::SZ_MB)), we_html_tools::TYPE_ALERT, 430));
		$tmptable->setCol(0, 0, [], $fileUploader->getHtmlAlertBoxes());

		//$tmptable->setCol(2, 0, array('style'=>'vertical-align:middle;'), we_html_tools::htmlTextInput("upload", 35, "", 255, "onclick=\"document.we_form.import_from[1].checked=true;\"", "file"));
		$tmptable->setCol(1, 0, ['style' => 'vertical-align:middle;'], $fileUploader->getHTML());

		$table = new we_html_table(['class' => 'default withSpace'], 2, 2);
		$table->setCol(0, 0, ['colspan' => 2], we_html_forms::radiobutton(self::EXPORT_LOCAL, ($import_from == self::EXPORT_LOCAL), 'import_from', g_l('modules_customer', '[upload_import]'), true, 'defaultfont'));
		$table->setColContent(1, 1, $tmptable->getHtml());

		$parts[] = ['headline' => '',
			'html' => $table->getHTML(),
			'space' => we_html_multiIconBox::SPACE_MED,
			'noline' => 1
		];

		return we_html_tools::getHtmlTop(g_l('modules_customer', '[import_title]'), '', '', $css . self::getJSFrame() . $js, we_html_element::htmlBody(['class' => 'weDialogBody'], we_html_element::htmlForm([
						'name' => 'we_form', 'method' => 'post', 'target' => 'body', 'enctype' => 'multipart/form-data'], $this->getHiddens(['art' => self::ART_IMPORT, 'step' => 2]) .
						we_html_multiIconBox::getHTML('', $parts, 30, '', -1, '', '', false, g_l('modules_customer', '[import_step2]'))
					)
				)
		);
	}

	function getHTMLImportStep3(){
		$import_from = we_base_request::_(we_base_request::STRING, 'import_from', self::EXPORT_SERVER);
		$source = we_base_request::_(we_base_request::FILE, 'source', '/');
		$type = we_base_request::_(we_base_request::STRING, 'type', '');

		if($import_from == self::EXPORT_LOCAL){
			$fileUploader = new we_fileupload_resp_base();
			//$fileUploader->setTypeCondition();
			$filename = $fileUploader->commitUploadedFile();
		} else {
			$filename = $source;
		}
		$filesource = $filename ? $_SERVER['DOCUMENT_ROOT'] . $filename : '';

		$parts = [];
		$jsCmd = new we_base_jsCmd();

		if(is_file($filesource) && is_readable($filesource)){
			switch($type){
				case self::TYPE_CSV:
					$line = we_base_file::loadLine($filesource, 0, 80960);
					$charsets = ['UTF-8', 'ISO-8859-15', 'ISO-8859-1']; //charsetHandler::getAvailCharsets();
					$charset = mb_detect_encoding($line, $charsets, true);
					$charCount = count_chars($line, 0);

					$csv_delimiters = [';' => g_l('modules_customer', '[semicolon]'), ',' => g_l('modules_customer', '[comma]'), ':' => g_l('modules_customer', '[colon]'),
						'\t' => g_l('modules_customer', '[tab]'), ' ' => g_l('modules_customer', '[space]')];
					$csv_encloses = ['"' => g_l('modules_customer', '[double_quote]'), '\'' => g_l('modules_customer', '[single_quote]')];
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

					$fileformattable = new we_html_table(['style' => 'margin-top:10px;'], 5, 1);

					$file_encoding = new we_html_select(['name' => "csv_lineend", 'class' => 'defaultfont', 'style' => 'width: 254px;']);
					$file_encoding->addOption('windows', g_l('modules_customer', '[windows]'));
					$file_encoding->addOption('unix', g_l('modules_customer', '[unix]'));
					$file_encoding->addOption('mac', g_l('modules_customer', '[mac]'));
					$file_encoding->selectOption($csv_lineend);

					$charsets = we_base_charsetHandler::inst()->getCharsetsForTagWizzard();
					//$charset = $GLOBALS['WE_BACKENDCHARSET'];
					//$GLOBALS['weDefaultCharset'] = get_value("default_charset");
					$importCharset = we_html_tools::htmlTextInput('the_charset', 8, ($charset === 'ASCII' ? 'ISO8859-1' : $charset), 255, '', 'text', 100);
					$importCharsetChooser = we_html_tools::htmlSelect("ImportCharsetSelect", $charsets, 1, ($charset === 'ASCII' ? 'ISO8859-1' : $charset), false, ['onchange' => "we_cmd('selectCharset_onchange', {select: this})"], 'value', 160, 'defaultfont', false);
					$import_Charset = '<table class="default"><tr><td>' . $importCharset . '</td><td>' . $importCharsetChooser . '</td></tr></table>';


					$fileformattable->setCol(0, 0, ['class' => 'defaultfont'], g_l('modules_customer', '[csv_lineend]') . we_html_element::htmlBr() . $file_encoding->getHtml());
					$fileformattable->setCol(1, 0, ['class' => 'defaultfont'], g_l('modules_customer', '[import_charset]') . we_html_element::htmlBr() . $import_Charset);
					$fileformattable->setColContent(2, 0, $this->getHTMLChooser('csv_delimiter', $csv_delimiter, $csv_delimiters, g_l('modules_customer', '[csv_delimiter]')));
					$fileformattable->setColContent(3, 0, $this->getHTMLChooser('csv_enclose', $csv_enclose, $csv_encloses, g_l('modules_customer', '[csv_enclose]')));

					$fileformattable->setColContent(4, 0, we_html_forms::checkbox(1, $csv_fieldnames, "csv_fieldnames", g_l('modules_customer', '[csv_fieldnames]')));

					$parts = [['headline' => g_l('modules_customer', '[csv_params]'), 'html' => $fileformattable->getHtml(), 'space' => we_html_multiIconBox::SPACE_MED2]];
					break;
				case we_import_functions::TYPE_GENERIC_XML:
					//invoke parser
					$xp = new we_xml_parser($filesource);
					$xmlWellFormed = ($xp->parseError === '');

					if($xmlWellFormed){
						// Node-set with paths to the child nodes.
						$node_set = $xp->evaluate("*/child::*");
						$children = $xp->nodes[$xp->root]['children'];

						$recs = [];
						foreach($children as $key => $value){
							$flag = true;
							for($k = 1; $k < ($value + 1); $k++){
								if(!$xp->hasChildNodes($xp->root . '/' . $key . '[' . $k . ']')){
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
						$rcdSelect = new we_html_select(['name' => 'we_select',
							'class' => 'defaultfont',
							(($isSingleNode) ? 'disabled' : 'style') => '',
							'onchange' => 'selectWeSelect_doOnselect(this)']
						);
						$optid = 0;
						foreach($recs as $value => $text){
							if($optid == 0){
								$firstItem = $value;
								$firstOptVal = $text;
							}
							$rcdSelect->addOption($text, $value);
							if(isset($v['rcd']) && $text == $v['rcd']){
								$rcdSelect->selectOption($value);
							}
							$optid++;
						}

						$tblSelect = new we_html_table([], 1, 7);
						$tblSelect->setCol(0, 1, [], $rcdSelect->getHtml());
						$tblSelect->setCol(0, 2, ['width' => 20]);
						$tblSelect->setCol(0, 3, ['class' => 'defaultfont'], g_l('modules_customer', '[num_data_sets]'));
						$tblSelect->setCol(0, 4, [], we_html_tools::htmlTextInput('xml_from', 4, 1, 5, 'align=right', 'text', 30, '', '', ($isSingleNode && ($firstOptVal == 1)) ? 1 : 0));
						$tblSelect->setCol(0, 5, ['class' => 'defaultfont'], g_l('modules_customer', '[to]'));
						$tblSelect->setCol(0, 6, [], we_html_tools::htmlTextInput('xml_to', 4, $firstOptVal, 5, 'align=right', 'text', 30, '', '', ($isSingleNode && ($firstOptVal == 1)) ? 1 : 0));

						$tblFrame = new we_html_table([], 3, 2);
						$tblFrame->setCol(0, 0, ['colspan' => 2, 'class' => 'defaultfont'], ($isSingleNode) ? we_html_tools::htmlAlertAttentionBox(g_l('modules_customer', '[well_formed]') . ' ' . g_l('modules_customer', '[select_elements]'), we_html_tools::TYPE_INFO, 570) :
								we_html_tools::htmlAlertAttentionBox(g_l('modules_customer', '[xml_valid_1]') . ' ' . $optid . ' ' . g_l('modules_customer', '[xml_valid_m2]'), we_html_tools::TYPE_INFO, 570));
						$tblFrame->setCol(1, 0, ['colspan' => 2]);
						$tblFrame->setCol(2, 1, [], $tblSelect->getHtml());

						$_REQUEST['dataset'] = $firstItem;
						$parts = [['html' => $tblFrame->getHtml(), 'noline' => 1]];
					} else {
						$parts = [["html" => we_html_tools::htmlAlertAttentionBox(g_l('modules_customer', (!$xmlWellFormed) ? '[not_well_formed]' : '[missing_child_node]'), we_html_tools::TYPE_ALERT, 570),
							'noline' => 1]];
						$jsCmd->addCmd('reload_footer', [self::ART_IMPORT, 99]);
					}
					break;
			}
		} else {
			$jsCmd->addCmd('reload_footer', [self::ART_IMPORT, 99]);
			$parts[] = ['html' => we_html_tools::htmlAlertAttentionBox(g_l('modules_customer', '[missing_filesource]'), we_html_tools::TYPE_ALERT, 570), 'noline' => 1];
		}

		$_REQUEST['filename'] = $filename;
		return we_html_tools::getHtmlTop(g_l('modules_customer', '[import_title]'), '', '', self::getJSFrame() . $jsCmd->getCmds(), we_html_element::htmlBody(['class' => "weDialogBody"], we_html_element::htmlForm([
						'name' => 'we_form', 'method' => 'post', 'target' => 'body'], $this->getHiddens(['art' => self::ART_IMPORT, 'step' => 3]) .
						we_html_multiIconBox::getHTML('', $parts, 30, '', -1, '', '', false, g_l('modules_customer', '[import_step3]'))
					)
				)
		);
	}

	function getHTMLImportStep4(){
		$filename = we_base_request::_(we_base_request::FILE, 'filename', '');
		$type = we_base_request::_(we_base_request::STRING, 'type', '');
		$dataset = we_base_request::_(we_base_request::RAW, 'dataset', '');
		$csv_delimiter = we_base_request::_(we_base_request::RAW_CHECKED, 'csv_delimiter', self::CSV_DELIMITER);
		$csv_enclose = we_base_request::_(we_base_request::RAW_CHECKED, 'csv_enclose', self::CSV_ENCLOSE);
		$csv_lineend = we_base_request::_(we_base_request::STRING, 'csv_lineend', self::CSV_LINEEND);
		$the_charset = we_base_request::_(we_base_request::STRING, 'the_charset', self::THE_CHARSET);
		$csv_fieldnames = we_base_request::_(we_base_request::BOOL, 'csv_fieldnames');
		$same = we_base_request::_(we_base_request::STRING, 'same', 'rename');

		$field_mappings = we_base_request::_(we_base_request::RAW, 'field_mappings', '');
		$att_mappings = we_base_request::_(we_base_request::RAW, 'att_mappings', '');

		if($type == self::TYPE_CSV){
			$arrgs = ['delimiter' => $csv_delimiter,
				'enclose' => $csv_enclose,
				'lineend' => $csv_lineend,
				'fieldnames' => $csv_fieldnames,
				'charset' => $the_charset,
			];
		} else {
			$arrgs = ['dataset' => $dataset];
		}

		$nodes = we_customer_EI::getDataset($type, $filename, $arrgs);
		$records = we_customer_EI::getCustomersFieldset();

		$tableheader = ($type == we_import_functions::TYPE_GENERIC_XML ?
			[['dat' => g_l('modules_customer', '[we_flds]')], ['dat' => g_l('modules_customer', '[rcd_flds]')], ['dat' => g_l('import', '[attributes]')]] :
			[['dat' => g_l('modules_customer', '[we_flds]')], ['dat' => g_l('modules_customer', '[rcd_flds]')]]
			);

		$rows = [];
		$i = 0;

		foreach($records as $record){
			$we_fields = new we_html_select(['name' => 'field_mappings[' . $record . ']',
				'class' => 'defaultfont',
				'onclick' => '',
				'style' => '']
			);

			$we_fields->addOption('', g_l('modules_customer', '[any]'));

			foreach(array_keys($nodes) as $node){
				$we_fields->addOption(oldHtmlspecialchars(str_replace(' ', '', $node)), oldHtmlspecialchars($node));
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
				$rows[] = [['dat' => $record],
					['dat' => $we_fields->getHTML()],
					['dat' => we_html_tools::htmlTextInput('att_mappings[' . $record . ']', 30, (isset($att_mappings[$record]) ? $att_mappings[$record] : ''), 255, '', 'text', 100)]
				];
			} else {
				$rows[] = [['dat' => $record],
					['dat' => $we_fields->getHTML()]
				];
			}
			$i++;
		}

		$table = new we_html_table(['class' => 'default'], 4, 1);
		$table->setColContent(0, 0, we_html_forms::radiobutton('rename', ($same === 'rename'), 'same', g_l('modules_customer', '[same_rename]'), true, 'defaultfont', ''));
		$table->setColContent(1, 0, we_html_forms::radiobutton('overwrite', ($same === 'overwrite'), 'same', g_l('modules_customer', '[same_overwrite]'), true, 'defaultfont', ''));
		$table->setColContent(2, 0, we_html_forms::radiobutton('skip', ($same === 'skip'), 'same', g_l('modules_customer', '[same_skip]'), true, 'defaultfont', ''));

		$parts = [['headline' => g_l('modules_customer', '[same_names]'),
			'html' => $table->getHtml(),
			],
			["headline" => g_l('modules_customer', '[import_step4]'),
				'html' => '<br/>' . we_html_tools::htmlDialogBorder3(510, $rows, $tableheader, 'defaultfont'),
				'space' => we_html_multiIconBox::SPACE_MED2],
		];


		return we_html_tools::getHtmlTop(g_l('modules_customer', '[import_title]'), '', '', self::getJSFrame() . we_html_multiIconBox::getJS(), we_html_element::htmlBody([
					'class' => 'weDialogBody'], we_html_element::htmlForm([
						'name' => 'we_form', 'method' => 'post', 'target' => 'body'], $this->getHiddens(['art' => self::ART_IMPORT, 'step' => 4]) .
						we_html_multiIconBox::getHTML('xml', $parts, 30, '', -1, '', '', false, g_l('modules_customer', '[import_step4]'))
					)
				)
		);
	}

	function getHTMLImportStep5(){
		$tmpdir = we_base_request::_(we_base_request::FILE, 'tmpdir');
		$impno = we_base_request::_(we_base_request::INT, 'impno', 0);

		$table = new we_html_table([], 3, 1);
		$table->setCol(0, 0, ['class' => 'defaultfont'], sprintf(g_l('modules_customer', '[import_finished_desc]'), $impno));

		if($tmpdir && is_file(TEMP_PATH . $tmpdir . '/' . $tmpdir . '.log') && is_readable(TEMP_PATH . $tmpdir . '/' . $tmpdir . '.log')){
			$log = we_base_file::load(TEMP_PATH . $tmpdir . '/' . $tmpdir . '.log', 'rb');
			if($log){

				$table->setColContent(1, 0, we_html_tools::htmlAlertAttentionBox(g_l('modules_customer', '[show_log]'), we_html_tools::TYPE_ALERT, 550));
				$table->setColContent(2, 0, we_html_element::htmlTextArea(['name' => "log", "rows" => 15, "cols" => 15, 'style' => "width: 550px; height: 200px;"], oldHtmlspecialchars($log)));
				unlink(TEMP_PATH . $tmpdir . '/' . $tmpdir . '.log');
			}
		}
		$parts = [['headline' => '',
			'html' => $table->getHtml(),
			'space' => we_html_multiIconBox::SPACE_SMALL
			]
		];

		if(is_dir(TEMP_PATH . $tmpdir)){
			rmdir(TEMP_PATH . $tmpdir);
		}

		return we_html_tools::getHtmlTop(g_l('modules_customer', '[import_title]'), '', '', self::getJSFrame() . we_html_multiIconBox::getJS(), we_html_element::htmlBody([
					'class' => 'weDialogBody'], we_html_element::htmlForm([
						'name' => 'we_form', 'method' => 'post', 'target' => 'load'], we_html_multiIconBox::getHTML('', $parts, 30, '', -1, '', '', false, g_l('modules_customer', '[import_step5]'))
					)
				)
		);
	}

	function getHTMLFooter($mode, $step){

		return ($mode == self::ART_EXPORT ? $this->getHTMLExportFooter($step) : $this->getHTMLImportFooter($step));
	}

	function getHTMLExportFooter($step = 1){
		$content = new we_html_table(['class' => 'default', 'width' => 575, 'style' => 'text-align:right'], 1, 2);

		if($step == 1){
			$buttons = we_html_button::position_yes_no_cancel(
					we_html_button::create_button(we_html_button::BACK, '', '', 0, 0, '', '', true) . we_html_button::create_button(we_html_button::NEXT, "javascript:we_cmd('reload_frame', {frame: 'load', pnt: 'eiload', art: '" . self::ART_EXPORT . "', cmd: 'export_next', step: " . $step . "});"), we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close();")
			);
		} else if($step == 4){
			$buttons = we_html_button::position_yes_no_cancel(
					we_html_button::create_button(we_html_button::BACK, '', '', 0, 0, '', '', true) .
					we_html_button::create_button(we_html_button::NEXT, '', '', 0, 0, '', '', true), we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close();")
			);
			$text = g_l('modules_customer', '[exporting]');
			$progress = 0;
			$progressbar = new we_progressBar($progress, 200);
			$progressbar->addText($text, we_progressBar::TOP, 'current_description');

			$content->setCol(0, 0, null, (isset($progressbar) ? $progressbar->getHtml() : ''));
		} else if($step == 5){
			$buttons = we_html_button::position_yes_no_cancel(
					we_html_button::create_button(we_html_button::BACK, '', '', 0, 0, '', '', true) .
					we_html_button::create_button(we_html_button::NEXT, '', '', 0, 0, '', '', true), we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close();")
			);
		} else {
			$buttons = we_html_button::position_yes_no_cancel(
					we_html_button::create_button(we_html_button::BACK, "javascript:we_cmd('reload_frame', {frame: 'load', pnt: 'eiload', art: '" . self::ART_EXPORT . "', cmd: 'export_back', step: " . $step . "});") .
					we_html_button::create_button(we_html_button::NEXT, "javascript:we_cmd('reload_frame', {frame: 'load', pnt: 'eiload', art: '" . self::ART_EXPORT . "', cmd: 'export_next', step: " . $step . "});"), we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close();")
			);
		}
		$content->setCol(0, 1, ['style' => "text-align:right"], $buttons);

		return we_html_tools::getHtmlTop('', '', '', self::getJSFrame() . (isset($progressbar) ? we_progressBar::getJSCode() : ''), we_html_element::htmlBody(['class' => 'weDialogButtonsBody'], we_html_element::htmlForm([
						'name' => 'we_form',
						'method' => 'post',
						'target' => 'load',
						'action' => $this->frameset
						], $content->getHtml()
					)
				)
		);
	}

	function getHTMLImportFooter($step = 1){
		$content = new we_html_table(['class' => 'default', "width" => 575, 'style' => "text-align:right"], 1, 2);

		switch($step){
			case "1":
				$buttons = we_html_button::position_yes_no_cancel(
						we_html_button::create_button(we_html_button::BACK, "", '', 0, 0, "", "", true) .
						we_html_button::create_button(we_html_button::NEXT, "javascript:we_cmd('reload_frame', {frame: 'load', pnt: 'eiload', art: '" . self::ART_IMPORT . "', cmd: 'import_next', step: " . $step . "});"), we_html_button::create_button(we_html_button::CANCEL, 'javascript:top.close();')
				);
				break;
			case "2":
				$buttons = we_html_button::position_yes_no_cancel(
						we_html_button::create_button(we_html_button::BACK, "javascript:we_cmd('reload_frame', {frame: 'load', pnt: 'eiload', art: '" . self::ART_IMPORT . "', cmd: 'import_back', step: " . $step . "});") .
						we_html_button::create_button(we_html_button::NEXT, "javascript:we_cmd('reload_frame', {frame: 'load', pnt: 'eiload', art: '" . self::ART_IMPORT . "', cmd: 'import_next', step: " . $step . "});", '', 0, 0, '', '', false, false, '_footer'), we_html_button::create_button(we_html_button::CANCEL, "javascript:we_cmd(uploader_cancel);")
				);
				break;
			case "5":
				$buttons = we_html_button::position_yes_no_cancel(
						we_html_button::create_button(we_html_button::BACK, "", '', 0, 0, "", "", true) .
						we_html_button::create_button(we_html_button::NEXT, "", '', 0, 0, "", "", true), we_html_button::create_button(we_html_button::CANCEL, 'javascript:top.close();')
				);
				$text = g_l('modules_customer', '[importing]');
				$progress = 0;
				$progressbar = new we_progressBar($progress, 200);
				$progressbar->addText($text, we_progressBar::TOP, "current_description");

				$content->setCol(0, 0, null, (isset($progressbar) ? $progressbar->getHtml() : ""));
				break;
			case "6":
				$buttons = we_html_button::position_yes_no_cancel(
						we_html_button::create_button(we_html_button::CLOSE, "javascript:top.close();")
				);
				break;
			case "99":
				$buttons = we_html_button::position_yes_no_cancel(
						we_html_button::create_button(we_html_button::BACK, "javascript:we_cmd('reload_frame', {frame: 'load', pnt: 'eiload', art: '" . self::ART_IMPORT . "', cmd: 'import_back', step: 2});") .
						we_html_button::create_button(we_html_button::NEXT, "", '', 0, 0, "", "", true), we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close();")
				);
				break;
			default:
				$buttons = we_html_button::position_yes_no_cancel(
						we_html_button::create_button(we_html_button::BACK, "javascript:we_cmd('reload_frame', {frame: 'load', pnt: 'eiload', art: '" . self::ART_IMPORT . "', cmd: 'import_back', step: " . $step . "});") .
						we_html_button::create_button(we_html_button::NEXT, "javascript:we_cmd('reload_frame', {frame: 'load', pnt: 'eiload', art: '" . self::ART_IMPORT . "', cmd: 'import_next', step: " . $step . "});"), we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close();")
				);
		}
		$content->setCol(0, 1, ['style' => 'text-align:right'], $buttons);

		return we_html_tools::getHtmlTop('', '', '', self::getJSFrame() . (isset($progressbar) ? we_progressBar::getJSCode() : ''), we_html_element::htmlBody(['class' => 'weDialogButtonsBody'], we_html_element::htmlForm([
						'name' => 'we_form',
						'method' => 'post',
						'target' => 'load',
						'action' => $this->frameset
						], $content->getHtml()
					)
				)
		);
	}

	private function getLoadCode(){
		if(($pid = we_base_request::_(we_base_request::INT, 'pid'))){
			return we_html_tools::getHtmlTop('', '', '', self::getJSFrame() . we_base_jsCmd::singleCmd('process_cmd_load', [we_base_request::_(we_base_request::TABLE, "tab"),
						$pid, we_base_request::_(we_base_request::STRING, "openFolders")]), we_html_element::htmlBody());
		}
		return '';
	}

	private function getExportNextCode(){
		switch(we_base_request::_(we_base_request::INT, 'step')){
			case 1:
			case 2:
			case 3:
			case 4:
				return we_html_tools::getHtmlTop(g_l('modules_customer', '[export_title]'), '', '', self::getJSFrame() .
						we_base_jsCmd::singleCmd('load_processCmd', ['cmd' => 'export_next', 'art' => self::ART_EXPORT]), we_html_element::htmlBody(['bgcolor' => '#ffffff',
							'style' => 'margin:5px;'], we_html_element::htmlForm(['name' => 'we_form',
								'method' => 'post', 'target' => 'body', 'action' => $this->frameset], '')
						)
				);
			default:
				return '';
		}
	}

	private function getExportBackCode(){
		return we_html_tools::getHtmlTop(g_l('modules_customer', '[export_title]'), '', '', self::getJSFrame() . we_base_jsCmd::singleCmd('load_processCmd', ['cmd' => 'export_back',
					'art' => self::ART_EXPORT]), we_html_element::htmlBody(['bgcolor' => '#ffffff',
					'style' => 'margin:5px'], we_html_element::htmlForm(['name' => 'we_form',
						'method' => 'post', 'target' => 'body', 'action' => $this->frameset], '')
				)
		);
	}

	private function getExportCode(){
		$file_format = we_base_request::_(we_base_request::STRING, 'type', we_import_functions::TYPE_GENERIC_XML);
		$file_name = we_base_request::_(we_base_request::FILE, 'filename', date('Y-m-d'));
		$export_to = we_base_request::_(we_base_request::STRING, 'export_to', self::EXPORT_SERVER);
		$path = ($export_to == self::EXPORT_SERVER ? we_base_request::_(we_base_request::FILE, 'path', '') : rtrim(TEMP_DIR, '/'));
		$cdata = we_base_request::_(we_base_request::INT, 'cdata', 0);
		$csv_delimiter = we_base_request::_(we_base_request::RAW_CHECKED, 'csv_delimiter', '');
		$csv_enclose = we_base_request::_(we_base_request::RAW_CHECKED, 'csv_enclose', '');
		$csv_lineend = we_base_request::_(we_base_request::STRING, 'csv_lineend', '');
		$csv_fieldnames = we_base_request::_(we_base_request::BOOL, 'csv_fieldnames');

		$customers = $this->getExportCustomers();
		if(!$customers){

			//FIXME: add code to switch to previous page
			t_e('noting to export', $customers);
		}

		$hiddens = we_html_element::htmlHiddens(['pnt' => 'eiload',
				'art' => self::ART_EXPORT,
				'customers' => implode(',', $customers),
				'file_format' => $file_format,
				'filename' => $file_name,
				'export_to' => $export_to,
				'path' => $path,
				'all' => count($customers),
				'cmd' => 'do_export',
				'step' => 4]);

		if($file_format == we_import_functions::TYPE_GENERIC_XML){
			$hiddens .= we_html_element::htmlHidden('cdata', $cdata);
		}

		if($file_format == self::TYPE_CSV){
			$hiddens .= ($csv_enclose === '"' ?
				"<input type='hidden' name='csv_enclose' value='" . $csv_enclose . "' />" :
				we_html_element::htmlHidden("csv_enclose", $csv_enclose)
				) .
				we_html_element::htmlHiddens(['csv_delimiter' => $csv_delimiter,
					'csv_lineend' => $csv_lineend,
					'csv_fieldnames' => $csv_fieldnames ? 1 : 0]);
		}

		return we_html_tools::getHtmlTop(g_l('modules_customer', '[export_title]'), '', '', '', we_html_element::htmlBody(['onload' => 'document.we_form.submit()'], we_html_element::htmlForm([
						'name' => 'we_form', 'method' => 'post', 'target' => 'load', 'action' => $this->frameset], $hiddens)
				)
		);
	}

	private function getDoExportCode(){
		$customers = we_base_request::_(we_base_request::INTLISTA, 'customers', []);
		$file_format = we_base_request::_(we_base_request::STRING, 'file_format', '');
		$export_to = we_base_request::_(we_base_request::STRING, 'export_to', self::EXPORT_SERVER);
		$path = ($export_to == self::EXPORT_SERVER ? we_base_request::_(we_base_request::FILE, 'path', '') : TEMP_DIR);
		$filename = we_base_request::_(we_base_request::FILE, 'filename', '');
		$firstexec = we_base_request::_(we_base_request::INT, 'firstexec', -999);
		$all = we_base_request::_(we_base_request::INT, 'all', 0);
		$cdata = we_base_request::_(we_base_request::INT, 'cdata', 0);

		$hiddens = we_html_element::htmlHiddens([
				'file_format' => $file_format,
				'filename' => $filename,
				'export_to' => $export_to,
				'path' => $path]);

		if($file_format == we_import_functions::TYPE_GENERIC_XML){
			$hiddens .= we_html_element::htmlHidden('cdata', $cdata);
		}
		if($file_format == self::TYPE_CSV){
			$csv_delimiter = we_base_request::_(we_base_request::RAW_CHECKED, 'csv_delimiter', '');
			$csv_enclose = we_base_request::_(we_base_request::RAW_CHECKED, 'csv_enclose', '');
			$csv_lineend = we_base_request::_(we_base_request::STRING, 'csv_lineend', '');
			$csv_fieldnames = we_base_request::_(we_base_request::BOOL, 'csv_fieldnames', true);

			$hiddens .= ($csv_enclose === '"' ?
				"<input type='hidden' name='csv_enclose' value='" . $csv_enclose . "' />" :
				we_html_element::htmlHidden("csv_enclose", $csv_enclose)
				) .
				we_html_element::htmlHiddens(['csv_delimiter' => $csv_delimiter,
					'csv_lineend' => $csv_lineend]);
		}
		if($customers){
			$options = ['customers' => [],
				'filename' => $_SERVER['DOCUMENT_ROOT'] . $path . '/' . $filename,
				'format' => $file_format,
				'firstexec' => $firstexec,
				'customers' => array_splice($customers, 0, $this->exim_number),
			];

			if($file_format == we_import_functions::TYPE_GENERIC_XML){
				$options['cdata'] = $cdata;
			}
			if($file_format == self::TYPE_CSV){
				$options['csv_delimiter'] = $csv_delimiter;
				$options['csv_enclose'] = $csv_enclose;
				$options['csv_lineend'] = $csv_lineend;
				$options['csv_fieldnames'] = ($firstexec == -999 ? $csv_fieldnames : false);
			}
			$success = we_customer_EI::exportCustomers($options);
		}

		$hiddens .= we_html_element::htmlHidden('art', self::ART_EXPORT) .
			we_html_element::htmlHiddens(
				($customers ? [
				'pnt' => 'eiload',
				'cmd' => 'do_export',
				'firstexec' => 0,
				'all' => $all,
				'customers' => implode(',', $customers)
				] : [
				'pnt' => 'eiload',
				'cmd' => 'end_export']
		));

		$exports = count($customers);
		$percent = max(min(($all ? (int) ((($all - $exports + 2) / $all) * 100) : 0), 100), 0);


		$progressjs = we_html_element::jsElement('if (top.footer.setProgress) top.footer.setProgress(' . $percent . ');');

		return we_html_tools::getHtmlTop(g_l('modules_customer', '[export_title]'), '', '', $progressjs, we_html_element::htmlBody(['onload' => 'document.we_form.submit()'], we_html_element::htmlForm([
						'name' => 'we_form', 'method' => 'post', 'target' => 'load', 'action' => $this->frameset], $hiddens)
				)
		);
	}

	private function getEndExportCode(){
		$export_to = we_base_request::_(we_base_request::FILE, 'export_to', self::EXPORT_SERVER);
		$file_format = we_base_request::_(we_base_request::STRING, 'file_format', '');
		$filename = we_base_request::_(we_base_request::FILE, 'filename', '');
		$path = ($export_to == self::EXPORT_SERVER ? we_base_request::_(we_base_request::FILE, 'path', '') : TEMP_DIR);

		if($file_format == we_import_functions::TYPE_GENERIC_XML){

			$file_name = $_SERVER['DOCUMENT_ROOT'] . $path . '/' . $filename;
			we_customer_EI::save2File($file_name, we_backup_util::weXmlExImFooter);
		}

		return we_html_tools::getHtmlTop(g_l('modules_customer', '[export_title]'), '', '', '', we_html_element::htmlBody(['onload' => 'document.we_form.submit()'], we_html_element::htmlForm([
						'name' => 'we_form', 'method' => 'post', 'target' => 'body', 'action' => $this->frameset], we_html_element::htmlHiddens(['pnt' => 'eibody',
							'step' => 4,
							'art' => self::ART_EXPORT,
							'export_to' => $export_to,
							'filename' => $filename,
							'path' => $path])
					)
				)
		);
	}

	private function getImportNextCode(){
		if(we_base_request::_(we_base_request::INT, 'step') !== false){
			return we_html_tools::getHtmlTop(g_l('modules_customer', '[import_title]'), '', '', self::getJSFrame() .
					we_base_jsCmd::singleCmd('load_processCmd', ['cmd' => 'import_next']), we_html_element::htmlBody(['bgcolor' => '#ffffff',
						'style' => 'margin: 5px'], we_html_element::htmlForm(['name' => 'we_form',
							"method" => 'post', 'target' => 'body', 'action' => $this->frameset], '')
					)
			);
		}
		return '';
	}

	private function getImportBackCode(){
		return we_html_tools::getHtmlTop(g_l('modules_customer', '[import_title]'), '', '', self::getJSFrame() . we_base_jsCmd::singleCmd('load_processCmd', ['cmd' => 'import_back',
					'art' => self::ART_IMPORT]), we_html_element::htmlBody(['bgcolor' => '#ffffff',
					'style' => 'margin: 5px'], we_html_element::htmlForm(['name' => 'we_form',
						'method' => 'post', 'target' => 'body', 'action' => $this->frameset], '')
				)
		);
	}

	private function getImportCode(){
		$filename = we_base_request::_(we_base_request::FILE, 'filename', '');
//		$import_from = we_base_request::_(we_base_request::STRING, "import_from", "");
		$type = we_base_request::_(we_base_request::RAW, 'type', '');
		$xml_from = we_base_request::_(we_base_request::RAW, 'xml_from', '');
		$xml_to = we_base_request::_(we_base_request::RAW, 'xml_to', '');
		$dataset = we_base_request::_(we_base_request::RAW, 'dataset', '');
		$csv_delimiter = we_base_request::_(we_base_request::RAW_CHECKED, 'csv_delimiter', self::CSV_DELIMITER);
		$csv_enclose = we_base_request::_(we_base_request::RAW_CHECKED, 'csv_enclose', self::CSV_ENCLOSE);
		$csv_lineend = we_base_request::_(we_base_request::STRING, 'csv_lineend', self::CSV_LINEEND);
		$the_charset = we_base_request::_(we_base_request::STRING, 'the_charset', self::THE_CHARSET);
		$csv_fieldnames = we_base_request::_(we_base_request::BOOL, 'csv_fieldnames', true);

		$same = we_base_request::_(we_base_request::STRING, 'same', 'rename');

		$field_mappings = we_base_request::_(we_base_request::RAW, 'field_mappings', []);
		$att_mappings = we_base_request::_(we_base_request::RAW, 'att_mappings', []);

		$options = [
			'type' => $type,
			'filename' => $filename,
			'exim' => $this->exim_number
		];
		if($type == self::TYPE_CSV){
			$options['csv_delimiter'] = $csv_delimiter;
			$options['csv_enclose'] = $csv_enclose;
			$options['csv_lineend'] = $csv_lineend;
			$options['the_charset'] = $the_charset;
			$options['csv_fieldnames'] = $csv_fieldnames;
		} else {
			$options['dataset'] = $dataset;
			$options['xml_from'] = $xml_from;
			$options['xml_to'] = $xml_to;
		}

		$filesnum = we_customer_EI::prepareImport($options);
		$hiddens = we_html_element::htmlHiddens(['pnt' => 'eiload',
				'art' => self::ART_IMPORT,
				'cmd' => 'do_import',
				'step' => 5,
				'tmpdir' => $filesnum['tmp_dir'],
				'fstart' => 0,
				'fcount' => $filesnum['file_count'],
				'same' => $same]);

		foreach($field_mappings as $key => $field){
			$hiddens .= we_html_element::htmlHidden('field_mappings[' . $key . ']', $field);
		}
		foreach($att_mappings as $key => $field){
			$hiddens .= we_html_element::htmlHidden('att_mappings[' . $key . ']', $field);
		}

		return we_html_tools::getHtmlTop(g_l('modules_customer', '[import_title]'), '', '', self::getJSFrame() . we_base_jsCmd::singleCmd('load_processCmd', ['cmd' => 'import']), we_html_element::htmlBody([
					'bgcolor' => '#ffffff', 'style' => 'margin:5px'], we_html_element::htmlForm(['name' => 'we_form', 'method' => 'post', 'target' => 'load', 'action' => $this->frameset], $hiddens)
				)
		);
	}

	private function getDoImportCode(){
		$tmpdir = we_base_request::_(we_base_request::FILE, 'tmpdir', '');
		$fstart = we_base_request::_(we_base_request::INT, 'fstart', 0);
		$fcount = we_base_request::_(we_base_request::INT, 'fcount', '');
		$field_mappings = we_base_request::_(we_base_request::RAW, 'field_mappings', []);
		$att_mappings = we_base_request::_(we_base_request::RAW, 'att_mappings', []);
		$same = we_base_request::_(we_base_request::STRING, 'same', 'rename');
		$impno = we_base_request::_(we_base_request::INT, 'impno', 0);

		if(we_customer_EI::importCustomers(['xmlfile' => TEMP_PATH . $tmpdir . '/temp_' . $fstart . '.xml',
				'field_mappings' => $field_mappings,
				'att_mappings' => $att_mappings,
				'same' => $same,
				'logfile' => TEMP_PATH . $tmpdir . '/' . $tmpdir . '.log'
				]
			)){
			$impno++;
		}
		$fstart++;

		$hiddens = we_html_element::htmlHiddens(['pnt' => 'eiload',
				'art' => self::ART_IMPORT,
				'cmd' => 'do_import',
				'tmpdir' => $tmpdir,
				'fstart' => $fstart,
				'fcount' => $fcount,
				'impno' => $impno,
				'same' => $same]);

		foreach($field_mappings as $key => $field){
			$hiddens .= we_html_element::htmlHidden('field_mappings[' . $key . ']', $field);
		}
		foreach($att_mappings as $key => $field){
			$hiddens .= we_html_element::htmlHidden('att_mappings[' . $key . ']', $field);
		}

		$percent = ($fcount == 0 || $fcount == '0' ? 0 : min(100, max(0, (int) (($fstart / $fcount) * 100))) );

		return we_html_tools::getHtmlTop(g_l('modules_customer', '[import_title]'), '', '', self::getJSFrame() . we_base_jsCmd::singleCmd('load_processCmd', ['cmd' => 'do_import',
					'fstart' => $fstart, 'fcount' => $fcount, 'percent' => $percent]), we_html_element::htmlBody(['bgcolor' => '#ffffff', 'style' => 'margin:5px'], we_html_element::htmlForm([
						'name' => 'we_form', 'method' => 'post', 'target' => 'load', 'action' => $this->frameset], $hiddens
					)
				)
		);
	}

	private function getEndImportCode(){
		$tmpdir = we_base_request::_(we_base_request::FILE, "tmpdir", "");
		$impno = we_base_request::_(we_base_request::INT, "impno", 0);

		return we_html_tools::getHtmlTop(g_l('modules_customer', '[import_title]'), '', '', self::getJSFrame() . we_base_jsCmd::singleCmd('load_processCmd', ['cmd' => 'import_end',
					'art' => self::ART_IMPORT]), we_html_element::htmlBody(['bgcolor' => '#ffffff', 'style' => 'margin:5px'], we_html_element::htmlForm(['name' => 'we_form', 'method' => 'post',
						'target' => 'body', 'action' => $this->frameset], we_html_element::htmlHiddens([
							'tmpdir' => $tmpdir,
							'impno' => $impno,
							'pnt' => 'eibody',
							'art' => self::ART_IMPORT,
							'step' => 5])
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

	private function formFileChooser($width = 400, $IDName = 'ParentID', $IDValue = '/', $cmd = '', $filter = ''){
		// IMI: replace enc (+ eval)
		$button = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('browse_server','" . $IDName . "','" . $filter . "',document.we_form.elements['" . $IDName . "'].value,'" . $cmd . "');");

		return we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($IDName, 30, $IDValue, '', 'readonly', 'text', $width, 0), '', 'left', 'defaultfont', '', permissionhandler::hasPerm('CAN_SELECT_EXTERNAL_FILES') ? $button : '');
	}

	function getHTMLCustomer(&$jsCmd){

		switch(we_base_request::_(we_base_request::STRING, 'wcmd')){
			case 'add_customer':
				$customers = we_base_request::_(we_base_request::INTLISTA, 'customers', []);
				$customers = array_unique(array_merge($customers, we_base_request::_(we_base_request::INTLISTA, 'cus', [])));
				break;
			case 'del_customer':
				$customers = we_base_request::_(we_base_request::INTLISTA, 'customers', []);
				if(($id = we_base_request::_(we_base_request::INT, 'cus'))){
					foreach($customers as $k => $v){
						if($v == $id){
							unset($customers[$k]);
						}
					}
				}
				break;
			case 'del_all_customers':
				$customers = [];
				break;
			default:
				$customers = we_base_request::_(we_base_request::INTLISTA, 'customers', []);
		}
		$customers = array_filter($customers);
		$hiddens = we_html_element::htmlHiddens(['wcmd' => '',
				'cus' => we_base_request::_(we_base_request::INTLIST, 'cus', ''),
				'customers' => implode(',', $customers)]);

		$delallbut = we_html_button::create_button(we_html_button::DELETE_ALL, "javascript:we_cmd('del_all_customers')", '', 0, 0, '', '', ($customers ? false : true));
		$addbut = we_html_button::create_button(we_html_button::ADD, "javascript:we_cmd('we_customer_selector','','" . CUSTOMER_TABLE . "','','','add_customer')");
		$custs = new we_chooser_multiDir(400, ($customers ?: []), 'del_customer', $delallbut . $addbut, '', '"we/customer"', CUSTOMER_TABLE);

		$custs->isEditable = permissionhandler::hasPerm('EDIT_CUSTOMER');
		$jsCmd->addCmd('set_topVar', ['name' => 'customers', 'value' => '', 'fromInput' => 1]);

		return $hiddens . $custs->get();
	}

	private function getHTMLChooser($name, $value, $values, $title){
		$select = new we_html_select(['name' => $name . '_select', 'onchange' => "we_cmd('chooser_onchange', this, '" . $name . "')",
			'style' => 'width:200px;']);
		$select->addOption('', '');
		foreach($values as $k => $v){
			$select->addOption(oldHtmlspecialchars($k), oldHtmlspecialchars($v));
		}
		$table = new we_html_table(['class' => 'default', 'width' => 250], 1, 3);

		$table->setColContent(0, 0, we_html_tools::htmlTextInput($name, 5, $value));
		$table->setCol(0, 1, ['style' => 'padding-left:10px;'], $select->getHtml());

		return we_html_tools::htmlFormElementTable($table->getHtml(), $title);
	}

	function getHTMLCustomerFilter(&$jsCmd){
		$count = we_base_request::_(we_base_request::INT, 'filter_count', 0);

		switch(we_base_request::_(we_base_request::STRING, 'fcmd')){
			case 'add_filter':
				$count++;
				break;
			case 'del_filter':
				if($count){
					$count--;
				} else {
					$count = 0;
				}
				break;
			default:
		}

		$jsCmd->addCmd('set_formField', ['name' => 'filter_count', 'value' => $count]);
		$custfields = [];
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

		$operators = ['=', '<>', '<', '<=', '>', '>=', 'LIKE'];
		$logic = ['AND' => 'AND', 'OR' => 'OR'];

		$table = new we_html_table(['class' => 'default'], 1, 3);
		$colspan = '3';

		$c = 0;
		$fields_names = ['fieldname', 'operator', 'fieldvalue', 'logic'];

		for($i = 0; $i < $count; $i++){
			$new = ['fieldname' => '', 'operator' => '', 'fieldvalue' => '', 'logic' => ''];
			foreach($fields_names as $field){
				if(($val = we_base_request::_(we_base_request::STRING, 'filter_' . $field . '_' . $i))){
					$new[$field] = $val;
				}
			}
			if($i != 0){
				$table->addRow();
				$table->setCol($c, 0, ['colspan' => $colspan], we_html_tools::htmlSelect('filter_logic_' . $i, $logic, 1, $new['logic'], false, [], 'value', 70));
				$c++;
			} else {
				$table->addRow();
				$table->setCol($c, 0, ['colspan' => $colspan], we_html_element::htmlHidden('filter_logic_0', ''));
				$c++;
			}

			$table->addRow();
			$table->setCol($c, 0, [], we_html_tools::htmlSelect('filter_fieldname_' . $i, $custfields, 1, $new['fieldname'], false, [], 'value', 200));
			$table->setCol($c, 1, [], we_html_tools::htmlSelect('filter_operator_' . $i, $operators, 1, $new['operator'], false, [], 'value', 70));
			$table->setCol($c, 2, [], we_html_tools::htmlTextInput('filter_fieldvalue_' . $i, 16, $new['fieldvalue']));
			$c++;
		}

		$plus = we_html_button::create_button(we_html_button::PLUS, "javascript:we_cmd('change_filter', 'add_filter')");
		$trash = we_html_button::create_button(we_html_button::TRASH, "javascript:we_cmd('change_filter', 'del_filter')");

		$c++;
		$table->addRow();
		$table->setCol($c, 0, ['colspan' => $colspan, 'style' => 'padding-top:5px;'], $plus . $trash);

		return $js .
			//(array('name'=>"filter_count","value"=>$count)).
			we_html_element::htmlHidden('fcmd', '') .
			$table->getHtml();
	}

	function getOperator($num){
		switch($num){
			case 0:
				return '=';
			case 1:
				return '<>';
			case 2:
				return '<';
			case 3:
				return '<=';
			case 4:
				return '>';
			case 5:
				return '>=';
			case 6:
				return 'LIKE';
		}
	}

	private function getExportCustomers(){
		switch(we_base_request::_(we_base_request::STRING, 'selection')){
			case self::SELECTION_MANUAL:
				return we_base_request::_(we_base_request::INTLISTA, 'customers', []);
			default:
				$filter_count = we_base_request::_(we_base_request::INT, 'filter_count', 0);
				$filter_fieldname = $filter_operator = $filter_fieldvalue = $filter_logic = [];

				$fields_names = ['fieldname', 'operator', 'fieldvalue', 'logic'];
				for($i = 0; $i < $filter_count; $i++){
					foreach($fields_names as $field){
						$var = "filter_" . $field;
						${$var}[] = we_base_request::_(we_base_request::STRING, $var . '_' . $i, 0);
					}
				}
				$filterarr = [];
				foreach($filter_fieldname as $k => $v){
					$filterarr[] = ($k ? (' ' . $filter_logic[$k] . ' ') : '') . $v . ' ' . $this->getOperator($filter_operator[$k]) . " '" . (is_numeric($filter_fieldvalue[$k]) ? $filter_fieldvalue[$k] : $this->db->escape($filter_fieldvalue[$k])) . "'";
				}

				$this->db->query('SELECT ID FROM ' . CUSTOMER_TABLE . ($filterarr ? ' WHERE (' . implode(' ', $filterarr) . ')' : ''));
				return $this->db->getAll(true);
		}
	}

}
