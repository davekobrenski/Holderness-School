<?php
	$siteSettings = getWebsiteSettingsData();
	if($siteSettings["alert_showing"] == 1) {
		//$parser = new ParsedownExtra();
		$smarty = new \Michelf\SmartyPants();
?><div class="container-fluid">
	<div class="woahbar">
		<span class="msg">
		<?php
			if(!empty($siteSettings["alert_link"])) {
				if(!empty($siteSettings["alert_link_text"])) {
					//provide button
					echo $smarty->transform($siteSettings["alert_text"]) . ' &nbsp;<a class="btn btn-default btn-sm" href="'.$siteSettings["alert_link"].'">'.$siteSettings["alert_link_text"].'</a>';
				} else {
					//whole text is link
					echo '<a class="alert-link" href="'.$siteSettings["alert_link"].'">'.$smarty->transform($siteSettings["alert_text"]).'</a>';
				}
			} else {
				echo $smarty->transform($siteSettings["alert_text"]);
			}	
		?>
		</span>
	</div>
</div>
<?php } ?>