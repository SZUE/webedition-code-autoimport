<?php

//
// ---> Wizards
//

$lang["Wizard"]["Settings"]["title"] = "Settings";
$lang["Wizard"]["Features"]["title"] = "Choose features";
$lang["Wizard"]["Summary"]["title"] = "Summary";
$lang["Wizard"]["DownloadAndInstallSoftware"]["title"] = "Install application";
$lang["Wizard"]["DownloadAndInstallSnippets"]["title"] = "Install additional data";
$lang["Wizard"]["FinishInstallation"]["title"] = "Finish installation";


//
// ---> Steps
//

// ImportantAnnouncement
$lang["Step"]["ImportantAnnouncement"]["title"] = "Important Information";
$lang["Step"]["ImportantAnnouncement"]["headline"] = "Important Information";
$lang["Step"]["ImportantAnnouncement"]["content"] = '<img src="./ApplicationInstaller/img/leLayout/alert.gif" style="margin:0px 10px 10px 0px; float:right;"/>Due to maintenance, the update server will not be available for updates and installations from Friday, November 28th, 2008 until Monday, December 1st, 2008. We are sorry for the inconvenience.<br /><br />The webEdition installation archive on <a href="http://sourceforge.net/projects/webedition/" target="_blank">Sourceforge project page</a> will be available to you during this time for installations.';

// LicenceAgreement
$lang["Step"]["LicenceAgreement"]["title"] = "Licence agreement";
$lang["Step"]["LicenceAgreement"]["headline"] = "Licence agreement";
$lang["Step"]["LicenceAgreement"]["content"] = "";

$lang["Step"]["LicenceAgreement"]["labelAccept"] = "General license conditions accepted";


// DocumentRoot
$lang["Step"]["DocumentRoot"]["title"] = "DOCUMENT_ROOT";
$lang["Step"]["DocumentRoot"]["headline"] = "DOCUMENT_ROOT";
$lang["Step"]["DocumentRoot"]["content"] = "Enter the DOCUMENT_ROOT here. The DOCUMENT_ROOT ist the path on the server to the directory conatining your HTML-files.<br /><br />The server returns the following path (MouseOver for the complete path) as DOCUMENT_ROOT: <br />%s<br /><br />If this is correct, you don't need to enter anything in the input field below!";

$lang["Step"]["DocumentRoot"]["DocumentRoot"] = "DOCUMENT_ROOT";
$lang["Step"]["DocumentRoot"]["requestNotValid"] = "The DOCUMENT_ROOT you entered could not be found on the server. Please verify your input";
$lang["Step"]["DocumentRoot"]["autoDocRootNotValid"] = "The DOCUMENT_ROOT could not be found on the server. Please enter the correct DOCUMENT_ROOT.";


// SystemRequirements
$lang["Step"]["SystemRequirements"]["title"] = "System requirements";
$lang["Step"]["SystemRequirements"]["headline"] = "System requirements";
$lang["Step"]["SystemRequirements"]["content"] = "Now the system requirements will be checked.";

$lang["Step"]["SystemRequirements"]["failure"] = "Error";
$lang["Step"]["SystemRequirements"]["ok"] = "OK";
$lang["Step"]["SystemRequirements"]["php_version"] = "PHP Version";
$lang["Step"]["SystemRequirements"]["mysql"] = "MySQL support";
$lang["Step"]["SystemRequirements"]["is_writeable"] = "Installation directory writable";
$lang["Step"]["SystemRequirements"]["error"] = "One or more of the system requirements are not satisfied. webEdition cannot be installed.<br />The installation will be cancelled.<br /><br />";


// SoftwareRequirements
$lang["Step"]["SoftwareRequirements"]["title"] = "Software requirements";
$lang["Step"]["SoftwareRequirements"]["headline"] = "Software requirements";
$lang["Step"]["SoftwareRequirements"]["content"] = "Now the software requirements will be checked.";

