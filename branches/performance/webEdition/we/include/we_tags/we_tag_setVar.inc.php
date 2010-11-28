<?php
function we_tag_setVar($attribs, $content){
	$foo = attributFehltError($attribs, "nameto", "setVar");
	if ($foo)
		return $foo;
	$foo = attributFehltError($attribs, "to", "setVar");
	if ($foo)
		return $foo;

	$nameFrom = we_getTagAttribute("namefrom", $attribs);
	$nameTo = we_getTagAttribute("nameto", $attribs);
	$typeFrom = we_getTagAttribute("typefrom", $attribs, "text");
	$to = we_getTagAttribute("to", $attribs);
	$from = we_getTagAttribute("from", $attribs);
	$propertyTo = we_getTagAttribute("propertyto", $attribs, "", true);
	$propertyFrom = we_getTagAttribute("propertyfrom", $attribs, "", true);
	$striptags = we_getTagAttribute("striptags", $attribs, "", true);
	$formnameTo = we_getTagAttribute("formnameto", $attribs, "we_global_form");
	$formnameFrom = we_getTagAttribute("formnamefrom", $attribs, "we_global_form");
	if (isset($attribs["value"])) {
		$valueFrom = we_getTagAttribute("value", $attribs);
	} else {

		$valueFrom = "";
		switch ($from) {
			case "request" :
				$valueFrom = isset($_REQUEST[$nameFrom]) ? $_REQUEST[$nameFrom] : "";
				break;
			case "post" :
				$valueFrom = isset($_POST[$nameFrom]) ? $_POST[$nameFrom] : "";
				break;
			case "get" :
				$valueFrom = isset($_GET[$nameFrom]) ? $_GET[$nameFrom] : "";
				break;
			case "global" :
				$valueFrom = isset($GLOBALS[$nameFrom]) ? $GLOBALS[$nameFrom] : "";
				break;
			case "session" :
				$valueFrom = isset($_SESSION[$nameFrom]) ? $_SESSION[$nameFrom] : "";
				break;
			case "top" :
				if ($propertyFrom) {
					eval(
							'$valueFrom = isset($GLOBALS["WE_MAIN_DOC"]->' . $nameFrom . ') ? $GLOBALS["WE_MAIN_DOC"]->' . $nameFrom . ' : "";');
				} else {
					if ($typeFrom == "href") {
						$valueFrom = isset($GLOBALS["WE_MAIN_DOC"]->elements[$nameFrom . '_we_jkhdsf_int']) ? $GLOBALS["WE_MAIN_DOC"]->getField(
								array(
									"name" => $nameFrom
								),
								$typeFrom,
								true) : "";
					} else {
						$valueFrom = isset($GLOBALS["WE_MAIN_DOC"]->elements[$nameFrom]) ? $GLOBALS["WE_MAIN_DOC"]->getField(
								array(
									"name" => $nameFrom
								),
								$typeFrom,
								true) : "";
					}
				}
				break;
			case "self" :
				if ($propertyFrom) {
					eval(
							'$valueFrom = isset($GLOBALS["we_doc"]->' . $nameFrom . ') ? $GLOBALS["we_doc"]->' . $nameFrom . ' : "";');
				} else {
					if ($typeFrom == "href") {
						$valueFrom = isset($GLOBALS["we_doc"]->elements[$nameFrom . '_we_jkhdsf_int']) ? $GLOBALS["we_doc"]->getField(
								array(
									"name" => $nameFrom
								),
								$typeFrom,
								true) : "";
					} else {
						$valueFrom = isset($GLOBALS["we_doc"]->elements[$nameFrom]) ? $GLOBALS["we_doc"]->getField(
								array(
									"name" => $nameFrom
								),
								$typeFrom,
								true) : "";
					}
				}
				break;
			case "object" :
			case "document" :
				if ($propertyFrom) {
					eval(
							'$valueFrom = isset($GLOBALS["we_' . $from . '"][$formnameFrom]->' . $nameFrom . ') ? $GLOBALS["we_' . $from . '"][$formnameFrom]->' . $nameFrom . ' : "";');
				} else {
					$valueFrom = isset($GLOBALS["we_" . $from][$formnameFrom]->elements[$nameFrom]) ? $GLOBALS["we_" . $from][$formnameFrom]->getElement(
							$nameFrom) : "";
				}
				break;
			case "sessionfield" :
				$valueFrom = isset($_SESSION["webuser"][$nameFrom]) ? $_SESSION["webuser"][$nameFrom] : "";
				break;
			case "calendar" :
				$valueFrom = listviewBase::getCalendarFieldValue($GLOBALS["lv"]->calendar_struct, $nameFrom);
				break;
			case "listview" :
				if (!isset($GLOBALS["lv"])) {
					return parseError($GLOBALS["l_parser"]["setVar_lv_not_in_lv"]);
				}
				$valueFrom = we_tag_field(array(
					'name' => $nameFrom, 'type' => $typeFrom
				), "");
				break;
			case "block" :

				if ($typeFrom == "href") {

					if ($GLOBALS["we_doc"]->elements[$nameFrom . "_we_jkhdsf_int"]["dat"]) {
						$nameFrom .= "_we_jkhdsf_intPath";
					}
				}
				$valueFrom = isset($GLOBALS["WE_MAIN_DOC"]->elements[$nameFrom]) ? $GLOBALS["WE_MAIN_DOC"]->getField(
						array(
							"name" => $nameFrom
						),
						$typeFrom,
						true) : "";
				break;
			case "listdir" :
				$valueFrom = isset($GLOBALS['we_position']['listdir'][$nameFrom]) ? $GLOBALS['we_position']['listdir'][$nameFrom] : "";
				break;

		}
	}
	if($striptags){$valueFrom=strip_tags(htmlentities($valueFrom));}
	switch ($to) {
		case "request" :
			$_REQUEST[$nameTo] = $valueFrom;
			break;
		case "post" :
			$_POST[$nameTo] = $valueFrom;
			break;
		case "get" :
			$_GET[$nameTo] = $valueFrom;
			break;
		case "global" :
			$GLOBALS[$nameTo] = $valueFrom;
			break;
		case "session" :
			$_SESSION[$nameTo] = $valueFrom;
			break;
		case "top" :
			if ($propertyTo) {
				eval('$GLOBALS["WE_MAIN_DOC_REF"]->' . $nameTo . ' = $valueFrom;');
			} else {
				$GLOBALS["WE_MAIN_DOC_REF"]->setElement($nameTo, $valueFrom);
			}
			break;
		case "block" :
		case "self" :
			if ($propertyTo) {
				eval('$GLOBALS["we_doc"]->' . $nameTo . ' = $valueFrom;');
			} else {
				$GLOBALS["we_doc"]->setElement($nameTo, $valueFrom);
			}
			break;
		case "object" :
		case "document" :
			if ($propertyTo) {
				if (isset($GLOBALS["we_$to"][$formnameTo]))
					eval('$GLOBALS["we_$to"][$formnameTo]->' . $nameTo . ' = $valueFrom;');
			} else {
				if (isset($GLOBALS["we_$to"][$formnameTo]))
					$GLOBALS["we_$to"][$formnameTo]->setElement($nameTo, $valueFrom);
			}
			break;
		case "sessionfield" :
			if (isset($_SESSION["webuser"][$nameTo]))
				$_SESSION["webuser"][$nameTo] = $valueFrom;
	}

}?>
