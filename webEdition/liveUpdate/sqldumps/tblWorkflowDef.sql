CREATE TABLE ###TBLPREFIX###tblWorkflowDef (
  ID mediumint unsigned NOT NULL auto_increment,
  ParentID mediumint unsigned NOT NULL default '0',
  IsFolder tinyint unsigned NOT NULL default '0',
  `Text` tinytext NOT NULL default '',
  Path varchar(255) NOT NULL default '/',
  `Type` tinyint unsigned NOT NULL default '0',
  Folders text NOT NULL,
  DocType text NOT NULL,
  Objects text NOT NULL,
  ObjectFileFolders tinytext NOT NULL default '',
  Categories text NOT NULL,
  ObjCategories tinytext NOT NULL default '',
  `Status` tinyint unsigned NOT NULL default '0',
  EmailPath tinyint unsigned NOT NULL DEFAULT '0',
  LastStepAutoPublish tinyint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (ID)
) ENGINE=MyISAM;