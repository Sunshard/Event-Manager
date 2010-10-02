<?php
	// Server settings may be modified below
	$serverName = "localhost";
	$dbName = "Event-Manager";	
	$mainPage = "index.php";
	
	// Allow use of Twig templates (see twig-project.org)
	require_once($_SERVER["DOCUMENT_ROOT"]."/Twig/lib/Twig/Autoloader.php");
	Twig_Autoloader::register();	
	
	$loader = new Twig_Loader_Filesystem($_SERVER["DOCUMENT_ROOT"].'/Twig/templates');
	$twig = new Twig_Environment($loader, array(
		'cache' => $_SERVER["DOCUMENT_ROOT"].'/Twig/compilation_cache',
	));
	
	/* Connection Modes for SQLSRV Driver for PHP 
		- Connecting with SQL Server Authentication
		- Connecting with Windows Authentication
		Due to the complications inherent in Windows authentication (with NTFS Filesystem Permission)
		using SQL Server Mode
	*/ 
	// SQL Server Authentication Mode
	$userName = "WebDev";
	$Pass = "P@ssw0rd";
	$sqlsrv_connection = array("UID"=>$userName, "PWD"=>$Pass, "Database"=>$avantusDb);
	
	// Windows Authentication Mode
	//$sqlsrv_connection = array("Database"=>$avantusDb);
	
	//connection to the database
	   $dbhandle = sqlsrv_connect($avantusServer, $sqlsrv_connection)
	   or die( FormatErrors( sqlsrv_errors() ) );
		
	// FormatErrors code was written by Microsoft (retrieved from AdventureWorks example database on MSDN)
	function FormatErrors( $errors )
	{	// Display errors.
		echo "Error information: <br/><br/>";

		foreach ( $errors as $error )
		{
			echo "SQLSTATE: ".$error['SQLSTATE']."<br/>";
			echo "Code: ".$error['code']."<br/>";
			echo "Message: ".$error['message']."<br/>";
		}
	}
	
	/* This function will release resources and redirect to a specified url.
		If flag is set to 1, it will store the array of variables provided in session
		If flag is set to 0, it will destroy the session
		 
		parameters:
			$conn - connection resource returned by query
			$msg - message to display
			$delay - delay in seconds before page change (must be a number)
			$redir - webpage in local directory (AEM) to redirect to
			$flag - determines how this is processed
			$store - an associative array of session variables to store
	*/
	function sqlStmtHandler($conn, $msg, $delay, $redir, $flag, $store)
	{
		if($flag == 1)
		{
			foreach($store as $key => $value)
			{ // 'unpack' each pair of values and store them in Session.
				$_SESSION[$key] = $value;
			}
		}
		elseif($flag == 0)
		{
			session_destroy();
		}
		else { // sqlStmtHandler has been passed a bad flag at this point.
			die("FATAL ERROR: abnormal variable passed");
		}
		
		sqlsrv_close( $conn);
		header( "refresh: $delay; url=/AEM/$redir" );
		echo "<h3>$msg</h3>";		
	}
