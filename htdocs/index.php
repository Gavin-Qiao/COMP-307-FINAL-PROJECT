<?php

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

if (sizeof($_GET)==0)
{
	display("HTDOCS/MATTER/CONTENT/top-of-all-files.txt");
	display("HTDOCS/MATTER/CONTENT/login-style.txt");
	display("HTDOCS/MATTER/CONTENT/top-of-all-bodys.txt");
	display("HTDOCS/MATTER/CONTENT/header.txt");
	display("HTDOCS/MATTER/CONTENT/login-body.txt");
	display("HTDOCS/MATTER/CONTENT/bottom-of-all-files.txt");
}

?>
