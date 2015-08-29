<?php
include($templatesDir . "/nav.inc.php");

$data = getWebsiteSettingsData();
$infoCoordinates = json_decode($data["infoCoordinates"], true);
$addr = $data["info_address"].' / ' . $data["info_address2"];
$city = $data["info_citystate"].' '.$data["info_zipcode"];

if(is_array($infoCoordinates)) {
	echo '<div class="mapbox-contain">
		<div class="mapbox" id="mapbox" data-tap-disabled="true" data-zoom="15" data-lat="'.$infoCoordinates[1].'" data-lon="'.$infoCoordinates[0].'" data-maptitle="'.strtolower($data["site_title"]).'" data-address="'.$addr.'" data-city="'.$city.'"></div>
		<div class="map-loading"><span class="message">loading map <i class="fa fa-spin fa-spinner"></i></span></div>
		<div class="map-chooser">
			<a href="javascript:;" data-map-type="outdoors" class="activemap">topo</a> 
			<a href="javascript:;" data-map-type="road">road</a> 
			<a href="javascript:;" data-map-type="satellite">satellite</a>
		</div>
	</div>';
}							

?>

<div class="container outer-contain">
	<div class="row">
		<div class="col-md-8 col-md-offset-4 header-content">
			<h1 class="page-title"><?=$pageData["navTitle"]?></h1>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-4 secondary-nav">
			<ul class="list-unstyled">
				<?php
					if($pageData["parentID"] == null) {
						//top level
						foreach($primaryNav as $link) {
							if($pageData["pageID"] == $link["pageID"]) {
								//its the page we want.
								if(is_array($link["children"]) && count($link["children"]) > 0) {
									foreach($link["children"] as $sub) {
										if($sub["enabled"] == 1) {
											echo '<li><a href="'.($sub["isExternal"] ? $sub["externalURL"] : '/' . $sub["urlSlug"]).'" '.($sub["isExternal"] ? 'target="_blank"' : '').'>'.strtolower($sub["navTitle"]).'</a></li>';
										}
									}
								}
							}
						}
					} else {
						//lower level
						foreach($primaryNav as $link) {
							if($pageData["topParent"] == $link["pageID"]) {
								if(is_array($link["children"]) && count($link["children"]) > 0) {
									foreach($link["children"] as $sub) {
										if($sub["enabled"] == 1) {
											echo '<li class="'.($pageData["pageID"] == $sub["pageID"] ? 'active' : '').'"><a href="'.($sub["isExternal"] ? $sub["externalURL"] : '/' . $sub["urlSlug"]).'" '.($sub["isExternal"] ? 'target="_blank"' : '').'>'.strtolower($sub["navTitle"]).'</a></li>';
										}
									}
								}
							}
						}
					}
				?>
			</ul>	
		</div>
		<div class="col-md-8 inner-content" data-swiftype-index="true">
			<?php
				include($templatesDir . "/output.inc.php");
			?>	
		</div>
	</div>
	
</div>
