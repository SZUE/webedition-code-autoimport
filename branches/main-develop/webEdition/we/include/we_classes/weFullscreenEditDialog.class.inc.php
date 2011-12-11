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
 * @package    webEdition_wysiwyg
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */


include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_classes/weDialog.class.inc.php");
include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_classes/we_wysiwyg.class.inc.php");


class weFullscreenEditDialog extends weDialog{

##################################################################################################

	var $JsOnly = true;
	var $ClassName = __CLASS__;
	var $changeableArgs = array("src");

##################################################################################################

	function weFullscreenEditDialog(){
		$this->weDialog();
		$this->dialogTitle = g_l('wysiwyg',"[fullscreen_editor]");
		$this->args["src"] = "";
	}

##################################################################################################

	function getDialogContentHTML(){
		$js = '<script  type="text/javascript">isFullScreen = true;</script>';
		$e = new we_wysiwyg("we_dialog_args[src]",$this->args["screenWidth"]-90,$this->args["screenHeight"]-200,'',$this->args["propString"],$this->args["bgcolor"],$this->args["editname"],$this->args["className"],"",$this->args["outsideWE"],$this->args["xml"],$this->args["removeFirstParagraph"],true,$this->args["baseHref"],$this->args["charset"],$this->args["cssClasses"],$this->args['language']);
		return we_wysiwyg::getHeaderHTML().$js.$e->getHTML();
	}


##################################################################################################

	function getBodyTagHTML(){
		return '<body class="weDialogBody" onUnload="doUnload()"">
';
	}

	function getJs() {
		$_BROWSER=new we_browserDetect();

		$js = we_htmlElement::jsScript(JS_DIR.'windows.js').'
			<script  type="text/javascript"><!--
				var isGecko = '.($_BROWSER->isGecko()?'true':'false') .';
				var textareaFocus = false;

				if (isGecko) {
					document.addEventListener("keyup",doKeyDown,true);
				} else {
					document.onkeydown = doKeyDown;
				}

				function doKeyDown(e) {
					var key;

					if (isGecko) {
						key = e.keyCode;
					} else {
						key = event.keyCode;
					}

					switch (key) {
						case 27:
							top.close();
							break;';

		$js .= '	}
				}

				function weDoOk() {';
					if ($this->pageNr == $this->numPages && $this->JsOnly) {
						$js .= '
							if (!textareaFocus) {
								' . $this->getOkJs() . '
							}';
					}
		$js .= '
				}
				';

		$js .= '
				function IsDigit(e) {
					var key;

					if (isGecko) {
						key = e.charCode;
					} else {
						key = event.keyCode;
					}

					return (((key >= 48) && (key <= 57)) || (key == 0) || (key == 13));
				}

				function openColorChooser(name,value) {
					var win = new jsWindow("colorDialog.php?we_dialog_args[type]=dialog&we_dialog_args[name]="+escape(name)+"&we_dialog_args[color]="+escape(value),"colordialog",-1,-1,400,380,true,false,true,false);
				}

				function IsDigitPercent(e) {
					var key;

					if (isGecko) {
						key = e.charCode;
					} else {
						key = event.keyCode;
					}

					return (((key >= 48) && (key <= 57)) || (key == 37) || (key == 0)  || (key == 13));
				}

				function doUnload() {
					if (jsWindow_count) {
						for (i = 0; i < jsWindow_count; i++) {
							eval("jsWindow" + i + "Object.close()");
						}
					}
				}

				self.focus();
			//-->
			</script>';

		return $js;
	}


##################################################################################################
}