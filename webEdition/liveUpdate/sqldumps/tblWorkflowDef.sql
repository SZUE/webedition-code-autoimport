CREATE TABLE ###TBLPREFIX###tblWorkflowDef (
  ID int unsigned NOT NULL auto_increment,
  ParentID int unsigned NOT NULL default '0',
  IsFolder tinyint unsigned NOT NULL default '0',
  `Text` varchar(255) NOT NULL default '',
  Path varchar(255) NOT NULL default '/',
  `Type` bigint unsigned NOT NULL default '0',
  Folders text NOT NULL,
  DocType text NOT NULL,
  Objects text NOT NULL,
  ObjectFileFolders varchar(255) NOT NULL default '',
  Categories text NOT NULL,
  ObjCategories varchar(255) NOT NULL default '',
  `Status` tinyint unsigned NOT NULL default '0',
  EmailPath tinyint unsigned NOT NULL DEFAULT '0',
  LastStepAutoPublish tinyint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (ID)
) ENGINE=MyISAM;