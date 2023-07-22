<?php
session_start();
/**
 * Table Class Generator v0.00
 */

include 'definitions.php';
include 'datalogger.class.php';
include 'geofencing.class.php';
include 'mysql.class.php';
include './vendor/BrowserDetection/lib/BrowserDetection.php';

$browser = new Wolfcast\BrowserDetection($_SERVER['HTTP_USER_AGENT']);
$_SESSION['postvars']['global']['id']="";
$_SESSION['postvars']['global']['pagename']="Form Processor";
include 'hitcounter.php';
$errorcount = 0;


if (isset($_POST['generateFor']) && $_POST['generateFor']!=='') {
	$generateFor = strtoupper($_POST['generateFor']);
} else {
	$errorcount++;
	$errorcode = 2010;
	$descr = "The language selection of C# or VB is a required field";
	$_SESSION['error']['code'] = $errorcode;
	$_SESSION['error']['message'] = $descr;
	datalogger::SetError($errorcode, "Language Required", $descr);
	Header('Location: ./index.php');
	die();
}

if (isset($_POST['usercode']) && $_POST['usercode']!=='') {
	$usercode = strtoupper($_POST['usercode']);
} else {
	$errorcount++;
	$errorcode = 2011;
	$descr = "The User Code is a required field";
	$_SESSION['error']['code'] = $errorcode;
	$_SESSION['error']['message'] = $descr;
	datalogger::SetError($errorcode, "User Code Required", $descr);
	Header('Location: ./index.php');
	die();
}

if (isset($_POST['namespace']) && $_POST['namespace']!=='') {
	$namespace = $_POST['namespace'];
} else {
	if ($generateFor==='C#') {
		$errorcount++;
		$errorcode = 2012;
		$descr = "Namespace is a required field for C#";
		$_SESSION['error']['code'] = $errorcode;
		$_SESSION['error']['message'] = $descr;
		datalogger::SetError($errorcode, "User Code Required", $descr);
		Header('Location: ./index.php');
		die();
	} else {
		datalogger::SetWarning(2012, "Namespace Missing", "Namespace is not entered but is also not required for VB");
	}
	$namespace = '';
}

if (isset($_POST['comment']) && $_POST['comment']!=='') {
	$comment = $_POST['comment'];
}else {
	datalogger::SetWarning(2013, "Comment Missing", "The Comment Field is missing but is optional");
	$comment = '';
}

if (isset($_POST['SQLOutput']) && $_POST['SQLOutput']!=='') {
	$tableconstrunct = $_POST['SQLOutput'];
}else {
	$errorcount++;
	$errorcode = 2014;
	$descr = "he important infomration Table Creation Data is a required field.";
	$_SESSION['error']['code'] = $errorcode;
	$_SESSION['error']['message'] = $descr;
	datalogger::SetError($errorcode, "User Code Required", $descr);
	Header('Location: ./index.php');
	die();
}

if ($errorcount ==0) {

	datalogger::datalog("PAGE", 1000, "FORM PROCESS");

	if ($generateFor==='VB') {
		include 'generatorVB.class.php';
		$generated = new generatorVB($tableconstrunct, $usercode, $comment);
		$date = date(DATETIME_DISPLAY);
		$text = <<<TEXT
	{$usercode} has used the {$generateFor} Generator to create a class called {$generated->classname()} on {$date}
TEXT;
		datalogger::datalog("TC GENERATOR", 1200,"VB Table Class Generator", $text, $generated->Generate() );
		echo "<pre style='background-color:white;'>", print_r($generated->Generate(), 1), "</pre>";
		die();
	} else if($generateFor==='C#') {
		include 'generatorC.class.php';
		$generated = new generatorC($tableconstrunct,$namespace, $usercode, $comment);
		$date = date(DATETIME_DISPLAY);
		$text = <<<TEXT
	{$usercode} has used the {$generateFor} Generator to create a class called {$generated->classname()} on {$date}
TEXT;
		datalogger::datalog("TC GENERATOR", 1201,"C# Table Class Generator", $text, $generated->Generate() );
		echo "<pre style='background-color:white;'>", print_r($generated->Generate(), 1), "</pre>";
		die();

	}
}
