<?php
	/**
	 * FEATURED pages
	 * usually just on the home / splash page
	 * shows all pages marked as featured in CMS
	*/
	
	$featured = getFeaturedPagesArray();
	if(count($featured) > 0) {
		echo '<div class="featured-pages">
		<ul class="list-unstyled">';
		foreach($featured as $row) {
			$gray = $row["gray"];
			echo '<li class="col-xs-6 col-sm-4">
				<a href="'.($row["isExternal"] == 1 ? $row["externalURL"] : '/'.$row["urlSlug"]).'" '.($row["isExternal"] == 1 ? 'target="_blank"' : '').'>
					<img src="'.$gray["url"].'" class="img-responsive" alt="'.htmlentities($row["pageTitle"]).'">
					<p>'.strtolower($row["navTitle"]).'</p>
				</a>
			</li>';
		}
		echo '</ul>
		</div>';
	}
?>