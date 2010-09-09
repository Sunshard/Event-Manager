<?php

echo '<html>
	<title>Test Form</title>
	
	<head></head>
	
	<body>
	<br />';
	
	date_default_timezone_set('Asia/Singapore');
	
	$script_tz = date_default_timezone_get();
	
echo "<br /><br /> Current Script Timezone: " . $script_tz . "<br /><br /><br />";

		phpinfo();
echo '<br />
	</body>

</html>';

?>