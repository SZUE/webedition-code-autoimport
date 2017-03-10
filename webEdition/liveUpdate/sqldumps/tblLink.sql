###ONCOL(Icon,###TBLPREFIX###tblUser) UPDATE ###TBLPREFIX###tblLink l SET l.Type="href" WHERE l.Name LIKE "%we_jkhdsf_%" AND Type="txt";###
/* query separator */
###ONCOL(Icon,###TBLPREFIX###tblUser) UPDATE ###TBLPREFIX###tblLink l SET l.Type="txt" WHERE l.Type IN ('text','application');###
/* query separator */
###ONCOL(Icon,###TBLPREFIX###tblUser) UPDATE ###TBLPREFIX###tblLink l SET l.Type="attrib" WHERE l.Name IN('Charset','useMetaTitle','origwidth','origheight','width','height') AND Type="txt";###
/* query separator */
###ONCOL(Icon,###TBLPREFIX###tblUser) UPDATE ###TBLPREFIX###tblLink l SET l.Type="block" WHERE l.Type='list';###
/* query separator */
###ONCOL(DID,###TBLPREFIX###tblLink)UPDATE ###TBLPREFIX###tblLink SET Type="object" WHERE DocumentTable="tblFile" AND nHash=x'fe40feec71672d515faa242b1cff2165';
/* query separator */
###ONCOL(DID,###TBLPREFIX###tblLink)UPDATE ###TBLPREFIX###tblLink SET Type="link" WHERE DocumentTable="tblFile" AND nHash IN (x'09d2a3e9b7efc29e9a998d7ae84cca87',x'b6b8646a49103a66f9d9e2aae212bdbe');