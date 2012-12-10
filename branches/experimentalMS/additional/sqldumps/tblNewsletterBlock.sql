CREATE TABLE tblNewsletterBlock (
  ID bigint  NOT NULL IDENTITY(1,1),
  NewsletterID bigint  NOT NULL default '0',
  Groups varchar(255) NOT NULL default '',
  "Type" tinyint  NOT NULL default '0',
  LinkID bigint  NOT NULL default '0',
  Field varchar(255) NOT NULL default '',
  Source text NOT NULL,
  Html text NOT NULL,
  Pack tinyint  NOT NULL default '0',
  PRIMARY KEY  (ID)
) 
CREATE INDEX idx_tblNewsletterBlock_NewsletterID ON tblNewsletterBlock(NewsletterID);