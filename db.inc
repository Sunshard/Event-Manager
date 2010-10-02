<?php
  // Common database variables and queries will be stored here
  
  // Database Table Names
	$eventsTable = "Events";
	$formsTable = "Forms";
	$dataTypesTable = "DataTypes";
	$fieldsTable = "Fields";
	$formsFieldsTable = "FieldLink";
	
  // Table Labels for data manipulated by the application
	$dropDownType = "List";
	
	/* The following query is intended to return the following: All fields and their 
		corresponding data type definitions belonging to the FormID specified.
	*/
	$baseFormQuery = "SELECT * FROM $fieldsTable AS f
					INNER JOIN $dataTypesTable AS d
					ON f.dataTypeID = d.dataTypeID
					WHERE f.fieldID IN
						(SELECT FieldID FROM $formsFieldsTable WHERE FormID = ?)";

	$formViewQuery = $baseFormQuery ."ORDER BY f.Position ASC" ;
	$formView = "CREATE VIEW FormEditor AS $baseFormQuery";