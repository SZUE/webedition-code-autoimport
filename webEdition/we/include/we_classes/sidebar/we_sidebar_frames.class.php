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
abstract class we_sidebar_frames{

	public static function getHTML($what){
		switch($what){
			case 'content':
				echo self::getHTMLContent();
				break;

			default:
				echo self::getHTMLFrameset();
				break;
		}
	}

	private static function getHTMLFrameset(){
		?>
		<div id="weSidebarBody">
			<div id="weSidebarHeader">
				<div id="Headline"><?= g_l('sidebar', '[headline]'); ?></div>
				<div id="CloseButton">
					<span class="close" onclick="WE().layout.sidebar.close();">
						<i class="fa fa-close fa-lg "></i>
					</span>

				</div>
			</div>
			<div id="weSidebarContentDiv">
				<iframe id="weSidebarContent" src="<?= WEBEDITION_DIR; ?>we_cmd.php?we_cmd[0]=loadSidebarDocument" name="weSidebarContent"></iframe>
			</div>
		</div>
		<?php
	}

	private static function showSidebarText(array $textArray){
		$ret = '';
		unset($textArray[2]); // #6261: do not show entry [2]
		foreach($textArray as $text){
			$link = "%s";
			if(!empty($text['link'])){

				if(stripos($text['link'], 'javascript:') === 0){
					$text['link'] = str_replace("\"", "'", $text['link']); #6625
					$text['link'] = str_replace("`", "'", $text['link']); #6625
					$link = '<a href="' . $text['link'] . '">%s</a>';
				} else {
					$link = '<a href="' . $text['link'] . '" target="_blank">%s</a>';
				}
			}

			$headline = (empty($text['headline']) ? '' : sprintf($link, $text['headline']));
			$ret .= '<tr>
				<td class="defaultfont" style="vertical-align:top;padding-top:5px;" colspan="2"><strong>' . $headline . '</strong><br /><br/>' . $text['text'] . '</td>
			</tr>
			<tr>';
		}
		return $ret;
	}

	private static function getDefaultContent(){
		echo we_html_tools::getHtmlTop('sideBar', '', '', '', we_html_element::htmlBody(['class' => "weSidebarBody"], '<table>' .
				self::showSidebarText(g_l('sidebar', '[default]')) .
				(we_base_permission::hasPerm('ADMINISTRATOR') ?
					self::showSidebarText(g_l('sidebar', '[admin]')) :
					'') .
				'</table>'
		));
	}

	public static function getHTMLContent(){
		$file = we_base_request::_(we_base_request::URL, 'we_cmd', '', 1);
		$params = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 2);
		define('WE_SIDEBAR', true);

		if(stripos($file, "http://") === 0 || stripos($file, "https://") === 0){
			//not implemented
			//header("Location: " . $file);
			exit();
		}

		if(strpos($file, '/') !== 0){
			$file = id_to_path($file, FILE_TABLE, $GLOBALS['DB_WE'], false, false, true);
		}

		if(!file_exists($_SERVER['DOCUMENT_ROOT'] . $file) || !is_file($_SERVER['DOCUMENT_ROOT'] . $file)){
			$file = id_to_path(intval(SIDEBAR_DEFAULT_DOCUMENT), FILE_TABLE, $GLOBALS['DB_WE'], false, false, true);
			if(!$file || substr($file, -1) === '/' || $file === 'default'){
				$file = '';
			}
		}

		if($file){
			//manipulate GET/REQUEST for document
			$_GET = [];
			parse_str($params, $_GET);
			$_REQUEST = $_GET;
			ob_start();
			include($_SERVER['DOCUMENT_ROOT'] . $file);

			$cnt = 0;
			$SrcCode = str_replace('<head>', '<head><script src="/webEdition/js/global.js"></script>', ob_get_clean(), $cnt);
		} else {
			$SrcCode = self::getDefaultContent();
		}

		echo we_SEEM::parseDocument(($cnt ? $SrcCode : '<script src="/webEdition/js/global.js"></script>' . $SrcCode));

		exit();
	}

}
