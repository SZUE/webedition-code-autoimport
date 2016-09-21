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
  Users text NOT NULL default '',
  UsersReadOnly text NOT NULL,
  DefaultCategory text NOT NULL,
  DefaultParentID int unsigned NOT NULL default '0',
  DefaultText text NOT NULL default '',
  DefaultValues longtext NOT NULL,
  DefaultDesc tinytext NOT NULL default '',
  DefaultTitle tinytext NOT NULL default '',
  DefaultKeywords tinytext NOT NULL default '',
  DefaultUrl tinytext NOT NULL default '',
  DefaultUrlfield0 tinytext NOT NULL DEFAULT '_',
  DefaultUrlfield1 tinytext NOT NULL DEFAULT '_',
  DefaultUrlfield2 tinytext NOT NULL DEFAULT '_',
  DefaultUrlfield3 tinytext NOT NULL DEFAULT '_',
  DefaultTriggerID int unsigned NOT NULL default '0',
  ClassName enum('we_object') NOT NULL default 'we_object',
  Workspaces varchar(1000) NOT NULL default '',
  DefaultWorkspaces varchar(1000) NOT NULL default '',
  Templates varchar(255) NOT NULL default '',
  PRIMARY KEY (ID),
  UNIQUE KEY Path (Path)
) ENGINE=MyISAM;