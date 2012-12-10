CREATE TABLE tblUpdateLog (
  ID int  NOT NULL IDENTITY(1,1),
  dortigeID int  NOT NULL default '0',
  datum datetime default NULL,
  aktion text NOT NULL,
  versionsnummer varchar(10) NOT NULL default '',
  module text NOT NULL,
  error tinyint  NOT NULL default '0',
  step int  NOT NULL default '0',
  PRIMARY KEY  (ID)
)
