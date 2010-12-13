CREATE TABLE tblErrorLog (
  ID int(11) NOT NULL auto_increment,
  `Type` enum('Error','Warning','Parse error','Notice','Core error','Core warning','Compile error','Compile warning','User error','User warning','User notice','Deprecated notice','User deprecated notice','unknown Error') NOT NULL,
  `Function` varchar(255) NOT NULL default '',
  `File` varchar(255) NOT NULL default '',
  `Line` int(11) NOT NULL,
  `Text` text NOT NULL,
  `Backtrace` text NOT NULL,
  `Date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (ID),
  KEY `Date` (`Date`)
) ENGINE=MyISAM;
