<?php

if($_SESSION['clientImportType'] == "detail") {
	$AvailableImports = downloadSnippet::getDetailImports();

} else {
	$AvailableImports = downloadSnippet::getMasterImports();
	
}

function getButtonForTemplates($name, $onClick, $disabled = false) {
	
	$Button = 'we_button::create_button(
					"' . $name . '",
					"javascript:' . $onClick. '",
					"test", 
					120,
					22,
					"",
					"",
					' . ($disabled ? 'true' : 'false') . '
				)';

	$Button = str_replace('"', "~~~", $Button);
	$Button = str_replace("'", "***", $Button);

	return $Button;
	
}

$JavaScript	=	''
			.	'var imports = new Array();\n'
			.	'var previews = new Array();\n'
			.	'var previewId = \'\'\n'
			.	'var previewIndex = 0\n'
			.	'function addImport(id) {\n'
			.	'	imports[imports.length] = id;\n'
			.	'	previews[id] = new Array();\n'
			.	'}\n'
			.	'function addPreview(id, imageSrc, width, height, text) {\n'
			.	'	myImport = new Array();\n'
			.	'	temp = new Image();\n'
			.	'	temp.src = imageSrc;\n'
			.	'	myImport[\'src\'] = imageSrc;\n'
			.	'	myImport[\'width\'] = width;\n'
			.	'	myImport[\'height\'] = height;\n'
			.	'	myImport[\'text\'] = text;\n'
			.	'	previews[id][previews[id].length] = myImport;\n'
			.	'}\n'
			.	'\n'
			.	'function showPreview(id) {\n'
			.	'	previewId = id;\n'
			.	'	previewIndex = -1;\n'
			.	'	nextPreview();\n'
			.	'}\n'
			.	'\n'
			.	'function getPreview() {\n'
			.	'	top.document.getElementById(\'leWizardPreviewImage\').src = previews[previewId][previewIndex][\'src\'];\n'
			.	'	top.document.getElementById(\'leWizardPreviewImage\').height = previews[previewId][previewIndex][\'height\'];\n'
			.	'	top.document.getElementById(\'leWizardPreviewImage\').width = previews[previewId][previewIndex][\'width\'];\n'
			.	'	top.document.getElementById(\'leWizardPreview\').style.height = (parseInt(previews[previewId][previewIndex][\'height\']) + 95) + \'px\';\n'
			.	'	top.document.getElementById(\'leWizardPreview\').style.width = previews[previewId][previewIndex][\'width\'];\n'
			.	'	top.document.getElementById(\'leWizardPreviewText\').innerHTML = previews[previewId][previewIndex][\'text\'];\n'
			.	'	top.document.getElementById(\'leWizardPreview\').style.marginTop = (-1)*(previews[previewId][previewIndex][\'height\']/2)-50;\n'
			.	'	top.document.getElementById(\'leWizardPreview\').style.marginLeft = (-1)*(previews[previewId][previewIndex][\'width\']/2)-5;\n'
			.	'	top.document.getElementById(\'leWizardContent\').style.overflow = \'hidden\';\n'
			.	'	top.document.getElementById(\'leWizardPreview\').style.zIndex = \'101\';\n'
			.	'	top.document.getElementById(\'leWizardPreviewContainer\').style.zIndex = \'100\';\n'
			.	'	top.document.getElementById(\'leWizardPreview\').style.display = \'block\';\n'
			.	'	top.document.getElementById(\'leWizardPreviewContainer\').style.display = \'block\';\n'
			.	'	if(previews[previewId].length == previewIndex + 1) {\n'
			.	'		top.weButton.disable(\'direction_right\');\n'
			.	'	} else {\n'
			.	'		top.weButton.enable(\'direction_right\');\n'
			.	'	}\n'
			.	'	if(previewIndex == 0) {\n'
			.	'		top.weButton.disable(\'direction_left\');\n'
			.	'	} else {\n'
			.	'		top.weButton.enable(\'direction_left\');\n'
			.	'	}\n'
			.	'}\n'
			.	'\n'
			.	'function nextPreview() {\n'
			.	'	if(previews[previewId].length >= previewIndex + 1) {'
			.	'		previewIndex++;\n'
			.	'	}\n'
			.	'	getPreview();\n'
			.	'}\n'
			.	'\n'
			.	'function backPreview() {\n'
			.	'	if(previewIndex > 0) {'
			.	'		previewIndex--;\n'
			.	'	}\n'
			.	'	getPreview();\n'
			.	'}\n'
			.	'\n'
			.	'function hidePreview() {\n'
			.	'	top.document.getElementById(\'leWizardPreview\').style.zIndex = \'2\';\n'
			.	'	top.document.getElementById(\'leWizardPreviewContainer\').style.zIndex = \'1\';\n'
			.	'	top.document.getElementById(\'leWizardPreview\').style.display = \'none\';\n'
			.	'	top.document.getElementById(\'leWizardPreviewContainer\').style.display = \'none\';\n'
			.	'	top.document.getElementById(\'leWizardContent\').style.overflow = \'auto\';\n'
			.	'	top.document.getElementById(\'leWizardPreviewImage\').src = \'/webEdition/images/pixel.gif\';\n'
			.	'}\n'
			.	'\n'
			.	'function setSelected(id) {\n'
			.	'	for(i = 0; i < imports.length; i++) {\n'
			.	'		if(imports[i] == id) {\n'
			.	'			top.document.getElementById(\'imports_\' + imports[i]).className = \'cellselected\';\n'
			.	'			top.document.getElementById(\'import\').value = id;\n'
			.	'		} else if(imports[i] != id) {\n'
			.	'			top.document.getElementById(\'imports_\' + imports[i]).className = \'cell\';\n'
			.	'		}'
			.	'	}'
			.	'}\n';
			

