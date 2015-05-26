<?php
include('../../_init.inc.php');

$smarty = new \Michelf\SmartyPants();
$parser = new ParsedownExtra();

if(!empty($_POST["blockID"])) {
	$blockData = getPageContentBlock($_POST["blockID"]);
	$optionsJson = json_decode($blockData["optionsJson"], true);
	if(!is_array($optionsJson)) $optionsJson = array();
	if(!is_array($optionsJson["calendars"])) {
		if(!empty($optionsJson["calendars"])) {
			$optionsJson["calendars"] = array($optionsJson["calendars"]);
		} else {
			$optionsJson["calendars"] = array();
		}
	}
	$useCals = $optionsJson["calendars"];
	
	arr($useCals);
	
	//we'll want to grab all events for the given month - so we need a month
	//ideally, we need some google calendars. but, if none found, we'll still otput a lovely calendar anyway.
	
} else {
	echo '<div class="alert alert-danger">Error: calendar content not found.</div>';
}