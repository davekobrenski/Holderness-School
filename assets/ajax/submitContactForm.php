<?php
include('../../_init.inc.php');
header('Content-Type: application/json');

$json = array();
$json["success"] = false;
$json["message"] = null;

$blockID = $_POST["blockID"];
$block = getPageContentBlock($_POST["blockID"]);
$sendTo = null;
$errs = array();

if(!empty($_POST["blockID"]) && is_array($block)) {
	
	$sessKey = "amhuman-" . $_POST["blockID"];
	
	if($_SESSION["$sessKey"] != $_POST["human"]) {
		$errs[] = "Verification phrase is incorrect.";
	}
	
	//map out the field names for error reporting
	$names["first-name"] = "First name";
	$names["last-name"] = "Last name";
	$names["email"] = "Email address";
	$names["subject"] = "Subject";
	$names["message"] = "Message";
	$names["human"] = "Verification phrase";
	
	foreach($_POST as $key=>$val) {
		$val = trim(strip_tags($val));
		if($val == '') {
			if($names[$key] != '') {
				$errs[] = "{$names[$key]} cannot be empty.";
			}
		} else {
			$userData["$key"] = $val;
		}
	}
	
	$optionsJson = json_decode($block["optionsJson"], true);
	if(!is_array($optionsJson)) $optionsJson = array();
	if(empty($optionsJson["sendTo"])) $optionsJson["sendTo"] = 'recipient-email';
	
	//deal with recipient
	if($optionsJson["sendTo"] == "visitor-select") {
		//make sure recipient is chosen and valid
		$allPeople = getEmployeeEmailsArray();	
		if(array_key_exists($userData['recipient'], $allPeople)) {
			$selectedPerson = $allPeople["{$userData['recipient']}"];
			if(filter_var($selectedPerson["email"], FILTER_VALIDATE_EMAIL) === false) {
				//bad email
				$errs[] = "Selected email not valid.";
			} else {
				$sendTo = $selectedPerson["email"];
			}
		} else {
			//person not found
			$errs[] = "Selected recipient not found.";
		}
	} else {
		//make sure there is a valid email to send to (configured by admin)
		if(filter_var($optionsJson["recipientEmail"], FILTER_VALIDATE_EMAIL) === false) {
			//no email, or invalid
			$errs[] = "Error: form not configured properly. Please contact a site administrator if the problem persists.";
		} else {
			$sendTo = $optionsJson["recipientEmail"];
		}
	}
	
	if(count($errs) > 0) {
		$json["message"] = '<div class="alert alert-danger">There were some problems submitting the form: <ul><li>'.implode("</li><li>", $errs).'</li></ul></div>';
	} else {
		//we should have sendTo now and some form data	
		$fromName = $userData["first-name"] . " " . $userData["last-name"];
		$replyToEmail = $userData["email"];
		$subj = $userData["subject"];
		$emailSubject = "[$thisOrgShortName] " . $userData["subject"];
		$message = $userData["message"];
		
		$date = date("F j, Y \\a\\t g:ia");
		$body ="Message received from the $thisOrgName website on: $date\n---\n\nFrom: $fromName ($replyToEmail)\nSubject: $subj\n\n$message";
		
		$fromEmail = "no-reply@" . $organizationDomain;
		
		$headers = "From: $thisOrgName <$fromEmail>" . "\r\n" .
			"Reply-To: $replyToEmail" . "\r\n" .
			'X-Mailer: PHP/' . phpversion();
		
		if(mail($sendTo, $emailSubject, $body, $headers)) {
			$json["message"] = '<div class="alert alert-success">Email was sent successfully. We will get back to you as soon as possible. Thanks!</div>';
			$json["success"] = true;
		} else {
			$json["message"] = '<div class="alert alert-danger">Problem sending email. Please try again.</div>';
		}
	}
} else {
	$json["message"] = '<div class="alert alert-danger">An error occurred with this form. Please try again.</div>';
}

echo json_encode($json);

?>