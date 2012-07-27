<?php
/** Generated language file of webEdition CMS */
$l_backup=array(
	''=>'Attention! We strongly recommend you to perform an update repeat after restoring a backup from an <strong>older installation of webEdition</strong> (before 6.3.3)!',
	'backup_deleted'=>'The backup file %s has been deleted',
	'backup_form'=>'Backup from',
	'backup_log_exp'=>'The log will be saved in /webEdition/we_backup/data/lastlog.php',
	'banner_info'=>'Banner and statistics from the banner module.',
	'binary_info'=>'The binary data - images, PDFs and other documents.',
	'bzip'=>'bzip',
	'cannot_save_backup'=>'Unable to save backup file.',
	'cannot_save_tmpfile'=>'Unable to create temporary file. Chek if you have write premissions over %s',
	'cannot_send_backup'=>'Unable to execute backup.',
	'cannot_split_file'=>'Can not prepare file `%s` for restore!',
	'cannot_split_file_ziped'=>'The file has been compressed with unsupported compression method.',
	'can_not_open_file'=>'Unable to open file `%s`.',
	'charset_warning'=>'If you encounter problems when restoring a backup, please ensure that the <strong>target system uses the same character set as the source system</strong>. This applies both to the character set of the database (collation) as well as for the character set of the user interface language!',
	'compress'=>'Compress',
	'compress_file'=>'Compress file',
	'convert_charset'=>'Attention! Using this option in an existing site can lead to total loss of all data, please follow the instruction in http://documentation.webedition.org/de/webedition/administration/charset-conversion-of-legacy-sites',
	'convert_charset_data'=>'While importing the backup, convert the site from ISO to UTF-8',
	'core_info'=>'All documents and templates.',
	'customer_import_file_found'=>'The file looks like import file with customer`s data. Please use the "Import/Export" option from the customer module (PRO) to import the data.',
	'customer_info'=>'Customers and accounts data from the customer module.',
	'decompress'=>'Decompress',
	'defaultcharset_warning'=>'<span style="color:ff0000">Attention! The standard charset is not defined.</span> For some server configurations, this can lead to problems while importing backups.!',
	'delete_entry'=>'Delete %s',
	'delete_nok'=>'The files can not be deleted!',
	'delete_old_files'=>'Delete old files...',
	'delold_confirm'=>'All existing data will be erased!\nAre you sure?',
	'delold_notice'=>'Delete existing files on server (recommended)?<br/>
All files managed by webEdition are deleted! Documents and templates will be kept in database. Your website will be back online after a full rebuild (documents + templates).',
	'del_backup_confirm'=>'Do you want to delete selected backup file?',
	'download'=>'Please click here.',
	'download_failed'=>'Either the file you requested does not exist or you are not permitted to download it.',
	'download_file'=>'Download file',
	'download_starting'=>'Download of the backup file has been started.<br/><br/>If the download does not start after 10 seconds,<br/>',
	'error'=>'Error',
	'error_compressing_backup'=>'An error occured while compressing the backup, so the backup could not be finished!',
	'error_delete'=>'The backup file can not be deleted! You should try to delete it over FTP from the /webEdition/we_backup folder.',
	'error_timeout'=>'An timeout occured while creating the backup, so the backup could not be finished!',
	'export_backup_log'=>'Create log',
	'export_banner_data'=>'Save banner data',
	'export_banner_dep'=>'You have selected the option `Save banner data`. The banner data need the documents and because of that, `Save documents and templates` has been automatically selected.',
	'export_binary_data'=>'Save binary data (images, pdfs, ...)',
	'export_binary_dep'=>'You have selected the option `Save binary data`. The binary data need the documents and because of that, `Save documents and templates` has been automatically selected.',
	'export_check_all'=>'Check all',
	'export_configuration_data'=>'Save configuration',
	'export_content'=>'Exporting content',
	'export_core_data'=>'Save documents and templates',
	'export_customer_data'=>'Save customers data',
	'export_doctypes'=>'Save document types',
	'export_export_data'=>'Save export data',
	'export_extern_data'=>'Save extern files/folders',
	'export_files'=>'Exporting files',
	'export_glossary_data'=>'Save glossary data',
	'export_indexes'=>'Save indexes',
	'export_info'=>'Data from the export module.',
	'export_links'=>'Save links',
	'export_location'=>'Specify where you want to save the backup file. If it is stored on the server, you find the file in `/webEdition/we_backup/data/`.',
	'export_location_send'=>'On local hard disk',
	'export_location_server'=>'On server',
	'export_newsletter_data'=>'Save newsletter data',
	'export_newsletter_dep'=>'You have selected the option `Save newsletter data`. The Newsletter Module needs the documents and users data and because of that, `Save documents and templates` and `Save customers data` has been automatically selected.',
	'export_object_data'=>'Save object and classes',
	'export_options'=>'Select the data that should be saved.',
	'export_prefs'=>'Save preferences',
	'export_schedule_data'=>'Save schedule data',
	'export_schedule_dep'=>'You have selected the option `Save schedule data`. The Schedule Module needs the documents and objects and because of that, `Save documents and templates` and `Save objects and classes` has been automatically selected.',
	'export_settings_data'=>'Save settings',
	'export_shop_data'=>'Save shop data',
	'export_shop_dep'=>'You have selected the option `Save shop data`. The Shop Module needs the customers data and because of that, `Save customers data` has been automatically selected.',
	'export_spellchecker_data'=>'Save spellchecker data',
	'export_step1'=>'Step 1 of 2 - Backup parameters',
	'export_step2'=>'Step 2 of 2 - Backup complete',
	'export_templates'=>'Exporting templates',
	'export_temporary_data'=>'Save temporary data',
	'export_temporary_dep'=>'You have selected the option `Save temporary data`. The temporary data need the documents and because of that, `Save documents and templates` has been automatically selected.',
	'export_title'=>'Export',
	'export_todo_data'=>'Save task/messaging data',
	'export_todo_dep'=>'You have selected the option `Save task/messaging data`. The Task/Messaging Module needs the users data and because of that, `Save user data` has been automatically selected.',
	'export_users_data'=>'Save user data',
	'export_user_data'=>'Save user data',
	'export_versions_binarys_data'=>'Save Version-Binary-Files',
	'export_versions_binarys_dep'=>'You have selected the option `Save Version-Binary-Files`. The Version-Binary-Files need the documents, objects and version data and because of that, `Save documents and templates`, `Save object and classes` and `Save version data` has been automatically selected.',
	'export_versions_data'=>'Save version data',
	'export_versions_dep'=>'You have selected the option `Save version data`. The version data need the documents, objects and version-binary-files and because of that, `Save documents and templates`, `Save object and classes` and `Save Version-Binary-Files` has been automatically selected.',
	'export_voting_data'=>'Save voting data',
	'export_workflow_data'=>'Save workflow data',
	'export_workflow_dep'=>'You have selected the option `Save workflow data`. The Workflow Module needs the documents and users data and because of that,  `Save documents and templates` and `Save workflow data` has been automatically selected.',
	'external_backup'=>'External data saving...',
	'extern'=>'Restore webEdition external files and folders',
	'extern_backup_question_exp'=>'You selected the option `Save webEdition external files and folders`. This option could take some time and may lead to some system-specific errors. Do you want to proceed anyway?',
	'extern_backup_question_exp_all'=>'You selected the option `Check all`. That also checks the option `Save webEdition external files and folders`. This option could take some time and may lead to some system-specific errors. <br/><br/>Do you want to let `Save webEdition external files and folders` be checked anyway?',
	'extern_backup_question_imp'=>'You selected the option `Restore webEdition external files and folders`. This option could take some time and may lead to some system-specific errors. Do you want to proceed anyway?',
	'extern_backup_question_imp_all'=>'You selected the option `Check all`. That also checks the option `Restore webEdition external files and folders`. This option could take some time and may lead to some system-specific errors. <br/><br/>Do you want to let `Restore webEdition external files and folders` be checked anyway?',
	'extern_exp'=>'This option could take some time and may lead to some system-specific errors.',
	'extern_files_question'=>'Save webEdition external files and folders.',
	'extern_files_size'=>'Since the maximum file size is limited to %.1f MB (%s byte) by your database settings, multiple files may be created.',
	'filename'=>'File name',
	'filename_compression'=>'Here you can give a name to the target backup file and to enable compression. The file will be compressed by using gzip compression and resulting file will have .gz extension. This option could take some time!<br/>If the backup was not successful, please try to change the settings.',
	'filename_info'=>'Enter the name of the backup file.',
	'files_not_deleted'=>'One or more files could not be erased! It is possible that they are write-protected. Erase the files by hand. The following files are effected:',
	'file_missing'=>'The backup file is missing!',
	'file_not_readable'=>'The backup file is not readable. Please check the file permissions.',
	'finished'=>'Finished',
	'finished_fail'=>'The import of backup data has not finished successfully.',
	'finished_success'=>'The import of backup data was successful.',
	'finish'=>'The backup was successfully created.',
	'finish_error'=>'Error: Unable to execute backup.',
	'finish_warning'=>'Warning: Backup completed, however some files may not be complete!',
	'format_unknown'=>'The file format is unknown!',
	'ftp_hint'=>'Attention! Use the Binary mode for the download by FTP if the backup file is zip compressed! A download in ASCII 	mode destroys the file, so that it cannot be recovered!',
	'glossary_info'=>'Data from the glossary.',
	'gzip'=>'gzip',
	'import_banner_data'=>'Restore banner data',
	'import_banner_dep'=>'You have selected the option `Restore banner data`. The banner data need the documents data and because of that, `Restore documents and templates` has been automatically selected.',
	'import_binary_data'=>'Restore binary data (images, pdfs, ...)',
	'import_binary_dep'=>'You have selected the option `Restore binary data`. The binary data need the documents data and because of that, `Restore documents and templates` has been automatically selected.',
	'import_check_all'=>'Check all',
	'import_configuration_data'=>'Restore configuration',
	'import_content'=>'Importing content',
	'import_core_data'=>'Restore documents and templates',
	'import_customer_data'=>'Restore customers data',
	'import_doctypes'=>'Restore doctypes',
	'import_export_data'=>'Restore export data',
	'import_extern_data'=>'Restore extern files/folders',
	'import_files'=>'Importing files',
	'import_file_found'=>'The file looks like webEdition import file. Please use the "Import/Export" option from the "File" menu to import the data.',
	'import_file_found_question'=>'Would you like now to close the current dialog and to start the import/export wizard?',
	'import_from_local'=>'Restoring from local file',
	'import_from_server'=>'Restoring data from server',
	'import_glossary_data'=>'Restore glossary data',
	'import_indexes'=>'Restore indexes',
	'import_links'=>'Restore links',
	'import_newsletter_data'=>'Restore newsletter data',
	'import_newsletter_dep'=>'You have selected the option `Restore newsletter data`. The Newsletter Module needs the documents and users data and because of that,  `Restore documents and templates` and `Restore customers data` has been automatically selected.',
	'import_object_data'=>'Restore objects and classes',
	'import_options'=>'Select the data that should be restored.',
	'import_prefs'=>'Restore preferences',
	'import_schedule_data'=>'Restore schedule data',
	'import_schedule_dep'=>'You have selected the option `Restore schedule data`. The Schedule Module needs the documents data and objects and because of that, `Restore documents and templates` and `Restore objects and classes` has been automatically selected.',
	'import_settings_data'=>'Restore settings',
	'import_shop_data'=>'Restore shop data',
	'import_shop_dep'=>'You have selected the option `Restore shop data`. The Shop Module needs the customers data and because of that, `Restore customers data` has been automatically selected.',
	'import_spellchecker_data'=>'Restore spellchecker data',
	'import_templates'=>'Importing templates',
	'import_temporary_data'=>'Restore temporary data',
	'import_temporary_dep'=>'You have selected the option `Restore temporary data`. The temporary data need the documents data and because of that, `Restore documents and templates` has been automatically selected.',
	'import_todo_data'=>'Restore task/messaging data',
	'import_todo_dep'=>'You have selected the option `Restore task/messaging data`. The Task/Messaging Module needs the users data and because of that, `Restore user data` has been automatically selected.',
	'import_users_data'=>'Restore user data',
	'import_user_data'=>'Restore user data',
	'import_versions_binarys_data'=>'Restore Version-Binary-Files',
	'import_versions_binarys_dep'=>'You have selected the option `Restore Version-Binary-Files`. The Version-Binary-Files need the documents data, object data an version data and because of that, `Restore documents and templates`, `Restore objects and classes and `Restore version data` has been automatically selected.',
	'import_versions_data'=>'Restore version data',
	'import_versions_dep'=>'You have selected the option `Restore version data`. The version data need the documents data, object data an version-binary-files and because of that, `Restore documents and templates`, `Restore objects and classes and `Restore Version-Binary-Files` has been automatically selected.',
	'import_voting_data'=>'Restore voting data',
	'import_workflow_data'=>'Restore workflow data',
	'import_workflow_dep'=>'You have selected the option `Restore workflow data`. The Workflow Module needs the documents and users data and because of that, `Restore documents and templates` and `Restore user data` has been automatically selected.',
	'name_notok'=>'The file name is not proper!',
	'newsletter_info'=>'Data from newsletter module.',
	'none'=>'none',
	'nothing_selected'=>'Nothing selected!',
	'nothing_selected_fromlist'=>'Choose the backup file that you want to import from the list to proceed!',
	'nothing_to_delete'=>'There is nothing to delete!',
	'no_resource'=>'Fatal Error: There are not enough resources to finish the backup!',
	'object_info'=>'Objects and classes from the DB/Object module.',
	'old_backups_warning'=>'Attention! We strongly recommend you to perform an update repeat after restoring a backup from an <strong>older installation of webEdition</strong> (before 6.3.3)!',
	'option'=>'Backup options',
	'other_files'=>'Other files',
	'preparing_file'=>'Preparing file for import...',
	'protect'=>'Protect backup file',
	'protect_txt'=>'The backup file will be protected from unprivileged download with additional php code. This protection requires additional disk space for import!',
	'query_is_too_big'=>'The backup contains a file which could not be restored as it exceeds the limit of %s bytes!',
	'question_taketime'=>'Export can take some time.',
	'question_wait'=>'Please wait!',
	'rebuild'=>'Automatic rebuild',
	'recover_backup_unsaved_changes'=>'Some open files have unsaved changes. Please check these before you continue.',
	'recover_option'=>'Import options',
	'save_before'=>'During import all existing data will be erased! It is recommended that you save your existing data first.',
	'save_not_checked'=>'You have not choosen where to save the backup file!',
	'save_question'=>'Do you want to save your existing data?',
	'schedule_info'=>'Scheduler data from scheduler module.',
	'select_server_file'=>'Choose the backup file you want to import from this list.',
	'select_upload_file'=>'Upload import from local file',
	'settings'=>'Restore preferences',
	'settings_info'=>'webEdition application settings.',
	'shop_info'=>'Orders data from the shop module.',
	'show_all'=>'Show all files',
	'spellchecker_info'=>'Data for spellchecker: settings, general and personal dictionaries.',
	'step1'=>'Step 1/4 - Save existing data',
	'step2'=>'Step 2/4 - Select import source',
	'step3'=>'Step 3/4 - Import saved data',
	'step4'=>'Step 4/4 - Restore finished',
	'temporary_info'=>'Data from unpublished documents and objects.',
	'todo_info'=>'Messages and tasks from the tasks/messaging module.',
	'tools_export_desc'=>'Here you can save webEdition tools data. Please select the desired tools from the list.',
	'tools_import_desc'=>'Here you can restore webEdition tools data. Please select the desired tools from the list.',
	'too_big_file'=>'File `%s` cannot be written as the size exceeds the maximum file size.',
	'unselect_dep2'=>'You have unselected `%s`. Following options will be automatically unselected.',
	'unselect_dep3'=>'This options can be selected again.',
	'unspecified_error'=>'An unknown error occurred!',
	'upload_failed'=>'The file can`t be uploaded. Please verify if the file size is greater then %s',
	'user_info'=>'User and accounts data from  the user module.',
	'versions_binarys_info'=>'This option could take some time and memory because the folder /webEdition/we/versions/ could be very large. It is recommended to save this folder manually.',
	'versions_info'=>'Data from Versioning.',
	'view_log'=>'Backup-Log',
	'view_log_not_found'=>'The backup log file was not found!',
	'view_log_no_perm'=>'You do not have the needed permissions to view the backup log file!',
	'voting_info'=>'Data from the voting module.',
	'warning'=>'Warning',
	'we_backups'=>'webEdition Backups',
	'wizard_backup_title'=>'Create Backup Wizard',
	'wizard_recover_title'=>'Restore Backup Wizard',
	'wizard_title'=>'Restore Backup Wizard',
	'wizard_title_export'=>'Backup Export Wizard',
	'workflow_info'=>'Data from the workflow module.',
	'working'=>'Working...',
	'zip'=>'zip',
);