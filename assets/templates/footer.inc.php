
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
		
		<div class="modal fade" id="site-search-modal" tabindex="-1" role="dialog" aria-hidden="true" data-swiftype-index="false">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h3 class="modal-title">Search</h3>
					</div>
					<div class="modal-body">
						<p>Use the form below to search for specific content on the website.</p>
						<form>
						<div class="form-group">
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-search"></i></span>
								<input type="text" class="form-control input-lg st-default-search-input" id="site-search-input">
								<span class="input-group-btn btn-group-lg">
									<button class="btn btn-success" type="submit"><i class="fa fa-sign-in"></i></button>
								</span>
							</div>
							
							<p class="help-block">Enter search terms above and press enter/return.</p>
						</div>
						</form>
						<div class="st-search-container"></div>
						
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>
		
	</div> <!-- /.main-wrapper -->
	
	<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.2/js/bootstrap.min.js"></script>	
	<script type="text/javascript" src="/assets/js/min/main-min.<?=$cacheBust?>.js"></script>
	<?=$pageData["footerInject"]?>
	 <?php
		//Hello bar code, if it's set in the 'site settings' tab of the cms
		include($templatesDir . "/hellobar.inc.php");
	?>
	<script type="text/javascript">
	  (function(w,d,t,u,n,s,e){w['SwiftypeObject']=n;w[n]=w[n]||function(){
	  (w[n].q=w[n].q||[]).push(arguments);};s=d.createElement(t);
	  e=d.getElementsByTagName(t)[0];s.async=1;s.src=u;e.parentNode.insertBefore(s,e);
	  })(window,document,'script','//s.swiftypecdn.com/install/v2/st.js','_st');
	
	  _st('install','Kfc9Xbfg44ujTLepyUsu','2.0.0');
	</script>
	</body>
</html>