###ONCOL(Icon,###TBLPREFIX###tblnavigation) UPDATE ###TBLPREFIX###tblnavigation SET SelectionType="doctype" WHERE SelectionType="docType";###
/* query separator */
###UPDATEDROPCOL(Icon,###TBLPREFIX###tblnavigation)###
/* query separator */


###ONCOL(FolderSelection,###TBLPREFIX###tblnavigation) UPDATE ###TBLPREFIX###tblnavigation SET FolderSelection="doctype" WHERE IsFolder=1 AND FolderSelection IS NULL;###
/* query separator */
###ONCOL(FolderSelection,###TBLPREFIX###tblnavigation) ALTER TABLE ###TBLPREFIX###tblnavigation CHANGE `FolderSelection` `FolderSelection` enum('docLink','objLink','urlLink','catLink') NOT NULL default 'docLink';###
/* query separator */
###ONCOL(FolderSelection,###TBLPREFIX###tblnavigation) UPDATE ###TBLPREFIX###tblnavigation SET FolderSelection=SelectionType WHERE IsFolder=0;###
/* query separator */

###ONCOL(FolderSelection,###TBLPREFIX###tblnavigation) ALTER TABLE ###TBLPREFIX###tblnavigation CHANGE SelectionType DynamicSelection enum('doctype','category','classname') NOT NULL default 'doctype';###
/* query separator */
###ONCOL(FolderSelection,###TBLPREFIX###tblnavigation) ALTER TABLE ###TBLPREFIX###tblnavigation CHANGE `FolderSelection` `SelectionType` ENUM('docLink','objLink','urlLink','catLink') NOT NULL default 'docLink';###
ALTER TABLE `tblnavigation` CHANGE `SelectionType` `SelectionType` ENUM('urlLink','categfory','catLink','classname','objLink','docLink','doctype') NOT NULL DEFAULT 'docLink';
/* query separator */


CREATE TABLE ###TBLPREFIX###tblnavigation (
  ID int unsigned NOT NULL auto_increment,
  ParentID int unsigned NOT NULL default '0',
  Path varchar(2048) NOT NULL default '',
	CreationDate int unsigned NOT NULL default '0',
	ModDate int unsigned NOT NULL default '0',
	Published int unsigned NOT NULL default '1',
	CreatorID int unsigned NOT NULL default '0',
	ModifierID int unsigned NOT NULL default '0',
	RestrictOwners tinyint unsigned NOT NULL default '0',
	Owners tinytext NOT NULL,
	OwnersReadOnly text NOT NULL,
  Text varchar(255) NOT NULL default '',
  Display text NOT NULL,
  ContentType enum('weNavigation') NOT NULL default 'weNavigation',
  IsFolder tinyint unsigned NOT NULL default '0',
  TitleField text NOT NULL,
  IconID int unsigned NOT NULL default '0',
  Selection enum('dynamic','nodynamic','static') NOT NULL default 'static',
  LinkID int unsigned NOT NULL default '0',
  CurrentOnUrlPar tinyint unsigned NOT NULL DEFAULT '0',
  CurrentOnAnker tinyint unsigned NOT NULL DEFAULT '0',
  SelectionType enum('docLink','objLink','urlLink','catLink') NOT NULL default 'docLink',
	DynamicSelection enum('doctype','category','classname') NOT NULL default 'doctype',
  FolderID int unsigned NOT NULL default '0',
  DocTypeID mediumint unsigned NOT NULL default '0',
  ClassID smallint unsigned NOT NULL default '0',
  Categories text NOT NULL,
  CatAnd tinyint unsigned NOT NULL default '1',
  Sort text NOT NULL,
  ShowCount tinyint unsigned NOT NULL default '0',
  Ordn tinyint unsigned NOT NULL default '0',
  Depended tinyint unsigned NOT NULL default '0',
  WorkspaceID int NOT NULL default '-1',
  CatParameter text NOT NULL,
  Parameter text NOT NULL,
  LinkSelection text NOT NULL,
  Url text NOT NULL,
  UrlID int unsigned NOT NULL default '0',
  Charset ENUM('','UTF-8','ISO-8859-1','ISO-8859-2','ISO-8859-3','ISO-8859-4','ISO-8859-5','ISO-8859-6','ISO-8859-7','ISO-8859-8','ISO-8859-9','ISO-8859-10','ISO-8859-11','ISO-8859-12','ISO-8859-13','ISO-8859-14','ISO-8859-15','Windows-1251','Windows-1252') NOT NULL default '',
  Attributes text NOT NULL,
  LimitAccess tinyint unsigned NOT NULL default '0',
  AllCustomers tinyint unsigned NOT NULL default '1',
  ApplyFilter tinyint unsigned NOT NULL default '0',
  Customers text NOT NULL,
  CustomerFilter text NOT NULL,
  BlackList text NOT NULL,
  WhiteList text NOT NULL,
  UseDocumentFilter tinyint unsigned NOT NULL default '0',
  PRIMARY KEY  (ID),
  UNIQUE KEY ParentID (ParentID,Text),
  KEY LinkID (LinkID),
  KEY Path (Path(250))
) ENGINE=MyISAM;

/* query separator */
###ONCOL(FolderUrl,###TBLPREFIX###tblnavigation) UPDATE ###TBLPREFIX###tblnavigation SET Url=FolderUrl WHERE IsFolder=1;###
/* query separator */
###UPDATEDROPCOL(FolderUrl,###TBLPREFIX###tblnavigation)###
/* query separator */
###ONCOL(FolderWsID,###TBLPREFIX###tblnavigation) UPDATE ###TBLPREFIX###tblnavigation SET WorkspaceID=FolderWsID WHERE IsFolder=1;###
/* query separator */
###UPDATEDROPCOL(FolderWsID,###TBLPREFIX###tblnavigation)###
/* query separator */
###ONCOL(FolderParameter,###TBLPREFIX###tblnavigation) UPDATE ###TBLPREFIX###tblnavigation SET Parameter=FolderParameter WHERE IsFolder=1;###
/* query separator */
###UPDATEDROPCOL(FolderParameter,###TBLPREFIX###tblnavigation)###

/* query separator */
UPDATE ###TBLPREFIX###tblnavigation SET Url="" WHERE Url="http://";
