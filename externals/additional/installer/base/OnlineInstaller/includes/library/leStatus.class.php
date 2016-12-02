<?php

class leStatus{

	static function get(&$OnlineInstaller, $id, $Wizard = null, $Step = null, $ShowMoreComponents = true){

		$StatusBar = "<ul id=\"{$id}Bar\">";

		$NextWizardStyle = ($Wizard != null ? "FinishedStep" : "UpcomingStep");

		// wizardnames/stepnames for the progress on left side
		foreach($OnlineInstaller->Wizards as $_wizard){

			if($Wizard == $_wizard->Name){
				$WizardStyle = "ActiveStep";
				$NextWizardStyle = "UpcomingStep";
			} else {
				$WizardStyle = $NextWizardStyle;
			}

			$StatusBar .= "<li id=\"liWizard_{$_wizard->Name}\" class=\"{$id}{$WizardStyle}\">" . $GLOBALS['lang']['Wizard'][$_wizard->Name]['title']
				. "<ul id=\"ulWizard_{$_wizard->Name}\" class=\"{$id}{$WizardStyle}\">";


			$Steps = $_wizard->WizardSteps;
			if(!empty($Steps)){
				$NextStepStyle = ($Step != null ? "FinishedStep" : "UpcomingStep");

				foreach($Steps as $_step){
					if($Step == $_step->Name){
						$StepStyle = "ActiveStep";
						$NextStepStyle = "UpcomingStep";
						$NextWizardStyle = "UpcomingStep";
					} else {
						$StepStyle = $NextStepStyle;
					}

					if($_step->ShowInStatusBar){
						$Attribute = "";
						if($_step->IterationStep){
							$Attribute = " iterationStep=\"true\"";
						}
						$StatusBar .= "<li$Attribute id=\"liWizardStep_" . $_wizard->Name . "__" . $_step->Name . "\" class=\"{$id}{$StepStyle}\"" . (count($Steps) <= 1 ? " style=\"display:none\"" : "") . ">" . $_step->Language['title'] . "</li>";
					}
				}
			}
			$StatusBar .= "</ul></li>";
		}

		if($ShowMoreComponents){
			$StatusBar .= "<li id=\"replaceableWizardStep\" class=\"{$id}UpcomingStep\">" . $GLOBALS['lang']['Template']['moreComponentsToCome'] . "</li>";
		}

		$StatusBar .= "</ul>";
		return $StatusBar;
	}

}
