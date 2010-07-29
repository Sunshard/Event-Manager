<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
<!--AEM Start Page-->
<!--Created on 13/07/2010 by Brian Wong-->
<!--CSS Implementation is desirable, as appearance is rather plain.-->

<?php // Additional PHP goes here
	session_start();
	// $_SESSION['startCheck'] = "true";
?>

<html lang="en-SG">

<title>Avantus Event Manager</title>

<body>

	<!--Provide user option to either search existing events or create a new one.-->
	<p>
		Welcome to the Avantus Event Manager.<br />
		To modify an existing template, please enter the event ID: <br />
		<form action="searchForm.php" method="post">
			<input type="text" name="searchEvents" /><br />
			<input type="submit" value="Search"/><br />
			<br />
		</form>
	</p>
	
	<p>
		Alternatively if you wish to create a new event, key in the details below:<br />
		<br />
		<form action="createEvent.php" method="post">
			<table border="1">	
				<tr> <td>Event Name : </td><td> <input type="text" name="eventName" /></td></tr>
				<tr> <td>Course Code : </td><td><input type="text" name="courseCode" /></td></tr>
			</table>		
		
		<input type="submit" value="Create Event"/>
		
		</form>
	</p>

</body>

</html> 