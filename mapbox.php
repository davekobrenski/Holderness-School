<?php
require_once('config/_config.inc.php');	
header('Content-Type: text/html; charset=utf-8');

?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Map</title>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="robots" content="noindex">
		<script src='//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js'></script>
		<script src='https://api.tiles.mapbox.com/mapbox.js/v2.1.5/mapbox.js'></script>
		<link href='https://api.tiles.mapbox.com/mapbox.js/v2.1.5/mapbox.css' rel='stylesheet' />
		<style>
			body { margin:0; padding:0; }
			#mapbox { position:absolute; top:0; bottom:0; width:100%; }
			#mapbox .leaflet-control-attribution {display: none;}
			#mapbox .leaflet-control-layers {display: none;}
			#mapbox .leaflet-control-container leaflet-top {z-index: 999;}
		</style>
	</head>
	<body>
		<?php
			
			if(!empty($_GET["lat"]) && !empty($_GET["lon"])) {
				$lat = $_GET["lat"];
				$lon = $_GET["lon"];
			} else {
				$lon = -71.674128;
				$lat = 43.759704;
			}
			
			if(!empty($_GET["z"]) && is_numeric($_GET["z"]) && $_GET["z"] > 2 && $_GET["z"] < 20) {
				$zoom = $_GET["z"];
			} else {
				$zoom = 12;
			}
			
			if(!empty($_GET["data"])) {
				$mapDetails = unserialize(base64_decode($_GET["data"]));
				if(!is_array($mapDetails)) $mapDetails = array();
			}
			
			$showPin = true;
			if(isset($_GET["p"]) && $_GET["p"] == 0) {
				$showPin = false;
			}
			
		?>
		<div id='mapbox'></div>
		<script>
			var mapData = [<?=$lat?>, <?=$lon?>];
			var zoom = <?=$zoom?>;
			L.mapbox.accessToken = '<?=MAPBOX_KEY?>';
			var map = L.mapbox.map('mapbox', null, {
				zoomControl: false,
				tileLayer: {
					detectRetina: true,
					minZoom: 2
				}
			}).setView(mapData, zoom);
			
			map.scrollWheelZoom.disable();
			new L.Control.Zoom({ position: 'bottomright' }).addTo(map);
			
			var mapStyles = new Object();
			mapStyles.outdoors = L.mapbox.tileLayer('davekobrenski.ladefclp'); //default
			mapStyles.satellite = L.mapbox.tileLayer('davekobrenski.lae0b054');	
			mapStyles.road = L.mapbox.tileLayer('examples.ra3sdcxr');
			
			mapStyles.outdoors.addTo(map);
			<?php if($showPin) { ?>
			setTimeout(function() {
				var marker = L.mapbox.featureLayer({
					type: 'Feature',
					geometry: {
						type: 'Point',
						coordinates: [<?=$lon?>, <?=$lat?>]
					},
					properties: {
						title: '<?=$mapDetails["title"]?>',
						description: '<?=$mapDetails["description"]?>',
						'marker-size': 'large',
				        'marker-color': '#AB1840',
				        "marker-symbol": "star"
					}
				}).addTo(map);
			}, 100);
			<?php } ?>
		</script>
	</body>
</html>