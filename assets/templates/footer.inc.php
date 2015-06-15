
	</div> <!-- /.page-content -->
		
		<footer>	
			<section class="container-fluid outer dark">
				<div class="seal"></div>
				<div class="interior">
					<div class="row">
						<div class="col-md-12">
							<?php
								$siteData = getWebsiteSettingsData();
								
								$address1 = explode(' ', $siteData["info_address"]);
								$words =  array();
								foreach($address1 as $word) {
									if(is_numeric($word)) {
										$words[] = '<span class="numbers">'.$word.'</span>';
									} else {
										$words[] = strtolower($word);
									}
								}
								$address1 = implode(' ', $words);
								
								$address2 = explode(' ', $siteData["info_address2"]);
								$words =  array();
								foreach($address2 as $word) {
									if(is_numeric($word)) {
										$words[] = '<span class="numbers">'.$word.'</span>';
									} else {
										$words[] = strtolower($word);
									}
								}
								$address2 = implode(' ', $words);
													
								echo '<ul class="list-inline">
									<li><a href="/contact">contact</a></li>
									<li>'.$address1.'</li>
									<li>'.$address2.'</li>
									<li>'.strtolower($siteData["info_citystate"]).'</li>
									<li><span class="numbers">'.$siteData["info_phone"].'</span></li>
								</ul>';

							?>
						</div>
					</div>
				</div>
			</section>
		</footer>
		
	</div> <!-- /.main-wrapper -->
	
	<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.2/js/bootstrap.min.js"></script>	
	<script type="text/javascript" src="/assets/js/min/main-min.<?=$cacheBust?>.js"></script>
	<?=$pageData["footerInject"]?>
	 <?php
		//Hello bar code, if it's set in the 'site settings' tab of the cms
		include($templatesDir . "/hellobar.inc.php");
	?>
	</body>
</html>