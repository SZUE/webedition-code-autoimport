CREATE TABLE tblContentTypes (
  OrderNr int  NOT NULL default '0',
  ContentType varchar(32) NOT NULL default '',
  Extension varchar(128) NOT NULL default '',
  DefaultCode text NOT NULL,
  IconID int  NOT NULL default '0',
  Template tinyint  NOT NULL default '0',
  "File" tinyint  NOT NULL default '0',
  PRIMARY KEY  (ContentType)
) 
