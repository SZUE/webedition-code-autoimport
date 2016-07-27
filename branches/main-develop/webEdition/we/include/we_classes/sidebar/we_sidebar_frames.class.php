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
class we_sidebar_frames{

	function getHTML($what){
		switch($what){

			case 'content':
				echo $this->getHTMLContent();
				break;

			default:
				echo $this->getHTMLFrameset();
				break;
		}
	}

	function getHTMLFrameset(){
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
				<iframe id="weSidebarContent" src="<?= WEBEDITION_DIR; ?>sideBarFrame.php?pnt=content" name="weSidebarContent"></iframe>
			</div>
		</div>
		<?php
	}

	function getHTMLContent(){
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
				$file = WEBEDITION_DIR . 'sidebar/default.php';
			}
		}

		//manipulate GET/REQUEST for document
		$_GET = [];
		parse_str($params, $_GET);
		$_REQUEST = $_GET;
		ob_start();
		include($_SERVER['DOCUMENT_ROOT'] . $file);

		$SrcCode = ob_get_clean();
		$cnt = 0;
		$SrcCode = str_replace('<head>', '<head><script src="/webEdition/js/global.js"></script>', $SrcCode, $cnt);
		if(!$cnt){
			$SrcCode = '<script src="/webEdition/js/global.js"></script>' . $SrcCode;
		}
		echo we_SEEM::parseDocument($SrcCode);

		exit();
	}

}
