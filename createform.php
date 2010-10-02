<?php
	// session variables retrieved and server connection initialised.
	session_start();
	include 'server.inc';
	$linkID = $_SESSION["EventID"];
	$formID = $_SESSION["FormID"];
	$newForm = $_SESSION["newForm"];

	// Strings used in TRANSACT-SQL. Statements may be modified here.
	// Not fully changeable - check code for variable repurposing.
	$sqlQuery = "SELECT * FROM $formsTable WHERE FormID = ?";
	$sqlInsert = "INSERT INTO $formsTable (EventID) VALUES (?)";

	// Variables used in dynamic form generation (using PHP)
	$fieldQty = 10;
	$fieldData = array();
	$maxRow = $fieldQty; // Highest row position in field array.
	$htmlSelectType = 'selected="selected"'; // Minified form is 'selected'; Xml-compliant version may not recognised in Firefox (RRRGH)

	// Code below retrieves any data for the form and assists population of its fields.
	if($newForm == false)
	{   // Run query.
		$result = sqlsrv_query($dbhandle, $sqlQuery, array(&$formID));

		if($result === false)
		{   // No form found, terminate application
			die (FormatErrors( sqlsrv_errors()));
		}
	}
	elseif($newForm == true) { // Create a new form
		$result = sqlsrv_query($dbhandle, $sqlInsert, array(&$linkID));

		if($result === false)
		{   // Fatal Error, unable to create form
			die (FormatErrors( sqlsrv_errors()));
		}

		sqlsrv_free_stmt($result);
		/* Run a query to locate the newly created form. Operates on the assumption
			that forms are created with ascending unique IDs, (orders them in descending order).
		*/
		$sqlQuery = "SELECT * FROM $formsTable WHERE EventID = ? ORDER BY FormID DESC";
		$result = sqlsrv_query($dbhandle, $sqlQuery, array(&$linkID));

		if($result === false)
		{   // Fatal Error, unable to create form
			die (FormatErrors( sqlsrv_errors()));
		}
	}
	else { // Assume that the session variable has been incorrectly passed, and terminate program.
		die("FATAL ERROR: Session variable newForm is not set. <br />");
	}

	// Retrieve the first form's data as an associative array
	$formData = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC );

	if($formData === false)
	{ // Data retrieval unsuccessful, terminate application.
		echo 'Data retrieval unsuccessful<br />';
		die (FormatErrors( sqlsrv_errors()));
	}

	sqlsrv_free_stmt($result); // Free resources

	// Set the FormID to the latest value (required for new forms prev. assigned -1)
	// and formName (if present
	$formID = $formData['FormID'];
	$nameString = "";
	if(isset($formData["formName"]))
	{
		$nameString = 'Form Name:'. $formData["formName"];
	}

	// Prepare to retrieve the number of fields and their data if the form already exists
	if($newForm == false)
	{ 	// Retrieve result.
		$result = sqlsrv_query($dbhandle, $formViewQuery, array(&$formID));

		// Retrieve and store data in an associative array.
		while ($fieldRow = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC))
		{
			$fieldData[] = $fieldRow;
			// Determine if this record's position value is the highest row we need to generate to.
			if($fieldRow['Position'] > $maxRow) {
				$maxRow = $fieldRow['Position'];
			}
		}
	}

	// Temporary variables used to populate fields
	$optionValues = array("Customer Name", "Salutation", "Designation", "Company Name", "Email");
	$posArray = array();

	//!! This code will not function correctly if more than one of the same description is encountered.
	foreach($fieldData as $field)
	{	// Add any additional, distinct Descriptions to optionValues
		if(!(in_array($field["Description"], $optionValues))) {
			$optionValues[] = $field["Description"];
		}

		$posArray[] = $field["Position"]; // populate an array of positions
	}

	// Sort created array (Note: The retrievd SQL data is already sorted in ascending order of position)
	sort($optionValues);

	/* Start document, include the jQuery libraries.
		Aim of the jQuery code:
		- each table row generated below is given the class "field"
		- jQuery code is to detect when a button is clicked and enable/disable the field's rows accordingly.
	*/
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
	<title>Form Editor</title>

	<head>
		<!-- Attempted use of jQuery to allow more complex behaviour-->
		<link type="text/css" href="css/ui-lightness/jquery-ui-1.8.4.custom.css" rel="Stylesheet" />
		<script src="./js/jquery-1.4.2.min.js" type="text/javascript"></script>
		<script src="./js/jquery-ui-1.8.4.custom.min.js" type="text/javascript"></script>
		<?php echo '<h2>Event ID: ' . $linkID . ' Form ID: ' . $formID . ' <br />'
						. $nameString . '</h2><br /><br />' ?>
	</head>
<!--
	Print table header. Contents include:
	- Enabled:- Uses Javascript + jquery to show/hide the field from the users.
	- Field Label:- The Description of the field the users see
	- Data type:- An archetype of the field's contents; used for mapping to the backend database.

	To Do:
	- Determine if we can get an overriding css for the table. If so, overwrite border
	- css should also replace the 'align' property in th as it is deprecated.
-->
	<body>
		<form action="formReview.php" method="post">
			<table border="1">
				<tr>
					<th  align="center" valign="middle" scope="col">Enable / Disable</th>
					<th  align="center" valign="middle" scope="col">Field Label</th>
					<th  align="center" valign="middle" scope="col">Data Type</th>
				</tr>