$lang["Step"]["SoftwareRequirements"]["failure"] = "Error";
$lang["Step"]["SystemRequirements"]["ok"] = "OK";
$lang["Step"]["SoftwareRequirements"]["php_version"] = "PHP Version";
$lang["Step"]["SoftwareRequirements"]["error"] = "One or more of the software requirements are not satisfied. webEdition cannot be installed.<br />The installation will be cancelled.<br /><br />";
$lang["Step"]["SoftwareRequirements"]["mbstring"] = "PHP Multibyte String Functions";
$lang["Step"]["SoftwareRequirements"]["mbstringNotAvailable"] = "The PHP Multibyte String Functions are obviously not available on this server, so the webEdition features regarding charset selection may be limited.";
$lang["Step"]["SoftwareRequirements"]["gdlib"] = "PHP GDlib functions";
$lang["Step"]["SoftwareRequirements"]["gdlibNotAvailable"] = "The PHP GDlib functions are obviously not available on this server, so some image manipulation and preview features of webEdition may be limited.";
$lang["Step"]["SoftwareRequirements"]["found"] = "found";
$lang["Step"]["SoftwareRequirements"]["exif"] = "EXIF support";
$lang["Step"]["SoftwareRequirements"]["exifNotAvailable"] = "The exif PHP extension is not available on this server, therefore EXIF metadata for images are not usabale.";
$lang["Step"]["SoftwareRequirements"]["pcre"] = "Version of PCRE PHP extension: ";
$lang["Step"]["SoftwareRequirements"]["pcreOLD"] = "Your PCRE-Version is outdated: This can lead to problems, particularly in future webEdition versions";
$lang["Step"]["SoftwareRequirements"]['sdk_db'] = 'SDK/Apps DB support';
$lang["Step"]["SoftwareRequirements"]['sdk_dbWarnung'] = 'SDK DB operations and WE-APPS using database access are not available, the following PHP extensions are missing: PDO and PDO_mysql';
$lang["Step"]["SoftwareRequirements"]['phpext'] = 'Required PHP extensions';
$lang["Step"]["SoftwareRequirements"]['phpextWarning'] = 'Not available required PHP extensions: ';
$lang["Step"]["SoftwareRequirements"]['reqNotDetec'] = 'Some of the software requirements could not be checked (Suhosin?). Please check the system requirements at http://www.webedition.org/de/webedition-cms/systemvoraussetzungen.php ';
$lang["Step"]["SoftwareRequirements"]['softreq'] = "Additional software requirements";

// InstallationDirectory
$lang["Step"]["InstallationDirectory"]["title"] = "Installation directory";
$lang["Step"]["InstallationDirectory"]["headline"] = "Check installation directory";
$lang["Step"]["InstallationDirectory"]["content"] = "There seems no webEdition installed on the server so far. You can continue with the installation.";

$lang["Step"]["InstallationDirectory"]["installationForbidden"] = "Several webEdition files, but no webEdition configuration can be found on the server. The installation can not be continued until the webEdition directory is deleted from the server.";
$lang["Step"]["InstallationDirectory"]["alreadyInstalled"] = "There seems to be an existing webEdition installation on this server. Before continuing with the instllation, you have to verify the previous installation. This is a security issue to prevent an unauthorized overriding of a running webEdition.";
$lang["Step"]["InstallationDirectory"]["textNotInstalled"] = "There seems no webEdition installed on the server so far. You can continue with the installation.";
$lang["Step"]["InstallationDirectory"]["installationVeryfied"] = "The installation was veryfied. The old webEdition directory will be moved during the installation process.";
$lang["Step"]["InstallationDirectory"]["dataNotValid"] = "The data you have entered does not match with the found data in file /webEdition/we/include/we_conf.inc.php.";
$lang["Step"]["InstallationDirectory"]["userNameDb"] = "Database user";
$lang["Step"]["InstallationDirectory"]["passDb"] = "Database password";


// Database
$lang["Step"]["Database"]["title"] = "Database";
$lang["Step"]["Database"]["headline"] = "Database";
$lang["Step"]["Database"]["content"] = "Enter the access information for your MySQL-Database server! You will receive these data from your web-space provider. If the database does not exist, it will be created.";

