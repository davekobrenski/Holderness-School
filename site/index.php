<?php
include('../_init.inc.php');

if(is_array($_SERVER["argv"]) && count($_SERVER["argv"]) > 0) {
	$args = explode("=", $_SERVER["argv"][0]);
	$page = trim($args[1]);
	$pageInit = getWebPageBySlug($page);
	$pageData = getWebPageDataForOutput($pageInit["pageID"]);
}

if(!$pageData) {
	header("Location: /404");
	exit;
}

//only admins can see disabled pages
if($pageData["enabled"] != 1) {
	if(!check_valid_user(1)) {
		header("Location: /404"); //maybe make this someting else? tell them they must be logged in to see the page?
		exit;
	}
}

//deal with password submit for protected pages
if(trim($pageData["password"]) != '') {
	if(isset($_POST["protectedPage"]) && isset($_POST["pageID"])) {
		//check the password, and make sure the request is from the same page
		if($_POST["pageID"] == $pageData["pageID"]) {
			if(!empty($_POST["password"])) {
				if(encrypt(trim($_POST["password"]), SALT) == $pageData["password"]) {
					//good to go
					if(!in_array($pageData['pageID'], $_SESSION["protected-pages"])) {
						$_SESSION["protected-pages"][] = $pageData['pageID'];
					}
					header("Location: /" . $pageData["urlSlug"]);
					exit;
				} else {
					$loginErrorMsg = "Incorrect password.";
				}
			} else {
				$loginErrorMsg = "Password is required to see this page.";
			}
		} else {
			$loginErrorMsg = "Login failed. Please try again.";
		}	
	}
}

//update data with defaults if empty
$defaults = getWebsiteSettingsData();

if(trim($pageData["pageTitle"]) == '') {
	$pageData["pageTitle"] = $defaults["site_title"];
}

if(trim($pageData["metaDesc"]) == '') {
	$pageData["metaDesc"] = $defaults["site_metadesc"];
}

if(trim($pageData["ogDesc"]) == '') {
	$pageData["ogDesc"] = $defaults["site_description"];
}

$pageData["info_twitter"] = $defaults["info_twitter"];

//figure out where in the nav it is: primary, secondary, etc
$templateClass = '';
if($pageData["parentID"] == null) {
	$templateClass = 'primary';
} else {
	$parentData = getWebPageData($pageData["parentID"]);
	if($parentData["parentID"] == null) {
		$templateClass = 'secondary';
	} else {
		$templateClass = 'tertiary';
	}
}

//set a flag depending on whether there is an image for this page
$hasImgClass = 'no-image';
if($pageData["hasImage"]) {
	$hasImgClass = 'has-image';
}

$pageProtected = false;
$loginFormClass = '';
if(trim($pageData["password"]) != '' && (!is_array($_SESSION["protected-pages"]) || !in_array($pageData['pageID'], $_SESSION["protected-pages"]))) {
	$pageProtected = true;
	$loginFormClass = 'loginform '; //leave the space at the end
}

//experimental. for pages that are a "duplicate" of another, just swap out its content for the other page. needs testing
if($pageData["pageType"] == "link") {
	$linkedPage = getWebPageBySlug($pageData["externalURL"]);
	if(!is_array($linkedPage)) {
		header("Location: /404");
		exit;
	} else {
		$origData = $pageData;
		$pageData = $linkedPage; //swap its data out
		//but keep its original pageID and slug, so that it links and displays in nav properly
		$pageData["pageID"] = $origData["pageID"];
		$pageData["urlSlug"] = $origData["urlSlug"];
		$pageData["getContentFrom"] = $linkedPage["pageID"];
		$pageData["originalData"] = $origData;
	}
}

include($siteHeader);

if($pageProtected === true) {
	include($templatesDir . "/password.inc.php");
} else {
	if($pageData["isHomePage"] == 1) {
		include($templatesDir . "/index.inc.php");
	} else if($pageData["isContactPage"]) {
		include($templatesDir . "/contact.inc.php");
	} else {
		//find out what pageType it is: page, blog, calendar
		if($pageData["pageType"] == 'page') {
			include($templatesDir . "/$templateClass.inc.php");
		} else if($pageData["pageType"] == 'blog') {
			include($templatesDir . "/blog.inc.php");
		} else if($pageData["pageType"] == 'calendar') {
			include($templatesDir . "/calendar.inc.php");
		} else {
			echo '<div class="alert alert-warning">Error: bad page type.</div>';	
		}
	}
}

include($siteFooter);
?>