<?php
include('../../_init.inc.php');

$itemsPerPage = 5;

if(!empty($_POST["blockID"])) {
	$blockID = $_POST["blockID"];
	$blockData = getPageContentBlock($blockID);
	$pageData = getWebPageData($blockData["pageID"]);
	$optionsJson = json_decode($blockData["optionsJson"], true);
	$useBlogs = array_filter(explode(",", $optionsJson["accounts"]));
	
	if(!$optionsJson["limit"]) $optionsJson["limit"] = -1;
	if($optionsJson["limit"] == -1) {
		$userLimit = 1000;
	} else if($optionsJson["limit"] == -2) {
		//single blog post
		$userLimit = 1;
	} else {
		$userLimit = $optionsJson["limit"];
	}
	
	if($itemsPerPage > $userLimit) $itemsPerPage = $userLimit;
	
	$currentPage = (!$_POST["page"] ? 1 : $_POST["page"]);
	
	$tumblogs = getAllSocialAccountsArray("tumblr"); // <-- accounts. each account can have multiple blogs
	$enabled = getEnabledTumblogs(); // <-- list of blogs. may come from different accounts
	
	$goodBlogs = array_intersect($enabled, $useBlogs);
	
	if((is_array($goodBlogs) && count($goodBlogs) > 0) && (is_array($tumblogs) && count($tumblogs) > 0)) {
		$allPosts = array(); //to hold actual post data. to do: pagination...?
		$totalAllPosts = 0;
		foreach($tumblogs as $account) {
			$options = unserialize($account["options"]);
			if(!is_array($options)) {
				$options = array();
			}
			//loop through each account, and make sure the blogs associated with each account still exist. get the data from the one(s) we need
			try{
				$auth = unserialize(base64_decode($account["auth"]));
				$client = new Tumblr\API\Client(TUMBLR_OAUTH_KEY, TUMBLR_OAUTH_SECRET, $auth[2], $auth[3]);
				$info = $client->getUserInfo();
				$blogs = $info->user->blogs;
				
				if(is_array($blogs) && count($blogs) > 0) {
					foreach($blogs as $obj) {
						$blog = (array)$obj;
						$blog["blog_img"] = $client->getBlogAvatar($blog["name"], "128");
						
						$eKey = array_search($blog["name"], $goodBlogs);
						if($eKey !== false) {
							//then we're good to go, use this one
							//apparently, tumblr's api doesn't even let you sort by date, so if you want to get the most recent posts by date, you have to loop through the whole frigging thing and sort them yourself. so stupid.
							if(trim($optionsJson["postID"]) != '' && is_numeric($optionsJson["postID"])) {
								$posts = (array)$client->getBlogPosts($blog["name"], array('limit' => 1, 'id' => $optionsJson["postID"]));
							} else {
								$posts = (array)$client->getBlogPosts($blog["name"], array('limit' => 20)); //gets first 20, that's the max you can get at a time
							}
							$temp = $posts["posts"];
							$numPosts = $posts["total_posts"];
							$totalAllPosts = $totalAllPosts + $numPosts;
							if($numPosts > 0) {
								if($numPosts > 20) {
									//how many pages?
									$numPages = ceil($numPosts / 20);
									$onPage = 1;
									while($onPage <= $numPages) {
										$offset = ((20 * $onPage) - 20);
										$posts = (array)$client->getBlogPosts($blog["name"], array('limit' => 20, 'offset' => $offset));
										$temp = array_merge($temp, $posts["posts"]); 
										$onPage++;
									}
								}
								if(is_array($temp)) {
									foreach($temp as $post) {
										$date = date("Y-m-d", strtotime($post->date));
										$ts = $post->timestamp;
										$id = $post->id;
										
										if($post->state == 'published') {
											$thePost = (array)$post;
											$thePost["blog_img"] = $blog["blog_img"];
											$allPosts["$date-$id"] = $thePost;
										}
									}
								}
							}
						}					
					}
				}
			} catch(Exception $e) {
				echo '<div class="alert alert-danger">Error connecting to tumblr: '.$e->getMessage().'</div>';
			}
		}
		
		//now see what we have for posts
		if(count($allPosts) > 0) {
			krsort($allPosts);

			//arr($allPosts);

			$postPages = array_chunk($allPosts, $itemsPerPage, true); //chunk the array into pages of the specified amount so we can paginate through them. thanks, tumblr. jerks.
			$pagesInArr = count($postPages);
			
			if($totalAllPosts > $userLimit) $totalAllPosts = $userLimit; //don't show more posts than that

			$pg = $currentPage - 1;
			$thesePosts = $postPages["$pg"];
		
			$urlPattern = $pageData["urlSlug"] . '?p=(:num)';
			$paginator = new \JasonGrimes\Paginator($totalAllPosts, $itemsPerPage, $currentPage, $urlPattern);
			//include($templatesDir . "/pagination.inc.php");
			
			foreach($thesePosts as $key=>$thePost) {
				$data = $thePost;
				$ts = $thePost["timestamp"];
				$tagz = array();
				if(is_array($data["tags"]) && count($data["tags"]) > 0) {
					foreach($data["tags"] as $tag) {
						$tagz[] = "#$tag";
					} 
				}
				echo '<div class="panel panel-default" data-tumblr-id="'.$data["id"].'">
					<div class="panel-heading">';
					
						if(!empty($data["blog_img"])) {
							echo '<img src="'.$data["blog_img"].'" class="pull-left" style="width:70px;height:70px;margin:8px 14px 0 0">';
						}
						
						echo '<h3 class="tumblr-header">'.(empty($data["title"]) ? date("F j, Y", $ts) : $data["title"]).'</h3>
						<div class="tumblr-info">
							Posted in: '.$data["blog_name"].' '.(count($tagz) > 0 ? ' | '. implode(" ", $tagz) : '').' 
							<br>
							<span class="text-muted">'.date("F j, Y", strtotime($date)).'</span>
						</div>
					</div>
					<div class="panel-body">';
					
						if($data["type"] == "video") {
							echo $data["caption"];
							$player = $data["player"][2]->embed_code;
							
							$url = urlencode($data["post_url"]);
							$fetch = "http://open.iframe.ly/api/oembed?url=$url&origin=davekobrenski";
							$content = file_get_contents($fetch);
							$embedData = json_decode($content, true);
							if(is_array($embedData)) {
								if(!empty($embedData["html"])) {
									echo '<div>';
									echo $embedData["html"];
									echo '</div>';
								}
							}
						}
						if($data["type"] == "photo") {
							echo $data["caption"];
							if(is_array($data["photos"])) {
								echo '<div class="row tumblr-photo-blck">';
									foreach($data["photos"] as $obj) {
										echo '<div class="col-md-6">
											<div class="thumbnail"><img src="' . $obj->alt_sizes[0]->url . '" class="img-responsive"></div>
										</div>';
									}
								echo '</div>';
							}
						}
						
						if($data["description"]) {
							echo $data["description"];
						}
						
						if($data["body"]) {
							echo $data["body"];
						}
						
						if($data["type"] == "link") {
							if(!empty($data["url"])) {
								$parts = parse_url($data["url"]);
								
								if(isset($data["link_image"])) {
									echo '<p>
										<a href="'.$data["url"].'" target="_blank" class="_thumbnail">
											<img src="'.$data["link_image"].'" class="img-responsive">
										</a>
									</p>
									<p class="help-block"><i class="fa fa-link"></i> <a href="'.$data["url"].'" target="_blank">'.$parts["scheme"] .'://'. $parts["host"].'&hellip;</a></p>';
								} else {
									echo '<p><span class="text-muted small"><i class="fa fa-link"></i></span> <a href="'.$data["url"].'" target="_blank">'.$parts["scheme"] .'://'. $parts["host"].'&hellip;</a></p>';
								}
							}
						}
							
					echo '</div>
				</div>';
				//$data["type"] - text, quote, link, answer, video, audio, photo, chat
			}
			
			//put pagination at end, too
			include($templatesDir . "/pagination.inc.php");
		} else {
			//no posts found
			echo '<div class="alert alert-info">No recent posts found.</div>';
		}
	} else {
		//no blogs to show. message to user...
		echo '<div class="alert alert-danger">Problem: couldn&rsquo;t find any blogs to show.</div>';
	}
} else {
	//error, no blockID
	echo '<div class="alert alert-danger">Error: no content specified.</div>';
}

?>