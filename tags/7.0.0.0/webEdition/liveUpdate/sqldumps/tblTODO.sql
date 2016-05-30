###UPDATEDROPCOL(account_id,###TBLPREFIX###tblTODO)###
/* query separator */

CREATE TABLE ###TBLPREFIX###tblTODO (
  ID int unsigned NOT NULL auto_increment,
  ParentID int unsigned default NULL,
  UserID int unsigned NOT NULL default '0',
  msg_type tinyint unsigned NOT NULL default '0',
  obj_type tinyint unsigned NOT NULL default '0',
  headerDate int unsigned default NULL,
  headerSubject varchar(255) default NULL,
  headerCreator int unsigned default NULL,
  headerAssigner int unsigned default NULL,
  headerStatus tinyint unsigned default NULL,
  headerDeadline int unsigned default NULL,
  Priority tinyint unsigned default NULL,
  Properties smallint unsigned default NULL,
  MessageText text,
  Content_Type varchar(10) default NULL,
  seenStatus tinyint unsigned NOT NULL default '0',
  tag tinyint unsigned default NULL,
  PRIMARY KEY  (ID)
) ENGINE=MyISAM;