$lang["Step"]["Database"]['connecttype']	= "Connection type";
$lang["Step"]["Database"]['connect'] = "Normal (connect)";
$lang["Step"]["Database"]['pconnect'] = "Persistent (pconnect)";
$lang["Step"]["Database"]['pconnect_na'] = "not available";
$lang["Step"]["Database"]['host'] = "Server (Host)";
$lang["Step"]["Database"]['user'] = "Username";
$lang["Step"]["Database"]['pass'] = "Password";
$lang["Step"]["Database"]['name'] = "Database";
$lang["Step"]["Database"]['prefix'] = "Table Prefix (optional)";
$lang["Step"]["Database"]["connect_help"] = "Please enter the kind of connection you wish to make to your database.\\n\\nPersistent DB-connections bear much resemblance to normal DB-connections, but there are two differences.\\n\\nFirst:  Before a new connection is made to the database, the system attempts to use an existing persistent connection to the same host, using an existing username and password. If this action succeeds, the existing connection is used instead of establishing a new one.\\n\\nSecond: The connection to the MySQL server will not be closed when the PHP-script is finished. The connection stays open for further use.\\n\\nIf you are unsure which connection type to choose, please choose \'Normal (connect)\'.";
$lang["Step"]["Database"]['host_help'] = "Enter the server name (host) or the IP of the database server.\\n For example: db47.ihrprovider.de, 194.44.55.66 or \':/var/run/mysqld/mysqld.sock\' if you use Unix sockets. \\nIf the database server runs on the same computer as the web server, enter \'localhost\' in most cases.";
$lang["Step"]["Database"]['user_help'] = "Enter the username for your database.";
$lang["Step"]["Database"]['pass_help'] = "Enter the password for your database.";
$lang["Step"]["Database"]['name_help'] = "Enter the name of your database here. If the database with the name entered here does not exist, it will be created provided that you have the necessary authorization.";
$lang["Step"]["Database"]['prefix_help'] = "You can enter a table-prefix here. If you do so it is possible to install several webEditions on a single database.";
$lang["Step"]["Database"]['ErrorDBConnect'] = "Unable to establish database connection!<br />Please verify your data.";
$lang["Step"]["Database"]['ErrorDBHost'] = "Enter the server name (host) of the database server!";
$lang["Step"]["Database"]['ErrorDBUser'] = "Enter the username of the database server!";
$lang["Step"]["Database"]['ErrorDBName'] = "Enter the password of the database server!";
$lang["Step"]["Database"]["ErrorCreateDb"] = "Database '%s' not created!. Please check your MySQL-Privileges.<br />MySQL-Fehler: %s (%s)";


// DatabasePermissions
$lang["Step"]["DatabasePermissions"]["title"] = "Database permissions";
$lang["Step"]["DatabasePermissions"]["headline"] = "Check database permissions";
$lang["Step"]["DatabasePermissions"]["content"] = "You have the required priviliges to install webEdition.<br /><ul><li>CREATE TABLE</li><li>ALTER TABLE</li><li>DROP TAPBE</li></ul>";

$lang["Step"]["DatabasePermissions"]["dbserverwarning"] = "<br/>The database server reports the version %s, webEdition requires at least the  MySQL-Server version 5.0. webEdition may work with the used version, but this can not be guarented for new webEdition versions (i.e. after updates). For webEdition version 7,  MySQL version 5 will definitely be required.<br/><span style=\"color:red;font-weight:bold\">In addition: The installed MySQL version is outdated. There are no security updates available for this version which may put the security of the whole system at risk!</span><br/>";

$lang["Step"]["DatabasePermissions"]["AccessDenied"] = "The database '%s' does not exist. The user you entered has no the required permissions to create or use the database. Please move back and check the user data or the permissions of the database.";
$lang["Step"]["DatabasePermissions"]["errorNotCreateTable"] = "<strong>Missing privilege: create table</strong><br />The database user has not the required privileges to create a table. This privilege is required to install webEdition. Please check your database user and increase his privileges. Normally your webspace-provider can assist you doing this.";
$lang["Step"]["DatabasePermissions"]["errorNotAlterTable"] = "<strong>Missing privilege: alter table</strong><br />The database user has not the required privileges to alter a table. This privilege is required to install webEdition. Please check your database user and increase his privileges. Normally your webspace-provider can assist you doing this.";
$lang["Step"]["DatabasePermissions"]["errorNotDropTable"] = "<strong>Missing privilege: drop table</strong><br />The database user has not the required privileges to drop a table. This privilege is required to install webEdition. Please check your database user and increase his privileges. Normally your webspace-provider can assist you doing this.";

