<?php
	/* Setup Section (start session, include common code) */
	session_start();
	include 'server.inc';

	/**************************UI Variables - Modify as needed*******************************\
	/ $queryFail     \
	/ $noEventFail	 |_____ 	   Type: String
	/ $noFormsFail	 |		Description: UI Messages on Application Events
	/ $eventSuccess  /
	/****************************************************************************************/
	
	$queryFail = "Query execution failed. Returning to main page.";
	$noEventFail = "No event with corresponding EventID found. Returning to main page.";
	$noFormsFail = "No form with corresponding EventID found. Transferring to Form Editor.";
	$eventSuccess = "Record Found. Proceeding to Form Editor";
	
	/* Application Variables */
	$_SESSION['EventID'] = $_POST['searchEvents']; // Store the EventID we need in Session.
	$delay = 3; // Number does not need to be enclosed in quotes when passed as a parameter.
	
	// TRANSACT-SQL Query: determines which records have the corresponding Event ID.
	$baseQuery="SELECT * FROM $eventsTable WHERE (EventID = ?)";
	//execute the SQL query and return records.
	$result = sqlsrv_query( $dbhandle, $baseQuery, array(&$_POST['searchEvents']));
	
	// Then determine whether the event exists
	if($result === false) {
		sqlStmtHandler($dbhandle, $queryFail, $delay, $mainPage, 0, array()); }
	elseif(sqlsrv_has_rows($result) === false) {
		sqlStmtHandler($dbhandle, $noEventFail, $delay, $mainPage, 0, array()); }
	else {
		// now confirm that Forms for the eventID specified exists
		sqlsrv_free_stmt($result);
		$baseQuery="SELECT * FROM $formsTable WHERE (EventID = ?)"; 
		/* Use a query with a static cursor for this instance 
			See: http://msdn.microsoft.com/en-us/library/ee376927%28v=SQL.90%29.aspx
			For an explanation and note the limitations
		*/
		$result = sqlsrv_query($dbhandle, $baseQuery, array(&$_POST['searchEvents']), array("Scrollable"=>'static'));

		if($result === false) {
		sqlStmtHandler($dbhandle, $queryFail, $delay, $mainPage, 0, array()); }
		elseif(sqlsrv_has_rows($result) === false) 
		{
			$store = array('FormID'=>-1, 'newForm'=>true);
			sqlStmtHandler($dbhandle, $noFormsFail, $delay, "createForm.php", 1, $store);
		}
		else {
			// Determine which form the user wants to use. Unfortunately, to ensure that error situations
			// and the HTML below do not mix, a particularly ugly use of echo is the simplest solution.
			
					
			// Create a menu to allow user to select a form (uses jQuery)
			// in an ungainly attempt to avoid using three pages, this will reload the page.
			// (a interface with frames should be the next thing on my list of improvements).
					
				echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
<html>
	<title>Form Selection</title>	
		
	<head>
		<h3>Please select the form to modify</h3><br />
		<!-- Attempted use of jQuery to allow more complex behaviour-->
		<script src="./js/jquery-1.4.2.min.js" type="text/javascript"></script>
		</head>
		
		<body>
			<form id="formSelect" name="formSelect" method="post">
			<table border="1">
			<tr>
				<th  align="center" valign="middle" scope="col">Form ID</th>
				<th  align="center" valign="middle" scope="col">Form Name<small>(if present)</small></th>
				<th  align="center" valign="middle" scope="col">Link</th>
			</tr>';	
					
						while( $form = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC))
						{	// Store formID as the button's ID
							echo '<tr>
							<td>' . $form['FormID'] . '</td>
							<td>' . $form['FormName'] . '</td>
							<td><input type="button" id = "' . $form["FormID"] .'" name="enableForm" value="Edit" /></td>
						</tr>';	
						} // Close the table & include a create new form button (uses id=-1) to id new form requests
						echo '</table>
					<br /><br />	
					<input type="button" id="-1" name="createNewForm" value="Create New Form" />
					</form>';
					
					// jQuery Script to pass the variable on. Remember to extend with dynamic form creation & submission
					// Note use of jQuery's this and ensure it works in all utilised environments.
					
					// !! THIS. attr is malfunctioning and failing to pass the id.
					echo '<script type="text/javascript">
							$("#formSelect input:button").click(function() {
								var retFormID = $(this).attr(\'id\');
								var postArray = new Array();
								postArray["formID"] = retFormID;
								post_to_url("formRedirect.php", postArray, "post");
							});
							
						// Pure Javascript Solution, as suggested by Rakesh Pai:
						// http://stackoverflow.com/questions/133925/javascript-post-request-like-a-form-submit
						function post_to_url(path, params, method) {
							method = method || "post"; // Set method to post by default, if not specified.

							// The rest of this code assumes you are not using a library.
							// It can be made less wordy if you use one.
							var form = document.createElement("form");
							form.setAttribute("method", method);
							form.setAttribute("action", path);
				
							for(var key in params)
							{
								var hiddenField = document.createElement("input");
								hiddenField.setAttribute("type", "hidden");
								hiddenField.setAttribute("name", key);
								hiddenField.setAttribute("value", params[key]);

								form.appendChild(hiddenField);
							}

							document.body.appendChild(form);    // Not entirely sure if this is necessary
							form.submit();
						}
							
						</script>
				</body>
			</html>';
					

			}
		}
?>