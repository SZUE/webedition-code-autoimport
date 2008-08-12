# MySQL dump 8.13
#
# Host: localhost    Database: we_andy_clean_en
#--------------------------------------------------------
# Server version	3.23.37-log

#
# Table structure for table 'tblAnzeigePrefs'
#

CREATE TABLE tblAnzeigePrefs (
  ID int(15) NOT NULL auto_increment,
  strDateiname varchar(255) NOT NULL default '',
  strFelder text NOT NULL,
  PRIMARY KEY  (ID),
  UNIQUE KEY ID (ID),
  KEY ID_2 (ID)
) TYPE=MyISAM;

#
# Dumping data for table 'tblAnzeigePrefs'
#

INSERT INTO tblAnzeigePrefs VALUES (1,'edit_shop_properties','First name:tblWebUser||Forename,Last name:tblWebUser||Surname,Address 1:tblWebUser||Contact_Address1,Address 2:tblWebUser||Contact_Address2,Country:tblWebUser||Contact_Country,Quantity:webE||<quantity>,Title:webE||shoptitle,Description:webE||shopdescription,Amount:webE||<price>,Total:webE||<totalprice>');
INSERT INTO tblAnzeigePrefs VALUES (2,'shop_pref','$|16|english');

#
# Table structure for table 'tblCategorys'
#

CREATE TABLE tblCategorys (
  ID int(11) NOT NULL auto_increment,
  Category varchar(64) NOT NULL default '',
  Text varchar(64) default NULL,
  Path varchar(255) default NULL,
  ParentID bigint(20) default '0',
  IsFolder tinyint(1) default '0',
  Icon varchar(64) default 'cat.gif',
  PRIMARY KEY  (ID)
) TYPE=MyISAM;

#
# Dumping data for table 'tblCategorys'
#

INSERT INTO tblCategorys VALUES (8,'Action','Action','/Action',0,0,'cat.gif');
INSERT INTO tblCategorys VALUES (9,'Comedy','Comedy','/Comedy',0,0,'cat.gif');
INSERT INTO tblCategorys VALUES (12,'Drama','Drama','/Drama',0,0,'cat.gif');
INSERT INTO tblCategorys VALUES (15,'Love Story','Love Story','/Love Story',0,0,'cat.gif');
INSERT INTO tblCategorys VALUES (16,'shop','shop','/shop',0,0,'cat.gif');
INSERT INTO tblCategorys VALUES (18,'shop_broschuere','shop_broschuere','/shop_broschuere',0,0,'cat.gif');
INSERT INTO tblCategorys VALUES (19,'Addresses','Addresses','/Addresses',0,1,'folder.gif');
INSERT INTO tblCategorys VALUES (20,'events','events','/Addresses/events',19,0,'cat.gif');

#
# Table structure for table 'tblCleanUp'
#

CREATE TABLE tblCleanUp (
  Path varchar(255) NOT NULL default '',
  Date int(11) NOT NULL default '0'
) TYPE=MyISAM;

#
# Dumping data for table 'tblCleanUp'
#


#
# Table structure for table 'tblContent'
#

CREATE TABLE tblContent (
  ID bigint(20) NOT NULL auto_increment,
  BDID int(11) NOT NULL default '0',
  Dat longtext,
  IsBinary tinyint(4) NOT NULL default '0',
  AutoBR char(3) NOT NULL default 'off',
  LanguageID int(11) NOT NULL default '0',
  PRIMARY KEY  (ID),
  KEY link (ID,LanguageID),
  KEY search (ID,LanguageID,IsBinary)
) TYPE=MyISAM;

#
# Dumping data for table 'tblContent'
#

INSERT INTO tblContent VALUES (7575,0,'<html>\n   <head>\n      <we:title>CMS Channel</we:title>\n      <we:description>Demo-Website for the CMS webEdition</we:description>\n      <we:keywords>cms,webEdition</we:keywords>\n      <link href=\"<we:url id=\"89\" />\" rel=\"styleSheet\" type=\"text/css\">\n   </head>\n   <body  background=\"/we_demo/layout_images/bg.gif\" bgcolor=\"white\" leftmargin=\"0\" marginwidth=\"0\" topmargin=\"8\" marginheight=\"8\">\n      <we:form id=\"114\" method=\"get\"><table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"620\">\n         <tr>\n            <td width=\"27\"></td>\n            <td width=\"50\"><img src=\"/we_demo/layout_images/we_logo.gif\" width=\"50\" height=\"50\" border=\"0\"></td>\n            <td width=\"54\"></td>\n            <td><span class=\"headline\">&nbsp;CMS Channel - </span><span class=\"headline_small\">Search Results</span></td>\n            <td class=\"normal\" width=\"74\"><we:date type=\"js\" format=\"m/d/Y\"/>&nbsp;</td>\n         </tr>\n         <tr>\n            <td colspan=\"3\"></td>\n            <td colspan=\"2\" align=\"right\"><span class=\"normal\"><b>Search:</b><span class=\"normal\">&nbsp;</span><we:search type=\"textinput\" size=\"15\"/><span class=\"normal\">&nbsp;</span><input type=\"submit\" value=\"ok\"><span class=\"normal\">&nbsp;</span></td>\n         </tr>\n         <tr>\n            <td width=\"27\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"27\" height=\"10\" border=\"0\"></td>\n            <td width=\"50\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"50\" height=\"10\" border=\"0\"></td>\n            <td width=\"54\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"54\" height=\"10\" border=\"0\"></td>\n            <td><img src=\"/we_demo/layout_images/pixel.gif\" width=\"418\" height=\"10\" border=\"0\"></td>\n            <td width=\"74\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"71\" height=\"10\" border=\"0\"></td>\n         </tr>\n         <tr>\n            <td class=\"normal\" width=\"27\"></td>\n            <td colspan=\"2\" class=\"normal\" valign=\"top\"><we:include id=\"90\"/></td>\n            <td bgcolor=\"white\" colspan=\"2\" valign=\"top\">\n               <table cellpadding=\"6\" cellspacing=\"0\" border=\"0\">\n                  <tr>\n                     <td valign=\"top\" class=\"normal\">\n<we:listview rows=\"6\">\n                          <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"500\">\n<we:repeat>\n                                <tr>\n                                   <td class=\"normal\"><b><we:field name=\"Title\" alt=\"we_path\" hyperlink=\"on\"/></b><br><we:field name=\"Description\" alt=\"we_text\" max=\"200\"/></td>\n                                </tr>\n                                <tr>\n                                   <td><img src=\"/we_demo/layout_images/pixel.gif\" width=\"500\" height=\"6\" border=\"0\"></td>\n                               </tr>\n</we:repeat>\n <we:ifNotFound>\n                                 <tr>\n                                   <td colspan=\"3\" class=\"normal\">Sorry, no results were found!</td>\n                               </tr>\n<we:else/>\n                               <tr>\n                                   <td colspan=\"3\" class=\"normal\">\n                                       <table cellpadding=\"0\" border=\"0\" cellspacing=\"0\" width=\"100%\">\n                                         <tr>\n                                           <td colspan=\"2\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"10\" height=\"6\" border=\"0\"></td>\n                                        </tr>\n                                         <tr>\n                                           <td class=\"normal\"><we:ifBack><we:back>&lt;&lt; back</we:back></we:ifBack></td>\n                                           <td class=\"normal\" align=\"right\"><we:ifNext><we:next>next &gt;&gt;</we:next></we:ifNext></td>\n                                        </tr>\n                                      </table>\n                                  </td>\n                               </tr>\n</we:ifNotFound>\n                      </table>\n</we:listview>\n                    </td>\n                 </tr>\n              </table>              \n            </td>\n         </tr>\n      </table></we:form>\n   </body>\n</html>',0,'',0);
INSERT INTO tblContent VALUES (7568,0,'<html>\n   <head>\n      <we:title>CMS Channel</we:title>\n      <we:description>Demo-Website for the CMS webEdition</we:description>\n      <we:keywords>cms,webEdition</we:keywords>\n		<style media=\"screen\" type=\"text/css\"><!--\n#menu1 { position: absolute; z-index: 5; top: 63px; left: 131px; width: 122px; height: 10px;visibility: hidden  }\n#dummy{ position: absolute; z-index: 1; top: 0px; left: 0px; width: 400px; height: 400px;visibility: hidden  }\n--></style>\n      <link href=\"<we:url id=\"89\" />\" rel=\"styleSheet\" type=\"text/css\">\n   </head>\n   <body  background=\"/we_demo/layout_images/bg.gif\" bgcolor=\"white\" leftmargin=\"0\" marginwidth=\"0\" topmargin=\"8\" marginheight=\"8\">\n     <we:form id=\"114\" method=\"get\"><table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"620\">\n         <tr>\n            <td width=\"27\"></td>\n            <td width=\"50\"><img src=\"/we_demo/layout_images/we_logo.gif\" width=\"50\" height=\"50\" border=\"0\"></td>\n            <td width=\"54\"></td>\n            <td><span class=\"headline\">&nbsp;CMS Channel - </span><span class=\"headline_small\">News</span></td>\n            <td class=\"normal\" width=\"74\"><we:date type=\"js\" format=\"m/d/Y\"/>&nbsp;</td>\n         </tr>\n         <tr>\n            <td colspan=\"3\"></td>\n            <td colspan=\"2\"><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\"><tr><td class=\"normal\"><a href=\"#\" onMouseOver=\"pullDown(\'menu1\');\"  style=\"text-decoration:none\">Top News</a></td><td align=\"right\"><span class=\"normal\"><b>Search:</b><span class=\"normal\">&nbsp;</span><we:search type=\"textinput\" size=\"15\"/><span class=\"normal\">&nbsp;</span><input type=\"submit\" value=\"ok\"><span class=\"normal\">&nbsp;</span></td></tr></table></td>\n         </tr>\n         <tr>\n            <td width=\"27\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"27\" height=\"10\" border=\"0\"></td>\n            <td width=\"50\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"50\" height=\"10\" border=\"0\"></td>\n            <td width=\"54\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"54\" height=\"10\" border=\"0\"></td>\n            <td><img src=\"/we_demo/layout_images/pixel.gif\" width=\"418\" height=\"10\" border=\"0\"></td>\n            <td width=\"74\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"71\" height=\"10\" border=\"0\"></td>\n         </tr>\n         <tr>\n            <td class=\"normal\" width=\"27\"></td>\n            <td colspan=\"2\" class=\"normal\" valign=\"top\"><we:include id=\"90\"/></td>\n            <td bgcolor=\"white\" colspan=\"2\" valign=\"top\">\n               <table cellpadding=\"6\" cellspacing=\"0\" border=\"0\">\n                  <tr>\n                     <td valign=\"top\" class=\"normal\">\n<we:listview rows=\"6\" doctype=\"newsArticle\" order=\"Date\" desc=\"true\">\n                          <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"500\">\n<we:repeat>\n                                <tr>\n                                   <td class=\"normal\"><we:field type=\"date\" name=\"Date\" format=\"m/d/Y\"/></td>\n                                   <td><img src=\"/we_demo/layout_images/pixel.gif\" width=\"10\" height=\"2\" border=\"0\"></td>\n                                   <td class=\"normal\"><b><we:field type=\"text\" name=\"Headline\" hyperlink=\"on\"/></b></td>\n                                </tr>\n                                <tr>\n                                   <td><img src=\"/we_demo/layout_images/pixel.gif\" width=\"90\" height=\"6\" border=\"0\"></td>\n                                   <td><img src=\"/we_demo/layout_images/pixel.gif\" width=\"10\" height=\"6\" border=\"0\"></td>\n                                   <td><img src=\"/we_demo/layout_images/pixel.gif\" width=\"400\" height=\"6\" border=\"0\"></td>\n                               </tr>\n</we:repeat>\n <we:ifNotFound>\n                                 <tr>\n                                   <td colspan=\"3\" class=\"normal\">No News available!</td>\n                               </tr>\n<we:else/>\n                               <tr>\n                                   <td colspan=\"3\" class=\"normal\">\n                                       <table cellpadding=\"0\" border=\"0\" cellspacing=\"0\" width=\"100%\">\n                                         <tr>\n                                           <td colspan=\"2\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"10\" height=\"6\" border=\"0\"></td>\n                                        </tr>\n                                         <tr>\n                                           <td class=\"normal\"><we:ifBack><we:back>&lt;&lt; zur�ck</we:back></we:ifBack></td>\n                                           <td class=\"normal\" align=\"right\"><we:ifNext><we:next>weiter &gt;&gt;</we:next></we:ifNext></td>\n                                        </tr>\n                                      </table>\n                                  </td>\n                               </tr>\n</we:ifNotFound>\n                      </table>\n</we:listview>\n                    </td>\n                 </tr>\n              </table>              \n            </td>\n         </tr>\n      </table></we:form>\n<we:include id=\"301\"/>\n   </body>\n</html>',0,'',0);
INSERT INTO tblContent VALUES (7570,0,'<html>\n   <head>\n      <we:title>CMS-Channel</we:title>\n      <we:description>Demo-Website for the CMS webEdition</we:description>\n      <we:keywords>cms,webEdition</we:keywords>\n      <link href=\"<we:url id=\"89\" />\" rel=\"styleSheet\" type=\"text/css\">\n   </head>\n   <body  background=\"/we_demo/layout_images/bg.gif\" bgcolor=\"white\" leftmargin=\"0\" marginwidth=\"0\" topmargin=\"8\" marginheight=\"8\">\n      <we:form id=\"114\" method=\"get\"><table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"620\">\n         <tr>\n            <td width=\"27\"></td>\n            <td width=\"50\"><img src=\"/we_demo/layout_images/we_logo.gif\" width=\"50\" height=\"50\" border=\"0\"></td>\n            <td width=\"54\"></td>\n            <td><span class=\"headline\">&nbsp;CMS-Channel - </span><span class=\"headline_small\">News</span></td>\n            <td class=\"normal\" width=\"74\"><we:date type=\"js\" format=\"m/d/Y\"/>&nbsp;</td>\n         </tr>\n         <tr>\n            <td colspan=\"3\"></td>\n            <td colspan=\"2\" align=\"right\"><span class=\"normal\"><b>Search:</b><span class=\"normal\">&nbsp;</span><we:search type=\"textinput\" size=\"15\"/><span class=\"normal\">&nbsp;</span><input type=\"submit\" value=\"ok\"><span class=\"normal\">&nbsp;</span></td>\n         </tr>\n         <tr>\n            <td width=\"27\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"27\" height=\"10\" border=\"0\"></td>\n            <td width=\"50\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"50\" height=\"10\" border=\"0\"></td>\n            <td width=\"54\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"54\" height=\"10\" border=\"0\"></td>\n            <td><img src=\"/we_demo/layout_images/pixel.gif\" width=\"418\" height=\"10\" border=\"0\"></td>\n            <td width=\"74\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"71\" height=\"10\" border=\"0\"></td>\n         </tr>\n         <tr>\n            <td class=\"normal\" width=\"27\"></td>\n            <td colspan=\"2\" class=\"normal\" valign=\"top\"><we:include id=\"90\"/></td>\n            <td bgcolor=\"white\" colspan=\"2\" valign=\"top\">\n               <table cellpadding=\"6\" cellspacing=\"0\" border=\"0\">\n                  <tr>\n                     <td valign=\"top\" class=\"normal\"><we:ifEditmode><span class=\"normal\"><b>Date:</b><br></span><we:input type=\"date\" name=\"Date\"><br><br></we:ifEditmode>\n                         <we:ifEditmode><span class=\"normal\"><b>Headline:</b></span><br></we:ifEditmode><span class=\"headline_small\"><we:input type=\"text\" name=\"Headline\" size=\"60\"/></span><br><br>\n<we:ifNotEmpty match=\"Bild\">\n                         <table <we:ifNotEditmode>align=\"right\" </we:ifNotEditmode>cellpadding=\"5\" cellspacing=\"0\" border=\"0\" width=\"60\">\n                            <tr>\n                               <td><we:img name=\"Bild\"/></td>\n                           </tr>\n<we:ifNotEmpty match=\"Bildunterschrift\">\n                           <tr>\n                              <td class=\"small\"><we:ifEditmode><span class=\"normal\"><b>Caption:</b></span><br></we:ifEditmode><we:textarea name=\"Bildunterschrift\" rows=\"2\" cols=\"25\"/></td>\n                          </tr>\n </we:ifNotEmpty>\n                      </table>\n  </we:ifNotEmpty>\n                     <we:textarea name=\"Text\" cols=\"60\" rows=\"30\" autobr=\"on\" dhtmledit=\"on\" showMenues=\"on\" importrtf=\"on\"/></td>\n                 </tr>\n              </table>              \n            </td>\n         </tr>\n      </table></we:form>\n   </body>\n</html>',0,'',0);
INSERT INTO tblContent VALUES (7573,0,'<html>\n   <head>\n      <we:title>CMS Channel</we:title>\n      <we:description>Demo-Website for the CMS webEdition</we:description>\n      <we:keywords>cms,webEdition</we:keywords>\n      <link href=\"<we:url id=\"89\" />\" rel=\"styleSheet\" type=\"text/css\">\n   </head>\n   <body  background=\"/we_demo/layout_images/bg.gif\" bgcolor=\"white\" leftmargin=\"0\" marginwidth=\"0\" topmargin=\"8\" marginheight=\"8\">\n      <we:form id=\"114\" method=\"get\"><table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"620\">\n         <tr>\n            <td width=\"27\"></td>\n            <td width=\"50\"><img src=\"/we_demo/layout_images/we_logo.gif\" width=\"50\" height=\"50\" border=\"0\"></td>\n            <td width=\"54\"></td>\n            <td><span class=\"headline\">&nbsp;CMS Channel - </span><span class=\"headline_small\">Program</span></td>\n            <td class=\"normal\" width=\"74\"><we:date type=\"js\" format=\"m/d/Y\"/>&nbsp;</td>\n         </tr>\n         <tr>\n            <td colspan=\"3\"></td>\n            <td colspan=\"2\" align=\"right\"><span class=\"normal\"><b>Search:</b><span class=\"normal\">&nbsp;</span><we:search type=\"textinput\" size=\"15\"/><span class=\"normal\">&nbsp;</span><input type=\"submit\" value=\"ok\"><span class=\"normal\">&nbsp;</span></td>\n         </tr>\n         <tr>\n            <td width=\"27\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"27\" height=\"10\" border=\"0\"></td>\n            <td width=\"50\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"50\" height=\"10\" border=\"0\"></td>\n            <td width=\"54\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"54\" height=\"10\" border=\"0\"></td>\n            <td><img src=\"/we_demo/layout_images/pixel.gif\" width=\"418\" height=\"10\" border=\"0\"></td>\n            <td width=\"74\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"71\" height=\"10\" border=\"0\"></td>\n         </tr>\n         <tr>\n            <td class=\"normal\" width=\"27\"></td>\n            <td colspan=\"2\" class=\"normal\" valign=\"top\"><we:include id=\"90\"/></td>\n            <td bgcolor=\"white\" colspan=\"2\" valign=\"top\">\n               <table cellpadding=\"6\" cellspacing=\"0\" border=\"0\">\n                  <tr>\n                     <td valign=\"top\" class=\"normal\">\n<we:list name=\"Programmliste\">\n                        <table width=\"500\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n                           <tr>\n                              <td class=\"normal\" valign=\"top\"><b><we:input type=\"date\" name=\"Date\" format=\"H:i\"/>&nbsp </b></td>\n                              <td class=\"normal\" valign=\"top\"><we:input type=\"text\" name=\"Sendung\" size=\"40\"/></td>\n                              <td class=\"normal\" valign=\"top\"><we:textarea cols=\"50\" rows=\"2\" name=\"Beschreibung\" autobr=\"on\"/></td>\n                           </tr>\n                           <tr>\n                              <td><img src=\"/we_demo/layout_images/pixel.gif\" width=\"100\" height=\"6\" border=\"0\"></td>\n                              <td><img src=\"/we_demo/layout_images/pixel.gif\" width=\"150\" height=\"6\" border=\"0\"></td>\n                              <td><img src=\"/we_demo/layout_images/pixel.gif\" width=\"250\" height=\"6\" border=\"0\"></td>\n                           </tr>\n                        </table>\n</we:list>\n                    </td>\n                 </tr>\n              </table>              \n            </td>\n         </tr>\n      </table></we:form>\n   </body>\n</html>',0,'',0);
INSERT INTO tblContent VALUES (7170,0,'News, <br>Weatherforecast',0,'',0);
INSERT INTO tblContent VALUES (7574,0,'<html>\n   <head>\n      <we:title>CMS Channel</we:title>\n      <we:description>Demo-Website for the CMS webEdition</we:description>\n      <we:keywords>cms,webEdition</we:keywords>\n      <link href=\"<we:url id=\"89\" />\" rel=\"styleSheet\" type=\"text/css\">\n   </head>\n   <body  background=\"/we_demo/layout_images/bg.gif\" bgcolor=\"white\" leftmargin=\"0\" marginwidth=\"0\" topmargin=\"8\" marginheight=\"8\">\n      <we:form id=\"114\" method=\"get\"><table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"620\">\n         <tr>\n            <td width=\"27\"></td>\n            <td width=\"50\"><img src=\"/we_demo/layout_images/we_logo.gif\" width=\"50\" height=\"50\" border=\"0\"></td>\n            <td width=\"54\"></td>\n            <td><span class=\"headline\">&nbsp;CMS Channel - </span><span class=\"headline_small\">Links</span></td>\n            <td class=\"normal\" width=\"74\"><we:date type=\"js\" format=\"m/d/Y\"/>&nbsp;</td>\n         </tr>\n         <tr>\n            <td colspan=\"3\"></td>\n            <td colspan=\"2\" align=\"right\"><span class=\"normal\"><b>Search:</b><span class=\"normal\">&nbsp;</span><we:search type=\"textinput\" size=\"15\"/><span class=\"normal\">&nbsp;</span><input type=\"submit\" value=\"ok\"><span class=\"normal\">&nbsp;</span></td>\n         </tr>\n         <tr>\n            <td width=\"27\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"27\" height=\"10\" border=\"0\"></td>\n            <td width=\"50\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"50\" height=\"10\" border=\"0\"></td>\n            <td width=\"54\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"54\" height=\"10\" border=\"0\"></td>\n            <td><img src=\"/we_demo/layout_images/pixel.gif\" width=\"418\" height=\"10\" border=\"0\"></td>\n            <td width=\"74\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"71\" height=\"10\" border=\"0\"></td>\n         </tr>\n         <tr>\n            <td class=\"normal\" width=\"27\"></td>\n            <td colspan=\"2\" class=\"normal\" valign=\"top\"><we:include id=\"90\"/></td>\n            <td bgcolor=\"white\" colspan=\"2\" valign=\"top\">\n               <table cellpadding=\"6\" cellspacing=\"0\" border=\"0\">\n                  <tr>\n                     <td valign=\"top\" class=\"normal\">\n<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">\n<we:linklist name=\"Linkliste\"/>\n   <tr>\n      <td class=\"link\" valign=\"top\"><nobr><we:link></nobr></td><td><img src=\"/we_demo/layout_images/pixel.gif\" width=\"20\" height=\"2\" border=\"0\"></td><td valign=\"top\" class=\"normal\"><we:textarea name=\"Erklaerung\" rows=\"2\" cols=\"40\"/></td>\n   </tr>\n<we:postlink>\n<tr>\n<td colspan=\"3\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"2\" height=\"6\" border=\"0\"></td>\n</tr>\n</we:postlink>\n</we:linklist>\n</table>\n                    </td>\n                 </tr>\n              </table>              \n            </td>\n         </tr>\n      </table></we:form>\n   </body>\n</html>',0,'',0);
INSERT INTO tblContent VALUES (7571,0,'<html>\n   <head>\n      <we:title>CMS Channel</we:title>\n      <we:description>Demo-Website for the CMS webEdition</we:description>\n      <we:keywords>cms,webEdition</we:keywords>\n     <link href=\"<we:url id=\"89\" />\" rel=\"styleSheet\" type=\"text/css\">\n   </head>\n   <body  background=\"/we_demo/layout_images/bg.gif\" bgcolor=\"white\" leftmargin=\"0\" marginwidth=\"0\" topmargin=\"8\" marginheight=\"8\">\n      <we:form id=\"114\" method=\"get\"><table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"620\">\n         <tr>\n            <td width=\"27\"></td>\n            <td width=\"50\"><img src=\"/we_demo/layout_images/we_logo.gif\" width=\"50\" height=\"50\" border=\"0\"></td>\n            <td width=\"54\"></td>\n            <td><span class=\"headline\">&nbsp;CMS-Channel - </span><span class=\"headline_small\">Movie Reviews</span></td>\n            <td class=\"normal\" width=\"74\"><we:date type=\"js\" format=\"m/d/Y\"/>&nbsp;</td>\n         </tr>\n         <tr>\n            <td colspan=\"3\"></td>\n            <td colspan=\"2\"><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\"><tr><td class=\"normal\"></td><td align=\"right\"><span class=\"normal\"><b>Search:</b><span class=\"normal\">&nbsp;</span><we:search type=\"textinput\" size=\"15\"/><span class=\"normal\">&nbsp;</span><input type=\"submit\" value=\"ok\"><span class=\"normal\">&nbsp;</span></td></tr></table></td>\n         </tr>\n         <tr>\n            <td width=\"27\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"27\" height=\"10\" border=\"0\"></td>\n            <td width=\"50\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"50\" height=\"10\" border=\"0\"></td>\n            <td width=\"54\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"54\" height=\"10\" border=\"0\"></td>\n            <td><img src=\"/we_demo/layout_images/pixel.gif\" width=\"418\" height=\"10\" border=\"0\"></td>\n            <td width=\"74\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"71\" height=\"10\" border=\"0\"></td>\n         </tr>\n         <tr>\n            <td class=\"normal\" width=\"27\"></td>\n            <td colspan=\"2\" class=\"normal\" valign=\"top\"><we:include id=\"90\"/></td>\n            <td bgcolor=\"white\" colspan=\"2\" valign=\"top\">\n               <table cellpadding=\"6\" cellspacing=\"0\" border=\"0\">\n                  <tr>\n                     <td valign=\"top\" class=\"normal\">\n                         <we:ifEditmode><span class=\"normal\"><b>Movie Title:</b></span><br></we:ifEditmode><span class=\"headline_small\"><we:category/>:&nbsp;<we:input type=\"text\" name=\"Filmtitel\" size=\"60\"/></span><br><br>\n<we:ifNotEmpty match=\"Bild\">\n                         <table <we:ifNotEditmode>align=\"right\" </we:ifNotEditmode>cellpadding=\"5\" cellspacing=\"0\" border=\"0\" width=\"60\">\n                            <tr>\n                               <td><we:img name=\"Bild\"/></td>\n                           </tr>\n<we:ifNotEmpty match=\"Bildunterschrift\">\n                           <tr>\n                              <td class=\"small\"><we:ifEditmode><span class=\"normal\"><b>Caption:</b></span><br></we:ifEditmode><we:textarea name=\"Bildunterschrift\" rows=\"2\" cols=\"25\"/></td>\n                          </tr>\n </we:ifNotEmpty>\n                      </table>\n  </we:ifNotEmpty>\n                     <we:textarea name=\"Text\" cols=\"60\" rows=\"30\" autobr=\"on\" dhtmledit=\"on\" showMenues=\"on\"/></td>\n                 </tr>\n              </table>              \n            </td>\n         </tr>\n      </table>\n</we:form></body>\n</html>',0,'',0);
INSERT INTO tblContent VALUES (7314,0,'cms,webEdition',0,'',0);
INSERT INTO tblContent VALUES (7005,0,'0000001001699700',0,'000',0);
INSERT INTO tblContent VALUES (7169,0,'CMS-Channel',0,'',0);
INSERT INTO tblContent VALUES (7149,0,'The CMS-Market worldwide',0,'',0);
INSERT INTO tblContent VALUES (7203,0,'image/jpeg',0,'',0);
INSERT INTO tblContent VALUES (7204,0,'250',0,'',0);
INSERT INTO tblContent VALUES (7205,0,'250',0,'',0);
INSERT INTO tblContent VALUES (7206,0,'����\0JFIF\0\0\0d\0d\0\0��\0Ducky\0\0\0\0\0\0\0��XICC_PROFILE\0\0\0HLino\0\0mntrRGB XYZ �\0\0	\0\01\0\0acspMSFT\0\0\0\0IEC sRGB\0\0\0\0\0\0\0\0\0\0\0\0\0��\0\0\0\0\0�-HP  \0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0cprt\0\0P\0\0\03desc\0\0�\0\0\0lwtpt\0\0�\0\0\0bkpt\0\0\0\0\0rXYZ\0\0\0\0\0gXYZ\0\0,\0\0\0bXYZ\0\0@\0\0\0dmnd\0\0T\0\0\0pdmdd\0\0�\0\0\0�vued\0\0L\0\0\0�view\0\0�\0\0\0$lumi\0\0�\0\0\0meas\0\0\0\0\0$tech\0\00\0\0\0rTRC\0\0<\0\0gTRC\0\0<\0\0bTRC\0\0<\0\0text\0\0\0\0Copyright (c) 1998 Hewlett-Packard Company\0\0desc\0\0\0\0\0\0\0sRGB IEC61966-2.1\0\0\0\0\0\0\0\0\0\0\0sRGB IEC61966-2.1\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0XYZ \0\0\0\0\0\0�Q\0\0\0\0�XYZ \0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0XYZ \0\0\0\0\0\0o�\0\08�\0\0�XYZ \0\0\0\0\0\0b�\0\0��\0\0�XYZ \0\0\0\0\0\0$�\0\0�\0\0��desc\0\0\0\0\0\0\0IEC http://www.iec.ch\0\0\0\0\0\0\0\0\0\0\0IEC http://www.iec.ch\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0desc\0\0\0\0\0\0\0.IEC 61966-2.1 Default RGB colour space - sRGB\0\0\0\0\0\0\0\0\0\0\0.IEC 61966-2.1 Default RGB colour space - sRGB\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0desc\0\0\0\0\0\0\0,Reference Viewing Condition in IEC61966-2.1\0\0\0\0\0\0\0\0\0\0\0,Reference Viewing Condition in IEC61966-2.1\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0view\0\0\0\0\0��\0_.\0�\0��\0\0\\�\0\0\0XYZ \0\0\0\0\0L	V\0P\0\0\0W�meas\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0�\0\0\0sig \0\0\0\0CRT curv\0\0\0\0\0\0\0\0\0\0\0\n\0\0\0\0\0#\0(\0-\02\07\0;\0@\0E\0J\0O\0T\0Y\0^\0c\0h\0m\0r\0w\0|\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\r%+28>ELRY`gnu|����������������&/8AKT]gqz������������\0!-8COZfr~���������� -;HUcq~���������\r+:IXgw��������\'7HYj{�������+=Oat�������2FZn�������		%	:	O	d	y	�	�	�	�	�	�\n\n\'\n=\nT\nj\n�\n�\n�\n�\n�\n�\"9Qi������*C\\u�����\r\r\r&\r@\rZ\rt\r�\r�\r�\r�\r�.Id����	%A^z����	&Ca~����1Om����&Ed����#Cc����\'Ij����4Vx���&Il����Ae����@e���� Ek���\Z\Z*\ZQ\Zw\Z�\Z�\Z�;c���*R{���Gp���@j���>i���  A l � � �!!H!u!�!�!�\"\'\"U\"�\"�\"�#\n#8#f#�#�#�$$M$|$�$�%	%8%h%�%�%�&\'&W&�&�&�\'\'I\'z\'�\'�(\r(?(q(�(�))8)k)�)�**5*h*�*�++6+i+�+�,,9,n,�,�--A-v-�-�..L.�.�.�/$/Z/�/�/�050l0�0�11J1�1�1�2*2c2�2�3\r3F33�3�4+4e4�4�55M5�5�5�676r6�6�7$7`7�7�88P8�8�99B99�9�:6:t:�:�;-;k;�;�<\'<e<�<�=\"=a=�=�> >`>�>�?!?a?�?�@#@d@�@�A)AjA�A�B0BrB�B�C:C}C�DDGD�D�EEUE�E�F\"FgF�F�G5G{G�HHKH�H�IIcI�I�J7J}J�KKSK�K�L*LrL�MMJM�M�N%NnN�O\0OIO�O�P\'PqP�QQPQ�Q�R1R|R�SS_S�S�TBT�T�U(UuU�VV\\V�V�WDW�W�X/X}X�Y\ZYiY�ZZVZ�Z�[E[�[�\\5\\�\\�]\']x]�^\Z^l^�__a_�``W`�`�aOa�a�bIb�b�cCc�c�d@d�d�e=e�e�f=f�f�g=g�g�h?h�h�iCi�i�jHj�j�kOk�k�lWl�mm`m�nnkn�ooxo�p+p�p�q:q�q�rKr�ss]s�ttpt�u(u�u�v>v�v�wVw�xxnx�y*y�y�zFz�{{c{�|!|�|�}A}�~~b~�#��G���\n�k�͂0����W�������G����r�ׇ;����i�Ή3�����d�ʋ0�����c�ʍ1�����f�Ώ6����n�֑?����z��M��� �����_�ɖ4���\n�u���L���$�����h�՛B��������d�Ҟ@��������i�ءG���&����v��V�ǥ8���\Z�����n��R�ĩ7�������u��\\�ЭD���-������\0�u��`�ֲK�³8���%�������y��h��Y�ѹJ�º;���.���!������\n�����z���p���g���_���X���Q���K���F���Aǿ�=ȼ�:ɹ�8ʷ�6˶�5̵�5͵�6ζ�7ϸ�9к�<Ѿ�?���D���I���N���U���\\���d���l���v��ۀ�܊�ݖ�ޢ�)߯�6��D���S���c���s����\r����2��F���[���p������(��@���X���r������4���P���m��������8���W���w����)���K���m����\0Adobe\0d�\0\0\0��\0�\0\r\r\r\r\Z\Z\"\"###)*-*)#66;;66AAAAAAAAAAAAAAA, ,8(####(825---52==88==AAAAAAAAAAAAAAA��\0\0�\0�\"\0��\0�\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0!1AQaq�\"2�B���R��b#3r��4��CS$��5\0\0\0\0\0\0\0\0!1AQaq\"2���B��R��\0\0\0?\0����E�Mä�n\rH�*5Eh�%\0�ߝ�f�I�\\4��Ss<�tf6a��=��WS���(ˣWi��OYfC)\0_3L��<�-�-�_��B,��G�;(��H(�21Z��v�P��\0�Q��)I�G�s4���;\0_3Y޳�bn\\��a��i\Z�]R?��ﷇm]�\Zh��<T�i�o��e:\\pu>��i�t�)������5��[U\\���0��\Z/:N�l6@\r��Lx�������H��a���\\d- �b�»�Oq�0nN,��I(F\r�,Sn�&���o3[�?0V1b��T��IfV\"��\"�E��s��nrD�\"h��M��n��^bӫX�c|8P�7����cL���E�MwrgCyє���2�-�*�@,4�0P�p�H��þ���^с��� 6?\ZE ���A�G@Z��_<D�q8�X��X�`M�T�jƔI�t�x\n,h \0\n����cB�ŬFj-���8 \n��JG*�eo�8���\n�00��Z���pr���f(�X:qSU�\0N��{ӡ��Fߝ�\0Y�+�4=�w����eV��n\0��y@R{h�F��>ʜ��I���P�q��6�a\\�����/ۍJ1�VӐ���\Z3#�n�	\'\\j>J�\Z�K0��K{�b���Q�ܖk��V�J��]T[1�l	�����KpQ�5�#�����K19_U�*�������n��D,��\0￑>��ۻC��_S\'��,fs����Į�l�Q�\0͘��8�:�@���Q��\Zf�}\rm�%��m�W4x�F7;~�I�p���V�ΤZ1k|jS�Sj<��\'�(�	k�N����8dOVW� _l�-~�UƉB��8����0#s��y���$p4p�����X�SbM�GP}Fڳ�@lq�+!�</�]D\n/�6ƀ͊����{���g �\0_�Ί�������s�B�3+��4��C:ZW���%8�W4I!�7K�(��)\0�U4]���F8�M4�XHnY��O�G�@�s���Y5rb��P���W���.8с�t�ޣ�wg\\��[��5C��3�������Un�ʛ��&�F\r�Z��%=�U�eU^�,̿&��-�:�d�[��ՠn0�)���JF��ޫ���@IpE��U���	�`A��+\Z&Bqo�5�g�i�ˁ\"��i���������:x�񩪑y�_�C�K�f����*�5X\0�?�ߥu�#����Su�3�\nl\"UKd\r��Vs�zH�U�m*����a����̩$�W���S�\0�٧�~B�mln����*�1�\ZC�b���PZG��=�d+F��\Z��Q��AYp	���eɉ���m�y�Ab���+���ۿ06c�ҩ��XI+|M��Ծb�3qF�3Q��KD��Q*s�(���S�my�r��X�{w�i�\Z���\\l:b%�1�P�y�F&�4Ҝ/ƹ�,�(\r�΁)e,F���L��,{�RI�\r�ȓQ��Nb�56��v\0�\\_�bN��p�M��`o|j�\'K�Q����)�q�IΣ�����:4h}�m�Ĝ��NO3�w�kp�Cvs\ZP]���lx�bA�׀n�y�z>�r>\n��ua���ҋ�f�\r(����4D,�ϲ��\n���U�mp	��ap��\n9��<�lLo�q�n��@�Nu��9+��ě灩���;`; M#�P_8���W��*��qƜ�u l�K!����\"oq,xq���.��q.�8�g<�����7]?�12Zb܆C��Q�H�m2K�Ym�1]_T�~��~Ȳ�nc�D�f> U��J�lH64�ME�mk��<���-�l�J/ƾg�y�0�X�N:�v���\Zȱj�q�ȏ�Q%��r>SM�$/eͱ�`����oEDlI?�����a�WI�F��pn\0�S!|�����2q�w�W�q�ĎH\n��FE�M�Y3c|���;��\0-�J����=Ϝ�{+�� �*);Yc�<适叅\'��v�ن���H5�qloM��r>5-� }Xq���:\'b/�N\0$�����֙I���\0�X�\Z�-�9UK�ϻ��U4�K&�I\n;k�j�8Ȉ��ƐEV}V���ՊH28�b�\Z�X�Q�H���ia=�F8�4@u��Г�bjxW#R37��(����q�\\]q�\'�OH\"ⓓ^�p6������ʨq8SK���ϝ,\"F�(��oƟ`-�\Z�Y� �@��c�h�l�\Z,�\n��x�W>�\\]|�38Z�����Ҍ�w�\":e���Ң��2;��j﬇[��v�w,2d�c�Փ_��c?�F�\0s���\r�ݞ�� ����k?��#���OCO���{.)�#���r(�#��T��+R��R�؛���d[���7�jL<��o���AQ��Z�`ݴrp�.�ro����{*2�3�����{��ۏ\n�]M�urB�7�<-ΒAl)���qcΆ�I7���E�v��h6*D`��d�Ag��8rqS�Ƣ�_0<�MHe���:��mn�D�h��B����:��8q�bI}7:lp«�6\' 	Ə�{ţ�B��{8޹��%�Z�Z��0G�\"\n�\\�+S�[۶lxRq�@ؐ~n��%���\0�d�g�i��[*�͢��.�[�B��li@�5�LyГ�:%�E�(�8�R�/�}8�����eQ���r��p@�����H�0�p��G����\Z������`<My\0��A[4��\0��C�ݮ�MX:rxR�b�#��N$�SFD)��.���.&�Z���iM~�b�ܢ��\0n8���2u�B���ʝ����W��U�L?*����h�\r#��gv�Y��U]�_-Y���� �*|��a��e�6\'��(\Z\";�\ZF�r�-~��do6�h�1�ǢA��\rH��-�B��h�)e�a��(]7z��/{��|��ߴq�Wm��[J�5\0\Z��:���@4�W���#�U�$�D��ʁ�\0V�c\n�\n�x�U��䜪{�#��;��2�W�9P�ʜO�6�)����n�]Ax�S�)�\Z����k9x���]�\r������k[id9X�ƣ���Z��&�H�n-����P3����g�����ǐ�.=�t�Q���R�\\M�\r�7E�X��/k`s�ȬU�Vq�^�#�Kfs��� 7k�Mm��ߝ&Њ ���q^�J�N�H1ZFs��`x\Z�Ț��؟W>4P.q*Q7\Z�+0���[��9�Alnq5�m�{|(%ˑ|I7R�y��H,���e��z���@>b�wSP-�\Z�+`�6�5�%�uE���X�mq@��V�����-ͲR,i�P<:Fc�J%���@s%��Q����핀��1l�(p�j8�r��868p��K��.�,�uG�fT�,��\Z\'��������*<�\n����jP���f9�i�`�l��:{P�ͥ08�U7��\0fh�)�[�YQ�\0��MV������׾��>�J�#�(:������:|\r\\��\"I�R�Y�E������D��cF��/�`�F.���m q<�\"q��,�n���ݿpI���!���>�\r����U�w�o�{�����n�>:�O�YmR�W�`5;J3�0��N�\Zi��(f\'s�_���۲9��p���kj6��U�����Υ�P1\0�oʳ�~����BF�k�=1�����Y����u���F�o(d7���s�9W�z��y������ ���Đ*���c�m8�o����\\�������e%#���0*����P�7\"��W�v�O\"�U�Y=����V�a���%A4��8����c1�i+���٤����\\��^���>\"����:�^�0�G��;j.���kT�Fm4���fi\rڗv~�\"���pyT��M\\\0���ܡx\"1��Y�U�6Τ*U��κrι� ���*b�-�hڻ;j�\0�~4�ˍ\rF�b1�)��R\"�+���.3��Rg*�!�5\n\"Ҁ��44ۇqI��;��n-|1&�I\ZmH�I���Tr孚�;�,m�ezD�FV��,��D�Ұ���Ý�AI�}�)���\0o�D �3���c����O�WxS�%��.MWm�cc�8U�d��4��Q�,�?s	��|�n�!��m\\�J�|0�YI�ɔ���R�����\Z��K$v��-Y��3H�_�Y�NY�U�fP�{d2�#�3�FXU���K��yP��mw�P�j\"|�)���{򮳜TPu��HN���#0�b����J�#��:}����<y�X����\0k�3ͳ_m\".�=�з(�J*��p��:\r���f�ȁ���ʪ�ܖ��1����)�\n�\'u��]��~\"��y7L�T� -a��\Z��:�����$�t^R6�r#���Y�T0�d1��y[�� m��4�\0�=�g����L�D��s ҍLlE�j;9�(�����l��@\"��Ď�V��v]j���_�6�S{`��\0VT��?|�m�Xd֬�t�{�{���h��\0H������f�����A`,�W7�*�cҤ�m#�V`#%*?q�]��[7�8�0�W�a��L�j���<�Iw14f��Ee\n3-���\\�����Ɏ;ivH��KG%�`�r_¬v�s�u]򆌝&E~�®�����ڪ��qж���Vy�R$FF\Zv�K�����ru%��i�0o\"Yv�	�\n1T@eb\"�}���n��>�-�[��MhzwR=De�����uk[�cFm@��i.3\n��NX�#�i�h��M��`\0.ó�L��(��/�Q�]%���ƫ��שF|�NY�\nM�Z-]�n#�U��u�4����5\"��E)�cqJcmF��E&���QS�K�t0�4�w�Ʀ�Y\r�5 ��N cWUpq\"Ċ�ဿƺ�h#��*T�34��L��W��N��eM��\"���v���l����Q��cry^����k�£�D�����8r�^�ߩ���#YXԋ]X�®�/kڨ|�uc,�X����F����x�X}:���R�/f�!VP�ȸ(F\nT_�Y�i�Q��R�g�@�4r���9m�b�̝Ok�B��Wb�\'��zo�YU��u96�1j�v�J>�E�j2]\rO���*˺\ZY	�I,�ǆ��^���x�H�����,�.[�m6�ɽ}��zi�C/���n@���¡Ӿ������P�Ol3�/>5���2�60��c�^Q��Ez�s:�$l3\'\\�3��Z���}��7R:L\Z2i�Eq�8�/Xh��	6��n��9��q��cW���N���Ѹ��v�a��Օ�Db����9V����m�[s���vO���$O�a��G��O/$!���cRc`I��lf�Eӣs\Z�*�g��[M����k}f?�N@������H&��Ţ\0I�$��IV�S��S�4��VA�x�ulmul���E�LdN^S�_��*N�F�J_2&��ԝNV�!Զ�,a������Ƥq&��}N�}��#�0�mmB���6�͏�E\"�&��ˇ�a�?R���,����.2��7��̕���g���s�jaf�Z��$���~`���spq��um�kP�-q�j:��\nF*��!\\���b<ڳoF�� ���|*q��dE \"�BUr9wԡ`�Gr�J��J��_�Pb��\\�\'$́N<�BT���R��ŻZ���l0A���W��!7ƗN#����{ߑ�Ȋ�0�Q�E�V\"�`Bi���4R?�a�\Z��\"\nڐ���B����p�:�}&��\0*����g!C�6m7#V�mqλ�w@�Y���}=�HhnU���t�W�Vs{0�rc�Ϊ���*=�l�5�Υ���>Sw��.�ʭ.�Ө��/������?޶]BH\n>��eV�-��_3ru��edޘ�Fa/(E̛w�Ѻ\'G��m���L�=�&�9���?n�u8�:�8l�-�s��ζ��~u��\0D�����wp�ڱd�JM\rŘfj�ⰻ������j�1�bl=$���Vu���h�Ҙ�k*�l��\0�a���B�2�lw[M?SD͒���gڝq��\'�y��!7�e]e8�wc~��o���\0x��%26D��.���\r�ZX��A�g¡O���~�ܙd�-r`�f��� �&�mc�aٮ��ʾP�Yo��\"\'D�s#�G6��P(��}NU��r���RN�4�G�2��]U���[��\0\Z\r�����������2��e�H¯��ꋳ��g!T	<5-���~y�F6�f�v��<-V�:�6�\\����ݵS�r4}\ZE��P�V7���-���D%�D]�|S˘��P�S���\Z��߸�G���\r�1�ŵ;�=��aTK?������3h[a�5��[\r��m���FШp�u�v�<k�=��e�Ȇp?�1P�=��,���Y�Ci\reH�ٻ�-�ۮ����X�N���z�	�W�O�1ĝL ,|�`N\Z�:�����\0v�T����3sk��:	�@�j&��҃�Gk c��;�:C9	��R�X6#\nlZ7W6\"��鵓�g���B����js�8>k\\�71\rZ�\0�t$�\r�jG(��:��;Ey�/kGۋ�ƹ\"i��:������S���u2�\\<\r-.���7�jH��}��Ja�e��u�O0��*��K!�Ϊ��h�,�Ǭq�?�2�m�[���a��\0)��90s(A�]��m ���49!�6�e��_�`hf)���eu_���re�c.�˹1��f[�u >E/�Fx�<()�@���,nC�b�����ެ�kC��s.�z�#��6ѩ�_��±I\Z��E�	\\t���eְyMy7\\N�;4�t��O��$s&�xj�C�U.׬t��_O+���Qow���4/��8��=:^ǝ�n��7�[/U�-9f��\\�a��H�C-���x�<)Ժ�����G~s!�\0�;}ö�N�mfyXa��[X��9\Z|��ƍ��2e~�M�71k��6��6�}��#�^��кgH��_q���j�^E�%�n6Ʈ7�/Z���1�ﯤ	Δ#)�v�ލ�O҄;�)����wO�5\"�H���PvZ[bU�S�I�ޘ�T�${a�01��O5��E���v��~�#�4��͹��d��gfbx�l\08U6f�]A;b�����ˤ�mK�����>�W������Fv�:x�P�,�fM�\0:-�G=\r�3m��S�@W,�-0�� ���5ϷgY�2������7�i��j�V\n�r�k��<mƽsVP)(;�$�dR�7�����[bf�OjTv,@\ZdD�V\0���А�\0ol��B5}���J�t���1�Ҽ�J[��iw[^�ۋ���p*څ������n��\Z��y0�V7�9��㿝\"��X֋������\n�1N�ί�=����\Z��{jB�n*^�r�qK\"����U�]#,��3)�����4�.Q��U\n�.G�q�7��H(�gSuČ�u9R����u_���G�,oΗ�����AF�UU�\r�G�-���A�8\nl\0�r#\n�Ƣ+B�¹�:�d�{ކ�m@�o|h�\0��I�x�8cnt��,��q[��/��ED�љ���,.`�7Y�E���q\"��[wi5�$��7*���n��d�8����f��\0\"�M���ڳ}Ǽ~�vt��X*5���/XZS|ɭbyн�~=�R(#�bs��d���ul��aq�?�e��=?g��ob9\'��wX����S_iEv$`2Oa�D�c�[�\0�\"�lv�T�H������q���ͱM��Lm��>ڳ� |��jl	|H6��}��F���n!Rc\rs��22�Ѡ����B\0Ve\"@�}D-�����$]_l�M���$J̧:��xp5Q<f����,d_~Rm&\"�˦��g�{	7�,���.$��ʫ���Eg꼑�N��*�֗��L���\"4�«�\0��ק�S�Ȭ�BYzxq�5���\r��o�Zc�I�Ǿ\Z&�3F	�0�i~��\";pv�Eh�ʰ�f���Q��Z���9�-�Ѷ�HvѨ}�j��-o1�5����s%˩��\0r���µ3m�q��y$�nK�Ț��5�T�Po}m�!B�%��va�\'�\n��?�j�qfo(\0��Vc�LF�s��`Ejero�?�oM2�m�I�������aI�PW1����Sq�	���@kazGu�u���O3~5�{�[�Q�ٶ�쓦w���,`6��2��V�M�II�8ŔvR�J��A�E���K������`0�����xPc�m��vO¸Ek�v�M�6$P�b�ʗ��{}�� �]��S|,K\'ic�$S�WP#�*P4bA��x��/��Pc�@�}< �k\\ӻ�&�R��񸪴F�N\r̀�~��(�<\rV�([�j�3#\\���h������r�\"���R��� \0����I�X(xs�m����\'E9g���1�s�V�(.���Q�4p�\"��F�RCi<�Q��x�2_g�R����/�O>��kԤK�N�M�7C�pt�5��\"L����{��u6�%Mb�����*�T;��g�e�<j��*X\\y�±oѺ�]M�fw�h���#dPJ3�>v�Z��e�`\'�»I����4k��i�����ݴp��F��e��O~�Z��8J���x���(s�A�o�WV�\"}(O�{4�H�i7�+�D-mr��A�0\"�v�K�n��ڨ��l��P\"��/P�{D\0@9_�R-N�g��HF�c���\0o������N�VUBmrOm�i�U�%ܿ�\nN�ky�_x�̧ܧ���P���1U��T�WS��,`�A[�X�@\\�j�20��A�[]�&�L��7��N�4Cu��VY4��suI�_�gwpn\'��Q�<\\�MXu~��d�Nb۴�#��ͭ~7��^�q�B�t�f���ϡne�U��r�o��n\r�5󮔓t�ɑ�K�2����w�ͷ�[ۑ�+_�pg`{� �N$\Z]�E���N]�#2���Ε��s�H�� e�#[�cf\Z8�Y���\necP������� ��m����I��e��\n�,p=� el��o��VCun9\rO��=E�r\rLŏu34Z�)�b+��b��@�[���o\'Ub\0Ct�����\\�/r�a���E\"���FmBǕ.�r50���Ɩ]`	���FH��Ѐ9p4�K������C��I`CF\'*�ɭ���rPۍF5*F9񮃃1+ $�s�K���`8�מ�7\0���PK##\0Gx�#ٰ��EC�z�����\\@���D�F�!�b�mz��ue�#Ǽ�&�H�a&�sq�¬�-��:gJ1���A���i&���ɭ��2�ۡ�؆�YH+��o��6��ݦ�F#��;j6�Y���u]��&�]�i���U�p��26��Ȍ1X��m������&�m=���v��U�\"�V\"���!�ö��C;6������WV$٪�����G���ܷ�kf��<��\\9\ZL�p��.O񹦙�/����m�\0!Q��n6��Z#o��iB�NF��UU�(ͷ�.sPlO�].�4����oٍ)8�n_q��n�U�`�f�|�+���F!�X]�.m�/��SҝjP�X�a���p��6��4�����z!�C)͏��_�=���Łd]�ɛF8�z���tG�Qa­����Z��1au���^�cPq�Dk�MC��W��<˟\nk��-j�N\r�����c��儡�0�#���\0�����n�dCu[)��lE)���\0��K�l�|R�y��\'����IC��{vP(9#������F,q�Q��ʬAǕR���@�Q��( �\rd,���*�r-���;Li\n���N���c�sPR4�*���c�R\'$KQ�\0P�ʌ�{��:�U7�W,�I�����r�}L�¨#חV���q��k�k�ST`�ԝrS�#�]��?P�����vs��kmY�\Z�5\'�ö��k�I-.�(�`U��ޡ������C��Y���4EV��y�\\\nmԬ�yI#3���\rΨ}�k%�(R�q�Ə�����S���.>�ڰ�������k�l��j�瑲�;��U��bj����N�\0�~&�3e��\"�<w`E�E�x�5W(��4�\"�\0\Z�\0cЉbKO�u��;�I9p���$m�������W�$����:A���8��«c*�	��*�}K[=��]�l���:G! C:��vm�}�;��[]��\n����M�ϸ��O��d{�jU$G3DfV],<�ګz��l�^ٰ�#�Ն���z޺3et���S~]�%M�6���o�5j�\'���Q�־�ћ�vV}4�,.��!�m�gU|����}���{8;�H�`�v65(�f*M�t��%��*P�g�煮&d$�F׷y��fa�\\��ǠЏ�e���W��u�m)/�L�4�>9P��+��\0Br��5)m�[周>k�{�Ǻ�$��A�˷��8����4ɳ�weD|�|(��h�<9Ԕ-�8�7�w��Ő�u=�!mp8�Q#$��?�j��{�2��8Ѽٵ)�1�h��Ҫ�ň�r�%�{��/�\0q�\0J�_7m�`U�;�Dh��6kMq*7�\"[��U��D}#��\\r�0��\0S�\0�����\0��˳��_���\0e������OJ��9vVt�ح���R;)x6�{�K^��̞�\n�z8�ζ���H���w�Z�Eeg�������S3ds����y��_�SP!�L�cb3�I�zw��d[���ݾ�L����ygD����Ӷ��N\rk1���\'�o$��+�A�ݗ�l�ݭ��&��\0�����vU�f2�]�d��ƻlolj\r��.UN�X6�r��|��z��M�}�*��\0����ϱ��',1,'',0);
INSERT INTO tblContent VALUES (7202,0,'14254',0,'',0);
INSERT INTO tblContent VALUES (7164,0,'0000001001696400',0,'000',0);
INSERT INTO tblContent VALUES (7165,0,'Detailed news from around the world',0,'on',0);
INSERT INTO tblContent VALUES (7279,0,'image/jpeg',0,'',0);
INSERT INTO tblContent VALUES (7280,0,'197',0,'',0);
INSERT INTO tblContent VALUES (7281,0,'130',0,'',0);
INSERT INTO tblContent VALUES (7282,0,'����\0JFIF\0\0\0d\0d\0\0��\0Ducky\0\0\0\0\0\0\0��XICC_PROFILE\0\0\0HLino\0\0mntrRGB XYZ �\0\0	\0\01\0\0acspMSFT\0\0\0\0IEC sRGB\0\0\0\0\0\0\0\0\0\0\0\0\0��\0\0\0\0\0�-HP  \0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0cprt\0\0P\0\0\03desc\0\0�\0\0\0lwtpt\0\0�\0\0\0bkpt\0\0\0\0\0rXYZ\0\0\0\0\0gXYZ\0\0,\0\0\0bXYZ\0\0@\0\0\0dmnd\0\0T\0\0\0pdmdd\0\0�\0\0\0�vued\0\0L\0\0\0�view\0\0�\0\0\0$lumi\0\0�\0\0\0meas\0\0\0\0\0$tech\0\00\0\0\0rTRC\0\0<\0\0gTRC\0\0<\0\0bTRC\0\0<\0\0text\0\0\0\0Copyright (c) 1998 Hewlett-Packard Company\0\0desc\0\0\0\0\0\0\0sRGB IEC61966-2.1\0\0\0\0\0\0\0\0\0\0\0sRGB IEC61966-2.1\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0XYZ \0\0\0\0\0\0�Q\0\0\0\0�XYZ \0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0XYZ \0\0\0\0\0\0o�\0\08�\0\0�XYZ \0\0\0\0\0\0b�\0\0��\0\0�XYZ \0\0\0\0\0\0$�\0\0�\0\0��desc\0\0\0\0\0\0\0IEC http://www.iec.ch\0\0\0\0\0\0\0\0\0\0\0IEC http://www.iec.ch\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0desc\0\0\0\0\0\0\0.IEC 61966-2.1 Default RGB colour space - sRGB\0\0\0\0\0\0\0\0\0\0\0.IEC 61966-2.1 Default RGB colour space - sRGB\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0desc\0\0\0\0\0\0\0,Reference Viewing Condition in IEC61966-2.1\0\0\0\0\0\0\0\0\0\0\0,Reference Viewing Condition in IEC61966-2.1\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0view\0\0\0\0\0��\0_.\0�\0��\0\0\\�\0\0\0XYZ \0\0\0\0\0L	V\0P\0\0\0W�meas\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0�\0\0\0sig \0\0\0\0CRT curv\0\0\0\0\0\0\0\0\0\0\0\n\0\0\0\0\0#\0(\0-\02\07\0;\0@\0E\0J\0O\0T\0Y\0^\0c\0h\0m\0r\0w\0|\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\r%+28>ELRY`gnu|����������������&/8AKT]gqz������������\0!-8COZfr~���������� -;HUcq~���������\r+:IXgw��������\'7HYj{�������+=Oat�������2FZn�������		%	:	O	d	y	�	�	�	�	�	�\n\n\'\n=\nT\nj\n�\n�\n�\n�\n�\n�\"9Qi������*C\\u�����\r\r\r&\r@\rZ\rt\r�\r�\r�\r�\r�.Id����	%A^z����	&Ca~����1Om����&Ed����#Cc����\'Ij����4Vx���&Il����Ae����@e���� Ek���\Z\Z*\ZQ\Zw\Z�\Z�\Z�;c���*R{���Gp���@j���>i���  A l � � �!!H!u!�!�!�\"\'\"U\"�\"�\"�#\n#8#f#�#�#�$$M$|$�$�%	%8%h%�%�%�&\'&W&�&�&�\'\'I\'z\'�\'�(\r(?(q(�(�))8)k)�)�**5*h*�*�++6+i+�+�,,9,n,�,�--A-v-�-�..L.�.�.�/$/Z/�/�/�050l0�0�11J1�1�1�2*2c2�2�3\r3F33�3�4+4e4�4�55M5�5�5�676r6�6�7$7`7�7�88P8�8�99B99�9�:6:t:�:�;-;k;�;�<\'<e<�<�=\"=a=�=�> >`>�>�?!?a?�?�@#@d@�@�A)AjA�A�B0BrB�B�C:C}C�DDGD�D�EEUE�E�F\"FgF�F�G5G{G�HHKH�H�IIcI�I�J7J}J�KKSK�K�L*LrL�MMJM�M�N%NnN�O\0OIO�O�P\'PqP�QQPQ�Q�R1R|R�SS_S�S�TBT�T�U(UuU�VV\\V�V�WDW�W�X/X}X�Y\ZYiY�ZZVZ�Z�[E[�[�\\5\\�\\�]\']x]�^\Z^l^�__a_�``W`�`�aOa�a�bIb�b�cCc�c�d@d�d�e=e�e�f=f�f�g=g�g�h?h�h�iCi�i�jHj�j�kOk�k�lWl�mm`m�nnkn�ooxo�p+p�p�q:q�q�rKr�ss]s�ttpt�u(u�u�v>v�v�wVw�xxnx�y*y�y�zFz�{{c{�|!|�|�}A}�~~b~�#��G���\n�k�͂0����W�������G����r�ׇ;����i�Ή3�����d�ʋ0�����c�ʍ1�����f�Ώ6����n�֑?����z��M��� �����_�ɖ4���\n�u���L���$�����h�՛B��������d�Ҟ@��������i�ءG���&����v��V�ǥ8���\Z�����n��R�ĩ7�������u��\\�ЭD���-������\0�u��`�ֲK�³8���%�������y��h��Y�ѹJ�º;���.���!������\n�����z���p���g���_���X���Q���K���F���Aǿ�=ȼ�:ɹ�8ʷ�6˶�5̵�5͵�6ζ�7ϸ�9к�<Ѿ�?���D���I���N���U���\\���d���l���v��ۀ�܊�ݖ�ޢ�)߯�6��D���S���c���s����\r����2��F���[���p������(��@���X���r������4���P���m��������8���W���w����)���K���m����\0Adobe\0d�\0\0\0��\0�\0\r\r\r\r\Z\Z\"\"###)*-*)#66;;66AAAAAAAAAAAAAAA, ,8(####(825---52==88==AAAAAAAAAAAAAAA��\0\0�\0�\"\0��\0�\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0!1AQ\"aq�2R����Bbr#3S��$���T���Csd\0\0\0\0\0\0\0\0!1AQaq��\"�2�R��\0\0\0?\0lM\r>n>?�o\n!�w��*^���o^Un��!�ʳ��2�v�\'���vR��އď(c�;ӑ�Ƭq�Ӓ@�߈֩Ʃ폓)h�����,�G��֒��3yX{V�g\\w\r�\0�kyO��6������ڮ�cE�s���c\r4�_�-�U�NA X_a�JZi�ǈ_a^�*d@��.�sW�Dܙ>K��U����E�yZ����qz��ap3dVPw#[���{)1ʶ#�]dsbzk�R�\rEh!$kaIn�DL��˶��[K{�ֽU\'a\\e�\05�\\D��J�U�<X%Ԣx�����V@�Z�l+v���Q��5���ƓK���$Qsŋ��ʊ�Y��Z�(���H:���WI���9�Q�� �$V��qc���\0�ȕH�P��F6��1F�D\"�`4�oJ�j�O22��\'�\"i����[��;SFI.�����rU�U�a�4�mޘ��w3lu&�׶���h��,�\Z�BO�~��#U�Kz��Z�lva�N��K��^�m�&�FK����X�_el!c���Tt�	nL,���H�b5�Bڭji�2����DڥcIB*��R�;�\'��2�v#�|�	��������9!��{��_*�{�J�Yd7��{꥔\0o�����s�29$�oi����>S�b����W+$��u��U��7�R�2����2��j4���mz �ɜ�9XƖ��Eě����أp[�]�&���#���z#I��\n\r��j\n��wL�f���w7t\'Fֻ��r�pB�pz\Z��u��ƪE\\2���ȓӅ䱲�u OMC�r��YIX���ӳw��r^	�9�3�;F��$�9T����?���R\r-�ɱ���<�$mj�ȣ�`\"��[�?�Hsv�W�!;\Zc��k���b���4\Z�C�wT�J��jiaʡA�.WZ���76��\n|�u�U\Z9���E ��+#���P	Xc/s��d�>`|�!k���D�XH��Y��]M�.VF;J�@��hU_M�Tx�s�q��,���Sv�Z4��EԂ4�֭�;`�1��29%�<Mt(�M�q�B�ƞ�G�6��m$���\0��.,��S7\\wS-�>�W��\"����Xef>T��k�E-B5�J�R�;�C|t;/˙͐�E4��7PN��r�!xZ�*I�_=�WF�U�b44�1��7?)=MJ�P�h5n���ټ��:ؑSg�.9c\Z��WH���+d!1���Z�v\\0�@?1�}�X�ܨ7�6��-�������~Z�����_�_��R��Ig¯��e�х�~`.��][�|��G���b����o�Z�(�+ۘ�PmD�V�����zT9N&=K*FZ5g�$oM�F��P^R����\Z\Z��4gҖ^lt<��L]�iTV��5��)x@��Ѽ���~b7�չ��r,zk���Z(~^e�s��_Ƈž!x�N����Xx�q�rѣhwl�s�0$WQ�\0�T:�`~4�&l\\|�f�K�	?)7�3ā�4ŗ����VVnAFF@�k��{i�vP.)�m�U�@�An n}�Z�9FT8.9(7*\r��t�w��G�f$8|���%���湊�{>�i&FYI|�EPB��M-��Xի�ʗ+%�I�i\Z���~��|���Z�eƐ/.�Z�Zp��\r��\r�G�S��W�:G4R�0�+�V�S�����+j�j%�9>�R��\0+��\'��>ڕ����~\"}(�E�2c��oE�O����zQ��\\x�#��l��U�D���AҲiW9�%G��V�}��SE,i%���������Fb]JHW��_�/�r\'~�K[����T������n&����PxO�*��0\\u�p�t�$�+\rB���[-�D9G��ͯ����cY����9:������%cw.�Rտp������6�Fa����d%�J,���<OCj�1$��2�<�Đ��6%�1�%̥J(�{u�4pyr1�G+( ܩ�Xij�΀i��Nw.&��2�DS��hkS���/v�����1��\'�F�V]�x�v\"���&\0�\0��+����4*f[�,�����όi�T��3q�O�:WM�؝��3�E\Z�p@I�K�M7e���A�\\��q�=���c��>�j6�r���U$_�\"���l������\n��D/��K,�~�Pmy���8�>N��ܛn[\r���I�\0ٿ��\0[~�����_����F�\Z��׶Һ�Q��J����j/*p1c�6��*�\"�k��{^�\\|©9�������Mqڿ���W5�Fh�)7������:��;�rڀ=Է#Ҕ_�OiM�;yA~�3VǉH�(1�$�Xj_�DO��9#��?��\0*�%tW���i�.4��v�!r�,e6Յ��ۚ)rs�Ug\0}�Xʎ��H+�\'�j�<H��ǐ��:{�>��b\\��GR�E`Շ�Tœ/���k]~�����NT�<p�_h�����X?�d6�$���G�C�c�����Bk���X��Z�����o�j�!ݒ�|��ٽ�q�X�V���v;k�_oJK.FDO�%�_ҷ?(q�7���-H;�͍��/�qӨ���]���~��&������`�r�+�6`��t�\n�X��	C+����Sd�b3+4,yp�zg۟��>�eb�ɱz�6uh\'�O퍸�6�Yi�֥FJ�mz����Vaz�8C�cu����W��nJ�\05�	k�h�&V\"����������=x̗��}�>4�Dp�||i�����l|��\\x���V�������ݴ�\0�I�<r��2�կb�^������ݓNA�J!͍��J��_)n;��1��x�ll��yq�A�de�ԏ\Z��0d��2K	}��S�߷���Znq��;XQ���4\0��/&V�]t��ֻ��\'�m�.r�,1+��P��&�L�,aȱ;�G�bl	�E��4;�����V\"w��y�����G�)G-�Kvq�N�N֫��c�G���ij�gI0�_���|��q�א7�S��!꼡��[i��9Y�++��6�m(��$34��)\Z��F���\n7���6A*�Vq��Wڿ��,o���\Z�h���>ɭ�Z0����\0����R��3}g��O�ԭ�z�l\04�c����Y4aV$�ɎYn6�f}D�Cק����2FZǸ�ؤ�5=+�ڝ5���&,���\'�|w�T�Dgi c~_�:�P�j_�apn)�_pZ��rں~��4���y4�,gd*�\0|v:Ӿ�O�Q��u��Ldz��v�ӽd底�FU���f��ب*G����H�!4#kx�4��*9K,o{�Sv�P����>@ʣ��/}��m~�F��cs̛�}���\"W[�rSZ�@[{\r맮�$��jal2�q�L_�p��Ƽ�I��F�X��h	d\"$`z�o�3�eHP^�\\�j������\\��%|��b��y��A�غ��ɩ���V8���5���n��*�%�.Μ\0����4�V}��i�\"�=�\0¥���J\0K����zקj`�{FANrԼ�����/��$�@}H,|*7Y-W���n,�o�A�Ľ�\0\0�\04���\n�$�Z�>�$%d�D>R��=ZB�m��9$H^N\\�[~eg�\0:q�{�z��g[Jџ>�~�xچ��fO&�c@y�[_�����;�;���Ek�j[�E���Ռ����i$�PN���\0�n��)DX/\"�1!�-��_�\'M�)�a������!���E����z\Z��!�(6kx��P�M\r�\0�]�P���i#h�_7�k^�R� ؖ���Ǔ�N�\Z�@g�a����밹�1�h��ZI#�k�ۑ�Tl^m�\0,9�r=Zՙ�E�n9ؙ\Z�O��Q���4#�zzj_��\0���*�~��]�}�T�??�c�[��\'%�x�r����VMMQ���;����,|)nEКz�\"-B�EtȰ�5�S��,�Z_����\Z	��\rN\\A�c��A�%iD}�;|�v��P1g�؋�X34���}R��=�.��lƙ��特��K���\"]0u�Bd��? w _J�l��0�P~�M^�i�Nݭ��f�dAo��_�\"�ok/�P��`�H ����#	%Dʷ���$�]^ľ�6��a��Q�7W�Sph�1<]�\'AmH��Y:��m�k���:�,���Xfa��M��P�|x�\0�[\"O���\\�g͚(�q���+��{h�0�I\r���$.�}s޶m��ըOS��.?��k|�j���\0���\0�6����T���\0��b�&�U�k+Խv��#s�!^���aeږR���f�;��:�}F�W�Ёɽ�r5�9��-��X�|�����4$����k��֤�Xno��kT��\\uV��V\'CU����6��iQc\\�j����mX�X9G%f��Ⱦ(w���<f\"\\v-�U���ƹ��k��4�0��|v�����R�X���=4�m���T��g�ԩs+��jT�]G9�J�+��J��EXT�A��:VF�*V�.P�^T�L)+Q��T�Xǆ���˱���^6������u*R���`�Tt���\0�R�q\'��',1,'',0);
INSERT INTO tblContent VALUES (7576,0,'<script language=\"JavaScript1.2\">\nfunction MM_findObj(n, d) { //v4.0\n  var p,i,x;  if(!d) d=document; if((p=n.indexOf(\"?\"))>0&&parent.frames.length) {\n    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}\n  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];\n  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);\n  if(!x && document.getElementById) x=document.getElementById(n); return x;\n}\nfunction MM_showHideLayers() { //v3.0\n  var i,p,v,obj,args=MM_showHideLayers.arguments;\n  for (i=0; i<(args.length-2); i+=3) if ((obj=MM_findObj(args[i]))!=null) { v=args[i+2];\n    if (obj.style) { obj=obj.style; v=(v==\'show\')?\'visible\':(v=\'hide\')?\'hidden\':v; }\n    obj.visibility=v; }\n}\n\nfunction pullDown(layer){\n	MM_showHideLayers(\'dummy\',\'\',\'show\');\n	MM_showHideLayers(layer,\'\',\'show\');\n}\n\nfunction pullUp(){\n<we:ifNotWebEdition>\n	MM_showHideLayers(\'dummy\',\'\',\'hide\');\n	MM_showHideLayers(\'menu1\',\'\',\'hide\');\n</we:ifNotWebEdition>\n}\n</script>\n<div id=\"menu1\">\n<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">\n<tr><td class=\"normal\">&nbsp;</td></tr>\n<tr><td  bgcolor=\"silver\"><table border=\"0\" cellpadding=\"3\" cellspacing=\"0\">\n<we:linklist name=\"Linklist\">\n<tr><td class=\"normal\"><we:link/></td></tr>\n</we:linklist>\n</table></td></tr>\n</table></div>\n<div id=\"dummy\"><a href=\"#\" onMouseOver=\"pullUp();\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"400\" height=\"400\" border=\"0\"></a></div>\n',0,'',0);
INSERT INTO tblContent VALUES (7325,0,' ',0,'',0);
INSERT INTO tblContent VALUES (7591,0,'<we:ifnewsletterexists><we:ifHtmlMail>To unsubscribe from this newsletter click <a href=\"<we:newsletterUnsubscribeLink id=\"310\" />\">here</a>\n<we:else>To unsubscribe from this newsletter click the following link: <we:newsletterUnsubscribeLink id=\"310\" /></we:ifHtmlMail>\n<we:else/>The Newsletter Module is not installed!</we:ifnewsletterexists>',0,'',0);
INSERT INTO tblContent VALUES (7061,0,'Sonax',0,'',0);
INSERT INTO tblContent VALUES (7367,0,'Beste Gr��e\nIhr webEdition Team',0,'',0);
INSERT INTO tblContent VALUES (7580,0,'<we:createShop shopname=\"shopers\"/><table width=\"265\" border=\"0\">\n <tr>\n   <td>&nbsp;</td>\n   <td class=\"small\" bgcolor=\"silver\" align=\"center\" colspan=\"3\"><we:a id=\"148\">to the basket</we:a></td>\n </tr>\n<we:repeatShopItem shopname=\"shopers\">\n <tr>\n  <td>&nbsp;</td>\n  <td class=\"small\"  bgcolor=\"white\"><we:field name=\"Bild\" type=\"img\" hyperlink=\"on\" border=\"0\" height=\"30\" width=\"30\" align=\"top\"/></td> \n  <td class=\"small\" ><we:showShopItemNumber shopname=\"shopers\"/> x <b><we:field name=\"Artikelname\" alt=\"we_path\" hyperlink=\"on\"/></b><br>Total: EURO <we:calculate sum=\"waren\" num_format=\"english\"><we:showShopItemNumber shopname=\"shopers\">*<we:field name=\"Preis\"/></we:calculate></td>\n  <td bgcolor=\"white\" border=\"0\" height=\"30\" width=\"30\" align=\"left\"><we:a id=\"self\" delarticle=\"on\"><img src=\"/we_demo/shop/images/basket_out2.gif\" width=\"26\" height=\"26\" border=\"0\"></we:a></td>\n</tr>    \n</we:repeatShopItem>\n<tr>\n <td colspan=\"4\">&nbsp;</td>\n </tr>\n<tr>\n <td></td>\n <td align=\"left\" colspan=\"3\">\n   <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n      <tr><td class=\"small\" colspan=\"2\" align=\"center\" bgcolor=\"silver\"><b>Sum</b></td></tr>\n      <tr><td class=\"small\" align=\"right\"><b>Euro:&nbsp;</b></td><td class=\"normal\" align=\"right\"><we:sum name=\"waren\" num_format=\"english\"/></td></tr>\n      </tr><td class=\"small\" align=\"right\">(incl. VAT):&nbsp;</td><td class=\"normal\" align=\"right\"><we:addPercent percent=\"16\" num_format=\"english\"><we:sum name=\"waren\"/></we:addPercent></td></tr>\n   </table>\n </td>\n</tr>\n</table>',0,'',0);
INSERT INTO tblContent VALUES (7524,0,'Shop',0,'',0);
INSERT INTO tblContent VALUES (6936,0,'webEdition twenyty domains with User module and scheduler',0,'',0);
INSERT INTO tblContent VALUES (7518,0,'<strong>webEdition TWENTY</strong>  is for the administration of 20 domains. <br>\nThe target group is medium-sized Internet companies who want to\nmaintain their website dynamically. This bundle contains User module\nand Scheduler for 20 domains.<br>\n<br>',0,'on',0);
INSERT INTO tblContent VALUES (7517,152,'',0,'',0);
INSERT INTO tblContent VALUES (7431,0,' ',0,'',0);
INSERT INTO tblContent VALUES (7432,0,'Additional licences of webEdition for twenty domains.\n\n',0,'off',0);
INSERT INTO tblContent VALUES (7477,0,'webEdition for five domains',0,'',0);
INSERT INTO tblContent VALUES (7475,0,'cms,webEdition',0,'',0);
INSERT INTO tblContent VALUES (7583,0,'<we:makeMail />\nNew order<br><br>\nName: <we:sessionField name=\"Forename\" type=\"print\"/> <we:sessionField name=\"Surname\" type=\"print\"/><br>\nAddress: <we:sessionField name=\"Contact_Address1\" type=\"print\"/>  <we:sessionField name=\"Contact_Address2\" type=\"print\"/><br><br>\nCountry: <we:sessionField name=\"Contact_Country\" type=\"print\"/><br>\nGoods:\n<we:createShop shopname=\"shopers\"/><br>\n<we:repeatShopItem shopname=\"shopers\">\nItem: <we:field name=\"Title\" alt=\"we_path\"> &nbsp; <we:field name=\"Description\" alt=\"we_text\" max=\"200\"/><br>\nAmount: &nbsp;<we:showShopItemNumber shopname=\"shopers\"/><br>\nPrice: &nbsp; EURO &nbsp;<we:calculate sum=\"mailsum\"><we:showShopItemNumber shopname=\"shopers\">*<we:field name=\"Preis\"/></we:calculate><br><br>\n</we:repeatShopItem>\nSum: EURO <we:sum name=\"mailsum\"/>',0,'',0);
INSERT INTO tblContent VALUES (7328,0,'a:2:{i:0;a:12:{s:4:\"href\";s:1:\"#\";s:4:\"text\";s:13:\"CMS Highlight\";s:6:\"target\";s:0:\"\";s:4:\"type\";s:3:\"int\";s:5:\"ctype\";s:4:\"text\";s:2:\"nr\";i:1;s:2:\"id\";s:3:\"128\";s:7:\"attribs\";s:0:\"\";s:13:\"jswin_attribs\";a:11:{s:5:\"jswin\";N;s:8:\"jscenter\";N;s:6:\"jsposx\";s:0:\"\";s:6:\"jsposy\";s:0:\"\";s:7:\"jswidth\";s:0:\"\";s:8:\"jsheight\";s:0:\"\";s:8:\"jsstatus\";N;s:12:\"jsscrollbars\";N;s:9:\"jsmenubar\";N;s:11:\"jsresizable\";N;s:10:\"jslocation\";N;}s:6:\"img_id\";s:0:\"\";s:7:\"img_src\";s:0:\"\";s:11:\"img_attribs\";a:7:{s:5:\"width\";s:0:\"\";s:6:\"height\";s:0:\"\";s:6:\"border\";s:0:\"\";s:6:\"hspace\";s:0:\"\";s:6:\"vspace\";s:0:\"\";s:5:\"align\";s:0:\"\";s:3:\"alt\";s:0:\"\";}}i:1;a:12:{s:4:\"href\";s:1:\"#\";s:4:\"text\";s:11:\"drafty Page\";s:6:\"target\";s:0:\"\";s:4:\"type\";s:3:\"int\";s:5:\"ctype\";s:4:\"text\";s:2:\"nr\";i:2;s:2:\"id\";s:2:\"96\";s:7:\"attribs\";s:0:\"\";s:13:\"jswin_attribs\";a:11:{s:5:\"jswin\";N;s:8:\"jscenter\";N;s:6:\"jsposx\";s:0:\"\";s:6:\"jsposy\";s:0:\"\";s:7:\"jswidth\";s:0:\"\";s:8:\"jsheight\";s:0:\"\";s:8:\"jsstatus\";N;s:12:\"jsscrollbars\";N;s:9:\"jsmenubar\";N;s:11:\"jsresizable\";N;s:10:\"jslocation\";N;}s:6:\"img_id\";s:0:\"\";s:7:\"img_src\";s:0:\"\";s:11:\"img_attribs\";a:7:{s:5:\"width\";s:0:\"\";s:6:\"height\";s:0:\"\";s:6:\"border\";s:0:\"\";s:6:\"hspace\";s:0:\"\";s:6:\"vspace\";s:0:\"\";s:5:\"align\";s:0:\"\";s:3:\"alt\";s:0:\"\";}}}',0,'',0);
INSERT INTO tblContent VALUES (7189,0,'.headline  { color: black; font-weight: bold; font-size: 20px; font-family: Verdana }\n.headline_small { color: black; font-weight: bold; font-size: 16px; font-family: Verdana }\n.normal { color: black; font-size: 12px; font-family: Verdana }\n.link { font-size: 12px; font-family: Verdana }\n.small{ color: black; font-size: 10px; font-family: Verdana }\n.small a{ color: black; font-size: 10px; font-family: Verdana }\n.small a:hover{ color: red; font-size: 10px; font-family: Verdana }\na { color: black; font-size: 12px; font-family: Verdana }\na:hover{ color:red; font-size: 12px; font-family: Verdana }\n.hdl1 { color: #9cf; font-size: 18px; font-family: Arial, sans-serif }\n.hdl1 a{ color: #9cf; font-size: 18px; font-family: Arial, sans-serif;text-decoration:none }\n.hdl1 a:hover{ color: white; font-size: 18px; font-family: Arial, sans-serif;text-decoration:none }\n.hdl2 { color: black; font-size: 18px; font-family: Arial, sans-serif }\n.hdl2 a{ color: black; font-size: 18px; font-family: Arial, sans-serif;text-decoration:none }\n.hdl2 a:hover{ color: white; font-size: 18px; font-family: Arial, sans-serif;text-decoration:none }\n.hdl4 { color: #99CC00; font-size: 18px; font-family: Arial, sans-serif }\n.hdl4 a{ color: #99CC00; font-size: 18px; font-family: Arial, sans-serif;text-decoration:none }\n.hdl4 a:hover{ color: #FFFFFF; font-size: 18px; font-family: Arial, sans-serif;text-decoration:none }\n.norm0  { color: black; font-size: 11px; font-family: Arial, sans-serif; letter-spacing: 1px }\n.norm1  { color: white; font-size: 11px; font-family: Arial, sans-serif; letter-spacing: 1px }\n.norm2   { color: white; font-size: 11px; font-family: Verdana, sans-serif }\n.norm2  a { color: white; font-size: 11px; font-family: Verdana, sans-serif;text-decoration:none }\n.norm2  a:hover { color: white; font-size: 11px; font-family: Verdana, sans-serif;text-decoration:none }\n.norm3   { color: black; font-size: 11px; font-family: Verdana, sans-serif }\n.norm3  a{ color: black; font-size: 11px; font-family: Verdana, sans-serif }\n.norm3  a:hover{ color: white; font-size: 11px; font-family: Verdana, sans-serif }\n.norm4   { color: black; font-size: 11px; font-family: Verdana, sans-serif }\n.norm4  a { color: black; font-size: 11px; font-family: Verdana, sans-serif;text-decoration:none  }\n.norm4  a:hover { color: white; font-size: 11px; font-family: Verdana, sans-serif;text-decoration:none  }\n.bold1   { color: #9cf; font-weight: bold; font-size: 11px; font-family: Verdana, sans-serif }\n.nav0 { color: black; font-size: 10px; font-family: Verdana }\n.nav0 a { color: black; font-size: 10px; font-family: Verdana;text-decoration:none }\n.nav0 a:hover { color: white; font-size: 10px; font-family: Verdana;text-decoration:none }\n.nav0b { color: white; font-size: 10px; font-family: Verdana;text-decoration:none }\n.nav0b a { color: white; font-size: 10px; font-family: Verdana;text-decoration:none }\n.nav1 { color: black; font-weight: bold; font-size: 11px; font-family: Verdana }\n.nav1 a { color: black; font-weight: bold; font-size: 11px; font-family: Verdana;text-decoration:none }\n.nav1 a:hover { color: white; font-weight: bold; font-size: 11px; font-family: Verdana;text-decoration:none }\n.nav2 { color: white; font-size: 10px; font-family: Verdana }\n.nav3 { color: white; font-weight: bold; font-size: 11px; font-family: Verdana }\n.nav3 a { color: white; font-weight: bold; font-size: 11px; font-family: Verdana;text-decoration:none }\n.nav4 { color: black; font-size: 10px; font-family: Verdana }\n.nav4 a { color: black; font-size: 10px; font-family: Verdana;text-decoration:none }\n.nav4 a:hover { color: #7F9F1C; font-size: 10px; font-family: Verdana;text-decoration:none }\n.nav5 { color: black; font-size: 10px; font-family: Verdana }\n.nav5 a { color: black; font-size: 10px; font-family: Verdana;text-decoration:none }\n.nav5 a:hover { color: white; font-size: 10px; font-family: Verdana;text-decoration:none }\n.hdl3{	color: black;	font-size: 12px;font-family: Verdana, sans-serif;font-weight : bold; text-decoration:none }\n.norm5   {color: #1559b0;	font-size: 10px;font-family: Verdana, sans-serif;font-weight : bold; text-decoration:none }\n.norm5   a{color: #1559b0;	font-size: 10px;font-family: Verdana, sans-serif;font-weight : bold;}\n.norm6   {color: white;	font-size: 14px;font-family: Verdana, sans-serif;font-weight : bold;}\n.norm7   {color: black;	font-size: 10px;font-family: Verdana, sans-serif;font-weight : bold;}\n.norm8   {color: #1559b0;	font-size: 14px;	font-family: Verdana, sans-serif;font-weight : bold;}\n.norm9  { color: #1559b0; font-size: 11px; font-weight : bold;font-family: Arial, sans-serif; }\n.norm10  { color: #1559b0; font-size: 11px; font-family: Arial, sans-serif; }\n.norm11  { color: white; font-size: 11px; font-weight : bold;font-family: Arial, sans-serif; }\n.norm12   {color: #1559b0;	font-size: 12px;	font-family: Verdana, sans-serif;font-weight : bold;letter-spacing: 1px }\n.norm13   {color: #1559b0;	font-size: 12px;	font-family: Verdana, sans-serif;letter-spacing: 1px }\n.norm14   {color: black;font-size: 10px;font-family: Verdana, sans-serif;text-decoration : underline;}\n.norm15  {color: black;	font-size: 11px;font-family: Verdana, sans-serif;font-weight : bold;}\n.norm16 {color: black;font-size: 10px;font-family: Verdana, sans-serif;font-weight: bold; text-decoration:none }\n.norm16  a {color: black;font-size: 10px;font-family: Verdana, sans-serif;font-weight: bold; text-decoration:none }\n.norm16  a:hover {color: red;font-size: 10px;font-family: Verdana, sans-serif;font-weight: bold; text-decoration:none }',0,'',0);
INSERT INTO tblContent VALUES (7578,0,'<html>\n   <head>\n      <we:title>Shop</we:title>\n      <we:description>Demo-Website for the CMS webEdition</we:description>\n      <we:keywords>cms,webEdition</we:keywords>\n      <link href=\"<we:url id=\"89\" />\" rel=\"styleSheet\" type=\"text/css\">\n   </head>\n   <body  background=\"/we_demo/layout_images/bg.gif\" bgcolor=\"white\" leftmargin=\"0\" marginwidth=\"0\" topmargin=\"8\" marginheight=\"8\">\n <we:ifshopexists>\n     <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"600\">\n         <tr>\n            <td width=\"27\"></td>\n            <td width=\"50\"><img src=\"/we_demo/layout_images/we_logo.gif\" width=\"50\" height=\"50\" border=\"0\"></td>\n            <td width=\"54\"></td>\n            <td><span class=\"headline\">&nbsp;Shop-Channel - </span><span class=\"headline_small\">Item</span></td>\n            <td class=\"normal\" width=\"74\"></td>\n         </tr>\n         <tr>\n            <td width=\"27\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"27\" height=\"10\" border=\"0\"></td>\n            <td width=\"50\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"50\" height=\"10\" border=\"0\"></td>\n            <td width=\"54\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"54\" height=\"10\" border=\"0\"></td>\n            <td><img src=\"/we_demo/layout_images/pixel.gif\" width=\"418\" height=\"10\" border=\"0\"></td>\n            <td width=\"74\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"71\" height=\"10\" border=\"0\"></td>\n         </tr>\n         <tr>\n            <td class=\"normal\" width=\"27\"></td>\n            <td colspan=\"2\" class=\"normal\" valign=\"top\"><we:include id=\"90\"/></td>\n            <td bgcolor=\"white\" colspan=\"2\" valign=\"top\">\n               <table cellpadding=\"6\" cellspacing=\"0\" border=\"0\">\n                  <tr>\n                     <td valign=\"top\" class=\"normal\">\n                         <we:ifEditmode><span class=\"normal\"><b>Item name:</b></span><br></we:ifEditmode><span class=\"headline_small\"><we:input type=\"text\" name=\"Artikelname\" size=\"60\"/></span><br><br>\n<we:ifNotEmpty match=\"Bild\">\n                         <table <we:ifNotEditmode>align=\"right\" </we:ifNotEditmode>cellpadding=\"5\" cellspacing=\"0\" border=\"0\" width=\"60\">\n                            <tr>\n                               <td><we:img name=\"Bild\"/></td>\n                           </tr>\n<we:ifNotEmpty match=\"Caption\">\n                           <tr>\n                              <td class=\"small\"><we:ifEditmode><span class=\"normal\"><b>Caption:</b></span><br></we:ifEditmode><we:textarea name=\"Bildunterschrift\" rows=\"2\" cols=\"25\"/></td>\n                          </tr>\n </we:ifNotEmpty>\n                      </table>\n  </we:ifNotEmpty>\n                     <we:textarea name=\"Text\" cols=\"60\" rows=\"10\" autobr=\"on\" dhtmledit=\"on\" showMenues=\"on\" importrtf=\"on\"/>\n<we:ifEditmode><br><br><span class=\"norm12\"><b>Price:</b></span><br></we:ifEditmode><br><span class=\"normal\"><b>EURO<b> <we:input type=\"text\" name=\"Preis\" size=\"60\" num_format=\"english\"/></span>\n<br><br><we:a id=\"148\" shop=\"on\">[Order]</we:a>					 \n</td>\n                 </tr>\n              </table> \n <we:ifEditmode><table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n<tr><td class=\"norm12\"><b>Order:</b></td><td><we:input type=\"text\" name=\"Ordnung\" size=\"2\"/></td></tr>\n<tr><td class=\"norm12\"><b>Item title:</b></td><td><we:input type=\"text\" name=\"shoptitle\" size=\"30\"/></td></tr>\n<tr><td class=\"norm12\"><b>Item description:</b></td><td><we:textarea rows=\"5\" cols=\"30\" name=\"shopdescription\"/></td></tr>\n</table></we:ifEditmode>         \n            </td>\n<td valign=\"top\"><we:include id=\"149\"/> </td>\n         </tr>\n      </table>\n<we:else/>\nUnfortunately the shop is not installed!\n</we:ifshopexists>\n   </body>\n</html>\n',0,'',0);
INSERT INTO tblContent VALUES (7513,0,'webEdition TWENTY Bundle User module/Scheduler',0,'',0);
INSERT INTO tblContent VALUES (7514,0,'webEdition TWENTY with User module and Scheduler for 20 domains',0,'off',0);
INSERT INTO tblContent VALUES (7515,0,'7',0,'',0);
INSERT INTO tblContent VALUES (7516,0,'webEdition TWENTY Bundle User module./Schedulder',0,'',0);
INSERT INTO tblContent VALUES (7497,0,'<strong>webEdition TWENTY</strong> is for the administration of 20 domains. <br>\nThe target group is medium-sized Internet companies who want to maintain their website dynamically.<br>\n<br>',0,'on',0);
INSERT INTO tblContent VALUES (7579,0,'<html>\n   <head>\n      <we:title>Shop</we:title>\n      <we:description>Example webSite webEdition</we:description>\n      <we:keywords>cms,webEdition</we:keywords>\n      <link href=\"<we:url id=\"89\" />\" rel=\"styleSheet\" type=\"text/css\">\n   </head>\n   <body  background=\"/we_demo/layout_images/bg.gif\" bgcolor=\"white\" leftmargin=\"0\" marginwidth=\"0\" topmargin=\"8\" marginheight=\"8\">\n <we:ifshopexists>\n     <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"620\" class=\"normal\" >\n         <tr>\n            <td width=\"27\"></td>\n            <td width=\"50\"><img src=\"/we_demo/layout_images/we_logo.gif\" width=\"50\" height=\"50\" border=\"0\"></td>\n            <td width=\"54\"></td>\n            <td><span class=\"headline\">&nbsp;Shop-Chanel - </span><span class=\"headline_small\">Basket</span></td>\n            <td class=\"normal\" width=\"74\"></td>\n         </tr>\n         <tr>\n            <td width=\"27\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"27\" height=\"10\" border=\"0\"></td>\n            <td width=\"50\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"50\" height=\"10\" border=\"0\"></td>\n            <td width=\"54\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"54\" height=\"10\" border=\"0\"></td>\n            <td><img src=\"/we_demo/layout_images/pixel.gif\" width=\"418\" height=\"10\" border=\"0\"></td>\n            <td width=\"74\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"71\" height=\"10\" border=\"0\"></td>\n         </tr>\n         <tr>\n            <td class=\"normal\" width=\"27\"></td>\n            <td colspan=\"2\" class=\"normal\" valign=\"top\"><we:include id=\"90\"/></td>\n            <td bgcolor=\"white\" colspan=\"2\" valign=\"top\">\n\n<we:createShop shopname=\"shopers\"/><we:addDelShopItem shopname=\"shopers\"/>\n<table width=\"100%\" border=\"0\">\n<tr bgcolor=\"silver\">\n<td class=\"normal\">Article</td>\n<td class=\"normal\" width=\"50\">Amount</td>\n<td class=\"normal\">Price</td>\n<td class=\"normal\">Total</td>\n</tr>\n<we:repeatShopItem shopname=\"shopers\">\n                                <tr>\n                                   <td class=\"normal\"  bgcolor=\"white\"><table border=\"0\"><tr><td><we:field name=\"Bild\" type=\"img\" hyperlink=\"on\" border=\"0\" height=\"30\" width=\"30\" align=\"top\"/>\n		</td><td class=\"normal\" ><b><we:field name=\"Artikelname\" alt=\"we_path\" hyperlink=\"on\"/></b><br><we:field name=\"shopdescription\" alt=\"we_text\" max=\"200\"/></td>\n</tr></table>\n<td align=\"center\" class=\"normal\"><we:showShopItemNumber shopname=\"shopers\"/><br>[<we:a id=\"148\" shop=\"on\" amount=\"1\">+1</we:a>|<we:a id=\"148\" shop=\"on\" amount=\"-1\">-1</we:a>]</td>\n<td class=\"normal\"><we:field name=\"Preis\"/></td>\n<td  align=\"right\" class=\"normal\">EURO <br><we:calculate sum=\"warenkorb\" num_format=\"english\"><we:showShopItemNumber shopname=\"shopers\">* <we:field name=\"Preis\"/></we:calculate></td>\n\n</td>\n                                </tr>\n\n\n</we:repeatShopItem>\n<tr><td colspan=\"4\"></td></tr>\n<tr bgcolor=\"silver\"><td class=\"normal\" colspan=\"3\">Total sum:</td><td align=\"right\" class=\"normal\">\n EURO <br> <we:sum name=\"warenkorb\" num_format=\"english\"/>\n</td></tr>\n</table><br><br><br>\n<table>\n <td align=\"left\" class=\"normal\">\n  <we:a id=\"self\" shopname=\"shopers\" delshop=\"on\"> Delete basket</we:a>\n </td>\n <td width=\"240\"></td>\n <td align=\"right\" class=\"normal\"> \n  <we:a id=\"157\">Cashier</we:a>\n </td>\n</table>\n\n            </td>\n<td valign=\"top\"><we:include id=\"149\"/> </td>\n         </tr>\n      </table>\n<we:else/>\nUnfortunately the Shop management is not installed! \n</we:ifshopexists>\n   </body>\n</html>\n',0,'',0);
INSERT INTO tblContent VALUES (7577,0,'<we:ifshopexists>\n<we:createShop shopname=\"shopers\"/>\n<we:addDelShopItem shopname=\"shopers\"/>\n</we:ifshopexists><html>\n   <head>\n      <we:title>CMS Channel</we:title>\n      <we:description>Demo-Website for the CMS webEdition</we:description>\n      <we:keywords>cms,webEdition</we:keywords>\n      <link href=\"<we:url id=\"89\" />\" rel=\"styleSheet\" type=\"text/css\">\n   </head>\n   <body  background=\"/we_demo/layout_images/bg.gif\" bgcolor=\"white\" leftmargin=\"0\" marginwidth=\"0\" topmargin=\"8\" marginheight=\"8\">\n      <we:form id=\"114\" method=\"get\"><table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"620\">\n         <tr>\n            <td width=\"27\"></td>\n            <td width=\"50\"><img src=\"/we_demo/layout_images/we_logo.gif\" width=\"50\" height=\"50\" border=\"0\"></td>\n            <td width=\"54\"></td>\n            <td><span class=\"headline\">&nbsp;CMS Channel - </span><span class=\"headline_small\">Shop</span></td>\n            <td class=\"normal\" width=\"74\"><we:date type=\"js\" format=\"m/d/Y\"/>&nbsp;</td>\n         </tr>\n         <tr>\n            <td colspan=\"3\"></td>\n            <td colspan=\"2\" align=\"right\"><span class=\"normal\"><b>Search:</b><span class=\"normal\">&nbsp;</span><we:search type=\"textinput\" size=\"15\"/><span class=\"normal\">&nbsp;</span><input type=\"submit\" value=\"ok\"><span class=\"normal\">&nbsp;</span></td>\n         </tr>\n         <tr>\n            <td width=\"27\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"27\" height=\"10\" border=\"0\"></td>\n            <td width=\"50\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"50\" height=\"10\" border=\"0\"></td>\n            <td width=\"54\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"54\" height=\"10\" border=\"0\"></td>\n            <td><img src=\"/we_demo/layout_images/pixel.gif\" width=\"418\" height=\"10\" border=\"0\"></td>\n            <td width=\"74\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"71\" height=\"10\" border=\"0\"></td>\n         </tr>\n         <tr>\n            <td class=\"normal\" width=\"27\"></td>\n            <td colspan=\"2\" class=\"normal\" valign=\"top\"><we:include id=\"90\"/></td>\n            <td bgcolor=\"white\" colspan=\"2\" valign=\"top\">\n               <table cellpadding=\"6\" cellspacing=\"0\" border=\"0\">\n                  <tr>\n                     <td valign=\"top\" class=\"normal\">\n</we:form>\n<we:ifshopexists>\n<we:listview  categories=\"shop\" order=\"Ordnung\">\n                          <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"500\">\n<we:repeat><we:form type=\"shopliste\" method=\"get\">\n  <tr>\n    <td colspan=\"10\" bgcolor=\"#99ccff\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"430\" height=\"1\"></td>\n  </tr>\n<tr>\n    <td width=\"8\" bgcolor=\"white\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"8\" height=\"26\" border=\"0\"></td>\n    <td width=\"163\" class=\"norm16\" bgcolor=\"white\"><we:field name=\"Artikelname\" alt=\"we_path\" hyperlink=\"on\"/></td>\n    <td width=\"8\" bgcolor=\"white\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"8\" height=\"26\" border=\"0\"></td>\n    <td width=\"30\" class=\"nav0\" bgcolor=\"white\" align=\"right\">Amount:&nbsp;</td>\n    <td width=\"30\" class=\"nav0\" bgcolor=\"white\" align=\"right\">\n      <select name=\"shop_anzahl\" size=\"1\" class=\"nav0\">\n        <option value=\"1\">1</option>\n        <option value=\"2\">2</option>\n        <option value=\"3\">3</option>\n        <option value=\"4\">4</option>\n        <option value=\"5\">5</option>\n        <option value=\"6\">6</option>\n        <option value=\"7\">7</option>\n        <option value=\"8\">8</option>\n        <option value=\"9\">9</option>\n        <option value=\"10\">10</option>\n      </select></td>\n    <td width=\"8\" bgcolor=\"white\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"8\" height=\"26\" border=\"0\"></td>\n    <td width=\"140\" class=\"nav0\" align=\"right\" bgcolor=\"white\"><span class=\"norm7\"><we:field name=\"Preis\"  num_format=\"english\" nachkomma=\"0\"/> Euro</span>&nbsp;plus VAT</td>\n    <td width=\"8\" bgcolor=\"white\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"8\" height=\"26\" border=\"0\"></td>\n    <td width=\"1\" bgcolor=\"#99ccff\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"1\" height=\"26\" border=\"0\"></td>\n    <td width=\"26\" bgcolor=\"white\"><input type=\"image\" src=\"/we_demo/shop/images/basket_in2.gif\" border=\"0\"></td>\n  </tr>\n  </we:form>\n</we:repeat>\n <we:ifNotFound>\n                                 <tr>\n                                   <td colspan=\"3\" class=\"normal\">Unfortunately there are no items in the shop at the moment!</td>\n                               </tr>\n<we:else/>\n   <tr>\n    <td colspan=\"10\" bgcolor=\"#99ccff\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"430\" height=\"1\"></td>\n  </tr>\n                              <tr>\n                                   <td colspan=\"3\" class=\"normal\">\n                                       <table cellpadding=\"0\" border=\"0\" cellspacing=\"0\" width=\"100%\">\n                                         <tr>\n                                           <td colspan=\"2\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"10\" height=\"6\" border=\"0\"></td>\n                                        </tr>\n                                         <tr>\n                                           <td class=\"normal\"><we:ifBack><we:back>&lt;&lt; back</we:back></we:ifBack></td>\n                                           <td class=\"normal\" align=\"right\"><we:ifNext><we:next>next &gt;&gt;</we:next></we:ifNext></td>\n                                        </tr>\n                                      </table>\n                                  </td>\n                               </tr>\n</we:ifNotFound>\n                      </table>\n</we:listview>\n<we:else/>\nUnfortunately the shop is not installed!\n</we:ifshopexists>\n                    </td>\n                 </tr>\n              </table>              \n            </td>\n<td valign=\"top\"><we:ifshopexists><we:include id=\"149\"/></we:ifshopexists> </td>\n         </tr>\n      </table>\n   </body>\n</html>\n',0,'',0);
INSERT INTO tblContent VALUES (7358,0,'26',0,'',0);
INSERT INTO tblContent VALUES (7359,0,'26',0,'',0);
INSERT INTO tblContent VALUES (7360,0,'image/gif',0,'',0);
INSERT INTO tblContent VALUES (7407,0,'webEdition five additional licences',0,'',0);
INSERT INTO tblContent VALUES (7150,0,'0000001001671200',0,'000',0);
INSERT INTO tblContent VALUES (7151,0,'0000001001673000',0,'000',0);
INSERT INTO tblContent VALUES (7152,0,'a:13:{i:0;s:2:\"_2\";i:1;s:2:\"_3\";i:2;s:2:\"_1\";i:3;s:2:\"_4\";i:4;s:2:\"_5\";i:5;s:2:\"_6\";i:6;s:2:\"_7\";i:7;s:2:\"_8\";i:8;s:2:\"_9\";i:9;s:3:\"_10\";i:10;s:3:\"_11\";i:11;s:3:\"_12\";i:12;s:3:\"_13\";}',0,'',0);
INSERT INTO tblContent VALUES (7184,0,'a:2:{i:0;a:11:{s:4:\"href\";s:24:\"http://www.webedition.de\";s:4:\"text\";s:24:\"webEdition Software GmbH\";s:6:\"target\";s:6:\"_blank\";s:4:\"type\";s:3:\"ext\";s:5:\"ctype\";s:4:\"text\";s:2:\"id\";s:0:\"\";s:7:\"attribs\";s:0:\"\";s:6:\"img_id\";s:0:\"\";s:7:\"img_src\";s:0:\"\";s:11:\"img_attribs\";a:7:{s:5:\"width\";s:0:\"\";s:6:\"height\";s:0:\"\";s:6:\"border\";s:0:\"\";s:6:\"hspace\";s:0:\"\";s:6:\"vspace\";s:0:\"\";s:5:\"align\";s:0:\"\";s:3:\"alt\";s:0:\"\";}s:2:\"nr\";i:0;}i:1;a:12:{s:4:\"href\";s:28:\"http://www.webedition.de/int\";s:4:\"text\";s:10:\"webEdition\";s:6:\"target\";s:0:\"\";s:4:\"type\";s:3:\"ext\";s:5:\"ctype\";s:4:\"text\";s:2:\"id\";s:0:\"\";s:7:\"attribs\";s:0:\"\";s:6:\"img_id\";s:0:\"\";s:7:\"img_src\";s:0:\"\";s:11:\"img_attribs\";a:7:{s:5:\"width\";s:0:\"\";s:6:\"height\";s:0:\"\";s:6:\"border\";s:0:\"\";s:6:\"hspace\";s:0:\"\";s:6:\"vspace\";s:0:\"\";s:5:\"align\";s:0:\"\";s:3:\"alt\";s:0:\"\";}s:2:\"nr\";i:1;s:13:\"jswin_attribs\";a:11:{s:5:\"jswin\";N;s:8:\"jscenter\";N;s:6:\"jsposx\";s:0:\"\";s:6:\"jsposy\";s:0:\"\";s:7:\"jswidth\";s:0:\"\";s:8:\"jsheight\";s:0:\"\";s:8:\"jsstatus\";N;s:12:\"jsscrollbars\";N;s:9:\"jsmenubar\";N;s:11:\"jsresizable\";N;s:10:\"jslocation\";N;}}}',0,'',0);
INSERT INTO tblContent VALUES (7581,0,'<we:ifcustomerexists><we:sessionStart/><we:saveRegisteredUser/></we:ifcustomerexists>\n<html>\n   <head>\n      <we:title>CMS Channel</we:title>\n      <we:description>Demo-Website for the CMS webEdition</we:description>\n      <we:keywords>cms,webEdition</we:keywords>\n      <link href=\"<we:url id=\"89\" />\" rel=\"styleSheet\" type=\"text/css\">\n   </head>\n   <body  background=\"/we_demo/layout_images/bg.gif\" bgcolor=\"white\" leftmargin=\"0\" marginwidth=\"0\" topmargin=\"8\" marginheight=\"8\">\n <we:ifcustomerexists><we:registerSwitch/></we:ifcustomerexists>\n     <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"600\">\n         <tr>\n            <td width=\"27\"></td>\n            <td width=\"50\"><img src=\"/we_demo/layout_images/we_logo.gif\" width=\"50\" height=\"50\" border=\"0\"></td>\n            <td width=\"54\"></td>\n            <td><span class=\"headline\">&nbsp;CMS Channel - </span><span class=\"headline_small\">Customer login</span></td>\n            <td class=\"normal\" width=\"74\"></td>\n         </tr>\n         <tr>\n            <td width=\"27\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"27\" height=\"10\" border=\"0\"></td>\n            <td width=\"50\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"50\" height=\"10\" border=\"0\"></td>\n            <td width=\"54\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"54\" height=\"10\" border=\"0\"></td>\n            <td><img src=\"/we_demo/layout_images/pixel.gif\" width=\"418\" height=\"10\" border=\"0\"></td>\n            <td width=\"74\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"71\" height=\"10\" border=\"0\"></td>\n         </tr>\n         <tr>\n            <td class=\"normal\" width=\"27\"></td>\n            <td colspan=\"2\" class=\"normal\" valign=\"top\"><we:include id=\"90\"/></td>\n            <td bgcolor=\"white\" colspan=\"2\" valign=\"top\"><we:ifcustomerexists>\n               <table cellpadding=\"6\" cellspacing=\"0\" border=\"0\">\n                  <tr>\n                  <we:ifRegisteredUser>\n                  <td colspan =\"2\" class =\"normal\">\n                  Hallo <we:sessionField name=\"Username\" type=\"print\"/>,<br><br>please check your customer data again. If everything is correct, please press \"send order\". If you want to change the data, please press \"change\"!<br><br>\n\n                  <br><table border=\"0\" cellpadding=\"4\" cellspacing=\"0\">\n                            <tr><td class =\"normal\"><b>Firstname:</b></td><td class =\"normal\"><we:sessionField name=\"Forename\" type=\"print\"/></td></tr>\n                            <tr><td class =\"normal\"><b>Name:</b></td><td class=\"normal\"><we:sessionField name=\"Surname\" type=\"print\"/></td></tr>\n                            <tr><td class =\"normal\"><b>Adress 1:</b></td><td class =\"normal\"><we:sessionField name=\"Contact_Address1\" type=\"print\"/></td></tr>\n                            <tr><td class =\"normal\"><b>Adress 2:</b></td><td class =\"normal\"><we:sessionField name=\"Contact_Address2\" type=\"print\"/></td></tr>\n                            <tr><td class =\"normal\"><b>Country:</td><td class =\"normal\"><we:sessionField name=\"Contact_Country\" type=\"print\"/></tr></td>\n                         </table>\n                   <br><form><input type=\"button\" value=\"change\" onClick=\"self.location=\'<we:url id=\"156\"/>\'\">&nbsp;&nbsp;<input type=\"button\" value=\"send order\" onClick=\"self.location=\'<we:url id=\"173\"/>\'\"></form>\n                    </td>\n                    </we:ifRegisteredUser>\n\n                    <we:ifNotRegisteredUser>\n                    <td colspan=\"2\" class=\"normal\">  <we:form id=\"self\">\n<we:ifLoginFailed>\n                  <font color=\"red\">Username and/or password incorrect!</font><br>\n<we:else/>\n                   \n                      Dear webEdition customer,<br><br> \n                      if you visit our shop for the first time, please click <we:a id=\"156\">here</we:a>.<br>\n                      <br>\n                      If you already are a registered customer, please enter your username and password into the appropriate fields and press \"login\".<br>\n </we:ifLoginFailed>                     \n                    </td>\n                  </tr> \n                 <tr>\n                   <td width=\"120\" align=\"top\" class=\"normal\">\n                   Username:\n                   </td> \n                    <td align=\"top\">\n                    <we:sessionField name=\"Username\" type=\"textinput\"/>\n                   </td>\n                 </tr>\n                 <tr>\n                   <td width=\"120\" align=\"top\" class=\"normal\"> \n                    Password:\n                   \n                     </td>\n                   <td align=\"top\">\n                    <we:sessionField name=\"Password\" type=\"password\"/>\n                   </td>\n                 </tr>\n                 <tr>\n                   <td colspan=\"2\">             \n                      <input type=\"submit\" value=\"login\">\n                       </we:form>\n                   </td>\n                    </we:ifNotRegisteredUser>\n                 </tr>\n              </table>\n<we:else/>\nUnfortunately the customer management is not installed!\n</we:ifcustomerexists>        \n            </td>\n<td valign=\"top\"><we:ifshopexists><we:include id=\"149\"/></we:ifshopexists> </td>\n         </tr>\n      </table>\n   </body>\n</html>\n\n\n\n\n',0,'',0);
INSERT INTO tblContent VALUES (7341,0,' ',0,'',0);
INSERT INTO tblContent VALUES (7584,0,'<we:ifshopexists><we:sessionStart/><we:ifRegisteredUser>\n<we:createShop shopname=\"shopers\"/><we:writeShopData shopname=\"shopers\" pricename=\"Preis\"/><we:ifShopNotEmpty shopname=\"shopers\"><we:sendMail id=\"162\" subject=\"order\" recipient=\"bitbucket@webedition.de\" from=\"meila@shop.de\"/></we:ifShopNotEmpty>\n</we:ifRegisteredUser></we:ifshopexists>\n<html>\n   <head>\n      <we:title>Shop</we:title>\n      <we:description>Demo-Website for the CMS webEdition</we:description>\n      <we:keywords>cms,webEdition</we:keywords>\n      <link href=\"<we:url id=\"89\" />\" rel=\"styleSheet\" type=\"text/css\">\n   </head>\n   <body  background=\"/we_demo/layout_images/bg.gif\" bgcolor=\"white\" leftmargin=\"0\" marginwidth=\"0\" topmargin=\"8\" marginheight=\"8\">\n <we:ifshopexists><we:registerSwitch/></we:ifshopexists>\n     <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"600\">\n         <tr>\n            <td width=\"27\"></td>\n            <td width=\"50\"><img src=\"/we_demo/layout_images/we_logo.gif\" width=\"50\" height=\"50\" border=\"0\"></td>\n            <td width=\"54\"></td>\n            <td><span class=\"headline\">&nbsp;Shop-Channel - </span><span class=\"headline_small\"><we:ifShopEmpty shopname=\"shopers\">Error<we:else/>Thank you</we:ifShopEmpty></span></td>\n            <td class=\"normal\" width=\"74\"></td>\n         </tr>\n         <tr>\n            <td width=\"27\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"27\" height=\"10\" border=\"0\"></td>\n            <td width=\"50\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"50\" height=\"10\" border=\"0\"></td>\n            <td width=\"54\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"54\" height=\"10\" border=\"0\"></td>\n            <td><img src=\"/we_demo/layout_images/pixel.gif\" width=\"418\" height=\"10\" border=\"0\"></td>\n            <td width=\"74\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"71\" height=\"10\" border=\"0\"></td>\n         </tr>\n         <tr>\n            <td class=\"normal\" width=\"27\"></td>\n            <td colspan=\"2\" class=\"normal\" valign=\"top\"><we:include id=\"90\"/></td>\n            <td bgcolor=\"white\" colspan=\"2\" valign=\"top\">\n               <table cellpadding=\"6\" cellspacing=\"0\" border=\"0\">\n               <tr>\n                 <td class=\"normal\"><we:ifshopexists>\n<we:ifShopEmpty shopname=\"shopers\">\nYour Basket is empty!\n<we:else/>\nThank you for the order.\n</we:ifShopEmpty><we:else/>\nUnfortunately the shop is not installed!\n</we:ifshopexists>\n                 </td>\n               </tr>\n              </table> \n         \n            </td>\n<td valign=\"top\"><we:include id=\"149\"/> </td>\n         </tr>\n      </table>\n   </body>\n</html><we:ifshopexists><we:ifRegisteredUser><we:deleteShop shopname=\"shopers\"></we:ifRegisteredUser></we:ifshopexists>\n',0,'',0);
INSERT INTO tblContent VALUES (7231,0,'Tirol, Love Story',0,'',0);
INSERT INTO tblContent VALUES (7232,0,'yearning for the Tirol',0,'',0);
INSERT INTO tblContent VALUES (7233,135,'',0,'',0);
INSERT INTO tblContent VALUES (7234,0,'Love Story in the tirol mountains',0,'',0);
INSERT INTO tblContent VALUES (7235,0,'Beautiful Francesca is born the daugther of the poor Tyrolese peasant Georgio. <br />\nLife is hard in the small mountain village of Monte. If it wasn�t for her father, Francesca would have already left the village for the more exciting big cities of Germany or Italy. <br />\nOne day Francesca meets the handsome German student Christian way up in the mountains. Christian is apparently injured, and Francesca offers to shelter him for the night. However, Georgio does not agree with this plan.<br />\nAfter a furious dispute with her father, Francesca is determined to leave the village and wants to go to Germany with Christian. ',0,'on',0);
INSERT INTO tblContent VALUES (7036,0,'Talai, island, holidays',0,'',0);
INSERT INTO tblContent VALUES (7473,152,'',0,'',0);
INSERT INTO tblContent VALUES (7474,0,'webEdition FIVE',0,'',0);
INSERT INTO tblContent VALUES (7383,0,'cms,webEdition',0,'',0);
INSERT INTO tblContent VALUES (7384,0,'Additional licence of webEdition for one domain.',0,'',0);
INSERT INTO tblContent VALUES (7409,0,' ',0,'',0);
INSERT INTO tblContent VALUES (7410,0,'Additional licence of webEdition for five domains.\n\n',0,'off',0);
INSERT INTO tblContent VALUES (7433,0,'1.299,00',0,'',0);
INSERT INTO tblContent VALUES (4363,0,'webEdition TWENTY',0,'',0);
INSERT INTO tblContent VALUES (7586,0,'<html>\n   <head>\n      <we:title>CMS Channel</we:title>\n      <we:description>Demo-Website for the CMS webEdition</we:description>\n      <we:keywords>cms,webEdition</we:keywords>\n      <link href=\"<we:url id=\"89\" />\" rel=\"styleSheet\" type=\"text/css\">\n   </head>\n   <body  background=\"/we_demo/layout_images/bg.gif\" bgcolor=\"white\" leftmargin=\"0\" marginwidth=\"0\" topmargin=\"8\" marginheight=\"8\">\n      <we:form id=\"114\" method=\"get\"><table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"620\">\n         <tr>\n            <td width=\"27\"></td>\n            <td width=\"50\"><img src=\"/we_demo/layout_images/we_logo.gif\" width=\"50\" height=\"50\" border=\"0\"></td>\n            <td width=\"54\"></td>\n            <td><span class=\"headline\">&nbsp;CMS Channel - </span><span class=\"headline_small\">Veranstaltungen</span></td>\n            <td class=\"normal\" width=\"74\"><we:date type=\"js\" format=\"d.m.Y\"/>&nbsp;</td>\n         </tr>\n         <tr>\n            <td colspan=\"3\"></td>\n            <td colspan=\"2\" align=\"right\"><span class=\"normal\"><b>Suche:</b><span class=\"normal\">&nbsp;</span><we:search type=\"textinput\" size=\"15\"/><span class=\"normal\">&nbsp;</span><input type=\"submit\" value=\"ok\"><span class=\"normal\">&nbsp;</span></td>\n         </tr>\n         <tr>\n            <td width=\"27\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"27\" height=\"10\" border=\"0\"></td>\n            <td width=\"50\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"50\" height=\"10\" border=\"0\"></td>\n            <td width=\"54\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"54\" height=\"10\" border=\"0\"></td>\n            <td><img src=\"/we_demo/layout_images/pixel.gif\" width=\"418\" height=\"10\" border=\"0\"></td>\n            <td width=\"74\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"71\" height=\"10\" border=\"0\"></td>\n         </tr>\n         <tr>\n            <td class=\"normal\" width=\"27\"></td>\n            <td colspan=\"2\" class=\"normal\" valign=\"top\"><we:include id=\"90\"/></td>\n            <td bgcolor=\"white\" colspan=\"2\" valign=\"top\">\n               <table cellpadding=\"6\" cellspacing=\"0\" border=\"0\">\n                  <tr>\n                     <td valign=\"top\" class=\"normal\">\n<we:ifobjektexists>\n		<table cellpadding=\"2\" cellspacing=\"0\" border=\"0\" width=\"400\">\n			<tr>\n				<td width=\"400\" class=\"normal\"><we:var type=\"date\" name=\"EventDate\" format=\"m/d/Y -  g:i a\" ></td>\n			</tr>\n			<tr>\n				<td width=\"400\" class=\"normal\"><b><we:var name=\"EventName\"></b></td>\n			</tr>\n			<tr>\n				<td width=\"400\" class=\"normal\"><we:var name=\"EventDescription\"></td>\n			</tr>\n			<tr>\n				<td width=\"400\" class=\"small\"><b>Event Location</b>\n<br><we:var name=\"Name1\">\n<we:ifNotEmpty match=\"Name2\"><br><we:var name=\"Name2\"></we:ifNotEmpty>\n<br><we:var name=\"Street\">\n<br><we:var name=\"ZIP\">&nbsp;<we:var name=\"City\">\n<we:ifNotEmpty match=\"Phone\"><br><b>Fon: </b><we:var name=\"Phone\"></we:ifNotEmpty>\n<we:ifNotEmpty match=\"Email\"><br><b>E-Mail: </b><a href=\"mailto:<we:var name=\"Email\">\"><we:var name=\"Email\"></a></we:ifNotEmpty>\n<we:ifNotEmpty match=\"URL\"><br><b>Homepage: </b><a href=\"<we:var name=\"URL\">\" target=\"_blank\"><we:var name=\"URL\"></a></we:ifNotEmpty></td>\n\n			</tr>\n			<tr>\n				<td width=\"400\" class=\"normal\">&nbsp;</td>\n			</tr>\n			<tr>\n				<td width=\"400\" class=\"normal\"><a href=\"javascript:history.back()\" style=\"text-decoration:none\">&lt;&lt;&nbsp;back</a></td>\n			</tr>\n		</table>\n<we:else/>\nDatabase-/ Object module not installed!\n</we:ifobjektexists>                  </td>\n                 </tr>\n              </table>              \n            </td>\n         </tr>\n      </table></we:form>\n   </body>\n</html>',0,'',0);
INSERT INTO tblContent VALUES (7585,0,'<html>\n   <head>\n      <we:title>CMS Channel event addresses</we:title>\n      <we:description>Demo-Website for the CMS webEdition</we:description>\n      <we:keywords>cms,webEdition</we:keywords>\n      <link href=\"<we:url id=\"89\" />\" rel=\"styleSheet\" type=\"text/css\">\n   </head>\n   <body  background=\"/we_demo/layout_images/bg.gif\" bgcolor=\"white\" leftmargin=\"0\" marginwidth=\"0\" topmargin=\"8\" marginheight=\"8\">\n      <we:form id=\"114\" method=\"get\"><table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"620\">\n         <tr>\n            <td width=\"27\"></td>\n            <td width=\"50\"><img src=\"/we_demo/layout_images/we_logo.gif\" width=\"50\" height=\"50\" border=\"0\"></td>\n            <td width=\"54\"></td>\n            <td><span class=\"headline\">&nbsp;CMS Channel - </span><span class=\"headline_small\">Event adresses</span></td>\n            <td class=\"normal\" width=\"74\"><we:date type=\"js\" format=\"d.m.Y\"/>&nbsp;</td>\n         </tr>\n         <tr>\n            <td colspan=\"3\"></td>\n            <td colspan=\"2\" align=\"right\"><span class=\"normal\"><b>Search:</b><span class=\"normal\">&nbsp;</span><we:search type=\"textinput\" size=\"15\"/><span class=\"normal\">&nbsp;</span><input type=\"submit\" value=\"ok\"><span class=\"normal\">&nbsp;</span></td>\n         </tr>\n         <tr>\n            <td width=\"27\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"27\" height=\"10\" border=\"0\"></td>\n            <td width=\"50\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"50\" height=\"10\" border=\"0\"></td>\n            <td width=\"54\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"54\" height=\"10\" border=\"0\"></td>\n            <td><img src=\"/we_demo/layout_images/pixel.gif\" width=\"418\" height=\"10\" border=\"0\"></td>\n            <td width=\"74\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"71\" height=\"10\" border=\"0\"></td>\n         </tr>\n         <tr>\n            <td class=\"normal\" width=\"27\"></td>\n            <td colspan=\"2\" class=\"normal\" valign=\"top\"><we:include id=\"90\"/></td>\n            <td bgcolor=\"white\" colspan=\"2\" valign=\"top\">\n               <table cellpadding=\"6\" cellspacing=\"0\" border=\"0\">\n                  <tr>\n                     <td valign=\"top\" class=\"normal\">\n<we:ifobjektexists> \n<we:listview type=\"object\" classid=\"1\" order=\"Name1\" categories=\"we_doc\" rows=\"6\">\n                          <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"500\">\n<we:repeat>\n                                <tr>\n                                   <td class=\"normal\">\n                                      <b><we:field name=\"Name1\" /></b><br>\n                                      <we:ifFieldNotEmpty match=\"Name2\"><we:field name=\"Name2\"/><br></we:ifFieldNotEmpty>\n                                      <we:field name=\"Street\"/><br>\n                                      <we:field name=\"ZIP\"/>&nbsp;<we:field name=\"City\"/>\n                                      <we:ifFieldNotEmpty match=\"Phone\"><br><we:field name=\"Phone\"></we:ifFieldNotEmpty>\n                                      <we:ifFieldNotEmpty match=\"Email\"><br><a href=\"mailto:<we:field name=\"Email\"/>\"><we:field name=\"Email\"/></a></we:ifFieldNotEmpty>\n                                     <we:ifFieldNotEmpty match=\"URL\"><br><a href=\"<we:field name=\"URL\"/>\" target=\"_blank\"><we:field name=\"URL\"/></a></we:ifFieldNotEmpty>\n                                   </td>\n                                </tr>\n                                <tr>\n                                   <td><img src=\"/we_demo/layout_images/pixel.gif\" width=\"500\" height=\"10\" border=\"0\"></td>\n                               </tr>\n</we:repeat>\n <we:ifNotFound>\n                                 <tr>\n                                   <td colspan=\"3\" class=\"normal\">Sorry, no results were found!</td>\n                               </tr>\n<we:else/>\n                               <tr>\n                                   <td colspan=\"3\" class=\"normal\">\n                                       <table cellpadding=\"0\" border=\"0\" cellspacing=\"0\" width=\"100%\">\n                                         <tr>\n                                           <td colspan=\"2\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"10\" height=\"6\" border=\"0\"></td>\n                                        </tr>\n                                         <tr>\n                                           <td class=\"normal\"><we:ifBack><we:back>&lt;&lt; back</we:back></we:ifBack></td>\n                                           <td class=\"normal\" align=\"right\"><we:ifNext><we:next>next&gt;&gt;</we:next></we:ifNext></td>\n                                        </tr>\n                                      </table>\n                                  </td>\n                               </tr>\n</we:ifNotFound>\n                      </table>\n</we:listview>\n <we:else/>\nDatabase-/ Object module not installed! \n</we:ifobjektexists> \n                   </td>\n                 </tr>\n              </table>              \n            </td>\n         </tr>\n      </table></we:form>\n   </body>\n</html>',0,'',0);
INSERT INTO tblContent VALUES (7569,0,'<we:linklist name=\"Linklist\"><p><we:ifSelf><b></we:ifSelf><we:link style=\"text-decoration:none\"><we:ifSelf></b></we:ifSelf></p></we:linklist>\n',0,'',0);
INSERT INTO tblContent VALUES (7572,0,'<html>\n   <head>\n      <we:title>CMS Channel</we:title>\n      <we:description>Demo-Website for the CMS webEdition</we:description>\n      <we:keywords>cms,webEdition</we:keywords>\n      <link href=\"<we:url id=\"89\" />\" rel=\"styleSheet\" type=\"text/css\">\n   </head>\n   <body  background=\"/we_demo/layout_images/bg.gif\" bgcolor=\"white\" leftmargin=\"0\" marginwidth=\"0\" topmargin=\"8\" marginheight=\"8\">\n      <we:form id=\"114\" method=\"get\"><table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"620\">\n         <tr>\n            <td width=\"27\"></td>\n            <td width=\"50\"><img src=\"/we_demo/layout_images/we_logo.gif\" width=\"50\" height=\"50\" border=\"0\"></td>\n            <td width=\"54\"></td>\n            <td><span class=\"headline\">&nbsp;CMS Channel - </span><span class=\"headline_small\">Movie Reviews</span></td>\n            <td class=\"normal\" width=\"74\"><we:date type=\"js\" format=\"m/d/Y\"/>&nbsp;</td>\n         </tr>\n         <tr>\n            <td colspan=\"3\"></td>\n            <td colspan=\"2\" align=\"right\"><span class=\"normal\"><b>Search:</b><span class=\"normal\">&nbsp;</span><we:search type=\"textinput\" size=\"15\"/><span class=\"normal\">&nbsp;</span><input type=\"submit\" value=\"ok\"><span class=\"normal\">&nbsp;</span></td>\n         </tr>\n         <tr>\n            <td width=\"27\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"27\" height=\"10\" border=\"0\"></td>\n            <td width=\"50\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"50\" height=\"10\" border=\"0\"></td>\n            <td width=\"54\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"54\" height=\"10\" border=\"0\"></td>\n            <td><img src=\"/we_demo/layout_images/pixel.gif\" width=\"418\" height=\"10\" border=\"0\"></td>\n            <td width=\"74\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"71\" height=\"10\" border=\"0\"></td>\n         </tr>\n         <tr>\n            <td class=\"normal\" width=\"27\"></td>\n            <td colspan=\"2\" class=\"normal\" valign=\"top\"><we:include id=\"90\"/></td>\n            <td bgcolor=\"white\" colspan=\"2\" valign=\"top\">\n               <table cellpadding=\"6\" cellspacing=\"0\" border=\"0\">\n                  <tr>\n                     <td valign=\"top\" class=\"normal\">\n<p><select size=\"1\" onChange=\"document.location=this.options[this.selectedIndex].value\">\n<option value=\"<we:url id=\"105\"/>\">All</option>\n<option value=\"<we:url id=\"105\"/>?cat=Action\"<we:ifVar name=\"cat\" type=\"request\" match=\"Action\"> selected</we:ifVar>>Action</option>\n<option value=\"<we:url id=\"105\"/>?cat=Comedy\"<we:ifVar name=\"cat\" type=\"request\" match=\"Comedy\"> selected</we:ifVar>>Comedy</option>\n<option value=\"<we:url id=\"105\"/>?cat=Drama\"<we:ifVar name=\"cat\" type=\"request\" match=\"Drama\"> selected</we:ifVar>>Drama</option>\n<option value=\"<we:url id=\"105\"/>?cat=Love Story\"<we:ifVar name=\"cat\" type=\"request\" match=\"Love Story\"> selected</we:ifVar>>Love Story</option>\n</select></p>\n<p><we:listview rows=\"6\" doctype=\"movieReview\" categories=\"\\$cat\" order=\"Filmtitel\">\n                          <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"500\">\n<we:repeat>\n                                <tr>\n                                   <td class=\"normal\"><b>-&nbsp;</b></td>\n                                   <td class=\"normal\"><b><we:field type=\"text\" name=\"Filmtitel\" hyperlink=\"on\"/></b></td>\n                                </tr>\n                                <tr>\n                                   <td></td>\n                                   <td><img src=\"/we_demo/layout_images/pixel.gif\" width=\"490\" height=\"6\" border=\"0\"></td>\n                               </tr>\n</we:repeat>\n <we:ifNotFound>\n                                 <tr>\n                                   <td class=\"normal\" colspan=\"2\">No Movie Reviews available!</td>\n                               </tr>\n<we:else/>\n                               <tr>\n                                   <td class=\"normal\" colspan=\"2\">\n                                       <table cellpadding=\"0\" border=\"0\" cellspacing=\"0\" width=\"100%\">\n                                         <tr>\n                                           <td colspan=\"2\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"10\" height=\"6\" border=\"0\"></td>\n                                        </tr>\n                                         <tr>\n                                           <td class=\"normal\"><we:ifBack><we:back>&lt;&lt; zur�ck</we:back></we:ifBack></td>\n                                           <td class=\"normal\" align=\"right\"><we:ifNext><we:next>weiter &gt;&gt;</we:next></we:ifNext></td>\n                                        </tr>\n                                      </table>\n                                  </td>\n                               </tr>\n</we:ifNotFound>\n                      </table>\n</we:listview></p>\n                    </td>\n                 </tr>\n              </table>              \n            </td>\n         </tr>\n      </table>\n   </body></we:form>\n</html>',0,'',0);
INSERT INTO tblContent VALUES (7587,0,'<html>\n   <head>\n      <we:title>CMS Channel</we:title>\n      <we:description>Demo-Website for the CMS webEdition</we:description>\n      <we:keywords>cms,webEdition</we:keywords>\n      <link href=\"<we:url id=\"89\" />\" rel=\"styleSheet\" type=\"text/css\">\n   </head>\n   <body  background=\"/we_demo/layout_images/bg.gif\" bgcolor=\"white\" leftmargin=\"0\" marginwidth=\"0\" topmargin=\"8\" marginheight=\"8\">\n      <we:form id=\"114\" method=\"get\"><table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"620\">\n         <tr>\n            <td width=\"27\"></td>\n            <td width=\"50\"><img src=\"/we_demo/layout_images/we_logo.gif\" width=\"50\" height=\"50\" border=\"0\"></td>\n            <td width=\"54\"></td>\n            <td><span class=\"headline\">&nbsp;CMS Channel - </span><span class=\"headline_small\">Events</span></td>\n            <td class=\"normal\" width=\"74\"><we:date type=\"js\" format=\"d.m.Y\"/>&nbsp;</td>\n         </tr>\n         <tr>\n            <td colspan=\"3\"></td>\n            <td colspan=\"2\" align=\"right\"><span class=\"normal\"><b>Search:</b><span class=\"normal\">&nbsp;</span><we:search type=\"textinput\" size=\"15\"/><span class=\"normal\">&nbsp;</span><input type=\"submit\" value=\"ok\"><span class=\"normal\">&nbsp;</span></td>\n         </tr>\n         <tr>\n            <td width=\"27\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"27\" height=\"10\" border=\"0\"></td>\n            <td width=\"50\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"50\" height=\"10\" border=\"0\"></td>\n            <td width=\"54\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"54\" height=\"10\" border=\"0\"></td>\n            <td><img src=\"/we_demo/layout_images/pixel.gif\" width=\"418\" height=\"10\" border=\"0\"></td>\n            <td width=\"74\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"71\" height=\"10\" border=\"0\"></td>\n         </tr>\n         <tr>\n            <td class=\"normal\" width=\"27\"></td>\n            <td colspan=\"2\" class=\"normal\" valign=\"top\"><we:include id=\"90\"/></td>\n            <td bgcolor=\"white\" colspan=\"2\" valign=\"top\">\n               <table cellpadding=\"6\" cellspacing=\"0\" border=\"0\">\n                  <tr>\n                     <td valign=\"top\" class=\"normal\">\n<we:ifobjektexists>\n<we:listview type=\"object\" classid=\"2\" rows=\"4\" order=\"Veranstaltungsdatum\">\n                          <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"500\">\n<we:repeat>\n                                <tr>\n                                   <td class=\"normal\"><b><we:field type=\"date\" name=\"EventDate\"  format=\"m/d/Y -  g:i a\"> - <we:field name=\"EventName\" hyperlink=\"on\"/></b><br><we:field name=\"EventDescription\"></td>\n                                </tr>\n                                <tr>\n                                   <td><img src=\"/we_demo/layout_images/pixel.gif\" width=\"500\" height=\"10\" border=\"0\"></td>\n                               </tr>\n</we:repeat>\n <we:ifNotFound>\n                                 <tr>\n                                   <td colspan=\"3\" class=\"normal\">Sorry, no results were found!</td>\n                               </tr>\n<we:else/>\n                               <tr>\n                                   <td colspan=\"3\" class=\"normal\">\n                                       <table cellpadding=\"0\" border=\"0\" cellspacing=\"0\" width=\"100%\">\n                                         <tr>\n                                           <td colspan=\"2\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"10\" height=\"6\" border=\"0\"></td>\n                                        </tr>\n                                         <tr>\n                                           <td class=\"normal\"><we:ifBack><we:back style=\"text-decoration:none\">&lt;&lt; back</we:back></we:ifBack></td>\n                                           <td class=\"normal\" align=\"right\"><we:ifNext><we:next style=\"text-decoration:none\">next &gt;&gt;</we:next></we:ifNext></td>\n                                        </tr>\n                                      </table>\n                                  </td>\n                               </tr>\n</we:ifNotFound>\n                      </table>\n</we:listview>\n<we:else/>\nDatabase-/ Object module not installed!\n</we:ifobjektexists> \n                    </td>\n                 </tr>\n              </table>              \n            </td>\n         </tr>\n      </table></we:form>\n   </body>\n</html>',0,'',0);
INSERT INTO tblContent VALUES (7037,0,'Holidays',0,'',0);
INSERT INTO tblContent VALUES (7038,0,'0000001001613300',0,'000',0);
INSERT INTO tblContent VALUES (7039,0,' ',0,'',0);
INSERT INTO tblContent VALUES (7040,136,'',0,'',0);
INSERT INTO tblContent VALUES (7041,0,'Talai � the island of dreams is the latest quiet tip',0,'',0);
INSERT INTO tblContent VALUES (7042,0,'Talai: Apart from extremely friendly people, the island also offers pleasing countryside, impressive cliffs and beautiful bays inviting you to spend an uninhibited holiday there.<br />\n<br />\nAs Talai can only be reached via the sea, it is expected to remain free from mass tourism and as such is truly an insider tip.',0,'on',0);
INSERT INTO tblContent VALUES (7059,0,'The future...<br />\n<br />\nIn the year 2150 John wants to go on an expedition to the Sonax-Galaxy. He supposes yet undiscovered living spaces there. According to the valid jurisdiction in the year 2150 the explorer of new living spaces is the sole owner of the frontier. Nevertheless, such expeditions have to be authorized by the F.o.a.N., the Federation of all Nations. Shortly after his departure John recognizes that this trip is not going to be an easy one. As he enters the Sonax-Galaxy, he is approached by a fleet of federated space-ships...',0,'on',0);
INSERT INTO tblContent VALUES (7166,0,'Software News',0,'',0);
INSERT INTO tblContent VALUES (7167,0,'Daily overview',0,'on',0);
INSERT INTO tblContent VALUES (7168,0,'You ask, we answer - usefull hints & suggestions around software',0,'on',0);
INSERT INTO tblContent VALUES (7192,0,'a:8:{i:0;a:13:{s:4:\"href\";s:1:\"#\";s:4:\"text\";s:4:\"News\";s:6:\"target\";s:0:\"\";s:4:\"type\";s:3:\"int\";s:5:\"ctype\";s:4:\"text\";s:2:\"id\";s:2:\"98\";s:7:\"attribs\";s:0:\"\";s:6:\"img_id\";s:0:\"\";s:7:\"img_src\";s:0:\"\";s:11:\"img_attribs\";a:7:{s:5:\"width\";s:0:\"\";s:6:\"height\";s:0:\"\";s:6:\"border\";s:0:\"\";s:6:\"hspace\";s:0:\"\";s:6:\"vspace\";s:0:\"\";s:5:\"align\";s:0:\"\";s:3:\"alt\";s:0:\"\";}s:2:\"nr\";i:0;s:6:\"obj_id\";s:0:\"\";s:13:\"jswin_attribs\";a:11:{s:5:\"jswin\";N;s:8:\"jscenter\";N;s:6:\"jsposx\";s:0:\"\";s:6:\"jsposy\";s:0:\"\";s:7:\"jswidth\";s:0:\"\";s:8:\"jsheight\";s:0:\"\";s:8:\"jsstatus\";N;s:12:\"jsscrollbars\";N;s:9:\"jsmenubar\";N;s:11:\"jsresizable\";N;s:10:\"jslocation\";N;}}i:1;a:13:{s:4:\"href\";s:1:\"#\";s:4:\"text\";s:13:\"Movie-Reviews\";s:6:\"target\";s:0:\"\";s:4:\"type\";s:3:\"int\";s:5:\"ctype\";s:4:\"text\";s:2:\"id\";s:3:\"105\";s:7:\"attribs\";s:0:\"\";s:6:\"img_id\";s:0:\"\";s:7:\"img_src\";s:0:\"\";s:11:\"img_attribs\";a:7:{s:5:\"width\";s:0:\"\";s:6:\"height\";s:0:\"\";s:6:\"border\";s:0:\"\";s:6:\"hspace\";s:0:\"\";s:6:\"vspace\";s:0:\"\";s:5:\"align\";s:0:\"\";s:3:\"alt\";s:0:\"\";}s:2:\"nr\";i:1;s:13:\"jswin_attribs\";a:11:{s:5:\"jswin\";N;s:8:\"jscenter\";N;s:6:\"jsposx\";s:0:\"\";s:6:\"jsposy\";s:0:\"\";s:7:\"jswidth\";s:0:\"\";s:8:\"jsheight\";s:0:\"\";s:8:\"jsstatus\";N;s:12:\"jsscrollbars\";N;s:9:\"jsmenubar\";N;s:11:\"jsresizable\";N;s:10:\"jslocation\";N;}s:6:\"obj_id\";s:0:\"\";}i:2;a:13:{s:4:\"href\";s:1:\"#\";s:4:\"text\";s:7:\"Program\";s:6:\"target\";s:0:\"\";s:4:\"type\";s:3:\"int\";s:5:\"ctype\";s:4:\"text\";s:2:\"id\";s:3:\"111\";s:7:\"attribs\";s:0:\"\";s:6:\"img_id\";s:0:\"\";s:7:\"img_src\";s:0:\"\";s:11:\"img_attribs\";a:7:{s:5:\"width\";s:0:\"\";s:6:\"height\";s:0:\"\";s:6:\"border\";s:0:\"\";s:6:\"hspace\";s:0:\"\";s:6:\"vspace\";s:0:\"\";s:5:\"align\";s:0:\"\";s:3:\"alt\";s:0:\"\";}s:2:\"nr\";i:2;s:13:\"jswin_attribs\";a:11:{s:5:\"jswin\";N;s:8:\"jscenter\";N;s:6:\"jsposx\";s:0:\"\";s:6:\"jsposy\";s:0:\"\";s:7:\"jswidth\";s:0:\"\";s:8:\"jsheight\";s:0:\"\";s:8:\"jsstatus\";N;s:12:\"jsscrollbars\";N;s:9:\"jsmenubar\";N;s:11:\"jsresizable\";N;s:10:\"jslocation\";N;}s:6:\"obj_id\";s:0:\"\";}i:3;a:13:{s:4:\"href\";s:1:\"#\";s:4:\"text\";s:5:\"Links\";s:6:\"target\";s:0:\"\";s:4:\"type\";s:3:\"int\";s:5:\"ctype\";s:4:\"text\";s:2:\"id\";s:3:\"113\";s:7:\"attribs\";s:0:\"\";s:6:\"img_id\";s:0:\"\";s:7:\"img_src\";s:0:\"\";s:11:\"img_attribs\";a:7:{s:5:\"width\";s:0:\"\";s:6:\"height\";s:0:\"\";s:6:\"border\";s:0:\"\";s:6:\"hspace\";s:0:\"\";s:6:\"vspace\";s:0:\"\";s:5:\"align\";s:0:\"\";s:3:\"alt\";s:0:\"\";}s:2:\"nr\";i:3;s:6:\"obj_id\";s:0:\"\";s:13:\"jswin_attribs\";a:11:{s:5:\"jswin\";N;s:8:\"jscenter\";N;s:6:\"jsposx\";s:0:\"\";s:6:\"jsposy\";s:0:\"\";s:7:\"jswidth\";s:0:\"\";s:8:\"jsheight\";s:0:\"\";s:8:\"jsstatus\";N;s:12:\"jsscrollbars\";N;s:9:\"jsmenubar\";N;s:11:\"jsresizable\";N;s:10:\"jslocation\";N;}}i:4;a:13:{s:4:\"href\";s:1:\"#\";s:4:\"text\";s:11:\"Shop-System\";s:6:\"target\";s:0:\"\";s:4:\"type\";s:3:\"int\";s:5:\"ctype\";s:4:\"text\";s:2:\"id\";s:3:\"145\";s:7:\"attribs\";s:0:\"\";s:6:\"img_id\";s:0:\"\";s:7:\"img_src\";s:0:\"\";s:11:\"img_attribs\";a:7:{s:5:\"width\";s:0:\"\";s:6:\"height\";s:0:\"\";s:6:\"border\";s:0:\"\";s:6:\"hspace\";s:0:\"\";s:6:\"vspace\";s:0:\"\";s:5:\"align\";s:0:\"\";s:3:\"alt\";s:0:\"\";}s:2:\"nr\";i:4;s:13:\"jswin_attribs\";a:11:{s:5:\"jswin\";N;s:8:\"jscenter\";N;s:6:\"jsposx\";s:0:\"\";s:6:\"jsposy\";s:0:\"\";s:7:\"jswidth\";s:0:\"\";s:8:\"jsheight\";s:0:\"\";s:8:\"jsstatus\";N;s:12:\"jsscrollbars\";N;s:9:\"jsmenubar\";N;s:11:\"jsresizable\";N;s:10:\"jslocation\";N;}s:6:\"obj_id\";s:0:\"\";}i:5;a:13:{s:4:\"href\";s:1:\"#\";s:4:\"text\";s:6:\"Events\";s:6:\"target\";s:0:\"\";s:4:\"type\";s:3:\"int\";s:5:\"ctype\";s:4:\"text\";s:2:\"nr\";i:5;s:2:\"id\";s:3:\"307\";s:6:\"obj_id\";s:0:\"\";s:7:\"attribs\";s:0:\"\";s:13:\"jswin_attribs\";a:11:{s:5:\"jswin\";N;s:8:\"jscenter\";N;s:6:\"jsposx\";s:0:\"\";s:6:\"jsposy\";s:0:\"\";s:7:\"jswidth\";s:0:\"\";s:8:\"jsheight\";s:0:\"\";s:8:\"jsstatus\";N;s:12:\"jsscrollbars\";N;s:9:\"jsmenubar\";N;s:11:\"jsresizable\";N;s:10:\"jslocation\";N;}s:6:\"img_id\";s:0:\"\";s:7:\"img_src\";s:0:\"\";s:11:\"img_attribs\";a:7:{s:5:\"width\";s:0:\"\";s:6:\"height\";s:0:\"\";s:6:\"border\";s:0:\"\";s:6:\"hspace\";s:0:\"\";s:6:\"vspace\";s:0:\"\";s:5:\"align\";s:0:\"\";s:3:\"alt\";s:0:\"\";}}i:6;a:13:{s:4:\"href\";s:1:\"#\";s:4:\"text\";s:15:\"Event addresses\";s:6:\"target\";s:0:\"\";s:4:\"type\";s:3:\"int\";s:5:\"ctype\";s:4:\"text\";s:2:\"nr\";i:6;s:2:\"id\";s:3:\"308\";s:6:\"obj_id\";s:0:\"\";s:7:\"attribs\";s:0:\"\";s:13:\"jswin_attribs\";a:11:{s:5:\"jswin\";N;s:8:\"jscenter\";N;s:6:\"jsposx\";s:0:\"\";s:6:\"jsposy\";s:0:\"\";s:7:\"jswidth\";s:0:\"\";s:8:\"jsheight\";s:0:\"\";s:8:\"jsstatus\";N;s:12:\"jsscrollbars\";N;s:9:\"jsmenubar\";N;s:11:\"jsresizable\";N;s:10:\"jslocation\";N;}s:6:\"img_id\";s:0:\"\";s:7:\"img_src\";s:0:\"\";s:11:\"img_attribs\";a:7:{s:5:\"width\";s:0:\"\";s:6:\"height\";s:0:\"\";s:6:\"border\";s:0:\"\";s:6:\"hspace\";s:0:\"\";s:6:\"vspace\";s:0:\"\";s:5:\"align\";s:0:\"\";s:3:\"alt\";s:0:\"\";}}i:7;a:13:{s:4:\"href\";s:1:\"#\";s:4:\"text\";s:10:\"Newsletter\";s:6:\"target\";s:0:\"\";s:4:\"type\";s:3:\"int\";s:5:\"ctype\";s:4:\"text\";s:2:\"nr\";i:7;s:2:\"id\";s:3:\"312\";s:6:\"obj_id\";s:0:\"\";s:7:\"attribs\";s:0:\"\";s:13:\"jswin_attribs\";a:11:{s:5:\"jswin\";N;s:8:\"jscenter\";N;s:6:\"jsposx\";s:0:\"\";s:6:\"jsposy\";s:0:\"\";s:7:\"jswidth\";s:0:\"\";s:8:\"jsheight\";s:0:\"\";s:8:\"jsstatus\";N;s:12:\"jsscrollbars\";N;s:9:\"jsmenubar\";N;s:11:\"jsresizable\";N;s:10:\"jslocation\";N;}s:6:\"img_id\";s:0:\"\";s:7:\"img_src\";s:0:\"\";s:11:\"img_attribs\";a:7:{s:5:\"width\";s:0:\"\";s:6:\"height\";s:0:\"\";s:6:\"border\";s:0:\"\";s:6:\"hspace\";s:0:\"\";s:6:\"vspace\";s:0:\"\";s:5:\"align\";s:0:\"\";s:3:\"alt\";s:0:\"\";}}}',0,'',0);
INSERT INTO tblContent VALUES (7278,0,'8067',0,'',0);
INSERT INTO tblContent VALUES (7004,0,'Stress at work',0,'',0);
INSERT INTO tblContent VALUES (7020,140,'',0,'',0);
INSERT INTO tblContent VALUES (7021,0,'0000001001354100',0,'000',0);
INSERT INTO tblContent VALUES (7022,0,'Wind energy',0,'',0);
INSERT INTO tblContent VALUES (7063,0,'Sonax - pure action!',0,'',0);
INSERT INTO tblContent VALUES (7159,0,'Software News from around the world',0,'on',0);
INSERT INTO tblContent VALUES (7160,0,'CMS-Systems',0,'',0);
INSERT INTO tblContent VALUES (7161,0,'Comparison of the best CMS - Systems',0,'on',0);
INSERT INTO tblContent VALUES (7162,0,'0000001001613600',0,'000',0);
INSERT INTO tblContent VALUES (7163,0,' ',0,'',0);
INSERT INTO tblContent VALUES (7186,0,'cms,webEdition',0,'',0);
INSERT INTO tblContent VALUES (7200,0,'CMS-Channel',0,'',0);
INSERT INTO tblContent VALUES (7238,0,'256',0,'',0);
INSERT INTO tblContent VALUES (7239,0,'260',0,'',0);
INSERT INTO tblContent VALUES (7266,0,'CMS for the people!',0,'off',0);
INSERT INTO tblContent VALUES (7267,0,'webEdition Software GmbH was founded in April 2003 with the aim of\ndeveloping and marketing the webEdition content management system\n(CMS).&nbsp; webEdition was first developed by Astarte New Media AG,\nwhich established an outstanding reputation as a software development\ncompany with such products as the CD recording software, Toast, for the\nMacintosh platform, and the DVD authoring tool known as DVDirector for\nthe PC and Macintosh markets. Having sold more than 4000 licences since\nfirst entering the market in November of 2001, the webEdition CMS has\nestablished itself as one of the leading systems in the German CMS\nmarket. <br>\n<br>\nThis success is owing to two factors: first, webEdition is designed\nspecifically for small and medium-sized businesses; second, webEdition\noffers an extremely competitive price-quality relationship. Even the\nStandard version of this modular system, starting at just 159 Euro,\nhas capabilities that one finds only in systems that often cost more\nthan 100 times the price. <br>',0,'on',0);
INSERT INTO tblContent VALUES (7286,0,'200',0,'',0);
INSERT INTO tblContent VALUES (7287,0,'303',0,'',0);
INSERT INTO tblContent VALUES (7322,0,' ',0,'',0);
INSERT INTO tblContent VALUES (7347,0,'customer login',0,'',0);
INSERT INTO tblContent VALUES (7368,0,' ',0,'',0);
INSERT INTO tblContent VALUES (7389,0,'Additional licence of webEdition for one domain.',0,'off',0);
INSERT INTO tblContent VALUES (7498,0,'webEdition for twenty domains',0,'off',0);
INSERT INTO tblContent VALUES (7529,0,'image/gif',0,'',0);
INSERT INTO tblContent VALUES (7538,0,'image/gif',0,'',0);
INSERT INTO tblContent VALUES (7588,0,'<we:ifnewsletterexists><we:ifFemale>\nDear Ms. <we:newsletterSalutation type=\"lastname\" />\n<we:else /><we:ifMale>\nDear Mr. <we:newsletterSalutation type=\"lastname\" />\n<we:else /><we:ifTitleAndLastName>\nDear <we:newsletterSalutation type=\"title\" /> <we:newsletterSalutation type=\"lastname\" />\n<we:else />\nDear Customer</we:ifTitleAndLastName></we:ifMale></we:ifFemale>,\n<we:ifHtmlMail><br><br><we:else />\n\n\n</we:ifHtmlMail>\n<we:else/>\nThe Newsletter Module is not installed!\n</we:ifnewsletterexists>',0,'',0);
INSERT INTO tblContent VALUES (7589,0,'<we:ifnewsletterexists><we:ifHtmlMail><we:ifFemale>Dear Ms.<we:newsletterSalutation type=\"title\" /> <we:newsletterSalutation type=\"lastname\" />,<br><br>\n\n<we:else /><we:ifMale>Dear Mr.<we:newsletterSalutation type=\"title\" /> <we:newsletterSalutation type=\"lastname\" />,<br><br>\n\n<we:else />Dear Customer,<br><br>\n\n</we:ifMale></we:ifFemale>please ckick the following link to confirm the entry in our newsletter: <we:newsletterConfirmLink/><br><br>Vielen Dank Ihr CMS-Kanal Team\n<we:else><we:ifFemale>Dear Ms.<we:newsletterSalutation type=\"title\" /> <we:newsletterSalutation type=\"lastname\" />,\n\n<we:else /><we:ifMale>Dear Mr.<we:newsletterSalutation type=\"title\" /> <we:newsletterSalutation type=\"lastname\" />,\n\n<we:else />Dear Customer,\n\n</we:ifMale></we:ifFemale>please ckick the following link to confirm the entry in our newsletter: <we:newsletterConfirmLink/>\n\n\nThanks, your CMS Channel Team</we:ifHtmlMail><we:else/>The Newsletter Module is not installed!</we:ifnewsletterexists>',0,'',0);
INSERT INTO tblContent VALUES (7590,0,'<we:ifnewsletterexists><we:addDelNewsletterEmail path=\"we_demo/newsletter/sport.txt,we_demo/newsletter/politics.txt,we_demo/newsletter/computer.txt\" doubleoptin=\"false\" mailid=\"488\" subject=\"Eintrag im CMS-Kanal Newsletter\" from=\"newsletter@mydomain.de\" /></we:ifnewsletterexists><html>\n   <head>\n      <we:title>CMS Channel</we:title>\n      <we:description>Demo-Website for the CMS webEdition</we:description>\n      <we:keywords>cms,webEdition</we:keywords>\n      <link href=\"<we:url id=\"89\" />\" rel=\"styleSheet\" type=\"text/css\">\n<we:ifnewsletterexists>\n<script language=\"JavaScript\"><!--\n\nvar msg = \"\";\n\n<we:ifSubscribe><we:ifDoubleOptIn>\nmsg = \'An email was sent to <we:var type=\"global\" name=\"WE_NEWSLETTER_EMAIL\">! To confirm the entry in our newsletter, please klick the mails link.\';\n<we:else>\nmsg = \'The email address <we:var type=\"global\" name=\"WE_NEWSLETTER_EMAIL\"> was successfully saved!\';\n</we:ifDoubleOptIn>\n</we:ifSubscribe>\n<we:ifNotSubscribe>\n<we:ifMailingListEmpty>\nmsg = \'Please check one of the mailing lists!\';\n<we:else>\n<we:ifEmailExists>\nmsg = \'The entered email address allready exits in our database!\';\n<we:else>\n<we:ifEmailInvalid>\nmsg = \'The entered email address is not valid!\';\n<we:else>\nmsg = \'ATTENTION: An error occured while subscribing the email adress <we:var type=\"global\" name=\"WE_NEWSLETTER_EMAIL\">! Please contact mail@mydomain.de!\';\n</we:ifEmailInvalid>\n</we:ifEmailExists>\n</we:ifMailingListEmpty>\n</we:ifNotSubscribe >\n<we:ifUnsubscribe>\nmsg = \'The email address <we:var type=\"global\" name=\"WE_NEWSLETTER_EMAIL\"> was successfully removed!\';\n</we:ifUnsubscribe>\n<we:ifNotUnsubscribe>\n<we:ifEmailNotExists>\nmsg = \'The email address <we:var type=\"global\" name=\"WE_NEWSLETTER_EMAIL\"> does not exists in our database!\';\n<we:else>\n<we:ifMailingListEmpty>\nmsg = \'Please check one of the mailing lists!\';\n<we:else>\n<we:ifEmailInvalid>\nmsg = \'The entered email address is not valid!\';\n<we:else>\nmsg = \'ATTENTION: An error occured when unsubscribing <we:var type=\"global\" name=\"WE_NEWSLETTER_EMAIL\">! Please contact mail@meinedomain.de!\';\n</we:ifEmailInvalid>\n</we:ifMailingListEmpty>\n</we:ifEmailNotExists>\n</we:ifNotUnsubscribe>\n\nif(msg){\n    alert(msg);\n}\n\n//-->\n</script>\n</we:ifnewsletterexists>\n   </head>\n   <body  background=\"/we_demo/layout_images/bg.gif\" bgcolor=\"white\" leftmargin=\"0\" marginwidth=\"0\" topmargin=\"8\" marginheight=\"8\">\n<we:ifnewsletterexists><we:form id=\"114\" method=\"get\"><table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"620\">\n         <tr>\n            <td width=\"27\"></td>\n            <td width=\"50\"><img src=\"/we_demo/layout_images/we_logo.gif\" width=\"50\" height=\"50\" border=\"0\"></td>\n            <td width=\"54\"></td>\n            <td><span class=\"headline\">&nbsp;CMS Channel - </span><span class=\"headline_small\">Newsletter</span></td>\n            <td class=\"normal\" width=\"74\"><we:date type=\"js\" format=\"d.m.Y\"/>&nbsp;</td>\n         </tr>\n         <tr>\n            <td colspan=\"3\"></td>\n            <td colspan=\"2\" align=\"right\"><span class=\"normal\"><b>Search:</b><span class=\"normal\">&nbsp;</span><we:search type=\"textinput\" size=\"15\"/><span class=\"normal\">&nbsp;</span><input type=\"submit\" value=\"ok\"><span class=\"normal\">&nbsp;</span></td>\n         </tr>\n         <tr>\n            <td width=\"27\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"27\" height=\"10\" border=\"0\"></td>\n            <td width=\"50\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"50\" height=\"10\" border=\"0\"></td>\n            <td width=\"54\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"54\" height=\"10\" border=\"0\"></td>\n            <td><img src=\"/we_demo/layout_images/pixel.gif\" width=\"418\" height=\"10\" border=\"0\"></td>\n            <td width=\"74\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"71\" height=\"10\" border=\"0\"></td>\n         </tr>\n         <tr>\n            <td class=\"normal\" width=\"27\"></td>\n            <td colspan=\"2\" class=\"normal\" valign=\"top\"><we:include id=\"90\"/></td>\n            <td bgcolor=\"white\" colspan=\"2\" valign=\"top\">\n\n</we:form><we:form id=\"self\">\n<table cellpadding=\"5\" border=\"0\" cellspacing=\"0\"  width=\"500\">\n     <tr>\n          <td class=\"normal\">&nbsp;</td>\n                <td class=\"normal\" colspan=\"4\"><we:textarea name=\"text\" wysiwyg=\"true\" width=\"500\" height=\"100\" autobr=\"true\"></td>\n   </tr>\n</table>\n<table cellpadding=\"5\" border=\"0\" cellspacing=\"0\"  width=\"500\">\n      <tr>\n          <td class=\"normal\">&nbsp;</td>\n          <td class=\"normal\"><b>Subscribe</b></td>\n                <td><img src=\"<we:url id=\"304\">\" width=\"10\" height=\"20\" border=\"0\"></td>\n              <td>&nbsp;</td>\n               <td>&nbsp;</td>\n               <td>&nbsp;</td>\n       </tr>\n <tr>\n          <td class=\"normal\">&nbsp;</td>\n          <td class=\"normal\" align=\"right\">Salutation:</td>\n         <td><we:subscribe type=\"salutation\" values=\"Mr.,Ms.\"/></td>\n         <td><img src=\"<we:url id=\"304\">\" width=\"10\" height=\"20\" border=\"0\"></td>\n              <td>&nbsp;</td>\n       </tr>\n <tr>\n           <td class=\"normal\">&nbsp;</td>\n         <td class=\"normal\" align=\"right\">Title:</td>\n          <td><we:subscribe type=\"title\" values=\"Dr.,Prof.\"/></td>\n              <td><img src=\"<we:url id=\"304\">\" width=\"10\" height=\"20\" border=\"0\"></td>\n              <td>&nbsp;</td>\n       </tr>\n <tr>\n          <td class=\"normal\">&nbsp;</td>\n          <td class=\"normal\" align=\"right\">First Name:</td>\n                <td><we:subscribe size=\"40\" type=\"firstname\" /></td>\n          <td><img src=\"<we:url id=\"304\">\" width=\"10\" height=\"20\" border=\"0\"></td>\n              <td>&nbsp;</td>\n       </tr>\n <tr>\n          <td class=\"normal\">&nbsp;</td>\n          <td class=\"normal\" align=\"right\">Last Name:</td>\n               <td><we:subscribe size=\"40\" type=\"lastname\" /></td>\n           <td><img src=\"<we:url id=\"304\">\" width=\"10\" height=\"20\" border=\"0\"></td>\n              <td>&nbsp;</td>\n       </tr>\n <tr>\n          <td class=\"normal\">&nbsp;</td>\n          <td class=\"normal\" align=\"right\">E-Mail*:</td>\n                <td><we:subscribe size=\"40\"/></td>\n            <td><img src=\"<we:url id=\"304\">\" width=\"10\" height=\"20\" border=\"0\"></td>\n              <td>&nbsp;</td>\n       </tr>\n <tr>\n          <td class=\"normal\">&nbsp;</td>\n          <td class=\"normal\" align=\"right\">Format:</td>\n         <td><!--<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td class=\"txt10\"><we:subscribe type=\"htmlCheckbox\" checked=\"checked\"/></td><td class=\"txt10\">HTML Mail</td></tr></table>--><we:subscribe type=\"htmlSelect\" values=\"Text-Mail,HTML-Mail\" value=\"1\"/></td>\n               <td><img src=\"<we:url id=\"304\">\" width=\"10\" height=\"20\" border=\"0\"></td>\n              <td></td>\n     </tr>\n <tr>\n          <td class=\"normal\">&nbsp;</td>\n          <td class=\"normal\" valign=\"top\" align=\"right\">Mailinglists:</td>\n          <td><table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n                        <tr><td class=\"txt10\"><we:subscribe type=\"listCheckbox\"/></td><td class=\"normal\">Sport</td></tr>\n                       <tr><td class=\"txt10\"><we:subscribe type=\"listCheckbox\"/></td><td class=\"normal\">Politics</td></tr>\n                     <tr><td class=\"txt10\"><we:subscribe type=\"listCheckbox\"/></td><td class=\"normal\">Computer</td></tr>\n                    </table><!--<we:subscribe type=\"listSelect\" values=\"sport,politik,computer\" size=\"3\"/>--></td>\n                <td><img src=\"<we:url id=\"304\">\" width=\"10\" height=\"20\" border=\"0\"></td>\n              <td><input type=\"submit\" value=\"subscribe\"></td>\n      </tr>\n <tr>\n          <td class=\"normal\">&nbsp;</td>\n          <td><img src=\"<we:url id=\"304\">\" width=\"90\" height=\"2\" border=\"0\"></td>\n               <td><img src=\"<we:url id=\"304\">\" width=\"10\" height=\"2\" border=\"0\"></td>\n               <td><img src=\"<we:url id=\"304\">\" width=\"10\" height=\"2\" border=\"0\"></td>\n               <td>&nbsp;</td>\n       </tr>\n</table></we:form>\n<we:form id=\"self\">\n<table cellpadding=\"5\" border=\"0\" cellspacing=\"0\"  width=\"500\">\n       <tr>\n          <td class=\"normal\">&nbsp;</td>\n          <td class=\"normal\"><b>Unsubscribe</b></td>\n                <td><img src=\"<we:url id=\"304\">\" width=\"10\" height=\"20\" border=\"0\"></td>\n              <td>&nbsp;</td>\n               <td>&nbsp;</td>\n               <td>&nbsp;</td>\n       </tr>\n <tr>\n          <td class=\"normal\">&nbsp;</td>\n          <td class=\"normal\" align=\"right\">E-Mail:</td>\n         <td><we:unsubscribe size=\"40\"/></td>\n          <td><img src=\"<we:url id=\"304\">\" width=\"10\" height=\"20\" border=\"0\"></td>\n              <td><input type=\"submit\" value=\"unsubscribe\"></td>\n      </tr>\n <tr>\n          <td class=\"normal\">&nbsp;</td>\n          <td><img src=\"<we:url id=\"304\">\" width=\"90\" height=\"2\" border=\"0\"></td>\n               <td><img src=\"<we:url id=\"304\">\" width=\"10\" height=\"2\" border=\"0\"></td>\n               <td><img src=\"<we:url id=\"304\">\" width=\"10\" height=\"2\" border=\"0\"></td>\n               <td>&nbsp;</td>\n       </tr>\n</table>\n</we:form>\n             \n            </td>\n         </tr>\n      </table>\n<we:else />\nThe Newsletter Module is not installed!\n</we:ifnewsletterexists>\n  </body>\n</html>',0,'',0);
INSERT INTO tblContent VALUES (7543,0,'If you would like to be updated about future plans of<b> CMS Channel</b>,\nplease provide us with your name and email address. Your\ndetails will only be used in conjunction with information pertaining to\nCMS Channel and will not be passed on the third parties. If you don\'t\nwant to receive our newsletter anymore, please unsubscribe by entering\nyour email address in the field provided.',0,'',0);
INSERT INTO tblContent VALUES (7582,0,'<we:ifcustomerexists><we:sessionStart/></we:ifcustomerexists>\n<html>\n   <head>\n      <we:title>CMS Channel</we:title>\n      <we:description>Demo-Website for the CMS webEdition</we:description>\n      <we:keywords>cms,webEdition</we:keywords>\n      <link href=\"<we:url id=\"89\" />\" rel=\"styleSheet\" type=\"text/css\">\n   </head>\n   <body  background=\"/we_demo/layout_images/bg.gif\" bgcolor=\"white\" leftmargin=\"0\" marginwidth=\"0\" topmargin=\"8\" marginheight=\"8\">\n<we:ifcustomerexists><we:registerSwitch /></we:ifcustomerexists> \n     <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"600\">\n         <tr>\n            <td width=\"27\"></td>\n            <td width=\"50\"><img src=\"/we_demo/layout_images/we_logo.gif\" width=\"50\" height=\"50\" border=\"0\"></td>\n            <td width=\"54\"></td>\n            <td><span class=\"headline\">&nbsp;CMS Channel - </span><span class=\"headline_small\">Customer data</span></td>\n            <td class=\"normal\" width=\"74\"></td>\n         </tr>\n         <tr>\n            <td width=\"27\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"27\" height=\"10\" border=\"0\"></td>\n            <td width=\"50\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"50\" height=\"10\" border=\"0\"></td>\n            <td width=\"54\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"54\" height=\"10\" border=\"0\"></td>\n            <td><img src=\"/we_demo/layout_images/pixel.gif\" width=\"418\" height=\"10\" border=\"0\"></td>\n            <td width=\"74\"><img src=\"/we_demo/layout_images/pixel.gif\" width=\"71\" height=\"10\" border=\"0\"></td>\n         </tr>\n         <tr>\n            <td class=\"normal\" width=\"27\"></td>\n            <td colspan=\"2\" class=\"normal\" valign=\"top\"><we:include id=\"90\"/></td>\n            <td bgcolor=\"white\" colspan=\"2\" valign=\"top\">\n               <we:ifcustomerexists><table cellpadding=\"6\" cellspacing=\"0\" border=\"0\" class=\"normal\">\n                  <tr>\n                  <we:form id=\"157\" method=\"get\"><we:sessionField name=\"ID\" type=\"hidden\"/>\n                  <td width=\"120\" align=\"top\">Username: </td>\n                  <td><we:ifRegisteredUser><we:sessionField name=\"Username\" type=\"print\"/><we:else/><we:sessionField name=\"Username\" type=\"textinput\"/></we:ifRegisteredUser></td>\n                  </tr> \n                  <tr>\n                  <td width=\"120\" align=\"top\">Password: </td>\n                  <td><we:sessionField name=\"Password\" type=\"password\"/></td>\n                  </tr> \n <tr>\n                  <td width=\"120\" align=\"top\"><br></td>\n                  <td><br></td>\n                  </tr> \n                  <tr>\n                  <td width=\"120\" align=\"top\">First Name: </td>\n                  <td class=\"normal\"><we:sessionField name=\"Forename\" type=\"textinput\"/></td>\n                  </tr>  \n                  <tr>\n\n                  <td width=\"120\" align=\"top\">Last Name: </td>\n                  <td><we:sessionField name=\"Surname\" type=\"textinput\"/></td>\n                  </tr>  \n                  <tr>\n\n                  <td width=\"120\" align=\"top\">Adress1: </td>\n                  <td><we:sessionField name=\"Contact_Address1\" type=\"textinput\"/></td>\n                  </tr>  \n                  <tr>\n\n                  <td width=\"120\" align=\"top\">Adress2: </td>\n                  <td><we:sessionField name=\"Contact_Address2\" type=\"textinput\"/></td>\n                  </tr>\n                  <tr>\n\n                  <td width=\"120\" align=\"top\">Country: </td>\n                  <td><we:sessionField name=\"Contact_Country\" type=\"textinput\"/></td>\n                  </tr>\n                  <tr>\n\n                  <td colspan=\"2\" align=\"top\"><input type=\"button\" value=\"back\" onClick=\"javascript:history.back()\">&nbsp;&nbsp;<input type=\"submit\" value=\"<we:ifNotRegisteredUser>save<we:else>Change data</we:ifNotRegisteredUser>\"> </td>\n                  </tr>                  \n                   </we:form>\n              </table> \n <we:else/>\nUnfortunately the customer management is not installed! \n</we:ifcustomerexists>       \n            </td>\n<td valign=\"top\"><we:ifshopexists><we:include id=\"149\"/></we:ifshopexists> </td>\n         </tr>\n      </table>\n   </body>\n</html>\n\n\n',0,'',0);
INSERT INTO tblContent VALUES (7001,0,' ',0,'',0);
INSERT INTO tblContent VALUES (7002,0,'How to put an end to stress',0,'off',0);
INSERT INTO tblContent VALUES (7003,139,'',0,'',0);
INSERT INTO tblContent VALUES (7023,0,'Wind energy',0,'',0);
INSERT INTO tblContent VALUES (7049,0,' ',0,'',0);
INSERT INTO tblContent VALUES (7048,0,'Recent news from the CMS-Channel',0,'',0);
INSERT INTO tblContent VALUES (7060,0,' ',0,'',0);
INSERT INTO tblContent VALUES (7074,0,'Comedy about the holiday experience of two girls',0,'',0);
INSERT INTO tblContent VALUES (7075,0,'Boys\'n Girls',0,'',0);
INSERT INTO tblContent VALUES (7076,0,'Finally, school is out for summer and everybody has a little fun in the sun. Nina and Melissa have recently turned 17 and are best friends. They are determined to make this summer a very special one. But then a handsome boy moves in next door and both girls have a crush on him. Now, Melissa and Nina become rivals in a funny game of love and romance, each trying to catch the attention of the young lad.',0,'on',0);
INSERT INTO tblContent VALUES (7158,0,'0000001001669400',0,'000',0);
INSERT INTO tblContent VALUES (7157,0,'Good morning<br>Software',0,'',0);
INSERT INTO tblContent VALUES (7153,0,'News from around the world',0,'on',0);
INSERT INTO tblContent VALUES (7154,0,'News, <br>Weatherforecast',0,'',0);
INSERT INTO tblContent VALUES (7155,0,'0000001001653200',0,'000',0);
INSERT INTO tblContent VALUES (7156,0,'Boys\'n Girls',0,'',0);
INSERT INTO tblContent VALUES (7183,0,'The webEdition Software GmbH relaunches your web site by using its own CMS. You can easily produce and administer the contents of your site by yourself.\neasily create and change your own content.',0,'off',0);
INSERT INTO tblContent VALUES (7201,0,'Demo-Website for the CMS webEdition',0,'',0);
INSERT INTO tblContent VALUES (7216,0,'Withered Roses',0,'',0);
INSERT INTO tblContent VALUES (7217,0,'The hairdresser`s shop is the perfect place to pick up the latest gossip in town. This is why Marlene comes here to chat with her friends. Presently, the talk of the town is a young lady who has recently settled here and suffers from a unilateral facial paralysis. <br />\nSuddenly, this lady enters the shop and takes the seat right next to Marlene...',0,'on',0);
INSERT INTO tblContent VALUES (7218,0,'Drama',0,'',0);
INSERT INTO tblContent VALUES (7246,0,'10140',0,'',0);
INSERT INTO tblContent VALUES (7247,0,'no',0,'',0);
INSERT INTO tblContent VALUES (7248,0,'����\0JFIF\0\0\0d\0d\0\0��\0Ducky\0\0\0\0\0D\0\0��XICC_PROFILE\0\0\0HLino\0\0mntrRGB XYZ �\0\0	\0\01\0\0acspMSFT\0\0\0\0IEC sRGB\0\0\0\0\0\0\0\0\0\0\0\0\0��\0\0\0\0\0�-HP  \0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0cprt\0\0P\0\0\03desc\0\0�\0\0\0lwtpt\0\0�\0\0\0bkpt\0\0\0\0\0rXYZ\0\0\0\0\0gXYZ\0\0,\0\0\0bXYZ\0\0@\0\0\0dmnd\0\0T\0\0\0pdmdd\0\0�\0\0\0�vued\0\0L\0\0\0�view\0\0�\0\0\0$lumi\0\0�\0\0\0meas\0\0\0\0\0$tech\0\00\0\0\0rTRC\0\0<\0\0gTRC\0\0<\0\0bTRC\0\0<\0\0text\0\0\0\0Copyright (c) 1998 Hewlett-Packard Company\0\0desc\0\0\0\0\0\0\0sRGB IEC61966-2.1\0\0\0\0\0\0\0\0\0\0\0sRGB IEC61966-2.1\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0XYZ \0\0\0\0\0\0�Q\0\0\0\0�XYZ \0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0XYZ \0\0\0\0\0\0o�\0\08�\0\0�XYZ \0\0\0\0\0\0b�\0\0��\0\0�XYZ \0\0\0\0\0\0$�\0\0�\0\0��desc\0\0\0\0\0\0\0IEC http://www.iec.ch\0\0\0\0\0\0\0\0\0\0\0IEC http://www.iec.ch\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0desc\0\0\0\0\0\0\0.IEC 61966-2.1 Default RGB colour space - sRGB\0\0\0\0\0\0\0\0\0\0\0.IEC 61966-2.1 Default RGB colour space - sRGB\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0desc\0\0\0\0\0\0\0,Reference Viewing Condition in IEC61966-2.1\0\0\0\0\0\0\0\0\0\0\0,Reference Viewing Condition in IEC61966-2.1\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0view\0\0\0\0\0��\0_.\0�\0��\0\0\\�\0\0\0XYZ \0\0\0\0\0L	V\0P\0\0\0W�meas\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0�\0\0\0sig \0\0\0\0CRT curv\0\0\0\0\0\0\0\0\0\0\0\n\0\0\0\0\0#\0(\0-\02\07\0;\0@\0E\0J\0O\0T\0Y\0^\0c\0h\0m\0r\0w\0|\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\r%+28>ELRY`gnu|����������������&/8AKT]gqz������������\0!-8COZfr~���������� -;HUcq~���������\r+:IXgw��������\'7HYj{�������+=Oat�������2FZn�������		%	:	O	d	y	�	�	�	�	�	�\n\n\'\n=\nT\nj\n�\n�\n�\n�\n�\n�\"9Qi������*C\\u�����\r\r\r&\r@\rZ\rt\r�\r�\r�\r�\r�.Id����	%A^z����	&Ca~����1Om����&Ed����#Cc����\'Ij����4Vx���&Il����Ae����@e���� Ek���\Z\Z*\ZQ\Zw\Z�\Z�\Z�;c���*R{���Gp���@j���>i���  A l � � �!!H!u!�!�!�\"\'\"U\"�\"�\"�#\n#8#f#�#�#�$$M$|$�$�%	%8%h%�%�%�&\'&W&�&�&�\'\'I\'z\'�\'�(\r(?(q(�(�))8)k)�)�**5*h*�*�++6+i+�+�,,9,n,�,�--A-v-�-�..L.�.�.�/$/Z/�/�/�050l0�0�11J1�1�1�2*2c2�2�3\r3F33�3�4+4e4�4�55M5�5�5�676r6�6�7$7`7�7�88P8�8�99B99�9�:6:t:�:�;-;k;�;�<\'<e<�<�=\"=a=�=�> >`>�>�?!?a?�?�@#@d@�@�A)AjA�A�B0BrB�B�C:C}C�DDGD�D�EEUE�E�F\"FgF�F�G5G{G�HHKH�H�IIcI�I�J7J}J�KKSK�K�L*LrL�MMJM�M�N%NnN�O\0OIO�O�P\'PqP�QQPQ�Q�R1R|R�SS_S�S�TBT�T�U(UuU�VV\\V�V�WDW�W�X/X}X�Y\ZYiY�ZZVZ�Z�[E[�[�\\5\\�\\�]\']x]�^\Z^l^�__a_�``W`�`�aOa�a�bIb�b�cCc�c�d@d�d�e=e�e�f=f�f�g=g�g�h?h�h�iCi�i�jHj�j�kOk�k�lWl�mm`m�nnkn�ooxo�p+p�p�q:q�q�rKr�ss]s�ttpt�u(u�u�v>v�v�wVw�xxnx�y*y�y�zFz�{{c{�|!|�|�}A}�~~b~�#��G���\n�k�͂0����W�������G����r�ׇ;����i�Ή3�����d�ʋ0�����c�ʍ1�����f�Ώ6����n�֑?����z��M��� �����_�ɖ4���\n�u���L���$�����h�՛B��������d�Ҟ@��������i�ءG���&����v��V�ǥ8���\Z�����n��R�ĩ7�������u��\\�ЭD���-������\0�u��`�ֲK�³8���%�������y��h��Y�ѹJ�º;���.���!������\n�����z���p���g���_���X���Q���K���F���Aǿ�=ȼ�:ɹ�8ʷ�6˶�5̵�5͵�6ζ�7ϸ�9к�<Ѿ�?���D���I���N���U���\\���d���l���v��ۀ�܊�ݖ�ޢ�)߯�6��D���S���c���s����\r����2��F���[���p������(��@���X���r������4���P���m��������8���W���w����)���K���m����\0Adobe\0d�\0\0\0��\0�\0				\r\r\r\r		��\0\0�\0�\0��\0�\0\0\0\0\0\0\0\0\0\0\0\0	\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0!1AQaq���\"��2#�BRr��b���$�	3SsU�\0\0\0\0\0\0!1AQq\"a����2R���Bbr#3��\0\0\0?\0�Պ����7���-�W5�c\rnj�9��-\"�\rC�+�(�詟�[��Յ�T  �ʇ��OT������[L�\Z�#V�2z6�3�	�Z�L�-Z��{\r\Z\Zg�y)�\'T(�j��\n2���:�XV���´�IBp�%c��)�\\�Ja*rW*fU$�D²�$:(�\\YNXy,�\Z��2��e0޷Q���޶Q�.+��o[)��z7���5ϣ]��Z�\r�V�����\n���rc\\�ZBpe?�0���k���L�h��U��p����(�/�Q�j��Y?��Ka�vS������I��y\'�6�L��2�O$d��\'�y,�u1��D�u>XFK_O�_�y,+�OLa<��RZG�eUIi��<�j�-^:!8R������8��L(M_��%JH<��+*r���\\J��jtYMZ���A䱴7��&��az�+b�������?5ϫ\\�Z�_/��kM�T�2�Q\\�W��`\'\r+S(*g��<��CS���.(�<rK*�r:�4FFYW�%����ƸFI3k��6�u#���qW<��a�W���y�=�\Z��\0	�8i���:x#%�F��D�h�W��+�[��q!ZJ��=���UD��)�q�<���MW��5YLռB0_=S���K����)N��)��,8Y�4�P�౴5�����chtRų�����W���\rl�j���2*s^�\r4��֧��iZ�Pi��^�1���K*����L�m�4\nv\Z��u��Y,,��6	�W��j�U�b�/�6)�~�M6��6)����N,0���	�x�}O%Qb�)��5&5�Ө����3\ri5�j�W��L�d��I�8¤�S�5)j��THQ���d�穡�<��-��l�[=d&`�z�gD�.�\rJ���C䲴.$�hV6�ձe�8�-�YO���V�{�Z�v�P�N\Z#f�Cj�q���c\rm��u�8)�#����aj:�?$�Yea�%�a;j�%�J��Il\n���؛\n�#b�EnO`>�0��X�T�hj�6$N����Iu�q�d��.����׊�)�og���Q{u�q|��N%4��̕�%1c¤�s���,Z�KW���K\n2�O$�5\\�8�,�W\Z+�LԮz�]e�Ԯ�n(DԪ�lgE(Ԯx|��Al��YZ\ZD�؇�V�������g��t=Z��洛:�����L��g\rm�l�CT���Vp�Ū�u���0�c���0��܀S7Nʧ�%���mS�r�+jy#q�AS<B[�\nd�	n&��ӂ{�V���{�Z\Z�\\ܰ���[��M��(؄�=lp䴥�Jj�_g��7���n�nX�O ���&s�NҚ�A�x�8��xT������JZ��TY8P���\'R��q�TI`�z�tUev*\rtU���٭�O�����Y����jQf���S3³�O��Zʗ���g��t�z�4X͞��W��E;��1�-�`��J&�!v:���ƫ���S7\\��\r� �j:|4S�\Z�����\'mM8j��T����T���D�-[\Zy�0���}��~��-Y4�4$n0��·�,\"u,q	��c\Z-#�����I��\0q���r~��33�a�C�#�2���Դ{�$�L�\'���j�ZE��FZ��\\XaFJڝ�`�5mņ�x�\\X����\\Y8-�_�\\H�)�Xk���MI�V�0�,�֫��Yc5&�_P�<`��<ps\n�?HxsY�Yt��x�.	�ޘ4���X�~Ħ� �*���qU��(�C�~*cÂ��av:�wZ��\nv\Z�2��8�7LںpS��VVθKr�(���Oq�qT~�;��N4�V\rA���=��W9������W�.X@�\'s�\\\\������L��\0X����>�fj6)���a녲8�����}]c���Le�B�OD���ڍ��;{w�Ebh����_#A�̘�Ss������Z2�[�bp��,m��Bzc����_��ƾp�{��\\ꪷ,(�9|�b�|Ы��_R@�-\"ł���,X,�ثa��U���&�%�[�*�)�%���YZ�#�2�e\n>���eN��_�9^E��=M������gs���he\\����n0g\\k�x)ئ�VӇ�D�j�b��an:��0�ʾIlXLڧD�	�[\\�6$���%�l���ņ¿$d`}7,\'�Z:�Q@���U�L<���t�?j�Ar�n���w��m��YH�ϒ���3�xx9g�ݶ��{/w��{�d�pm)\Zث��YNc�ON�G&z�3���=��d���>���ێoH�Y�t\Zd�ǚ�/�D��)y,�m��jX6����oVË�~!upޕ򙏳��V�;۷~��q_�;\0i�Ş�����,�\"}����1�h��s�$pQ4Ǵ������K8<)��������i,؇���`��ʨ��In���ڤV�Ч�6��1��XMp]�~�!�u����7�X��TO!jk^�q�r��z�E3q��54�S7,.�W����L��0��0���)��v��	l���3��[탞��G��a�\0#\'��a�Dd��ç�0�����{z����f����h��埚����s>H��睑�������R7M�B_B�Ǯ\Z�\' Fà>\'����>Z\"�^9��=��ݟ��h��nhM\r�Y�+�-�V~.�|�����t�qfsR��Zh�4���kK�rSW�v���\'�V6S�c����3[3��\"���2��h����{���񣊉�\0���2]�]L��\nV!�p�������-\"�Ԯ�\\|�ŊjUf%{\rI��2y+�Ԇ�@���2�H.E�NiE�^����4�g�u�:y�0�^n�Ƀ����B�n�N+�oO���`�\Z��R�����~��,.2�\rذ�\Z��[�\r�rKd�%l8FK	�FF����n؇�2Xm�h���3�\'�����ذS�M,mt0��F�\n�5#�{B�����z��C�y��o�8���|�X�sW���}��c?\r6��A��V�>5�׏��v`�c��J\"�ԾX\0�a���*�{�޽�9���n��y�7�v>]����e�i�K�,E�tX�שM�x�\"�E�p�Z�jQb0��ѡ]�ǂ�.ZY`�W-I�G��.Z�\\�tԪ�I)� ���$�e4,�������U�x���}G��N��\Zh��4���蚘A_�Sq��u�Kb�i��[6s�!<�+c��(�A\Z[հ�����a�J20�b6)�Ќ��h�F�Z9�Kے�B{st�	d����Oa��~�_��p�����[{�ù9mKy|U�k؋2;�p�,0�4\\I\n��W2{�������Nǹn�Y�k�+��ѭ�]�4�U�l�ץ�!�\Z�Kf�ϋ9�\'/J����.�s����zV8�жN�i��/������\"Ml�0H���q�5�ƺ��i\Z��`\Z�#��\'��H�\Z��\'�W�К��\\rL��5]A���s)�Z��.�]Dt��V�\r>k亏���׭�aD�eM2���μѹan8I��f[<�un#Ot��3Or��!�ܰ�5=�����VzS�j:Q��8FūOa�\'�jԵ\rQ���\r\\\r������,p������m�o/��|��#C�����U��4|/�=�G�(��Z]�s�}-�j��i�y7ĭi��o����a���N��s��\0��wg��(�ʽP�\\?������ܑ>^���<e����_`wC�>�Wm�r/V`�`|z��Ey����߲w����;�=�u+�7�[{���4��8�Ú�9�|�☃ع�[�j:�\rh���C �k�|i��Q��%��WPt�-�¨�-	-+�B��~A�5Q�S�Ei�_$��-ϟ4u�w�j�g�|ls���6���*�d�\Z�qpW�D�m���j����O��S\0�r&j�h��5l{���\'�j�jΟ�7-YU�j�j��Ɖ�Z1�7\Z58F�V��7=����=�8:����\0v�+��ل�G���${EY�����k���ak�����vM�u��&��\'�0����s��L<��)�{)c8�֢?	 k�Uo�)�y=b�5=A��=��6�������e�+�\0�ُ���X��5$�4\Z���aZ�>\'�;�l�ٛ8��{e\n���G;!oS�7��9�U7Į��>�w�,m�72F�1�d���D��S�H��4���!�H%8�8�|�א�s�[�W�����Ď�4~�B�zO�\n;��n5��y���e4[c���;�SE��ⵎtMR���\\s�D��U�ѰvUG1h۩WX�g�\'�SFr�9�Fz�}R�d\'�\Z�u�@\\�T�c�\'�-\Z�#�z4s�eS��<Q�=�\0p������>>V���;�%�q\\E~�\0��ݿdX흖rA�v���`a\\gd���yf⎱t���h��^�Z�\0����5�o�˘��3����[���F�P��L�CK�\0��ec�oN�7�w�dPܶ}�}�G�wܠ�n}#f?��<C����%];��!����\n6>��{���Ks���{E1�]ޜ��AoP��ͧDOq5�©Ú������n��w����E�{y��n�W��.Hח�F�@���b&|&SZN�X��K�36�V�Վ)�\"�{��Gƴ�<\0Q��oҌfZ���c�0>ݬbQ��<Ky#���d��\'(�a<��N�\Z+�Vv�|���W�-�W�y�\0[	�t�IO�>���R��	2���4���ϝ+�8������{G��޾�|m�y���\rm�&�H�\0�gD��K����/��W�y#�D[���J���]S��ȥ\0z��͉\ZO�h��⹣���������?����ϸ�X��=�3�Q��NǓ�����Z����&{����z�h}����x�7�	�+ރ^g9?�W\'/�s���[��x�����?�~�����j>�hl���@pg��Pc���\0�*���{~�W��⯷o�����ཫ<ѳ~����qY�Y��O�\rsc\'�[O�rǕ�Y�s�>�����{c�k�>��}�J���*����v6\\���y8���Y��]�_��O���;��#��ή��uҐ�3$%p�s�_�<%�N^I�k\'~8�|���\nO�bٞ�;�����w�N�;�����^/���8���=����;��۞�_�<@|\\ֹ��S~Ӹ������������O�=��Um헸v��ݠ�P�_:��r��Y���x��|�aިY\0ױ�������Jy����b�g��ԧ�#�S���56�R}$n�Ծ�Q�(��9˱����N���?��<������\0������Ë�+���?���s�k�՞����\0�`^5��i��^��s&�^f8�S�8�s��ֲ�\0�6�����3�������\0}����\08{=��u<�+�9�۾c�7.û�I=t��\\ӓ憿O���nw��]�$-/�m�����X	�ao�|rZ>��xv���I�׫�w��V\Z?h�<��SfRGx�\'nY�D���*���b9*0�{�\0|��b��)�#C��&9\Z��%�y�WS��ϾGC��c����7=�{lϙ̡IZX�>��|G��\0�8\ri<03�M�j��k���vgh��F�\0u�\".��|��Y��ZE���|��a��൭���W�����EzO�\0 3��tA\r\ny\0����F�y�lX���yPn7�:���#��W��\0�BS>p����e�;\'����Cv����d��4�z��g�o:â��=����&��/��\Z�M;v\0�^Ǳ�ss���>�ßs���x�\Z�ego���<�ڥk\\���>:���x�����?u\"O�}�w,O�ͧԏLϮy��7�^�	£�c���������=�7p��և�����p+������]~����մ<[�7ݽ�ݽ�e���3\"�*�^G����I x O�}?��mY�bvy���qsky�s����������d�����(AZX\"�G>6�׸��y\\���nI����{5�N�)39�x`��~���\\^�O�:���b�zG,yLKJz�k>s1�<�ޯ|;o�����b��\0�wzI��k�\Z	�Μ����Ŷ����\0�xo4�<ζ������Y%��q�G�Ɵ��\'�_,{������E�}��ͺ�Qݜ>hd��uy%�-��M}?�\'ɿ��g���\0)|�\0������ػ6͸n�ַN\'E,2��?��5�8�w��O-�#�Q��oФZ�����o����J�M�:�����#,9�9����҈���Ѭw�Q��=�\0�Q�������7L����D���d� d8r���<q�2��9�v�kߴ���=�N+�[��m��3.%� �\0�Z[���b=�ƾ��X�m�K�����%�Jy�\\vWaoY����S7�{L��W���!i���3�3�+�ݴ%$�pG�s��8,����J��\0e��\0�8���/����˱�\0�\0@���%���\0�5OA<\0�mÂd�#��(ɵQ&2�3��( \0�0\0@\0 \0�\0@\0a� \r�>H%<���((( \0�\0@\0 \0�\0@\0 \0�\0@\0 \0�\0@\0 \0�\0@\0 \0�\0@\0 \0�\0@\0 \0�\0@\0 \0�\0@\0 \0�\0@��',1,'',0);
INSERT INTO tblContent VALUES (7272,0,'10171',0,'',0);
INSERT INTO tblContent VALUES (7273,0,'no',0,'',0);
INSERT INTO tblContent VALUES (7274,0,'����\0JFIF\0\0\0d\0d\0\0��\0Ducky\0\0\0\0\0\0\0��XICC_PROFILE\0\0\0HLino\0\0mntrRGB XYZ �\0\0	\0\01\0\0acspMSFT\0\0\0\0IEC sRGB\0\0\0\0\0\0\0\0\0\0\0\0\0��\0\0\0\0\0�-HP  \0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0cprt\0\0P\0\0\03desc\0\0�\0\0\0lwtpt\0\0�\0\0\0bkpt\0\0\0\0\0rXYZ\0\0\0\0\0gXYZ\0\0,\0\0\0bXYZ\0\0@\0\0\0dmnd\0\0T\0\0\0pdmdd\0\0�\0\0\0�vued\0\0L\0\0\0�view\0\0�\0\0\0$lumi\0\0�\0\0\0meas\0\0\0\0\0$tech\0\00\0\0\0rTRC\0\0<\0\0gTRC\0\0<\0\0bTRC\0\0<\0\0text\0\0\0\0Copyright (c) 1998 Hewlett-Packard Company\0\0desc\0\0\0\0\0\0\0sRGB IEC61966-2.1\0\0\0\0\0\0\0\0\0\0\0sRGB IEC61966-2.1\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0XYZ \0\0\0\0\0\0�Q\0\0\0\0�XYZ \0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0XYZ \0\0\0\0\0\0o�\0\08�\0\0�XYZ \0\0\0\0\0\0b�\0\0��\0\0�XYZ \0\0\0\0\0\0$�\0\0�\0\0��desc\0\0\0\0\0\0\0IEC http://www.iec.ch\0\0\0\0\0\0\0\0\0\0\0IEC http://www.iec.ch\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0desc\0\0\0\0\0\0\0.IEC 61966-2.1 Default RGB colour space - sRGB\0\0\0\0\0\0\0\0\0\0\0.IEC 61966-2.1 Default RGB colour space - sRGB\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0desc\0\0\0\0\0\0\0,Reference Viewing Condition in IEC61966-2.1\0\0\0\0\0\0\0\0\0\0\0,Reference Viewing Condition in IEC61966-2.1\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0view\0\0\0\0\0��\0_.\0�\0��\0\0\\�\0\0\0XYZ \0\0\0\0\0L	V\0P\0\0\0W�meas\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0�\0\0\0sig \0\0\0\0CRT curv\0\0\0\0\0\0\0\0\0\0\0\n\0\0\0\0\0#\0(\0-\02\07\0;\0@\0E\0J\0O\0T\0Y\0^\0c\0h\0m\0r\0w\0|\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\r%+28>ELRY`gnu|����������������&/8AKT]gqz������������\0!-8COZfr~���������� -;HUcq~���������\r+:IXgw��������\'7HYj{�������+=Oat�������2FZn�������		%	:	O	d	y	�	�	�	�	�	�\n\n\'\n=\nT\nj\n�\n�\n�\n�\n�\n�\"9Qi������*C\\u�����\r\r\r&\r@\rZ\rt\r�\r�\r�\r�\r�.Id����	%A^z����	&Ca~����1Om����&Ed����#Cc����\'Ij����4Vx���&Il����Ae����@e���� Ek���\Z\Z*\ZQ\Zw\Z�\Z�\Z�;c���*R{���Gp���@j���>i���  A l � � �!!H!u!�!�!�\"\'\"U\"�\"�\"�#\n#8#f#�#�#�$$M$|$�$�%	%8%h%�%�%�&\'&W&�&�&�\'\'I\'z\'�\'�(\r(?(q(�(�))8)k)�)�**5*h*�*�++6+i+�+�,,9,n,�,�--A-v-�-�..L.�.�.�/$/Z/�/�/�050l0�0�11J1�1�1�2*2c2�2�3\r3F33�3�4+4e4�4�55M5�5�5�676r6�6�7$7`7�7�88P8�8�99B99�9�:6:t:�:�;-;k;�;�<\'<e<�<�=\"=a=�=�> >`>�>�?!?a?�?�@#@d@�@�A)AjA�A�B0BrB�B�C:C}C�DDGD�D�EEUE�E�F\"FgF�F�G5G{G�HHKH�H�IIcI�I�J7J}J�KKSK�K�L*LrL�MMJM�M�N%NnN�O\0OIO�O�P\'PqP�QQPQ�Q�R1R|R�SS_S�S�TBT�T�U(UuU�VV\\V�V�WDW�W�X/X}X�Y\ZYiY�ZZVZ�Z�[E[�[�\\5\\�\\�]\']x]�^\Z^l^�__a_�``W`�`�aOa�a�bIb�b�cCc�c�d@d�d�e=e�e�f=f�f�g=g�g�h?h�h�iCi�i�jHj�j�kOk�k�lWl�mm`m�nnkn�ooxo�p+p�p�q:q�q�rKr�ss]s�ttpt�u(u�u�v>v�v�wVw�xxnx�y*y�y�zFz�{{c{�|!|�|�}A}�~~b~�#��G���\n�k�͂0����W�������G����r�ׇ;����i�Ή3�����d�ʋ0�����c�ʍ1�����f�Ώ6����n�֑?����z��M��� �����_�ɖ4���\n�u���L���$�����h�՛B��������d�Ҟ@��������i�ءG���&����v��V�ǥ8���\Z�����n��R�ĩ7�������u��\\�ЭD���-������\0�u��`�ֲK�³8���%�������y��h��Y�ѹJ�º;���.���!������\n�����z���p���g���_���X���Q���K���F���Aǿ�=ȼ�:ɹ�8ʷ�6˶�5̵�5͵�6ζ�7ϸ�9к�<Ѿ�?���D���I���N���U���\\���d���l���v��ۀ�܊�ݖ�ޢ�)߯�6��D���S���c���s����\r����2��F���[���p������(��@���X���r������4���P���m��������8���W���w����)���K���m����\0Adobe\0d�\0\0\0��\0�\0\r\r\r\r\Z\Z\"\"###)*-*)#66;;66AAAAAAAAAAAAAAA, ,8(####(825---52==88==AAAAAAAAAAAAAAA��\0\0�\0�\"\0��\0�\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\n\0\0\0\0!1AQaq��\"2��BR��br���#3�CSs�c$�TDt%\0\0\0\0\0\0\0\0\0!1AQa�q�2��BRr�\"b��т�S��\0\0\0?\0�4�E4�=\'�{�����F�?�����\'�cEt��z__)�^�P��B����pS^����k��m�]��<-I^�YA�w,�LdR1���v�D��foN�VH�mfl��k+;ZL�Ժ+�6����Z\0{\\���B�v��M��F\r��\0�鹇��8l�pY_F�h�t$�{y!|a��̎����n���u��\\L^���	��M�И�<�|d�q���E�����/KZ��\nI���h.�]����,�B�;?�bG�\"��^}/���O��\'���}P���Q~ݳ��y�\0SP�Kx��[������{.%�?�7��v`����/�N�>����3��U��9\\=�Z?��m˱s.��Â�}В�F�<����7���)��{=�GK��\0o-�߆�=���\\���$?E����2�P��^@IŔ��)\'�=��օ��̧gOu��W�@�\Z2	%?I��0\'��ұX1��{K�/*̗1���X7�\\Gj>�B�3�m+�r�9ۚ\'�w��UJ,���zx�����c��[\r��a�eJXs&��I�裖_�qD�x����|v�[7�H*����\r��~�~	r��/J�/Ć�_��p��\r��%%΅�fZ��[�!�1Z-�5�B�o5��R�\'G�1�+�l�\r���5*���J���.���f<&oc���(i1�U�<�~_/����l���{(��Α�踻\Z�lb��b���-���EOȄ���04�Wjһe�_��$t�-g	ku �1�n�q��/�E�&=�֯���ww�I\0:~�|�=��Қ0�`�\0��О�VcR���wW����z���cl|���j�tg޺�.��9jh�ӱq���1��ۀ]n[�%��i�$�u�W���I%�t��Tk���`ބ��7�e�!)f��`���\0�ø�,%� �Ь\"u3@nVAQe\n��F�zN�j�T(!�r�sbČ�k�Sa]@N�R�I���Q*�ɺ[�ɜ�M�x�K�N�M��g��É��c�o�v̓�1�<� �v.���v�&�[5���i����\r�^S�\0��K��pLl��na��?�q\\�B��W���?t�������v��\r���\0��Fis�	�d����8EE;wYj?�_�F_�i���C�6��^��4�s(����3�\\�o�3W5�{1k�z�㽻kZ�E���}���O��0��l�H��>a@Cm#d��H�\n?������n��?�\0y����df�9��REv������B:�>��m[cZR�ml��~�T�a��E��k	���dwڙ�GK0w0���Z\"q�r\\\\�WON*�W�Q��?mW����j-��$��7����g��\0���!o2�<-�\"��5���h�\Z�ֻK���g�A����t��]�ph�Iu�۳���K��=���p�\'��)���Z��3qU�\"���?�����0�[\r�%�{��Mp+4|��B\0��ov�X�9|v��P�o��T�:��~s�Ԫu�n1��mk�浍.\'`�u��)#]Ѡ9F>R�v�QZG���k�ei������¸q�x�n��ހ�����E���VKFtL�w*�v�\0��:Z��: u;��\n��B GJ ��3SP!HTb-AJ��R�ZE�;�ml�\04,=�>�@�T�ɘ���]��`��Kb�ߪ�u\n�W��٥�u�?i� a�)\\އ\n���;������*\n�@�U�m�ֽ���<�����hF���J�,��s19�8)@\rW��Qk��\Z�\0���g9�|��Ѵ�����y�X�<Ѱ<�Sֽ�j-;j�[����u�Bg���^͈\rh���������B���8O��i!;�a�߂9Qr&��t�f�жEag\0��Z:iR�T=K.��9����!����\n�TAC%\Z*�D�MP@�B-%V��\0f�2\0P���j�\n�Q�����}�Q��+\0*fWJ�E���\nPhV\Z�f�;Uje|ýB�t���oz�=�<�$�\Z�KIR9\"}t8:��+KF�e�*}(\n�J+Ԯ��\Z:�\Z�G���v��3X\n��V�+-�\'c��k�ހ�V�+\"�qSI@]\Z�WR:�V��w+�� |�Fi$�i�H\"Nei����h[BhGRMi�MUY��,c#\\��C��Bs�%3\r.��\rM04A������=�<�����֮u)��t�yӴ�۪�ԚUo�ێOV�ι:�5�Rl�c���ŭ��<:�Z����x�M5(V(;�O�rU�� ^�2� �U�+\Z�J�L����C�n��Y㼖}u�k@�:R��]�a>W2����H��J�COeT���RQ&�!T^MR�*�@}Aq($�M�hH�5a9�*h�3�u-���@.45[9��O�����p4i�\\�m�!;5RT�Ŷo�y�y��Jdi��ղF��� �­#hW�m\\�M����@y��(>UѦ�ry��UJ��\n�\"�r@	.�$)Wt\"�G�b�rLgK�i��@�xd}\"���c�hl^*ԓ���Ӱ�A\rq9��o\n���+�`������si>1��#\r��]�\r:W��X�U�1�S�~=�T�Y��x��*v֩��W�NZܓpײy��z\nu�?q��S{��Jڟ�����[ob��É���G1x�G���d�Dql#�O���VhŰ�&)�7����Oc=5H}[��1A���.��o3�����-$n^��1�/��d��������1��5����,��(�Ոd�c�V��\n��U�����?�Ҧ��z��S&.g�p����;p+�b(.w9�=��]K�ig�a�����+�e;��t��8��:�����u�F��\Z���8?^/4;�Dak��\'����7��7jpeU����@�Җ�a��S-�M1	8�l0s��C#�}K#��7��Gk�S9����֚�0Ɓdm�[rhv�`��+�����z�UV�Ǽ���n�\n�b��8EhK���Z\0#�Z8��Z���.�4(�ޯQ܀Q���\n�  �N�N;H��FЃ�A��T�Ҵ�@�\"��B+��\Z�s�\Z\0��b-M�/�U�ܼ���{3@��q^�\Z�0^g�5�s˥%��Eq9��F����Pe��b�Gj�ǃ<�H׼�����c|A�uA����w�g5��\"� �jZ���悷.��0w1�\'1���\r��Q�uF���H.d���xq���.&?�η{+$e�2ֵ��k�w�?��oҦ��Nx7�m-I�4��o�8���p]��&�����@\r�~�+��ׇr�gk}���+��u�TkL`�S\Z;�O ��r�}^h�F���s=Ui�\"GL=�b�L܄�X*�ҰtĽ:E\0WGS\0��1�(H�-��T��8�d\n�ַL�}�A:�=�w-ۥ��̴�.��Mr�Z�qp�C�4�i�j�6Kw1\Z�cNA��}+;�����\\-�(0!��$<���mk�{���j�� ��$l8���ɸt��C��g�2{����p�1 ��$SА��U)�q��|ȧ\0�m�Q�����W�09�1�T41�\r�\0���:�0�m�jsPߖ�[Q�q�#S�8ο��	���]4�IB*=�[���y@�B	ع�>s�ed�~�R��}��)��9��ӿvj�v��k��cVT\\u�=�H��v�]�/-��������~f�- B��l�S����P�-j!�**SL�5K�pv�;S�G�mj���Sz����l�\0�<�;�� a^����&�R[����G��-=���r�a�9��̄3�3}7�_7��8f3�#� l��(F�Y�(g;���cph�^��Ȯv�1�0���7Ğ�Z�q����foHg�j��w��%m*]+Z;�\\�|\\�g�Z�!����/��?���Z�m��}������\\�����>8G�������{������к\r�&�ί�wg�1^F��F0��sjr\'�����j�g.�\'���9���)�Rɲ	ƒ�\ZI�e�5��K�G-k�j��u���z=Ƴ��>�\'K|U��⸞�`���aإ�X\0d2&�[KL<%yX�e�	���/v�S~ii)�ϕf���5�״��p��vFղ�����8BëC��{�s�I��Oa�gecf?�q�;�+F�c���i�AI\r`��<}�~U�F��j����\'ax�%@��n����!�Z����j0k��\0�\n�!�vE�j�ش��Z��E#�ӀQ����\Z�A!|��to3���j.�� �����5�?u�\0E�l�*�g\n%���k�y�bOr�߷�X��c�c�ĸ�Aر~з�X��f��n��ki�^��ͥ�([=�|;\Z��a��n�1�x�;����:\0v��֌[�-?Dc��֐b�_�94�{n-�qe�M^A�v�/$�Z��x�ܶ�E}� \Z%`s��UhJ��^�tkA<�[X8�>\'��E�V�v�\'/uG��wާʓ�~F��I�=K�{�kI�%���6�y�2�8��6��*���B���MWx1#s��?�\ZV�ڛx?�Z4h.?��kd��d8�\r�v��W|�V�����\'?h���%���эI�7��\\�#\Z��}$�껄n$��KW;�?t�|��t`�@���6��w�E����icyq�{�ހ	y>��_��G�oz�k�*��sWF��62���ԩ�Qd�#�h~�UW	���7\0½j�^�p=glt�QL��ꃋ� 34\Z��Gپ�:9C\\9t;�\'���J�h@$��Z_2��َiq-�1*�4�י�����4Q�!�e�r�$�f�\Z���O���o$(�v�!��{�A�Z�Z�XI��Pk����B�n���!itq�8�������F�\nhd��c~��N�b�v�r�Z��\'�\\8��N��F\'��Y�@��� 4u̣&�;j~eŒ�ߛsx�tQՃ��A��osC��������X�K��B�Ou�hG\"$z�^_oin�{q�6e\\�̔Fι?�׏�QP��\n��\n�*p�B�\Z�j@4͆a��7qd�6�� Cj	>��F���EV{{�ȃKI�8��u�0��#��gr�{ʡ��qƒ�H{M>1��&��O����z��&��Y�e����b��Z\r�]��޹eᷳ��K\\\0�Ɣ؜\\��\\3�{B�!m��_~��7�P���@�y8u:�����p�Y�s5\"�TcB���]ÿ���x*��P��i5�S˼C\n�B��\'Ǵ`kB(�&oV��ڇV���1��O�\0r��:�N�D4�g9�n��(m�-��h���\00]&j��uu�O�r�_D�G?�a���+N�Wnߐ�o4N2�H�p\'h��0���J���+��a+륢�r�l=�eNf��Z�Ұ|\\��á_�ȤBv�@��rí�քģ��좒X2�\'�[��*֫��f��0G!��\0n��+�Kc�.���i�4h�g��ZH\rK��O�Y6�Px���;�+����b��}�e�qKAs��BH�AR?ۖ,�5�����s�*FK�o	�e|���J�n���,9�rG#\Z�7���Ï�Pr(o\'\'a�G�Wa�:��\"�T{�P&\0G\rā������d�x��L0�N4�,��\na�ڠ�[�		�P8����1�<��\r�׭���\"�5���Ԕ�-	#�Z)��N�K�9ō�WU�¸��ɜ��2���<H�ʭ�V:g\0�x��M7W�lb��W9Ƥ��ē�RCf�]�%Ir�ǜ�ۂL��Y�nwH�Rm+H�?\r�$�#O���4g~+3�CW}7����H�f��;ЦR�v[*�����3���kNu�?�	����TW���\Z,\'���%ځ�ܶ�f�+w	&���R�G~\\�����B9�_�L��\n�4ӡ�T�5�C��st9Ǥ�S9u�x4��]��F������5�n�tb�-�8�#�T�A��)$��76��:�o�Ag0��l�V��o-&����8�R�6�o(m���\ZB�?o�k|��,囹�\ZeO0B�I��0�26��PB9?��m�����81�0�����4�9իq�+���m��ˮf@/;:@�K9�\0G�@^�_�܈�����\'	�͂���\r���rH���:����p��\"J��\r!���@=(*���36�����|\'{ûj���0�Ӊ�\'s)5�62ۚ�`�UU��k@�\\SN�H�s��$�֣Э�v`㸣ӆ8!Eqdnc����t;AB��4�pM�i�  %cng>�a�@C��\0W@<�B��\\4�%����\'-�h�iԈi�H�Ͷ��\"JЙ��A�F�B���\Z�gZl?2����ZC�F	3�t�6 (H[���8�h-V�^����v 2>`��+7x~�zJqc�jK)!sK<��@nc8	9�D�(t�F�����Ԙ#�Ң��12@�\'�$E��*�Q�(�R�ѹ�X*]M���8Վ#�9���\03uu�7 4��@\'\\���cn��a��Zr�ZܜkЖ���\n-���R��^�(�]���\ZW$4~`�t�B��Ɲ�+^�D��q��\Z0�:a�\\6�c�f��~�?or��9�Z(6�:����SLe�x�z��ϖ�E���.�S�b��0![�������*�E�\Z��۔Q@J�D(���mR��B�*B�f�ǰ����T��c�QD\0�ÙQ��v�Q\0T�Ks]_5TQ\0Xኺ�������CS�E\Z�4�QR\n���@�Zb��	���)Ң���',1,'',0);
INSERT INTO tblContent VALUES (7294,0,'7894',0,'',0);
INSERT INTO tblContent VALUES (7295,0,'no',0,'',0);
INSERT INTO tblContent VALUES (7296,0,'����\0JFIF\0\0\0d\0d\0\0��\0Ducky\0\0\0\0\0\0\0��XICC_PROFILE\0\0\0HLino\0\0mntrRGB XYZ �\0\0	\0\01\0\0acspMSFT\0\0\0\0IEC sRGB\0\0\0\0\0\0\0\0\0\0\0\0\0��\0\0\0\0\0�-HP  \0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0cprt\0\0P\0\0\03desc\0\0�\0\0\0lwtpt\0\0�\0\0\0bkpt\0\0\0\0\0rXYZ\0\0\0\0\0gXYZ\0\0,\0\0\0bXYZ\0\0@\0\0\0dmnd\0\0T\0\0\0pdmdd\0\0�\0\0\0�vued\0\0L\0\0\0�view\0\0�\0\0\0$lumi\0\0�\0\0\0meas\0\0\0\0\0$tech\0\00\0\0\0rTRC\0\0<\0\0gTRC\0\0<\0\0bTRC\0\0<\0\0text\0\0\0\0Copyright (c) 1998 Hewlett-Packard Company\0\0desc\0\0\0\0\0\0\0sRGB IEC61966-2.1\0\0\0\0\0\0\0\0\0\0\0sRGB IEC61966-2.1\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0XYZ \0\0\0\0\0\0�Q\0\0\0\0�XYZ \0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0XYZ \0\0\0\0\0\0o�\0\08�\0\0�XYZ \0\0\0\0\0\0b�\0\0��\0\0�XYZ \0\0\0\0\0\0$�\0\0�\0\0��desc\0\0\0\0\0\0\0IEC http://www.iec.ch\0\0\0\0\0\0\0\0\0\0\0IEC http://www.iec.ch\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0desc\0\0\0\0\0\0\0.IEC 61966-2.1 Default RGB colour space - sRGB\0\0\0\0\0\0\0\0\0\0\0.IEC 61966-2.1 Default RGB colour space - sRGB\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0desc\0\0\0\0\0\0\0,Reference Viewing Condition in IEC61966-2.1\0\0\0\0\0\0\0\0\0\0\0,Reference Viewing Condition in IEC61966-2.1\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0view\0\0\0\0\0��\0_.\0�\0��\0\0\\�\0\0\0XYZ \0\0\0\0\0L	V\0P\0\0\0W�meas\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0�\0\0\0sig \0\0\0\0CRT curv\0\0\0\0\0\0\0\0\0\0\0\n\0\0\0\0\0#\0(\0-\02\07\0;\0@\0E\0J\0O\0T\0Y\0^\0c\0h\0m\0r\0w\0|\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\r%+28>ELRY`gnu|����������������&/8AKT]gqz������������\0!-8COZfr~���������� -;HUcq~���������\r+:IXgw��������\'7HYj{�������+=Oat�������2FZn�������		%	:	O	d	y	�	�	�	�	�	�\n\n\'\n=\nT\nj\n�\n�\n�\n�\n�\n�\"9Qi������*C\\u�����\r\r\r&\r@\rZ\rt\r�\r�\r�\r�\r�.Id����	%A^z����	&Ca~����1Om����&Ed����#Cc����\'Ij����4Vx���&Il����Ae����@e���� Ek���\Z\Z*\ZQ\Zw\Z�\Z�\Z�;c���*R{���Gp���@j���>i���  A l � � �!!H!u!�!�!�\"\'\"U\"�\"�\"�#\n#8#f#�#�#�$$M$|$�$�%	%8%h%�%�%�&\'&W&�&�&�\'\'I\'z\'�\'�(\r(?(q(�(�))8)k)�)�**5*h*�*�++6+i+�+�,,9,n,�,�--A-v-�-�..L.�.�.�/$/Z/�/�/�050l0�0�11J1�1�1�2*2c2�2�3\r3F33�3�4+4e4�4�55M5�5�5�676r6�6�7$7`7�7�88P8�8�99B99�9�:6:t:�:�;-;k;�;�<\'<e<�<�=\"=a=�=�> >`>�>�?!?a?�?�@#@d@�@�A)AjA�A�B0BrB�B�C:C}C�DDGD�D�EEUE�E�F\"FgF�F�G5G{G�HHKH�H�IIcI�I�J7J}J�KKSK�K�L*LrL�MMJM�M�N%NnN�O\0OIO�O�P\'PqP�QQPQ�Q�R1R|R�SS_S�S�TBT�T�U(UuU�VV\\V�V�WDW�W�X/X}X�Y\ZYiY�ZZVZ�Z�[E[�[�\\5\\�\\�]\']x]�^\Z^l^�__a_�``W`�`�aOa�a�bIb�b�cCc�c�d@d�d�e=e�e�f=f�f�g=g�g�h?h�h�iCi�i�jHj�j�kOk�k�lWl�mm`m�nnkn�ooxo�p+p�p�q:q�q�rKr�ss]s�ttpt�u(u�u�v>v�v�wVw�xxnx�y*y�y�zFz�{{c{�|!|�|�}A}�~~b~�#��G���\n�k�͂0����W�������G����r�ׇ;����i�Ή3�����d�ʋ0�����c�ʍ1�����f�Ώ6����n�֑?����z��M��� �����_�ɖ4���\n�u���L���$�����h�՛B��������d�Ҟ@��������i�ءG���&����v��V�ǥ8���\Z�����n��R�ĩ7�������u��\\�ЭD���-������\0�u��`�ֲK�³8���%�������y��h��Y�ѹJ�º;���.���!������\n�����z���p���g���_���X���Q���K���F���Aǿ�=ȼ�:ɹ�8ʷ�6˶�5̵�5͵�6ζ�7ϸ�9к�<Ѿ�?���D���I���N���U���\\���d���l���v��ۀ�܊�ݖ�ޢ�)߯�6��D���S���c���s����\r����2��F���[���p������(��@���X���r������4���P���m��������8���W���w����)���K���m����\0Adobe\0d�\0\0\0��\0�\0\r\r\r\r\Z\Z\"\"###)*-*)#66;;66AAAAAAAAAAAAAAA, ,8(####(825---52==88==AAAAAAAAAAAAAAA��\0\0�\0�\"\0��\0�\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0!1AQa\"q�2���R���Bb��r�#3S��C�T\0\0\0\0\0\0\0\0\0\0!1QAa����\"BR��\0\0\0?\0�PU���Eh�^�����\0�#t9�\n&P\n�I��Y\'*fR(&Ġڠ��J���] Zl.��\0�7T�ʀ��QB���a\n �	B�(T�EHIDU3�Q\0��LՂ;v���HYy*��[nvf�n5͔���س�3!�Y�_N:��v��H\0*��\0�j0�5nYIC��p	�!�X#sH��@\"PL֌�#\0*	�DPA 	��0Oe���@gea�	�ԐQ�8�1�*�u��pP�&N\rQ�YI�*e�R`�Y9:(��r�2ş��#(����C���\'�@��#�(e	GɆ���0W֙h�^�jĄ�2�`66��)J�\0>+M�D���\n��?��q�E.\n��Os;-Q�	Hl�c,��\'q̢�dp\'ږ�$�	�G���~P�ޡ��B1�W2��*���F�ru��t�o�{̄�pC�_�IN���.��)��QmD\"��kp2,E\\&�`�c*A\"dB! PL��\'��8�A�)!��w�b�%��19\"ثiP \n�\0B`�*#)��2�+`��`� ����sC(1e#B�9�r*Td��*�BH[\'Y��^<�2�ď�\r�r��b�i@Y�f�\"�q3�]��[��	z�bʣ2C\'\\�\'p��\'�IR�(��K���+U�0�P+�P\0��4\0�!%H��D4Z�`�]��&$DfJF�0�>��̕bXXZ����&�E�qhSt\0�Uq�w�V,�s|k�7�2G^7:��A�G���rcjQ�\'�^˚�ձj[އb���-���L�`�:��U�IH�b<6+ڠ�\0#z�!3j��)\0	d�ݘE��X(�Z���IWD�TDb,qV7ǈE�uTB��:�ؙ-I�Vm�l�bt*�X� �PH�@؅R�� ��6��>�d�Ge4D\n�_�Z�t�\"G�2���L�_�۔�# w�A.��2a�@W�#}sܾGkф�M�7�e�}|Y��V���\nX��]�r\'�0����ƈ����[-�g؎>����{�dFF��F�q��.��ޤ�1Eڸ�W;��17^<�=9�f���q��K���Ň&CZ���Ln܉��b,��B|W:7��X��vM埱+�C��\0tzwb=�+�ϸ�qpܺI�\\G\'�)�:C���~������F�zQ��� ��a)�D�=�oqb7m���PiW^j�@�:p�1����ڕZ��b3�ƭ2θ���G$�|�{�BA��V��E�b% \0a,�$Q����BRbk���3a��nG���qUc�w���1Ň)C.��\'nW���+)����HՅA/भ^�K�0k�>\Z,���\0\'�ketF���[g�hi�\Z�?�+=ݸ��\Z����乢����q�\rd����$�\0�{�w��vG{(�\"[e)�) ��N1��ʄ��\\��_��@pA-���DĻ�#�@/������hu���\0�o��%���a�D0;��<W�3�����vݩ[;o��E�\"NO���^{��9An*�\nI�B$\Z.E����-�gs���];�~�f�g���A�\0Ȁ���oȯ��M�=k��ϵTn^�xȅ�}؋R��$e�y�>廵�N�ɭ�Ԣ�l��\'B��G�Y�\'K,[$��;���P�s[e>Ђ6�秽,@P�U�oPgo�-6\0(m0rYi2��)��2��BEq��v���.�ݾ�]9<�G�Cv\\>��y��m8�K����zl���r�\0��J-!V!�\Zf���v�>0;Ұ��2]��o(�Y\0��<�)�5;eb�6�\"_!����x���d�1\"b_q`x���{8�|Q�p4�.�j������ED�q9q@\"Or\n�2���	2�1���&���B�J1��j�.�nz�&`-�VK��;H�D,FN3�wH����q�7u��/rD��䲗�#c��q}AA�L̨H���<��U��<Bb�s`D����G�v`~_�@�z�p���]}�4���\0�q�\0�Q�Gu����]%�h���]����ÿV�̊���@��@p�?S���|��]����w~�)S��U��H4��s�V=�V��zօ�m_ɫ�2�Dq��C\r�i����F$@�\'�}#;߿�^�s���dJ�� ����R�[Pnf���V֖�}�	�U���ޥ_&RX\r�(D��[JN����-��asz�U7�)+b-�/sKt��L�@�%���eF�1�,���%�����⮈�e�#\'ۉH�P�)�d`%���z�J�Nv�Va���:���g��m�b���0#m�4���\0�m̓.�nK3���C��y(nD�U�R0����nű)$>	w�^�/v�����a �+�\'��ee�T�澥��Og�BQ%˻���Y�;s��8�]<w3�vF;���|��_f����v�f̉�eu���\0rw�Z�\0��\0�?�#x;�{�}@�O���j=��cU`�T&\0+�%�8�6�B�(\n\0�3�p�($��0V�CDL$��+�7	��6����P?a�52��76#�(�S�$2�\0Q�Vѹ�E��n���\"\"D��bf���HK�5$JY��H)�0$p\'ج�P��P��f\n��%X��ř�n?0)!����I�r�	f��!�䇩�A��3\rP�\\����3��P���ƅQ��f��U#*���j����D�\rh�J:2N�j�E�$Rb4�)��	JQ/�P�\r���D�� �-�kR7v���nG�2/P�.:)��3%�X��J���ď��B3z�Î����(���\Z�\"��c(G\0��qa�XE�`3M�gw�\'lAbx�Rd멸���:�xj���䣺r�l*\\��5A�N��uZ9a��2 �A>�W\'Z��aP�\Z�P�n�#LX����X�NtS��VA\"�����b�J���>�B���\Z�F��nv��\r-1\\����$�|V��s�K�,�K@mR���dN�<*�r#F\Z\n��y	6�{\n��*��yh*s��Ж1�(P�7e����	��vɈ�\\��bI�h���IM\n��@��U?�A}\n�=�kr�vPG(�b\0~L�h�U�Z�A��S�o_�c(\\$����5�?�+�ɠ��e�8��i��ߪd!�Պ��(�E\n�h�*�9h�^X��z:��вT-NH�@����<r8��4J�f�/ffr_�2\"\01\0�4gh�q�T�Gȹ�d��e�̶G�(�G3�Ni#دp$po����{�f���7����8���$	�	tF�@�\'20)��D-�;�\rR/[�h\0�:0t�~N3\0y�ܜ�\')#BM���̷��ǁ �&�!\'s��N*\\7~a;{e��0$b�s��F䑟t�v�$;�[�[wzgq��08����\\E��Q�����Li(��kN	\Z�(;֔	{x���<�erLH�GJ]�汀s�A ��m�m81��-��L^��)�f[u7�����Z�w�A��Cj���ۜb^NN���D��я�T�\'��%Nt9�+R	I�\\�C�\nF��bAU�ܑb1b��\\Ը$z�h�=f�Z�� ���JF%�:�	�7Ξa[�*=GJQff<Vm��b�w�t���}�,�����!�-Q/�0�n�Ū4X��ש���+pRbN�_�f�|5+rr��l�J�jq�:� jq*�\n��@dK1�F��߈�D�Ϥ�qG\0�\"�qԪL��ݮt@H�:�RQܒI�g%M�ɠ�s�@�Aa�3=�#op��c�~eIGeG�Y	��v�����`���\0n�eO2U�r�).h�/�-�q&������ZĨ��@lQt�`���f�(\"F+]�`[��[���H��.ɖ�����O�N`��V���2e#�\r@��i�����I�X =پob�Q�y����U�X1�b����z��(���j&YV���FbB���\ZL�[YD�$$A���?���ؙ�J) \'��.h�c ���Y�韌������F������k��\"8c����c�\r�&A�$� %Z��U������r�څ�`�\'	��5My�4�n_�5&R�mD�Hw���\"P�����b	�8���ey��G�B��V�\nD;��T����7$sK�8�d�%��-���5uQE·nCE��Fl<�Edy�E���y�������EF���Q�jΧ+��(�q����wy��E� ��v4t�5[�����������[b�����h���_�$ۙ�Y����d��H��3��T�6���\0��uYf�C%�&}śV�pY�u���q�QE1�Ԑ��|�j����W��䢋?&�ؿ���������{��QD��(���*����E��*[�7�=}�/�X�:x���j��Y|QB��z~(����(�|���',1,'',0);
INSERT INTO tblContent VALUES (7302,0,'250',0,'',0);
INSERT INTO tblContent VALUES (7303,0,'226',0,'',0);
INSERT INTO tblContent VALUES (7304,0,'����\0JFIF\0\0\0d\0d\0\0��\0Ducky\0\0\0\0\0F\0\0��\0Adobe\0d�\0\0\0��\0�\0\n				\n\n\n\r\r\n\n��\0\0�\0�\0��\0�\0\0\0\0\0\0\0\0\0\0\0\0\0\0	\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0!1AQaq\"2����B�R#b�3�r��CS��c$\0\0\0\0\0!1AQaq\"����2B����Rr�#b��3�C$��\0\0\0?\0馝i^7�뉋X�L�46��<�(ME��PMEQ��PME���R�h�\n�MP�#ʀ\r�R�`�E@6��b\0�TbH6��eE5G��B�\Z�I���H!Rzޙ&#��ZOSN��srScƬPeNhh��G��Penhb�I�\r\\�T��Ĺ��O��5�a�sk\nW����*��9���|ۺ�Uu�A\"��ų;R�a:�	)t��@�t�MK��*�lR���C�����>D���X�����t��Q�\0�Z��ckC�d�u�+��և\r�c�Yw�v�<�:NA�:^[b[�Q�d��n��Ø�������{��aG-��G��G��29�I����S�ds���q���S�b󄕛��J��M�b��E\\�*���m؎�W(��V	�2�1!	/�$�)��G|l�F�ҝXB;�j�!w���ua�2)̛���\ZxZa�L�w�W�}ȇ�Ș	t�_:�-��(W��pR��EaW��$�۪����D�y�.��ڂ\n[\n�&���.u/�3��\0��Cr�H�e+eIJ�-�$�J�H��\n�*M)~t�&�\n�*HB�z����T�\0��:_¯�Li��c�������۷�e�ο9,��V_.�<KpZ�f���V�uvm2;��\'�ˊ@(�SO���!��)a-��{h��H�o�d��=�K��9���?�KȈ�RO/�owI�^�����\\��S��U�lj����s���e��K��7֭��P�LX+��(���\'H�8���TF6�U��qgOx�;��d��B^�.\r\\�ð��]��=�o��)H=:�r��&�\r��}Ȑ>��\Z4�v�=;��n��^���m\"��	3�%8���[Ѫ)y�C�>Z��\r�v\nLşP�5ɹu\'�Ѕ�1+�~\'�>گ�Y�@+�%B��+���<�B�xJ%GZA%V�z��ꌮ{tћb��|�IEӱ`��+�ϋU�9��%�a�&�A��mk���V�;���,Ɓ	���6B�c��v�8�_gr)�I���i�2�Ζ�4��E.��m��_�JFɠM�Ζ��*�	�T@���\0�\0�HO�I�l�9���̴�T�V�=�4ʮ�2����e$�����-\'�k1>!� �Z7��$�[�/�^��H���(�vU�9Rٚ�ְ����K��J1ōrҔ�����Ť,��C�H�m�LD�8�o�\r�u_�I�Ɋ$Q��1�K)$xQ͓\rB�1Pd�\\t�)�4�*cq�QeіO�cy̯թ���\01\0����1UfE��]t�v)�P	�X�uIp/��<K���#$A7`V��>&���R�}�j2�l�*CaI\r��ō+Q�ӏb0N8�o2߬�n�Z���J�Si�\"ή+�BJ��a�O���0c���@i/^�{��ޥJu[�\'�݄Z�Z��G6E�T���7�Ish=+v�]̌��x������;�Ex)�k]�3��F{w�7��s�m�_����Ƚ�,��D_S�\n5t[BJ)�2RR��EΖ�V�9�#A� ݔ�|#��(�؟�b\\n����5�Y�y7w�t��Պ��&w�ƑDr�z�R�5*�ʍA!J�\0!4\0��qQP;�3�m$�E��!�\'�c���ަ��%Te-gd:�1ЫW/u��h�vv�,L?���ɣ!��|C��^�n�Ӧ�\ZWh�����}lR�*J��<�DmJ?U47BQ���M���\n�D��+1�dZ~�O:�5)R~.-�m��$\\�Y�rcUE�E��x�� �RmҺ�~��8�����`;�!�)jX	mF���\0KIU�uMN��`�t���W��s�Ip;�����r��N���������Q������_����b�U��I^���Bv�C��d���֣ww��%D8��G�^z].jUG��S��3��m���cd������]�V^�2G���TY�Ớ¢4�jR\0&���{L�Zߦ��6�a�5���ă�xޟgfv�Q��	۫0xهq��TRAҽl��ƌ��;r�,���\Z�B���`�\0⎒�nKo���J\Z2S��\rc�e�ƕGF��\\¸��8����-{�6���!5Z#d��$�ԖEΠ�\n[���d=����z\n��T�e�4r�}GV�2O�\\i:���*!���U�`��HBj\0)4T\0�(�QP�T�O!mCz���΍�F���\0S	I¹���*D�=�wg�-J\'Cz�I�֕\n�*ɰ����F��M�� 	l\rOuk�K3�1���D6��@	�[֋Ta9be����6�JkM�-X�o7��§5.��z��O�T���I�]�C71���[� ⪳)`=�$�9��̅+\"��jnt�q���O��K�ih��b:V�\n��ĉ(����R�ҩ��CT7.���l�Ap\Z_�T�,R\rb�b:�7��OBu)2���p����ՆT8wj�DC�\Z^��jL:d8��4�P�l��e�[V~ֳݵ��gq�2k��&$�B��4��//N&�yOFwo\\kr���ۭ}mT�[�Knt����B�|F��G�x1x�$2�V�!@�Z�QMb[nR��4�ܖT�1��R�ڭ:�k�-���L����QJ�$�I+%L�%�Za�SU2�ݸJ������5>�$\0HW��˶ѷg5ri�q]ε�L��BTT�TT�T�P	�\0�@�J k\\�M��=!¶���e��o{kN�H)�)�T�oqW�PVX;s�e��%Z��^(���+#=��;�\0\'�\\U�t�E�lKqJ�c�|;v�[�2I�N?\"^;:�0JP�*H����v�9�d�qĻd���\Z-9�\Z��[���tnCR���\Z��)?�,ש�o�C���:<ȡWa\0A��%8���na*��U �U����T��o��\0���S�TL���{m\\�J�eW�ۜ6�g:滆�2܋\r�Rm�Qw�J�E�:TZ��3r����W��Yw�ي/X�O�k{�)#��Ź���4<>2;���ꓯ���$M�2����A�&�`6�?&��;��%�L)2(I$�^�V���LadBBB]E��^���d�\r�iA*S��%�}#����Mrn�R;���\0��3П�c�-���i����E{���e*gg���c�u(\r-&�&֨���MQ�v�2��)#��^\nO\Z�I�ɷ�ʈ��\\F�Ԫ����yTT�	�\0��\0\n�\0!U\0w���o1���TQ\\YpU*P2�] ��):ޮ�!�O][�5p��\Zg��X~�!@�&�LV����r*1Hu>�@�^��ك��zj*��I�d6\\(b0Q\Z_�v#Ӥ�g]F)��\'yD����k�P6�y֥�Q�L��.SM#\\��XB�0$��@�=6��=+%�	�x�l�5��/{����N�p�,�ۭ��\n6s�J��nGs�M��Z����W�;r�έ(=@#��mM�7$�!��f�e;-±��qQ�GS��\\��U�هUiR��b(��󭐰�Ss|�X���2\Za����jĒų\r��y!T̓A�P�OA{TNV�V�ޭb.gXISoOBT>d��\\���,�G�n$����R]L��x�Z�.�id]�7��3�Ky��A��WzޥD��z\"��R��Ϯ{���!�(xyWK�۩�C����$�(��\0P�rB�ښ�ZE�h����r<\\0�y*���B���=E�Sifn�Ȥb��o�ΰ\'~PN�V�uk�]�E�Utg����S/1p� ��j-qTܴ���!55T07Q`Bj	\nU@�C`V����**ߌwԊگ�\\�!i��GU%\0�9,���j�9�D�OJaG-F�Tԓx�\"}�`�Kn�\r������S����m����E��l<I�c�rƧ����\"�����z�=�y����G���cj$�ǀ�K��Y�����\"r��(\"y�cSM�;׺�i96����Tmru�+�v!�`�^��-��̊�m��9�H�~D�ͥ���̝�v�)1!e$�X��l�A�eS�b$���|��V�#�X���<��薖c��^ø�w����6�6�F��F�5Ιj\"�������mгd��s�]��̶�\\�\0�rx0���7�X\Z�U+�o2�e.9>#����pZ�\nW��U�D[pe���(��7o���*AIJTlW^WyeBx<ř9G4���n=f:��o�Q>��\'��c�\Z���i�w-)\'S@���lF5�\\d9%\0��H*�2qu���ؘ�ye6�I�����&�P%�Ⳳ2\n���To���z��ơԝ���i���	J�S`4U�k���)F��e���E�������-�>�Vv��]kSW��Y�f)�jZ�\\NM��o�\ZB\nl��\'�V)��њ�MU\rU�U��QRBKP+���QP6^=#Ղ�u��\ne��JI�fԣ-J���Q�f�M\\���I!rUF������i\\o#O)�-�� ��l�$v����+�h����NUh�f8�]�&��kR�ZB��:�n��[��TluӭG����d҇���*���z��NxԽZ�x*a�\\�vZF�jH��&���ŲH�%�d��2d��6��J��a�����W=2�d�ӇqH�Ud�d�����C��T�j��ƀ9��\0�WyQS1�@BM�RE�5�\n����ǆ�=�U)�D�H�m�puBJ\nH��8_w�q8�Y�\n��I�zZ�=��E?�O\'��栥M�M��2�w��H����jK�z�to/���N�#���Dה�[J�Y�U���msg)�Ifm��0�����\\��i���]q���R�S�x|C媛lr0�/�-��2��&�я�\Z���W�V�zT5����Yt�w7��˘�+�d��jI�Uk\n\0�罪k��� ��eqq�.L-�Ѥ ��O�>�\0I�E���Y�K����z.(nV�ҥ&��c�\0�㱣����kz�[��N%\r���܆���܅��mX�����ߞͭ��\"�2HS�H�\0�u�[���_��2�\0���/����!i)ZI���<06�.�\Z��D��Z��_���p��\Z��xh�C!�AҴ�^�c:�4}b@�R�\Zi�,�jTYC�\"���4��djE��)�nn,�$�{�_�����B�:�?��q([r6�IPk\\�FQ����-Ͱ�X���`#}F*B	v0��k{����J�q�\'�d��%C^-�Է\n��_��WU��2�7�yW}&���|�3�\'�H�O���̡��m�v���I\0�Jt�K�w�Q�lL9��L[O%�%��P�Ud,�\\�͍��sI-&FBC1[K�\"J��m2���x�^td���x�/\r�����5�FA�6��u��IA �@ր-��	��I��6$c E��ZX<�Eo\r��<mV[��\'�Œ�h�J��y�F��Q��RHv��J��t����F��zi�a������n�Lgx��.l�c�,N��#?�dGp+p(�������,1�:V��䟸lu&�Mu�!��i�d\"���čY�J������ᰭ@�eҕd��T�*B,R��T��|��G�Lx.Hy�2��h6��2n�,��d!�e\"kL�!��K��.uc�)4�=���e2/3���,��[��\n�[�W#���l��\Z��0`��\Z\06��>����KĹ���F���d0�(���ɡ��t(Ӣ�R�p�\'Q�Y8��%%Fqw~;B�7�^k��RT��:|�t���_R����/.��{����9���\0�c�M䙡�+���yX�l�s�F������iq*�Ez\r�<J��^\'���_�c��Jj#B�ж�K��j��l��B!�\nV�Qi����vxbg��C5�`��ض�)w��b�N�E]�J]�IGǡ*I	����J��kx&������խ�����f�K&w��S���`$�v-�:������ԫD�#y\'o8�*aM� ��-�����s�1{�_��v@�\'��Y�Kj��j�mp&�<��_?��(��d~�\r��eR���+��>\\_�N5���\0	��)h�+��G+Ļw�<��w%@AI�*\0�d{�ݹX���u�ڮ��\Z�!^g�@H����ǜL|�������u�\Z\0�u�A%��e���4���[�M��J�1\\?���k�f� \"��|&�\'qݱ�95$G�8wt$�T�\r#��ֹ�qH.�[-�č�}��ܟ*�ۆ~ˢE[r3 ƭ���u�{Q�x{HL��P�$��1�Eĺ��͍�\r�H�\0�=B�������t��\015}�֊�D(1��.��Q��uy�G�Ѩ]HBV%\'!��?J�P�j��K\"�J/1�8����29p~��o�U���38ۭhV��\'�D���I��a1���&�E6�δ�5�*�E�GM���3iy�$Y*Q \0Ms�c,���M	-ݿ|, x�V���3�tF��d))Q�Ez;v�͔��lt���V�8�,F(k�\ZEr��E:B6HFf�iN)�qBDe�|>$ד�֪�3��g�MS���d�Q�$8��=k�����I��Vұ�]��*[����%\"�-��BUc6�եk��B	��somUnR��TI!T�P��E��\n0����@3÷�JC�$�����$��f筀��;ؾڬ�X��>�D���k�����)Z���g���h�\0G?�A?����%bvˈ�A��[tS�\"�wD)�Xa�h�\0`D���j7ʙj�]�K�X��?��BEN<XUCWV�̺��rLL{_�`��Q}�;��U>��\0M�+ܛ\n�,Gt�F����N�]�$�kOW�bE��[���0��^�)�Dr�Jo�I>�N�.��O����R��0O��\0%�$�!��vs�) ����km�%�K�W��]��N�����\r�Ut$�����!$�6�jͫ����\r�£��+��xy�f�5y�Y��6UA���qI�l����l(Z�$�:%_	�k��m�,���\\E�����w	�nI~VWOƼ�RG��4�6#c�u\"��]7r+�N��� v�E�zl���U�DI��&t�-�By~D��}���\'H��w化��,~B�:�>Q\'�s��c�!_���q�AT8N	�X�)�����O)��#ZB��b��\0V�?��\n�ӫ�Gyv���1���E�\0����j�Y��y���aj�6��/6� �nІ��U�҅s�B�4�$y ^�G�]o�MR���\\s�l)����h��j�w5jt��u.�E�|	J=§A\Z�U5�_s��uJ�!ɍ�#���=��岁u,i6�QlG$������A3rQ�	��:��������+w��S2����+pw4˫����?�����O(�����p�������:��ާE������P�e�vY\'n�^-\r:���]������I�:�&o�M3|�O�=©��Դ�Dh�e[�l�8�\r����mI���^�ԋ�\\~u]$��:�\0����v�X��ܛ��d�#�PE	hh�m֬��\0�+k�o�2���5�m�җ�����h�pnu(:����6rTm�<.	����H/8��	�{y�ƋuU:��,Y&����7y�y�5lv��J��+��3���,%J��h\0V����E/u� �������ܥŉ��<gH=T�#���jyQ��\'� �g��Y�6��S��Wq�bfTn�8��\0\Z�L��rA��\'�8��rj4w����\"�I�\0Q���TǕ�:��\ZW`96�T���-B*Ld|�^�2��W\"�AR$��J����Y��\n�Į���pr\r)C�����ֈ��>s��g���*4i�:X��j��|X��<���_ݮJ\"�X�+iQ�)�J�\0e�+V��f�؜�t3��Y�<�`���.>�z��\"����bڮ-�����>����k�C�#�M�����бm��:nvt�\n�Ku�IZԯ��i^��l�E,���7\'���ƨ�S|�R��S����\07��H�4�\n�}EA^<�\0�=̊ �ƒ:�J�\"#[�^�k<]�O:������#��@�&\"-V!(J2���Cb�^Zj�!�C��O���jR�t��	ڊ�e�6�r����Z��{�%\n�(�J��K�\Z%E>�K����+�7�j(�HԨ}-	�!�y�ݭ&�YY�t!yRZd�W��ԫRy\"�	+�Z-�dZqC��K����r����BJ�!}��l��^�O�#��I]��,�m��ʏ�\0���+6.�AL�_(�p�(`y4��ޯ[kk���W&�ʿ�dd.�G�@����n+$EI�V������I�i�A�H�=�U.e�$~V֕��Uv��㉉�)�T��My[��#���,��Q@T��E\0j�F��d��cn�T�L�<j*���Z�RES)^tA_[x��B�� 0ǹ�ImW�\\K$o2`\\t$�]���9�Qu+�m]���$6�W4�T%\"����������y��5�(X�	ɚ�����x\Z<��:\n��f:G�\0#�O�j��Eu!��\'��9�i���S�������ԯM�w`�,�dt6KI?y&�[gŅH	����DCM�)�T��i��qaVW�w�J�jKQ|i �깫����F$�g��2ҜI�P�}ɰ�\"�H(�yǕ�թgĨ�Ƭ�i��Z���Q��JT�F�؝z�W;{�,�w]�2���#s�%(؎�W�]�B�5|���6��,:��y]�����z�������*���� �F����c$�\Z8�v��:N��Z�I�Uk�BUʒ�5L��G)�oY�P�rya-+_YoΈ����r�&,޸\r՛��r�P=���\0��zV���������*�S��YM8���A#�;\\I�5R,y\Z�\0��4�\n�meC%�im�t�t���d���ZJZ��\Z^�2(;L�:�ԝ!ђ~=���ŏ��&��؊*��0~Z�$��W�&��Pj]��EI����4=�_�HP��*A$���\r&���j�DP�˽����7Q�t����r�r�ڛ~��>��\r�������W�q�#+ßR<4�i@<paë\Zn\'�z�kwz�n3�������[�Q��:����{8�~��E����gy��\Z3�{ҽ2��\0R����`)[�����o�{��(�ގ-�B����}���w&ڃ2Ck?ΒG�\r^�C�RP�3�s�m��\0tfgs�c���uƒ	&�p���\0~5��^u^Ûw���}:g�*|hCI���(��5�\0@�jC�\'Am�&��}fYI��kZ~�?�!��2�2�H`�Ɩ���Zcvɦr���Z��(���W��ӳ5@\'KҀ��h 8P�*�yӦ�{i�E�6 �C�\'��`�l�*H�5���{si7�+�nF&���Mm��\Z��N��W�E��T�	�M�EB�~���P���T�EH�;�P��j&�͚�\"h=m})������up�5��u;;X�o��\0�ӵҺ&�Ζ���\'��oܱ#^p��WS�쯓n�3�]��g#�OO���m�b�P^���Y\'�_����m+Ŵ��C��R�j��ἧp��¨�IՖ�9ڔm�7�x��.�E�~M(K2���~JWx\r�yB�[L�ť\0�J_);tV���rV߿��羷��E�����W//��n�˷ʟL��\Z�z#��Pm��l���V��E�n獩�kN�����p��ꢕV*�k�\n���(�glb�=X蹴�Z�=���e.�u���->�6\r-�u��Ssc�+����Kaդ�]Uhҍ)�\\T��c�\0�y3�c� �4�t���������ܕ\r7(���	Q��ʕF����%TҮ]��<Q\r#���UXǝtLs\Z�@z�\\�[�V�\n/��|ZxR;r\\\rq�X�����X��n�~��Ö�����j\Z(O�~�HkS�↧��-\'q�\0�wO`�(��:��tW��\0e��\"�Q�U�^#W��\"�!Gv�w��~b����)?y���os�x�x7�J�Ŵ������ V����9}9��gi/��ϻ��p|sf7�KϿ�lq��	*6����]�����5*`�	�N���Z��j��L]xx�����PåJ\0njI�$�qI�\"��ĪH�q�0�z�Z�I!ˮ����E-�1-�M3�A�	�*�z�N��Y$�t4+��=C���Em��T�\"���\Z���o��M(4����*ؒ��k\\����J�f^<�I��}�����/�+�\rT��T�rz���\\��9Iռ�Җl���iF1TIU�\Z�lb1x�\'\Z^s�$�#)L}R�����\'��$�)�h��%�_��87�S�rR�E/~:p�b���{5�y�V_1\\��s&;�����P�U�m���o�_\Z����B���j����*��_�{�F��ؼ�W��ɭ,��\\�b�ĥ.8�������}\rǍ=�n�]�=>[��1���U�9S$��vx��n�>;5?*.B>9Y9b��71��B�$)�����u+qI4�W���w}qݕ�[N.ZUW�每���<�s�J�~uR�ł����BK��U�Cg�*[ϖՠW�M�ys�O��;���)[�rд��5F���9=҂��fK�9��#�S��O���8�.�$ z��U��\\�:XQ\'q`�\\�\0�y���R��c\\h�ѵ���,�o��.��u���7,��W�T�6���m�)h,�%;���S͒�v��؞���1���<�[�t��-�n���:���ܒ��/���y�d��\Z-��!i%I$�EQ5�̛;�NV�WRӃ�4QG�R������k�\0W̓{�4�3�q.2���ao(%\"�,�	Ё�X�K�Dm�	K�2j���*1a�{���|{�!(3�m�%�J�Ď�k�~��ǞP�q	A멩����{�E9S�XƘ�*�U�Wv,�մ)A?-�����f=)�w�$ⱊ(��O��?*��^�=l�&����ֵzC�!���\n\0\Z���@��{c�U�+f��������jFy\"E�.�mt�\"���RYe*U�U�B����i��k��1�l>A�T�E���%B�\r¦��	`P��*z�@�\n=!�!��¤��1�\Z�+�Z�V�BR�F\"��YN+�H�\0�^C�W��?��?��O�v�[��_�\n��\0�\r^�!/�rI��K���\"4�\0[�i���Ґ��aP�BMV�Yn�ŵ)��J�W/yA�,F�:CP\0���!_��>�~b�Erk���~bC�7N��iH^�#)�B��f��\nT]Y#jJRz%JH�SVUʅ)�{�q�yO�\"c�̉HTu�_BQ0�T�A%7!.-J��j~d�L���iңE�U���������Gq1��!��+O�!�ڏgl�}���s��J6�oL�I�t�W*��^����z�R�<�w6!f���y���d���9~��ڒ��HB�-+���i*G�WN����2V���:_��qyվ+��E��9��Nftf#J����&Ky̋a?P�b����\n�S��Mz��=���l�[�\\[kT��:(���p�xRQ�}��΍�q!@����J܌��J�0�,7��J;�k��2V����fs�Z����cL�r_gÉJ��.#�Ȍ�i����mAL�b�z��fņ��j�i\'E���^�zW#�����^�҇˸�\0���=)����}��\'�@�֍��sSˉ��t��ڻk�OT|W�9�Ti�;SjfK*(u��)*��%%U��k��nNT���\Zb�=@	�1#�^�	��!�`�]�/�[m2�\"iN+�	�%��\n[��-O���r���6�X1�U�k\'�v�ЪR�l�\0�\"��P��E�~ʱ!*;n����x��T�F$�Ћ^���$X���mYe\"�D�B܈�|�\n?i \n������]��}А\\���+��e�ys�&Չ_t��\n>BL{1��21����&�\r���� ���-+^��D\rkty�U�<}��]qR�nTr��.O��*�QQ�R�\\JS��(�\02���a=~H��	۟��/=��C\\��:�-��ZTtnt�lP/���U߱cn���H�WP�}Z�z�ͮ�\\��?��~��\0�|�7�@cc�vDx�\r��q�u��JEd��f�UE�]���f��ɹ`�rI��{-�H�ׁ��/*�-8יa�4�K�q\nZ���oC�U�6���iCO\\믧N�T�&�4��t��@�)�\'+�,k�H��A#����o����/�I��~����R^������}��\n#r+�OC��0�S����B��5V�W:��V�%��}7����R���:��i��ǔ4�s;\'��o�akl+�}�o��B�����[���n�v�`������8��g����\ZqH�-��u��@�t�	E��*U��n��rSQo\'�U�UH�\0z�=@/wq��?�l\0�-���-u2AI?b��W��7���?��Zm�oۺ�����fq�w���\0�\0\0ނGQ��}��Vh�\Z\'Z���T�w�]L�Hp�OZ�V䪊�P���E���PB�%�ơ6�i�HZ�ڄQ�MH�\":MB��BG�\nJ��t�laOPک��y���\"9D8�\rH ���G������ދ�,�����wqN��t���E�RB�PA�\\k�ב>�j<���s>$�&�lf�>��a�>���i@^���n��ҵܿ�4y�����U��9�K�[U����Ont��md=)��lj$sܶEH��c\n[U�U�y	�\0�\n��M�n7؏\r�+�;XG���L���j��{=stB�xM��\Za*#��*��KUٯ����*��̸�/���#��Gs��6fi��d�^G �BJ���KH\0��`t�+N����ޥ�����h�����ܮ.²ND�aYW���\\F�+RT�:���i�q^��ݖ��N��g���ʳ}��g�r����/�D@��T��$��R�\\�j�u�r1��F�[��[�,�1iWE\\�Խ��;ݞ��\\\n��9�73\n텵$P��(:��n���o/�\"�>G�zK]N��_m���d�y^��-_{q\\���4i޳m�Q\r��]JF�qM�H�O�v�?&ۍ�h?Z��\0����ٸ�*��+�>�3؇�̞\nI��d�ḯ2Å�mz�Ҏ���}goy^��)$��})y�\0�\0f�u;q_����\0�C������j�c���]ֽ�0-�@�\0�\0*Ѳ�R��\\�J5�\"#ȷ��ȭ��\n��X`>�/[�s�D���@ڛ�.��rX\0Z�Y4D���N��U�44�DP��4ڂ�����l�=HS%�&��-���&*[-���,��Yn�7\"�%T�V/N����G&���QJ�h�k�	�c�}������p��~��K�����%��W�ޱ���?��!8�W�)���2W�i���������\'�|�ַ?��\'�k�9Ϲ9D��\0&�2��\')%M����J�S\\��Uɿ�=��˷��+�����q��{�q�S������Miu�e���(�܀�z�k�JҒ���=+ks�w�r^gV��ɗ��w��n\\8w1Di��,<��\Zd2���\nԅ�]*B��}k�o\'rz\'�N_����ߟb�qj����R�\'َ�`9V\" FB��V=�$ �jfJ\0 �H�6�Y�[{�Ia���7�uN�r�ߝyj����{��x�\0�x���p�7�V�4���T۩I���R��V�Ũ�b�	*�����ޏvv�ە%O8�\r�?��}��È�Z6k0����%ñi�\\���\0�*�?JT��_W쬫qj���C���=�����*>T�j��~G!�\nsY>{���#+5m�4��\0��J���g�:u�okj/5�\n�PM�_���Tth\r��A�\Z�\"b2M��2��S�R�+W��P���Y��X�Sި��	{]_�Q+�x�-@��\00�@Z0��$yS��Y�^���|�AM{�Ԉ����u6�(�-��:�F��3��֙]#@���S+�iNl[�摠Pf����4\n�;��J��?����sD������l�j�!�,�eN��\n)�,�ֿ�/����~����p��\0��q\\����Wz87nxl�&hLFfD������0��҆��n\Z#[�ƺ�=�-A�Z��u��+���I,���\'?�Ƚ--�.C2S4��R�����\'C\\�`�=}�)���{�i�Gcfx�m;��B�2�B�\"HZ#�ܶ\\@!L�B\\�wj���\"�ݝ�[�y(��f|���y��?��)�,�|xTC�}���,��c�r��z,w\Z�#�ie�����8�G��:�mvN̵ͬ:ߨ����Y��o\Z��J��;�?p]��\0n��\\,,{�q3�J\\d��U݁v*ܫy�\\V[�;���0;[5{�t�k��JI�>	࿎�8�h�ݱ���0�\\l�����n���V7��~��s�veL�A�:ͽ��\'HJ/ׇn4��\'�Gg{7���O�O�����#w�;�!��ޚ-�]ͺ�}�r�>m��>�����6��o�6rK|G��1��l����J.:2�q�R�;�P7�y�nmV����o	�⤩�i>�z�_y;E��؞%��;2\n]�����ַ�[����Z�T�Sm��j3_��:�D�wS�fJ��\nN/��p^k���F����q�>(�c�	�\0Pս��\Z�s��Q�Vz����]�_�Y̝��\0bq^]��\0j��x�6�,���Skqm%k\r�T��T��\\��T�h��=��;;z��\Z�Q炧k93�w3���{E�p�\'�S�)�\0���j�W�{�Է7�vY���^�1�Lg��\0\"����	�J�mR��v�X�Wq���ȩ���P8r��:G�$Q/:*��y�*�U57%��数��M�*�+5,oE���$,~��u\r���5Ŭ�}�ޡ�l6���ʮM�Q�i�I�9�Y���I¸�雈���g��ꮝ?�����鹐�ѩ\r���I���8��:�z����v��Xk��{+9�M<�\0j� ��4��E�]����ۛ�\n2���Gul�������r�A�X�*J�U%f�s��y�9��������\0�j\"�ɼ�T,Z��c���:f2ct�\"ϊ��\ZK*(q�n�:P�N�{��r.2UO4nX�ݗp �n6K�ʺ��K�:�?R�j�rEt�Ԯ�F�<]�G�\'*�R�v�B�gy�t^�2Y����C8�IRZ/+�J֥�)JpN�:UW�S�r���tM������K�}�̙Ҽ�\"������Y>�ZT��J<$�AS���鿝t�˓�K�T��x����bSJ���9|�-\0a�t�:}xc��1����H����\0ةd�)�j�]٨.\'?�oc�����՗k��5��g�Y���m�Hi������,��j4�>\r�����ʽ���De��z�\0�Ҁ<(\0E2S\Z�,P��[��\n:9\n��(���\0B���}M@0]T���m�\0\"�MEI�@�� (,>��+�h%.�\"�\0>�\rе �\0���R���-������8Fs*��%��Q�?�z�=��gul����������y�j?�i�x-��\0+$�]���iط�ޡ�x�|�7�\0ڒ�;dc[v�TۊA?b��<�<^RgR׭�/��_�k��ݜ\Z���2|v�p�+$�EŔ�;�k�]��\n?ؕ��^%$k5Q������ ��yt���_ul���\\�n?��+?��V�|�e�W���R���\\[��sov4N/�o�uM��)+����I�a�����/�9O(��,�r^^U#�%�!(Yn	$�ӭS;�xI�i�k��[����7��>D5Tn3���R c��6K���h\0��U���NR�b���ϭ�\r[�i}���0_( W�>V�\0z��Q@��z�\rt�${\rVX�y��L�4!��)�����*\0�4\0o\n\005\"��M���M\0)�Π+N�m\0\"�PL�&�Y�$���\r+ ��W�i�CJQ�u� ��\0�Ȏ���[*鹵)\'�\"��K<G��X��0%���KY���?�k���Y紳,⎝�����ݟ��\Z�e�\\��s(�\0��Pڶ�I7?(�v𴚂�J7�Gq�jW���Qd�Y6Ү0\0zڠ�(ƀ=�\0���o�0�-~QK�\"��P�x\Z\0�@>4\0d����(($U4\0*��Ҡ�@�F�|h%ҒEN�MC �I��;#zQ�\r1\0T\0aҥ\0q@��%񠐧��`�PHS@��H?��',1,'',0);
INSERT INTO tblContent VALUES (7321,0,'example basket for the webEdition demo-shop',0,'',0);
INSERT INTO tblContent VALUES (7331,0,'127',0,'',0);
INSERT INTO tblContent VALUES (7332,0,'image/jpeg',0,'',0);
INSERT INTO tblContent VALUES (7333,0,'����\0JFIF\0\0H\0H\0\0���Photoshop 3.0\08BIM�\0\0\0\0\0x\0\0\0\0H\0H\0\0\0\0\r\Z����,6G{�\0\0\0\0H\0H\0\0\0\0\r\Z\0\0\0\0d\0\0\0\0\0�\0\'\0\0\0\0\0\0\0\0\0\0\0\0\0\0`\0�\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\08BIM�\0\0\0\0\0\0H\0\0\0\0\0H\0\0\0\08BIM\r\0\0\0\0\0\0\0\0x8BIM�\0\0\0\0\0\0\0\0\0\0\0\0\08BIM\n\0\0\0\0\0\0\08BIM\'\0\0\0\0\0\n\0\0\0\0\0\0\0\08BIM�\0\0\0\0\0H\0/ff\0\0lff\0\0\0\0\0\0\0/ff\0\0���\0\0\0\0\0\0\02\0\0\0\0Z\0\0\0\0\0\0\0\0\05\0\0\0\0-\0\0\0\0\0\0\0\08BIM�\0\0\0\0\0p\0\0�����������������������\0\0\0\0�����������������������\0\0\0\0�����������������������\0\0\0\0�����������������������\0\08BIM\0\0\0\0\0\0\0\0\0\0@\0\0@\0\0\0\08BIM\0\0\0\0\0\0\0\08BIM\0\0\0\0�\0\0\0\0\0\0p\0\0\0j\0\0P\0\0� \0\0�\0\0����\0JFIF\0\0H\0H\0\0��\0Adobe\0d�\0\0\0��\0�\0			\n\r\r\r��\0\0j\0p\"\0��\0\0��?\0\0\0\0\0\0\0\0\0\0	\n\0\0\0\0\0\0\0\0\0	\n\03\0!1AQa\"q�2���B#$R�b34r��C%�S���cs5���&D�TdE£t6�U�e���u��F\'���������������Vfv��������7GWgw��������\05\0!1AQaq\"2����B#�R��3$b�r��CScs4�%���&5��D�T�dEU6te����u��F���������������Vfv��������\'7GWgw�������\0\0\0?\0�	�b�%����\'��8�\0Ȩ�ࠦUQ�t���c���$��7�g�U^���f�k�\0��/��y4�An�զ�t�}-T��VM]<QU�e9N#!�$=A�2���=�e���C�_C8�O)j|\0e�A}`�����6m��B�&uG�z�?w�{�$�\'���*y��잟�{�&�ky?�4�J��\ZǸ{Z�w��\0����4�ڂ�t�_W.����Ұ��2�]-8��L+�+�{�]m���)?�^}M{��\0f��s�n��\0<[[Q�h��\0ԫ|�@\Z�o��:����\0ȯZ��]�V{62���w3+\"�K�j���UЉ��u���kp�\0���rm*�2�#�!?��\nﺗ����[������s�/��Xdf���}��_O����\0���~��T���V�϶��Z�M\"�����o��n�����z�DĊ\'���\nD�\0zZ�\'_\r?9��ނ�����@2<�E�\Z:��׈1��@�g���ښ��Uƻ\'�\\EC&ד٬�q���������}h��q���[���c���0��������l�������?�/%�0S�ƿe�,-�U�S=&����I�?F�S�\'��UveS������m�5����y�����\r�w����Y��=Us���FZ�W��������� ?�����_���>��:��ђ��t�Vlmo�v$W���i��o�����Z&wR���[��@���@s���T�V��}���Tʷ~�r�W:�xx�7�<6�~�j$\Z�Ȼ{h}V����~����?���4>�3���ُkme��3�G�v���ֿuw?�W�_����z���Y�\"2�G�c�^	N$JP���s��~&~GW�Ű=��2ƴ�׆��s�e���3{}�������`�3+!�dX\ZCK^Z���n��u8������b����>�ӯ�9׷�6�T�$W����ոB����9��I���Q�����\n������h--}���v���>����ҿ����G�0����_݌Vrg��X��$��c���>|oڽ;�������\0�]��u�J�v�M�����Em�xm�puo�sH--��9��ؼé��b���c~�p�vSl۹��36�7{�c)o��\0O�����C��L��	�73�\"�����$��R�z�D�#2Ө�������E������hp5Ք斏�p�-s���O����t7��<����k�.���g��W;�}Gc�.\'��P��G/�}F\no�쬂�j��܆������\0��rc>�eGHo�#����t2^C\ZΫ�F(���3����M>=�&���)I૶����G����GO��L�����\0}Pv����jW��ĸ�7C)����Y�\Z��׷�s�s��~��>�E���V~U���ϱ��G�K���9�n�ͼ��8Z+\'����c\\��c��X�Y�O���=���7�ſ��7#�1ߕi�6����~?;��~!<:�\0�#������f\r�f�ՙG���T:�}���f3��rUuUg��U�[r7�̷ԥ���Uϡ����N&-{����gu��\0M���ϰ�l��U�u�\08��˘����7m}_��U�p8u�k���3,�9����s=^��@4�\0��9x���D�/��%�#�o	�s���-5�k\\[�=[�o�I�\0�����-_���j�6)c2(���O�{�`6�g���[�����\0n~�:CO��b�H�J5�{WW�������b�S�9ճ&��[{�s*���K��Z�ȹpHv�\\_%��%�s���l����c��Y��Y�d�&�oqu.�u%��*��z�\0W���;�Y�Y���\0Mu��W�>�S�K��\Z�H��=��ާ�M�w��\01G�1��)p�~��\"-���R�1��*��3)K�g���\0쬌|\nq�r�ǽ�6�qݍ�W���vlw��\0���\\�����^�+���WY��k	���c�����=�\0��s��b�������h����,��?K�����ڧ��ޛ�V=[N-8�=��\\�G�q���\"������*�;`L\n�F&]�\0��\0�m�8�qJ@�FS�e�?�p�������~~E	ԧ��ߚ�v����f~\"t����E:p`Bp�4�~$Ե��.Ī�Upɥĉ���zn�������������c�*�z�>E��:��`�ֺ�ƹ��m/�6�l�Uk��\0��]n]��b��͆;P�o���r��g�O���o�%��e�\0�\rK}ncH ����&���辳�b��8�������YkXX��MO��{�eޓ��\0��\0�X_g�-.m6�V��Ǉ�j.C�`����7!�vW�����/�[=\n�[�����ݸ����~���2�$A&$������	������Z�&.kbdn\0�C�?D�O�V��{���[��7֫)���m��}V��Y����zV����7�G����!��#��_���w2�^���E��cAk��:�a��vO�v��uu;\'��w�EU:���w��s�K��Asϩc�\Z\Z�n{��[�Z�B���6ʬ{~��q3�}���=k�eX�:���\n�_g�{>�o��]�Sm��l������c[�۔X�Fs\"���o��6A(c�	q�w��V��K��O���`�+7]����\0�,����,m���K������\09�g�p�~E���T�v]KYa�\'�����4~��[�����G�:��\0�zut�;9�,dR-����nK�]����c��\r���ȷԮ���\05�9�\0��wcQ���b�+�k[���_N羋\\�7��_� ��ׄD�2-�@��?Fr�����\0�\'(:8��)5��B�S�>+%����g����\0:Pӷ����D��c����O���u{݄*��.�(�nԛے���N����z1���V_NKR���Q��M����d�6bY^K�e���6􌌫>�W����FM��������-R鹖����(|9���YU�ҍ��]έ���z��/W�}u]�W���\'��\0�X���g�E����C��X�_��w�6}�۱�f��*/����bo�q����� qG��_�0e$�ǆ\\\"�P�/���I�8ݏwS����;k�\'sm!ޯ�WVn6��#+��O����V]��[���z;xYz�=w��Ȳ�߶�m-�6����׻�}���}TY�[I]w}���k�w��为��w�������xv��\0;}����zu�����\nC\\�@��mA�\"�km��ewZ�[ml�e���C/3)e�F9N�ᔿ�>P��Ŀ>�rs��M��X�ncr�1��mV�Z��˪����힯��f7���G�:܌g�}��F�����^S]vE�bZ�7;\Z�f[�ܿ��\0��\0?�o����6<�/-ƭ�}(6�����I����7�ߤA�S�}�)��Kv[5�:��������\nL\\�H@�r��^<qY�2���/�T��WK���k����u����d9�݉MN~cm�EL�d�ǿ��_�\n�C��[W[�(��]����3����gK����7#�ߏ��.�O�����N�˸��ᅞ���5�;>�LO�߰�g�m�\0Zzi��gKx���ʐ���kf%u��b�Y�JfG��\"e��%�,%�1�:\n������*�mg�Y����ښ�o��?K�[���v.c�1[qz��`b�1�����z�X5�⾡�z����^ܠ@�P5~�S��{���vF�\0�?[���z�\0��6���*�����P���Ǳ����e��ſԻsXʬ�\0��\Z���`�c,u��u��7hn��Ү�����\0v�3c������ĺ1�N�:�ۿ\0�\0��D��\0���6��_����\Z��q�\0aL�Ev����}�Gc�\0y��Q�G�t����«�����{7��uw7e�w~���ߢ>�z~h\"���?��;J�����|�}ߗ���\0�Z؍�|ח.�G��uk��잧��<m���c��8VȪ�2_���+�/�?��=5U\"�D�]������P��c���u�����{����lk�W��+��V�X�|\\߳[�����=��m�b׃]���}W=卿춹՚���~F7٭�]���tڱ��U�Nѡ�胸�\'M�����-ӍBp#����X`L�.\"A%oG��k�c�ֿ�����nx��e�Ք�S}5�M��4���G�=|�r�쳧����~|��D��:x����Mןб����s��Z�����E���Swms�W��#��F�N��,�ُ-ǰ���s��\0_c86ս�?���LZ�n����t\r��1s���\0䑛ӳ���\0��\0�ZTt��:ƃ��J��G4}Ǐ���q?���-3��]�n�\0C�;��OX׺����5�ԩ�;[�o�M������k-��g���\07�a�\0�o�m�wP?���\0���П��TOt��(U��*��>�����\0�8m���q���o�nC��\0�YB(���q�[�)����\0��Z��\0Y�G��,�=\"��ӱx���\0��r ��i�.#b�Lf�󛍹ZH���$�l�*�]eC³��bgߘ�\0��{�\0�m�����1��	j�:��].>f*��8\0w�Y=�)�?4���K��w%D����	)��\08BIM\0\0\0\0\0\0\0\0\0\0��XICC_PROFILE\0\0\0HLino\0\0mntrRGB XYZ �\0\0	\0\01\0\0acspMSFT\0\0\0\0IEC sRGB\0\0\0\0\0\0\0\0\0\0\0\0\0\0��\0\0\0\0\0�-HP  \0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0cprt\0\0P\0\0\03desc\0\0�\0\0\0lwtpt\0\0�\0\0\0bkpt\0\0\0\0\0rXYZ\0\0\0\0\0gXYZ\0\0,\0\0\0bXYZ\0\0@\0\0\0dmnd\0\0T\0\0\0pdmdd\0\0�\0\0\0�vued\0\0L\0\0\0�view\0\0�\0\0\0$lumi\0\0�\0\0\0meas\0\0\0\0\0$tech\0\00\0\0\0rTRC\0\0<\0\0gTRC\0\0<\0\0bTRC\0\0<\0\0text\0\0\0\0Copyright (c) 1998 Hewlett-Packard Company\0\0desc\0\0\0\0\0\0\0sRGB IEC61966-2.1\0\0\0\0\0\0\0\0\0\0\0sRGB IEC61966-2.1\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0XYZ \0\0\0\0\0\0�Q\0\0\0\0�XYZ \0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0XYZ \0\0\0\0\0\0o�\0\08�\0\0�XYZ \0\0\0\0\0\0b�\0\0��\0\0�XYZ \0\0\0\0\0\0$�\0\0�\0\0��desc\0\0\0\0\0\0\0IEC http://www.iec.ch\0\0\0\0\0\0\0\0\0\0\0IEC http://www.iec.ch\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0desc\0\0\0\0\0\0\0.IEC 61966-2.1 Default RGB colour space - sRGB\0\0\0\0\0\0\0\0\0\0\0.IEC 61966-2.1 Default RGB colour space - sRGB\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0desc\0\0\0\0\0\0\0,Reference Viewing Condition in IEC61966-2.1\0\0\0\0\0\0\0\0\0\0\0,Reference Viewing Condition in IEC61966-2.1\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0view\0\0\0\0\0��\0_.\0�\0��\0\0\\�\0\0\0XYZ \0\0\0\0\0L	V\0P\0\0\0W�meas\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0�\0\0\0sig \0\0\0\0CRT curv\0\0\0\0\0\0\0\0\0\0\0\n\0\0\0\0\0#\0(\0-\02\07\0;\0@\0E\0J\0O\0T\0Y\0^\0c\0h\0m\0r\0w\0|\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\r%+28>ELRY`gnu|����������������&/8AKT]gqz������������\0!-8COZfr~���������� -;HUcq~���������\r+:IXgw��������\'7HYj{�������+=Oat�������2FZn�������		%	:	O	d	y	�	�	�	�	�	�\n\n\'\n=\nT\nj\n�\n�\n�\n�\n�\n�\"9Qi������*C\\u�����\r\r\r&\r@\rZ\rt\r�\r�\r�\r�\r�.Id����	%A^z����	&Ca~����1Om����&Ed����#Cc����\'Ij����4Vx���&Il����Ae����@e���� Ek���\Z\Z*\ZQ\Zw\Z�\Z�\Z�;c���*R{���Gp���@j���>i���  A l � � �!!H!u!�!�!�\"\'\"U\"�\"�\"�#\n#8#f#�#�#�$$M$|$�$�%	%8%h%�%�%�&\'&W&�&�&�\'\'I\'z\'�\'�(\r(?(q(�(�))8)k)�)�**5*h*�*�++6+i+�+�,,9,n,�,�--A-v-�-�..L.�.�.�/$/Z/�/�/�050l0�0�11J1�1�1�2*2c2�2�3\r3F33�3�4+4e4�4�55M5�5�5�676r6�6�7$7`7�7�88P8�8�99B99�9�:6:t:�:�;-;k;�;�<\'<e<�<�=\"=a=�=�> >`>�>�?!?a?�?�@#@d@�@�A)AjA�A�B0BrB�B�C:C}C�DDGD�D�EEUE�E�F\"FgF�F�G5G{G�HHKH�H�IIcI�I�J7J}J�KKSK�K�L*LrL�MMJM�M�N%NnN�O\0OIO�O�P\'PqP�QQPQ�Q�R1R|R�SS_S�S�TBT�T�U(UuU�VV\\V�V�WDW�W�X/X}X�Y\ZYiY�ZZVZ�Z�[E[�[�\\5\\�\\�]\']x]�^\Z^l^�__a_�``W`�`�aOa�a�bIb�b�cCc�c�d@d�d�e=e�e�f=f�f�g=g�g�h?h�h�iCi�i�jHj�j�kOk�k�lWl�mm`m�nnkn�ooxo�p+p�p�q:q�q�rKr�ss]s�ttpt�u(u�u�v>v�v�wVw�xxnx�y*y�y�zFz�{{c{�|!|�|�}A}�~~b~�#��G���\n�k�͂0����W�������G����r�ׇ;����i�Ή3�����d�ʋ0�����c�ʍ1�����f�Ώ6����n�֑?����z��M��� �����_�ɖ4���\n�u���L���$�����h�՛B��������d�Ҟ@��������i�ءG���&����v��V�ǥ8���\Z�����n��R�ĩ7�������u��\\�ЭD���-������\0�u��`�ֲK�³8���%�������y��h��Y�ѹJ�º;���.���!������\n�����z���p���g���_���X���Q���K���F���Aǿ�=ȼ�:ɹ�8ʷ�6˶�5̵�5͵�6ζ�7ϸ�9к�<Ѿ�?���D���I���N���U���\\���d���l���v��ۀ�܊�ݖ�ޢ�)߯�6��D���S���c���s����\r����2��F���[���p������(��@���X���r������4���P���m��������8���W���w����)���K���m����\0Adobe\0d@\0\0\0��\0�\0��\0\0\0�\0��\0\0���\0\0\0\0\0\0\0\0\0\0\0\0\0	\n\0\0\0\0\0\0\0\0\0\0\0\0	\0\n\0	u!\"\01A2#	QBa$3Rq�b�%C���&4r\n��5\'�S6��DTsEF7Gc(UVW\Z�����d�t��e�����)8f�u*9:HIJXYZghijvwxyz�������������������������������������������������������\0m!1\0\"AQ2aqB�#�R�b3	�$��Cr��4%�ScD�&5T6Ed\'\ns��Ft����UeuV7��������)\Z��������������(GWf8v��������gw��������HXhx��������9IYiy��������*:JZjz����������\0\0\0?\0���̢%�M��*�c��H���z�����r�$^�`��LP㇩�154_�֤g�����f�w� a2S,�a6yl�T�yC�I)ր�\nB�{�ޣ@�1���8dV�8�I������W�iO�B���2��p9<4��%��1����[�o�>Z5X7�W�N=;������\0\'�|�f���\rB��_NK��$�$�i��2���g ������D���\0��_���\'���?g�	H�i�Vt�dpάc�5�\0G�G�,�����øSO�����V�� �\0��^�s�i��|bh����%�}*���?���=��4���S�(���P*�I\"���_!J����b>8��ܲ#5�28�%�!��k�?O��sB|��i�<c����5��\0X\Z�x�ك&��\Z7����\"�<W 1\0���k Pzz~�yc�u�4�+��\\L�x�I��Z)��\refEF�\"x�_M��\r����Q���*�\0Uh:�\ZV�q���?��b���$,%���VW�C��?���\n�O��_g�4=l�@=?i��\nq��u�f���9{����6d�/� I��#�=�WT|�\0��������?�L~\\G���Wb��3��Į�E���\0y��yV���k\\�\0����ֲu���?�Ly�\0(�tC�T_\r�*���jHl@�2؂up@��B����O����k�Pp��??ϩH�TH_[hxʴe.���]ZP���?M�\0������^���\Z���\rG��_������H�d�dp�i!��T�I\'�k{��4Z�8\Zg�������̓�?�p����QY�,��p�G\ndTMB�\"��v����O~b�5��4���\0c�hs֫�(����X_��<���.��Q���\\�e���矯�8�ҟʟe�_ϫ�V��?�?g�8u�Ю�T���Nn�4���*�*��N��\"Fn,��.>��[FԢ����+�MQJ��k�˦ffId�2�\\x�7��L\\����ei,E�$�,=퉫\0+O����>�:�PW�?2>�=1�A�N�$h\ZjPdH�M0i)��,(����HQ�S�H�^�����\0�4�\0�g��p�W\\�Ւ	�?.�Vc�\0	��Z��/�V폑�_�;��6?�ݛ���������d�d3X���ܙ\r��3ԈN_t�j+�]�+ST`����[BΌ�\Z�h\0<\0�AJ���30@��=~g�����S��7/���\\�����c��Nlݪޣ��}��ò5���?_����ޟ��?�ּG�KT���\0W��7��I���-ž&aq`j�N��.��ppxmn��S��F��=��[���<�y�={�l�>���\'��ifd/��ӳԹ�a۟\"{bjdc}$A����z����_�t60qZ��?���V��:%���|.��+�=!�쏕m���o�;++�����\'���u����Y�:�7xʺ^�ȵbJ��4��2I�A(�Ǔ�ێD�.i���n{���h|\"�0޼�ԩb��Š�F�U�)@e�\05n6��r�)����6��X�x��e��1����C\"�b�!hC>��L_E��﴾_��å��\rӴ�_v�(ѝG�vl�>�Y9�[�7�\0�e<|�q������k�|U\\�?��ܷ�%�4_�=)XX)�����\nZ�/|�c�\"���\0����6i%���G[�\0�\0?\n�����2�+ ��m|���X�����ײv�k���Z\rü\"I$�[�>��le���z��_���*i�W�/�_��A�[�u�¨��c;�x��*vOr�+g7���茝\Z�n�V�sǺ�$�P����o��~�d\\П������\0l%o�!Ҵ�~�v��Pe���~l޳�3l���Wɔޝ����;G��,����V��H�)њG5og?+��-�;=�K%���r�E��Z��w驕���r�q�~杋���f�.�o�{(J��V���p#h��g 4��Mz>@�\0-��z���~B|w���Y�2���ޯ�]\'�ii�ۣ1M�۴rc�b�=���35qS�\'�,Q���1��(�J;:��������������^]�]k#�e1���!�&��Ɠ	-���\0��R�P��̏�\ZS�Tp>B����59�(ʬ�6����2\02:�P���\0�\'�M\0�?.���z�$3���ύi���u&Xu��/,E��1���<�=��8�������\Z������\0o��z�ѭ���WH��Mr�giu�1\Zg\0�L���G�������� ���t??#�d��3�O�~��~���y���*�#J�F�\r���ԏ����$�?<�����ζ��yS���\0�uS��T.��%���(��i­�	Y����>�kO�OC���\0.~ޜ�5��������GvJ���eMd���|�I ��/�pH�|Q��k�P8���\\��Xd�d�5,�x�~]\'��!����A�Q�^��Pn,=L��Ò��Q_T��e�QY_*WTc��1�VSм�\'x���Y_f/er�Wkw�:{T���B���	�����,�*̥�PW,��W�$`Z�N�5��z�(�3VBe�����#e3D�%2��)I\"eoO����Q%h\\D|ʚ�)�����][��ΦA���+�=8�p���ڌU��(`���SJ�Bl�.�HO~=�h�O�&�A��O,�5tʤW��������1���/�r&�R��>2(Cz��3�?ԟ�^���<�YH�\0��3�\0<۟����Qv�,m�?$(`O�wv����\0��>ގ��\\�����[����s�0�V��\r��\r�?��&���Ǯz��6��u�k}\r�����^���|��.�������~�|]���\0z��>���6�8�}�Ro��c��������\0/Pg���V^�\n�\\��/���\0:&�p#�Y���\Z�n���y�F�\\��9r��3r��ɨ���?����F�\\|�\0oZ �&��K!�m&��j�W�4�䲛�ԟ��=���I�Ma���||�Ҁ��<3���q��)]٘���)w�Z�S��,\nߕ?�{�I��Ϗ?�}�������\0������#�N��J��U�� �$�d�q��o��WO��p���U(:�8�������>}�Ү���3���h� �h��?,.B�?��=rʠc�J|���]U~F������1��^t4i������,��\"�?>�D��ވѲA��s�~T���ӣ!���1����*-_�h��T�Q��T��\"\r(��(�ܲ�ᵸ���\Z��/_����\nԏO?�>^^_��v�B�#�g�]뼻F��_xnn����r�����ޱ���l6Ss�]�w������j�\n4�z\\�ZH�h��)wB|�[�;�t���h�ݣ����73�T��2�\n�Q`;��\n�;ͦ7�~�>�m^��6�̻��6�w�n��a\Z4�\'��書Ƒ�*7ied�xi�O�����}��[���l�;}Vo���4t��+���1Q+W����۳n�v�ৡ��q�K%5.0#S���j�tt���7g��X���Z��R���h�V�\0\r̓�*IdvY(�-u��k^1s��<���}���;��C�?�X�Y��8\'����\Z�e��5p�$�ǹ_Nn\Z}�6?`�M5}&�������z�Jz,�^=϶���r���:,\\yL�]l�ԭ4i�3hUR�r�,�l��}�D	-��sEbȆ)JB��QB�MD��T�����ۜ[��˜��%��Y]#Ȫ�ȳۤ�$�\Z$bG�Y�5U�MF:J���~?�o����ټ���#3��f���n|�F�nm���K$Sb)3��\'��9!�x\"hđ��y���畹Z{����r�2a�$F�]��ѕ�b\Z�J/u9ѽ��ۜ��D����\\H�h�i!�Ȫ��\Z�\"4����\n�Rj(�?���?��\r���v�ټ6��.Z]����ci�{\n:]�S>.=���x��]�@&�!Y��2��M[2}�����/i9����R�޺��ȓ5�b�g���Rɤ�S\\�u֤��o�o�7�u�瓭���w$��������,�MU�t�\\b�1�H��_�E$Sa_�����\'i��Y�(s�nc�b��Rɐ����f�19盡��jr8ݧ������Y>�x�I��:�g�<��s�>��/�m�;���Kɢ6u��+L�SQ#J�*u�������\0���ͼ������\0˨jHU�VK�Tw�2Ґ�!,�eKP��\0*���򎟷vGy�7n���^����I���S+��n��ǌ��w%ڃ�\ZlGN�RRRI=<�%R�3������������^R�{}���H\ZV��X�\Z3)iQY�@��X\r&�\0ǿ�ￜ���͛_<�%���d��`HZT��,�X)�\"��:�g���7~��{/w�J�\\�R�f����`M,���&��Ve�1�XrY,^.l�lF:u�����*d�5�p�6��M�kڢ�(庸�y5hC+�r��I�Fm5Ҥ����òl���-��Egk,�ZL�,1��#<q�p�P<�����jE\r|x�v����@l����A�6�Wv���l���ۛ�1_��-f��c�{>�wUg�\r���O��82��E��Jj����UY���u._���z�����~��i�H�a�bVy��\"}\0�A�ml��&�����߲��yS��B�ӗwն�H�s5�Jʑ��+���I�Yc�+T���y�T�� Q�ꊄ��c?�c�d�#���!��3F��+�`c?,~����}��4��/��#�\\��X�y�\0K�����$k�a�	=��S��$#����d�;�Ϗ��r/�2��ǵlcQ��E4�vEr8�W��U&�)ȡͽ�v�K�w7B��d����\0Ң���5��P+@jz�����ge��,j�WIe���_����>]i\n�wЊO�1��|	��A+4��H\rnE���j7m3���O�����s�p����\0\0��:�<��]�[kX�B�Ƣꍏ���K0�\0^���h��\0&?ب���UH$i��_���ٖ��J��92FIPZ��sc{����Z\0I�w��k�\n�J��<���\0g_�Ӯ%G�G��)�MU$��I,���Z=(�?�6�����G|�\0<g�5�Y�s���<*R�gIz���G:�R�0���S@����\Z����X���{��Q�|�?h�L��.�X\0�/N?>���9xX`��ht�8�D�uJHP꿤Z��>�`R�p8W�����ק�T�~��+Q�p����넟mc�O1�����{�lm�ɴ��/a˼�sw��Vl����Q�m\n-Mw��|�5����y�AC�t���n�3s��ki���%Ie\r�[�2�2�\ZO��:�*�����B�pc��\0�Y�>�l�����W0E,0��n������Z��hZR�\\\Z!ugO�������>s��PS��ۧ>p�E�h2�1�DS��;/�*��2���$�\Z��m�j�t��f�D7�L��i<?Tl�Rt�WP¹\Z��SYr�1�������^m7v�<u�3�\Z\'�W������)Vc��NXܗ��\0���f��Dm<-Mv+�7��e��{����b�x�sP`���d�ן��)�J�Z����;���.��\0��~�˼߹]�I�v���ܪ�m$��4��/�l��F���V�n��W��g5�o�[k��M�m�zZH�*ۤ�ܤ	�!�8�8��F�3Ί�P�O�?���碷�;�$�\0Kx���8���Y�2��Y��+��F��;2�se%��u�;=��KWG�p��I\n��S�ѩ0G�>�{��sv�>�?v��\\��W2��x�D�Rѻ�x�\" ���1�%}���ڞP��\0� �lo�Ren�Ҹ���[_��I�3$s��P�41HPWW�\0��C�����nť���<����6\rV��b�����\\~#��q����\rK�*��\'5Rӯ�\0���.|��ӑwNN����#�7�����;?���ʇ]��J���\0�/�v>�{��{�i�b]��[�c���:G,�D.r�c�Y�me���VO�U��@wGU������޽O�6�m=�\0K�n<����p��=�jw�[w?YDk������I�e�h�F\\(��׶��ʼ�f��e�ܡ�ŷ$JRF����K\Z��Vx�IC $���>�{}�|��<�w2�l�6�=���b.�R�bI]#�+x�*>�#�B�Ψ�~���;%A��=�_\'砒�1��A�N��{�:i\n�j��m�����%�Ȭ����T_���y��y��\r���h��bj%f���,�\0hIVhʨ�j���7��^��n��n���$�2z[�1����D���0�G2;�Un_������sc7���l���]A�0���z��#nm}յkq��RaWGF���J�	��\Z�\\�4�[��v�u�{��g{���s��-@!�U�/������:_�׬���m�x������{��b���Rg��3�P�C��=�t�K��읧�;+\'���{n�^������|�������T����e���>��8�(`��0MI)��_�fݷ.V�m�,岾���5i<9�I�(�)FZ����FxCȶ\\������n���Xn)+�^<Q�C�t�k�\"p�*���F�+\0��7n��#{��{ڗ����M�O��?�\'�u�\"�ԙ-�3����E��b�	SGV��� �C2���=�#�������o�s[^\'$�+ƫ [�x�T0��RE@=u��Nk��z�����gv��m�܍�@˂��킺\Z2�\r4$8�w��au� �s�:\"����ȶ�q�a���o�O������#R�n�+vy\\�b<EqyJ�i�b�[W\Z���G�Q�_������x�>�|5�hb)w,eJ���vT�e����p#���4�����\0֢^A;t����ӽ�O*��23X��ο!Xf����\\G���RMu�j4��*Y�?K����$Xߏ��H�U��������Nnq)���\051��n���U�F�\r(Sj7H��\'�X~��è��\0��z�Pk@x��\0��=Gf�c�u������\'\0�m��:l?�{��(Z��Ο��_o^��i���i������ԭ��a�XdqB���e�,C#\'��(��\0�G�~9&�����5��x��T\0���}���|�EW<i,��\'�=��)m%��i{ƃ�(?�z*h@��A��z>=:�q�p�\n�~��?N�c\ZD��h��S$a�$i$����:@n~�����Y�� \Z���\0�8P@44��˟�?�||�#xA���d���.��.0��Lu�}�ߛ��$�u-=^�\r͸��*����R,���6>�#�?ݣ��vw���U�p�+K�y*C���Z�I�t�tP�]d����~.p��/k\"��cѶ{��� ��fd��(���˖�▣R��n;sn,=]=v\'p�մ�\r-n\'/����x�dji�*`����렭���gO��]D��YC,m�+��5������sCo�a�� ��7��{���і�4�`E<�qѯ�̕F���\r�Q��K�]s�7w�j�od��F嚻j�\n|&B ��LbU-\\7������)j������6�S�}�K���m2���$-3F^J�\"mZ��I\Z`\"��^��o�|��O2��d���X[^��]�В�u��%�<]k���Aq5�\"�_��Pd��i�UzJJ~��|.3-�KM��k�[3K5GPK��cb�è��\0^u{?�,��s��z4��^H�Pi\'����cRC;��N��߻�ϗ����o{T1�/�VKM��()��J�j5y���\0��~c�G���2�c��\0a��5j�SרN�\n�����w��<�-���C���dѲ،�:�Pf��a�sx���W���M\\����I�OԠ�#�-���ǹm~3��G��hѸ���p	b�pz�u͟Ԏs��g�H�-�g,R(t�9�`j��\'�j0�/��Qb:OsWg�c���\Z�M�����\Z\nJ�*��j�j����L P\0�W�O��C^��\\۷�es(��$*��0��-��\Z�	�d�4o�[���r���ߕ�*�V8�b+��Nф��*T��!|��/�2~%|x��7KO��/����d����G	���͓�R��qf1�w7MU]��բL�8�RZ)7������g�<�$q3Aw����Z-p���W�4�Q# �ci��������g��\0na�x�嶭����R`�J�ۆ�Y)RG�6R�E�u!�\rT�MD2I�MD2�J���1Ȍ9)Qc�6��\r\Z�eJЃ�Q�~]`M-����&��)*��}A\0����.����~��܋UIS�/g�-�P�ҏ>Ү��!D�D�I�j���cӨk�D(J����l�C���V`���O�q2���h�{�|��Ѓ��\0v^j��y���v�^�/l�� ���)Ww\" uN�@*AW�f?��Ǯ��3���-��vOV�GN�eN7-��xPI����oz�sPGWE����*,�c-Mz�SN��CJ�W���m|��>۴��wi�n7���,\'O��BCC�+D��d8+���$����o���7�n��,�y{l��F\n�=ʾ�j�m�ȪRo\nD�j4����K�������!N�@N�.������oǼ �4�������|W\'�_����Ա�V�\0\n12 dʐ��lx��Ϻ�hE|����_�u�µo����X�\'X��x�L��\0�\0\\�X�\"�\\\r�ϻh?\r\ri��Wҿ��í0�_���|�\0��_�լ���f�J�s�˘�\' 9y�E�wv\0����rK#�A�F��V�u��b�z��PS�����8���������Yce#Ɵ�e��y�o��s\0k��֟:��8�����l��\0U!��h�(X�zR������\n�\\�es������5J��c���׫�W\Zֿ���=$�\rP���=W62�����\0�Y,�E>S���g ���_��6�����q�Z*Z�f��Qb��\"9cL���vn}�e�Ny�ŒgeN^(���T[�M(�\'J\n� ���S�:�\0�f8c����\n�4/��ܪ�.�_�w�Mnt�N�v��K!�\0z��^�厍W����t?�M��wVB,n̢���qU͆�=��c2Us1I��\n���㳭����e!����ʷ\\��[�ӷ@_td0G�3(��KGRA ��u:}�������3d޷ۡ.Ռ���id��	5��m :�2��O@6Z��e���RZm�������֝��e�2�K�y������3�c�*����S\0���:��w����	I��[3���+�>���Ԏ�~�x�\"��8ch2z��}^J��Li�`���Q�9�(���\r<L�n)\'�틉����.fX�ZU��H���Δ�Jll/w��v�I\'�}D\"+3*Y�$*��I8���~��B���\ZǦ�TTYZ��\"�\n߽��Bԩ��L��|�cX�DKT�\n늦���9�!˵�fv�U�2ܕ�9`��k(jk����h���2���Ƿ�V��tc�T��a@��2��ēaBx�%�#]Q�mI�6�O`n��Ɇۛ���1����\\vvLt�Wob�3�6tR���E������z�ɩ�zjZ�č^Jx�Z�#�vج.ﭤ�����th�	�y\\I*�\0	\Z�\n�e�Wp��^��\r�ov{M�^�m[��J��{xa����dW-�M$�\Z�:�^(����\0^���@�sa����-�Ju���t*t��ܛ_��sp��j��ن�=�[�hi�e��WgWt�#�Q��!��l�EM\\��N$_3�[��\r�u���\0)�=�� M�]��eb��b}lX��-C����w�E��{�ț��nf��ݭ^d/*̆5�)��\ru�\\�j�Ͽ7W�~\"����\ZJ�Wd�����xW�7?��]����ܖ3��j�����<1r����+\"�_s�7��\\n�*�w���A�?��c,�4:e���	�Mj���r��m���햲	mwk�����@�AJ��+����R��i�ՆZhUY��%�P4�u��>I>�����T4��\0Q?erO��S�^$���o�R���jv���d���C�A�H$��x�݋�A_���>o�`��g�����h|���Ca{�\n�b�X)kh�\0x7��\\	_�}?�=)�V�O�W�ǯ��˯�֫Z��Z(��dQ\"�*�T��-�H�V�_��^9�LT�\Z�uPW�b�<1�+�p��Z��_��ׅq�<�\\�����J�*���X�\0v7���q�&�O���M:T-s��\0�q?��ë\0�\0?������M��Fʼ�24l�)�Ֆ6���\rk?�;*MH������F�l���\0�\Z�\0��M��w�t���5��Y|O��;�%<X��v�)�S�_`C�)�%QEE���?�����>1�I�Z*\Z|����}��`\'��l���\0<�\0�u��{p����*�l��|�H�@2Z�=�3�Bt�:<vG�JG��.�����cZؙ�*2q�l��\"W�3�H��_��R��muldI��W�X!~uZ�S�)�ŕͤ;��r�V$h�+i�e\0\",+V�A�`�S�����ɛ힉��m��+��kv�=i�NWo�5�����3;>�q���lE��d2T��))g�����p�؎~�ow��>\\��n.v���VSu�!�B�I0��ʱF�W`C3��OL~���{��I�s�#Agi�M{h�;�xn�$�\r�N�F���!HF�U-T׷�lu�n� 0�\0A��.��}x\rG�{WM�|�mT�T��i#���Y���њ#E3\Z�Le��ة_�^�t�x�Gg���)�|U���^	�knK־�I�H�@�ņZ����(x��9��ku7G*|���Ͳ����]�CA���q���Ը�ۘ���{��$��<x����ެ=-D��*e�Yc�sQr�.s\'7}� ����g�.Y�k�%���o�Jt,E�Y�%Dq1r��C�+���y�&�z�u�0�v\n�kgu��o�3,�$�h�X��VC5�X��R4��e�������?�}���Gp�ã�N��;��{Ot�de؝Oظ��u�������n��LdYfi+v�@*)��j|�\Z�7��ϸ�o2r��,cxܬ���\\�	��\r�*,����	�^�5�v�ryG������l��7��ȶ��T-Ej ly�U�T��e�����\0\0�{7y�J}��{�+�}�]-=F��/������;��ck�����3��z�fB�rx��Q\n5R�b���������V-/�n[u�f��a�;`�,�|Kt�����F!��\'��w/s�{�k9c{������y�}k= ���e{S!��D6�$3J$�C�yt���W�E��5�SR,Y�!����������b����_�����O�zt�ed�E\Z��C�\r�[if*\\�^��?��4@TS��u��EO�����x����\0�@�\0�\Z�d��.\0�t�/����\0-��5��y���8��׬J�L���w� ��bR��*~�F�M�7�{)$В��?�\Z�qX$W��hi�\\���+�8���\Z$q��]&]%��)�д��M��ߪ>d�\0*z|���>��V�S�/���u�X޾�JD��\0�w����}\\�?�Ϻj�G�q��?oW�����/�z�T�y�\0������>$�\0���?����O�m�\0+�\08�\0��:Ϯ{�\0x��9�\0m���\0�����=�7�?��6؛l��l/e���=���RBF{�r�wo��\\�X�\Z8�\0���E��b�����#OMW��a�O�����U�K;\ZjeT���Z��\\�k}|,���	{��!�ʜ�wA ��j(	@@jH�\"�[V�۫�e՘�-���g�)�tP̨�F���ee��{uu�?�_�sa���_ߺ����螱��߹��VS�b��F�������ۙ��נ�fpɋ�[�%&������b\nt��Y�uV,��`�6x/ov�JD3���*$̉���|W��1��@i\\��ۑ.ۙ����xvG��^����	���iV}^Hm�Sm�H�l%SY�~��ttN��͝�o�:�i��ٛ���n�ٻw����g����h&�ڹ�o���H�3�r~���=��^o��ϙ/��p��˫F{ki�tO4ڞ1k�L��!HMF�j*�������[G ����Z��e~�wwm25���5�K��-���JtԴ5-F���=u�����f�y�^��Yn��e66���[�\0/���c�m�D4{�fVo��6�\\x��#KDb������y��\r�k�m�ۘ%�w���;t���N!�f�V����h�	�z����/7�ϒ�r�\0��f嫾`��{� ���F�9�ѷ��Ỽ�\Z�d]\nT��\02M˷7��wf��Hzseg7�^ϴ��\0�mN&<&ܠ�c�X���J�id�Q�\n��\'�Tʫp�Sߛ�����n����H�6�\Z�oF�W`\'�5h@s�V�=dwݛrڷ���׹��\0)\r�`�o����\"(Sj�$�\Z�\'�!RS�w+�GT�I+ɭǑ�X3h�ơM�������\0��6�v����:�M)���N>oN�ʒ�S�K�G\Z�4��B�W��<܏t<u�?����~���kO/������j!rE�D�7�;X��,�����y�����N���_?���ׅ?��?����Ь�&Y��m\n�ş��<�[�?>V���l��=���\0��ّ�|�~޴��o\ZW?����JW�&�d]D3)$ �\Z�FQ�_\nǀG��5c��Mk�������P�*0��G��3�4Ծ*�r%+4IΆҬ��`lV*ĀM�@�s�l�+�����t�*W9�?/�~ފ�Ƭ�\0R|B���zn���;�ov^��l��3|L=Fc+�����Q���v����X�р�I,u4�E\\�B<����_w�s6ky�~�y���k;���(�H�p��	A+,���DEu֤b��>��������\\�ɻ��oa���\Z�Y��,���F�����\Z�PĽh�Y�\0l�v��AC?vu���ƶ�ٻ�u�w?Eg)�:���2}���f˶Wh�:��&��LZSU��Q�����߿ܧc�m���-�d��&y�6�;e�S\0K�/�Q ��Ht5��;w���M�s�vK^x��[]5�[�e���n�KJ��6��mK$�JH(4Ӯc�z�on����,=�����~�w�}�I�v���{�a塒l_bl�״;?�߻\"�8�Ő���k4rC(�xf�5;�<�����4����g��[���VdQ���I��H,�)�+��r��ß��v�٢�_�ܚB��~��f�2�HC���RT��线km���e��N|��\\[g���CbT�S�w`�ђ�Ub����{O�*+q�|����1����iC$��,y_�O�msgwiΗS[܉�9�Qc+���(nV@h2i�\"��ʹ����q��齼�����[{6�_��I�Ǹ��Kx�72a�*H������.S�Oo���\'Pl��o�<f���~��n��=o���9\"�:K���9J��α�Sc�W2�p\0�}�9JIa�AޞYU@{J�f\n�J�I$\0I�㨠wG;����{PD���T*)vb��\"�PjX������_w�m����g�������,N�����:g{P�}�Q5^)��Roڬ6_u�㥬�X㠣�,�t���ɮz��M����NM���u*���<�h�Q��22��	�4���n�r�?r{�owe,b��)+��MD�B�Y*u\0)S9�>i�T[���=o�X\r��s��vuG���[o\r�v�v�s[���fﭧ��Ԕ�xh2��zD�\'���5T�P^:����n��wt��mie���ۄ�H]��c��������V&6Ju0I\r����V�G�qm�;��ous˖�d.^�#KYm��eU��S�d��GtGnt��[ݹN��=�&���=׿�{-�{1A��gvN3g	���rv�`�?��6.�����yg���P�{��g�a�r��a�m���XCX�{�51�K�V��Im9@�w��\0��3{��[�5s�[��s3��g��:el(s���P]}����B��ؿ\r��[l�=�_�;�sn\\�Jw�u��������xϏ����6\n\r�Y�x�Z�q�Z���T�P�a�D�ʰ��q^����x�v}����%��0���,����1]U\nYCR��k�v�l���q�]���Lk�L��g�7�UT�0TU��(U]��:;nm�����ߎ���V7��񫪾5b1�*h�Oe�x�kgp]}�:����#�6�UTt{�!4x��AEO�u�椤��s�>�v�4����Vۻ^m2�.E�ȴ��Fr#y ��x\\h���\0���j}���6�字���i��hEͼ�62\\Y�\"\"��f�܏�\\#�,\"{�	c�eX��?���η�;�xt.��\r��2c�;�t���W%f\Zl�!��n\Zl�w�NCw��6�8(�H���d���=��	&�=��^�Cy�m��\n^��I1Τ�[�G-~�l��jQ�Oh�4j\'Y��Ou����ݭ�ޛ�bޭ,�`��XH���Kn��ݯ5���V	�~!5~���4���ٵ����iE#H��o`��p���V�g����3J��c�/���Ҧ�]�G�L�J�nh��N�Q���bG#�k��@V?��O��GZ� q�?���z�ק�Y<M�h�M�JAm@뾖[��_�uy��_g���z�3LW�Y�\0/_�ѫ�%q���r��K(�`�d��SC�s�p�7>�LP�c���Wׯ�	���S��5J��y<(��)	ꐑ��$�F nM��0uv�`�\0�����N��*G�<�\0�C��:j�S�[��S����hj�B��1k�x�JB�5��ϡ�}�\'ތL��	��<�����˧#WvH�5J��kJc��厭���xٽ��\r�O�;?��C������^nm�����[sֽ��vf��R��t�6>�\ZL �����,�:�٥i%_f��=�\0�/�K���|��Z�^2�tI�X.���#�|ݵsG\'[���n̛���b�f�̲�}L!�h�6��\"+Ы�Ct��hm���qן\"z���;ϡ���SmQ�����y|�߽����foM��[jQ./���sn�r�+�OOUQ�cZ2�Jr&���&Ѵsq	�[ܬp�$�+E]�.�i%ggcV�\Z���:����\rט���\0}�\'}���K�UC�0Wi�����!igdR�eYE*U�ꭶ}wv�@���O�7�\0ttf/.��]���U�:���������!���PK?[c{v�]>���bհ[�m��z�p�T��\0�6�?.��\00_��\0�V/�y���9�0H���]3�&��3\ZF�1��({S��n�˴X�ٖ>tے{��q����&��*�\Z�2�H�)1����9���&�\0��(;#gu�aϹ3����ޯ�m���ϒ��مm���=cO��4��dm����Y����Fʦ:��XR�dŧ�6�iy�m@E{�-���(#�\"�e�v�\0��f��<#���a�V{�_<{��1���q5N��m#���T�C���g��fv�+��:�����#rSCCO\'�\Z�U�b���Ñ�޶Q�s��\r��ۉ�X��fA#4�`cUBK8#@�)�1o�D\\��mm�m�_.�tbO\Z	�-�1��\\��*�dd���:ҫ��c�s��m�b1Sٹ}���쏎;+9�{{g|�����h�y������nN��$ٕ�\r4�4Y�C���EL�}\'�~�m�����ɉrw�v<�k8[Iv�cb\Z=��F�@}R+>���!�����w����d�L�[)��k��9�MQ�n��=�h�cpQ[� ����^�����}ۻ�ot��,�ro�����[S��.�n�������ml}�G�ܻ���x\Zj��ii��x`���%��kث��Ər��E���l���$�J6���d�姊&!��\n�KȪϕ���?m��fK��w��r�\n�Oު�\"]ĺkX���0H�\0�i������\05���z�\0������}��,~���`�*]�=\r��;���~��}���^)��������%U.0W�f��{��:ą����v���\0ӵۚdzg���s�#�4��鷵��!`�?!N�\0���r�7�ۋ�wog�7Wd�s�����s\Z��ԝ!��[	��o_vWdmݍѝu�1#���*w�١�d�]�Y���!G%\r6�����O̜�f��m3˸[ [s7��BB�L�Q�1d��e5�e�=r��\r�׶s\r����O�E%�U1�V6P��*��WKU��U�s�����\"��}���K�����~W|���N��O�c�z�5�������꽍׻?��[˯$�jj�G]��]�GAO-��)�*���p�3t��nn�ݭcO\Z�嵿�����;C���[�[�:WV��tH ғ$?�|��{@K��뽢�f1�Ciw�Mg�Z�3L������/���d���X�����������\r�ם�՝���?ە]Ϻ�?�:G���7��.��\r��}����cn�|�sE��ț�w��\\%D	*�1o���!��f�u�E}�m1뽚vb��#ɦ���YV�A�c�bgD������˿�۾�HX M,в)Dz�,S\'��R��$���W� ��+I��:�j}+bA��m��{�(	��\0W��R������:Y�T^3b�K�*4���H�=L,<�@���] 1�����׳���\0WQ���Q,��v,B����W[�:��t!#�,/ou����C��\0g��_�c�\\z�ҫ*��f\Zy]#[����`����\\��`YB����A��Nk���Σ��y|��>t�<y�_��R���%cԲ��(���1���]r/����P�����x���?�׉���ʟ���1��x��M$��Y�Y�)�S�6��,I\0p}ѝc�b��?�9������H\'�XҲ���E@ϗ�)�M��{l|w����D}���4�N\'w�v~z\Z�j�X�oY�6����=絥��]uFv�&O���Ƅ�#��a/9��v��v������r	\"J�	�5���\"-�RM:��\\��Z�^�u̼��\rq���n�Gx�G_X\'/\r���{�H@��#p<7i%\'��o�c�Yi���Ut����4}q��cQ��e�M��$��]D�_�-��y52�^�?���9��S�����$�,�\0(�o��t�W�亢&W�t{�I�X���:�����;L�l�o��sp�݉�\'ڈ~��Xy��oI?�v�G���>g�k���#���?��va�8�8\0>�.�y�!�&��/�{�+!\n�l�xm\\k2!Ԋ���iG��y���_t=�P���?�mH?��?�������/����G�7�0O�� �j�Q@[�_�+\\�h�gZ�sok���l#�^|��\0icn����I�^ڿ2O_���?���4�\0�3u����.��ߠ�G|ո�r�`1q���է�G�Қ��ta��?�t�}�ځ���>K�\0�g�\n���/qG���i��Ϻ;.���m[K��f}�9!i�s6��cZ/�m[�)�.�x���~ƀ������ɣ鏎�\0�[I�Y�\0���!�\0�}�����S^���Z��v���I��(����n�?�n?��St�\05���n�]��؟����m���a1[[�����6\"��Z*̗k䤤�l}|�)�F\'��H�u���}��7]�z�mݯ쮡���yT� �eMH�(e֣R�j�jyOn��� 7�so<r����`�Z��Q�1�J��\0&{�gm�����vV��8�݀��Wk�_p�p�\\`�O`bq�x������4�IɅ�P^/G�é\'����n�ܑRes� �E����7���[gmE�ۘ*l���ہS	��c���T�6��t��D��s@*A?3�S�\0+�\0��,�x$��@Mt��r�<�S3P~&c���J�5������vk�<�J	4&�?��\0W�z��\n�\0�����UPŎ�g6�ܯ�nޕ�����U<���/�z��Q�?����ΖkP��B5B�#�g\0��n7я6�\0_޽s��zq�\0%z�+_�g�<��Ӫ�k!�m:�����u�cU��\0A\'����@TU��+A���d_,ua�Z���\0��\0�Ϩ�.G��1�h�PX���^�n4�}/�Ǵk�|��O�x���3_Σ?��\0\'L�rP��L�/��؆�9\0�M±�Go����q��Wޞz��=3ю��\0%>r��xg�zc��[�̩�kzKa��q�9�n�ڻ~\r�>:�MƛzM��+�\0����9����Z�Ҭ�$������yW�?u[X�u�ݮ�j.g4���#��\\���\n��/���w��=��{�Idesj�?�-c7�I�9�I\"<��4�ω������ˮ���~��=흷��{�mlݯ�9}Ѽwjm�B��P6[q�,�>#�J�U.�L�N�(J��^A��s��{�㴿���|��۸=������fw7R`r���w�_�#�x���a�0�M�K�*wv��a0���婱���Qࠚ�š��Ң��^����N�߻������sK��{S�q���r�[�7n�ŽL��d�����7�UGU�����\0i���hyK~��\0n�=��W��Cxl��$���+ⵞ{�Q�h�i�/಴P�����:a��?x�ryC��ג�[�v�3bPܦ�.淼��v���]���s�����b�.���P��,\"֛�HvE4��\r�ܻS�aLN7y`�}��-�̼w���gth�Pnm��ҽ������ؕu�=v��Ö�#q$i `��$3�����a�X��+��i�����$��ۿ��zo����J����xb�ߤ���Ǽ#ۻd��+��u�gbW������u�\0���G��U�z]S%�o��\"ď�ǿu���{��[P�2;�I�����IG�xh�i�dH1uN�z�b���E�ܢ��/��d��F^�h�����HL%g�j<ƁIR��5��q�++w���������@�#���?g�k��l����b���\0X��\\����}#U������?��/�c��?�Һ���_���:\\\'\\vo���\0�\0u[Z���j�[���s���p���/�_�׼h��-���>���8t7_��lM�4����1kg`��\"�8�q����Q��~G��\0���\"NCwy�\0��>�}bN��PXG�v�*^����`�Ju�ۏ��hPH(������_<�i�����?��M/N�*�V�[h��V�� �s�a������\0�io��WUA�\'���<}+^�m��Ƅ�\0j��ϥ>,���W/�Ho��g�y�a��������O}����ܤ�?������=����\0����r��\0��({�]]���׺r�fr�s3��[{)_��w/��8�.��2x\\�!M��f1�)ꂿ�����,jH\"���t`%����y�%;��)��m]�S����穷��\r�ڛ+=���Sɔ�����]�����b增�����T=?�u����}��ym߽ٞ���3�2�����{�7��te�tX�^F�2��x*�n\'GG$���ii�~\ZZt�|;��oe.�o��G�;34K#�e�*�(^�D�8D�BЮ�b�n�(7����m�4TK����U���+!q�4�4&���)���&�����Wv�%�;���St�	��Ο=�{+|g�sl�v.�C�e�e��y	6�.MH��!���E��j�:4����12�e5� �*��jg�\ZZ:\Zl^7�/$�6*�*Z\n4+IAJ�\Zx����{��׺�����ߺ�B�G�����.a]Ѩ0۾C�� ���zJ凩c�\Z�~�~�{�\Z)4��j`\n�\0�uf�a���� zy+�GD]q�G��+r���#�M��\0m���&���c���R\".M?>8�s�oZg��6�N�8��V���I�w-o�SE�)�[�V����\"a�qR)���?��E���<��G��o]� �w�\0*�r�r�ս+��7������Ӏ?���*W�u\0Ci��Z���z�շ��T�1MqpH���\r�/\"׷���@h	���<>�(sO�xz~�O�M���b�	׎UE��~�_�⡨��ϯ���+����\0�_O����\0�C��>�ʮ�2��w�����������w��q���*1�������o<�\'CJ� �|���d�\0�G��9^��l^�����Mqa7\'5�X��-=Vס�{�J�ŋyi2�/9IS������7��I5�VT&��#����k�o�.^��ڞÜ�����%&[Ya�1���*5��p��j�L��Ŀb~���sV�k�ǹr�����1+�g7�S-��!�R�\\@^&\n��!\rFQ+��^T\'ľŠ�G�p�:�m*�iFkyRI�\0�~��h���]f)��(j�>@��<7�{�`yb���6��{����I��X0uY��`	>� cŔ~}h�ƼCS�Bv/�|խ#��������f�{�=*zo�����$#߄$�K��^ׄLO�:���[����o�O���O�[#�s%\0��0(�x#�w) x���ք�A>����@��@��\Z3�>WA\Z3\0�z-�k_�6k�bSk�0M���\n�MC�������L�yF�R�\'��ˎq��1�_����\0��[���^�۷��ƶ�q���PO�ݼ8�����|�t�FY����ϡ#���*Q�l�����d����%�>�7^�����{�����B�j��<}>����>������q�|~T�B;�7��T[��zl�}ݼε�G�p	�[et�\0�=�,��uz\Z�z�Oa_��\0��\0���t���T���4_��-,@mÿ;[=r?���o_��b�{�X\0��=I#?��o��L�\Z)��W�|�v��/�[�Um/�}7��E��E6dY\\��tzya\\�zl�B����P+!!��\nbZW�>_fq���B�4�_�����\Z��?U�\n8Z*\r��qѪ��fC�E�*�4*��\0}?�=�\\-4F��@��e�ÅE<4<jH������jlldA=\nBN���dV����R�o�\0�\0�S!]<\nT���jx\Zy��\Zw�8�����ԟ�8[x�v���P_�k�~>�O�>��޿?�m>|j>޷��\\���\0���.��ֺz�b�ȑ�.IS�Xf ��%�d�j\"�NO���Ӛ|�\0��\\d~}S��?�����t�\"KۆT\r̄�k�,lx�\0��cLׅ<�\0���=k\'M*��_�LU	v&\'@�H��O���\\�~��{hր)�>ϗ�9����_��:d��.�I \'�y�������,x���@h=}����z�����\0W�Uzg�yᥤU���\r��bĞ�b9�F\0���\0���ՑՉ�J���?�u��ko�D��I)���-�PK�?���(�UaZ�����˧�$\0k����1��i����}Ho8�nC���������8Ҙ��?�����ԝ\'���\0�ǯ����@��-Չ2�ߋ��r���tP���|���(	�O������=B���j艽�到�٤��m����������+ה��4?�����-%�Z�Sk��<){�{0%�����~��>�^����\0W�Z4���O�&�f?�6��$^�+zm��xR� ���Mz�~�*�\0���ZW���?.?h��c%�`$5�����[3����r=�ԅ�h�1�O�����\Z�T��\0Q������c�6����G$��g<i�}-�@��J��q���dz�y�����_φ~ΡKO�ӧ�F��M�:�f,�P?�}��@>�����C�:�2q���\0/���E4�V&�#��%�YE��I��o�����f}I����Į?���?N�I7�J{�QVhm���.u?���@<28y��/Μ8��\0�/�X�\0c����,w��ք������_��4�4����p�ϯcM+��?o��:��',1,'',0);
INSERT INTO tblContent VALUES (7352,0,'137',0,'',0);
INSERT INTO tblContent VALUES (7353,0,'GIF89a\Z\0\Z\0�\0\0���Y����\0\0\0,\0\0\0\0\Z\0\Z\0@b���*�lqZc�ˮJ̅!(R%�y�֠�jfn)�/�M�{��6\n��r�_��,1u�i+�\Z^��\Z��G��P�&S��6��9j�OZ2�o�Ѕ�P\0\0;',1,'',0);
INSERT INTO tblContent VALUES (7351,0,'no',0,'',0);
INSERT INTO tblContent VALUES (7387,0,'Additional licence of<strong> webEdition</strong> for one domain.<br>\n<br>',0,'on',0);
INSERT INTO tblContent VALUES (7388,0,'webEdition additional licence',0,'',0);
INSERT INTO tblContent VALUES (7408,0,'Additional licence of webEdition for five domains.',0,'',0);
INSERT INTO tblContent VALUES (7406,0,'599,00',0,'',0);
INSERT INTO tblContent VALUES (7430,152,'',0,'',0);
INSERT INTO tblContent VALUES (7428,0,'Additional licence of webEdition for twenty domains.',0,'',0);
INSERT INTO tblContent VALUES (7598,152,'',0,'',0);
INSERT INTO tblContent VALUES (7596,0,'The basic version of<strong> webEdition</strong> is for the administration of one domain. <br>\nThe target group is all small and medium-sized companies as well as\nprivate individuals who dont have any knowledge of HTML but still want\nto maintain their website dynamically.<br>\n<br>',0,'on',0);
INSERT INTO tblContent VALUES (7597,0,'webEdition Basis version',0,'',0);
INSERT INTO tblContent VALUES (7472,0,'<strong>webEdition TWENTY</strong>  is for the administration of 5 domains. <br>\nThe target group is medium-sized Internet companies who want to maintain their website dynamically. <br>\n<br>',0,'on',0);
INSERT INTO tblContent VALUES (7492,152,'',0,'',0);
INSERT INTO tblContent VALUES (7493,0,'webEdition TWENTY',0,'',0);
INSERT INTO tblContent VALUES (7494,0,'5',0,'',0);
INSERT INTO tblContent VALUES (7495,0,'webEdition TWENTY',0,'',0);
INSERT INTO tblContent VALUES (7496,0,' ',0,'',0);
INSERT INTO tblContent VALUES (7525,0,' ',0,'',0);
INSERT INTO tblContent VALUES (7528,0,'GIF89a$\0�\0\0���������\0\0\0!�\0\0\0\0\0,\0\0\0\0$\0\0焏������ڋ�N����H�扦�ʶ��`@��������\n�Ģ�ɖ̦�	�JU�����j������dT�N���;X����:������~�(8(�fx�����C����7IYiy����)��	\Z*:��iz������꺑\Z+;�Aj{���D����\n,�[l,�����|��\\6,=M�}�ż�ݝ�\rSM^n�!��~����n�.?�qn��@��������\n\0\0;',1,'',0);
INSERT INTO tblContent VALUES (7533,0,'image/gif',0,'',0);
INSERT INTO tblContent VALUES (7268,0,'0000001049148000',0,'000',0);
INSERT INTO tblContent VALUES (7006,0,'Time flies and time is running out under stress!',0,'',0);
INSERT INTO tblContent VALUES (7007,0,'Time flies and time is running out under stress!',0,'',0);
INSERT INTO tblContent VALUES (7018,0,' ',0,'',0);
INSERT INTO tblContent VALUES (7019,0,'Currently, our wind power stations produce about 7000 megawatt � this equals the same amount of energy that is produced by seven nuclear power stations.<br />\nWind energy contributes by 2,5% to the German generation of current. Lower Saxony is at the top of the generation of wind energy, over 2700 fan blowers are located there.<br />\n<br />\nLearn how wind energy might be used even more efficently in the future by reading the interview with Professor M�ller.<br />\n<br />\n',0,'on',0);
INSERT INTO tblContent VALUES (7062,0,'Sonax',0,'',0);
INSERT INTO tblContent VALUES (7073,0,'Comedy, Holidays',0,'',0);
INSERT INTO tblContent VALUES (7084,0,'CMS-Channel',0,'',0);
INSERT INTO tblContent VALUES (7148,0,'CMS-Systems conquer the world',0,'on',0);
INSERT INTO tblContent VALUES (7140,0,'webEdition to be reviewed today',0,'on',0);
INSERT INTO tblContent VALUES (7141,0,'News, <br>Weatherforecast',0,'',0);
INSERT INTO tblContent VALUES (7142,0,'0000001001682000',0,'000',0);
INSERT INTO tblContent VALUES (7143,0,'Interview with a CMS-Specialist',0,'on',0);
INSERT INTO tblContent VALUES (7144,0,'0000001001678400',0,'000',0);
INSERT INTO tblContent VALUES (7145,0,'CMS - Special',0,'',0);
INSERT INTO tblContent VALUES (7146,0,'CMS-Talk live',0,'',0);
INSERT INTO tblContent VALUES (7147,0,'0000001001674800',0,'000',0);
INSERT INTO tblContent VALUES (7139,0,'News from around the world',0,'on',0);
INSERT INTO tblContent VALUES (7185,0,'webEdition: Opt for webEdition now! With webEdition you will be able to keep the contents of your web site up to date any time.',0,'off',0);
INSERT INTO tblContent VALUES (7199,0,' ',0,'',0);
INSERT INTO tblContent VALUES (7240,0,'image/jpeg',0,'',0);
INSERT INTO tblContent VALUES (7237,0,'����\0JFIF\0\0\0d\0d\0\0��\0Ducky\0\0\0\0\0\0\0��XICC_PROFILE\0\0\0HLino\0\0mntrRGB XYZ �\0\0	\0\01\0\0acspMSFT\0\0\0\0IEC sRGB\0\0\0\0\0\0\0\0\0\0\0\0\0��\0\0\0\0\0�-HP  \0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0cprt\0\0P\0\0\03desc\0\0�\0\0\0lwtpt\0\0�\0\0\0bkpt\0\0\0\0\0rXYZ\0\0\0\0\0gXYZ\0\0,\0\0\0bXYZ\0\0@\0\0\0dmnd\0\0T\0\0\0pdmdd\0\0�\0\0\0�vued\0\0L\0\0\0�view\0\0�\0\0\0$lumi\0\0�\0\0\0meas\0\0\0\0\0$tech\0\00\0\0\0rTRC\0\0<\0\0gTRC\0\0<\0\0bTRC\0\0<\0\0text\0\0\0\0Copyright (c) 1998 Hewlett-Packard Company\0\0desc\0\0\0\0\0\0\0sRGB IEC61966-2.1\0\0\0\0\0\0\0\0\0\0\0sRGB IEC61966-2.1\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0XYZ \0\0\0\0\0\0�Q\0\0\0\0�XYZ \0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0XYZ \0\0\0\0\0\0o�\0\08�\0\0�XYZ \0\0\0\0\0\0b�\0\0��\0\0�XYZ \0\0\0\0\0\0$�\0\0�\0\0��desc\0\0\0\0\0\0\0IEC http://www.iec.ch\0\0\0\0\0\0\0\0\0\0\0IEC http://www.iec.ch\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0desc\0\0\0\0\0\0\0.IEC 61966-2.1 Default RGB colour space - sRGB\0\0\0\0\0\0\0\0\0\0\0.IEC 61966-2.1 Default RGB colour space - sRGB\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0desc\0\0\0\0\0\0\0,Reference Viewing Condition in IEC61966-2.1\0\0\0\0\0\0\0\0\0\0\0,Reference Viewing Condition in IEC61966-2.1\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0view\0\0\0\0\0��\0_.\0�\0��\0\0\\�\0\0\0XYZ \0\0\0\0\0L	V\0P\0\0\0W�meas\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0�\0\0\0sig \0\0\0\0CRT curv\0\0\0\0\0\0\0\0\0\0\0\n\0\0\0\0\0#\0(\0-\02\07\0;\0@\0E\0J\0O\0T\0Y\0^\0c\0h\0m\0r\0w\0|\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\r%+28>ELRY`gnu|����������������&/8AKT]gqz������������\0!-8COZfr~���������� -;HUcq~���������\r+:IXgw��������\'7HYj{�������+=Oat�������2FZn�������		%	:	O	d	y	�	�	�	�	�	�\n\n\'\n=\nT\nj\n�\n�\n�\n�\n�\n�\"9Qi������*C\\u�����\r\r\r&\r@\rZ\rt\r�\r�\r�\r�\r�.Id����	%A^z����	&Ca~����1Om����&Ed����#Cc����\'Ij����4Vx���&Il����Ae����@e���� Ek���\Z\Z*\ZQ\Zw\Z�\Z�\Z�;c���*R{���Gp���@j���>i���  A l � � �!!H!u!�!�!�\"\'\"U\"�\"�\"�#\n#8#f#�#�#�$$M$|$�$�%	%8%h%�%�%�&\'&W&�&�&�\'\'I\'z\'�\'�(\r(?(q(�(�))8)k)�)�**5*h*�*�++6+i+�+�,,9,n,�,�--A-v-�-�..L.�.�.�/$/Z/�/�/�050l0�0�11J1�1�1�2*2c2�2�3\r3F33�3�4+4e4�4�55M5�5�5�676r6�6�7$7`7�7�88P8�8�99B99�9�:6:t:�:�;-;k;�;�<\'<e<�<�=\"=a=�=�> >`>�>�?!?a?�?�@#@d@�@�A)AjA�A�B0BrB�B�C:C}C�DDGD�D�EEUE�E�F\"FgF�F�G5G{G�HHKH�H�IIcI�I�J7J}J�KKSK�K�L*LrL�MMJM�M�N%NnN�O\0OIO�O�P\'PqP�QQPQ�Q�R1R|R�SS_S�S�TBT�T�U(UuU�VV\\V�V�WDW�W�X/X}X�Y\ZYiY�ZZVZ�Z�[E[�[�\\5\\�\\�]\']x]�^\Z^l^�__a_�``W`�`�aOa�a�bIb�b�cCc�c�d@d�d�e=e�e�f=f�f�g=g�g�h?h�h�iCi�i�jHj�j�kOk�k�lWl�mm`m�nnkn�ooxo�p+p�p�q:q�q�rKr�ss]s�ttpt�u(u�u�v>v�v�wVw�xxnx�y*y�y�zFz�{{c{�|!|�|�}A}�~~b~�#��G���\n�k�͂0����W�������G����r�ׇ;����i�Ή3�����d�ʋ0�����c�ʍ1�����f�Ώ6����n�֑?����z��M��� �����_�ɖ4���\n�u���L���$�����h�՛B��������d�Ҟ@��������i�ءG���&����v��V�ǥ8���\Z�����n��R�ĩ7�������u��\\�ЭD���-������\0�u��`�ֲK�³8���%�������y��h��Y�ѹJ�º;���.���!������\n�����z���p���g���_���X���Q���K���F���Aǿ�=ȼ�:ɹ�8ʷ�6˶�5̵�5͵�6ζ�7ϸ�9к�<Ѿ�?���D���I���N���U���\\���d���l���v��ۀ�܊�ݖ�ޢ�)߯�6��D���S���c���s����\r����2��F���[���p������(��@���X���r������4���P���m��������8���W���w����)���K���m����\0Adobe\0d�\0\0\0��\0�\0\r\r\r\r\Z\Z\"\"###)*-*)#66;;66AAAAAAAAAAAAAAA, ,8(####(825---52==88==AAAAAAAAAAAAAAA��\0\0\"\0��\0�\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0!1AQaq\"2������BR#�br�C�3D���S$�4Td%\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0��\0\0\0?\0�Ӊ�LU�\Z�3/�\r$aL� `{h+��϶���*�c��mG��l�I�jb�\"�-2�x�\0�Q� ��~T�8N/@�ܯu%@��jvu18�8Q?-��(+��o�MXW[���9��쮿)�(��(c8Һ\0�`�5��f8T�@�4U�p����\rX<F���\Z�u`j.\"q���B$�h��ElI�:�*���\"�T�q��XX�2@^qBg#!��:���\r3$𢂞�|H������-�0��&�� �r��@�Ę�L�P�43�:���\"q��(#\"���[kzZ1+6(�]_E씃��Xv#��[.fH 4KVę�D��\\��񢮰����(�e�9�>�m���l���kfI��T\\+�a�����#:r�4@-ʓMI��0$T�#/}\"�����\Z��\Z8Y��Qd�x�I.4���\0\nP�*��Ę�!�)I cRh#<(d���i��\nh���n8�4	ƭ�l�l8�(��x\n��}b���*�����^�k�\0W��o�����	tK����%TFTT:^�],�Y�<��·:oV{�N�t�7\0�gK��+EzM+����֢\rsKn�\'�l�����\0I%G���\Z�ɶs� �em���$�#Qt>4o?�*�cIǍZ1I[A8P�T΂.�Rx��y�`}u^� ���Aj�}�w<��=��H:|ݔgPbq�3 �8�MO���F\'3Q}��8�ٳl(�0��P,$��4[cR��ܳ�FH�\Z��q��¬��Iʂ�zdPd��\\�ԝ�@�MB�^��Q����Vb��iǴq���G��\0�8����q�KAFT\n�@�i�1�R+��� �t�v��,L�l�fA�T��a����ԢT���iH� �;��N���#:3¹\r�J}�X݋M	z��X�ӝv �k+��Ž�0-��^\\�b��j��X�_s��\0^tɍ!�$\Z���/n6�p��f7@\n	\0�p�HMⷂ�H�e��L�nam�Q�!�y�TU�$iA��Q�ey�(77�c�~�Y�8�e�\nNU]�\0h�d��An��\'H6��rcKGh����I˅W��3�5w�n�`�f�x{*f�@1�T��h L�V۰�Bi��<*�ۍ0�#��D	�ʝ�N1\\A��Q5��+){h��(\"Q\Z����U��DԴ)� 4�R	���DU#:�\0E3L�� �A΀T�Φ� w�P�\nAh ��i	Q�)�(#QC-pb@��a��T��A5Q0f���E1x�\n	�Ԁ�#* �4����gv��C�S��բP	��Xo�_rl����������|�d��(l�G]��+��6��n1�J�Gmm�������ar�NKZ��yZ\ZN�Z\0id�fu���+��o]���訬}����m7W\Z�Q(�%^ܕ;pƅ�L��~\Z��}ss����P*WL�d��T�����1>���XE\Z��1ô��nR�.�{D�>z��Sn��!�1�\0����ܗvҠ�F$��(:΍���[�Iԏ��\0&�p�8�5���K���w���z�KQլ����[D�  �@��H\'A�U�-�	���-�	4��Ӧp4[�X�V\0��=�\Z���>4W&\neL1ʓ\"\'D6�L��j*;���8QVC��sAF���:������2ڨ��ē��;�����1�f�cUZ��x��H}*dus1����ڴ�N�����2�7n�Љ�UI���/��\Z�@^�?Q5獶|\\��+���T��^RA��(�X����W�[sEiZ�mn��\r��kƗ{�L�_s��5���7E����a�ρ���5�g���hmL&��M�[��@�\"p,;|(5�S*�ޫ��	9�\"Qq8�3��+�1��r���\'��	\n��|���A��l+@]����n�!�S�A����wW�h�l��Øx�>5���)��A�-�F~��J~��>�̕f�Xe�\Z��N�Ӧ���m���A��j���{��tm�n/�.n���3��c��^��o:�����jĐ	DQ�D������XB]��mg\0Z\'þ�\r��M�Ya+�v�*J�ne�W1�ϧ�[m��m�W,�}1]��_.ڳb�Rݯ2���ų鶧��qc�W1���]�K]eT��+�Vڥ���%�\n�\n PU,�`��Q=�ՆE�ʀ{}1i0�&$O\n)B<8\nclfp������{\r@�L���i]PHnꄦ�[�K��e\\�\Z�����}.O���n۟T�P��D�i���N-�Q�r���?������i�m��M]���A����F�{��ucn�_�Js��X�5�h��^mw�om����VUWm��8c\n��=�O붭����V��[�BTB���e��J�\"bT�3�]�t́3�P{�|(MҺyǒ=����m\\nS[l�	�kK��P\\[Lc^\ZxjG�ij�)�d{���6D!�ʆ��.���v��]�ŧ�\\����ʝ	%}J�λ+�\"�޹�}�P��%E��a���h7:g˝:�-���嫌p�@����Mg�>���6᯲��j�éc�K��Y��f\Z\\�B|�tt��F5��˛pn+UŻ Pr�iw#�\\����o�:D7�V�N�캋��˶\Z�!�u*��^��m����]ˁ�L�p���#k�[�@�p}>l=ƃooӆ��9�-��:���*�o�A[����i!SlA<�X���(	�����O��\'Bٵ���H����\'��>����f���ق�IU\'&�W.�fk۱x��u^P,5�N���[�Wz����k0�Ǎ������bc)\"���f�z��[��51�N˟x�fG�Dns�`\0þ�^.\'\r#��W�^�l���;��>\0Q�˽e�$w���V�i_+��{�R1{M͝��������IҸ���5#u����\"��Қ�M4�*cH�bh�MJj$�95iTI���NM@�!�@ԉ�����V�\0E�#�J�l���aY]Y\Z�ͥ�]n�a2\r�deA[���ww��sA�7�]��;L\Z�ۣ��![��\"#�ʲ�W�	�m��J��(_k�����}t����q�Q�\Z���4��إ��t��X�=��oZ\\�p�#+(�R������{�U�����ծM�cj��dr��>�����,Ų����{_�lV�t���Ϊ��!{�t�c�SGG��0O5\0{�40pqf39aJ0�J�\Z�n��5:3Z�%CF<F�z5��˶�*�A��++r�����Tǈʫ�f6��9>�A.�	����:`��:���.e\n{+\\�\0g��f4u��p����@2O\Z��ih4F�����v7~�Q���S۳����v1-<q���V����\0�d��w\"��n�\Z.ۓ^��>���w��w�b{������j\'�n�\0�l�ہ�\0Ens�&��b���؟�>)M�\0�n8���]q�J�7*\ZװPc�\0�7����nc����oo��\'�+T��j��(L�8�O��AC���\0F������/����^��Mkjs�l�\0\"��&�lN{{G�����\0ks�֛����絾<[��&��������N?����V�����\0�?U	����cml��7kb����-u��{/t�Q�y@,51�gW>Y��,o�ʇ��SA���k���g�m_u�7\n:�*Ň�c\0®m��g[��ظ��<�s,�\0v���ݿ�\0w�Y�og��\rP�j��K���7U��yw���7���޹�r�4V�n�bF�\\bso`��`Kgƺ��m��F�(Eb��0�¹��f�,�$F p�kh���E��5�~�R�Q��bꓡ�)�_2���4��A��H��P˓	�M{LCA��C�rݥа��4�t�\"��7K�oz����[����1Ù8�I�����v`�j��s��^2k�0S%�v�[����F�=��s΂R@�iM14TR9aLM4������������Ҏky��Q��Ό��t�C�Mb��`��v��2tf�v�+�h���n��AU����L����զ��;�7R��ĽWY�����3�\0\'^�%�?�\"��=F����m~!�v�����_�`�{����s�a��Q��j}o=U�+�b��8��u8\nrv>���Ӷ�w��+���i�!����E�R3oT����o[�+�3�ʣun�kv��c6�kwY�A� |�ݮ�f�.����;i:O�FA��z����tMV�D �H��+��r�����&��1� ��h;͢m�hưT,� FK�7�;mɴ��q�3�d{�Ѭ(�]��|���}UJݲ�����+�g�(�+گNZ��.�i�F��r��{Z�3~hu{(bZ����8�*c��d�[�6z��q�����U�VIbNp\'����kn��?����n+\0�e[�n-,����\0��n8խt��$\r���9���%칐Tj(�1�/Ԓ\"էc�ߖ?����xgO �=�A-�ݙf=I\\��,�21�ݗR�;u<�����}K�$�8v��\r���A���\rG@�C�Y�;�����Y>TЏ̽4Qε͍�E�,�;9�K��/��r��>������\00�7�;x0�ٙ��a�Om��r>4B�!J�\0��|���(6�];���\0���\0ު[~����䍲r�t�c9���̻Q����(9�f��=�1cn�<A5 -���Oo\n\"��N�\0�q�hr����\r>5�����6+ia9��9���`��V=�ww������\0ࢶv{1���J�-��,�������vg4�O�EN��9��O�O�u��Ga,��0���=�h*�J��}-�Յ�]=��}��ڲ����\n�Ɠ#�+��Nl\n0��h��=���vC�M���������\"?)�A(m�\r��Qo�����t�\0�h[��3�[�{�?\ns�6M��;\n�w�fʌ/���\0�Z�m���k��-�\'I�Ƭ7R؜�G���suh�����mf�,�\\, ����w��m�Ƶiu�W��+Ez������G��S^��`X�&Iq�}�h7:m�{[[������>CF�w ���g=F�_����������[D�%���p��vW���ؽ�,��J�F2x`(%�Z��V<��,>5�gˁ���<k����C��X��ι�k��\\\\�cMќp���Kp	�Y��oCj�q�+J������Ĺy\r��?�\0*�(5-��*{�6�h��l�d	ʈ��{�/�w\rc��h�:G�}P3��\Z��D�c�Z;��YWP&(:��R�pa���c^�����=�E~aj��s�~&�ш=�����w��曝s�7��TSc��YZ8�@7�c*A�*�-׆}���I��;C*���>��͞�\\�2�$��*�im�=���囂	#�M^^��X�zŋ���{}�����\r?��>�kY٭��02�8g�E[K�}��ۻ�p\r˭p�&¬-ۃ|f��ȍZ\'�j���M�1�5���T0O�YŘ�,L��e�o��%F$A���+�M��~	Z�逬�Mk����h+�vd`���t�-����s\n۷�gR9d0�(7n�q��PsWz�=\Z[�Gƨ��o-=���$WQr��覆.�<�\nb���m�*��1��.�P`Oq V&������r}��I�2�ln[�.	\\\\A�8Ҹ�V>Kd���	��A�1�=.��a�䶇E��~[y��q����ѯp(�<d\\���h�a�H�l~�@>�p�#\n��Z�@�ѐ �5�s���<�:���\'���OE]\'�@��Sܸ{�s\0�P��O�=�xm��V&Y�@� y����T�!#�4�o\Z��\'0�;�.C�\Z|(*j��w��m?�~��nKC����jiL�{��[HĦ��)?{�4�L�Ź��)�B�\Z��Ƭ��C�pOhE�+��P��mo\'\'��?U[D����bDS-��\rpxDM�g��PMbI˺��3\Z&��\'*(w��B�\r&q�hQ�\Z8���c\\NG0(�������@k��(�pљ�%¼��QM�+��\0\Z	��O�3\0I���\"J�Aj��@��A��\n���Igf\'�����G}U��8\n��7!O��L��j��SO\0D|h#����]@�[qtݼ�q��]�õ�\ZA\0�W2D<(\Z��*����ku�1����@a.N*;��2`��lc�����YY,;E�E��f\Zf��H&0{�(�-�^$�>�kS��\'v��h\'`�`�{˪�@8�O��2,a�ͩ�A�����m\\bZ;�aP6A��+x�F=�x8�`�`F�( @����!X��?��Χ=���\0$0Fq�8x�$���PT;rx��1�;U\'�|юUe�ݑ��C.���uWn�q�S��bt�<	��cH&r��hf��I#8\Z\r�*d��R&ݕg$�0 ��M1�+4��\0�{�$��ma��$�l\Z��%��n�\0Y\0Q���g�>ꬮ��(�&j\\�\0i+8A0g�����Xc�>�2+CV�pC Ր�/�]Y\0��OtvP�Pc�oi��@�����9�\'?�C�\0 ��W�@g�^$}��Ɩ�+9�P@`��p\"#.����h]��c�j,p&D�H�,fx�{�v��˫1�\'�����Il�#�i5��T��`P����[����B$��-Bs��o�R�5J�I�cp�o8���*����⡋L �{p��Pr�-5�l�&��/b�[qk�UQ���0cY}G`/#�XFA8��w\ZM���8��mZ#���$1��a!��{�m[���~�AO�<�U�d��������X�~�K�d8�ksd�w1!q�\"�l 8ɭ݅�m�1��$F����U-�`m�*�Uơ�V�MG]�J� ����@̑�O�\0ƞX�-���\n�T��Rݓ��*ˈP0� q�5�?�A��mW�UGF�@� �IŠ����K�	�ʂ�Zh\Z<�E@s�Տ��G�I��C�	9�i�Ƀ�h���D�K4��~�һ�XT����q��G=�ckbY�B�,��jVnm7�i z��j��#��y�3�\',M[��&n`�<8\n	E�p���~���˲F0I�OuVk�71��`d�=��[g�BYÊ�0��+%��	`8	�_\ndkv���9G��吣R1\"	9���ҁ��g01�b�M��H���`OmM/��Gh��5&�ui�:e�W��-�c���*���4��3\0�\0�����ڋ�]��^:�@\0�wFcK \'�s�VE��(�q�A\\+/%F0\r:�$�ZdGmC1~j�Y\Zg�1N�h���ňŁ절ۇm.Tc�Iϼq��.B9��MT[҄t�ɣ��A�U͵��ǳ��:�e�7�4�5>J�� ?�:b���A�\Z��jE�«���A�:\0>ߖ\0$��gO����W�+L�\"�Z΁���uN(-�B孳=,\ZA���7v�r��J|��q����n�Xۛ�v���q+���]��I�X����wW˪��ŀΫ��,,���	S����v�61p0s�&�w;[[��\\��`{@�f�����Đ4�>T�>��m*i\n��f��IP�nqdM\Zp�&�0)�J)��r���anR�0\"A8)Ζ�Y\0	9�bn�0�Q��2ƌ�AW���Tfr�����p\Z�癢-�x�_�QXXD$�3\nD�xЅ�`�\0*�#��p�D�-��*�uF\ZA��iY�=�4��b!�^̗݅\"�X1���0k��x� �dcC�c{�P��ld�A�V\"�e�d�����8}TF�[�c1�:H�E޴��V-��*�.j�1��t>>_�)1fU����bu���M�\Z#[1���̞1q��\0!�I0&{~�.�������\"��u��h[dlG����P\0e��O��cR�� )����~4j얍0�׆>\\p�{H��3�Pp��\Z�\'5�$�y��0\0��=�ny|�W LGuEE���\\	ó�Mmm!\Z�	�I�o\n�b�!��xɊ�a�D\'\\4��S7\\\r\'ςA�\'��jAQ`�1�s�S��3�Z�UVw�� w����#E��q�\ZI�\0DI�S��6ΐ�\'/hƦ]�x-���P��#^v0)\'T��)���ؖ�$Cw��[�r�6À�q$�#\n�>�mI�YH�#����Uq ����S�RZB�$e�eCkwa�id3�����,�J�eOv6J�H�.=z���%�6��:q%O��ò�i���q`���{f*:X�\0,�L��A��I�{��r�L�cːʱ���[���]P7\r��4�#Hh�	ʹ�K�t%�&�8(�h����EC�Hub�\0���j��՗\0�i8�9xVJ���hX�����j+�\n�j�Q�`0ō�|��pF#����/;0ein���6��?��*ox�,��`�KOm��\rF3��LIs$�T����U2I��s�;01L�\n��8O�w�\r�\n.+AC1�QG�\'Q��\\q��}N�!`\nI��57��r��j�a>�T\\�m����#�Z۶���a���l�y�1��R�.4��Հ�P��-sK|�\0eU���j�)\r�a�U�v�Dj\Z&\0��5II*���u�G���*5(ғ!A���5if��04.$�ƇqZB�WhbT����u;�e0ś�N3+�t�H���j��SH�=�LՕ�*hUb��\0o�Ҷl7��,�8�3#��������:��\r�#06\0��L�b����)��2G����:����d��;���%.j8�_ $i����;I�$fY&\'����&r��>zD�5��B[�������;�q\n�tdp��g\'8�kD[*�C714~�TU�1V��!�I�4��K�Yt�`��$L\0>�J���`�}(�N\n��K�N-+F(u]�${~4�T1[8�\n̀A��j��!Q�b�G���mCC1eB$���3����F�PXH$���G��DP��͕@,�¾ڧsu���R�Y�Us��b1���v[l����{*f�@�X;���Ƿ���:��mO����\r=ޭ�+>��#N��9km��MZp��v���X\04�HƳ/uf��`Z��8(�:�\\�K���q��q�E�Awe�l����Lp���zM�s��t�`��;��ԶZƤO0�&$Numl��y)�Zrb����{k\n\\?g� \r|t���8�ٷ����9c1���E-�c�\0���S�=�²*�W2Ė���A�s}l�Q1�A_�{\r���� 6!���L��3m`���i�F�l}�+Gl�͔!a�Fjx��*+�bY1�dN�����S�$+`-����w��/1��!��ÌvP����XczLRw�\n�v� [ z��D�;%�\r2q+ ꌨ�m,Z�\Z��D��\n�����z��g��\Z	��\"��l1@	��b��������cL�u@�bD�a�����o�2Y)\'�݅���癊��@��T���\Z܋D�3s\0`a�����<�XI��\r\r�8bA¢6����`A���F\"�����}Pt��?r�k_,iէA���\'�{)R���\\��8��\\�v9\Z/ի�NCV|#�)R��k�>\\���Y��O՝�鴾��Ƿ_���*	>�[j�^��38~�S^����ҥA+|�H�F3\Z�G�\Za��CO�SDN�?E*T�����Ǘ>���U�i:�V�̏��J���]:�\r��w�Y��O�w���\'�T�).�30c�xR����J����Z�NX���3>�ʾy<���>^\\i�3��iR�[�\\/;DH��Zc��L�F�1�:�j��)R�1��N�:\'�9���϶��4a:��������J�칐uiц�:�N3>Ȋ�������My�\Z��R�����]S:�O*4Ο.��>����9�H�h\Z\"#���3J��|�-�cJ�tG��N��B\'O\rY�}�=��PS:��t����=�L�5??=}<��8��R�@�Sr\'&�9~���=��\n������\0N}P}Q�ÿ��*��',1,'',0);
INSERT INTO tblContent VALUES (7236,0,'no',0,'',0);
INSERT INTO tblContent VALUES (7245,0,'image/jpeg',0,'',0);
INSERT INTO tblContent VALUES (7244,0,'165',0,'',0);
INSERT INTO tblContent VALUES (7264,0,'CMS',0,'',0);
INSERT INTO tblContent VALUES (7265,0,'webEdition Software GmbH',0,'',0);
INSERT INTO tblContent VALUES (7271,0,'image/jpeg',0,'',0);
INSERT INTO tblContent VALUES (7270,0,'183',0,'',0);
INSERT INTO tblContent VALUES (7288,0,'image/jpeg',0,'',0);
INSERT INTO tblContent VALUES (7285,0,'����\0JFIF\0\0\0d\0d\0\0��\0Ducky\0\0\0\0\0\0\0��XICC_PROFILE\0\0\0HLino\0\0mntrRGB XYZ �\0\0	\0\01\0\0acspMSFT\0\0\0\0IEC sRGB\0\0\0\0\0\0\0\0\0\0\0\0\0��\0\0\0\0\0�-HP  \0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0cprt\0\0P\0\0\03desc\0\0�\0\0\0lwtpt\0\0�\0\0\0bkpt\0\0\0\0\0rXYZ\0\0\0\0\0gXYZ\0\0,\0\0\0bXYZ\0\0@\0\0\0dmnd\0\0T\0\0\0pdmdd\0\0�\0\0\0�vued\0\0L\0\0\0�view\0\0�\0\0\0$lumi\0\0�\0\0\0meas\0\0\0\0\0$tech\0\00\0\0\0rTRC\0\0<\0\0gTRC\0\0<\0\0bTRC\0\0<\0\0text\0\0\0\0Copyright (c) 1998 Hewlett-Packard Company\0\0desc\0\0\0\0\0\0\0sRGB IEC61966-2.1\0\0\0\0\0\0\0\0\0\0\0sRGB IEC61966-2.1\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0XYZ \0\0\0\0\0\0�Q\0\0\0\0�XYZ \0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0XYZ \0\0\0\0\0\0o�\0\08�\0\0�XYZ \0\0\0\0\0\0b�\0\0��\0\0�XYZ \0\0\0\0\0\0$�\0\0�\0\0��desc\0\0\0\0\0\0\0IEC http://www.iec.ch\0\0\0\0\0\0\0\0\0\0\0IEC http://www.iec.ch\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0desc\0\0\0\0\0\0\0.IEC 61966-2.1 Default RGB colour space - sRGB\0\0\0\0\0\0\0\0\0\0\0.IEC 61966-2.1 Default RGB colour space - sRGB\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0desc\0\0\0\0\0\0\0,Reference Viewing Condition in IEC61966-2.1\0\0\0\0\0\0\0\0\0\0\0,Reference Viewing Condition in IEC61966-2.1\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0view\0\0\0\0\0��\0_.\0�\0��\0\0\\�\0\0\0XYZ \0\0\0\0\0L	V\0P\0\0\0W�meas\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0�\0\0\0sig \0\0\0\0CRT curv\0\0\0\0\0\0\0\0\0\0\0\n\0\0\0\0\0#\0(\0-\02\07\0;\0@\0E\0J\0O\0T\0Y\0^\0c\0h\0m\0r\0w\0|\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\0�\r%+28>ELRY`gnu|����������������&/8AKT]gqz������������\0!-8COZfr~���������� -;HUcq~���������\r+:IXgw��������\'7HYj{�������+=Oat�������2FZn�������		%	:	O	d	y	�	�	�	�	�	�\n\n\'\n=\nT\nj\n�\n�\n�\n�\n�\n�\"9Qi������*C\\u�����\r\r\r&\r@\rZ\rt\r�\r�\r�\r�\r�.Id����	%A^z����	&Ca~����1Om����&Ed����#Cc����\'Ij����4Vx���&Il����Ae����@e���� Ek���\Z\Z*\ZQ\Zw\Z�\Z�\Z�;c���*R{���Gp���@j���>i���  A l � � �!!H!u!�!�!�\"\'\"U\"�\"�\"�#\n#8#f#�#�#�$$M$|$�$�%	%8%h%�%�%�&\'&W&�&�&�\'\'I\'z\'�\'�(\r(?(q(�(�))8)k)�)�**5*h*�*�++6+i+�+�,,9,n,�,�--A-v-�-�..L.�.�.�/$/Z/�/�/�050l0�0�11J1�1�1�2*2c2�2�3\r3F33�3�4+4e4�4�55M5�5�5�676r6�6�7$7`7�7�88P8�8�99B99�9�:6:t:�:�;-;k;�;�<\'<e<�<�=\"=a=�=�> >`>�>�?!?a?�?�@#@d@�@�A)AjA�A�B0BrB�B�C:C}C�DDGD�D�EEUE�E�F\"FgF�F�G5G{G�HHKH�H�IIcI�I�J7J}J�KKSK�K�L*LrL�MMJM�M�N%NnN�O\0OIO�O�P\'PqP�QQPQ�Q�R1R|R�SS_S�S�TBT�T�U(UuU�VV\\V�V�WDW�W�X/X}X�Y\ZYiY�ZZVZ�Z�[E[�[�\\5\\�\\�]\']x]�^\Z^l^�__a_�``W`�`�aOa�a�bIb�b�cCc�c�d@d�d�e=e�e�f=f�f�g=g�g�h?h�h�iCi�i�jHj�j�kOk�k�lWl�mm`m�nnkn�ooxo�p+p�p�q:q�q�rKr�ss]s�ttpt�u(u�u�v>v�v�wVw�xxnx�y*y�y�zFz�{{c{�|!|�|�}A}�~~b~�#��G���\n�k�͂0����W�������G����r�ׇ;����i�Ή3�����d�ʋ0�����c�ʍ1�����f�Ώ6����n�֑?����z��M��� �����_�ɖ4���\n�u���L���$�����h�՛B��������d�Ҟ@��������i�ءG���&����v��V�ǥ8���\Z�����n��R�ĩ7�������u��\\�ЭD���-������\0�u��`�ֲK�³8���%�������y��h��Y�ѹJ�º;���.���!������\n�����z���p���g���_���X���Q���K���F���Aǿ�=ȼ�:ɹ�8ʷ�6˶�5̵�5͵�6ζ�7ϸ�9к�<Ѿ�?���D���I���N���U���\\���d���l���v��ۀ�܊�ݖ�ޢ�)߯�6��D���S���c���s����\r����2��F���[���p������(��@���X���r������4���P���m��������8���W���w����)���K���m����\0Adobe\0d�\0\0\0��\0�\0\r\r\r\r\Z\Z\"\"###)*-*)#66;;66AAAAAAAAAAAAAAA, ,8(####(825---52==88==AAAAAAAAAAAAAAA��\0/\0�\"\0��\0�\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0!1AQaq\"2��B����Rb#�r�3�C$��D%\0\0\0\0\0\0\0!1AQaq\"�2�BR��r#��\0\0\0?\03OM��jW�%Oz���D+�R��$P`��4ܩ�\n�%��K��T4��TWb�\n����KTMQPGsS�Y�Z��5N�#��רS�S\n5*zj\0jT��j\0jj���@��S�+PDR�-J�5��{`�Z,�^F�cķ�kF_�0�s�n��]Y��j�\0���j#�o�8�:��>�L+ϭ�G��jΨ \Z��~\Z6�0~��r�V�+3OU��S*�ji�C�!�L5���e��YƴċU%��U1�0��Qe��\Zt##J���b/ZR�ޕ\05+SҠ�+Sڕ\0FԭR��6��N�֠ڕJԨ�|Vj*�\\���;X�uZ��ѵ�5^CqM��j�@+���/8��6?q�:ʂDVUmB��B{F\Z���(,�\0M�-��4iK{�r�nۑ^��T�r\"�Q�@-�0�5*�	5�/;q�����֭�A2K�\nO�\\��ܱՉ�&��#�%�-�\Z���7��&� ����kTY���:L9�pw�_	��6��@׭q6�z��\\\"pfe\r�\rIV7?��A��!��o����i1Ш�,�_�����-�<���b�iI9^,��\r��:\ZU\")�]�ԩ�J�\0ԩ�V�\ZV���\0FԪV�A�N���X\n�}h̘�H�^5 l-T�vl�ZF\"Ee��}�iV�T0��%��/�Y^uD\0\n�TX\r\0�OJ�DjT��0jT�PSҧ�=ذ���R��^�[d��������U��G�a̿�O�}�©�RcO��1�q���qY,n/�W嶬��w�ked�8��4�w�H��u5�dO��L�\0�Q���A�Zxr�޺�,�!�0$��7��k&׃�S���ȅؘ��z_[T��W�Y\"T�>����؃B�(++���i�CpqF)ȋNK\'@�����[\"�5�~Wj�ם\rT�*j�,jT��@\rJ���0kR��@\rJ��h{/��U��۩�@�~ʥ�u޼��Z���ר���I.DQ�+Ʌ��(�.�m]8sO��^˛��ӔB�=*�9�ڕH+1⠒vSD�{j��\'E�����Ljl���ǎ�q_׀U*=�\\U!V\'���ce��a����\0ߏ�غ�+�ju\r������[jk�Pa�F�7��Z�U���U��.\0\0q���6u�[j\0\"�\0i�_?��ީ�T��6�xN�\n��-Z�(�_o\\�H�6��TJ,�y8sb�oPt��M=Qwr��(]1��u[��u���N*��Ow���(B�������6�z�F:F�YT\0��WW�(m�����)Us���\"��eO�n(I�Z���+�X�u2Z���E?�)�zUs \ZTmD�)Z�Jզ�*zT\0���o\Z���v����D\r�K��E{�$k��+�g���Ƣnv�|*��~:�������γ��>iI\0��x��C\ZPhj/OP�\nġ����r-�k���+��֓o�Yg���Ѝ�1���L>���[FW����[P�b�1v\0@\'�@�_ktN\'A1\Z���^�~��2^׳����EJ��\n�Z5�2HHP��_���\r3rbXn^�*��痐W��c��cz/��r@����斩4ʦ�x\\�9��\0В4����-\Z):��h�+��&#���}.���׫{���#2`�eB�� `o�WB_��kO���~BĂ4�zVVfv���G9�H�<��x��2ܝ?ٸrv�~e2����ڨ��v~�+���M���]_c��f��d�ҐJJ���\0�rA	6�Wo�_w�G�V(򩺨ߊ�\0\Z-����Ӷ�_�\r��a�[�u��[^�Q+���a��lh�L����%��q���)���^E���\\���D�|�Z�����\'m(�1qQ[{���R^�6\'�/J��O�\\@�\0ԦNE�(�\0����+7�|��vc�E\Z}�){��B�Ko}*P� r�8����*:��;7qe�!#�4W�vc�r2�uX����\'͍�!R?\rjI�Dצ枮�-����[iC����B�gs���+p�*�B+.\\��#�HB�J��������\0S���h�R>	.��K_������A�́_�^��:\n���őy\\�k��ի�ȫ��9J䴮��\0�k��F���/#	NZ8∉���ϭf�,�N���t�e��&�3�}#��a�0�\n�ܭ�_�:z��>9�����/n3,}�Mӓ4�jl��o��]�BQ�J����a��\\�i�1��ʰ�b�����E�\Z%�����uWc�ۜ�꼩q\"�s�P�&+�\0O�[̊�b���B��)��B��DГ�s[��T�Ӻ�q�&%���F�jSL`���c�-?u&K�	nWM����x�2[��Z�{m�]�e��\0���]�3�.�mv������G�䙙y ;!n����\r�1�XM�#��2mQ��CU`�g��a���̐�y.�p����<�<X�XǸߜ���?}_#<����u-�Y[��@Ꚃҷޘ��4E܀>���\n䀹���jE+?���n��ԅ���	=I(\0V�ګD��Z���\nU!J�F9��Ҏ2�P�>��;Dk(�Hɻ\'��i��\"�5��s�$W��T�VQɏ0P�S�\Z4I(�ұǋQ \Z��\Z؜�\"�A���0��@�t\r�����C��e6B��]+>C�E�!�+=zPU�t~D���֧�6���Ť�Wy�0��$����P�<�HN�#:��(�Nv._hy%\'揈m��ק�?c��\'�\\�-\'��<Uy�&�4��k䠄NOs��� O���Z�8�>�yE�Ix\'�/��GoQ.fa��x��?֥�N�\Z��]���0��QE���]�}�fj���\0AmQ�\r��rc]NFH�I��Ef\'�^�v8cʗ\'=����}l<u��r!���g\0�oj؝G���dCvs�4xP+=�iG+ir��77:\n�\nO��C��#A��	��\0��\0����S�N:����:|�`|��6DI\0F���nX�J�>5��j֬臢�e�,h�\Z��s,�zl��{����^ᒺ�(��Ό<�kS����B��\r��N��輁�R��F\r���Tݵ$�l�R47�����\r;���\0�n��쫣PrV}����\0q�Ո\Z7���[+\Z���Z����o�x9r��>L��K�j\\j݉�)j���Q�4zT�P�&D2�A`N�J���9��;��TS�jm5�MͯSӋ�2]���m��L��u���I��\roY_!�c��@��@tQ��~68���&��h7֪��o��<����\0�!\n!�\0�M/�ݿ\'&Irdol��Zğ]mF:�9��4	�v���\0���3Z�q�o/8�63��8��������x��v\'���\0�sv�o�6��k3b�2��2�\0��Y{|y��6Y�M��ܣ���j�r4C�/\\��_k96\r�������G�~ڗow��J�ǛFZ@w&f ��+����㤯~(I�-��h�OpF�J��,aQ��	v��V����m��;n׌1p!�7�����S�J����E��`�����<(�g���4��Y��\0Z��V���7�&D��$�kqnϠ��;b�t���J#vb���tPt���m��Kֲ;���h���0�UfA��+;�����u�-����v��117�f>VҪ�<����Vbm��_���r\"_P_��#Lޑ�3�\n�Ul�Qt��5����L��~�nBh�E�����j8�P4��z������h��@z�k���܈e[2B����	j�Xj��J�hV\0��I�i��6�t�N�Ő�Eˀo`�ͫln���Ie]�޷|�!&8�s��Tǀ|}�#$�������c�a���G��]���E�{���ֳ9�\\�(�`�Z��̪���ƴM.F�B	����)�rp`R,xG�^��R���¸�9�e�FWb�ǁ���Ϡ��ߨf�ox��@\n�x�ێ�\0<�H�H��ǩ�=�Fh\r+q�W�������G������[�Q��1	9F�q؋7��o�x�}�,�L��D��h��>�vF�g���[��<�n�B����Ԟ��Z�d`�#C�C�\Z;��t�$,���F�Fżk�,���2��!X�>Fǭg�#���� ��}>�L��B\n4\n6�֨t%#隣�����1�g�\r�[���*�ۘ[���-z����X�2����}Wj!\'i��G\Z�N\"�7��c�Ą�C�X�l&a����5�u;޶ag`�8�q��Z��,A)ԋ:ӊ����$�83^Y#>��j���_�!zk�4��s�MF��Mz�5�V��=�SL��<�[�4����-���u5nGnȀ�Iő���Ԋ�9d�(|��G�w����tf.\n�_��֥�Ƒ��I$UO[�^��S*.+/�$Lcu;�7�y��\r����U��1�|q(5<8�PCi��P��;���:�#�u�#����Rv-d�K{�2�fj��Y,(�|������x鯥F�wj��藺?Ã��������v_;��&KIb8G{����xm].Wk�)�	!S�ۗ$�\0����6?陡 ����D?W��SV�Mv��ǂH1a�HL=�w��.3jo�6��!���7��b�t*nt���U�}�3)�l�PW�3��\0x�lAܿOeɨ�9�TY[�_o:��W��J>�]�u����O~L\0\n���m��A����:^����I\\�*5S\r����G`��(�o�r�s$�;|�v�ӻ�nd3��]4;xV�e�t~���嚥?���Z���\0��6C��:��F��\0a��9b���lzԆ��WT?E�-�ː[�W�j?��Bߔ�o��@���R������,�+\"��Kh��w�I\n+��n�(d�݇V/�G�p���U�����B!� �1�\ri-nZ�3;�e���Z�ݺ�w$��}����D�rŞ2�^���y?;-��\n�u��ն��̋@�15\\�6<�#3���F���(��:}��L���y9�Z7^��*i�ﵩs�TBW�Q�*9�����桑<�q�ז��S�R�PjǭW#(ذSo*�ܻ�Ǜ,�\0׳�੷�SD�ܙX�fk�V%��#k��m�\ZŐAͅ�����>��D��1,x�O�Kz�-�t5Mޡ�:���E���T���2β�U *���� �BF�7���ۙ�����iY	\'[ܓ�)^:���q�Gm���Yz���ϵ~�ZտQ��\0�+\n�����c�D��c�]لKr�t��E[\Z��\\V� ��k�7�=B���R��\n�^�m�A�/�\\\ZX�XF�eRފ^��h���d��hA�}䝬�}u��2���(8��(;�ﭘ&<¹�/\'BV\"z[B��ª�����&����=ŀlH�Ƞ�&^#��X��F/�?���eP�n�������)!o}\r��pőb<���O�[jR~��AY�`\\�.��q�D�-�O�ط��G_\ZÒ�����ݞ�c\Z/V �H�h�\"YH	������TcQ&ND�a�$�MX�\0��eB�e#حu[��~��c6y]OPn���{��\0�`k�r��$b@4�\0߾�O�{NH�㢓��|m��\n���_�:ۋ0g�t�x��b�]�F�����Er@�N���b�,1�k���\'{s6����\0z�^6�dx�B��_Z��3M$����I[-��͑x@p]H5˜�0�.I�����Y��#�d[��ʶ�Z���ÏS�+����5��.�,,Z�Il���3�ǒ1�1�)���c�U=�u�W*e��qT]#���#�4�I��j��aԑ�@T���T�Ƞ,�6R4�*��:�	b-k��h/ƥ��!?$l̻��z-�X�@�Q�5E<w;SlcR��h~~Eu���\r1�RDi͚CbS�-�[�$q@n\\k}ko�L�,��\0�u�L���X%�@8P���3=x�MP����@<�������}(i\0?������?����X�3����t�x�����<cQ�X��\n\rfv0	c���Ǥ��x�V��;^4�ٌ-�C:�|��E���(^�)��Cw<�م([��X�.��6*5Ҋ��~��$X�p�QeFn\r�=�q���1��9�@���\0Q�Y���;B��v��U:4�ȝ���}��B�%�߉����ySc�@ZP��t��[W\'�E�ə>\ncW��5�殫.,����Q.,�4n�ob*v�u����B��Ô����\'J3q��B�$���sҝ���ƞ���X���,U�w���(�&c���-зA�Kl]��_���G̤h�|�TJ�.ܔ�\n\'f���|.����0�>�A��9��`�ć�_�����k�Q뚬��i��\0��O.?�6�K$I���\Z5[������MLj|:xPɊh#gx�}9�M+����V���N�&�>>��F�����G������?~�9R$hog����=%��R3���zSℵ���\0�%�³gθ��G r�\0E�t��G�&�x�j�1�d#�NŶ�4�Ew�n����}�E�H�l���4	f�\'�pcc��#Z720@���Fƶ�Tup��m�F#[��XȖ܉��ũR@�t�3�U��ڃΤ�Z���w>��#�p�[��b� +ܒ���*�NPk/.p�r�E��\n���[�S;ك�Ж,Ó��է��P�9�S�Q��:����~=Xo�T��b]=����5��	#��yV�^��L�^������p>��<�FRUWS�[QY�]L@�pXڶ�\\[�WdF۲�D�~9*��\0�M<��]�~x\" ���ٗ�xW@�I�过�O��;�Fw������aj}ЫFr��.e� 3\'�\n�\Z*PwC��R(�j���F�r$~��ڻnI�%��V��@5w~�W�Y#_t����}��}$�׬��O�3�NVl�*F��K����oҺ�����P^ކdD�\"F=�	�*z���)$@o��ֶ�TIF�R���؋�\0���C����ʫ��\Z��kץK��Bs���E��Sf��Z��we����J���X���ē�U����\'XUd�)�(.5_�!���k?	 �YKs�vPH\0U���e �1���Q����V�9��I�(�\")$���\0�e��z�/E)����)�d�����\rm�U?�uh�^_\'�b��\0,W��~������#e*m�㨵�=t�[��`�ǋ&>L��qҞd�\0w�.D��}I�`,kt9k\"�RY��z����p9��%V&J��#����:��A��ϝ*�2������`iK�9�d(�pj ����^y8�Ů����Q��D�_%,��:�I7�\0QN��r�2���ynESl/�V��7ۥv0KǷG�6���d�����_%��M�R�Q/��ԿʈOڧɒI��n.\rU�܆ ���ލ$5\n��g�����<O���Dy��G��&K\n�,O�t���^h7�­]�-Ya\'����s�\\N����L�u:\Z�w�3����r6RDH�d������*E$d6��_��v����S\'�5��E�;*<�W%�\\��@���2�N9�\'��=E�pb`muwQo\0mz�\'2#ɏ�+{y~����\Z7�7,\n�ą=	�~�ܭ���ƒ���\"5����?��+��ɐ��7}��������Nz}�Va�ΗX�do�ix����:���֬�%�����p\'r�����]�0�����ޱ�ٱ�\r�W��㸾�U��]��\"�����#���<m�o���\Z��ͬ>��nb�B�Z��:�}��C��{I���U���r���U��ƹ~熐���o�G��;0�\Z�z��Q4\r(��+\r�?2p+Rs�&@�(;)W\\d��V��ץ[��Ξ�i��Y�ek�P�N�~���,e��*��q\"�dd�H�G�r@kk����� r�_+��V�u���c�{ܳ/�)��>i@�!>嵴��!���>�v�K��A��I��s�P�5��2���/��l�\\d#��eY�Gˉ���jE�����%#fpǗ�o$���y�D�<�e�#{��V,	Bd�O���zT��$&���r��)�5z9K��Ȣ�Ԝ��-��]�k;�)8����[�tַ�$ay��;P��4y�y���z�ؗ$��q��¦F��X����G���n�6�\0+��w\'��C�M�ث)1���w��,�#�\"I�RqI?�J�V)gd��妞4�>&n^;���A�}e����1Gu�:x�������]��@ \\Ҵ_@|jH{\"��\0�����U�QO�ζ������2��(m�+e����	?��[T�6aɊ���������¡��޺@�31R\rYOCs��i�����R���vo�;�${��W��hp��@P,m�����]��g�g\'�oz��:�Es�jfF�A\'r?��c6Qpz-*!,������5��1$@��S�t�1���\'2���B�K7�Z�H�R��ȧp���j��p�4d�t�M�X�oZ�1������]�Wd��x���<�#���Eq�۶fv�$���犺��}7���\'��ʁ�#��m[�eU$�����\0�\Z�$\r�_h��\06�$�X��F��\0 �\n/�/�������~�@^`��0y_[Vv���9�s.+��7%����ƫ��O&��!��W9>6��\Z�q�F��u]��Z�?P�̕ᔌ�L/�0���\r��p��H������j�eC�n6QY��J��U��!F<�nj<ӂ�1W6�P������+:�?����P�2_��uѕ�?��\0\'Ow��AZpe�>4�$�n�1�Ή��y��1��_���Cf�Kֵ�bS����/[\">�<*+~	x��\r�S��@��[�n����Z�j`u#η�3��\0\Z�����l��qp>�\n鈰�G���1D���-�������\0\n�C�����4�@c֪�I\'��6�vo�<�^3a�[�BF�GtuE4vXm�QF�+��S㔳�ҕ\\�3�{.Ҭ4-�pa��E�a^����5۽�ygp��Q�]T�p*��ݻ�\Zb;��7���o�J��,�=+���+��k�2,h϶���\0��\0҉a~��W��o�NQV�j�}�;�����I�\\�Ho��R�g9C��7�Da�9m��j��ɑ�w��\Z�<b�b��0���81J	�̺ƚ3�z:��A�|��\"-j��f�Q	�_�{���:�oU´d�7(\r?��e<�w`5���\"��bͅ*���}�S�Z�}vW�\rzV<�Ue`��B��G�1UtMhI]ΠȲ�[�ku���5(�1�\0(�\'{�ܙ��\r�6.F���s�����v�V���YB4�6:~\Z�\r��G�0�2��P�=�sm�����;|dn��³�7�=�6\0��������̬<���������<Es\'�R��+TA�=Uc5�m���֞���QQm:��Kui,lX?w�����@w��\Z�e<�\Z�H��4@�k�iy�vX�%�8Dvч�\n��0�:|�A�rӥm���\0�d���;�U^/��5~8�!W�:�Rj���+���@�ʛx���[�^��^��\r��YȬ�wՏ��cw��anC���;V���		���X:?����mƗ�^f�{[q}MBa`��5�F�ok�S*jl�C~�E2eū2*�����a��Pu�@�QoJ���a�##qȯ����k�,u�x����8�Ղ����o���FQ������tň_]7��67��B@-����`~[��@�\Z�̶����%S���:R�W����?�֦�Ȉ��1\'z�m�~��ӭ�\r���8�u$_Z	�L�a\Z�]��kG^�e׈$\r�l+�r���p�A��h�Ǳ�X�Q7�\0�G��t\'PzkYq	=�0�D�\'�Zb�ǏJ�o�tG�ܚ���\'��H*�\"�|�\r-H��Z&�H�~�V9\n�B@t�A=F�uc�t�z�;�9r�E�\0QT�^&��\Zʁ���oE��\\9G%�G[P���<�<%�J�+z�R�����]6�d�]x��ԫ.X\n��\0���%�2lh��',1,'',0);
INSERT INTO tblContent VALUES (7284,0,'no',0,'',0);
INSERT INTO tblContent VALUES (7293,0,'image/jpeg',0,'',0);
INSERT INTO tblContent VALUES (7292,0,'165',0,'',0);
INSERT INTO tblContent VALUES (7301,0,'image/jpeg',0,'',0);
INSERT INTO tblContent VALUES (7300,0,'0',0,'',0);
INSERT INTO tblContent VALUES (7313,0,' ',0,'',0);
INSERT INTO tblContent VALUES (7330,0,'134',0,'',0);
INSERT INTO tblContent VALUES (7340,0,'user data',0,'',0);
INSERT INTO tblContent VALUES (7346,0,'customer login for the webEdition Demo-Shop',0,'',0);
INSERT INTO tblContent VALUES (7348,0,'cms,webEdition',0,'',0);
INSERT INTO tblContent VALUES (7350,0,'0',0,'',0);
INSERT INTO tblContent VALUES (7349,0,'0',0,'',0);
INSERT INTO tblContent VALUES (7357,0,'GIF89a\Z\0\Z\0�\0\0���Y����\0\0\0,\0\0\0\0\Z\0\Z\0@`���*�lqZc�ˮJ̅!(v^Fj\'�5��:�[�4[�m=�{��Q ĘP�x��$�[��#i\r��R��P	�=>�Y�\rm��ձHDy�]�\"�����\0\0;',1,'',0);
INSERT INTO tblContent VALUES (7385,0,'129,00',0,'',0);
INSERT INTO tblContent VALUES (7386,0,'webEdition additional licence',0,'',0);
INSERT INTO tblContent VALUES (7411,0,'five Additional licences of webEdition ',0,'',0);
INSERT INTO tblContent VALUES (7405,0,'Additional licences of<strong> webEdition</strong> for five domains.<br>\n<br>',0,'on',0);
INSERT INTO tblContent VALUES (7404,152,'',0,'',0);
INSERT INTO tblContent VALUES (7429,0,'cms,webEdition',0,'',0);
INSERT INTO tblContent VALUES (7426,0,'6',0,'',0);
INSERT INTO tblContent VALUES (7427,0,'webEdition twenty additional licences',0,'',0);
INSERT INTO tblContent VALUES (7595,0,'webEdition Basis version',0,'',0);
INSERT INTO tblContent VALUES (7476,0,'webEdition for five domains',0,'off',0);
INSERT INTO tblContent VALUES (7471,0,'3',0,'',0);
INSERT INTO tblContent VALUES (7470,0,' ',0,'',0);
INSERT INTO tblContent VALUES (7526,0,'Demo-Website for the CMS webEdition',0,'',0);
INSERT INTO tblContent VALUES (7527,0,'278',0,'',0);
INSERT INTO tblContent VALUES (7532,0,'GIF89a\0\0��\0���\0\0\0!�\0\0\0\0,\0\0\0\0\0\0\0�Q\0;',1,'',0);
INSERT INTO tblContent VALUES (7537,0,'GIF89a2\02\0��\0~�ۥ��=��p��n�5�����������>��}����������u����\"~�C��-��.����▹�t����P��I��������&��y��������f��m�����_��	n����a��������w�Z��v���ئ����3��(��\0b�]��s�S��q�o�E��m�A��+��}�\Z|�m�\0k�\0l����s�\0h����\0h�\0g�\0i�\0e�\0d�\0f�\0j�\0l�\0j�������\0f�b��z�\nr����������%��v��\Zz�������~�m����1�����\ns�p�2��v����y����������$����M��9�����k������֓�����������\0c�������\0d���Њ�����������s����������r�ع��Q��q���� }���՟����������o��r�ך����������m��3�����w�r��X�����\nr����*}����������g��{�����������v��y�ڞ�Ӌ�����l�ײ��l����q����\rt�|�p���������ں��\0e�}��t���Ҋ�����0��(��\'��l�o�������+�����l����������x� �K��������q��:��A��\0^�C�����t���㎾�j��O��\\��t��x�{�{����������r�ؤ��s��d��������:��>��8��:�¶�������5�Ǡ�Ӣ�Է�ע�厾����s��������^�������������\0m����!�\0\0�\0,\0\0\0\02\02\0\0�\0�	H��@-�N��\'��64��a!Ǡŋ�	 C��FF�SZ�ʊȗ0CZ��@�J9ð��S\n�6/**��hL,��˨S�P�(�c�՗�����אeज��Y\njb�q��h{���*�����6�ms�d	pۤ�n[``����g�J�1����*�����VAx萰�A\r\"G|�V�A��K�BW��,S�������Od�����\\�J܌�Я����	��dgTg������<vHn��T�,�1��˷��z�n|5R���H�D���\rʅ�^-���yP�)h7b�cHy\0$�R.]u��a�H�ÊJ���`��F�W1�םA	�DD%`W�6�q��`�J��D:f\\�K�tHUO-��ׅ�C�1y�u��r_X�3�\r\0�t�i\'�xl�]E��\"�U�5��KB$�袊J3\0v# 	�\'X��\\i�-<2ħ��\n*`�!���:N�\0����*�8H��\0\n�t��)�!��=S�?7�F�E�6Kΰy4ZQF,ìuf\0Ķ�v뭷����p	�I0E1�\r)שK\rA�+���;�,��AQ>���ug�RĊl����QIA���4�!�ňEm���`w��ga�\rRb�8c(a�0�t\n\n<,�ׁP�WDOJd����`w�/P>UD	��Jp0�생3	$QcL�=a�-��D6ѱ/LO�R �lAEhFD)�M+�T��R�����vZ�_���:���$�����J\r2�KE@�A�vLQ�|�+�O<Z�]4k`k>�L�?D�\0�\Z�[G2��z@\r\'��\"���qG2��^;F��S\Z���w8A���!5�,�Q@\0;',1,'',0);
INSERT INTO tblContent VALUES (7008,0,'Stress',0,'',0);
INSERT INTO tblContent VALUES (7000,0,'Nowadays, a lot of people are always busy and seem to be constantly under stress. They are caught in a vicious circle with dangerous side effects: When work is piling up, people get under stress; yet � when under stress, you will be unable to work efficently. Even the smallest additional problem seems to be insurmountable now and produces more stress.Although it is common knowledge that stress has a lot of negative effects, the dialy grind is often welcomed by people saying: �A little bit of stress brings out the best of me.� However, psychologists state that people under stress are unable to maintain an efficient time management and cannot handle the arising volume of work sufficently. Consequently, the most important factor to reduce stress is an efficent and sensible time management.',0,'on',0);
INSERT INTO tblContent VALUES (7024,0,'Get wind of the new energy!',0,'',0);
INSERT INTO tblContent VALUES (7017,0,'Get wind of the new energy ',0,'',0);
INSERT INTO tblContent VALUES (7034,0,'Very casual - Talai',0,'off',0);
INSERT INTO tblContent VALUES (7035,0,'Talai � the island of dreams is the latest quiet tip',0,'',0);
INSERT INTO tblContent VALUES (7047,0,'News, CMS, webEdition',0,'',0);
INSERT INTO tblContent VALUES (7050,0,'CMS-Channel News',0,'',0);
INSERT INTO tblContent VALUES (7058,0,'Sonax',0,'',0);
INSERT INTO tblContent VALUES (7064,138,'',0,'',0);
INSERT INTO tblContent VALUES (7072,133,'',0,'',0);
INSERT INTO tblContent VALUES (7077,0,'Boys\'n Girls',0,'',0);
INSERT INTO tblContent VALUES (7078,0,' ',0,'',0);
INSERT INTO tblContent VALUES (7085,0,'Demo-Website for the CMS webEdition',0,'',0);
INSERT INTO tblContent VALUES (7083,0,'cms,webEdition',0,'',0);
INSERT INTO tblContent VALUES (7086,0,' ',0,'',0);
INSERT INTO tblContent VALUES (7171,0,'Every evening a tasty tidbit to relax!',0,'on',0);
INSERT INTO tblContent VALUES (7138,0,'0000001001682900',0,'000',0);
INSERT INTO tblContent VALUES (7137,0,'Comparison of the best CMS - Systems',0,'on',0);
INSERT INTO tblContent VALUES (7136,0,'CMS-Systems',0,'',0);
INSERT INTO tblContent VALUES (7135,0,'0000001001685600',0,'000',0);
INSERT INTO tblContent VALUES (7134,0,'webEdition the price sensation among CMS-Systems',0,'on',0);
INSERT INTO tblContent VALUES (7133,0,'Bargains',0,'',0);
INSERT INTO tblContent VALUES (7132,0,'0000001001689200',0,'000',0);
INSERT INTO tblContent VALUES (7131,0,'0000001001692800',0,'000',0);
INSERT INTO tblContent VALUES (7172,0,'Useful hints & suggestions',0,'',0);
INSERT INTO tblContent VALUES (7173,0,'Demo-Website for the CMS webEdition',0,'',0);
INSERT INTO tblContent VALUES (7174,0,'cms,webEdition',0,'',0);
INSERT INTO tblContent VALUES (7182,0,'Demo-Website for the CMS webEdition',0,'',0);
INSERT INTO tblContent VALUES (7187,0,'CMS-Channel',0,'',0);
INSERT INTO tblContent VALUES (7188,0,' ',0,'',0);
INSERT INTO tblContent VALUES (7193,0,' ',0,'',0);
INSERT INTO tblContent VALUES (7198,0,'cms,webEdition',0,'',0);
INSERT INTO tblContent VALUES (7207,0,'no',0,'',0);
INSERT INTO tblContent VALUES (7219,0,'Drama about the daily problems of people who are different',0,'',0);
INSERT INTO tblContent VALUES (7215,0,'Withered Roses',0,'',0);
INSERT INTO tblContent VALUES (7220,0,' ',0,'',0);
INSERT INTO tblContent VALUES (7221,134,'',0,'',0);
INSERT INTO tblContent VALUES (7229,0,'Yearning for the Tirol',0,'',0);
INSERT INTO tblContent VALUES (7230,0,' ',0,'',0);
INSERT INTO tblContent VALUES (7241,0,'13571',0,'',0);
INSERT INTO tblContent VALUES (7242,0,'0',0,'',0);
INSERT INTO tblContent VALUES (7243,0,'0',0,'',0);
INSERT INTO tblContent VALUES (7249,0,'0',0,'',0);
INSERT INTO tblContent VALUES (7250,0,'250',0,'',0);
INSERT INTO tblContent VALUES (7251,0,'0',0,'',0);
INSERT INTO tblContent VALUES (7262,0,'webEdition Software GmbH',0,'',0);
INSERT INTO tblContent VALUES (7263,0,'webEdition Software GmbH',0,'',0);
INSERT INTO tblContent VALUES (7261,0,' ',0,'',0);
INSERT INTO tblContent VALUES (7269,137,'',0,'',0);
INSERT INTO tblContent VALUES (7275,0,'0',0,'',0);
INSERT INTO tblContent VALUES (7276,0,'250',0,'',0);
INSERT INTO tblContent VALUES (7277,0,'0',0,'',0);
INSERT INTO tblContent VALUES (7283,0,'no',0,'',0);
INSERT INTO tblContent VALUES (7289,0,'13484',0,'',0);
INSERT INTO tblContent VALUES (7290,0,'0',0,'',0);
INSERT INTO tblContent VALUES (7291,0,'0',0,'',0);
INSERT INTO tblContent VALUES (7297,0,'0',0,'',0);
INSERT INTO tblContent VALUES (7298,0,'250',0,'',0);
INSERT INTO tblContent VALUES (7299,0,'0',0,'',0);
INSERT INTO tblContent VALUES (7305,0,'no',0,'',0);
INSERT INTO tblContent VALUES (7306,0,'16256',0,'',0);
INSERT INTO tblContent VALUES (7307,0,'0',0,'',0);
INSERT INTO tblContent VALUES (7312,0,'List of all items from the webEdition demo-shop',0,'',0);
INSERT INTO tblContent VALUES (7315,0,'Shop-Channel - Item list',0,'',0);
INSERT INTO tblContent VALUES (7320,0,'webEdition, cms',0,'',0);
INSERT INTO tblContent VALUES (7323,0,'Basket',0,'',0);
INSERT INTO tblContent VALUES (7329,0,' ',0,'',0);
INSERT INTO tblContent VALUES (7334,0,'31135',0,'',0);
INSERT INTO tblContent VALUES (7342,0,'cms,webEdition',0,'',0);
INSERT INTO tblContent VALUES (7339,0,'user data of the webEdition demo shop',0,'',0);
INSERT INTO tblContent VALUES (7354,0,'26',0,'',0);
INSERT INTO tblContent VALUES (7355,0,'26',0,'',0);
INSERT INTO tblContent VALUES (7356,0,'image/gif',0,'',0);
INSERT INTO tblContent VALUES (7361,0,'135',0,'',0);
INSERT INTO tblContent VALUES (7362,0,'no',0,'',0);
INSERT INTO tblContent VALUES (7363,0,'0',0,'',0);
INSERT INTO tblContent VALUES (7364,0,'0',0,'',0);
INSERT INTO tblContent VALUES (7382,0,' ',0,'',0);
INSERT INTO tblContent VALUES (7381,0,'2',0,'',0);
INSERT INTO tblContent VALUES (7380,0,'Additional licence of webEdition ',0,'',0);
INSERT INTO tblContent VALUES (7390,152,'',0,'',0);
INSERT INTO tblContent VALUES (7403,0,'cms,webEdition',0,'',0);
INSERT INTO tblContent VALUES (7402,0,'4',0,'',0);
INSERT INTO tblContent VALUES (7412,0,'webEdition five additional licences',0,'',0);
INSERT INTO tblContent VALUES (7434,0,'webEdition twenty additional licences',0,'',0);
INSERT INTO tblContent VALUES (7425,0,'Additional licences of<strong> webEdition</strong> for twenty domains.<br>\n<br>',0,'on',0);
INSERT INTO tblContent VALUES (7424,0,'twenty Additional licences of webEdition ',0,'',0);
INSERT INTO tblContent VALUES (7594,0,'webEdition Basis version',0,'',0);
INSERT INTO tblContent VALUES (7593,0,'webEdition for one domain',0,'',0);
INSERT INTO tblContent VALUES (7592,0,'1',0,'',0);
INSERT INTO tblContent VALUES (7478,0,'webEdition FIVE',0,'',0);
INSERT INTO tblContent VALUES (7469,0,'webEdition FIVE',0,'',0);
INSERT INTO tblContent VALUES (7468,0,'599,00',0,'',0);
INSERT INTO tblContent VALUES (7490,0,'cms,webEdition',0,'',0);
INSERT INTO tblContent VALUES (7491,0,'webEdition for twenty domains',0,'',0);
INSERT INTO tblContent VALUES (7489,0,'1.299,00',0,'',0);
INSERT INTO tblContent VALUES (7509,0,'cms,webEdition',0,'',0);
INSERT INTO tblContent VALUES (7510,0,' ',0,'',0);
INSERT INTO tblContent VALUES (7511,0,'4299,00',0,'',0);
INSERT INTO tblContent VALUES (7512,0,'webEdition TWENTY with User module and Scheduler ',0,'',0);
INSERT INTO tblContent VALUES (7523,0,'cms,webEdition',0,'',0);
INSERT INTO tblContent VALUES (7530,0,'36',0,'',0);
INSERT INTO tblContent VALUES (7531,0,'258',0,'',0);
INSERT INTO tblContent VALUES (7534,0,'2',0,'',0);
INSERT INTO tblContent VALUES (7535,0,'2',0,'',0);
INSERT INTO tblContent VALUES (7536,0,'43',0,'',0);
INSERT INTO tblContent VALUES (7539,0,'50',0,'',0);
INSERT INTO tblContent VALUES (7540,0,'50',0,'',0);
INSERT INTO tblContent VALUES (7541,0,'1689',0,'',0);
INSERT INTO tblContent VALUES (7599,0,'199,00',0,'',0);
INSERT INTO tblContent VALUES (7600,0,'cms,webEdition',0,'',0);
INSERT INTO tblContent VALUES (7601,0,' ',0,'',0);
INSERT INTO tblContent VALUES (7602,0,'webEdition for one domain',0,'off',0);

#
# Table structure for table 'tblContentTypes'
#

CREATE TABLE tblContentTypes (
  OrderNr int(11) NOT NULL default '0',
  ContentType varchar(32) NOT NULL default '',
  Extension varchar(128) NOT NULL default '',
  DefaultCode text NOT NULL,
  IconID int(11) NOT NULL default '0',
  Template tinyint(4) NOT NULL default '0',
  File tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (ContentType)
) TYPE=MyISAM;

#
# Dumping data for table 'tblContentTypes'
#

INSERT INTO tblContentTypes VALUES (10,'text/html','.html,.htm,.shtm,.shtml,.stm,.php,.jsp,.asp,.pl,.cgi','<html>\r\n        <head>\r\n                <title></title>\r\n        </head>\r\n        <body>\r\n        </body>\r\n</html>',17,0,1);
INSERT INTO tblContentTypes VALUES (0,'text/webedition','.html,.htm,.shtm,.shtml,.stm,.php,.jsp,.asp,.pl,.cgi','',16,0,1);
INSERT INTO tblContentTypes VALUES (50,'image/*','.gif,.jpg,.jpeg,.png','',7,0,1);
INSERT INTO tblContentTypes VALUES (0,'text/weTmpl','.tmpl','<html>\r\n	<head>\r\n		<we:title>webEdition Default-Vorlage</we:title>\r\n		<we:description>Diese Vorlage ist einfaches Beispiel einer News-Seite</we:description>\r\n		<we:keywords>webEdition, cms, redaktionssystem</we:keywords>\r\n	</head>\r\n	<body>\r\n		<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"400\">\r\n			<tr>\r\n				<td><p><font face=\"verdana\" size=\"2\"><we:input type=\"date\" name=\"Date\" format=\"d.m.Y\" /></font></b></p>\r\n				<p><b><font face=\"verdana\" size=\"2\"><we:input type=\"text\" name=\"Headline\" size=\"60\"/></font></b></p>\r\n				<p><we:ifNotEmpty match=\"Image\"><we:img name=\"Image\"/><we:ifEditmode><br><br></we:ifEditmode></we:ifNotEmpty><we:textarea name=\"Content\" cols=\"80\" rows=\"40\" autobr=\"on\" dhtmledit=\"on\" showMenues=\"on\"/></p>\r\n				</td>\r\n			</tr>\r\n		</table>\r\n	</body>\r\n</html>',17,1,0);
INSERT INTO tblContentTypes VALUES (40,'text/js','.js','// created by webEdition',17,0,1);
INSERT INTO tblContentTypes VALUES (30,'text/css','.css','',17,0,1);
INSERT INTO tblContentTypes VALUES (0,'folder','','',2,0,0);
INSERT INTO tblContentTypes VALUES (60,'application/x-shockwave-flash','.swf','',18,0,1);
INSERT INTO tblContentTypes VALUES (70,'application/*','.doc,.xls,.ppt,.pdf,.zip,.sit,.bin,.hqx,.exe','',1,0,1);

#
# Table structure for table 'tblDocTypes'
#

CREATE TABLE tblDocTypes (
  ID int(11) NOT NULL auto_increment,
  DocType varchar(32) NOT NULL default '',
  Extension varchar(10) NOT NULL default '',
  ParentID int(11) NOT NULL default '0',
  ParentPath varchar(255) NOT NULL default '',
  SubDir int(11) NOT NULL default '0',
  TemplateID int(11) NOT NULL default '0',
  IsDynamic tinyint(1) NOT NULL default '0',
  IsSearchable tinyint(1) NOT NULL default '0',
  ContentTable varchar(32) NOT NULL default '',
  JavaScript text NOT NULL,
  Notify text NOT NULL,
  NotifyTemplateID int(11) NOT NULL default '0',
  NotifySubject varchar(64) NOT NULL default '',
  NotifyOnChange tinyint(1) NOT NULL default '0',
  LockID int(11) NOT NULL default '0',
  Templates varchar(255) NOT NULL default '',
  Deleted int(11) NOT NULL default '0',
  Category varchar(255) default NULL,
  PRIMARY KEY  (ID)
) TYPE=MyISAM;

#
# Dumping data for table 'tblDocTypes'
#

INSERT INTO tblDocTypes VALUES (2,'newsArticle','.html',94,'/we_demo/news',0,27,0,1,'','','',0,'',0,0,'27',0,'');
INSERT INTO tblDocTypes VALUES (3,'movieReview','.html',102,'/we_demo/filmberichte',0,29,0,1,'','','',0,'',0,0,'29',0,'');
INSERT INTO tblDocTypes VALUES (4,'item','.php',165,'/shop/artikel',0,42,1,1,'','','',0,'',0,0,'42',0,'');

#
# Table structure for table 'tblErrorLog'
#

CREATE TABLE tblErrorLog (
  ID int(11) NOT NULL auto_increment,
  Text text NOT NULL,
  Date int(11) NOT NULL default '0',
  PRIMARY KEY  (ID)
) TYPE=MyISAM;

#
# Dumping data for table 'tblErrorLog'
#


#
# Table structure for table 'tblFailedLogins'
#

CREATE TABLE tblFailedLogins (
  ID bigint(20) NOT NULL default '0',
  Username varchar(64) NOT NULL default '',
  Password varchar(32) NOT NULL default '',
  IP varchar(15) NOT NULL default '',
  LoginDate int(11) NOT NULL default '0'
) TYPE=MyISAM;

#
# Dumping data for table 'tblFailedLogins'
#


#
# Table structure for table 'tblFile'
#

CREATE TABLE tblFile (
  ID int(11) NOT NULL auto_increment,
  ParentID int(11) NOT NULL default '0',
  Text varchar(255) binary NOT NULL default '',
  Icon varchar(64) NOT NULL default '',
  IsFolder tinyint(4) NOT NULL default '0',
  ContentType varchar(32) NOT NULL default '0',
  CreationDate int(11) NOT NULL default '0',
  ModDate int(11) NOT NULL default '0',
  Path varchar(255) binary NOT NULL default '',
  TemplateID int(11) NOT NULL default '0',
  Filename varchar(255) binary NOT NULL default '',
  Extension varchar(16) binary NOT NULL default '',
  IsDynamic tinyint(4) NOT NULL default '0',
  IsSearchable tinyint(1) NOT NULL default '0',
  DocType varchar(32) NOT NULL default '',
  ClassName varchar(64) NOT NULL default '',
  Category varchar(255) default NULL,
  Deleted int(11) NOT NULL default '0',
  Published int(11) NOT NULL default '0',
  CreatorID bigint(20) NOT NULL default '0',
  ModifierID bigint(20) NOT NULL default '0',
  RestrictOwners tinyint(1) NOT NULL default '0',
  Owners varchar(255) NOT NULL default '',
  OwnersReadOnly text NOT NULL,
  documentArray text NOT NULL,
  PRIMARY KEY  (ID)
) TYPE=MyISAM;

#
# Dumping data for table 'tblFile'
#

INSERT INTO tblFile VALUES (94,132,'news','folder.gif',1,'folder',1001610920,1002568990,'/we_demo/news',0,'news','',0,0,'','we_folder','',0,0,0,0,0,'','','');
INSERT INTO tblFile VALUES (95,94,'news_stress.html','we_dokument.gif',0,'text/webedition',1001610895,1022264949,'/we_demo/news/news_stress.html',27,'news_stress','.html',0,1,'2','we_webEditionDocument','',0,1022264951,0,0,0,'','','');
INSERT INTO tblFile VALUES (96,94,'news_energy.html','we_dokument.gif',0,'text/webedition',1001611164,1022264731,'/we_demo/news/news_energy.html',27,'news_energy','.html',0,1,'2','we_webEditionDocument','',0,1022264732,0,0,0,'','','');
INSERT INTO tblFile VALUES (97,94,'news_holiday.html','we_dokument.gif',0,'text/webedition',1001611624,1022264892,'/we_demo/news/news_holiday.html',27,'news_holiday','.html',0,1,'2','we_webEditionDocument','',0,1022264893,0,0,0,'','','');
INSERT INTO tblFile VALUES (98,132,'index.php','we_dokument.gif',0,'text/webedition',1001616552,1059917513,'/we_demo/index.php',24,'index','.php',1,1,'','we_webEditionDocument','',0,1059917513,0,1,0,'','','');
INSERT INTO tblFile VALUES (100,94,'images','folder.gif',1,'folder',1001669838,1002568990,'/we_demo/news/images',0,'images','',0,0,'','we_folder','',0,0,0,0,0,'','','');
INSERT INTO tblFile VALUES (102,132,'moviereviews','folder.gif',1,'folder',1001671063,1022005940,'/we_demo/moviereviews',0,'moviereviews','',0,0,'','we_folder','',0,0,0,0,0,'','','');
INSERT INTO tblFile VALUES (103,102,'sonax.html','we_dokument.gif',0,'text/webedition',1001671142,1022264556,'/we_demo/moviereviews/sonax.html',29,'sonax','.html',0,1,'3','we_webEditionDocument',',8,',0,1022264558,0,0,0,'','','');
INSERT INTO tblFile VALUES (104,102,'boysn_girls.html','we_dokument.gif',0,'text/webedition',1001672624,1022264472,'/we_demo/moviereviews/boysn_girls.html',29,'boysn_girls','.html',0,1,'3','we_webEditionDocument',',9,',0,1022264473,0,0,0,'','','');
INSERT INTO tblFile VALUES (105,102,'reviews_list.php','we_dokument.gif',0,'text/webedition',1001674908,1059917565,'/we_demo/moviereviews/reviews_list.php',30,'reviews_list','.php',1,1,'','we_webEditionDocument','',0,1059917565,0,1,0,'','','');
INSERT INTO tblFile VALUES (110,132,'program','folder.gif',1,'folder',1001679118,1022010342,'/we_demo/program',0,'program','',0,0,'','we_folder','',0,0,0,0,0,'','','');
INSERT INTO tblFile VALUES (111,110,'program.html','we_dokument.gif',0,'text/webedition',1001678315,1059917665,'/we_demo/program/program.html',36,'program','.html',0,1,'','we_webEditionDocument','',0,1059917665,0,1,0,'','','');
INSERT INTO tblFile VALUES (112,132,'links','folder.gif',1,'folder',1001679914,1002568992,'/we_demo/links',0,'links','',0,0,'','we_folder','',0,0,0,0,0,'','','');
INSERT INTO tblFile VALUES (113,112,'links.html','we_dokument.gif',0,'text/webedition',1001679890,1059917535,'/we_demo/links/links.html',38,'links','.html',0,1,'','we_webEditionDocument','',0,1059917535,0,1,0,'','','');
INSERT INTO tblFile VALUES (89,132,'style.css','prog.gif',0,'text/css',1001608139,1021127156,'/we_demo/style.css',0,'style','.css',0,0,'','we_textDocument','',0,1076404444,0,0,0,'','','');
INSERT INTO tblFile VALUES (90,132,'navigation.php','we_dokument.gif',0,'text/webedition',1001608757,1058722221,'/we_demo/navigation.php',26,'navigation','.php',1,0,'','we_webEditionDocument','',0,1058722222,0,1,0,'','','');
INSERT INTO tblFile VALUES (114,132,'search.php','we_dokument.gif',0,'text/webedition',1001686953,1022011880,'/we_demo/search.php',39,'search','.php',1,0,'','we_webEditionDocument','',0,1022088129,0,0,0,'','','');
INSERT INTO tblFile VALUES (138,100,'Sonax_astronaut.jpg','image.gif',0,'image/*',1002620738,1002620790,'/we_demo/news/images/Sonax_astronaut.jpg',0,'Sonax_astronaut','.jpg',0,0,'','we_imageDocument','',0,1076404446,0,0,0,'','','');
INSERT INTO tblFile VALUES (117,102,'roses.html','we_dokument.gif',0,'text/webedition',1001932119,1022264505,'/we_demo/moviereviews/roses.html',29,'roses','.html',0,1,'3','we_webEditionDocument',',12,',0,1022264507,0,0,0,'','','');
INSERT INTO tblFile VALUES (118,102,'tirol.html','we_dokument.gif',0,'text/webedition',1001934682,1022264635,'/we_demo/moviereviews/tirol.html',29,'tirol','.html',0,1,'3','we_webEditionDocument',',15,',0,1022264637,0,0,0,'','','');
INSERT INTO tblFile VALUES (139,100,'stress_at_work.jpg','image.gif',0,'image/*',1002620797,1022006913,'/we_demo/news/images/stress_at_work.jpg',0,'stress_at_work','.jpg',0,0,'','we_imageDocument','',0,1076404449,0,0,0,'','','');
INSERT INTO tblFile VALUES (140,100,'windenergy.jpg','image.gif',0,'image/*',1002620836,1022006924,'/we_demo/news/images/windenergy.jpg',0,'windenergy','.jpg',0,0,'','we_imageDocument','',0,1076404449,0,0,0,'','','');
INSERT INTO tblFile VALUES (128,94,'news_cms.html','we_dokument.gif',0,'text/webedition',1002185822,1059917621,'/we_demo/news/news_cms.html',27,'news_cms','.html',0,1,'2','we_webEditionDocument','',0,1059917621,0,1,0,'','','');
INSERT INTO tblFile VALUES (133,100,'boys_girls_chairs.jpg','image.gif',0,'image/*',1002620492,1022006871,'/we_demo/news/images/boys_girls_chairs.jpg',0,'boys_girls_chairs','.jpg',0,0,'','we_imageDocument','',0,1076404451,0,0,0,'','','');
INSERT INTO tblFile VALUES (134,100,'drama.jpg','image.gif',0,'image/*',1002620561,1002620590,'/we_demo/news/images/drama.jpg',0,'drama','.jpg',0,0,'','we_imageDocument','',0,1076404452,0,0,0,'','','');
INSERT INTO tblFile VALUES (135,100,'lovest_mountains.jpg','image.gif',0,'image/*',1002620603,1022006886,'/we_demo/news/images/lovest_mountains.jpg',0,'lovest_mountains','.jpg',0,0,'','we_imageDocument','',0,1076404453,0,0,0,'','','');
INSERT INTO tblFile VALUES (136,100,'news_holidays.jpg','image.gif',0,'image/*',1002620648,1022007181,'/we_demo/news/images/news_holidays.jpg',0,'news_holidays','.jpg',0,0,'','we_imageDocument','',0,1076404453,0,0,0,'','','');
INSERT INTO tblFile VALUES (137,100,'software3.jpg','image.gif',0,'image/*',1002620698,1022009191,'/we_demo/news/images/software3.jpg',0,'software3','.jpg',0,0,'','we_imageDocument','',0,1076404455,0,0,0,'','','');
INSERT INTO tblFile VALUES (132,0,'we_demo','folder.gif',1,'folder',1002567927,1002568990,'/we_demo',0,'we_demo','',0,0,'','we_folder','',0,0,0,0,0,'','','');
INSERT INTO tblFile VALUES (142,132,'shop','folder.gif',1,'folder',1008879442,1020692289,'/we_demo/shop',0,'shop','',0,0,'','we_folder','',0,0,0,0,0,'','','');
INSERT INTO tblFile VALUES (145,142,'index.php','we_dokument.gif',0,'text/webedition',1008879722,1059917713,'/we_demo/shop/index.php',41,'index','.php',1,0,'','we_webEditionDocument','',0,1059917713,0,1,0,'','','');
INSERT INTO tblFile VALUES (148,142,'basket.php','we_dokument.gif',0,'text/webedition',1009381856,1059917689,'/we_demo/shop/basket.php',43,'basket','.php',1,1,'','we_webEditionDocument','',0,1059917689,0,1,0,'','','');
INSERT INTO tblFile VALUES (149,142,'goodslist.php','we_dokument.gif',0,'text/webedition',1009401543,1022006842,'/we_demo/shop/goodslist.php',44,'goodslist','.php',1,1,'','we_webEditionDocument','',0,1022088147,0,0,0,'','','');
INSERT INTO tblFile VALUES (301,132,'dhtmlpopup.html','we_dokument.gif',0,'text/webedition',1021052223,1022082814,'/we_demo/dhtmlpopup.html',40,'dhtmlpopup','.html',0,0,'','we_webEditionDocument','',0,1022089175,0,0,0,'','','');
INSERT INTO tblFile VALUES (151,142,'images','folder.gif',1,'folder',1009479523,1020692290,'/we_demo/shop/images',0,'images','',0,0,'','we_folder','',0,0,0,0,0,'','','');
INSERT INTO tblFile VALUES (152,151,'webediton_pack.jpg','image.gif',0,'image/*',1009479545,1020692290,'/we_demo/shop/images/webediton_pack.jpg',0,'webediton_pack','.jpg',0,0,'','we_imageDocument','',0,1076404464,0,0,0,'','','');
INSERT INTO tblFile VALUES (156,142,'userdata.php','we_dokument.gif',0,'text/webedition',1010064607,1059917805,'/we_demo/shop/userdata.php',48,'userdata','.php',1,0,'','we_webEditionDocument','',0,1059917805,0,1,0,'','','');
INSERT INTO tblFile VALUES (157,142,'login.php','we_dokument.gif',0,'text/webedition',1010341071,1059917765,'/we_demo/shop/login.php',45,'login','.php',1,0,'','we_webEditionDocument','',0,1059917765,0,1,0,'','','');
INSERT INTO tblFile VALUES (299,151,'basket_in2.gif','image.gif',0,'image/*',1020874596,1022006770,'/we_demo/shop/images/basket_in2.gif',0,'basket_in2','.gif',0,0,'','we_imageDocument','',0,1076404467,0,0,0,'','','');
INSERT INTO tblFile VALUES (300,151,'basket_out2.gif','image.gif',0,'image/*',1020874770,1022006753,'/we_demo/shop/images/basket_out2.gif',0,'basket_out2','.gif',0,0,'','we_imageDocument','',0,1076404467,0,0,0,'','','');
INSERT INTO tblFile VALUES (162,142,'mailtext.php','we_dokument.gif',0,'text/webedition',1016723002,1020874304,'/we_demo/shop/mailtext.php',49,'mailtext','.php',1,0,'','we_webEditionDocument','',0,1022088156,0,0,0,'','','');
INSERT INTO tblFile VALUES (165,142,'items','folder.gif',1,'folder',1020444278,1022007022,'/we_demo/shop/items',0,'items','',0,0,'','we_folder','',0,0,0,0,0,'','','');
INSERT INTO tblFile VALUES (166,165,'webedition_update.php','we_dokument.gif',0,'text/webedition',1020444340,1059918312,'/we_demo/shop/items/webedition_update.php',42,'webedition_update','.php',1,1,'4','we_webEditionDocument',',16,',0,1059918312,0,1,0,'','','');
INSERT INTO tblFile VALUES (167,165,'webedition_update_five.php','we_dokument.gif',0,'text/webedition',1020444434,1059918301,'/we_demo/shop/items/webedition_update_five.php',42,'webedition_update_five','.php',1,1,'4','we_webEditionDocument',',16,',0,1059918301,0,1,0,'','','');
INSERT INTO tblFile VALUES (168,165,'webedition_update_twenty.php','we_dokument.gif',0,'text/webedition',1020444485,1059918289,'/we_demo/shop/items/webedition_update_twenty.php',42,'webedition_update_twenty','.php',1,1,'4','we_webEditionDocument',',16,',0,1059918289,0,1,0,'','','');
INSERT INTO tblFile VALUES (169,165,'webedition.php','we_dokument.gif',0,'text/webedition',1020444546,1076609053,'/we_demo/shop/items/webedition.php',42,'webedition','.php',1,1,'4','we_webEditionDocument',',16,',0,1076609053,0,1,0,'','','');
INSERT INTO tblFile VALUES (170,165,'webedition_five.php','we_dokument.gif',0,'text/webedition',1020444762,1059918192,'/we_demo/shop/items/webedition_five.php',42,'webedition_five','.php',1,1,'4','we_webEditionDocument',',16,',0,1059918192,0,1,0,'','','');
INSERT INTO tblFile VALUES (171,165,'webedition_twenty.php','we_dokument.gif',0,'text/webedition',1020444869,1059918205,'/we_demo/shop/items/webedition_twenty.php',42,'webedition_twenty','.php',1,1,'4','we_webEditionDocument',',16,',0,1059918205,0,1,0,'','','');
INSERT INTO tblFile VALUES (172,165,'webedition_bundle.php','we_dokument.gif',0,'text/webedition',1020444903,1059918180,'/we_demo/shop/items/webedition_bundle.php',42,'webedition_bundle','.php',1,1,'4','we_webEditionDocument',',16,',0,1059918180,0,1,0,'','','');
INSERT INTO tblFile VALUES (173,142,'order.php','we_dokument.gif',0,'text/webedition',1020449872,1059917782,'/we_demo/shop/order.php',56,'order','.php',1,0,'','we_webEditionDocument','',0,1059917782,0,1,0,'','','');
INSERT INTO tblFile VALUES (302,132,'layout_images','folder.gif',1,'folder',1035904120,1035904288,'/we_demo/layout_images',0,'layout_images','',0,0,'','we_folder','',0,0,1,1,0,'','','');
INSERT INTO tblFile VALUES (303,302,'bg.gif','image.gif',0,'image/*',1035904497,1035904497,'/we_demo/layout_images/bg.gif',0,'bg','.gif',0,0,'','we_imageDocument','',0,1076404478,0,1,0,'','','');
INSERT INTO tblFile VALUES (304,302,'pixel.gif','image.gif',0,'image/*',1035904497,1035904497,'/we_demo/layout_images/pixel.gif',0,'pixel','.gif',0,0,'','we_imageDocument','',0,1076404479,0,1,0,'','','');
INSERT INTO tblFile VALUES (305,302,'we_logo.gif','image.gif',0,'image/*',1035904497,1035904497,'/we_demo/layout_images/we_logo.gif',0,'we_logo','.gif',0,0,'','we_imageDocument','',0,1076404480,0,1,0,'','','');
INSERT INTO tblFile VALUES (306,132,'events','folder.gif',1,'folder',1035905499,1035905682,'/we_demo/events',0,'events','',0,0,'','we_folder','',0,0,1,1,0,'','','');
INSERT INTO tblFile VALUES (307,306,'overview.php','we_dokument.gif',0,'text/webedition',1035905724,1035905839,'/we_demo/events/overview.php',63,'overview','.php',1,0,'','we_webEditionDocument','',0,1035905839,1,1,0,'','','');
INSERT INTO tblFile VALUES (308,306,'eventaddresses.php','we_dokument.gif',0,'text/webedition',1035909232,1035909372,'/we_demo/events/eventaddresses.php',60,'eventaddresses','.php',1,0,'','we_webEditionDocument','',0,1035909372,1,1,0,'','','');
INSERT INTO tblFile VALUES (309,132,'newsletter','folder.gif',1,'folder',1058721910,1058721933,'/we_demo/newsletter',0,'newsletter','',0,0,'','we_folder','',0,0,1,1,0,'','','');
INSERT INTO tblFile VALUES (310,309,'newsletter.php','we_dokument.gif',0,'text/webedition',1058721940,1058721996,'/we_demo/newsletter/newsletter.php',67,'newsletter','.php',1,0,'','we_webEditionDocument','',0,1058721996,1,1,0,'','','');
INSERT INTO tblFile VALUES (311,309,'salutation.php','we_dokument.gif',0,'text/webedition',1058722006,1058722052,'/we_demo/newsletter/salutation.php',66,'salutation','.php',1,0,'','we_webEditionDocument','',0,1058722052,1,1,0,'','','');
INSERT INTO tblFile VALUES (312,309,'index.php','we_dokument.gif',0,'text/webedition',1058722070,1059918014,'/we_demo/newsletter/index.php',68,'index','.php',1,0,'','we_webEditionDocument','',0,1059918014,1,1,0,'','','');
INSERT INTO tblFile VALUES (313,309,'unsubscribeBlock.php','we_dokument.gif',0,'text/webedition',1059943311,1059943346,'/we_demo/newsletter/unsubscribeBlock.php',69,'unsubscribeBlock','.php',1,0,'','we_webEditionDocument','',0,1059943346,1,1,0,'','','');

#
# Table structure for table 'tblIndex'
#

CREATE TABLE tblIndex (
  DID int(11) NOT NULL default '0',
  Text text NOT NULL,
  ID bigint(20) NOT NULL default '0',
  OID bigint(20) NOT NULL default '0',
  BText longblob NOT NULL,
  Workspace varchar(255) NOT NULL default '/',
  WorkspaceID bigint(20) NOT NULL default '0',
  Category varchar(255) NOT NULL default '',
  ClassID bigint(20) NOT NULL default '0',
  Doctype bigint(20) NOT NULL default '0',
  Title varchar(255) NOT NULL default '',
  Description text NOT NULL,
  Path varchar(255) NOT NULL default ''
) TYPE=MyISAM;

#
# Dumping data for table 'tblIndex'
#

INSERT INTO tblIndex VALUES (95,'Time flies and time is running out under stress! Time flies and time is running out under stress! Stress at work How to put an end to stress Stress   Nowadays, a lot of people are always busy and seem to be constantly under stress. They are caught in a vicious circle with dangerous side effects: When work is piling up, people get under stress; yet � when under stress, you will be unable to work efficently. Even the smallest additional problem seems to be insurmountable now and produces more stress.Although it is common knowledge that stress has a lot of negative effects, the dialy grind is often welcomed by people saying: �A little bit of stress brings out the best of me.� However, psychologists state that people under stress are unable to maintain an efficient time management and cannot handle the arising volume of work sufficently. Consequently, the most important factor to reduce stress is an efficent and sensible time management.',0,0,'Time flies and time is running out under stress! Time flies and time is running out under stress! Stress at work How to put an end to stress Stress   Nowadays, a lot of people are always busy and seem to be constantly under stress. They are caught in a vicious circle with dangerous side effects: When work is piling up, people get under stress; yet � when under stress, you will be unable to work efficently. Even the smallest additional problem seems to be insurmountable now and produces more stress.Although it is common knowledge that stress has a lot of negative effects, the dialy grind is often welcomed by people saying: �A little bit of stress brings out the best of me.� However, psychologists state that people under stress are unable to maintain an efficient time management and cannot handle the arising volume of work sufficently. Consequently, the most important factor to reduce stress is an efficent and sensible time management.','/we_demo/news',94,'',0,2,'Stress at work','Time flies and time is running out under stress!','/we_demo/news/news_stress.html');
INSERT INTO tblIndex VALUES (96,'Get wind of the new energy! Wind energy Wind energy Currently, our wind power stations produce about 7000 megawatt � this equals the same amount of energy that is produced by seven nuclear power stations.\nWind energy contributes by 2,5% to the German generation of current. Lower Saxony is at the top of the generation of wind energy, over 2700 fan blowers are located there.\n\nLearn how wind energy might be used even more efficently in the future by reading the interview with Professor M�ller.\n\n   Get wind of the new energy',0,0,'Get wind of the new energy! Wind energy Wind energy Currently, our wind power stations produce about 7000 megawatt � this equals the same amount of energy that is produced by seven nuclear power stations.\nWind energy contributes by 2,5% to the German generation of current. Lower Saxony is at the top of the generation of wind energy, over 2700 fan blowers are located there.\n\nLearn how wind energy might be used even more efficently in the future by reading the interview with Professor M�ller.\n\n   Get wind of the new energy','/we_demo/news',94,'',0,2,'Wind energy','Get wind of the new energy ','/we_demo/news/news_energy.html');
INSERT INTO tblIndex VALUES (97,'Talai: Apart from extremely friendly people, the island also offers pleasing countryside, impressive cliffs and beautiful bays inviting you to spend an uninhibited holiday there.\n\nAs Talai can only be reached via the sea, it is expected to remain free from mass tourism and as such is truly an insider tip. Talai � the island of dreams is the latest quiet tip   Holidays Talai, island, holidays Talai � the island of dreams is the latest quiet tip Very casual - Talai',0,0,'Talai: Apart from extremely friendly people, the island also offers pleasing countryside, impressive cliffs and beautiful bays inviting you to spend an uninhibited holiday there.\n\nAs Talai can only be reached via the sea, it is expected to remain free from mass tourism and as such is truly an insider tip. Talai � the island of dreams is the latest quiet tip   Holidays Talai, island, holidays Talai � the island of dreams is the latest quiet tip Very casual - Talai','/we_demo/news',94,'',0,2,'Holidays','Talai � the island of dreams is the latest quiet tip','/we_demo/news/news_holiday.html');
INSERT INTO tblIndex VALUES (98,'CMS-Channel News   Recent news from the CMS-Channel News, CMS, webEdition',0,0,'CMS-Channel News   Recent news from the CMS-Channel News, CMS, webEdition','/we_demo',132,'',0,0,'CMS-Channel News','Recent news from the CMS-Channel','/we_demo/index.php');
INSERT INTO tblIndex VALUES (103,'Sonax Sonax   The future...\n\nIn the year 2150 John wants to go on an expedition to the Sonax-Galaxy. He supposes yet undiscovered living spaces there. According to the valid jurisdiction in the year 2150 the explorer of new living spaces is the sole owner of the frontier. Nevertheless, such expeditions have to be authorized by the F.o.a.N., the Federation of all Nations. Shortly after his departure John recognizes that this trip is not going to be an easy one. As he enters the Sonax-Galaxy, he is approached by a fleet of federated space-ships... Sonax Sonax - pure action!',0,0,'Sonax Sonax   The future...\n\nIn the year 2150 John wants to go on an expedition to the Sonax-Galaxy. He supposes yet undiscovered living spaces there. According to the valid jurisdiction in the year 2150 the explorer of new living spaces is the sole owner of the frontier. Nevertheless, such expeditions have to be authorized by the F.o.a.N., the Federation of all Nations. Shortly after his departure John recognizes that this trip is not going to be an easy one. As he enters the Sonax-Galaxy, he is approached by a fleet of federated space-ships... Sonax Sonax - pure action!','/we_demo/moviereviews',102,',8,',0,3,'Sonax','Sonax - pure action!','/we_demo/moviereviews/sonax.html');
INSERT INTO tblIndex VALUES (104,'Boys\'n Girls Finally, school is out for summer and everybody has a little fun in the sun. Nina and Melissa have recently turned 17 and are best friends. They are determined to make this summer a very special one. But then a handsome boy moves in next door and both girls have a crush on him. Now, Melissa and Nina become rivals in a funny game of love and romance, each trying to catch the attention of the young lad. Boys\'n Girls Comedy about the holiday experience of two girls Comedy, Holidays',0,0,'Boys\'n Girls Finally, school is out for summer and everybody has a little fun in the sun. Nina and Melissa have recently turned 17 and are best friends. They are determined to make this summer a very special one. But then a handsome boy moves in next door and both girls have a crush on him. Now, Melissa and Nina become rivals in a funny game of love and romance, each trying to catch the attention of the young lad. Boys\'n Girls Comedy about the holiday experience of two girls Comedy, Holidays','/we_demo/moviereviews',102,',9,',0,3,'Boys\'n Girls','Comedy about the holiday experience of two girls','/we_demo/moviereviews/boysn_girls.html');
INSERT INTO tblIndex VALUES (105,'cms,webEdition CMS-Channel Demo-Website for the CMS webEdition',0,0,'cms,webEdition CMS-Channel Demo-Website for the CMS webEdition','/we_demo/moviereviews',102,'',0,0,'CMS-Channel','Demo-Website for the CMS webEdition','/we_demo/moviereviews/reviews_list.php');
INSERT INTO tblIndex VALUES (111,'CMS-Channel You ask, we answer - usefull hints & suggestions around software Daily overview Software News Detailed news from around the world   Comparison of the best CMS - Systems CMS-Systems Software News from around the world Good morningSoftware Boys\'n Girls News, Weatherforecast News from around the world The CMS-Market worldwide CMS-Systems conquer the world CMS-Talk live CMS - Special Interview with a CMS-Specialist News, Weatherforecast webEdition to be reviewed today News from around the world Comparison of the best CMS - Systems CMS-Systems webEdition the price sensation among CMS-Systems Bargains News, Weatherforecast Every evening a tasty tidbit to relax! Useful hints & suggestions Demo-Website for the CMS webEdition cms,webEdition',0,0,'CMS-Channel You ask, we answer - usefull hints & suggestions around software Daily overview Software News Detailed news from around the world   Comparison of the best CMS - Systems CMS-Systems Software News from around the world Good morningSoftware Boys\'n Girls News, Weatherforecast News from around the world The CMS-Market worldwide CMS-Systems conquer the world CMS-Talk live CMS - Special Interview with a CMS-Specialist News, Weatherforecast webEdition to be reviewed today News from around the world Comparison of the best CMS - Systems CMS-Systems webEdition the price sensation among CMS-Systems Bargains News, Weatherforecast Every evening a tasty tidbit to relax! Useful hints & suggestions Demo-Website for the CMS webEdition cms,webEdition','/we_demo/program',110,'',0,0,'CMS-Channel','Demo-Website for the CMS webEdition','/we_demo/program/program.html');
INSERT INTO tblIndex VALUES (113,'cms,webEdition CMS-Channel webEdition: Opt for webEdition now! With webEdition you will be able to keep the contents of your web site up to date any time. The webEdition Software GmbH relaunches your web site by using its own CMS. You can easily produce and administer the contents of your site by yourself.\neasily create and change your own content. Demo-Website for the CMS webEdition',0,0,'cms,webEdition CMS-Channel webEdition: Opt for webEdition now! With webEdition you will be able to keep the contents of your web site up to date any time. The webEdition Software GmbH relaunches your web site by using its own CMS. You can easily produce and administer the contents of your site by yourself.\neasily create and change your own content. Demo-Website for the CMS webEdition','/we_demo/links',112,'',0,0,'CMS-Channel','Demo-Website for the CMS webEdition','/we_demo/links/links.html');
INSERT INTO tblIndex VALUES (117,'Drama about the daily problems of people who are different Drama Withered Roses The hairdresser`s shop is the perfect place to pick up the latest gossip in town. This is why Marlene comes here to chat with her friends. Presently, the talk of the town is a young lady who has recently settled here and suffers from a unilateral facial paralysis. \nSuddenly, this lady enters the shop and takes the seat right next to Marlene... Withered Roses',0,0,'Drama about the daily problems of people who are different Drama Withered Roses The hairdresser`s shop is the perfect place to pick up the latest gossip in town. This is why Marlene comes here to chat with her friends. Presently, the talk of the town is a young lady who has recently settled here and suffers from a unilateral facial paralysis. \nSuddenly, this lady enters the shop and takes the seat right next to Marlene... Withered Roses','/we_demo/moviereviews',102,',12,',0,3,'Withered Roses','Drama about the daily problems of people who are different','/we_demo/moviereviews/roses.html');
INSERT INTO tblIndex VALUES (118,'Love Story in the tirol mountains yearning for the Tirol Tirol, Love Story   Yearning for the Tirol Beautiful Francesca is born the daugther of the poor Tyrolese peasant Georgio. \nLife is hard in the small mountain village of Monte. If it wasn�t for her father, Francesca would have already left the village for the more exciting big cities of Germany or Italy. \nOne day Francesca meets the handsome German student Christian way up in the mountains. Christian is apparently injured, and Francesca offers to shelter him for the night. However, Georgio does not agree with this plan.\nAfter a furious dispute with her father, Francesca is determined to leave the village and wants to go to Germany with Christian.',0,0,'Love Story in the tirol mountains yearning for the Tirol Tirol, Love Story   Yearning for the Tirol Beautiful Francesca is born the daugther of the poor Tyrolese peasant Georgio. \nLife is hard in the small mountain village of Monte. If it wasn�t for her father, Francesca would have already left the village for the more exciting big cities of Germany or Italy. \nOne day Francesca meets the handsome German student Christian way up in the mountains. Christian is apparently injured, and Francesca offers to shelter him for the night. However, Georgio does not agree with this plan.\nAfter a furious dispute with her father, Francesca is determined to leave the village and wants to go to Germany with Christian.','/we_demo/moviereviews',102,',15,',0,3,'yearning for the Tirol','Love Story in the tirol mountains','/we_demo/moviereviews/tirol.html');
INSERT INTO tblIndex VALUES (128,'webEdition Software GmbH was founded in April 2003 with the aim of\ndeveloping and marketing the webEdition content management system\n(CMS).&nbsp; webEdition was first developed by Astarte New Media AG,\nwhich established an outstanding reputation as a software development\ncompany with such products as the CD recording software, Toast, for the\nMacintosh platform, and the DVD authoring tool known as DVDirector for\nthe PC and Macintosh markets. Having sold more than 4000 licences since\nfirst entering the market in November of 2001, the webEdition CMS has\nestablished itself as one of the leading systems in the German CMS\nmarket. \n\nThis success is owing to two factors: first, webEdition is designed\nspecifically for small and medium-sized businesses; second, webEdition\noffers an extremely competitive price-quality relationship. Even the\nStandard version of this modular system, starting at just 159 Euro,\nhas capabilities that one finds only in systems that often cost more\nthan 100 times the price.  CMS for the people! CMS webEdition Software GmbH webEdition Software GmbH webEdition Software GmbH',0,0,'webEdition Software GmbH was founded in April 2003 with the aim of\ndeveloping and marketing the webEdition content management system\n(CMS).&nbsp; webEdition was first developed by Astarte New Media AG,\nwhich established an outstanding reputation as a software development\ncompany with such products as the CD recording software, Toast, for the\nMacintosh platform, and the DVD authoring tool known as DVDirector for\nthe PC and Macintosh markets. Having sold more than 4000 licences since\nfirst entering the market in November of 2001, the webEdition CMS has\nestablished itself as one of the leading systems in the German CMS\nmarket. \n\nThis success is owing to two factors: first, webEdition is designed\nspecifically for small and medium-sized businesses; second, webEdition\noffers an extremely competitive price-quality relationship. Even the\nStandard version of this modular system, starting at just 159 Euro,\nhas capabilities that one finds only in systems that often cost more\nthan 100 times the price.  CMS for the people! CMS webEdition Software GmbH webEdition Software GmbH webEdition Software GmbH','/we_demo/news',94,'',0,2,'webEdition Software GmbH','webEdition Software GmbH','/we_demo/news/news_cms.html');
INSERT INTO tblIndex VALUES (148,'webEdition, cms example basket for the webEdition demo-shop   Basket',0,0,'webEdition, cms example basket for the webEdition demo-shop   Basket','/we_demo/shop',142,'',0,0,'Basket','example basket for the webEdition demo-shop','/we_demo/shop/basket.php');
INSERT INTO tblIndex VALUES (149,'',0,0,'','/we_demo/shop',142,'',0,0,'','','/we_demo/shop/goodslist.php');
INSERT INTO tblIndex VALUES (166,'Additional licence of webEdition  2   cms,webEdition Additional licence of webEdition for one domain. 129,00 webEdition additional licence Additional licence of webEdition for one domain.\n webEdition additional licence Additional licence of webEdition for one domain.',0,0,'Additional licence of webEdition  2   cms,webEdition Additional licence of webEdition for one domain. 129,00 webEdition additional licence Additional licence of webEdition for one domain.\n webEdition additional licence Additional licence of webEdition for one domain.','/we_demo/shop/items',165,',16,',0,4,'webEdition additional licence','Additional licence of webEdition for one domain.','/we_demo/shop/items/webedition_update.php');
INSERT INTO tblIndex VALUES (167,'4 cms,webEdition Additional licences of webEdition for five domains.\n 599,00 webEdition five additional licences Additional licence of webEdition for five domains.   Additional licence of webEdition for five domains.\n\n five Additional licences of webEdition  webEdition five additional licences',0,0,'4 cms,webEdition Additional licences of webEdition for five domains.\n 599,00 webEdition five additional licences Additional licence of webEdition for five domains.   Additional licence of webEdition for five domains.\n\n five Additional licences of webEdition  webEdition five additional licences','/we_demo/shop/items',165,',16,',0,4,'webEdition five additional licences','Additional licence of webEdition for five domains.','/we_demo/shop/items/webedition_update_five.php');
INSERT INTO tblIndex VALUES (168,'twenty Additional licences of webEdition  Additional licences of webEdition for twenty domains.\n 6 webEdition twenty additional licences Additional licence of webEdition for twenty domains. cms,webEdition   Additional licences of webEdition for twenty domains.\n\n 1.299,00 webEdition twenty additional licences',0,0,'twenty Additional licences of webEdition  Additional licences of webEdition for twenty domains.\n 6 webEdition twenty additional licences Additional licence of webEdition for twenty domains. cms,webEdition   Additional licences of webEdition for twenty domains.\n\n 1.299,00 webEdition twenty additional licences','/we_demo/shop/items',165,',16,',0,4,'webEdition twenty additional licences','Additional licence of webEdition for twenty domains.','/we_demo/shop/items/webedition_update_twenty.php');
INSERT INTO tblIndex VALUES (169,'1 webEdition for one domain webEdition Basis version webEdition Basis version The basic version of webEdition is for the administration of one domain. \nThe target group is all small and medium-sized companies as well as\nprivate individuals who dont have any knowledge of HTML but still want\nto maintain their website dynamically.\n webEdition Basis version 199,00 cms,webEdition   webEdition for one domain',0,0,'1 webEdition for one domain webEdition Basis version webEdition Basis version The basic version of webEdition is for the administration of one domain. \nThe target group is all small and medium-sized companies as well as\nprivate individuals who dont have any knowledge of HTML but still want\nto maintain their website dynamically.\n webEdition Basis version 199,00 cms,webEdition   webEdition for one domain','/we_demo/shop/items',165,',16,',0,4,'webEdition Basis version','webEdition for one domain','/we_demo/shop/items/webedition.php');
INSERT INTO tblIndex VALUES (170,'599,00 webEdition FIVE   3 webEdition TWENTY  is for the administration of 5 domains. \nThe target group is medium-sized Internet companies who want to maintain their website dynamically. \n webEdition FIVE cms,webEdition webEdition for five domains webEdition for five domains webEdition FIVE',0,0,'599,00 webEdition FIVE   3 webEdition TWENTY  is for the administration of 5 domains. \nThe target group is medium-sized Internet companies who want to maintain their website dynamically. \n webEdition FIVE cms,webEdition webEdition for five domains webEdition for five domains webEdition FIVE','/we_demo/shop/items',165,',16,',0,4,'webEdition FIVE','webEdition for five domains','/we_demo/shop/items/webedition_five.php');
INSERT INTO tblIndex VALUES (171,'1.299,00 cms,webEdition webEdition for twenty domains webEdition TWENTY 5 webEdition TWENTY   webEdition TWENTY is for the administration of 20 domains. \nThe target group is medium-sized Internet companies who want to maintain their website dynamically.\n webEdition for twenty domains',0,0,'1.299,00 cms,webEdition webEdition for twenty domains webEdition TWENTY 5 webEdition TWENTY   webEdition TWENTY is for the administration of 20 domains. \nThe target group is medium-sized Internet companies who want to maintain their website dynamically.\n webEdition for twenty domains','/we_demo/shop/items',165,',16,',0,4,'webEdition TWENTY','webEdition for twenty domains','/we_demo/shop/items/webedition_twenty.php');
INSERT INTO tblIndex VALUES (172,'cms,webEdition   4299,00 webEdition TWENTY with User module and Scheduler  webEdition TWENTY Bundle User module/Scheduler webEdition TWENTY with User module and Scheduler for 20 domains 7 webEdition TWENTY Bundle User module./Schedulder webEdition TWENTY  is for the administration of 20 domains. \nThe target group is medium-sized Internet companies who want to\nmaintain their website dynamically. This bundle contains User module\nand Scheduler for 20 domains.',0,0,'cms,webEdition   4299,00 webEdition TWENTY with User module and Scheduler  webEdition TWENTY Bundle User module/Scheduler webEdition TWENTY with User module and Scheduler for 20 domains 7 webEdition TWENTY Bundle User module./Schedulder webEdition TWENTY  is for the administration of 20 domains. \nThe target group is medium-sized Internet companies who want to\nmaintain their website dynamically. This bundle contains User module\nand Scheduler for 20 domains.','/we_demo/shop/items',165,',16,',0,4,'webEdition TWENTY Bundle User module/Scheduler','','/we_demo/shop/items/webedition_bundle.php');
INSERT INTO tblIndex VALUES (0,'Why CMS? Christian Schulmeyer explains the advantage of using content management systems. 1033030800 2 Duration:2 HoursSeats: 100Fee: free Entry Why CMS? Christian Schulmeyer explains the advantage of using content management systems. webEdition Software GmbH Waldstra�e 40b 76133 Karlsruhe 0721 / 2018810 info@webedition.de http://www.webedition.de',0,12,'Why CMS? Christian Schulmeyer explains the advantage of using content management systems. 1033030800 2 Duration:2 HoursSeats: 100Fee: free Entry Why CMS? Christian Schulmeyer explains the advantage of using content management systems. webEdition Software GmbH Waldstra�e 40b 76133 Karlsruhe 0721 / 2018810 info@webedition.de http://www.webedition.de','/we_demo/events',306,'',2,0,'Why CMS?','Christian Schulmeyer explains the advantage of using content management systems.','2002_08_24_12');
INSERT INTO tblIndex VALUES (0,'Workshop for webEdition developer This workshop is targeted to developer with knowledge and experience in HTML, DHTML, Javascript, PHP4 and Database Technologies 1036659600 6 Duration: 2 DaysNo. Attendees: 10Fee: 400.00 Euro / Attendee Workshop for webEdition developer This workshop is targeted to developer with knowledge and experience in HTML, DHTML, Javascript, PHP4 and Database Technologies webEdition Hall  webEdition Street 120 56785 WebEdition 0190 / 346782346 info@webEdition.de http://www.webedition.de',0,13,'Workshop for webEdition developer This workshop is targeted to developer with knowledge and experience in HTML, DHTML, Javascript, PHP4 and Database Technologies 1036659600 6 Duration: 2 DaysNo. Attendees: 10Fee: 400.00 Euro / Attendee Workshop for webEdition developer This workshop is targeted to developer with knowledge and experience in HTML, DHTML, Javascript, PHP4 and Database Technologies webEdition Hall  webEdition Street 120 56785 WebEdition 0190 / 346782346 info@webEdition.de http://www.webedition.de','/we_demo/events',306,'',2,0,'Workshop for webEdition developer','This workshop is targeted to developer with knowledge and experience in HTML, DHTML, Javascript, PHP4 and Database Technologies','2002_08_24_13');
INSERT INTO tblIndex VALUES (0,'PHP Basics Basic developing in PHP. You will learn how to develop interactive Websites on you own based on PHP. 1033286400 7 Fee: 350.00 Euro / AttendeeDuration: 1 Day Fightclub  242 Fifth Fist 67857 PLeaseme 00989 / 37489234 fist@fightclub.com http://fightclub.com  /addresses/Fightclub',0,14,'PHP Basics Basic developing in PHP. You will learn how to develop interactive Websites on you own based on PHP. 1033286400 7 Fee: 350.00 Euro / AttendeeDuration: 1 Day Fightclub  242 Fifth Fist 67857 PLeaseme 00989 / 37489234 fist@fightclub.com http://fightclub.com  /addresses/Fightclub','/we_demo/events',306,'',2,0,'','','2002_08_24_14');
INSERT INTO tblIndex VALUES (0,'\"El beso de la tierra\" - Video in Spanish w/o subtitles Regie: Lucinda Torre, Spain\nAt 8.00 pm: Welcome and introduction\nAt 8.30 pm: Presentation of the movie in spanish without subtitles. 1033149600 5  \"El beso de la tierra\" - Video in Spanish w/o subtitles Regie: Lucinda Torre, Spain\nAt 8.00 pm: Welcome and introduction\nAt 8.30 pm: Presentation of the movie in spanish without subtitles. Arena di Maggio  Via Arena 12 45464 Magicten 009876 / 4389334 info@arenadimaggio.com http://www.arenadimaggio.com',0,15,'\"El beso de la tierra\" - Video in Spanish w/o subtitles Regie: Lucinda Torre, Spain\nAt 8.00 pm: Welcome and introduction\nAt 8.30 pm: Presentation of the movie in spanish without subtitles. 1033149600 5  \"El beso de la tierra\" - Video in Spanish w/o subtitles Regie: Lucinda Torre, Spain\nAt 8.00 pm: Welcome and introduction\nAt 8.30 pm: Presentation of the movie in spanish without subtitles. Arena di Maggio  Via Arena 12 45464 Magicten 009876 / 4389334 info@arenadimaggio.com http://www.arenadimaggio.com','/we_demo/events',306,'',2,0,'\"El beso de la tierra\" - Video in Spanish w/o subtitles','Regie: Lucinda Torre, Spain\nAt 8.00 pm: Welcome and introduction\nAt 8.30 pm: Presentation of the movie in spanish without subtitles.','2002_08_24_15');
INSERT INTO tblIndex VALUES (0,'Web of Life Exhibition \"intermedium2\" featuring the presentation of the multimedia project \"Web of Life\".  1031061600 3  Web of Life Exhibition \"intermedium2\" featuring the presentation of the multimedia project \"Web of Life\".  CMS-Hall  CMS-Street 111 12345 CMS-City 01212 / 38934 cms@cms.cms http://www.cms.cms',0,16,'Web of Life Exhibition \"intermedium2\" featuring the presentation of the multimedia project \"Web of Life\".  1031061600 3  Web of Life Exhibition \"intermedium2\" featuring the presentation of the multimedia project \"Web of Life\".  CMS-Hall  CMS-Street 111 12345 CMS-City 01212 / 38934 cms@cms.cms http://www.cms.cms','/we_demo/events',306,'',2,0,'Web of Life','Exhibition \"intermedium2\" featuring the presentation of the multimedia project \"Web of Life\". ','2002_08_24_16');

#
# Table structure for table 'tblLink'
#

CREATE TABLE tblLink (
  DID int(11) NOT NULL default '0',
  CID int(11) NOT NULL default '0',
  Type varchar(16) NOT NULL default '0',
  Name varchar(255) NOT NULL default '',
  DocumentTable varchar(64) NOT NULL default 'tblFile',
  KEY DID (DID)
) TYPE=MyISAM;

#
# Dumping data for table 'tblLink'
#

INSERT INTO tblLink VALUES (98,7050,'txt','Title','tblFile');
INSERT INTO tblLink VALUES (30,7572,'txt','data','tblTemplates');
INSERT INTO tblLink VALUES (39,7575,'txt','data','tblTemplates');
INSERT INTO tblLink VALUES (38,7574,'txt','data','tblTemplates');
INSERT INTO tblLink VALUES (111,7169,'txt','Title','tblFile');
INSERT INTO tblLink VALUES (29,7571,'txt','data','tblTemplates');
INSERT INTO tblLink VALUES (138,7207,'txt','LinkType','tblFile');
INSERT INTO tblLink VALUES (111,7168,'txt','Beschreibung_10','tblFile');
INSERT INTO tblLink VALUES (138,7206,'image','data','tblFile');
INSERT INTO tblLink VALUES (138,7205,'attrib','width','tblFile');
INSERT INTO tblLink VALUES (36,7573,'txt','data','tblTemplates');
INSERT INTO tblLink VALUES (24,7568,'txt','data','tblTemplates');
INSERT INTO tblLink VALUES (89,7189,'txt','data','tblFile');
INSERT INTO tblLink VALUES (26,7569,'txt','data','tblTemplates');
INSERT INTO tblLink VALUES (27,7570,'txt','data','tblTemplates');
INSERT INTO tblLink VALUES (134,7283,'txt','LinkType','tblFile');
INSERT INTO tblLink VALUES (134,7282,'image','data','tblFile');
INSERT INTO tblLink VALUES (134,7281,'attrib','width','tblFile');
INSERT INTO tblLink VALUES (111,7167,'txt','Beschreibung_12','tblFile');
INSERT INTO tblLink VALUES (111,7166,'txt','Sendung_12','tblFile');
INSERT INTO tblLink VALUES (134,7280,'attrib','height','tblFile');
INSERT INTO tblLink VALUES (134,7279,'attrib','type','tblFile');
INSERT INTO tblLink VALUES (138,7204,'attrib','height','tblFile');
INSERT INTO tblLink VALUES (138,7203,'attrib','type','tblFile');
INSERT INTO tblLink VALUES (137,7305,'txt','LinkType','tblFile');
INSERT INTO tblLink VALUES (133,7275,'txt','RollOverFlag','tblFile');
INSERT INTO tblLink VALUES (96,7024,'txt','Headline','tblFile');
INSERT INTO tblLink VALUES (136,7297,'txt','RollOverFlag','tblFile');
INSERT INTO tblLink VALUES (138,7202,'attrib','filesize','tblFile');
INSERT INTO tblLink VALUES (140,7249,'txt','RollOverFlag','tblFile');
INSERT INTO tblLink VALUES (40,7576,'txt','data','tblTemplates');
INSERT INTO tblLink VALUES (90,7193,'txt','we_nxcjidshf','tblFile');
INSERT INTO tblLink VALUES (41,7577,'txt','data','tblTemplates');
INSERT INTO tblLink VALUES (42,7578,'txt','data','tblTemplates');
INSERT INTO tblLink VALUES (300,7363,'txt','RollOverFlag','tblFile');
INSERT INTO tblLink VALUES (300,7362,'txt','LinkType','tblFile');
INSERT INTO tblLink VALUES (300,7361,'attrib','filesize','tblFile');
INSERT INTO tblLink VALUES (300,7360,'attrib','type','tblFile');
INSERT INTO tblLink VALUES (43,7579,'txt','data','tblTemplates');
INSERT INTO tblLink VALUES (44,7580,'txt','data','tblTemplates');
INSERT INTO tblLink VALUES (149,7325,'txt','we_nxcjidshf','tblFile');
INSERT INTO tblLink VALUES (152,7333,'image','data','tblFile');
INSERT INTO tblLink VALUES (170,7476,'txt','shopdescription','tblFile');
INSERT INTO tblLink VALUES (45,7581,'txt','data','tblTemplates');
INSERT INTO tblLink VALUES (48,7582,'txt','data','tblTemplates');
INSERT INTO tblLink VALUES (49,7583,'txt','data','tblTemplates');
INSERT INTO tblLink VALUES (299,7354,'attrib','height','tblFile');
INSERT INTO tblLink VALUES (299,7353,'image','data','tblFile');
INSERT INTO tblLink VALUES (299,7352,'attrib','filesize','tblFile');
INSERT INTO tblLink VALUES (173,7526,'txt','Description','tblFile');
INSERT INTO tblLink VALUES (56,7584,'txt','data','tblTemplates');
INSERT INTO tblLink VALUES (152,7332,'attrib','type','tblFile');
INSERT INTO tblLink VALUES (299,7351,'txt','LinkType','tblFile');
INSERT INTO tblLink VALUES (172,7517,'img','Bild','tblFile');
INSERT INTO tblLink VALUES (167,7412,'txt','shoptitle','tblFile');
INSERT INTO tblLink VALUES (172,7516,'txt','shoptitle','tblFile');
INSERT INTO tblLink VALUES (171,7496,'txt','we_nxcjidshf','tblFile');
INSERT INTO tblLink VALUES (299,7355,'attrib','width','tblFile');
INSERT INTO tblLink VALUES (301,7329,'txt','we_nxcjidshf','tblFile');
INSERT INTO tblLink VALUES (301,7328,'linklist','Linklist','tblFile');
INSERT INTO tblLink VALUES (166,7389,'txt','shopdescription','tblFile');
INSERT INTO tblLink VALUES (156,7342,'txt','Keywords','tblFile');
INSERT INTO tblLink VALUES (172,7515,'txt','Ordnung','tblFile');
INSERT INTO tblLink VALUES (96,7023,'txt','Keywords','tblFile');
INSERT INTO tblLink VALUES (97,7042,'txt','Text','tblFile');
INSERT INTO tblLink VALUES (96,7022,'txt','Title','tblFile');
INSERT INTO tblLink VALUES (300,7359,'attrib','height','tblFile');
INSERT INTO tblLink VALUES (133,7274,'image','data','tblFile');
INSERT INTO tblLink VALUES (140,7248,'image','data','tblFile');
INSERT INTO tblLink VALUES (169,7601,'txt','we_nxcjidshf','tblFile');
INSERT INTO tblLink VALUES (136,7296,'image','data','tblFile');
INSERT INTO tblLink VALUES (137,7304,'image','data','tblFile');
INSERT INTO tblLink VALUES (111,7165,'txt','Beschreibung_11','tblFile');
INSERT INTO tblLink VALUES (111,7164,'date','Date_12','tblFile');
INSERT INTO tblLink VALUES (114,7201,'txt','Description','tblFile');
INSERT INTO tblLink VALUES (169,7600,'txt','Keywords','tblFile');
INSERT INTO tblLink VALUES (169,7599,'txt','Preis','tblFile');
INSERT INTO tblLink VALUES (166,7388,'txt','Title','tblFile');
INSERT INTO tblLink VALUES (168,7433,'txt','Preis','tblFile');
INSERT INTO tblLink VALUES (113,7188,'txt','we_nxcjidshf','tblFile');
INSERT INTO tblLink VALUES (118,7234,'txt','Description','tblFile');
INSERT INTO tblLink VALUES (168,7432,'txt','shopdescription','tblFile');
INSERT INTO tblLink VALUES (148,7323,'txt','Title','tblFile');
INSERT INTO tblLink VALUES (145,7314,'txt','Keywords','tblFile');
INSERT INTO tblLink VALUES (157,7348,'txt','Keywords','tblFile');
INSERT INTO tblLink VALUES (95,7007,'txt','Headline','tblFile');
INSERT INTO tblLink VALUES (104,7078,'txt','we_nxcjidshf','tblFile');
INSERT INTO tblLink VALUES (117,7221,'img','Bild','tblFile');
INSERT INTO tblLink VALUES (96,7021,'date','Date','tblFile');
INSERT INTO tblLink VALUES (97,7041,'txt','Headline','tblFile');
INSERT INTO tblLink VALUES (95,7006,'txt','Description','tblFile');
INSERT INTO tblLink VALUES (170,7475,'txt','Keywords','tblFile');
INSERT INTO tblLink VALUES (171,7495,'txt','Artikelname','tblFile');
INSERT INTO tblLink VALUES (167,7411,'txt','Artikelname','tblFile');
INSERT INTO tblLink VALUES (303,7530,'attrib','height','tblFile');
INSERT INTO tblLink VALUES (303,7529,'attrib','type','tblFile');
INSERT INTO tblLink VALUES (304,7535,'attrib','width','tblFile');
INSERT INTO tblLink VALUES (305,7540,'attrib','width','tblFile');
INSERT INTO tblLink VALUES (62,7586,'txt','data','tblTemplates');
INSERT INTO tblLink VALUES (60,7585,'txt','data','tblTemplates');
INSERT INTO tblLink VALUES (63,7587,'txt','data','tblTemplates');
INSERT INTO tblLink VALUES (105,7086,'txt','we_nxcjidshf','tblFile');
INSERT INTO tblLink VALUES (95,7005,'date','Date','tblFile');
INSERT INTO tblLink VALUES (96,7020,'img','Bild','tblFile');
INSERT INTO tblLink VALUES (111,7163,'txt','we_nxcjidshf','tblFile');
INSERT INTO tblLink VALUES (104,7077,'txt','Title','tblFile');
INSERT INTO tblLink VALUES (104,7076,'txt','Text','tblFile');
INSERT INTO tblLink VALUES (111,7162,'date','Date_13','tblFile');
INSERT INTO tblLink VALUES (90,7192,'linklist','Linklist','tblFile');
INSERT INTO tblLink VALUES (117,7220,'txt','we_nxcjidshf','tblFile');
INSERT INTO tblLink VALUES (117,7219,'txt','Description','tblFile');
INSERT INTO tblLink VALUES (118,7233,'img','Bild','tblFile');
INSERT INTO tblLink VALUES (139,7241,'attrib','filesize','tblFile');
INSERT INTO tblLink VALUES (134,7278,'attrib','filesize','tblFile');
INSERT INTO tblLink VALUES (135,7289,'attrib','filesize','tblFile');
INSERT INTO tblLink VALUES (137,7303,'attrib','height','tblFile');
INSERT INTO tblLink VALUES (128,7267,'txt','Text','tblFile');
INSERT INTO tblLink VALUES (95,7003,'img','Bild','tblFile');
INSERT INTO tblLink VALUES (97,7040,'img','Bild','tblFile');
INSERT INTO tblLink VALUES (97,7039,'txt','we_nxcjidshf','tblFile');
INSERT INTO tblLink VALUES (103,7062,'txt','Keywords','tblFile');
INSERT INTO tblLink VALUES (104,7075,'txt','Filmtitel','tblFile');
INSERT INTO tblLink VALUES (104,7074,'txt','Description','tblFile');
INSERT INTO tblLink VALUES (111,7161,'txt','Beschreibung_3','tblFile');
INSERT INTO tblLink VALUES (111,7160,'txt','Sendung_3','tblFile');
INSERT INTO tblLink VALUES (111,7159,'txt','Beschreibung_2','tblFile');
INSERT INTO tblLink VALUES (111,7157,'txt','Sendung_2','tblFile');
INSERT INTO tblLink VALUES (113,7186,'txt','Keywords','tblFile');
INSERT INTO tblLink VALUES (113,7187,'txt','Title','tblFile');
INSERT INTO tblLink VALUES (118,7232,'txt','Title','tblFile');
INSERT INTO tblLink VALUES (139,7240,'attrib','type','tblFile');
INSERT INTO tblLink VALUES (140,7247,'txt','LinkType','tblFile');
INSERT INTO tblLink VALUES (128,7266,'txt','Bildunterschrift','tblFile');
INSERT INTO tblLink VALUES (133,7273,'txt','LinkType','tblFile');
INSERT INTO tblLink VALUES (135,7288,'attrib','type','tblFile');
INSERT INTO tblLink VALUES (136,7295,'txt','LinkType','tblFile');
INSERT INTO tblLink VALUES (299,7356,'attrib','type','tblFile');
INSERT INTO tblLink VALUES (157,7347,'txt','Title','tblFile');
INSERT INTO tblLink VALUES (166,7387,'txt','Text','tblFile');
INSERT INTO tblLink VALUES (166,7386,'txt','shoptitle','tblFile');
INSERT INTO tblLink VALUES (167,7410,'txt','shopdescription','tblFile');
INSERT INTO tblLink VALUES (167,7409,'txt','we_nxcjidshf','tblFile');
INSERT INTO tblLink VALUES (168,7429,'txt','Keywords','tblFile');
INSERT INTO tblLink VALUES (169,7598,'img','Bild','tblFile');
INSERT INTO tblLink VALUES (170,7474,'txt','shoptitle','tblFile');
INSERT INTO tblLink VALUES (170,7473,'img','Bild','tblFile');
INSERT INTO tblLink VALUES (170,7472,'txt','Text','tblFile');
INSERT INTO tblLink VALUES (171,7494,'txt','Ordnung','tblFile');
INSERT INTO tblLink VALUES (172,7514,'txt','shopdescription','tblFile');
INSERT INTO tblLink VALUES (303,7531,'attrib','width','tblFile');
INSERT INTO tblLink VALUES (304,7534,'attrib','height','tblFile');
INSERT INTO tblLink VALUES (305,7539,'attrib','height','tblFile');
INSERT INTO tblLink VALUES (66,7588,'txt','data','tblTemplates');
INSERT INTO tblLink VALUES (67,7589,'txt','data','tblTemplates');
INSERT INTO tblLink VALUES (68,7590,'txt','data','tblTemplates');
INSERT INTO tblLink VALUES (312,7543,'txt','text','tblFile');
INSERT INTO tblLink VALUES (111,7158,'date','Date_3','tblFile');
INSERT INTO tblLink VALUES (95,7004,'txt','Title','tblFile');
INSERT INTO tblLink VALUES (96,7019,'txt','Text','tblFile');
INSERT INTO tblLink VALUES (97,7037,'txt','Title','tblFile');
INSERT INTO tblLink VALUES (98,7049,'txt','we_nxcjidshf','tblFile');
INSERT INTO tblLink VALUES (103,7061,'txt','Title','tblFile');
INSERT INTO tblLink VALUES (105,7085,'txt','Description','tblFile');
INSERT INTO tblLink VALUES (111,7156,'txt','Sendung_13','tblFile');
INSERT INTO tblLink VALUES (111,7155,'date','Date_2','tblFile');
INSERT INTO tblLink VALUES (111,7154,'txt','Sendung_1','tblFile');
INSERT INTO tblLink VALUES (111,7153,'txt','Beschreibung_1','tblFile');
INSERT INTO tblLink VALUES (111,7152,'list','Programmliste','tblFile');
INSERT INTO tblLink VALUES (113,7185,'txt','ErklaerungLinkliste_TAGS_1','tblFile');
INSERT INTO tblLink VALUES (113,7184,'linklist','Linkliste','tblFile');
INSERT INTO tblLink VALUES (114,7200,'txt','Title','tblFile');
INSERT INTO tblLink VALUES (117,7218,'txt','Keywords','tblFile');
INSERT INTO tblLink VALUES (117,7216,'txt','Title','tblFile');
INSERT INTO tblLink VALUES (118,7231,'txt','Keywords','tblFile');
INSERT INTO tblLink VALUES (139,7239,'attrib','height','tblFile');
INSERT INTO tblLink VALUES (139,7238,'attrib','width','tblFile');
INSERT INTO tblLink VALUES (140,7246,'attrib','filesize','tblFile');
INSERT INTO tblLink VALUES (133,7272,'attrib','filesize','tblFile');
INSERT INTO tblLink VALUES (135,7287,'attrib','height','tblFile');
INSERT INTO tblLink VALUES (135,7286,'attrib','width','tblFile');
INSERT INTO tblLink VALUES (136,7294,'attrib','filesize','tblFile');
INSERT INTO tblLink VALUES (137,7302,'attrib','width','tblFile');
INSERT INTO tblLink VALUES (137,7301,'attrib','type','tblFile');
INSERT INTO tblLink VALUES (152,7331,'attrib','height','tblFile');
INSERT INTO tblLink VALUES (156,7341,'txt','we_nxcjidshf','tblFile');
INSERT INTO tblLink VALUES (299,7350,'txt','RollOverFlag','tblFile');
INSERT INTO tblLink VALUES (300,7358,'attrib','width','tblFile');
INSERT INTO tblLink VALUES (167,7408,'txt','Description','tblFile');
INSERT INTO tblLink VALUES (167,7407,'txt','Title','tblFile');
INSERT INTO tblLink VALUES (168,7430,'img','Bild','tblFile');
INSERT INTO tblLink VALUES (169,7597,'txt','Title','tblFile');
INSERT INTO tblLink VALUES (169,7596,'txt','Text','tblFile');
INSERT INTO tblLink VALUES (171,7493,'txt','Title','tblFile');
INSERT INTO tblLink VALUES (173,7525,'txt','we_nxcjidshf','tblFile');
INSERT INTO tblLink VALUES (303,7528,'image','data','tblFile');
INSERT INTO tblLink VALUES (304,7533,'attrib','type','tblFile');
INSERT INTO tblLink VALUES (305,7538,'attrib','type','tblFile');
INSERT INTO tblLink VALUES (128,7264,'txt','Keywords','tblFile');
INSERT INTO tblLink VALUES (111,7151,'date','Date_4','tblFile');
INSERT INTO tblLink VALUES (148,7322,'txt','we_nxcjidshf','tblFile');
INSERT INTO tblLink VALUES (172,7513,'txt','Title','tblFile');
INSERT INTO tblLink VALUES (166,7385,'txt','Preis','tblFile');
INSERT INTO tblLink VALUES (168,7428,'txt','Description','tblFile');
INSERT INTO tblLink VALUES (69,7591,'txt','data','tblTemplates');
INSERT INTO tblLink VALUES (95,7002,'txt','Bildunterschrift','tblFile');
INSERT INTO tblLink VALUES (96,7018,'txt','we_nxcjidshf','tblFile');
INSERT INTO tblLink VALUES (97,7038,'date','Date','tblFile');
INSERT INTO tblLink VALUES (97,7036,'txt','Keywords','tblFile');
INSERT INTO tblLink VALUES (98,7048,'txt','Description','tblFile');
INSERT INTO tblLink VALUES (103,7060,'txt','we_nxcjidshf','tblFile');
INSERT INTO tblLink VALUES (103,7059,'txt','Text','tblFile');
INSERT INTO tblLink VALUES (104,7073,'txt','Keywords','tblFile');
INSERT INTO tblLink VALUES (105,7084,'txt','Title','tblFile');
INSERT INTO tblLink VALUES (111,7150,'date','Date_1','tblFile');
INSERT INTO tblLink VALUES (111,7149,'txt','Sendung_4','tblFile');
INSERT INTO tblLink VALUES (111,7148,'txt','Beschreibung_4','tblFile');
INSERT INTO tblLink VALUES (111,7147,'date','Date_5','tblFile');
INSERT INTO tblLink VALUES (111,7146,'txt','Sendung_5','tblFile');
INSERT INTO tblLink VALUES (111,7145,'txt','Sendung_6','tblFile');
INSERT INTO tblLink VALUES (111,7144,'date','Date_6','tblFile');
INSERT INTO tblLink VALUES (111,7143,'txt','Beschreibung_5','tblFile');
INSERT INTO tblLink VALUES (111,7142,'date','Date_7','tblFile');
INSERT INTO tblLink VALUES (111,7141,'txt','Sendung_7','tblFile');
INSERT INTO tblLink VALUES (111,7140,'txt','Beschreibung_6','tblFile');
INSERT INTO tblLink VALUES (114,7199,'txt','we_nxcjidshf','tblFile');
INSERT INTO tblLink VALUES (117,7217,'txt','Text','tblFile');
INSERT INTO tblLink VALUES (118,7230,'txt','we_nxcjidshf','tblFile');
INSERT INTO tblLink VALUES (139,7237,'image','data','tblFile');
INSERT INTO tblLink VALUES (139,7236,'txt','LinkType','tblFile');
INSERT INTO tblLink VALUES (140,7245,'attrib','type','tblFile');
INSERT INTO tblLink VALUES (140,7244,'attrib','height','tblFile');
INSERT INTO tblLink VALUES (128,7265,'txt','Headline','tblFile');
INSERT INTO tblLink VALUES (128,7263,'txt','Title','tblFile');
INSERT INTO tblLink VALUES (133,7271,'attrib','type','tblFile');
INSERT INTO tblLink VALUES (133,7270,'attrib','height','tblFile');
INSERT INTO tblLink VALUES (135,7285,'image','data','tblFile');
INSERT INTO tblLink VALUES (135,7284,'txt','LinkType','tblFile');
INSERT INTO tblLink VALUES (136,7293,'attrib','type','tblFile');
INSERT INTO tblLink VALUES (136,7292,'attrib','height','tblFile');
INSERT INTO tblLink VALUES (137,7300,'txt','RollOverFlag','tblFile');
INSERT INTO tblLink VALUES (145,7313,'txt','we_nxcjidshf','tblFile');
INSERT INTO tblLink VALUES (148,7321,'txt','Description','tblFile');
INSERT INTO tblLink VALUES (152,7330,'attrib','width','tblFile');
INSERT INTO tblLink VALUES (156,7340,'txt','Title','tblFile');
INSERT INTO tblLink VALUES (157,7346,'txt','Description','tblFile');
INSERT INTO tblLink VALUES (299,7349,'txt','RollOverID','tblFile');
INSERT INTO tblLink VALUES (300,7357,'image','data','tblFile');
INSERT INTO tblLink VALUES (162,7367,'txt','danke','tblFile');
INSERT INTO tblLink VALUES (166,7384,'txt','Description','tblFile');
INSERT INTO tblLink VALUES (166,7383,'txt','Keywords','tblFile');
INSERT INTO tblLink VALUES (167,7406,'txt','Preis','tblFile');
INSERT INTO tblLink VALUES (167,7405,'txt','Text','tblFile');
INSERT INTO tblLink VALUES (167,7404,'img','Bild','tblFile');
INSERT INTO tblLink VALUES (168,7427,'txt','shoptitle','tblFile');
INSERT INTO tblLink VALUES (168,7426,'txt','Ordnung','tblFile');
INSERT INTO tblLink VALUES (169,7595,'txt','Artikelname','tblFile');
INSERT INTO tblLink VALUES (170,7471,'txt','Ordnung','tblFile');
INSERT INTO tblLink VALUES (170,7470,'txt','we_nxcjidshf','tblFile');
INSERT INTO tblLink VALUES (171,7492,'img','Bild','tblFile');
INSERT INTO tblLink VALUES (171,7491,'txt','Description','tblFile');
INSERT INTO tblLink VALUES (172,7512,'txt','Artikelname','tblFile');
INSERT INTO tblLink VALUES (172,7511,'txt','Preis','tblFile');
INSERT INTO tblLink VALUES (173,7524,'txt','Title','tblFile');
INSERT INTO tblLink VALUES (303,7527,'attrib','filesize','tblFile');
INSERT INTO tblLink VALUES (304,7532,'image','data','tblFile');
INSERT INTO tblLink VALUES (305,7537,'image','data','tblFile');
INSERT INTO tblLink VALUES (95,7008,'txt','Keywords','tblFile');
INSERT INTO tblLink VALUES (95,7001,'txt','we_nxcjidshf','tblFile');
INSERT INTO tblLink VALUES (95,7000,'txt','Text','tblFile');
INSERT INTO tblLink VALUES (96,7017,'txt','Description','tblFile');
INSERT INTO tblLink VALUES (97,7035,'txt','Description','tblFile');
INSERT INTO tblLink VALUES (97,7034,'txt','Bildunterschrift','tblFile');
INSERT INTO tblLink VALUES (98,7047,'txt','Keywords','tblFile');
INSERT INTO tblLink VALUES (103,7058,'txt','Filmtitel','tblFile');
INSERT INTO tblLink VALUES (103,7063,'txt','Description','tblFile');
INSERT INTO tblLink VALUES (103,7064,'img','Bild','tblFile');
INSERT INTO tblLink VALUES (104,7072,'img','Bild','tblFile');
INSERT INTO tblLink VALUES (105,7083,'txt','Keywords','tblFile');
INSERT INTO tblLink VALUES (111,7139,'txt','Beschreibung_7','tblFile');
INSERT INTO tblLink VALUES (111,7138,'date','Date_8','tblFile');
INSERT INTO tblLink VALUES (111,7137,'txt','Beschreibung_8','tblFile');
INSERT INTO tblLink VALUES (111,7136,'txt','Sendung_8','tblFile');
INSERT INTO tblLink VALUES (111,7135,'date','Date_9','tblFile');
INSERT INTO tblLink VALUES (111,7134,'txt','Beschreibung_9','tblFile');
INSERT INTO tblLink VALUES (111,7133,'txt','Sendung_9','tblFile');
INSERT INTO tblLink VALUES (111,7132,'date','Date_10','tblFile');
INSERT INTO tblLink VALUES (111,7131,'date','Date_11','tblFile');
INSERT INTO tblLink VALUES (111,7170,'txt','Sendung_11','tblFile');
INSERT INTO tblLink VALUES (111,7171,'txt','Beschreibung_13','tblFile');
INSERT INTO tblLink VALUES (111,7172,'txt','Sendung_10','tblFile');
INSERT INTO tblLink VALUES (111,7173,'txt','Description','tblFile');
INSERT INTO tblLink VALUES (111,7174,'txt','Keywords','tblFile');
INSERT INTO tblLink VALUES (113,7183,'txt','ErklaerungLinkliste_TAGS_0','tblFile');
INSERT INTO tblLink VALUES (113,7182,'txt','Description','tblFile');
INSERT INTO tblLink VALUES (114,7198,'txt','Keywords','tblFile');
INSERT INTO tblLink VALUES (117,7215,'txt','Filmtitel','tblFile');
INSERT INTO tblLink VALUES (118,7229,'txt','Filmtitel','tblFile');
INSERT INTO tblLink VALUES (118,7235,'txt','Text','tblFile');
INSERT INTO tblLink VALUES (139,7242,'txt','RollOverFlag','tblFile');
INSERT INTO tblLink VALUES (139,7243,'txt','RollOverID','tblFile');
INSERT INTO tblLink VALUES (140,7250,'attrib','width','tblFile');
INSERT INTO tblLink VALUES (140,7251,'txt','RollOverID','tblFile');
INSERT INTO tblLink VALUES (128,7268,'date','Date','tblFile');
INSERT INTO tblLink VALUES (128,7262,'txt','Description','tblFile');
INSERT INTO tblLink VALUES (128,7261,'txt','we_nxcjidshf','tblFile');
INSERT INTO tblLink VALUES (128,7269,'img','Bild','tblFile');
INSERT INTO tblLink VALUES (133,7276,'attrib','width','tblFile');
INSERT INTO tblLink VALUES (133,7277,'txt','RollOverID','tblFile');
INSERT INTO tblLink VALUES (135,7290,'txt','RollOverFlag','tblFile');
INSERT INTO tblLink VALUES (135,7291,'txt','RollOverID','tblFile');
INSERT INTO tblLink VALUES (136,7298,'attrib','width','tblFile');
INSERT INTO tblLink VALUES (136,7299,'txt','RollOverID','tblFile');
INSERT INTO tblLink VALUES (137,7306,'attrib','filesize','tblFile');
INSERT INTO tblLink VALUES (137,7307,'txt','RollOverID','tblFile');
INSERT INTO tblLink VALUES (145,7312,'txt','Description','tblFile');
INSERT INTO tblLink VALUES (145,7315,'txt','Title','tblFile');
INSERT INTO tblLink VALUES (148,7320,'txt','Keywords','tblFile');
INSERT INTO tblLink VALUES (152,7334,'attrib','filesize','tblFile');
INSERT INTO tblLink VALUES (156,7339,'txt','Description','tblFile');
INSERT INTO tblLink VALUES (300,7364,'txt','RollOverID','tblFile');
INSERT INTO tblLink VALUES (162,7368,'txt','we_nxcjidshf','tblFile');
INSERT INTO tblLink VALUES (166,7382,'txt','we_nxcjidshf','tblFile');
INSERT INTO tblLink VALUES (166,7381,'txt','Ordnung','tblFile');
INSERT INTO tblLink VALUES (166,7380,'txt','Artikelname','tblFile');
INSERT INTO tblLink VALUES (166,7390,'img','Bild','tblFile');
INSERT INTO tblLink VALUES (167,7403,'txt','Keywords','tblFile');
INSERT INTO tblLink VALUES (167,7402,'txt','Ordnung','tblFile');
INSERT INTO tblLink VALUES (168,7425,'txt','Text','tblFile');
INSERT INTO tblLink VALUES (168,7424,'txt','Artikelname','tblFile');
INSERT INTO tblLink VALUES (168,7434,'txt','Title','tblFile');
INSERT INTO tblLink VALUES (169,7594,'txt','shoptitle','tblFile');
INSERT INTO tblLink VALUES (169,7593,'txt','Description','tblFile');
INSERT INTO tblLink VALUES (169,7592,'txt','Ordnung','tblFile');
INSERT INTO tblLink VALUES (170,7469,'txt','Title','tblFile');
INSERT INTO tblLink VALUES (170,7468,'txt','Preis','tblFile');
INSERT INTO tblLink VALUES (170,7477,'txt','Description','tblFile');
INSERT INTO tblLink VALUES (170,7478,'txt','Artikelname','tblFile');
INSERT INTO tblLink VALUES (171,7490,'txt','Keywords','tblFile');
INSERT INTO tblLink VALUES (171,7489,'txt','Preis','tblFile');
INSERT INTO tblLink VALUES (171,7497,'txt','Text','tblFile');
INSERT INTO tblLink VALUES (171,7498,'txt','shopdescription','tblFile');
INSERT INTO tblLink VALUES (172,7510,'txt','we_nxcjidshf','tblFile');
INSERT INTO tblLink VALUES (172,7509,'txt','Keywords','tblFile');
INSERT INTO tblLink VALUES (172,7518,'txt','Text','tblFile');
INSERT INTO tblLink VALUES (173,7523,'txt','Keywords','tblFile');
INSERT INTO tblLink VALUES (304,7536,'attrib','filesize','tblFile');
INSERT INTO tblLink VALUES (305,7541,'attrib','filesize','tblFile');
INSERT INTO tblLink VALUES (169,7602,'txt','shopdescription','tblFile');

#
# Table structure for table 'tblLock'
#

CREATE TABLE tblLock (
  ID bigint(20) NOT NULL default '0',
  UserID bigint(20) NOT NULL default '0',
  tbl varchar(32) NOT NULL default ''
) TYPE=MyISAM;

#
# Dumping data for table 'tblLock'
#


#
# Table structure for table 'tblMessages'
#

CREATE TABLE tblMessages (
  ID int(11) NOT NULL auto_increment,
  ParentID int(11) default '0',
  UserID int(11) default NULL,
  msg_type tinyint(4) NOT NULL default '0',
  obj_type tinyint(4) NOT NULL default '0',
  headerDate int(11) default NULL,
  headerSubject varchar(255) default NULL,
  headerUserID int(11) default NULL,
  headerFrom varchar(255) default NULL,
  headerTo varchar(255) default NULL,
  Priority tinyint(4) default NULL,
  seenStatus tinyint(4) unsigned default '0',
  MessageText text,
  tag tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (ID),
  KEY ID (ID)
) TYPE=MyISAM;

#
# Dumping data for table 'tblMessages'
#


#
# Table structure for table 'tblMsgAccounts'
#

CREATE TABLE tblMsgAccounts (
  ID int(11) NOT NULL auto_increment,
  UserID int(11) default NULL,
  name varchar(255) NOT NULL default '',
  msg_type int(11) default NULL,
  deletable tinyint(4) NOT NULL default '1',
  uri varchar(255) default NULL,
  user varchar(255) default NULL,
  pass varchar(255) default NULL,
  update_interval smallint(5) unsigned NOT NULL default '0',
  ext varchar(255) default NULL,
  PRIMARY KEY  (ID)
) TYPE=MyISAM;

#
# Dumping data for table 'tblMsgAccounts'
#


#
# Table structure for table 'tblMsgAddrbook'
#

CREATE TABLE tblMsgAddrbook (
  ID int(11) NOT NULL auto_increment,
  UserID int(11) default NULL,
  strMsgType varchar(255) default NULL,
  strID varchar(255) default NULL,
  strAlias varchar(255) NOT NULL default '',
  strFirstname varchar(255) default NULL,
  strSurname varchar(255) default NULL,
  PRIMARY KEY  (ID)
) TYPE=MyISAM;

#
# Dumping data for table 'tblMsgAddrbook'
#


#
# Table structure for table 'tblMsgFolders'
#

CREATE TABLE tblMsgFolders (
  ID int(11) NOT NULL auto_increment,
  ParentID int(11) default '0',
  UserID int(11) NOT NULL default '0',
  account_id int(11) default '-1',
  msg_type tinyint(4) NOT NULL default '0',
  obj_type tinyint(4) NOT NULL default '0',
  Name varchar(255) NOT NULL default '',
  sortItem varchar(255) default NULL,
  sortOrder varchar(5) default NULL,
  Properties int(10) unsigned default '0',
  tag tinyint(4) default NULL,
  PRIMARY KEY  (ID)
) TYPE=MyISAM;

#
# Dumping data for table 'tblMsgFolders'
#

INSERT INTO tblMsgFolders VALUES (1,0,1,-1,1,3,'Messages',NULL,NULL,1,NULL);
INSERT INTO tblMsgFolders VALUES (2,1,1,-1,1,5,'Sent',NULL,NULL,1,NULL);
INSERT INTO tblMsgFolders VALUES (3,0,1,-1,2,3,'Task',NULL,NULL,1,NULL);
INSERT INTO tblMsgFolders VALUES (4,3,1,-1,2,13,'Done',NULL,NULL,1,NULL);
INSERT INTO tblMsgFolders VALUES (5,3,1,-1,2,11,'rejected',NULL,NULL,1,NULL);

#
# Table structure for table 'tblMsgSettings'
#

CREATE TABLE tblMsgSettings (
  ID int(11) NOT NULL auto_increment,
  UserID int(11) NOT NULL default '0',
  strKey varchar(255) default NULL,
  strVal varchar(255) default NULL,
  PRIMARY KEY  (ID)
) TYPE=MyISAM;

#
# Dumping data for table 'tblMsgSettings'
#


#
# Table structure for table 'tblNewsletter'
#

CREATE TABLE tblNewsletter (
  ID bigint(20) NOT NULL auto_increment,
  ParentID bigint(20) NOT NULL default '0',
  IsFolder tinyint(1) NOT NULL default '0',
  Icon varchar(255) NOT NULL default 'newsletter.gif',
  Path varchar(255) NOT NULL default '',
  Text varchar(255) NOT NULL default '',
  Subject varchar(255) NOT NULL default '',
  Sender varchar(255) NOT NULL default '',
  Reply varchar(255) NOT NULL default '',
  Test varchar(255) NOT NULL default '',
  Log text NOT NULL,
  Step int(11) NOT NULL default '0',
  Offset int(11) NOT NULL default '0',
  PRIMARY KEY  (ID)
) TYPE=MyISAM;

#
# Dumping data for table 'tblNewsletter'
#


#
# Table structure for table 'tblNewsletterBlock'
#

CREATE TABLE tblNewsletterBlock (
  ID bigint(20) NOT NULL auto_increment,
  NewsletterID bigint(20) NOT NULL default '0',
  Groups varchar(255) NOT NULL default '',
  Type tinyint(4) NOT NULL default '0',
  LinkID bigint(20) NOT NULL default '0',
  Field varchar(255) NOT NULL default '',
  Source longtext NOT NULL,
  Html longtext NOT NULL,
  Pack tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (ID)
) TYPE=MyISAM;

#
# Dumping data for table 'tblNewsletterBlock'
#


#
# Table structure for table 'tblNewsletterConfirm'
#

CREATE TABLE tblNewsletterConfirm (
  confirmID varchar(96) NOT NULL default '',
  subscribe_mail varchar(255) NOT NULL default '',
  subscribe_html tinyint(1) NOT NULL default '0',
  subscribe_salutation varchar(255) NOT NULL default '',
  subscribe_title varchar(255) NOT NULL default '',
  subscribe_firstname varchar(255) NOT NULL default '',
  subscribe_lastname varchar(255) NOT NULL default '',
  lists text NOT NULL,
  expires bigint(20) NOT NULL default '0'
) TYPE=MyISAM;

#
# Dumping data for table 'tblNewsletterConfirm'
#


#
# Table structure for table 'tblNewsletterGroup'
#

CREATE TABLE tblNewsletterGroup (
  ID bigint(20) NOT NULL auto_increment,
  NewsletterID bigint(20) NOT NULL default '0',
  Emails longtext NOT NULL,
  Customers longtext NOT NULL,
  SendAll tinyint(1) NOT NULL default '1',
  Filter blob NOT NULL,
  Extern longtext,
  PRIMARY KEY  (ID)
) TYPE=MyISAM;

#
# Dumping data for table 'tblNewsletterGroup'
#


#
# Table structure for table 'tblNewsletterLog'
#

CREATE TABLE tblNewsletterLog (
  ID bigint(20) NOT NULL auto_increment,
  NewsletterID bigint(20) NOT NULL default '0',
  LogTime bigint(20) NOT NULL default '0',
  Log varchar(255) NOT NULL default '',
  Param varchar(255) NOT NULL default '',
  PRIMARY KEY  (ID)
) TYPE=MyISAM;

#
# Dumping data for table 'tblNewsletterLog'
#


#
# Table structure for table 'tblNewsletterPrefs'
#

CREATE TABLE tblNewsletterPrefs (
  pref_name varchar(255) NOT NULL default '',
  pref_value longtext NOT NULL
) TYPE=MyISAM;

#
# Dumping data for table 'tblNewsletterPrefs'
#

INSERT INTO tblNewsletterPrefs VALUES ('reject_malformed','1');
INSERT INTO tblNewsletterPrefs VALUES ('send_step','150');
INSERT INTO tblNewsletterPrefs VALUES ('reject_not_verified','1');
INSERT INTO tblNewsletterPrefs VALUES ('test_account','test@meineDomain.de');
INSERT INTO tblNewsletterPrefs VALUES ('log_sending','1');
INSERT INTO tblNewsletterPrefs VALUES ('default_sender','mailer@meineDomain.de');
INSERT INTO tblNewsletterPrefs VALUES ('default_reply','reply@meineDomain.de');
INSERT INTO tblNewsletterPrefs VALUES ('customer_email_field','Kontakt_Email');
INSERT INTO tblNewsletterPrefs VALUES ('customer_html_field','htmlMailYesNo');
INSERT INTO tblNewsletterPrefs VALUES ('default_htmlmail','0');
INSERT INTO tblNewsletterPrefs VALUES ('customer_firstname_field','Forename');
INSERT INTO tblNewsletterPrefs VALUES ('customer_lastname_field','Surname');
INSERT INTO tblNewsletterPrefs VALUES ('customer_salutation_field','Anrede_Salutation');
INSERT INTO tblNewsletterPrefs VALUES ('female_salutation','Frau');
INSERT INTO tblNewsletterPrefs VALUES ('male_salutation','Herr');
INSERT INTO tblNewsletterPrefs VALUES ('customer_title_field','Anrede_Title');
INSERT INTO tblNewsletterPrefs VALUES ('black_list','');
INSERT INTO tblNewsletterPrefs VALUES ('title_or_salutation','0');
INSERT INTO tblNewsletterPrefs VALUES ('global_mailing_list','');
INSERT INTO tblNewsletterPrefs VALUES ('reject_save_malformed','1');
INSERT INTO tblNewsletterPrefs VALUES ('use_https_refer','0');
INSERT INTO tblNewsletterPrefs VALUES ('send_wait','0');

#
# Table structure for table 'tblObject'
#

CREATE TABLE tblObject (
  ID int(11) NOT NULL auto_increment,
  ParentID int(11) NOT NULL default '0',
  strOrder text NOT NULL,
  Text varchar(255) NOT NULL default '',
  Icon varchar(64) NOT NULL default '',
  IsFolder tinyint(4) NOT NULL default '0',
  ContentType varchar(32) NOT NULL default '0',
  CreationDate int(11) NOT NULL default '0',
  ModDate int(11) NOT NULL default '0',
  Path varchar(255) NOT NULL default '',
  CreatorID bigint(20) NOT NULL default '0',
  ModifierID bigint(20) NOT NULL default '0',
  RestrictOwners tinyint(1) NOT NULL default '0',
  Owners varchar(255) NOT NULL default '',
  OwnersReadOnly text NOT NULL,
  RestrictUsers tinyint(1) NOT NULL default '0',
  Users varchar(255) NOT NULL default '',
  UsersReadOnly text NOT NULL,
  DefaultCategory varchar(255) NOT NULL default '',
  DefaultParentID bigint(20) NOT NULL default '0',
  DefaultText varchar(255) NOT NULL default '',
  DefaultValues text NOT NULL,
  DefaultDesc varchar(255) NOT NULL default '',
  DefaultTitle varchar(255) NOT NULL default '',
  ClassName varchar(64) NOT NULL default '',
  Workspaces varchar(255) NOT NULL default '',
  Templates varchar(255) NOT NULL default '',
  PRIMARY KEY  (ID)
) TYPE=MyISAM;

#
# Dumping data for table 'tblObject'
#

INSERT INTO tblObject VALUES (1,0,'0,1,2,3,4,5,6,7','addresses','object.gif',0,'object',1030117297,1035907295,'/addresses',1,1,0,'','',0,'','','',0,'','a:9:{s:13:\"WorkspaceFlag\";i:1;s:11:\"input_Name1\";a:13:{s:7:\"default\";s:0:\"\";s:6:\"autobr\";N;s:9:\"dhtmledit\";N;s:9:\"showmenus\";N;s:10:\"forbidhtml\";N;s:9:\"forbidphp\";N;s:5:\"users\";N;s:8:\"required\";s:1:\"1\";s:3:\"int\";N;s:5:\"intID\";N;s:7:\"intPath\";N;s:8:\"uniqueID\";s:32:\"d2003e1cfa23d4fa65a7b65ee7367d9f\";s:4:\"meta\";a:1:{s:0:\"\";N;}}s:11:\"input_Name2\";a:13:{s:7:\"default\";s:0:\"\";s:6:\"autobr\";N;s:9:\"dhtmledit\";N;s:9:\"showmenus\";N;s:10:\"forbidhtml\";N;s:9:\"forbidphp\";N;s:5:\"users\";N;s:8:\"required\";s:0:\"\";s:3:\"int\";N;s:5:\"intID\";N;s:7:\"intPath\";N;s:8:\"uniqueID\";s:32:\"73d07087298e8831948873f8a588f110\";s:4:\"meta\";a:1:{s:0:\"\";N;}}s:12:\"input_Street\";a:13:{s:7:\"default\";s:0:\"\";s:6:\"autobr\";N;s:9:\"dhtmledit\";N;s:9:\"showmenus\";N;s:10:\"forbidhtml\";N;s:9:\"forbidphp\";N;s:5:\"users\";N;s:8:\"required\";s:1:\"1\";s:3:\"int\";N;s:5:\"intID\";N;s:7:\"intPath\";N;s:8:\"uniqueID\";s:32:\"9fc5e1a8fe0bb162481bc34fc5624c39\";s:4:\"meta\";a:1:{s:0:\"\";N;}}s:9:\"input_ZIP\";a:13:{s:7:\"default\";s:0:\"\";s:6:\"autobr\";N;s:9:\"dhtmledit\";N;s:9:\"showmenus\";N;s:10:\"forbidhtml\";N;s:9:\"forbidphp\";N;s:5:\"users\";N;s:8:\"required\";s:1:\"1\";s:3:\"int\";N;s:5:\"intID\";N;s:7:\"intPath\";N;s:8:\"uniqueID\";s:32:\"6c4b4b7afd6517da86f58e41228738fa\";s:4:\"meta\";a:1:{s:0:\"\";N;}}s:10:\"input_City\";a:13:{s:7:\"default\";s:0:\"\";s:6:\"autobr\";N;s:9:\"dhtmledit\";N;s:9:\"showmenus\";N;s:10:\"forbidhtml\";N;s:9:\"forbidphp\";N;s:5:\"users\";N;s:8:\"required\";s:1:\"1\";s:3:\"int\";N;s:5:\"intID\";N;s:7:\"intPath\";N;s:8:\"uniqueID\";s:32:\"b7bdc15ab62967b7e52e427124383856\";s:4:\"meta\";a:1:{s:0:\"\";N;}}s:11:\"input_Phone\";a:13:{s:7:\"default\";s:0:\"\";s:6:\"autobr\";N;s:9:\"dhtmledit\";N;s:9:\"showmenus\";N;s:10:\"forbidhtml\";N;s:9:\"forbidphp\";N;s:5:\"users\";N;s:8:\"required\";s:0:\"\";s:3:\"int\";N;s:5:\"intID\";N;s:7:\"intPath\";N;s:8:\"uniqueID\";s:32:\"f4dce7561924e87322742c903d770e80\";s:4:\"meta\";a:1:{s:0:\"\";N;}}s:11:\"input_Email\";a:13:{s:7:\"default\";s:0:\"\";s:6:\"autobr\";N;s:9:\"dhtmledit\";N;s:9:\"showmenus\";N;s:10:\"forbidhtml\";N;s:9:\"forbidphp\";N;s:5:\"users\";N;s:8:\"required\";s:0:\"\";s:3:\"int\";N;s:5:\"intID\";N;s:7:\"intPath\";N;s:8:\"uniqueID\";s:32:\"a5072724eac3b1eea80f15d9561b598d\";s:4:\"meta\";a:1:{s:0:\"\";N;}}s:9:\"input_URL\";a:13:{s:7:\"default\";s:0:\"\";s:6:\"autobr\";N;s:9:\"dhtmledit\";N;s:9:\"showmenus\";N;s:10:\"forbidhtml\";N;s:9:\"forbidphp\";N;s:5:\"users\";N;s:8:\"required\";s:0:\"\";s:3:\"int\";N;s:5:\"intID\";N;s:7:\"intPath\";N;s:8:\"uniqueID\";s:32:\"82c762fba08d2514266d1fe3a54644d7\";s:4:\"meta\";a:1:{s:0:\"\";N;}}}','input_Name1','input_Name1','we_object','','');
INSERT INTO tblObject VALUES (2,0,'0,1,4,2,3','events','object.gif',0,'object',1030209283,1035907373,'/events',1,1,0,'','',0,'','','',0,'%Y%_%m%_%d%_%ID%','a:6:{s:13:\"WorkspaceFlag\";i:1;s:15:\"input_EventName\";a:13:{s:7:\"default\";s:0:\"\";s:6:\"autobr\";N;s:9:\"dhtmledit\";N;s:9:\"showmenus\";N;s:10:\"forbidhtml\";N;s:9:\"forbidphp\";N;s:5:\"users\";N;s:8:\"required\";s:1:\"1\";s:3:\"int\";N;s:5:\"intID\";N;s:7:\"intPath\";N;s:8:\"uniqueID\";s:32:\"648384762b12d00e0e734f9a811b3145\";s:4:\"meta\";a:1:{s:0:\"\";N;}}s:21:\"text_EventDescription\";a:13:{s:7:\"default\";s:0:\"\";s:6:\"autobr\";s:3:\"off\";s:9:\"dhtmledit\";s:2:\"on\";s:9:\"showmenus\";s:3:\"off\";s:10:\"forbidhtml\";s:3:\"off\";s:9:\"forbidphp\";s:2:\"on\";s:5:\"users\";N;s:8:\"required\";s:1:\"1\";s:3:\"int\";N;s:5:\"intID\";N;s:7:\"intPath\";N;s:8:\"uniqueID\";s:32:\"380b1b117348d74b2e8d5fe52d24df88\";s:4:\"meta\";a:1:{s:0:\"\";N;}}s:14:\"date_EventDate\";a:13:{s:7:\"default\";s:0:\"\";s:6:\"autobr\";N;s:9:\"dhtmledit\";N;s:9:\"showmenus\";N;s:10:\"forbidhtml\";N;s:9:\"forbidphp\";N;s:5:\"users\";N;s:8:\"required\";s:1:\"1\";s:3:\"int\";N;s:5:\"intID\";N;s:7:\"intPath\";N;s:8:\"uniqueID\";s:32:\"9c42efeb4fd9dc1914f112744447a9e7\";s:4:\"meta\";a:1:{s:0:\"\";N;}}s:8:\"object_1\";a:13:{s:7:\"default\";s:0:\"\";s:6:\"autobr\";N;s:9:\"dhtmledit\";N;s:9:\"showmenus\";N;s:10:\"forbidhtml\";N;s:9:\"forbidphp\";N;s:5:\"users\";N;s:8:\"required\";s:1:\"1\";s:3:\"int\";N;s:5:\"intID\";N;s:7:\"intPath\";N;s:8:\"uniqueID\";s:32:\"0c7b7b7af26152591992398a5eba5658\";s:4:\"meta\";a:1:{s:0:\"\";N;}}s:16:\"text_EventDetail\";a:13:{s:7:\"default\";s:0:\"\";s:6:\"autobr\";s:3:\"off\";s:9:\"dhtmledit\";s:2:\"on\";s:9:\"showmenus\";s:3:\"off\";s:10:\"forbidhtml\";s:3:\"off\";s:9:\"forbidphp\";s:2:\"on\";s:5:\"users\";N;s:8:\"required\";s:0:\"\";s:3:\"int\";N;s:5:\"intID\";N;s:7:\"intPath\";N;s:8:\"uniqueID\";s:32:\"15c932b2872fb1a48cf00462fb70c03b\";s:4:\"meta\";a:1:{s:0:\"\";N;}}}','text_EventDescription','input_EventName','we_object',',306,',',62,');

#
# Table structure for table 'tblObjectFiles'
#

CREATE TABLE tblObjectFiles (
  ID int(11) NOT NULL auto_increment,
  ParentID int(11) NOT NULL default '0',
  Text varchar(255) NOT NULL default '',
  Icon varchar(64) NOT NULL default '',
  IsFolder tinyint(4) NOT NULL default '0',
  ContentType varchar(32) NOT NULL default '0',
  CreationDate int(11) NOT NULL default '0',
  ModDate int(11) NOT NULL default '0',
  Path varchar(255) NOT NULL default '',
  CreatorID bigint(20) NOT NULL default '0',
  ModifierID bigint(20) NOT NULL default '0',
  RestrictOwners tinyint(1) NOT NULL default '0',
  Owners varchar(255) NOT NULL default '',
  OwnersReadOnly text NOT NULL,
  Workspaces varchar(255) NOT NULL default '',
  ExtraWorkspaces varchar(255) NOT NULL default '',
  ExtraWorkspacesSelected varchar(255) NOT NULL default '',
  Templates varchar(255) NOT NULL default '',
  ExtraTemplates varchar(255) NOT NULL default '',
  TableID bigint(20) NOT NULL default '0',
  ObjectID bigint(20) NOT NULL default '0',
  Category varchar(255) NOT NULL default '',
  ClassName varchar(64) NOT NULL default '',
  IsClassFolder tinyint(1) NOT NULL default '0',
  IsNotEditable tinyint(1) NOT NULL default '0',
  Published int(11) NOT NULL default '0',
  PRIMARY KEY  (ID)
) TYPE=MyISAM;

#
# Dumping data for table 'tblObjectFiles'
#

INSERT INTO tblObjectFiles VALUES (11,0,'events','class_folder.gif',1,'folder',1030209564,1030209564,'/events',1,1,0,'','','','','','','',0,0,'','we_class_folder',1,0,0);
INSERT INTO tblObjectFiles VALUES (8,1,'HallOfSports','objectFile.gif',0,'objectFile',1030124391,1036067819,'/addresses/HallOfSports',1,1,0,'','','','','','','',1,10,',20,','we_objectFile',0,0,1036067819);
INSERT INTO tblObjectFiles VALUES (9,1,'Curch','objectFile.gif',0,'objectFile',1030124620,1036067841,'/addresses/Curch',1,1,0,'','','','','','','',1,11,'','we_objectFile',0,0,1036067842);
INSERT INTO tblObjectFiles VALUES (10,1,'SoccerStadium','objectFile.gif',0,'objectFile',1030125015,1036067669,'/addresses/SoccerStadium',1,1,0,'','','','','','','',1,12,',20,','we_objectFile',0,0,1036067669);
INSERT INTO tblObjectFiles VALUES (5,1,'ArenadiMaggio','objectFile.gif',0,'objectFile',1030123592,1036067777,'/addresses/ArenadiMaggio',1,1,0,'','','','','','','',1,7,',20,','we_objectFile',0,0,1036067777);
INSERT INTO tblObjectFiles VALUES (7,1,'Fightclub','objectFile.gif',0,'objectFile',1030124185,1036068217,'/addresses/Fightclub',1,1,0,'','','','','','','',1,9,'','we_objectFile',0,0,1036068217);
INSERT INTO tblObjectFiles VALUES (3,1,'CMS-Hall','objectFile.gif',0,'objectFile',1030123178,1035910586,'/addresses/CMS-Hall',1,1,0,'','','','','','','',1,5,',20,','we_objectFile',0,0,1035910586);
INSERT INTO tblObjectFiles VALUES (6,1,'webEdition-Hall','objectFile.gif',0,'objectFile',1030123840,1036064516,'/addresses/webEdition-Hall',1,1,0,'','','','','','','',1,8,',20,','we_objectFile',0,0,1036064516);
INSERT INTO tblObjectFiles VALUES (1,0,'addresses','class_folder.gif',1,'folder',1030117546,1030117546,'/addresses',1,1,0,'','','','','','','',0,0,'','we_class_folder',1,0,0);
INSERT INTO tblObjectFiles VALUES (2,1,'webEdition','objectFile.gif',0,'objectFile',1030119213,1036068080,'/addresses/webEdition',1,1,0,'','','','','','','',1,4,'','we_objectFile',0,0,1036068080);
INSERT INTO tblObjectFiles VALUES (16,11,'2002_08_24_16','objectFile.gif',0,'objectFile',1030212263,1036067587,'/events/2002_08_24_16',1,1,0,'','',',306,','','',',62,','',2,7,'','we_objectFile',0,0,1036067587);
INSERT INTO tblObjectFiles VALUES (13,11,'2002_08_24_13','objectFile.gif',0,'objectFile',1030210227,1036067363,'/events/2002_08_24_13',1,1,0,'','',',306,','','',',62,','',2,4,'','we_objectFile',0,0,1036067363);
INSERT INTO tblObjectFiles VALUES (14,11,'2002_08_24_14','objectFile.gif',0,'objectFile',1030211078,1076404814,'/events/2002_08_24_14',1,1,0,'','',',306,','','',',62,','',2,5,'','we_objectFile',0,0,1076404814);
INSERT INTO tblObjectFiles VALUES (15,11,'2002_08_24_15','objectFile.gif',0,'objectFile',1030211372,1036067118,'/events/2002_08_24_15',1,1,0,'','',',306,','','',',62,','',2,6,'','we_objectFile',0,0,1036067118);
INSERT INTO tblObjectFiles VALUES (12,11,'2002_08_24_12','objectFile.gif',0,'objectFile',1030209716,1036067440,'/events/2002_08_24_12',1,1,0,'','',',306,','','',',62,','',2,3,'','we_objectFile',0,0,1036067440);

#
# Table structure for table 'tblObject_1'
#

CREATE TABLE tblObject_1 (
  ID bigint(20) NOT NULL auto_increment,
  OF_ID bigint(20) NOT NULL default '0',
  OF_ParentID bigint(20) NOT NULL default '0',
  OF_Text varchar(255) NOT NULL default '',
  OF_Path varchar(255) NOT NULL default '',
  OF_Workspaces varchar(255) NOT NULL default '',
  OF_ExtraWorkspaces varchar(255) NOT NULL default '',
  OF_ExtraWorkspacesSelected varchar(255) NOT NULL default '',
  OF_Templates varchar(255) NOT NULL default '',
  OF_ExtraTemplates varchar(255) NOT NULL default '',
  OF_Category varchar(255) NOT NULL default '',
  OF_Published int(11) NOT NULL default '0',
  input_Name1 varchar(255) NOT NULL default '',
  input_Name2 varchar(255) NOT NULL default '',
  input_Street varchar(255) NOT NULL default '',
  input_ZIP varchar(10) NOT NULL default '',
  input_City varchar(255) NOT NULL default '',
  input_Phone varchar(255) NOT NULL default '',
  input_Email varchar(255) NOT NULL default '',
  input_URL varchar(255) NOT NULL default '',
  PRIMARY KEY  (ID)
) TYPE=MyISAM;

#
# Dumping data for table 'tblObject_1'
#

INSERT INTO tblObject_1 VALUES (9,7,1,'Fightclub','/addresses/Fightclub','','','','','','',1036068217,'Fightclub','','242 Fifth Fist','67857','PLeaseme','00989 / 37489234','fist@fightclub.com','http://fightclub.com');
INSERT INTO tblObject_1 VALUES (8,6,1,'webEdition-Hall','/addresses/webEdition-Hall','','','','','',',20,',1036064516,'webEdition Hall','','webEdition Street 120','56785','WebEdition','0190 / 346782346','info@webEdition.de','http://www.webedition.de');
INSERT INTO tblObject_1 VALUES (12,10,1,'SoccerStadium','/addresses/SoccerStadium','','','','','',',20,',1036067669,'Soccer Stadium','','65 Sportstreet','67868','Sportcity','','','');
INSERT INTO tblObject_1 VALUES (7,5,1,'ArenadiMaggio','/addresses/ArenadiMaggio','','','','','',',20,',1036067777,'Arena di Maggio','','Via Arena 12','45464','Magicten','009876 / 4389334','info@arenadimaggio.com','http://www.arenadimaggio.com');
INSERT INTO tblObject_1 VALUES (11,9,1,'Curch','/addresses/Curch','','','','','','',1036067842,'Church','','Church Street 54','65438','Curch City','','','');
INSERT INTO tblObject_1 VALUES (1,0,0,'','','','','','','','',0,'','','','','','','','');
INSERT INTO tblObject_1 VALUES (5,3,1,'CMS-Hall','/addresses/CMS-Hall','','','','','',',20,',1035910586,'CMS-Hall','','CMS-Street 111','12345','CMS-City','01212 / 38934','cms@cms.cms','http://www.cms.cms');
INSERT INTO tblObject_1 VALUES (10,8,1,'HallOfSports','/addresses/HallOfSports','','','','','',',20,',1036067819,'Hall of sports','','98 Sportstreet','23412','Sportstown','','','');

#
# Table structure for table 'tblObject_2'
#

CREATE TABLE tblObject_2 (
  ID bigint(20) NOT NULL auto_increment,
  OF_ID bigint(20) NOT NULL default '0',
  OF_ParentID bigint(20) NOT NULL default '0',
  OF_Text varchar(255) NOT NULL default '',
  OF_Path varchar(255) NOT NULL default '',
  OF_Workspaces varchar(255) NOT NULL default '',
  OF_ExtraWorkspaces varchar(255) NOT NULL default '',
  OF_ExtraWorkspacesSelected varchar(255) NOT NULL default '',
  OF_Templates varchar(255) NOT NULL default '',
  OF_ExtraTemplates varchar(255) NOT NULL default '',
  OF_Category varchar(255) NOT NULL default '',
  OF_Published int(11) NOT NULL default '0',
  input_EventName varchar(255) NOT NULL default '',
  text_EventDescription longtext NOT NULL,
  date_EventDate int(11) NOT NULL default '0',
  object_1 bigint(22) NOT NULL default '0',
  text_EventDetail longtext NOT NULL,
  PRIMARY KEY  (ID)
) TYPE=MyISAM;

#
# Dumping data for table 'tblObject_2'
#

INSERT INTO tblObject_2 VALUES (4,13,11,'2002_08_24_13','/events/2002_08_24_13',',306,','','',',62,','','',1036067363,'Workshop for webEdition developer','This workshop is targeted to developer with knowledge and experience in HTML, DHTML, Javascript, PHP4 and Database Technologies',1036659600,6,'<P><STRONG>Duration:</STRONG> 2 Days<BR><STRONG>No. Attendees:</STRONG> 10<BR><STRONG>Fee: </STRONG>400.00 Euro / Attendee</P>');
INSERT INTO tblObject_2 VALUES (1,0,0,'','','','','','','','',0,'','',0,0,'');
INSERT INTO tblObject_2 VALUES (3,12,11,'2002_08_24_12','/events/2002_08_24_12',',306,','','',',62,','','',1036067440,'Why CMS?','Christian Schulmeyer explains the advantage of using content management systems.',1033030800,2,'<STRONG>Duration:</STRONG>2 Hours<BR><STRONG>Seats:</STRONG> 100<BR><STRONG>Fee:</STRONG> free Entry');
INSERT INTO tblObject_2 VALUES (5,14,11,'2002_08_24_14','/events/2002_08_24_14',',306,','','',',62,','','',1076404814,'PHP Basics','Basic developing in PHP. You will learn how to develop interactive Websites on you own based on PHP.',1033286400,7,'<strong>Fee:</strong> 350.00 Euro / Attendee<br><strong>Duration:</strong> 1 Day');
INSERT INTO tblObject_2 VALUES (7,16,11,'2002_08_24_16','/events/2002_08_24_16',',306,','','',',62,','','',1036067587,'Web of Life','Exhibition \"intermedium2\" featuring the presentation of the multimedia project \"Web of Life\". ',1031061600,3,'');
INSERT INTO tblObject_2 VALUES (6,15,11,'2002_08_24_15','/events/2002_08_24_15',',306,','','',',62,','','',1036067118,'\"El beso de la tierra\" - Video in Spanish w/o subtitles','Regie: Lucinda Torre, Spain\nAt 8.00 pm: Welcome and introduction\nAt 8.30 pm: Presentation of the movie in spanish without subtitles.',1033149600,5,'');

#
# Table structure for table 'tblOrders'
#

CREATE TABLE tblOrders (
  IntID int(11) NOT NULL auto_increment,
  IntOrderID int(11) default NULL,
  IntCustomerID int(11) default NULL,
  IntArticleID int(11) default NULL,
  IntQuantity int(11) default NULL,
  DateOrder datetime default NULL,
  DateShipping datetime default NULL,
  DatePayment datetime default NULL,
  Price float default NULL,
  IntPayment_Type tinyint(4) default NULL,
  strSerial longtext NOT NULL,
  PRIMARY KEY  (IntID),
  UNIQUE KEY IntID (IntID),
  KEY IntID_2 (IntID)
) TYPE=MyISAM;

#
# Dumping data for table 'tblOrders'
#

INSERT INTO tblOrders VALUES (1,1,1,458,1,'2003-11-23 14:38:55','0000-00-00 00:00:00','0000-00-00 00:00:00',159,NULL,'a:62:{s:11:\"Artikelname\";s:23:\"webEdition Basisversion\";s:8:\"Keywords\";s:14:\"cms,webEdition\";s:5:\"Title\";s:23:\"webEdition Basisversion\";s:7:\"Ordnung\";s:1:\"1\";s:15:\"shopdescription\";s:13:\"CMS f�rs Volk\";s:4:\"Bild\";s:3:\"438\";s:4:\"Text\";s:263:\"Mit der Basisversion von <STRONG>webEdition</STRONG> kann man eine Domain verwalten. <BR><BR>Die Zielgruppe sind alle kleinen und mittelst�ndischen Firmen sowie Privatleute, die keine Kenntnisse in HTML haben, aber ihre Webseite dennoch dynamisch pflegen wollen. \";s:5:\"Preis\";s:6:\"159.00\";s:11:\"Description\";s:13:\"CMS f�rs Volk\";s:9:\"shoptitle\";s:23:\"webEdition Basisversion\";s:7:\"wedoc_0\";s:3:\"458\";s:8:\"wedoc_ID\";s:3:\"458\";s:7:\"wedoc_1\";s:3:\"421\";s:14:\"wedoc_ParentID\";s:3:\"421\";s:7:\"wedoc_2\";s:14:\"webedition.php\";s:10:\"wedoc_Text\";s:14:\"webedition.php\";s:7:\"wedoc_3\";s:15:\"we_dokument.gif\";s:10:\"wedoc_Icon\";s:15:\"we_dokument.gif\";s:7:\"wedoc_4\";s:1:\"0\";s:14:\"wedoc_IsFolder\";s:1:\"0\";s:7:\"wedoc_5\";s:15:\"text/webedition\";s:17:\"wedoc_ContentType\";s:15:\"text/webedition\";s:7:\"wedoc_6\";s:10:\"1040237045\";s:18:\"wedoc_CreationDate\";s:10:\"1040237045\";s:7:\"wedoc_7\";s:10:\"1040242948\";s:13:\"wedoc_ModDate\";s:10:\"1040242948\";s:7:\"wedoc_8\";s:36:\"/we_demo/shop/artikel/webedition.php\";s:10:\"wedoc_Path\";s:36:\"/we_demo/shop/artikel/webedition.php\";s:7:\"wedoc_9\";s:3:\"108\";s:16:\"wedoc_TemplateID\";s:3:\"108\";s:8:\"wedoc_10\";s:10:\"webedition\";s:14:\"wedoc_Filename\";s:10:\"webedition\";s:8:\"wedoc_11\";s:4:\".php\";s:15:\"wedoc_Extension\";s:4:\".php\";s:8:\"wedoc_12\";s:1:\"1\";s:15:\"wedoc_IsDynamic\";s:1:\"1\";s:8:\"wedoc_13\";s:1:\"1\";s:18:\"wedoc_IsSearchable\";s:1:\"1\";s:8:\"wedoc_14\";s:2:\"10\";s:13:\"wedoc_DocType\";s:2:\"10\";s:8:\"wedoc_15\";s:21:\"we_webEditionDocument\";s:15:\"wedoc_ClassName\";s:21:\"we_webEditionDocument\";s:8:\"wedoc_16\";s:3:\",7,\";s:14:\"wedoc_Category\";s:3:\",7,\";s:8:\"wedoc_17\";s:1:\"0\";s:13:\"wedoc_Deleted\";s:1:\"0\";s:8:\"wedoc_18\";s:10:\"1040242948\";s:15:\"wedoc_Published\";s:10:\"1040242948\";s:8:\"wedoc_19\";s:1:\"1\";s:15:\"wedoc_CreatorID\";s:1:\"1\";s:8:\"wedoc_20\";s:1:\"1\";s:16:\"wedoc_ModifierID\";s:1:\"1\";s:8:\"wedoc_21\";s:1:\"0\";s:20:\"wedoc_RestrictOwners\";s:1:\"0\";s:8:\"wedoc_22\";s:0:\"\";s:12:\"wedoc_Owners\";s:0:\"\";s:8:\"wedoc_23\";s:0:\"\";s:20:\"wedoc_OwnersReadOnly\";s:0:\"\";s:8:\"wedoc_24\";s:0:\"\";s:19:\"wedoc_documentArray\";s:0:\"\";s:7:\"WE_PATH\";s:36:\"/we_demo/shop/artikel/webedition.php\";s:7:\"WE_TEXT\";s:362:\"webEdition Basisversion CMS f�rs Volk 159.00 Mit der Basisversion von webEdition kann man eine Domain verwalten. Die Zielgruppe sind alle kleinen und mittelst�ndischen Firmen sowie Privatleute, die keine Kenntnisse in HTML haben, aber ihre Webseite dennoch dynamisch pflegen wollen.  CMS f�rs Volk webEdition Basisversion 1 cms,webEdition webEdition Basisversion\";}');
INSERT INTO tblOrders VALUES (2,2,1,169,1,'2004-02-12 17:44:55','0000-00-00 00:00:00','0000-00-00 00:00:00',199,NULL,'a:63:{s:7:\"Ordnung\";s:1:\"1\";s:11:\"Description\";s:25:\"webEdition for one domain\";s:9:\"shoptitle\";s:24:\"webEdition Basis version\";s:11:\"Artikelname\";s:24:\"webEdition Basis version\";s:4:\"Text\";s:281:\"The basic version of<strong> webEdition</strong> is for the administration of one domain. <br>\nThe target group is all small and medium-sized companies as well as\nprivate individuals who dont have any knowledge of HTML but still want\nto maintain their website dynamically.<br>\n<br>\";s:5:\"Title\";s:24:\"webEdition Basis version\";s:4:\"Bild\";s:3:\"152\";s:5:\"Preis\";s:6:\"159,00\";s:8:\"Keywords\";s:14:\"cms,webEdition\";s:12:\"we_nxcjidshf\";s:1:\" \";s:15:\"shopdescription\";s:25:\"webEdition for one domain\";s:7:\"wedoc_0\";s:3:\"169\";s:8:\"wedoc_ID\";s:3:\"169\";s:7:\"wedoc_1\";s:3:\"165\";s:14:\"wedoc_ParentID\";s:3:\"165\";s:7:\"wedoc_2\";s:14:\"webedition.php\";s:10:\"wedoc_Text\";s:14:\"webedition.php\";s:7:\"wedoc_3\";s:15:\"we_dokument.gif\";s:10:\"wedoc_Icon\";s:15:\"we_dokument.gif\";s:7:\"wedoc_4\";s:1:\"0\";s:14:\"wedoc_IsFolder\";s:1:\"0\";s:7:\"wedoc_5\";s:15:\"text/webedition\";s:17:\"wedoc_ContentType\";s:15:\"text/webedition\";s:7:\"wedoc_6\";s:10:\"1020444546\";s:18:\"wedoc_CreationDate\";s:10:\"1020444546\";s:7:\"wedoc_7\";s:10:\"1059918167\";s:13:\"wedoc_ModDate\";s:10:\"1059918167\";s:7:\"wedoc_8\";s:34:\"/we_demo/shop/items/webedition.php\";s:10:\"wedoc_Path\";s:34:\"/we_demo/shop/items/webedition.php\";s:7:\"wedoc_9\";s:2:\"42\";s:16:\"wedoc_TemplateID\";s:2:\"42\";s:8:\"wedoc_10\";s:10:\"webedition\";s:14:\"wedoc_Filename\";s:10:\"webedition\";s:8:\"wedoc_11\";s:4:\".php\";s:15:\"wedoc_Extension\";s:4:\".php\";s:8:\"wedoc_12\";s:1:\"1\";s:15:\"wedoc_IsDynamic\";s:1:\"1\";s:8:\"wedoc_13\";s:1:\"1\";s:18:\"wedoc_IsSearchable\";s:1:\"1\";s:8:\"wedoc_14\";s:1:\"4\";s:13:\"wedoc_DocType\";s:1:\"4\";s:8:\"wedoc_15\";s:21:\"we_webEditionDocument\";s:15:\"wedoc_ClassName\";s:21:\"we_webEditionDocument\";s:8:\"wedoc_16\";s:4:\",16,\";s:14:\"wedoc_Category\";s:4:\",16,\";s:8:\"wedoc_17\";s:1:\"0\";s:13:\"wedoc_Deleted\";s:1:\"0\";s:8:\"wedoc_18\";s:10:\"1059918167\";s:15:\"wedoc_Published\";s:10:\"1059918167\";s:8:\"wedoc_19\";s:1:\"0\";s:15:\"wedoc_CreatorID\";s:1:\"0\";s:8:\"wedoc_20\";s:1:\"1\";s:16:\"wedoc_ModifierID\";s:1:\"1\";s:8:\"wedoc_21\";s:1:\"0\";s:20:\"wedoc_RestrictOwners\";s:1:\"0\";s:8:\"wedoc_22\";s:0:\"\";s:12:\"wedoc_Owners\";s:0:\"\";s:8:\"wedoc_23\";s:0:\"\";s:20:\"wedoc_OwnersReadOnly\";s:0:\"\";s:8:\"wedoc_24\";s:0:\"\";s:19:\"wedoc_documentArray\";s:0:\"\";s:7:\"WE_PATH\";s:34:\"/we_demo/shop/items/webedition.php\";s:7:\"WE_TEXT\";s:403:\"cms,webEdition 159,00 webEdition Basis version The basic version of webEdition is for the administration of one domain. \nThe target group is all small and medium-sized companies as well as\nprivate individuals who dont have any knowledge of HTML but still want\nto maintain their website dynamically.\n webEdition Basis version webEdition Basis version webEdition for one domain 1 webEdition for one domain\";}');

#
# Table structure for table 'tblPasswd'
#

CREATE TABLE tblPasswd (
  passwd varchar(32) NOT NULL default '',
  username varchar(128) NOT NULL default ''
) TYPE=MyISAM;

#
# Dumping data for table 'tblPasswd'
#

INSERT INTO tblPasswd VALUES ('21232f297a57a5a743894a0e4a801fc3','admin');

#
# Table structure for table 'tblPrefs'
#

CREATE TABLE tblPrefs (
  userID bigint(20) NOT NULL default '0',
  FileFilter int(11) NOT NULL default '0',
  openFolders_tblFile text NOT NULL,
  openFolders_tblTemplates text NOT NULL,
  DefaultTemplateID int(11) NOT NULL default '0',
  DefaultStaticExt varchar(7) NOT NULL default '',
  DefaultDynamicExt varchar(7) NOT NULL default '',
  DefaultHTMLExt varchar(7) NOT NULL default '',
  sizeOpt tinyint(1) NOT NULL default '0',
  weWidth int(11) NOT NULL default '0',
  weHeight int(11) NOT NULL default '0',
  usePlugin tinyint(1) NOT NULL default '0',
  autostartPlugin tinyint(1) NOT NULL default '0',
  promptPlugin tinyint(1) NOT NULL default '0',
  Language varchar(64) NOT NULL default '',
  openFolders_tblObject text,
  openFolders_tblObjectFiles text,
  phpOnOff tinyint(1) NOT NULL default '0',
  seem_start_file int(11) NOT NULL default '0',
  editorSizeOpt tinyint(1) NOT NULL default '0',
  editorWidth int(11) NOT NULL default '0',
  editorHeight int(11) NOT NULL default '0',
  debug_normal tinyint(1) NOT NULL default '0',
  debug_seem tinyint(1) NOT NULL default '0',
  editorFontname varchar(255) NOT NULL default 'none',
  editorFontsize int(2) NOT NULL default '-1',
  editorFont tinyint(1) NOT NULL default '0',
  default_tree_count int(11) NOT NULL default '0'
) TYPE=MyISAM;

#
# Dumping data for table 'tblPrefs'
#

INSERT INTO tblPrefs VALUES (1,0,'','',0,'.html','.php','.html',0,0,0,0,0,0,'English','','',0,98,0,0,0,0,0,'none',-1,0,0);

#
# Table structure for table 'tblRecipients'
#

CREATE TABLE tblRecipients (
  ID bigint(20) NOT NULL auto_increment,
  Email varchar(255) NOT NULL default '',
  PRIMARY KEY  (ID)
) TYPE=MyISAM;

#
# Dumping data for table 'tblRecipients'
#


#
# Table structure for table 'tblSchedule'
#

CREATE TABLE tblSchedule (
  DID bigint(20) NOT NULL default '0',
  Wann int(11) NOT NULL default '0',
  Was int(11) NOT NULL default '0',
  ClassName varchar(64) NOT NULL default '',
  SerializedData longblob,
  Schedpro longtext,
  Type tinyint(3) NOT NULL default '0',
  Active tinyint(1) default '1'
) TYPE=MyISAM;

#
# Dumping data for table 'tblSchedule'
#


#
# Table structure for table 'tblTODO'
#

CREATE TABLE tblTODO (
  ID int(11) NOT NULL auto_increment,
  ParentID int(11) default NULL,
  UserID int(11) NOT NULL default '0',
  account_id int(11) NOT NULL default '-1',
  msg_type tinyint(4) NOT NULL default '0',
  obj_type tinyint(4) NOT NULL default '0',
  headerDate int(11) default NULL,
  headerSubject varchar(255) default NULL,
  headerCreator int(11) default NULL,
  headerAssigner int(11) default NULL,
  headerStatus tinyint(4) default '0',
  headerDeadline int(11) default NULL,
  Priority tinyint(4) default NULL,
  Properties smallint(5) unsigned default '0',
  MessageText text,
  Content_Type varchar(10) default 'text',
  seenStatus tinyint(3) unsigned default '0',
  tag tinyint(3) unsigned default '0',
  PRIMARY KEY  (ID)
) TYPE=MyISAM;

#
# Dumping data for table 'tblTODO'
#


#
# Table structure for table 'tblTODOHistory'
#

CREATE TABLE tblTODOHistory (
  ID int(11) NOT NULL auto_increment,
  ParentID int(11) NOT NULL default '0',
  UserID int(11) NOT NULL default '0',
  fromUserID int(11) NOT NULL default '0',
  Comment text,
  Created int(11) default NULL,
  action int(10) unsigned default '0',
  status tinyint(3) unsigned default NULL,
  tag tinyint(3) unsigned default '0',
  PRIMARY KEY  (ID)
) TYPE=MyISAM;

#
# Dumping data for table 'tblTODOHistory'
#


#
# Table structure for table 'tblTemplates'
#

CREATE TABLE tblTemplates (
  ID int(11) NOT NULL auto_increment,
  ParentID int(11) NOT NULL default '0',
  Text varchar(255) NOT NULL default '',
  Icon varchar(64) NOT NULL default '',
  IsFolder tinyint(4) NOT NULL default '0',
  ContentType varchar(32) NOT NULL default '0',
  CreationDate int(11) NOT NULL default '0',
  ModDate int(11) NOT NULL default '0',
  Path varchar(255) NOT NULL default '',
  Filename varchar(64) NOT NULL default '',
  Extension varchar(10) NOT NULL default '',
  ClassName varchar(64) NOT NULL default '',
  Deleted int(11) NOT NULL default '0',
  Owners varchar(255) default NULL,
  RestrictOwners tinyint(1) default '0',
  OwnersReadOnly text,
  CreatorID bigint(20) NOT NULL default '0',
  ModifierID bigint(20) NOT NULL default '0',
  PRIMARY KEY  (ID)
) TYPE=MyISAM;

#
# Dumping data for table 'tblTemplates'
#

INSERT INTO tblTemplates VALUES (30,28,'review_list.tmpl','prog.gif',0,'text/weTmpl',1001607487,1059945458,'/we_demo/moviereviews/review_list.tmpl','review_list','.tmpl','we_template',0,'',0,'',0,1);
INSERT INTO tblTemplates VALUES (27,25,'news_article.tmpl','prog.gif',0,'text/weTmpl',1001607487,1022006273,'/we_demo/news/news_article.tmpl','news_article','.tmpl','we_template',0,'',0,'',0,0);
INSERT INTO tblTemplates VALUES (28,64,'moviereviews','folder.gif',1,'folder',1001670430,1058718701,'/we_demo/moviereviews','moviereviews','','we_folder',0,'',0,'',0,1);
INSERT INTO tblTemplates VALUES (29,28,'review.tmpl','prog.gif',0,'text/weTmpl',1001607487,1059643365,'/we_demo/moviereviews/review.tmpl','review','.tmpl','we_template',0,'',0,'',0,1);
INSERT INTO tblTemplates VALUES (24,25,'news.tmpl','prog.gif',0,'text/weTmpl',1001607487,1059643741,'/we_demo/news/news.tmpl','news','.tmpl','we_template',0,'',0,'',0,1);
INSERT INTO tblTemplates VALUES (25,64,'news','folder.gif',1,'folder',1001608087,1058718676,'/we_demo/news','news','','we_folder',0,'',0,'',0,1);
INSERT INTO tblTemplates VALUES (26,64,'navigation.tmpl','prog.gif',0,'text/weTmpl',1001608604,1058718791,'/we_demo/navigation.tmpl','navigation','.tmpl','we_template',0,'',0,'',0,1);
INSERT INTO tblTemplates VALUES (35,64,'program','folder.gif',1,'folder',1001676959,1058718651,'/we_demo/program','program','','we_folder',0,'',0,'',0,1);
INSERT INTO tblTemplates VALUES (36,35,'program.tmpl','prog.gif',0,'text/weTmpl',1001607487,1059643690,'/we_demo/program/program.tmpl','program','.tmpl','we_template',0,'',0,'',0,1);
INSERT INTO tblTemplates VALUES (37,64,'links','folder.gif',1,'folder',1001679249,1058718725,'/we_demo/links','links','','we_folder',0,'',0,'',0,1);
INSERT INTO tblTemplates VALUES (38,37,'links.tmpl','prog.gif',0,'text/weTmpl',1001607487,1059643294,'/we_demo/links/links.tmpl','links','.tmpl','we_template',0,'',0,'',0,1);
INSERT INTO tblTemplates VALUES (39,64,'search.tmpl','prog.gif',0,'text/weTmpl',1001607487,1059643504,'/we_demo/search.tmpl','search','.tmpl','we_template',0,'',0,'',0,1);
INSERT INTO tblTemplates VALUES (40,64,'dhtmlpop.tmpl','prog.gif',0,'text/weTmpl',1003238493,1058718837,'/we_demo/dhtmlpop.tmpl','dhtmlpop','.tmpl','we_template',0,'',0,'',0,1);
INSERT INTO tblTemplates VALUES (41,50,'itemlist.tmpl','prog.gif',0,'text/weTmpl',1001607487,1059643559,'/we_demo/shop/itemlist.tmpl','itemlist','.tmpl','we_template',0,'',0,'',0,1);
INSERT INTO tblTemplates VALUES (42,50,'item.tmpl','prog.gif',0,'text/weTmpl',1001607487,1022084522,'/we_demo/shop/item.tmpl','item','.tmpl','we_template',0,'',0,'',0,0);
INSERT INTO tblTemplates VALUES (43,50,'basket.tmpl','prog.gif',0,'text/weTmpl',1008880772,1022084448,'/we_demo/shop/basket.tmpl','basket','.tmpl','we_template',0,'',0,'',0,0);
INSERT INTO tblTemplates VALUES (44,50,'goods.tmpl','prog.gif',0,'text/weTmpl',1009401219,1022084483,'/we_demo/shop/goods.tmpl','goods','.tmpl','we_template',0,'',0,'',0,0);
INSERT INTO tblTemplates VALUES (45,58,'login.tmpl','prog.gif',0,'text/weTmpl',1009483480,1059643606,'/we_demo/user/login.tmpl','login','.tmpl','we_template',0,'',0,'',0,1);
INSERT INTO tblTemplates VALUES (58,64,'user','folder.gif',1,'folder',1020692853,1058718569,'/we_demo/user','user','','we_folder',0,'',0,'',0,1);
INSERT INTO tblTemplates VALUES (48,58,'userdata.tmpl','prog.gif',0,'text/weTmpl',1011708366,1059643644,'/we_demo/user/userdata.tmpl','userdata','.tmpl','we_template',0,'',0,'',0,1);
INSERT INTO tblTemplates VALUES (49,50,'ordermail.tmpl','prog.gif',0,'text/weTmpl',1016722290,1022006534,'/we_demo/shop/ordermail.tmpl','ordermail','.tmpl','we_template',0,'',0,'',0,0);
INSERT INTO tblTemplates VALUES (50,64,'shop','folder.gif',1,'folder',1020075442,1058718617,'/we_demo/shop','shop','','we_folder',0,'',0,'',0,1);
INSERT INTO tblTemplates VALUES (56,50,'sendorder.tmpl','prog.gif',0,'text/weTmpl',1020449802,1022149648,'/we_demo/shop/sendorder.tmpl','sendorder','.tmpl','we_template',0,'',0,'',0,0);
INSERT INTO tblTemplates VALUES (61,64,'events','folder.gif',1,'folder',1035905557,1058718746,'/we_demo/events','events','','we_folder',0,'',0,'',1,1);
INSERT INTO tblTemplates VALUES (60,64,'addresses.tmpl','prog.gif',0,'text/weTmpl',1035905394,1059643253,'/we_demo/addresses.tmpl','addresses','.tmpl','we_template',0,'',0,'',1,1);
INSERT INTO tblTemplates VALUES (62,61,'detail.tmpl','prog.gif',0,'text/weTmpl',1035905585,1059643226,'/we_demo/events/detail.tmpl','detail','.tmpl','we_template',0,'',0,'',1,1);
INSERT INTO tblTemplates VALUES (63,61,'list.tmpl','prog.gif',0,'text/weTmpl',1035905626,1059643181,'/we_demo/events/list.tmpl','list','.tmpl','we_template',0,'',0,'',1,1);
INSERT INTO tblTemplates VALUES (64,0,'we_demo','folder.gif',1,'folder',1058718525,1058718539,'/we_demo','we_demo','','we_folder',0,'',0,'',1,1);
INSERT INTO tblTemplates VALUES (65,64,'newsletter','folder.gif',1,'folder',1058719288,1058719318,'/we_demo/newsletter','newsletter','','we_folder',0,'',0,'',1,1);
INSERT INTO tblTemplates VALUES (66,65,'salutation.tmpl','prog.gif',0,'text/weTmpl',1058719353,1059666452,'/we_demo/newsletter/salutation.tmpl','salutation','.tmpl','we_template',0,'',0,'',1,1);
INSERT INTO tblTemplates VALUES (67,65,'mail.tmpl','prog.gif',0,'text/weTmpl',1058720288,1059943463,'/we_demo/newsletter/mail.tmpl','mail','.tmpl','we_template',0,'',0,'',1,1);
INSERT INTO tblTemplates VALUES (68,65,'newsletter.tmpl','prog.gif',0,'text/weTmpl',1058720487,1059918096,'/we_demo/newsletter/newsletter.tmpl','newsletter','.tmpl','we_template',0,'',0,'',1,1);
INSERT INTO tblTemplates VALUES (69,65,'unsubscribeBlock.tmpl','prog.gif',0,'text/weTmpl',1059943110,1059943869,'/we_demo/newsletter/unsubscribeBlock.tmpl','unsubscribeBlock','.tmpl','we_template',0,'',0,'',1,1);

#
# Table structure for table 'tblTemporaryDoc'
#

CREATE TABLE tblTemporaryDoc (
  ID bigint(20) NOT NULL auto_increment,
  DocumentID bigint(20) NOT NULL default '0',
  DocumentObject longtext NOT NULL,
  DocTable varchar(64) NOT NULL default '',
  UnixTimestamp bigint(20) NOT NULL default '0',
  Active tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (ID)
) TYPE=MyISAM;

#
# Dumping data for table 'tblTemporaryDoc'
#


#
# Table structure for table 'tblUpdateLog'
#

CREATE TABLE tblUpdateLog (
  ID int(255) NOT NULL auto_increment,
  dortigeID int(255) NOT NULL default '0',
  datum datetime default NULL,
  aktion text NOT NULL,
  versionsnummer varchar(10) NOT NULL default '',
  module text NOT NULL,
  error tinyint(1) NOT NULL default '0',
  step int(4) NOT NULL default '0',
  PRIMARY KEY  (ID)
) TYPE=MyISAM;

#
# Dumping data for table 'tblUpdateLog'
#


#
# Table structure for table 'tblUser'
#

CREATE TABLE tblUser (
  ID bigint(20) NOT NULL auto_increment,
  ParentID bigint(20) NOT NULL default '0',
  Text varchar(255) NOT NULL default '',
  Path varchar(255) NOT NULL default '',
  Icon varchar(64) NOT NULL default '',
  IsFolder tinyint(1) NOT NULL default '0',
  Type tinyint(4) NOT NULL default '0',
  First varchar(255) NOT NULL default '',
  Second varchar(255) NOT NULL default '',
  Address varchar(255) NOT NULL default '0',
  HouseNo varchar(11) NOT NULL default '',
  City varchar(255) NOT NULL default '',
  PLZ int(11) NOT NULL default '0',
  State varchar(255) NOT NULL default '',
  Country varchar(255) NOT NULL default '',
  Tel_preselection varchar(11) NOT NULL default '0',
  Telephone varchar(32) NOT NULL default '',
  Fax_preselection varchar(11) NOT NULL default '0',
  Fax varchar(32) NOT NULL default '',
  Handy varchar(32) NOT NULL default '',
  Email varchar(255) NOT NULL default '',
  Description text NOT NULL,
  username varchar(255) NOT NULL default '',
  passwd varchar(255) NOT NULL default '',
  Permissions text NOT NULL,
  ParentPerms tinyint(4) NOT NULL default '0',
  Alias bigint(20) NOT NULL default '0',
  CreatorID bigint(20) NOT NULL default '0',
  CreateDate bigint(20) NOT NULL default '0',
  ModifierID bigint(20) NOT NULL default '0',
  ModifyDate bigint(20) NOT NULL default '0',
  Ping int(11) NOT NULL default '0',
  Portal varchar(255) NOT NULL default '',
  workSpace varchar(255) NOT NULL default '',
  workSpaceDef varchar(255) NOT NULL default '',
  workSpaceTmp varchar(255) NOT NULL default '',
  ParentWs tinyint(4) NOT NULL default '0',
  ParentWst tinyint(4) NOT NULL default '0',
  Salutation varchar(32) NOT NULL default '',
  PRIMARY KEY  (ID),
  UNIQUE KEY ID (ID),
  KEY ID_2 (ID)
) TYPE=MyISAM;

#
# Dumping data for table 'tblUser'
#

INSERT INTO tblUser VALUES (1,0,'admin','/admin','user.gif',0,0,'webEdition','','','','',0,'','','','','','','','','','admin','21232f297a57a5a743894a0e4a801fc3','a:55:{s:13:\"ADMINISTRATOR\";s:1:\"1\";s:18:\"NEW_WEBEDITIONSITE\";s:1:\"1\";s:10:\"NEW_GRAFIK\";s:1:\"1\";s:8:\"NEW_HTML\";s:1:\"1\";s:9:\"NEW_FLASH\";s:1:\"1\";s:6:\"NEW_JS\";s:1:\"1\";s:7:\"NEW_CSS\";s:1:\"1\";s:12:\"NEW_SONSTIGE\";s:1:\"1\";s:12:\"NEW_TEMPLATE\";s:1:\"1\";s:14:\"NEW_DOC_FOLDER\";s:1:\"1\";s:22:\"CHANGE_DOC_FOLDER_PATH\";s:1:\"0\";s:15:\"NEW_TEMP_FOLDER\";s:1:\"1\";s:17:\"CAN_SEE_DOCUMENTS\";s:1:\"1\";s:17:\"CAN_SEE_TEMPLATES\";s:1:\"1\";s:22:\"SAVE_DOCUMENT_TEMPLATE\";s:1:\"1\";s:17:\"DELETE_DOC_FOLDER\";s:1:\"1\";s:18:\"DELETE_TEMP_FOLDER\";s:1:\"1\";s:15:\"DELETE_DOCUMENT\";s:1:\"1\";s:15:\"DELETE_TEMPLATE\";s:1:\"1\";s:13:\"BROWSE_SERVER\";s:1:\"1\";s:12:\"EDIT_DOCTYPE\";s:1:\"1\";s:14:\"EDIT_KATEGORIE\";s:1:\"1\";s:7:\"REBUILD\";s:1:\"1\";s:6:\"EXPORT\";s:1:\"1\";s:6:\"IMPORT\";s:1:\"1\";s:9:\"NEW_GROUP\";s:1:\"1\";s:8:\"NEW_USER\";s:1:\"1\";s:10:\"SAVE_GROUP\";s:1:\"1\";s:9:\"SAVE_USER\";s:1:\"1\";s:12:\"DELETE_GROUP\";s:1:\"1\";s:11:\"DELETE_USER\";s:1:\"1\";s:7:\"PUBLISH\";s:1:\"1\";s:21:\"EDIT_SETTINGS_DEF_EXT\";s:1:\"1\";s:13:\"EDIT_SETTINGS\";s:1:\"1\";s:11:\"EDIT_PASSWD\";s:1:\"1\";s:12:\"NEW_CUSTOMER\";s:1:\"0\";s:15:\"DELETE_CUSTOMER\";s:1:\"0\";s:13:\"EDIT_CUSTOMER\";s:1:\"0\";s:19:\"SHOW_CUSTOMER_ADMIN\";s:1:\"0\";s:16:\"NEW_SHOP_ARTICLE\";s:1:\"0\";s:19:\"DELETE_SHOP_ARTICLE\";s:1:\"0\";s:15:\"EDIT_SHOP_ORDER\";s:1:\"0\";s:17:\"DELETE_SHOP_ORDER\";s:1:\"0\";s:15:\"EDIT_SHOP_PREFS\";s:1:\"0\";s:19:\"CAN_SEE_OBJECTFILES\";s:1:\"1\";s:14:\"NEW_OBJECTFILE\";s:1:\"1\";s:21:\"NEW_OBJECTFILE_FOLDER\";s:1:\"1\";s:17:\"DELETE_OBJECTFILE\";s:1:\"1\";s:15:\"CAN_SEE_OBJECTS\";s:1:\"0\";s:10:\"NEW_OBJECT\";s:1:\"0\";s:13:\"DELETE_OBJECT\";s:1:\"0\";s:12:\"NEW_WORKFLOW\";s:1:\"0\";s:15:\"DELETE_WORKFLOW\";s:1:\"0\";s:13:\"EDIT_WORKFLOW\";s:1:\"0\";s:9:\"EMPTY_LOG\";s:1:\"0\";}',0,0,0,0,0,0,0,'','','','',0,0,'');

#
# Table structure for table 'tblWebAdmin'
#

CREATE TABLE tblWebAdmin (
  Name varchar(255) NOT NULL default '',
  Value text NOT NULL
) TYPE=MyISAM;

#
# Dumping data for table 'tblWebAdmin'
#

INSERT INTO tblWebAdmin VALUES ('FieldAdds','a:5:{s:13:\"Newsletter_Ok\";a:1:{s:7:\"default\";s:4:\",yes\";}s:19:\"Newsletter_HTMLMail\";a:1:{s:7:\"default\";s:4:\",yes\";}s:21:\"Salutation_Salutation\";a:1:{s:7:\"default\";s:8:\",Mr.,Ms.\";}s:16:\"Salutation_Title\";a:1:{s:7:\"default\";s:11:\",Dr., Prof.\";}s:9:\"UserGroup\";a:1:{s:7:\"default\";s:12:\"Admins,Users\";}}');
INSERT INTO tblWebAdmin VALUES ('SortView','a:1:{s:9:\"UserGroup\";a:1:{i:0;a:4:{s:6:\"branch\";s:5:\"Other\";s:5:\"field\";s:9:\"UserGroup\";s:8:\"function\";s:0:\"\";s:5:\"order\";s:3:\"ASC\";}}}');
INSERT INTO tblWebAdmin VALUES ('Prefs','a:2:{s:10:\"start_year\";s:4:\"1900\";s:17:\"default_sort_view\";s:9:\"UserGroup\";}');

#
# Table structure for table 'tblWebUser'
#

CREATE TABLE tblWebUser (
  ID bigint(20) NOT NULL auto_increment,
  Username varchar(32) NOT NULL default '',
  Password varchar(32) NOT NULL default '',
  Salutation_Salutation varchar(200) NOT NULL default '',
  Salutation_Title varchar(200) NOT NULL default '',
  Forename varchar(128) NOT NULL default '',
  Surname varchar(128) NOT NULL default '',
  Contact_Address1 varchar(128) NOT NULL default '',
  Contact_Address2 varchar(128) NOT NULL default '',
  Contact_Country varchar(128) NOT NULL default '',
  Contact_State varchar(128) NOT NULL default '',
  Contact_Tel1 varchar(64) NOT NULL default '',
  Contact_Tel2 varchar(64) NOT NULL default '',
  Contact_Tel3 varchar(64) NOT NULL default '',
  Contact_Email varchar(128) NOT NULL default '',
  Contact_Homepage varchar(128) NOT NULL default '',
  MemberSince varchar(24) NOT NULL default '0',
  LastLogin varchar(24) NOT NULL default '0',
  LastAccess varchar(24) NOT NULL default '0',
  ParentID bigint(20) NOT NULL default '0',
  Path varchar(255) default '',
  IsFolder tinyint(1) default '0',
  Icon varchar(255) default 'customer.gif',
  Text varchar(255) default '',
  Newsletter_Ok varchar(200) NOT NULL default '',
  Newsletter_HTMLMail varchar(200) NOT NULL default '',
  UserGroup varchar(200) NOT NULL default '',
  PRIMARY KEY  (ID),
  KEY Username (Username),
  KEY user_pass (Username,Password),
  KEY Email (Contact_Email),
  KEY LastAccess (LastAccess)
) TYPE=MyISAM;

#
# Dumping data for table 'tblWebUser'
#

INSERT INTO tblWebUser VALUES (1,'admin','admin','','','webEdition','Software GmbH','Waldstrasse 40b','D-76133 Karlsruhe','Germany','','','','','','',0,1076604226,1076604295,0,'/admin',0,'customer.gif','admin','','','Admins');
INSERT INTO tblWebUser VALUES (2,'customer','customer','','','web','user','webland','universe','reality','','','','','','',0,0,0,0,'/customer',0,'customer.gif','customer','','','Users');

#
# Table structure for table 'tblWorkflowDef'
#

CREATE TABLE tblWorkflowDef (
  ID int(11) NOT NULL auto_increment,
  Text varchar(255) NOT NULL default '',
  Type bigint(20) NOT NULL default '0',
  Folders varchar(255) NOT NULL default '0',
  DocType bigint(20) NOT NULL default '0',
  Objects varchar(255) NOT NULL default '',
  Categories varchar(255) NOT NULL default '',
  ObjCategories varchar(255) NOT NULL default '',
  Status tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (ID)
) TYPE=MyISAM;

#
# Dumping data for table 'tblWorkflowDef'
#


#
# Table structure for table 'tblWorkflowDoc'
#

CREATE TABLE tblWorkflowDoc (
  ID int(11) NOT NULL auto_increment,
  workflowID int(11) NOT NULL default '0',
  documentID int(11) NOT NULL default '0',
  userID int(11) NOT NULL default '0',
  Status tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (ID)
) TYPE=MyISAM;

#
# Dumping data for table 'tblWorkflowDoc'
#


#
# Table structure for table 'tblWorkflowDocStep'
#

CREATE TABLE tblWorkflowDocStep (
  ID int(11) NOT NULL auto_increment,
  workflowDocID int(11) NOT NULL default '0',
  workflowStepID bigint(20) NOT NULL default '0',
  startDate bigint(20) NOT NULL default '0',
  finishDate bigint(20) NOT NULL default '0',
  Status tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (ID)
) TYPE=MyISAM;

#
# Dumping data for table 'tblWorkflowDocStep'
#


#
# Table structure for table 'tblWorkflowDocTask'
#

CREATE TABLE tblWorkflowDocTask (
  ID int(11) NOT NULL auto_increment,
  documentStepID bigint(20) NOT NULL default '0',
  workflowTaskID bigint(20) NOT NULL default '0',
  Date bigint(20) NOT NULL default '0',
  todoID bigint(20) NOT NULL default '0',
  Status int(11) NOT NULL default '0',
  PRIMARY KEY  (ID)
) TYPE=MyISAM;

#
# Dumping data for table 'tblWorkflowDocTask'
#


#
# Table structure for table 'tblWorkflowLog'
#

CREATE TABLE tblWorkflowLog (
  ID bigint(20) NOT NULL auto_increment,
  RefID bigint(20) NOT NULL default '0',
  docTable varchar(255) NOT NULL default '',
  userID bigint(20) NOT NULL default '0',
  logDate bigint(20) NOT NULL default '0',
  Type tinyint(4) NOT NULL default '0',
  Description varchar(255) NOT NULL default '',
  PRIMARY KEY  (ID),
  UNIQUE KEY ID (ID),
  KEY ID_2 (ID)
) TYPE=MyISAM;

#
# Dumping data for table 'tblWorkflowLog'
#


#
# Table structure for table 'tblWorkflowStep'
#

CREATE TABLE tblWorkflowStep (
  ID int(11) NOT NULL auto_increment,
  Worktime int(11) NOT NULL default '0',
  timeAction tinyint(1) NOT NULL default '0',
  stepCondition int(11) NOT NULL default '0',
  workflowID int(11) NOT NULL default '0',
  PRIMARY KEY  (ID),
  KEY workflowDef (workflowID)
) TYPE=MyISAM;

#
# Dumping data for table 'tblWorkflowStep'
#


#
# Table structure for table 'tblWorkflowTask'
#

CREATE TABLE tblWorkflowTask (
  ID int(11) NOT NULL auto_increment,
  userID int(11) NOT NULL default '0',
  Edit int(11) NOT NULL default '0',
  Mail int(11) NOT NULL default '0',
  stepID int(11) NOT NULL default '0',
  PRIMARY KEY  (ID),
  KEY Step (stepID)
) TYPE=MyISAM;

#
# Dumping data for table 'tblWorkflowTask'
#


#
# Table structure for table 'tblbanner'
#

CREATE TABLE tblbanner (
  ID bigint(20) NOT NULL auto_increment,
  ParentID bigint(20) NOT NULL default '0',
  Text varchar(255) NOT NULL default '',
  Path varchar(255) NOT NULL default '',
  Icon varchar(64) NOT NULL default '',
  IsFolder tinyint(1) NOT NULL default '0',
  CreatorID bigint(20) NOT NULL default '0',
  CreateDate bigint(20) NOT NULL default '0',
  ModifierID bigint(20) NOT NULL default '0',
  ModifyDate bigint(20) NOT NULL default '0',
  bannerID bigint(20) NOT NULL default '0',
  bannerUrl varchar(255) NOT NULL default '',
  bannerIntID bigint(20) NOT NULL default '0',
  IntHref tinyint(1) NOT NULL default '0',
  maxShow bigint(20) NOT NULL default '0',
  maxClicks bigint(20) NOT NULL default '0',
  IsDefault tinyint(1) NOT NULL default '0',
  clickPrice double NOT NULL default '0',
  showPrice double NOT NULL default '0',
  StartOk tinyint(1) NOT NULL default '0',
  EndOk tinyint(1) NOT NULL default '0',
  StartDate bigint(20) NOT NULL default '0',
  EndDate bigint(20) NOT NULL default '0',
  FileIDs varchar(255) NOT NULL default '',
  FolderIDs varchar(255) NOT NULL default '',
  CategoryIDs varchar(255) NOT NULL default '',
  DoctypeIDs varchar(255) NOT NULL default '',
  IsActive tinyint(1) NOT NULL default '1',
  clicks bigint(20) NOT NULL default '0',
  views bigint(20) NOT NULL default '0',
  Customers varchar(255) NOT NULL default '',
  TagName varchar(255) NOT NULL default '',
  weight tinyint(2) NOT NULL default '4',
  PRIMARY KEY  (ID),
  UNIQUE KEY ID (ID),
  KEY ID_2 (ID),
  KEY IsFolder (IsFolder),
  KEY IsActive (IsActive),
  KEY IsFolder_2 (IsFolder,IsActive)
) TYPE=MyISAM;

#
# Dumping data for table 'tblbanner'
#

INSERT INTO tblbanner VALUES (1,0,'we-Banner','/we-Banner','banner.gif',0,0,0,0,0,499,'http://www.webedition.de',0,0,10000,1000,0,0,0,0,0,1069608060,1069611660,'',',417,','','',1,8,96,',1,','100x600',4);

#
# Table structure for table 'tblbannerclicks'
#

CREATE TABLE tblbannerclicks (
  ID bigint(20) NOT NULL default '0',
  Timestamp bigint(20) default NULL,
  IP varchar(30) NOT NULL default '',
  Referer varchar(255) NOT NULL default '',
  DID bigint(20) NOT NULL default '0',
  Page varchar(255) NOT NULL default '',
  KEY bannerid_date (ID,Timestamp),
  KEY date (Timestamp)
) TYPE=MyISAM;

#
# Dumping data for table 'tblbannerclicks'
#

INSERT INTO tblbannerclicks VALUES (1,1069671723,'127.0.0.1','http://127.0.0.1/we_demo/',443,'');

#
# Table structure for table 'tblbannerprefs'
#

CREATE TABLE tblbannerprefs (
  pref_name varchar(255) NOT NULL default '',
  pref_value varchar(255) NOT NULL default ''
) TYPE=MyISAM;

#
# Dumping data for table 'tblbannerprefs'
#

INSERT INTO tblbannerprefs VALUES ('DefaultBannerID','1');

#
# Table structure for table 'tblbannerviews'
#

CREATE TABLE tblbannerviews (
  ID bigint(20) NOT NULL default '0',
  Timestamp bigint(20) default NULL,
  IP varchar(30) NOT NULL default '',
  Referer varchar(255) NOT NULL default '',
  DID bigint(20) NOT NULL default '0',
  Page varchar(255) NOT NULL default '',
  KEY bannerid_date (ID,Timestamp),
  KEY date (Timestamp)
) TYPE=MyISAM;

#
# Dumping data for table 'tblbannerviews'
#

