<?php
	// Initialise number of base fields to 10. This limit can be altered with the variable below.
	$fieldQty = 10;
	
	// Start document, on-page Javascript included below.
	echo '
		<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
		<html>
		<title>Event Form Design</title>	
		
		<head>
		<script type="text/javascript"> // Note: If we want PHP to modify the script, it may be necessary to have this in-place on the page.
		<!--		
			function enableDataEntry()
			{ // Initialise (Poor discipline, determine if there is a better way to initialise)
			var enableArray = document.getElementsbyName(isEnabled);
			// Warning: Need to pass by reference not by value. Ensure this is the case.
			var labelArray = document.getElementsbyName(label);
			var typeArray = document.getElementsbyName(dataType);
			
			for(var i = 1; i < $fieldQty; i++)
			{
				if(enableArray[i].checked) // Determine if this works in Javascript
				{ // Need to pass by reference. Check if this succeeds.
					labelArray[i].disabled="false";
					typeArray[i].disabled="false";
				}
				else { // Assume it has been unchecked.
					labelArray[i].disabled="true";
					typeArray[i].disabled="true";
				}
			}
		// -->
		</script>
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
	
	echo	'<body>
	
			<form action="formReview.php" method="post">

			<table border="1">
			<tr>
				<th  align="center" valign="middle" scope="col">Enabled? <small>(Yes/No)</small></th>
				<th  align="center" valign="middle" scope="col">Field Label</th>
				<th  align="center" valign="middle" scope="col">Data Type</th>
			</tr>';


	for($i = 1; $i < $fieldQty; $i++) 
	{	/* Looping production of input fields.
		
		Notes: 
		-	Determine if tabindex functions.
		-	Check that the javascript works
		
		Description of Data Types:
		
		Customer Name (CName) - 
		Salutation - 
		Company Name - 
		Designation
		Email
		Custom
		*/

		echo '<tr>
				<td><input type="checkbox" name="isEnabled" tabindex=(3*$i - 2)/></td>
				<td><input type="text" name="label" tabindex=(3*$i - 1)/></td>
				<td><select name="dataType" tabindex= 3*$i>
						<option value="dataTypeCName" selected="selected">Customer Name</option>
						<option value="dataType">Salutation</option>
						<option value="dataTypeDesignation">Designation</option>
						<option value="dataTypeCompany">Company Name</option>
						<option value="dataTypeEmail">Email</option>
						<option value="dataTypeCustom">Custom</option>
					</select></td>
			</tr>';
	}
	
	echo '</table>
	<br><input type="submit" />
	</form>
	</body>
	</html>';
?>
