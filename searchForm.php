<?php
	/* Setup Section (start session, include common code) */
	session_start();
	include 'server.inc';

	// Initialise Variables
	$queryFail = "Query execution failed. Returning to main page.";
	$noEventFail = "No event with corresponding EventID found. Returning to main page.";
	$noFormsFail = "No form with corresponding EventID found. Returning to main page.";
	$delay = 5; // Determine if the number needs to be enclosed in quotes.

	$tableName = "Events";
	
	// SQL Query: determines which records have the corresponding Event ID.
	$baseQuery="SELECT *
				FROM $tableName
				WHERE (EventID = ?)";
	
	//execute the SQL query and return records.
	$result = sqlsrv_query( $dbhandle, $baseQuery, array(&$_POST['searchEvents']));
	
	// Then determine whether the event exists
	if($result === false)
		sqlStmtHandler($dbhandle, $queryFail, $delay, $mainPage);
	
	if(!sqlsrv_has_rows($result))
			sqlStmtHandler($dbhandle, $noEventFail, $delay, $mainPage);
	
	// now confirm that Forms for the eventID specified exists
	sqlsrv_free_stmt($result);
	$tableName = "Forms"; // Test if this works.
	$result = sqlsrv_query($dbhandle, $baseQuery, array(&$_POST['searchEvents']));
	
	if($result === false)
		sqlStmtHandler($dbhandle, $queryFail, $delay, $mainPage);
		
	if(!sqlsrv_has_rows($result))
		sqlStmtHandler($dbhandle, $noFormsFail, $delay, $mainPage);
	
	// At this point, we need only determine which form to enter (depending on how many forms are returned).
	if(sqlsrv_num_rows($result) > 1)
	{	// Create a menu to allow user to select a form
	echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
		<html>
		<title>Form Selection</title>	
		
		<head></head>
		
		<body>
			<table border="1">
			<tr>
				<th  align="center" valign="middle" scope="col">Form ID</th>
				<th  align="center" valign="middle" scope="col">Link</th>
			</tr>';	
	
		while( $form = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC))
		{	
			echo '<td>' . $form['FormID'] . '</td><td><input type="button" name="enable" value="Edit" onClick="()" /></td>';
		}
		echo '</table> </body></html>';
		
	}
	else { 
		$_SESSION['useEventID'] = $_POST['searchEvents']; // Store variable in session
		$_SESSION['newForm'] = "false"; // Indicate form already exists
		sqlsrv_close( $dbhandle);
		header( 'refresh: 5; url=/AEM/createform.php' );
		echo '<h1> Record Found. Proceeding to event modification </h1>';	
	}
?>