CREATE TABLE tblbannerviews (
  viewid bigint NOT NULL IDENTITY(1,1),
  ID bigint NOT NULL default '0',
  "Timestamp" int default NULL,
  IP varchar NOT NULL default '',
  Referer varchar(255) NOT NULL default '',
  DID bigint NOT NULL default '0',
  Page varchar(255) NOT NULL default '',
  PRIMARY KEY (viewid)
)
