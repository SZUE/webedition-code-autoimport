<?php
$installAbleLanguages = $GLOBALS['updateServerTemplateData']['installAbleLanguages'];
$installAbleBetaLanguages = $GLOBALS['updateServerTemplateData']['installAbleBetaLanguages'];

$Output =	'<table class="leContentTable">'
		.	'<tr>'
		.	'<td class="defaultfont" width="50%"><strong>### . $this->Language[\'language\'] . ###</strong></td>'
		.	'<td class="defaultfont" width="25%" align="center"><strong>### . $this->Language[\'system\'] . ###</strong></td>'
		.	'<td class="defaultfont" width="25%" align="center"><strong>### . $this->Language[\'additional\'] . ###</strong></td>'
		.	'</tr>';


if(isset($_SESSION['clientSyslng']) && !in_array($_SESSION['clientSyslng'], $installAbleLanguages)) {
	unset($_SESSION['clientSyslng']);

}

$SPk1= array_search('Spanish',$installAbleLanguages);
if ($SPk1 !==false){
	unset($installAbleLanguages[$SPk1]);	
};
$SPk2= array_search('French',$installAbleLanguages);
if ($SPk2!==false){
unset($installAbleLanguages[$SPk2]);	
};
$SPk3= array_search('Polish',$installAbleLanguages);
;
if ($SPk3!==false){
unset($installAbleLanguages[$SPk3]);	
};
$SPk4= array_search('Russian',$installAbleLanguages);
if ($SPk4!==false){
unset($installAbleLanguages[$SPk4]);	
};
sort($installAbleLanguages);
if(!isset($_SESSION['clientSyslng'])) {
	if($_SESSION['clientLng'] == "de" && in_array("Deutsch", $installAbleLanguages)) {
		$_SESSION['clientSyslng'] = "Deutsch";
		
	} elseif(in_array("English", $installAbleLanguages)) {
		$_SESSION['clientSyslng'] = "English";
		
	}
	
}

for ($i=0; $i<sizeof($installAbleLanguages); $i++) {

	$onClickJs	=	"
					for(i=0; i<" . sizeof($installAbleLanguages) . "; i++) {
						document.getElementById('le_extraLanguages['+i+']').disabled=false;
					}
					document.getElementById('le_extraLanguages[" . $i . "]').disabled = this.checked;";

	$Selected	=	(		isset($_SESSION['clientSyslng'])
						&&	$_SESSION['clientSyslng'] == $installAbleLanguages[$i]
					?
						true
					:
						(		!isset($_SESSION['clientSyslng'])
							&&	$i == 0
						?
							true
						:
							false
						)
					);

	$DefaultRadio = 'leCheckbox::get(
						"le_defaultLanguage",
						"' . $installAbleLanguages[$i] . '",
						array(
							"onClick"	=> "' . str_replace("\n", "", str_replace("\r\n", "\n", $onClickJs)) . '",
							"id"		=> "le_defaultLanguage_' . $i . '",
						),
						"",
						"' . $Selected . '",
						"radio"
					)';
	$DefaultRadio = str_replace('"', "###", $DefaultRadio);
	$DefaultRadio = str_replace("'", "***", $DefaultRadio);

	$Disabled =	(		isset($_SESSION['clientSyslng'])
					&&	$_SESSION['clientSyslng'] == $installAbleLanguages[$i]
				?
					"array('disabled'=>'disabled')"
				:	(		!isset($_SESSION['clientSyslng'])
						&&	$i == 0
					?
						"array('disabled'=>'disabled')"
					:
						"array()"
					)
				);

	$Selected	=	(		isset($_SESSION['clientDesiredLanguages'])
						&&	in_array($installAbleLanguages[$i], $_SESSION['clientDesiredLanguages'])
						&&	$installAbleLanguages[$i] != $_SESSION['clientSyslng']
					?
						true
					:
						false
					);

	$AdditionalCheckbox = 'leCheckbox::get(
								"le_extraLanguages[' . $i . ']",
								"' . $installAbleLanguages[$i] . '",
								' . $Disabled . ',
								"",
								"' . $Selected . '"
							)';
	$AdditionalCheckbox = str_replace('"', "###", $AdditionalCheckbox);
	$AdditionalCheckbox = str_replace("'", "***", $AdditionalCheckbox);

	$Output	.=	'<tr>'
			.	'<td><label for="le_defaultLanguage_' . $i . '">' . $installAbleLanguages[$i] . (in_array($installAbleLanguages[$i],$installAbleBetaLanguages)? ' <font color="red">[beta]</font>' :'') .'</label></td>'
			.	'<td align="center">### . ' . $DefaultRadio . ' . ###</td>'
			.	'<td align="center">### . ' . $AdditionalCheckbox . ' . ###</td>'
			.	'</tr>';

}
$Output .= '</table>';

$Output = '"' . str_replace('###', '"',  str_replace('***', "'", str_replace('"', '\"', $Output))) . '"';

$Code = <<<CODE
<?php

\$this->setHeadline(\$this->Language['headline']);
\$this->setContent(\$this->Language['content'] . {$Output});
return LE_STEP_NEXT;

?>
CODE;

$liveUpdateResponse['Type'] = 'executeOnline';
$liveUpdateResponse['Code'] = $Code;

?>