$lang["Step"]["DatabasePermissions"]["overWriteExistingDb"] = "webEdition is already installed on your database server. To use several webEdition on a single database server, you can use a table prefix. If you want to use a table prefix go back to database settings and enter the prefix you want to use.<br /><br />If you continue, the tables (and containing data) of your old webEdition will be deleted. Do you want to continue with the installation?";
$lang["Step"]["DatabasePermissions"]["overWriteExistingDbCheckBox"] = "Continue and overwrite old data";

$lang["Step"]["DatabasePermissions"]["Collation"] = "Collation";
$lang["Step"]["DatabasePermissions"]["defaultCollation"] = "default value of the MySQL server";


// Login
$lang["Step"]["Login"]["title"] = "Login";
$lang["Step"]["Login"]["headline"] = "Login";
$lang["Step"]["Login"]["content"] = "Enter your webEdition access data here. This information allows you to log into your installed webEdition.";

$lang["Step"]["Login"]['user'] = "Username";
$lang["Step"]["Login"]['pass'] = "Password";
$lang["Step"]["Login"]['confirm'] = "Confirm password";
$lang["Step"]["Login"]['user_help'] = "Enter the username to log into the installed webEdition.";
$lang["Step"]["Login"]['pass_help'] = "Enter the password to log into the installed webEdition.";
$lang["Step"]["Login"]['confirm_help'] = "Enter the password to log into the installed webEdition.";
$lang["Step"]["Login"]["UsernameFailure"] = "Please enter your prefered username.";
$lang["Step"]["Login"]["UsernameToShort"] = "Username must be at least 2 characters long!";
$lang["Step"]["Login"]["UsernameInvalid"] = "User names can only contain letters (a-z and A-Z), numbers (0-9) and the characters '.', '-' and '_'!";
$lang["Step"]["Login"]["PasswordFailure"] = "Please insert your prefered password.";
$lang["Step"]["Login"]["PasswordToShort"] = "Password must be at least 4 characters long!";
$lang["Step"]["Login"]["PasswordInvalid"] = "No spaces allowed in password!";
$lang["Step"]["Login"]["ConfirmFailure"] = "Passwords do not match.";


// ChooseLanguage
$lang["Step"]["ChooseLanguage"]["title"] = "Language";
$lang["Step"]["ChooseLanguage"]["headline"] = "Choose languages";
$lang["Step"]["ChooseLanguage"]["content"] = "Please choose your prefered languages. The system language will be the default language in webEdition. We recommend to use an UTF-8 version. Additional languages can be installed later with the online updater.<br/><b>Important notice:</b>As <font color=\"red\">[beta]</font> marked languages might be incomplete and even with errors. Please contact the project team if you want to help to complete the translations. ";

$lang["Step"]["ChooseLanguage"]["language"] = "Languages";
$lang["Step"]["ChooseLanguage"]["system"] = "System";
$lang["Step"]["ChooseLanguage"]["additional"] = "Additional";


// ChooseVersion
$lang["Step"]["ChooseVersion"]["title"] = "Version";
$lang["Step"]["ChooseVersion"]["headline"] = "Choose version";
$lang["Step"]["ChooseVersion"]["content"] = "Please choose which webEdition version you want to install.";

$lang["Step"]["ChooseVersion"]["cannotInstallWebEdition"] = "webEdition could not be installed, because there is no translation for all selected languages.";
$lang["Step"]["ChooseVersion"]["missingTranslations"] = "The newest webEdition version (%s) could not be installed, because there is no translation for all selected languages. We recommend in this case th install the version %s.";
$lang["Step"]["ChooseVersion"]["highestVersionRecommended"] = "We always recommend to install the newest version.";
$lang["Step"]["ChooseVersion"]["version"] = "Version";
$lang["Step"]["ChooseVersion"]["noNotLiveVersion"] = "Currently, no alpha- or beta versions are available. Install the latest official version.";

$lang["Step"]["ChooseVersion"]['nightly-build'] = 'nightly build';
$lang["Step"]["ChooseVersion"]['alpha'] = 'Alpha';
$lang["Step"]["ChooseVersion"]['beta'] = 'Beta';
$lang["Step"]["ChooseVersion"]['rc'] = 'RC';
$lang["Step"]["ChooseVersion"]['release'] = 'official Release';

