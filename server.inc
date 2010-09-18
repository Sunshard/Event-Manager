<?php
	// Server settings may be modified below
	$serverName = "localhost";
	$userName = "root";
	$Pass = "P@ssw0rd";
	$dbName = "Event_Manager";
	
	$mainPage = "index.php";
	
	// Following code is specific to MS SQL Server 2008 REPLACE_SECTION

	 /*{	
	// SQLSRV Driver-Specific Strings (constructed from above information)
	$sqlsrv_connection = array("UID"=>$userName, "PWD"=>$Pass, "Database"=>$dbName);
	
	//connection to the database
	   $dbhandle = sqlsrv_connect($avantusServer, $sqlsrv_connection)
	   or die( FormatErrors( sqlsrv_errors() ) );
	} 
		
	// FormatErrors code was written by Microsoft (retrieved from AdventureWorks example database on MSDN)
	function FormatErrors( $errors )
	{	// Display errors.
		echo "Error information: <br/>";

		foreach ( $errors as $error )
		{
			echo "SQLSTATE: ".$error['SQLSTATE']."<br/>";
			echo "Code: ".$error['code']."<br/>";
			echo "Message: ".$error['message']."<br/>";
		}
	} */
	
	/* This function will release resources and redirect to a specified url.
		NOTE: It will attempt to any session running.
		 
		parameters:
			$conn - connection resource returned by query
			$errmsg - error message if connection fails
			$delay - delay in seconds before page change (must be a number)
			$redir - webpage in local directory (AEM) to redirect to
	*/
	function sqlStmtHandler($conn, $errmsg, $delay, $redir)
	{ // If the query fails, release resources and return to the main page.
			session_destroy();
			sqlsrv_close( $conn); // Replace with an implementation independant variation. REPLACE_SECTION
			header( "refresh: $delay; url=/AEM/$redir" );
			echo "<h1>Error: $errmsg</h1>";	
	}
?>
