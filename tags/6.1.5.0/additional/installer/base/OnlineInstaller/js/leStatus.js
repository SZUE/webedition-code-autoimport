function leStatus() {}


leStatus.getChildNodes = function(myElem, filter) {

	if (!myElem) {
		var _childs = [];
		return _childs;
	}


	var childs = myElem.childNodes;

	if (filter) {

		var _childs = [];

		for (var i = 0; i < childs.length; i++) {

			if(childs[i].nodeName == filter) {

				_childs.push(childs[i]);
			}
		}
		return _childs;

	} else {
		return childs;

	}

}


leStatus.update = function(id, wizard, step) {
	var ulWizards = id + "Bar";

	var liWizard = "liWizard_" + wizard;
	var liWizardStep = "liWizardStep_" + wizard + "__" + step;


	// all wizards up to {wizard} are done
	var liWizards = leStatus.getChildNodes(document.getElementById(ulWizards), "LI");

	var setClassWizardStep = id + "FinishedStep";
	var setClassWizard = id + "FinishedStep";

	for (var i = 0; i < liWizards.length; i++) {

		var ulWizardStepsArray = leStatus.getChildNodes(liWizards[i], "UL");
		var liWizardSteps = leStatus.getChildNodes(ulWizardStepsArray[0], "LI");

		var isIterationStep = false;

		liWizards[i].className = setClassWizard

		for (var j=0; j<liWizardSteps.length; j++) {
			isIterationStep = false;
			liWizardSteps[j].className = setClassWizardStep;

			if (liWizardSteps[j].id == liWizardStep) {
				liWizardSteps[j].className = id + "ActiveStep";

				if ( liWizardSteps[j].getAttribute("iterationStep") ) {

				} else {

				}

				liWizards[i].className = id + "ActiveStep";
				setClassWizardStep = id + "UpcomingStep";
				setClassWizard = id + "UpcomingStep";

			} else {
				liWizardSteps[j].className = setClassWizardStep;

			}

		}

	}

}
