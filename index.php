<?php
include('_init.inc.php');

$pageData = getWebsiteHomePage();

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

include($siteHeader);
include($templatesDir . "/index.inc.php");
include($siteFooter);	
?>