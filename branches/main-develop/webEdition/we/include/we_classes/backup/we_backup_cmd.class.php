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
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
abstract class we_backup_cmd{

	public static function getHTML(){
		if(function_exists('apache_setenv')){
			apache_setenv('no-gzip', 1);
		}
		ini_set('zlib.output_compression', 0);

		$cmd = we_base_request::_(we_base_request::STRING, 'cmd');
		if(!$cmd){
			t_e('called without command');
			exit();
		}

		switch($cmd){
			case 'export':
			case 'import':
				if(isset($_SESSION['weS']['weBackupVars'])){
					$last = $_SESSION['weS']['weBackupVars']['limits']['requestTime'];
					$_SESSION['weS']['weBackupVars']['limits']['requestTime'] = (isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] :
						//we don't have the time of the request, assume some time is already spent.
						time() - 3);

					if(we_base_request::_(we_base_request::BOOL, 'reload')){
						$tmp = $_SESSION['weS']['weBackupVars']['limits']['requestTime'] - $last;
						t_e('Backup caused reload', $last, $_SESSION['weS']['weBackupVars']['limits'], $tmp);
						$tmp -= 4;
						if($tmp > 0 && $tmp < $_SESSION['weS']['weBackupVars']['limits']['exec']){
							$_SESSION['weS']['weBackupVars']['limits']['exec'] = $tmp;
						}

						if(!isset($_SESSION['weS']['weBackupVars']['retry'])){
							$_SESSION['weS']['weBackupVars']['retry'] = 1;
						} else {
							++$_SESSION['weS']['weBackupVars']['retry'];
						}

						if($_SESSION['weS']['weBackupVars']['retry'] > 10 || $_SESSION['weS']['weBackupVars']['options']['compress'] != we_backup_util::NO_COMPRESSION){//in case of compression the file can't be used
							$_SESSION['weS']['weBackupVars']['retry'] = 1;
							$jsCmd = new we_base_jsCmd();
							$jsCmd->addMsg(g_l('backup', '[error_timeout]'), we_base_util::WE_MESSAGE_ERROR);
							echo we_html_tools::getHtmlTop('', '', '', $jsCmd->getCmds(), we_html_element::htmlBody());
							exit();
						}
					}
					$_SESSION['weS']['weBackupVars']['limits']['lastMem'] = 0;

					echo we_html_tools::getHtmlTop('', '', '', we_html_element::jsScript(JS_DIR . 'backup_wizard.js', '', ['id' => 'loadVarBackup_wizard',
							'data-backup' => setDynamicVar([
								'reload' => (we_base_request::_(we_base_request::INT, 'reload', 0) + 1),
								'mode' => (we_base_request::_(we_base_request::STRING, 'cmd') == we_backup_wizard::RECOVER ? 'import' : 'export'),
								'reloadTimer' => (min($_SESSION['weS']['weBackupVars']['limits']['exec'], 32) * 1000) + 5000 //wait extra 5 secs
						])]), we_html_element::htmlBody());
				}
		}

