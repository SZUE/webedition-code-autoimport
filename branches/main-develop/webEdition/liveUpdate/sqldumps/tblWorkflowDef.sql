CREATE TABLE ###TBLPREFIX###tblWorkflowDef (
  ID int(11) unsigned NOT NULL auto_increment,
  ParentID int(11) unsigned NOT NULL default '0',
  IsFolder tinyint(1) unsigned NOT NULL default '0',
  `Text` varchar(255) NOT NULL default '',
  `Path` varchar(255) NOT NULL default '/',
  `Type` bigint(20) unsigned NOT NULL default '0',
  Folders varchar(255) NOT NULL default '',
  DocType varchar(255) NOT NULL default '',
  Objects varchar(255) NOT NULL default '',
  ObjectFileFolders varchar(255) NOT NULL default '',
  Categories text NOT NULL,
  ObjCategories varchar(255) NOT NULL default '',
  `Status` tinyint(1) unsigned NOT NULL default '0',
  `EmailPath` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `LastStepAutoPublish` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (ID)
) ENGINE=MyISAM;