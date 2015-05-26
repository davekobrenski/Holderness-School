<?php
	
include($templatesDir . "/nav.inc.php");

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

if($pageData["hasImage"]) {
	echo '<div class="page-banner" style="'.$bgImg.'"></div>';
}

$smarty = new \Michelf\SmartyPants();
$pageData["navTitle"] = $smarty->transform($pageData["navTitle"]);
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
					
					//need to find out which navigation the page originated from
					$useTopNav = false;
					foreach($topNav as $pge) {
						if($pge["pageID"] == $pageData["pageID"]) {
							$useTopNav = true;
							break;
						}
					}
					
					if($useTopNav) {
						$myNav = $topNav;
					} else {
						//find out if its the unlinked nav
						$myNav = $primaryNav;
						if(is_array($unlinkedNav) && count($unlinkedNav) > 0) {
							foreach($unlinkedNav as $pge) {
								if($pge["pageID"] == $pageData["pageID"]) {
									$myNav = $unlinkedNav;
									break;
								}
							}
						}
					}
					
					foreach($myNav as $link) {
						if($pageData["pageID"] == $link["pageID"]) {
							//its the page we want.
							if(is_array($link["children"]) && count($link["children"]) > 0) {
								foreach($link["children"] as $sub) {
									if($sub["enabled"] == 1) {
											echo '<li><a href="'.($sub["isExternal"] ? $sub["externalURL"] : '/' . $sub["urlSlug"]).'" '.($sub["isExternal"] ? 'target="_blank"' : '').'>'.strtolower($smarty->transform($sub["navTitle"])).'</a>';
											if(is_array($sub["children"]) && count($sub["children"]) > 0) {
												echo '<ul class="list-unstyled">';
												foreach($sub["children"] as $tertiary) {
													if($tertiary["enabled"] == 1) {
														echo '<li class="'.($pageData["pageID"] == $tertiary["pageID"] ? 'active' : '').'"><a href="'.($tertiary["isExternal"] ? $tertiary["externalURL"] : '/' . $tertiary["urlSlug"]).'" '.($tertiary["isExternal"] ? 'target="_blank"' : '').'>'.strtolower($smarty->transform($tertiary["navTitle"])).'</a></li>';
													}
												}
												echo '</ul>';
											}
											echo '</li>';	
									}
								}
							}
						}
					}
				?>
			</ul>	
		</div>
		<div class="col-md-8 inner-content">
			<?php
				include($templatesDir . "/output.inc.php");
			?>	
		</div>
	</div>
</div>
