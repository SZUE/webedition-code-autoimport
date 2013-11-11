###UPDATEONLY###UPDATE ###TBLPREFIX###tblnavigation SET SelectionType="doctype" WHERE SelectionType="docType"

/* query separator */

CREATE TABLE ###TBLPREFIX###tblnavigation (
  ID int(11) unsigned NOT NULL auto_increment,
  ParentID int(11) unsigned NOT NULL default '0',
  Path varchar(2000) NOT NULL default '',
  Published tinyint(1) unsigned NOT NULL DEFAULT '1',
  Text varchar(800) NOT NULL default '',
  Display varchar(800) NOT NULL default '',
  ContentType enum('weNavigation') NOT NULL default 'weNavigation',
  Icon enum('folder.gif','link.gif') NOT NULL,
  IsFolder tinyint(1) unsigned NOT NULL default '0',
  TitleField varchar(255) NOT NULL default '',
  IconID int(11) unsigned NOT NULL default '0',
  Selection enum('dynamic','nodynamic','static') NOT NULL,
  LinkID bigint(20) unsigned NOT NULL default '0',
  CurrentOnUrlPar tinyint(1) unsigned NOT NULL DEFAULT '0',
  CurrentOnAnker tinyint(1) unsigned NOT NULL DEFAULT '0',
  SelectionType enum('urlLink','category','catLink','classname','objLink','docLink','doctype') NOT NULL default 'docLink',
  FolderID int(11) unsigned NOT NULL default '0',
  DocTypeID smallint(6) unsigned NOT NULL,
  ClassID int(11) unsigned NOT NULL default '0',
  Categories text NOT NULL,
  CatAnd tinyint(1) unsigned NOT NULL default '1',
  Sort text NOT NULL,
  ShowCount tinyint(4) unsigned NOT NULL default '0',
  Ordn tinyint(4) unsigned NOT NULL default '0',
  Depended tinyint(1) unsigned NOT NULL default '0',
  WorkspaceID int(11) NOT NULL default '-1',
  CatParameter varchar(255) NOT NULL default '',
  Parameter varchar(255) NOT NULL default '',
  LinkSelection varchar(255) NOT NULL default '',
  Url varchar(255) NOT NULL default '',
  UrlID int(11) unsigned NOT NULL default '0',
  Charset ENUM('','UTF-8','ISO-8859-1','ISO-8859-2','ISO-8859-3','ISO-8859-4','ISO-8859-5','ISO-8859-6','ISO-8859-7','ISO-8859-8','ISO-8859-9','ISO-8859-10','ISO-8859-11','ISO-8859-12','ISO-8859-13','ISO-8859-14','ISO-8859-15','Windows-1251','Windows-1252') NOT NULL default '',
  Attributes text NOT NULL,
  FolderSelection enum('docLink','objLink','urlLink') NOT NULL,
  FolderWsID int(11) unsigned NOT NULL default '0',
  FolderParameter varchar(255) NOT NULL default '',
  FolderUrl varchar(255) NOT NULL default '',
  LimitAccess tinyint(4) unsigned NOT NULL default '0',
  AllCustomers tinyint(4) unsigned NOT NULL default '1',
  ApplyFilter tinyint(4) unsigned NOT NULL default '0',
  Customers text NOT NULL,
  CustomerFilter text NOT NULL,
  BlackList text NOT NULL,
  WhiteList text NOT NULL,
  UseDocumentFilter tinyint(4) unsigned NOT NULL default '0',
  PRIMARY KEY  (ID),
  KEY ParentID (ParentID),
  KEY LinkID (LinkID),
  KEY Path (Path)
) ENGINE=MyISAM;
