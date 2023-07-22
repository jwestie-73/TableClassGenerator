<?php

if (isset($_SESSION['error'])) {
	datalogger::datalog("DATALOGGER", 2999, "Clear Errors", "Internally reset error codes in session", implode("/",$_SESSION['error']));

	unset($_SESSION['error']);

}
