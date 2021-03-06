<?php
	$cacheBust = 342068;
	if(!$pageData) {
		header("Location: /404");
		exit;
	}
	if($_SERVER["REMOTE_ADDR"] == "216.107.193.66" || $_SERVER["REMOTE_ADDR"] == "74.75.100.53") {
		if(!empty($_COOKIE['intVisID'])) {
			$intVisID = $_COOKIE['intVisID'];
		} else {
			$intVisID = uniqid();
			setcookie('intVisID', $intVisID, time() + (86400 * 30));
		}
	}

?><!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie10 lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie10 lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie10 lt-ie9"> <![endif]-->
<!--[if IE 9]>         <html class="no-js lt-ie10"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-js"> <!--<![endif]-->
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<title><?=strip_tags($pageData["pageTitle"])?></title>
		<meta name="description" content="<?=$pageData["metaDesc"]?>">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<meta name="apple-mobile-web-app-capable" content="yes">		
		<?php		
			$image = getCroppedFocalPointImageForPage($pageData["pageID"], 1200, 630, 90);
			$twitImage = getCroppedFocalPointImageForPage($pageData["pageID"], 120, 120, 90);
			//more info on formats and validation: http://moz.com/blog/meta-data-templates-123
		?>
		
		<!-- Open Graph data -->
		<meta property="og:title" content="<?=$pageData["pageTitle"]?>" />
		<meta property="og:type" content="website" />
		<meta property="og:url" content="<?=PUBLIC_URL_BASE . ($pageData["isHomePage"] == 1 ? '' : $pageData["urlSlug"])?>" />
		<meta property="og:description" content="<?=$pageData["ogDesc"]?>" />
		<meta property="og:site_name" content="<?=$thisOrgName?>" />
		<meta property="og:image" content="https://<?=$_SERVER["SERVER_NAME"]?><?=$image["url"]?>" />
		
		<!-- swiftype search data -->
		<meta class="swiftype" name="title" data-type="string" content="<?=$pageData["pageTitle"]?>" />
		<meta class="swiftype" name="image" data-type="enum" content="https://<?=$_SERVER["SERVER_NAME"]?><?=$twitImage["url"]?>" />
		
		<?php if(!empty($pageData["info_twitter"])) { ?>
		<!-- Twitter Card data -->
		<meta name="twitter:card" content="summary">
		<meta name="twitter:site" content="<?=$pageData["info_twitter"]?>">
		<meta name="twitter:title" content="<?=$pageData["pageTitle"]?>">
		<meta name="twitter:description" content="<?=$pageData["ogDesc"]?>">
		<meta name="twitter:creator" content="<?=$pageData["info_twitter"]?>">
		<meta name="twitter:image" content="https://<?=$_SERVER["SERVER_NAME"]?><?=$twitImage["url"]?>" />
		<?php } ?>
		
		<link rel="apple-touch-icon-precomposed" sizes="144x144" href="https://<?=$_SERVER["SERVER_NAME"]?>/assets/icons/apple-touch-icon-144x144.png" />
		<link rel="apple-touch-icon-precomposed" sizes="152x152" href="https://<?=$_SERVER["SERVER_NAME"]?>/assets/icons/apple-touch-icon-152x152.png" />
		<link rel="icon" type="image/png" href="https://<?=$_SERVER["SERVER_NAME"]?>/assets/icons/favicon-32x32.png" sizes="32x32" />
		<link rel="icon" type="image/png" href="https://<?=$_SERVER["SERVER_NAME"]?>/assets/icons/favicon-16x16.png" sizes="16x16" />
		<meta name="application-name" content="Holderness School"/>
		<meta name="msapplication-TileColor" content="#FFFFFF" />
		<meta name="msapplication-TileImage" content="https://<?=$_SERVER["SERVER_NAME"]?>/assets/icons/mstile-144x144.png" />
		
		<?php
			$typeKitJS = "typekitConfig = {kitId: 'nuf7kjc'};(function() {var tk = document.createElement('script');tk.src = '//use.typekit.net/' + typekitConfig.kitId + '.js';tk.async = 'true';tk.onload = tk.onreadystatechange = function() {var rs = this.readyState;if (rs && rs != 'complete' && rs != 'loaded') return;try { Typekit.load(typekitConfig); } catch (e) {}};var s = document.getElementsByTagName('script')[0];s.parentNode.insertBefore(tk, s);})();";
			
		if(getenv("BUGSNAG_JS_CLIENT")) { ?>
			<script src="//d2wy8f7a9ursnm.cloudfront.net/bugsnag-2.min.js" data-apikey="<?=getenv("BUGSNAG_JS_CLIENT")?>"></script>
			<script><?php $scriptName = $_SERVER["SERVER_NAME"] . $_SERVER["SCRIPT_NAME"];
			if($_SERVER["QUERY_STRING"]) $scriptName .= "?" . $_SERVER["QUERY_STRING"];
			echo 'Bugsnag.context = "'.$scriptName.'";' . ""; 
			if(check_valid_user(1)) {
			echo 'Bugsnag.user = {id: '.$logged_user["id"].',name: "'.$logged_user["fname"].' '.$logged_user["lname"].'",email: "'.$logged_user["email"].'"};' . "\n";
			} 
			echo $typeKitJS;
			?></script>
		<?php } else { ?>
		<script><?=$typeKitJS?></script>	
		<?php } ?>
		
		<script><?php $scriptName = $_SERVER["SERVER_NAME"] . $_SERVER["SCRIPT_NAME"];
				if($_SERVER["QUERY_STRING"]) $scriptName .= "?" . $_SERVER["QUERY_STRING"];
				echo 'Bugsnag.context = "'.$scriptName.'";' . ""; 
				if(check_valid_user(1)) {
				echo 'Bugsnag.user = {id: '.$logged_user["id"].',name: "'.$logged_user["fname"].' '.$logged_user["lname"].'",email: "'.$logged_user["email"].'"};' . "";
				} ?> typekitConfig = {kitId: 'nuf7kjc'};(function() {var tk = document.createElement('script');tk.src = '//use.typekit.net/' + typekitConfig.kitId + '.js';tk.async = 'true';tk.onload = tk.onreadystatechange = function() {var rs = this.readyState;if (rs && rs != 'complete' && rs != 'loaded') return;try { Typekit.load(typekitConfig); } catch (e) {}};var s = document.getElementsByTagName('script')[0];s.parentNode.insertBefore(tk, s);})();</script>
		<script type="text/javascript" src="/assets/js/modernizr.min.js"></script>
		<link rel="stylesheet" href="/assets/css/styles.<?=$cacheBust?>.css">
		<?=$pageData["headerInject"]?>
		
		<script>
			!function(g,s,q,r,d){r=g[r]=g[r]||function(){(r.q=r.q||[]).push(
			arguments)};d=s.createElement(q);q=s.getElementsByTagName(q)[0];
			d.src='//d1l6p2sc9645hc.cloudfront.net/tracker.js';q.parentNode.
			insertBefore(d,q)}(window,document,'script','_gs');		
			_gs('GSN-660130-Z'); 
			<?php if(check_valid_user(1)) {
				$userAvatar = getUserAvatarUrl($logged_user["token"], 75, 75, 95);
				if(strpos($userAvatar, 'http') !== 0) {
					$userAvatar = "https://" . $publicDomain . $userAvatar;
				}
			?>
			_gs('identify', {
				id:    '<?=$logged_user["id"]?>',
				name:  '<?=$logged_user["fname"].' '.$logged_user["lname"]?>',
				email: '<?=$logged_user["email"]?>',
				avatar: '<?=$userAvatar?>'
			});
			<?php } else {
				if($_SERVER["REMOTE_ADDR"] == "216.107.193.66") { ?>
			_gs('identify', {
				id: '<?=$intVisID?>',
				name:  'Holderness Internal [<?=$intVisID?>]',
				avatar: 'https://www.holderness.org/config/files/client/default_user.png?v=1438348366'
			});
				<?php } else if($_SERVER["REMOTE_ADDR"] == "74.75.100.53") { ?>
			_gs('identify', {
				id: '<?=$intVisID?>',
				name:  'BBM Designs [<?=$intVisID?>]',
				avatar: 'https://s3-us-west-2.amazonaws.com/slack-files2/avatars/2015-05-26/5057653320_727bd8dbe31e23add7fa_192.jpg'
			});		
				<?php } else {}
			} ?>
		</script>
   </head>
   <body <?='class="'.$loginFormClass.''.($pageData["isHomePage"] == 1 ? 'index' : ($pageData["parentID"] == null || $pageData["isContactPage"] == 1 ? 'primary' : $templateClass)).' '.($pageData["isContactPage"] == 1 ? 'contact' : '').' '.$hasImgClass.' '.$hasAlertClass.'"'?>>
		<?php
			//google analytics code, if it's set in the 'site settings' tab of the cms
			include($templatesDir . "/analytics.inc.php");
		?>
	   <div id="preloader">
		   <div class="preload-msg">
			   <div class="preload-inner">
				   <p><img src="/assets/images/holderness-stacked.gif" alt=""></p>
					<p><img src="/assets/images/preloader.gif" alt=""></p>
			   </div>
		   </div>
		</div>
		
	   <div class="main-wrapper preloader-hide">
		   <?php
				//notifications - hello bar style
				include($templatesDir . "/hb-alerts.inc.php");
			?>
		   <!-- main navigation -->
			<nav class="navbar navbar-inverse main-nav navbar-fixed-top">
				<div class="container-fluid">
					<div class="navbar-header">
						<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
							<span class="sr-only">Toggle navigation</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
						<a class="navbar-brand" href="/">
							<img src="/assets/images/<?=($pageData["isHomePage"] == 1 ? 'holderness-logo-white.png' : 'holderness-logo-color-272.gif')?>">
						</a>
					</div>
					<div id="navbar" class="navbar-collapse collapse">
						<ul class="nav navbar-nav navbar-right">
							<?php
								$topNav = getWebPageTreeArray(1, true, false);
								if(is_array($topNav) && count($topNav) > 0) {
									foreach($topNav as $link) {
										if($link["enabled"] == 1) {
											if($link["pageType"] == "link") {
												$linkedPage = getWebPageBySlug($link["externalURL"]);
												if(is_array($linkedPage)) {
													$navTitle = $link["navTitle"];
													$link = $linkedPage;
													$link["navTitle"] = $navTitle;
												} else {
													$link["enabled"] = 0; //hide it, because it's invalid
												}
											}
											
											if($link["isHomePage"] != 1 && $link["enabled"] == 1) {
												echo '<li class="'.($pageData["topParent"] == $link["pageID"] ? 'active' : '').'"><a href="'.($link["isExternal"] ? $link["externalURL"] : '/' . $link["urlSlug"]).'" '.($link["isExternal"] ? 'target="_blank"' : '').'>'.strtolower($link["navTitle"]).'</a></li>';
											}
										}
									}
								}
								echo '<li><span class="social-links">
								<a href="javascript:;" class="search-modal-open btn btn-social-icon btn-primary btn-round" data-toggle="tooltip" data-placement="bottom" title="Search the website">
									<i class="fa fa-search fa-flip-horizontal"></i>
								</a>';
									$socialIcons = getSocialIconsArray();
									if(is_array($socialIcons) && count($socialIcons) > 0) {
										foreach($socialIcons as $key=>$icon) {
											$network = $icon["network"];
											$nw = $network; 
											$nwIcon = $network;
											$addClass = ''; 
											//displayName //network //username
											$toolTip = ucwords($network) . ": " . $icon["displayName"];
											
											switch($network) {
												case "vimeo": $nwIcon = "vimeo-square"; break;
												case "google": $nwIcon = "google-plus"; $nw = "google-plus"; break;
												case "smugmug": $nwIcon = "camera"; $addClass = 'btn-brightgreen'; break;
											}
											echo '<button class="btn btn-social-icon btn-round btn-'.$nw.' '.$addClass.'" data-href="'.$icon["visitUrl"].'" data-key="'.$key.'" data-toggle="tooltip" data-placement="bottom" title="'.$toolTip.'"><i class="fa fa-'.$nwIcon.'"></i></button> ';
										}
									}
									//echo '<button class="btn btn-social-icon btn-round btn-brightyellow" data-href="/search" data-key="srch"><i class="fa fa-search"></i></button> ';
									echo '<button class="btn btn-social-icon btn-round login-icon" data-href="'.$adminBaseURL.'" data-key="login" data-toggle="tooltip" data-placement="bottom" title="Administrators"></button> '; //assets/images/hs-login-icon.png
								echo '</span></li>';
							?>
						</ul>
					</div><!--/.nav-collapse -->
				</div>
			</nav>
		<div class="page-content">	