

<!-- main image(s) -->
<section id="index-carousel" class="fullscreen-carousel carousel slide" >		
	<div class="carousel-inner">
		<?php
		//we'll look for the first instance of a 'carousel' content block, and use that.
		//if we don't find one, then we'll just use the page's image and disable prev/next controls
		$useCarousel = false;
		$carouselBlock = getPageContentBlocks($pageData["pageID"], 'carousel', 1)	;
		if(count($carouselBlock) > 0) {
			$block = reset($carouselBlock);
			$items = getPageContentBlockItemsData($block["blockID"]);
			if(count($items) > 0) {
				$useCarousel = true;
			}
		}
		
		if($useCarousel) {
			//create carousel. we have items.
			$i=0;
			foreach($items as $contentID=>$item) {
				$itemData = json_decode($item["dataJSON"], true);
				if($itemData['filename']) {
					$image = return_maxWidth_image_array(1800, 85, $itemData['filename'], 'images');
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
						<div class="fill" style="background-image: url(\'config/'.$image["url"].'\'); background-position:'.$posLeft.'% '.$posTop.'%"></div>';
						if($itemData["captionHeader"]) {
							echo '<div class="carousel-caption theme1"><h1>'.$itemData["captionHeader"].'</h1></div>';
						}
					echo '</div>';
					$i++;
				}
			}
			
			echo '<div class="seal hidden-xs"></div>

			<a class="left carousel-control" href="#index-carousel" data-slide="prev">
	            <span class="move-prev"><i class="fa fa-caret-left"></i></span>
	        </a>
	        <a class="right carousel-control" href="#index-carousel" data-slide="next">
	            <span class="move-next"><i class="fa fa-caret-right"></i></span>
	        </a>
	        <a class="arrow-down" href="javascript:;" data-element="#news-schedule"><i class="fa fa-caret-down"></i></a>';
		} else {
			//use page image
			$bgImg = '';
			if($pageData["hasImage"]) {
				$imgUrl = $pageData["mainImageSpecs"]["cache_url"];
				if(is_array($pageData["mainImageData"]["cssData"])) {
					$posTop = $pageData["mainImageData"]["cssData"][0];
					$posLeft = $pageData["mainImageData"]["cssData"][1];
				} else {
					$posTop = 50;
					$posLeft = 50;
				}	
			} else {
				$imgUrl = $pageData["mainImageSpecs"]["cache_url"];
				$posTop = 50;
				$posLeft = 50;
			}
			
			$bgImg = "background-image: url('$imgUrl'); background-position: $posLeft% $posTop%;";
			
			echo '<div class="item active">
				<div class="fill" style="'.$bgImg.'"></div>
				<div class="carousel-caption theme1"></div>
			</div>
			
			<div class="seal hidden-xs"></div>
			<a class="arrow-down" href="javascript:;" data-element="#news-schedule"><i class="fa fa-caret-down"></i></a>';
		}
		?>
	</div>
</section>

<!-- news / schedule boxes -->
<section class="container-fluid" id="news-schedule">
	<div class="row">
		<div class="col-sm-6 updates news">
			<div class="content">
				<div class="seal hidden-xs"></div>
				<div class="inner">
					<div id="news-loader">
						<h2><i class="fa fa-spin fa-spinner"></i> loading news</h2>
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-6 updates events">
			<div class="content">
				<div class="seal hidden-xs"></div>
				<div class="inner">
					<div id="events-loader">
						<h2><i class="fa fa-spin fa-spinner"></i> loading schedule</h2>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- featured pages -->
<section class="container-fluid">
	<div class="row featured-pages">
		<ul class="list-unstyled">
			<?php
				$conn = db_connect();
				$query = "select * from web_pages where flaggedSpecial=1 order by featuredOrder, displayOrder";
				$result = MySQL_query($query, $conn);
				$num_results = mysql_num_rows($result);
				if($num_results > 0) {
					for ($i=0; $i < $num_results; $i++) {
						$row = mysql_fetch_assoc($result);
						$gray = getCroppedFocalPointImageForPage($row["pageID"], 620, 434, 80, false, true); //grayscale
						echo '<li class="col-xs-6 col-md-3">
							<a href="'.($row["isExternal"] == 1 ? $row["externalURL"] : '/'.$row["urlSlug"]).'" '.($row["isExternal"] == 1 ? 'target="_blank"' : '').'>
								<img src="'.$gray["url"].'" class="img-responsive" alt="'.htmlentities($row["pageTitle"]).'">
								<p>'.strtolower($row["navTitle"]).'</p>
							</a>
						</li>';
					}
				}
			?>
		</ul>
	</div>
</section>
