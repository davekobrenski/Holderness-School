<?php
session_start();
include('config/_config.inc.php');

if(getenv("BUGSNAG_CLIENT")) {
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

//set some variables for the public page
$publicAssetsDir = "/assets";
$publicTemplatesDir = "/templates";
$publicHeaderFile = "/header.inc.php";
$publicFooterFile = "/footer.inc.php";

$templatesDir = __DIR__ . $publicAssetsDir . $publicTemplatesDir;
$siteHeader = $templatesDir . $publicHeaderFile;
$siteFooter = $templatesDir . $publicFooterFile;

?>