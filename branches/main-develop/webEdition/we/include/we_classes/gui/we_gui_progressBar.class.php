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
class we_gui_progressBar{
	const TOP = 0;
	const RIGHT = 1;
	const BOTTOM = 2;
	const LEFT = 3;

	private $progress = 0;
	private $texts = [];
	private $stud_len = 100;
	private $progressTextPlace = self::RIGHT;
	private $name = "";
	private static $studLen = [];

	public function __construct($progress = 0, $stud_len = 100, $name = ''){
		$this->setProgress($progress);
		$this->stud_len = $stud_len;
		$this->name = $name;
		self::$studLen[$name] = [
			'koef' => $stud_len / 100
		];
	}

	public static function getJSCode(){
		return we_html_element::jsScript(JS_DIR . 'we_progressBar.js', '', ['id' => 'loadVarProgressBar', 'data-progress' => setDynamicVar(self::$studLen)]);
	}

	public function addText($text = "", $place = self::TOP, $id = ""){
		$this->texts[] = ['name' => $id, "text" => $text, "place" => $place];
	}

	private function setProgress($progress = 0){
		$this->progress = min(100, $progress);
	}

	public function setProgressTextPlace($place = 0){
		$this->progressTextPlace = $place;
	}

	public function getHTML($class = '', $style = ''){
		$left = $right = $top = $bottom = '';

		$this->addText($this->progress . '%', $this->progressTextPlace, 'progress_text');
		$this->addText('', self::BOTTOM, 'elapsedTime');

		foreach($this->texts as $text){
			$tmp = '<td ' . ($text['name'] ? 'id="' . $text['name'] . $this->name . '" ' : "") . 'class="small bold">' . $text["text"] . '</td>';
			switch($text['place']){
				case self::TOP:
					$top .= $tmp;
					break;
				case self::RIGHT:
					$right .= $tmp;
					break;
				case self::BOTTOM:
					$bottom .= $tmp;
					break;
				case self::LEFT:
					$left .= $tmp;
					break;
			}
		}


		$progress_len = ($this->stud_len / 100) * $this->progress;
		$rest_len = $this->stud_len - $progress_len;

		return '<table class="default progressbar ' . $class . '" style="' . $style . '">' .
			($top ? '<tr>' . $top . '</tr>' : '') .
			'<tr>' . $left .
			'<td style="padding-right:5px;"><div id="progress_image' . $this->name . '" class="progress_image" style="width:' . $progress_len . 'px;"></div><div id="progress_image_bg' . $this->name . '" class="progress_image_bg" style="width:' . $rest_len . 'px;"></div></td>' .
			$right .
			'</tr>' .
			($bottom ? '<tr>' . $bottom . '</tr>' : '' )
			. '</table>';
	}

}
