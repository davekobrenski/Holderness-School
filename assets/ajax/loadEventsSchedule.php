<?php
include('../../_init.inc.php');

//set a date range to show here:
$numDays = 3; //how many days to show at once.
$dayOffset = 0; //not using - normally, get this from post values. default is 0

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
}

if(!empty($optionsJson["days"]) && is_numeric($optionsJson["days"])) {
	$numDays = $optionsJson["days"];
}

$google = getAllSocialAccountsArray("google");
$enabled = getEnabledGoogleCals();
$allEvents = array();

$nextBackOffset = $dayOffset - $numDays;
$nextFwdOffset = $dayOffset + $numDays;

if($dayOffset >= 0) $dayOffset = "+" . $dayOffset;

$startShowing = strtotime(date("Y-m-d") . " $dayOffset days"); //today
$endShowing = strtotime("+$numDays days", $startShowing); //offset from $startShowing

$i=0;
while ($i < $numDays) {
	$tday = date("Y-m-d", strtotime("+$i days", $startShowing));
	$allEvents["$tday"] = array();
	$i++;
}

$optParams = array();
$optParams["timeMin"] = date("Y-m-d", $startShowing) . "T00:00:00Z";
$optParams["timeMax"] = date("Y-m-d", $endShowing) . "T10:00:00Z";

if(!empty($_POST["title"])) echo '<h2>' . (!empty($_POST["linkTo"]) ? '<a href="'.$_POST["linkTo"].'">' : '') . $_POST["title"] . (!empty($_POST["linkTo"]) ? ' <i class="fa fa-arrow-circle-o-right"></i></a>' : '') . '</h2>';

echo '<ul class="list-unstyled">';

	if((is_array($enabled) && count($enabled) > 0) && (is_array($google) && count($google) > 0) && is_array($useCals) && count($useCals) > 0) {
		foreach($google as $account) {
			$options = unserialize($account["options"]);
			if(is_array($options)) { //only go here if we have some saved calendars; we can then find out if its in the content block's list
				$validCals = array_intersect($options, $useCals); //limit to what user has selected
				if(is_array($validCals) && count($validCals) > 0) {	
					try{
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
									$eventData["description"] = strip_tags($parser->text($eventData["description"]), "<a>");
									
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
									
									//find out what day(s) in our array it fits into
									foreach($allEvents as $dateKey=>$dayArray) {
										if(strtotime($eventData["startDay"]) <= strtotime($dateKey) &&
											(strtotime($endDate)-1) >= strtotime($dateKey)) {
											//then add it to this day
											$allEvents["$dateKey"]["{$eventData['startTime']}"][] = $eventData;
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
						echo '<li>Error connecting to google: '.$e->getMessage().'</li>';
					}
				}
			}
		}
		
		//arr($allEvents);
		$count = 0;
		foreach($allEvents as $day=>$evs) {
			$count += count($evs);
		}
		
		if($count == 0) {
			if(!empty($_POST["title"])) echo '<li><h2>'.$_POST["title"].'</h2></li>';
			echo '<li>no upcoming events found.</li>';
		}
		
		foreach($allEvents as $day=>$events) {
			if(is_array($events) && count($events) > 0) {
				echo '<li><h3>'.strtolower(date("l, F", strtotime($day))).' <span class="numbers">'.date("j", strtotime($day)).'</span></h3></li>';
				ksort($events);
				foreach($events as $time=>$things) {
					if($time == "00:00:00") {
						//all day events
						foreach($things as $event) {
							echo '<li>
								'.($event["description"] != '' ? '<strong>'.$event["summary"].'</strong> — ' .$event["description"] : $event["summary"]).'
								'.($event["location"] ? ' <span class="subtext">(' . $event["location"] .')</span>' : '').'	
							</li>';
						}
					} else {
						//time events - group em 
						$atTime = date("g:i", strtotime("$day $time"));
						$atTimeAMPM = date("a", strtotime("$day $time"));
						foreach($things as $event) {
							echo '<li><span class="numbers-time">'.$atTime.' '.$atTimeAMPM.'</span>
								'.($event["description"] != '' ? '<strong>'.$event["summary"].'</strong> — ' .$event["description"] : $event["summary"]).'
								'.($event["location"] ? ' <span class="subtext"><small><i class="fa fa-location-arrow"></i></small> ' . $event["location"] .'</span>' : '').'
							</li>';
						}
					}
				}
			} else {
				//echo '<li><span class="text-muted">No events scheduled.</span></li>';
			}					
		}
	} else {
		echo '<li>No enabled calendars to show.</li>';
	}
echo '</ul>';

?>
