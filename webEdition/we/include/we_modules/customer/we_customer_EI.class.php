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
abstract class we_customer_EI{

	public static function exportCustomers($options = []){
		$code = '';
		switch($options['format']){
			case we_import_functions::TYPE_GENERIC_XML:
				$code = self::exportXML($options);
				break;
			case 'csv':
				$code = self::exportCSV($options);
				break;
		}
		// write to file
		return ($code ?
				self::save2File($options['filename'], $code) :
				false);
	}

	public static function getDataset($type, $filename, $arrgs = []){
		switch($type){
			case we_import_functions::TYPE_GENERIC_XML:
				return self::getXMLDataset($filename, $arrgs['dataset']);
			case 'csv':
				return self::getCSVDataset($filename, $arrgs['delimiter'], $arrgs['enclose'], $arrgs['lineend'], $arrgs['fieldnames'], $arrgs['charset']);
		}
	}

	public static function save2File($filename, $code = '', $flags = 'ab'){
		return we_base_file::save($filename, $code, $flags);
	}

	public static function getCustomersFieldset(){
		$customer = new we_customer_customer();
		return $customer->getFieldset();
	}

	public static function exportXML(array $options = []){
		if(isset($options['customers']) && is_array($options['customers'])){

			$customer = new we_customer_customer();
			$fields = $customer->getFieldsDbProperties();

			$xml_out = (isset($options['firstexec']) && $options['firstexec'] == -999 ?
					we_exim_XMLExIm::getHeader('', 'customer') :
					'');

			foreach($options['customers'] as $cid){
				if($cid){
					$customer_xml = new we_html_baseCollection('customer');
					$customer = new we_customer_customer($cid);
					if($customer->ID){
						foreach(array_keys($fields) as $k){
							if(!$customer->isProtected($k)){
								$value = $customer->{$k};
								if($value != ''){
									$value = ($options['cdata'] ? (we_exim_contentProvider::needCdata($value) ? '<![CDATA[' . $value . ']]>' : $value) : htmlentities($value)); //FIXME: is this a good idea??
								}
								$customer_xml->addChild(new we_html_baseElement($k, true, null, $value));
							}
						}
					}
					$xml_out.=$customer_xml->getHtml() . we_backup_util::backupMarker . "\n";
				}
			}
			return $xml_out . we_exim_XMLExIm::getFooter();
		}
		return '';
	}

	/* Function creates new xml element.
	 *
	 * element - [name] - element name
	 * 				 [attributes] - atributes array in form arry["attribute_name"]=attribute_value
	 * 				 [content] - if array childs otherwise some content
	 *
	 */

	function buildXMLElement($elements){
		$out = '';
		foreach($elements as $element){
			$element = new we_html_baseElement($element['name'], true, $element['attributes'], (is_array($element['content']) ? self::buildXMLElement($element['content']) : $element['content']));
			$out.=$element->getHTML();
		}
		return $out;
	}

	function getXMLDataset($filename, $dataset){
		$xp = new we_xml_parser($_SERVER['DOCUMENT_ROOT'] . $filename);
		$nodeSet = $xp->evaluate($xp->root . '/' . $dataset . '[1]/child::*');
		$nodes = $attrs = [];

		foreach($nodeSet as $node){
			$nodeName = $xp->nodeName($node);
			$nodeattribs = [];
			if($xp->hasAttributes($node)){
				$attrs = $attrs + ['@n:' => g_l('modules_customer', '[none]')];
				$attributes = $xp->getAttributes($node);
				foreach($attributes as $name => $value){
					$nodeattribs[$name] = $value;
				}
			}
			$nodes[$nodeName] = $nodeattribs;
		}
		return $nodes;
	}

