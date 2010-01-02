CREATE TABLE `tblvotinglog` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `votingsession` varchar(255) NOT NULL,
  `voting` bigint(20) NOT NULL,
  `time` int(11) unsigned NOT NULL,
  `ip` varchar(255) NOT NULL,
  `agent` varchar(255) NOT NULL,
  `userid` bigint(20) NOT NULL DEFAULT '0',
  `cookie` tinyint(1) NOT NULL,
  `fallback` tinyint(1) NOT NULL,
  `status` tinyint(2) NOT NULL,
  `answer` varchar(255) NOT NULL,
  `answertext` text NOT NULL,
  `successor` bigint(20) unsigned NOT NULL DEFAULT '0',
  `additionalfields` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM ;