$Output	=	"<input type=\"hidden\" name=\"import\" id=\"import\" value=\"" . (isset($_SESSION['clientSelectedImport']) ? $_SESSION['clientSelectedImport'] : "") . "\">"
		.	"<table class=\"table\">";
		
$size = sizeof($AvailableImports);
for($i = 0; $i < $size; $i++) {
	$Import = array_shift($AvailableImports);

	// Open div
	if($i % 2 == 0) {
		$Output .= "<tr>";
	}
	
	$Output		.=	'<td valign="top" class="cell" id="imports_' . $Import['ID'] . '" onmouseover="if(top.document.getElementById(\'import\').value!=\''. $Import['ID'] . '\') { document.getElementById(\'imports_' . $Import['ID'] . '\').className=\'cellover\'; }" onmouseout="if(top.document.getElementById(\'import\').value==\''. $Import['ID'] . '\') { document.getElementById(\'imports_' . $Import['ID'] . '\').className=\'cellselected\'; } else { document.getElementById(\'imports_' . $Import['ID'] . '\').className=\'cell\'; }" onclick="top.frames[\'leLoadFrame\'].setSelected(\'' . $Import['ID'] . '\');">'
				.	'<table width="100%" class="defaultfont">';
				
	if($Import['Title'] != "") {
		$Output	.=	'<tr>'
				.	'<td colspan="2" align="left"><b>' . $Import['Title'] . '</b></td>'
				.	'</tr>';
				
	}
				
	if($Import['Image']['Src'] != "") {
		$Output	.=	'<tr>'
				.	'<td colspan="2" align="left"><img class="screenshot" src="' . $Import['Image']['Src'] . '" width="' . $Import['Image']['Width'] . '" height="' . $Import['Image']['Height'] . '" border="0" alt="' . $Import['Title'] . '"></td>'
				.	'</tr>';
	}
				
	
	$Output	.=	'<tr>'
			.	'<td colspan="2" valign="top" align="left" style="height: 40px;"><div style="height: 40px; padding: 2px; overflow: auto; background-color: #F1F6FB;">' . ($Import['Description'] != "" ? $Import['Description'] : "") . '</div></td>'
			.	'</tr>'
			.	'<tr>'
			.	'<td width="50%" align="left">~~~ . ' . getButtonForTemplates('preview', 'top.frames[\'leLoadFrame\'].showPreview(\'' . $Import['ID'] . '\');', (sizeof($Import['Preview']) > 0 ? false : true)) . ' . ~~~</td>'
			.	'<td width="50%" align="right">~~~ . ' . getButtonForTemplates('select', 'top.frames[\'leLoadFrame\'].setSelected(\'' . $Import['ID'] . '\');') . ' . ~~~</td>'
			.	'</tr>'
			.	'</table>'
			.	'</td>';
				
	$JavaScript .= 'top.frames["leLoadFrame"].addImport("' . $Import['ID'] . '");';
	if(is_array($Import['Preview']) && sizeof($Import['Preview']) > 0) {
		foreach($Import['Preview'] as $key => $value) {
			$id = $Import['ID'];
			$src = (isset($value['Src']) && $value['Src'] != "") ? $value['Src'] : "";
			$width = (isset($value['Width']) && $value['Width'] != "") ? $value['Width'] : "";
			$height = (isset($value['Height']) && $value['Height'] != "") ? $value['Height'] : "";
			$text = (isset($value['Description']) && $value['Description'] != "") ? $value['Description'] : "";
			$JavaScript .= 'top.frames["leLoadFrame"].addPreview("' .$id . '", "' . $src . '", "' . $width . '", "' . $height . '", "' . $text . '");';
			
		}
		
	}
	
	// Close DIV
	if($i % 2 == 0 && sizeof($AvailableImports)==0) {
		$Output .= "<td></td>"
				.	"</tr>"
				.	"<tr>"
				.	"<td class=\"cellspacer\"></td>"
				.	"<td class=\"cellspacer\"></td>"
				.	"</tr>";
		
	} elseif($i % 2 == 1) {
		$Output .= "</tr>"
				.	"<tr>"
				.	"<td class=\"cellspacer\"></td>"
				.	"<td class=\"cellspacer\"></td>"
				.	"</tr>";
	}

}
$Output .= "</table>";

if(isset($_SESSION['clientSelectedImport'])) {
	$JavaScript .= 'top.frames[\'leLoadFrame\'].setSelected(\'' . $_SESSION['clientSelectedImport'] . '\');';
}

$Output = '"' . str_replace('~~~', '"',  str_replace('***', "'", str_replace('"', '\"', $Output))) . '"';
$JavaScript = '"' . str_replace('~~~', '"',  str_replace('***', "'", str_replace('"', '\"', $JavaScript))) . '"';

$Code = <<<CODE
<?php

\$this->setContent({$Output});
\$Template->addJavascript({$JavaScript});

return LE_WIZARDSTEP_NEXT;

?>
CODE;

$liveUpdateResponse['Type'] = 'executeOnline';
$liveUpdateResponse['Code'] = $Code;

?>