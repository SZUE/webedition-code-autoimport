<?php

	class ChooseSnippets extends leStep {

		function execute(&$Template = '') {

			if(!$this->CheckFailed) {
				$Template->addJavascript("top.leEffect.resizeWide(554);");

			}

			return $this->executeOnline($Template, "feature", "snippetsForm");

		}


		function check(&$Template = '') {

			$Template->addJavascript("top.leEffect.resizeSmall(316);");
			return true;

		}

	}


?>