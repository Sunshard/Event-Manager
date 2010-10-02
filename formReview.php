<?php
	// Initialise session and connect to server.
	session_start();
	include 'server.inc';
	
	// Determine contents of the POST
	
	print_r($_POST["label"]);
	print_r($_POST["dataType"]);
	
?>