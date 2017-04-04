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
abstract class we_html_multiIconBox{
	const SPACE_SMALL = 'small';
	const SPACE_ICON = 'icon';
	const SPACE_MED = 'med';
	const SPACE_MED2 = 'med2';
	const SPACE_BIG = 'big';
	const PROP_PATH = '<i class="fa fa-folder"></i>';
	const PROP_DOC = '<i class="fa fa-file-text"></i>';
	const PROP_CATEGORIES = '<i class="fa fa-tags"></i>';
	const PROP_USER = '<i class="fa fa-user"></i>';
	const PROP_CONTENT = '<i class="fa fa-inbox"></i>';
	const PROP_OTHER = '<i class="fa fa-magic"></i>';
	const PROP_LANG = '<i class="fa fa-language"></i>';
	const PROP_COPY = '<i class="fa fa-copy"></i>';
	const PROP_NAVI = '<i class="fa fa-compass"></i>';
	const PROP_CHARSET = '<i class="fa fa-paragraph"></i>';
	const PROP_LINK = '<i class="fa fa-link"></i>';
	const PROP_MASTER = '<i class="fa fa-sitemap"></i>';
	const PROP_META = '<i class="fa fa-plus-circle"></i>';
	const PROP_UPLOAD = '<i class="fa fa-upload"></i>';
	const PROP_ATTRIB = '<i class="fa fa-cog"></i>';
	const PROP_WORKSPACE = '<i class="fa fa-gavel"></i>';
	const PROP_DISPLAY = '<i class="fa fa-eye"></i>';
	const PROP_CLASS = '<i class="fa fa-code"></i>';
	const PROP_KEYBOARD = '<i class="fa fa-keyboard-o"></i>';
	const PROP_WINDOW = '<i class="fa fa-window-maximize"></i>';
	const PROP_REVREL = '<i class="fa fa-exchange"></i>';
	const PROP_IMAGE = '<i class="fa fa-image"></i>';
	const PROP_SORT = '<i class="fa fa-sort"></i>';
	const PROP_NEWSLETTER = '<i class="fa fa-newspaper-o"></i>';
	const PROP_MAIL= '<i class="fa fa-envelope"></i>';
	//INFO
	const INFO_CALENDAR = '<i class="fa fa-calendar"></i>';
	const INFO_WORKFLOW = '<i class="fa fa-gears"></i>';
	const INFO_REFERENCES = '<i class="fa fa-shield"></i>';
	//CUSTOMER
	const CUSTOMER_FILTER = '<i class="fa fa-filter"></i>';

	/**
	 * @desc 	Get HTML-Code of the multibox
	 * @param	$name				string
	 * @param	$width				int
	 * @param	$content			array
	 * @param	$buttons			string
	 * @param	$foldAtNr			int
	 * @param	$foldRight			unknown
	 * @param	$foldDown			unknown
	 * @param	$displayAtStartup	bool
	 * @param	$headline			string
	 * @return	string
	 */
	static function getHTML($name, array $content, $marginLeft = 0, $buttons = '', $foldAtNr = -1, $foldRight = '', $foldDown = '', $displayAtStartup = false, $headline = '', $delegate = ''){
		$uniqname = $name ?: md5(uniqid(__FILE__, true));

		$out = $headline ?
			self::_getBoxStartHeadline($name, $headline, $uniqname, $marginLeft) :
			self::_getBoxStart($uniqname, $name);

		foreach($content as $i => $c){
			if($c === null){
				continue;
			}
			$forceRightHeadline = (!empty($c['forceRightHeadline']));
			$icon = (empty($c['icon']) ? '' : $c['icon']);
			$headline = (empty($c['headline']) ? '' : '<div id="headline_' . $uniqname . '_' . $i . '" class="weMultiIconBoxHeadline">' . $c["headline"] . '</div>' );
			$leftWidth = (empty($c['space']) ? '' : $c["space"] );
			$leftContent = $icon ?: (($leftWidth && (!$forceRightHeadline)) ? $headline : '');

			$out .= (isset($c['class']) ? '<div class="' . $c['class'] . '">' : '') .
				($i == $foldAtNr && $foldAtNr < count($content) ? // only if the folded items contain stuff.
				we_html_element::htmlSpan(['class' => 'btn_direction_weMultibox_table' . ($marginLeft ? ' withMargin' : '')], self::_getButton($uniqname, "weToggleBox('" . $uniqname . "','" . addslashes($foldDown) . "','" . addslashes($foldRight) . "');" . ($delegate ?: "" ), ($displayAtStartup ? 'down' : 'right'), g_l('global', '[openCloseBox]')) .
					'<span class="toggleBox" id="text_' . $uniqname . '" onclick="weToggleBox(\'' . $uniqname . '\',\'' . addslashes($foldDown) . '\',\'' . addslashes($foldRight) . '\');' . ($delegate ?: "" ) . '">' . ($displayAtStartup ? $foldDown : $foldRight) . '</span>'
				) .
				'<br/><table id="table_' . $uniqname . '" class="default iconBoxTable" style="' . ($displayAtStartup ? '' : 'display:none') . '"><tr><td>' : '') .
				'<div class="weMultiIconBoxContent ' . ($i < (count($content) - 1) && (empty($c['noline'])) ? 'weMultiIconBoxLine' : '' ) . ($marginLeft ? ' withMargin' : '') . '" id="div_' . $uniqname . '_' . $i . '">' .
				($leftContent || $leftWidth ?
				'<div class="multiiconleft leftSpace-' . $leftWidth . '">' . ((!$leftContent) && $leftWidth ? "&nbsp;" : $leftContent) . '</div>' :
				'') .
				//right
				'<div class="multiIconRight">' . ($icon || !$leftContent || $forceRightHeadline ? $headline : '') . '<div>' . (!empty($c["html"]) ? $c["html"] : '') . '</div></div>' .
				'</div>' .
				(isset($c['class']) ? '</div>' : '');
		}

		if($foldAtNr >= 0 && $foldAtNr < count($content)){
			$out .= '</td></tr></table>';
		}

		$out .= self::_getBoxEnd();

		return ($buttons ?
			//ignore height, replace by bottom:
			'<div class="weMultiIconBoxWithFooter">' . $out . '</div>
				<div class="editfooter">' . $buttons . '</div>' :
			$out);
	}

	static function getDynJS($uniqname = '', $marginLeft = 0){
		return we_html_element::jsScript(JS_DIR . 'multiIconBox.js', '', ['id' => 'loadVarMultiIconBox', 'data-iconbox' => setDynamicVar([
					'name' => $uniqname,
					'margin' => $marginLeft,
		])]);
	}

	private static function _getBoxStartHeadline($name, $headline, $uniqname, $marginLeft = 0){
		return '<div class="default multiIcon defaultfont" style="overflow:auto" id="' . $name . '">
	<div class="weDialogHeadline' . ($marginLeft ? ' withMargin' : '') . '">' . $headline . '</div>
	<div id="td_' . $uniqname . '">';
	}

	static function _getBoxStart($uniqname, $name = ''){
		return '<div class="default multiIcon defaultfont" style="padding-bottom:2px;" id="' . $name . '">
		<div id="td_' . $uniqname . '">';
	}

	static function _getBoxEnd(){
		return '</div>
</div>';
	}

	static function _getButton($name, $cmd, $state = "right", $title = ""){
		return we_html_button::create_button('fa:btn_direction,fa-lg fa-caret-' . $state, "javascript:" . $cmd . ";", '', 0, 0, '', '', false, true, $name, false, $title);
	}

}
