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
function getCheckBoxForModule($Name, $Value, $Text, $onClickJs='') {

	$registeredModules = $GLOBALS['updateServerTemplateData']['registeredModules'];

	$Selected	=	(
						(		isset($_SESSION['clientDesiredModules'])
							&&	in_array($Value, $_SESSION['clientDesiredModules'])
						) || (
							in_array($Value, $registeredModules)
						)
					?
						true
					:
						false
					);

	$Text2	=	(	in_array($Value, $registeredModules)
				?
					'<i>' . $Text . '</i>'
				:
					$Text
				);

	if(in_array($Value, $registeredModules)) {
		$onClickJs = 'this.checked=true;alert(***' . sprintf($GLOBALS["lang"]["installApplication"]["module_must_be_reinstalled"], $Value) . '***);';
	}

	$CheckboxValue = trim(preg_replace("/\(.*\)/", "", $Text));

	$Checkbox = 'leCheckbox::get(
					"le_modules[' . $Value . ']",
					"' . $CheckboxValue . '",
					array(
						"onClick"	=> "' . str_replace("\n", "", str_replace("\r\n", "\n", $onClickJs)) . '",
						"id"		=> "le_modules[' . $Name . ']"
					),
					"' . $Text2 . '",
					"' . $Selected . '"
				)';

	$Checkbox = str_replace('"', "###", $Checkbox);
	$Checkbox = str_replace("'", "***", $Checkbox);

	return $Checkbox;

}


$Output	=	'<table class="leContentTable">'
		.	'<tr>'
		.	'<td><b>### . $this->Language[\'modules\'] . ###</b></td>'
		.	'</tr>';

$tableDependentModules = '';
$tableProModules = '';

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
				$jsRequiredModules .= 'top.frames[\'leLoadFrame\'].addRequiredModule("' . $moduleKey . '", "' . $depModule . '");';

			}

			$dependentModuleTexts[] = $existingModules[$depModule]['text'];
			if ( !(in_array($depModule, $_SESSION['clientInstalledModules']) || isset($installAbleModules[$depModule])) ) {
				$showEntry = false;

			}

		}

		if ($showEntry) {
			sort($dependentModuleTexts);
			$tableDependentModules .= '<tr><td colspan="2">### . ' . getCheckBoxForModule($moduleKey, $moduleKey, $module . ' (' . implode(', ', $dependentModuleTexts) . ')', 'top.frames[\'leLoadFrame\'].clickCheckBox(\'' . $moduleKey . '\');') . ' . ###</td></tr>';

		}

	} else {
		if ($existingModules[$moduleKey]['grade'] == 'normal') {
			$Output .=	'<tr>'
					.	'<td>### . ' . getCheckBoxForModule($moduleKey, $moduleKey, $module, 'top.frames[\'leLoadFrame\'].clickCheckBox(\'' . $moduleKey . '\');', $moduleKey) . ' . ###</td>'
					.	'</tr>';
			if (isset($proModules[$moduleKey]) && isset( $installAbleModules[$proModules[$moduleKey]] )) {
				if (!in_array($moduleKey, $_SESSION['clientInstalledModules'])) {
					$jsRequiredModules .= 'top.frames[\'leLoadFrame\'].addRequiredModule("' . $proModules[$moduleKey] . '", "' . $moduleKey . '");';

				}
				$tableProModules	.=	'<tr>'
									.	'<td>### . ' . getCheckBoxForModule($proModules[$moduleKey], $proModules[$moduleKey], $existingModules[$proModules[$moduleKey]]['text'], 'top.frames[\'leLoadFrame\'].clickCheckBox(\'' . $proModules[$moduleKey] . '\');') . ' . ###</td>'
									.	'</tr>';

			}

		}

	}

}


$Output .=	($tableProModules ? ('<tr><td>&nbsp;</td></tr><tr><td><b>### . $this->Language[\'pro_modules\'] . ###</b></td></tr>' . $tableProModules) : '') . '';
$Output .=	($tableDependentModules ? ('<tr><td>&nbsp;</td></tr><tr><td><b>### . $this->Language[\'depending_modules\'] . ###</b></td></tr>' . $tableDependentModules) : '') . '';
$Output	.=	'</table>';



$Javascript =	'
	dependentModules = [];
	requiredModules = [];

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
		if ( box = top.document.getElementById("le_modules[" + boxId + "]") ) {
			if (box.checked) {
				checkRequiredModules(boxId);

			} else {
				uncheckDependentModules(boxId);

			}

		}

	}


	function uncheckDependentModules(modulekey) {
		if (dependentModules[modulekey]) {
			for (i=0; i<dependentModules[modulekey].length; i++) {
				if (elem = top.document.getElementById("le_modules[" + dependentModules[modulekey][i] + "]")) {
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
				if (elem = top.document.getElementById("le_modules[" + requiredModules[modulekey][i] + "]")) {
					if (!elem.checked) {
						elem.checked = true;

					}

				}

			}

		}

	}
';


$Output = '"' . str_replace('###', '"',  str_replace('***', "'", str_replace('"', '\"', $Output))) . '"';
$Javascript = '"' . str_replace('###', '"',  str_replace('***', "'", str_replace('"', '\"', $Javascript))) . '"';

$Code = <<<CODE
<?php

\$this->setHeadline(\$this->Language['headline']);
\$this->setContent(\$this->Language['content'] . {$Output});
\$Template->addJavascript({$Javascript});
return LE_STEP_NEXT;

?>
CODE;

$liveUpdateResponse['Type'] = 'executeOnline';
$liveUpdateResponse['Code'] = $Code;

?>