<?php

foreach (glob(CLASSES_PATH . '*.class.php') as $filename)
{
	require_once $filename;
}

require_once VENDORS . 'BrowserDetection/lib/BrowserDetection.php';
