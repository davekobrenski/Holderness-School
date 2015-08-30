<?php
	/**
	 * INSTAGRAM images
	 * currently pulls 20 latest images, gets ajax'd in
	 * TO DO: pagination, so we can get more than 20 latest...?
	*/
	if($optionsJson["socialID"]) {
		echo '<div id="instagram-'.$content["blockID"].'" class="instagram-feed" data-block-id="'.$content["blockID"].'" data-page="1"><p><i class="fa fa-spin fa-spinner"></i></p></div>';
	}
?>