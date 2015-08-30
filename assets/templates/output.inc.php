<?php
	$includeDir = __DIR__ . "/content-blocks";
	
	if(!is_dir($includeDir)) {
		echo '<div class="alert alert-danger">Error: Content directory not found or a misconfiguration has occurred.</div>';
	} else {
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
			echo '<div class="row inner-flex" data-swiftype-index="true">
				<div class="content-block type-top-spacer col-md-12"></div>';
			
			foreach($contentBlocks as $content) {
				$cBlockType = $content["blockType"];
				$cBlockFile = $includeDir . "/" . $cBlockType . ".inc.php";
				
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
					
					if(file_exists($cBlockFile)) {
						include($cBlockFile);
					} else {
						//unknown content block type
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
			//nothing to show here...
		}
	}
?>