<?php
	/**
	 * MAP block
	 * uses mapbox API
	 * https://www.mapbox.com/developers/api/
	*/
	$embedQ = http_build_query(array(
		"lat"=> $optionsJson["lat"],
		"lon"=> $optionsJson["lon"],
		"z"=> $optionsJson["zoom"],
		"p"=> $optionsJson["showPin"],
		"data"=>base64_encode(serialize(array("title"=>$optionsJson["mapText"], "description"=>$optionsJson["placeName"])))
	));
	$embedUrl = http_build_url(PUBLIC_URL_BASE . "mapbox", array("query"=>$embedQ));
	echo '<div id="mapbox-'.$content["blockID"].'" class="user-mapbox">
		<div class="embed-responsive embed-responsive-16by9" style="position: relative;width: 100%; height: 100%; overflow: hidden">
			<iframe class="embed-responsive-item" src="'.$embedUrl.'" style="position: absolute; top: 0; bottom: 0; left: 0; width: 100%; height: 100%; border: 0;"></iframe>
		</div>
	</div>';
?>