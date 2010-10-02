<?php
	/* Setup Section (start session, include common code) */
	session_start();
	include 'server.inc';
	$eventSuccess = "Record Found. Proceeding to Form Editor";
	$delay = 2;
	
	// If the user has submitted the form, redirect to the form editor
	if($_POST['formID'] == -1)				
	{
		$store = array('FormID'=>$_POST['formID'], 'newForm'=>true);
	}
	else {
		$store = array('FormID'=>$_POST['formID'], 'newForm'=>false);				
	}
	
	
	sqlStmtHandler($dbhandle, $eventSuccess, $delay, "createForm.php", 1, $store);
?>