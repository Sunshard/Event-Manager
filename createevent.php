<?php
	// Ensure session is running, connect to server
	session_start();
	include 'server.inc';
	
	// Initialise variables
	$delay = 3;
	$eventSuccess = "Event Successfully created, proceeding to Form Editor.";
	$tooManyEvents = "Error: Event with identical details already present. Returning to main page.";
	$eventDetails = array(&$_POST['eventName'], &$_POST['courseCode']);

	// Transact-SQL Statements (expand explanation if possible)
	$sqlQuery = "SELECT *
				 FROM $eventsTable 
				 WHERE( (eventName = ?) AND(courseCode = ?) )";
	$sqlInsert = "INSERT INTO $eventsTable 
					( eventName, courseCode) 
					VALUES (?,?)"; 

	/* Determine if an Event with identical details is present using TRANSACT-SQL
		If so, handle appropriately using sqlStmtHandler.						 */	
	$result = sqlsrv_query($dbhandle, $sqlQuery, $eventDetails);
	
	if(sqlsrv_has_rows($result)) {	
		sqlStmtHandler($dbhandle, $tooManyEvents, $delay, $mainPage, 0, array());
	}
	else { // Using the form details, create the event in the table.
	
		$result = sqlsrv_query($dbhandle ,$sqlInsert, $eventDetails);
	
		// If the insertion fails, throw a warning and exit immediately. (Problem severe enough to warrant attention).
		if($result === false)
		{ 
			echo "Fatal Error: Row insertion failed.<br />";
			die (FormatErrors( sqlsrv_errors() ) );
		}
		else
		{ // If the row is successfully inserted, attempt to retrieve the same record and acquire its eventID.
			sqlsrv_free_stmt( $result);
			$result = sqlsrv_query($dbhandle, $sqlQuery, $eventDetails);
			
			// Note: If the newly inserted record cannot be found, then throw a fatal error.
			if( sqlsrv_fetch( $result ) === false )
			{
				echo "Error in retrieving row.";
				die( FormatErrors( sqlsrv_errors() ));
			}
			
			$id = sqlsrv_get_field($result, 0);
		
			// Shut down SQL Server connection, store session variables & pass them to the form editor.
			$store = array('EventID'=>$id, 'newForm'=>true, 'FormID'=>-1);
			sqlStmtHandler($dbhandle, $eventSuccess, $delay, "createForm.php", 1, $store);
		}
	}

?>