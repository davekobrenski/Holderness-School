<?php
include('../../_init.inc.php');

use Abraham\TwitterOAuth\TwitterOAuth;
$smarty = new \Michelf\SmartyPants();

$maxToShow = 20; //default

if(!empty($_POST["blockID"])) {
	$blockData = getPageContentBlock($_POST["blockID"]);
	$optionsJson = json_decode($blockData["optionsJson"], true);
	$useAccts = explode(",", $optionsJson["accounts"]);
}

if(!empty($optionsJson["limit"]) && is_numeric($optionsJson["limit"])) {
	$maxToShow = $optionsJson["limit"];
}

if(!empty($_POST["title"])) echo '<h2>'.$_POST["title"].'</h2>';

$toLower = $_POST["lowercase"];

$displayStyle = "list";

if(!empty($_POST["displayStyle"])) {
	$displayStyle = $_POST["displayStyle"];
}

if($displayStyle == "list") {
	echo '<ul class="list-unstyled">';
		$twitterAccts = getAnyTwitterAccounts();
		if(count($twitterAccts) > 0) {
			$account = $twitterAccts[0];
			$auth = unserialize(base64_decode($account["auth"]));
			$connection = new TwitterOAuth(TWITTER_OAUTH_KEY, TWITTER_OAUTH_SECRET, $auth[2], $auth[3]);
			$allTweets = array();
			
			if(is_array($useAccts) && count($useAccts) > 0) {
				foreach($useAccts as $acct) {
					$acct = trim(strip_tags($acct));
					$content = $connection->get("statuses/user_timeline", array("screen_name"=>"$acct", "count"=>$maxToShow+15, "exclude_replies"=>true, "include_rts"=>false, "trim_user"=>true));
					$tweets = (array)$content;
					foreach($tweets as $tweet) {
						$dateString = strtotime($tweet->created_at);
						$dateMonth = strtolower(date("F", $dateString));		
						$dateDay = date("j", $dateString);
						//$tweet = $smarty->transform($tweet); //smarty
						$parsed = jsonTweetTextToHTML($tweet, true);
						$parsed = $smarty->transform($parsed); //smarty
						$allTweets["$dateString"][] = "<li>$dateMonth <span class=\"numbers\">$dateDay</span>: $parsed</li>";
					}
				}
				
				krsort($allTweets);
				$x=0;
				if(count($allTweets) > 0) {
					foreach($allTweets as $stamp=>$tweets) {
						foreach($tweets as $tweet) {
							echo $tweet;
							$x++;
							if($x >= $maxToShow) {
								break 2;
							}
						}
					}
				} else {
					echo '<li>No recent news to show.</li>';
				}		
			} else {
				echo '<li>No recent news to show.</li>';
			}
		} else {
			echo '<li>No recent news to show.</li>';
		}
	echo '</ul>';
} else {
	//use blockquotes
	$twitterAccts = getAnyTwitterAccounts();
	if(count($twitterAccts) > 0) {
		$account = $twitterAccts[0];
		$auth = unserialize(base64_decode($account["auth"]));
		$connection = new TwitterOAuth(TWITTER_OAUTH_KEY, TWITTER_OAUTH_SECRET, $auth[2], $auth[3]);
		$allTweets = array();
		
		if(is_array($useAccts) && count($useAccts) > 0) {
			foreach($useAccts as $acct) {
				$acct = trim(strip_tags($acct));
				
				if(substr($acct, 0, 1) == '@') $acct = substr_replace($acct, '', 0, 1);
				
				$content = $connection->get("statuses/user_timeline", array("screen_name"=>"$acct", "count"=>$maxToShow+15, "exclude_replies"=>true, "include_rts"=>false, "trim_user"=>true));
				$tweets = (array)$content;
				foreach($tweets as $tweet) {
					$tweetID = $tweet->id;
					$dateString = strtotime($tweet->created_at);
					$dateMonth = date("F", $dateString);		
					$dateDay = date("j", $dateString);
					$parsed = jsonTweetTextToHTML($tweet, false);
					$parsed = $smarty->transform($parsed); //smarty
					$allTweets["$dateString"][] = "<blockquote class=\"tweet\"><p>$dateMonth $dateDay: $parsed <br><cite><a href=\"https://twitter.com/$acct/status/$tweetID\">@$acct</a></cite></p></blockquote>";
				}
			}
			
			krsort($allTweets);
			$x=0;
			if(count($allTweets) > 0) {
				foreach($allTweets as $stamp=>$tweets) {
					foreach($tweets as $tweet) {
						echo $tweet;
						$x++;
						if($x >= $maxToShow) {
							break 2;
						}
					}
				}
			} else {
				echo '<blockquote class="tweet">No recent tweets to show.</blockquote>';
			}		
		} else {
			echo '<blockquote class="tweet">No recent tweets to show.</blockquote>';
		}
	} else {
		echo '<blockquote class="tweet">No recent tweets to show.</blockquote>';
	}
}
?>