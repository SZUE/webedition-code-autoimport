###UPDATEDROPCOL(Icon,###TBLPREFIX###tblObject)###
/* query separator */
###UPDATEDROPCOL(CacheType,###TBLPREFIX###tblObject)###
/* query separator */
###UPDATEDROPCOL(CacheLifeTime,###TBLPREFIX###tblObject)###
/* query separator */
CREATE TABLE ###TBLPREFIX###tblObject (
  ID int unsigned NOT NULL auto_increment,
  ParentID int unsigned NOT NULL default '0',
  strOrder text NOT NULL,
  `Text` varchar(255) NOT NULL default '',
  IsFolder tinyint unsigned NOT NULL default '0',
  ContentType enum('object') NOT NULL default 'object',
  CreationDate int unsigned NOT NULL default '0',
  ModDate int unsigned NOT NULL default '0',
  Path varchar(255) NOT NULL default '',
  CreatorID int unsigned NOT NULL default '0',
  ModifierID int unsigned NOT NULL default '0',
  RestrictOwners tinyint unsigned NOT NULL default '0',
  Owners varchar(255) NOT NULL default '',
  OwnersReadOnly text NOT NULL,
  RestrictUsers tinyint unsigned NOT NULL default '0',
  Users varchar(255) NOT NULL default '',
  UsersReadOnly text NOT NULL,
  DefaultCategory text NOT NULL,
  DefaultParentID int unsigned NOT NULL default '0',
  DefaultText varchar(255) NOT NULL default '',
  DefaultValues longtext NOT NULL,
  DefaultDesc varchar(255) NOT NULL default '',
  DefaultTitle varchar(255) NOT NULL default '',
  DefaultKeywords varchar(255) NOT NULL default '',
  DefaultUrl varchar(255) NOT NULL default '',
  DefaultUrlfield0 varchar(255) NOT NULL DEFAULT '_',
  DefaultUrlfield1 varchar(255) NOT NULL DEFAULT '_',
  DefaultUrlfield2 varchar(255) NOT NULL DEFAULT '_',
  DefaultUrlfield3 varchar(255) NOT NULL DEFAULT '_',
  DefaultTriggerID int unsigned NOT NULL default '0',
  ClassName enum('we_object') NOT NULL default 'we_object',
  Workspaces varchar(1000) NOT NULL default '',
  DefaultWorkspaces varchar(1000) NOT NULL default '',
  Templates varchar(255) NOT NULL default '',
  PRIMARY KEY (ID),
  KEY Path (Path),
  KEY IsFolder (IsFolder)
) ENGINE=MyISAM;