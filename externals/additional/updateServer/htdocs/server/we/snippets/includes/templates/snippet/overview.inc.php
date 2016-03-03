<?php
if($_SESSION['clientImportType'] == "detail"){
	$AvailableImports = downloadSnippet::getDetailImports();
} else {
	$AvailableImports = downloadSnippet::getMasterImports();
}

function getButtonForTemplates($name, $onClick, $disabled = false){

	$Button = 'we_button::create_button(
					"' . $name . '",
					"javascript:' . $onClick . '",
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

$JavaScript = '
var imports = [];
var previews = [];
var previewId = \'\'
var previewIndex = 0
function addImport(id) {
	imports[imports.length] = id;
	previews[id] = [];
}
function addPreview(id, imageSrc, width, height, text) {
	myImport = {};
	temp = new Image();
	temp.src = imageSrc;
	myImport[\'src\'] = imageSrc;
	myImport[\'width\'] = width;
	myImport[\'height\'] = height;
	myImport[\'text\'] = text;
	previews[id][previews[id].length] = myImport;
}

function showPreview(id) {
	previewId = id;
	previewIndex = -1;
	nextPreview();
}

function getPreview() {
	top.document.getElementById(\'leWizardPreviewImage\').src = previews[previewId][previewIndex][\'src\'];
	top.document.getElementById(\'leWizardPreviewImage\').height = previews[previewId][previewIndex][\'height\'];
	top.document.getElementById(\'leWizardPreviewImage\').width = previews[previewId][previewIndex][\'width\'];
	top.document.getElementById(\'leWizardPreview\').style.height = (parseInt(previews[previewId][previewIndex][\'height\']) + 95) + \'px\';
	top.document.getElementById(\'leWizardPreview\').style.width = previews[previewId][previewIndex][\'width\'];
	top.document.getElementById(\'leWizardPreviewText\').innerHTML = previews[previewId][previewIndex][\'text\'];
	top.document.getElementById(\'leWizardPreview\').style.marginTop = (-1)*(previews[previewId][previewIndex][\'height\']/2)-50;
	top.document.getElementById(\'leWizardPreview\').style.marginLeft = (-1)*(previews[previewId][previewIndex][\'width\']/2)-5;
	top.document.getElementById(\'leWizardContent\').style.overflow = \'hidden\';
	top.document.getElementById(\'leWizardPreview\').style.zIndex = \'101\';
	top.document.getElementById(\'leWizardPreviewContainer\').style.zIndex = \'100\';
	top.document.getElementById(\'leWizardPreview\').style.display = \'block\';
	top.document.getElementById(\'leWizardPreviewContainer\').style.display = \'block\';
	if(previews[previewId].length == previewIndex + 1) {
		top.weButton.disable(\'direction_right\');
	} else {
		top.weButton.enable(\'direction_right\');
	}
	if(previewIndex == 0) {
		top.weButton.disable(\'direction_left\');
	} else {
		top.weButton.enable(\'direction_left\');
	}
}

function nextPreview() {
	if(previews[previewId].length >= previewIndex + 1) {
		previewIndex++;
	}
	getPreview();
}

function backPreview() {
	if(previewIndex > 0) {
		previewIndex--;
	}
	getPreview();
}

function hidePreview() {
	top.document.getElementById(\'leWizardPreview\').style.zIndex = \'2\';
	top.document.getElementById(\'leWizardPreviewContainer\').style.zIndex = \'1\';
	top.document.getElementById(\'leWizardPreview\').style.display = \'none\';
	top.document.getElementById(\'leWizardPreviewContainer\').style.display = \'none\';
	top.document.getElementById(\'leWizardContent\').style.overflow = \'auto\';
	top.document.getElementById(\'leWizardPreviewImage\').src = \'/webEdition/images/pixel.gif\';
}

function setSelected(id) {
	for(i = 0; i < imports.length; i++) {
		if(imports[i] == id) {
			top.document.getElementById(\'imports_\' + imports[i]).className = \'cellselected\';
			top.document.getElementById(\'import\').value = id;
		} else if(imports[i] != id) {
			top.document.getElementById(\'imports_\' + imports[i]).className = \'cell\';
		}
	}
}';


$Output = "<input type=\"hidden\" name=\"import\" id=\"import\" value=\"" . (isset($_SESSION['clientSelectedImport']) ? $_SESSION['clientSelectedImport'] : "") . "\">"
	. "<table class=\"table\">";

$size = sizeof($AvailableImports);
for($i = 0; $i < $size; $i++){
	$Import = array_shift($AvailableImports);

	// Open div
	if($i % 2 == 0){
		$Output .= "<tr>";
	}

	$Output .= '<td valign="top" class="cell" id="imports_' . $Import['ID'] . '" onmouseover="if(top.document.getElementById(\'import\').value!=\'' . $Import['ID'] . '\') { document.getElementById(\'imports_' . $Import['ID'] . '\').className=\'cellover\'; }" onmouseout="if(top.document.getElementById(\'import\').value==\'' . $Import['ID'] . '\') { document.getElementById(\'imports_' . $Import['ID'] . '\').className=\'cellselected\'; } else { document.getElementById(\'imports_' . $Import['ID'] . '\').className=\'cell\'; }" onclick="top.frames[\'leLoadFrame\'].setSelected(\'' . $Import['ID'] . '\');">'
		. '<table width="100%" class="defaultfont">';

	if($Import['Title'] != ""){
		$Output .= '<tr>'
			. '<td colspan="2" align="left"><b>' . $Import['Title'] . '</b></td>'
			. '</tr>';
	}

	if($Import['Image']['Src'] != ""){
		$Output .= '<tr>'
			. '<td colspan="2" align="left"><img class="screenshot" src="' . $Import['Image']['Src'] . '" width="' . $Import['Image']['Width'] . '" height="' . $Import['Image']['Height'] . '" border="0" alt="' . $Import['Title'] . '"></td>'
			. '</tr>';
	}


	$Output .= '<tr>'
		. '<td colspan="2" valign="top" align="left" style="height: 40px;"><div style="height: 40px; padding: 2px; overflow: auto; background-color: #F1F6FB;">' . ($Import['Description'] != "" ? $Import['Description'] : "") . '</div></td>'
		. '</tr>'
		. '<tr>'
		. '<td width="50%" align="left">~~~ . ' . getButtonForTemplates('preview', 'top.frames[\'leLoadFrame\'].showPreview(\'' . $Import['ID'] . '\');', (sizeof($Import['Preview']) > 0 ? false : true)) . ' . ~~~</td>'
		. '<td width="50%" align="right">~~~ . ' . getButtonForTemplates('select', 'top.frames[\'leLoadFrame\'].setSelected(\'' . $Import['ID'] . '\');') . ' . ~~~</td>'
		. '</tr>'
		. '</table>'
		. '</td>';

	$JavaScript .= 'top.frames["leLoadFrame"].addImport("' . $Import['ID'] . '");';
	if(is_array($Import['Preview']) && !empty($Import['Preview'])){
		foreach($Import['Preview'] as $key => $value){
			$id = $Import['ID'];
			$src = (isset($value['Src']) && $value['Src'] != "") ? $value['Src'] : "";
			$width = (isset($value['Width']) && $value['Width'] != "") ? $value['Width'] : "";
			$height = (isset($value['Height']) && $value['Height'] != "") ? $value['Height'] : "";
			$text = (isset($value['Description']) && $value['Description'] != "") ? $value['Description'] : "";
			$JavaScript .= 'top.frames["leLoadFrame"].addPreview("' . $id . '", "' . $src . '", "' . $width . '", "' . $height . '", "' . $text . '");';
		}
	}

	// Close DIV
	if($i % 2 == 0 && empty($AvailableImports)){
		$Output .= "<td></td>"
			. "</tr>"
			. "<tr>"
			. "<td class=\"cellspacer\"></td>"
			. "<td class=\"cellspacer\"></td>"
			. "</tr>";
	} elseif($i % 2 == 1){
		$Output .= "</tr>"
			. "<tr>"
			. "<td class=\"cellspacer\"></td>"
			. "<td class=\"cellspacer\"></td>"
			. "</tr>";
	}
}
$Output .= "</table>";

if(isset($_SESSION['clientSelectedImport'])){
	$JavaScript .= 'top.frames[\'leLoadFrame\'].setSelected(\'' . $_SESSION['clientSelectedImport'] . '\');';
}

$Output = '"' . str_replace('~~~', '"', str_replace('***', "'", str_replace('"', '\"', $Output))) . '"';
$JavaScript = '"' . str_replace('~~~', '"', str_replace('***', "'", str_replace('"', '\"', $JavaScript))) . '"';

$Code = <<<CODE
<?php

\$this->setContent({$Output});
\$Template->addJavascript({$JavaScript});

return LE_WIZARDSTEP_NEXT;

?>
CODE;

$liveUpdateResponse['Type'] = 'executeOnline';
$liveUpdateResponse['Code'] = $Code;