// SerialNumber
$lang["Step"]["SerialNumber"]["title"] = "Serial number";
$lang["Step"]["SerialNumber"]["headline"] = "Serial number";
$lang["Step"]["SerialNumber"]["content"] = "You could now register your copy of webEdition and use the full functional range. Please enter your serial number.<br />If you don't have a serial number, you could by a licence at <a href=\"http://www.living-e.com/\" target=\"_blank\">http://www.living-e.com/</a> or install webEdition as demo without entering a serial number.";

$lang["Step"]["SerialNumber"]["labelRegister"] = "Yes, i would now register webEdition.";
$lang["Step"]["SerialNumber"]["serial"] = "Serial number";
$lang["Step"]["SerialNumber"]["serialNotValid"] = "The entered serial number is invalid.";


// ChooseModules
$lang["Step"]["ChooseModules"]["title"] = "Modules";
$lang["Step"]["ChooseModules"]["headline"] = "Choose modules";
$lang["Step"]["ChooseModules"]["content"] = "Please choose the modules which have to be installed";

$lang["Step"]["ChooseModules"]["modules"] = "Module";
$lang["Step"]["ChooseModules"]["pro_modules"] = "Pro module";
$lang["Step"]["ChooseModules"]["depending_modules"] = "Depending module";
$lang["Step"]["ChooseModules"]["no_serial"] = "It is not possible to install modules in demo mode.";
$lang["Step"]["ChooseModules"]["no_modules"] = "There a no modules left for the entered serial.";


// ChooseSnippets
$lang["Step"]["ChooseSnippets"]["title"] = "Additional data";
$lang["Step"]["ChooseSnippets"]["headline"] = "Choose additional data";
$lang["Step"]["ChooseSnippets"]["content"] = "This step will be executed by the LiveUpdate-Server";


// Summary
$lang["Step"]["Summary"]["title"] = "Summary";
$lang["Step"]["Summary"]["headline"] = "Summary";
$lang["Step"]["Summary"]["content"] = "Are all the displayed data correct?";

$lang["Step"]["Summary"]['webEditionBase'] = "webEdition";
$lang["Step"]["Summary"]['webEditionURL'] = "URL";
$lang["Step"]["Summary"]['webEditionUsername'] = "Username";
$lang["Step"]["Summary"]['webEditionPassword'] = "Passwort";
$lang["Step"]["Summary"]['webEditionSerial'] = "Serial number";
$lang["Step"]["Summary"]['webEditionVersion'] = "Version";
$lang["Step"]["Summary"]['webEditionLanguage'] = "webEdition Languages";
$lang["Step"]["Summary"]['webEditionSystemLanguage'] = "System language";
$lang["Step"]["Summary"]['webEditionAdditionalLanguages'] = "additional languages";
$lang["Step"]["Summary"]['Module'] = "Module";
$lang["Step"]["Summary"]['databaseConnection'] = "Database connection";
$lang["Step"]["Summary"]['databaseHost'] = "Host";
$lang["Step"]["Summary"]['databaseUsername'] = "Username";
$lang["Step"]["Summary"]['databasePassword'] = "Password";
$lang["Step"]["Summary"]['databaseName'] = "Database";
$lang["Step"]["Summary"]['databaseTablePrefix'] = "Table prefix";
$lang["Step"]["Summary"]['databaseCharset'] = "Charset";
$lang["Step"]["Summary"]['databaseCollation'] = "Collation";
$lang["Step"]["Summary"]['databaseDefault'] = "Default settings";
$lang["Step"]["Summary"]['databaseConnectionType'] = "Connection type";
$lang["Step"]["Summary"]['proxyServer'] = "Proxy server";
$lang["Step"]["Summary"]['proxyHost'] = "Host";
$lang["Step"]["Summary"]['proxyPort'] = "Port";
$lang["Step"]["Summary"]['proxyUsername'] = "Username";
$lang["Step"]["Summary"]['proxyPassword'] = "Password";
$lang["Step"]["Summary"]['snippets'] = "Additional data";
$lang["Step"]["Summary"]['yes'] = "yes";
$lang["Step"]["Summary"]['no'] = "no";
$lang["Step"]["Summary"]['showPasswords'] = "Show passwords";
$lang["Step"]["Summary"]['hidePasswords'] = "Hide passwords";


