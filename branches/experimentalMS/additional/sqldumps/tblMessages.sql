CREATE TABLE tblMessages (
  ID int  NOT NULL IDENTITY(1,1),
  ParentID int  default NULL,
  UserID int  default NULL,
  msg_type tinyint  NOT NULL default '0',
  obj_type tinyint  NOT NULL default '0',
  headerDate int  default NULL,
  headerSubject varchar(255) default NULL,
  headerUserID int  default NULL,
  headerFrom varchar(255) default NULL,
  headerTo varchar(255) default NULL,
  Priority tinyint  default NULL,
  seenStatus tinyint  NOT NULL default '0',
  MessageText text,
  tag tinyint  NOT NULL default '0',
  PRIMARY KEY  (ID)
) 
CREATE INDEX idx_tblMessages_UserID ON tblMessages(UserID);
CREATE INDEX idx_tblMessages_query ON tblMessages(obj_type,msg_type,ParentID,UserID);