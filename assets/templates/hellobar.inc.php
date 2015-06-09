<?php
	$siteSettings = getWebsiteSettingsData();
	if(!empty($siteSettings["hello_bar_key"])) {
?>
<script src="//my.hellobar.com/<?=trim($siteSettings["hello_bar_key"])?>.js" type="text/javascript" charset="utf-8" async="async"></script>
<?php } ?>