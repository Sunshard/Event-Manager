<?php // Type number of fields correctly for the table
	$fieldQty = (int)$_POST['fieldQty'];
	$user=;
	$pass=;
	/* Connection to Database here (using example code for a PDO connection) */
	// $avantusDb = new PDO('mysql:host=localhost;dbname=Events', $user, $pass);
	// Example of comprec's (uses mysqli not PDO):  new mysqli("mysql.hjmills.co.uk", "comprec", "dan1el", "hjmills_comprec");

	/*
	Print table header. Contents include:
	- field label
	- enabled (Using a checkbox, with the tick present if yes).
	- data type **Determine if necessary / desirable
	
	To Do: Determine if we can get an overriding css for the table. If so, overwrite border
	*/

	//! Determine how to encapsulate table correctly as another submission form
	
	echo '<form action="eventpreview.php" method="post">

			<table border="1">
			<tr>
				<th 
				<th  align="center" valign="middle" scope="col">Enabled? <small>(Yes/No)</small></th>
				<th  align="center" valign="middle" scope="col">Field Label</th>
				<th  align="center" valign="middle" scope="col">Data Type</th>
			</tr>';


	for($i = 1; $i < $fieldQty; $i++) 
	{	/* Looping production of input fields.
		Data types currently limited to String, Number and Date.
		*/

		echo '<tr>
				<td><input type="checkbox" name="isEnabled$fieldQty" /></td>
				<td><input type="text" name="label$fieldQty" /></td>
				<td><select name="dataType$fieldQty">
						<option value="String" selected="selected">String</option>
						<option value="Number">Number</option>
						<option value="Date">Date</option>
					</select></td>
			</tr>';
	}
	
	echo '</table>
	<br><input type="submit" />
	</form>';
?>
