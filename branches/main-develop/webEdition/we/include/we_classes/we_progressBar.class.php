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
class we_progressBar{
	private $progress = 0;
	private $texts = [];
	private $stud_width = 10;
	private $stud_len = 100;
	private $name = "";
	private static $studLen = [];

	public function __construct($progress = 0, $stud_len = 100, $name = ''){
		$this->setProgress($progress);
		$this->stud_len = $stud_len;
		$this->name = $name;
		self::$studLen[$name] = $stud_len;
	}

	public static function getJSCode(){
		return we_html_element::jsScript(JS_DIR . 'we_progressBar.js', '', ['id' => 'loadVarProgressBar', 'data-progress' => setDynamicVar(self::$studLen)]);
	}

	public function addText($text = "", $id = "", $class = "small", $color = "#006699", $height = 10, $bold = 1){
		$this->texts[] = ['name' => $id, "text" => $text, "class" => $class, "color" => $color, "bold" => $bold, "italic" => 0, "height" => $height];
	}

	private function setProgress($progress = 0){
		$this->progress = min(100, $progress);
	}

	public function setStudWidth($stud_width = 10){
		$this->stud_width = $stud_width;
	}

	public function getHTML($class = '', $style = ''){
		 $top = '';

		$this->addText($this->progress . '%', 'progress_text');

		foreach($this->texts as $text){
			$top .= '<td ' . ($text['name'] ? 'id="' . $text['name'] . $this->name . '" ' : "") . 'class="' . $text['class'] . ($text["bold"] ? " bold" : "" ) . '" style="line-height:12px;color:' . $text["color"] . ';margin-right:5px;">' . $text["text"] . '</td>';
		}


		$progress_len = ($this->stud_len / 100) * $this->progress;
		$rest_len = $this->stud_len - $progress_len;

		return '<table class="default ' . $class . '" style="display:inline-table;font-size: 1px;line-height: 0;' . $style . '">' .
			'<tr>' . $top . '</tr>' .
			'<tr>' .
			'<td><div id="progress_image' . $this->name . '" class="progress_image" style="width:' . $progress_len . 'px;height:' . ($this->stud_width - 2) . 'px;"></div><div id="progress_image_bg' . $this->name . '" class="progress_image_bg" style="width:' . $rest_len . 'px;height:' . ($this->stud_width - 2) . 'px;"></div></td>' .
			'</tr>'
			. '</table>';
	}

}
