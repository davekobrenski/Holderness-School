<?php
include('../../_init.inc.php');

if(!empty($_POST["blockID"])) {
	$blockData = getPageContentBlock($_POST["blockID"]);
	$userData = json_decode($blockData["optionsJson"], true);
	
	if($userData["url"]) {
		if(filter_var($userData["url"], FILTER_VALIDATE_URL) === false) {
			//bad url
			//echo 'bad';
		} else {
			$url = urlencode($userData["url"]);
			$fetch = "http://open.iframe.ly/api/oembed?url=$url&origin=davekobrenski";
			$content = file_get_contents($fetch);
			$embedData = json_decode($content, true);
			if(is_array($embedData)) {
				if(!empty($embedData["html"])) {
					//echo '<div class="media-embed">';
					echo $embedData["html"];
					//'.(!empty($embedData["author"]) ? '('.$embedData["author"].')' : '').'
					//via <a href="'.$embedData["url"].'" target="_blank">'.$embedData["provider_name"].'</a>
					echo '<p class="caption">'.$embedData["title"].'</p>';
				}
			}
		}
	}	
}

?>