<?php

$existingModules = $GLOBALS['updateServerTemplateData']['existingModules'];
$installAbleModules = $GLOBALS['updateServerTemplateData']['installAbleModules'];

asort($installAbleModules);

// prepare installable modules
$proModules = array();
foreach ($installAbleModules as $moduleKey => $moduletext) {
	if ($existingModules[$moduleKey]['grade'] == 'pro') {
		$proModules[$existingModules[$moduleKey]['basismodule']] = $moduleKey;
	}
}

/**
 * returns html checkbox for webEdition
 *
 * @param sring $name
 * @param string $value
 * @param string $text
 * @param string $onclick
 * @return string
 */
function getCheckBoxForModule($name, $value, $text, $onclick='') {

	if (in_array($value, $_SESSION['clientInstalledModules'])) {

		$onclick = "$onclick;alert('" . $GLOBALS['lang']['modules']['moduleAlreadyInstalled'] . "');";
		$text = "<i>$text</i>";
	}

	return '\' . we_forms::checkbox("' . $value . '", false, "' . $name . '", "' . $text . '", false, "defaultfont", "' . $onclick . '") . \'';
}


$tableModules = '';
$tableDependentModules = '';

$jsRequiredModules = '';

foreach ($installAbleModules as $moduleKey => $module) {

	// dependent modules in extra table
	if ( isset($existingModules[$moduleKey]['dependent']) ) { // dependent modules are extra

		// check if all dependent modules are installed
		$showEntry = true;

		$dependentModules = explode(',', $existingModules[$moduleKey]['dependent']);
		$dependentModuleTexts = array();

		foreach ($dependentModules as $depModule) {

			if (!in_array($depModule, $_SESSION['clientInstalledModules'])) {
				$jsRequiredModules .= '
				addRequiredModule("' . $moduleKey . '", "' . $depModule . '");';
			}

			$dependentModuleTexts[] = $existingModules[$depModule]['text'];

			if ( !(in_array($depModule, $_SESSION['clientInstalledModules']) || isset($installAbleModules[$depModule])) ) {

				$showEntry = false;
			}
		}

		if ($showEntry) {
			sort($dependentModuleTexts);
			$tableDependentModules .= '<tr><td colspan="2">' . getCheckBoxForModule("module_$moduleKey", $moduleKey, $module . ' (' . implode(', ', $dependentModuleTexts) . ')', 'clickCheckBox(\'' . $moduleKey . '\');') . '</td></tr>';
		}
	} else {

		if ($existingModules[$moduleKey]['grade'] == 'normal') {

			$tableModules .= '<tr><td>' . getCheckBoxForModule("module_$moduleKey", $moduleKey, $module, 'clickCheckBox(\'' . $moduleKey . '\');', $moduleKey) . '</td>';
			if (isset($proModules[$moduleKey]) && isset( $installAbleModules[$proModules[$moduleKey]] )) {


				if (!in_array($moduleKey, $_SESSION['clientInstalledModules'])) {
					$jsRequiredModules .= '
				addRequiredModule("' . $proModules[$moduleKey] . '", "' . $moduleKey . '");';
				}

				$tableModules .= '<td>' . getCheckBoxForModule("module_" . $proModules[$moduleKey], $proModules[$moduleKey], $existingModules[$proModules[$moduleKey]]['text'], 'clickCheckBox(\'' . $proModules[$moduleKey] . '\');') . '</td>';
			}
			$tableModules .= '</tr>';
		}
	}
}

$tableModules = '<table class="defaultfont">
<tr>
	<td><b>' . $GLOBALS['lang']['modules']['normalModules'] . '</b></td>
	<td><b>' . $GLOBALS['lang']['modules']['proModules'] . '</b></td>
</tr>
' . $tableModules . '
' . ($tableDependentModules ?
		('<tr>
	<td><b>' . $GLOBALS['lang']['modules']['dependentModules'] . '</b></td>
</tr>
' . $tableDependentModules) : '') . '
</table>';



// build response array
$liveUpdateResponse['Type'] = 'eval';
$liveUpdateResponse['Code'] = '<?php

$we_button = new we_button();

$submitButton = $we_button->create_button("next", "javascript:document.we_form.submit();");

$head = \'
	<script type="text/javascript">

		var dependentModules = [];
		var requiredModules = [];

		function addRequiredModule(module, requiredModule) {

			if (!requiredModules[module]) {
				requiredModules[module] = [];
			}
			requiredModules[module].push(requiredModule);

			if (!dependentModules[requiredModule]) {
				dependentModules[requiredModule] = [];
			}
			dependentModules[requiredModule].push(module);
		}

		' . $jsRequiredModules . '

		//installedModules
		function clickCheckBox(boxId){

			if ( box = document.getElementById("module_" + boxId) ) {

				if (box.checked) {

					checkRequiredModules(box.value);
				} else {
					uncheckDependentModules(box.value);
				}
			}
		}

		function uncheckDependentModules(modulekey) {

			if (dependentModules[modulekey]) {

				for (i=0; i<dependentModules[modulekey].length; i++) {

					if (elem = document.getElementById("module_" + dependentModules[modulekey][i])) {
						if (elem.checked) {
							elem.checked = false;
						}
					}
				}
			}
		}

		function checkRequiredModules(modulekey) {

			if (requiredModules[modulekey]) {

				for (i=0; i<requiredModules[modulekey].length; i++) {

					if (elem = document.getElementById("module_" + requiredModules[modulekey][i])) {
						if (!elem.checked) {
							elem.checked = true;
						}
					}
				}
			}
		}
	</script>
\';

$content = \'
<form name="we_form">
' . updateUtil::getCommonFormFields('modules', 'confirmModules') . '

' . $tableModules . '

</form>

\' . $submitButton . \'

\';

print liveUpdateTemplates::getHtml("' . addslashes($GLOBALS['lang']['modules']['headline']) . '", $content, $head);
?>';

?>