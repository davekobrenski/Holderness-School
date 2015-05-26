<div class="navbar navbar-default yamm primary-nav">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-primary" aria-expanded="false" aria-controls="navbar">
				<span class="toggler">navigation <i class="fa fa-caret-down"></i></span>
			</button>
		</div>
		<div id="navbar-primary" class="navbar-collapse collapse">
			<ul class="nav navbar-nav navbar-right">	
				<?php
					$primaryNav = getWebPageTreeArray(2, true, false);	
					$unlinkedNav = getWebPageTreeArray(0, true, false);
					
					$smarty = new \Michelf\SmartyPants();
					
					if(is_array($primaryNav) && count($primaryNav) > 0) {
						foreach($primaryNav as $link) {
							if($link["enabled"] == 1) {			
								if(is_array($link["children"]) && count($link["children"]) > 0) {
									echo '<li class="dropdown yamm-fw '.($pageData["topParent"] == $link["pageID"] ? 'active' : '').'">
										<a href="'.($link["isExternal"] ? $link["externalURL"] : '/' . $link["urlSlug"]).'" '.($link["isExternal"] ? 'target="_blank"' : '').' data-toggle="dropdown" class="dropdown-toggle">
											'.strtolower($link["navTitle"]).'
										</a>
										<ul class="dropdown-menu">
											<li class="logo"></li>
											<li class="mega">
												<div class="container">
													<div class="row">
														<div class="col-sm-6">
															<dl>';
															
															$featured = array();
															foreach($link["children"] as $sub) {
																if($sub["navFeatured"] == 1 && $sub["enabled"] == 1) {
																	$featured[] = $sub;
																}
															}
															
															if(count($featured) > 0) {
																$f=0;
																while($f <= 2) {
																	$pge = $featured["$f"];
																	if($pge["enabled"] == 1) echo '<dt><a href="'.($pge["isExternal"] ? $pge["externalURL"] : '/' . $pge["urlSlug"]).'" '.($pge["isExternal"] ? 'target="_blank"' : '').'>'.strtolower($smarty->transform($pge["navTitle"])).'</a></dt>
																	<dd>'.$smarty->transform($pge["metaDesc"]).'</dd>';
																	$f++;
																}
															} else {
																//put something here anyway? take first two nav items
																$f=0;
																while($f <= 2) {
																	if(array_key_exists($f, $link["children"])) {
																		$pge = $link["children"][$f];
																		if($pge["enabled"] == 1) echo '<dt><a href="'.($pge["isExternal"] ? $pge["externalURL"] : '/' . $pge["urlSlug"]).'" '.($pge["isExternal"] ? 'target="_blank"' : '').'>'.strtolower($smarty->transform($pge["navTitle"])).'</a></dt>
																		<dd>'.$smarty->transform($pge["metaDesc"]).'</dd>';
																	}
																	$f++;
																}
															}
															echo '</dl>
														</div>
														<div class="col-sm-6">
															<div class="row">
																<ul class="list-unstyled">';
																	foreach($link["children"] as $sub) {
																		if($sub["enabled"] == 1) {
																			echo '<li class="col-md-6"><a href="'.($sub["isExternal"] ? $sub["externalURL"] : '/' . $sub["urlSlug"]).'" '.($sub["isExternal"] ? 'target="_blank"' : '').'>'.strtolower($smarty->transform($sub["navTitle"])).'</a></li>';
																		}	
																	}
																echo '</ul>
															</div>
														</div>
													</div>
												</div>
											</li>
										</ul>
									</li>';
								} else {
									//no need for menu
									echo '<li class="'.($pageData["pageID"] == $link["pageID"] ? 'active' : '').'"><a href="'.($link["isExternal"] ? $link["externalURL"] : '/' . $link["urlSlug"]).'" '.($link["isExternal"] ? 'target="_blank"' : '').'>'.strtolower($smarty->transform($link["navTitle"])).'</a></li>';
								}
							}
						}
					}
				?>
			</ul>
		</div>
	</div>
</div>