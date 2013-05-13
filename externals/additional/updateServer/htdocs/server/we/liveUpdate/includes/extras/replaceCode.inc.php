<?php

/**
 * This file contains an array with changes necessary during registration of webEdition
 * AND
 * changes needed for the onlineInstaller to downgrade this version to a demo version
 */

/*
 * changes for BETA-3900
 */

// add licensee to we_conf.inc.php
$replaceCode['we_conf']['path']['3900'] = '/webEdition/we/include/conf/we_conf.inc%s';
$replaceCode['we_conf']['needle']['3900'] = '"WE_LIZENZ","[0-9A-Za-z -]*"';
$replaceCode['we_conf']['replace']['3900'] = '"WE_LIZENZ","%s"';

// add version and uid
$replaceCode['we_version']['path']['3900'] = '/webEdition/we/include/we_version%s';
$replaceCode['we_version']['replace']['3900'] = '<?php
define("WE_VERSION","%s");
define("WE_VERSION_SUPP","%s");
define("WE_ZFVERSION","%s");
define("WE_SVNREV","%s");
define("WE_VERSION_SUPP_VERSION","%s");
define("WE_VERSION_BRANCH","%s");
define("WE_VERSION_NAME","%s");

?>';

// remove demo pop-up webEdition.php
$replaceCode['webEdition']['path']['3900'] = '/webEdition/webEdition%s';
$replaceCode['webEdition']['needle']['3900'] = 'var we_demo = true;';
$replaceCode['webEdition']['replace']['3900'] = 'var we_demo = false;';

// change menu entries
$replaceCode['menu1']['path']['3900'] = '/webEdition/we/include/java_menu/we_menu.inc%s';
$replaceCode['menu1']['needle']['3900'] = '\$we_menu\["5050000"\]\["text"\] = \$l_javaMenu\["register"\]';
$replaceCode['menu1']['replace']['3900'] = '$we_menu["5050000"]["text"] = $l_javaMenu["update"]';

$replaceCode['menu2']['path']['3900'] = '/webEdition/we/include/java_menu/we_menu.inc%s';
$replaceCode['menu2']['needle']['3900'] = '\$we_menu\["3060000"\]\["text"\] = \$l_javaMenu\["register"\]';
$replaceCode['menu2']['replace']['3900'] = '$we_menu["3060000"]["text"] = $l_javaMenu["module_installation"]';

// template savecode
$replaceCode['templateSaveCode']['path']['3900'] = '/webEdition/we/include/we_editors/we_editor.inc%s';
$replaceCode['templateSaveCode']['needle']['3900'] = '#save template2';
$replaceCode['templateSaveCode']['replace']['3900'] = <<< TemplateSaveCodeBoundary
						\$TEMPLATE_SAVE_CODE2 = true;
						\$arr = getTemplAndDocIDsOfTemplate(\$we_doc->ID, true, true);
						\$nrDocsUsedByThisTemplate = count(\$arr["documentIDs"]);
						\$nrTemplatesUsedByThisTemplate = count(\$arr["templateIDs"]);
						\$somethingNeedsToBeResaved = (\$nrDocsUsedByThisTemplate+\$nrTemplatesUsedByThisTemplate) > 0;

						if(\$_REQUEST["we_cmd"][2]) {
							//this is the second call to save_document (see next else command)
							include(\$_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_templates/we_template_save_question.inc.php"); // this includes the gui for the save question dialog
							\$we_doc->saveInSession(\$_SESSION["we_data"][\$we_transaction]); // save the changed object in session
							exit();
						} else if(!\$_REQUEST["we_cmd"][3] && \$somethingNeedsToBeResaved) {
							// this happens when the template is saved and there are documents which use the template and "automatic rebuild" is not checked!
							include(\$_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_TemplateSave.inc.php"); // this calls again we_cmd with save_document and sets we_cmd[2]
							\$we_doc->saveInSession(\$_SESSION["we_data"][\$we_transaction]); // save the changed object in session
							exit();
						} else {
							//this happens when we_cmd[3] is set and not we_cmd[2]
							if(\$we_doc->we_save()) {
								\$wasSaved = true;
								\$wasNew = (abs(\$we_doc->ID) == 0) ? true : false;
								\$we_JavaScript .= "_EditorFrame.getDocumentReference().frames[0].we_setPath('".\$we_doc->Path."', '" . \$we_doc->Text . "');\n";
								\$we_JavaScript .= "_EditorFrame.setEditorDocumentId(".\$we_doc->ID.");\n".\$we_doc->getUpdateTreeScript().";\n";// save/ rename a document
								\$we_responseText = sprintf(\$l_we_editor[\$we_doc->ContentType]["response_save_ok"],\$we_doc->Path);
								\$we_responseTextType = WE_MESSAGE_NOTICE;
								if(\$_REQUEST["we_cmd"][4]) {
									// this happens when the documents which uses the templates has to be rebuilt. (if user clicks "yes" at template save question or if automatic rebuild was set)
									if(\$somethingNeedsToBeResaved) {
										\$we_JavaScript .= 'top.toggleBusy(0);top.openWindow(\''.WEBEDITION_DIR.'we_cmd.php?we_cmd[0]=rebuild&step=2&btype=rebuild_filter&templateID='.\$we_doc->ID.'&responseText='.rawurlencode(sprintf(\$we_responseText,\$we_doc->Path)).'\',\'resave\',-1,-1,600,130,0,true);';
										\$we_responseText = '';
									}
								}
							} else {
								// we got an error while saving the template
								\$we_JavaScript = "";
								\$we_responseText = sprintf(\$l_we_editor[\$we_doc->ContentType]["response_save_notok"],\$we_doc->Path);
								\$we_responseTextType = WE_MESSAGE_ERROR;
							}
						}
TemplateSaveCodeBoundary;


?>