	function exportCSV(array $options = []){
		if(isset($options['customers']) && is_array($options['customers'])){
			$customer_csv = [];
			$customer = new we_customer_customer();
			$fields = $customer->getFieldsDbProperties();
			foreach($options['customers'] as $cid){
				if($cid){
					$customer = new we_customer_customer($cid);
					if($customer->ID){
						$customer_csv[$cid] = [];
						foreach($fields as $k => $v){
							if(!$customer->isProtected($k)){
								$value = $customer->{$k};
								$customer_csv[$cid][] = $value;
							}
						}
					}
				}
			}

			$field_names = [];
			foreach($fields as $k => $v){
				if(!$customer->isProtected($k)){
					$field_names[] = $k;
				}
			}

			$csv_out = '';
			$enclose = trim($options['csv_enclose']);
			switch(trim($options['csv_lineend'])){
				case g_l('modules_customer', '[unix]'):
					$lineend = "\n";
					break;
				case g_l('modules_customer', '[mac]') :
					$lineend = "\r";
					break;
				default:
					$lineend = "\r\n";
					break;
			}
			$delimiter = $enclose . ($options['csv_delimiter'] === '\t' ? "\t" : trim($options['csv_delimiter'])) . $enclose;

			if($options['csv_fieldnames']){
				$csv_out.=$enclose . implode($delimiter, $field_names) . $enclose . $lineend;
			}

			foreach($customer_csv as $ck => $cv){
				$csv_out.=$enclose . implode($delimiter, $cv) . $enclose . $lineend;
			}

			return $csv_out;
		}
		return '';
	}

	function getCSVDataset($filename, $delimiter, $enclose, $lineend, $fieldnames, $charset){
		if(!$charset){
			$charset = DEFAULT_CHARSET;
		}
		if($delimiter === '\t'){
			$delimiter = "\t";
		}
		$csvFile = $_SERVER['DOCUMENT_ROOT'] . $filename;
		$nodes = [];

		if(file_exists($csvFile) && is_readable($csvFile)){
			$recs = [];

			if($lineend === 'mac'){
				we_base_file::replaceInFile("\r", "\n", $csvFile);
			}

			$cp = new we_import_CSV;
			$cp->setFile($csvFile);
			$cp->setDelim($delimiter);
			$cp->setFromCharset($charset);
			$cp->setEnclosure(($enclose === '') ? '"' : $enclose);
			$cp->parseCSV();
			$num = count($cp->FieldNames);
			$recs = [];
			for($c = 0; $c < $num; $c++){
				$recs[$c] = $cp->CSVFieldName($c);
			}
			for($i = 0; $i < count($recs); $i++){
				if($fieldnames){
					$nodes[$recs[$i]] = [];
				} else {
					$nodes[g_l('modules_customer', '[record_field]') . ' ' . ($i + 1)] = [];
				}
			}
		}

		return $nodes;
	}

	function getUniqueId(){
		// md5 encrypted hash with the start value microtime(). The function
		// uniqid() prevents from simultanious access, within a microsecond.
		return md5(uniqid(__FILE__, true)); // #6590, changed from: uniqid(microtime())
	}

