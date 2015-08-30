<?php
	/**
	 * CAROUSEL block
	 * image carousel (bootstrap / jquery)
	*/
	$carouselItems = getPageContentBlockItemsData($content["blockID"]);
	if(count($carouselItems) > 0) {
		echo '<div id="carousel-'.$content["blockID"].'" class="carousel slide">
		<div class="carousel-inner" role="listbox">';
		$i=0;
		foreach($carouselItems as $contentID=>$item) {
			$itemData = json_decode($item["dataJSON"], true);
			if($itemData['filename']) {
				$image = return_maxWidth_image_array(956, 85, $itemData['filename'], 'images');
				$imageUrl = ADMIN_BASE_DIR . "/" . $image["url"];
				$imgData = $itemData["imgData"];
				
				if(!is_array($imgData)) {
					$imgData = array();
					$imgData["focalData"] = array(0,0, $image["width"], $image["height"]);
					$imgData["cssData"] = array(50,50);
				} else {
					//these are coming back as comma sep strings. explode so we can work with them as arrays.
					$imgData["focalData"] = explode(",", $imgData["focalData"]);
					$imgData["cssData"] = explode(",", $imgData["cssData"]);
				}
				
				$cssData = $imgData["cssData"];
				$posTop = $cssData[0];
				$posLeft = $cssData[1];
				
				echo '<div class="item '.($i==0 ? 'active':'').'">
					<div class="fill" style="background-image: url(\''.$imageUrl.'\'); background-position:'.$posLeft.'% '.$posTop.'%"></div>';
					if(trim($itemData["captionHeader"]) != '') echo '<div class="carousel-caption"><p>'.$itemData["captionHeader"].'</p></div>';
				echo '</div>';		
				$i++;
			}
		}
		
		echo '</div>';
		if(count($carouselItems) > 1) {
			echo '<a class="left carousel-control" href="#carousel-'.$content["blockID"].'" role="button" data-slide="prev">
				<span class="move-prev"><i class="fa fa-caret-left"></i></span>
			</a>
			<a class="right carousel-control" href="#carousel-'.$content["blockID"].'" role="button" data-slide="next">
				<span class="move-next"><i class="fa fa-caret-right"></i></span>
			</a>';
		}
	echo '</div>';
	}
?>