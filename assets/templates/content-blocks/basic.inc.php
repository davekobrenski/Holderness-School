<?php
	/**
	 * Basic content block
	 * markdown and/or html
	 * text content
	*/
	
	if($content["isMarkdown"] != 1) {
		echo $smarty->transform($content["htmlData"]);
	} else {
		$contents = $content["htmlData"];
		$contents = $smarty->transform($contents); //smarty
		$contents = $parser->text($contents); //markdown parser
		echo $contents;
	}
?>