<?php
	/**
	 * Block quote
	 * optional author / footer
	 * text content
	*/
	
	$srcUrl = null;
	if(!empty($optionsJson["source"]) && filter_var($optionsJson["source"], FILTER_VALIDATE_URL)) {
		$srcUrl = $optionsJson["source"];
	}
	echo '<blockquote>
		<p>'. $smarty->transform($optionsJson["quote"]).'</p>
		'.(!empty($optionsJson["author"]) ? '<footer>'.($srcUrl ? '<a href="'.$srcUrl.'" target="_blank">' : '').'  '.$optionsJson["author"].'  '.($srcUrl ? '</a>' : '').'</footer>' : '').'
	</blockquote>';
?>