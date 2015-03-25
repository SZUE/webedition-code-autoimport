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

	var $_frameset = '';

	public function __construct(){
		$this->_frameset = WEBEDITION_DIR . 'sideBarFrame.php';
	}

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
		<style type="text/css">
			#Headline {
				padding-left	: 5px;
				line-height		: 20px;
				vertical-align	: middle;
				float			: left;
				width			: 80%;
			}
			#CloseButton {
				padding-top		: 3px;
				padding-right	: 4px;
				float			: right;
			}
		</style>
		</head>
		<body style="background-color:#bfbfbf; background-repeat:repeat;margin:0px 0px 0px 0px;overflow:hidden;">
			<div id="weSidebarHeader" name="weSidebarHeader" style="overflow: hidden;position:absolute;top:0px;left:0px;right:0px;height:22px;background-color: silver;	background-image: url('<?php echo IMAGE_DIR; ?>backgrounds/multitabBG.gif');font-family: Verdana, Arial, sans-serif;font-size: 10px;">
				<div id="Headline">
					<?php echo g_l('sidebar', '[headline]'); ?>
				</div>
				<div id="CloseButton">
					<img src="<?php echo IMAGE_DIR; ?>multiTabs/close.gif" border="0" vspace="0" hspace="0" onclick="top.weSidebar.close();" onmouseover="this.src = '<?php echo IMAGE_DIR; ?>multiTabs/closeOver.gif'" onmouseout="this.src = '<?php echo IMAGE_DIR; ?>multiTabs/close.gif'" />
				</div>
			</div>
			<div style="position:absolute;top:22px;left:0px;right:0px;bottom:40px;border-bottom: 1px solid black;border-top: 1px solid black;">
				<iframe src="<?php echo $this->_frameset; ?>?pnt=content" style="border: 0px;background-color:white;width:100%;height:100%;overflow: auto;" name="weSidebarContent"></iframe>
			</div>
			<div name="weSidebarFooter" id="weSidebarFooter" style="overflow: hidden;position:absolute;bottom:0px;left:0px;right:0px;height:40px;background-color:#f0f0f0;background-image: url('<?php echo IMAGE_DIR; ?>edit/editfooterback.gif'); ">
			</div>
		</body>

		</html>
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
			$file = id_to_path($file, FILE_TABLE, $GLOBALS['DB_WE'], false, false, false, true);
		}

		if(!file_exists($_SERVER['DOCUMENT_ROOT'] . $file) || !is_file($_SERVER['DOCUMENT_ROOT'] . $file)){
			$file = id_to_path(intval(SIDEBAR_DEFAULT_DOCUMENT), FILE_TABLE, $GLOBALS['DB_WE'], false, false, false, true);
			if(!$file || substr($file, -1) === '/' || $file === 'default'){
				$file = WEBEDITION_DIR . 'sidebar/default.php';
			}
		}

		//manipulate GET/REQUEST for document
		$_GET = array();
		parse_str($params, $_GET);
		$_REQUEST = $_GET;
		ob_start();
		include($_SERVER['DOCUMENT_ROOT'] . $file);

		$SrcCode = ob_get_clean();

		echo we_SEEM::parseDocument($SrcCode);

		exit();
	}

}
