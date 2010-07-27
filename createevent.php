<?php
	// Initialise variables (consider making them global if easier)
	
	include 'server.inc';
	
	// Table to access
	$tableName = "Events";

	//connection to the database
	$dbhandle = sqlsrv_connect($avantusServer, $sqlsrv_connection)
	    or die( FormatErrors( sqlsrv_errors() ) );
		
	// Original handler, code above attempts to use built-in error handler.
	//	or die("Couldn't connect to $avantusDb on $avantusServer"); 

	// Attempt to insert a new record based on the event profile
	$insert = "INSERT INTO ";
	$insert .= $tableName; // Table name
	$insert .= " ( event, course_code) VALUES ("; // Field names in table
	$insert .= $_POST['eventName'];
	$insert .= " , ";
	$insert .= $_POST['courseCode'];
	$insert .= " USE $tableName)";

	//execute the SQL query and return records
	$result = sqlsrv_query($dbhandle ,$insert);
		
	/* Free statement and connection resources. */
	// sqlsrv_free_stmt( $insert); Determine how this works
	sqlsrv_close( $dbhandle);

	
	// Redirect to the event form creator. (Determine if this works)
	header( 'Location: createform.php' ); 
	
	// Built-in Microsoft Error Code Handler
	function FormatErrors( $errors )
	{	/* Display errors. */
		echo "Error information: <br/>";

		foreach ( $errors as $error )
		{
			echo "SQLSTATE: ".$error['SQLSTATE']."<br/>";
			echo "Code: ".$error['code']."<br/>";
			echo "Message: ".$error['message']."<br/>";
		}
	}
?>