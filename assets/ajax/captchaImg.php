<?php
include('../../_init.inc.php');
header('Content-Type: application/json');
$json = array();

$json["blockID"] = $_GET["blockID"];
$sessKey = "amhuman-" . $json["blockID"];
$builder = new \Gregwar\Captcha\CaptchaBuilder;

$builder->setBackgroundColor(255,255,255);
$builder->setMaxBehindLines(0);
$builder->setMaxFrontLines(0);
$builder->build();

$json["img"] = $builder->inline();
$_SESSION["$sessKey"] = $builder->getPhrase();

echo json_encode($json);
?>