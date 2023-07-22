<?php
session_start();
include 'definitions.php';
include 'autoloader.php';

$browser = new Wolfcast\BrowserDetection($_SERVER['HTTP_USER_AGENT']);
$_SESSION['postvars']['global']['id']="";
$_SESSION['postvars']['global']['pagename']="Landing Page";
include 'hitcounter.php';
?>

<html>
<head>
	<title>Table Class Generator</title>
	<link rel="stylesheet" href="<?=STYLES?>datalogger.css?version=1.123456">
</head>
<body>
<h1>Table Class Generator</h1>
<h3>Version 0.00</h3>
<?= datalogger::CheckError()?>
<?php include 'clear_errors.php';?>
<div>
	<form action="generator.php" method="post" enctype="multipart/form">
		<p>Generate Class for:</p>
		<input type="radio" name="generateFor" id="CSharp" value="C#">
		<label for="CSharp">C# / MVC</label><br>
		<input type="radio" name="generateFor" id="VB" value="VB">
		<label for="VB">VB</label><br><br>

		<input type="text" name="usercode" id="usercode">
		<label for="usercode">User Code</label><br><br>

		<input type="text" name="namespace" id="namespace">
		<label for="namespace">Name Space</label><br><br>

		<input type="text" name="comment" id="comment">
		<label for="comment">Comment</label><br><br>

		<textarea name="SQLOutput" id="SQLOutput" rows="12" cols="50">
		</textarea>
		<label for="SQLOutput">SQL Output</label><br><br>

		<input type="submit" value="Generate">
	</form>
</div>
</body>
</html>
