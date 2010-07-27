<?php
	// Initialise number of base fields to 10. This limit can be altered with the variable below.
	$fieldQty = 10;
	
	// Start document, on-page Javascript included below.
	echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
		<html>
		<title>Event Form Design</title>	
		
		<head>
		
		</head>';
	
	/*
	Print table header. Contents include:
	- Enabled:- Marks whether the field is in use (Using a checkbox, with the tick present if yes).
	- Field Label:- The Description of the field the users see 	
	- Data type:- An archetype of the field's contents; used for mapping to the backend database.
	
	To Do: 
	- Determine if we can get an overriding css for the table. If so, overwrite border
	- css should also replace the 'align' property in th as it is deprecated.
	*/
	
	echo '<body>
			<form action="formReview.php" method="post">

			<table border="1">
			<tr>
				<th  align="center" valign="middle" scope="col">Enable / Disable</th>
				<th  align="center" valign="middle" scope="col">Field Label</th>
				<th  align="center" valign="middle" scope="col">Data Type</th>
			</tr>';

	for($i = 1; $i <= $fieldQty; $i++) 
	{	/* Looping production of input fields.
		
		Notes: 
		-	Determine if tabindex functions.
		-	Check that the javascript works
		
		Description of Data Types:
		
		Customer Name (CName) - String to hold customer name
		Salutation (Title) - a.k.a. Titles (e.g. Mr., Mrs.)
		Company Name (Company) - String to hold customer's company
		Designation - Job Role
		Email - String
		Custom - Special field; must be manipulable to any design required
				- Suggest using Javascript / PHP to allow exact manipulation
				- Backend to use simple text field.
		*/

	// Hidden input to store toggle state (until we can figure out how to store it elsewhere)
	echo 	'
			<tr>
				<td><input type="button" name="enable" value="Toggle" tabindex="';
		$j = (3*$i - 2); echo "$j";
		echo '" onclick="enableDataEntry(';
		$j = $i - 1;
		echo "$j"; echo ')" /><input type="hidden" value="0" name="enableCheck" /></td>';
		echo '
			<td><input type="text" name="label" tabindex="';
		$j = (3*$i - 1); echo "$j";
		echo '" disabled="true" /></td>
				<td><select name="dataType" tabindex="';
		$j = (3*$i); echo "$j";
		echo '" disabled="true">
					<option value="dataTypeCName" selected="selected">Customer Name</option>
					<option value="dataTypeTitle">Salutation</option>
					<option value="dataTypeDesignation">Designation</option>
					<option value="dataTypeCompany">Company Name</option>
					<option value="dataTypeEmail">Email</option>
					<option value="dataTypeCustom">Custom</option>
				</select></td>
			</tr>';
	}
	
	  echo '</table>
			<br /><input type="submit" />
			</form>';
			
	echo '<script type="text/javascript"> // Note: If we want PHP to modify the script, it may be necessary to have this in-place on the page.
		<!--		
			function enableDataEntry(select)';
	echo "	{ // Initialise (Poor discipline, determine if there is a better way to initialise)
			var enableArray = document.getElementsByName('enableCheck');
			// Warning: Need to pass by reference not by value. Ensure this is the case.
			var labelArray = document.getElementsByName('label');
			var typeArray = document.getElementsByName('dataType');";
			
	echo '	
				if(enableArray[select].value="0") // Determine if this works in Javascript
				{ // Need to pass by reference. Not working currently.
					labelArray[select].disabled="false";
					typeArray[select].disabled="false";
					enableArray[select].value="1";
				}
				else { // Assume it has been unchecked.
					labelArray[select].disabled="true";
					typeArray[select].disabled="true";
					enableArray[select].value="0";
				}
			}
		// -->
		</script>
	</body>
</html>';
?>
