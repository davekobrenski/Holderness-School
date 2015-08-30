<?php
	/**
	 * CODE block
	 * can output code
	 * or display markup with syntax highlighting
	*/
	
	if($optionsJson["language"] != 'html' || $optionsJson["printcode"] == 'true') {
		$lang = $optionsJson["language"];
		if($lang == 'html') $lang = 'markup';
		if($lang == 'mysql') $lang = 'sql';
		echo '<pre><code class="language-'.$lang.'">'.htmlentities($content["htmlData"]).'</code></pre>';
	} else {
		echo $content["htmlData"]; //just let er rip
	}
?>