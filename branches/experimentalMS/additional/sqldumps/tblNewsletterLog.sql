CREATE TABLE tblNewsletterLog (
  ID bigint  NOT NULL IDENTITY(1,1),
  NewsletterID bigint  NOT NULL default '0',
  LogTime bigint  NOT NULL default '0',
  "Log" varchar(255) NOT NULL default '',
  "Param" varchar(255) NOT NULL default '',
  PRIMARY KEY  (ID)
) 
/*

  KEY NewsletterID (NewsletterID)

*/