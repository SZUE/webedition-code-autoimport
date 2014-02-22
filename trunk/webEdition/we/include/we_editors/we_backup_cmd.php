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
//make sure we will have at least an good timestamp
if(isset($_SESSION['weS']['weBackupVars']) && !empty($_SESSION['weS']['weBackupVars'])){
	we_backup_backup::limitsReached('', 1);
}

if(!isset($_REQUEST['cmd'])){
	t_e('called without command');
	exit();
}

if(($_REQUEST['cmd'] == 'export' || $_REQUEST['cmd'] == 'import') && isset($_SESSION['weS']['weBackupVars'])){
	$last = $_SESSION['weS']['weBackupVars']['limits']['requestTime'];
	$_SESSION['weS']['weBackupVars']['limits']['requestTime'] = (isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] :
			//we don't have the time of the request, assume some time is already spent.
			time() - 3);
	$_SESSION['weS']['weBackupVars']['limits']['lastMem'] = 0;

	if(isset($_REQUEST['reload']) && $_REQUEST['reload']){
		$tmp = $_SESSION['weS']['weBackupVars']['limits']['requestTime'] - $last;
		t_e('Backup caused reload', $last, $_SESSION['weS']['weBackupVars']['limits'], $tmp);
		$tmp-=4;
		if($tmp > 0 && $tmp < $_SESSION['weS']['weBackupVars']['limits']['exec']){
			$_SESSION['weS']['weBackupVars']['limits']['exec'] = $tmp;
		}

		if(!isset($_SESSION['weS']['weBackupVars']['retry'])){
			$_SESSION['weS']['weBackupVars']['retry'] = 1;
		} else {
			++$_SESSION['weS']['weBackupVars']['retry'];
		}

		if($_SESSION['weS']['weBackupVars']['retry'] > 10){
			$_SESSION['weS']['weBackupVars']['retry'] = 1;
			print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('backup', '[error_timeout]'), we_message_reporting::WE_MESSAGE_ERROR));
			exit();
		}
	}
}

