CREATE TABLE tblErrorLog (
  ID int(11) NOT NULL auto_increment,
  `Text` text NOT NULL,
  `Date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY  (ID)
) ENGINE=MyISAM;
