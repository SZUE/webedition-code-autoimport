<?php

// +----------------------------------------------------------------------+
// | webEdition                                                           |
// +----------------------------------------------------------------------+
// | PHP version 4.1.0 or greater                                         |
// +----------------------------------------------------------------------+
// | Copyright (c) living-e AG                   |
// +----------------------------------------------------------------------+
//



function we_tag_votingField($attribs, $content) {

	if(isset($GLOBALS['_we_voting'])){
		$name = we_getTagAttributeTagParser("name",$attribs);
		$type = we_getTagAttributeTagParser("type",$attribs);
		$precision = we_getTagAttributeTagParser("precision",$attribs,0);
		$num_format = we_getTagAttributeTagParser("num_format",$attribs,'');

		switch ($name){
			case 'id':
				switch ($type){
					case 'answer':
						return $GLOBALS['_we_voting']->answerCount;
					break;
					case 'select':
						return '_we_voting_answer_' . $GLOBALS['_we_voting']->ID;
					break;
					case 'radio':
					case 'chekbox':
						return '_we_voting_answer_' . $GLOBALS['_we_voting']->ID . '_' . $GLOBALS['_we_voting']->answerCount;
					break;
					case 'voting':
					default:
						return $GLOBALS['_we_voting']->ID;
					break;					
				}
			break;
			case 'question':
				return stripslashes($GLOBALS['_we_voting']->QASet[$GLOBALS['_we_voting']->defVersion]['question']);
			break;
			case 'answer':
				switch ($type){
					case 'radio':
						$atts = removeAttribs($attribs,array('name','type'));
						$atts['name'] = '_we_voting_answer_' . $GLOBALS['_we_voting']->ID;
						$atts['id'] = '_we_voting_answer_' . $GLOBALS['_we_voting']->ID . '_' . $GLOBALS['_we_voting']->answerCount;
						$atts['value'] = $GLOBALS['_we_voting']->answerCount;
						$atts['type'] = 'radio';

						return getHtmlTag('input',$atts,'');

					break;
					case 'checkbox':
						$atts = removeAttribs($attribs,array('name','type'));
						$atts['name'] = '_we_voting_answer_' . $GLOBALS['_we_voting']->ID . '_' . $GLOBALS['_we_voting']->answerCount;
						$atts['id'] = '_we_voting_answer_' . $GLOBALS['_we_voting']->ID . '_' . $GLOBALS['_we_voting']->answerCount;
						$atts['value'] = $GLOBALS['_we_voting']->answerCount;
						$atts['type'] = 'checkbox';

						return getHtmlTag('input',$atts,'');

					break;
					case 'select':
						$code = '';
						if($GLOBALS['_we_voting']->answerCount==0){

							$atts = removeAttribs($attribs,array('name','type'));
							$atts['name'] = '_we_voting_answer_' . $GLOBALS['_we_voting']->ID;
							$atts['id'] = '_we_voting_answer_' . $GLOBALS['_we_voting']->ID;

							$code = getHtmlTag('select',$atts,'');
						}

						$atts = removeAttribs($attribs,array('name','type'));
						$atts['value'] = $GLOBALS['_we_voting']->answerCount;

						$code .= getHtmlTag('option',$atts,addslashes($GLOBALS['_we_voting']->getAnswer()));
						if($GLOBALS['_we_voting']->isLastSet()){
							$code .= '</select>';
						}
						return $code;
					break;
					case 'text':
					default:
						return stripslashes($GLOBALS['_we_voting']->QASet[$GLOBALS['_we_voting']->defVersion]['answers'][$GLOBALS['_we_voting']->answerCount]);
					break;
				}
			break;
			case 'result':
				return $GLOBALS['_we_voting']->getResult($type,$num_format,$precision);
			break;
			case 'date':
				$format = we_getTagAttributeTagParser("format",$attribs,"");
				include($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_language/" . $GLOBALS["WE_LANGUAGE"] . "/we_editor_info.inc.php");
				return date(($format!="" ? $format : $l_we_editor_info["date_format"]), $GLOBALS['_we_voting']->PublishDate);
			break;
		}

	}

	return null;
}
?>