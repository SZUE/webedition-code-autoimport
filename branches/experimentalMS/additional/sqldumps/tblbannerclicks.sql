CREATE TABLE tblbannerclicks (
  clickid bigint NOT NULL IDENTITY(1,1) PRIMARY KEY,
  ID bigint  NOT NULL default '0',
  Timestamp int  default NULL,
  IP varchar(30) NOT NULL default '',
  Referer varchar(255) NOT NULL default '',
  DID bigint  NOT NULL default '0',
  Page varchar(255) NOT NULL default ''
) 