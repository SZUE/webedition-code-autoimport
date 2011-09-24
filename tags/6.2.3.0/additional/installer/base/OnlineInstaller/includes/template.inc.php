<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<title><?php echo $GLOBALS['lang']['Template']['title']; if(isset($_REQUEST['debug']) && isset($LU_Version)) { echo " &bull; Version ".$LU_Version; }?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['leInstallerCharset'];?>" />

	<!-- FavIcon -->
	<link rel="SHORTCUT ICON" href="<?php echo LE_ONLINE_INSTALLER_URL; ?>/img/leLayout/favicon.ico" />

	<!-- Use styles for forms -->
	<link type="text/css" rel="stylesheet" id="leCSSForm" href="<?php echo LE_ONLINE_INSTALLER_URL; ?>/css/leForm.css.php" media="screen" />

	<!-- Use styles for content -->
	<link type="text/css" rel="stylesheet" id="leCSSContent" href="<?php echo LE_ONLINE_INSTALLER_URL; ?>/css/leContent.css.php" media="screen" />

	<!-- Use button styles from Online-Installer -->
	<link type="text/css" rel="stylesheet" id="leCSSButton" href="<?php echo LE_ONLINE_INSTALLER_URL; ?>/css/leButton.css.php" media="screen" />

	<!-- Use layout styles from Online-Installer -->
	<link type="text/css" rel="stylesheet" id="leCSSLayout" href="<?php echo LE_ONLINE_INSTALLER_URL; ?>/css/leLayout.css.php" media="screen" />

	<!-- Use progress bar styles from Online-Installer -->
	<link type="text/css" rel="stylesheet" id="leCSSProgressBar" href="<?php echo LE_ONLINE_INSTALLER_URL; ?>/css/leProgressBar.css.php" media="screen" />

	<!-- Use status styles from Online-Installer -->
	<link type="text/css" rel="stylesheet" id="leCSSStatus" href="<?php echo LE_ONLINE_INSTALLER_URL; ?>/css/leStatus.css.php" media="screen" />

	<!-- Use styles for forms -->
	<link type="text/css" rel="stylesheet" id="leCSSPrint" href="<?php echo LE_ONLINE_INSTALLER_URL; ?>/css/lePrint.css.php" media="print" />

	<!-- JavaScript Effects -->
	<script type="text/javascript" id="leJSEffects" src="<?php echo LE_ONLINE_INSTALLER_URL; ?>/js/leEffects.js"></script>

	<!-- JavaScript Form -->
	<script type="text/javascript" id="leJSForm" src="<?php echo LE_ONLINE_INSTALLER_URL; ?>/js/leForm.js"></script>

	<!-- JavaScript Content -->
	<script type="text/javascript" id="leJSContent" src="<?php echo LE_ONLINE_INSTALLER_URL; ?>/js/leContent.js"></script>

	<!-- JavaScript Buttons -->
	<script type="text/javascript" id="leJSButton" src="<?php echo LE_ONLINE_INSTALLER_URL; ?>/js/leButton.js"></script>

	<!-- JavaScript Progress Bar -->
	<script type="text/javascript" id="leJSProgressBar" src="<?php echo LE_ONLINE_INSTALLER_URL; ?>/js/leProgressBar.js"></script>

	<!-- JavaScript Status Bar -->
	<script type="text/javascript" id="leJSStatus" src="<?php echo LE_ONLINE_INSTALLER_URL; ?>/js/leStatus.js"></script>

	<!-- external Javascript libraries -->
	<script src="<?php echo LE_ONLINE_INSTALLER_URL; ?>/js/external/prototype.js" type="text/javascript" ></script>
	<script src="<?php echo LE_ONLINE_INSTALLER_URL; ?>/js/external/scriptaculous.js?load=effects" type="text/javascript" ></script>

	<script type="text/JavaScript">
		var nextUrl = "";
		var backUrl = "";
		var repeatUrl = "";

		function togglePhpinfo() {
			if($('phpinfo').style.display == 'none') {
				$('phpinfo').appear({duration:0.4});
			} else {
				$('phpinfo').fade({duration:0.4});
			}
		}
	</script>

</head>

<body>

