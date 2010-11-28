<?php
function we_tag_tracker($attribs, $content){
	if ($GLOBALS["we_doc"]->InWebEdition) {
		return "";
	}
	$type = we_getTagAttribute("type", $attribs, "standard");
	$ssl = we_getTagAttribute("ssl", $attribs, "", true);
	$websitename = we_getTagAttribute("websitename", $attribs, $_SERVER['SERVER_NAME']);
	$trackname = we_getTagAttribute("trackname", $attribs);

	if ($trackname == "WE_PATH") {
		if (isset($_REQUEST['we_objectID'])) {
			$trackname = "/object" . id_to_path($_REQUEST['we_objectID'], OBJECT_FILES_TABLE);
		} else {
			$trackname = $GLOBALS["WE_MAIN_DOC"]->Path;
		}
	} else
		if ($trackname == "WE_TITLE") {
			$trackname = $GLOBALS["WE_MAIN_DOC"]->getElement("Title");
		}

	if (!defined("WE_TRACKER_DIR")) {
		define("WE_TRACKER_DIR", "/pageLogger");
	}

	$trackerurl = ($ssl ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . WE_TRACKER_DIR;

	if ($type == 'standard') {
		return '<!-- pageLogger Code BEGIN -->
<script type="text/javascript" src="' . $trackerurl . '/scripts/picmodejs.js"></script>
<script type="text/javascript">
<!--
_my_stat_write(\'' . $websitename . '\',\'' . $trackerurl . '\'' . ($trackname ? (",'" . addslashes(
				$trackname) . "'") : "") . ');
//-->
</script>
<noscript>
<img width="1" height="1"  alt="" src="' . $trackerurl . '/connector.php?' . $websitename . '&amp;mode=NOSCRIPT' . ($trackname ? ("&amp;trackname=" . rawurlencode(
				$trackname)) : "") . '" />
</noscript>
<!-- pageLogger Code END -->
';
	} else
		if ($type == 'robot') {
			include ($_SERVER["DOCUMENT_ROOT"] . WE_TRACKER_DIR . "/spidertracker.php");
			@logspider($websitename);
		} else
			if ($type == 'fileserver') {
				@include_once ($_SERVER["DOCUMENT_ROOT"] . WE_TRACKER_DIR . "/service/fileserver.php");
			} else
				if ($type == 'downloads') {
					@include_once ($_SERVER["DOCUMENT_ROOT"] . WE_TRACKER_DIR . "/includes/showcat.inc.php");
					$category = we_getTagAttribute("category", $attribs);
					$order = we_getTagAttribute("order", $attribs, "FILETITLE");
					$desc = we_getTagAttribute("desc", $attribs, "", true, true);
					$rows = we_getTagAttribute("rows", $attribs, "10");
					showcat($category, $order, $desc ? "DESC" : "ASC", $rows, $websitename);
				}
}?>
