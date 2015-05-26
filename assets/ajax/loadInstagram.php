<?php
include('../../_init.inc.php');

if(!empty($_POST["blockID"])) {
	$blockData = getPageContentBlock($_POST["blockID"]);
	$optionsJson = json_decode($blockData["optionsJson"], true);
	if(!is_array($optionsJson)) $optionsJson = array();
	
	//all we need is the socialID. we'll get the latest 20 images from that instagram account.
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
				
				$media = $user->getMedia();
				
				if(count($media) > 0) {
					echo '<div class="row">
						<div class="insta-gallery-display">';
						foreach($media as $key=>$photo) {
							$lgImg = (array)$photo->getStandardResImage();
							$smImg = (array)$photo->getLowResImage();
							$tnImg = (array)$photo->getThumbnail();
							echo '<div class="col-sm-6 col-lg-4 insta-item">
								<div class="thumbnail">
									<div class="caption">'.$createdTime = $photo->getCreatedTime("j M Y").'</div>
									<a href="javascript:;" data-block-id="'.$blockData["blockID"].'" data-insta-id="'.$photo->getId().'">
										<img src="'.$smImg["url"].'" class="img-responsive">
										<span class="icon-overlay">
											<i class="fa fa-search"></i>
										</span>
									</a>
									<div class="caption">
										<span class="dt"><i class="fa fa-heart"></i> '.$photo->getLikesCount().' likes </span>
										<span class="dt"><i class="fa fa-comment fa-flip-horizontal"></i> '.count($photo->getComments()).' comments </span>
									</div>
								</div>
							</div>';
						}
						echo '</div>
						
						<div class="modal fade instagram-modal" id="instagram-modal" tabindex="-1" role="dialog" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-body"></div>
									<div class="modal-footer">
										<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="insta-header">
						<img src="'.$socialAcct["avatarUrl"].'" class="avatar"> 
						<a href="'.$socialAcct["visitUrl"].'" target="_blank">'.$socialAcct["username"].'</a> on instagram
					</div>';
				}
			} catch(Exception $e) {
				//do something?
			}
		}
	}
}


?>