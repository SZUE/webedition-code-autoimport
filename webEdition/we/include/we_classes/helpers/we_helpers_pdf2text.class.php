<?php

/**
 * webEdition CMS
 *
 * $Rev: 5656 $
 * $Author: mokraemer $
 * $Date: 2013-01-29 00:36:45 +0100 (Di, 29. Jan 2013) $
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
 * @author		 Marc Krämer
 * @category   webEdition
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
//define('DEBUG', 'fontout|page|tree|line'); //line|fontout|page|tree
//define('DEBUG', 'tree'); //line|fontout|page|tree
//define('DEBUG_MEM', 1);
//ini_set('memory_limit', '21M');

class we_helpers_pdf2text{

	const READPORTION = 512000;
	const NL = "\n";
	const SPACE = ' ';
	const DEFLATE_ALL = false;

	private static $space = 0;
	private static $encodings = array();
	private static $mapping = array();
	private $root = '';
	private $data = array();
	private $fonts = array();
	private $unset = array();
	private $objects = array();
	private $currentFontRessource = array();
	private $text = '';
	private $file = '';

	public function __construct($file){
		$this->file = $file;
	}

	public function processText(){
		$this->setupFont();
		defined('DEBUG') && $this->mem();
		$this->fillData($this->file);
		defined('DEBUG') && $this->mem();
		$this->unset = array();
		if(defined('DEBUG') && strstr(DEBUG, 'tree')){
			print_r($this->data);
		}
		$this->setFontTables();
		defined('DEBUG') && $this->mem();
		$this->getAllPageObjects(trim($this->data[$this->root]['Pages'], ' []'));
		defined('DEBUG') && $this->mem();
		$this->unsetElem();
		$this->getText();
		unset($this->data);
		/* echo $this->root;
		  print_r($this->fonts);
		  print_r($this->objects); */

		return $this->text;
	}

	public function getInfo(){
		$offset = filesize($this->file) - 1024;
		$file = fopen($this->file, 'r');
		$data = fread($file, 1024);
		fseek($file, $offset);
		$data .= fread($file, 1024);
		fclose($file);
		$match = array();
		if(preg_match('#trailer[\r\n ]*<<(.*)>>#s', $data, $match)){
			preg_match_all('#/(\w+)[ \r\n]{0,2}(\d+ \d+) R[\r\n]*#s', $match[1], $match, PREG_SET_ORDER);

			foreach($match as $cur){
				if($cur[1] == 'Info'){
					$info = $cur[2];
					break;
				}
			}
			for($data = $this->readPortion($this->file); !empty($data); $data = $this->readPortion()){
				if(preg_match('#[\r\n ]+(' . $info . ' obj.*endobj)#Us', $data, $match)){
					$this->readPortion(-1);
					$this->parsePDF($match[0]);
					break;
				}
			}
			$info = $this->data[$info];
			$this->data = array();
			foreach($info as $key => &$cur){
				$cur = trim($cur, '() ');
				if(strstr($key, 'Date')){
					if(($cur = DateTime::createFromFormat('YmdHis', substr($cur, 2, 14)))){
						$cur = $cur->format(g_l('date', '[format][default]'));
					}
				}
			}
			return $info;
		}
		return array();
	}

	private function setFontTables(){
		foreach($this->fonts as $cur){
			$elem = &$this->data[$cur];
			$elem['charMap'] = array();
			$encoding = (isset($elem['Encoding']) ? $elem['Encoding'] : '');
			if(substr($encoding, -1) == 'R'){
				$id = rtrim($encoding, 'R ');
				$this->unset[] = rtrim($encoding, 'R ');
				$this->processFontDictionary($this->data[$id], $elem);
			} else{
				$this->setDefaultFontTable($encoding, $elem);
			}

			if(isset($elem['ToUnicode'])){
				$id = rtrim($elem['ToUnicode'], ' R');
				$this->unset[] = $id;

				self::applyToUnicode(self::getStream($this->data[$id]), $elem['charMap']);
			}
		}
	}

	private function processFontDictionary($dict, &$elem){
		$this->setDefaultFontTable(isset($dict['BaseEncoding']) ? $dict['BaseEncoding'] : '', $elem);
		//print_r($elem);
		if(isset($dict['Differences'])){
			$matches = array();
			$diff = $dict['Differences'];
			preg_match_all('#(\d+)(([\r\n ]*\/\w+)*)#s', $diff, $matches, PREG_SET_ORDER);
			foreach($matches as $m){
				$start = $m[1];
				$replace = explode(' ', trim(strtr($m[2], array("\n" => ' ', "\r" => ' ', '/' => ' ', '_' => '', '   ' => ' ', '  ' => ' '))));
				foreach($replace as $cur){
					$cur = trim($cur);
					if(empty($cur)){
						continue;
					}
					$from = $this->unichr($start++);
					if(!isset($this->mapping[$cur])){
						continue;
					}
					$to = $this->mapping[trim($cur)];
					if($from != $to){
						$elem['charMap'][$from] = $to;
					}
				}
			}
		}
		//print_r($elem);
	}

	private function setDefaultFontTable($encoding, &$elem){
		switch($encoding){
			default:
				$encoding = ltrim($encoding, '/');
				if(isset($this->encodings[$encoding])){
					$elem['charMap'] = $this->encodings[$encoding];
					return;
				} else{
					//print_r($this->encodings);
					echo 'not found:' . $encoding;
				}
			case '':
			case '/Identity':
			case '/Identity-h':
			case '/Identity-v':
				$elem['charMap'] = $this->encodings['standardEncoding'];
		}
	}

	private static function getStream($elem){
		if(!isset($elem['stream'])){
			print_r($elem);
		}
		return (!self::DEFLATE_ALL && $elem['Filter'] == '/FlateDecode' ?
				@gzuncompress($elem['stream']) :
				$elem['stream']);
	}

	private static function applyToUnicode($data, &$table){
		$match = array();
		if(preg_match('#beginbfchar(.*)endbfchar#s', $data, $match)){
			preg_match_all('#<([[:alnum:]]*)>[ ]*<([[:alnum:]]*)>#s', $match[1], $match);
			//print_r($match);
			foreach($match[1] as $key => $cur){
				if($cur == $match[2][$key]){
					continue;
				}
				$table[chr(hexdec($cur))] = self::unichr($match[2][$key], true);
			}
			//print_r($table);
		}
		if(preg_match('#beginbfrange(.*)endbfrange#s', $data, $match)){
			preg_match_all('#<([[:alnum:]]{2,4})>[ ]*<([[:alnum:]]{2,4})>[ ]*\[*([ ]*<[[:alnum:]]{2,4}>)+\]*#s', $match[1], $match);
			foreach($match[1] as $key => $cur){
				$start = hexdec($cur);
				$end = hexdec($match[2][$key]);
				$values = trim($match[3][$key], '<> ');
				if(strlen($values) <= 4){
					//single value incremented
					$value = hexdec($values);
					if($start == $value){
						//equal maps
						continue;
					}
					for(; $start < $end; ++$start){
						$table[self::unichr($start)] = self::unichr($value++);
					}
				} else{
					$values = explode('>', strtr($values, array(' ' => '', '<' => '')));
					foreach($values as $cur){
						$table[self::unichr($start++)] = self::unichr($cur, true);
					}
				}
			}
		}
	}

	private function fillData($fname){
		for($data = $this->readPortion($fname); !empty($data); $data = $this->readPortion()){
			$this->parsePDF($data);
		}
		defined('DEBUG') && $this->mem();
	}

	/**
	 * Return unicode char by its code
	 *
	 * @param int $u
	 * @return char
	 */
	private static function unichr($u, $hex = false){
		if($hex){
			$ret = '';
			foreach(str_split($u, 4) as $cur){
				$ret.=mb_convert_encoding('&#x' . $cur . ';', 'UTF-8', 'HTML-ENTITIES');
			}
			return $ret;
		} else{
			return mb_convert_encoding('&#' . intval($u) . ';', 'UTF-8', 'HTML-ENTITIES');
		}
	}

	private function setupFont(){
		if(!empty($this->encodings)){
			return;
		}
		require('we_helpers_pdfmapping.inc.php');
		require('we_helpers_pdfencodings.inc.php');
		foreach($nameToUnicodeTab as &$cur){
			$cur = self::unichr($cur);
		}
		unset($cur);

		$newEnc = array();
		foreach($encodings as $type => $myenc){
			$newEnc[$type] = array();
			foreach($myenc as $key => $char){
				if($char != NULL){
					$char = $nameToUnicodeTab[$char];
					$key = chr($key);
					if($char != $key){
						$newEnc[$type][$key] = $char;
					}
				} else{
					$newEnc[$type][chr($key)] = '';
				}
			}
			unset($char);
		}
		unset($myenc);
		$this->encodings = $newEnc;
		$this->mapping = $nameToUnicodeTab;
	}

	private function readPortion($fname = ''){
		static $file = 0;
		static $lastPos = 0;
		if($fname == -1){
			if($file){
				fclose($file);
				$file = 0;
			}
			return'';
		}

		$file = $file ? $file : fopen($fname, 'r');
		$data = '';
		while(($read = fread($file, self::READPORTION))) {
			$data.=$read;
			if(strrpos($read, 'endobj') !== FALSE){
				break;
			}
		}
		if(!$data || (strlen($data) < self::READPORTION && (strrpos($data, 'endobj') === FALSE))){
			fclose($file);
			return '';
		}

		$pos = (strrpos($data, 'endobj') + 7) - strlen($data);
		fseek($file, $pos, SEEK_CUR);
		$lastPos+=strlen($data) + $pos;
		return substr($data, 0, $pos);
	}

	private function parsePDF($data){
		$matches = $matches2 = $matches3 = array();
		preg_match_all('#(\d+ \d+) obj[\r\n]+(.*)endobj#Us', $data, $matches, PREG_SET_ORDER);
		defined('DEBUG') && $this->mem();
		unset($data);
		defined('DEBUG') && $this->mem();
		foreach($matches as $key => $m){
			unset($matches[$key]);
			if(in_array($m[1], $this->unset)){
				continue;
			}
			$values = array();
			if(!preg_match('#(xxxx\d+xxx)|<<(.*)>>[\r\n ]*stream[\r\n]+(.*)endstream#s', $m[2], $matches2)){
				if(!preg_match('#(\d+)|<<(.*)>>#s', $m[2], $matches2)){
					continue;
				}
			}
			defined('DEBUG') && $this->mem();
			unset($m[2]);
			if(isset($matches2[2])){
				preg_match_all('#/(\w+)[ \r\n]{0,2}(\d+ \d+ R|/\w+|\[[^\]]*\]|\([^)]*\))[\r\n]*#s', $matches2[2], $matches3, PREG_SET_ORDER);
				defined('DEBUG') && $this->mem();
				foreach($matches3 as $cur){
					$values[$cur[1]] = $cur[2];
				}
				if(isset($values['Type'])){
					switch($values['Type']){
						case '/FontDescriptor':
							$set = isset($values['FontFile']) ? $values['FontFile'] : (isset($values['FontFile2']) ? $values['FontFile2'] : (isset($values['FontFile3']) ? $values['FontFile3'] : ''));
							if($set){
								$this->unset[] = rtrim($set, ' R');
							}
							continue 2;
						case '/Catalog':
							$this->root = $m[1];
							break;
						case '/XObject':
							continue 2;
						case '/Font':
							$this->fonts[] = $m[1];
							break;
					}
				}

				if(isset($values['Subtype'])){
					switch($values['Subtype']){
						case '/Image':
						/* 						case '/TrueType':
						  case '/Type1':
						  case '/Type2':
						  case '/Type3': */
						case '/XML':
						case '/Link': //no need for links
						case '/Type1C': //Filter font files
							continue 2;
					}
				}
			}
			if($matches2[1]){
				$values['value'] = $matches2[1];
			}
			if(isset($matches2[3])){
				$values['stream'] = (self::DEFLATE_ALL && isset($values['Filter']) && $values['Filter'] == '/FlateDecode' ? @gzuncompress($matches2[3]) : $matches2[3]);
			}
			/* if(isset($values['Filter'])&&!isset($values['stream'])){
			  print_r($matches2);
			  print_r( $m[2]);
			  } */
			$this->data[$m[1]] = $values;
		}
		defined('DEBUG') && $this->mem();
	}

	private function getAllPageObjects($id){
		$id = array_map('trim', array_filter(explode(' R', $id)));
		foreach($id as $cur){
			if(empty($cur)){
				continue;
			}
			$elem = $this->data[$cur];
			switch($elem['Type']){
				case '/Pages':
					$this->unset[] = $cur;
					$this->getAllPageObjects(trim($elem['Kids'], ' []'));
					break;
				case '/Page':
					if(defined('DEBUG') && strstr(DEBUG, 'page')){
						print_r($elem);
					}
					$fonts = array();
					$this->getPageFonts($fonts, $elem);
					if(isset($elem['Font'])){
						$this->getPageFonts($fonts, $this->data[rtrim($elem['Font'], ' R')]);
					}
					if(isset($elem['Resources'])){
						$this->getPageFonts($fonts, $this->data[rtrim($elem['Resources'], ' R')]);
					}
					if(!empty($fonts)){
						$fonts['Type'] = '/FontRessource';
						$this->data[$cur] = $fonts;
						$this->objects[] = $cur;
					}
					$x = array_filter(explode(' R', trim($elem['Contents'], ' []')));
					$x = array_map('trim', $x);
					$this->objects = array_merge($this->objects, $x);
			}
		}
	}

	private function getPageFonts(array &$fonts, array $elem){
		foreach($elem as $key => $cur){
			if($key == 'stream'){
				continue;
			}
			$cur = rtrim($cur, ' R');
			if(in_array($cur, $this->fonts)){
				$fonts[$key] = $this->data[$cur]['charMap'];
			}
		}
	}

	private function getText(){
		$texts = $lines = array();
		foreach($this->objects as $cur){
			$elem = $this->data[$cur];
			unset($this->data[$cur]);
			if(isset($elem['Type']) && $elem['Type'] == '/FontRessource'){
				$this->currentFontRessource = $elem;
				continue;
			}
			$stream = self::getStream($elem);
			preg_match_all('#BT[\r\n]+(.*)ET#Us', $stream, $texts, PREG_SET_ORDER);
			unset($stream);
			foreach($texts as $m){
				$this->setTextLines($m[1]);
			}
		}
	}

	private function setTextLines($text){
		static $selectedFont = '';
		$tmpText = '';
		$fs = 10;
		$hasData = false;
		$lines = array();
		preg_match_all('#([^\r\n]*)?[ \r\n]{0,2}(T.|rg|RG|"|\')#Us', $text, $lines, PREG_SET_ORDER);
		/* print_r(str_replace("\r","\n",$text));
		  print_r($lines);
		  return; */
		foreach($lines as $line){
			if(defined('DEBUG') && strstr(DEBUG, 'line')){
				print_r($line);
			}
			switch($line[2]){
				case 'Tf'://fontsize
					if($hasData){
						$this->applyTextChars($tmpText, $selectedFont);
						$tmpText = '';
					}
					$hasData = false;
					list($selectedFont, $fs) = explode(' ', trim($line[1], ' '));
					$fs = floatval($fs);
					$selectedFont = trim($selectedFont, ' /');
					break;
				case 'T*'://newline
					$tmpText .= self::NL;
					break;
				case 'Td'://potential newline
					list(, $tmp) = explode(' ', $line[1]);
					if($tmp){
						$tmpText .= self::NL;
					}
					break;
				case 'TD'://newline
					$tmpText .= self::SPACE;
					break;
				case '\'':
				case '"':
					$tmpText .= self::NL;
//no break
				case 'TJ':
				case 'Tj':
					$hasData = true;
					$tmpText.=$this->extractPSTextElement($line[1], $fs);
					break;
			}
		}
		$tmpText.=self::SPACE;
		$this->applyTextChars($tmpText, $selectedFont);
	}

	private function applyTextChars($text, $selectedFont){
		$text = str_replace(array('\\\\', '\(', '\)'), array('\\\\', '(', ')'), $text);

		if($selectedFont == ''){
			$this->text.=$text;
			return;
		}

		if(isset($this->currentFontRessource[$selectedFont])){
			if(defined('DEBUG') && strstr(DEBUG, 'fontout')){
				print_r($this->currentFontRessource[$selectedFont]);
			}
			$res = $this->currentFontRessource[$selectedFont];
			$tmp = '';
			for($i = 0; $i < strlen($text); ++$i){
				$x = $text{$i};
				$tmp.=isset($res[$x]) ? $res[$x] : $x;
			}
			//		$tmp = str_replace(array_keys($this->currentFontRessource[$selectedFont]), $this->currentFontRessource[$selectedFont], $text);
			if(defined('DEBUG') && strstr(DEBUG, 'fontout')){
				echo 'Font:' . $selectedFont . ' ' . $text . ' post: ' . $tmp . "\n";
			}
			$this->text.=$tmp;
		} else{
			echo 'Error-text: ' . $selectedFont;
		}
	}

	private function setOctChar($char){
		return chr(octdec($char[1]));
	}

	private function extractPSTextElement($string, $fs){
		self::$space = -4 * $fs;
		$parts = array();
		preg_match_all('#\(((?:\\\\.|[^\\\\\\)])+)\)(-?\d+\.\d{1,7})?#', $string, $parts);

		//add spaces only if size is bigger than a certain amount
		$parts[2] = array_filter($parts[2], 'self::lower');
		foreach(array_keys($parts[2]) as $key){
			$parts[1][$key].=self::SPACE;
		}
		$tmp = implode('', $parts[1]);

		return preg_replace_callback('#\\\\(\d{3})#', 'self::setOctChar', $tmp);
	}

	private static function lower($val){
		return $val < self::$space;
	}

	private function unsetElem(){
		foreach($this->unset as $cur){
			unset($this->data[$cur]);
		}
		$this->unset = array();
	}

	private function mem($last = false){
		static $max = 0;
		if(defined('DEBUG_MEM')){
			if($max < memory_get_usage()){
				$max = memory_get_usage();
			}
		}
		if($last)
			print('Mem usage ' . round((($max / 1024) / 1024), 3) . ' MiB' . "\n");
	}

}
