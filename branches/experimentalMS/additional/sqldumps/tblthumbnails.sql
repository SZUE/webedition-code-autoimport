CREATE TABLE tblthumbnails (
  ID bigint  NOT NULL IDENTITY(1,1),
  Name varchar(255) NOT NULL default '',
  "Date" int  NOT NULL default '0',
  Format char(3) NOT NULL default '',
  Height int  default NULL,
  Width int  default NULL,
  Ratio tinyint  NOT NULL default '0',
  Maxsize tinyint  NOT NULL default '0',
  Interlace tinyint  NOT NULL default '1',
  Fitinside int  NOT NULL default '0',
  Directory varchar(255) NOT NULL default '',
  Utilize tinyint  NOT NULL default '0',
  Quality tinyint  NOT NULL DEFAULT  '8',
  PRIMARY KEY  (ID)
) 
