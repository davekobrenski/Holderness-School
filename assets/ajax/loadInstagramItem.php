<?php
include('../../_init.inc.php');

if(!empty($_POST["blockID"]) && !empty($_POST["mediaID"])) {
	$mediaID = $_POST["mediaID"];
	$blockData = getPageContentBlock($_POST["blockID"]);
	$optionsJson = json_decode($blockData["optionsJson"], true);
	if(!is_array($optionsJson)) $optionsJson = array();
	
	if($optionsJson["socialID"]) {
		$socialAcct = getSocialNetworkData($optionsJson["socialID"]);
		if($socialAcct["network"] == 'instagram') {
			$options = unserialize($socialAcct["options"]);
			if(!is_array($options)) {
				$options = array();
			}
			$userID = $socialAcct["userID"];
			$auth = unserialize(base64_decode($socialAcct["auth"]));
			
			try{
				$instagram = new Instagram\Instagram;
				$instagram->setClientID($auth[0]);
				$instagram->setAccessToken($auth[2]);
				$user = $instagram->getCurrentUser();
				
				$photo = $instagram->getMedia($mediaID);
				$lgImg = (array)$photo->getStandardResImage();
				$createdTime = $photo->getCreatedTime();
				$timeAgo = time_since($createdTime);
				
				$comments = $photo->getComments();
				
				if($photo->getType() == "video") {
					$video = (array)$photo->getStandardResVideo();
					//arr($video);
					//TO DO...
				}
				
				echo '<div class="insta-item-detail">
					<div class="insta-info">
						<img src="'.$socialAcct["avatarUrl"].'" class="avatar"> 
						<a href="'.$socialAcct["visitUrl"].'" target="_blank">'.$socialAcct["username"].'</a>
						<span class="cDate">&bull; '.$timeAgo.' ago</span>
					</div>
					
					<img src="'.$lgImg["url"].'" class="img-responsive">
					
					<div class="insta-data">
						<span class="dt"><i class="fa fa-heart"></i> '.$photo->getLikesCount().' likes </span>
						<span class="dt"><i class="fa fa-comment fa-flip-horizontal"></i> '.count($photo->getComments()).' comments </span>
						<span class="dt"><a href="'.$photo->getLink() .'" target="_blank"><i class="fa fa-instagram"></i> view on instagram</a></span>
					</div>
					
					<div class="caption">
						'.$photo->getCaption().'
					</div>
					
					<div class="insta-comments">';
						foreach($comments as $comment) {
							echo '<div class="comment">
								<span class="user">@'.$comment->getUser().'</span> '.$comment.'
							</div>';
						}
					echo '</div>
				</div>';
			} catch(Exception $e) {
				//do something?
			}
		}
	}
}

?>