		switch($cmd){
			case 'export':
				self::export();
				we_backup_util::writeLog();
				session_write_close();
				break;
			case 'import':
				self::import();
				break;
			case 'rebuild':
				echo we_html_tools::getHtmlTop('', '', '', we_html_element::jsScript(JS_DIR . 'backup_wizard.js') . we_base_jsCmd::singleCmd('rebuild'), we_html_element::htmlBody());
				break;

			default:
		}
	}

	private static function logExportStart(){
		we_backup_util::limitsReached('', 1);

		we_backup_util::addLog('Start backup export');
		we_backup_util::addLog('Export to server: ' . ($_SESSION['weS']['weBackupVars']['options']['export2server'] ? 'yes' : 'no'));
		we_backup_util::addLog('Export to local: ' . ($_SESSION['weS']['weBackupVars']['options']['export2send'] ? 'yes' : 'no'));
		we_backup_util::addLog('File name: ' . $_SESSION['weS']['weBackupVars']['backup_file']);
		we_backup_util::addLog('Use compression: ' . ($_SESSION['weS']['weBackupVars']['options']['compress'] != we_backup_util::NO_COMPRESSION ? 'yes (' . $_SESSION['weS']['weBackupVars']['options']['compress'] . ')' : 'no'));
		we_backup_util::addLog('Export external files: ' . ($_SESSION['weS']['weBackupVars']['options']['backup_extern'] ? 'yes' : 'no'));
		we_backup_util::addLog('Backup steps: FAST_BACKUP');
		we_backup_util::writeLog();
	}

	private static function exportExtern(){
		$description = g_l('backup', '[external_backup]');
		$percent = 0;
		if(($fh = $_SESSION['weS']['weBackupVars']['open']($_SESSION['weS']['weBackupVars']['backup_file'], 'ab'))){
			$_SESSION['weS']['weBackupVars']['backup_steps'] = 2;
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
				we_backup_util::writeLog();
			} while(!empty($_SESSION['weS']['weBackupVars']['extern_files']) && we_backup_util::limitsReached('', microtime(true) - $start));
			$_SESSION['weS']['weBackupVars']['close']($fh);
		}
		return [$description, $percent];
	}

	private static function export(){
		if(!isset($_SESSION['weS']['weBackupVars']) || empty($_SESSION['weS']['weBackupVars'])){
			$_SESSION['weS']['weBackupVars'] = [];

			if(we_backup_preparer::prepareExport() === true){
				self::logExportStart();
			} else {
				t_e('No write permissions!');
				return;
			}

			$description = g_l('backup', '[working]');
		} elseif(isset($_SESSION['weS']['weBackupVars']['extern_files']) && !empty($_SESSION['weS']['weBackupVars']['extern_files'])){
			list($description, $percent) = self::exportExtern();
		} else {
			$_SESSION['weS']['weBackupVars']['backup_steps'] = 10;
			$fh = $_SESSION['weS']['weBackupVars']['open']($_SESSION['weS']['weBackupVars']['backup_file'], 'ab');

			do{
				$start = microtime(true);
				for($i = 0; $i < $_SESSION['weS']['weBackupVars']['backup_steps']; $i++){

					$description = we_backup_util::getDescription($_SESSION['weS']['weBackupVars']['current_table'], 'export');
					$percent = we_backup_util::getExportPercent();

					if(we_backup_export::export($fh, $_SESSION['weS']['weBackupVars']['offset'], $_SESSION['weS']['weBackupVars']['row_counter'], $_SESSION['weS']['weBackupVars']['backup_steps'], $_SESSION['weS']['weBackupVars']['options']['backup_binary'], $_SESSION['weS']['weBackupVars']['backup_log'], $_SESSION['weS']['weBackupVars']['handle_options']['versions_binarys']) === false){
// force end
						$_SESSION['weS']['weBackupVars']['row_counter'] = $_SESSION['weS']['weBackupVars']['row_count'];
						break 2;
					}
				}
				we_backup_util::writeLog();
			} while(we_backup_util::limitsReached($_SESSION['weS']['weBackupVars']['current_table'], microtime(true) - $start));
			$_SESSION['weS']['weBackupVars']['close']($fh);
		}
		if(($_SESSION['weS']['weBackupVars']['row_counter'] < $_SESSION['weS']['weBackupVars']['row_count']) || (isset($_SESSION['weS']['weBackupVars']['extern_files']) && !empty($_SESSION['weS']['weBackupVars']['extern_files']) ) || we_backup_util::hasNextTable()){
			$percent = we_backup_util::getExportPercent();
			we_backup_util::addLog('Issuing next request.');
			echo we_html_tools::getHtmlTop('', '', '', we_html_element::jsScript(JS_DIR . 'backup_wizard.js'), we_html_element::htmlBody(['onload' => "run('export'," . $percent . ",'" . $description . "');"]));
			return;
		}

		$files = [];
// export spellchecker files
		/* if(defined('SPELLCHECKER') && $_SESSION['weS']['weBackupVars']['handle_options']['spellchecker']){
		  we_backup_util::addLog('Exporting data for spellchecker');

		  $files[] = WE_SPELLCHECKER_MODULE_DIR . 'spellchecker.conf.inc.php';
		  $dir = dir(WE_SPELLCHECKER_MODULE_PATH . 'dict');
		  while(false !== ($entry = $dir->read())){
		  if($entry === '.' || $entry === '..' || (substr($entry, -4) === '.zip') || is_dir(WE_SPELLCHECKER_MODULE_PATH . 'dict/' . $entry)){
		  continue;
		  }
		  $files[] = WE_SPELLCHECKER_MODULE_DIR . 'dict/' . $entry;
		  }
		  $dir->close();
		  } */

// export settings from the file
		if($_SESSION['weS']['weBackupVars']['handle_options']['settings']){
			we_backup_util::addLog('Exporting settings');
			$files = array_merge($files, we_backup_util::getSettingsFiles(false));
		}

		if($_SESSION['weS']['weBackupVars']['handle_options']['hooks']){
			we_backup_preparer::getFileList($files, WE_INCLUDES_PATH . 'we_hook/custom_hooks');
		}

		if($_SESSION['weS']['weBackupVars']['handle_options']['customTags']){
			we_backup_preparer::getFileList($files, WE_INCLUDES_PATH . 'we_tags/custom_tags');
			we_backup_preparer::getFileList($files, WE_INCLUDES_PATH . 'weTagWizard/we_tags/custom_tags');
		}

		if($files){
			we_backup_util::exportFiles($_SESSION['weS']['weBackupVars']['backup_file'], $files);
		}
		we_backup_util::writeLog();

		we_base_file::save($_SESSION['weS']['weBackupVars']['backup_file'], we_backup_util::weXmlExImFooter, 'ab', $_SESSION['weS']['weBackupVars']['options']['compress']);

		if($_SESSION['weS']['weBackupVars']['protect'] && substr($_SESSION['weS']['weBackupVars']['filename'], -4) != ".php"){
			$_SESSION['weS']['weBackupVars']['filename'] .= '.php';
		}

//copy the file to right location
		if($_SESSION['weS']['weBackupVars']['options']['export2server'] == 1){
			$backup_filename = BACKUP_PATH . 'data/' . $_SESSION['weS']['weBackupVars']['filename'];

			we_backup_util::addLog('Move file to ' . $backup_filename);

			if($_SESSION['weS']['weBackupVars']['options']['export2send'] == 0){
				rename($_SESSION['weS']['weBackupVars']['backup_file'], $backup_filename);
				$_SESSION['weS']['weBackupVars']['backup_file'] = $backup_filename;
			} else {
				copy($_SESSION['weS']['weBackupVars']['backup_file'], $backup_filename);
			}
		}

		if($_SESSION['weS']['weBackupVars']['options']['export2send'] == 1){
			we_base_file::insertIntoCleanUp($_SESSION['weS']['weBackupVars']['backup_file'], 8 * 3600); //8h
		}

		echo we_html_tools::getHtmlTop('', '', '', we_html_element::jsScript(JS_DIR . 'backup_wizard.js'), we_html_element::htmlBody(['onload' => "backupFinished('" . g_l('backup', '[finished]') . "');"]));

		we_backup_util::addLog('Backup export finished');
	}

	private static function logImportStart(){
		we_backup_util::addLog('Start backup import');
		we_backup_util::addLog('File name: ' . $_SESSION['weS']['weBackupVars']['backup_file']);
		we_backup_util::addLog('Format: ' . $_SESSION['weS']['weBackupVars']['options']['format']);
		we_backup_util::addLog('Use compression: ' . ($_SESSION['weS']['weBackupVars']['options']['compress'] != we_backup_util::NO_COMPRESSION ? 'yes' : 'no'));
		we_backup_util::addLog('Import external files: ' . ($_SESSION['weS']['weBackupVars']['options']['backup_extern'] ? 'yes' : 'no'));
	}

	private static function setImportVars(){
		if(we_backup_preparer::prepareImport() === true){

			if($_SESSION['weS']['weBackupVars']['options']['compress'] != we_backup_util::NO_COMPRESSION && !we_base_file::hasGzip()){
				$err = we_backup_preparer::getErrorMessage();
				unset($_SESSION['weS']['weBackupVars']);
				echo we_html_tools::getHtmlTop('', '', '', $err, we_html_element::htmlBody());
				exit();
			}
			self::logImportStart();
		} else {

			$err = we_backup_preparer::getErrorMessage();

			we_backup_util::writeLog();
			unset($_SESSION['weS']['weBackupVars']);
			echo we_html_tools::getHtmlTop('', '', '', $err, we_html_element::htmlBody());
			exit();
		}
	}

	private static function import(){
		if(!isset($_SESSION['weS']['weBackupVars']) || empty($_SESSION['weS']['weBackupVars'])){
			self::setImportVars();
			$description = g_l('backup', '[working]');
		} else if(!empty($_SESSION['weS']['weBackupVars']['files_to_delete'])){
			$description = g_l('backup', '[delete_old_files]');
			do{
				$start = microtime(true);
				for($i = 0; $i < 50; ++$i){
					if(empty($_SESSION['weS']['weBackupVars']['files_to_delete'])){
						break;
					}
					we_base_file::delete(array_pop($_SESSION['weS']['weBackupVars']['files_to_delete']));
				}
				$percent = we_backup_util::getImportPercent();
			} while(!empty($_SESSION['weS']['weBackupVars']['files_to_delete']) && we_backup_util::limitsReached('', microtime(true) - $start));
		} elseif(($_SESSION['weS']['weBackupVars']['offset'] < $_SESSION['weS']['weBackupVars']['offset_end'])){
			if($_SESSION['weS']['weBackupVars']['options']['format'] !== 'xml'){
				t_e('error', 'unsupported Fileformat');
				exit();
			}
			$percent = we_backup_util::getImportPercent();
			$description = we_backup_util::getDescription($_SESSION['weS']['weBackupVars']['current_table'], 'import');
			do{
				$start = microtime(true);
				switch($_SESSION['weS']['weBackupVars']['current_table']){
					case CONTENT_TABLE:
					case NEWSLETTER_LOG_TABLE:
					case HISTORY_TABLE:
					case FORMMAIL_LOG_TABLE:
						$count = 150;
						break;
					default:
						$count = 10;
				}

				if(!we_backup_import::import($_SESSION['weS']['weBackupVars']['backup_file'], $_SESSION['weS']['weBackupVars']['offset'], $count, $_SESSION['weS']['weBackupVars']['options']['compress'] != we_backup_util::NO_COMPRESSION, $_SESSION['weS']['weBackupVars']['encoding'])){
					if($_SESSION['weS']['weBackupVars']['offset'] == 0){
						we_backup_util::addLog(sprintf('File %s not readable.', $_SESSION['weS']['weBackupVars']['backup_file']));

						//FIXME: show status as JS, check if file is readable earlier!
						exit();
					}
					break;
				}
				$percent = we_backup_util::getImportPercent();
				we_backup_util::writeLog();
			} while(we_backup_util::limitsReached('', microtime(true) - $start));
			we_backup_fileReader::closeFile();

			$description = we_backup_util::getDescription($_SESSION['weS']['weBackupVars']['current_table'], 'import');
		} elseif(($_SESSION['weS']['weBackupVars']['offset'] == $_SESSION['weS']['weBackupVars']['offset_end'])){
			if(empty($_SESSION['weS']['weBackupVars']['update'])){
				$_SESSION['weS']['weBackupVars']['update'] = we_updater::doUpdate('internal');
			} else {
				// perform update
				$_SESSION['weS']['weBackupVars']['update'] = we_updater::doUpdate($_SESSION['weS']['weBackupVars']['update']['what'], $_SESSION['weS']['weBackupVars']['update']);
			}
			if(!$_SESSION['weS']['weBackupVars']['update']){//no more update changes
				$_SESSION['weS']['weBackupVars']['offset']++;
			}
			$description = 'Update ' . (!empty($_SESSION['weS']['weBackupVars']['update']['text']) ? $_SESSION['weS']['weBackupVars']['update']['text'] : '');
		} elseif(empty($_SESSION['weS']['weBackupVars']['update'])){
			//make sure we_update is run on next request
			++$_SESSION['weS']['weBackupVars']['offset'];
		}

		if(($_SESSION['weS']['weBackupVars']['offset'] <= $_SESSION['weS']['weBackupVars']['offset_end']) ||
			(!empty($_SESSION['weS']['weBackupVars']['files_to_delete']))
		){

			we_backup_util::addLog('Issuing next request.');

			echo we_html_tools::getHtmlTop('', '', '', we_html_element::jsScript(JS_DIR . 'backup_wizard.js'), we_html_element::htmlBody(['onload' => "run('import','" . we_backup_util::getImportPercent() . "','" . $description . "');"]));
		} else {

			if(is_file(TEMP_PATH . $_SESSION['weS']['weBackupVars']['backup_file'])){
				unlink(TEMP_PATH . $_SESSION['weS']['weBackupVars']['backup_file']);
			}

// reload user prefs
			$_SESSION['prefs'] = we_users_user::readPrefs($_SESSION['user']['ID'], $GLOBALS['DB_WE']);
			$jsCmd = new we_base_jsCmd();
			$jsCmd->addCmd('importFinished', [
				'doRebuild' => $_SESSION['weS']['weBackupVars']['options']['rebuild'],
				'file' => $_SESSION['weS']['weBackupVars']['options']['rebuild'] ? $_SESSION['weS']['weBackupVars']['backup_file'] : ''
			]);
			list($cmd, $val) = we_main_headermenu::getMenuReloadCode('', true);
			$jsCmd->addCmd($cmd, $val);

			echo we_html_tools::getHtmlTop('', '', '', we_html_element::jsScript(JS_DIR . 'backup_wizard.js') . $jsCmd->getCmds(), we_html_element::htmlBody());
			we_backup_util::addLog('Backup import finished');
		}

		we_backup_util::writeLog();
	}

}
