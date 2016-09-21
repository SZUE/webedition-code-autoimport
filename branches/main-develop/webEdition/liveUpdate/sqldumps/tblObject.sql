###UPDATEDROPCOL(Icon,###TBLPREFIX###tblObject)###
/* query separator */
###UPDATEDROPCOL(CacheType,###TBLPREFIX###tblObject)###
/* query separator */
###UPDATEDROPCOL(CacheLifeTime,###TBLPREFIX###tblObject)###
/* query separator */
###UPDATEDROPKEY(IsFolder,###TBLPREFIX###tblObject)###
/* query separator */

CREATE TABLE ###TBLPREFIX###tblObject (
  ID int unsigned NOT NULL auto_increment,
  ParentID int unsigned NOT NULL default '0',
  strOrder text NOT NULL,
  `Text` varchar(150) NOT NULL default '',
  IsFolder tinyint unsigned NOT NULL default '0',
  ContentType enum('object') NOT NULL default 'object',
  CreationDate int unsigned NOT NULL default '0',
  ModDate int unsigned NOT NULL default '0',
  Path varchar(150) NOT NULL default '',
  CreatorID int unsigned NOT NULL default '0',
  ModifierID int unsigned NOT NULL default '0',
  RestrictOwners tinyint unsigned NOT NULL default '0',
  Owners text NOT NULL default '',
  OwnersReadOnly text NOT NULL,
  RestrictUsers tinyint unsigned NOT NULL default '0',
  Users text NOT NULL,
  UsersReadOnly text NOT NULL,
  DefaultCategory text NOT NULL,
  DefaultParentID int unsigned NOT NULL default '0',
  DefaultText text NOT NULL,
  DefaultValues longtext NOT NULL,
  DefaultDesc tinytext NOT NULL,
  DefaultTitle tinytext NOT NULL,
  DefaultKeywords tinytext NOT NULL,
  DefaultUrl tinytext NOT NULL,
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
  UNIQUE KEY Path (Path)
) ENGINE=MyISAM;