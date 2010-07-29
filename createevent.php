<?php
	// Include variables and connect to server
	session_start();
	// echo $_SESSION['startCheck'];
	include 'server.inc';
	// Table to access
	$tableName = "Events";

	// Determine if the details provided are already present.
	$sqlQuery = "SELECT *
				 FROM $tableName 
				 WHERE( (eventName = ?) AND(courseCode = ?) )";
				 
	$result = sqlsrv_query($dbhandle, $sqlQuery, array(&$_POST['eventName'], &$_POST['courseCode']));
	
	if(sqlsrv_has_rows($result))
	{ // refuse to allow transaction by freeing resources and returning to the main page.
		session_destroy();
		sqlsrv_close( $dbhandle);
		header( 'refresh: 5; url=/AEM/index.php' );
		echo '<h1>Error: Event with identical details already present. Returning to main page.</h1>';
	}	
	
	// Attempt to insert a new record based on the event profile
	$sqlInsert = "INSERT INTO $tableName 
				( eventName, courseCode) VALUES (?,?)"; // Field names in table

	$parameters = array(&$_POST['eventName'], &$_POST['courseCode']);
	
	//execute the SQL query and return records
	$result = sqlsrv_query($dbhandle ,$sqlInsert, $parameters);
	
	if($result === false)
	{ // Insertion has failed, terminate process and inform user
     echo "Row insertion failed.<br />";
     die (FormatErrors( sqlsrv_errors() ) );
	}
	else // Row successfully inserted
	{   // Free statement
		sqlsrv_free_stmt( $result);
		// re-run initial query
		$result = sqlsrv_query($dbhandle, $sqlQuery, array(&$_POST['eventName'], &$_POST['courseCode']));
		// Retrieve record EventID 
		if( sqlsrv_fetch( $result ) === false )
		{
			echo "Error in retrieving row.";
			die( FormatErrors( sqlsrv_errors() ));
		}
		
		$id = sqlsrv_get_field($result, 0);
		
		sqlsrv_close( $dbhandle);
		$_SESSION["useEventID"] = $id; // Store variable in session
		$_SESSION["newForm"] = "true"; // Indicate that a new form must be created.		
		header( 'refresh: 5; url=/AEM/createform.php'); // Delay in redirect, appears to resolve Session issues.
		echo "Matching form found. Transferring to Form Editor.";
	}

?>