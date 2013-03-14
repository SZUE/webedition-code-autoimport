<?php

	class DocumentRoot extends leStep {

		function execute(&$Template = '') {

			$Name = 'le_documentRoot';
			$Value = isset($_SESSION["le_documentRoot"]) ? $_SESSION["le_documentRoot"] :  "";
			$Attribs = array(
				'size'	=> '40',
				'style'	=> 'width: 293px',
			);
			$Input = leInput::get($Name, $Value, $Attribs);

			$Content = "
<p>
	" . sprintf($this->Language['content'], "<input type=\"text\" class=\"defaultfont\" style=\"font-weight: bold; border: 0px solid #EFEFEF; background: #EFEFEF; width: 295px;\" value=\"" . htmlspecialchars($_SERVER['DOCUMENT' . '_ROOT']) . "\" title=\"" . htmlspecialchars($_SERVER['DOCUMENT' . '_ROOT']) . "\" readonly=\"readonly\" />") . "<br />
	<br />
	<b>{$this->Language['DocumentRoot']}</b><br />
	{$Input}
</p>";

			$this->setHeadline($this->Language['headline']);

			$this->setContent($Content);

			if($this->CheckFailed) {
				$Template->addJavascript("top.leForm.setFocus('le_documentRoot');");

			}

			return LE_STEP_NEXT;

		}


		function check(&$Template = '') {

			// check if DOCUMENT_ROOT is valid
			if ( isset($_REQUEST['le_documentRoot']) && $_REQUEST['le_documentRoot'] ) {

				// check if given DOCUMENT_ROOT exists
				if (!is_dir($_REQUEST["le_documentRoot"])) {
					$Template->addError($this->Language['requestNotValid']);
					return false;

				} else {
					$_SESSION['le_documentRoot'] = trim($_REQUEST["le_documentRoot"]);

				}

			} else {

				// check if detected DOCUMENT_ROOT exists:
				if (!is_dir($_SERVER['DOCUMENT' . '_ROOT']) && !is_dir($_SERVER['DOCUMENT' . '_ROOT'].'/')) {
					$Template->addError($this->Language['autoDocRootNotValid']);
					return false;

				} else {

				}

			}
			return true;

		}

	}

?>