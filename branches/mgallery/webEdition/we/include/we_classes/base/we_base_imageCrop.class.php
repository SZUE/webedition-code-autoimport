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

/**
 * Class we_image_crop
 *
 * Provides image cropping functions.
 */
abstract class we_base_imageCrop{

	static function getJS(){
		if(!$GLOBALS['we_doc']->issetElement("origwidth")){
			$GLOBALS['we_doc']->setElement("origwidth", $GLOBALS['we_doc']->getElement("width"), 'attrib');
		}
		if(!$GLOBALS['we_doc']->issetElement("origheight")){
			$GLOBALS['we_doc']->setElement("origheight", $GLOBALS['we_doc']->getElement("height"), 'attrib');
		}

		return we_html_element::jsElement('
var size={
	"origW" : ' . $GLOBALS['we_doc']->getElement("origwidth", "dat", 'document.getElementById("weImage") ? document.getElementById("weImage").width : 0') . ',
	"origH" : ' . $GLOBALS['we_doc']->getElement("origheight", "dat", 'document.getElementById("weImage") ? document.getElementById("weImage").height : 0') . '
};') .
			we_html_element::jsScript(JS_DIR . 'imageCrop.js');
	}

	static function getCSS(){
		return we_html_element::cssLink(CSS_DIR . 'imageCrop.css');
	}

	static function getCrop($attribs){
		$cancelbut = we_html_button::create_button(we_html_button::CANCEL, "javascript:we_cmd('crop_cancel')");
		$okbut = we_html_button::create_button(we_html_button::SAVE, "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('doImage_crop',document.we_form.cropCoordX.value,document.we_form.cropCoordY.value,document.we_form.CropWidth.value,document.we_form.CropHeight.value);", true, 0, 0, "", "", true, false);

		return we_html_element::htmlHidden("cropCoordX", "", "cropCoordX") .
			we_html_element::htmlHidden("cropCoordY", "", "cropCoordY") . '
<table cellpadding="0" cellspacing="5" border="0">
  <tr>
    <td>
      <div id="weImgDiv">
        <div id="weImagePanelBorder"><div id="weImagePanel">
          <img id="weImage" src="' . $attribs["src"] . '"' . (isset($attribs["width"]) ? ' width="' . $attribs["width"] . '"' : '' ) . (isset($attribs["height"]) ? ' height="' . $attribs["height"] . '"' : '') . (isset($attribs["alt"]) ? ' alt="' . $attribs["alt"] . '"' : '') . ' /></div>
        </div>
      </div>
      <div id="weControl" style="display:none;height:24px;background:#CECECE;border-top:solid 1px #fff;padding:3px;">
        <table border="0" width="100%" cellpadding="0" cellspacing="0">
          <tr>
        	  <td style="width:100px;padding-top:4px;">
        	  	<div id="console" style="display:none;">
        		  <div id="weSizeDiv">
         	 		  <input type="text" name="CropWidth" id="CropWidth" value="0" onchange="CropTool.setCropWidth(this.value);" onkeydown="return CropTool.catchKeystroke(event,this);" />
								<input type="text" name="CropHeight" id="CropHeight" value="0" onchange="CropTool.setCropHeight(this.value);" onkeydown="return CropTool.catchKeystroke(event,this);" />
              </div>
              <a id="cropButtonZoomIn" title="' . g_l('crop', '[enlarge_crop_area]') . '" onmousedown="CropTool.zoom(1);"><i class="fa fa-expand"></i></a>
              <a id="cropButtonZoomOut" title="' . g_l('crop', '[reduce_crop_area]') . '" onmousedown="CropTool.zoom(-1);"><i class="fa fa-compress"></i></a>
              </div>
            </td>
            <td>' . we_html_button::position_yes_no_cancel($okbut, "", $cancelbut, 10, "", "", 2) . '</td>
          </tr>
        </table>
      </div>
   	</td>
  </tr>
</table>
';
	}

}
