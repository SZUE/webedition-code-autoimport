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
class we_thumbnail_frames extends we_modules_frame{
	const TAB_PROPERTIES = 1;

	public function __construct($frameset){
		parent::__construct($frameset);
		$this->module = 'thumbnail';
		$this->Tree = new we_thumbnail_tree($this->jsCmd);
		$this->View = new we_thumbnail_view();
	}

	function getHTML($what = '', $mode = '', $step = 0){
		switch($what){
			case 'frameset':
				return $this->getHTMLFrameset($this->Tree->getJSTreeCode());
			case "edfooter":
				return $this->getHTMLEditorFooter([
						we_html_button::SAVE => [['ADMINISTRATOR'], 'we_save'],
						we_html_button::DELETE => [['ADMINISTRATOR'], ['confirmDeleteThumb', $this->View->Model->Name]]
				]);

			default:
				return parent::getHTML($what, $mode, $step);
		}
	}

	protected function getHTMLEditorHeader($mode = 0){
		if(we_base_request::_(we_base_request::BOOL, "home")){
			return parent::getHTMLEditorHeader(0);
		}

		$we_tabs = new we_gui_tabs();

		$we_tabs->addTab('', we_base_constants::WE_ICON_PROPERTIES, false, self::TAB_PROPERTIES, ["id" => "tab_" . self::TAB_PROPERTIES, 'title' => g_l('modules_voting', '[property]')]);

		$this->jsCmd->addCmd('setTab', self::TAB_PROPERTIES);
		$tabsHead = we_html_element::cssLink(CSS_DIR . 'we_tab.css') .
			we_html_element::jsScript(JS_DIR . 'initTabs.js') .
			we_html_element::jsScript(WE_JS_MODULES_DIR . '/thumbnail/thumbnail_top.js');


		$body = we_html_element::htmlBody([
				"onresize" => "weTabs.setFrameSize()",
				"onload" => "weTabs.setFrameSize();document.getElementById('tab_" . self::TAB_PROPERTIES . "').className='tabActive';",
				"id" => "eHeaderBody"
				], '<div id="main"><div id="headrow"><b>' . g_l('thumbnails', '[thumbnails]') . ':&nbsp;</b><span id="h_path" class="header_small"><b id="titlePath">' . str_replace(" ", "&nbsp;", $this->View->Model->Name) . '</b></span></div>' .
				$we_tabs->getHTML() .
				'</div>'
		);

		return $this->getHTMLDocument($body, $tabsHead);
	}

	protected function getHTMLEditorBody(){
		if(we_base_request::_(we_base_request::BOOL, "home")){
			return $this->View->getHomeScreen();
		}

		return $this->getHTMLDocument(we_html_element::htmlBody(['class' => 'weEditorBody', "onload" => "loaded=1;", "onunload" => "doUnload()"], we_html_element::htmlForm([
						'name' => 'we_form', "onsubmit" => "return false"], $this->View->getCommonHiddens(['cmd' => 'thumbnail_edit', 'pnt' => 'edbody']) . $this->getHTMLProperties($this->jsCmd))), $this->View->getJSProperty() . $this->jsCmd->getCmds());
	}

