###UPDATEDROPCOL(clickid,###TBLPREFIX###tblMessages)###
/* query separator */
###UPDATEDROPCOL(Timestamp,###TBLPREFIX###tblMessages)###
/* query separator */
###UPDATEDROPCOL(IP,###TBLPREFIX###tblMessages)###
/* query separator */
###UPDATEDROPCOL(Referer,###TBLPREFIX###tblMessages)###
/* query separator */
###UPDATEDROPCOL(DID,###TBLPREFIX###tblMessages)###
/* query separator */
###UPDATEDROPCOL(Page,###TBLPREFIX###tblMessages)###
/* query separator */

CREATE TABLE ###TBLPREFIX###tblMessages (
  ID int unsigned NOT NULL auto_increment,
  ParentID int unsigned default NULL,
  UserID int unsigned default NULL,
  msg_type tinyint unsigned NOT NULL default '0',
  obj_type tinyint unsigned NOT NULL default '0',
  headerDate int unsigned default NULL,
  headerSubject varchar(255) default NULL,
  headerUserID int unsigned default NULL,
  headerFrom varchar(255) default NULL,
  headerTo varchar(255) default NULL,
  Priority tinyint unsigned default NULL,
  seenStatus tinyint unsigned NOT NULL default '0',
  MessageText text,
  tag tinyint unsigned NOT NULL default '0',
  PRIMARY KEY  (ID),
  KEY UserID (UserID),
  KEY `query` (`obj_type`,`msg_type`,`ParentID`,`UserID`)
) ENGINE=MyISAM;
