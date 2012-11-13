<?php

/**
 * webEdition CMS
 *
 * $Rev$
 * $Author$
 * $Date$
 *
 * This source is part of webEdition CMS. webEdition CMS is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionCMS/License.txt
 *
 * @category   webEdition
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

we_html_tools::protect();
@set_time_limit(360);

if(isset($_REQUEST['cmd'])){

	if(($_REQUEST['cmd'] == 'export' || $_REQUEST['cmd'] == 'import') && isset($_SESSION['weS']['weBackupVars'])){
		$_steps = explode(',', weBackup::backupSteps);

		if(isset($_REQUEST['reload']) && $_REQUEST['reload']){
			$_key = array_search($_SESSION['weS']['weBackupVars']['backup_steps'], $_steps);

			if($_key > 0){
				$_SESSION['weS']['weBackupVars']['backup_steps'] = $_steps[$_key - 1];
				if($_SESSION['weS']['weBackupVars']['backup_log']){
					weBackupUtil::addLog('Backup step reduced to ' . $_SESSION['weS']['weBackupVars']['backup_steps']);
				}

				print 'Backup step reduced to ' . $_SESSION['weS']['weBackupVars']['backup_steps'] . '
							Reload...';
				flush();
			}



			if($_key < 1){
				if(!isset($_SESSION['weS']['weBackupVars']['retry'])){
					$_SESSION['weS']['weBackupVars']['retry'] = 1;
				} else{
					$_SESSION['weS']['weBackupVars']['retry']++;
				}

				if($_SESSION['weS']['weBackupVars']['retry'] > 10){
					$_SESSION['weS']['weBackupVars']['retry'] = 1;
					print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('backup', '[error_timeout]'), we_message_reporting::WE_MESSAGE_ERROR));
					exit();
				}
			}
		} elseif(!FAST_BACKUP){
			$_pref = getPref('BACKUP_STEPS');
			if($_SESSION['weS']['weBackupVars']['backup_steps'] < $_pref){
				$_key = array_search($_SESSION['weS']['weBackupVars']['backup_steps'], $_steps);
				$_SESSION['weS']['weBackupVars']['backup_steps'] = $_steps[$_key + 1];
				if($_SESSION['weS']['weBackupVars']['backup_log']){
					weBackupUtil::addLog('Backup step increased to ' . $_SESSION['weS']['weBackupVars']['backup_steps']);
				}
			}
		}
	}

	switch($_REQUEST['cmd']){

		case 'export':

			if(!isset($_SESSION['weS']['weBackupVars']) || empty($_SESSION['weS']['weBackupVars'])){

				$_SESSION['weS']['weBackupVars'] = array();

				if(weBackupPreparer::prepareExport() === true){
					weBackupUtil::addLog('Start backup export');
					weBackupUtil::addLog('Export to server: ' . ($_SESSION['weS']['weBackupVars']['options']['export2server'] ? 'yes' : 'no'));
					weBackupUtil::addLog('Export to local: ' . ($_SESSION['weS']['weBackupVars']['options']['export2send'] ? 'yes' : 'no'));
					weBackupUtil::addLog('File name: ' . $_SESSION['weS']['weBackupVars']['backup_file']);
					weBackupUtil::addLog('Use compression: ' . ($_SESSION['weS']['weBackupVars']['options']['compress'] ? 'yes (' . $_SESSION['weS']['weBackupVars']['options']['compress'] . ')' : 'no'));
					weBackupUtil::addLog('Export external files: ' . ($_SESSION['weS']['weBackupVars']['options']['backup_extern'] ? 'yes' : 'no'));
					weBackupUtil::addLog('Backup steps: ' . $_SESSION['weS']['weBackupVars']['backup_steps']);
				} else{
					weBackupUtil::writeLog();
					die('No write permissions!');
				}

				$description = g_l('backup', '[working]');
			} else if(isset($_SESSION['weS']['weBackupVars']['extern_files']) && count($_SESSION['weS']['weBackupVars']['extern_files']) > 0){
				if(($fh = fopen($_SESSION['weS']['weBackupVars']['backup_file'], 'ab'))){
					if(FAST_BACKUP){
						$_SESSION['weS']['weBackupVars']['backup_steps'] = 5;
					}
					do{
						$start = microtime(true);
						for($i = 0; $i < $_SESSION['weS']['weBackupVars']['backup_steps']; $i++){

							$file_to_export = array_pop($_SESSION['weS']['weBackupVars']['extern_files']);
							if(!empty($file_to_export)){
								if($_SESSION['weS']['weBackupVars']['backup_log']){
									weBackupUtil::addLog('Exporting file ' . $file_to_export);
								}
								weBackupUtil::exportFile($file_to_export, $fh);
							}
						}
					} while(FAST_BACKUP ? weBackup::limitsReached('', microtime(true) - $start) : false);
					fclose($fh);
				}
				$description = g_l('backup', '[external_backup]');
			} else{
				if(FAST_BACKUP){
					$_SESSION['weS']['weBackupVars']['backup_steps'] = 5;
				}
				$oldDesc = $oldPercent = '';
				do{
					$start = microtime(true);

					if(weBackupExport::export($_SESSION['weS']['weBackupVars']['backup_file'], $_SESSION['weS']['weBackupVars']['offset'], $_SESSION['weS']['weBackupVars']['row_counter'], $_SESSION['weS']['weBackupVars']['backup_steps'], $_SESSION['weS']['weBackupVars']['options']['backup_binary'], $_SESSION['weS']['weBackupVars']['backup_log'], $_SESSION['weS']['weBackupVars']['handle_options']['versions_binarys']) === false){
						// force end
						$_SESSION['weS']['weBackupVars']['row_counter'] = $_SESSION['weS']['weBackupVars']['row_count'];
						break;
					}

					$description = weBackupUtil::getDescription($_SESSION['weS']['weBackupVars']['current_table'], 'export');
					$percent = weBackupUtil::getExportPercent();
					if($oldPercent != $percent || $oldDesc != $description){
						print we_html_element::jsElement(weBackupUtil::getProgressJS($percent, $description));
						flush();
						$oldPercent = $percent;
						$oldDesc = $description;
					}
					weBackupUtil::writeLog();
				} while(FAST_BACKUP ? weBackup::limitsReached(weBackupUtil::getCurrentTable(), microtime(true) - $start) : false);
			}

			if(($_SESSION['weS']['weBackupVars']['row_counter'] < $_SESSION['weS']['weBackupVars']['row_count']) || (isset($_SESSION['weS']['weBackupVars']['extern_files']) && count($_SESSION['weS']['weBackupVars']['extern_files']) > 0) || weBackupUtil::hasNextTable()){

				$percent = weBackupUtil::getExportPercent();

				print we_html_element::jsElement('
						function run(){' . weBackupUtil::getProgressJS($percent, $description) . '
							top.cmd.location = "' . WE_INCLUDES_DIR . 'we_editors/we_backup_cmd.php?cmd=export";
							top.checker.location = "' . WE_INCLUDES_DIR . 'we_editors/we_make_backup.php?pnt=checker";
						}
						run();');
			} else{

				include_once(WE_INCLUDES_PATH . 'we_exim/weXMLExImConf.inc.php');

				$_files = array();
				// export spellchecker files
				if(defined('SPELLCHECKER') && $_SESSION['weS']['weBackupVars']['handle_options']['spellchecker']){
					if($_SESSION['weS']['weBackupVars']['backup_log']){
						weBackupUtil::addLog('Exporting data for spellchecker');
					}

					$_files[] = WE_SPELLCHECKER_MODULE_DIR . 'spellchecker.conf.inc.php';
					$_dir = dir(WE_SPELLCHECKER_MODULE_PATH . 'dict');
					while(false !== ($entry = $_dir->read())) {
						if($entry == '.' || $entry == '..' || (substr($entry, -4) == '.zip') || is_dir(WE_SPELLCHECKER_MODULE_PATH . 'dict/' . $entry)){
							continue;
						}
						$_files[] = WE_SPELLCHECKER_MODULE_DIR . 'dict/' . $entry;
					}
					$_dir->close();
				}

				// export settings from the file
				if($_SESSION['weS']['weBackupVars']['handle_options']['settings']){
					weBackupUtil::addLog('Exporting settings');
					$_files[] = WE_INCLUDES_DIR . 'conf/we_conf_global.inc.php';
					$_files[] = WE_INCLUDES_DIR . 'conf/we_conf_language.inc.php';
				}

				if(!empty($_files)){
					weBackupUtil::exportFiles($_SESSION['weS']['weBackupVars']['backup_file'], $_files);
				}


				weFile::save($_SESSION['weS']['weBackupVars']['backup_file'], $GLOBALS['weXmlExImFooter'], 'ab');

				//compress file
				if(!empty($_SESSION['weS']['weBackupVars']['options']['compress']) && !isset($_SESSION['weS']['weBackupVars']['compression_done'])){

					if($_SESSION['weS']['weBackupVars']['backup_log']){
						weBackupUtil::addLog('Compressing...');
					}

					if($_SESSION['weS']['weBackupVars']['protect']){
						weFile::save($_SESSION['weS']['weBackupVars']['backup_file'] . '.gz', $GLOBALS['weXmlExImProtectCode']);
					}

					$_SESSION['weS']['weBackupVars']['backup_file'] = weFile::compress($_SESSION['weS']['weBackupVars']['backup_file'], 'gzip', '', true, 'ab');

					if($_SESSION['weS']['weBackupVars']['backup_file'] === false){
						weBackupUtil::addLog('Fatal error: compression failed!');
						print we_html_element::jsElement(weBackupUtil::getProgressJS(100, g_l('backup', "[error]")) . '
						top.checker.location = "' . HTML_DIR . 'white.html";
						alert("' . g_l('backup', '[error_compressing_backup]') . '");
						');
						unset($_SESSION['weS']['weBackupVars']);
						exit();
					}
					$_SESSION['weS']['weBackupVars']['compression_done'] = 1;
					$_SESSION['weS']['weBackupVars']['filename'] = basename($_SESSION['weS']['weBackupVars']['backup_file']);
				}

				if($_SESSION['weS']['weBackupVars']['protect'] && substr($_SESSION['weS']['weBackupVars']['filename'], -4) != ".php"){
					$_SESSION['weS']['weBackupVars']['filename'] .= '.php';
				}

				//copy the file to right location
				if($_SESSION['weS']['weBackupVars']['options']['export2server'] == 1){
					$_backup_filename = $_SERVER['DOCUMENT_ROOT'] . BACKUP_DIR . 'data/' . $_SESSION['weS']['weBackupVars']['filename'];

					if($_SESSION['weS']['weBackupVars']['backup_log']){
						weBackupUtil::addLog('Move file to ' . $_backup_filename);
					}

					if($_SESSION['weS']['weBackupVars']['options']['export2send'] == 0){
						rename($_SESSION['weS']['weBackupVars']['backup_file'], $_backup_filename);
						$_SESSION['weS']['weBackupVars']['backup_file'] = $_backup_filename;
					} else{
						copy($_SESSION['weS']['weBackupVars']['backup_file'], $_backup_filename);
					}
				}

				if($_SESSION['weS']['weBackupVars']['options']['export2send'] == 1){
					we_util_File::insertIntoCleanUp($_SESSION['weS']['weBackupVars']['backup_file'], time() + 8 * 3600); //8h
				}

				print we_html_element::jsElement(weBackupUtil::getProgressJS(100, g_l('backup', "[finished]")) .
						'top.body.setLocation("' . WE_INCLUDES_DIR . 'we_editors/we_make_backup.php?pnt=body&step=2");
						if(top.checker != "undefined"){
							top.checker.location("' . HTML_DIR . 'white.html");
						}');

				if($_SESSION['weS']['weBackupVars']['backup_log']){
					weBackupUtil::addLog('Backup export finished');
				}
			}

			weBackupUtil::writeLog();

			break;

		case 'import':

			if(!isset($_SESSION['weS']['weBackupVars']) || empty($_SESSION['weS']['weBackupVars'])){

				if(weBackupPreparer::prepareImport() === true){

					if($_SESSION['weS']['weBackupVars']['options']['compress'] && !weFile::hasGzip()){
						$_err = weBackupPreparer::getErrorMessage();
						unset($_SESSION['weS']['weBackupVars']);
						print $_err;
						exit();
					}


					if($_SESSION['weS']['weBackupVars']['backup_log']){
						weBackupUtil::addLog('Start backup import');
						weBackupUtil::addLog('File name: ' . $_SESSION['weS']['weBackupVars']['backup_file']);
						weBackupUtil::addLog('Format: ' . $_SESSION['weS']['weBackupVars']['options']['format']);
						weBackupUtil::addLog('Use compression: ' . ($_SESSION['weS']['weBackupVars']['options']['compress'] ? 'yes' : 'no'));
						weBackupUtil::addLog('Import external files: ' . ($_SESSION['weS']['weBackupVars']['options']['backup_extern'] ? 'yes' : 'no'));
					}
				} else{

					$_err = weBackupPreparer::getErrorMessage();

					weBackupUtil::writeLog();
					unset($_SESSION['weS']['weBackupVars']);
					print $_err;
					exit();
				}

				$description = g_l('backup', '[working]');
			} else if(isset($_SESSION['weS']['weBackupVars']['files_to_delete']) && count($_SESSION['weS']['weBackupVars']['files_to_delete']) > 0){
				for($i = 0; $i < $_SESSION['weS']['weBackupVars']['backup_steps']; $i++){
					$file_to_delete = array_pop($_SESSION['weS']['weBackupVars']['files_to_delete']);
					if(is_dir($file_to_delete)){
						@rmdir($file_to_delete);
					} else{
						@unlink($file_to_delete);
					}
				}
				$description = g_l('backup', '[delete_old_files]');
			} else{
				if($_SESSION['weS']['weBackupVars']['options']['format'] == 'xml'){
					weBackupImport::import($_SESSION['weS']['weBackupVars']['backup_file'], $_SESSION['weS']['weBackupVars']['offset'], $_SESSION['weS']['weBackupVars']['backup_steps'], $_SESSION['weS']['weBackupVars']['options']['compress'], $_SESSION['weS']['weBackupVars']['encoding'], $_SESSION['weS']['weBackupVars']['backup_log']
					);
				} else{
					weBackupImportSql::import($_SESSION['weS']['weBackupVars']['backup_file'], $_SESSION['weS']['weBackupVars']['offset'], $_SESSION['weS']['weBackupVars']['backup_steps'], $_SESSION['weS']['weBackupVars']['options']['compress'], $_SESSION['weS']['weBackupVars']['encoding'], $_SESSION['weS']['weBackupVars']['backup_log']
					);
				}

				$description = weBackupUtil::getDescription($_SESSION['weS']['weBackupVars']['current_table'], 'import');
			}

			if(($_SESSION['weS']['weBackupVars']['offset'] < $_SESSION['weS']['weBackupVars']['offset_end']) ||
				(isset($_SESSION['weS']['weBackupVars']['files_to_delete']) && count($_SESSION['weS']['weBackupVars']['files_to_delete']))
			){

				$percent = weBackupUtil::getImportPercent();


				print we_html_element::jsElement('
						function run(){' . weBackupUtil::getProgressJS($percent, $description) . '
							top.cmd.location = "' . WE_INCLUDES_DIR . 'we_editors/we_backup_cmd.php?cmd=import";
							top.checker.location = "' . WE_INCLUDES_DIR . 'we_editors/we_recover_backup.php?pnt=checker";
						}

						run();
						');
			} else{

				// perform update
				$updater = new weBackupUpdater();
				$updater->doUpdate();

				if($_SESSION['weS']['weBackupVars']['options']['format'] == 'sql'){
					weBackupImportSql::delBackupTable();
				}

				if(is_file($_SERVER['DOCUMENT_ROOT'] . BACKUP_DIR . 'tmp/' . $_SESSION['weS']['weBackupVars']['backup_file'])){
					unlink($_SERVER['DOCUMENT_ROOT'] . BACKUP_DIR . 'tmp/' . $_SESSION['weS']['weBackupVars']['backup_file']);
				}

				// reload user prefs
				$_SESSION['prefs'] = getHash('SELECT * FROM ' . PREFS_TABLE, $DB_WE);
				$exprefs = getHash('SELECT * FROM ' . PREFS_TABLE . ' WHERE userID = ' . intval($_SESSION['user']['ID']), $DB_WE);
				if(is_array($exprefs) && (isset($exprefs['userID']) && $exprefs['userID'] != 0) && !empty($exprefs)){
					$_SESSION['prefs'] = $exprefs;
				}

				print we_html_element::jsElement('
						top.checker.location = "' . HTML_DIR . 'white.html";
						var op = top.opener.top.makeFoldersOpenString();
						top.opener.top.we_cmd("load", top . opener . top . treeData . table);
						' . we_main_headermenu::getMenuReloadCode() . '
						top.busy.location = "' . WE_INCLUDES_DIR . 'we_editors/we_recover_backup.php?pnt=busy&operation_mode=busy&current_description=' . g_l('backup', '[finished]') . '&percent=100";
						' . ( $_SESSION['weS']['weBackupVars']['options']['rebuild'] ?
							'top.cmd.location = "' . WE_INCLUDES_DIR . 'we_editors/we_recover_backup.php?pnt=cmd&operation_mode=rebuild";' :
							'top.body.location = "' . WE_INCLUDES_DIR . 'we_editors/we_recover_backup.php?pnt=body&step=4&temp_filename=' . $_SESSION['weS']['weBackupVars']['backup_file'] . '";'
						) . weBackupUtil::getProgressJS(100, g_l('backup', '[finished]')));

				if($_SESSION['weS']['weBackupVars']['backup_log']){
					weBackupUtil::addLog('Backup import finished');
				}
			}

			weBackupUtil::writeLog();

			break;

		case 'rebuild':
			print we_html_element::jsElement('
						top.opener.top.openWindow("' . WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=rebuild&step=2&btype=rebuild_all&responseText=' . g_l('backup', "[finished_success]") . '", "rebuildwin", -1, -1, 600, 130, 0, true);
						setTimeout("top.close();", 300);
						');
			break;

		default:
	}
}

