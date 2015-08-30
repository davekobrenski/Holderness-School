<?php
	/**
	 * EMPLOYEE Directory
	 * pulls data from ad_users and imported employees
	*/
	
	if(file_exists(LOGOS_ABS_PATH . "/default_user.png")) {
		$defaultIcon = LOGOS_BASE_URL.'/default_user.png?v='.date("U");
	} else {
		$gravatar = new \forxer\Gravatar\Image();
		$gravatar->enableSecure();
		$gravatar->setSize(200);
		$gravatar->setDefaultImage('identicon');
		$defaultIcon = $gravatar->getUrl('fakeemail@bbmdesigns.com');
	}
	
	$staffPhotos = USER_ICONS_PATH;
	$allPeople = getEmployeeDirectoryArray();
	
	$accordionID = "dept-employees-".$content["blockID"];
	$accordionSearchID = "accordion-".$content["blockID"]."-search";
	
	if(is_array($allPeople) && count($allPeople) > 0) {
		echo '<div class="panel-group directory-panel" id="'.$accordionID.'" role="tablist" aria-multiselectable="true">
		
			<div class="panel panel-default search-panel">
				<div class="panel-heading" role="tab" id="search-heading-'.$content["blockID"].'">
					<h4 class="panel-title">
						<form class="form-inline employee-search" data-block-id="'.$content["blockID"].'">
							<div class="form-group">
								<div class="input-group">
									<div class="input-group-addon">
										<span class="fa-stack">
											<i class="fa fa-circle fa-stack-2x"></i>
											<i class="fa fa-search fa-stack-1x fa-inverse"></i>
										</span>
									</div>
									<input type="text" id="'.$accordionSearchID.'" class="form-control search-input" placeholder="" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" /> 
								</div>
								<a class="collapsed" data-toggle="collapse" data-parent="#'.$accordionID.'" href="#search-res-'.$content["blockID"].'-collapse">
									<label>Search By Name</label>
								</a>
							</div>
						</form>
					</h4>
				</div>
				<div id="search-res-'.$content["blockID"].'-collapse" class="panel-collapse collapse" role="tabpanel">
					<div class="panel-body search-results" id="search-results-'.$content["blockID"].'">
						<h4 style="margin-top: -32px">Type a few letters above to search</h4>
					</div>
				</div>
			</div>';
		
		
		foreach($allPeople as $deptName=>$people) {
			$deptKey = filename_safe($deptName);
			echo '<div class="panel panel-default department-panel-group" data-department="'.$deptName.'">
				<div class="panel-heading" role="tab" id="'.$deptKey.'-heading">
					<h4 class="panel-title">
						<a class="collapsed" data-toggle="collapse" data-parent="#'.$accordionID.'" href="#'.$deptKey.'-collapse" aria-expanded="true" aria-controls="'.$deptKey.'-collapse">
							'.$deptName.'
						</a>
					</h4>
				</div>
				<div id="'.$deptKey.'-collapse" class="panel-collapse collapse" role="tabpanel" aria-labelledby="'.$deptKey.'-heading">
					<div class="panel-body"><div class="row">';
						
					if(is_array($people) && count($people) > 0) {
						foreach($people as $key=>$person) {
							$personIcon = null;
							
							if($person["found_in"] == 'ad_users') {
								$personIcon = getUserAvatarUrl($person["token"], 200, 200, 85);
							} else {
								if(!empty($person["photo"]) && is_file($staffPhotos . "/" . $person["photo"])) {
									$pic = return_cropped_image_array(200, 200, 85, $person["photo"], USER_ICONS);
									$personIcon = ADMIN_BASE_DIR ."/". $pic["url"];
								}
							}
							
							if(!$personIcon) $personIcon = $defaultIcon;
							
							echo '<div class="col-sm-6 person-block" data-person="'.htmlentities($person["firstName"]).' '.htmlentities($person["lastName"]).'">
								<div class="row">
									<div class="col-xs-4 col-sm-4 person-image">
										<img src="'.$personIcon.'" class="img-responsive">
									</div>
									<div class="col-xs-8 col-sm-8 person-details">
										<h4>'.$person["firstName"].' '.$person["lastName"].'</h4>
										<p>
											'.(!empty($person["staffTitle"]) ? $person["staffTitle"] .'<br>' : '').'
											'.(!empty($person["email"]) ? $person["email"] .'<br>' : '').'
											'.(!empty($person["phoneExt"]) ? "Extension " . $person["phoneExt"] : '').'
										</p>
									</div>
								</div>
							</div>';
						}
					}
						
					echo '</div></div>
				</div>
			</div>';
		}
		echo '</div>';
	}
?>