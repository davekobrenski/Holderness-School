<?php
	
include($templatesDir . "/nav.inc.php");
$smarty = new \Michelf\SmartyPants();

//breadcrumb nav
if(is_array($pageData["originalData"])) {
	$crumbs = array($pageData["originalData"]["topParent"], $pageData["originalData"]["parentID"], $pageData["originalData"]["pageID"]);
} else {
	$crumbs = array($pageData["topParent"], $pageData["parentID"], $pageData["pageID"]);
}

$bread = getBreadCrumbDataForPageIDs($crumbs);

if(is_array($bread) && count($bread) > 0) {
	echo '<div class="breadcrumb-nav">
		<ul class="breadcrumb">';
		foreach($bread as $id=>$link) {
			echo '<li class="'.($pageData["pageID"] == $link["pageID"] ? 'active hidden-xs' : '').'"><a href="'.($link["isExternal"] ? $link["externalURL"] : '/' . $link["urlSlug"]).'" '.($link["isExternal"] ? 'target="_blank"' : '').'>'.strtolower($smarty->transform($link["navTitle"])).'</a>';
		}
	echo '</ul>
	</div>';
}

$pageData["navTitle"] = $smarty->transform($pageData["navTitle"]);
?>


	<div class="container outer-contain">
		<div class="row">
			<div class="col-md-12">
				<div class="row">
					<div class="col-md-12">
						<h1 class="page-title"><?=$smarty->transform($pageData["navTitle"])?></h1>
					</div>
					<div class="col-md-12 inner-content">
						<?php include($templatesDir . "/output.inc.php"); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
