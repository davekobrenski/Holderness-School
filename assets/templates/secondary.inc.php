<?php
	
include($templatesDir . "/nav.inc.php");
$smarty = new \Michelf\SmartyPants();
$pageData["navTitle"] = $smarty->transform($pageData["navTitle"]);
$pageData["pageTitle"] = $smarty->transform($pageData["pageTitle"]);
?>

<div class="container outer-contain">
	<div class="row">
		<div class="col-md-8 col-md-offset-4 header-content">
			<h1 class="page-title"><?=$pageData["pageTitle"]?></h1>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-4 secondary-nav">
			<ul class="list-unstyled">
				<?php
					//need to find out which navigation the page originated from
					$useTopNav = false;
					foreach($topNav as $pge) {
						if($pge["pageID"] == $pageData["topParent"]) {
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
								if($pge["pageID"] == $pageData["topParent"]) {
									$myNav = $unlinkedNav;
									break;
								}
							}
						}
					}
					
					$parentPage = getWebPageData($pageData["topParent"]); //the parent, so we can display a 'back' link to it
					foreach($myNav as $link) {
						if($pageData["topParent"] == $link["pageID"]) {
							if(is_array($link["children"]) && count($link["children"]) > 0) {
								
								//navTitle urlSlug
								echo '<li class="parent-link"><a href="'.($parentPage["isExternal"] ? $parentPage["externalURL"] : '/' . $parentPage["urlSlug"]).'" '.($parentPage["isExternal"] ? 'target="_blank"' : '').'>'.strtolower($smarty->transform($parentPage["navTitle"])).'</a></li>';
								
								foreach($link["children"] as $sub) {
									if($sub["enabled"] == 1) {
										echo '<li class="'.($pageData["pageID"] == $sub["pageID"] ? 'active' : '').'"><a href="'.($sub["isExternal"] ? $sub["externalURL"] : '/' . $sub["urlSlug"]).'" '.($sub["isExternal"] ? 'target="_blank"' : '').'>'.strtolower($smarty->transform($sub["navTitle"])).'</a>';
											
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