// PrepareApplicationInstallation
$lang["Step"]["PrepareApplicationInstallation"]["title"] = "Prepare installation";
$lang["Step"]["PrepareApplicationInstallation"]["headline"] = "Prepare application installation";
$lang["Step"]["PrepareApplicationInstallation"]["content"] = "This step will be executed by the LiveUpdate-Server";


// DetermineApplicationFiles
$lang["Step"]["DetermineApplicationFiles"]["title"] = "Determine files";
$lang["Step"]["DetermineApplicationFiles"]["headline"] = "Determine application files";
$lang["Step"]["DetermineApplicationFiles"]["content"] = "This step will be executed by the LiveUpdate-Server";


// DownloadApplicationFiles
$lang["Step"]["DownloadApplicationFiles"]["title"] = "Download files";
$lang["Step"]["DownloadApplicationFiles"]["headline"] = "Download application files";
$lang["Step"]["DownloadApplicationFiles"]["content"] = "This step will be executed by the LiveUpdate-Server";


// UpdateApplicationDatabase
$lang["Step"]["UpdateApplicationDatabase"]["title"] = "Setting up database";
$lang["Step"]["UpdateApplicationDatabase"]["headline"] = "Setting up database";
$lang["Step"]["UpdateApplicationDatabase"]["content"] = "This step will be executed by the LiveUpdate-Server";


// PrepareApplicationFiles
$lang["Step"]["PrepareApplicationFiles"]["title"] = "Prepare files";
$lang["Step"]["PrepareApplicationFiles"]["headline"] = "Prepare application files";
$lang["Step"]["PrepareApplicationFiles"]["content"] = "This step will be executed by the LiveUpdate-Server";


// CopyApplicationFiles
$lang["Step"]["CopyApplicationFiles"]["title"] = "Copy files";
$lang["Step"]["CopyApplicationFiles"]["headline"] = "Copy application files";
$lang["Step"]["CopyApplicationFiles"]["content"] = "This step will be executed by the LiveUpdate-Server";


// WriteApplicationConfiguration
$lang["Step"]["WriteApplicationConfiguration"]["title"] = "Configure webEdition";
$lang["Step"]["WriteApplicationConfiguration"]["headline"] = "Configure webEdition";
$lang["Step"]["WriteApplicationConfiguration"]["content"] = "This step will be executed by the LiveUpdate-Server";


// FinishApplicationInstallation
$lang["Step"]["FinishApplicationInstallation"]["title"] = "Finish installation";
$lang["Step"]["FinishApplicationInstallation"]["headline"] = "Installation will be finished";
$lang["Step"]["FinishApplicationInstallation"]["content"] = "The installation will now be finished.";


// Community
$lang["Step"]["Community"]["title"] = "webEdition community";
$lang["Step"]["Community"]["headline"] = "join the community!";
$lang["Step"]["Community"]["content"] = "blah blah blubb ...";


// InstallationFinished
$lang["Step"]["InstallationFinished"]["title"] = "Installation finished";
$lang["Step"]["InstallationFinished"]["headline"] = "webEdition is now installed";
$lang["Step"]["InstallationFinished"]["content"] = "webEdition is now installed on your server. You could now login with your chosen username and password.";

$lang["Step"]["InstallationFinished"]["login_webEdition"] = "Start webEdition";
$lang["Step"]["InstallationFinished"]["additional_software"] = "You could install some of our other products, if you are interested in these.<br />You could simply use this installer to do this.";
$lang["Step"]["InstallationFinished"]["installMore"] = "Yes, i want to test other products";
$lang["Step"]["InstallationFinished"]["choose_software"] = "Please choose the application you want to install";


// CleanUp
$lang["Step"]["CleanUp"]["title"] = "Clean up";
$lang["Step"]["CleanUp"]["headline"] = "Clean up the installer";
$lang["Step"]["CleanUp"]["content"] = "At the end of the installation, the demo data will be installed and the installation files will be deleted.";

$lang["Step"]["CleanUp"]["delete_failed"] = "For security reasons we recommend, that you delete the installer files now.";
$lang["Step"]["CleanUp"]["openWebEdition"] = "Start webEdition";


?>