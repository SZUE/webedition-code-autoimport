CREATE TABLE tblwidgetnotepad (
  ID bigint  NOT NULL IDENTITY(1,1),
  WidgetName varchar(100) NOT NULL default '',
  UserID int  NOT NULL default '0',
  CreationDate datetime NOT NULL default '0000-00-00',
  Title varchar(255) NOT NULL default '',
  "Text" text NOT NULL,
  Priority  varchar(25) CHECK(Priority IN ('low','medium','high')) NOT NULL default 'low',
  Valid  varchar(25) CHECK(Valid  IN ('always','date','period')) NOT NULL default 'always',
  ValidFrom datetime NOT NULL default '0000-00-00',
  ValidUntil datetime NOT NULL default '0000-00-00',
  PRIMARY KEY  (ID)
)
/* query separator */
INSERT INTO tblwidgetnotepad VALUES ('webEdition', 1,GetDate(), 'Welcome to webEdition!', '', 'low', 'always', GetDate(), '3000-01-01');
