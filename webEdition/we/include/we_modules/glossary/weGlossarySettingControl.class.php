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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
class weGlossarySettingControl{

	function processCommands(){

		$js = '';
		$html = '';

		if(isset($_REQUEST['cmd'])){

			switch($_REQUEST['cmd']){

				case "save_glossary_setting":
					if($this->saveSettings()){
						$html .= we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_glossary', '[preferences_saved]'), we_message_reporting::WE_MESSAGE_NOTICE));
					} else{
						$html .= we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_glossary', '[preferences_not_saved]'), we_message_reporting::WE_MESSAGE_ERROR));
					}
					break;
			}

			print we_html_tools::htmlTop();
			print we_html_element::jsElement($js);
			print "</head>
			<body>
				$html
			</body>
			</html>";
			exit;
		}
	}

	function processVariables(){

	}

	function saveSettings($default = false){

		if($default){
			$GlossaryAutomaticReplacement = 'false';
		} else{

			$GlossaryAutomaticReplacement = 'false';
			if(isset($_REQUEST['GlossaryAutomaticReplacement']) && $_REQUEST['GlossaryAutomaticReplacement'] == 1){
				$GlossaryAutomaticReplacement = 'true';
			}
		}

		$code = <<<EOF
<?php

\$GLOBALS['weGlossaryAutomaticReplacement'] = {$GlossaryAutomaticReplacement};

EOF;

		$configFile = $_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/we_modules/glossary/we_conf_glossary_settings.inc.php";
		$fh = fopen($configFile, "w+");
		if(!$fh){
			return false;
		}
		fputs($fh, $code);
		return fclose($fh);
	}

}
