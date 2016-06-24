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
	var $showProgressText = true;
	var $progressTextPlace = 1;
	var $callback_code = "";
	var $callback_timeout = "";
	var $name = "";

	public function __construct($progress = 0, $showProgressText = true){
		$this->setProgress($progress);
		$this->showProgressText = $showProgressText;
	}

	/*
	public function getJS($pgFrame = '', $doReturn = false){
		if($doReturn){
			return $this->getJSCode($pgFrame);
		}
		echo $this->getJSCode($pgFrame);
		flush();
	}*/

	public function getJSCode($pgFrame = ''){
		$frame = $pgFrame ? $pgFrame . '.' : '';

		return we_html_element::jsElement('
function setProgressText' . $this->name . '(name,text){
	if(' . $frame . 'document.getElementById){
		var div = ' . $frame . 'document.getElementById(name);
		if(div){
			div.innerHTML = text;
		}
	}else if(' . $frame . 'document.all){
		var div = ' . $frame . 'document.all[name];
		if(div){
			div.innerHTML = text;
		}
	}
}

function setProgress' . $this->name . '(progress){
	var koef=' . ($this->stud_len / 100) . ';' .
				$frame . 'document.getElementById("progress_image' . $this->name . '").style.width=koef*progress+"px";' .
				$frame . 'document.getElementById("progress_image_bg' . $this->name . '").style.width=(koef*100)-(koef*progress)+"px";' .
				($this->showProgressText ?
					'setProgressText' . $this->name . '("progress_text' . $this->name . '",progress+"%");' :
					'') .
				($this->callback_code ?
					'if(progress<100) to=setTimeout(function(){' . $this->callback_code . '},' . $this->callback_timeout . ');
							else var to=clearTimeout(to);
					' : '') . '
}' .
				($this->callback_code ?
					'var to=setTimeout(function(){' . $this->callback_code . '},' . $this->callback_timeout . ');' :
					'')
		);
	}

	public function addText($text = "", $place = 0, $id = "", $class = "small", $color = "#006699", $height = 10, $bold = 1){
		$this->texts[] = array("name" => $id, "text" => $text, "class" => $class, "color" => $color, "bold" => $bold, "italic" => 0, "place" => $place, "height" => $height);
	}

	private function setProgress($progress = 0){
		$this->progress = min(100, $progress);
	}

	public function setName($name){
		$this->name = $name;
	}

	public function setCallback($code, $timeout){
		t_e('callback for pb set');
		$this->callback_code = $code;
		$this->callback_timeout = $timeout;
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

	public function setProgressLen($len = 100){
		$this->stud_len = $len;
	}

	function setProgressTextVisible($visible = true){
		$this->showProgressText = $visible;
	}

	function emptyTexts(){
		$this->texts = [];
	}

	public function getHTML($class = '', $style = ''){
		$left = $right = $top = $bottom = '';

		if($this->showProgressText){
			$this->addText('<div id="progress_text' . $this->name . '">' . $this->progress . "%</div>", $this->progressTextPlace);
		}

		foreach($this->texts as $text){
			switch($text["place"]){
				case 0:
					$top.='<td ' . ($text["name"] ? 'id="' . $text["name"] . $this->name . '" ' : "") . 'class="' . $text["class"] . ($text["bold"] ? " bold" : "" ) . '" style="line-height:12px;color:' . $text["color"] . ';margin-right:5px;">' . $text["text"] . '</td>';
					break;
				case 1:
					$right.='<td ' . ($text["name"] ? 'id="' . $text["name"] . $this->name . '" ' : "") . 'class="' . $text["class"] . $text["class"] . ($text["bold"] ? " bold" : "" ) . '" style="line-height:12px;color:' . $text["color"] . ';padding-left:5px;">' . $text["text"] . '</td>';
					break;
				case 2:
					$bottom.='<td ' . ($text["name"] ? 'id="' . $text["name"] . $this->name . '" ' : "") . 'class="' . $text["class"] . $text["class"] . ($text["bold"] ? " bold" : "" ) . '" style="line-height:12px;color:' . $text["color"] . ';margin-right:5px;">' . $text["text"] . '</td>';
					break;
				case 3:
					$left.='<td ' . ($text["name"] ? 'id="' . $text["name"] . $this->name . '" ' : "") . 'class="' . $text["class"] . $text["class"] . ($text["bold"] ? " bold" : "" ) . '" style="line-height:12px;color:' . $text["color"] . ';margin-right:5px;">' . $text["text"] . '</td>';
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
			'</tr></table>' .
			($bottom ?
				'<table class="default"><tr>' . $bottom . '</tr></table>' :
				''
			);
	}

}
