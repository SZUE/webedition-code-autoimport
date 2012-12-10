CREATE TABLE tblNewsletterGroup (
  ID bigint  NOT NULL IDENTITY(1,1),
  NewsletterID bigint NOT NULL default '0',
  Emails text NOT NULL,
  Customers text NOT NULL,
  SendAll tinyint  NOT NULL default '0',
  Filter varbinary(max) NOT NULL,
  Extern text,
  PRIMARY KEY (ID)
 
) 
/*
 KEY NewsletterID (NewsletterID)

*/