switch($_REQUEST['cmd']){

	case 'export':

		if(!isset($_SESSION['weS']['weBackupVars']) || empty($_SESSION['weS']['weBackupVars'])){
			$_SESSION['weS']['weBackupVars'] = array();

			if(we_backup_preparer::prepareExport() === true){
				we_backup_backup::limitsReached('', 1);

				we_backup_util::addLog('Start backup export');
				we_backup_util::addLog('Export to server: ' . ($_SESSION['weS']['weBackupVars']['options']['export2server'] ? 'yes' : 'no'));
				we_backup_util::addLog('Export to local: ' . ($_SESSION['weS']['weBackupVars']['options']['export2send'] ? 'yes' : 'no'));
				we_backup_util::addLog('File name: ' . $_SESSION['weS']['weBackupVars']['backup_file']);
				we_backup_util::addLog('Use compression: ' . ($_SESSION['weS']['weBackupVars']['options']['compress'] != we_backup_base::NO_COMPRESSION ? 'yes (' . $_SESSION['weS']['weBackupVars']['options']['compress'] . ')' : 'no'));
				we_backup_util::addLog('Export external files: ' . ($_SESSION['weS']['weBackupVars']['options']['backup_extern'] ? 'yes' : 'no'));
				we_backup_util::addLog('Backup steps: FAST_BACKUP');
				we_backup_util::writeLog();
			} else {
				we_backup_util::writeLog();
				die('No write permissions!');
			}

			$description = g_l('backup', '[working]');
		} elseif(isset($_SESSION['weS']['weBackupVars']['extern_files']) && !empty($_SESSION['weS']['weBackupVars']['extern_files'])){
			if(($fh = $_SESSION['weS']['weBackupVars']['open']($_SESSION['weS']['weBackupVars']['backup_file'], 'ab'))){
				$_SESSION['weS']['weBackupVars']['backup_steps'] = 2;
				$description = g_l('backup', '[external_backup]');
				$oldPercent = 0;
				print we_html_element::jsElement(we_backup_util::getProgressJS(0, $description));
				flush();
				do{
					$start = microtime(true);
					for($i = 0; $i < $_SESSION['weS']['weBackupVars']['backup_steps']; $i++){

						$file_to_export = array_pop($_SESSION['weS']['weBackupVars']['extern_files']);
						if(empty($file_to_export)){
							break 2;
						}
						we_backup_util::addLog('Exporting file ' . $file_to_export);
						we_backup_util::writeLog();
						we_backup_util::exportFile($file_to_export, $fh);
					}
					$percent = we_backup_util::getExportPercent();
					if($oldPercent != $percent){
						print we_html_element::jsElement(we_backup_util::getProgressJS($percent, $description));
						flush();
						$oldPercent = $percent;
					}
					we_backup_util::writeLog();
				} while(!empty($_SESSION['weS']['weBackupVars']['extern_files']) && we_backup_backup::limitsReached('', microtime(true) - $start));
				$_SESSION['weS']['weBackupVars']['close']($fh);
			}
		} else {
			$_SESSION['weS']['weBackupVars']['backup_steps'] = 10;
			$oldPercent = 0;
			$_fh = $_SESSION['weS']['weBackupVars']['open']($_SESSION['weS']['weBackupVars']['backup_file'], 'ab');

			do{
				$start = microtime(true);
				for($i = 0; $i < $_SESSION['weS']['weBackupVars']['backup_steps']; $i++){

					$description = we_backup_util::getDescription($_SESSION['weS']['weBackupVars']['current_table'], 'export');
					$percent = we_backup_util::getExportPercent();
					if($oldPercent != $percent){
						print we_html_element::jsElement(we_backup_util::getProgressJS($percent, $description));
						flush();
						$oldPercent = $percent;
					}

					if(we_backup_export::export($_fh, $_SESSION['weS']['weBackupVars']['offset'], $_SESSION['weS']['weBackupVars']['row_counter'], $_SESSION['weS']['weBackupVars']['backup_steps'], $_SESSION['weS']['weBackupVars']['options']['backup_binary'], $_SESSION['weS']['weBackupVars']['backup_log'], $_SESSION['weS']['weBackupVars']['handle_options']['versions_binarys']) === false){
// force end
						$_SESSION['weS']['weBackupVars']['row_counter'] = $_SESSION['weS']['weBackupVars']['row_count'];
						break 2;
					}
				}
				we_backup_util::writeLog();
			} while(we_backup_backup::limitsReached(we_backup_util::getCurrentTable(), microtime(true) - $start));
			$_SESSION['weS']['weBackupVars']['close']($_fh);
		}
		if(($_SESSION['weS']['weBackupVars']['row_counter'] < $_SESSION['weS']['weBackupVars']['row_count']) || (isset($_SESSION['weS']['weBackupVars']['extern_files']) && count($_SESSION['weS']['weBackupVars']['extern_files']) > 0) || we_backup_util::hasNextTable()){

			$percent = we_backup_util::getExportPercent();

			print we_html_element::jsElement('
						function run(){' . we_backup_util::getProgressJS($percent, $description) . '
							top.cmd.location = "' . WE_INCLUDES_DIR . 'we_editors/we_backup_cmd.php?cmd=export";
							top.checker.location = "' . WE_INCLUDES_DIR . 'we_editors/we_make_backup.php?pnt=checker";
						}
						run();');
			flush();
		} else {

			$_files = array();
// export spellchecker files
			if(defined('SPELLCHECKER') && $_SESSION['weS']['weBackupVars']['handle_options']['spellchecker']){
				we_backup_util::addLog('Exporting data for spellchecker');

				$_files[] = WE_SPELLCHECKER_MODULE_DIR . 'spellchecker.conf.inc.php';
				$_dir = dir(WE_SPELLCHECKER_MODULE_PATH . 'dict');
				while(false !== ($entry = $_dir->read())){
					if($entry == '.' || $entry == '..' || (substr($entry, -4) == '.zip') || is_dir(WE_SPELLCHECKER_MODULE_PATH . 'dict/' . $entry)){
						continue;
					}
					$_files[] = WE_SPELLCHECKER_MODULE_DIR . 'dict/' . $entry;
				}
				$_dir->close();
			}

// export settings from the file
			if($_SESSION['weS']['weBackupVars']['handle_options']['settings']){
				we_backup_util::addLog('Exporting settings');
				$_files[] = WE_INCLUDES_DIR . 'conf/we_conf_global.inc.php';
				$_files[] = WE_INCLUDES_DIR . 'conf/we_conf_language.inc.php';
			}

			if(!empty($_files)){
				we_backup_util::exportFiles($_SESSION['weS']['weBackupVars']['backup_file'], $_files);
			}
			we_backup_util::writeLog();


			we_base_file::save($_SESSION['weS']['weBackupVars']['backup_file'], we_backup_backup::weXmlExImFooter, 'ab', $_SESSION['weS']['weBackupVars']['options']['compress']);

			if($_SESSION['weS']['weBackupVars']['protect'] && substr($_SESSION['weS']['weBackupVars']['filename'], -4) != ".php"){
				$_SESSION['weS']['weBackupVars']['filename'] .= '.php';
			}

//copy the file to right location
			if($_SESSION['weS']['weBackupVars']['options']['export2server'] == 1){
				$_backup_filename = $_SERVER['DOCUMENT_ROOT'] . BACKUP_DIR . 'data/' . $_SESSION['weS']['weBackupVars']['filename'];

				we_backup_util::addLog('Move file to ' . $_backup_filename);

				if($_SESSION['weS']['weBackupVars']['options']['export2send'] == 0){
					rename($_SESSION['weS']['weBackupVars']['backup_file'], $_backup_filename);
					$_SESSION['weS']['weBackupVars']['backup_file'] = $_backup_filename;
				} else {
					copy($_SESSION['weS']['weBackupVars']['backup_file'], $_backup_filename);
				}
			}

			if($_SESSION['weS']['weBackupVars']['options']['export2send'] == 1){
				we_util_File::insertIntoCleanUp($_SESSION['weS']['weBackupVars']['backup_file'], time() + 8 * 3600); //8h
			}

			print we_html_element::jsElement(we_backup_util::getProgressJS(100, g_l('backup', "[finished]")) .
					'top.body.setLocation("' . WE_INCLUDES_DIR . 'we_editors/we_make_backup.php?pnt=body&step=2");
							top.cmd.location = "' . HTML_DIR . 'white.html";
						if(top.checker != "undefined"){
							if(typeof top.checker.setLocation == "function") {
								top.checker.setLocation("' . HTML_DIR . 'white.html");
							}else{
								top.checker.location = "' . HTML_DIR . 'white.html";
							}
						}');

			we_backup_util::addLog('Backup export finished');
		}

		we_backup_util::writeLog();
		break;

	case 'import':

		if(!isset($_SESSION['weS']['weBackupVars']) || empty($_SESSION['weS']['weBackupVars'])){

			if(we_backup_preparer::prepareImport() === true){

				if($_SESSION['weS']['weBackupVars']['options']['compress'] != we_backup_base::NO_COMPRESSION && !we_base_file::hasGzip()){
					$_err = we_backup_preparer::getErrorMessage();
					unset($_SESSION['weS']['weBackupVars']);
					print $_err;
					exit();
				}

				we_backup_util::addLog('Start backup import');
				we_backup_util::addLog('File name: ' . $_SESSION['weS']['weBackupVars']['backup_file']);
				we_backup_util::addLog('Format: ' . $_SESSION['weS']['weBackupVars']['options']['format']);
				we_backup_util::addLog('Use compression: ' . ($_SESSION['weS']['weBackupVars']['options']['compress']!=we_backup_base::NO_COMPRESSION ? 'yes' : 'no'));
				we_backup_util::addLog('Import external files: ' . ($_SESSION['weS']['weBackupVars']['options']['backup_extern'] ? 'yes' : 'no'));
			} else {

				$_err = we_backup_preparer::getErrorMessage();

				we_backup_util::writeLog();
				unset($_SESSION['weS']['weBackupVars']);
				print $_err;
				exit();
			}

			$description = g_l('backup', '[working]');
		} else if(isset($_SESSION['weS']['weBackupVars']['files_to_delete']) && !empty($_SESSION['weS']['weBackupVars']['files_to_delete'])){
			$description = g_l('backup', '[delete_old_files]');
			print we_html_element::jsElement(we_backup_util::getProgressJS(0, $description));
			flush();
			$oldPercent = 0;
			do{
				$start = microtime(true);
				for($i = 0; $i < 50; ++$i){
					if(empty($_SESSION['weS']['weBackupVars']['files_to_delete'])){
						break;
					}
					we_base_file::delete(array_pop($_SESSION['weS']['weBackupVars']['files_to_delete']));
				}
				$percent = we_backup_util::getImportPercent();
				if($oldPercent != $percent){
					print we_html_element::jsElement(we_backup_util::getProgressJS($percent, $description));
					flush();
					$oldPercent = $percent;
				}
			} while(!empty($_SESSION['weS']['weBackupVars']['files_to_delete']) && we_backup_backup::limitsReached('', microtime(true) - $start));
		} elseif(($_SESSION['weS']['weBackupVars']['offset'] < $_SESSION['weS']['weBackupVars']['offset_end'])){
			if($_SESSION['weS']['weBackupVars']['options']['format'] == 'xml'){
				$oldPercent = 0;
				$percent = we_backup_util::getImportPercent();
				$description = we_backup_util::getDescription($_SESSION['weS']['weBackupVars']['current_table'], 'import');
				print we_html_element::jsElement(we_backup_util::getProgressJS($percent, $description));
				flush();
				do{
					$start = microtime(true);
					$count = ($_SESSION['weS']['weBackupVars']['current_table'] == LINK_TABLE || $_SESSION['weS']['weBackupVars']['current_table'] == CONTENT_TABLE ? 150 : 10);
					if(!we_backup_import::import($_SESSION['weS']['weBackupVars']['backup_file'], $_SESSION['weS']['weBackupVars']['offset'], $count, $_SESSION['weS']['weBackupVars']['options']['compress']!=we_backup_base::NO_COMPRESSION, $_SESSION['weS']['weBackupVars']['encoding'])){
						if($_SESSION['weS']['weBackupVars']['offset'] == 0){
							we_backup_util::addLog(sprintf('File %s not readable.', $_SESSION['weS']['weBackupVars']['backup_file']));

							//FIXME: show status as JS, check if file is readable earlier!
							exit();
						}
						break;
					}
					$percent = we_backup_util::getImportPercent();
					if($oldPercent != $percent){
						$description = we_backup_util::getDescription($_SESSION['weS']['weBackupVars']['current_table'], 'import');
						print we_html_element::jsElement(we_backup_util::getProgressJS($percent, $description));
						flush();
						$oldPercent = $percent;
					}
					we_backup_util::writeLog();
				} while(we_backup_backup::limitsReached('', microtime(true) - $start));
				we_backup_fileReader::closeFile();
			} else {
				we_backup_importSql::import($_SESSION['weS']['weBackupVars']['backup_file'], $_SESSION['weS']['weBackupVars']['offset'], $_SESSION['weS']['weBackupVars']['backup_steps'], $_SESSION['weS']['weBackupVars']['options']['compress'], $_SESSION['weS']['weBackupVars']['encoding'], $_SESSION['weS']['weBackupVars']['backup_log']);
			}

			$description = we_backup_util::getDescription($_SESSION['weS']['weBackupVars']['current_table'], 'import');
		} else {
			//make sure we_update is run on next request
			++$_SESSION['weS']['weBackupVars']['offset'];
		}

		if(($_SESSION['weS']['weBackupVars']['offset'] <= $_SESSION['weS']['weBackupVars']['offset_end']) ||
			(isset($_SESSION['weS']['weBackupVars']['files_to_delete']) && !empty($_SESSION['weS']['weBackupVars']['files_to_delete']))
		){

			print we_html_element::jsElement('
						function run(){' . we_backup_util::getProgressJS(we_backup_util::getImportPercent(), $description) . '
							top.cmd.location = "' . WE_INCLUDES_DIR . 'we_editors/we_backup_cmd.php?cmd=import";
							top.checker.location = "' . WE_INCLUDES_DIR . 'we_editors/we_recover_backup.php?pnt=checker";
						}

						run();
						');
		} else {

// perform update
			$updater = new we_updater();
			$updater->doUpdate();

			if($_SESSION['weS']['weBackupVars']['options']['format'] == 'sql'){
				we_backup_importSql::delBackupTable();
			}

			if(is_file($_SERVER['DOCUMENT_ROOT'] . BACKUP_DIR . 'tmp/' . $_SESSION['weS']['weBackupVars']['backup_file'])){
				unlink($_SERVER['DOCUMENT_ROOT'] . BACKUP_DIR . 'tmp/' . $_SESSION['weS']['weBackupVars']['backup_file']);
			}

// reload user prefs
			$_SESSION['prefs'] = we_users_user::readPrefs($_SESSION['user']['ID'], $DB_WE);

			print we_html_element::jsElement('
						top.checker.location = "' . HTML_DIR . 'white.html";
						var op = top.opener.top.makeFoldersOpenString();
						top.opener.top.we_cmd("load", top . opener . top . treeData . table);
						' . we_main_headermenu::getMenuReloadCode() . '
						top.busy.location = "' . WE_INCLUDES_DIR . 'we_editors/we_recover_backup.php?pnt=busy&operation_mode=busy&current_description=' . g_l('backup', '[finished]') . '&percent=100";
						' . ( $_SESSION['weS']['weBackupVars']['options']['rebuild'] ?
						'top.cmd.location = "' . WE_INCLUDES_DIR . 'we_editors/we_recover_backup.php?pnt=cmd&operation_mode=rebuild";' :
						'top.body.location = "' . WE_INCLUDES_DIR . 'we_editors/we_recover_backup.php?pnt=body&step=4&temp_filename=' . $_SESSION['weS']['weBackupVars']['backup_file'] . '";'
					) . we_backup_util::getProgressJS(100, g_l('backup', '[finished]')));

			we_backup_util::addLog('Backup import finished');
		}

		we_backup_util::writeLog();

		break;

	case 'rebuild':
		print we_html_element::jsElement('
						top.opener.top.openWindow("' . WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=rebuild&step=2&btype=rebuild_all&responseText=' . g_l('backup', "[finished_success]") . '", "rebuildwin", -1, -1, 600, 130, 0, true);
						setTimeout("top.close();", 300);
						');
		break;

	default:
}
