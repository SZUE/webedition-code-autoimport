<?php
/**
 * webEdition CMS
 *
 * This source is part of webEdition CMS. webEdition CMS is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionCMS/License.txt
 *
 * @category   webEdition
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

		require_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we.inc.php");

		protect();
		
		include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."we.inc.php");
		include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."we_html_tools.inc.php");
		include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_classes/html/we_button.inc.php");
		include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_classes/html/we_multibox.inc.php");
		include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_language/".$GLOBALS["WE_LANGUAGE"]."/sysinfo.inc.php");
		include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_language/".$GLOBALS["WE_LANGUAGE"]."/global.inc.php");
		include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/lib/we/core/autoload.php");
		include_once($_SERVER["DOCUMENT_ROOT"] . "/webEdition/we/include/we_language/" . $GLOBALS["WE_LANGUAGE"] . "/charset/charset.inc.php");
$_charset=$GLOBALS['_language']["charset"];

		function getInfoTable($_infoArr) {
			 global $_charset;
			$out='
			<table align="center" bgcolor="#FFFFFF" cellpadding="4" cellspacing="0" style="border: 1px solid #265da6;" width="610">
  <colgroup>
  <col width="10%"/>
  <col width="90%" />
  </colgroup>
  <tr bgcolor="#f7f7f7" valign="top">
  	<td nowrap="nowrap" style="border-bottom: 1px solid #265da6; border-right: 1px solid #265da6;"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>#'.$_infoArr['ID'].'</b></font></td>
    <td  style="border-bottom: 1px solid #265da6;"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.htmlentities($_infoArr['Date'], ENT_COMPAT,$_charset).'</font></td>
  </tr>
  <tr valign="top">
    <td nowrap="nowrap" style="border-bottom: 1px solid #265da6; border-right: 1px solid #265da6;"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Error type:</b></font></td>
    <td style="border-bottom: 1px solid #265da6;"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><i>'.htmlentities($_infoArr['Type'], ENT_COMPAT,$_charset).'</i></font></td>
  </tr>
  <tr valign="top">
    <td  style="border-bottom: 1px solid #265da6; border-right: 1px solid #265da6;"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Error message:</b></font></td>
    <td style="border-bottom: 1px solid #265da6;"><pre><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><i>'.htmlentities($_infoArr['Text'], ENT_COMPAT,$_charset).'</i></font></pre></td>
  </tr>
  <tr valign="top">
    <td nowrap="nowrap" style="border-bottom: 1px solid #265da6; border-right: 1px solid #265da6;"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Script name:</b></font></td>
    <td style="border-bottom: 1px solid #265da6;"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><i>'.htmlentities($_infoArr['File'], ENT_COMPAT,$_charset).'</i></font></td>
  </tr>
  <tr valign="top">
    <td nowrap="nowrap" style="border-bottom: 1px solid #265da6; border-right: 1px solid #265da6;"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Line number:</b></font></td>
    <td style="border-bottom: 1px solid #265da6;"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><i>'.htmlentities($_infoArr['Line'], ENT_COMPAT,$_charset).'</i></font></td>
  </tr>
  <tr valign="top">
    <td nowrap="nowrap" style="border-bottom: 1px solid #265da6;border-right: 1px solid #265da6;"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Backtrace</b></font></td>
    <td style="border-bottom: 1px solid #265da6;"><pre><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.htmlentities($_infoArr['Backtrace'], ENT_COMPAT,$_charset).'
      </font></pre></td>
  </tr>
  <tr valign="top">
    <td nowrap="nowrap" style="border-bottom: 1px solid #265da6;border-right: 1px solid #265da6;"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Request</b></font></td>
    <td style="border-bottom: 1px solid #265da6;"><pre><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.htmlentities($_infoArr['Request'], ENT_COMPAT,$_charset).'
      </font></pre></td>
  </tr>
  <tr valign="top">
    <td nowrap="nowrap" style="border-right: 1px solid #265da6;"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Server</b></font></td>
    <td style="border-bottom: 1px solid #265da6;"><pre><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.htmlentities($_infoArr['Server'], ENT_COMPAT,$_charset).'
      </font></pre></td>
  </tr>
  <tr valign="top">
    <td nowrap="nowrap" style="border-bottom: 1px solid #265da6;border-right: 1px solid #265da6;"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Session</b></font></td>
    <td style="border-bottom: 1px solid #265da6;"><pre><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.htmlentities($_infoArr['Session'], ENT_COMPAT,$_charset).'
      </font></pre></td>
  </tr>
  <tr valign="top">
    <td nowrap="nowrap" style="border-bottom: 1px solid #265da6;border-right: 1px solid #265da6;"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Global</b></font></td>
    <td ><pre><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.htmlentities($_infoArr['Global'], ENT_COMPAT,$_charset).'
      </font></pre></td>
  </tr>
  
</table>
			
			';
			
			return $out;;
		}

		$we_button = new we_button();

		$buttons = $we_button->position_yes_no_cancel(
				$we_button->create_button("delete",'/webEdition/errorlog.php' . "?delete"),
				$we_button->create_button("refresh",'/webEdition/errorlog.php'),
				$we_button->create_button("close", "javascript:self.close()")
		);
		
		
		

		$_space_size = 10;
		$_parts = array();
		
		
		$db=  new DB_WE();
		if (isset($_REQUEST['delete'])){
			$q="TRUNCATE TABLE `".ERROR_LOG_TABLE."`";
			$db->query($q);
		}
		$q="SELECT * FROM ".ERROR_LOG_TABLE." ORDER BY ID DESC ";
		$db->query($q);
		$count = 1;
	
		$nextprev = "";
		$size = $db->num_rows();
		if ($size>0){
			
			$start = (isset($_REQUEST['start']) ? abs($_REQUEST['start']) : 0);
			$start = $start < 0 ? 0 : $start;
			$start = $start>$size ? $size : $start;

			$back = $start - $count;
			$back = $back < 0 ? 0 : $back;

			$next = $start + $count;
			$next = $next>$size ? $size : $next;

			$ind = 0;
			$nextprev = '<table style="margin-top: 10px;" border="0" cellpadding="0" cellspacing="0"><tr><td>';
			if($start>0){
				$nextprev .= $we_button->create_button("next", '/webEdition/errorlog.php' . "?start=".$back); //bt_back
			}else{
				$nextprev .= $we_button->create_button("next", "", false, 100, 22, "", "", true);
			}

			$nextprev .= getPixel(23,1)."</td><td align='center' class='defaultfont' width='120'><b>".($size-$start);

			//$nextprev .= "&nbsp;-&nbsp".($size-$start+$count);

			$nextprev .= "&nbsp;".$GLOBALS["l_global"]["from"]." ".($size)."</b></td><td>".getPixel(23,1);

			if($next < $size){
				$nextprev .= $we_button->create_button("back",'/webEdition/errorlog.php' . "?start=".$next); //bt_next
			}else{
				$nextprev .= $we_button->create_button("back", "", "", 100, 22, "", "", true);
			}
			$nextprev .= "</td></tr></table>";
			$_parts[] = array(
					  
					  'html'=> $nextprev,
					  'space'=>$_space_size
				  );
			$q="SELECT * FROM ".ERROR_LOG_TABLE." ORDER By ID DESC LIMIT ".$start.",".$count;
			$db->query($q);
		  
			while ($db->next_record()){
				$_parts[] = array(	  
					  'html'=> getInfoTable($db->Record),
					  'space'=>$_space_size  
				);
			}
		} else {
			$_parts[] = array(
				'html'=> 'No entries found',
				'space'=>$_space_size
				);
		}	

?>
<html>
<head>
 
<title><?php print 'Errorlog';?></title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_charset;?>">

<script type="text/javascript" src="<?php print JS_DIR; ?>attachKeyListener.js"></script>
<script type="text/javascript" src="<?php print JS_DIR; ?>keyListener.js"></script>
<script type="text/javascript">
	function closeOnEscape() {
		return true;
	}
</script>

<?php
		print STYLESHEET;
?>

</head>

<body class="weDialogBody" style="overflow:hidden;" onLoad="self.focus();">
<div id="info" style="display: block;">
<?php		
		print we_multiIconBox::getJS();
		print we_multiIconBox::getHTML('',700, $_parts,30,$buttons,-1,'','',false, "", "", 620, "auto");
		
?>
</div>
</body>
</html>