	function prepareImport($options){
		$ret = ['tmp_dir' => '',
			'file_count' => '',
			];

		$type = $options['type'];
		$filename = $options['filename'];

		switch($type){
			case we_import_functions::TYPE_GENERIC_XML:
				$dataset = $options['dataset'];
				$xml_from = $options['xml_from'];
				$xml_to = $options['xml_to'];

				$parse = new we_xml_splitFile($_SERVER['DOCUMENT_ROOT'] . $filename);
				$parse->splitFile('*/' . $dataset, $xml_from, $xml_to);

				$ret['tmp_dir'] = str_replace(TEMP_PATH, '', $parse->path);
				$ret['file_count'] = $parse->fileId;
				break;

			case 'csv':
				$csv_delimiter = $options['csv_delimiter'];
				$csv_enclose = $options['csv_enclose'];
				$csv_fields = $options['csv_fieldnames'];
				$csv_charset = $options['the_charset'];
				//$exim = $options['exim'];

				$csvFile = $_SERVER['DOCUMENT_ROOT'] . $filename;

				if(file_exists($csvFile) && is_readable($csvFile)){

					// create temp dir
					$unique = self::getUniqueId();
					$path = TEMP_PATH . $unique;

					we_base_file::createLocalFolderByPath($path);
					$path.='/';

					$fcount = 0;
					$rootnode = ['name' => 'customer',
						'attributes' => null,
						'content' => []
						];

					$csv = new we_customer_CSVImport();

					$csv->setDelim($csv_delimiter);
					$csv->setEnclosure($csv_enclose);
					$csv->setHeader($csv_fields);
					$csv->setFile($csvFile);
					$csv->setFromCharset($csv_charset);
					$csv->setToCharset('UTF-8');
					$csv->parseCSV();
					while(($data = $csv->CSVFetchRow()) != FALSE){
						$rootnode['content'] = [];
						foreach($data as $kdat => $vdat){
							$rootnode['content'][] = ['name' => ($csv_fields ? $csv->FieldNames[$kdat] : (str_replace(' ', '', g_l('modules_customer', '[record_field]')) . ($kdat + 1))),
								'attributes' => null,
								'content' => '<![CDATA[' . $vdat . ']]>'
								];
						}
						$f = $path . 'temp_' . $fcount . '.xml';
						self::save2File($f, we_exim_XMLExIm::getHeader('UTF-8', 'customer', true) . self::buildXMLElement([$rootnode]), 'wb');
						we_base_file::insertIntoCleanUp($f);
						$fcount++;
					}
					we_base_file::insertIntoCleanUp(rtrim($path, '/'));
					$ret['tmp_dir'] = $unique;
					$ret['file_count'] = $fcount;
				}
				return $ret;
		}

		return $ret;
	}

	function importCustomers($options = []){
		$ret = false;
		$xmlfile = isset($options['xmlfile']) ? $options['xmlfile'] : '';
		$field_mappings = isset($options['field_mappings']) ? $options['field_mappings'] : [];
		//$attrib_mappings = isset($options['attrib_mappings']) ? $options['attrib_mappings'] : [];

		$same = isset($options['same']) ? $options['same'] : '';
		$logfile = isset($options['logfile']) ? $options['logfile'] : '';

		$db = $GLOBALS['DB_WE'];

		$customer = new we_customer_customer();
		$xp = new we_xml_parser($xmlfile);

		$fields = array_flip($field_mappings);
		$nodeSet = $xp->evaluate($xp->root . '/*');
		foreach($nodeSet as $node){
			$node_name = $xp->nodeName($node);
			$node_value = $xp->getData($node);
			if(isset($fields[$node_name])){
				$customer->{$fields[$node_name]} = iconv('UTF-8', DEFAULT_CHARSET, $node_value);
			}
		}
		$existid = f('SELECT ID FROM ' . CUSTOMER_TABLE . ' WHERE Username="' . $db->escape($customer->Username) . '" AND ID!=' . intval($customer->ID));
		if($existid){
			switch($same){
				case 'rename':
					$exists = true;
					$count = 0;
					$oldname = $customer->Username;
					while($exists){
						$count++;
						$new_name = $customer->Username . $count;
						$exists = f('SELECT ID FROM ' . CUSTOMER_TABLE . ' WHERE Username="' . $db->escape($new_name) . '" AND ID!=' . intval($customer->ID));
					}
					$customer->Username = $new_name;
					$customer->save();
					self::save2File($logfile, sprintf(g_l('modules_customer', '[rename_customer]'), $oldname, $customer->Username) . "\n");
					$ret = true;
					break;
				case 'overwrite':
					$customer->overwrite($existid);
					self::save2File($logfile, sprintf(g_l('modules_customer', '[overwrite_customer]'), $customer->Username) . "\n");
					$ret = true;
					break;
				default:
				case 'skip':
					self::save2File($logfile, sprintf(g_l('modules_customer', '[skip_customer]'), $customer->Username) . "\n");
					break;
			}
		} else {
			$ret = true;
			$customer->save();
		}

		unlink($xmlfile);
		return $ret;
	}

}
