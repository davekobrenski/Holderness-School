<div class="container outer-contain">
	<div class="login-form">
		<div class="row">
			<div class="col-sm-8 col-md-6 col-lg-5 center-block">
				<form class="form-signin" role="form" method="post" action="/<?=$pageData["urlSlug"]?>">
					<input type="hidden" name="pageID" value="<?=$pageData["pageID"]?>" />
					<input type="hidden" name="protectedPage" value="1" />
					<h2 class="form-signin-heading">
						<span class="fa-stack fa-lg">
							<i class="fa fa-circle fa-stack-2x"></i>
							<i class="fa fa-lock fa-stack-1x fa-inverse"></i>
						</span>
					</h2>
					<div class="alert alert-danger" style="display:<?=(!empty($loginErrorMsg) ? 'block' : 'none')?>"><?=$loginErrorMsg?></div>
					<div class="form-group">
						<div class="input-group">
							<span class="input-group-addon"><i class="fa fa-key"></i></span>
							<input name="password" type="password" class="form-control input-lg" name="password" value="" placeholder="Password" autofocus>
							<span class="input-group-btn btn-group-lg">
								<button class="btn btn-success" type="submit"><i class="fa fa-sign-in"></i></button>
							</span>
						</div>	
					</div>
					<p class="help-block">Enter the password to view this page</p>
				</form>
			</div>
		</div>
	</div>
</div>