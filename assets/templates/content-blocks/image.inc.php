<?php
	/**
	 * IMAGE content block
	 * with optional caption
	 * optionall click-thru link
	*/
	
	$uploadDir = FILES_PATH . "images";
	$displayDir = FILES_REL_URL . "images";
	
	if($optionsJson["clickthru"] != '') {
		$optionsJson["caption"] = '<a href="'.$optionsJson["clickthru"].'" '.($optionsJson["newwindow"] == 'true' ? 'target="_blank"' : '').'>' . $optionsJson["caption"] . '</a>';
	}
	
	if($optionsJson["caption_position"] == 'above') {
		if(trim($optionsJson["caption"]) != '') echo '<p class="caption '.($optionsJson["align"] == "center" ? 'text-center' : '').'">'.$optionsJson["caption"].'</p>';
	}
	
	if($optionsJson["filename"] != '' && is_file("$uploadDir/{$optionsJson['filename']}") && exif_imagetype("$uploadDir/{$optionsJson['filename']}")) {
		
		if($optionsJson["constrain-square"]) {
			$image = return_cropped_image_array(956, 956, 85, $optionsJson['filename'], 'images');
		} else {
			$image = return_maxWidth_image_array(956, 85, $optionsJson['filename'], 'images');
		}	
		
		$imageUrl = ADMIN_BASE_DIR . "/" . $image["url"];
	} else if(filter_var($optionsJson["imgurl"], FILTER_VALIDATE_URL)) {
		$imageUrl = $optionsJson["imgurl"];
	} else {
		$imageUrl = null;
	}
	
	if($imageUrl) {
		if($optionsJson["align"] == "center" || $optionsJson["stretch"]) {
			echo '<div class="center-align image-block '.($optionsJson["caption_position"] == 'overlay' || $optionsJson["caption_position"] == 'above' ? 'with-overlay' : '').'">';
		} else {
			echo '<div class="image-block '.($optionsJson["caption_position"] == 'overlay' || $optionsJson["caption_position"] == 'above' ? 'with-overlay' : '').'">';
		}
		
		if($optionsJson["clickthru"] != '') {
			echo '<a href="'.$optionsJson["clickthru"].'" '.($optionsJson["newwindow"] == 'true' ? 'target="_blank"' : '').'>';
		}
		
		if($optionsJson["stretch"]) {
			echo '<img class="stretch" src="'.$imageUrl.'">'; //show at full width regardless
		} else {
			echo '<img class="img-responsive" src="'.$imageUrl.'">'; //for small screens - show at max-width 100%;
		}
		
		if($optionsJson["clickthru"] != '') {
			echo '</a>';
		}
		
		if($optionsJson["caption_position"] == 'overlay') {
			echo '<div class="caption-overlay '.($optionsJson["align"] == "center" ? 'text-center' : '').'"><p>'.$optionsJson["caption"].'</p></div>';
		}
		
		echo '</div>';
	}
	
	if($optionsJson["caption_position"] == 'below' || empty($optionsJson["caption_position"])) {
		if(trim($optionsJson["caption"]) != '')  echo '<p class="caption '.($optionsJson["align"] == "center" ? 'text-center' : '').'">'.$optionsJson["caption"].'</p>';
	}
	
?>