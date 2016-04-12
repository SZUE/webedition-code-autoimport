CREATE TABLE ###TBLPREFIX###tblvotinglog (
  `id` int unsigned NOT NULL auto_increment,
  `votingsession` varchar(255) NOT NULL,
  `voting` mediumint unsigned NOT NULL,
  `time` int unsigned NOT NULL,
  `ip` varchar(40) NOT NULL,
  `agent` varchar(255) NOT NULL,
  `userid` bigint unsigned NOT NULL DEFAULT '0',
  `cookie` tinyint unsigned NOT NULL,
  `fallback` tinyint unsigned NOT NULL,
  `status` tinyint unsigned NOT NULL,
  `answer` varchar(255) NOT NULL,
  `answertext` text NOT NULL,
  `successor` int unsigned NOT NULL DEFAULT '0',
  `additionalfields` text NOT NULL,
  PRIMARY KEY  (id),
	KEY voting(voting,userid)
) ENGINE=MyISAM ;
