<?php
session_start();
include('config/_config.inc.php');

/**
 * Init & Authentication for the PUBLIC site files
 * this script gets included at the top of any page, and loads all config vars in.
*/

if(getenv("BUGSNAG_CLIENT") !== false) {
	$bugsnag = new Bugsnag_Client(getenv("BUGSNAG_CLIENT"));
	$bugSnagScriptName = $_SERVER["SERVER_NAME"] . $_SERVER["SCRIPT_NAME"];
	if($_SERVER["QUERY_STRING"]) $bugSnagScriptName .= "?" . $_SERVER["QUERY_STRING"];
	$bugsnag->setContext($bugSnagScriptName);
	
	if(check_valid_user(1)) {
		$bugsnag->setUser(array(
	    	'name' => $logged_user["fname"] . ' ' . $logged_user["lname"],
			'email' => $logged_user["email"]
		));
	}
	
	set_error_handler(array($bugsnag, "errorHandler"));
	set_exception_handler(array($bugsnag, "exceptionHandler"));
}

//get the directory of the assets for this site's chosen template, as specified in dot env, or use default "assets" dir
if(getenv("SITE_TEMPLATE") !== false) {
	$publicAssetsDir = getenv("CMS_ADMIN_BASE") . "/site/" . getenv("SITE_TEMPLATE"); // <-- this looks like: "/config/site/holderness" - notice the leading slash
	$testDir = __DIR__ . $siteTemplateDir;
	if(!is_dir($testDir)) {
		$publicAssetsDir = "/assets";
	}
} else {
	$publicAssetsDir = "/assets";
}

//set some variables for the public page
$publicTemplatesDir = "/templates"; //this should live inside the $publicAssetsDir
$publicHeaderFile = "/header.inc.php"; //these should live inside the $publicTemplatesDir
$publicFooterFile = "/footer.inc.php";

//now we can save the include paths for the template files for use elsewhere
//TO DO: make these globals instead of vars
$templatesDir = __DIR__ . $publicAssetsDir . $publicTemplatesDir;
$siteHeader = $templatesDir . $publicHeaderFile;
$siteFooter = $templatesDir . $publicFooterFile;

?>