CREATE TABLE ###TBLPREFIX###tblSessions (
  `session_id` binary(40) NOT NULL,
  `lockTime` timestamp NULL DEFAULT NULL,
  `lockid` char(23) NOT NULL,
  `touch` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sessionName` char(15) NOT NULL,
  `session_data` longblob NOT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=MyISAM;
