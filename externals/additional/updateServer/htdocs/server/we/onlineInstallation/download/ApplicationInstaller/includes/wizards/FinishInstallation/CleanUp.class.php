<?php

	class CleanUp extends leStep {

		var $EnabledButtons = array();


		function execute(&$Template = '') {

			$this->setHeadline($this->Language['headline']);

			
			$liveUpdateFnc = new liveUpdateFunctions();

				
			if(!$liveUpdateFnc->deleteDir(LE_INSTALLER_PATH)) {
				$message = $this->Language['delete_failed'];
				
			} else {
				$message = $this->Language['content'];
				
			}
			$Button = leButton::get("openWebEdition", $this->Language['openWebEdition'], "javascript:top.location.replace('/webEdition/index.php');", 150, 22, "", false, false);
			
			
			$Content = <<<EOF
<p>
{$message}<br />
<br />
<div align="center" class="defaultfont">
{$Button}
</div><div style="margin-top:20px;">Diese webEdition Version wurde ermöglicht durch die Arbeit des gemeinnützigen webEdition e.V. Unterstützen Sie die kostenlose und freiwillige Arbeit der Vereins- und Community-Mitglieder. 
<br>Ermöglichen Sie durch Ihre Spende, dass:<ul>
<li>der webEdition e.V. professionelle Entwickler einstellen kann</li>
<li>die Beseitigung von Fehlern sowie die Entwicklung<br>
neuer Features beschleunigt wird</li>
<li>die Weiterentwicklung von webEdition langfristig<br>
gesichert wird</li></ul><form target="_blank" action="https://www.paypal.com/cgi-bin/webscr" method="post">
                  <input type="hidden" name="cmd" value="_s-xclick">
                  <input type="hidden" name="hosted_button_id" value="BERPPPT588RAE">
                  <input type="image" src="https://www.paypalobjects.com/de_DE/DE/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="Jetzt einfach, schnell und sicher online spenden – mit PayPal.">
                  <img alt="" border="0" src="https://www.paypalobjects.com/de_DE/i/scr/pixel.gif" width="1" height="1">
                </form></div>
EOF;
			$this->setContent($Content);
			
			$Javascript = <<<EOF
top.openWebEdition_mouse_event = "";
top.openWebEdition_enabled = true;
EOF;

			$Template->addJavascript($Javascript);

			return LE_STEP_NEXT;

		}


		function check(&$Template = '') {
			
			return true;

		}


	}

?>