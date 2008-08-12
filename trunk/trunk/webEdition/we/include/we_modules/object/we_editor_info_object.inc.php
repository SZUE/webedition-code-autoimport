<?php

// +----------------------------------------------------------------------+
// | webEdition                                                           |
// +----------------------------------------------------------------------+
// | PHP version 4.1.0 or greater                                         |
// +----------------------------------------------------------------------+
// | Copyright (c) 2000 - 2007 living-e AG                                |
// +----------------------------------------------------------------------+
//


include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."we_html_tools.inc.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."we_language/".$GLOBALS["WE_LANGUAGE"]."/we_editor_info.inc.php");
include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_classes/html/we_multibox.inc.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_language/" . $GLOBALS["WE_LANGUAGE"] . "/modules/users.inc.php");

htmlTop();

$parts = array();

$_html = '<div class="weMultiIconBoxHeadline" style="margin-bottom:5px;">ID</div>' .
	'<div style="margin-bottom:10px;">' . ($GLOBALS['we_doc']->ID ?  $GLOBALS['we_doc']->ID : "-") . '</div>';

$_html .= '<div class="weMultiIconBoxHeadline" style="padding-bottom:5px;">'.$GLOBALS["l_we_editor_info"]["content_type"].'</div>' .
	'<div style="margin-bottom:10px;">' . $GLOBALS["l_we_editor_info"][$GLOBALS["we_doc"]->ContentType] .'</div>';


array_push($parts, array(	"headline"=>"",
							"html"=>$_html,
							"space"=>140,
							"icon" => "meta.gif"
						)
			);



$_html = '<div class="weMultiIconBoxHeadline" style="padding-bottom:5px;">'.$GLOBALS["l_we_editor_info"]["creation_date"].'</div>' .
	'<div style="margin-bottom:10px;">' . date($GLOBALS["l_we_editor_info"]["date_format"], $GLOBALS["we_doc"]->CreationDate) .'</div>';




if($GLOBALS["we_doc"]->CreatorID){
	$GLOBALS["DB_WE"]->query("SELECT First,Second,username FROM " . USER_TABLE . " WHERE ID=".$GLOBALS["we_doc"]->CreatorID);
	if ($GLOBALS["DB_WE"]->next_record()) {
		$_html .= '<div class="weMultiIconBoxHeadline" style="padding-bottom:5px;">'.$GLOBALS["l_users"]["created_by"].'</div>' .
			'<div style="margin-bottom:10px;">' . $GLOBALS["DB_WE"]->f("First").' '.$GLOBALS["DB_WE"]->f("Second").' ('.$GLOBALS["DB_WE"]->f("username").')' .'</div>';
	}
}

$_html .= '<div class="weMultiIconBoxHeadline" style="padding-bottom:5px;">'.$GLOBALS["l_we_editor_info"]["changed_date"].'</div>' .
	'<div style="margin-bottom:10px;">' . date($GLOBALS["l_we_editor_info"]["date_format"], $GLOBALS["we_doc"]->ModDate) .'</div>';


if($GLOBALS["we_doc"]->ModifierID){
	$GLOBALS["DB_WE"]->query("SELECT First,Second,username FROM " . USER_TABLE . " WHERE ID=".$GLOBALS["we_doc"]->ModifierID);
	if ($GLOBALS["DB_WE"]->next_record()) {
		$_html .= '<div class="weMultiIconBoxHeadline" style="padding-bottom:5px;">'.$GLOBALS["l_users"]["changed_by"].'</div>' .
			'<div style="margin-bottom:10px;">' . $GLOBALS["DB_WE"]->f("First").' '.$GLOBALS["DB_WE"]->f("Second").' ('.$GLOBALS["DB_WE"]->f("username").')' .'</div>';

	}
}


array_push($parts, array(	"headline"=>"",
						"html"=>$_html,
						"space"=>140,
						"icon" => "cal.gif"
					)
		);


?>
<?php print STYLESHEET; ?>
	</head>
	<body class="weEditorBody">
<?php
print we_multiIconBox::getJS();
print we_multiIconBox::getHTML("","100%",$parts,30,"",-1,"","",false);
?>
	</body>
</html>