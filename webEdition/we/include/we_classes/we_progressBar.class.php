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
	var $progress = 0;
	var $texts = [];
	var $stud_width = 10;
	var $stud_len = 100;
	var $progressTextPlace = 1;
	var $name = "";

	public function __construct($progress = 0){
		$this->setProgress($progress);
	}

	public function getJSCode(){
		return we_html_element::jsElement('
function setProgressText(name,text){
	var div = document.getElementById(name);
	if(div){
		div.innerHTML = text;
	}
}

function setProgress(name,progress){
	var koef=' . ($this->stud_len / 100) . ';
		document.getElementById("progress_image"+name).style.width=koef*progress+"px";
		document.getElementById("progress_image_bg"+name).style.width=(koef*100)-(koef*progress)+"px";
		setProgressText("progress_text"+name,progress+"%");
}');
	}

	public function addText($text = "", $place = 0, $id = "", $class = "small", $color = "#006699", $height = 10, $bold = 1){
		$this->texts[] = ['name' => $id, "text" => $text, "class" => $class, "color" => $color, "bold" => $bold, "italic" => 0, "place" => $place, "height" => $height];
	}

	private function setProgress($progress = 0){
		$this->progress = min(100, $progress);
	}

	public function setName($name){
		$this->name = $name;
	}

	public function setStudWidth($stud_width = 10){
		$this->stud_width = $stud_width;
	}

	public function setStudLen($stud_len = 100){
		$this->stud_len = $stud_len;
	}

	function setProgressTextPlace($place = 0){
		$this->progressTextPlace = $place;
	}

	function emptyTexts(){
		$this->texts = [];
	}

	public function getHTML($class = '', $style = ''){
		$left = $right = $top = $bottom = '';

		$this->addText($this->progress . '%', $this->progressTextPlace, 'progress_text');

		foreach($this->texts as $text){
			switch($text["place"]){
				case 0:
					$top .= '<td ' . ($text['name'] ? 'id="' . $text['name'] . $this->name . '" ' : "") . 'class="' . $text['class'] . ($text["bold"] ? " bold" : "" ) . '" style="line-height:12px;color:' . $text["color"] . ';margin-right:5px;">' . $text["text"] . '</td>';
					break;
				case 1:
					$right .= '<td ' . ($text['name'] ? 'id="' . $text['name'] . $this->name . '" ' : "") . 'class="' . $text['class'] . $text['class'] . ($text["bold"] ? " bold" : "" ) . '" style="line-height:12px;color:' . $text["color"] . ';padding-left:5px;">' . $text["text"] . '</td>';
					break;
				case 2:
					$bottom .= '<td ' . ($text['name'] ? 'id="' . $text['name'] . $this->name . '" ' : "") . 'class="' . $text['class'] . $text['class'] . ($text["bold"] ? " bold" : "" ) . '" style="line-height:12px;color:' . $text["color"] . ';margin-right:5px;">' . $text["text"] . '</td>';
					break;
				case 3:
					$left .= '<td ' . ($text['name'] ? 'id="' . $text['name'] . $this->name . '" ' : "") . 'class="' . $text['class'] . $text['class'] . ($text["bold"] ? " bold" : "" ) . '" style="line-height:12px;color:' . $text["color"] . ';margin-right:5px;">' . $text["text"] . '</td>';
					break;
			}
		}


		$progress_len = ($this->stud_len / 100) * $this->progress;
		$rest_len = $this->stud_len - $progress_len;

		return '<table class="default ' . $class . '" style="display:inline-table;font-size: 1px;line-height: 0;' . $style . '">' . ($top ?
			'<tr>' . $top . '</tr>' : '') .
			'<tr>' . $left .
			'<td><div id="progress_image' . $this->name . '" class="progress_image" style="width:' . $progress_len . 'px;height:' . ($this->stud_width - 2) . 'px;"></div><div id="progress_image_bg' . $this->name . '" class="progress_image_bg" style="width:' . $rest_len . 'px;height:' . ($this->stud_width - 2) . 'px;"></div></td>' .
			$right .
			'</tr>' .
			($bottom ? '<tr>' . $bottom . '</tr>' : '' )
			. '</table>';
	}

}
