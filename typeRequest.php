<?php
	include 'server.inc';
	
	// Define variables used in the program
	$dataTypesTable = "DataTypes";
	$fieldQuery = "SELECT Description, Definition FROM $dataTypesTable";
	$standardDataTypes = array("Customer Name", "Salutation", "Designation", "Company Name", "Email");
	$returnData = array();
	
	// Select all data types from the store.
	$result = sqlsrv_query($dbhandle, $fieldQuery);

	// Check the description of each data type returned, removing matches in $standardDataTypes
	while($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) 
	{	
		if(!(in_array($row['Description'], $standardDataTypes)))
		{ // Store the remaining data in an array
		
			$returnData[] = $row;
		}
	}
	
	sqlsrv_close($dbhandle);
	
	// sort($returnData); Determine a functioning sorting method for this array.
	
	// Output HTML for requesting form. Note: Consider using jQuery UI to turn this into either a multi-part form
	// or a set of accordion tabs.
?>
	
Please select a base data type below: 
<br /><br />
	<table border="1">
		<tr>
			<th  align="center" valign="middle" scope="col">Field Description</th>	
			<th  align="center" valign="middle" scope="col">SQL Data Type</th>
			<th  align="center" valign="middle" scope="col">Select</th>
		</tr>
					
		<?php foreach($returnData as $dataRow): ?>
		<tr>
			<td><?php echo $dataRow["Description"]; ?></td> 
			<td><?php echo $dataRow["Definition"]; ?></td>
			<td><input type="radio" name="dataSelect" value="<?php $dataRow['Description'] ?>" /></td>
		</tr>
		<?php endforeach; ?>
	</table>
	<br />