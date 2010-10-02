<?php
/*
Original code sourced from: http://www.reconn.us/content/view/30/51/

Notes: Create a folder named images located in the path you are planning to place the php script you are about to create. 
Make sure it has write rights for everybody or the scripts won't work ( it won't be able to upload the files into the directory).

Under Microsoft Windows (& IIS 7.5), the solution recommended is to:
	- Set write permissions in the NTFS File for the IUSR group (IIS's worker pool)
	- Deny execute permissions to the folder. (Securing the folder and working tandem with the simple extension checker used here).
	(In this case, the entire website has merely had read & execute permissions inexpertly removed: determine what
	 side-effects this will cause, if any.)

*/

// Ensure session 
session_start();

//define a maxim size for the uploaded images in Kb (Confirm size needed with Web Dev side)
 define ("MAX_SIZE","1000"); 

//This function reads the extension of the file. It is used to determine if the file  is an image by checking the extension.
 function getExtension($str) {
         $i = strrpos($str,".");
         if (!$i) { return ""; }
         $l = strlen($str) - $i;
         $ext = substr($str,$i+1,$l);
         return $ext;
 }

//This variable is used as a flag. The value is initialized with 0 (meaning no error found)  
//and it will be changed to 1 if an error occures.  
//If the error occures the file will not be uploaded.
 $errors=0;
//checks if the form has been submitted
 if(isset($_POST['Submit'])) 
 {
 	//reads the name of the file the user submitted for uploading
 	$image=$_FILES['image']['name'];
 	//if it is not empty
 	if ($image) 
 	{
 	//get the original name of the file from the clients machine
 		$filename = stripslashes($_FILES['image']['name']);
 	//get the extension of the file in a lower case format
  		$extension = getExtension($filename);
 		$extension = strtolower($extension);
 	//if it is not a known extension, we will suppose it is an error and will not  upload the file,  
	//otherwise we will do more tests
		if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif")) 
		{  //print error message
 			echo '<h1>Unknown extension!</h1>';
 			$errors=1;
 		}
 		else {
			//get the size of the image in bytes
			//$_FILES['image']['tmp_name'] is the temporary filename of the file
			//in which the uploaded file was stored on the server
			$tmp_name = $_FILES['image']['tmp_name'];
			$size=filesize($_FILES['image']['tmp_name']);

			//compare the size with the maxim size we defined and print error if bigger
			if ($size > MAX_SIZE*1024)
			{
				echo '<h1>You have exceeded the size limit!</h1>';
				$errors=1;
			}
			
			//we will give an unique name, for example the time in unix time format
			// $image_name=time().'.'.$extension;
			$image_name = $filename . '.' .$extension;
			// Consider using $_SESSION values to help name this for easy access.
		
			//the new name will be containing the full path where will be stored (images folder)
			$newname = $image_name; $uploadsdir = "images";
			//we verify if the image has been uploaded, and print error instead
			
			$copied = move_uploaded_file($tmp_name, "$newname"); 
			
			// $copied = copy($_FILES['image']['tmp_name'], "$uploadsdir/$newname");
			if (!$copied) 
			{
				echo "Attempted copy to $uploadsdir/$newname <br />";
				echo '<h1>Copy unsuccessfull!</h1><br />';
				$errors=1;
			}
		}
	}
}

//If no errors registred, print the success message
if(isset($_POST['Submit']) && !$errors) 
{	
 	echo "<h1>File Uploaded Successfully! Try again!</h1>";
}

 ?>

 <html>
	<body>
 
		<!--next comes the form, you must set the enctype to "multipart/frm-data" and use an input type "file" -->
		<form name="newad" method="post" enctype="multipart/form-data"  action="">
			<table>
				<tr><td><input name="image" type="file" ></td></tr>
				<tr><td><input name="Submit" type="submit" value="Upload image"></td></tr>
			</table>	
		</form>
	</body>
</html>