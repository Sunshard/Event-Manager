<?php
	// Server settings may be modified below
	$avantusServer = "1-PC";
	$userName = "WebDev";
	$Pass = "P@ssw0rd";
	$avantusDb = "AEM";
	
	// SQLSRV Driver-Specific Strings (constructed from above information)
	$sqlsrv_connection = array("UID"=>$userName, "PWD"=>$Pass, "Database"=>$avantusDb);
	
	//connection to the database
	$dbhandle = sqlsrv_connect($avantusServer, $sqlsrv_connection)
	   or die( FormatErrors( sqlsrv_errors() ) );
		
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
	} 
?>