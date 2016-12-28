###UPDATEDROPCOL(account_id,###TBLPREFIX###tblMsgFolders)###
/* query separator */

CREATE TABLE ###TBLPREFIX###tblMsgFolders (
  ID int unsigned NOT NULL auto_increment,
  ParentID int unsigned default NULL,
  UserID int unsigned NOT NULL default '0',
  msg_type tinyint unsigned NOT NULL default '0',
  obj_type tinyint unsigned NOT NULL default '0',
  Name varchar(255) NOT NULL default '',
  sortItem varchar(255) default NULL,
  sortOrder enum("asc","desc") default NULL,
  Properties int unsigned default NULL,
  tag tinyint unsigned default NULL,
  PRIMARY KEY  (ID)
) ENGINE=MyISAM;
/* query separator */
INSERT IGNORE INTO ###TBLPREFIX###tblMsgFolders SET ID=1,ParentID=0,UserID=1,msg_type=1,obj_type=3,Name='Messages',Properties=1;
/* query separator */
INSERT IGNORE INTO ###TBLPREFIX###tblMsgFolders SET ID=2,ParentID=1,UserID=1,msg_type=1,obj_type=5,Name='Sent',Properties=1;
/* query separator */
INSERT IGNORE INTO ###TBLPREFIX###tblMsgFolders SET ID=3,ParentID=0,UserID=1,msg_type=2,obj_type=3,Name='Task',Properties=1;
/* query separator */
INSERT IGNORE INTO ###TBLPREFIX###tblMsgFolders SET ID=4,ParentID=3,UserID=1,msg_type=2,obj_type=13,Name='Done',Properties=1;
/* query separator */
INSERT IGNORE INTO ###TBLPREFIX###tblMsgFolders SET ID=5,ParentID=3,UserID=1,msg_type=2,obj_type=11,Name='rejected',Properties=1;
