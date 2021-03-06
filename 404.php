<?php
include('_init.inc.php');
header("HTTP/1.0 404 Not Found");

$pageData = getWebsiteHomePage();

$pageData["isHomePage"] = 0;
$pageData["parentID"] = $pageData["pageID"];
$pageData["404"] = true;

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

//set a flag if their is a site-wide notification bar
$hasAlertClass = '';
if($defaults["alert_showing"] == 1) {
	$hasAlertClass = 'has-alert';
}

$templateClass = 'tertiary';
$pageData["navTitle"] = "Page Not Found";

$specialContentBlocks = array();
$specialContentBlocks[0]["blockType"] = "basic";
$specialContentBlocks[0]["isMarkdown"] = 1;
$specialContentBlocks[0]["htmlData"] = "The page you requested was not found.

The page may have moved, or you may have stumbled upon an outdated link. In any case, you can use the navigation above to find what you are looking for. 

If you have found a link on the page that is broken and needs updating, feel free to [contact us](/contact). Thanks!";

include($siteHeader);
include($templatesDir . "/$templateClass.inc.php");
include($siteFooter);	
?>