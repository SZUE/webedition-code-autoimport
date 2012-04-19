<?php
/**
 * webEdition CMS
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



function we_tag_votingField($attribs, $content) {
	include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_modules/voting/weVoting.php");

	if(isset($GLOBALS['_we_voting'])){
		$name = we_getTagAttributeTagParser("name",$attribs,'',false,false,true);
		$type = we_getTagAttributeTagParser("type",$attribs);
		$precision = we_getTagAttributeTagParser("precision",$attribs,0);
		$num_format = we_getTagAttributeTagParser("num_format",$attribs,'');

		switch ($name){
			case 'id':
				switch ($type){
					case 'answer':
						$returnvalue =  $GLOBALS['_we_voting']->answerCount;
					break;
					case 'select':
						$returnvalue =  '_we_voting_answer_' . $GLOBALS['_we_voting']->ID;
					break;
					case 'radio':
					case 'checkbox':
					case 'chekbox':
						$returnvalue =  '_we_voting_answer_' . $GLOBALS['_we_voting']->ID . '_' . $GLOBALS['_we_voting']->answerCount;
					break;
					case 'voting':
					default:
						$returnvalue =  $GLOBALS['_we_voting']->ID;
					break;
				}
			break;
			case 'question':
				$returnvalue =  stripslashes($GLOBALS['_we_voting']->QASet[$GLOBALS['_we_voting']->defVersion]['question']);
			break;
			case 'answer':
				switch ($type){
					case 'radio':
						$code = '';
						$GLOBALS['_we_voting']->IsRadio = true;
						$countanswers= count($GLOBALS['_we_voting']->QASet[$GLOBALS['_we_voting']->defVersion]['answers']);
						if ($GLOBALS['_we_voting']->AllowFreeText && $GLOBALS['_we_voting']->answerCount == $countanswers-1){$subb = 1;} else {$subb = 0;}
						if ($GLOBALS['_we_voting']->answerCount < $countanswers - $subb){
							$atts = removeAttribs($attribs,array('name','type'));
							$atts['name'] = '_we_voting_answer_' . $GLOBALS['_we_voting']->ID;
							if ($GLOBALS['_we_voting']->IsRequired && $GLOBALS['_we_voting']->answerCount==0) {
								$atts['type'] = 'hidden';
								$code .=  getHtmlTag('input',$atts,'');
							}
							$atts['id'] = '_we_voting_answer_' . $GLOBALS['_we_voting']->ID . '_' . $GLOBALS['_we_voting']->answerCount;
							$atts['value'] = $GLOBALS['_we_voting']->answerCount;
							$atts['type'] = 'radio';
							if (isset($_SESSION['_we_voting_sessionData']) && isset($_SESSION['_we_voting_sessionData'][$GLOBALS['_we_voting']->ID])){
								$selItem = $_SESSION['_we_voting_sessionData'][$GLOBALS['_we_voting']->ID]['value'][0];
								if (is_numeric($selItem) && $selItem == $GLOBALS['_we_voting']->answerCount) {
									$atts['checked'] = 'checked';
								}
							}
							if($GLOBALS['_we_voting']->AllowFreeText){
								$countanswers--;
								$atts['onclick']=  "_we_voting_answer_". $GLOBALS['_we_voting']->ID . "_".$countanswers.".value='';";
							}

							$code .=  getHtmlTag('input',$atts,'');
						}
						$returnvalue =  $code;
					break;
					case 'checkbox':
						$code = '';
						$GLOBALS['_we_voting']->IsCheckbox = true;
						$countanswers= count($GLOBALS['_we_voting']->QASet[$GLOBALS['_we_voting']->defVersion]['answers']);
						if ($GLOBALS['_we_voting']->AllowFreeText && $GLOBALS['_we_voting']->answerCount == $countanswers-1){$subb = 1;} else {$subb = 0;}
						if ($GLOBALS['_we_voting']->answerCount < $countanswers - $subb){
							$atts = removeAttribs($attribs,array('name','type'));
							$atts['name'] = '_we_voting_answer_' . $GLOBALS['_we_voting']->ID;
							if ($GLOBALS['_we_voting']->IsRequired && $GLOBALS['_we_voting']->answerCount==0) {
								$atts['type'] = 'hidden';
								$code .=  getHtmlTag('input',$atts,'');
							}
							$atts['name'] = '_we_voting_answer_' . $GLOBALS['_we_voting']->ID . '_' . $GLOBALS['_we_voting']->answerCount;
							$atts['id'] = '_we_voting_answer_' . $GLOBALS['_we_voting']->ID . '_' . $GLOBALS['_we_voting']->answerCount;
							$atts['value'] = $GLOBALS['_we_voting']->answerCount;
							$atts['type'] = 'checkbox';
							if (isset($_SESSION['_we_voting_sessionData']) && isset($_SESSION['_we_voting_sessionData'][$GLOBALS['_we_voting']->ID])){
								foreach ($_SESSION['_we_voting_sessionData'][$GLOBALS['_we_voting']->ID]['value'] as $kk => $wert) {
									$selItem = $_SESSION['_we_voting_sessionData'][$GLOBALS['_we_voting']->ID]['value'][$kk];
									$selItem = $wert;

									if (is_numeric($selItem) &&  $selItem == $GLOBALS['_we_voting']->answerCount) {
										$atts['checked'] = 'checked';
									}
								}
							}

							$code .= getHtmlTag('input',$atts,'');
						}
						$returnvalue =  $code;
					break;
					case 'select':
						$code = '';
						if($GLOBALS['_we_voting']->answerCount==0){

							$atts = removeAttribs($attribs,array('name','type'));
							$atts['name'] = '_we_voting_answer_' . $GLOBALS['_we_voting']->ID;
							$atts['id'] = '_we_voting_answer_' . $GLOBALS['_we_voting']->ID;

							$code .= getHtmlTag('select',$atts,'');
						}

						$atts = removeAttribs($attribs,array('name','type'));
						$atts['value'] = $GLOBALS['_we_voting']->answerCount;

						$code .= getHtmlTag('option',$atts,addslashes($GLOBALS['_we_voting']->getAnswer()));
						if($GLOBALS['_we_voting']->isLastSet()){
							$code .= '</select>';
						}
						$returnvalue =  $code;
					break;
					case 'image':
						$code = '';
						$countanswers= count($GLOBALS['_we_voting']->QASet[$GLOBALS['_we_voting']->defVersion]['answers']);
						if ($GLOBALS['_we_voting']->answerCount < $countanswers){
							$myImageID = stripslashes($GLOBALS['_we_voting']->QASetAdditions[$GLOBALS['_we_voting']->defVersion]['imageID'][$GLOBALS['_we_voting']->answerCount]);
							if (is_numeric($myImageID)) {
								$myImage= new we_imageDocument();
								$myImage->initByID($myImageID);

								$atts = removeAttribs($attribs,array('name','type','precision','num_format','nameto','to'));
								$myImage->initByAttribs($atts);
								$code = $myImage->getHtml();
							}
						}
						$returnvalue =  $code;
					break;
					case 'media':
						$countanswers= count($GLOBALS['_we_voting']->QASet[$GLOBALS['_we_voting']->defVersion]['answers']);
						if ($GLOBALS['_we_voting']->answerCount < $countanswers){
							$myMediaID = stripslashes($GLOBALS['_we_voting']->QASetAdditions[$GLOBALS['_we_voting']->defVersion]['mediaID'][$GLOBALS['_we_voting']->answerCount]);
						}
						$returnvalue =  id_to_path($myMediaID);
					break;
					case 'textinput':
						$code = '';
						if ($GLOBALS['_we_voting']['AllowFreeText']) {

							$atts = removeAttribs($attribs,array('name','type'));
							$countanswers= count($GLOBALS['_we_voting']->QASet[$GLOBALS['_we_voting']->defVersion]['answers']);
							if ($GLOBALS['_we_voting']->answerCount == $countanswers-1){
								$atts['type'] = 'text';
								$atts['name'] = '_we_voting_answer_' . $GLOBALS['_we_voting']->ID . '_' . $GLOBALS['_we_voting']->answerCount;
								$atts['id'] = '_we_voting_answer_' . $GLOBALS['_we_voting']->ID . '_' . $GLOBALS['_we_voting']->answerCount;
								$value= '';
								if (isset($_SESSION['_we_voting_sessionData']) && isset($_SESSION['_we_voting_sessionData'][$GLOBALS['_we_voting']->ID])){
									if ($GLOBALS['_we_voting']->IsRadio) {
										$value = $_SESSION['_we_voting_sessionData'][$GLOBALS['_we_voting']->ID]['value'][0];
									} else {
										if ($GLOBALS['_we_voting']->IsCheckbox) {
											$mycount = count($_SESSION['_we_voting_sessionData'][$GLOBALS['_we_voting']->ID]['value']);
											$value = $_SESSION['_we_voting_sessionData'][$GLOBALS['_we_voting']->ID]['value'][$mycount-1];

										} else {
											$value = $_SESSION['_we_voting_sessionData'][$GLOBALS['_we_voting']->ID]['value'][$GLOBALS['_we_voting']->answerCount];
										}
									}

								}
								if(isset($GLOBALS['_we_voting']->IsRadio) && $GLOBALS['_we_voting']->IsRadio) {
									$atts['onkeydown'] ='';
									for ($i = 0;$i < $countanswers - 1;$i++) {
										$atts['onkeydown'] .= "_we_voting_answer_". $GLOBALS['_we_voting']->ID . "_".$i.".checked=0;";
									}

								}
								$code .= getHtmlTag('input',$atts,$value);
							}
						}
						$returnvalue =  $code;
					break;
					case 'textarea':
						$code = '';
						if ($GLOBALS['_we_voting']->AllowFreeText) {
							$atts = removeAttribs($attribs,array('name','type'));
							$countanswers= count($GLOBALS['_we_voting']->QASet[$GLOBALS['_we_voting']->defVersion]['answers']);
							if ($GLOBALS['_we_voting']->answerCount == $countanswers-1){
								$atts['name'] = '_we_voting_answer_' . $GLOBALS['_we_voting']->ID . '_' . $GLOBALS['_we_voting']->answerCount;
								$atts['id'] = '_we_voting_answerfree_' . $GLOBALS['_we_voting']->ID . '_' . $GLOBALS['_we_voting']->answerCount;
								$value= '';
								if (isset($_SESSION['_we_voting_sessionData']) && isset($_SESSION['_we_voting_sessionData'][$GLOBALS['_we_voting']->ID])){
									if ($GLOBALS['_we_voting']->IsRadio) {
										$value = $_SESSION['_we_voting_sessionData'][$GLOBALS['_we_voting']->ID]['value'][0];
									} else {
										if ($GLOBALS['_we_voting']->IsCheckbox) {
											$mycount = count($_SESSION['_we_voting_sessionData'][$GLOBALS['_we_voting']->ID]['value']);
											$value = $_SESSION['_we_voting_sessionData'][$GLOBALS['_we_voting']->ID]['value'][$mycount-1];

										} else {
											$value = $_SESSION['_we_voting_sessionData'][$GLOBALS['_we_voting']->ID]['value'][$GLOBALS['_we_voting']->answerCount];
										}
									}

								}
								if(isset($GLOBALS['_we_voting']->IsRadio) && $GLOBALS['_we_voting']->IsRadio) {
									$atts['onkeydown'] ='';
									for ($i = 0;$i < $countanswers - 1;$i++) {
										$atts['onkeydown'] .= "_we_voting_answer_". $GLOBALS['_we_voting']->ID . "_".$i.".checked=0;";
									}

								}
								$code = getHtmlTag('textarea',$atts,$value,true);
							}
						}
						$returnvalue =  $code;
					break;
					case 'text':
					default:
						$code = '';
						$countanswers= count($GLOBALS['_we_voting']->QASet[$GLOBALS['_we_voting']->defVersion]['answers']);
						if ($GLOBALS['_we_voting']->answerCount < $countanswers){
							$code = stripslashes($GLOBALS['_we_voting']->QASet[$GLOBALS['_we_voting']->defVersion]['answers'][$GLOBALS['_we_voting']->answerCount]);
						}
						$returnvalue =  $code;
					break;
				}
			break;
			case 'result':
				$returnvalue =  $GLOBALS['_we_voting']->getResult($type,$num_format,$precision);
			break;
			case 'date':
				$format = we_getTagAttributeTagParser("format",$attribs,"");
				include($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_language/" . $GLOBALS["WE_LANGUAGE"] . "/we_editor_info.inc.php");
				$returnvalue =  date(($format!="" ? $format : $l_we_editor_info["date_format"]), $GLOBALS['_we_voting']->PublishDate);
			break;
		}

	}
	return $returnvalue;
}
