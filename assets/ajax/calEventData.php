<?php
include('../../_init.inc.php');

if(isset($_GET["sID"]) && isset($_GET["eID"]) && isset($_GET["cID"])) {
	$smarty = new \Michelf\SmartyPants();
	$parser = new ParsedownExtra();
	
	$account = getSocialAccountBySocialID($_GET["sID"]);
	
	if(is_array($account) && $account["network"] == "google") {
		$options = unserialize($account["options"]);
		if(is_array($options)) {
			try {
				$client = new Google_Client();
				$client->setClientId(GOOGLE_CLIENT_ID);
				$client->setClientSecret(GOOGLE_OAUTH_SECRET);
				$auth = unserialize(base64_decode($account["auth"]));
				$tokens = unserialize(base64_decode($auth[2]));		
				$client->setAccessToken(json_encode($tokens));
				$service = new Google_Service_Calendar($client);
				
				$event = $calendar = $service->events->get($_GET["cID"], $_GET["eID"]);
				
				$start = $event->getStart();
				$end = $event->getEnd();
				
				$eventData = array();
				
				$eventData["summary"] = $smarty->transform($event->getSummary()); //$event->getSummary();
				
				$eventData["description"] = $smarty->transform($event->getDescription()); //$event->getDescription();
				$eventData["description"] = strip_tags($parser->text(trim($eventData["description"])), "<a><p><br><em><strong>");
				
				$eventData["location"] = trim($event->getLocation());
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
				
				$eventData["startDay"] = date("F j, Y", strtotime($startDate));
				$eventData["startTime"] = date("H:i:s", strtotime($startDate));
				$eventData["endDay"] = date("F j, Y", strtotime($endDate) -1);
				$eventData["endTime"] = date("H:i:s", strtotime($endDate));
				
				echo '<h2>' . $event->summary . '</h2>
				<ul class="list-unstyled">';
				
				if($eventData["startTime"] == "00:00:00") {
					//all day
					if($eventData["startDay"] == $eventData["endDay"]) {
						$range = $eventData["startDay"];
					} else {
						$range = $eventData["startDay"] . ' – ' . $eventData["endDay"];
					}
					
					echo '<li>'.$range.'</li>';
					if($eventData["description"] != '') echo '<li  style="margin-top:15px">'.$eventData["description"].'</li>';
					if($eventData["location"] != '') echo '<li style="margin-top:15px" class="text-muted"><small><i class="fa fa-location-arrow"></i></small> '.$eventData["location"].'</li>';
				} else {
					//time event
					
					if($eventData["startDay"] == $eventData["endDay"]) {
						$range = $eventData["startDay"];
						if($eventData["startTime"] == $eventData["endTime"]) {
							$range .= " at " . date("g:i a", strtotime($startDate));
						} else {
							$range .= " <br>" . date("g:i a", strtotime($startDate)) . " – " . date("g:i a", strtotime($endDate));
						}
					} else {
						$range = $eventData["startDay"] . ' at '.date("g:i a", strtotime($startDate)).' until ' . $eventData["endDay"] . ' at ' . date("g:i a", strtotime($endDate));
					}
					
					echo '<li>'.$range.'</li>';
					if($eventData["description"] != '') echo '<li  style="margin-top:15px">'.$eventData["description"].'</li>';
					if($eventData["location"] != '') echo '<li style="margin-top:15px" class="text-muted"><small><i class="fa fa-location-arrow"></i></small> '.$eventData["location"].'</li>';
				}
				
				echo '</ul>';
				
				//arr($eventData);	
				
			} catch(Exception $e) {
				echo '<div class="alert alert-danger">Error retrieving event details: '.$e->getMessage().'</div>';
			}
		} else {
			echo '<div class="alert alert-warning">Error retrieving event details.</div>';
		}
	} else {
		echo '<div class="alert alert-warning">Error retrieving event details.</div>';
	}	
} else {
	echo '<div class="alert alert-warning">Error retrieving event details.</div>';
}

?>