<?php
	/**
	 * MEDIA Gallery
	 * for publications, images, products, etc.
	 * several display options (gallery / list etc)
	*/
	
	$uploadDir = FILES_PATH . "images";
	$displayDir = FILES_REL_URL . "images";
	$mediaItems = getPageContentBlockItemsData($content["blockID"]);
	
	$optionsJson = json_decode($content["optionsJson"], true);
	if(!is_array($optionsJson)) $optionsJson = array();
	if(!$optionsJson["itemsPerRow"]) $optionsJson["itemsPerRow"] = 4;
	if(!$optionsJson["displayStyle"]) $optionsJson["displayStyle"] = 'mediagallery';
	if(!$optionsJson["autoCropRatio"]) $optionsJson["autoCropRatio"] = "3:4";
	
	if(count($mediaItems) > 0) {
		if($optionsJson["displayStyle"] == 'medialist') {
			foreach($mediaItems as $contentID=>$item) {
				$dataJSON = json_decode($item["dataJSON"], true);
				$captionHeader = $dataJSON["captionHeader"];
				$captionHeader = $smarty->transform($captionHeader); //smarty
				
				$bodyText = $dataJSON["bodyText"];
				$bodyText = $smarty->transform($bodyText); //smarty
				$bodyText = $parser->text($bodyText); //markdown parser
				
				$cropImg = return_maxWidth_image_array(700, 85, $dataJSON["filename"], "images");
				$imageUrl = ADMIN_BASE_DIR . "/" . $cropImg["url"];
				$img = '<img src="'.$imageUrl.'" class="img-responsive">';
				$captionLinkText = $dataJSON["captionLinkText"];
				if(filter_var($dataJSON["externalLink"], FILTER_VALIDATE_URL)) {
					$img = '<a href="'.$dataJSON["externalLink"].'" target="_blank" class="thumbnail">
						'.$img.'
						<span class="icon-overlay">
							<i class="fa fa-arrow-circle-right"></i>
						</span>
					</a>';
					$captionLinkText = '<a href="'.$dataJSON["externalLink"].'" target="_blank">'.$captionLinkText.'</a>';
				}
				
				echo '<div class="row">
					<div class="book-list-display clearfix">
						<div class="col-sm-5">
							'.$img.'
							<div class="small text-muted">
								'.$captionLinkText.'
							</div>
						</div>
					
						<div class="col-sm-7 book-info">
							<h3 class="bk-title">'.$captionHeader.'</h3>';
							echo $bodyText;
						echo '</div>
						<div class="col-sm-12 separator"><hr></div>
					</div>
				</div>';
			}
		} else {
			echo '<div class="row"><div class="book-gallery-display">';
				foreach($mediaItems as $contentID=>$item) {
					$dataJSON = json_decode($item["dataJSON"], true);
					$captionHeader = $dataJSON["captionHeader"];
					$captionHeader = $smarty->transform($captionHeader); //smarty
		
					if(is_array($dataJSON)) {
						
						$baseSize = 725;
						$cropRatio = explode(':', $optionsJson["autoCropRatio"]);
						$w = $cropRatio[0] * $baseSize;
						$h = $cropRatio[1] * $baseSize; 
						//this gets a $baseSize square for 1:1. now reduce it so its not huge for everything else
						if($w > $baseSize) {
							$h = ceil(($baseSize * $h) / $w);
							$w = $baseSize;
						}
						
						$cropImg = getCroppedFocalPointImage($dataJSON["filename"], $dataJSON["imgData"], $w, $h, 85, false, false);
						$bookColumns = 12 / $optionsJson["itemsPerRow"];
						
						if(is_array($cropImg)) {
							echo '<div class="'.($optionsJson["itemsPerRow"] > 1 ? 'col-xs-6': '').' col-sm-'.$bookColumns.' book-item" data-content-id="'.$contentID.'">
								<a href="#" class="thumbnail" data-toggle="modal" data-target="#book-modal-'.$contentID.'">
									<img src="'.$cropImg["url"].'" class="img-responsive">
									<span class="icon-overlay">
										<i class="fa fa-search"></i>
									</span>
									<div class="caption">
										<h4>'.$captionHeader.'</h4>
									</div>
								</a>
							</div>';
						}
						
						$bodyText = $dataJSON["bodyText"];
						$bodyText = $smarty->transform($bodyText); //smarty
						$bodyText = $parser->text($bodyText); //markdown parser
						
						$captionLinkText = $dataJSON["captionLinkText"];
						$img = '<img src="'.$cropImg["url"].'" class="img-responsive">';
						if(filter_var($dataJSON["externalLink"], FILTER_VALIDATE_URL)) {
							$img = '<a href="'.$dataJSON["externalLink"].'" target="_blank" class="thumbnail">
								'.$img.'
								<span class="icon-overlay">
									<i class="fa fa-arrow-circle-right"></i>
								</span>
							</a>';
							$captionLinkText = '<a href="'.$dataJSON["externalLink"].'" target="_blank">'.$captionLinkText.'</a>';
						}

						//output the item info modal
						echo '<div class="modal fade" id="book-modal-'.$contentID.'" tabindex="-1" role="dialog" aria-labelledby="modalLabel-'.$contentID.'" aria-hidden="true">
							<div class="modal-dialog modal-lg">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										<h4 class="modal-title" id="modalLabel-'.$contentID.'">Details</h4>
									</div>
									<div class="modal-body">
										<div class="row">
											<div class="col-md-5">
												'.$img.'
												<div class="small text-muted">
													'.$captionLinkText.'
												</div>
											</div>
											<div class="col-md-7 book-info">
												<h3 class="bk-title">'.$captionHeader.'</h3>';
												echo $bodyText;
											echo '</div>
										</div>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
									</div>
								</div>
							</div>
						</div>';
					}
				}
			echo '</div></div>';
		}
	}
?>