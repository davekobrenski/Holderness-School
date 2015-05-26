<?php
include('../../_init.inc.php');
header('Content-Type: application/json');

$smarty = new \Michelf\SmartyPants();
$parser = new ParsedownExtra();

$eventsOut = array();

$startShowing = $_GET['from'] / 1000;
$endShowing = $_GET['to'] / 1000;

$numDays = date("t", $startShowing);

$i=0;
while ($i < $numDays) {
	$tday = date("Y-m-d", strtotime("+$i days", $startShowing));
	$allEvents["$tday"] = array();
	$i++;
}

$optParams = array();
$optParams["timeMin"] = date("Y-m-d", $startShowing) . "T00:00:00Z";
$optParams["timeMax"] = date("Y-m-d", $endShowing) . "T10:00:00Z";

//echo json_encode($optParams); exit;

if(!empty($_GET["blockID"])) {
	$blockData = getPageContentBlock($_GET["blockID"]);
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
	
	$google = getAllSocialAccountsArray("google");
	$enabled = getEnabledGoogleCals();
	
	if((is_array($enabled) && count($enabled) > 0) && (is_array($google) && count($google) > 0) && is_array($useCals) && count($useCals) > 0) {
		foreach($google as $account) {
			$options = unserialize($account["options"]);
			if(is_array($options)) { //only go here if we have some saved calendars; we can then find out if its in the content block's list
				$validCals = array_intersect($options, $useCals); //limit to what user has selected
				if(is_array($validCals) && count($validCals) > 0) {
					try {
						$client = new Google_Client();
						$client->setClientId(GOOGLE_CLIENT_ID);
						$client->setClientSecret(GOOGLE_OAUTH_SECRET);
						$auth = unserialize(base64_decode($account["auth"]));
						$tokens = unserialize(base64_decode($auth[2]));		
						$client->setAccessToken(json_encode($tokens));
						$service = new Google_Service_Calendar($client);
						
						foreach($validCals as $calID) {
							$events = $service->events->listEvents($calID, $optParams);
							$calendar = $service->calendars->get($calID);
							$calendarName = $calendar->getSummary();
							
							while(true) {
								foreach ($events->getItems() as $event) {
									$start = $event->getStart();
									$end = $event->getEnd();
									$eventData = array();
									$eventData["summary"] = $smarty->transform($event->getSummary()); //$event->getSummary();
									
									$eventData["description"] = $smarty->transform($event->getDescription()); //$event->getDescription();
									$eventData["description"] = strip_tags($parser->text($eventData["description"]), "<a><p><em>");
									
									$eventData["location"] = $event->getLocation();
									$eventData["calendar"] = $calendarName;
									
									if($start->date) {
										$startDate = $start->date;
									} else {
										$startDate = $start->dateTime;
									}
									
									if($end->date) {
										$endDate = $end->date;
									} else {
										$endDate = $end->dateTime;
									}
									
									$eventData["startDay"] = date("Y-m-d", strtotime($startDate));
									$eventData["startTime"] = date("H:i:s", strtotime($startDate));
									$eventData["endDay"] = date("Y-m-d", strtotime($endDate));
									$eventData["endTime"] = date("H:i:s", strtotime($endDate));
									
									//add the event to $eventsOut
									if($eventData["startTime"] == "00:00:00") {
										$eTitle = $eventData["summary"];
										$eClass = 'event-inverse'; //event-info, event-inverse
									} else {
										$tm = date("g:i", strtotime($startDate));
										$ampm = date("a", strtotime($startDate));
										$eTitle = '<span class="tme">' . $tm . $ampm . '</span> ' . $eventData["summary"];
										$eClass = 'event-info'; //event-info, event-inverse
									}
									
									if(trim($eventData["summary"]) != '') {
										//$eventsOut[] = array(
										$thisEvent = array(
											'id' => $event->id,
											'title' => $eTitle,
											'url' => '/assets/ajax/calEventData.php?eID='.$event->id.'&cID='.$calID.'&sID='.$account["socialID"],
											'class' => $eClass,
											'summary' => $eventData["summary"],
											'description' => $eventData["description"],
											'location' => $eventData["location"],
											'start' => strtotime($startDate) . '000',
											'end' => (strtotime($endDate) .'000') - 100
										);
									}
									
									
									foreach($allEvents as $dateKey=>$dayArray) {
										if(strtotime($eventData["startDay"]) <= strtotime($dateKey) && (strtotime($endDate)-1) >= strtotime($dateKey)) {
											//then add it to this day
											$allEvents["$dateKey"]["{$eventData['startTime']}"][] = $thisEvent;
										}
									}
									
									
								}
								$pageToken = $events->getNextPageToken();
								if ($pageToken) {
									$optParams["pageToken"] = $pageToken; //add it to our existing array
									$events = $service->events->listEvents($calID, $optParams);
								} else {
									break;
								}
							}
						}
					} catch(Exception $e) {
						//don't really need to do anything...?
					}
				}
			}
		}
	}
}

$eventIDs = array(); //to make sure we don't get the same event twice

//echo json_encode($allEvents, JSON_PRETTY_PRINT, JSON_UNESCAPED_UNICODE); exit;
foreach($allEvents as $day=>$events) {
	if(is_array($events) && count($events) > 0) {
		ksort($events);
		foreach($events as $time=>$things) {
			if($time == "00:00:00") {
				foreach($things as $event) {
					if(!in_array($event["id"], $eventIDs)) {
						$eventIDs[] = $event["id"];
						$event["weekDay"] = date("D", strtotime($day));
						$event["dayNum"] = date("j", strtotime($day));
						$event["monthYear"] = date("F Y", strtotime($day));
						$eventsOut[] = $event;
					}
				}
			} else {
				$atTime = date("g:i", strtotime("$day $time"));
				$atTimeAMPM = date("a", strtotime("$day $time"));
				foreach($things as $event) {
					if(!in_array($event["id"], $eventIDs)) {
						$eventIDs[] = $event["id"];
						$event["atTime"] = $atTime.' '.$atTimeAMPM;
						$event["weekDay"] = date("D", strtotime($day));
						$event["dayNum"] = date("j", strtotime($day));
						$event["monthYear"] = date("F Y", strtotime($day));
						$eventsOut[] = $event;
					}
				}
			}
		}
	}
}

echo json_encode(array('success' => 1, 'result' => $eventsOut));
?>