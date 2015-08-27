<?php
	
	$parser = new ParsedownExtra();
	$smarty = new \Michelf\SmartyPants();
	
	if(is_array($specialContentBlocks)) {
		$contentBlocks = $specialContentBlocks;
	} else {
		if(!empty($pageData["getContentFrom"])) {
			$contentBlocks = getPageContentBlocks($pageData["getContentFrom"]);
		} else {
			$contentBlocks = getPageContentBlocks($pageData["pageID"]);
		}
	}
	
	if(is_array($contentBlocks) && count($contentBlocks) > 0) {
		echo '<div class="row inner-flex">
			<div class="content-block type-top-spacer col-md-12"></div>';
		
		foreach($contentBlocks as $content) {
			
			$optionsJson = json_decode($content["optionsJson"], true);
			if(!is_array($optionsJson)) $optionsJson = array();
			
			if(is_numeric($content["columns"]) && $content["columns"] <=12 && $content["columns"] >=1) {
				$cols = $content["columns"];
			} else {
				$cols = 12;
			}
			
			echo '<div id="block-'.$content["blockID"].'" class="content-block type-'.$content["blockType"].' col-md-'.$cols.' '.($content["widget"] == 1 ? 'widget-block' : '').'">
			<div class="block-inner '.($content["widget"] == 1 ? 'widget-content' : '').'">';
				
				//account for "widget" style blocks
				$hdTxt = trim($content["headerText"]);
				if($content["widget"] == 1) {			
					if($hdTxt != '') echo '<div class="widget-header">'.$smarty->transform($hdTxt).'</div>';
					echo '<div class="widget">';
				}
				
				/**
				 * Basic content block
				 * markdown and/or html
				 * text content
				*/
				if($content["blockType"] == "basic") {
					if($content["isMarkdown"] != 1) {
						echo $smarty->transform($content["htmlData"]);
					} else {
						$contents = $content["htmlData"];
						$contents = $smarty->transform($contents); //smarty
						$contents = $parser->text($contents); //markdown parser
						echo $contents;
					}
					
				/**
				 * Horizontal rule
				 * 
				*/
				} else if($content["blockType"] == "hr") {
					echo "<hr>";
					
				/**
				 * IMAGE content block
				 * with optional caption
				 * optionall click-thru link
				*/
				} else if($content["blockType"] == "image") {
					
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
					
				/**
				 * Block quote
				 * optional author / footer
				 * text content
				*/
				} else if($content["blockType"] == "quote") {
					$srcUrl = null;
					if(!empty($optionsJson["source"]) && filter_var($optionsJson["source"], FILTER_VALIDATE_URL)) {
						$srcUrl = $optionsJson["source"];
					}
					echo '<blockquote>
						<p>'. $smarty->transform($optionsJson["quote"]).'</p>
						'.(!empty($optionsJson["author"]) ? '<footer>'.($srcUrl ? '<a href="'.$srcUrl.'" target="_blank">' : '').'  '.$optionsJson["author"].'  '.($srcUrl ? '</a>' : '').'</footer>' : '').'
					</blockquote>';
					
					
				/**
				 * CODE block
				 * can output code
				 * or display markup with syntax highlighting
				*/	
				} else if($content["blockType"] == "code") {
					if($optionsJson["language"] != 'html' || $optionsJson["printcode"] == 'true') {
						$lang = $optionsJson["language"];
						if($lang == 'html') $lang = 'markup';
						if($lang == 'mysql') $lang = 'sql';
						echo '<pre><code class="language-'.$lang.'">'.htmlentities($content["htmlData"]).'</code></pre>';
					} else {
						echo $content["htmlData"]; //just let er rip
					}
				
					
				/**
				 * MEDIA embed
				 * ajax'd in after load. uses iframe.ly for content
				*/
				} else if($content["blockType"] == "embed") {
					echo '<div class="media-embed loading" data-block-id="'.$content["blockID"].'"></div>';
				
				
				/**
				 * FEATURED pages
				 * usually just on the home / splash page
				 * shows all pages marked as featured in CMS
				*/
				} else if($content["blockType"] == "featured-pages") {
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
				
				
				/**
				 * TUMBLR feed
				 * gets ajax'd in after page load
				*/
				} else if($content["blockType"] == "tumblr-feed") {
					echo '<div id="tumblr-'.$content["blockID"].'" class="tumblr-feed-block" data-block-id="'.$content["blockID"].'" data-page="1"><p><i class="fa fa-spin fa-spinner"></i></p></div>';			
				
				
				/**
				 * TWITTER feed
				 * gets ajax'd in after page load
				*/
				} else if($content["blockType"] == "twitter-feed") {
					//twitter-feed-block
					echo '<div class="twitter-feed-block" data-block-id="'.$content["blockID"].'" data-display="blockquote"><p><i class="fa fa-spin fa-spinner"></i></p></div>';
				
				
				/**
				 * BOOK LIST
				 * display list of books based on Amazon book search API
				 * several display options (gallery / list etc)
				*/
				} else if($content["blockType"] == "book-list") {
					if(!$optionsJson["booksPerRow"]) $optionsJson["booksPerRow"] = 4;
					if(!$optionsJson["displayStyle"]) $optionsJson["displayStyle"] = 'bookshelf';
					$bookColumns = 12 / $optionsJson["booksPerRow"];
					
					$books = getPageContentBlockItemsData($content["blockID"]);
					if(count($books) > 0) {
						if($optionsJson["displayStyle"] == 'booklist') {
							foreach($books as $contentID=>$book) {
								$dataJSON = json_decode($book["dataJSON"], true);
								$reviewText = $dataJSON["reviewText"];
								$reviewText = $smarty->transform($reviewText); //smarty
								$reviewText = $parser->text($reviewText); //markdown parser
								echo '<div class="row">
									<div class="book-list-display clearfix">
										<div class="col-sm-4">
											<a href="'.$dataJSON["link"].'" target="_blank" class="thumbnail">
												<img src="'.$dataJSON["imgUrl"].'" class="img-responsive">
											</a>
											<div class="small text-muted">
												<a href="'.$dataJSON["link"].'" target="_blank">
													view on Amazon
												</a>
											</div>
										</div>
									
										<div class="col-sm-8 book-info">
											<h3 class="bk-title">'.$dataJSON["title"].'</h3>
											<h4 class="bk-author">'.$dataJSON["author"].'</h4>';
											if(!empty($dataJSON["submittedBy"])) echo '<p>Review by '.$dataJSON["submittedBy"].'</p>';
											echo $reviewText;
										echo '</div>
										<div class="col-sm-12 separator"><hr></div>
									</div>
								</div>';
							}
						} else {
							echo '<div class="row"><div class="book-gallery-display">';
								foreach($books as $contentID=>$book) {
									$dataJSON = json_decode($book["dataJSON"], true);
									if(is_array($dataJSON)) {
										$reviewText = $dataJSON["reviewText"];
										$reviewText = $smarty->transform($reviewText); //smarty
										$reviewText = $parser->text($reviewText); //markdown parser
										echo '<div class="'.($optionsJson["booksPerRow"] > 1 ? 'col-xs-6': '').' col-sm-'.$bookColumns.' book-item" data-content-id="'.$contentID.'">
											<a href="#" class="thumbnail" data-toggle="modal" data-target="#book-modal-'.$contentID.'">
												<img src="'.$dataJSON["imgUrl"].'" class="img-responsive">
												<span class="icon-overlay">
													<i class="fa fa-search"></i>
												</span>
											</a>
										</div>';
										
										//output the book info modal
										echo '<div class="modal fade" id="book-modal-'.$contentID.'" tabindex="-1" role="dialog" aria-labelledby="modalLabel-'.$contentID.'" aria-hidden="true">
											<div class="modal-dialog modal-lg">
												<div class="modal-content">
													<div class="modal-header">
														<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
														<h4 class="modal-title" id="modalLabel-'.$contentID.'">Book Details</h4>
													</div>
													<div class="modal-body">
														<div class="row">
															<div class="col-md-4">
																<a href="'.$dataJSON["link"].'" target="_blank" class="thumbnail">
																	<img src="'.$dataJSON["imgUrl"].'" class="img-responsive">
																</a>
																<div class="small text-muted">
																	<a href="'.$dataJSON["link"].'" target="_blank">
																		view on Amazon
																	</a>
																</div>
															</div>
															<div class="col-md-8 book-info">
																<h3 class="bk-title">'.$dataJSON["title"].'</h3>
																<h4 class="bk-author">'.$dataJSON["author"].'</h4>';
																if(!empty($dataJSON["submittedBy"])) echo '<p>Review by '.$dataJSON["submittedBy"].'</p>';
																echo $reviewText;
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
				
				
				/**
				 * MAP block
				 * uses mapbox API
				 * https://www.mapbox.com/developers/api/
				*/
				} else if($content["blockType"] == "map") {
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
				
				
				/**
				 * CONTACT form
				 * show generic contact form with captcha
				 * optionally pulls data from employee directory
				*/
				} else if($content["blockType"] == "contact-form") {
					
					if(empty($optionsJson["sendTo"])) {
						$optionsJson["sendTo"] = 'recipient-email';
					}
					
					$blockID = $content["blockID"];
					
					$builder = new \Gregwar\Captcha\CaptchaBuilder;
					
					$builder->setBackgroundColor(255,255,255);
					$builder->setMaxBehindLines(0);
					$builder->setMaxFrontLines(0);
					$builder->build();
					$sessKey = "amhuman-" . $content["blockID"];
					$_SESSION["$sessKey"] = $builder->getPhrase();
					
					echo '<form class="visitor-contact-form" data-block-id="'.$blockID.'">
						<div class="inner-form">
							<input type="hidden" name="blockID" value="'.$blockID.'" />
							<div class="row">
								<div class="col-md-5">
									<div class="form-group">
										<label for="first-name-'.$blockID.'" class="control-label">Name<sup class="req">*</sup></label>
										<input type="text" id="first-name-'.$blockID.'" name="first-name" class="form-control" placeholder="First" />
										<p class="help-block">First name</p>
									</div>
								</div>
								
								<div class="col-md-7">
									<div class="form-group">
										<label>&nbsp;</label>
										<input type="text" name="last-name" class="form-control" placeholder="Last" />
										<p class="help-block">Last name</p>
									</div>
								</div>
							</div>
							
							<div class="form-group">
								<label for="email-'.$blockID.'" class="control-label">Email address<sup class="req">*</sup></label>
								<input type="email" id="email-'.$blockID.'" name="email" class="form-control" placeholder="you@email.com" />
								<p class="help-block">Your email address</p>
							</div>';
							
							if($optionsJson["sendTo"] == 'visitor-select') {
								$allPeople = getEmployeeDirectoryArray(true); //only with valid emails
								if(is_array($allPeople) && count($allPeople) > 0) {
									echo '<div class="form-group">
									<label for="recipient-'.$blockID.'" class="control-label">Recipient<sup class="req">*</sup></label>
										<select class="form-control" name="recipient" id="recipient-'.$blockID.'">
										<option>Choose a recipient:</option>';
										foreach($allPeople as $deptName=>$people) {
											if(is_array($people) && count($people) > 0) {
												echo '<optgroup label="'.strtoupper($deptName).'">';
												foreach($people as $key=>$person) {
													echo '<option value="'.$key.'">'.$person["lastName"].', '.$person["firstName"].'</option>';
												}
												echo '</optgroup>';
											}
										}
										echo '</select>
									</div>';
								}
							}
							
							echo '<div class="form-group">
								<label for="subject-'.$blockID.'" class="control-label">Subject<sup class="req">*</sup></label>
								<input type="text" id="subject-'.$blockID.'" name="subject" class="form-control" placeholder="Subject" />
							</div>
							
							
							<div class="form-group">
								<label for="message-'.$blockID.'" class="control-label">Message<sup class="req">*</sup></label>
								<textarea id="message-'.$blockID.'" name="message" rows="4" class="form-control" placeholder="Your message"></textarea>
							</div>
							
							<div class="form-group captcha-group">
								<label for="human-'.$blockID.'">Anti-spam<sup class="req">*</sup></label>
								<img src="'.$builder->inline().'" class="captcha" id="captcha-'.$blockID.'">
								<div class="input-group">
									<div class="input-group-btn">
										<a class="btn btn-default refresh-captcha" data-block-id="'.$blockID.'"><i class="fa fa-refresh"></i></a>
									</div>
									<input type="text" id="human-'.$blockID.'" name="human" class="form-control" placeholder="type the text above" />
								</div>
								<p class="help-block">For verification, please type the text you see above</p>
							</div>
							
							<button class="btn btn-default" type="submit">Send Message</button>
						</div>	
						<div class="visitor-form-results"></div>
					</form>';
				

				/**
				 * CAROUSEL block
				 * image carousel (bootstrap / jquery)
				*/
				} else if($content["blockType"] == "carousel") {
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
					
				/**
				 * INSTAGRAM images
				 * currently pulls 20 latest images, gets ajax'd in
				 * TO DO: pagination, so we can get more than 20 latest...?
				*/	
				} else if($content["blockType"] == "instagram-feed") {
					
					if($optionsJson["socialID"]) {
						echo '<div id="instagram-'.$content["blockID"].'" class="instagram-feed" data-block-id="'.$content["blockID"].'" data-page="1"><p><i class="fa fa-spin fa-spinner"></i></p></div>';
					}
				
				/**
				 * UPCOMING EVENTS list
				 * from google calendar. 
				 * pulls current events in from google calendar(s)
				 * gets ajax'd in
				*/	
				} else if($content["blockType"] == "upcoming-events") {
					echo '<div class="cal-feed-block" data-block-id="'.$content["blockID"].'"><p><i class="fa fa-spin fa-spinner"></i></p></div>';
				
				/**
				 * EVENT Calendar
				 * from google calendar. 
				 * pulls current events in from google calendar(s)
				 * gets ajax'd in.
				 * with option to show list in addition to calendar view
				*/
				} else if($content["blockType"] == "calendar-events") {
					
					echo '<div class="cal-full-block" data-block-id="'.$content["blockID"].'" data-events-list="'.($optionsJson["showEventList"] == 1 ? 'true' : 'false').'">
					
						<div class="cal-grid-view">
							<div class="cal-options" id="cal-options-'.$content["blockID"].'">
								<div class="cal-controls pull-right">
									<div class="btn-group">
										<button class="btn btn-default btn-sm" data-calendar-nav="prev"><i class="fa fa-chevron-circle-left"></i></button>
										<button class="btn btn-default btn-sm" data-calendar-nav="today">Today</button>
										<button class="btn btn-default btn-sm" data-calendar-nav="next"><i class="fa fa-chevron-circle-right"></i></button>
									</div>
								</div>
								<h3><i class="fa fa-spin fa-spinner"></i> Loading&hellip;</h3>
							</div>
							<div id="calendar-load-'.$content["blockID"].'" ></div>
							<p class="help-block">Click a calendar cell above to view its events.</p>
						</div>
						
						<div class="cal-list-view">
							<div class="widget-header">Events for the Month</div>
							<div class="cal-list-view-inner">
								<div id="events-list-month-'.$content["blockID"].'"></div>
								<div id="ev-list-helper-'.$content["blockID"].'" class="ev-helper-wrap">
									<div id="events-list-'.$content["blockID"].'" class="events-list-wrap"></div>
									<div class="helper" style="display:none"><i class="fa fa-arrow-circle-down"></i></div>
								</div>
								<div id="ev-pg-contain-'.$content["blockID"].'" class="pagination-contain"></div>
							</div>
							
							<div class="modal fade" id="events-detail-modal">
							    <div class="modal-dialog">
							        <div class="modal-content">
							            <div class="modal-header">
							                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							                <h3>Event</h3>
							            </div>
							            <div class="modal-body"></div>
							            <div class="modal-footer">
							                <a href="#" data-dismiss="modal" class="btn btn-default">Close</a>
							            </div>
							        </div>
							    </div>
							</div>
						</div>
					</div>';
				
				/**
				 * EMPLOYEE Directory
				 * pulls data from ad_users and imported employees
				*/
				} else if($content["blockType"] == "employee-directory") {
					
					if(file_exists(LOGOS_ABS_PATH . "/default_user.png")) {
						$defaultIcon = LOGOS_BASE_URL.'/default_user.png?v='.date("U");
					} else {
						$gravatar = new \forxer\Gravatar\Image();
						$gravatar->enableSecure();
						$gravatar->setSize(200);
						$gravatar->setDefaultImage('identicon');
						$defaultIcon = $gravatar->getUrl('fakeemail@bbmdesigns.com');
					}
					
					$staffPhotos = USER_ICONS_PATH;
					$allPeople = getEmployeeDirectoryArray();
					
					$accordionID = "dept-employees-".$content["blockID"];
					$accordionSearchID = "accordion-".$content["blockID"]."-search";
					
					if(is_array($allPeople) && count($allPeople) > 0) {
						echo '<div class="panel-group directory-panel" id="'.$accordionID.'" role="tablist" aria-multiselectable="true">
						
							<div class="panel panel-default search-panel">
								<div class="panel-heading" role="tab" id="search-heading-'.$content["blockID"].'">
									<h4 class="panel-title">
										<form class="form-inline employee-search" data-block-id="'.$content["blockID"].'">
											<div class="form-group">
												<div class="input-group">
													<div class="input-group-addon">
														<span class="fa-stack">
															<i class="fa fa-circle fa-stack-2x"></i>
															<i class="fa fa-search fa-stack-1x fa-inverse"></i>
														</span>
													</div>
													<input type="text" id="'.$accordionSearchID.'" class="form-control search-input" placeholder="" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" /> 
												</div>
												<a class="collapsed" data-toggle="collapse" data-parent="#'.$accordionID.'" href="#search-res-'.$content["blockID"].'-collapse">
													<label>Search By Name</label>
												</a>
											</div>
										</form>
									</h4>
								</div>
								<div id="search-res-'.$content["blockID"].'-collapse" class="panel-collapse collapse" role="tabpanel">
									<div class="panel-body search-results" id="search-results-'.$content["blockID"].'">
										<h4 style="margin-top: -32px">Type a few letters above to search</h4>
									</div>
								</div>
							</div>';
						
						
						foreach($allPeople as $deptName=>$people) {
							$deptKey = filename_safe($deptName);
							echo '<div class="panel panel-default department-panel-group" data-department="'.$deptName.'">
								<div class="panel-heading" role="tab" id="'.$deptKey.'-heading">
									<h4 class="panel-title">
										<a class="collapsed" data-toggle="collapse" data-parent="#'.$accordionID.'" href="#'.$deptKey.'-collapse" aria-expanded="true" aria-controls="'.$deptKey.'-collapse">
											'.$deptName.'
										</a>
									</h4>
								</div>
								<div id="'.$deptKey.'-collapse" class="panel-collapse collapse" role="tabpanel" aria-labelledby="'.$deptKey.'-heading">
									<div class="panel-body"><div class="row">';
										
									if(is_array($people) && count($people) > 0) {
										foreach($people as $key=>$person) {
											$personIcon = null;
											
											if($person["found_in"] == 'ad_users') {
												$personIcon = getUserAvatarUrl($person["token"], 200, 200, 85);
											} else {
												if(!empty($person["photo"]) && is_file($staffPhotos . "/" . $person["photo"])) {
													$pic = return_cropped_image_array(200, 200, 85, $person["photo"], USER_ICONS);
													$personIcon = ADMIN_BASE_DIR ."/". $pic["url"];
												}
											}
											
											if(!$personIcon) $personIcon = $defaultIcon;
											
											echo '<div class="col-sm-6 person-block" data-person="'.htmlentities($person["firstName"]).' '.htmlentities($person["lastName"]).'">
												<div class="row">
													<div class="col-xs-4 col-sm-4 person-image">
														<img src="'.$personIcon.'" class="img-responsive">
													</div>
													<div class="col-xs-8 col-sm-8 person-details">
														<h4>'.$person["firstName"].' '.$person["lastName"].'</h4>
														<p>
															'.(!empty($person["staffTitle"]) ? $person["staffTitle"] .'<br>' : '').'
															'.(!empty($person["email"]) ? $person["email"] .'<br>' : '').'
															'.(!empty($person["phoneExt"]) ? "Extension " . $person["phoneExt"] : '').'
														</p>
													</div>
												</div>
											</div>';
										}
									}
										
									echo '</div></div>
								</div>
							</div>';
						}
						echo '</div>';
					}
					
				/**
				 * MEDIA Gallery
				 * for publications, images, products, etc.
				 * several display options (gallery / list etc)
				*/
				} else if($content["blockType"] == "media-gallery") {
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
					
				/**
				 * UNKNOWN content type - just output the data for now
				*/	
				} else {
					//unknown block type
					arr($content);
				}
				
				//end "widget" style blocks
				if($content["widget"] == 1) {
					echo '</div>';
				}		
			echo '</div>
			</div>';
		}
		echo '</div>';
	} else {
		//nothing to show here
	}
?>