<div id="leToolbar">
<div style="text-align:left; float:left; height:22px; padding:3px 10px 0px 24px; background:url(<?php echo leEmbeddedImage::get(LE_ONLINE_INSTALLER_PATH . "/img/leLayout/favicon.gif"); ?>) 3px center no-repeat;">webEdition Online Installer Version <?php print $LU_Version;?></div>
<div style="text-align:right;float:right; padding:1px 10px 0px 10px;">
<a style="cursor:pointer; float:right; padding-right:4px;" title="phpinfo" onclick="javascript:togglePhpinfo();"><img src="<?php echo leEmbeddedImage::get(LE_ONLINE_INSTALLER_PATH . "/img/leLayout/php.gif"); ?>" alt="phpinfo" style="" id="leEmoticonImg" /></a>
</div>
</div>


<form action="<?php print LE_INSTALLER_ADAPTER_URL ?>" target="leLoadFrame" method="post" name="leWebForm">
<input type="hidden" name="leWizard" value="" />
<input type="hidden" name="leStep" value="" />
<input type="hidden" name="liveUpdateSession" value="" />
<?php if(isset($_SESSION['testUpdate']) && $_SESSION['testUpdate'])  {echo '<input type="hidden" name="testUpdate" value="1" />';$_REQUEST['testUpdate']=1;} else {echo '<input type="hidden" name="testUpdate" value="0" />';$_REQUEST['testUpdate']=0;}; ?>
<div id="leLogo">
	<img src="<?php echo leEmbeddedImage::get(LE_ONLINE_INSTALLER_PATH . "/img/leLayout/logo.gif"); ?>" alt="" id="leLogoImg" />
</div>
<div id="leCategories">
</div>
<div id="leInstaller">
	<div id="leLeft"></div>
	<div id="leRight"></div>
	<div id="leCenter">
		<div id="leHead">
			<div id="leTitle">
				<?php echo $GLOBALS['lang']['Template']['headline']; ?>
			</div>
			<div id="leEmoticon" style="padding-top:12px;">
			</div>
		</div>
		<div id="leMain">
			<div id="leStatus">
				<?php echo leStatus::get($OnlineInstaller, 'leStatus'); ?>
			</div>
			<div id="leContent">

			</div>
			<div id="leProduct">
				<a href="http://www.webedition.org" target="_blank" title="www.webedition.org"><img src="<?php echo leEmbeddedImage::get(LE_ONLINE_INSTALLER_PATH . "/img/leLayout/product.gif"); ?>" border="0" alt="" id="leProductImg" /></a>
			</div>
		</div>
		<div id="leFoot">
			<?php echo leButton::get('back', $GLOBALS['lang']['Buttons']['back'], 'javascript:leForm.back();', 100, 22, "", true, false); ?>
			<?php echo leButton::get('next', $GLOBALS['lang']['Buttons']['next'], 'javascript:leForm.next();', 100, 22, "", false, false); ?>
			<?php echo leProgressBar::get('leProgress'); ?>
			<?php echo leButton::get('reload', 'function_reload.gif', 'javascript:leForm.reload();', 40, 22, "", true, false); ?>
			<?php echo leButton::get('print', $GLOBALS['lang']['Buttons']['print'], 'javascript:window.print();', 100, 22, "", false, false); ?>
		</div>
	</div>
</div>

<div id="debug" style="visibility: <?php echo (isset($_REQUEST['debug']) ? "block" : "hidden" ); ?>">
	<iframe id="leLoadFrame" src="<?php print $OnlineInstaller->getFirstStepUrl(); ?>" name="leLoadFrame" width="100%" height="100" frameborder="0"></iframe>
</div>	

<div id="phpinfo" style="display: none;">
	<iframe id="lePhpinfoFrame" src="<?php print LE_INSTALLER_ADAPTER_URL ?>?phpinfo=true" name="lePhpinfoFrame" width="100%" height="444" frameborder="0"></iframe>
	<a style="cursor:pointer; float:right; margin:0px; padding:2px 4px 0px 0px;" onclick="javascript:togglePhpinfo();"><?php echo $GLOBALS['lang']['Buttons']['close'];?></a>
</div>
<script type="text/javascript">
	leButton.hide('print');
	document.onkeypress = leForm.checkSubmit;
</script>
</form>
</body>
</html>