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
define("PROGRESS_H_IMAGE", IMAGE_DIR . 'balken.gif');
define("PROGRESS_H_IMAGE_BG", IMAGE_DIR . 'balken_bg.gif');

define("PROGRESS_V_IMAGE", IMAGE_DIR . 'balken_v.gif');
define("PROGRESS_V_IMAGE_BG", IMAGE_DIR . 'balken_bg_v.gif');

class we_progressBar{
	var $progress = 0;
	var $texts = array();
	var $orientation = 0;
	var $progress_image = PROGRESS_H_IMAGE;
	var $progress_image_bg = PROGRESS_H_IMAGE_BG;
	var $stud_width = 10;
	var $stud_len = 100;
	var $showProgressText = true;
	var $progressTextPlace = 1;
	var $showBack = true;
	var $callback_code = "";
	var $callback_timeout = "";
	var $name = "";

	public function __construct($progress = 0, $orientation = 0, $showProgressText = true){
		$this->setProgress($progress);
		$this->setOrientation($orientation);
		$this->showProgressText = $showProgressText;
	}

	public function getJS($pgFrame = '', $doReturn = false){
		if($doReturn){
			return $this->getJSCode($pgFrame);
		}
		echo $this->getJSCode($pgFrame);
		flush();
	}

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
				$frame . 'document.images["progress_image' . $this->name . '"].' . ($this->orientation ? 'height' : 'width') . '=koef*progress;' .
				($this->showBack ?
					$frame . 'document.images["progress_image_bg' . $this->name . '"].' . ($this->orientation ? 'height' : 'width') . '=(koef*100)-(koef*progress);' :
					''
				) .
				($this->showProgressText ?
					'setProgressText' . $this->name . '("progress_text' . $this->name . '",progress+"%");' :
					'') .
				($this->callback_code ?
					'if(progress<100) to=setTimeout(\'' . $this->callback_code . '\',' . $this->callback_timeout . ');
							else var to=clearTimeout(to);
					' : '') . '
}' .
				($this->callback_code ?
					'var to=setTimeout(\'' . $this->callback_code . '\',' . $this->callback_timeout . ');' :
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

	private function setOrientation($ort = 0){
		$this->orientation = $ort;
		$this->setProgresImages(($ort ? PROGRESS_V_IMAGE : PROGRESS_H_IMAGE), ($ort ? PROGRESS_V_IMAGE_BG : PROGRESS_H_IMAGE_BG));
	}

	private function setProgresImages($image = "", $image_bg = ""){
		if($image){
			$this->progress_image = $image;
		}
		if($image_bg){
			$this->progress_image_bg = $image_bg;
		}
	}

	public function setCallback($code, $timeout){
		$this->callback_code = $code;
		$this->callback_timeout = $code;

		$this->callback = 'var to=setTimeout("' . $code . '",' . $timeout . ');';
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

	function setBackVisible($visible = true){
		$this->showBack = $visible;
	}

	function setProgressTextVisible($visible = true){
		$this->showProgressText = $visible;
	}

	function emptyTexts(){
		$this->texts = array();
	}

	public function getHTML(){
		$left = $right = $top = $bottom = '';

		if($this->showProgressText){
			$this->addText('<div id="progress_text' . $this->name . '">' . $this->progress . "%</div>", $this->progressTextPlace);
		}

		foreach($this->texts as $text){
			switch($text["place"]){
				case 0:
					$top.='<td ' . ($text["name"] ? 'id="' . $text["name"] . $this->name . '" ' : "") . 'class="' . $text["class"] . '" style="color:' . $text["color"] . ';' . ($text["bold"] ? "font-weight:bold" : "" ) . '">' . $text["text"] . '</td>' .
						'<td>' . we_html_tools::getPixel(5, $text["height"]) . '</td>';
					break;
				case 1:
					$right.='<td ' . ($text["name"] ? 'id="' . $text["name"] . $this->name . '" ' : "") . 'class="' . $text["class"] . '" style="color:' . $text["color"] . ';' . ($text["bold"] ? "font-weight:bold" : "" ) . '">' . $text["text"] . '</td>';
					break;
				case 2:
					$bottom.='<td ' . ($text["name"] ? 'id="' . $text["name"] . $this->name . '" ' : "") . 'class="' . $text["class"] . '" style="color:' . $text["color"] . ';' . ($text["bold"] ? "font-weight:bold" : "" ) . '">' . $text["text"] . '</td>' .
						'<td>' . we_html_tools::getPixel(5, $text["height"]) . '</td>';
					break;
				case 3:
					$left.='<td ' . ($text["name"] ? 'id="' . $text["name"] . $this->name . '" ' : "") . 'class="' . $text["class"] . '" style="color:' . $text["color"] . ';' . ($text["bold"] ? "font-weight:bold" : "" ) . '">' . $text["text"] . '</td>';
					break;
			}
		}


		$progress_len = ($this->stud_len / 100) * $this->progress;
		$rest_len = $this->stud_len - $progress_len;

		return
			($top ?
				'<table style="border-spacing: 0px;border-style:none;" cellpadding="0"><tr>' . $top . '</tr></table>' :
				'') .
			'<table style="border-spacing: 0px;border-style:none;" cellpadding="0" >
			<tr>' . ($left ? $left . '<td>' . we_html_tools::getPixel(5, 1) . '</td>' : '') .
			($this->orientation ?
				'<td><table border="0" cellpadding="0" cellspacing="0">' . ($this->showBack ? '<tr><td><img name="progress_image_bg" src="' . $this->progress_image_bg . '" height="' . $rest_len . '" width="' . $this->stud_width . '" /></td></tr>' : "") . '<tr><td><img  name="progress_image" src="' . $this->progress_image . '" height="' . $progress_len . '" width="' . $this->stud_width . '" /></td></tr></table></td>' :
				'<td><img name="progress_image' . $this->name . '" src="' . $this->progress_image . '" width="' . $progress_len . '" height="' . $this->stud_width . '" /></td>' .
				($this->showBack ?
					'<td><img  name="progress_image_bg' . $this->name . '" src="' . $this->progress_image_bg . '" width="' . $rest_len . '" height="' . $this->stud_width . '" /></td>' :
					""
				)
			) .
			($right ?
				"<td>" . we_html_tools::getPixel(5, 1) . "</td>" . $right :
				""
			) .
			'</tr></table>' .
			($bottom ?
				'<table style="border-spacing: 0px;border-style:none;" cellpadding="0"><tr>' . $bottom . '</tr></table>' :
				''
			);
	}

}