	function getHTMLTab1(we_base_jsCmd $jsCmd){

		$thumbnail_name_input = we_html_tools::htmlTextInput('thumbnail_name', 22, $this->View->Model->Name, 255, '', 'text', 340);
		$thumbnail_description_input = we_html_tools::htmlTextInput('description', 22, $this->View->Model->description , 255, '', 'text', 340);
		/*		 * ***************************************************************
		 * PROPERTIES
		 * *************************************************************** */

		// Create specify thumbnail dimension input
		$thumbnail_quality = $this->View->Model->Quality ;
		$thumbnail_specify_table = new we_html_table(['class' => 'default inputs'], 3, 2);

		$thumbnail_specify_table->setCol(0, 0, ['class' => 'defaultfont', 'style' => 'padding-right:10px;'], g_l('thumbnails', '[width]') . ':');
		$thumbnail_specify_table->setCol(0, 1, null, we_html_tools::htmlTextInput('thumbnail_width', 6, $this->View->Model->Width , 4, '', 'number'));
		$thumbnail_specify_table->setCol(1, 0, ['class' => 'defaultfont'], g_l('thumbnails', '[height]') . ':');
		$thumbnail_specify_table->setCol(1, 1, null, we_html_tools::htmlTextInput('thumbnail_height', 6, $this->View->Model->Height , 4, '', 'number'));
		$thumbnail_specify_table->setCol(2, 0, ['class' => 'defaultfont', 'id' => 'thumbnail_quality_text_cell'], g_l('thumbnails', '[quality]') . ':');
		$thumbnail_specify_table->setCol(2, 1, ['class' => 'defaultfont', 'id' => 'thumbnail_quality_value_cell'], we_base_imageEdit::qualitySelect('thumbnail_quality', $thumbnail_quality));

		// Create checkboxes for options for thumbnails
		$opt = explode(',', $this->View->Model->Options);

		$options = [
			'opts' => [2, 1, [
					we_thumbnail::OPTION_MAXSIZE => [($this->View->Model->ID? intval(in_array(we_thumbnail::OPTION_MAXSIZE, $opt)) : -1), g_l('thumbnails', '[maximize]'), g_l('thumbnails', '[maximize_desc]')],
					we_thumbnail::OPTION_INTERLACE => [($this->View->Model->ID?intval(in_array(we_thumbnail::OPTION_INTERLACE, $opt)) : -1), g_l('thumbnails', '[interlace]'), g_l('thumbnails', '[interlace_desc]')],
				]
			],
			'cutting' => [4, 1, [
					we_thumbnail::OPTION_DEFAULT => [($this->View->Model->ID>0), g_l('thumbnails', '[cutting_none]'), g_l('thumbnails', '[cutting_none_desc]')],
					we_thumbnail::OPTION_RATIO => [($this->View->Model->ID ? in_array(we_thumbnail::OPTION_RATIO, $opt) : false), g_l('thumbnails', '[ratio]'), g_l('thumbnails', '[ratio_desc]')],
					we_thumbnail::OPTION_FITINSIDE => [($this->View->Model->ID ? in_array(we_thumbnail::OPTION_FITINSIDE, $opt) : false), g_l('thumbnails', '[fitinside]'), g_l('thumbnails', '[fitinside_desc]')],
					we_thumbnail::OPTION_CROP => [($this->View->Model->ID? in_array(we_thumbnail::OPTION_CROP, $opt) : false), g_l('thumbnails', '[crop]'), g_l('thumbnails', '[crop_desc]')],
				]
			],
			'filter' => [2, 3, [
					we_thumbnail::OPTION_UNSHARP => [($this->View->Model->ID? in_array(we_thumbnail::OPTION_UNSHARP, $opt) : false), g_l('thumbnails', '[unsharp]'), g_l('thumbnails', '[unsharp_desc]')],
					we_thumbnail::OPTION_GAUSSBLUR => [($this->View->Model->ID? in_array(we_thumbnail::OPTION_GAUSSBLUR, $opt) : false), g_l('thumbnails', '[gauss]'), g_l('thumbnails', '[gauss_desc]')],
					we_thumbnail::OPTION_GRAY => [($this->View->Model->ID? in_array(we_thumbnail::OPTION_GRAY, $opt) : false), g_l('thumbnails', '[gray]')],
					we_thumbnail::OPTION_NEGATE => [($this->View->Model->ID ? in_array(we_thumbnail::OPTION_NEGATE, $opt) : false), g_l('thumbnails', '[negate]'), g_l('thumbnails', '[negate_desc]')],
					we_thumbnail::OPTION_SEPIA => [($this->View->Model->ID ? in_array(we_thumbnail::OPTION_SEPIA, $opt) : false), g_l('thumbnails', '[sepia]')],
				]
			]
		];
		foreach($options as $k => $v){
			if(isset($v[2][we_thumbnail::OPTION_DEFAULT])){
				$v[2][we_thumbnail::OPTION_DEFAULT][0] = ($this->View->Model->ID==0) || ((count(array_intersect(array_keys($v[2]), $opt))) === 0);
			}

			$thumbnail_option_table[$k] = new we_html_table(['class' => 'editorThumbnailsOptions'], $v[0], $v[1]);
			$i = 0;
			foreach($v[2] as $key => $val){
				switch($k){
					case 'opts':
					case 'filter':
						$thumbnail_option_table[$k]->setCol(($i % $v[0]), intval($i++ / $v[0]), null, we_html_forms::checkbox($key, (intval($val[0]) > 0), 'Options[' . $key . ']', $val[1], false, 'defaultfont', '', (intval($val[0]) === -1), '', we_html_tools::TYPE_NONE, 0, '', '', (isset($val[2]) ? $val[2] : '')));
						break;
					default:
						$thumbnail_option_table[$k]->setCol(($i % $v[0]), intval($i++ / $v[0]), null, we_html_forms::radiobutton($key, $val[0], 'Options[' . $k . ']', $val[1], true, "defaultfont", '', false, '', we_html_tools::TYPE_NONE, 0, '', '', (isset($val[2]) ? $val[2] : '')));
				}
			}
		}

		$window_html = new we_html_table(['class' => 'editorThumbnailsOptions'], 1, 4);
		$window_html->setCol(0, 0, null, $thumbnail_specify_table->getHtml());
		$window_html->setCol(0, 1, null, we_html_element::htmlDiv([], g_l('thumbnails', '[output_options]') . ':') . we_html_element::htmlDiv([], $thumbnail_option_table['opts']->getHtml()));
		$window_html->setCol(0, 2, null, we_html_element::htmlDiv([], g_l('thumbnails', '[cutting]') . ':') . we_html_element::htmlDiv([], $thumbnail_option_table['cutting']->getHtml()));

		// OUTPUT FORMAT

		$thumbnail_format = $this->View->Model->Format;

		// Define available formats
		$thumbnails_formats = ['none' => g_l('thumbnails', '[format_original]'), we_thumbnail::FORMAT_GIF => g_l('thumbnails', '[format_gif]'), we_thumbnail::FORMAT_JPG => g_l('thumbnails', '[format_jpg]'),
			we_thumbnail::FORMAT_PNG => g_l('thumbnails', '[format_png]')];
		$thumbnail_format_select_attribs = ['name' => 'Format', 'id' => 'Format', 'class' => 'weSelect', 'style' => 'width: 225px;', 'onchange' => 'changeFormat()'];

		$thumbnail_format_select = new we_html_select($thumbnail_format_select_attribs);

		foreach($thumbnails_formats as $k => $v){
			if(in_array($k, we_base_imageEdit::supported_image_types()) || $k === 'none'){
				$thumbnail_format_select->addOption($k, $v);

				// Check if added option is selected
				if($thumbnail_format == $k || (!$thumbnail_format && ($k === 'none'))){
					$thumbnail_format_select->selectOption($k);
				}
			}
		}

		// Build dialog
		return [
			['headline' => g_l('thumbnails', '[name]'),
				'html' => $thumbnail_name_input,
				'space' => we_html_multiIconBox::SPACE_MED
			],
			['headline' => g_l('thumbnails', '[description]'),
				'html' => $thumbnail_description_input,
				'space' => we_html_multiIconBox::SPACE_MED
			],
			['headline' => g_l('thumbnails', '[properties]'),
				'html' => $window_html->getHtml(),
				'space' => we_html_multiIconBox::SPACE_SMALL
			],
			['headline' => 'Filter',
				'html' => we_html_element::htmlDiv(['class' => 'editorThumbnailsFilter'], $thumbnail_option_table['filter']->getHtml())
			],
			['headline' => g_l('thumbnails', '[format]'),
				'html' => $thumbnail_format_select->getHtml(),
				'space' => we_html_multiIconBox::SPACE_MED
			],
		];
	}

	function getHTMLProperties(we_base_jsCmd $jsCmd){// TODO: move to View
		//$t = we_base_request::_(we_base_request::INT, 'tabnr', 1);
		return we_html_element::jsScript(JS_DIR . 'utils/multi_editMulti.js') .
			we_html_element::htmlDiv(['id' => 'tab1'], we_html_multiIconBox::getHTML('', $this->getHTMLTab1($jsCmd), 0, '', -1, '', '', false));
	}

	protected function getHTMLCmd(){
		if(($pid = we_base_request::_(we_base_request::INT, "pid")) === false){
			return $this->getHTMLDocument(we_html_element::htmlBody(), $this->jsCmd->getCmds());
		}

		$offset = we_base_request::_(we_base_request::INT, "offset", 0);
		$this->jsCmd->addCmd('loadTree', ['clear' => !$pid, 'items' => we_thumbnail_tree::getItems($pid, $offset, $this->Tree->default_segment)]);

		return $this->getHTMLDocument(
				we_html_element::htmlBody([], we_html_element::htmlForm(
						['name' => 'we_form'], we_html_element::htmlHiddens([
							"pnt" => "cmd",
							"cmd" => "no_cmd"]
						)
					)
		));
	}

}
