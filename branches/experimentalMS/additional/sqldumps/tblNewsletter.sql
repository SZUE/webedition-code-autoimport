CREATE TABLE tblNewsletter (
  ID bigint  NOT NULL IDENTITY(1,1),
  ParentID bigint  NOT NULL default '0',
  IsFolder tinyint  NOT NULL default '0',
  Icon varchar(255) NOT NULL default '',
  "Path" varchar(255) NOT NULL default '',
  "Text" varchar(255) NOT NULL default '',
  "Subject" varchar(255) NOT NULL default '',
  Sender varchar(255) NOT NULL default '',
  Reply varchar(255) NOT NULL default '',
  Test varchar(255) NOT NULL default '',
  "Log" text NOT NULL,
  Step int  NOT NULL default '0',
  Offset int  NOT NULL default '0',
  Charset varchar(255) NOT NULL default '',
  isEmbedImages tinyint  NOT NULL default '0',
  PRIMARY KEY  (ID)
) 