<?php
	/**
	 * CONTACT form
	 * show generic contact form with captcha
	 * optionally pulls data from employee directory
	*/
	if(empty($optionsJson["sendTo"])) {
		$optionsJson["sendTo"] = 'recipient-email';
	}
	
	$blockID = $content["blockID"];
	
	$builder = new \Gregwar\Captcha\CaptchaBuilder;
	
	$builder->setBackgroundColor(255,255,255);
	$builder->setMaxBehindLines(0);
	$builder->setMaxFrontLines(0);
	$builder->build();
	$sessKey = "amhuman-" . $content["blockID"];
	$_SESSION["$sessKey"] = $builder->getPhrase();
	
	echo '<form class="visitor-contact-form" data-block-id="'.$blockID.'">
		<div class="inner-form">
			<input type="hidden" name="blockID" value="'.$blockID.'" />
			<div class="row">
				<div class="col-md-5">
					<div class="form-group">
						<label for="first-name-'.$blockID.'" class="control-label">Name<sup class="req">*</sup></label>
						<input type="text" id="first-name-'.$blockID.'" name="first-name" class="form-control" placeholder="First" />
						<p class="help-block">First name</p>
					</div>
				</div>
				
				<div class="col-md-7">
					<div class="form-group">
						<label>&nbsp;</label>
						<input type="text" name="last-name" class="form-control" placeholder="Last" />
						<p class="help-block">Last name</p>
					</div>
				</div>
			</div>
			
			<div class="form-group">
				<label for="email-'.$blockID.'" class="control-label">Email address<sup class="req">*</sup></label>
				<input type="email" id="email-'.$blockID.'" name="email" class="form-control" placeholder="you@email.com" />
				<p class="help-block">Your email address</p>
			</div>';
			
			if($optionsJson["sendTo"] == 'visitor-select') {
				$allPeople = getEmployeeDirectoryArray(true); //only with valid emails
				if(is_array($allPeople) && count($allPeople) > 0) {
					echo '<div class="form-group">
					<label for="recipient-'.$blockID.'" class="control-label">Recipient<sup class="req">*</sup></label>
						<select class="form-control" name="recipient" id="recipient-'.$blockID.'">
						<option>Choose a recipient:</option>';
						foreach($allPeople as $deptName=>$people) {
							if(is_array($people) && count($people) > 0) {
								echo '<optgroup label="'.strtoupper($deptName).'">';
								foreach($people as $key=>$person) {
									echo '<option value="'.$key.'">'.$person["lastName"].', '.$person["firstName"].'</option>';
								}
								echo '</optgroup>';
							}
						}
						echo '</select>
					</div>';
				}
			}
			
			echo '<div class="form-group">
				<label for="subject-'.$blockID.'" class="control-label">Subject<sup class="req">*</sup></label>
				<input type="text" id="subject-'.$blockID.'" name="subject" class="form-control" placeholder="Subject" />
			</div>
			
			
			<div class="form-group">
				<label for="message-'.$blockID.'" class="control-label">Message<sup class="req">*</sup></label>
				<textarea id="message-'.$blockID.'" name="message" rows="4" class="form-control" placeholder="Your message"></textarea>
			</div>
			
			<div class="form-group captcha-group">
				<label for="human-'.$blockID.'">Anti-spam<sup class="req">*</sup></label>
				<img src="'.$builder->inline().'" class="captcha" id="captcha-'.$blockID.'">
				<div class="input-group">
					<div class="input-group-btn">
						<a class="btn btn-default refresh-captcha" data-block-id="'.$blockID.'"><i class="fa fa-refresh"></i></a>
					</div>
					<input type="text" id="human-'.$blockID.'" name="human" class="form-control" placeholder="type the text above" />
				</div>
				<p class="help-block">For verification, please type the text you see above</p>
			</div>
			
			<button class="btn btn-default" type="submit">Send Message</button>
		</div>	
		<div class="visitor-form-results"></div>
	</form>';

?>