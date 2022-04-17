<?php

ini_set('display_errors', 1);

function display($path)
{
	$file = fopen($path,"r");
	while(!feof($file))
	{
		$line = fgets($file);
		echo $line;
	}
	fclose($file);
}

display("src/front-end/login.html");

?>
