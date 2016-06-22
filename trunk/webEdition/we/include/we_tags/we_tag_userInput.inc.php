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
function we_tag_userInput(array $attribs, $content){
	if(($foo = attributFehltError($attribs, 'name', __FUNCTION__))){
		return $foo;
	}

	$name = weTag_getAttribute('name', $attribs, '', we_base_request::STRING);
	$type = weTag_getAttribute('type', $attribs, '', we_base_request::STRING);
	$property = weTag_getAttribute('property', $attribs, false, we_base_request::BOOL);
	$format = weTag_getAttribute('format', $attribs, '', we_base_request::STRING);
	$checked = weTag_getAttribute('checked', $attribs, false, we_base_request::BOOL);
	$inlineedit = weTag_getAttribute('inlineedit', $attribs, true, we_base_request::BOOL);
	$value = weTag_getAttribute('value', $attribs, '', we_base_request::RAW);
	$editable = weTag_getAttribute('editable', $attribs, true, we_base_request::BOOL);
	$autobrAttr = weTag_getAttribute('autobr', $attribs, true, we_base_request::BOOL);
	$hidden = weTag_getAttribute('hidden', $attribs, false, we_base_request::BOOL);
	$size = weTag_getAttribute('size', $attribs, '', we_base_request::UNIT);
	$values = weTag_getAttribute('values', $attribs, '', we_base_request::RAW);
	$xml = weTag_getAttribute('xml', $attribs, XHTML_DEFAULT, we_base_request::BOOL);
	$removeFirstParagraph = weTag_getAttribute('removefirstparagraph', $attribs, defined('REMOVEFIRSTPARAGRAPH_DEFAULT') ? REMOVEFIRSTPARAGRAPH_DEFAULT : true, we_base_request::BOOL);

	if($hidden && ($type != 'date')){
		$type = 'hidden';
	}

	$fieldname = $property ?
		('we_ui_' . (isset($GLOBALS['WE_FORM']) ? $GLOBALS['WE_FORM'] : '') . '_' . $name) :
		('we_ui_' . (isset($GLOBALS['WE_FORM']) ?
			$GLOBALS['WE_FORM'] :
			''
		) . '[' . $name . ']');

	$object = (isset($GLOBALS['WE_FORM']) ?
			(isset($GLOBALS['we_object'][$GLOBALS['WE_FORM']]) ?
				$GLOBALS['we_object'][$GLOBALS['WE_FORM']] :
				(isset($GLOBALS['we_document'][$GLOBALS['WE_FORM']]) ?
					$GLOBALS['we_document'][$GLOBALS['WE_FORM']] :
					(isset($GLOBALS['we_doc']) ?
						$GLOBALS['we_doc'] :
						false))) :
			'');

	if($object){
		if($property){
			$isset = isset($object->{$name});
			$orgVal = $isset ? $object->{$name} : $value;
		} else {
			$isset = (!$object->ID && $object->getElement($name) === '' ?
					false :
					$object->issetElement($name));

			$orgVal = $isset ? $object->getElement($name) : $value;
		}
		$object_pid = $object->ParentID;
		$object_path = $object->Path;
		$object_tableID = $object instanceof we_objectFile ? $object->TableID : '';
		$content = $object->getFieldByVal($orgVal, $type, $attribs, true, $object_pid, $object_path, $GLOBALS['DB_WE'], $object_tableID);
	} else {
		$orgVal = $value;
		$object_pid = 0;
		$object_path = '';
		$object_tableID = '';
		$isset = false;
	}


	if(!$editable && !$hidden){
		switch($type){
			case 'img':
			case 'binary':
			case 'flashmovie':
			case 'quicktime':
				break;
			default:
				$hidden = getHtmlTag('input', array(
					'type' => 'hidden',
					'name' => $fieldname,
					'value' => oldHtmlspecialchars($orgVal),
					'xml' => $xml
				));
				return (($type != 'hidden') ? $content : '') . $hidden;
		}
	}
	switch($type){
		case 'img' :
			$imgDataId = we_base_request::_(we_base_request::HTML, 'WE_UI_IMG_DATA_ID_' . $name, md5(uniqid(__FUNCTION__, true)));

			if($editable){
				if(($foo = attributFehltError($attribs, 'parentid', __FUNCTION__))){
					return $foo;
				}

				if(!isset($_SESSION[$imgDataId])){
					$_SESSION[$imgDataId] = array();
				}
				$_SESSION[$imgDataId]['parentid'] = weTag_getAttribute('parentid', $attribs, 0, we_base_request::INT);
				//$_SESSION[$imgDataId]['maxfilesize'] = weTag_getAttribute('maxfilesize',$attribs);
				$_SESSION[$imgDataId]['width'] = weTag_getAttribute('width', $attribs, 0, we_base_request::INT);
				$_SESSION[$imgDataId]['height'] = weTag_getAttribute('height', $attribs, 0, we_base_request::INT);
				$_SESSION[$imgDataId]['quality'] = weTag_getAttribute('quality', $attribs, 8, we_base_request::INT);
				$_SESSION[$imgDataId]['keepratio'] = weTag_getAttribute('keepratio', $attribs, true, we_base_request::BOOL);
				$_SESSION[$imgDataId]['maximize'] = weTag_getAttribute('maximize', $attribs, false, we_base_request::BOOL);
				$_SESSION[$imgDataId]['id'] = $orgVal ? : '';

				$bordercolor = weTag_getAttribute('bordercolor', $attribs, '#006DB8', we_base_request::STRING);
				$checkboxstyle = weTag_getAttribute('checkboxstyle', $attribs, '', we_base_request::STRING);
				$inputstyle = weTag_getAttribute('inputstyle', $attribs, '', we_base_request::STRING);
				$checkboxclass = weTag_getAttribute('checkboxclass', $attribs, '', we_base_request::STRING);
				$inputclass = weTag_getAttribute('inputclass', $attribs, '', we_base_request::STRING);
				$checkboxtext = weTag_getAttribute('checkboxtext', $attribs, g_l('parser', '[delete]'), we_base_request::STRING);

				if($_SESSION[$imgDataId]['id']){
					$attribs['id'] = $_SESSION[$imgDataId]['id'];
				}

				if(isset($_SESSION[$imgDataId]['serverPath']) && strpos($_SESSION[$imgDataId]['serverPath'], TEMP_PATH) === false){
					$src = '/' . ltrim(substr($_SESSION[$imgDataId]['serverPath'], strlen($_SERVER['DOCUMENT_ROOT'])), '/');

					$imgTag = '<img src="' . $src . '" alt="" width="' . $_SESSION[$imgDataId]["imgwidth"] . '" height="' . $_SESSION[$imgDataId]["imgheight"] . '" />';
				} else {
					unset($attribs['width']);
					unset($attribs['height']);
					$imgTag = $GLOBALS['we_doc']->getField($attribs, 'img');
				}

				$checked = (!empty($_SESSION[$imgDataId]['doDelete'])) ? ' checked' : '';
				$inputstyle = ($size ? 'width:' . $size . 'em;' . $inputstyle : $inputstyle);
				return '<table class="weEditTable padding2 spacing2" style="border: solid ' . $bordercolor . ' 1px;">
	<tr>
		<td class="weEditmodeStyle" colspan="2" style="text-align:center">' . $imgTag . '
			<input type="hidden" name="WE_UI_IMG_DATA_ID_' . $name . '" value="' . $imgDataId . '" /></td>
	</tr>
	<tr>
		<td class="weEditmodeStyle" colspan="2" style="text-align:left">
			<input name="' . $fieldname . '" type="file" accept="' . implode(',', we_base_ContentTypes::inst()->getRealContentTypes(we_base_ContentTypes::IMAGE)) . '"' . ($inputstyle ? (' style="' . $inputstyle . '"') : '') . ($inputclass ? (' class="' . $inputclass . '"') : '') . '/>
		</td>
	</tr>
	<tr>
		<td class="weEditmodeStyle" colspan="2" style="text-align:left">
			<table class="weEditTable padding0 spacing0 border0">
				<tr>
					<td style="padding-right: 5px;"><input style="border:0px solid black;" type="checkbox" id="WE_UI_DEL_CHECKBOX_' . $name . '" name="WE_UI_DEL_CHECKBOX_' . $name . '" value="1" ' . $checked . '/></td>
					<td><label for="WE_UI_DEL_CHECKBOX_' . $name . '"' . ($checkboxstyle ? (' style="' . $checkboxstyle . '"') : '') . ($checkboxclass ? (' class="' . $checkboxclass . '"') : '') . '>' . $checkboxtext . '</label></td>
				</tr>
			</table>
		</td>
	</tr>
</table>';
			}

			$hidden = '<input type="hidden" name="WE_UI_IMG_DATA_ID_' . $name . '" value="' . $imgDataId . '" />';

			if(isset($_SESSION[$imgDataId]['serverPath'])){
				$src = '/' . ltrim(substr($_SESSION[$imgDataId]['serverPath'], strlen($_SERVER['DOCUMENT_ROOT'])), '/');

				return getHtmlTag('img', array(
						'src' => $src,
						'alt' => "",
						'style' => 'width:' . $_SESSION[$imgDataId]["imgwidth"] . 'px;height:' . $_SESSION[$imgDataId]["imgheight"] . 'px',
					)) . $hidden;
			}

			if(!empty($_SESSION[$imgDataId]['doDelete'])){
				return $hidden;
			}

			if((!empty($_SESSION[$imgDataId]['id'])) || (isset($orgVal) && $orgVal)){//Fix #9835
				unset($attribs['width']);
				unset($attribs['height']);
				$attribs['id'] = $_SESSION[$imgDataId]['id'] ? $_SESSION[$imgDataId]['id'] : $orgVal;
				return $GLOBALS['we_doc']->getField($attribs, 'img') . $hidden;
			}

			return '';

		case 'flashmovie' :
			$flashmovieDataId = we_base_request::_(we_base_request::HTML, 'WE_UI_FLASHMOVIE_DATA_ID_' . $name, md5(uniqid(__FUNCTION__, true)));

			if($editable){
				if(($foo = attributFehltError($attribs, 'parentid', __FUNCTION__))){
					return $foo;
				}

				if(!isset($_SESSION[$flashmovieDataId])){
					$_SESSION[$flashmovieDataId] = array();
				}
				$_SESSION[$flashmovieDataId]['parentid'] = weTag_getAttribute('parentid', $attribs, 0, we_base_request::INT);
				//$_SESSION[$imgDataId]['maxfilesize'] = weTag_getAttribute('maxfilesize',$attribs);
				$_SESSION[$flashmovieDataId]['width'] = weTag_getAttribute('width', $attribs, 0, we_base_request::INT);
				$_SESSION[$flashmovieDataId]['height'] = weTag_getAttribute('height', $attribs, 0, we_base_request::INT);
				$_SESSION[$flashmovieDataId]['id'] = $orgVal ? : '';

				$bordercolor = weTag_getAttribute('bordercolor', $attribs, '#006DB8', we_base_request::STRING);
				$checkboxstyle = weTag_getAttribute('checkboxstyle', $attribs, '', we_base_request::STRING);
				$inputstyle = weTag_getAttribute('inputstyle', $attribs, '', we_base_request::STRING);
				$checkboxclass = weTag_getAttribute('checkboxclass', $attribs, '', we_base_request::STRING);
				$inputclass = weTag_getAttribute('inputclass', $attribs, '', we_base_request::STRING);
				$checkboxtext = weTag_getAttribute('checkboxtext', $attribs, g_l('parser', '[delete]'), we_base_request::RAW);

				if($_SESSION[$flashmovieDataId]['id']){
					$attribs['id'] = $_SESSION[$flashmovieDataId]['id'];
				}

				if(isset($_SESSION[$flashmovieDataId]['serverPath'])){
					$src = '/' . ltrim(substr($_SESSION[$flashmovieDataId]['serverPath'], strlen($_SERVER['DOCUMENT_ROOT'])), '/');
					$flashmovieTag = '';
				} else {
					$attribs = removeAttribs($attribs, array('width', 'height'));
					// Include Flash class
					$flashmovieTag = (!empty($attribs['id']) ?
							$GLOBALS['we_doc']->getField($attribs, 'flashmovie') :
							'<img src="' . ICON_DIR . 'no_flashmovie.gif" alt="" width="64" height="64" />');
				}

				$checked = (!empty($_SESSION[$flashmovieDataId]['doDelete']) ? ' checked' : '');
				$inputstyle = ($size ? 'width:' . $size . 'em;' . $inputstyle : $inputstyle);
				return '<table class="weEditTable padding2 spacing2" style="border: solid ' . $bordercolor . ' 1px;">
	<tr>
		<td class="weEditmodeStyle" colspan="2" style="text-align:center">' . $flashmovieTag . '<input type="hidden" name="WE_UI_FLASHMOVIE_DATA_ID_' . $name . '" value="' . $flashmovieDataId . '" /></td>
	</tr>
	<tr>
		<td class="weEditmodeStyle" colspan="2" style="text-align:left"><input name="' . $fieldname . '" type="file" accept="application/x-shockwave-flash"' . ($inputstyle ? (' style="' . $inputstyle . '"') : '') . ($inputclass ? (' class="' . $inputclass . '"') : '') . '/></td>
	</tr>
	<tr>
		<td class="weEditmodeStyle" colspan="2" style="text-align:left">
			<table class="weEditTable padding0 spacing0 border0">
				<tr>
					<td style="padding-right: 5px;"><input style="border:0px solid black;" type="checkbox" id="WE_UI_DEL_CHECKBOX_' . $name . '" name="WE_UI_DEL_CHECKBOX_' . $name . '" value="1" ' . $checked . '/></td>
					<td><label for="WE_UI_DEL_CHECKBOX_' . $name . '"' . ($checkboxstyle ? (' style="' . $checkboxstyle . '"') : '') . ($checkboxclass ? (' class="' . $checkboxclass . '"') : '') . '>' . $checkboxtext . '</label></td>
				</tr>
			</table>
		</td>
	</tr>
</table>';
			}
			$hidden = '<input type="hidden" name="WE_UI_FLASHMOVIE_DATA_ID_' . $name . '" value="' . $flashmovieDataId . '" />';

			if(isset($_SESSION[$flashmovieDataId]['serverPath'])){
				$src = '/' . ltrim(substr($_SESSION[$flashmovieDataId]['serverPath'], strlen($_SERVER['DOCUMENT_ROOT'])), '/');

				return $hidden;
			}
			if(!empty($_SESSION[$flashmovieDataId]['id'])){

				if(!empty($_SESSION[$flashmovieDataId]['doDelete'])){
					return $hidden;
				}
				$attribs = removeAttribs($attribs, array('width', 'height'));
				$attribs['id'] = $_SESSION[$flashmovieDataId]['id'];
				return $GLOBALS['we_doc']->getField($attribs, 'flashmovie') . $hidden;
			}

			return '';
		case 'quicktime' :
			$quicktimeDataId = we_base_request::_(we_base_request::HTML, 'WE_UI_QUICKTIME_DATA_ID_' . $name, md5(uniqid(__FUNCTION__, true)));


			if($editable){
				if(($foo = attributFehltError($attribs, 'parentid', __FUNCTION__))){
					return $foo;
				}

				if(!isset($_SESSION[$quicktimeDataId])){
					$_SESSION[$quicktimeDataId] = array();
				}
				$_SESSION[$quicktimeDataId]['parentid'] = weTag_getAttribute('parentid', $attribs, 0, we_base_request::INT);
				//$_SESSION[$quicktimeDataId]['maxfilesize'] = weTag_getAttribute('maxfilesize',$attribs);
				$_SESSION[$quicktimeDataId]['width'] = weTag_getAttribute('width', $attribs, 0, we_base_request::INT);
				$_SESSION[$quicktimeDataId]['height'] = weTag_getAttribute('height', $attribs, 0, we_base_request::INT);
				$_SESSION[$quicktimeDataId]['id'] = $orgVal ? : '';

				$bordercolor = weTag_getAttribute('bordercolor', $attribs, '#006DB8', we_base_request::STRING);
				$checkboxstyle = weTag_getAttribute('checkboxstyle', $attribs, '', we_base_request::STRING);
				$inputstyle = weTag_getAttribute('inputstyle', $attribs, '', we_base_request::STRING);
				$checkboxclass = weTag_getAttribute('checkboxclass', $attribs, '', we_base_request::STRING);
				$inputclass = weTag_getAttribute('inputclass', $attribs, '', we_base_request::STRING);
				$checkboxtext = weTag_getAttribute('checkboxtext', $attribs, g_l('parser', '[delete]'), we_base_request::RAW);

				if($_SESSION[$quicktimeDataId]['id']){
					$attribs['id'] = $_SESSION[$quicktimeDataId]['id'];
				}

				if(isset($_SESSION[$quicktimeDataId]['serverPath'])){
					$src = '/' . ltrim(substr($_SESSION[$quicktimeDataId]['serverPath'], strlen($_SERVER['DOCUMENT_ROOT'])), '/');
					$quicktimeTag = '';
				} else {
					unset($attribs['width']);
					unset($attribs['height']);
					$quicktimeTag = (!empty($attribs['id']) ?
							$GLOBALS['we_doc']->getField($attribs, 'quicktime') :
							'<img src="' . ICON_DIR . 'no_quicktime.gif" alt="" width="64" height="64" />');
				}

				$checked = (!empty($_SESSION[$quicktimeDataId]["doDelete"]) ? ' checked' : '');
				$inputstyle = ($size ? 'width:' . $size . 'em;' . $inputstyle : $inputstyle);
				return '<table class="weEditTable padding2 spacing2" style="border: solid ' . $bordercolor . ' 1px;">
	<tr>
		<td class="weEditmodeStyle" colspan="2" style="text-align:center">' . $quicktimeTag . '
			<input type="hidden" name="WE_UI_QUICKTIME_DATA_ID_' . $name . '" value="' . $quicktimeDataId . '" /></td>
	</tr>
	<tr>
		<td class="weEditmodeStyle" colspan="2" style="text-align:left">
			<input name="' . $fieldname . '" type="file" accept="video/quicktime"' . ($inputstyle ? (' style="' . $inputstyle . '"') : '') . ($inputclass ? (' class="' . $inputclass . '"') : '') . '/>
		</td>
	</tr>
	<tr>
		<td class="weEditmodeStyle" colspan="2" style="text-align:left">
			<table class="weEditTable padding0 spacing0 border0">
				<tr>
					<td style="padding-right: 5px;">
						<input style="border:0px solid black;" type="checkbox" id="WE_UI_DEL_CHECKBOX_' . $name . '" name="WE_UI_DEL_CHECKBOX_' . $name . '" value="1" ' . $checked . '/>
					</td>
					<td>
						<label for="WE_UI_DEL_CHECKBOX_' . $name . '"' . ($checkboxstyle ? (' style="' . $checkboxstyle . '"') : '') . ($checkboxclass ? (' class="' . $checkboxclass . '"') : '') . '>' . $checkboxtext . '</label>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>';
			}
			$hidden = '<input type="hidden" name="WE_UI_QUICKTIME_DATA_ID_' . $name . '" value="' . $quicktimeDataId . '" />';

			if(isset($_SESSION[$quicktimeDataId]['serverPath'])){
				$src = '/' . ltrim(substr($_SESSION[$quicktimeDataId]['serverPath'], strlen($_SERVER['DOCUMENT_ROOT'])), '/');
				return $hidden;
			}
			if(empty($_SESSION[$quicktimeDataId]['id'])){
				return '';
			}

			if(!empty($_SESSION[$quicktimeDataId]['doDelete'])){
				return $hidden;
			}

			unset($attribs['width']);
			unset($attribs['height']);
			$attribs['id'] = $_SESSION[$quicktimeDataId]['id'];
			return $GLOBALS['we_doc']->getField($attribs, 'quicktime') . $hidden;

		case 'binary' :
			$binaryDataId = we_base_request::_(we_base_request::HTML, 'WE_UI_BINARY_DATA_ID_' . $name, md5(uniqid(__FUNCTION__, true)));

			if($editable){
				if(($foo = attributFehltError($attribs, 'parentid', __FUNCTION__))){
					return $foo;
				}

				if(!isset($_SESSION[$binaryDataId])){
					$_SESSION[$binaryDataId] = array();
				}
				$_SESSION[$binaryDataId]['parentid'] = weTag_getAttribute('parentid', $attribs, 0, we_base_request::INT);
				//$_SESSION[$binaryDataId]['maxfilesize'] = weTag_getAttribute('maxfilesize',$attribs);

				$_SESSION[$binaryDataId]['id'] = $orgVal ? : '';

				$bordercolor = weTag_getAttribute('bordercolor', $attribs, '#006DB8', we_base_request::STRING);
				$checkboxstyle = weTag_getAttribute('checkboxstyle', $attribs, '', we_base_request::STRING);
				$inputstyle = weTag_getAttribute('inputstyle', $attribs, '', we_base_request::STRING);
				$checkboxclass = weTag_getAttribute('checkboxclass', $attribs, '', we_base_request::STRING);
				$inputclass = weTag_getAttribute('inputclass', $attribs, '', we_base_request::STRING);
				$checkboxtext = weTag_getAttribute('checkboxtext', $attribs, g_l('parser', '[delete]'), we_base_request::RAW);

				if($_SESSION[$binaryDataId]['id']){
					$attribs['id'] = $_SESSION[$binaryDataId]['id'];
				}

				if(isset($_SESSION[$binaryDataId]['serverPath'])){
					$src = '/' . ltrim(substr($_SESSION[$binaryDataId]['serverPath'], strlen($_SERVER['DOCUMENT_ROOT'])), '/');
					$imgTag = 'imgTag';
				} else {
					$binaryTag = $GLOBALS['we_doc']->getField($attribs, 'binary');
					$t = explode('_', $binaryTag[0]);
					unset($t[1]);
					unset($t[0]);
					$fn = implode('_', $t);
					$imgTag = '<a href="' . $binaryTag[1] . '" target="_blank">' . $fn . '</a>';
				}

				$checked = (!empty($_SESSION[$binaryDataId]['doDelete']) ? ' checked' : '');
				$inputstyle = ($size ? 'width:' . $size . 'em;' : '') . $inputstyle;
				return '<table class="weEditTable padding2 spacing2" style="border: solid ' . $bordercolor . ' 1px;">
	<tr>
		<td class="weEditmodeStyle" colspan="2" style="text-align:center">' . $imgTag . '
			<input type="hidden" name="WE_UI_BINARY_DATA_ID_' . $name . '" value="' . $binaryDataId . '" /></td>
	</tr>
	<tr>
		<td class="weEditmodeStyle" colspan="2" style="text-align:left">
			<input name="' . $fieldname . '" type="file" accept="application/*"' . ($inputstyle ? (' style="' . $inputstyle . '"') : '') . ($inputclass ? (' class="' . $inputclass . '"') : '') . '/>
		</td>
	</tr>
	<tr>
		<td class="weEditmodeStyle" colspan="2" style="text-align:left">
			<table class="weEditTable padding0 spacing0 border0">
				<tr>
					<td style="padding-right: 5px;"><input style="border:0px solid black;" type="checkbox" id="WE_UI_DEL_CHECKBOX_' . $name . '" name="WE_UI_DEL_CHECKBOX_' . $name . '" value="1" ' . $checked . '/></td>
					<td><label for="WE_UI_DEL_CHECKBOX_' . $name . '"' . ($checkboxstyle ? (' style="' . $checkboxstyle . '"') : '') . ($checkboxclass ? (' class="' . $checkboxclass . '"') : '') . '>' . $checkboxtext . '</label></td>
				</tr>
			</table>
		</td>
	</tr>
</table>';
			}
			if(!isset($_SESSION[$binaryDataId])){
				$_SESSION[$binaryDataId] = array();
			}
			$_SESSION[$binaryDataId]['id'] = $orgVal ? : '';
			if($_SESSION[$binaryDataId]['id']){
				$attribs['id'] = $_SESSION[$binaryDataId]['id'];
			}
			$hidden = '<input type="hidden" name="WE_UI_BINARY_DATA_ID_' . $name . '" value="' . $binaryDataId . '" />';

			if(isset($_SESSION[$binaryDataId]["serverPath"])){
				$src = '/' . ltrim(substr($_SESSION[$binaryDataId]['serverPath'], strlen($_SERVER['DOCUMENT_ROOT'])), '/');
				return $hidden;
			}
			if(empty($_SESSION[$binaryDataId]['id'])){
				return '';
			}
			if(!empty($_SESSION[$binaryDataId]['doDelete'])){
				return $hidden;
			}

			$attribs['id'] = $_SESSION[$binaryDataId]['id'];
			$binaryTag = $GLOBALS['we_doc']->getField($attribs, 'binary');
			$t = explode('_', $binaryTag[0]);
			unset($t[1]);
			unset($t[0]);
			$fn = implode('_', $t);
			$imgTag = '<a href="' . $binaryTag[1] . '" target="_blank">' . $fn . '</a>';
			return $imgTag . $hidden;

		case 'textarea' :
			//$attribs['inlineedit'] = "true"; // bugfix: 7276
			if(weTag_getAttribute("pure", $attribs, false, we_base_request::BOOL)){
				$atts = removeAttribs($attribs, array(
					'wysiwyg',
					'commands',
					'pure',
					'type',
					'value',
					'checked',
					'autobr',
					'name',
					'values',
					'hidden',
					'editable',
					'format',
					'property',
					'size',
					'maxlength',
					'width',
					'height',
					'fontnames',
					'bgcolor',
					'editorcss',
					'ignoredocumentcss',
					'buttonpos'
				));
				return we_getTextareaField($fieldname, ($content ? : $value), $atts);
			}

			$autobr = $autobrAttr ? 'on' : 'off';
			$showAutobr = isset($attribs['autobr']);
			$charset = weTag_getAttribute('charset', $attribs, 'iso-8859-1', we_base_request::STRING);

			return we_html_element::jsElement('weFrontpageEdit=true;') .
				we_html_element::jsScript(JS_DIR . 'we_textarea.js') .
				we_html_element::jsScript(JS_DIR . 'windows.js') .
				(!$inlineedit ?
					//FIXME: does tiny really use weButtons?!
					STYLESHEET_MINIMAL .
					we_html_element::jsScript(WE_JS_TINYMCE_DIR . 'weTinyMceDialogs.js') :
					''
				) .
				we_html_forms::weTextarea($fieldname, ($content ? : $value), $attribs, $autobr, 'autobr', $showAutobr, $GLOBALS['we_doc']->getHttpPath(), false, false, $xml, $removeFirstParagraph, $charset, false, true, $name);

		case 'checkbox' :
			$atts = removeAttribs($attribs, array(
				'wysiwyg',
				'commands',
				'pure',
				'type',
				'value',
				'checked',
				'autobr',
				'name',
				'values',
				'hidden',
				'editable',
				'format',
				'property',
				'cols',
				'rows',
				'width',
				'height',
				'bgcolor',
				'fontnames'
			));
			if((!$isset) && $checked){
				$content = 1;
			}
			return we_getInputCheckboxField($fieldname, $content, $atts);
		case 'date' :
			$currentdate = weTag_getAttribute('currentdate', $attribs, false, we_base_request::BOOL);
			if($orgVal == 0 || $currentdate){
				$orgVal = time();
			}
			if($hidden){
				$attsHidden = array(
					'type' => 'hidden',
					'name' => $fieldname,
					'value' => $orgVal ? : time(),
					'xml' => $xml
				);
				return getHtmlTag('input', $attsHidden);
			}
			$year = date('Y');
			$minyear = weTag_getAttribute('minyear', $attribs, 0, we_base_request::INT);
			switch($minyear ? $minyear{0} : ''){
				case '+':
					$minyear = $year + intval(substr($minyear, 1));
					break;
				case '-':
					$minyear = $year - intval(substr($minyear, 1));
					break;
				default:
					$minyear = intval($minyear);
					break;
			}
			$maxyear = weTag_getAttribute('maxyear', $attribs, 0, we_base_request::INT);
			switch($maxyear ? $maxyear{0} : ''){
				case '+':
					$maxyear = $year + intval(substr($maxyear, 1));
					break;
				case '-':
					$maxyear = $year - intval(substr($maxyear, 1));
					break;
				default:
					$maxyear = intval($maxyear);
					break;
			}
			return we_html_tools::getDateInput('we_ui_' . (isset($GLOBALS['WE_FORM']) ? $GLOBALS['WE_FORM'] : '') . '[we_date_' . $name . ']', ($orgVal ? : time()), false, $format, '', '', $xml, $minyear, $maxyear);

		case 'country':
			$newAtts = removeAttribs($attribs, array('wysiwyg', 'commands', 'pure', 'type', 'value', 'checked', 'autobr', 'name', 'values', 'hidden', 'editable', 'format', 'property', 'rows', 'cols', 'fontnames', 'bgcolor', 'width', 'height', 'maxlength'));
			$docAttr = weTag_getAttribute('doc', $attribs, 'self', we_base_request::STRING);

			$doc = we_getDocForTag($docAttr);
			$lang = $doc->Language;
			$langcode = ($lang ?
					substr($lang, 0, 2) :
					array_search($GLOBALS['WE_LANGUAGE'], getWELangs()));

			$topCountries = array_flip(explode(',', WE_COUNTRIES_TOP));
			foreach($topCountries as $countrykey => &$countryvalue){
				$countryvalue = we_base_country::getTranslation($countrykey, we_base_country::TERRITORY, $langcode);
			}
			unset($countryvalue);
			$shownCountries = array_flip(explode(',', WE_COUNTRIES_SHOWN));
			foreach($shownCountries as $countrykey => &$countryvalue){
				$countryvalue = we_base_country::getTranslation($countrykey, we_base_country::TERRITORY, $langcode);
			}
			unset($countryvalue);
			$oldLocale = setlocale(LC_ALL, NULL);
			setlocale(LC_ALL, $lang . '.UTF-8');
			asort($topCountries, SORT_LOCALE_STRING);
			asort($shownCountries, SORT_LOCALE_STRING);
			setlocale(LC_ALL, $oldLocale);

			$options = '';
			if(WE_COUNTRIES_DEFAULT != ''){
				$options.='<option value="--" ' . ($orgVal === '--' ? ' selected="selected">' : '>') . WE_COUNTRIES_DEFAULT . '</option>';
			}
			foreach($topCountries as $countrykey => &$countryvalue){
				$options.='<option value="' . $countrykey . '" ' . ($orgVal == $countrykey ? ' selected="selected">' : '>') . CheckAndConvertISOfrontend($countryvalue) . '</option>';
			}
			unset($countryvalue);
			if(!empty($topCountries) && !empty($shownCountries)){
				$options.='<option value="-" disabled="disabled">----</option>';
			}
			foreach($shownCountries as $countrykey2 => &$countryvalue2){
				$options.='<option value="' . $countrykey2 . '" ' . ($orgVal == $countrykey2 ? ' selected="selected">' : '>') . CheckAndConvertISOfrontend($countryvalue2) . '</option>';
			}
			unset($countryvalue2);
			$newAtts['size'] = (isset($atts['size']) ? $atts['size'] : 1);
			$newAtts['name'] = $fieldname;
			return getHtmlTag('select', $newAtts, $options, true);
		case 'language':
			$newAtts = removeAttribs($attribs, array('wysiwyg', 'commands', 'pure', 'type', 'value', 'checked', 'autobr', 'name', 'values', 'hidden', 'editable', 'format', 'property', 'rows', 'cols', 'fontnames', 'bgcolor', 'width', 'height', 'maxlength'));

			$docAttr = weTag_getAttribute('doc', $attribs, 'self', we_base_request::STRING);
			$doc = we_getDocForTag($docAttr);
			$lang = $doc->Language;
			$langcode = ($lang ?
					substr($lang, 0, 2) :
					array_search($GLOBALS['WE_LANGUAGE'], getWELangs()) );

			$frontendL = $GLOBALS['weFrontendLanguages'];
			foreach($frontendL as &$lcvalue){
				$lccode = explode('_', $lcvalue);
				$lcvalue = $lccode[0];
			}
			$frontendLL = array();
			foreach($frontendL as &$lcvalue){
				$frontendLL[$lcvalue] = we_base_country::getTranslation($lcvalue, we_base_country::LANGUAGE, $langcode);
			}

			$oldLocale = setlocale(LC_ALL, NULL);
			setlocale(LC_ALL, $lang . '.UTF-8');
			asort($frontendLL, SORT_LOCALE_STRING);
			setlocale(LC_ALL, $oldLocale);
			$options = '';
			foreach($frontendLL as $langkey => &$langvalue){
				$options.='<option value="' . $langkey . '" ' . ($orgVal == $langkey ? ' selected="selected">' : '>') . CheckAndConvertISOfrontend($langvalue) . '</option>';
			}
			unset($langvalue);
			$newAtts['size'] = (isset($atts['size']) ? $atts['size'] : 1);
			$newAtts['name'] = $fieldname;
			return getHtmlTag('select', $newAtts, $options, true);
		case 'select' :
			$options = '';
			$atts = removeAttribs($attribs, array(
				'wysiwyg',
				'commands',
				'pure',
				'type',
				'value',
				'checked',
				'autobr',
				'name',
				'values',
				'hidden',
				'editable',
				'format',
				'property',
				'rows',
				'cols',
				'fontnames',
				'bgcolor',
				'width',
				'height',
				'maxlength'
			));
			if($values){

				$values = explode(',', $values);

				foreach($values as $txt){

					$attsOption = ($txt == $orgVal ?
							array(
							'selected' => 'selected'
							) :
							array());

					$options .= getHtmlTag('option', $attsOption, trim($txt), true);
				}
			} elseif($object && isset($object->DefArray["meta_" . $name])){
				foreach($object->DefArray["meta_" . $name]["meta"] as $key => $val){

					if($orgVal == $key){
						$atts2 = array(
							'value' => $key, 'selected' => 'selected'
						);
					} else {
						$atts2 = array(
							'value' => $key
						);
					}
					$attsOption = removeAttribs(array_merge($atts, $atts2), array('class'));
					$options .= getHtmlTag('option', $attsOption, $val, true);
				}
			}
			$atts['size'] = (isset($atts['size']) ? $atts['size'] : 1);
			$atts['name'] = $fieldname;
			return getHtmlTag('select', $atts, $options, true);
		case 'radio' :
			$atts = removeAttribs($attribs, array(
				'wysiwyg',
				'commands',
				'pure',
				'type',
				'value',
				'checked',
				'autobr',
				'name',
				'values',
				'hidden',
				'editable',
				'format',
				'property',
				'rows',
				'cols',
				'width',
				'height',
				'bgcolor',
				'fontnames'
			));
			return (!$isset ?
					we_getInputRadioField($fieldname, ($checked ? $value : $value . 'dummy'), $value, $atts) :
					we_getInputRadioField($fieldname, $orgVal, $value, $atts));

		case 'hidden':
			return getHtmlTag('input', array(
				'type' => 'hidden',
				'name' => $fieldname,
				'value' => oldHtmlspecialchars($content),
				'xml' => $xml,
			));
		case 'print' :
			return $orgVal;
		case 'choice' :
			$atts = removeAttribs($attribs, array(
				'wysiwyg',
				'commands',
				'pure',
				'type',
				'value',
				'checked',
				'autobr',
				'name',
				'values',
				'hidden',
				'editable',
				'format',
				'property',
				'cols',
				'rows',
				'width',
				'height',
				'bgcolor',
				'fontnames',
				'maxlength'
			));
			$mode = weTag_getAttribute('mode', $attribs, '', we_base_request::STRING);
			return we_html_tools::htmlInputChoiceField($fieldname, $orgVal, $values, $atts, $mode);
		case 'password':
			$atts = array_merge(removeAttribs($attribs, array(
				'wysiwyg',
				'commands',
				'pure',
				'type',
				'value',
				'checked',
				'autobr',
				'name',
				'values',
				'hidden',
				'editable',
				'format',
				'property',
				'cols',
				'rows',
				'width',
				'height',
				'bgcolor',
				'fontnames'
				)), array(
				'type' => 'password',
				'name' => $fieldname,
				'value' => oldHtmlspecialchars($orgVal))
			);

			return getHtmlTag('input', $atts);
		case 'textinput':
		default :
			$atts = removeAttribs($attribs, array(
				'wysiwyg',
				'commands',
				'pure',
				'type',
				'value',
				'checked',
				'autobr',
				'name',
				'values',
				'hidden',
				'editable',
				'format',
				'property',
				'cols',
				'rows',
				'width',
				'height',
				'bgcolor',
				'fontnames'
			));
			return we_getInputTextInputField($fieldname, $orgVal, $atts);
	}
}
