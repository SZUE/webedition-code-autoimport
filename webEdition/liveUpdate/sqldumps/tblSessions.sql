CREATE TABLE ###TBLPREFIX###tblSessions (
  `session_id` char(40) NOT NULL DEFAULT '',
  `lockTime` timestamp NULL DEFAULT NULL,
  `lockid` char(23) NOT NULL,
  `touch` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `session_data` longblob NOT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=MyISAM;
