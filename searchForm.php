<?php

// Need to test this code. Currently untested.

//-------------------------- Code Block similar to createevent. Consider methods for reducing this
//										i.e. global var
	include 'server.inc';

//------------------------------------End of Code Block


	// SQL Query: Aims to determine which forms have the corresponding Event ID and exist in the Events Table
	// Modify as necessary here:
	$query="SELECT * 
			FROM Forms 
			WHERE Forms.EventID = ?
			AND EXISTS 
				(SELECT * 
				 FROM Events 
				 WHERE Events.EventID = Forms.EventID";
	
	//execute the SQL query and return records.
	$result = sqlsrv_query( $dbhandle, $query, array($_POST['searchEvents']));

	// Notify user; consider a more effective method (e.g. a javascript message box using alert.)
	if(!sqlsrv_has_rows($result)) {
		session_destroy();
		sqlsrv_close( $dbhandle);
		header( 'refresh: 5; url=/AEM/index.php' );
		echo '<h1>Error: No forms with corresponding EventID found. Returning to main page.</h1>';
	}
	elseif(sqlsrv_num_rows($result) > 1) { // Determine a more effective way to respond to this. consider allowing form selection..
		sqlsrv_close( $dbhandle);
		session_destroy();
		header( 'refresh: 5; url=/AEM/index.php' );
		echo '<h1>Error: More than one form with corresponding EventID found. Returning to main page.</h1>';
	}
	else { // pass EventID to createform.php
		$_SESSION['useEventID'] = $_POST['searchEvents']; // Store variable in session
		$_SESSION['newForm'] = "false"; // Indicate form already exists
		sqlsrv_close( $dbhandle);
		header( 'refresh: 5; url=/AEM/createform.php' );
		echo '<h1> Record Found. Proceeding to event modification </h1>';
	}
?>