<?php
		/* Looping production of input fields

		Description of Data Types:

		Customer Name (CName) - String to hold customer name
		Salutation (Title) - a.k.a. Titles (e.g. Mr., Mrs.)
		Company Name (Company) - String to hold customer's company
		Designation - Job Role
		Email - String
		Custom - Special field; must be manipulable to any design required
			   - Implemented in the database as a selection of fields.

		NOTE: Validation is important when the user generates custom fields;
			  the application will not work correctly if the user attempts to define a
			  custom field with a Description that already exists.
		*/
	for($i = 0; $i < $maxRow; $i++): // For all existing table rows, draw the data used to populate the fields.
		$defaultDataType = array_fill(0, count($optionValues), ""); // Temporary variable to hold the
		$defaultLabel = "";
		$j = $i + 1;

		if(($newForm == false) && (in_array($j, $posArray)))
		{   // Leverage the fact that the $posArray is ordered like $fieldData
			$defaultLabel = $fieldData[$i]["Label"]; // Set as the field's Label

			//!! This code will not function correctly if more than one of the same description is encountered.
				for($k = 0; $k < count($optionValues); $k++)
				{
					if($optionValues[$k] == $fieldData[$i]["Description"]) {
						$defaultDataType[$k] = $htmlSelectType; }
				}
		}
		else { // Default to the first item in the list being selected.
			$defaultLabel = "";
			$defaultDataType[0] = $htmlSelectType;
		}

		// Generate the fields.
?>
				<tr id="field_<?php echo $i; ?>" class ="field">
					<td>
						<input type="checkbox" name="enable[]" id="Toggle_<?php echo $i; ?>" onchange=toggleDataEntry(<?php echo $i; ?>) /><label for "Toggle"></label>
					</td>
					<td>
						<input class="toggleMe" type="text" name="label[]" value = "<?php echo $defaultLabel; ?>" />
					</td>
					<td>
						<select class="toggleMe" name="dataType[]" onchange=checkCustom(<?php echo $i; ?>)>
<?php
	echo '							'; // For prettifying HTML output.
	for($k = 0; $k < count($optionValues); $k++)
		{ // Generate the standard options
			echo '<option value="' . $optionValues[$k] . '" ' . $defaultDataType[$k]  . '>' . $optionValues[$k] . '</option>
				'; // Introduce spacing (check if this parses correctly)
		}
	echo '
	';
?>
							<option value="Custom_<?php echo $i; ?>">Custom</option>
						</select>
					</td>
				</tr>
<?php endfor; ?>
			</table>
			<br />
			<input type="submit" />
		</form>

<!--
	Javascript controls; used to perform various enhancements:
	- TODO: include the jQuery Validation plugin, and use it to input mask the fields
	- Currently ensures that user is aware that disabling a field on submission will delete it from the table.

	DEBUG: function checkCustom() is redirecting to toggleDataEntry(0 in Firebug; determine why.
-->
		<script type="text/javascript">
		<!-- // hide Javascript from old browsers
			// Toggles fields when their corresponding enable/disable checkbox is changed
			function toggleDataEntry(select)
			{ // Check if the checkbox in the field is checked
				if( $("tr.field:eq(" + select + ") input:checkbox").is(":checked"))
				{
					$("tr.field:eq(" + select + ") .toggleMe").attr("disabled", "disabled");
				}
				else {
					$("tr.field:eq(" + select + ") .toggleMe").removeAttr("disabled");
				}
			}

			// Checks if the user has selected a custom field and attempts to provide them with a selection.
			// Uses jQuery UI 1.8.4 - consider securing the request for data...
			function checkCustom(select)
			{ // Select the option labelled custom
				if( $("tr.field:eq(" + select + ") [value^='Custom_']").is(":selected") )
				{	// Open the dialog box
					$("#newField").dialog('open')
								.html('Retrieving server data...'); // (Keep user informed)
					
					// Send post request for data types and return in dialog box
					$.post("typeRequest.php", function(data){
						$("#newField").html(data);
					}); 
				}
				else { // Ensure that the HTML for the tags remains the same
					$(this).html("Custom");
				}
			}

		$(document).ready(function() {	
			/* Hijack the form submission and check if any fields are disabled
				Ensure the user is informed that these fields will not be included any more.
			*/
			$("form").submit(function() {
				if($("tr.field input:checkbox").is(":checked")) {
					return confirm("Warning! One or more fields are disabled." +
					"\nThis will delete them from the database. Do you wish to continue?");}
			 });
			
			// Initialise the Field Generator dialog box.
			$("#newField").dialog({
				autoOpen: false,
				title: "New Field Generator",
				buttons:{ "Confirm Selection": function() {
							var newType = $("#newField input:checked").attr("value"); // Acquire selected type
							
							
							// Consider hiding or delaying the closing of this box if a new list is being created.
							if(newType == "<?php echo $dropDownType ?>")
							{ // Special handler spawns additional dialog for drop-down list items.
								
							}
							else {
								$(this).dialog("close"); 							
							}
							} 
						},
			});
			
			// Initialise the List Generator dialog box.
			// Consider extending with a 'Preview' button
			$("#newList").dialog({
				autoOpen: false,
				title: "Drop-down List Generator",
				buttons:{ "Commit": function() {
							
							// Need to acquire data. create large text box,
							// and have them delimited with commas.
							
							$(this).dialog("close"); 							
							}

							
						},
			})
			.html("Oh no");
		});
		// -->
		</script>

		<!--Placing container for dialog boxes here-->
		<div id="newField">
		</div>

		<div id="newList">
		</div>
		
	</body>
</html>

